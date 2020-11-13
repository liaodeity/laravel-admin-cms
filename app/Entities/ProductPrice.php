<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ProductPrice.
 *
 * @package namespace App\Entities;
 */
class ProductPrice extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['product_id', 'specification', 'price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
