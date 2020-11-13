<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class WxAccount.
 *
 * @package namespace App\Entities;
 */
class WxQrcode extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'scene_id', 'member_id', 'agent_id', 'scene_str', 'expire_seconds', 'url', 'access_id', 'access_type'
    ];

    public function access()
    {
        return $this->morphTo();
    }
}
