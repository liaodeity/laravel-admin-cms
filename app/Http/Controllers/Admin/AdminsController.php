<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Admin;
use App\Entities\Log;
use App\Http\Controllers\Controller;
use App\Presenters\AdminPresenter;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\AdminCreateRequest;
use App\Http\Requests\AdminUpdateRequest;
use App\Repositories\AdminRepositoryEloquent as AdminRepository;
use App\Validators\AdminValidator;

/**
 * Class AdminsController.
 * @package namespace App\Http\Controllers;
 */
class AdminsController extends Controller
{
    /**
     * @var AdminRepository
     */
    protected $repository;

    /**
     * @var AdminValidator
     */
    protected $validator;
    /**
     * @var AdminPresenter
     */
    protected $presenter;

    /**
     * AdminsController constructor.
     * @param AdminRepository $repository
     * @param AdminValidator $validator
     */
    public function __construct(AdminRepository $repository, AdminValidator $validator, AdminPresenter $presenter)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->presenter  = $presenter;
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!check_admin_permission('show admins')) {
            abort(403, trans('禁止访问，无权限'));
        }

        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        if (request()->wantsJson()) {
            $keyword = $request->keyword ?? '';
            $role_id = $request->role_id ?? '';
            $status  = $request->status ?? '';
            $orderBy = $request->_order_by ?? 'id desc';
            $admins  = app($this->repository->model());
            if ($keyword) $admins = $admins->where(function ($query) use ($keyword) {
                $query->where('username', 'like', '%' . $keyword . '%')->orWhere('nickname', 'like', '%' . $keyword . '%');
            });
            if ($status) $admins = $admins->where('status', $status);
            if ($role_id) {
                $admins = $admins->whereRaw(DB::raw(" id IN(SELECT model_id FROM `tb_model_has_roles` WHERE role_id=$role_id)"));
            }
            if ($orderBy) $admins = $admins->orderByRaw($orderBy);
            $admins = $admins->paginate();
            $html   = '';

            foreach ($admins as $item) {
                $role_name = $this->presenter->getRoleNameString($this->repository->getRoleList($item->id));
                $button    = '';
                $button    .= get_auth_show_button('show admins', route('admins.show', $item->id));
                $button    .= get_auth_edit_button('edit admins', route('admins.edit', $item->id));
                //$button .= get_auth_delete_button('delete admins', route('admins.destroy', $item->id));
                $del_btn_str = '';
                if($this->repository->allowDelete($item->id)){
                    $del_btn_str .= '<li><a onclick="delete_confirm_fun(\'删除记录\',\'' . route('admins.destroy', $item->id) . '\')" href="#">删除</a></li>';
                }
                if ($item->id != 1) {
                    $button .= '<button type="button" class="btn btn-sm btn-info dropdown-toggle"
                                                    data-toggle="dropdown" aria-expanded="false">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a onclick="confirm_fun(\'禁用记录\',\'' . route('admins.disable', $item->id) . '\')" href="#">禁用</a></li>
                                                <li><a onclick="confirm_fun(\'启用记录\',\'' . route('admins.enable', $item->id) . '\')" href="#">启用</a></li>
                                                <li class="divider"></li>
                                                '.$del_btn_str.'
                                            </ul>';

                }
                $disabled = $item->id == 1 ? ' disabled ' : '';
                $html     .= '<tr>
                                    <td><input class="check-item" type="checkbox" ' . $disabled . ' value="' . $item->id . '"></td>
                                    <td>' . $item->username . '</td>
                                    <td>' . $item->nickname . '</td>
                                    <td>' . $role_name . '</td>
                                    <td>' . $item->created_at . '</td>
                                    <td>' . $item->statusItem($item->status, true) . '</td>
                                    <td>
                                        <div class="btn-group">
                                            ' . $button . '
                                        </div>
                                    </td>
                                </tr>';
            }
            $page = html_entity_decode($admins->links());

            $total = $admins->total();

            return response()->json([
                'html'  => $html,
                'page'  => $page,
                'total' => $total,
            ]);
        }
        $buttonHtml = '';
        $admin      = app($this->repository->model());
        Log::createAdminLog(Log::SHOW_TYPE, '管理员列表 查看记录');
        $roleList = $this->repository->getRoleList();

        return view('admin.admins.index', compact('admin', 'buttonHtml', 'roleList'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (!check_admin_permission('create admins')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $method     = 'POST';
        $action_url = route('admins.store');
        $admin      = app($this->repository->model());
        $roleList   = $this->repository->getRoleList();

        return view('admin.admins.create_and_edit', compact('admin', 'action_url', 'method', 'roleList'));
    }

    /**
     * Store a newly created resource in storage.
     * @param AdminCreateRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(AdminCreateRequest $request)
    {
        if (!check_admin_permission('create admins')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {
            $input = $request->input('Admin');
            input_default($input, '');
            $input['admin_id'] = get_admin_id();
            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_CREATE);
            //加密
            $input['password'] = Hash::make($input['password']);

            $admin = $this->repository->create($input);

            $response = [
                'message' => trans('添加成功'),
                'data'    => $admin->toArray(),
            ];
            Log::createAdminLog(Log::ADD_TYPE, '管理员列表 创建记录');
            if ($request->wantsJson()) {
                $RoleName = $request->input('RoleName');
                if (!empty($RoleName) && is_array($RoleName)) {
                    $roleList = $this->repository->getRoleList($admin->id);
                    foreach ($roleList as $item) {
                        if (!in_array($item->name, $RoleName)) {
                            $admin->removeRole($item->name);//取消授权
                        }
                    }
                    $admin->assignRole($RoleName);//新增授权
                }

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()->first(),
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag()->first())->withInput();
        }
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!check_admin_permission('show admins')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $admin = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $admin,
            ]);
        }
        $roleList  = $this->repository->getRoleList($id);
        $role_name = $this->presenter->getRoleNameString($roleList);
        return view('admin.admins.show', compact('admin', 'roleList', 'role_name'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!check_admin_permission('edit admins')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $admin      = $this->repository->find($id);
        $method     = 'PUT';
        $action_url = route('admins.update', $id);
        $roleList   = $this->repository->getRoleList($id);

        return view('admin.admins.create_and_edit', compact('admin', 'action_url', 'method', 'roleList'));
    }

    /**
     * Update the specified resource in storage.
     * @param AdminUpdateRequest $request
     * @param string $id
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(AdminUpdateRequest $request, $id)
    {
        if (!check_admin_permission('edit admins')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {

            $input             = $request->input('Admin');
            $input['admin_id'] = get_admin_id();
            if (empty($input['password'])) {
                unset($input['password']);
            } else {
                //加密
                $input['password'] = Hash::make($input['password']);
                Log::createAdminLog(Log::EDIT_TYPE, '管理员列表 修改' . Admin::showName($id) . '密码');
            }
            $rules = $this->validator->getRules();
            $rules[ValidatorInterface::RULE_UPDATE]['username'] = 'required|unique:App\Entities\Admin,username,'.$id;
            $this->validator->setRules($rules);
            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $admin = $this->repository->update($input, $id);

            $response = [
                'message' => trans('修改成功'),
                'data'    => $admin->toArray(),
            ];
            Log::createAdminLog(Log::EDIT_TYPE, '管理员列表 修改记录');
            if ($request->wantsJson()) {
                $RoleName = $request->input('RoleName');
                if (!empty($RoleName) && is_array($RoleName)) {
                    $roleList = $this->repository->getRoleList($id);
                    foreach ($roleList as $item) {
                        if (!in_array($item->name, $RoleName)) {
                            $admin->removeRole($item->name);//取消授权
                        }
                    }
                    $admin->assignRole($RoleName);//新增授权
                }

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()->first(),
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag()->first())->withInput();
        }
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request, $id)
    {
        $id    = $request->id ?? $id;
        $idArr = explode(',', $id);
        foreach ($idArr as $_id) {
            $ret = app($this->repository->model())->where('id', $_id)->update(['status' => 2]);
        }

        Log::createAdminLog(Log::EDIT_TYPE, '管理员列表 禁用记录');
        if (request()->wantsJson()) {

            return response()->json([
                'message' => trans('禁用成功'),
                'ret'     => $ret,
            ]);
        }

        return redirect()->back()->with('message', trans('禁用成功'));
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function enable(Request $request, $id)
    {
        $id    = $request->id ?? $id;
        $idArr = explode(',', $id);
        foreach ($idArr as $_id) {
            $ret = app($this->repository->model())->where('id', $_id)->update(['status' => 1]);
        }

        Log::createAdminLog(Log::EDIT_TYPE, '管理员列表 启用记录');
        if (request()->wantsJson()) {

            return response()->json([
                'message' => trans('启用成功'),
                'ret'     => $ret,
            ]);
        }

        return redirect()->back()->with('message', trans('启用成功'));
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $id    = $request->id ?? $id;
        $idArr = explode(',', $id);
        $error = 0;
        $success = 0;
        foreach ($idArr as $_id) {
            if ($this->repository->allowDelete ($_id)) {
                $deleted = $this->repository->delete ($_id);
                if ($deleted) {
                    $success++;
                }
            } else {
                $error++;
            }
        }
        if ($error) {
            return response ()->json ([
                'error'   => true,
                'message' => trans ('已删除' . $success . '条记录，存在' . $error . '条记录被使用无法删除'),
            ]);
        }

        Log::createAdminLog(Log::DELETE_TYPE, '管理员列表 删除记录');
        if (request()->wantsJson()) {

            return response()->json([
                'message' => trans('删除成功'),
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', trans('删除成功'));
    }
}
