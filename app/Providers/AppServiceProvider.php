<?php

namespace App\Providers;

use App\Services\FileSystem\QiniuAdapter;
use App\Services\FileSystem\UploadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 注册上传云存储驱动
        UploadService::boot();
    }
}
