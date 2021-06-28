<?php

use App\Http\Controllers\Admin\AttachmentController;
use App\Http\Controllers\Admin\ConfigBaseInfoController;
use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\MainController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserMemberController;
use Illuminate\Support\Facades\Route;


/*后台*/
Route::get ('admin/login', [LoginController::class, 'index'])->name ('admin.login');
Route::post ('admin/login/check', [LoginController::class, 'check'])->name ('admin.login.check');
Route::get ('admin/login/captcha', [LoginController::class, 'captcha'])->name ('admin.login.captcha');

Route::middleware ('admin')->prefix ('admin')->group (function () {
    //后台首页
    Route::get ('/', [MainController::class, 'index'])->name ('admin');
    //控制台
    Route::get ('main/init', [MainController::class, 'init'])->name ('admin.main.init');
    Route::get ('main/console', [MainController::class, 'console'])->name ('admin.main.console');
    Route::get ('main/logout', [MainController::class, 'logout'])->name ('admin.main.logout');
    Route::get ('main/clear', [MainController::class, 'clear'])->name ('admin.main.clear');
    Route::post ('main/logs', [MainController::class, 'logs'])->name ('admin.main.logs');
    Route::post ('/main/sync_real_num', [MainController::class, 'syncRealNum']);
    Route::post ('/main/get_echart', [MainController::class, 'getEchart']);
    //上传
    Route::post ('/upload', [UploadController::class, 'image'])->name ('upload.image');
    Route::post ('/upload_file', [UploadController::class, 'file'])->name ('upload.file');
    Route::post ('/upload_excel', [UploadController::class, 'excel'])->name ('upload.excel');
    //配置
    Route::get ('config_base_info', [ConfigBaseInfoController::class, 'index']);
    Route::post ('config_base_info', [ConfigBaseInfoController::class, 'update']);
    Route::resource ('config', ConfigController::class);
    //用户资料
    Route::any ('/user/setting', [UserController::class, 'setting']);
    Route::any ('/user/password', [UserController::class, 'password']);
    //账号管理
    Route::resource ('user', UserController::class);
    Route::resource ('user_admin', UserAdminController::class);
    Route::get ('user_member/export', [UserMemberController::class, 'export']);
    Route::resource ('user_member', UserMemberController::class);
    //日志
    Route::resource ('log', LogController::class);
    //角色权限
    Route::resource ('permission', PermissionController::class);
    Route::any ('role/auth/list', [RoleController::class, 'listAuth']);
    Route::any ('role/auth/add', [RoleController::class, 'addAuth']);
    Route::any ('role/auth/{id}', [RoleController::class, 'auth']);
    Route::resource ('role', RoleController::class);
    //菜单
    Route::resource ('menu', MenuController::class);
    //单页面
    Route::resource ('page', PageController::class);
    //上传附件
    Route::resource ('attachment', AttachmentController::class);

});
