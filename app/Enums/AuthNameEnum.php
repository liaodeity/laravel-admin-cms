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


class AuthNameEnum extends BaseEnum
{
    const INDEX  = 'index';//列表
    const CREATE = 'create';//创建
    const EDIT   = 'edit';//修改
    const SHOW   = 'view';//查看
    const DELETE = 'delete';//删除
    const IMPORT = 'import';//导入
    const EXPORT = 'export';//导出
    protected static $ATTRS = [
        self::INDEX  => '列表',
        self::CREATE => '创建',
        self::EDIT => '修改',
        self::SHOW => '查看',
        self::DELETE => '删除',
        self::IMPORT => '导入',
        self::EXPORT => '导出',
    ];
}
