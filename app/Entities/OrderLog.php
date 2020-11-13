<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderLog.
 *
 * @package namespace App\Entities;
 */
class OrderLog extends Model implements Transformable
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
            'order_id', 'agent_id', 'admin_id', 'source_id', 'source_type', 'title', 'content', 'created_at'
        ];


    public static function createLog($orderID, $content, $title = '', $source_type = null, $source_id = null)
    {
        $insArr = [
            'order_id'    => $orderID,
            'agent_id'    => get_agent_id(),
            'admin_id'    => 0,
            'title'       => $title,
            'content'     => $content,
            'source_id'   => is_null($source_id) ? $orderID : $source_id,
            'source_type' => is_null($source_type) ? Order::class : $source_type,

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

    public static function createAdminLog($orderID, $content, $title = '', $source_type = null, $source_id = null)
    {
        $admin_id = get_admin_id();
        $admin_id = empty($admin_id) ? 1 : $admin_id;
        $insArr   = [
            'order_id'    => $orderID,
            'agent_id'    => 0,
            'admin_id'    => $admin_id,
            'title'       => $title,
            'content'     => $content,
            'source_id'   => is_null($source_id) ? $orderID : $source_id,
            'source_type' => is_null($source_type) ? Order::class : $source_type,

        ];
        $info     = self::create($insArr);
        if (isset($info->id)) {
            $auto = $info->id;
        } else {
            $auto = 0;
        }

        return $auto;
    }

    /**
     * 带日期时间插入
     * @param null $logTime
     */
    public static function setLogTime($logTime, $orderID, $content, $title = '', $source_type = null, $source_id = null)
    {
        self::$logTime = $logTime;
        return self::createLog($orderID, $content, $title, $source_type, $source_id);
    }
}
