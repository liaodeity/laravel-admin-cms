<?php

use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\MainController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;


/*后台*/
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
    Route::post ('/main/sync_real_num', [MainController::class, 'syncRealNum']);
    Route::post ('/main/get_echart', [MainController::class, 'getEchart']);

    //上传
    Route::post ('/upload', [UploadController::class, 'image'])->name ('upload.image');
    Route::post ('/upload_excel', [UploadController::class, 'excel'])->name ('upload.excel');

    Route::get ('config_base_info', [\App\Http\Controllers\Admin\ConfigBaseInfoController::class, 'index']);
    Route::post ('config_base_info', [\App\Http\Controllers\Admin\ConfigBaseInfoController::class, 'update']);
    Route::resource ('config', ConfigController::class);
    Route::any ('/user/setting', [UserController::class, 'setting']);
    Route::any ('/user/password', [UserController::class, 'password']);
    Route::resource ('user', UserController::class);
    Route::resource ('user_admin', \App\Http\Controllers\Admin\UserAdminController::class);
    Route::resource ('user_member', \App\Http\Controllers\Admin\UserMemberController::class);
    Route::resource ('log', LogController::class);

    Route::resource ('permission', \App\Http\Controllers\Admin\PermissionController::class);
    Route::any ('role/auth/list', [RoleController::class, 'listAuth']);
    Route::any ('role/auth/add', [RoleController::class, 'addAuth']);
    Route::any ('role/auth/{id}', [RoleController::class, 'auth']);
    Route::resource ('role', RoleController::class);

    Route::resource ('menu', \App\Http\Controllers\Admin\MenuController::class);
    Route::resource ('page', \App\Http\Controllers\Admin\PageController::class);
    Route::resource ('attachment', \App\Http\Controllers\Admin\AttachmentController::class);

});
