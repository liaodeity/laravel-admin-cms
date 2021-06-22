<?php

namespace Database\Seeders;

use App\Models\User;
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
        $count = User::count ();
        if ($count > 0) {
            dd ('数据已经初始化过，无法操作');
        }
        //恢复基础表内容
        $tables = config ('gui.base_table');
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
    }
}
