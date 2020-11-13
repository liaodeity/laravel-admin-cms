<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ExpressDelivery.
 *
 * @package namespace App\Entities;
 */
class ExpressDelivery extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'com_code', 'sort'];

    public static function getList()
    {
        return self::orderBy('sort', 'ASC')->get();

    }

    public function codeItem($ind = 'all', $html = false)
    {
        return get_item_parameter('express_delivery', $ind, $html);
    }

    /**
     * 常用推荐的标识
     */
    public function usedCodeItem()
    {
        $arr    = get_item_parameter('used_express_delivery', 'all', false);
        $allArr = $this->codeItem();
        foreach ($allArr as $key => $item) {
            if (!in_array($key, $arr)) {
                unset($allArr[$key]);
            }
        }
        return $allArr;
    }
}
