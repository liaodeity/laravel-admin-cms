<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2019/12/20
 */

namespace App\Services;

use App\Entities\Agent;
use Illuminate\Contracts\Encryption\DecryptException;

/**
 * 授权登录
 * Class AuthLoginService
 * @package App\Services
 */
class AuthLoginService
{
    private $effectiveSecond = 600;//10分钟

    /**
     * 授权地址 add by gui
     * @param $agentID
     * @return string
     * @throws \ErrorException
     */
    public function getAgentAuthUrl ($agentID)
    {
        $agent = Agent::find ($agentID);
        if (!$agent) {
            throw new \ErrorException('生成授权失败');
        }
        $timestamp = time ();
        $sign      = $this->getSign ($agentID, $agent->username, $timestamp);
        $auth      = [
            'agent_id'  => $agentID,
            'admin_id'  => get_admin_id (),
            'username'  => $agent->username,
            'timestamp' => $timestamp,
            'sign'      => $sign
        ];
        $json      = json_encode ($auth);

        $authCode = encrypt ($json);

        return route ('auth-agent-login', urlencode ($authCode));
    }

    /**
     * 签名 add by gui
     * @param $agentID
     * @param $username
     * @param $timestamp
     * @return string
     */
    protected function getSign ($agentID, $username, $timestamp)
    {
        return sha1 ($agentID . $username . $timestamp . '%^&*(@)&*');
    }

    /**
     * 检查权限密文是否正确 add by gui
     * @param $authCode
     * @return mixed
     * @throws \ErrorException
     */
    public function checkAuthToArray ($authCode)
    {
        if (empty($authCode)) {
            throw new \ErrorException('授权参数为空');
        }
        $authCode = urldecode ($authCode);
        try {
            $json = decrypt ($authCode);
            $arr  = json_decode ($json, true);
            if (empty($arr)) {
                throw new \ErrorException('授权地址内容出错');
            }
            if (!isset($arr['agent_id'])) {
                throw new \ErrorException('机构ID不存在，授权失败');

            }
            $agent = Agent::find ($arr['agent_id']);
            $sign  = $this->getSign ($agent->id, $agent->username, $arr['timestamp']);
            if ($arr['sign'] != $sign) {
                throw new \ErrorException('授权签名失败');
            }
            //有效时间
            $now_time = time () - $this->effectiveSecond;
            if ($arr['timestamp'] <= $now_time) {
                throw new \ErrorException('授权地址已过期，有效期：' . intval ($this->effectiveSecond / 60) . '分钟');
            }

            return $arr;
        } catch (DecryptException $e) {
            throw  new \ErrorException($e->getMessage ());
        }
    }
}
