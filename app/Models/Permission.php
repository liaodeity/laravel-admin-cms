<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2021/4/14
 */

namespace App\Models;


use App\Traits\DateTimeFormat;

class Permission extends \Spatie\Permission\Models\Permission
{
    use DateTimeFormat;
}
