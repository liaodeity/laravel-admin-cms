<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class WxAccount.
 *
 * @package namespace App\Entities;
 */
class WxAccount extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'openid', 'nickname', 'sex', 'province', 'city', 'country', 'headimgurl', 'unionid', 'account_id', 'account_type'
        ];

    public function account()
    {
        return $this->morphTo();
    }

    /**
     * 同步修改内容
     * add by gui
     */
    public function syncHeadImg()
    {
        $insArr = WxAccount::where('headimgurl', 'like', 'http%')->first();
        if (isset($insArr->headimgurl)) {
            $openID    = $insArr->openid ?? '';
            $content   = @file_get_contents($insArr->headimgurl);
            $filename  = md5($openID) . '.jpg';
            $directory = 'upload/wx-head';
            @mkdir($directory);
            $path = $directory . '/' . $filename;
            $ret  = @file_put_contents($path, $content);
            if ($ret) {
                $insArr->headimgurl = '/' . $path;
                $insArr->save();
            }
        }
    }
}
