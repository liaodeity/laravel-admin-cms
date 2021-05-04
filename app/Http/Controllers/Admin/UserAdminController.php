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
use App\Libs\Parameter;
use App\Libs\QueryWhere;
use App\Models\Log;
use App\Models\User;
use App\Models\User\UserAdmin;
use App\Repositories\UserRepository;
use App\Validators\UserValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Spatie\Permission\Models\Role;

class UserAdminController extends Controller
{
    protected $module_name = 'user_admin';
    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct (UserRepository $repository)
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
            return auth_error_return ();
        }
        if (request ()->ajax ()) {
            $limit = $request->input ('limit', 15);
            QueryWhere::defaultOrderBy ('users.id', 'DESC')->setRequest ($request->all ());
            $M = $this->repository->makeModel ()->select ('user_admins.*', 'users.name', 'user_infos.real_name', 'user_infos.gender', 'user_infos.telephone', 'user_infos.address');
            $M->join ('user_admins', 'users.id', '=', 'user_admins.user_id');
            $M->leftJoin ('user_infos', 'user_infos.user_id', '=', 'users.id');
            QueryWhere::eq ($M, 'user_admins.status');
            QueryWhere::like ($M, 'users.name');
            QueryWhere::like ($M, 'user_infos.real_name');
            QueryWhere::orderBy ($M);

            $roleId = QueryWhere::input ('role_id');
            if ($roleId) {
                $role    = Role::find ($roleId);
                $usersid = $role->users ()->pluck ('model_id');
                QueryWhere::in ($M, 'users.id', $usersid);
            }

            $M     = $M->paginate ($limit);
            $count = $M->total ();
            $data  = $M->items ();
            foreach ($data as $key => $item) {
                $roles = [];
                $user  = User::find ($item->user_id);
                if ($user) {
                    foreach ($user->roles as $role) {
                        $roles[] = $role->title;
                    }
                }

                $data[ $key ]['gender'] = Parameter::genderItem ($item->gender);
                $data[ $key ]['status'] = Parameter::userStatusItem ($item->status);
                $data[ $key ]['role']   = implode ('|', $roles);
            }
            $result = [
                'count' => $count,
                'data'  => $data
            ];

            return ajax_success_result ('成功', $result);

        } else {
            $user = $this->repository->makeModel ();

            $roles = Role::all ();

            return view ('admin.' . $this->module_name . '.index', compact ('user', 'roles'));
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
        $user    = new User;
        $_method = 'POST';
        $roleAll = Role::all ();

        return view ('admin.' . $this->module_name . '.add', compact ('user', '_method', 'roleAll'));
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
            'User.name'        => 'required',
            'User.password'    => 'required',
            'UserAdmin.status' => 'required'
        ], [], [
            'User.name'        => '登录账号',
            'User.password'    => '登录密码',
            'UserAdmin.status' => '状态'
        ]);

        if (!check_admin_auth ($this->module_name . '_create')) {
            return auth_error_return ();
        }


        $input = $request->input ('User');
        $input = $this->formatRequestInput (__FUNCTION__, $input);
        try {
            DB::beginTransaction ();
            if (!User::isSuperAdmin ()) {
                throw new BusinessException('非超级管理员，无法操作');
            }
            $input['password'] = Hash::make ($input['password']);
            $user              = $this->repository->create ($input);
            if ($user) {
                $this->repository->saveInfo ($user, $request);
                $this->repository->saveAdmin ($user, $request);
                $roleAll = Role::all ();
                $roles   = $request->role ?? [];
                foreach ($roleAll as $role) {
                    if (in_array ($role->name, $roles)) {
                        if (!$user->hasRole ($role->name)) {
                            $user->assignRole ($role->name);
                        }
                    } else {
                        $user->removeRole ($role->name);
                    }
                }

                Log::createLog (Log::ADD_TYPE, '添加用户账号', '', $user->id, User::class);
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
     * @param \App\Models\UserAdmin $userAdmin
     * @return \Illuminate\Http\Response
     */
    public function show (UserAdmin $userAdmin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\UserAdmin $userAdmin
     * @return \Illuminate\Http\Response
     */
    public function edit (UserAdmin $userAdmin)
    {
        if (!check_admin_auth ($this->module_name . ' edit')) {
            return auth_error_return ();
        }
        $_method = 'PUT';
        $roleAll = Role::all ();
        $user    = $userAdmin->user;

        return view ('admin.' . $this->module_name . '.add', compact ('user', '_method', 'roleAll'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User         $user
     * @return \Illuminate\Http\Response
     */
    public function update (Request $request, UserAdmin $userAdmin)
    {
        $request->validate ([
            'User.name'        => 'required',
            'UserAdmin.status' => 'required'
        ], [], [
            'User.name'        => '登录账号',
            'User.password'    => '登录密码',
            'UserAdmin.status' => '状态'
        ]);
        if (!check_admin_auth ($this->module_name . ' edit')) {
            return auth_error_return ();
        }
        $input = $request->input ('User');
        $input = $this->formatRequestInput (__FUNCTION__, $input);
        try {
            DB::beginTransaction ();
            $user    = $userAdmin->user;
            $isSuper = $user->hasRole ('super');
            if ($isSuper && $user->id != get_login_user_id ()) {
                throw new BusinessException('无法修改超级管理员信息，需管理员自行修改');
            }

            if (array_get ($input, 'password')) {
                $input['password'] = Hash::make ($input['password']);
            } else {
                unset($input['password']);
            }
            $user = $this->repository->update ($input, $user->id);
            if ($user) {
                $this->repository->saveInfo ($user, $request);
                $this->repository->saveAdmin ($user, $request);
                $roleAll = Role::all ();
                $roles   = $request->role ?? [];
                foreach ($roleAll as $role) {
                    if (in_array ($role->name, $roles)) {
                        if (!$user->hasRole ($role->name)) {
                            $user->assignRole ($role->name);
                        }
                    } else {
                        $user->removeRole ($role->name);
                    }
                }
                Log::createLog (Log::EDIT_TYPE, '修改用户账号', $user->toArray (), $user->id, User::class);
                if (array_get ($input, 'password')) {
                    Log::createLog (Log::EDIT_TYPE, '重置用户[' . $user->name . ']账号密码', '', $user->id, User::class);
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
     * @param \App\Models\UserAdmin $userAdmin
     * @return \Illuminate\Http\Response
     */
    public function destroy (UserAdmin $userAdmin)
    {
        //
    }
}
