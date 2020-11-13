<?php
/**
 * Created by localhost.
 * User: gui
 * Email: liaodeity@foxmail.com
 * Date: 2020/2/13
 */

namespace App\Http\Controllers;


use App\Entities\Log;
use App\Entities\Order;
use App\Entities\OrderLog;
use App\Entities\PayTrade;
use App\Services\MessageNoticeService;
use App\Services\PaymentService;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * 微信支付回调地址
     * add by gui
     */
    public function wechatNotify()
    {
        $wxPayment = app('wechat.payment');

        $response = $wxPayment->handlePaidNotify(function ($message, $fail) use ($wxPayment) {
            \Illuminate\Support\Facades\Log::info('微信异步支付订单通知', $message);
            $PaymentService = new PaymentService();
            $out_trade_no   = $message['out_trade_no'] ?? '';
            $transaction_id = $message['transaction_id'] ?? '';
            $total_fee      = $message['total_fee'] ?? 0;//订单总金额，单位为分
            if ($total_fee) {
                $total_fee = $total_fee / 100;//单位为元
            }

            //检查订单是否已处理，已支付
            try {
                $check = $PaymentService->checkPay($out_trade_no);
                if ($check) {
                    return true;//已处理
                }

            } catch (\ErrorException $e) {
                Log::createLog(Log::DEBUG_TYPE, '支付订单异步回调' . $e->getMessage() . '，商户订单：' . $out_trade_no);
            }
            $order = PayTrade::where('trade_no', $out_trade_no)->first();
            if (!$order) { // 如果订单不存在
                return true; // 订单没找到，别再通知我了
            }
            $order->notify_result_json = json_encode($message);

            //调用微信的【订单查询】接口查一下该笔订单的情况
            $wxOrder   = $wxPayment->order->queryByTransactionId($transaction_id);
            $payStatus = 0;//支付状态
            if ($wxOrder['return_code'] == 'SUCCESS') {//此字段是通信标识，非交易标识，交易是否成功需要查看trade_state来判断
                \Illuminate\Support\Facades\Log::info('微信异步支付订单查询', $wxOrder);
                //业务结果，交易状态，确保订单支付成功
                if (array_get($wxOrder, 'result_code') === 'SUCCESS' && array_get($wxOrder, 'trade_state') === 'SUCCESS') {
                    $payStatus = 1;//已支付
                } else {
                    Log::createLog(Log::DEBUG_TYPE, '支付异步订单状态失败，商户订单：' . $out_trade_no);
                    return $fail('订单查询失败，请稍后再通知我');
                }
            } else {
                Log::createLog(Log::DEBUG_TYPE, '查询支付订单失败，商户订单：' . $out_trade_no);
            }

            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if (array_get($message, 'result_code') === 'SUCCESS') {
                    $time_now              = date('Y-m-d H:i:s');
                    $time_end              = array_get($message, 'time_end', $time_now);
                    $time_end              = date('Y-m-d H:i:s', strtotime($time_end));
                    $order->trade_open_id  = array_get($message, 'openid', '');
                    $order->trade_price    = $total_fee;
                    $order->transaction_no = $transaction_id;
                    $order->trade_at       = $time_end ? $time_end : $time_now; // 更新支付时间为当前时间
                    $order->status         = $payStatus;

                    // 用户支付失败
                } elseif (array_get($message, 'result_code') === 'FAIL') {
                    $order->status = 2;//支付失败
                }
            } else {
                Log::createLog(Log::DEBUG_TYPE, '支付订单异步回调通信失败，商户订单：' . $out_trade_no);
                return $fail('通信失败，请稍后再通知我');
            }

            $ret = $order->save(); // 保存订单
            if ($ret) {

                //更新订单
                switch ($order->access_type){
                    case Order::class:
                        $o = Order::find($order->access_id);
                        $o->pay_amount = $total_fee;
                        $o->status = Order::NO_DELIVERY_STATUS;
                        $o->save();
                        //
                        OrderLog::createLog($o->id, '微信支付'.$total_fee.'元成功','修改状态：'.$o->statusItem($o->status));

                        //提醒管理员新订单
                        $MessageNoticeService = new MessageNoticeService();
                        $MessageNoticeService->newOrderNotice($o->id);

                        break;
                }

                Log::createLog(Log::LOG_TYPE, '支付订单异步回调成功，商户订单：' . $out_trade_no);
                return true; // 返回处理完成
            } else {
                $log = $order->toArray() ?? [];
                \Illuminate\Support\Facades\Log::info('订单更新信息', $log);
                Log::createLog(Log::DEBUG_TYPE, '查询支付订单更新失败，商户订单：' . $out_trade_no);
                return $fail('处理失败，请稍后再通知我');
            }
        });

        return $response;
    }

    /**
     * 支付宝回调地址 add by gui
     */
    public function alipyNotify ()
    {

    }
    /**
     * 获取支付二维码
     * add by gui
     */
    public function qrcode()
    {
        $type           = $this->request->get('type');
        $orderID        = $this->request->get('order_id');
        $PaymentService = new PaymentService();
        try {
            $code_url = $PaymentService->getPayQrcodeToOrderID($orderID, $type);
            $qrcode   = '';
            $trade_no = '';
            if ($code_url) {
                $trade_no = $PaymentService->getTradeNO();
                //生成二维码
                $qrCode = new QrCode($code_url);
                $qrCode->setEncoding('UTF-8');
                $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH());
                $qrcode = $qrCode->writeDataUri();
            }

            $response = [
                'message'  => '获取成功',
                'qrcode'   => $qrcode,
                'trade_no' => $trade_no
            ];

            if ($this->request->wantsJson()) {

                return response()->json($response);
            }
        } catch (\ErrorException $e) {
            if ($this->request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * 检查支付订单是否已支付
     * add by gui
     * @return \Illuminate\Http\JsonResponse
     */
    public function check()
    {
        $trade_no       = $this->request->get('trade_no');
        $PaymentService = new PaymentService();
        try {
            $check = $PaymentService->checkPay($trade_no);
            if ($check === true) {
                return response()->json([
                    'message' => '已支付成功'
                ]);
            } else {
                return response()->json([
                    'error'   => true,
                    'message' => '未支付'
                ]);
            }
        } catch (\ErrorException $e) {
            return response()->json([
                'error'   => true,
                'message' => $e->getMessage(),
            ]);
        }

    }
}
