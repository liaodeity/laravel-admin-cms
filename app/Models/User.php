<?php

namespace App\Models;

use App\Models\User\UserAdmin;
use App\Models\User\UserInfo;
use App\Models\User\UserMember;
use App\Traits\DateTimeFormat;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles ,DateTimeFormat;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function showName ($id)
    {
        $user = User::find ($id);

        return $user->name ?? '';
    }

    public static function isSuperAdmin ()
    {
        $uid = get_login_user_id ();
        if (empty($uid)) {
            return false;
        }
        $user = User::find ($uid);
        //超级管理员
        $isSuper = $user->hasRole ('super');
        if ($isSuper) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 主管角色 add by gui
     */
    public static function isSupervisor ()
    {
        $uid = get_login_user_id ();
        if (empty($uid)) {
            return false;
        }
        $user = User::find ($uid);
        //主管角色
        $isCheck = $user->hasRole ('supervisor');
        if ($isCheck) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 话务员角色 add by gui
     */
    public static function isOperator ()
    {
        $uid = get_login_user_id ();
        if (empty($uid)) {
            return false;
        }
        $user = User::find ($uid);
        //话务员角色
        $isCheck = $user->hasRole ('operator');
        if ($isCheck) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 只有话务权限，现在个别操作及查询 add by gui
     * @return bool
     */
    public static function onlyOperatorAuth ()
    {
        if(!User::isSupervisor () && User::isOperator ()){
            //只有话务员权限的时候
            return true;
        }else{
            return false;
        }
    }

    //public function statusItem ($ind = 'all', $html = false)
    //{
    //    return get_item_parameter ('user_status', $ind, $html);
    //}
    //
    //public function sexItem ($ind = 'all', $html = false)
    //{
    //    return get_item_parameter ('sex', $ind, $html);
    //}

    public function admin ()
    {
        return $this->hasOne (UserAdmin::class);
    }

    public function member ()
    {
        return $this->hasOne (UserMember::class);
    }
    public function info ()
    {
        return $this->hasOne (UserInfo::class);
    }
}
