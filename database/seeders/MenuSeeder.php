<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run ()
    {
        $table = 'menus';
        $file  = 'database/dev-backup/' . $table . '.json';
        if (!Storage::disk ('base')->exists ($file)) {
            return;
        }
        //Artisan::call ('dev:backup');
        $json = Storage::disk ('base')->get ($file);
        $data = json_decode ($json, true);
        foreach ($data as $item) {
            if (isset($item['uuid'])) {
                DB::table ($table)->where('uuid', $item['uuid'])->update ($item);
            } else {
                $item['uuid'] = get_uuid ();
                DB::table ($table)->updateOrInsert ($item);
            }
        }
    }
}
