<?php
/**
 * Created by localhost.
 * User: gui
 * Email: liaodeity@foxmail.com
 * Date: 2019/10/30
 */

namespace App\Http\Controllers;


use App\Entities\Admin;
use App\Entities\Agent;
use App\Entities\Log;
use App\Services\AuthLoginService;
use App\Services\LockScreenService;
use App\Services\LoginService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * @var LoginService
     */
    private $loginService;
    /**
     * @var LockScreenService
     */
    private $lockScreenService;

    /**
     * LoginController constructor.
     * @param LoginService $loginService
     */
    public function __construct (LoginService $loginService, LockScreenService $lockScreenService)
    {
        $this->loginService      = $loginService;
        $this->lockScreenService = $lockScreenService;
    }

    /**
     * 后台登录 add by gui
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function admin ()
    {
        $loginType = 'admin';

        return view ('login.admin_login', compact ('loginType'));
    }

    /**
     * 机构登录 add by gui
     */
    public function agent ()
    {
        $loginType = 'agent';

        return view ('login.agent_login', compact ('loginType'));
    }

    /**
     * 登录认证 add by gui
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function check (Request $request)
    {
        $input     = $request->input ('Login');
        $loginType = $input['loginType'] ?? '';
        $username  = $input['username'] ?? '';
        $password  = $input['password'] ?? '';
        $captcha   = array_get ($input, 'captcha');
        if(captcha_check ($captcha) !== true){
            $this->refreshSessionPlus();
            return response ()->json ([
                'error'   => true,
                'refresh' => $this->checkRefresh(),
                'message' => '验证码错误',
            ]);
        }
        try {
            $this->loginService->check ($loginType, $username, $password);
            $response = [
                'message'  => trans ('登录成功'),
                'main_url' => url ($loginType)
            ];
            Log::createLog (Log::LOGIN_TYPE, $username . '登录系统');
            //取消锁屏
            $this->lockScreenService->setType ($loginType)->cancelLock ();
            if ($request->wantsJson ()) {

                return response ()->json ($response);
            }

        } catch (\ErrorException $e) {
            if ($request->wantsJson ()) {
                $this->refreshSessionPlus();
                return response ()->json ([
                    'error'   => true,
                    'refresh' => true,//验证码已验证过，必须重新生成
                    'message' => $e->getMessage (),
                ]);
            }
        }
        if ($request->wantsJson ()) {
            $this->refreshSessionPlus();
            return response ()->json ([
                'error'   => true,
                'refresh' => true,
                'message' => '无法登陆',
            ]);
        }
    }

    protected function refreshSessionPlus ()
    {
        $num = session ()->get ('login.check.error.number',0);
        session ()->put ('login.check.error.number', $num+1);
    }
    protected function checkRefresh ()
    {
        $num = session ()->get ('login.check.error.number');
        captcha();//刷新一次验证码
        return $num >= 5 ? true : false;
    }
    /**
     * 机构授权自动登录 add by gui
     * @param AuthLoginService $authLoginService
     * @param                  $authCode
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function auth_agent_login (AuthLoginService $authLoginService, $authCode)
    {
        try {
            $loginArr = $authLoginService->checkAuthToArray ($authCode);
            $agentID  = $loginArr['agent_id'] ?? 0;
            if (empty($agentID)) {
                abort (403, '找不到授权登录的机构ID');
            }
            $agent = Agent::find ($agentID);
            session ([
                'login_agent_uid' => $agent->id
            ]);

            $admin_id = get_admin_id ();
            Log::createLog (Log::LOGIN_TYPE, Admin::showName ($admin_id) . '，使用授权登录账号：' . Agent::showName ($agent->id));

            //跳转登录
            return redirect (url ('agent'));

        } catch (\ErrorException $e) {
            abort (403, $e->getMessage ());
        }
    }

    public function checkLockScreen (Request $request, $type)
    {

        $lock     = $this->lockScreenService->setType ($type)->checkIsLock ();
        $response = [
            'message' => trans ('返回成功'),
            'is_lock' => $lock === true ? 1 : 0
        ];
        if ($request->wantsJson ()) {

            return response ()->json ($response);
        }
    }

    /**
     * 验证码 add by gui
     * @return array|\Intervention\Image\ImageManager|mixed
     * @throws \Exception
     */
    public function captcha ()
    {
        $type = get_config_value ('CAPTCHA_TYPE', 'math');
        switch ($type) {
            case 'math':
            case 'type1':
            case 'default':
                break;
            case 'random':
                $type = array_random (['math', 'type1']);
                break;
            default:
                $type = 'math';
                break;
        }

        return captcha ($type);
    }
}
