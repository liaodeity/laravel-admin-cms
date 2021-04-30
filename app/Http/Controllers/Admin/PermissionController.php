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
        if (!check_admin_auth ($this->module_name . '_' . __FUNCTION__)) {
            return auth_error_return ();
        }
        if (request ()->ajax ()) {
            $limit = $request->input ('limit', 15);
            QueryWhere::defaultOrderBy ('permissions.id', 'DESC')->setRequest ($request->all ());
            $M = $this->repository->makeModel ()->select ('permissions.*');
            QueryWhere::eq ($M, 'permissions.status');
            QueryWhere::like ($M, 'permissions.name');
            QueryWhere::like ($M, 'permissions.title');
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
    public function create (Request $request)
    {
        if (!check_admin_auth ($this->module_name . '_create')) {
            return auth_error_return ();
        }
        $_method = 'POST';
        $menus   = Menu::where('status',1)->orderBy ('sort', 'asc')->get ();
        $menus = $menus->toArray();
        $menus = list_to_tree ($menus);
        $auth    = new Permission();

        return view ('admin.' . $this->module_name . '.add', compact ('auth', 'menus', '_method'));
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
            'Permission.title' => 'required',
            'Permission.name'  => 'required',
        ], [], [
            'Permission.title' => '权限名称',
            'Permission.name'  => '权限标识',
        ]);

        if (!check_admin_auth ($this->module_name . '_create')) {
            return auth_error_return ();
        }
        $input = $request->input ('Permission');
        try {
            $name       = trim ($input['name']);
            $name       = strtolower ($name);
            $name       = str_replace (' ', '_', $name);
            $permission = Permission::where ('name', $name)->first ();
            if ($permission) {
                return ajax_error_result ('权限标识[' . $name . ']已经存在，无需重复添加');
            }
            $permission = Permission::create (['guard_name' => 'web', 'name' => $name, 'title' => $input['title'], 'menu_id' => (int)$input['menu_id']]);

            if ($permission) {
                return ajax_success_result ('添加成功');
            } else {
                return ajax_error_result ('添加失败');
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
        $_method = 'PUT';
        $roleAll = Role::all ();
        $menus   = Menu::where('status',1)->orderBy ('sort', 'asc')->get ();
        $menus = $menus->toArray();
        $menus = list_to_tree ($menus);

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
            'Permission.title' => 'required',
            'Permission.name'  => 'required',
        ], [], [
            'Permission.title' => '权限名称',
            'Permission.name'  => '权限标识',
        ]);

        if (!check_admin_auth ($this->module_name . '_create')) {
            return auth_error_return ();
        }
        $input = $request->input ('Permission');
        try {
            $name       = trim ($input['name']);
            $name       = strtolower ($name);
            $name       = str_replace (' ', '_', $name);
            $check = Permission::where ('name', $name)->where ('id', '<>', $permission->id)->first ();
            if ($check) {
                return ajax_error_result ('权限标识[' . $name . ']已经存在，无需重复添加');
            }
            $permission->fill(['guard_name' => 'web', 'name' => $name, 'title' => $input['title'], 'menu_id' => (int)$input['menu_id']]);
            $permission->save();
            if ($permission) {
                return ajax_success_result ('更新成功');
            } else {
                return ajax_error_result ('更新失败');
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
