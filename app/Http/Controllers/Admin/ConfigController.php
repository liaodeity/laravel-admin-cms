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
use App\Models\Log;
use App\Repositories\ConfigRepository;
use App\Validators\ConfigValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ConfigController extends Controller
{
    protected $module_name = 'config';
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index ()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create ()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store (Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Config $config
     * @return \Illuminate\Http\Response
     */
    public function show (Config $config)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Config $config
     * @return \Illuminate\Http\Response
     */
    public function edit (Config $config)
    {
        if (!check_admin_auth ($this->module_name)) {
            return auth_error_return();
        }
        return view ('admin.' . $this->module_name . '.add', compact ('config'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Config       $config
     * @return \Illuminate\Http\Response
     */
    public function update (Request $request, Config $config)
    {
        if (!check_admin_auth ($this->module_name.' edit')) {
            return auth_error_return();
        }
        $input = $request->input ('Config');
        $input = $this->formatRequestInput (__FUNCTION__, $input);
        try {
            $this->repository->makeValidator ()->with ($input)->passes (ConfigValidator::RULE_UPDATE);
            $ret = $this->repository->update ($input, $config->id);
            if ($ret) {
                Log::createLog (Log::EDIT_TYPE, '修改配置信息记录', $config->toArray (), $ret->id, Config::class);
                return ajax_success_result ('修改成功');
            } else {
                return ajax_success_result ('修改失败');
            }

        } catch (BusinessException $e) {
            return ajax_error_result ($e->getMessage ());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Config $config
     * @return \Illuminate\Http\Response
     */
    public function destroy (Config $config)
    {
        //
    }

    private function formatRequestInput (string $__FUNCTION__, $input)
    {
        switch ($__FUNCTION__) {
            case 'store':
            case 'update':
                $input['auto_relevant_num'] = array_get_number ($input, 'auto_relevant_num');
                break;
        }

        return $input;
    }

    public function setting ()
    {
        return view('admin.config.setting ');
    }
}
