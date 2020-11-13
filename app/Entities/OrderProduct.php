<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderProduct.
 * @package namespace App\Entities;
 */
class OrderProduct extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable
        = [
            'order_id',
            'product_id',
            'product_price_id',
            'title',
            'standard_no',
            'shelf_life',
            'specification',
            'generate_batch',
            'price',
            'number',
            'brokerage',
            'open_brokerage',
            'unit',
            'warehouse',
            'is_develop_member',
            'is_put_card'
        ];
    public function isPutCardItem ($ind = 'all', $html = false)
    {
        return get_item_parameter('is_put_card', $ind, $html);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function orderSaleProduct($saleID)
    {
        return $this->hasOne(OrderSaleProduct::class)->where('order_sale_id', $saleID)->first();
    }
}
