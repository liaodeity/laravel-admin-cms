<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class Agent.
 * @package namespace App\Entities;
 */
class Agent extends Model implements Transformable
{
    use TransformableTrait;
    use HasRoles;

    protected $guard_name = 'agent'; // 代理商权限标签
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable
        = [
            'username',
            'agent_no',
            'agent_name',
            'password',
            'birthday',
            'wx_name',
            'company_name',
            'contact_name',
            'contact_phone',
            'office_region_id',
            'office_address',
            'authorize_no',
            'authorize_date',
            'is_forever_authorize',
            'join_date',
            'status',
            'admin_id'
        ];

    /**
     * 是否超级管理员 add by gui
     * @return bool
     */
    public function isAgentRole()
    {
        $auth = $this->hasRole($this->guard_name . '_manager');

        return $auth ? true : false;
    }

    /**
     * 检查是否代理商，自动添加代理商角色
     * add by gui
     */
    public function checkAgentRole()
    {
        if (!$this->isAgentRole()) {
            //非代理商角色
            $this->assignRole([$this->guard_name . '_manager']);
        }
    }

    public function statusItem($ind = 'all', $html = false)
    {
        return get_item_parameter('use_status', $ind, $html);
    }

    public function regions()
    {
        return $this->hasMany(AgentRegion::class);
    }

    public function officeRegion()
    {
        return $this->belongsTo(Region::class,'office_region_id');
    }

    //微信绑定账号
    public function wxAccount()
    {
        return $this->morphOne(WxAccount::class, 'account');
    }

    public function memberAgents()
    {
        return $this->hasMany(MemberAgent::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
    public static function showName($id)
    {
        $info = self::find($id);
        return $info->agent_name ?? '';
    }
    public static function showMobile($id)
    {
        $info = self::find($id);
        return $info->contact_phone ?? '';
    }
    /**
     * 直接下线人数
     * add by gui
     * @return int
     */
    public function directChildNumber()
    {
        $number = 0;
        if ($this->id) {
            $number = $this->memberAgents()->where('agent_id',$this->id)->where('referrer_member_id', 0)->count();
        }
        return $number;
    }

    /**
     * 间接下线人数
     * add by gui
     * @return int
     */
    public function indirectChildNumber()
    {
        $number = 0;
        if ($this->id) {
            $number = $this->memberAgents()->where('agent_id',$this->id)->where('referrer_member_id', '>',0)->count();
        }
        return $number;
    }
}
