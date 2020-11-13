<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Agent;
use App\Entities\AgentRegion;
use App\Entities\Log;
use App\Entities\Order;
use App\Entities\Region;
use App\Entities\SerialNumber;
use App\Exports\AgentExport;
use App\Exports\MemberExport;
use App\Http\Controllers\Controller;
use App\Libs\ExportMame;
use App\Libs\QueryWhere;
use App\Presenters\AgentPresenter;
use App\Services\AuthLoginService;
use function foo\func;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\AgentCreateRequest;
use App\Http\Requests\AgentUpdateRequest;
use App\Repositories\AgentRepositoryEloquent as AgentRepository;
use App\Validators\AgentValidator;

/**
 * Class AgentsController.
 * @package namespace App\Http\Controllers;
 */
class AgentsController extends Controller
{
    /**
     * @var AgentRepository
     */
    protected $repository;

    /**
     * @var AgentValidator
     */
    protected $validator;
    /**
     * @var AgentPresenter
     */
    protected $presenter;
    /**
     * @var AuthLoginService
     */
    private $authLoginService;

    /**
     * AgentsController constructor.
     * @param AgentRepository $repository
     * @param AgentValidator $validator
     */
    public function __construct(AgentRepository $repository, AgentValidator $validator, AgentPresenter $presenter, AuthLoginService $authLoginService)
    {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->presenter = $presenter;
        $this->authLoginService = $authLoginService;
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!check_admin_permission('show agents')) {
            abort(403, trans('禁止访问，无权限'));
        }

        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        if (request()->wantsJson()) {
            $agents = $this->getData($request, false);
            $html = '';

            foreach ($agents as $item) {
                $item->checkAgentRole();//检查角色
                $button = '';
                $button .= get_auth_show_button('show agents', route('agents.show', $item->id));
                $button .= get_auth_edit_button('edit agents', route('agents.edit', $item->id));
                //                $button .= get_auth_delete_button ('delete agents', route ('agents.destroy', $item->id));
                try {
                    $auth_url = $this->authLoginService->getAgentAuthUrl($item->id);
                } catch (\ErrorException $e) {
                    if ($request->wantsJson()) {

                        return response()->json([
                            'error' => true,
                            'message' => $e->getMessage(),
                        ]);
                    }
                }
                $button .= get_auth_html(' login agents', '越权登录', '<button target="_blank" href="' . $auth_url . '" onclick="auth_login_fun(\'越权登录代理商系统\',\'' . $auth_url . '\',\'确认自动登录到代理商系统\')" class="btn btn-sm btn-info">越权登录</botton>');
                $more_button = '';
                $more_button .= get_auth_html('disable agents', '禁用', '<li><a onclick="confirm_fun(\'禁用记录\',\'' . route('agents.disable', $item->id) . '\')" href="#">禁用</a></li>');
                $more_button .= get_auth_html('enable agents', '启用', '<li><a onclick="confirm_fun(\'启用记录\',\'' . route('agents.enable', $item->id) . '\')" href="#">启用</a></li>');
                if ($this->repository->allowDelete($item->id)) {
                    $more_button .= get_auth_html('destroy agents', '删除', '<li><a onclick="delete_confirm_fun(\'删除记录\',\'' . route('agents.destroy', $item->id) . '\')" href="#">删除</a></li>');
                }
                $button .= get_more_button($more_button);

                //会员人数
                $direct_num = $item->directChildNumber();
                $indirect_num = $item->indirectChildNumber();
                $direct_num = get_auth_html('show members', $direct_num, '<a href="' . route('members.index', 'source=direct&agent_name=' . $item->agent_name) . '" class="btn btn-link">' . $direct_num . '</a>');
                $indirect_num = get_auth_html('show members', $indirect_num, '<a href="' . route('members.index', 'source=indirect&agent_name=' . $item->agent_name) . '" class="btn btn-link">' . $indirect_num . '</a>');

                $html .= '<tr>
                                    <td><input class="check-item" type="checkbox" value="' . $item->id . '"></td>
                                    <td>' . $item->agent_no . '</td>
                                    <td>' . $item->username . '</td>
                                    <td>' . $item->agent_name . '</td>
                                    <td>' . $item->wx_name . '</td>
                                    <td>' . $item->contact_name . '</td>
                                    <td>' . $item->contact_phone . '</td>
                                    <td>' . $item->office_address . '</td>
                                    <td>' . $direct_num . '</td>
                                    <td>' . $indirect_num . '</td>
                                    <td>' . $item->join_date . '</td>
                                    <td>' . $item->statusItem($item->status, true) . '</td>
                                    <td>
                                        <div class="btn-group">
                                            ' . $button . '
                                        </div>
                                    </td>
                                </tr>';
            }
            $page = html_entity_decode($agents->links());

            $total = $agents->total();

            return response()->json([
                'html' => $html,
                'page' => $page,
                'total' => $total,
            ]);
        }
        $buttonHtml = '';
        $agent = app($this->repository->model());
        Log::createAdminLog(Log::SHOW_TYPE, '代理商管理 查看记录');

        return view('admin.agents.index', compact('agent', 'buttonHtml'));
    }

    protected function getData(Request $request, $export = false)
    {
        $keyword = $request->keyword ?? '';
        $area_region = $request->area_region ?? '';
        $orderBy = $request->_order_by ?? 'agents.id desc';
        QueryWhere::setRequest($request);
        $M = app($this->repository->model())
            ->select('agents.*');
        QueryWhere::like($M, 'contact_phone');
        QueryWhere::like($M, 'office_address');
//        QueryWhere::region ($M, 'regions.area_region');
        if ($area_region) {
            $M = $M->whereRaw(" tb_agents.id IN( SELECT
	tb_agent_regions.`agent_id`
FROM
	`tb_agent_regions`
LEFT JOIN `tb_regions` ON `tb_agent_regions`.`proxy_region_id` = `tb_regions`.`id`
WHERE
	`tb_regions`.`area_region` LIKE '%|$area_region|%')");
        }

        QueryWhere::date($M, 'join_date');
        QueryWhere::eq($M, 'agents.status');

        if ($keyword) {
            $M = $M->where(function ($query) use ($keyword) {
                $query->where('agents.username', 'like', '%' . $keyword . '%')
                    ->orWhere('agents.agent_name', 'like', '%' . $keyword . '%')
                    ->orWhere('agents.wx_name', 'like', '%' . $keyword . '%');
            });
        }
        QueryWhere::orderBy($M, $orderBy);
        if ($export === true) {
            $agents = $M->get();
        } else {
            $agents = $M->paginate();
        }


        return $agents;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (!check_admin_permission('create agents')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $method = 'POST';
        $action_url = route('agents.store');
        $agent = app($this->repository->model());
        $proxyRegion = [];
        return view('admin.agents.create_and_edit', compact('agent', 'action_url', 'method', 'proxyRegion'));
    }

    /**
     * Store a newly created resource in storage.
     * @param AgentCreateRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(AgentCreateRequest $request)
    {
        if (!check_admin_permission('create agents')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error' => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {
            $input = $request->input('Agent');
            $input['admin_id'] = get_admin_id();
            input_default($input, '');
            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_CREATE);
            $input['agent_no'] = SerialNumber::autoNumber(Agent::class);
            //加密
            $input['password'] = Hash::make($input['password']);
            if (empty($input['birthday'])) {
                unset($input['birthday']);
            }
            if (empty($input['authorize_date'])) {
                unset($input['authorize_date']);
            }
            if (empty($input['join_date'])) {
                unset($input['join_date']);
            }
            if (empty($input['is_forever_authorize'])) {
                $input['is_forever_authorize'] = 0;
            }
            $agent = $this->repository->create($input);
            SerialNumber::updateSerialID($input['agent_no'], $agent->id);
            $this->repository->saveProxyRegion($agent->id, $request->all());
            $response = [
                'message' => trans('添加成功'),
                'data' => $agent->toArray(),
            ];

            Log::createAdminLog(Log::ADD_TYPE, '代理商管理 创建记录');
            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => true,
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
        if (!check_admin_permission('show agents')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $agent = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $agent,
            ]);
        }
        $proxyIds = [];
        foreach ($agent->regions as $region) {
            $proxyIds[] = $region->proxy_region_id;
        }
        $Region = new Region();
        $proxyRegion = $Region->getMoreRegionList($proxyIds);

        return view('admin.agents.show', compact('agent', 'proxyRegion'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!check_admin_permission('edit agents')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $agent = $this->repository->find($id);
        $method = 'PUT';
        $action_url = route('agents.update', $id);
        $proxyIds = [];
        foreach ($agent->regions as $region) {
            $proxyIds[] = $region->proxy_region_id;
        }
        $Region = new Region();
        $proxyRegion = $Region->getMoreRegionList($proxyIds);


        return view('admin.agents.create_and_edit', compact('agent', 'action_url', 'method', 'proxyRegion'));
    }

    /**
     * Update the specified resource in storage.
     * @param AgentUpdateRequest $request
     * @param string $id
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(AgentUpdateRequest $request, $id)
    {
        if (!check_admin_permission('edit agents')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error' => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {

            $input = $request->input('Agent');
            input_default($input, '');
            $rules = $this->validator->getRules();
            $rules[ValidatorInterface::RULE_UPDATE]['username'] = 'required|unique:App\Entities\Agent,username,' . $id;
            $rules[ValidatorInterface::RULE_UPDATE]['contact_phone'] = 'required|unique:App\Entities\Agent,contact_phone,' . $id;
            $this->validator->setRules($rules);
            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_UPDATE);
            if ($input['password']) {
                //加密
                $input['password'] = Hash::make($input['password']);
                Log::createAdminLog(Log::EDIT_TYPE, '代理商管理 修改' . Agent::showName($id) . '密码');
            } else {
                unset($input['password']);
            }
            if (empty($input['birthday'])) {
                $input['birthday'] = null;
            }
            if (empty($input['authorize_date'])) {
                $input['authorize_date'] = null;
            }
            if (empty($input['join_date'])) {
                $input['join_date'] = null;
            }
            if (empty($input['is_forever_authorize'])) {
                $input['is_forever_authorize'] = 0;
            }
            $agent = $this->repository->update($input, $id);
            if (!$agent->isAgentRole()) {

            }
            $this->repository->saveProxyRegion($id, $request->all());
            $response = [
                'message' => trans('修改成功'),
                'data' => $agent->toArray(),
            ];
            Log::createAdminLog(Log::EDIT_TYPE, '代理商管理 修改记录');
            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson()) {

                return response()->json([
                    'error' => true,
                    'message' => $e->getMessageBag()->first(),
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag()->first())->withInput();
        } catch (\ErrorException $e) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error' => true,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * 禁用账号
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request, $id)
    {
        if (!check_admin_permission('delete agents')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error' => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        $id = $request->id ?? $id;
        $idArr = explode(',', $id);
        foreach ($idArr as $_id) {
            $ret = app($this->repository->model())->where('id', $_id)->update(['status' => 2]);
        }

        Log::createAdminLog(Log::EDIT_TYPE, '代理商管理 禁用记录');
        if (request()->wantsJson()) {

            return response()->json([
                'message' => trans('禁用成功'),
                'ret' => $ret,
            ]);
        }

        return redirect()->back()->with('message', trans('禁用成功'));
    }

    /**
     * 启用账号
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function enable(Request $request, $id)
    {
        if (!check_admin_permission('delete agents')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error' => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        $id = $request->id ?? $id;
        $idArr = explode(',', $id);
        foreach ($idArr as $_id) {
            $regions = AgentRegion::where('agent_id',$_id)->get();

            foreach ($regions  as $region){
                try {
                    $this->repository->checkHasAgentRegion($region->proxy_region_id, $_id);
                } catch (\ErrorException $e) {
                    return response()->json([
                        'error' => true,
                        'message' => $e->getMessage().' 无法启用',
                    ]);
                }
            }

            $ret = app($this->repository->model())->where('id', $_id)->update(['status' => 1]);
        }

        Log::createAdminLog(Log::EDIT_TYPE, '代理商管理 启用记录');
        if (request()->wantsJson()) {

            return response()->json([
                'message' => trans('启用成功'),
                'ret' => $ret,
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
        if (!check_admin_permission('delete agents')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error' => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        $id = $request->id ?? $id;
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

        Log::createAdminLog(Log::DELETE_TYPE, '代理商管理 删除记录');
        if (request()->wantsJson()) {

            return response()->json([
                'message' => trans('删除成功'),
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', trans('删除成功'));
    }

    public function export(Request $request)
    {
        $agent = $this->getData($request, true);
        $ExportName = new ExportMame();
        $all = $request->all();
        if (isset($all['status']) && $all['status']) {
            $M = new Agent();
            $all['status'] = $M->statusItem($all['status']);
        }
        $export_name = $ExportName->setRequest($all)->getName('代理商管理', [
            'keyword' => '名称',
            'contact_phone' => '联系电话',
            'area_region_name' => '授权区域',
            'join_date_start' => '加盟时间开始',
            'join_date_end' => '加盟时间结束',
            'address' => '办公地址',
            'status' => '状态'
        ]);
        return Excel::download(new AgentExport($agent), $export_name . '.xlsx');
    }
}
