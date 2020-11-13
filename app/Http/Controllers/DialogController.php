<?php
/**
 * Created by localhost.
 * User: gui
 * Email: liaodeity@foxmail.com
 * Date: 2019/12/25
 */

namespace App\Http\Controllers;


use App\Entities\Member;
use App\Entities\Region;
use App\Libs\QueryWhere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DialogController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {

        $this->request = $request;
    }

    /**
     * 推荐人选择
     * add by gui
     * @param Request $request
     */
    public function referrer(Request $request)
    {
        $member_id = $request->id ?? 0;
        $no_id = $request->no_id ?? 0;
        $agent_id  = $request->agent_id ?? 0;
        if (request()->wantsJson()) {
            $agent_name = $request->agent_name ?? '';
            $keyword    = $request->keyword ?? '';
            $region_id  = $request->region_id ?? '';
            $referrer   = $request->referrer ?? '';
            $orderBy    = $request->_order_by ?? 'members.id desc ';
            $source     = $request->source ?? '';
            QueryWhere::setRequest($request);
            $M = app(Member::class)
                ->select('members.*')
                ->join('member_agents','members.id','=','member_agents.member_id')
                ->leftJoin('regions AS resident_region', 'resident_region_id', '=', 'resident_region.id');
            QueryWhere::like($M, 'member_agents.agent_id',$agent_id);
            QueryWhere::like($M, 'mobile');
            QueryWhere::like($M, 'working_year');
            QueryWhere::eq($M, 'members.status');
            QueryWhere::date($M, 'reg_date');
            QueryWhere::notIn($M, 'members.id',[$no_id]);
            QueryWhere::region($M, 'resident_region.area_region', $region_id);
            if ($keyword) $M = $M->where(function ($query) use ($keyword) {
                $query->where('real_name', 'like', '%' . $keyword . '%')
                    ->orWhere('wx_account', 'like', '%' . $keyword . '%')
                    ->orWhere('wx_name', 'like', '%' . $keyword . '%');
            });
            QueryWhere::orderBy($M, $orderBy);
            $members = $M->paginate();
            $html    = '';

            foreach ($members as $item) {
                $button = '';
                $button .= '<button type="button" onclick="select_item(\''.$agent_id.'\',\''.$item->id.'\',\''.$item->real_name.'\')" class="btn btn-sm btn-info">选择</button>';
                //dd($button);
                //会员人数
                $direct_num   = $item->directChildNumber();
                $indirect_num = $item->indirectChildNumber();
                $direct_num   = get_auth_html('show members', $direct_num, '<a href="' . route('members.index', 'source=direct&mobile=' . $item->mobile) . '" class="btn btn-link">' . $direct_num . '</a>');
                $indirect_num = get_auth_html('show members', $indirect_num, '<a href="' . route('members.index', 'source=indirect&mobile=' . $item->mobile) . '" class="btn btn-link">' . $indirect_num . '</a>');
                $amount       = $item->noPayBillAmount();
                $html         .= '<tr>
                                    <td><input class="check-item" type="checkbox" value="' . $item->id . '"></td>
                                    <td>' . $item->member_no . '</td>
                                    <td>' . $item->real_name . '</td>
                                    <td>' . $item->wx_name . '</td>
                                    <td>' . $item->mobile . '</td>
                                    <td>' . $item->reg_date . '</td>
                                    <td>' . $item->statusItem($item->status, true) . '</td>
                                    <td>
                                        <div class="btn-group">
                                            ' . $button . '
                                        </div>
                                    </td>
                                </tr>';
            }
            $page = html_entity_decode($members->links());

            $total = $members->total();

            return response()->json([
                'html'  => $html,
                'page'  => $page,
                'total' => $total,
            ]);
        }
        $member = new Member();
        return view('dialogs.referrer_select', compact('member'));
    }
}
