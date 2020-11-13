<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderSale.
 *
 * @package namespace App\Entities;
 */
class OrderSale extends Model implements Transformable
{
    use TransformableTrait;
    const COMPLETE_STATUS = 1;
    const NO_DEAL = 2;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'order_id', 'agent_id', 'sale_no', 'apply_sale_at', 'sale_delivery_id', 'apply_desc', 'process_desc', 'send_back_delivery_id', 'status'
        ];

    public function statusItem($ind = 'all', $html = false)
    {
        return get_item_parameter('order_sale_status', $ind, $html);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

//    售后快递
    public function saleDelivery()
    {
        return $this->morphOne(ExpressDeliveryInfo::class, 'relation')->where('id', $this->sale_delivery_id);
    }

//寄回快递
    public function sendBackDelivery()
    {
        return $this->morphOne(ExpressDeliveryInfo::class, 'relation')->where('id', $this->send_back_delivery_id);
    }

    public function logs ()
    {
        return $this->hasMany (OrderSaleLog::class)->orderBy('created_at', 'DESC')->orderBy('id', 'DESC');
    }
    public function qrcodes()
    {
        return $this->hasMany(OrderQrcode::class);
    }

    public function products ()
    {
        return $this->hasMany (OrderSaleProduct::class);
    }
    //订单产品数量
    public function productSumNumber()
    {
        return $this->products()->sum('number');
    }
    //成功生成二维码个数
    public function qrcodeSuccessCount()
    {
        return $this->qrcodes()
            ->where('qrcode_path', '<>', '')->where('status',1)->count();
    }
    //不发展会员数量，无需生成二维码
    //不发卡，无需生成二维码
    public function productNoMemberSumNumber()
    {
        return $this->products()->where (function ($query) {
            $query->where ('is_develop_member', 0)
                ->orWhere ('is_put_card', 0);
        })->sum('number');
    }
    //二维码数量
    public function getQrCodeNumber()
    {
        $all = $this->productSumNumber();
        $no = $this->productNoMemberSumNumber();
        return $all - $no;
    }
    /**
     * 获取订单显示明细
     * add by gui
     * @param $orderSaleID
     */
    public function itemOrderProducts($orderSaleID)
    {
        $list = DB::table('order_sale_products')->selectRaw('order_sale_id,product_id, count(product_id) as count')
            ->where('order_sale_id', $orderSaleID)
            ->groupBy('order_sale_id', 'product_id')->get();
        foreach ($list as $key => &$item) {
            $products       = OrderProduct::where('product_id', $item->product_id)
                ->where('order_id', $item->order_sale_id)->orderBy('price', 'ASC')->get();
            $item->products = $products;
        }
        return $list;
    }
}
