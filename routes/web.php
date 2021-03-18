<?php

use App\Http\Controllers\Admin\LoginController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get ('admin/login', [LoginController::class,'index'])->name ('admin.login');
Route::post ('admin/login/check', 'Admin\LoginController@check')->name ('admin.login.check');
Route::get ('admin/login/captcha', 'Admin\LoginController@captcha')->name ('admin.login.captcha');


Route::namespace ('Admin')->middleware ('admin')->prefix ('admin')->group (function () {
    Route::get ('/', 'MainController@index')->name ('admin');
    Route::get ('main/init', 'MainController@init')->name ('admin.main.init');
    Route::get ('main/console', 'MainController@console')->name ('admin.main.console');
    Route::get ('main/logout', 'MainController@logout')->name ('admin.main.logout');
    Route::get ('main/clear', 'MainController@clear')->name ('admin.main.clear');
    Route::post ('main/logs', 'MainController@logs')->name ('admin.main.logs');
    Route::post ('/main/sync_real_num', 'MainController@sync_real_num');
    Route::post ('/main/get_echart', 'MainController@get_echart');

    //上传
    Route::post ('/upload', 'UploadController@image')->name ('upload.image');
    Route::post ('/upload_excel', 'UploadController@excel')->name ('upload.excel');

    Route::resource ('config', 'ConfigController');
    Route::any ('/user/password', 'UserController@password');
    Route::resource ('user', 'UserController');
    Route::resource ('log', 'LogController');

    Route::any ('role/auth/list', 'RoleController@listAuth');
    Route::any ('role/auth/add', 'RoleController@addAuth');
    Route::any ('role/auth/{id}', 'RoleController@auth');
    Route::resource ('role', 'RoleController');


});
