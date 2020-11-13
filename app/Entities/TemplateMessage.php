<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class TemplateMessage.
 *
 * @package namespace App\Entities;
 */
class TemplateMessage extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mark_name', 'template_id', 'url', 'miniprogram_appid', 'miniprogram_pagepath', 'data_json'
    ];

    public function getTemplateIDToMarkName ($mark_name)
    {
        $info = $this->where ('mark_name', $mark_name)->first ();

        return $info->id ?? 0;
    }

    /**
     * 获取消息推送模板内容 add by gui
     * @param $mark_name
     * @return array
     * @throws \ErrorException
     */
    public function getParamsToMarkName ($mark_name)
    {
        if (empty($mark_name)) {
            throw new \ErrorException('模板消息标识为空');
        }
        $info = $this->where ('mark_name', $mark_name)->first ();
        if (!$info) {
            $this->create (['mark_name' => $mark_name,'data_json'=>'']);
            throw new \ErrorException('不存在模板消息标识');
        }

        $data = @json_decode ($info->data_json, true);
        if (empty($data)) {
            $data = [];
        }

        $mark_name_header        = $mark_name . '_header';
        $mark_name_footer        = $mark_name . '_footer';
        $header_value            = get_config_value ($mark_name_header, '');
        $footer_value            = get_config_value ($mark_name_footer, '');
        $data['first']['value'] = $header_value ?? '';
        $data['remark']['value'] = $footer_value ?? '';
        $info->data_json         = json_encode ($data, JSON_UNESCAPED_UNICODE);
        $info->save ();
        $url = $info->url ? url($info->url) : '';
        $params = [
            'touser'      => '',
            'template_id' => trim($info->template_id),
            'url'         => trim($url),
            'miniprogram' => [
                'appid'    => trim($info->miniprogram_appid),
                'pagepath' => trim($info->miniprogram_pagepath)
            ],
            'data'        => $data
        ];

        return $params;
    }
}
