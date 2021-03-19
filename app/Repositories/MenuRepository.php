<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2020-04-28
 */

namespace App\Repositories;


use App\Models\Menu;
use App\Validators\MenuValidator;

class MenuRepository extends BaseRepository implements InterfaceRepository
{

    public function model ()
    {
        return Menu::class;
    }

    public function allowDelete ($id)
    {
        return true;
    }
}
