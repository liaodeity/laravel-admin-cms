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


class ConfigTypeEnum extends BaseEnum
{
    //1=字符串、2=数字、3=数组、4=键值对数组、5=JSON、6=文本框
    const STR_TYPE  = 1;
    const NUM_TYPE  = 2;
    const ARR_TYPE  = 3;
    const ITEM_TYPE = 4;
    const JSON_TYPE = 5;
    const TEXT_TYPE = 6;

    protected static $ATTRS = [
        self::STR_TYPE  => '字符串',
        self::NUM_TYPE  => '数字',
        self::ARR_TYPE  => '数组',
        self::ITEM_TYPE => '键值对数组',
        self::JSON_TYPE => 'JSON',
        self::TEXT_TYPE => '文本框',
    ];
}
