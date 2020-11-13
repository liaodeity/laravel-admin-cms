<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Log;
use App\Entities\Order;
use App\Entities\OrderProduct;
use App\Exports\OrderDetailExport;
use App\Exports\OrderExport;
use App\Http\Controllers\Controller;
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
 * Class OrderPendingController.
 * @package namespace App\Http\Controllers;
 */
class OrderPendingController extends Controller
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
     * @param OrderValidator  $validator
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
        if (!check_admin_permission ('show orderPending')) {
            abort (403, trans ('禁止访问，无权限'));
        }

        $this->repository->pushCriteria (app ('Prettus\Repository\Criteria\RequestCriteria'));

        if (request ()->wantsJson ()) {
            $orderBy = $request->_order_by ?? 'orders.id DESC';
            QueryWhere::setRequest ($request);
            $M       = app ($this->repository->model ())
                ->select('orders.*')
                ->join('agents','orders.agent_id','=','agents.id')
                ->whereIn ('orders.status', [
                    Order::NO_PAY_STATUS,
                    Order::NO_DELIVERY_STATUS,
                    Order::YES_DELIVERY_STATUS,
                ]);
            QueryWhere::like ($M, 'order_no');
            QueryWhere::like ($M, 'agents.agent_name');
            QueryWhere::like($M, 'agents.company_name');
            QueryWhere::date ($M, 'orders.created_at');
            QueryWhere::eq ($M, 'orders.is_account_pay');
            QueryWhere::eq ($M, 'orders.is_effective',1);
            QueryWhere::orderBy ($M, $orderBy);
            $orders = $M->paginate ();
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

                $button .= get_auth_show_button ('show orderPending', route ('orders.show', $item->id));
                if (Order::isNoPay ($item->status)) {
                    //未付款
                    $button .= get_auth_show_button ('verify orderPending', route ('orderPending.verify', $item->id), '审核订单', '审核订单');
                }
                if (Order::isShowQrcode ($item->status)) {
                    $button .= get_auth_show_button ('qrcode orderPending', route ('orders.qrcode', $item->id), '查看订单二维码', '二维码');
                }
                if (Order::isDeal ($item->status)) {
                    $button .= get_auth_show_button ('deal orderPending', route ('orderPending.deal', $item->id), '处理订单', '处理订单');
                }
                $button .= get_auth_confirm_button('down orderPending', route('orderPending.export-order', $item->id), '确认导出当前订单明细？', '导出订单','confirm_export_fun');

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
        Log::createAdminLog (Log::SHOW_TYPE, '待处理订单 查看记录');

        return view ('admin.orderPending.index', compact ('order', 'buttonHtml'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create ()
    {
        if (!check_admin_permission ('create orderPending')) {
            abort (403, trans ('禁止访问，无权限'));
        }
        $method     = 'POST';
        $action_url = route ('orderPending.store');
        $order      = app ($this->repository->model ());

        return view ('admin.orderPending.create_and_edit', compact ('order', 'action_url', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     * @param OrderCreateRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store (OrderCreateRequest $request)
    {
        if (!check_admin_permission ('create orderPending')) {
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
            Log::createAdminLog (Log::ADD_TYPE, '待处理订单 创建记录');
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
                    'message' => $e->getMessage (),
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show ($id)
    {
        if (!check_admin_permission ('show orderPending')) {
            abort (403, trans ('禁止访问，无权限'));
        }
        $order = $this->repository->find ($id);

        if (request ()->wantsJson ()) {

            return response ()->json ([
                'data' => $order,
            ]);
        }

        return view ('admin.orderPending.show', compact ('order'));
    }

    /**
     * 二维码 add by gui
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function qrcode ($id)
    {
        if (!check_admin_permission ('show orderPending')) {
            abort (403, trans ('禁止访问，无权限'));
        }
        $order = $this->repository->find ($id);

        if (request ()->wantsJson ()) {

            return response ()->json ([
                'data' => $order,
            ]);
        }


        return view ('admin.orderPending.qrcode', compact ('order'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit ($id)
    {
        if (!check_admin_permission ('edit orderPending')) {
            abort (403, trans ('禁止访问，无权限'));
        }
        $order      = $this->repository->find ($id);
        $method     = 'PUT';
        $action_url = route ('orderPending.update', $id);

        return view ('admin.orderPending.create_and_edit', compact ('order', 'action_url', 'method'));
    }

    /**
     * 审核订单
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function verify ($id)
    {
        if (!check_admin_permission ('verify orderPending')) {
            abort (403, trans ('禁止访问，无权限'));
        }
        $order      = $this->repository->find ($id);
        $method     = 'PUT';
        $action_url = route ('orderPending.update', $id);

        return view ('admin.orderPending.verify', compact ('order', 'action_url', 'method'));
    }

    /**
     * 处理订单
     * add by gui
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function deal ($id)
    {
        if (!check_admin_permission ('deal orderPending')) {
            abort (403, trans ('禁止访问，无权限'));
        }
        $order      = $this->repository->find ($id);
        $method     = 'PUT';
        $action_url = route ('orderPending.update', $id);

        return view ('admin.orderPending.deal', compact ('order', 'action_url', 'method'));
    }

    /**
     * Update the specified resource in storage.
     * @param OrderUpdateRequest $request
     * @param string             $id
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update (OrderUpdateRequest $request, $id)
    {
        if (!check_admin_permission ('edit orderPending')) {
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
            $this->repository->saveOrderDeal ($id, $input);


            $input = $request->input ('OrderProduct');
            $this->repository->saveOrderProduct ($id, $input);


            //            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_UPDATE);

            //            $order = $this->repository->update($input, $id);


            $response = [
                'message' => trans ('修改成功'),
                'data'    => [],
            ];
            Log::createAdminLog (Log::EDIT_TYPE, '待处理订单 修改记录');
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
                    'message' => $e->getMessage (),
                ]);
            }
        }
    }


    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param int     $id
     * @return \Illuminate\Http\Response
     */
    public function destroy (Request $request, $id)
    {
        if (!check_admin_permission ('delete orderPending')) {
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

        Log::createAdminLog (Log::DELETE_TYPE, '待处理订单 删除记录');
        if (request ()->wantsJson ()) {

            return response ()->json ([
                'message' => trans ('删除成功'),
                'deleted' => $deleted,
            ]);
        }

        return redirect ()->back ()->with ('message', trans ('删除成功'));
    }

    public function exportOrder($id)
    {
        $order = $this->repository->find ($id);

        return Excel::download (new OrderDetailExport($order), '订单记录.xlsx');
    }
}
