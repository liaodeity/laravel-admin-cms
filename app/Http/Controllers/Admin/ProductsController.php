<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Log;
use App\Entities\ProductCate;
use App\Http\Controllers\Controller;
use App\Libs\QueryWhere;
use App\Presenters\ProductPresenter;
use ErrorException;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Repositories\ProductRepositoryEloquent as ProductRepository;
use App\Validators\ProductValidator;

/**
 * Class ProductsController.
 * @package namespace App\Http\Controllers;
 */
class ProductsController extends Controller
{
    /**
     * @var ProductRepository
     */
    protected $repository;

    /**
     * @var ProductValidator
     */
    protected $validator;
    /**
     * @var ProductPresenter
     */
    protected $presenter;

    /**
     * ProductsController constructor.
     * @param ProductRepository $repository
     * @param ProductValidator  $validator
     */
    public function __construct(ProductRepository $repository, ProductValidator $validator, ProductPresenter $presenter)
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
        if (!check_admin_permission('show products')) {
            abort(403, trans('禁止访问，无权限'));
        }

        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        if (request()->wantsJson()) {
//            $title     = $request->title ?? '';
            $orderBy  = $request->_order_by ?? 'sort ASC';
            QueryWhere::setRequest($request);
            $M = app($this->repository->model());
            QueryWhere::like($M,'title');
            QueryWhere::eq($M,'status');
            QueryWhere::eq($M,'is_develop_member');
            QueryWhere::orderBy($M, $orderBy);
//            if ($title) $products = $products->where('title', 'like', "%{$title}%");
//            if ($title) $products = $products->where('title', 'like', "%{$title}%");
//            if ($orderBy) $products = $products->orderByRaw($orderBy);

            $products = $M->paginate();
            $html     = '';

            foreach ($products as $item) {
                $button = '';
                $button .= get_auth_show_button('show products', route('products.show', $item->id));
                $button .= get_auth_show_button('video products', route('products.video', $item->id),'查看视频信息','视频');
                $button .= get_auth_edit_button('edit products', route('products.edit', $item->id));
                if ($this->repository->allowDelete ($item->id)) {
                    $button .= get_auth_delete_button ('delete products', route ('products.destroy', $item->id));
                }
                //dd($button);
                $product = $item;
                $count   = $item->prices->count();
                $html    .= view('common.product_item', compact('product', 'button', 'count'))->render();
                //$html .= '<tr>
                //                    <td><input class="check-item" type="checkbox" value="' . $item->id . '"></td>
                //                    <td>' . $item->name . '</td>
                //                    <td>' . $item->sort . '</td>
                //                    <td>' . $item->updated_at . '</td>
                //                    <td>
                //                        <div class="btn-group">
                //                            ' . $button . '
                //                        </div>
                //                    </td>
                //                </tr>';
            }
            $page = html_entity_decode($products->links());

            $total = $products->total();

            return response()->json([
                'html'  => $html,
                'page'  => $page,
                'total' => $total,
            ]);
        }
        $buttonHtml = '';
        $product    = app($this->repository->model());
        Log::createAdminLog(Log::SHOW_TYPE, '产品管理 查看记录');

        return view('admin.products.index', compact('product', 'buttonHtml'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (!check_admin_permission('create products')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $method     = 'POST';
        $action_url = route('products.store');
        $product    = app($this->repository->model());
        $cates      = ProductCate::where('status', 1)->get();

        return view('admin.products.create_and_edit', compact('product', 'action_url', 'method', 'cates'));
    }

    /**
     * Store a newly created resource in storage.
     * @param ProductCreateRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(ProductCreateRequest $request)
    {
        if (!check_admin_permission('create products')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {
            $input = $request->input('Product');
            input_default($input);
            $content = $input['content'];
            $input['content']  =strip_tags($input['content'],'<img><a>');
            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_CREATE);
            $input['content'] = $content;
            if(!check_color($input['card_background'])){
                throw new ErrorException('卡片底色 格式不正确');
            }
            $input['is_develop_member'] = $input['is_develop_member'] ?? 0;
            $product      = $this->repository->create($input);
            $ProductPrice = $request->input('ProductPrice');
            $this->repository->createProductPrice($product->id, $ProductPrice);
            $response = [
                'message' => trans('添加成功'),
                'data'    => $product->toArray(),
            ];
            Log::createAdminLog(Log::ADD_TYPE, '产品管理 创建记录');
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
    public function show($id)
    {
        if (!check_admin_permission('show products')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $product = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $product,
            ]);
        }
        $count        = $product->prices->count();
        $button       = '';
        $product_html = view('common.product_show', compact('product', 'button', 'count'))->render();

        return view('admin.products.show', compact('product', 'product_html'));
    }
    /**
     * 商品视频
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function video($id)
    {
        if (!check_admin_permission('show products')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $product = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $product,
            ]);
        }

        return view('admin.products.video', compact('product'));
    }
    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!check_admin_permission('edit products')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $product    = $this->repository->find($id);
        $method     = 'PUT';
        $action_url = route('products.update', $id);
        $cates      = ProductCate::where('status', 1)->get();

        return view('admin.products.create_and_edit', compact('product', 'action_url', 'method', 'cates'));
    }

    /**
     * Update the specified resource in storage.
     * @param ProductUpdateRequest $request
     * @param string               $id
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(ProductUpdateRequest $request, $id)
    {
        if (!check_admin_permission('edit products')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {

            $input = $request->input('Product');
            input_default($input);
            $content = $input['content'];
            $input['content']  =strip_tags($input['content'],'<img><a>');
            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_UPDATE);
            $input['content'] = $content;
            if(!check_color($input['card_background'])){
                throw new ErrorException('卡片底色 格式不正确');
            }
            $input['is_develop_member'] = $input['is_develop_member'] ?? 0;
            $product      = $this->repository->update($input, $id);
            $ProductPrice = $request->input('ProductPrice');
            $this->repository->createProductPrice($product->id, $ProductPrice);
            $response = [
                'message' => trans('修改成功'),
                'data'    => $product->toArray(),
            ];
            Log::createAdminLog(Log::EDIT_TYPE, '产品管理 修改记录');
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
     * @param Request $request
     * @param int     $id
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request, $id)
    {
        if (!check_admin_permission('delete products')) {
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
            $ret = app($this->repository->model())->where('id', $_id)->update(['status' => 4]);
        }

        Log::createAdminLog(Log::EDIT_TYPE, '商品管理 下架记录');
        if (request()->wantsJson()) {

            return response()->json([
                'message' => trans('下架成功'),
                'ret'     => $ret,
            ]);
        }

        return redirect()->back()->with('message', trans('下架成功'));
    }

    /**
     * @param Request $request
     * @param int     $id
     * @return \Illuminate\Http\Response
     */
    public function enable(Request $request, $id)
    {
        if (!check_admin_permission('delete products')) {
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
            $ret = app($this->repository->model())->where('id', $_id)->update(['status' => 1]);
        }

        Log::createAdminLog(Log::EDIT_TYPE, '商品管理 发布记录');
        if (request()->wantsJson()) {

            return response()->json([
                'message' => trans('发布成功'),
                'ret'     => $ret,
            ]);
        }

        return redirect()->back()->with('message', trans('发布成功'));
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param int     $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        if (!check_admin_permission('delete products')) {
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
        Log::createAdminLog(Log::DELETE_TYPE, '产品管理 删除记录');
        if (request()->wantsJson()) {

            return response()->json([
                'message' => trans('删除成功'),
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', trans('删除成功'));
    }
}
