<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class PayTrade.
 *
 * @package namespace App\Entities;
 */
class PayTrade extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'type', 'trade_no','openid','price','memberID','access_id','access_type','result_json','notify_result_json',
        'trade_at','trade_open_id','trade_price','transaction_no','status'
    ];
    public function typeItem ($ind = 'all', $html = false)
    {
        return get_item_parameter ('pay_trade_type', $ind, $html);
    }

    public function access()
    {
        return $this->morphTo();
    }
}
