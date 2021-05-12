<?php

namespace Database\Seeders;

use App\Models\Config;
use App\Models\ConfigGroup;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run ()
    {
        //配置内容
        if (!config ('app.debug')) {
            dd ('非开发环境，无法执行');
        }

        $groupId = ConfigGroup::insertGroup ('base_info', '基本信息');
        Config::setConfig ($groupId, 'site_title', '网站标题', Config::STR_TYPE, '某某公司网站');
        Config::setConfig ($groupId, 'site_short_title', '网站后台简称', Config::STR_TYPE, '建百站CMS', '长度为7个字以内');
        Config::setConfig ($groupId, 'company_name', '公司名称', Config::STR_TYPE, '中山市某某有限公司');
        Config::setConfig ($groupId, 'icp', '备案信息', Config::STR_TYPE, '');
        Config::setConfig ($groupId, 'admin_theme', '后台主题', Config::ITEM_TYPE, 'onepage', '', ['iframe'=>'多标签模式','onepage'=>'单页面模式']);
        $groupId = ConfigGroup::insertGroup ('contact_info', '联系人信息');
        Config::setConfig ($groupId, 'contact_name', '联系人名称', Config::STR_TYPE, '张三');
        Config::setConfig ($groupId, 'telephone', '联系号码', Config::STR_TYPE, '020-00001111');
        Config::setConfig ($groupId, 'email', '联系邮箱', Config::STR_TYPE, 'exk@text.com');
        Config::setConfig ($groupId, 'fax', '公司传真', Config::STR_TYPE, '020-00001111');
        Config::setConfig ($groupId, 'qq', 'QQ', Config::STR_TYPE, '00000000');
        Config::setConfig ($groupId, 'weixin', '微信', Config::STR_TYPE, '00000001');
        Config::setConfig ($groupId, 'address', '公司地址', Config::STR_TYPE, '中山市某某街道某某路某号');
    }
}
