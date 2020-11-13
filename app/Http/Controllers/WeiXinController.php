<?php
/**
 * Created by localhost.
 * User: gui
 * Email: liaodeity@foxmail.com
 * Date: 2019/12/29
 */

namespace App\Http\Controllers;


use App\Entities\Agent;
use App\Entities\Member;
use App\Entities\WxAccount;
use App\Entities\WxQrcode;
use App\Services\WeChatMsgService;
use App\Services\WxAccountService;
use App\Services\WxReplyService;
use EasyWeChat\Kernel\Messages\Image;
use Illuminate\Support\Facades\Log;

class WeiXinController extends WxBaseController
{
    protected $errorMsg = '';

    public function __construct ()
    {
        parent::__construct ();
    }

    /**
     * 执行微信回调处理 add by gui
     * @return mixed
     */
    public function callback ()
    {
        $this->officialAccount->server->push (function ($message) {
            //            Log::info('收到内容', $message);
            $MsgType  = $message['MsgType'] ?? '';
            $Event    = $message['Event'] ?? '';
            $EventKey = $message['EventKey'] ?? '';
            $Content  = $message['Content'] ?? '';
            $openID   = $message['FromUserName'] ?? '';
            $Content  = trim ($Content);
            switch ($MsgType) {
                case 'event':
                    switch ($EventKey) {
                        case 'MY_QR_CODE_IMG':
                            //我的二维码
                            return $this->getQrCodeImg ($openID);
                            break;
                    }

                    switch ($Event) {
                        case 'TEMPLATESENDJOBFINISH':
                            $status = $message['Status'] ?? '';

                            //模板消息推送通知
                            return $this->templateSendJobFinish ($openID, $status);
                            break;
                        case 'subscribe':
                        case 'SCAN':
                            //用户已关注时的事件推送【扫描带参数二维码事件】
                            if ($EventKey) {
                                //二维码scene_id
                                $ret = $this->scanSubscribe ($openID, $Event, $EventKey);
                                if ($ret) {
                                    return $ret;
                                }
                            }
                            if ($Event == 'subscribe') {
                                //默认关注回复
                                $WxReplyService = new WxReplyService();
                                $ret            = $WxReplyService->getSubscribeKeywordReply ();
                                if ($ret) {
                                    return $ret;
                                }
                            }
                            break;
                    }
                    //                    return '收到事件消息';
                    break;
                case 'text':
                    \App\Entities\Log::createLog (\App\Entities\Log::LOG_TYPE, '收到微信文字消息：' . $Content);
                    if (strlen ($Content) == 11 && substr ($Content, 0, 1) == '1') {
                        //手机号码，推送代理商二维码
                        $text = $this->getAgentPhoneQrCodeImg ($Content, $openID);
                        if ($text instanceof Image) {
                            return $text;
                        }
                        $text2 = $this->getMemberPhoneQrCodeImg ($Content, $openID);
                        if ($text2 instanceof Image) {
                            return $text2;
                        }
                        if ($text !== false) {
                            return $text;
                        }

                        if ($text2 !== false) {
                            return $text;
                        }
                    }
                    $WxReplyService = new WxReplyService();
                    $keyword        = $WxReplyService->getKeywordReply ($Content);
                    if ($keyword) {
                        return $keyword;
                    }
                    //                    return '收到文字消息';
                    break;
                case 'image':
                    //                    return '收到图片消息';
                    break;
                case 'voice':
                    //                    return '收到语音消息';
                    break;
                case 'video':
                    //                    return '收到视频消息';
                    break;
                case 'location':
                    //                    return '收到坐标消息';
                    break;
                case 'link':
                    //                    return '收到链接消息';
                    break;
                case 'file':
                    //                    return '收到文件消息';
                    // ... 其它消息
                default:
                    //                    return '收到其它消息';
                    break;
            }
        });

        return $this->officialAccount->server->serve ();
    }

    /**
     * @param $openID
     * @return Image|string
     * @deprecated
     * 获取二维码 add by gui
     */
    protected function getQrCodeImg ($openID)
    {
        $WeChatMsgService = new WeChatMsgService();
        $WxAccountService = new WxAccountService(new WxAccount());
        try {
            $agentID  = 0;
            $memberID = 0;
            $account  = $WxAccountService->getAccountToOpenID ($openID, Agent::class);
            if ($account) {
                $agentID = $account->account_id ?? 0;
            }
            $account = $WxAccountService->getAccountToOpenID ($openID, Member::class);
            if ($account) {
                $memberID = $account->account_id ?? 0;
            }

            $mediaID = $WeChatMsgService->myQrcodeMediaID ($memberID, $agentID);

            if ($mediaID) {
                return new Image($mediaID);
            } else {
                return '二维码不存在';
            }

        } catch (\ErrorException $e) {
            return $e->getMessage ();
        }
    }

    /**
     * 根据手机号码获取会员推送二维码 add by gui
     * @param        $phone
     * @param string $openID
     * @return bool|Image|string
     */
    public function getMemberPhoneQrCodeImg ($phone, $openID = '')
    {
        $member   = Member::where ('mobile', $phone)->where ('status', 1)->orderBy ('id', 'desc')->first ();
        $memberID = $member->id ?? 0;
        if (empty($memberID)) {
            return false;
        }
        $wxAccount = WxAccount::where ('openid', $openID)->where ('account_type', Member::class)->where ('account_id', $memberID)->first ();
        if (empty($wxAccount)) {
            return '该会员号码没有绑定当前微信号，无法获取二维码';

        }
        $memberAgent = $member->agents ()->get ();//继承推荐人所有代理商
        //判断时候所有代理商都不允许发展下线，如不允许提示错误
        $not_allow = true;
        foreach ($memberAgent as $item) {
            if ($item->is_allow_subordinate == 1) {
                $not_allow = false;
                break;
            }
        }
        if ($not_allow) {
            return '当前会员没有发展下级会员权限，不可以获取二维码，请与代理商联系。';

        }
        $WeChatMsgService = new WeChatMsgService();
        try {
            $mediaID = $WeChatMsgService->myQrcodeMediaID ($memberID, 0);
            if ($mediaID) {
                return new Image($mediaID);
            } else {
                return false;
            }
        } catch (\ErrorException $e) {
            \App\Entities\Log::createLog (\App\Entities\Log::DEBUG_TYPE, $e->getMessage ());

            return false;
        }
    }

    /**
     * 根据手机号码获取代理商二维码 add by gui
     */
    protected function getAgentPhoneQrCodeImg ($phone, $openID = '')
    {
        $agent   = Agent::where ('contact_phone', $phone)->where ('status', 1)->orderBy ('id', 'desc')->first ();
        $agentID = $agent->id ?? 0;
        if (empty($agentID)) {
            return false;
        }
        $wxAccount = WxAccount::where ('openid', $openID)->where ('account_type', Agent::class)->where ('account_id', $agentID)->first ();
        if (empty($wxAccount)) {
            return '该代理商号码没有绑定当前微信号，无法获取二维码';
        }

        $WeChatMsgService = new WeChatMsgService();
        try {
            $mediaID = $WeChatMsgService->myQrcodeMediaID (0, $agentID);
            if ($mediaID) {
                return new Image($mediaID);
            } else {
                return false;
            }
        } catch (\ErrorException $e) {
            \App\Entities\Log::createLog (\App\Entities\Log::DEBUG_TYPE, $e->getMessage ());

            return false;
        }
    }

    /**
     * 模板消息回执
     * @param $openID
     * @param $status
     */
    protected function templateSendJobFinish ($openID, $status)
    {
        $status = trim ($status);
        switch ($status) {
            case 'success':
                $status = '送达成功';
                break;
            case 'failed:user block':
                $status = '用户拒收';
                break;
            case 'failed: system failed':
                $status = '其他原因失败';
                break;
        }
        $info = WxAccount::where ('openid', $openID)->where ('nickname', '<>', '')->first ();
        $name = $info->nickname ?? $openID;
        \App\Entities\Log::createLog (\App\Entities\Log::LOG_TYPE, '发送消息给【' . $name . '】，送达状态：' . $status);
    }

    /**
     * 扫描或关注
     * @param $openID
     * @param $Event
     * @param $EventKey
     * @return bool
     */
    protected function scanSubscribe ($openID, $Event, $EventKey)
    {
        $scene_id = trim ($EventKey, 'qrscene_');
        $info     = WxQrcode::where ('scene_id', $scene_id)->first ();
        if (!isset($info->id)) {
            //不存在二维码记录
            return false;
        }
        $memberID = $info->member_id ?? 0;
        $agentID  = $info->agent_id ?? 0;
        $text     = '欢迎关注！';
        if ($memberID) {
            //会员
            $member = Member::find ($memberID);
            $url    = url ('member/reg?memberID=' . $memberID);
            $text   = "扫描推广码成功\n";
            $text   .= "推荐人：" . $member->real_name . "\n";
            $text   .= "注册地址：<a href='" . $url . "'>点击注册</a>";
        }
        if ($agentID) {
            //代理商
            $agent = Agent::find ($agentID);
            $url   = url ('member/reg?agentID=' . $agentID);
            $text  = "扫描推广码成功\n";
            $text  .= "代理商：" . $agent->agent_name . "\n";
            $text  .= "注册地址：<a href='" . $url . "'>点击注册</a>\n";
        }

        return $text;
    }
}
