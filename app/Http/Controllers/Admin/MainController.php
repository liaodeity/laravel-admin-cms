<?php
/*
|-----------------------------------------------------------------------------------------------------------
| laravel-admin-cms [ 简单高效的开发插件系统 ]
|-----------------------------------------------------------------------------------------------------------
| Licensed ( MIT )
| ----------------------------------------------------------------------------------------------------------
| Copyright (c) 2020-2021 https://gitee.com/liaodeiy/laravel-admin-cms All rights reserved.
| ----------------------------------------------------------------------------------------------------------
| Author: 廖春贵 < liaodeity@gmail.com >
|-----------------------------------------------------------------------------------------------------------
*/

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Feedback;
use App\Models\JoinUs;
use App\Models\Link;
use App\Models\Log;
use App\Models\Menu;
use App\Models\Recall;
use App\Models\User;
use App\Models\WebView;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

class MainController extends Controller
{
    public function index ()
    {
        $user = User::find (get_login_user_id ());

        return view ('admin.main.index', compact ('user'));
    }

    /**
     * 控制台 add by gui
     */
    public function console ()
    {
        $shortcutList = Menu::where ('status', 1)->where ('is_shortcut', 1)->orderBy ('sort', 'ASC')->get ();
        foreach ($shortcutList as $key => $menu) {
            //屏蔽无权限菜单
            if ($menu['auth_name'] && !check_admin_auth ($menu['auth_name'] . '_index')) {
                unset($shortcutList[ $key ]);
            }
        }
        $start       = Carbon::now ()->subDays (30)->toDateString ();
        $end         = Carbon::now ()->toDateString ();
        $echart_date = $start . ' - ' . $end;

        return view ('admin.main.console', compact ('shortcutList', 'echart_date'));
    }

    /**
     * 退出登录 add by gui
     */
    public function logout (Request $request)
    {
        $request->session ()->flush ();

        return redirect (route ('admin.login'));
    }

    /**
     * 清除缓存 add by gui
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear ()
    {
        Artisan::call ('cache:clear');
        Artisan::call ('route:clear');
        Artisan::call ('config:clear');
        Artisan::call ('view:clear');
        Artisan::call ('permission:cache-reset');

        return ajax_success_result ('服务端清理缓存成功');
    }

    /**
     * 菜单初始化 add by gui
     */
    public function init ()
    {
        $userId   = get_login_user_id ();
        $cacheKey = 'ADMIN.MENU_INIT.' . $userId;
        $initData = cache ($cacheKey);
        if ($initData) {
            return response ()->json ($initData);
        }
        $initData = [
            'clearInfo' => [
                'clearUrl' => route ('admin.main.clear')
            ],
            'homeInfo'  => [
                'title' => '控制台',
                'icon'  => 'fa fa-home',
                'href'  => 'admin/main/console'
            ],
            'logoInfo'  => [
                'title' => get_config_value ('site_short_title'),
                'image' => asset ('static/admin/images/logo.png'),
                'href'  => ''
            ]
        ];

        $menuInfo = [];
        $list     = Menu::where ('pid', 0)->where ('type', 1)->where ('status', 1)->orderBy ('sort', 'ASC')->get ();
        foreach ($list as $item) {
            $cate_module = $item->cate_module ?? '';
            $list2       = Menu::where ('pid', $item->id)->where ('type', 1)->where ('status', 1)->orderBy ('sort', 'ASC')->get ();
            $row         = [
                "id"    => $item->id ?? '',
                "title" => $item->title ?? '',
                'icon'  => $item->icon,
                'auth'  => $item->auth_name
            ];

            $row2 = [];
            foreach ($list2 as $item2) {
                $row2  = [
                    "id"     => $item2->id ?? '',
                    "title"  => $item2->title ?? '',
                    'icon'   => $item2->icon,
                    'href'   => $item2->href ? ($item2->href) : '',
                    'target' => '_self',
                    'auth'   => $item2->auth_name
                ];
                $list3 = Menu::where ('pid', $item2->id)->where ('type', 1)->where ('status', 1)->orderBy ('sort', 'ASC')->get ();
                foreach ($list3 as $item3) {
                    $row3            = [
                        "id"     => $item3->id ?? '',
                        "title"  => $item3->title ?? '',
                        'icon'   => $item3->icon,
                        'href'   => $item3->href ? ($item3->href) : '',
                        'target' => '_self',
                        'auth'   => $item3->auth_name
                    ];
                    $row2['child'][] = $row3;
                }

                $row['child'][] = $row2;
            }

            //采用资讯分类
            if ($cate_module) {
                //查询是否有分类模型
                $list2 = Category::where ('module', $cate_module)->where ('parent_id', 0)->where ('status', 1)->orderBy ('display_order', 'asc')->get ();
                $row2  = [];
                foreach ($list2 as $item2) {
                    $row2 = [
                        "id"     => 'category-' . $item2->id ?? '',
                        "title"  => $item2->title ?? '',
                        'icon'   => 'iconfont iconwenzhang',
                        'href'   => ('admin/' . $item2->module) . '?jump_menu=1&category_id=' . $item2->id,
                        'target' => '_self',
                        'auth'   => $item2->auth_name
                    ];
                    //第三层
                    $list3 = Category::where ('parent_id', $item2->id)->where ('status', 1)->orderBy ('display_order', 'asc')->get ();
                    $row3  = [];
                    foreach ($list3 as $item3) {
                        $row3            = [
                            "id"     => 'category-' . $item3->id ?? '',
                            "title"  => $item3->title ?? '',
                            'icon'   => 'iconfont iconwenzhang',
                            'href'   => ('admin/' . $item2->module) . '?jump_menu=1&category_id=' . $item3->id,
                            'target' => '_self',
                            'auth'   => $item3->auth_name
                        ];
                        $row2['child'][] = $row3;
                    }
                    $row['child'][] = $row2;
                }
            }

            $menuInfo[] = $row;

        }

        $initData['menuInfo'] = $menuInfo;
        $initData             = $this->authMenu ($initData);
        cache ()->put ($cacheKey, $initData, 360);


        return response ()->json ($initData);
    }

    protected function authMenu ($initData)
    {
        foreach ($initData['menuInfo'] as $key => $item) {
            if (!isset($item['child'])) {
                continue;
            }
            foreach ($item['child'] as $key2 => $child) {
                $check       = false;
                $permissions = Permission::where ('menu_id', $child['id'])->get ();
                foreach ($permissions as $val) {
                    if (check_admin_auth ($val['name'])) {
                        $check = true;
                        break;
                    }
                }
                if ($child['auth'] && check_admin_auth ($child['auth'] . '_index')) {
                    $check = true;
                }
                if (User::isSuperAdmin ()) {
                    $check = true;
                }
                if (!$check) {
                    unset($initData['menuInfo'][ $key ]['child'][ $key2 ]);
                }
            }
            if (empty($initData['menuInfo'][ $key ]['child'])) {
                unset($initData['menuInfo'][ $key ]);
            }
        }

        return $initData;
    }

    /**
     * 获取实时统计数据 add by gui
     */
    public function syncRealNum ()
    {
        $monthDate   = date ('Y-m-01');
        $userId      = get_login_user_id ();
        $todayOrderCount = 0;
        $monthOrderCount = 0;
        $monthOrderMoney = '0.00';
        $memberCount = User\UserMember::count ();


        $result['member']            = $memberCount;
        $result['today_order']       = $todayOrderCount;
        $result['month_order']       = $monthOrderCount;
        $result['month_order_money'] = $monthOrderMoney;

        return ajax_success_result ('', $result);
    }

    public function getEchart ()
    {
        $date_str   = request ()->input ('dates');
        $date       = array_get_date ($date_str);
        $start_date = array_get ($date, '_start');
        $end_date   = array_get ($date, '_end');
        $x_data     = [];
        $series     = [];
        if (empty($start_date)) {
            $start_date = Carbon::now ()->subDays (30)->toDateString ();
        }
        if (empty($end_date)) {
            $end_date = Carbon::now ()->toDateString ();
        }
        //访问用户
        $webUser  = WebView::selectRaw ('COUNT(DISTINCT web_user) as count,DATE(view_at) as date')
            ->whereDate ('view_at', '>=', $start_date)
            ->whereDate ('view_at', '<=', $end_date)
            ->groupByRaw ('DATE(view_at)')
            ->orderBy ('date', 'ASC')->get ();
        $userData = [];

        foreach ($webUser as $log) {
            $userData[ $log->date ] = $log->count ?? 0;
        }
        //访问次数
        $webView  = WebView::selectRaw ('COUNT(web_user) as count,DATE(view_at) as date')
            ->whereDate ('view_at', '>=', $start_date)
            ->whereDate ('view_at', '<=', $end_date)
            ->groupByRaw ('DATE(view_at)')
            ->orderBy ('date', 'ASC')->get();
        $viewData = [];
        foreach ($webView as $log) {
            $viewData[ $log->date ] = $log->count ?? 0;
        }

        for ($i = 0; $i < 100; $i++) {
            $_date = Carbon::parse ($start_date)->addDays ($i)->toDateString ();
            if ($_date > $end_date) {
                break;
            }
            $x_data[]         = $_date;
            $series['users'][] = isset($userData[ $_date ]) ? $userData[ $_date ] : 0;
            $series['views'][] = isset($viewData[ $_date ]) ? $viewData[ $_date ] : 0;
        }

        return ajax_success_result ('', [
            'start_date' => $start_date,
            'end_date'   => $end_date,
            'x_data'     => $x_data,
            'series'     => $series
        ]);
    }

    /**
     * 待办日志 add by gui
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logs (Request $request)
    {
        $user_id = get_login_user_id ();
        $list    = Log::select ('logs.*', 'log_reads.is_read')
            ->where ('type', Log::TO_DO_TYPE)
            ->leftJoin ('log_reads', function ($join) use ($user_id) {
                $join->on ('log_reads.log_id', '=', 'logs.id')
                    ->where ('log_reads.user_id', '=', $user_id);
            })
            ->orderBy ('id', 'DESC')
            ->paginate (17);
        $data    = $list->items () ?? [];
        foreach ($data as $key => $item) {
            $url   = '';
            $title = '';
            $id    = $item->source_id;
            switch ($item->source_type) {
                case Link::class;
                    $url   = '/admin/link?id=' . $id;
                    $title = '友情链接';
                    break;
                case Feedback::class:
                    $url   = '/admin/feedback?id=' . $id;
                    $title = '问题反馈';
                    break;
                case JoinUs::class:
                    $url   = '/admin/join_us?id=' . $id;
                    $title = '申请加入记录';
                    break;
            }
            $name                    = User::showName ($item->user_id);
            $data[ $key ]['read']    = $item->is_read == 1 ? '已读' : '未读';
            $data[ $key ]['log_at']  = Carbon::parse ($item->created_at)->format ('Y-m-d H:i');
            $data[ $key ]['url']     = $url;
            $data[ $key ]['content'] = ($name ? '[' . $name . ']' : '') . $item->title;
            $data[ $key ]['title']   = $title;
        }

        return ajax_success_result ('', ['data' => $data, 'page' => $list->links ()->render ()]);
    }
}
