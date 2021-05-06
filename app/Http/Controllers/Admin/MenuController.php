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

use App\Enums\MenuStatusEnum;
use App\Enums\MenuTypeEnum;
use App\Http\Controllers\Controller;
use App\Libs\QueryWhere;
use App\Models\Log;
use App\Models\Menu;
use App\Models\MenuRead;
use App\Repositories\MenuRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class MenuController extends Controller
{
    protected $module_name = 'menu';
    /**
     * @var MenuRepository
     */
    private $repository;

    public function __construct (MenuRepository $repository)
    {
        View::share ('MODULE_NAME', $this->module_name);//模块名称

        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request)
    {
        if (!check_admin_auth ($this->module_name . '_' . __FUNCTION__)) {
            return auth_error_return ();
        }
        if (request ()->ajax ()) {
            QueryWhere::setRequest ($request->all ());
            $M = $this->repository->makeModel ()->select ('menus.*');
            QueryWhere::like ($M, 'menu_name');
            QueryWhere::like ($M, 'auth_name');
            QueryWhere::eq ($M, 'status');
            QueryWhere::eq ($M, 'type');
            QueryWhere::like ($M, 'href');
            QueryWhere::like ($M, 'title');
            QueryWhere::orderBy ($M, 'menus.sort', 'ASC');
            $list = $M->get ();
            foreach ($list as $key => $item) {
                if ($request->input ('title') || $request->input ('status') != '') {
                    //进行了搜索，不进行上下级显示
                    $list[ $key ]['pid'] = 0;
                }
                $list[$key]['status'] = MenuStatusEnum::toHtml ($item->status);
                $list[$key]['type'] = MenuTypeEnum::toHtml ($item->type);
                $list[ $key ]['_view_auth'] = true;
                $list[ $key ]['_edit_url']  = url ('admin/menu/' . $item->id . '/edit');
            }
            $result = [
                'code'  => 0,
                'count' => count ($list),
                'data'  => $list,
            ];

            return response ()->json ($result);

        } else {
            $menu = $this->repository->makeModel ();

            return view ('admin.' . $this->module_name . '.index', compact ('menu'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create ()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store (Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Menu $menu
     * @return \Illuminate\Http\Response
     */
    public function show (Menu $menu)
    {
        if (!check_admin_auth ($this->module_name . ' show')) {
            return auth_error_return ();
        }

        return view ('admin.' . $this->module_name . '.show', compact ('menu'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Menu $menu
     * @return \Illuminate\Http\Response
     */
    public function edit (Menu $menu)
    {
        if (!check_admin_auth ($this->module_name . '_' . __FUNCTION__)) {
            return auth_error_return ();
        }
        $_method = 'PUT';

        return view ('admin.' . $this->module_name . '.create_or_edit', compact ('menu', '_method'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Menu         $menu
     * @return \Illuminate\Http\Response
     */
    public function update (Request $request, Menu $menu)
    {
        $request->validate ([
            'Menu.title'  => 'required',
            'Menu.status' => 'required',
        ], [], [
            'Menu.title'  => '菜单名称',
            'Menu.status' => '状态',
        ]);
        if (!check_admin_auth ($this->module_name . ' edit')) {
            return auth_error_return ();
        }
        $input = $request->input ('Menu');
        $input = $this->formatRequestInput (__FUNCTION__, $input);
        try {
            $menu = $this->repository->update ($input, $menu->id);
            if ($menu) {
                Log::createLog (Log::EDIT_TYPE, '修改菜单', $menu->toArray (), $menu->id, Menu::class);

                return ajax_success_result ('更新成功');
            } else {
                return ajax_success_result ('更新失败');
            }

        } catch (BusinessException $e) {
            return ajax_error_result ($e->getMessage ());
        }
    }

    private function formatRequestInput (string $__FUNCTION__, $input)
    {
        return $input;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Menu $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy (Menu $menu)
    {
        //
    }

    /**
     * 标记已读 add by gui
     * @param Menu $menu
     * @return \Illuminate\Http\JsonResponse
     */
    public function read (Menu $menu)
    {
        $menu_id  = get_menuin_user_id ();
        $insArr   = [
            'menu_id' => $menu->id,
            'user_id' => $menu_id,
            'is_read' => 1,
            'read_at' => now (),
        ];
        $menuRead = MenuRead::where ('menu_id', $menu->id)->where ('user_id', $menu_id)->first ();
        if (isset($menuRead->id)) {
            return ajax_success_result ('已读成功');
        }
        $ret = MenuRead::create ($insArr);
        if ($ret) {
            return ajax_success_result ('已读成功');
        } else {
            return ajax_error_result ('已读失败');
        }
    }
}
