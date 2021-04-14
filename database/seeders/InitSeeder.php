<?php

namespace Database\Seeders;

use App\Models\Config;
use App\Models\ConfigGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run ()
    {
        //恢复基础表内容
        $tables = [
            'users',
            'user_admins',
            'config_groups',
            'configs',
            'menus',
            'roles',
            'permissions',
            'role_has_permissions',
            'model_has_roles',
            'model_has_permissions',
        ];
        foreach ($tables as $table) {
            $file = 'database/dev-backup/' . $table . '.json';
            if (!Storage::disk ('base')->exists ($file)) {
                continue;
            }

            $json = Storage::disk ('base')->get ($file);
            $data = json_decode ($json, true);
            foreach ($data as $item) {
                DB::table ($table)->updateOrInsert ($item);
            }
        }

        //配置内容
        $groupId = ConfigGroup::insertGroup ('base_info', '基本信息');
        Config::setConfig ($groupId, 'site_title', '网站标题', Config::STR_TYPE, '某某公司网站');
        Config::setConfig ($groupId, 'company_name', '公司名称', Config::STR_TYPE, '中山市某某有限公司');
        Config::setConfig ($groupId, 'icp', '备案信息', Config::STR_TYPE, '');
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
