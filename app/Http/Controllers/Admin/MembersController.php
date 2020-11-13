<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Agent;
use App\Entities\Bill;
use App\Entities\Log;
use App\Entities\Member;
use App\Entities\WxAccount;
use App\Exports\MemberExport;
use App\Exports\OrderQrcodeExport;
use App\Http\Controllers\Controller;
use App\Libs\ExportMame;
use App\Libs\QueryWhere;
use App\Presenters\MemberPresenter;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\MemberCreateRequest;
use App\Http\Requests\MemberUpdateRequest;
use App\Repositories\MemberRepositoryEloquent as MemberRepository;
use App\Validators\MemberValidator;

/**
 * Class MembersController.
 * @package namespace App\Http\Controllers;
 */
class MembersController extends Controller
{
    /**
     * @var MemberRepository
     */
    protected $repository;

    /**
     * @var MemberValidator
     */
    protected $validator;
    /**
     * @var MemberPresenter
     */
    protected $presenter;

    /**
     * MembersController constructor.
     * @param MemberRepository $repository
     * @param MemberValidator  $validator
     */
    public function __construct (MemberRepository $repository, MemberValidator $validator, MemberPresenter $presenter)
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
        if (!check_admin_permission ('show members')) {
            abort (403, trans ('禁止访问，无权限'));
        }

        $this->repository->pushCriteria (app ('Prettus\Repository\Criteria\RequestCriteria'));

        if (request ()->wantsJson ()) {

            $members = $this->getData ($request, false);
            $html    = '';

            foreach ($members as $item) {
                $button = '';
                $button .= get_auth_show_button ('show members', route ('members.show', $item->id));
                $button .= get_auth_edit_button ('edit members', route ('members.edit', $item->id));
                //$button .= get_auth_delete_button('delete members', route('members.destroy', $item->id));
                $en_btn_str = '';
                if ($item->status == Member::STATUS_DISABLE || $item->status == Member::STATUS_ENABLE) {
                    $en_btn_str .= '<li><a onclick="confirm_fun(\'禁用记录\',\'' . route ('members.disable', $item->id) . '\')" href="#">禁用</a></li>
                                                <li><a onclick="confirm_fun(\'启用记录\',\'' . route ('members.enable', $item->id) . '\')" href="#">启用</a></li>
                                                <li class="divider"></li>';
                }
                $del_btn_str = '';
                if ($this->repository->allowDelete ($item->id)) {
                    $del_btn_str .= '<li><a onclick="delete_confirm_fun(\'删除记录\',\'' . route ('members.destroy', $item->id) . '\')" href="#">删除</a></li>';
                }
                if ($del_btn_str && $en_btn_str) {
                    $button .= '<button type="button" class="btn btn-sm btn-info dropdown-toggle"
                                                    data-toggle="dropdown" aria-expanded="false">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                ' . $en_btn_str . '
                                                ' . $del_btn_str . '
                                            </ul>';
                }

                //dd($button);
                //会员人数
                $direct_num   = $item->directChildNumber ();
                $indirect_num = $item->indirectChildNumber ();
                $direct_num   = get_auth_html ('show members', $direct_num, '<a href="' . route ('members.index', 'source=direct&referrer_id=' . $item->id) . '" class="btn btn-link">' . $direct_num . '</a>');
                $indirect_num = get_auth_html ('show members', $indirect_num, '<a href="' . route ('members.index', 'source=indirect&referrer_id=' . $item->id) . '" class="btn btn-link">' . $indirect_num . '</a>');
                $amount       = $item->noPayBillAmount ();
                $html         .= '<tr>
                                    <td><input class="check-item" type="checkbox" value="' . $item->id . '"></td>
                                    <td>' . $item->member_no . '</td>
                                    <td>' . $item->real_name . '</td>
                                    <td>' . $item->wx_name . '</td>
                                    <td>' . $item->mobile . '</td>
                                    <td>' . $amount . '</td>
                                    <td>' . $direct_num . '</td>
                                    <td>' . $indirect_num . '</td>
                                    <td>' . $item->reg_date . '</td>
                                    <td>' . $item->statusItem ($item->status, true) . '</td>
                                    <td>
                                        <div class="btn-group">
                                            ' . $button . '
                                        </div>
                                    </td>
                                </tr>';
            }
            $page = html_entity_decode ($members->links ());

            $total = $members->total ();

            return response ()->json ([
                'html'  => $html,
                'page'  => $page,
                'total' => $total,
            ]);
        }
        $buttonHtml = '';
        $member     = app ($this->repository->model ());
        Log::createAdminLog (Log::SHOW_TYPE, '会员列表 查看记录');

        return view ('admin.members.index', compact ('member', 'buttonHtml'));
    }

    protected function getData (Request $request, $export = false)
    {
        $agent_name = $request->agent_name ?? '';
        $keyword    = $request->keyword ?? '';
        $region_id  = $request->region_id ?? '';
        $referrer   = $request->referrer ?? '';
        $referrer_id   = $request->referrer_id ?? '';
        $orderBy    = $request->_order_by ?? 'members.id desc ';
        $source     = $request->source ?? '';
        QueryWhere::setRequest ($request);
        $M = app ($this->repository->model ())
            ->select ('members.*')
            ->leftJoin ('regions AS resident_region', 'resident_region_id', '=', 'resident_region.id');
        QueryWhere::like ($M, 'mobile');
        QueryWhere::like ($M, 'working_year');
        QueryWhere::eq ($M, 'members.status');
        QueryWhere::date ($M, 'reg_date');
        QueryWhere::region ($M, 'resident_region.area_region', $region_id);
        //        QueryWhere::like($M,'agent_name');
        $sql_agent_where = '';
        if ($agent_name) {
            $sql_agent_where .= " and a.agent_name LIKE '%$agent_name%' ";
        }

        if ($source == 'direct') {
            //代理商直接会员
            //$sql_agent_where .= " AND ma.referrer_member_id=0 ";
            if($referrer_id){
                $sql_agent_where .= " AND ma.referrer_member_id ='$referrer_id' ";
            }
        } elseif ($source == 'indirect') {
            //代理商间接会员

            $sql_agent_where .= " AND ma.referrer_member_id IN(SELECT member_id FROM `tb_member_agents` where referrer_member_id='$referrer_id') ";
        }

        if ($sql_agent_where) {
            $M = $M->whereRaw (DB::raw (" tb_members.id IN(SELECT
                                DISTINCT ma.member_id
                            FROM
                                `tb_member_agents` ma
                            INNER JOIN tb_agents a ON ma.agent_id = a.id
                            WHERE
                                    1=1 $sql_agent_where)"));
        }

        if ($referrer) {
            $M = $M->whereRaw (" tb_members.id IN(SELECT DISTINCT
	ma.member_id
FROM
	`tb_member_agents` ma
INNER JOIN tb_members m ON ma.referrer_member_id = m.id
WHERE
	m.real_name LIKE '%$referrer%')");
        }
        if ($region_id) {
            //            $M = $M->whereRaw(DB::raw(" resident_region_id IN(SELECT id FROM `tb_regions` where area_region like '%|$region_id|%')"));
        }
        //        dd($M->toSql());
        //        if ($phone) $members = $members->where('phone', 'like', "%{$phone}%");
        //        if ($working_year) $members = $members->where('working_year', 'like', "%{$working_year}%");
        //        if ($status) $members = $members->where('status', 'like', "%{$status}%");
        if ($keyword) $M = $M->where (function ($query) use ($keyword) {
            $query->where ('real_name', 'like', '%' . $keyword . '%')
                ->orWhere ('wx_account', 'like', '%' . $keyword . '%')
                ->orWhere ('wx_name', 'like', '%' . $keyword . '%');
        });
        //        if ($reg_date_start) $members = $members->whereRaw(DB::raw('DATE(reg_date)>=\'' . $reg_date_start . '\''));
        //        if ($reg_date_end) $members = $members->whereRaw(DB::raw('DATE(reg_date)<=\'' . $reg_date_end . '\''));

        //        if ($orderBy) $members = $members->orderByRaw($orderBy);
        QueryWhere::orderBy ($M, $orderBy);
        if ($export === true) {
            $members = $M->get ();
        } else {
            $members = $M->paginate ();
        }

        return $members;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create ()
    {
        if (!check_admin_permission ('create members')) {
            abort (403, trans ('禁止访问，无权限'));
        }
        $method     = 'POST';
        $action_url = route ('members.store');
        $member     = app ($this->repository->model ());

        return view ('admin.members.create_and_edit', compact ('member', 'action_url', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     * @param MemberCreateRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store (MemberCreateRequest $request)
    {
        if (!check_admin_permission ('create members')) {
            if ($request->wantsJson ()) {

                return response ()->json ([
                    'error'   => true,
                    'message' => trans ('禁止访问，无权限'),
                ]);
            }

            return redirect ()->back ()->withErrors (trans ('禁止访问，无权限'))->withInput ();
        }
        try {
            $input = $request->input ('Member');
            $this->validator->with ($input)->passesOrFail (ValidatorInterface::RULE_CREATE);

            $member = $this->repository->create ($input);

            $response = [
                'message' => trans ('添加成功'),
                'data'    => $member->toArray (),
            ];
            Log::createAdminLog (Log::ADD_TYPE, '会员列表 创建记录');
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
        if (!check_admin_permission ('show members')) {
            abort (403, trans ('禁止访问，无权限'));
        }
        $member = $this->repository->find ($id);

        if (request ()->wantsJson ()) {

            return response ()->json ([
                'data' => $member,
            ]);
        }

        return view ('admin.members.show', compact ('member'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit ($id)
    {
        if (!check_admin_permission ('edit members')) {
            abort (403, trans ('禁止访问，无权限'));
        }
        $member     = $this->repository->find ($id);
        $method     = 'PUT';
        $action_url = route ('members.update', $id);

        return view ('admin.members.create_and_edit', compact ('member', 'action_url', 'method'));
    }

    /**
     * Update the specified resource in storage.
     * @param MemberUpdateRequest $request
     * @param string              $id
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update (MemberUpdateRequest $request, $id)
    {
        if (!check_admin_permission ('edit members')) {
            if ($request->wantsJson ()) {

                return response ()->json ([
                    'error'   => true,
                    'message' => trans ('禁止访问，无权限'),
                ]);
            }

            return redirect ()->back ()->withErrors (trans ('禁止访问，无权限'))->withInput ();
        }
        try {

            $input = $request->input ('Member');
            $this->validator->with ($input)->passesOrFail (ValidatorInterface::RULE_UPDATE);
            if (empty($input['native_region_id'])) {
                throw new \ErrorException('籍贯区域 不能为空');
            }
            $member           = $this->repository->update ($input, $id);
            $inputMemberAgent = $request->input ('MemberAgent');
            if (is_array ($inputMemberAgent)) {
                foreach ($inputMemberAgent as $item) {
                    $this->repository->saveMemberAgent ($id, ['MemberAgent' => $item]);
                }
            }

            $response = [
                'message' => trans ('修改成功'),
                'data'    => $member->toArray (),
            ];
            Log::createAdminLog (Log::EDIT_TYPE, '会员列表 修改记录');
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

            return redirect ()->back ()->withErrors ($e->getMessage ())->withInput ();
        }
    }

    /**
     * @param Request $request
     * @param int     $id
     * @return \Illuminate\Http\Response
     */
    public function disable (Request $request, $id)
    {
        if (!check_admin_permission ('delete members')) {
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
            $ret = app ($this->repository->model ())->where ('id', $_id)->update (['status' => 2]);
        }

        Log::createAdminLog (Log::EDIT_TYPE, '会员列表 禁用记录');
        if (request ()->wantsJson ()) {

            return response ()->json ([
                'message' => trans ('禁用成功'),
                'ret'     => $ret,
            ]);
        }

        return redirect ()->back ()->with ('message', trans ('禁用成功'));
    }

    /**
     * @param Request $request
     * @param int     $id
     * @return \Illuminate\Http\Response
     */
    public function enable (Request $request, $id)
    {
        if (!check_admin_permission ('delete members')) {
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
            $ret = app ($this->repository->model ())->where ('id', $_id)->update (['status' => 1]);
        }

        Log::createAdminLog (Log::EDIT_TYPE, '会员列表 启用记录');
        if (request ()->wantsJson ()) {

            return response ()->json ([
                'message' => trans ('启用成功'),
                'ret'     => $ret,
            ]);
        }

        return redirect ()->back ()->with ('message', trans ('启用成功'));
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param int     $id
     * @return \Illuminate\Http\Response
     */
    public function destroy (Request $request, $id)
    {
        if (!check_admin_permission ('delete members')) {
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
        $error = 0;
        $success = 0;
        foreach ($idArr as $_id) {
            if ($this->repository->allowDelete ($_id)) {
                $deleted = $this->repository->delete ($_id);
                if ($deleted) {
                    WxAccount::where ('account_id', $_id)->where ('account_type', Member::class)->delete ();
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

        Log::createAdminLog (Log::DELETE_TYPE, '会员列表 删除记录');
        if (request ()->wantsJson ()) {

            return response ()->json ([
                'message' => trans ('删除成功'),
                'deleted' => $deleted,
            ]);
        }

        return redirect ()->back ()->with ('message', trans ('删除成功'));
    }

    public function export (Request $request)
    {
        $member     = $this->getData ($request, true);
        $ExportName = new ExportMame();
        $all        = $request->all ();
        $M          = new Agent();
        if (isset($all['status']) && $all['status']) {
            $all['status'] = $M->statusItem ($all['status']);
        }

        $export_name = $ExportName->setRequest ($all)->getName ('会员管理', [
            'agent_name'       => '代理商名称',
            'keyword'          => '名称',
            'mobile'           => '手机号码',
            'referrer'         => '推荐人',
            'area_region_name' => '所属区域',
            'reg_date_start'   => '注册时间开始',
            'reg_date_end'     => '注册时间结束',
            'working_year'     => '从业年限',
            'status'           => '状态'
        ]);

        return Excel::download (new MemberExport($member), $export_name . '.xlsx');
    }

}
