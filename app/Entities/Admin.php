<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class Admin.
 *
 * @package namespace App\Entities;
 */
class Admin extends Model implements Transformable
{
    use TransformableTrait;
    use HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['username', 'password', 'nickname', 'phone', 'status', 'admin_id'];

    protected $guard_name = 'admin'; // 管理员权限标签

    /**
     * 是否超级管理员 add by gui
     * @return bool
     */
    public function isSuperAdmin()
    {
        $auth = $this->hasRole('super');

        return $auth ? true : false;
    }

    public function statusItem($ind = 'all', $html = false)
    {
        return get_item_parameter('use_status', $ind, $html);
    }

    /**
     * 显示名称 add by gui
     * @param $id
     * @return mixed
     */
    public static function showName($id)
    {
        $info = self::find($id);
        return $info->nickname ?? $info->username;
    }
    //微信绑定账号
    public function wxAccount()
    {
        return $this->morphOne(WxAccount::class, 'account');
    }
}
