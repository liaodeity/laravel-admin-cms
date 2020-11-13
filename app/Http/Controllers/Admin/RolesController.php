<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Log;
use App\Entities\RoleInfo;
use App\Http\Controllers\Controller;
use App\Repositories\MenuRepositoryEloquent;
use App\Repositories\RoleRepositoryEloquent;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\RoleCreateRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Repositories\RoleRepositoryEloquent as RoleRepository;
use App\Validators\RoleValidator;
use Spatie\Permission\Models\Role;

/**
 * Class RolesController.
 * @package namespace App\Http\Controllers;
 */
class RolesController extends Controller
{
    /**
     * @var RoleRepository
     */
    protected $repository;

    /**
     * @var RoleValidator
     */
    protected $validator;

    /**
     * RolesController constructor.
     * @param RoleRepository $repository
     * @param RoleValidator $validator
     */
    public function __construct(RoleRepository $repository, RoleValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));


        if (request()->wantsJson()) {
            $keyword = $request->keyword ?? '';
            $status  = $request->status ?? '';
            $orderBy = $request->_order_by ?? 'id desc';
            $roles   = app($this->repository->model())
            ->select('auth_infos.*')
            ->selectRaw(DB::raw('(SELECT count(1) FROM `role_has_permissions` WHERE role_id=auth_infos.role_id) as p_count'));
            if ($keyword) $roles = $roles->where(function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%')->orWhere('desc', 'like', '%' . $keyword . '%');
            });
            if ($status) $roles = $roles->where('status', $status);

            if ($orderBy) $roles = $roles->orderByRaw($orderBy);
//            dd($roles->toSql());
            $roles = $roles->paginate();
            $html  = '';
            foreach ($roles as $item) {
                $button = '';
                $num    = $item->p_count;
                $button .= get_auth_show_button('show roles', route('roles.show', $item->id));
                $del_btn_str = '';
                if($this->repository->allowDelete($item->id)){
                    $del_btn_str .= '<li class="divider"></li>
                                                <li><a onclick="delete_confirm_fun(\'删除记录\',\'' . route('roles.destroy', $item->id) . '\')" href="#">删除</a></li>';
                }
                if($item->role->name != 'super'){
                    $button .= "<button type=\"button\" onclick=\"dialog_fun('编辑角色信息','" . route('roles.edit', $item->id) . "')\" class=\"btn btn-sm btn-info\">编辑</button>";
                    $button .= '<button type="button" class="btn btn-sm btn-info dropdown-toggle"
                                                    data-toggle="dropdown" aria-expanded="false">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a onclick="confirm_fun(\'禁用记录\',\'' . route('roles.disable', $item->id) . '\')" href="#">禁用</a></li>
                                                <li><a onclick="confirm_fun(\'启用记录\',\'' . route('roles.enable', $item->id) . '\')" href="#">启用</a></li>
                                                '.$del_btn_str.'
                                            </ul>';
                }else{
                    $num = '-';
                }

                $html   .= '<tr>
                                    <td><input class="check-item" type="checkbox" value="' . $item->id . '"></td>
                                    <td>' . $item->name . '</td>
                                    <td>' . $item->desc . '</td>
                                    <td>' . $num . '</td>
                                    <td>' . $item->created_at . '</td>
                                    <td>' . $item->statusItem($item->status, true) . '</td>
                                    <td>
                                        <div class="btn-group">
                                            ' . $button . '
                                        </div>
                                    </td>
                                </tr>';
            }
            $page = html_entity_decode($roles->links());

            //var_dump($page);
            $total = $roles->total();

            return response()->json([
                'html'  => $html,
                'page'  => $page,
                'total' => $total,
            ]);
        }
        $role = app($this->repository->model());

        return view('admin.roles.index', compact('role'));
    }

    public function create(MenuRepositoryEloquent $menuRepositoryEloquent)
    {
        $auth_str   = $menuRepositoryEloquent->getAuthArrayNodes();
        $role       = app($this->repository->model());
        $action_url = route('roles.store');
        $method     = 'POST';
        return view('admin.roles.create_and_edit', compact('auth_str', 'action_url', 'method', 'role'));
    }

    /**
     * Store a newly created resource in storage.
     * @param RoleCreateRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(RoleCreateRequest $request, MenuRepositoryEloquent $menuRepositoryEloquent)
    {
        try {

            $this->validator->with($request->input('RoleInfo'))->passesOrFail(ValidatorInterface::RULE_CREATE);

            //$role = $this->repository->create($request->all());
            DB::beginTransaction();
            $role     = Role::create([
                'name'       => uniqid(),
                'guard_name' => 'admin',
            ]);
            $response = [
                'message' => trans('添加成功'),
                'data'    => $role->toArray(),
            ];
            if (isset($role->id)) {
                $data            = $request->input('RoleInfo');
                $data['role_id'] = $role->id;
                $role = RoleInfo::create($data);
            }

//            dd($role->id);
            $this->repository->syncPermission($role->id, $request->all(), $menuRepositoryEloquent);
            DB::commit();
            if ($request->wantsJson()) {

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

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(MenuRepositoryEloquent $menuRepositoryEloquent,$id)
    {
        $role = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json(['data' => $role,]);
        }
        $auth_str   = $menuRepositoryEloquent->getAuthArrayNodes($id);
        return view('admin.roles.show', compact('auth_str','role'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(MenuRepositoryEloquent $menuRepositoryEloquent, $id)
    {
        $role       = $this->repository->find($id);
        $auth_str   = $menuRepositoryEloquent->getAuthArrayNodes($id);
        $action_url = route('roles.update', $id);
        $method     = 'PUT';
        return view('admin.roles.create_and_edit', compact('role', 'action_url', 'method', 'auth_str'));
    }

    /**
     * Update the specified resource in storage.
     * @param RoleUpdateRequest $request
     * @param string $id
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(RoleUpdateRequest $request, MenuRepositoryEloquent $menuRepositoryEloquent, $id)
    {
        try {
            $rules = $this->validator->getRules();
            $rules[ValidatorInterface::RULE_UPDATE]['name'] = 'required|unique:auth_infos,name,'.$id;
            $this->validator->setRules($rules);
            $this->validator->with($request->input('RoleInfo'))->passesOrFail(ValidatorInterface::RULE_UPDATE);
            $role = $this->repository->update($request->input('RoleInfo'), $id);

            $response = [
                'message' => trans('修改成功'),
                'data'    => $role->toArray(),
            ];
            $this->repository->syncPermission($id, $request->all(), $menuRepositoryEloquent);
            if ($request->wantsJson()) {

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

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
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

        Log::createAdminLog(Log::EDIT_TYPE, '角色管理 禁用记录');
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

        Log::createAdminLog(Log::EDIT_TYPE, '角色管理 启用记录');
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($this->repository->allowDelete($id)){
            $deleted = $this->repository->delete($id);
            if($deleted){

            }
        }

        if (request()->wantsJson()) {

            return response()->json([
                'message' => '删除成功',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', '角色管理 删除记录');
    }
}
