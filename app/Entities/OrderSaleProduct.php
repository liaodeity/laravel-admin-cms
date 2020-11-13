<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderSaleProduct.
 * @package namespace App\Entities;
 */
class OrderSaleProduct extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable
        = [
            'order_sale_id',
            'product_id',
            'order_product_id',
            'generate_batch',
            'number',
            'is_develop_member',
            'is_put_card'
        ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class);
    }
}
