<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Bill.
 *
 * @package namespace App\Entities;
 */
class Bill extends Model implements Transformable
{
    use TransformableTrait;
    const STATUS_COMPLETE = 1;
    const STATUS_INVALID  = 4;
    const STATUS_NO_PAY   = 2;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'pid', 'agent_id', 'product_id', 'order_id', 'order_product_id', 'order_sale_id', 'qrocde_id', 'member_id', 'bill_no', 'amount', 'bill_at', 'lat', 'lng', 'scan_address', 'verity_at', 'status'
        ];

    public function statusItem ($ind = 'all', $html = false)
    {
        return get_item_parameter ('bill_status', $ind, $html);
    }

    public function member ()
    {
        return $this->belongsTo (Member::class);
    }

    public function agent ()
    {
        return $this->belongsTo (Agent::class);
    }

    public function product ()
    {
        return $this->belongsTo (Product::class);
    }

    public function orderProduct ()
    {
        return $this->belongsTo (OrderProduct::class);
    }

    public function qrcode ()
    {
        return $this->belongsTo (OrderQrcode::class, 'qrocde_id');
    }
}
