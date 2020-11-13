<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderQrcodeLog.
 *
 * @package namespace App\Entities;
 */
class OrderQrcodeLog extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'qrcode_id', 'member_id', 'wx_account_id', 'source_id', 'source_type', 'title', 'content'
    ];

    public static function createWxAccountLog ($qrcode_id, $wx_account_id, $title, $content)
    {

        $insArr = [
            'qrcode_id'     => $qrcode_id,
            'source_id'     => $wx_account_id,
            'source_type'   => WxAccount::class,
            'title'         => $title,
            'content'       => $content,
            'wx_account_id' => $wx_account_id ?? 0,
        ];

        return self::create ($insArr);
    }

    public static function createMemberLog ($qrcode_id, $member_id, $title, $content)
    {
        $member_id = $member_id ? $member_id : get_member_id ();
        $insArr    = [
            'qrcode_id'   => $qrcode_id,
            'source_id'   => $member_id,
            'source_type' => Member::class,
            'title'       => $title,
            'content'     => $content,
            'member_id'   => $member_id ?? 0,
        ];

        return self::create ($insArr);
    }
}
