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
namespace App\Traits;


trait DateTimeFormat
{
    /**
     * Prepare a date for array / JSON serialization.
     *
     * @return string
     */
    protected function serializeDate (\DateTimeInterface $date)
    {
        return $date->format ('Y-m-d H:i:s');
    }
}
