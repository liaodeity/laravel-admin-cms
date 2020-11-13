<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AgentRegion.
 * @package namespace App\Entities;
 */
class AgentRegion extends Model implements Transformable
{
    use TransformableTrait;
    
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'agent_id',
        'proxy_region_id'
    ];
    
    public function statusItem($ind = 'all', $html = false)
    {
        return get_item_parameter ('use_status', $ind, $html);
    }

    public function region ()
    {
        return $this->belongsTo (Region::class,'proxy_region_id');
    }
}
