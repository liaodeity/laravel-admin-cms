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

use App\Enums\SexEnum;
use App\Enums\StatusEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Libs\QueryWhere;
use App\Models\Log;
use App\Models\Page;
use App\Repositories\PageRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class PageController extends Controller
{
    protected $module_name = 'page';
    /**
     * @var PageRepository
     */
    private $repository;

    public function __construct (PageRepository $repository)
    {
        SexEnum::attrs ();
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

        $category_id = $request->input ('category_id', 0);
        if (request ()->wantsJson ()) {
            $limit = $request->input ('limit', 15);
            QueryWhere::defaultOrderBy ('pages.id', 'DESC')->setRequest ($request->all ());
            $M = $this->repository->makeModel ()->select ('pages.*');
            QueryWhere::eq ($M, 'pages.category_id', $category_id);
            QueryWhere::eq ($M, 'pages.status');
            QueryWhere::eq ($M, 'pages.istop');
            QueryWhere::like ($M, 'pages.title');
            QueryWhere::like ($M, 'pages.link_label');
            QueryWhere::orderBy ($M);

            $M     = $M->paginate ($limit);
            $count = $M->total ();
            $data  = $M->items ();
            foreach ($data as $key => $item) {
                $data[ $key ]['_url']   = url ('page/detail/' . $item->id);
                $data[ $key ]['status'] = StatusEnum::toLabel ($item->status);
            }
            $result = [
                'count' => $count,
                'data'  => $data
            ];

            return ajax_success_result ('成功', $result);

        } else {
            $page = $this->repository->makeModel ();

            return view ('admin.' . $this->module_name . '.index', compact ('page', 'category_id'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create (Request $request)
    {
        $page         = $this->repository->makeModel ();
        $_method      = 'POST';
        $page->status = StatusEnum::NORMAL;

        return view ('admin.' . $this->module_name . '.add', compact ('page', '_method'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store (Request $request)
    {
        $request->validate ([
            'Page.title'   => 'required',
            'Page.name'    => 'unique:pages,name',
            'Page.content' => 'required',
            'Page.status'  => 'required',
        ], [], [
            'Page.title'   => '标题',
            'Page.name'    => '英文标识',
            'Page.content' => '内容',
            'Page.status'  => '状态',
        ]);
        if (!check_admin_auth ($this->module_name . '_edit')) {
            return auth_error_return ();
        }
        $input = $request->input ('Page');
        $input = $this->formatRequestInput (__FUNCTION__, $input);
        try {
            $input['user_id'] = get_login_user_id ();
            $page             = $this->repository->create ($input);
            if ($page) {
                $log_title = '添加单页面记录';
                Log::createLog (Log::ADD_TYPE, $log_title, '', $page->id, Page::class);

                return ajax_success_result ('添加成功');
            } else {
                return ajax_success_result ('添加失败');
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
     * Display the specified resource.
     *
     * @param \App\Models\Page $page
     * @return \Illuminate\Http\Response
     */
    public function show (Page $page)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Page $page
     * @return \Illuminate\Http\Response
     */
    public function edit (Page $page)
    {
        $_method = 'PUT';

        return view ('admin.' . $this->module_name . '.add', compact ('page', '_method'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Page         $page
     * @return \Illuminate\Http\Response
     */
    public function update (Request $request, Page $page)
    {
        $request->validate ([
            'Page.title'   => 'required',
            'Page.name'    => 'unique:pages,name,' . $page->id,
            'Page.content' => 'required',
            'Page.status'  => 'required',
        ], [], [
            'Page.title'   => '标题',
            'Page.name'    => '英文标识',
            'Page.content' => '内容',
            'Page.status'  => '状态',
        ]);
        $input = $request->input ('Page');
        $input = $this->formatRequestInput (__FUNCTION__, $input);
        try {
            $input['user_id'] = get_login_user_id ();
            $page             = $this->repository->update ($input, $page->id);
            if ($page) {
                $content   = $page->toArray () ?? '';
                $log_title = '修改单页面记录';
                Log::createLog (Log::EDIT_TYPE, $log_title, $content, $page->id, Page::class);

                return ajax_success_result ('修改成功');
            } else {
                return ajax_success_result ('修改失败');
            }

        } catch (BusinessException $e) {
            return ajax_error_result ($e->getMessage ());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     * @throws BusinessException
     */
    public function destroy ($id, Request $request)
    {
        $ids = $request->input ('ids', []);
        if (empty($ids)) {
            $ids[] = $id;
        }
        $ids   = (array)$ids;
        $M     = $this->repository->makeModel ();
        $lists = $M->whereIn ('id', $ids)->get ();
        $num   = 0;
        foreach ($lists as $item) {
            try {
                $this->repository->checkAuth ($item);
            } catch (BusinessException $e) {
                return ajax_error_result ($e->getMessage ());
            }
            $log_title = '删除单页面[' . ($item->category->title ?? '') . '->' . $item->title . ']记录';
            $check     = $this->repository->allowDelete ($item->id);
            if ($check) {
                $ret = $this->repository->delete ($item->id);
                if ($ret) {
                    Log::createLog (Log::DELETE_TYPE, $log_title, $item, $item->id, Page::class);
                    $num++;
                }
            }
        }

        return ajax_success_result ('成功删除' . $num . '条记录');
    }
}
