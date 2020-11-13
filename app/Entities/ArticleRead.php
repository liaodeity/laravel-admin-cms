<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ArticleRead.
 *
 * @package namespace App\Entities;
 */
class ArticleRead extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['article_id', 'agent_id', 'is_read'];

    public function isReadItem ($ind = 'all', $html = false)
    {
        if (is_null ($ind)) {
            $ind = 0;
        }

        return get_item_parameter ('is_read', $ind, $html);
    }
}
