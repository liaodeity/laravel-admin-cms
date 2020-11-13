<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class TemplateMessageLog.
 *
 * @package namespace App\Entities;
 */
class TemplateMessageLog extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'template_message_id', 'agent_id', 'member_id', 'admin_id', 'open_id', 'send_data','result_data','status'
    ];

}
