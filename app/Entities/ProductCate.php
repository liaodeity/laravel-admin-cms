<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ProductCate.
 *
 * @package namespace App\Entities;
 */
class ProductCate extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['cate_name', 'status'];

    public function statusItem ($ind = 'all', $html = false)
    {
        return get_item_parameter ('use_status', $ind, $html);
    }

    public function products()
    {
        return $this->hasMany(Product::class,'cate_id');
    }
}
