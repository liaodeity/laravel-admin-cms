<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebMenu extends Model
{
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
