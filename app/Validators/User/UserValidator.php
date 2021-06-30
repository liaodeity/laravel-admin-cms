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

namespace App\Validators\User;


use App\Validators\BaseValidator;

class UserValidator extends BaseValidator
{
    protected $rules      = [
        self::RULE_CREATE => [
            'name'     => 'required',
            'password' => 'required',
        ],
        self::RULE_UPDATE => [
            'name' => 'required',
        ]
    ];
    protected $attributes = [
        'name'     => '登录账号',
        'password' => '登录密码',
    ];
    protected $messages   = [
        'password.required' => '创建新账号需要设置密码'
    ];
}
