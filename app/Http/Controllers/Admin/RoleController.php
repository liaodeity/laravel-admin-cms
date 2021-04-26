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
        //$role       = $this->repository->makeModel ();
        //$_method    = 'POST';
        //$surveyList = Survey::where ('status', 1)->get ();
        //
        //return view ('admin.' . $this->module_name . '.add', compact ('role', '_method', 'surveyList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store (Request $request)
    {
        //$input = $request->input ('Role');
        //$input = $this->formatRequestInput (__FUNCTION__, $input);
        //try {
        //    $this->repository->makeValidator ()->with ($input)->passes (RoleValidator::RULE_UPDATE);
        //    $input['user_id'] = get_login_user_id ();
        //    $ret              = $this->repository->create ($input);
        //    if ($ret) {
        //        Log::createLog (Log::ADD_TYPE, '添加角色权限记录', '', $ret->id, Role::class);
        //
        //        return ajax_success_result ('创建成功');
        //    } else {
        //        return ajax_error_result ('创建失败');
        //    }
        //
        //} catch (BusinessException $e) {
        //    return ajax_error_result ($e->getMessage ());
        //}
    }

    private function formatRequestInput (string $__FUNCTION__, $input)
    {
        if (isset($input['role_date'])) {
            $roleDate            = explode ('至', $input['role_date']);
            $startDate           = isset($roleDate[0]) ? trim ($roleDate[0]) : null;
            $endDate             = isset($roleDate[1]) ? trim ($roleDate[1]) : null;
            $input['start_date'] = $startDate;
            $input['end_date']   = $endDate;
        }

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
        //$_method = 'PUT';
        //if (isset($role->start_date) && isset($role->end_date)) {
        //    $role->role_date = $role->start_date . ' 至 ' . $role->end_date;
        //}
        //$surveyList = Survey::where ('status', 1)->get ();
        //
        //return view ('admin.' . $this->module_name . '.add', compact ('role', '_method', 'surveyList'));
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
        //$input = $request->input ('Role');
        //$input = $this->formatRequestInput (__FUNCTION__, $input);
        //try {
        //    $this->repository->makeValidator ()->with ($input)->passes (RoleValidator::RULE_UPDATE);
        //    $input['user_id'] = get_login_user_id ();
        //    $ret              = $this->repository->update ($input, $role->id);
        //    if ($ret) {
        //        Log::createLog (Log::EDIT_TYPE, '修改角色权限记录', '', $ret->id, Role::class);
        //
        //        return ajax_success_result ('修改成功');
        //    } else {
        //        return ajax_success_result ('修改失败');
        //    }
        //
        //} catch (BusinessException $e) {
        //    return ajax_error_result ($e->getMessage ());
        //}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy ($id, Request $request)
    {
        //$ids = $request->input ('ids', []);
        //if (empty($ids)) {
        //    $ids[] = $id;
        //}
        //$ids   = (array)$ids;
        //$M     = $this->repository->makeModel ();
        //$lists = $M->whereIn ('id', $ids)->get ();
        //$num   = 0;
        //foreach ($lists as $item) {
        //    try {
        //        $this->repository->checkAuth ($item);
        //    } catch (BusinessException $e) {
        //        return ajax_error_result ($e->getMessage ());
        //    }
        //    $check = $this->repository->allowDelete ($item->id);
        //    if ($check) {
        //        Log::createLog (Log::DELETE_TYPE, '删除角色权限记录', $item, $item->id, Role::class);
        //        $ret = $this->repository->delete ($item->id);
        //        if ($ret) {
        //            $num++;
        //        }
        //    }
        //}
        //
        //return ajax_success_result ('成功删除' . $num . '条记录');
    }

    public function listAuth ()
    {
        if (!check_admin_auth ('role auth')) {
            return auth_error_return();
        }
        $_method = 'PUT';
        $menus   = Menu::where ('auth_name', '<>','')->orderBy ('sort', 'asc')->get ();
        $auths   = [];
        $role = new Role();
        foreach ($menus as $menu) {
            $auth = $this->repository->getPermission ($menu->id, $role);
            if (!$auth) {
                continue;
            }
            $auth['menu_name'] = $menu->title;
            $auths[]           = $auth;
        }
        $other = $this->repository->getPermission (0, $role);
        if ($other) {
            $other['menu_name'] = '其他';
            $auths[]            = $other;
        }

        return view ('admin.' . $this->module_name . '.index_auth', compact ('role', 'auths', 'menus', '_method'));
    }
    public function auth ($id, Request $request)
    {
        if (!check_admin_auth ('role auth')) {
            return auth_error_return();
        }
        $role = Role::findById ($id);
        if ($request->wantsJson ()) {
            $input = $request->input ('auth', []);
            try {
                $input = (array)$input;
                if (!empty($input)) {
                    foreach ($input as $val) {
                        $val   = trim ($val);
                        $check = $role->hasPermissionTo ($val);
                        if (!$check) {
                            $role->givePermissionTo ($val);
                        }
                    }
                }

                $alls = $role->getAllPermissions ();
                foreach ($alls as $item) {
                    if (!in_array ($item->name, $input)) {
                        $role->revokePermissionTo ($item->name);
                    }
                }

                return ajax_success_result ('调整权限成功');

            } catch (BusinessException $e) {
                return ajax_error_result ($e->getMessage ());
            }
        } else {
            $_method = 'PUT';
            $menus   = Menu::where ('auth_name', '<>','')->orderBy ('sort', 'asc')->get ();
            $auths   = [];
            foreach ($menus as $menu) {
                $auth = $this->repository->getPermission ($menu->id, $role);
                if (!$auth) {
                    continue;
                }
                $auth['menu_name'] = $menu->title;
                $auths[]           = $auth;
            }
            $other = $this->repository->getPermission (0, $role);
            if ($other) {
                $other['menu_name'] = '其他';
                $auths[]            = $other;
            }

            return view ('admin.' . $this->module_name . '.role_auth', compact ('role', 'auths', 'menus', '_method'));
        }
    }

    public function addAuth (Request $request)
    {


    }
}
