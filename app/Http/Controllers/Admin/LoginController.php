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

namespace App\Http\Controllers\Admin;


use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Services\LoginService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index ()
    {
        return view ('admin.login.index');
    }

    public function check (Request $request)
    {
        $username = $request->input ('username');
        $password = $request->input ('password');
        $captcha  = $request->input ('captcha');
        if (!captcha_check ($captcha)) {
            return ajax_error_result ('验证码不正确');
        }
        $result       = ['refresh' => true];
        $LoginService = new LoginService();
        try {
            $ret = $LoginService->checkLogin ($username, $password, LoginService::ADMIN_TYPE);
            if ($ret === true) {

                return ajax_success_result ('登录成功');
            } else {
                return ajax_error_result ('登录失败', $result);
            }
        } catch (BusinessException $e) {
            return ajax_error_result ($e->getMessage (), $result);
        }
    }

    public function captcha ()
    {
        return captcha ('admin_login');
    }
}
