<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Log;
use App\Entities\Order;
use App\Entities\OrderSale;
use App\Http\Controllers\Controller;
use App\Libs\QueryWhere;
use App\Presenters\OrderSalePresenter;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\OrderSaleCreateRequest;
use App\Http\Requests\OrderSaleUpdateRequest;
use App\Repositories\OrderSaleRepositoryEloquent as OrderSaleRepository;
use App\Validators\OrderSaleValidator;

/**
 * Class orderSalesController.
 * @package namespace App\Http\Controllers;
 */
class orderSalesController extends Controller
{
    /**
     * @var OrderSaleRepository
     */
    protected $repository;

    /**
     * @var OrderSaleValidator
     */
    protected $validator;
    /**
     * @var OrderSalePresenter
     */
    protected $presenter;

    /**
     * orderSalesController constructor.
     * @param OrderSaleRepository $repository
     * @param OrderSaleValidator $validator
     */
    public function __construct(OrderSaleRepository $repository, OrderSaleValidator $validator, OrderSalePresenter $presenter)
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
        if (!check_admin_permission('show orderSales')) {
            abort(403, trans('禁止访问，无权限'));
        }

        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        if (request()->wantsJson()) {
            $orderBy = $request->_order_by ?? 'order_sales.id desc';
            QueryWhere::setRequest($request);
            $M = app($this->repository->model())
                ->select('order_sales.*')
                ->join('orders', 'order_sales.order_id', '=', 'orders.id')
                ->join('agents', 'order_sales.agent_id', '=', 'agents.id');
            QueryWhere::like($M, 'order_sales.sale_no');
            QueryWhere::like($M, 'orders.order_no');
            QueryWhere::like($M, 'agents.agent_name');
            QueryWhere::like($M, 'agents.company_name');
            QueryWhere::date($M, 'order_sales.apply_sale_at');
            QueryWhere::eq($M, 'order_sales.status');
            QueryWhere::orderBy($M, $orderBy);
            $orderSales = $M->paginate();
            $html       = '';

            foreach ($orderSales as $item) {
                $button = '';
                $button .= get_auth_show_button('show orderSales', route('orderSales.show', $item->id));
                $button .= get_auth_show_button('show orderSales', route('orders.show', $item->order_id), '查看原始订单', '原始订单');
                if ($item->status == 2) {
                    $button .= get_auth_show_button('deal orderSales', route('orderSales.deal', $item->id), '处理售后', '处理售后');
                }
                if (OrderSale::COMPLETE_STATUS == $item->status || OrderSale::NO_DEAL == $item->status ) {
                    $button .= get_auth_show_button ('qrcode orderSales', route ('orderSales.qrcode', $item->id), '查看售后订单二维码', '二维码');
                }
                //                $button .= get_auth_delete_button('delete orderSales', route('orderSales.destroy', $item->id));
                //dd($button);
                $html .= '<tr>
                                    <td><input class="check-item" type="checkbox" value="' . $item->id . '"></td>
                                    <td>' . $item->sale_no . '</td>
                                    <td>' . $item->order->order_no . '</td>
                                    <td>' . $item->agent->agent_name . '</td>
                                    <td>' . $item->order->created_at . '</td>
                                    <td>' . $item->apply_sale_at . '</td>
                                    <td>' . $item->sale_amount . '</td>
                                    <td>' . $item->statusItem($item->status, true) . '</td>
                                    <td>
                                        <div class="btn-group">
                                            ' . $button . '
                                        </div>
                                    </td>
                                </tr>';
            }
            $page = html_entity_decode($orderSales->links());

            $total = $orderSales->total();

            return response()->json([
                'html'  => $html,
                'page'  => $page,
                'total' => $total,
            ]);
        }
        $buttonHtml = '';
        $orderSale  = app($this->repository->model());

        Log::createAdminLog(Log::SHOW_TYPE, '售后订单管理 查看记录');

        return view('admin.orderSales.index', compact('orderSale', 'buttonHtml'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (!check_admin_permission('create orderSales')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $method     = 'POST';
        $action_url = route('orderSales.store');
        $orderSale  = app($this->repository->model());

        return view('admin.orderSales.create_and_edit', compact('orderSale', 'action_url', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     * @param OrderSaleCreateRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(OrderSaleCreateRequest $request)
    {
        if (!check_admin_permission('create orderSales')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {
            $input = $request->input('OrderSale');
            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_CREATE);

            $orderSale = $this->repository->create($input);

            $response = [
                'message' => trans('添加成功'),
                'data'    => $orderSale->toArray(),
            ];
            Log::createAdminLog(Log::ADD_TYPE, '售后订单管理 创建记录');
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
        if (!check_admin_permission('show orderSales')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $orderSale = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $orderSale,
            ]);
        }

        return view('admin.orderSales.show', compact('orderSale'));
    }

    /**
     * 二维码 add by gui
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function qrcode ($id)
    {

        if (!check_admin_permission ('show orders')) {
            abort (403, trans ('禁止访问，无权限'));
        }
        $orderSale = $this->repository->find ($id);

        if (request ()->wantsJson ()) {

            return response ()->json ([
                'data' => $orderSale,
            ]);
        }
        $orderSale_look_url = route('orderQrcodes.index','sale_no='.$orderSale->sale_no);
        $down_card_url = route('orderQrcodes.downSaleType',[$id,'cardQrcode']);
        $down_qr_url = route('orderQrcodes.downSaleType',[$id,'Qrcode']);
        $progress_url = route('orderSales.progress',$id);
        $update_url = route('orderSales.update',$id);
        //使用与订单同一个二维码处理
        return view ('admin.orderSales.qrcode', compact ('orderSale','update_url','progress_url','down_card_url','down_qr_url','orderSale_look_url'));
    }
    /**
     * 二维码生成进度
     * add by gui
     */
    public function progress($id)
    {
        $orderSale = $this->repository->find ($id);
        $all_number = $orderSale->getQrCodeNumber();//总算量
        $yes_number = $orderSale->qrcodeSuccessCount();//已生成数量
        $files = Storage::allFiles('cardQrcode/' . $orderSale->sale_no);//文件大概数量

        $rate       = $all_number > 0 ? ($yes_number / $all_number) * 100 : 0;
        if($rate < 100){

            $file_number = count($files);//
            $rate       = $all_number > 0 ? ($file_number / $all_number) * 100 : 0;
            $yes_number = $file_number;
        }
        $no_number  = $all_number - $yes_number;
        if($no_number < 0){
            $no_number =0;
        }
        $rate = $rate > 0 ? $rate : 0;
        $rate = round($rate,2);
        if (request()->wantsJson()) {

            $data = [
                'yes_number' => $yes_number > 0 ? $yes_number : 0,
                'no_number'  => $no_number > $yes_number ? $yes_number : $no_number,
                'rate'       => $rate > 100 ? 100 : $rate
            ];
            return response()->json([
                'message' => trans('获取成功'),
                'result'    => $data,
            ]);
        }
    }
    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!check_admin_permission('edit orderSales')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $orderSale  = $this->repository->find($id);
        $method     = 'PUT';
        $action_url = route('orderSales.update', $id);

        return view('admin.orderSales.create_and_edit', compact('orderSale', 'action_url', 'method'));
    }

    /**
     * 处理售后订单 add by gui
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function deal($id)
    {
        if (!check_admin_permission('deal orderSales')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $orderSale  = $this->repository->find($id);
        $method     = 'PUT';
        $action_url = route('orderSales.update', $id);

        return view('admin.orderSales.deal', compact('orderSale', 'action_url', 'method'));
    }

    /**
     * Update the specified resource in storage.
     * @param OrderSaleUpdateRequest $request
     * @param string $id
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(OrderSaleUpdateRequest $request, $id)
    {
        if (!check_admin_permission('edit orderSales')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {

//            $input = $request->input('OrderSale');
//            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_UPDATE);

//            $orderSale = $this->repository->update($input, $id);
            $input = $request->all();
            if(isset($input['OrderSale'])){
                $this->repository->saveOrderSaleDeal($id, $input);
            }


            $input = $request->all ();
            if(isset($input['OrderQrcode'])){
                $this->repository->saveGenerateQrcode ($id, $input);
                $msg = '生成二维码成功';
            }


            $response = [
                'message' => $msg ?? '修改成功',
                'data'    => [],
            ];
            Log::createAdminLog(Log::EDIT_TYPE, '售后订单管理 修改记录');
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
            if ($request->wantsJson ()) {

                return response ()->json ([
                    'error'   => true,
                    'message' => $e->getMessage (),
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
        if (!check_admin_permission('delete orderSales')) {
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

        Log::createAdminLog(Log::DELETE_TYPE, '售后订单管理 删除记录');
        if (request()->wantsJson()) {

            return response()->json([
                'message' => trans('删除成功'),
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', trans('删除成功'));
    }
}
