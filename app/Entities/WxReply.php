<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class WxReply.
 *
 * @package namespace App\Entities;
 */
class WxReply extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'wx_reply';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content', 'type', 'if_like', 'is_subscribe', 'status'
    ];

    public function statusItem($ind = 'all', $html = false)
    {
        return get_item_parameter('use_status', $ind, $html);
    }

    public function ifLikeItem($ind = 'all', $html = false)
    {
        return get_item_parameter('if_like', $ind, $html);
    }

    public function isSubscribeItem($ind = 'all', $html = false)
    {
        return get_item_parameter('is_subscribe', $ind, $html);
    }

    public function keywords()
    {
        return $this->hasMany(WxReplyKeyword::class,'reply_id');
    }
}
