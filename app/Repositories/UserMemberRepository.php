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


use App\Models\User\UserMember;
use App\Validators\User\UserMemberValidator;

class UserMemberRepository extends BaseRepository implements InterfaceRepository
{

    public function model ()
    {
        return UserMember::class;
    }

    public function validator ()
    {
        return UserMemberValidator::class;
    }

    public function allowDelete ($id)
    {
        return true;
    }

}
