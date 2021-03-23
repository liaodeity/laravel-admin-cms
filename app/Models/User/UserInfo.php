<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2021/3/23
 */

namespace App\Models\User;


use Illuminate\Database\Eloquent\Model;

/**
 * Class UserInfo
 * @package App\Models\User
 */
class UserInfo extends Model
{
    protected $fillable = ['user_id', 'real_name', 'gender', 'telephone', 'address'];
}
