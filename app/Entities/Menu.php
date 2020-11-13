<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Menu.
 *
 * @package namespace App\Entities;
 */
class Menu extends Model implements Transformable
{
    use TransformableTrait;
    const MENU_TYPE_AUTH = 'auth';
    const MENU_TYPE_MENU = 'menu';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['pid', 'auth_name', 'module', 'type', 'sort', 'route_url', 'title', 'icon', 'status'];

    protected $guarded = [];

}
