<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2021/3/31
 */

namespace App\Repositories;


use App\Models\User\UserMember;

class UserMemberRepository extends BaseRepository implements InterfaceRepository
{

    public function model ()
    {
        return UserMember::class;
    }

    public function allowDelete ($id)
    {
        return true;
    }

}
