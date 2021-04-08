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

namespace App\Services;


use App\Models\Ad;
use App\Models\Article;
use App\Models\Category;
use App\Models\Link;
use App\Models\WebMenu;
use App\Repositories\MenuRepository;
use App\Repositories\WebMenuRepository;
use function foo\func;

class HomeService
{
    /**
     * @var WebMenuRepository
     */
    private $webMenuRepository;

    public function __construct (WebMenuRepository $webMenuRepository)
    {

        $this->webMenuRepository = $webMenuRepository;
    }

    /**
     * 获取前台菜单 add by gui
     * @return array
     */
    public function menu ()
    {
        return $this->webMenuRepository->getHomeMenuList ();
    }

    /**
     * 获取前台获取当前位置 add by gui
     */
    public function getNavPathMenuId ($page_id = 0, $cate_id = 0)
    {

        if ($page_id) {
            $menu = WebMenu::where ('page_id', $page_id)->first ();
        }
        if ($cate_id) {
            $menu = WebMenu::where ('category_id', $cate_id)->first ();
        }

        return $menu->id ?? null;
    }

    /**
     * 获取菜单的当前地址列表 add by gui
     * @param $menu_id
     * @return array
     */
    public function getNavPath ($menu_id)
    {
        if (empty($menu_id)) {
            return [];
        }

        return $this->webMenuRepository->getNavPath ($menu_id);
    }

    /**
     * 获取上一次分类列表 add by gui
     * @param $menu_id
     * @return array
     */
    public function getPidCategoryList ($menu_id, $menu_pid = 0)
    {
        if (empty($menu_id) && empty($menu_pid)) {
            return [];
        }
        if (empty($menu_pid)) {
            $cate = WebMenu::where ('id', $menu_id)->first ();
            $pid  = $cate->pid ?? 0;
        } else {
            $pid = $menu_pid;
        }

        $menus    = $this->webMenuRepository->getHomeMenuList ();
        $nav_cate = [];
        foreach ($menus as $menu) {
            if ($menu->id == $pid) {
                $nav_cate = $menu;
                break;
            }
            //第二层
            $child2 = array_get ($menu, '_child', []);
            foreach ($child2 as $item2) {
                //var_dump($item2->id);
                if ($item2->id == $pid) {
                    $nav_cate = $item2;
                    break;
                }
                //第三层

                $child3 = array_get ($item2, '_child', []);
                foreach ($child3 as $item3) {
                    if ($item3->id == $pid) {
                        $nav_cate = $item3;
                        break;
                    }
                }
            }
        }
        $nav_cate->_active_id = $menu_id;

        return $nav_cate;

    }

    /**
     * 获取分类上一篇文章 add by gui
     */
    public function getArticlePrev (Article $article)
    {
        $cate_id = $article->category_id ?? 0;
        $prev    = Article::where ('category_id', $cate_id)
            ->where ('id', '>', $article->id)
            ->where ('status', 1)
            ->orderBy ('created_at', 'DESC')->first ();
        if ($prev) {
            return '<a href="' . url ('article/detail/' . $prev->id) . '">' . $prev->title . '</a>';
        } else {
            return '无';
        }
    }

    /**
     * 获取分类上一篇文章 add by gui
     */
    public function getArticleNext (Article $article)
    {
        $cate_id = $article->category_id ?? 0;
        $prev    = Article::where ('category_id', $cate_id)
            ->where ('id', '<', $article->id)
            ->where ('status', 1)
            ->orderBy ('created_at', 'DESC')->first ();
        if ($prev) {
            return '<a href="' . url ('article/detail/' . $prev->id) . '">' . $prev->title . '</a>';
        } else {
            return '无';
        }
    }

    /**
     * 获取子分类ID add by gui
     * @param $cate_pid
     * @return array
     */
    public function getArticleChildId ($cate_pid)
    {

        //查询推荐的上一层分类的所有下级
        $pidArr = Category::where ('parent_id', $cate_pid)->where ('status', 1)->get ()
            ->map (function ($value) {
                return $value->id;
            })->toArray ();

        return $pidArr ?? [];
    }

    /**
     * 获取文字相关推荐 add by gui
     */
    public function getArticleRelevantList (Article $article)
    {
        $cate_id  = $article->category_id ?? 0;
        $cate     = Category::find ($cate_id);
        $cate_pid = $cate->parent_id ?? $cate_id;
        $pidArr   = $this->getArticleChildId ($cate_pid);
        $list     = Article::whereIn ('category_id', $pidArr)
            ->where ('id', '<>', $article->id)
            ->where ('is_relevant', 1)
            ->where ('status', 1)
            ->orderBy ('created_at', 'DESC')->paginate (5);

        return $list ?? [];
    }

    /**
     * 获取友情链接 add by gui
     * @return mixed
     */
    public function getLinks ()
    {
        $links = Link::where ('status', 1)->orderBy ('display_order', 'ASC')->get ();

        return $links;
    }

    /**
     * 获取SEO信息 add by gui
     * @param null  $article
     * @param null  $page
     * @param array $category
     * @return object
     */
    public function getSeoData ($article = null, $page = null, $category = null, $menu = null)
    {
        $seo_title       = get_config_value ('seo_title', '');
        $seo_keyword     = get_config_value ('seo_keyword', '');
        $seo_description = get_config_value ('seo_description', '');
        $title           = '';
        $keyword         = '';
        $description     = '';
        if (is_null ($category)) {
            $category = [];
        }
        if (!is_null ($menu)) {
            $title       = $menu->title ?? '';
            $keyword     = object_get ($menu, 'keyword', '');
            $description = object_get ($menu, 'description', '');
        }
        if (!is_null ($article)) {
            $category    = $article->category ?? [];
            $title       = $article->title ?? '';
            $keyword     = object_get ($article, 'keyword', '');
            $description = object_get ($article, 'description', '');
        }
        if (!is_null ($page)) {
            $category    = $page->category ?? [];
            $title       = $page->title ?? '';
            $keyword     = object_get ($page, 'keyword', '');
            $description = object_get ($page, 'description', '');
        }
        $category_name = $category->title ?? '';
        if ($category_name) {
            $seo_title = $category_name . '-' . $seo_title;
        }
        if ($title && $title != $category_name) {
            $seo_title = $title . '-' . $seo_title;
        }

        if ($keyword) {
            $seo_keyword = $keyword;
        }
        if ($description) {
            $seo_description = $description;
        }

        $seo = (object)[
            'title'       => $seo_title,
            'keyword'     => $seo_keyword,
            'description' => $seo_description
        ];

        //dd ($category);

        return $seo;
    }

    /**
     * 获取首页广告位轮播图 add by gui
     */
    public function getHomeIndexAd ()
    {
        $list = Ad::where ('type', 'index')->where ('status', 1)->orderBy ('display_order', 'ASC')->get ();

        return $list;
    }
}
