<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class MessageNotice.
 *
 * @package namespace App\Entities;
 */
class MessageNotice extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
       'type',
        'content',
        'access_id',
        'access_type',
//        'is_tips',
//        'tips_at'
    ];

    public function access()
    {
        return $this->morphTo();
    }
}
