<?php

use App\Entities\Admin;
use Illuminate\Database\Seeder;

class InitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run ()
    {
        //超级管理员角色
        \App\Entities\Role::updateOrCreate ([
            'name' => 'super'
        ], [
            'guard_name' => 'admin'
        ]);
        $roleId = \App\Entities\Role::where ('name', 'super')->value ('id');
        if ($roleId) {
            \App\Entities\RoleInfo::updateOrCreate ([
                'role_id' => $roleId
            ], [
                'name'       => '超级管理员',
                'desc'       => '拥有所有操作权限',
                'role_value' => '',
                'status'     => 1
            ]);
        }
        //管理员账号
        Admin::updateOrCreate ([
            'id' => 1
        ], [
            'username' => 'admin',
            'nickname' => 'admin',
            'phone'    => '',
            'status'   => 1,
            'admin_id' => 1,
            'password' => \Illuminate\Support\Facades\Hash::make ('admin12345')
        ]);
        $adminId = Admin::where ('username', 'admin')->value ('id');
        $admin   = Admin::find ($adminId);
        $admin->assignRole ($roleId);
    }
}
