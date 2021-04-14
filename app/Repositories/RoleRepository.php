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


use App\Models\Permission;
use App\Models\Role;

class RoleRepository extends BaseRepository implements InterfaceRepository
{

    public function model ()
    {
        return Role::class;
    }
    public function allowDelete ($id)
    {
        return true;
    }

    public function getPermission ($menuId = 0, Role $role)
    {
        $child = Permission::where ('menu_id', $menuId)->orderBy ('name')->get ();
        if ($child->isEmpty ()) {
            return false;
        }
        $_child = [];
        foreach ($child as $val) {
            $check = $role->hasPermissionTo ($val->name);
            $_child[] = [
                'id'      => $val->id,
                'name'    => $val->name,
                'title'   => $val->title,
                'checked' => $check ?? false,
            ];
        }
        $auth['child'] = $_child;

        return $auth;
    }
}
