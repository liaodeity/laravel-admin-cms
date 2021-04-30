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
use App\Models\Survey;
use App\Models\User;
use App\Repositories\RoleRepository;
use App\Validators\PermissionValidator;
use App\Validators\RoleValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

class RoleController extends Controller
{
    protected $module_name = 'role';
    /**
     * @var RoleRepository
     */
    private $repository;

    public function __construct (RoleRepository $repository)
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
        if (!check_admin_auth ($this->module_name.'_'.__FUNCTION__)) {
            return auth_error_return();
        }
        if (request ()->ajax ()) {
            $limit = $request->input ('limit', 15);
            QueryWhere::defaultOrderBy ('roles.id', 'DESC')->setRequest ($request->all ());
            $M = $this->repository->makeModel ()->select ('roles.*');
            QueryWhere::eq ($M, 'roles.status');
            QueryWhere::like ($M, 'roles.title');
            QueryWhere::date ($M, 'start_date');
            QueryWhere::date ($M, 'end_date');
            QueryWhere::orderBy ($M);

            $M     = $M->paginate ($limit);
            $count = $M->total ();
            $data  = $M->items ();
            foreach ($data as $key => $item) {
                $role = Role::findById ($item['id']);
                $data[$key]['auth_count'] = $item['name'] == 'super' ? '-' : $role->permissions ()->count ();
            }
            $result = [
                'count' => $count,
                'data'  => $data
            ];

            return ajax_success_result ('成功', $result);

        } else {
            $user = $this->repository->makeModel ();

            return view ('admin.' . $this->module_name . '.index', compact ('user'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create ()
    {
        $role       = $this->repository->makeModel ();
        $_method    = 'POST';

        return view ('admin.' . $this->module_name . '.add', compact ('role', '_method'));
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
            'Role.title' => 'required',
            'Role.name'  => 'required',
        ], [], [
            'Role.title' => '角色名称',
            'Role.name'  => '角色英文标识',
        ]);

        if (!check_admin_auth ($this->module_name . '_create')) {
            return auth_error_return ();
        }
        $input = $request->input ('Role');
        $input = $this->formatRequestInput (__FUNCTION__, $input);
        try {
            $ret              = $this->repository->saveRole ($input);
            if ($ret) {
                Log::createLog (Log::ADD_TYPE, '添加角色权限记录', '', $ret->id, Role::class);

                return ajax_success_result ('创建成功');
            } else {
                return ajax_error_result ('创建失败');
            }

        } catch (BusinessException $e) {
            return ajax_error_result ($e->getMessage ());
        }
    }

    private function formatRequestInput (string $__FUNCTION__, $input)
    {
        return $input;
    }

    /**
     * Display the specified resource.
     *
     * @param Role $role
     * @return void
     */
    public function show (Role $role)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Role $role
     * @return \Illuminate\Http\Response
     */
    public function edit (Role $role)
    {
        $_method = 'PUT';

        return view ('admin.' . $this->module_name . '.add', compact ('role', '_method'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Role                     $role
     * @return \Illuminate\Http\Response
     */
    public function update (Request $request, Role $role)
    {
        $request->validate ([
            'Role.title' => 'required',
            'Role.name'  => 'required',
        ], [], [
            'Role.title' => '角色名称',
            'Role.name'  => '角色英文标识',
        ]);

        if (!check_admin_auth ($this->module_name . '_create')) {
            return auth_error_return ();
        }
        $input = $request->input ('Role');
        $input = $this->formatRequestInput (__FUNCTION__, $input);
        try {
            $ret              = $this->repository->saveRole ($input, $role->id);
            if ($ret) {
                Log::createLog (Log::ADD_TYPE, '修改角色权限记录', '', $ret->id, Role::class);

                return ajax_success_result ('修改成功');
            } else {
                return ajax_error_result ('修改失败');
            }

        } catch (BusinessException $e) {
            return ajax_error_result ($e->getMessage ());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy ($id, Request $request)
    {

    }
}
