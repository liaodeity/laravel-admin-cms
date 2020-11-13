<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ExpressApi.
 *
 * @package namespace App\Entities;
 */
class ExpressApi extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['delivery_no','delivery_type','result_json','api_status','delivery_status','is_sign','last_express_at'];
}
