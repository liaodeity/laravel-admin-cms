<?php

namespace App\Entities;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderQrcode.
 *
 * @package namespace App\Entities;
 */
class OrderQrcode extends Model implements Transformable
{
    use TransformableTrait;
    const NO_USE_STATUS = 2;//未使用
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'order_id', 'order_sale_id', 'agent_id', 'order_product_id', 'qrcode_no', 'standard_no', 'specification', 'generate_batch', 'price', 'brokerage', 'qrcode_path', 'region_name', 'quality_inspector', 'production_date', 'status'
        ];

    public function statusItem($ind = 'all', $html = false)
    {
        return get_item_parameter('order_qrcode_status', $ind, $html);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderSale()
    {
        return $this->belongsTo(OrderSale::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function logs()
    {
        return $this->hasMany(OrderQrcodeLog::class, 'qrcode_id')->orderBy('created_at', 'DESC')->orderBy('id', 'DESC');
    }

    public function orderProduct()
    {
        return $this->belongsTo (OrderProduct::class);
    }
}
