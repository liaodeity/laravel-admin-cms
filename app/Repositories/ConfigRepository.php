<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2020-04-28
 */

namespace App\Repositories;


use App\Models\Config;
use App\Validators\ConfigValidator;

class ConfigRepository extends BaseRepository implements InterfaceRepository
{

    public function model ()
    {
        return Config::class;
    }

    public function allowDelete ($id)
    {
        return true;
    }
}
