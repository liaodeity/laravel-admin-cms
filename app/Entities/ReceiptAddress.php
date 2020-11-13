<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ReceiptAddress.
 *
 * @package namespace App\Entities;
 */
class ReceiptAddress extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'agent_id','consignee','consignee_phone','region_id','address','is_default'
    ];

    public static function isDefaultItem ($ind = 'all', $html = false)
    {
        return get_item_parameter ('is_default', $ind, $html);
    }

    public function region ()
    {
        return $this->belongsTo (Region::class);
    }

    public function agent ()
    {
        return $this->belongsTo (Agent::class);
    }
}
