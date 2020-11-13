<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Article.
 *
 * @package namespace App\Entities;
 */
class Article extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'content', 'push_source', 'view_number', 'status'];

    public function statusItem ($ind = 'all', $html = false)
    {
        return get_item_parameter ('show_status', $ind, $html);
    }

    public function reads ()
    {
        return $this->hasMany (ArticleRead::class);
    }
}
