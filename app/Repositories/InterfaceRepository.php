<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2020/3/12
 */

namespace App\Repositories;


interface InterfaceRepository
{
    public function model ();

    public function validator ();

    public function allowDelete ($id);
}
