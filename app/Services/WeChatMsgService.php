<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2019/12/31
 */

namespace App\Services;

use App\Entities\Admin;
use App\Entities\Agent;
use App\Entities\Log;
use App\Entities\Member;
use App\Entities\TemplateMessage;
use App\Entities\TemplateMessageLog;
use App\Entities\WxAccount;
use Illuminate\Support\Facades\Storage;

/**
 * 微信消息
 * Class WeChatMsgService
 * @package App\Services
 */
class WeChatMsgService
{
    /**
     * @var TemplateMessage
     */
    private $templateMessage;
    private $dataValue = [];
    private $sendData = [];
    private $markName;
    /**
     * @var WxAccountService
     */
    private $wxAccountService;
    private $getParam = '';


    public function __construct ()
    {
        $this->templateMessage  = new TemplateMessage();
        $this->wxAccountService = new WxAccountService(new WxAccount());

    }

    public function setDataValue (array $dataValue)
    {
        $this->dataValue = $dataValue;

        return $this;
    }

    /**
     * 初始化推送消息 add by gui
     * @throws \ErrorException
     */
    protected function initSendData ()
    {
        $this->sendData = $this->templateMessage->getParamsToMarkName ($this->markName);

        if (empty($this->sendData)) {
            $this->sendData = [];
        }
        $data = $this->sendData['data'] ?? [];
        foreach ($this->dataValue as $key => $val) {
            $data[$key]['value'] = $val;
        }
        //        foreach ($data as $key => $item) {
        //            if (isset($this->dataValue[$key])) {
        //                //存在内容
        //                $data[$key]['value'] = $this->dataValue[$key];
        //            }
        //        }
        if ($this->sendData['url']) {
            //携带参数
            $this->sendData['url'] = $this->sendData['url'] . '?' . $this->getParam;
        }

        $this->sendData['data'] = $data;
    }

    /**
     * 代理商生日消息 add by gui
     * @param $agentName
     * @param $memberMobile
     * @return WeChatMsgService
     * @throws \ErrorException
     */
    public function getAgentBirthday ($agentName, $memberMobile)
    {
        $this->markName = md5 (__FUNCTION__);
        $this->initSendData ();

        $data       = $this->sendData['data'] ?? [];
        $header_val = $data['first']['value'] ?? '';
        $footer_val = $data['remark']['value'] ?? '';
        if ($header_val) {
            $header_val             = str_replace ('{代理商名称}', $agentName, $header_val);
            $data['first']['value'] = $header_val;
        }
        if ($footer_val) {
            $footer_val              = str_replace ('{会员电话}', $memberMobile, $footer_val);
            $data['remark']['value'] = $footer_val;
        }

        $this->sendData['data'] = $data;

        return $this;

    }

    /**
     * 会员生日提醒
     * @param string $memberName 会员名称
     * @return $this
     * @throws \ErrorException
     */
    public function getMemberBirthday ($memberName)
    {
        $this->markName = md5 (__FUNCTION__);
        $this->initSendData ();
        $data       = $this->sendData['data'] ?? [];
        $header_val = $data['first']['value'] ?? '';
        if ($header_val) {
            $header_val             = str_replace ('{会员名称}', $memberName, $header_val);
            $data['first']['value'] = $header_val;
        }

        $this->sendData['data'] = $data;


        return $this;
    }

    /**
     * 提醒机构发放佣金 add by gui
     * @param $get_param
     * @return WeChatMsgService
     * @throws \ErrorException
     * @var \App\Services\ScanProductService::receiveBill
     */
    public function getAgentBillGrant ($get_param)
    {
        $this->getParam = $get_param;
        $this->markName = md5 (__FUNCTION__);

        $this->initSendData ();

        return $this;
    }

    /**
     * 管理员新订单提醒
     * 调用处 app/Services/MessageNoticeService.php:137@sendMessageNotice
     * @param $adminName
     * @return $this
     * @throws \ErrorException
     */
    public function getAdminNewOrder ($adminName)
    {
        $this->markName = md5 (__FUNCTION__);

        $this->initSendData ();
        $data       = $this->sendData['data'] ?? [];
        $header_val = $data['first']['value'] ?? '';
        if ($header_val) {
            $header_val             = str_replace ('{管理员名称}', $adminName, $header_val);
            $data['first']['value'] = $header_val;
        }
        $this->sendData['data'] = $data;

        return $this;
    }

    /**
     * 提醒会员佣金到账 add by gui
     * @return WeChatMsgService
     * @throws \ErrorException
     */
    public function getMessageBillArrive ()
    {
        $this->markName = md5 (__FUNCTION__);
        $this->initSendData ();

        return $this;
    }

    /**
     * 发送消息推送 add by gui
     * @param $openID
     * @return bool
     * @throws \ErrorException
     */
    public function sendMessage ($openID)
    {

        $this->sendData['touser'] = $openID;
        $officialAccount          = app ('wechat.official_account');
        $ret                      = $officialAccount->template_message->send ($this->sendData);

        //是否成功
        $status = isset($ret['errcode']) && $ret['errcode'] == 0 ? 1 : 0;
        if ($status == 0) {
            //推送消息失败
            Log::createLog (Log::DEBUG_TYPE, json_encode ($ret));
        }
        //推送日志
        $account   = $this->wxAccountService->getAccountToOpenID ($openID);
        $agent_id  = 0;
        $member_id = 0;
        $admin_id  = 0;
        switch ($account->account_type ?? '') {
            case Member::class:
                $member_id = $account->account_id ?? 0;
                break;
            case Agent::class:
                $agent_id = $account->account_id ?? 0;
                break;
            case Admin::class:
                $admin_id = $account->account_id ?? 0;
                break;
        }
        $template_message_id = $this->templateMessage->getTemplateIDToMarkName ($this->markName);
        $insArr              = [
            'open_id'             => $openID,
            'admin_id'            => $admin_id,
            'agent_id'            => $agent_id,
            'member_id'           => $member_id,
            'template_message_id' => $template_message_id,
            'send_data'           => json_encode ($this->sendData),
            'result_data'         => json_encode ($ret),
            'status'              => $status
        ];
        TemplateMessageLog::create ($insArr);

        return $status == 1 ? true : false;
    }

    /**
     * 获取微信二维码素材 add by gui
     * @param $memberID
     * @param $agentID
     * @return string
     * @throws \ErrorException
     */
    public function myQrcodeMediaID ($memberID, $agentID)
    {
        if (empty($memberID) && empty($agentID)) {
            throw new \ErrorException('必须为用户或代理商');
        }
        $officialAccount    = app ('wechat.official_account');
        $ShareQrcodeService = new ShareQrcodeService();

        $path = 'temp/' . date ('Ymd') . '/' . uniqid () . '.png';
        if ($agentID) {
            $content = $ShareQrcodeService->setType ('scan-agent')->getQrcodeStringToAgent ($agentID);
        }
        if ($memberID) {
            $content = $ShareQrcodeService->setType ('scan-member')->getQrcodeStringToMember ($memberID);
        }
//        \Illuminate\Support\Facades\Log::info($memberID . '#' . $agentID);
        $ret = Storage::put ($path, $content);
        if (!$ret) {
            throw new \ErrorException('生成二维码失败');
        }
        $path = storage_path ('app/' . $path);
        \Illuminate\Support\Facades\Log::info ($path);
        $ret = $officialAccount->media->uploadImage ($path);
        if (isset($ret['media_id'])) {
            return $ret['media_id'];
        } else {
            throw new \ErrorException('上传二维码失败');
        }
    }

    /**
     * 群发消息 add by gui
     * @param $text
     * @param array $openIdArr
     */
    public function broadcastingText ($text, array $openIdArr)
    {
        $officialAccount = app ('wechat.official_account');
        $ret             = $officialAccount->broadcasting->sendText ($text, $openIdArr);
        if (array_get ($ret, 'errcode') == 0) {
            Log::createJSONLog (Log::LOG_TYPE, '发送微信消息成功', $openIdArr);
            return true;
        } else {
            Log::createJSONLog (Log::LOG_TYPE, '发送微信消息失败，' . array_get ($ret, 'errmsg'), $openIdArr);
            return false;
        }
    }
}
