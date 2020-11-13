<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Log;
use App\Entities\Order;
use App\Entities\OrderProduct;
use App\Entities\OrderSale;
use App\Exports\AgentExport;
use App\Exports\OrderDetailExport;
use App\Exports\OrderExport;
use App\Http\Controllers\Controller;
use App\Libs\ExportMame;
use App\Libs\QueryWhere;
use App\Presenters\OrderPresenter;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\OrderCreateRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Repositories\OrderRepositoryEloquent as OrderRepository;
use App\Validators\OrderValidator;

/**
 * Class OrdersController.
 * @package namespace App\Http\Controllers;
 */
class OrdersController extends Controller
{
    /**
     * @var OrderRepository
     */
    protected $repository;

    /**
     * @var OrderValidator
     */
    protected $validator;
    /**
     * @var OrderPresenter
     */
    protected $presenter;

    /**
     * OrdersController constructor.
     * @param OrderRepository $repository
     * @param OrderValidator $validator
     */
    public function __construct (OrderRepository $repository, OrderValidator $validator, OrderPresenter $presenter)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->presenter  = $presenter;
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request)
    {
        if (!check_admin_permission ('show orders')) {
            abort (403, trans ('禁止访问，无权限'));
        }

        $this->repository->pushCriteria (app ('Prettus\Repository\Criteria\RequestCriteria'));

        if (request ()->wantsJson ()) {
            $orders = $this->getData ($request, false);
            $html   = '';

            foreach ($orders as $item) {
                $button = '';

                $sale_button = '';
                //售后按钮

                $info = $item->sales ()->orderBy ('apply_sale_at', 'desc')->first ();
                //不存在未处理售后
                if ($info && isset($info->status)) {
                    $status_text = $info->statusItem ($info->status);
                    $sale_button .= get_auth_html ('show orderSales', $status_text, '<a class="btn btn-sm btn-link" href="' . route ('orderSales.index', 'order_no=' . $item->order_no) . '" >' . $status_text . '</a>');
                }

                $button .= get_auth_show_button ('show orders', route ('orders.show', $item->id));
                if (Order::isShowQrcode ($item->status)) {
                    $button .= get_auth_show_button ('qrcode orders', route ('orders.qrcode', $item->id), '查看订单二维码', '二维码');
                }

                $button .= get_auth_confirm_button ('down orders', route ('orders.export-order', $item->id), '确认导出当前订单明细？', '导出订单', 'confirm_export_fun');

                //dd($button);
                $html .= '<tr>
                                    <td><input class="check-item" type="checkbox" value="' . $item->id . '"></td>
                                    <td>' . $item->order_no . '</td>
                                    <td>' . $item->agent->agent_name . '</td>
                                    <td>' . $item->order_amount . '</td>
                                    <td>' . $item->created_at . '</td>
                                    <td>' . $item->delivery_at . '</td>
                                    <td>' . $item->statusItem ($item->status, true) . '</td>
                                    <td>' . $sale_button . '</td>
                                    <td>
                                        <div class="btn-group">
                                            ' . $button . '
                                        </div>
                                    </td>
                                </tr>';
            }
            $page = html_entity_decode ($orders->links ());

            $total = $orders->total ();

            return response ()->json ([
                'html'  => $html,
                'page'  => $page,
                'total' => $total,
            ]);
        }
        $buttonHtml = '';
        $order      = app ($this->repository->model ());
        Log::createAdminLog (Log::SHOW_TYPE, '订单信息 查看记录');

        return view ('admin.orders.index', compact ('order', 'buttonHtml'));
    }

    public function getData ($request, $export = false)
    {
        $orderBy = $request->_order_by ?? 'orders.id asc';
        QueryWhere::setRequest ($request);
        $M = app ($this->repository->model ())
            ->select ('orders.*')
            ->join ('agents', 'orders.agent_id', '=', 'agents.id')
            ->whereIn ('orders.status', [
                Order::COMPLETE_STATUS,
                Order::CANCEL_STATUS,
            ]);
        QueryWhere::like ($M, 'orders.order_no');
        QueryWhere::like ($M, 'agents.agent_name');
        QueryWhere::like ($M, 'agents.company_name');
        QueryWhere::date ($M, 'orders.created_at');
        QueryWhere::eq ($M, 'orders.status');
        QueryWhere::eq ($M, 'orders.is_account_pay');
        QueryWhere::eq ($M, 'orders.is_effective',1);
        QueryWhere::orderBy ($M, $orderBy);
        if ($export === true) {
            $orders = $M->get ();
        } else {
            $orders = $M->paginate ();
        }

        return $orders;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create ()
    {
        if (!check_admin_permission ('create orders')) {
            abort (403, trans ('禁止访问，无权限'));
        }
        $method     = 'POST';
        $action_url = route ('orders.store');
        $order      = app ($this->repository->model ());

        return view ('admin.orders.create_and_edit', compact ('order', 'action_url', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     * @param OrderCreateRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store (OrderCreateRequest $request)
    {
        if (!check_admin_permission ('create orders')) {
            if ($request->wantsJson ()) {

                return response ()->json ([
                    'error'   => true,
                    'message' => trans ('禁止访问，无权限'),
                ]);
            }

            return redirect ()->back ()->withErrors (trans ('禁止访问，无权限'))->withInput ();
        }
        try {
            $input = $request->input ('Order');
            $this->validator->with ($input)->passesOrFail (ValidatorInterface::RULE_CREATE);

            $order = $this->repository->create ($input);

            $response = [
                'message' => trans ('添加成功'),
                'data'    => $order->toArray (),
            ];
            Log::createAdminLog (Log::ADD_TYPE, '订单信息 创建记录');
            if ($request->wantsJson ()) {

                return response ()->json ($response);
            }

            return redirect ()->back ()->with ('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson ()) {
                return response ()->json ([
                    'error'   => true,
                    'message' => $e->getMessageBag ()->first (),
                ]);
            }

            return redirect ()->back ()->withErrors ($e->getMessageBag ()->first ())->withInput ();
        }
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show ($id)
    {
        if (!check_admin_permission ('show orders')) {
            abort (403, trans ('禁止访问，无权限'));
        }
        $order = $this->repository->find ($id);

        if (request ()->wantsJson ()) {

            return response ()->json ([
                'data' => $order,
            ]);
        }

        return view ('admin.orders.show', compact ('order'));
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
        $order = $this->repository->find ($id);

        if (request ()->wantsJson ()) {

            return response ()->json ([
                'data' => $order,
            ]);
        }
        $order_look_url = route ('orderQrcodes.index', 'order_no=' . $order->order_no);
        $down_card_url  = route ('orderQrcodes.downType', [$order->id, 'cardQrcode']);
        $down_qr_url    = route ('orderQrcodes.downType', [$order->id, 'Qrcode']);
        $progress_url   = route ('orders.progress', $id);
        $update_url     = route ('orders.update', $id);
        return view ('admin.orders.qrcode', compact ('order', 'update_url', 'progress_url', 'order_look_url', 'down_card_url', 'down_qr_url'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit ($id)
    {
        if (!check_admin_permission ('edit orders')) {
            abort (403, trans ('禁止访问，无权限'));
        }
        $order      = $this->repository->find ($id);
        $method     = 'PUT';
        $action_url = route ('orders.update', $id);

        return view ('admin.orders.create_and_edit', compact ('order', 'action_url', 'method'));
    }

    /**
     * Update the specified resource in storage.
     * @param OrderUpdateRequest $request
     * @param string $id
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update (OrderUpdateRequest $request, $id)
    {
        if (!check_admin_permission ('edit orders')) {
            if ($request->wantsJson ()) {

                return response ()->json ([
                    'error'   => true,
                    'message' => trans ('禁止访问，无权限'),
                ]);
            }

            return redirect ()->back ()->withErrors (trans ('禁止访问，无权限'))->withInput ();
        }
        try {
            $input = $request->all ();
            $this->repository->saveGenerateQrcode ($id, $input);

            $response = [
                'message' => '生成二维码成功',
                'data'    => []
            ];
            Log::createAdminLog (Log::EDIT_TYPE, '订单信息 修改记录');
            if ($request->wantsJson ()) {

                return response ()->json ($response);
            }

            return redirect ()->back ()->with ('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson ()) {

                return response ()->json ([
                    'error'   => true,
                    'message' => $e->getMessageBag ()->first (),
                ]);
            }

            return redirect ()->back ()->withErrors ($e->getMessageBag ()->first ())->withInput ();
        } catch (\ErrorException $e) {
            if ($request->wantsJson ()) {

                return response ()->json ([
                    'error'   => true,
                    'message' => $e->getMessage ()
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
    public function destroy (Request $request, $id)
    {
        if (!check_admin_permission ('delete orders')) {
            if ($request->wantsJson ()) {

                return response ()->json ([
                    'error'   => true,
                    'message' => trans ('禁止访问，无权限'),
                ]);
            }

            return redirect ()->back ()->withErrors (trans ('禁止访问，无权限'))->withInput ();
        }
        $id    = $request->id ?? $id;
        $idArr = explode (',', $id);
        foreach ($idArr as $_id) {
            $deleted = $this->repository->delete ($_id);
        }

        Log::createAdminLog (Log::DELETE_TYPE, '订单信息 删除记录');
        if (request ()->wantsJson ()) {

            return response ()->json ([
                'message' => trans ('删除成功'),
                'deleted' => $deleted,
            ]);
        }

        return redirect ()->back ()->with ('message', trans ('删除成功'));
    }

    /**
     * 二维码生成进度
     * add by gui
     */
    public function progress ($id)
    {
        $order      = Order::find ($id);
        $all_number = $order->getQrCodeNumber ();//总算量
        $yes_number = $order->qrcodeSuccessCount ();//已生成数量
        $files      = Storage::allFiles ('cardQrcode/' . $order->order_no);//文件大概数量

        $rate = $all_number > 0 ? ($yes_number / $all_number) * 100 : 0;
        if ($rate < 100) {

            $file_number = count ($files);//
            $rate        = $all_number > 0 ? ($file_number / $all_number) * 100 : 0;
            $yes_number  = $file_number;
        }
        $no_number = $all_number - $yes_number;
        if ($no_number < 0) {
            $no_number = 0;
        }
        $rate = $rate > 0 ? $rate : 0;
        $rate = round ($rate, 2);
        if (request ()->wantsJson ()) {

            $data = [
                'yes_number' => $yes_number > 0 ? $yes_number : 0,
                'no_number'  => $no_number > $yes_number ? $yes_number : $no_number,
                'rate'       => $rate > 100 ? 100 : $rate
            ];
            return response ()->json ([
                'message' => trans ('获取成功'),
                'result'  => $data,
            ]);
        }
    }

    public function export (Request $request)
    {
        $order      = $this->getData ($request, true);
        $ExportName = new ExportMame();

        $all = $request->all ();
        if (isset($all['status']) && $all['status']) {
            $M             = new Order();
            $all['status'] = $M->statusItem ($all['status']);
        }
        $export_name = $ExportName->setRequest ($all)->getName ('已处理订单', [
            'order_no'         => '订单编号',
            'agent_name'       => '代理商名称',
            'created_at_start' => '下单时间开始',
            'created_at_end'   => '下单时间结束',
            'status'           => '状态'
        ]);
        //【修改】导出所有订单明细记录
        return Excel::download (new OrderExport($order), $export_name . '.xlsx');
    }

    public function exportOrder ($id)
    {
        $order = $this->repository->find ($id);

        return Excel::download (new OrderDetailExport($order), '导出订单_' . $order->order_no . '.xlsx');
    }
}
