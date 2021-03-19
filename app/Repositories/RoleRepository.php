<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2021-03-09
 */

namespace App\Repositories;


use App\Validators\RoleValidator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
