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
namespace App\Models;

use App\Traits\DateTimeFormat;
use Illuminate\Database\Eloquent\Model;

class WebMenu extends Model
{
    use DateTimeFormat;
    protected $fillable = ['id','pid','link_label','title','href','target','category_id','page_id','status','sort','user_id','created_at','updated_at','keyword','description'];

    public function statusItem ($ind = 'all', $html = false)
    {
        return get_item_parameter ('web_menu_status', $ind, $html);
    }
    public function targetItem ($ind = 'all', $html = false)
    {
        return get_item_parameter ('web_menu_target', $ind, $html);
    }

    public function pidMenu ()
    {
        return $this->belongsTo (WebMenu::class,'pid');
    }

    public function category ()
    {
        return $this->belongsTo (Category::class);
    }

    public function page ()
    {
        return $this->belongsTo (Page::class);
    }
}
