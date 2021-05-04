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

namespace App\Models;


use App\Traits\DateTimeFormat;
use Illuminate\Database\Eloquent\Model;

class ConfigGroup extends Model
{
    use DateTimeFormat;
    protected $fillable = ['name', 'title'];

    public static function insertGroup ($name, $title)
    {
        $name  = strtolower (trim ($name));
        $group = ConfigGroup::updateOrCreate ([
            'name' => $name
        ], [
            'name'  => $name,
            'title' => $title
        ]);

        return $group->id;
    }
}
