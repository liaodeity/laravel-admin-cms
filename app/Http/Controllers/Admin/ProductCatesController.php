<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Log;
use App\Http\Controllers\Controller;
use App\Presenters\ProductCatePresenter;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ProductCateCreateRequest;
use App\Http\Requests\ProductCateUpdateRequest;
use App\Repositories\ProductCateRepositoryEloquent as ProductCateRepository;
use App\Validators\ProductCateValidator;

/**
 * Class ProductCatesController.
 * @package namespace App\Http\Controllers;
 */
class ProductCatesController extends Controller
{
    /**
     * @var ProductCateRepository
     */
    protected $repository;

    /**
     * @var ProductCateValidator
     */
    protected $validator;
    /**
     * @var ProductCatePresenter
     */
    protected $presenter;

    /**
     * ProductCatesController constructor.
     * @param ProductCateRepository $repository
     * @param ProductCateValidator $validator
     */
    public function __construct(ProductCateRepository $repository, ProductCateValidator $validator, ProductCatePresenter $presenter)
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
        if (!check_admin_permission('show productCates')) {
            abort(403, trans('禁止访问，无权限'));
        }

        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        if (request()->wantsJson()) {
            $cate_name    = $request->cate_name ?? '';
            $status       = $request->status ?? '';
            $orderBy      = $request->_order_by ?? 'id desc';
            $productCates = app($this->repository->model());
            if ($cate_name) $productCates = $productCates->where('cate_name', 'like', "%{$cate_name}%");
            if ($status) $productCates = $productCates->where('status', $status);
            if ($orderBy) $productCates = $productCates->orderByRaw($orderBy);
            $productCates = $productCates->paginate();
            $html         = '';

            foreach ($productCates as $item) {
                $button = '';
                $button .= get_auth_show_button('show productCates', route('productCates.show', $item->id));
                $button .= get_auth_edit_button('edit productCates', route('productCates.edit', $item->id));
                if($this->repository->allowDelete($item->id)){
                    $button .= get_auth_delete_button('delete productCates', route('productCates.destroy', $item->id));
                }

                //dd($button);
                $html .= '<tr>
                                    <td><input class="check-item" type="checkbox" value="' . $item->id . '"></td>
                                    <td>' . $item->cate_name . '</td>
                                    <td>' . $item->products()->count() . '</td>
                                    <td>' . $item->created_at . '</td>
                                    <td>' . $item->statusItem($item->status, true) . '</td>
                                    <td>
                                        <div class="btn-group">
                                            ' . $button . '
                                        </div>
                                    </td>
                                </tr>';
            }
            $page = html_entity_decode($productCates->links());

            $total = $productCates->total();

            return response()->json([
                'html'  => $html,
                'page'  => $page,
                'total' => $total,
            ]);
        }
        $buttonHtml  = '';
        $productCate = app($this->repository->model());
        Log::createAdminLog(Log::SHOW_TYPE, '产品分类管理 查看记录');

        return view('admin.productCates.index', compact('productCate', 'buttonHtml'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (!check_admin_permission('create productCates')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $method      = 'POST';
        $action_url  = route('productCates.store');
        $productCate = app($this->repository->model());

        return view('admin.productCates.create_and_edit', compact('productCate', 'action_url', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     * @param ProductCateCreateRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(ProductCateCreateRequest $request)
    {
        if (!check_admin_permission('create productCates')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {
            $input = $request->input('ProductCate');
            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_CREATE);

            $productCate = $this->repository->create($input);

            $response = [
                'message' => trans('添加成功'),
                'data'    => $productCate->toArray(),
            ];
            Log::createAdminLog(Log::ADD_TYPE, '产品分类管理 创建记录');
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
        }
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!check_admin_permission('show productCates')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $productCate = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $productCate,
            ]);
        }

        return view('admin.productCates.show', compact('productCate'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!check_admin_permission('edit productCates')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $productCate = $this->repository->find($id);
        $method      = 'PUT';
        $action_url  = route('productCates.update', $id);

        return view('admin.productCates.create_and_edit', compact('productCate', 'action_url', 'method'));
    }

    /**
     * Update the specified resource in storage.
     * @param ProductCateUpdateRequest $request
     * @param string $id
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(ProductCateUpdateRequest $request, $id)
    {
        if (!check_admin_permission('edit productCates')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {

            $input = $request->input('ProductCate');
            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $productCate = $this->repository->update($input, $id);

            $response = [
                'message' => trans('修改成功'),
                'data'    => $productCate->toArray(),
            ];
            Log::createAdminLog(Log::EDIT_TYPE, '产品分类管理 修改记录');
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
        if (!check_admin_permission('delete productCates')) {
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
        Log::createAdminLog(Log::DELETE_TYPE, '产品分类管理 删除记录');
        if (request()->wantsJson()) {

            return response()->json([
                'message' => trans('删除成功'),
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', trans('删除成功'));
    }
}
