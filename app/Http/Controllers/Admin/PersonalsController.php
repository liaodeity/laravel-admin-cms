<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Admin;
use App\Entities\Log;
use App\Entities\WxAccount;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUpdateRequest;
use App\Presenters\AdminPresenter;
use App\Repositories\AdminRepositoryEloquent as AdminRepository;
use App\Services\ShareQrcodeService;
use App\Validators\AdminValidator;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class PersonalsController.
 * @package namespace App\Http\Controllers;
 */
class PersonalsController extends Controller
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
     * PersonalsController constructor.
     * @param AdminRepository $repository
     * @param AdminValidator $validator
     * @param AdminPresenter $presenter
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
        if (!check_admin_permission('show personals')) {
            abort(403, trans('禁止访问，无权限'));
        }

        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        if (request()->wantsJson()) {
            $name      = $request->name ?? '';
            $orderBy   = $request->_order_by ?? 'sort asc';
            $personals = app($this->repository->model());
            if ($name) $personals = $personals->where('name', 'like', "%{$name}%");
            list($order, $by) = explode(' ', $orderBy);
            if ($orderBy) $personals = $personals->orderBy($order, $by);
            $personals = $personals->paginate();
            $html      = '';

            foreach ($personals as $item) {
                $button = '';
                $button .= get_auth_show_button('show personals', route('personals.show', $item->id));
                $button .= get_auth_edit_button('edit personals', route('personals.edit', $item->id));
                $button .= get_auth_delete_button('delete personals', route('personals.destroy', $item->id));
                //dd($button);
                $html .= '<tr>
                                    <td><input class="check-item" type="checkbox" value="' . $item->id . '"></td>
                                    <td>' . $item->name . '</td>
                                    <td>' . $item->sort . '</td>
                                    <td>' . $item->updated_at . '</td>
                                    <td>
                                        <div class="btn-group">
                                            ' . $button . '
                                        </div>
                                    </td>
                                </tr>';
            }
            $page = html_entity_decode($personals->links());

            $total = $personals->total();

            return response()->json([
                'html'  => $html,
                'page'  => $page,
                'total' => $total,
            ]);
        }
        $buttonHtml = '';
        $admin   = app($this->repository->model());
        Log::createAdminLog(Log::SHOW_TYPE, '账号资料 查看记录');
        return view('admin.personals.index', compact('admin', 'buttonHtml'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (!check_admin_permission('create personals')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $method     = 'POST';
        $action_url = route('personals.store');
        $admin   = app($this->repository->model());

        return view('admin.personals.create_and_edit', compact('admin', 'action_url', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     * @param PersonalCreateRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(PersonalCreateRequest $request)
    {
        if (!check_admin_permission('create personals')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {
            $input = $request->input('Personal');
            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_CREATE);

            $personal = $this->repository->create($input);

            $response = [
                'message' => trans('添加成功'),
                'data'    => $personal->toArray(),
            ];
            Log::createAdminLog(Log::ADD_TYPE, '账号资料 创建记录');
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

            return redirect()->back()->withErrors($e->getMessageBag()->first())->withInput();
        } catch (\ErrorException $e) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $id = get_admin_id();
        if (!check_admin_permission('show personals')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $admin = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $admin,
            ]);
        }
        return view('admin.personals.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!check_admin_permission('edit personals')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $admin   = $this->repository->find($id);
        $method     = 'PUT';
        $action_url = route('personals.update', $id);

        return view('admin.personals.create_and_edit', compact('admin', 'action_url', 'method'));
    }

    /**
     * 修改密码 add by gui
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function password()
    {
        $id = get_admin_id();
        if (!check_admin_permission('password personals')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $admin   = $this->repository->find($id);
        $method     = 'PUT';
        $action_url = route('personals.update', $id);

        return view('admin.personals.password', compact('admin', 'action_url', 'method'));
    }

    /**
     * Update the specified resource in storage.
     * @param PersonalUpdateRequest $request
     * @param string $id
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(AdminUpdateRequest $request, $id)
    {
        $id = get_admin_id();
        if (!check_admin_permission('edit personals')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {

            $input = $request->all();

            $this->repository->savePersonal($id, $input);

            $response = [
                'message' => trans('修改成功'),
                'data'    => [],
                'url'=>route('personals.show', $id)
            ];
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

            return redirect()->back()->withErrors($e->getMessageBag()->first())->withInput();
        } catch (\ErrorException $e) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }


    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (!check_admin_permission('delete personals')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        $id    = $request->id ?? $id;
        $idArr = explode(',', $id);
        foreach ($idArr as $_id) {
            $deleted = $this->repository->delete($_id);
        }

        Log::createAdminLog(Log::DELETE_TYPE, '账号资料 删除记录');
        if (request()->wantsJson()) {

            return response()->json([
                'message' => trans('删除成功'),
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', trans('删除成功'));
    }
    //绑定微信
    public function wx (ShareQrcodeService $shareQrcodeService)
    {
        $id = get_admin_id ();
        $admin      = $this->repository->find ($id);
        $method     = 'PUT';
        $action_url = route ('personals.update', $id);
        try {
            $qrcode_img = $shareQrcodeService->setType ('scan-admin-bind')->getQrcodeUriToAdmin ($id);
        } catch (\ErrorException $e) {
            abort (403, $e->getMessage ());
        }

        return view ('admin.personals.wx', compact ('admin', 'action_url', 'method', 'qrcode_img'));
    }
    /**
     * 解绑微信 add by gui
     */
    public function wxUnbind ()
    {
        $id          = get_admin_id ();
        $admin       = $this->repository->find ($id);
        $wxAccountID = $admin->wxAccount->id ?? '';
        if (empty($wxAccountID)) {
            return response ()->json ([
                'message' => '未绑定微信，无需解绑',
            ]);
        }
        $info = WxAccount::find ($wxAccountID);
        $ret = $info->delete();
        if ($ret) {
            if (request ()->wantsJson ()) {

                return response ()->json ([
                    'message' => trans ('解绑微信成功'),
                ]);
            }
        } else {
            if (request ()->wantsJson ()) {

                return response ()->json ([
                    'error'   => true,
                    'message' => trans ('解绑微信失败'),
                ]);
            }
        }

        return redirect ()->back ()->with ('message', trans ('解绑微信失败'));

    }

    /**
     * 刷新二维码 add by gui
     * @param ShareQrcodeService $shareQrcodeService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function wxNewCode(ShareQrcodeService $shareQrcodeService)
    {
        $id = get_admin_id ();

        $admin      = $this->repository->find ($id);
        $method     = 'PUT';
        $action_url = route ('personals.update', $id);
        try {
            $str = uniqid().':'.time();
            $qrcode_img = $shareQrcodeService->setType ('scan-admin-bind')->setRandom($str)->getQrcodeUriToAdmin ($id);
        } catch (\ErrorException $e) {
            if (request ()->wantsJson ()) {

                return response ()->json ([
                    'error'   => true,
                    'message' => $e->getMessage()
                ]);
            }
            abort (403, $e->getMessage ());
        }
        if (request ()->wantsJson ()) {

            return response ()->json ([
                'message' => '刷新成功',
                'img'=>$qrcode_img
            ]);
        }
        return view ('admin.personals.wx', compact ('admin', 'action_url', 'method', 'qrcode_img'));
    }

    /**
     * 检查绑定 add by gui
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkWxBind()
    {
        $id = get_admin_id();

        $admin = $this->repository->find($id);
        $bind  = $admin->wxAccount->id ?? 0;
        if($bind){
            return response ()->json ([
                'message' => '已绑定',
            ]);
        }
        return response ()->json ([
            'error'   => true,
            'message' => '未绑定'
        ]);
    }
}
