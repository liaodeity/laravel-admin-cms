<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2021/3/31
 */

namespace App\Models\User;


use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserMember extends Model
{
    protected $fillable = ['user_id', 'login_count', 'last_login_at', 'status'];

    public function user ()
    {
        return $this->belongsTo (User::class);
    }
}
