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


use App\Exceptions\BusinessException;
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
            $check    = $role->hasPermissionTo ($val->name);
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

    /**
     *  add by gui
     * @param      $input
     * @param null $roleId
     * @return Role|\Illuminate\Database\Eloquent\Model|\Spatie\Permission\Contracts\Role|\Spatie\Permission\Models\Role
     * @throws BusinessException
     */
    public function saveRole ($input, $roleId = null)
    {
        $name = $input['name'] ?? '';
        if ($roleId) {
            //修改
            $role = Role::findById ($roleId);
            $role->fill ([
                'name'  => $name,
                'title' => $input['title'] ?? ''
            ]);
            $role->save ();
        } else {
            //创建
            $role = Role::where('name', $name)->first();
            if ($role) {
                throw new BusinessException('角色英文标识已经存在');
            }
            $role = Role::create ([
                'name'  => $name,
                'title' => $input['title'] ?? ''
            ]);
        }
        if (!$role) {
            throw new BusinessException('保存角色失败');
        }

        return $role;
    }
}
