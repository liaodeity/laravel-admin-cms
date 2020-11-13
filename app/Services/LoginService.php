<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2019/12/17
 */

namespace App\Services;


use App\Entities\Admin;
use App\Entities\Agent;
use Illuminate\Support\Facades\Hash;

class LoginService
{
    /**
     * 登录账号认证 add by gui
     * @param $loginType
     * @param $username
     * @param $password
     * @return bool
     * @throws \ErrorException
     */
    public function check($loginType, $username, $password)
    {
        if (empty($username)) {
            throw  new \ErrorException('请输入登录账号');
        }
        if (empty($password)) {
            throw new \ErrorException('请输入账号密码');
        }

        switch ($loginType) {
            case 'admin':
                $user        = Admin::where('username', $username)->first();
                $session_key = 'login_admin_uid';
                break;
            case 'agent':
                $user        = Agent::where('username', $username)->first();
                $session_key = 'login_agent_uid';
                break;
            default :
                throw new \ErrorException('登录类型不正确');
        }
        if (!isset($user->id)) {
            throw new \ErrorException('登录账号不正确');
        }
        if (!Hash::check($password, $user->password)) {
            throw new \ErrorException('账号密码不正确');
        }
        if ($user->status != 1) {
            throw new \ErrorException('账号已禁止登录');
        }

        //登录标识UID
        session([
            $session_key => $user->id
        ]);

        return true;
    }
}
