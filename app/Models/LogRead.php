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

class LogRead extends Model
{
    use DateTimeFormat;
    protected $fillable = ['id', 'log_id', 'user_id', 'is_read', 'read_at', 'created_at', 'updated_at'];

    public function user ()
    {
        return $this->belongsTo (User::class);
    }
}
