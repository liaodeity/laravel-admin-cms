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
    protected $fillable = ['site_name', 'company_name','sort_name', 'site_url', 'contact_name', 'telephone', 'email', 'fax', 'address', 'map_card_url', 'icp', 'seo_title', 'seo_keyword', 'seo_description','watermark_text', 'created_at', 'updated_at'];

    public static function getConfig ()
    {
        $config = Config::find (1);

        return $config;
    }
}
