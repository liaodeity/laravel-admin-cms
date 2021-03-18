<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = ['category_id','user_id','username','title','is_relevant','subhead','smalltitle','keyword','copy_from','from_link','link_url','description','content','tags','template','attach','attach_image','attach_thumb','istop','status','recommend','display_order','view_count','created_at','updated_at'];
    protected $dates = ['created_at'];
    public function statusItem ($ind = 'all', $html = false)
    {
        return get_item_parameter ('article_status', $ind, $html);
    }
    public function isTopItem ($ind = 'all', $html = false)
    {
        return get_item_parameter ('article_is_top', $ind, $html);
    }

    public function isRelevantItem ($ind = 'all', $html = false)
    {
        return get_item_parameter ('article_is_relevant', $ind, $html);
    }

    public function category ()
    {
        return $this->belongsTo (Category::class);
    }

}
