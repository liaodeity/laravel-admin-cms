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

class Menu extends Model
{
    use DateTimeFormat;
    protected $fillable = ['pid', 'uuid', 'type', 'title', 'auth_name', 'href', 'icon', 'target', 'is_shortcut', 'status', 'sort'];

    public function pidMenu ()
    {
        return $this->belongsTo (Menu::class, 'pid');
    }
}
