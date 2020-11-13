<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use function foo\func;

/**
 * Class Order.
 * @package namespace App\Entities;
 */
class Order extends Model implements Transformable
{
    use TransformableTrait;
    const COMPLETE_STATUS = 1;//已完成
    const NO_PAY_STATUS = 2;//未付款
    const NO_DELIVERY_STATUS = 3;//已付款未发货
    const YES_DELIVERY_STATUS = 5;//已发货
    const CANCEL_STATUS = 4;//已取消
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable
        = [
            'order_no',
            'agent_id',
            'order_amount',
            'pay_amount',
            'consignee',
            'consignee_phone',
            'consignee_region_id',
            'consignee_address',
            'remark',
            'agent_remark',
            'verify_at',
            'delivery_at',
            'receipt_at',
            'is_account_pay',
            'is_effective',
            'status',

        ];

    /**
     * 是否未付款
     * add by gui
     * @param $status
     * @return bool
     */
    public static function isNoPay ($status)
    {
        return $status == self::NO_PAY_STATUS ? true : false;
    }

    /**
     * 是否已付款未发货
     * add by gui
     * @param $status
     * @return bool
     */
    public static function isNoDelivery ($status)
    {
        return $status == self::NO_DELIVERY_STATUS ? true : false;
    }

    /**
     * 可处理订单
     * add by gui
     * @param $status
     */
    public static function isDeal ($status)
    {
        $ret = false;
        switch ($status) {
            case self::NO_DELIVERY_STATUS:
            case self::YES_DELIVERY_STATUS:
                $ret = true;
                break;
        }
        return $ret;
    }

    /**
     * 可显示二维码状态
     * add by gui
     * @param $status
     * @return bool
     */
    public static function isShowQrcode ($status)
    {
        $ret = false;
        switch ($status) {
            case self::NO_DELIVERY_STATUS:
            case self::YES_DELIVERY_STATUS:
            case self::COMPLETE_STATUS:
                $ret = true;
                break;
        }
        return $ret;
    }

    public function statusItem ($ind = 'all', $html = false)
    {
        return get_item_parameter ('order_status', $ind, $html);
    }

    public function isAccountPayItem ($ind = 'all', $html = false)
    {
        return get_item_parameter ('is_account_pay', $ind, $html);
    }

    //快递信息
    public function delivery ()
    {
        return $this->morphOne (ExpressDeliveryInfo::class, 'relation');
    }

    //代理商
    public function agent ()
    {
        return $this->hasOne (Agent::class, 'id', 'agent_id');
    }

    public function region ()
    {
        return $this->belongsTo (Region::class, 'consignee_region_id');
    }

    //产品明细
    public function products ()
    {
        return $this->hasMany (OrderProduct::class, 'order_id', 'id');
    }

    //订单产品数量
    public function productSumNumber ()
    {
        return $this->products ()->sum ('number');
    }
    //不发展会员数量，无需生成二维码
    //不放卡，无需生成二维码
    public function productNoMemberSumNumber ()
    {
        return $this->products ()->where (function ($query) {
            $query->where ('is_develop_member', 0)
                ->orWhere ('is_put_card', 0);
        })->sum ('number');
    }

    //二维码数量
    public function getQrCodeNumber ()
    {
        $all = $this->productSumNumber ();
        $no  = $this->productNoMemberSumNumber ();
        return $all - $no;
    }

    public function productsPriceSum ()
    {
        return $this->products ()->sum (DB::raw ('price*number'));
    }

    public function logs ()
    {
        return $this->hasMany (OrderLog::class)->orderBy ('created_at', 'DESC')->orderBy ('id', 'DESC');
    }

    public function qrcodes ()
    {
        return $this->hasMany (OrderQrcode::class)->where ('order_sale_id', 0);
    }

    /*
     * 售后记录
     */
    public function sales ()
    {
        return $this->hasMany (OrderSale::class);
    }

    //成功生成二维码个数
    public function qrcodeSuccessCount ()
    {
        return $this->qrcodes ()
            ->where ('qrcode_path', '<>', '')->where ('status', 1)->count ();
    }

    /**
     * 获取订单显示明细
     * add by gui
     * @param $orderID
     * @return \Illuminate\Support\Collection
     */
    public function itemOrderProducts ($orderID)
    {
        $list = DB::table ('order_products')->selectRaw ('order_id,product_id, count(product_id) as count')
            ->where ('order_id', $orderID)
            ->groupBy ('order_id', 'product_id')->get ();
        foreach ($list as $key => &$item) {
            $products       = OrderProduct::where ('product_id', $item->product_id)
                ->where ('order_id', $item->order_id)->orderBy ('price', 'ASC')->get ();
            $item->products = $products;
        }
        return $list;
    }
}
