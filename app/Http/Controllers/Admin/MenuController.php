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
use App\Libs\QueryWhere;
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
        if (!check_admin_auth ($this->module_name)) {
            return auth_error_return ();
        }
        if (request ()->ajax ()) {
            QueryWhere::setRequest ($request->all ());
            $M = $this->repository->makeModel ()->select ('menus.*');
            QueryWhere::eq ($M, 'status');
            QueryWhere::like ($M, 'title');
            QueryWhere::orderBy ($M, 'menus.sort', 'ASC');
            $list = $M->get ();
            foreach ($list as $key => $item) {
                if ($request->input ('title') || $request->input ('status') != '') {
                    //进行了搜索，不进行上下级显示
                    $list[ $key ]['pid'] = 0;
                }
                $list[ $key ]['_edit_url'] = url ('admin/menu/' . $item->id . '/edit');
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
        $content = json_decode ($menu->content, true);
        if ($content) {
            $menu->content = json_encode ($content, JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT);
        } else {
            $content = [];
        }
        $backup_content = $content['content'] ?? '';

        return view ('admin.' . $this->module_name . '.show', compact ('menu', 'content', 'backup_content'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Menu $menu
     * @return \Illuminate\Http\Response
     */
    public function edit (Menu $menu)
    {
        //
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
        //
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
        $user_id  = get_menuin_user_id ();
        $insArr   = [
            'menu_id' => $menu->id,
            'user_id' => $user_id,
            'is_read' => 1,
            'read_at' => now (),
        ];
        $menuRead = MenuRead::where ('menu_id', $menu->id)->where ('user_id', $user_id)->first ();
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
