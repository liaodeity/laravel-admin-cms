<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2020-04-28
 */

namespace App\Repositories;


use App\Models\Log;
use Illuminate\Database\Eloquent\Model;

class LogRepository extends BaseRepository implements InterfaceRepository
{

    public function model ()
    {
        return Log::class;
    }

    public function allowDelete (Model $model)
    {
        return true;
    }
}
