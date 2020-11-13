<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderSaleLog.
 *
 * @package namespace App\Entities;
 */
class OrderSaleLog extends Model implements Transformable
{
    use TransformableTrait;
    private static $logTime = null;//日志记录时间

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'order_sale_id', 'agent_id', 'admin_id', 'source_id', 'source_type', 'title', 'content', 'created_at'
        ];

    /**
     * 带日期时间插入
     * add by gui
     * @param $at
     * @return mixed
     */
    public static function setLogTime($at, $saleID, $content, $title = '', $source_type = null, $source_id = null)
    {
        self::$logTime = $at;
        return self::createLog($saleID, $content, $title, $source_type, $source_id);
    }

    public static function createLog($saleID, $content, $title = '', $source_type = null, $source_id = null)
    {
        $insArr = [
            'order_sale_id' => $saleID,
            'agent_id'      => get_agent_id(),
            'admin_id'      => get_admin_id(),
            'title'         => $title,
            'content'       => $content,
            'source_id'     => is_null($source_id) ? $saleID : $source_id,
            'source_type'   => is_null($source_type) ? OrderSale::class : $source_type,
        ];
        if (self::$logTime) {
            $insArr['created_at'] = self::$logTime;
        }
        $info = self::create($insArr);
        if (isset($info->id)) {
            $auto = $info->id;
        } else {
            $auto = 0;
        }

        return $auto;
    }
}
