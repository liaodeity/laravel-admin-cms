<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2020/1/17
 */

namespace App\Services;


use App\Entities\Admin;
use App\Entities\Log;
use App\Entities\MessageNotice;
use App\Entities\MessageNoticeUser;
use App\Entities\Order;
use http\Message;
use Illuminate\Support\Facades\Artisan;

class MessageNoticeService
{
    protected $type = '';

    public function __construct()
    {

    }

    /**
     * 消息类型
     * @param $type
     * @return $this
     */
    public function setType($type): MessageNoticeService
    {
        $this->type = $type;
        return $this;
    }

    /**
     * 新订单消息
     * @param $orderID
     * @return bool
     */
    public function newOrderNotice($orderID)
    {
        $content = '您有一个新订单，请注意查看';
        $insArr  = [
            'type'        => 'NEW_ORDER_TIP',
            'content'     => $content,
            'access_id'   => $orderID,
            'access_type' => Order::class
        ];
        $notice  = MessageNotice::create($insArr);
        if (empty($notice)) {
            Log::createLog(Log::DEBUG_TYPE, '新订单提醒添加失败');
            return false;
        }
        $noticeID = $notice->id ?? 0;
        $list     = Admin::where('status', 1)->where('send_order_tips', 1)->get();
        foreach ($list as $item) {
            $openid = $item->wxAccount->openid ?? '';
            //后台提醒
            $insArr = [
                'notice_id'   => $noticeID,
                'access_id'   => $item->id,
                'access_type' => Admin::class,
                'is_tips'     => 0,
            ];
            $ret    = MessageNoticeUser::create($insArr);
            if ($openid) {
                //微信提醒
                $insArr = [
                    'openid'      => $openid,
                    'notice_id'   => $noticeID,
                    'access_id'   => $item->id,
                    'access_type' => Admin::class,
                    'is_tips'     => 0,
                ];
                MessageNoticeUser::create($insArr);
            }
        }
        Artisan::call('notice:send');
    }

    /**
     * 检查是否有新订单提醒 add by gui
     * @param $access_id
     * @param $access_type
     * @return bool
     */
    public function hasNewOrderTip($access_id, $access_type)
    {
        $list = MessageNoticeUser::select('message_notice_users.id')
            ->join('message_notices', 'message_notice_users.notice_id', '=', 'message_notices.id')
            ->where('message_notice_users.access_id', $access_id)
            ->where('message_notice_users.access_type', $access_type)
            ->where('message_notice_users.is_tips', 0)->get();
        $new  = 0;
        foreach ($list as $item) {
            $ret = MessageNoticeUser::where('id', $item->id)->update(['is_tips' => 1, 'tips_at' => date('Y-m-d H:i:s')]);
            if ($ret) {
                $new++;
            }
        }
        return $new > 0 ? true : false;
    }

    /**
     * 发送提醒消息 add by gui
     * @return bool
     */
    public function sendMessageNotice()
    {
        $list             = MessageNoticeUser::select('message_notice_users.*', 'message_notices.type', 'message_notices.content')
            ->join('message_notices', 'message_notice_users.notice_id', '=', 'message_notices.id')
            ->where('message_notice_users.openid', '<>', '')
            ->where('message_notice_users.is_tips', 0)->get();
        $weChatMsgService = new WeChatMsgService();
        foreach ($list as $item) {
            $openID = $item->openid ?? '';
            if (empty($openID)) {
                continue;//无绑定微信
            }

            $adminName = '';
            switch ($item->access_type) {
                case Admin::class:
                    $info      = Admin::find($item->access_id);
                    $adminName = $info->nickname ?? '';
                    break;
            }

            switch ($item->type) {
                case 'NEW_ORDER_TIP':
                    //新订单消息
                    $notice    = MessageNotice::find($item->notice_id);
                    $order_id  = $notice->access_id ?? 0;
                    $order     = Order::find($order_id);
                    $order_no  = $order->order_no ?? '';
                    $order_at  = $order->created_at ?? '';
                    $sendValue = [
                        'keyword1' => $order_no,//订单编号
                        'keyword2' => $order->order_amount ?? 0,//订单金额
                        'keyword3' => date('Y-m-d H:i:s', strtotime($order_at)),//下单时间
                        'keyword4' => $order->consignee ?? '保密',//收货人
                        'keyword5' => $order->consignee_address ?? '保密'//收货人地址
                    ];
                    try {
                        $weChatMsgService->setDataValue($sendValue)->getAdminNewOrder($adminName)->sendMessage($openID);
                        \App\Entities\Log::createAdminLog(\App\Entities\Log::LOG_TYPE, '提示【' . $adminName . '】新订单，订单编号：' . $order_no);
                    } catch (\ErrorException $e) {
                        \App\Entities\Log::createLog(\App\Entities\Log::DEBUG_TYPE, $e->getMessage());
                        return false;
                    }
                    break;
            }

            $ret = MessageNoticeUser::where('id', $item->id)->update(['is_tips' => 1, 'tips_at' => date('Y-m-d H:i:s')]);
        }
        return true;
    }
}
