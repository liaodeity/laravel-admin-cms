<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2019/12/31
 */

namespace App\Entities;


use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Traits\TransformableTrait;

class Coordinate extends Model
{
    use TransformableTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lat','lng','province','city','district','town','adcode','address'
    ];
}
