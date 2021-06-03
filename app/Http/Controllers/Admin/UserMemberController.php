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
use App\Models\User\UserMember;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Spatie\Permission\Models\Role;

class UserMemberController extends Controller
{
    protected $module_name = 'user_member';
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
        if (!check_admin_auth ($this->module_name . '_' . __FUNCTION__)) {
            return auth_error_return ();
        }
        if (request ()->wantsJson ()) {
            $limit = $request->input ('limit', 15);
            QueryWhere::defaultOrderBy ('users.id', 'DESC')->setRequest ($request->all ());
            $M = $this->repository->makeModel ()->select ('user_members.*', 'users.name','users.login_count','users.last_login_at',
                'user_infos.real_name', 'user_infos.gender', 'user_infos.telephone', 'user_infos.address');
            $M->join ('user_members', 'users.id', '=', 'user_members.user_id');
            $M->leftJoin ('user_infos', 'user_infos.user_id', '=', 'users.id');
            QueryWhere::eq ($M, 'user_members.status');
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
                $data[ $key ]['gender'] = Parameter::genderItem ($item->gender);
                $data[ $key ]['status'] = Parameter::userStatusItem ($item->status);
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
            'User.name'         => 'required',
            'User.password'     => 'required',
            'UserMember.status' => 'required'
        ], [], [
            'User.name'         => '登录账号',
            'User.password'     => '登录密码',
            'UserMember.status' => '状态'
        ]);
        if (!check_admin_auth ($this->module_name . '_create')) {
            return auth_error_return ();
        }
        DB::beginTransaction ();
        $inputUser = $request->input ('User');
        $input     = $this->formatRequestInput (__FUNCTION__, $inputUser);
        try {
            if (!User::isSuperAdmin ()) {
                throw new BusinessException('非超级管理员，无法操作');
            }
            $input['password'] = Hash::make ($input['password']);
            $user              = $this->repository->create ($input);
            if ($user) {
                $this->repository->saveInfo ($user, $request);
                $this->repository->saveMember ($user, $request);
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


                Log::createLog (Log::ADD_TYPE, '添加会员账号', '', $user->id, User::class);
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
     * @param \App\Models\UserMember $userMember
     * @return \Illuminate\Http\Response
     */
    public function show (UserMember $userMember)
    {
        if (!check_admin_auth ($this->module_name . '_edit')) {
            return auth_error_return ();
        }
        $user = $userMember->user;

        return view ('admin.' . $this->module_name . '.show', compact ('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\UserMember $userMember
     * @return \Illuminate\Http\Response
     */
    public function edit (UserMember $userMember)
    {
        if (!check_admin_auth ($this->module_name . ' edit')) {
            return auth_error_return ();
        }
        $_method = 'PUT';
        $user    = $userMember->user;

        return view ('admin.' . $this->module_name . '.add', compact ('user', '_method'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param UserMember               $userMember
     * @return \Illuminate\Http\Response
     */
    public function update (Request $request, UserMember $userMember)
    {
        $request->validate ([
            'User.name'         => 'required',
            'UserMember.status' => 'required'
        ], [], [
            'User.name'         => '登录账号',
            'User.password'     => '登录密码',
            'UserMember.status' => '状态'
        ]);
        if (!check_admin_auth ($this->module_name . ' edit')) {
            return auth_error_return ();
        }
        $input = $request->input ('User');
        $input = $this->formatRequestInput (__FUNCTION__, $input);
        try {
            if (array_get ($input, 'password')) {
                $input['password'] = Hash::make ($input['password']);
            } else {
                unset($input['password']);
            }
            DB::beginTransaction ();
            $user = $this->repository->update ($input, $userMember->user_id);
            if ($userMember) {
                $this->repository->saveInfo ($user, $request);
                $this->repository->saveMember ($user, $request);
                Log::createLog (Log::EDIT_TYPE, '修改账号', $user->toArray (), $userMember->id, User::class);
                if (array_get ($input, 'password')) {
                    Log::createLog (Log::EDIT_TYPE, '重置会员[' . $user->name . ']账号密码', '', $user->id, User::class);
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
     * @param \App\Models\UserMember $userMember
     * @return \Illuminate\Http\Response
     */
    public function destroy (UserMember $userMember)
    {
        //
    }
}
