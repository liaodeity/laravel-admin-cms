<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ShareQrcode.
 *
 * @package namespace App\Entities;
 */
class ShareQrcode extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type','agent_id','member_id','admin_id','qrcode_no','qrcode_route','random'
    ];

    public function agent ()
    {
        return $this->belongsTo (Agent::class);
    }

    public function member ()
    {
        return $this->belongsTo (Member::class);
    }

    public function admin ()
    {
        return $this->belongsTo (Admin::class);
    }
}
