<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZanySoft\Zip\Zip;

class DevBackUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '备份系统的基础表信息';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct ()
    {
        parent::__construct ();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle ()
    {
        $tables = [
            'users',
            'configs',
            'menus',
            'roles',
            'permissions',
            'role_has_permissions',
            'model_has_roles',
            'model_has_permissions',
        ];
        $dir    = 'database/dev-backup';
        $data   = [];
        foreach ($tables as $table) {
            $file = $table . '.json';

            $result = DB::table ($table)->get ();
            if ($result->isEmpty ()) {
                continue;
            }
            $content        = $result->toArray ();
            $data[ $table ] = Storage::disk ('base')->put ($dir . '/' . $file, json_encode ($content, JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT));
        }
        //打包一份备份
        $bak_zip = storage_path ('app/dev-backup/' . date ('YmdHis') . '.bak.zip');
        $bakZip  = Zip::create ($bak_zip);
        $bakZip->add (base_path ($dir));
        $bakZip->close ();

    }
}
