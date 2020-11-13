<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Log;
use App\Entities\Order;
use App\Entities\OrderSale;
use App\Http\Controllers\Controller;
use App\Libs\QueryWhere;
use App\Presenters\OrderQrcodePresenter;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\OrderQrcodeCreateRequest;
use App\Http\Requests\OrderQrcodeUpdateRequest;
use App\Repositories\OrderQrcodeRepositoryEloquent as OrderQrcodeRepository;
use App\Validators\OrderQrcodeValidator;

/**
 * Class OrderQrcodesController.
 * @package namespace App\Http\Controllers;
 */
class OrderQrcodesController extends Controller
{
    /**
     * @var OrderQrcodeRepository
     */
    protected $repository;

    /**
     * @var OrderQrcodeValidator
     */
    protected $validator;
    /**
     * @var OrderQrcodePresenter
     */
    protected $presenter;

    /**
     * OrderQrcodesController constructor.
     * @param OrderQrcodeRepository $repository
     * @param OrderQrcodeValidator $validator
     */
    public function __construct(OrderQrcodeRepository $repository, OrderQrcodeValidator $validator, OrderQrcodePresenter $presenter)
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
        if (!check_admin_permission('show orderQrcodes')) {
            abort(403, trans('禁止访问，无权限'));
        }

        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        if (request()->wantsJson()) {
            //$name         = $request->name ?? '';
            $orderBy = $request->_order_by ?? 'order_qrcodes.id asc';
            QueryWhere::setRequest($request);
            $M = app($this->repository->model())
                ->select('order_qrcodes.*')
                ->join('orders', 'order_qrcodes.order_id', '=', 'orders.id')
                ->join('agents', 'order_qrcodes.agent_id', '=', 'agents.id')
                ->leftJoin('order_sales','order_qrcodes.order_sale_id','=','order_sales.id')
                ->join('order_products', 'order_qrcodes.order_product_id', '=', 'order_products.id')
                ->join('products', 'products.id', '=', 'order_products.product_id');

            QueryWhere::like($M, 'qrcode_no');
            QueryWhere::like($M, 'orders.order_no');
            QueryWhere::like($M, 'order_sales.sale_no');
            QueryWhere::like($M, 'agents.agent_name');
            QueryWhere::like($M, 'agents.company_name');
            QueryWhere::like($M, 'products.title');
            QueryWhere::date($M, 'orders.created_at');
            QueryWhere::eq($M, 'orders.status');
            QueryWhere::eq($M, 'order_qrcodes.status',1);
            QueryWhere::orderBy($M, $orderBy);
            $orderQrcodes = $M->paginate();
            $html         = '';

            foreach ($orderQrcodes as $item) {
                $button = '';
                $button .= get_auth_show_button('show orderQrcodes', route('orderQrcodes.show', $item->id));
                $button .= get_auth_confirm_button('down orderQrcodes', route('orderQrcodes.down', $item->id), '是不是下载二维码？', '下载', 'down_qrcode');
//                $button .= get_auth_delete_button ('delete orderQrcodes', route ('orderQrcodes.destroy', $item->id));
                //dd($button);
                $html .= '<tr>
                                    <td><input class="check-item" type="checkbox" value="' . $item->id . '"></td>
                                    <td>' . $item->qrcode_no . '</td>
                                    <td>' . $item->order->order_no . '</td>
                                    <td>' . $item->agent->agent_name . '</td>
                                    <td>' . $item->orderProduct->title . '</td>
                                    <td>' . $item->specification . '</td>
                                    <td>' . $item->price . '</td>
                                    <td>' . $item->brokerage . '</td>
                                    <td>' . $item->order->created_at . '</td>
                                    <td>' . $item->order->statusItem($item->order->status, true) . '</td>
                                    <td>
                                        <div class="btn-group">
                                            ' . $button . '
                                        </div>
                                    </td>
                                </tr>';
            }
            $page = html_entity_decode($orderQrcodes->links());

            $total = $orderQrcodes->total();

            return response()->json([
                'html'  => $html,
                'page'  => $page,
                'total' => $total,
            ]);
        }
        $buttonHtml  = '';
        $orderQrcode = app($this->repository->model());
        $order       = new Order();
        Log::createAdminLog(Log::SHOW_TYPE, '二维码 查看记录');

        return view('admin.orderQrcodes.index', compact('orderQrcode', 'buttonHtml', 'order'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (!check_admin_permission('create orderQrcodes')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $method      = 'POST';
        $action_url  = route('orderQrcodes.store');
        $orderQrcode = app($this->repository->model());

        return view('admin.orderQrcodes.create_and_edit', compact('orderQrcode', 'action_url', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     * @param OrderQrcodeCreateRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(OrderQrcodeCreateRequest $request)
    {
        if (!check_admin_permission('create orderQrcodes')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {
            $input = $request->input('OrderQrcode');
            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_CREATE);

            $orderQrcode = $this->repository->create($input);

            $response = [
                'message' => trans('添加成功'),
                'data'    => $orderQrcode->toArray(),
            ];
            Log::createAdminLog(Log::ADD_TYPE, '二维码 创建记录');
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
        if (!check_admin_permission('show orderQrcodes')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $orderQrcode = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $orderQrcode,
            ]);
        }
        $qrcode_content = '';
        $file          = ($orderQrcode->qrcode_path);

        if($file){
            $exists = Storage::exists($file);
            if($exists){
                $qrcode_content = Storage::get($file);
                if($qrcode_content){
                    $qrcode_content = base64_encode($qrcode_content);
                }
            }
        }

        return view('admin.orderQrcodes.show', compact('orderQrcode','qrcode_content'));
    }

    /**
     * 下载二维码 add by gui
     * @param $id
     * @return mixed
     */
    public function down($id)
    {
        if (!check_admin_permission('down orderQrcodes')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $orderQrcode   = $this->repository->find($id);
        $file          = ($orderQrcode->qrcode_path);
        $product_title = $orderQrcode->orderProduct->title ?? '';
        $title         = $product_title . '_' . $orderQrcode->qrcode_no . get_extension($file);
        return Storage::download($file, $title);
    }

    /**
     * 下载不同类型二维码 add by gui
     * @param $orderID
     * @param $type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function downType($orderID, $type)
    {
        if (!check_admin_permission('down orderQrcodes')) {
            abort(403, trans('禁止访问，无权限'));
        }
        switch ($type) {
            case 'Qrcode':
                $type_name = '纯二维码';
                break;
            case 'cardQrcode':
                $type_name = '合格证';
                break;
            default:
                abort(403, trans('下载类型不对，无法下载'));
                break;
        }
        $order = Order::find($orderID);
        try {
            $file = $this->repository->getDownZipPath('order',$orderID, $type);

            $title = $type_name . '_' . $order->order_no . get_extension($file);
            return Storage::download($file, $title);
        } catch (\ErrorException $e) {
            return redirect()->back()->with('message', $e->getMessage());
        }
    }

    /**
     * 下载售后订单二维码 add by gui
     * @param $orderSaleID
     * @param $type
     * @return mixed
     */
    public function downSaleType($orderSaleID, $type)
    {
        if (!check_admin_permission('down orderQrcodes')) {
            abort(403, trans('禁止访问，无权限'));
        }
        switch ($type) {
            case 'Qrcode':
                $type_name = '纯二维码';
                break;
            case 'cardQrcode':
                $type_name = '合格证';
                break;
            default:
                abort(403, trans('下载类型不对，无法下载'));
                break;
        }
        $orderSale = OrderSale::find($orderSaleID);
        try {
            $file = $this->repository->getDownZipPath('sale', $orderSaleID, $type);

            $title = $type_name . '_' . $orderSale->sale_no . get_extension($file);
            return Storage::download($file, $title);

        } catch (\ErrorException $e) {

        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!check_admin_permission('edit orderQrcodes')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $orderQrcode = $this->repository->find($id);
        $method      = 'PUT';
        $action_url  = route('orderQrcodes.update', $id);

        return view('admin.orderQrcodes.create_and_edit', compact('orderQrcode', 'action_url', 'method'));
    }

    /**
     * Update the specified resource in storage.
     * @param OrderQrcodeUpdateRequest $request
     * @param string $id
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(OrderQrcodeUpdateRequest $request, $id)
    {
        if (!check_admin_permission('edit orderQrcodes')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {

            $input = $request->input('OrderQrcode');
            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $orderQrcode = $this->repository->update($input, $id);

            $response = [
                'message' => trans('修改成功'),
                'data'    => $orderQrcode->toArray(),
            ];
            Log::createAdminLog(Log::EDIT_TYPE, '二维码 修改记录');
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
        if (!check_admin_permission('delete orderQrcodes')) {
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

        Log::createAdminLog(Log::DELETE_TYPE, '二维码 删除记录');
        if (request()->wantsJson()) {

            return response()->json([
                'message' => trans('删除成功'),
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', trans('删除成功'));
    }
}
