<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Log.
 * @package namespace App\Entities;
 */
class Log extends Model implements Transformable
{
    use TransformableTrait;
    const LOGIN_TYPE = '登录';
    const ADD_TYPE = '添加';
    const SHOW_TYPE = '查看';
    const EDIT_TYPE = '修改';
    const DELETE_TYPE = '删除';
    const LOG_TYPE = '信息日志';
    const DEBUG_TYPE = '异常日志';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable
        = [
            'type',
            'content',
            'admin_id',
            'agent_id',
            'member_id',
        ];

    public function typeItem($ind = 'all', $html = false)
    {
        $arr = [
            self::LOGIN_TYPE  => self::LOGIN_TYPE,
            self::ADD_TYPE    => self::ADD_TYPE,
            self::EDIT_TYPE   => self::EDIT_TYPE,
            self::SHOW_TYPE   => self::SHOW_TYPE,
            self::DELETE_TYPE => self::DELETE_TYPE,
            self::LOG_TYPE    => self::LOG_TYPE,
            self::DEBUG_TYPE  => self::DEBUG_TYPE
        ];
        if ($ind !== 'all') {
            $text = array_key_exists($ind, $arr) ? $arr[$ind] : null;

            return $html === true ? status_html($text) : $text;
        }

        return $arr;
    }

    /**
     * 新增记录日志
     * add by gui
     * @param $type
     * @param $content
     * @return mixed
     */
    public static function createLog($type, $content)
    {
        $admin_id = get_admin_id();
        $agent_id = empty($admin_id) ? get_agent_id() : 0;
        if (empty($admin_id) && empty($agent_id)) {
            $admin_id = 1;
        }
        $insArr = [
            'type'      => $type,
            'content'   => $content,
            'admin_id'  => $admin_id ?? 0,
            'agent_id'  => $agent_id ?? 0,
            'member_id' => $member_id ?? 0,
        ];

        return self::create($insArr);
    }

    public static function createJSONLog($type, $title, $content)
    {
        $admin_id = get_admin_id();
        $agent_id = empty($admin_id) ? get_agent_id() : 0;
        if (empty($admin_id) && empty($agent_id)) {
            $admin_id = 1;
        }
        if (!is_array($content) && json_decode($content, true)) {
            $content = json_decode($content, true);
        }
        $arr['title']   = $title;
        $arr['content'] = $content;
        $content        = json_encode($arr, JSON_UNESCAPED_UNICODE);

        $insArr = [
            'type'      => $type,
            'content'   => $content,
            'admin_id'  => $admin_id ?? 0,
            'agent_id'  => $agent_id ?? 0,
            'member_id' => $member_id ?? 0,
        ];

        return self::create($insArr);
    }

    public static function createAdminLog($type, $content)
    {
        $admin_id = get_admin_id();
        $insArr   = [
            'type'     => $type,
            'content'  => $content,
            'admin_id' => $admin_id ?? 0,
        ];

        return self::create($insArr);
    }

    public static function createAgentLog($type, $content)
    {
        $agent_id = get_agent_id();
        $insArr   = [
            'type'     => $type,
            'content'  => $content,
            'agent_id' => $agent_id ?? 0,
        ];

        return self::create($insArr);
    }

    public static function createMemberLog($type, $content)
    {
        $member_id = get_member_id();
        $insArr    = [
            'type'      => $type,
            'content'   => $content,
            'member_id' => $member_id ?? 0,
        ];

        return self::create($insArr);

    }

    public function getOperator($id)
    {
        $info = $this->find($id);
        if (!empty($info->admin_id)) {
            $info = Admin::find($info->admin_id);

            return $info->nickname;
        }
        if (!empty($info->agent_id)) {
            $info = Agent::find($info->agent_id);

            return $info->agent_name;
        }
        if (!empty($info->member_id)) {
            $info = Member::find($info->member_id);

            return $info->real_name;
        }

        return '';
    }

}
