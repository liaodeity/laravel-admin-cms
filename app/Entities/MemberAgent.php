<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class MemberAgent.
 * @package namespace App\Entities;
 */
class MemberAgent extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable
        = [
            'member_id', 'agent_id', 'referrer_member_id', 'lat', 'lng', 'loc_address', 'is_allow_subordinate', 'bill_rate','sp_status'
        ];

    //代理商
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    //推荐人
    public function referrer()
    {
        return $this->belongsTo(Member::class,  'referrer_member_id');
    }

    //图片
    public function pictures()
    {
        return $this->morphMany(Picture::class, 'picture');
    }
    public function bills()
    {
        return $this->belongsTo(Bill::class,'member_id');
    }
    /**
     * 待领取佣金 add by gui
     * @return mixed
     */
    public function noPayBillAmount()
    {
        return Member::find($this->member_id)->bills()->where('bills.agent_id',$this->agent_id)->where('status', Bill::STATUS_NO_PAY)->sum('amount');
    }

    /**
     * 已领取佣金
     * add by gui
     */
    public function yesPayBillAmount()
    {
        return Member::find($this->member_id)->bills()->where('bills.agent_id',$this->agent_id)->where('status', Bill::STATUS_COMPLETE)->sum('amount');
    }
}
