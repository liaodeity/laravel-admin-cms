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

namespace App\Http\Controllers\Admin;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Libs\QueryWhere;
use App\Models\Log;
use App\Models\Menu;
use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    protected $module_name = 'permission';
    /**
     * @var PermissionRepository
     */
    private $repository;

    public function __construct (PermissionRepository $repository)
    {
        View::share ('MODULE_NAME', $this->module_name);//模块名称
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request)
    {
        if (!check_admin_auth ($this->module_name)) {
            return auth_error_return ();
        }
        if (request ()->ajax ()) {
            $limit = $request->input ('limit', 15);
            QueryWhere::defaultOrderBy ('permissions.id', 'DESC')->setRequest ($request->all ());
            $M = $this->repository->makeModel ()->select ('permissions.*');
            QueryWhere::eq ($M, 'permissions.status');
            QueryWhere::like ($M, 'permissions.name');
            QueryWhere::like ($M, 'Permission_infos.real_name');
            QueryWhere::orderBy ($M);

            $M     = $M->paginate ($limit);
            $count = $M->total ();
            $data  = $M->items ();
            foreach ($data as $key => $item) {
                $data[ $key ]['menu_title'] = Menu::where ('id', $item->menu_id)->value ('title');
            }
            $result = [
                'count' => $count,
                'data'  => $data
            ];

            return ajax_success_result ('成功', $result);

        } else {
            $permission = $this->repository->makeModel ();

            $roles = Role::all ();

            return view ('admin.' . $this->module_name . '.index', compact ('permission', 'roles'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create ()
    {
        if (!check_admin_auth ($this->module_name . '_create')) {
            return auth_error_return ();
        }
        $permission = new Permission;
        $_method    = 'POST';
        $roleAll    = Role::all ();

        return view ('admin.' . $this->module_name . '.add', compact ('Permission', '_method', 'roleAll'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store (Request $request)
    {
        $request->validate ([
            'Permission.name'     => 'required',
            'Permission.password' => 'required',
            'Permission.status'   => 'required'
        ], [], [
            'Permission.name'     => '登录账号',
            'Permission.password' => '登录密码',
            'Permission.status'   => '状态'
        ]);

        if (!check_admin_auth ($this->module_name . '_create')) {
            return auth_error_return ();
        }


        $input = $request->input ('Permission');
        $input = $this->formatRequestInput (__FUNCTION__, $input);
        try {
            DB::beginTransaction ();
            if (!Permission::isSuperAdmin ()) {
                throw new BusinessException('非超级管理员，无法操作');
            }
            $input['password'] = Hash::make ($input['password']);
            $permission        = $this->repository->create ($input);
            if ($permission) {
                $this->repository->saveInfo ($permission, $request);
                $this->repository->saveAdmin ($permission, $request);
                $roleAll = Role::all ();
                $roles   = $request->role ?? [];
                foreach ($roleAll as $role) {
                    if (in_array ($role->name, $roles)) {
                        if (!$permission->hasRole ($role->name)) {
                            $permission->assignRole ($role->name);
                        }
                    } else {
                        $permission->removeRole ($role->name);
                    }
                }

                Log::createLog (Log::ADD_TYPE, '添加用户账号', '', $permission->id, Permission::class);
                DB::commit ();

                return ajax_success_result ('添加成功');
            } else {
                return ajax_success_result ('添加失败');
            }

        } catch (BusinessException $e) {
            return ajax_error_result ($e->getMessage ());
        }
    }

    private function formatRequestInput (string $__FUNCTION__, $input)
    {
        switch ($__FUNCTION__) {
            case 'update':
            case 'store':
                $input['sex'] = array_get_number ($input, 'sex', 0);
                break;
        }

        return $input;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Permission $permission
     * @return \Illuminate\Http\Response
     */
    public function show (Permission $permission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Permission $permission
     * @return \Illuminate\Http\Response
     */
    public function edit (Permission $permission)
    {
        if (!check_admin_auth ($this->module_name . ' edit')) {
            return auth_error_return ();
        }
        $_method    = 'PUT';
        $roleAll    = Role::all ();
        $permission = $permission->Permission;
        $menus      = Menu::orderBy ('sort', 'asc')->get ();

        return view ('admin.' . $this->module_name . '.add', compact ('menus', 'permission', '_method', 'roleAll'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Permission   $permission
     * @return \Illuminate\Http\Response
     */
    public function update (Request $request, Permission $permission)
    {
        $request->validate ([
            'Permission.name'   => 'required',
            'Permission.status' => 'required'
        ], [], [
            'Permission.name'     => '登录账号',
            'Permission.password' => '登录密码',
            'Permission.status'   => '状态'
        ]);
        if (!check_admin_auth ($this->module_name . ' edit')) {
            return auth_error_return ();
        }
        $input = $request->input ('Permission');
        $input = $this->formatRequestInput (__FUNCTION__, $input);
        try {
            DB::beginTransaction ();
            $permission = $permission->Permission;
            $isSuper    = $permission->hasRole ('super');
            if ($isSuper && $permission->id != get_login_Permission_id ()) {
                throw new BusinessException('无法修改超级管理员信息，需管理员自行修改');
            }

            if (array_get ($input, 'password')) {
                $input['password'] = Hash::make ($input['password']);
            } else {
                unset($input['password']);
            }
            $permission = $this->repository->update ($input, $permission->id);
            if ($permission) {
                $this->repository->saveInfo ($permission, $request);
                $this->repository->saveAdmin ($permission, $request);
                $roleAll = Role::all ();
                $roles   = $request->role ?? [];
                foreach ($roleAll as $role) {
                    if (in_array ($role->name, $roles)) {
                        if (!$permission->hasRole ($role->name)) {
                            $permission->assignRole ($role->name);
                        }
                    } else {
                        $permission->removeRole ($role->name);
                    }
                }
                Log::createLog (Log::EDIT_TYPE, '修改用户账号', $permission->toArray (), $permission->id, Permission::class);
                if (array_get ($input, 'password')) {
                    Log::createLog (Log::EDIT_TYPE, '重置用户[' . $permission->name . ']账号密码', '', $permission->id, Permission::class);
                }
                DB::commit ();

                return ajax_success_result ('更新成功');
            } else {
                return ajax_success_result ('更新失败');
            }

        } catch (BusinessException $e) {
            return ajax_error_result ($e->getMessage ());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Permission $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy (Permission $permission)
    {
        //
    }
}
