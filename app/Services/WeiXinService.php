<?php
/**
 * Created by localhost.
 * User: gui
 * Email: liaodeity@foxmail.com
 * Date: 2020/2/29
 */

namespace App\Services;


use App\Entities\Config;
use Illuminate\Support\Facades\Log;

class WeiXinService
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $officialAccount;

    public function __construct()
    {
        $this->officialAccount = app('wechat.official_account');
    }

    /**
     * 微信菜单更新 add by gui
     * @return bool
     * @throws \ErrorException
     */
    public function menuUpdate()
    {
        $ret = $this->officialAccount->menu->current();
        Log::info('获取当前微信导航菜单', $ret);

        //
        $buttons = config('wechat.menu');
        $context = get_config_value('wx_menu', json_encode($buttons));
        $menu    = json_decode($context, true);
        $buttons = Config::wxMenuToData($menu);
//        dd($buttons);
        $ret = $this->officialAccount->menu->create($buttons);
        if ($ret['errcode'] != 0) {
            $json = json_encode($ret);
            \App\Entities\Log::createLog(\App\Entities\Log::DEBUG_TYPE, '更新微信菜单失败：' . $json);
            $msg = $ret['errmsg'] ?? '更新云端微信接口失败';
            throw new \ErrorException($msg);
        } else {
            \App\Entities\Log::createLog(\App\Entities\Log::LOG_TYPE, '发布更新微信菜单成功');
            return true;
        }
    }

}
