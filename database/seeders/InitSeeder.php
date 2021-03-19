<?php

namespace Database\Seeders;

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

        $tables = [
            'users',
            //'configs',
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
                //switch ($table) {
                //    case 'menus':
                //        Menu::create ($item);
                //        break;
                //    case 'roles':
                //        Role::create($item);
                //        break;
                //    case 'permissions':
                //        Permission::create($item);
                //        break;
                //    case 'role_has_permissions':
                //
                //        break;
                //    case 'model_has_roles':
                //        break;
                //    case 'model_has_permissions':
                //        break;
                //
                //}
            }

        }
    }
}
