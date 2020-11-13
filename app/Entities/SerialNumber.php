<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class SerialNumber.
 * @package namespace App\Entities;
 */
class SerialNumber extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'serial_id',
        'serial_type',
    ];

    /**
     * 更新模型的流水号关系 add by gui
     * @param $model
     * @param $id
     * @param $field
     * @return bool
     * @throws \ErrorException
     */
    public static function authUpdateToSourceNo ($model, $id, $field)
    {

        $M = app ($model)->find ($id);
        if (empty($M)) {
            throw new \ErrorException('信息不存在，无法生存编号');
        }
        $no        = self::autoNumber ($model, $id);
        $M->$field = $no;
        $ret       = $M->save ();
        if (!$ret) {
            throw new \ErrorException('更新编号异常');
        }

        return true;
    }

    //获取编号
    public static function autoNumber($type, $id = 0)
    {
        $insArr = [
            'serial_id'   => $id,
            'serial_type' => $type,
        ];
        $serial = self::create($insArr);
        if (isset($serial->id)) {
            $auto = $serial->id;
        } else {
            Log::createLog(Log::DEBUG_TYPE, $type . '生成编号异常');
            $auto = 0;
        }

        return $auto;
    }

    /**
     * 更新关联ID
     * add by gui
     * @param $id
     * @param $serial_id
     * @return bool
     */
    public static function updateSerialID($id, $serial_id)
    {
        $ret = self::where('id', $id)->update(['serial_id' => $serial_id]);
        if (!$ret) {
            Log::createLog(Log::DEBUG_TYPE, $id . '更新关联ID失败' . $serial_id);
        }

        return $ret ? true : false;
    }
}
