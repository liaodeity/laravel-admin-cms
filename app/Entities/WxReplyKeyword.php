<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class WxReplyKeyword.
 *
 * @package namespace App\Entities;
 */
class WxReplyKeyword extends Model implements Transformable
{
    use TransformableTrait;

//    protected $table = 'wx_reply_';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['reply_id', 'keyword'];
}
