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
        $tables = config ('gui.base_table');
        $dir    = 'database/dev-backup';
        $data   = [];
        foreach ($tables as $table) {
            $file = $table . '.json';

            $result = DB::table ($table)->get ();
            $content        = $result->toArray ();
            $data[ $table ] = Storage::disk ('base')->put ($dir . '/' . $file, json_encode ($content, JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT));
        }
        //打包一份备份
        if(!Storage::exists ('dev-backup')){
            Storage::makeDirectory ('dev-backup');
        }
        $bak_zip = storage_path ('app/dev-backup/' . date ('YmdHis') . '.bak.zip');
        $bakZip  = Zip::create ($bak_zip);
        $bakZip->add (base_path ($dir));
        $bakZip->close ();

    }
}
