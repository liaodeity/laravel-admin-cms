<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2019/12/4
 */

namespace App\Http\Controllers\Member;


use App\Entities\Agent;
use App\Entities\Bill;
use App\Entities\Member;
use App\Entities\WxAccount;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WxBaseController;
use App\Services\ShareQrcodeService;
use App\Services\WxAccountService;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AgentController extends WxBaseController
{
    /**
     * @var int
     */
    private $agentID;
    /**
     * @var Request
     */
    private $request;

    public function __construct (Request $request)
    {
        parent::__construct ();
        $this->agentID = 1;
        $this->request = $request;
    }

    //代理商确认佣金
    public function billConfirm ()
    {
        $response = $this->oauth ($this->request->fullUrl ());
        if ($response !== true) {
            return $response;//跳转到授权
        }
        $openID = $this->getOpenID ();

        $billID = $this->request->bill_id ?? 0;

        $WxAccountService = new WxAccountService(new WxAccount());
        try {
            $wxAccount = $WxAccountService->getAccountToOpenID ($openID, Agent::class);
            if (!isset($wxAccount->account_id)) {
                throw new \ErrorException('获取代理商信息失败');
            }
            $agentID = $wxAccount->account_id ?? 0;
            $agent   = Agent::find ($agentID);
            $bill    = Bill::find ($billID);
            if ($bill->agent_id != $agentID) {
                throw new \ErrorException('无权限处理该代理商佣金');
            }
            $member = Member::find ($bill->member_id);

            return view ('member.bill_confirm', compact ('member', 'bill', 'agent'));

        } catch (\ErrorException $e) {
            abort (403, $e->getMessage ());
        }
    }
    //更新佣金发放信息
    public function billUpdate ()
    {
        $billID          = $this->request->id ?? 0;
        $status          = $this->request->status ?? 4;
        $bill            = Bill::find ($billID);
        $bill->verity_at = date ('Y-m-d H:i:s');
        $bill->status    = $status;

        try {
            $WxAccountService = new WxAccountService(new WxAccount());
            $openID           = $this->getOpenID ();
            $wxAccount        = $WxAccountService->getAccountToOpenID ($openID, Agent::class);
            if (!isset($wxAccount->account_id)) {
                throw new \ErrorException('获取代理商信息失败');
            }
            $agentID = $wxAccount->account_id ?? 0;
            $agent   = Agent::find ($agentID);
            if ($bill->agent_id != $agentID) {
                throw new \ErrorException('无权限处理该代理商佣金');
            }
        } catch (\ErrorException $e) {
            return ajax_error_message ($e->getMessage ());
        }

        $ret = $bill->save ();
        if ($ret) {
            $message = $status == 1 ? '已转账!' : '已作废!';
            $arr     = [
                'message' => $message
            ];

            return response ()->json ($arr);
        } else {
            return ajax_error_message ('操作失败，稍后再试');
        }
    }

    /**
     * 微信绑定[跳转到微信获取openid等]
     * add by gui
     */
    public function wxBind ()
    {
        $this->setOpenID ('');
        $response = $this->oauth ($this->request->fullUrl ());
        if ($response !== true) {
            return $response;//跳转到授权
        }
        $openID   = $this->getOpenID ();
        $agent_id = session ('scan_flush_agent_id');
        if ($agent_id) {
            $WxAccountService        = new WxAccountService(new WxAccount());
            $insArr                  = $this->officialAccount->user->get ($openID);
            $insArr['openid']        = $openID;
            $insArr['account_id']    = $agent_id;
            $insArr ['account_type'] = Agent::class;

            try {
                $count = WxAccount::where ('account_type', Agent::class)->where ('account_id', $agent_id)->count ();
                if ($count) {
                    abort (403, '已绑定其他微信，请解绑后操作');
                }
                $WxAccountService->createOrUpdateAccount ($insArr);

            } catch (\ErrorException $e) {
                abort (403, $e->getMessage ());
            }

            return redirect (url ('member/agent-qrcode'));
        }

    }

    /**
     *  引导关注公众号
     *  add by gui
     */
    public function qrcode ()
    {
        return view ('member.agent_qrcode');
    }
    //生日提醒，备用
    public function birthdayTip ()
    {
        $response = $this->oauth ($this->request->fullUrl ());
        if ($response !== true) {
            return $response;//跳转到授权
        }
        $openID           = $this->getOpenID ();
        $WxAccountService = new WxAccountService(new WxAccount());
        try {
            $wxAccount = $WxAccountService->getAccountToOpenID ($openID, Agent::class);
            if (!isset($wxAccount->account_id)) {
                throw new \ErrorException('获取代理商信息失败');
            }
            $agentID  = $wxAccount->account_id ?? 0;
            $agent    = Agent::find ($agentID);
            $today    = date ('m-d');
            $members  = Member::select ('members.*')
                ->whereNotNull ('members.birthday')
                ->where ('members.birthday', 'like', '%-' . $today)
                ->join ('member_agents', 'member_agents.member_id', '=', 'members.id')
                ->where('member_agents.agent_id', $agentID)
                ->get ();

            return view ('member.birthday_tip', compact ('agent', 'members'));

        } catch (\ErrorException $e) {
            abort (403, $e->getMessage ());
        }
    }
}
