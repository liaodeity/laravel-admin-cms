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
namespace App\Repositories;


use App\Models\Log;
use Illuminate\Database\Eloquent\Model;

class LogRepository extends BaseRepository implements InterfaceRepository
{

    public function model ()
    {
        return Log::class;
    }

    public function allowDelete ($id)
    {
        return true;
    }
}
