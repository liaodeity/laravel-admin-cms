<?php
/*
|-----------------------------------------------------------------------------------------------------------
| gui-giggle [ 简单高效的开发插件系统 ]
|-----------------------------------------------------------------------------------------------------------
| Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
| ----------------------------------------------------------------------------------------------------------
| Copyright (c) 2014-2020 https://github.com/liaodeity/gui-giggle All rights reserved.
| ----------------------------------------------------------------------------------------------------------
| Author: 廖春贵 < liaodeity@gmail.com >
|-----------------------------------------------------------------------------------------------------------
*/

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register ()
    {
        $file  = 'plugin_map.json';
        $exits = Storage::exists ($file);
        if (!$exits || config ('app.debug')) {
            $plugins = Storage::disk ('base')->directories ('app/Plugins');
            if ($plugins) {
                $maps = [];
                foreach ($plugins as $plugin) {
                    list($app, $plugin, $module) = explode ('/', $plugin);
                    //TODO 判断禁用的插件
                    $maps[] = [
                        'name'  => $module,
                        'class' => 'App\\Plugins\\' . $module . '\\Providers\\' . $module . 'ServiceProvider'
                    ];
                }
                $content = json_encode ($maps, JSON_PRETTY_PRINT);
                Storage::put ($file, $content);
            }
        }
        $exits = Storage::exists ($file);
        if ($exits) {
            $map       = Storage::get ($file);
            $providers = json_decode ($map, true);
            if ($providers) {
                array_map (function ($provider) {
                    if (class_exists ($provider['class']))
                        $this->app->register ($provider['class']);
                }, $providers);
            }
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot ()
    {
        //
    }
}
