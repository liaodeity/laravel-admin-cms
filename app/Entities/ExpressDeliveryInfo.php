<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ExpressDelivery.
 *
 * @package namespace App\Entities;
 */
class ExpressDeliveryInfo extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['delivery_id', 'name', 'delivery_no', 'relation_id', 'relation_type', 'sync_at'];

    public function delivery()
    {
        return $this->belongsTo(ExpressDelivery::class);
    }

    //获取编号
    public static function createInfo($delivery_id, $delivery_no, $relation_type, $relation_id = 0)
    {
        $delivery = ExpressDelivery::find($delivery_id);
        $name     = $delivery->name;
        $insArr   = [
            'delivery_id'   => $delivery_id,
            'name'          => $name,
            'delivery_no'   => $delivery_no,
            'relation_id'   => $relation_id,
            'relation_type' => $relation_type
        ];
        $info     = self::create($insArr);
        if (isset($info->id)) {
            $auto = $info->id;
            Artisan::call('express:sync');//第一时间在家快递信息
        } else {
            $auto = 0;
        }

        return $auto;
    }
}
