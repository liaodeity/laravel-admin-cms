<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Config.
 * @package namespace App\Entities;
 */
class Config extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable
        = [
            'type',
            'name',
            'title',
            'context',
            'param_json',
            'admin_id',
            'created_at',
            'updated_at',
            'desc',
        ];

    /**
     * 获取配置信息 add by gui
     * @param $name
     * @param $default
     * @return mixed
     */
    public function getConfig ($name, $default)
    {
        $config = $this->where ('name', $name)->first ();
        if (!$config) {
            $insArr = [
                'type'       => 'string',
                'name'       => $name,
                'title'      => $name,
                'context'    => $default,
                'param_json' => '',
                'desc'       => ''
            ];
            $this->create ($insArr);
        }

        return $config ? $config->context : $default;
    }

    /**
     * 获取配置内容信息 add by gui
     * @param Config $config
     * @return mixed|string
     */
    public function getContextValue (Config $config)
    {
        $context  =$config->context;
        if($config->name == 'wx_menu'){
            $context = '<i style="color: #d2d6de;">--内容过多无法显示，请查看或编辑--</i>';
        }elseif($config->type == 'array'){
            foreach (json_decode ($config->param_json) as $val){
                if(object_get ($val,'key') == $context){
                    $context = object_get ($val, 'value');
                }
            }
        }
        return $context;
    }

    /**
     * 对配置内容进行格式化 add by gui
     * @param Config $config
     * @return array|mixed
     */
    public static function getContextFormat (Config $config)
    {
        if ($config->name == 'wx_menu') {
            $context = json_decode ($config->context, true);
            $context = empty($context) ? [] : $context;
            foreach ($context as $key => $item) {
                if (isset($item['sub_button']) && !empty($item['sub_button'])) {
                    $sub = [];
                    foreach ($item['sub_button'] as $item2) {
                        $name = $item2['name'] ?? '';
                        $url  = $item2['url'] ?? '';
                        if (empty($name) || empty($url)) {
                            continue;
                        }
                        $sub[] = [
                            "type" => "view",
                            "name" => $name,
                            "url"  => $url
                        ];
                    }
                }
            }
        }elseif($config->type == 'array'){
            $context = json_decode ($config->param_json);
        }

        return $context ?? $config->context;
    }

    /**
     * 将微信菜单转换成数字Data add by gui
     * @param $menu
     * @return array
     */
    public static function wxMenuToData ($menu)
    {
        $rows = [];
        foreach ($menu as $key => $item) {
            $row = [
                'name' => $item['name'] ?? ''
            ];
            if (isset($item['sub_button']) && !empty($item['sub_button'])) {
                $sub = [];
                foreach ($item['sub_button'] as $item2) {
                    $name = $item2['name'] ?? '';
                    $url  = $item2['url'] ?? '';
                    if (empty($name) || empty($url)) {
                        continue;
                    }
                    $jsonArr = @json_decode ($url, true);
                    if (is_array ($jsonArr)) {
                        $appid    = array_get ($jsonArr, 'appid');
                        $pagepath = array_get ($jsonArr, 'pagepath');
                        $url      = array_get ($jsonArr, 'url');
                        if (empty($appid) || empty($pagepath) || empty($url)) {
                            //不是小程序
                            continue;
                        }
                        $sub[] = [
                            "type"     => "miniprogram",
                            "name"     => $name,
                            "url"      => $url,
                            "appid"    => $appid,
                            "pagepath" => $pagepath
                        ];
                    } else {
                        $sub[] = [
                            "type" => "view",
                            "name" => $name,
                            "url"  => $url
                        ];
                    }

                }
                if (!empty($sub)) {
                    $row['sub_button'] = $sub;
                }
            }
            if (empty($row['sub_button'])) {
                $jsonArr = @json_decode ($item['url'], true);
                if (is_array ($jsonArr)) {
                    $appid    = array_get ($jsonArr, 'appid');
                    $pagepath = array_get ($jsonArr, 'pagepath');
                    $url      = array_get ($jsonArr, 'url');
                    if (empty($appid) || empty($pagepath) || empty($url)) {
                        //不是小程序
                        continue;
                    }
                    $row = [
                        "type"     => "miniprogram",
                        "name"     => $item['name'],
                        "url"      => $url,
                        "appid"    => $appid,
                        "pagepath" => $pagepath

                    ];
                } else {
                    $row['type'] = "view";
                    $row['url']  = $item['url'] ?? '';
                }

            }

            if (!empty($row['name'])) {
                $rows[] = $row;
            }

        }

        //        dd($rows);
        return $rows;
    }
}
