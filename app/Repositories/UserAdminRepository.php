<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2020-04-28
 */

namespace App\Repositories;


use App\Models\User;

class UserAdminRepository extends BaseRepository implements InterfaceRepository
{

    public function model ()
    {
        return User\UserAdmin::class;
    }

    public function allowDelete ($id)
    {
        return true;
    }
}
