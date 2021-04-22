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
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Libs\QueryWhere;
use App\Models\Article;
use App\Models\Category;
use App\Models\Log;
use App\Models\User;
use App\Repositories\ArticleRepository;
use App\Validators\ArticleValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ArticleController extends Controller
{
    protected $module_name = 'article';
    /**
     * @var ArticleRepository
     */
    private $repository;

    public function __construct (ArticleRepository $repository)
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
        if (request ()->ajax ()) {
            $limit = $request->input ('limit', 15);
            QueryWhere::defaultOrderBy ('articles.id', 'DESC')->setRequest ($request->all ());
            $M = $this->repository->makeModel ()->select ('articles.*');
            QueryWhere::eq ($M, 'articles.category_id', $category_id);
            QueryWhere::eq ($M, 'articles.status');
            QueryWhere::eq ($M, 'articles.istop');
            QueryWhere::like ($M, 'articles.title');
            QueryWhere::like ($M, 'articles.link_label');
            QueryWhere::orderBy ($M);

            $M     = $M->paginate ($limit);
            $count = $M->total ();
            $data  = $M->items ();
            foreach ($data as $key => $item) {
                $data[ $key ]['_url']   = url ('article/detail/' . $item->id);
                $data[ $key ]['status'] = $item->statusItem ($item->status);
                $data[ $key ]['istop']  = $item->isTopItem ($item->istop);
            }
            $result = [
                'count' => $count,
                'data'  => $data
            ];

            return ajax_success_result ('成功', $result);

        } else {
            $article = $this->repository->makeModel ();

            return view ('admin.' . $this->module_name . '.index', compact ('article', 'category_id'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create (Request $request)
    {
        $article              = $this->repository->makeModel ();
        $_method              = 'POST';
        $article->category_id = $request->input ('category_id', 0);
        $article->user_id     = get_login_user_id ();
        $article->username    = User::showName (get_login_user_id ());

        return view ('admin.' . $this->module_name . '.add', compact ('article', '_method'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store (Request $request)
    {
        $input = $request->input ('Article');
        $input = $this->formatRequestInput (__FUNCTION__, $input);
        try {
            $this->repository->makeValidator ()->with ($input)->passes (ArticleValidator::RULE_CREATE);
            $attach_image = array_get ($input, 'attach_image');
            if (empty($attach_image)) {
                $cate_id  = array_get ($input, 'category_id');
                $category = Category::find ($cate_id);
                if (object_get ($category, 'template') == 'news_activity') {
                    //活动必须有封面图片
                    throw new BusinessException('分类[' . $category->title . ']必须要有文章封面图片');
                }
            }
            $input['user_id'] = get_login_user_id ();
            $ret              = $this->repository->create ($input);
            if ($ret) {
                $log_title = '添加资讯文章[' . ($ret->category->title ?? '') . '->' . $ret->title . ']记录';
                Log::createLog (Log::ADD_TYPE, $log_title, '', $ret->id, Article::class);

                return ajax_success_result ('添加成功');
            } else {
                return ajax_success_result ('添加失败');
            }

        } catch (BusinessException $e) {
            return ajax_error_result ($e->getMessage ());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Article $article
     * @return \Illuminate\Http\Response
     */
    public function show (Article $article)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Article $article
     * @return \Illuminate\Http\Response
     */
    public function edit (Article $article)
    {
        $_method = 'PUT';

        return view ('admin.' . $this->module_name . '.add', compact ('article', '_method'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Article      $article
     * @return \Illuminate\Http\Response
     */
    public function update (Request $request, Article $article)
    {
        $input = $request->input ('Article');
        $input = $this->formatRequestInput (__FUNCTION__, $input);
        try {
            $this->repository->makeValidator ()->with ($input)->passes (ArticleValidator::RULE_UPDATE);
            $attach_image = array_get ($input, 'attach_image');
            if (empty($attach_image)) {
                $cate_id  = array_get ($input, 'category_id');
                $category = Category::find ($cate_id);
                if (object_get ($category, 'template') == 'news_activity') {
                    //活动必须有封面图片
                    throw new BusinessException('分类[' . $category->title . ']必须要有文章封面图片');
                }
            }

            $input['user_id'] = get_login_user_id ();
            $ret              = $this->repository->update ($input, $article->id);
            if ($ret) {
                $content   = $article->toArray () ?? '';
                $log_title = '修改资讯文章[' . ($ret->category->title ?? '') . '->' . $ret->title . ']记录';
                Log::createLog (Log::EDIT_TYPE, $log_title, $content, $ret->id, Article::class);

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
            $log_title = '删除资讯文章[' . ($item->category->title ?? '') . '->' . $item->title . ']记录';
            $check     = $this->repository->allowDelete ($item->id);
            if ($check) {
                $ret = $this->repository->delete ($item->id);
                if ($ret) {
                    Log::createLog (Log::DELETE_TYPE, $log_title, $item, $item->id, Article::class);
                    $num++;
                }
            }
        }

        return ajax_success_result ('成功删除' . $num . '条记录');
    }

    private function formatRequestInput (string $__FUNCTION__, $input)
    {
        return $input;
    }
}
