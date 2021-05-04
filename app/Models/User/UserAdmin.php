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

namespace App\Models\User;


use App\Models\User;
use App\Traits\DateTimeFormat;
use Illuminate\Database\Eloquent\Model;

class UserAdmin extends Model
{
    use DateTimeFormat;
    protected $fillable = ['user_id', 'login_count', 'last_login_at', 'status'];

    public function user ()
    {
        return $this->belongsTo (User::class);
    }
}
