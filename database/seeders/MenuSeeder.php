<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run ()
    {
        $json    = file_get_contents (storage_path ('app/dev-backup/20210319144612/menus.json'));
        $pidMenu = json_decode ($json, true);
        Artisan::call ('dev:backup');
        Menu::where ('id', '<>', '')->delete ();
        foreach ($pidMenu as $key => $menu) {
            Menu::create ($menu);
        }
    }
}
