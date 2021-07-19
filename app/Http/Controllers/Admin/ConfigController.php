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

use App\Enums\ConfigTypeEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Libs\QueryWhere;
use App\Models\Config;
use App\Models\ConfigGroup;
use App\Models\Log;
use App\Repositories\ConfigRepository;
use App\Validators\ConfigValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
    public function index (Request $request)
    {
        if (!check_admin_auth ($this->module_name . '_' . __FUNCTION__)) {
            return auth_error_return ();
        }
        if (request ()->wantsJson ()) {
            $limit = $request->input ('limit', 15);
            QueryWhere::setRequest ($request->all ());
            $M = $this->repository->makeModel ()->select ('configs.*');
            QueryWhere::eq ($M, 'group_id');
            QueryWhere::eq ($M, 'type');
            QueryWhere::like ($M, 'title');
            QueryWhere::orderBy ($M, 'configs.group_id');
            $M->orderBy('configs.type');
            $list  = $M->paginate ($limit);
            $count = $list->total ();
            $data  = $list->items ();
            foreach ($data as $key => $item) {
                $data[ $key ]['group_id'] = $item->group->title ?? '';
                $data[ $key ]['type']     = ConfigTypeEnum::toLabel ($item->type);
            }
            $result = [
                'code'  => 0,
                'count' => $count,
                'data'  => $data,
            ];

            return response ()->json ($result);

        } else {
            $config = $this->repository->makeModel ();
            $groups = ConfigGroup::all ();

            return view ('admin.' . $this->module_name . '.index', compact ('config','groups'));
        }
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
        if (!check_admin_auth ($this->module_name.'_show')) {
            return auth_error_return ();
        }

        return view ('admin.' . $this->module_name . '.show', compact ('config'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Config $config
     * @return \Illuminate\Http\Response
     */
    public function edit (Config $config)
    {
        if (!check_admin_auth ($this->module_name.'_edit')) {
            return auth_error_return ();
        }
        $_method = 'PUT';

        return view ('admin.' . $this->module_name . '.add', compact ('config', '_method'));
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
        if (!check_admin_auth ($this->module_name . '_edit')) {
            return auth_error_return ();
        }
        $input = $request->input ('Config');
        $input = $this->formatRequestInput (__FUNCTION__, $input);
        try {
            $config = $this->repository->update ($input, $config->id);
            if ($config) {
                $this->repository->saveContent ($config, $config->content);
                Log::createLog (Log::EDIT_TYPE, '修改配置信息记录', $config->toArray (), $config->id, Config::class);

                return ajax_success_result ('修改成功');
            } else {
                return ajax_success_result ('修改失败');
            }

        } catch (BusinessException $e) {
            return ajax_error_result ($e->getMessage ());
        }
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

    public function setting (Request $request)
    {
        if (!check_admin_auth ($this->module_name . '_setting')) {
            return auth_error_return ();
        }
        if ($request->isMethod ('post')) {
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
        } else {
            $groupId = ConfigGroup::where ('name', 'base_info')->value ('id');
            $configs = Config::where ('group_id', $groupId)->get ();

            return view ('admin.config.setting', compact ('configs'));
        }

    }
}
