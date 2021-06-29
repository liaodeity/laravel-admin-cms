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

class UserMemberValidator extends BaseValidator
{
    protected $rules      = [
        self::RULE_CREATE => [
            'status' => 'required'
        ],
        self::RULE_UPDATE => [
            'status' => 'required'
        ]
    ];
    protected $attributes = [
        'status' => '状态'
    ];
}
