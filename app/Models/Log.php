<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    //1=登录、2=添加、3=查看、4=删除、5=修改、6=日志、7=异常、8=>待办日志
    const LOGIN_TYPE  = 1;
    const ADD_TYPE    = 2;
    const VIEW_TYPE   = 3;
    const DELETE_TYPE = 4;
    const EDIT_TYPE   = 5;
    const INFO_TYPE   = 6;
    const ERROR_TYPE  = 7;
    const TO_DO_TYPE  = 8;
    protected $fillable = ['content', 'created_at', 'source_id', 'source_type', 'title', 'type', 'updated_at', 'user_id'];

    public function typeItem ($ind = 'all', $html = false)
    {
        return  get_item_parameter ('log_type', $ind, $html);
    }

    /**
     * 新增记录日志
     * add by gui
     * @param integer $type        类型
     * @param         $title
     * @param string  $content     日志
     * @param null    $source_id   来源ID
     * @param null    $source_type 来源模型，如User::class
     * @return mixed
     */
    public static function createLog ($type, $title, $content, $source_id = null, $source_type = null)
    {
        $user_id = get_login_user_id ();
        if (!is_string ($content)) {
            $content = json_encode ($content);
        }
        $insArr = [
            'type'        => $type,
            'title'       => $title,
            'content'     => $content ?? '',
            'user_id'     => $user_id ?? 0,
            'source_id'   => $source_id ?? 0,
            'source_type' => $source_type ?? ''
        ];

        return self::create ($insArr);
    }
}
