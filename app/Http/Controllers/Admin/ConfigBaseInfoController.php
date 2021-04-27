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

namespace App\Http\Controllers\Admin;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Models\ConfigGroup;
use App\Repositories\ConfigRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class ConfigBaseInfoController extends Controller
{
    protected $module_name = 'config_base_info';
    /**
     * @var ConfigRepository
     */
    private $repository;

    public function __construct (ConfigRepository $repository)
    {
        View::share ('MODULE_NAME', $this->module_name);//模块名称
        $this->repository = $repository;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index ()
    {
        if (!check_admin_auth ($this->module_name . '_' . __FUNCTION__)) {
            return auth_error_return ();
        }
        $groupId = ConfigGroup::where ('name', 'base_info')->value ('id');
        $configs = Config::where ('group_id', $groupId)->get ();

        return view ('admin.' . $this->module_name . '.add', compact ('configs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update (Request $request)
    {
        if (!check_admin_auth ($this->module_name . '_edit')) {
            return auth_error_return ();
        }
        $input = $request->input ('Config');
        if (!$input) {
            throw new BusinessException('没有配置参数');
        }
        DB::beginTransaction ();
        foreach ($input as $id => $content) {
            $this->repository->saveContent (Config::find ($id), $content);
        }
        DB::commit ();

        return ajax_success_result ('保存成功');
    }
}
