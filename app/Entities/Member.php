<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Member.
 * @package namespace App\Entities;
 */
class Member extends Model implements Transformable
{
    use TransformableTrait;
    const STATUS_ENABLE = 1;//使用中
    const STATUS_DISABLE = 2;//禁用
    const STATUS_PENDING = 3;//待审核
    const STATUS_FAIL = 4;//审核不通过
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable
        = [
            'member_no',
            'real_name',
            'wx_account',
            'wx_name',
            'wx_qr_code',
            'mobile',
            'birthday',
            'sex',
            'work_type',
            'working_year',
            'native_region_id',
            'resident_region_id',
            'resident_address',
            'business_channel',
            'reg_date',
            'last_login_at',
            'status',
        ];

    public static function showName($id)
    {
        $info = self::find($id);

        return $info->real_name ?? '';
    }

    public static function showMobile ($id)
    {
        $info = self::find($id);

        return $info->mobile ?? '';
    }
    //微信绑定账号
    public function wxAccount ()
    {
        return $this->morphOne (WxAccount::class,'account');
    }

    //状态
    public function statusItem($ind = 'all', $html = false)
    {
        return get_item_parameter('member_status', $ind, $html);
    }

    //性别
    public function genderArray()
    {
        return get_lang_parameter('gender');
    }

    //从业年限
    public function workingYearArray()
    {
        return get_lang_parameter('working_year');
    }

    //工种
    public function workTypeArray()
    {
        return get_lang_parameter('work_type');
    }

    public function agents()
    {
        return $this->hasMany(MemberAgent::class);
    }

    public function nativeRegion ()
    {
        return $this->belongsTo (Region::class,'native_region_id');
    }

    public function residentRegion ()
    {
        return $this->belongsTo (Region::class,'resident_region_id');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * 待领取佣金 add by gui
     * @return mixed
     */
    public function noPayBillAmount()
    {
        return $this->bills()->where('status', Bill::STATUS_NO_PAY)->sum('amount');
    }

    /**
     * 已领取佣金
     * add by gui
     */
    public function yesPayBillAmount()
    {
        return $this->bills()->where('status', Bill::STATUS_COMPLETE)->sum('amount');
    }

    public function allBillAmount()
    {
        $no = $this->noPayBillAmount();
        $yes = $this->yesPayBillAmount();
        return $yes+$no;
    }
    /**
     * 直接人数
     * add by gui
     * @return int
     */
    public function directChildNumber()
    {
        $number = 0;
        if ($this->id) {
            $number = MemberAgent::where('referrer_member_id', $this->id)->count();
        }
        return $number;
    }

    /**
     * 间接人数
     * add by gui
     * @return int
     */
    public function indirectChildNumber()
    {
        $number = 0;
        if ($this->id) {
            $list  = MemberAgent::where('referrer_member_id', $this->id)->get();
            $idArr = [];
            foreach ($list as $item) {
                $idArr[] = $item['member_id'];
            }

            if (!empty($idArr)) {
                $number = MemberAgent::whereIn('referrer_member_id', $idArr)->count();

            }
//            dd($number);

        }
        return $number;
    }
}
