<?php

namespace App\Repositories;

use App\Entities\Menu;
use App\Entities\RoleInfo;
use App\Http\Controllers\Admin\RolesController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\RoleRepository;
use App\Entities\Role;
use App\Validators\RoleValidator;
use Spatie\Permission\Models\Permission;

/**
 * Class RoleRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class RoleRepositoryEloquent extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return RoleInfo::class;
    }

    /**
     * Specify Validator class name
     *
     * @return mixed
     */
    public function validator()
    {

        return RoleValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * 将菜单与权限关联，并同步添加到权限记录表
     * @param $roleInfoID
     * @param $input
     * @return mixed
     */
    public function syncPermission($roleInfoID, $input, MenuRepositoryEloquent $menuRepositoryEloquent)
    {
        $inputRoleInfo = $input['RoleInfo'] ?? [];
        $role_value = $inputRoleInfo['role_value'] ?? [];
        $ids = explode(',', $role_value);
        $roleInfo = RoleInfo::find($roleInfoID);
//        dd($role_value);
        $role     = \Spatie\Permission\Models\Role::findById($roleInfo->role_id, 'admin');
//        $menuRepositoryEloquent  = app('\App\Repositories\MenuRepositoryEloquent');
//        $auth_str = $menuRepositoryEloquent->getAuthArrayNodes($roleInfoID);
//        $authArr  = json_decode($auth_str);
//        dd($authArr);
        $authArr = Menu::whereIn('id', $ids)->where('module','admin')->get();
        $ids = [];
        foreach ($authArr as $item) {
            $ids[]  = $item->id;

            $auth_name = $item->auth_name ?? '';
            if (empty($auth_name)) {
                continue;
            }
            $ret = Permission::findOrCreate($auth_name, 'admin');
            if ($auth_name && $ret)
                $role->givePermissionTo($auth_name);//添加角色权限
        }

        //不勾选的权限，取消角色权限

        $notAuth = Menu::whereNotIn('id', $ids)->where('module','admin')->get();
        $role     = \Spatie\Permission\Models\Role::findById($roleInfo->role_id, 'admin');
        foreach ($notAuth as $item){
            $auth_name = $item->auth_name ?? '';

            if (empty($auth_name)) {
                continue;
            }

            $ret = Permission::findOrCreate($auth_name, 'admin');
            if ($auth_name && $ret){
                $role->revokePermissionTo($auth_name);//删除角色权限
            }
        }

    }

    public function allowDelete($id)
    {
        $info    = $this->find($id);
        $role_id = $info->role_id ?? 0;
        $info    = DB::select('select count(1) as total from tb_model_has_roles where role_id = ? LIMIT 1', [$role_id]);
        $count   = $info{0}->total ?? 0;
        if ($count) {
            return false;
        }
        return true;
    }
}
