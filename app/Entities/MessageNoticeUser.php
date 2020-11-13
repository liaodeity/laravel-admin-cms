<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class MessageNoticeUser.
 *
 * @package namespace App\Entities;
 */
class MessageNoticeUser extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'openid',
        'notice_id',
        'access_id',
        'access_type',
        'is_tips',
        'tips_at'
    ];

    public function access()
    {
        return $this->morphTo();
    }
}
