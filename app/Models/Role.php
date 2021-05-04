<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2021/4/14
 */

namespace App\Models;


use App\Traits\DateTimeFormat;

class Role extends \Spatie\Permission\Models\Role
{
    use DateTimeFormat;
}
