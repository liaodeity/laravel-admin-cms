<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Log;
use App\Http\Controllers\Controller;
use App\Libs\QueryWhere;
use App\Presenters\BillPresenter;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\BillCreateRequest;
use App\Http\Requests\BillUpdateRequest;
use App\Repositories\BillRepositoryEloquent as BillRepository;
use App\Validators\BillValidator;

/**
 * Class BillsController.
 * @package namespace App\Http\Controllers;
 */
class BillsController extends Controller
{
    /**
     * @var BillRepository
     */
    protected $repository;

    /**
     * @var BillValidator
     */
    protected $validator;
    /**
     * @var BillPresenter
     */
    protected $presenter;

    /**
     * BillsController constructor.
     * @param BillRepository $repository
     * @param BillValidator $validator
     */
    public function __construct(BillRepository $repository, BillValidator $validator, BillPresenter $presenter)
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
        if (!check_admin_permission('show bills')) {
            abort(403, trans('禁止访问，无权限'));
        }

        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        if (request()->wantsJson()) {
            $member_keyword = $request->member_keyword ?? '';
            $orderBy        = $request->_order_by ?? 'bills.bill_at DESC';
            QueryWhere::setRequest($request);
            $M = app($this->repository->model())
                ->select('bills.*')
                ->leftJoin('agents', 'bills.agent_id', '=', 'agents.id')
                ->leftJoin('products', 'bills.product_id', '=', 'products.id')
                ->leftJoin('members', 'bills.member_id', '=', 'members.id');
            QueryWhere::like($M, 'bills.bill_no');
            QueryWhere::like($M, 'agents.agent_name');
            QueryWhere::like($M, 'products.title');
            QueryWhere::date($M, 'bills.bill_at');
            QueryWhere::date($M, 'bills.verity_at');
            QueryWhere::eq($M, 'bills.status');

            if ($member_keyword) {
                $M = $M->where(function ($query) use ($member_keyword) {
                    $query->where('members.member_no', 'like', '%' . $member_keyword . '%')
                        ->orWhere('members.wx_account', 'like', '%' . $member_keyword . '%')
                        ->orWhere('members.wx_name', 'like', '%' . $member_keyword . '%')
                        ->orWhere('members.mobile', 'like', '%' . $member_keyword . '%')
                        ->orWhere('members.real_name', 'like', '%' . $member_keyword . '%');
                });
            }

            QueryWhere::orderBy($M, $orderBy);
            $bills = $M->paginate();
            $html  = '';

            foreach ($bills as $item) {
                $button = '';
                $button .= get_auth_show_button('show bills', route('bills.show', $item->id));
                $button .= get_auth_show_button('show orderQrcodes', route('orderQrcodes.show', $item->qrocde_id),'查看二维码信息','二维码');
//                $button .= get_auth_edit_button('edit bills', route('bills.edit', $item->id));
//                $button .= get_auth_delete_button('delete bills', route('bills.destroy', $item->id));
                $html   .= '<tr>
                                    <td><input class="check-item" type="checkbox" value="' . $item->id . '"></td>
                                    <td>' . $item->bill_no . '</td>
                                    <td>' . ($item->agent->agent_name ?? '') . '</td>
                                    <td>' . ($item->product->title ?? '') . '</td>
                                    <td>' . ($item->member->member_no ?? '') . '</td>
                                    <td>' . ($item->member->real_name ?? '') . '</td>
                                    <td>' . ($item->member->mobile ?? '') . '</td>
                                    <td>' . $item->amount . '</td>
                                    <td>' . $item->created_at . '</td>
                                    <td>' . $item->lat . ',' . $item->lng . '(' . $item->scan_address . ')' . '</td>
                                    <td>' . $item->verity_at . '</td>
                                    <td>' . $item->statusItem($item->status, true) . '</td>
                                    
                                    <td>
                                        <div class="btn-group">
                                            ' . $button . '
                                        </div>
                                    </td>
                                </tr>';
            }
            $page = html_entity_decode($bills->links());

            $total = $bills->total();

            return response()->json([
                'html'  => $html,
                'page'  => $page,
                'total' => $total,
            ]);
        }
        $buttonHtml = '';
        $bill       = app($this->repository->model());
        Log::createAdminLog(Log::SHOW_TYPE, '佣金管理 查看记录');

        return view('admin.bills.index', compact('bill', 'buttonHtml'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (!check_admin_permission('create bills')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $method     = 'POST';
        $action_url = route('bills.store');
        $bill       = app($this->repository->model());

        return view('admin.bills.create_and_edit', compact('bill', 'action_url', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     * @param BillCreateRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(BillCreateRequest $request)
    {
        if (!check_admin_permission('create bills')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {
            $input = $request->input('Bill');
            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_CREATE);

            $bill = $this->repository->create($input);

            $response = [
                'message' => trans('添加成功'),
                'data'    => $bill->toArray(),
            ];
            Log::createAdminLog(Log::ADD_TYPE, '佣金管理 创建记录');
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
        if (!check_admin_permission('show bills')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $bill = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $bill,
            ]);
        }
        $member = $bill->member;
        return view('admin.bills.show', compact('bill','member'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!check_admin_permission('edit bills')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $bill       = $this->repository->find($id);
        $method     = 'PUT';
        $action_url = route('bills.update', $id);

        return view('admin.bills.create_and_edit', compact('bill', 'action_url', 'method'));
    }

    /**
     * Update the specified resource in storage.
     * @param BillUpdateRequest $request
     * @param string $id
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(BillUpdateRequest $request, $id)
    {
        if (!check_admin_permission('edit bills')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {

            $input = $request->input('Bill');
            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $bill = $this->repository->update($input, $id);

            $response = [
                'message' => trans('修改成功'),
                'data'    => $bill->toArray(),
            ];
            Log::createAdminLog(Log::EDIT_TYPE, '佣金管理 修改记录');
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
        if (!check_admin_permission('delete bills')) {
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

        Log::createAdminLog(Log::DELETE_TYPE, '佣金管理 删除记录');
        if (request()->wantsJson()) {

            return response()->json([
                'message' => trans('删除成功'),
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', trans('删除成功'));
    }
}
