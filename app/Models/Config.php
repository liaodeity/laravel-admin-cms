<?php
/*
|-----------------------------------------------------------------------------------------------------------
| laravel-admin-cms [ 简单高效的开发插件系统 ]
|-----------------------------------------------------------------------------------------------------------
| Licensed ( MIT )
| ----------------------------------------------------------------------------------------------------------
| Copyright (c) 2020-2021 https://gitee.com/liaodeiy/laravel-admin-cms All rights reserved.
| ----------------------------------------------------------------------------------------------------------
| Author: 廖春贵 < liaodeity@gmail.com >
|-----------------------------------------------------------------------------------------------------------
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    const STR_TYPE  = 1;
    const NUM_TYPE  = 2;
    const ARR_TYPE  = 3;
    const ITEM_TYPE = 4;
    const JSON_TYPE = 5;
    protected $fillable = ['name', 'title', 'group_id', 'type', 'content', 'param_json', 'description'];

    public static function getValue ($name, $default = null)
    {
        $name   = strtolower (trim ($name));
        $config = Config::where ('name', $name)->first ();

        if (!$config) {
            return $default;
        }
        $content = $config->contnet;
        switch ($config->type) {
            case self::NUM_TYPE:
                $content = (int)$content;
                break;
            case self::ARR_TYPE:
            case self::ITEM_TYPE:
                $content = json_decode ($content, true);
                if (!$content || !is_array ($content)) {
                    $content = [];
                }
                break;
            case self::JSON_TYPE:
                $json = json_decode ($content, true);
                if (!$json) {
                    $content = '{}';
                }
                break;
            default:
                $content = $config->content;
        }

        return $content ?? $default;
    }

    public static function setConfig ($groupId, $name, $title, $type, $content, $desc = '', $json = '')
    {
        if (is_array ($json)) {
            $json = json_encode ($json, JSON_UNESCAPED_UNICODE);
        }
        $name   = strtolower (trim ($name));
        $config = Config::updateOrCreate ([
            'name' => $name
        ], [
            'name'        => $name,
            'title'       => $title,
            'group_id'    => $groupId,
            'type'        => (int)$type,
            'content'     => $content,
            'param_json'  => $json,
            'description' => $desc
        ]);

        return $config;
    }
}
