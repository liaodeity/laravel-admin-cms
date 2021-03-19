<?php

use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\MainController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get ('/', function () {
    return view ('welcome');
});

Route::get ('admin/login', [LoginController::class, 'index'])->name ('admin.login');
Route::post ('admin/login/check', [LoginController::class, 'check'])->name ('admin.login.check');
Route::get ('admin/login/captcha', [LoginController::class, 'captcha'])->name ('admin.login.captcha');


Route::middleware ('admin')->prefix ('admin')->group (function () {
    Route::get ('/', [MainController::class, 'index'])->name ('admin');
    Route::get ('main/init', [MainController::class, 'init'])->name ('admin.main.init');
    Route::get ('main/console', [MainController::class, 'console'])->name ('admin.main.console');
    Route::get ('main/logout', [MainController::class, 'logout'])->name ('admin.main.logout');
    Route::get ('main/clear', [MainController::class, 'clear'])->name ('admin.main.clear');
    Route::post ('main/logs', [MainController::class, 'logs'])->name ('admin.main.logs');
    Route::post ('/main/sync_real_num', [MainController::class, 'sync_real_num']);
    Route::post ('/main/get_echart', [MainController::class, 'get_echart']);

    //上传
    Route::post ('/upload', [UploadController::class, 'image'])->name ('upload.image');
    Route::post ('/upload_excel', [UploadController::class, 'excel'])->name ('upload.excel');

    Route::any ('config/setting', [ConfigController::class, 'setting']);
    Route::resource ('config', ConfigController::class);
    Route::any ('/user/setting', [UserController::class, 'setting']);
    Route::any ('/user/password', [UserController::class, 'password']);
    Route::resource ('user', UserController::class);
    Route::resource ('log', LogController::class);

    Route::any ('role/auth/list', [RoleController::class, 'listAuth']);
    Route::any ('role/auth/add', [RoleController::class, 'addAuth']);
    Route::any ('role/auth/{id}', [RoleController::class, 'auth']);
    Route::resource ('role', RoleController::class);


});
