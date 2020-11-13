<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Role.
 * @package namespace App\Entities;
 */
class RoleInfo extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'auth_infos';
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name',
        'desc',
        'role_id',
        'role_value',
        'status',
    ];

    public function statusItem ($ind = 'all', $html = false)
    {
        return get_item_parameter ('use_status', $ind, $html);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
