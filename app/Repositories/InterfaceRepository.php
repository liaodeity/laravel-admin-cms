<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2020/3/12
 */

namespace App\Repositories;


use Illuminate\Database\Eloquent\Model;

interface InterfaceRepository
{
    public function model ();

    public function allowDelete (Model $model);
}
