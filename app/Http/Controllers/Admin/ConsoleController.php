<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2019/12/23
 */

namespace App\Http\Controllers\Admin;


use App\Entities\Admin;
use App\Entities\Bill;
use App\Entities\Member;
use App\Entities\Order;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ConsoleController extends Controller
{
    /**
     * 控制台 add by gui
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index ()
    {
        $admin            = Admin::find (get_admin_id ());
        $order_count      = Order::whereNotIn('status',[4])->count ();
        $member_count     = Member::count ();
        $bill_amount      = Bill::whereNotIn('status',[4])->sum ('amount');
        $yes_bill_amount   = Bill::where ('status', 1)->sum ('amount');
        $chart['order']   = $this->orderProduct ();
        $chart['bill']    = $this->billAll ();
        $chart['member']  = $this->memberAll ();
        $chart['no_bill'] = $this->noBillAll ();

        return view ('admin.console.index', compact ('admin', 'order_count', 'member_count', 'bill_amount', 'yes_bill_amount', 'chart'));
    }

    /**
     * 产品订单统计 add by gui
     * @return array
     */
    public function orderProduct ()
    {
        $sql  = "SELECT
	DATE_FORMAT(o.created_at, '%Y-%m') AS dmonth,
	SUM(op.number) AS number,
	SUM(op.price*op.number) AS amount
FROM
	`tb_orders` o
INNER JOIN tb_order_products op ON o.id = op.order_id
WHERE
	o.`status` IN (1,3,5)
GROUP BY
	dmonth";
        $list = DB::select ($sql);
        $data = [];
        foreach ($list as $item) {
            $data['x'][]        = $item->dmonth;
            $data['y_num'][]    = $item->number;
            $data['y_amount'][] = $item->amount;
        }

        return $data;
    }
    //佣金统计
    public function billAll ()
    {
        $sql  = "SELECT
	DATE_FORMAT(b.bill_at, '%Y-%m') AS dmonth,
	SUM(b.amount) AS amount
FROM
	`tb_bills` b WHERE status<>4
GROUP BY
	dmonth";
        $list = DB::select ($sql);
        $data = [];
        foreach ($list as $item) {
            $data['x'][] = $item->dmonth;
            $data['y'][] = $item->amount;
        }

        return $data;
    }
    //会员统计
    public function memberAll ()
    {
        $sql  = "SELECT
	DATE_FORMAT(m.reg_date, '%Y-%m') AS dmonth,
	count(m.id) AS count
FROM
	`tb_members` m
GROUP BY
	dmonth";
        $list = DB::select ($sql);
        $data = [];
        foreach ($list as $item) {
            $data['x'][] = $item->dmonth;
            $data['y'][] = $item->count;
        }

        //dd($data);
        return $data;
    }
    //未发放佣金统计
    public function noBillAll ()
    {
        $sql  = "SELECT
	DATE_FORMAT(b.bill_at, '%Y-%m') AS dmonth,
	SUM(b.amount) AS amount
FROM
	`tb_bills` b WHERE b.status=1
GROUP BY
	dmonth";
        $list = DB::select ($sql);
        $data = [];
        foreach ($list as $item) {
            $data['x'][] = $item->dmonth;
            $data['y'][] = $item->amount;
        }

        return $data;
    }

}
