<?php
/**
 * Created by localhost.
 * User: gui
 * Email: liaodeity@foxmail.com
 * Date: 2019/10/30
 */

namespace App\Http\Controllers\Admin;


use App\Entities\Admin;
use App\Entities\Order;
use App\Entities\OrderSale;
use App\Http\Controllers\Controller;
use App\Repositories\MenuRepositoryEloquent;
use App\Services\LockScreenService;
use App\Services\MessageNoticeService;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index (MenuRepositoryEloquent $menuRepositoryEloquent)
    {
        return redirect (url('admin-console'));
        //$menus = $menuRepositoryEloquent->getMenuList ();
        //$admin = Admin::find (get_admin_id ());
        //return view ('admin.main.index', compact ('menus', 'admin'));
    }

    /**
     * 提醒信息
     * add by gui
     */
    public function tips (Request $request)
    {
        $adminID = get_admin_id ();
        //待处理订单
        $not_deal_order = app (Order::class)
            ->join ('agents', 'orders.agent_id', '=', 'agents.id')
            ->whereIn ('orders.status', [
                Order::NO_PAY_STATUS,
                Order::NO_DELIVERY_STATUS,
                Order::YES_DELIVERY_STATUS,
            ])
            ->where ('orders.is_effective', 1)
            ->count ('orders.id');

        //待处理售后
        $not_deal_order_sale = app (OrderSale::class)
            ->select ('order_sales.*')
            ->join ('orders', 'order_sales.order_id', '=', 'orders.id')
            ->join ('agents', 'order_sales.agent_id', '=', 'agents.id')
            ->where ('order_sales.status', OrderSale::NO_DEAL)
            ->count ('order_sales.id');

        //是否有新订单
        $MessageNoticeService = new MessageNoticeService();
        $ret                  = $MessageNoticeService->hasNewOrderTip ($adminID, Admin::class);
        $has_new_order        = $ret ? 1 : 0;


        $response = [
            'message' => '获取成功',
            'result'  => [
                'order_menu'    => (int)$not_deal_order + (int)$not_deal_order_sale,
                'orderPending'  => (int)$not_deal_order,
                'orderSales'    => (int)$not_deal_order_sale,
                'has_new_order' => $has_new_order
            ]
        ];

        $M = new \App\Entities\WxAccount();
        $M->syncHeadImg ();

        if ($request->wantsJson ()) {

            return response ()->json ($response);
        }
    }

    public function lockscreen (LockScreenService $lockScreenService)
    {

        $lockScreenService->setType ('admin')->setLock ();
        $admin = Admin::find (get_admin_id ());
        return view ('admin.main.lockscreen', compact ('admin'));
    }

    public function logout ()
    {
        session ()->flush ();
        return redirect (route ('admin-login'));
    }
}
