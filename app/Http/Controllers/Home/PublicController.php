<?php
/*
|-----------------------------------------------------------------------------------------------------------
| laravel-admin-cms [ 简单高效的开发插件系统 ]
|-----------------------------------------------------------------------------------------------------------
| Licensed ( MIT )
| ----------------------------------------------------------------------------------------------------------
| Copyright (c) 2020-2021 https://gitee.com/liaodeiy/laravel-admin-cms All rights reserved.
| ----------------------------------------------------------------------------------------------------------
| Author: 廖春贵 < liaodeity@gmail.com >
|-----------------------------------------------------------------------------------------------------------
*/

namespace App\Http\Controllers\Home;


use App\Http\Controllers\Controller;
use App\Models\WebView;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function webViewBrowsing (Request $request)
    {
        $web_user  = get_login_user_id ();
        $userAgent = $request->userAgent ();
        $clientIp  = $request->getClientIp ();
        $referer   = $request->server ('HTTP_REFERER');
        $viewUrl   = $request->input ('url');
        if (!$viewUrl) {
            $viewUrl = $request->header ('referer');
        }
        if (!$web_user) {
            $web_user = session ()->getId ();
        }
        $insArr = [
            'web_user'   => $web_user,
            'user_agent' => str_limit ($userAgent, 500,''),
            'client_ip'  => $clientIp ?? '',
            'referer'    => $referer ?? '',
            'view_url'   => $viewUrl,
            'view_at'    => now ()
        ];
        $ret    = WebView::create ($insArr);
        $data   = ['result' => $ret ? true : false];

        return ajax_success_result ('', $data);
    }
}
