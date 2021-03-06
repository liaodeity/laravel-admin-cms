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


class MenuTypeEnum extends BaseEnum
{
    const MENU = 1;//菜单
    const BTN  = 2;//按钮
    protected static $ATTRS = [
        self::MENU => '菜单',
        self::BTN  => '按钮'
    ];
    protected static $COLORS = [
        self::MENU => ColorEnum::PRIMARY,
        self::BTN   => ColorEnum::SECONDARY
    ];
}
