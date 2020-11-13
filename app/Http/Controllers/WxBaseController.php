<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2019/12/27
 */

namespace App\Http\Controllers;


use App\Entities\Member;
use App\Entities\WxAccount;
use App\Services\WxAccountService;

class WxBaseController extends Controller
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $officialAccount;
    /**
     * @var WxAccountService
     */
    protected $wxAccountService;

    /**
     * WxBaseController constructor.
     */
    public function __construct ()
    {
        $this->officialAccount  = app ('wechat.official_account');
        $this->wxAccountService = new WxAccountService(new WxAccount());

    }

    protected function setOpenID ($opedID)
    {
        session ()->put ('wx_member_open_id', $opedID);
        if(empty($opedID)){
            return false;
        }
        try {
            $info = $this->wxAccountService->getAccountToOpenID($opedID, Member::class);
            if(isset($info->id)){
                $member_id = $info->account_id ?? 0;
                if($member_id){
                    session()->put('login_member_uid',$member_id);
                    $member = Member::find($member_id);
                    $member->last_login_at = date('Y-m-d H:i:s');
                    $member->save();
                }
            }
        } catch (\ErrorException $e) {
        }

    }

    protected function getOpenID ()
    {
        if(config('wechat.location_debug')){
            return 'location';
        }

        return session ('wx_member_open_id', false);
    }

    /**
     *  add by gui
     * @throws \ErrorException
     */
    protected function getWxAccount ()
    {
        $wxAccount = $this->wxAccountService->getAccountToOpenID ($this->getOpenID ());

        return $wxAccount;
    }

    /**
     * 获取jsSdk的配置json add by gui
     * @param      $APIs
     * @param bool $debug
     * @param null $url
     * @return mixed
     */
    protected function getJsSDKJson ($APIs, $debug = false, $url = null)
    {
        if(config('wechat.location_debug')){
            return '{}';
        }

        if ($url) {
            $this->officialAccount->jssdk->setUrl ($url);
        }

        $json = $this->officialAccount->jssdk->buildConfig ($APIs, $debug);

        return $json;
    }

    /**
     * 授权地址获取，如open不存在 add by gui
     * @param null $url
     * @return bool
     * @throws \ErrorException
     */
    protected function oauth ($url = null)
    {
        if(config('wechat.location_debug')){
           return true;
        }
        $openID = $this->getOpenID ();
        if (!empty($openID)) {
            return true;
        }

        if (empty($openID) && !isset($_REQUEST['code'])) {
            $response = $this->officialAccount->oauth->redirect ($url);

            return $response;
        }
        $user    = $this->officialAccount->oauth->user ();
        $account = $user->getOriginal ();
        $openID = $user->getId ();//OpenId

        $this->wxAccountService->createOrUpdateAccount ($account);

        $this->setOpenID ($openID);

        return true;
    }


}
