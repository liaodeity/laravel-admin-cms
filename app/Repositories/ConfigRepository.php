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

namespace App\Repositories;


use App\Models\Config;

class ConfigRepository extends BaseRepository implements InterfaceRepository
{

    public function model ()
    {
        return Config::class;
    }

    public function allowDelete ($id)
    {
        return true;
    }

    /**
     * 保存配置内容 add by gui
     * @param Config $config
     * @param        $content
     * @return bool
     */
    public function saveContent (Config $config, $content)
    {
        $config->content = $content;

        return $config->save ();
    }
}
