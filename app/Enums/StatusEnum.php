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

namespace App\Enums;


class StatusEnum extends BaseEnum
{
    const NORMAL = 1;//正常
    const HIDE   = 0;//隐藏

    protected static $ATTRS  = [
        self::NORMAL => '正常',
        self::HIDE   => '隐藏',
    ];
    protected static $COLORS = [
        self::NORMAL => ColorEnum::SUCCESS,
        self::HIDE   => ColorEnum::INFO
    ];
}
