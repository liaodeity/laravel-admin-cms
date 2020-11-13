<?php
/**
 * Created by localhost.
 * User: gui
 * Email: liaodeity@foxmail.com
 * Date: 2020/2/14
 */

namespace App\Services;


use App\Entities\Log;
use App\Entities\Order;
use App\Entities\PayTrade;
use App\Entities\SerialNumber;

class PaymentService
{
    const WX_PAY = 1;
    const ALI_PAY = 2;
    //线下支付
    const OFFLINE_PAY = 3;
    private $tradeType = 0;
    //内部订单号
    private $tradeNO = '';
    /**
     * 流水号ID
     * @var int
     */
    private $serialID;

    public function __construct()
    {

    }

    /**
     * 获取支付二维码 add by gui
     * @param $orderID
     * @param $tradeType
     * @return mixed
     * @throws \ErrorException
     */
    public function getPayQrcodeToOrderID($orderID, $tradeType)
    {
        if (empty($orderID)) {
            throw new \ErrorException('订单ID缺失');
        }
        if (empty($tradeType)) {
            throw new \ErrorException('支付类型缺失');
        }
        $this->tradeType = $tradeType;
        switch ($tradeType) {
            case 1:
                $code_url = $this->wechatNative($orderID);
                break;
            case 2:
                $code_url = $this->alipayNative($orderID);
                break;
            case 3:
                //线下无需生成
                $code_url = null;
                break;
        }
        return $code_url;
    }

    /**
     * 生成微信二维码
     * add by gui
     * @param $orderID
     * @return mixed
     * @throws \ErrorException
     */
    public function wechatNative($orderID)
    {
        $order = Order::find($orderID);

        $order_amount = $order->order_amount ?? 0;
        if (empty($order_amount)) {
            throw new \ErrorException('支付金额为空');
        }
        $this->setTradeNO();
        $out_trade_no = $this->getTradeNO();
        $total_fee    = intval($order_amount * 100);//金额（分）
        $title        = $order->order_no . '订单商品';

        $wxPayment = app('wechat.payment');
        $result    = $wxPayment->order->unify([
            'body'         => $title,
            'out_trade_no' => $out_trade_no,
            'total_fee'    => $total_fee,
            'notify_url'   => url('payments/wechat-notify'), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type'   => 'NATIVE', // 请对应换成你的支付方式对应的值类型
        ]);

        //创建订单记录
        $insArr = [
            'type'               => $this->tradeType,
            'trade_no'           => $out_trade_no,
            'price'              => $total_fee / 100,
            'memberID'           => 0,
            'access_id'          => $orderID,
            'access_type'        => Order::class,
            'result_json'        => json_encode($result),
            'notify_result_json' => '',
            'status'             => 0
        ];
        $ret    = $this->createPayTrade($insArr);
        if (!$ret) {
            Log::createLog(Log::DEBUG_TYPE, '支付订单新增失败');
            throw new \ErrorException('支付订单新增失败');
        }

        if ($result['return_code'] == 'SUCCESS') {

            return $result['code_url'];
        } else {
            Log::createLog(Log::DEBUG_TYPE, json_encode($result));
            throw new \ErrorException($result['return_msg']);
        }
    }

    /**
     * 支付宝二维码
     * add by gui
     * @param $orderID
     * @return mixed
     * @throws \ErrorException
     */
    public function alipayNative($orderID)
    {
        throw new \ErrorException('暂不支持...');
    }

    /**
     * 检查是否订单已支付
     * add by gui
     * @param $trade_no
     * @throws \ErrorException
     */
    public function checkPay($trade_no)
    {
        $info = PayTrade::where('trade_no', $trade_no)->first();
        if (empty($info)) {
            throw new \ErrorException('订单不存在');
        }
        if ($info->status == 1 && $info->trade_at && $info->trade_price) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取支付订单信息
     * add by gui
     * @param $trade_no
     * @return mixed
     * @throws \ErrorException
     */
    public function getTradeInfo($trade_no)
    {
        $info = PayTrade::where('trade_no', $trade_no)->first();
        if (empty($info)) {
            throw new \ErrorException('订单不存在');
        }
        return $info;
    }

    /**
     * @return string
     */
    public function getTradeNO(): string
    {
        return $this->tradeNO;
    }

    /**
     * 创建订单号
     * add by gui
     * @param $insArr
     * @return mixed
     */
    protected function createPayTrade($insArr)
    {
        $info = PayTrade::create($insArr);
        if (isset($info->id) && $this->serialID) {
            SerialNumber::updateSerialID($this->serialID, $info->id);
        }
        return $info;
    }

    /**
     * 生成一个订单号
     * add by gui
     */
    protected function setTradeNO()
    {
        $serialID       = SerialNumber::autoNumber(PayTrade::class);
        $this->serialID = $serialID;
        if (empty($serialID)) {
            $serialID = rand(10000000, 99999999);
        }
        switch ($this->tradeType) {
            case 1:
                $this->tradeNO = 'wx' . date('YmdHis') . $serialID;
                break;
            case 2:
                $this->tradeNO = 'ali' . date('YmdHis') . $serialID;
                break;
            case 3:
                $this->tradeNO = 'ol' . date('YmdHis') . $serialID;
                break;
        }
    }

}
