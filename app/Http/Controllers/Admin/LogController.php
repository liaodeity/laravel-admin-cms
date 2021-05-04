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

use App\Http\Controllers\Controller;
use App\Libs\QueryWhere;
use App\Models\Log;
use App\Models\LogRead;
use App\Models\User;
use App\Repositories\LogRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class LogController extends Controller
{
    protected $module_name = 'log';
    /**
     * @var LogRepository
     */
    private $repository;

    public function __construct (LogRepository $repository)
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
            return auth_error_return();
        }
        if (request ()->ajax ()) {
            $limit       = $request->input ('limit', 15);
            $source_type = $request->input ('source_type');
            QueryWhere::defaultOrderBy ('logs.id', 'DESC')->setRequest ($request->all ());
            $M = $this->repository->makeModel ()->select ('logs.*');
            QueryWhere::eq ($M, 'logs.type');
            QueryWhere::like ($M, 'logs.title');
            QueryWhere::like ($M, 'logs.content');
            QueryWhere::like ($M, 'logs.source_id');
            QueryWhere::date ($M, 'created_at');
            if ($source_type) {
                QueryWhere::eq ($M, 'source_type', 'App\\Models\\'.$source_type);
            }
            QueryWhere::orderBy ($M);

            $M     = $M->paginate ($limit);
            $count = $M->total ();
            $data  = $M->items ();
            foreach ($data as $key => $item) {
                $data[ $key ]['user_id'] = User::showName ($item->user_id);
                $data[ $key ]['type']    = $item->typeItem ($item->type);
            }
            $result = [
                'count' => $count,
                'data'  => $data
            ];

            return ajax_success_result ('成功', $result);

        } else {
            $log = $this->repository->makeModel ();

            return view ('admin.' . $this->module_name . '.index', compact ('log'));
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
     * @param \App\Models\Log $log
     * @return \Illuminate\Http\Response
     */
    public function show (Log $log)
    {
        if (!check_admin_auth ($this->module_name.' show')) {
            return auth_error_return();
        }
        $content = json_decode ($log->content, true);
        if ($content) {
            $log->content = json_encode ($content, JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT);
        } else {
            $content = [];
        }
        $backup_content = $content['content'] ?? '';

        return view ('admin.' . $this->module_name . '.show', compact ('log', 'content', 'backup_content'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Log $log
     * @return \Illuminate\Http\Response
     */
    public function edit (Log $log)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Log          $log
     * @return \Illuminate\Http\Response
     */
    public function update (Request $request, Log $log)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Log $log
     * @return \Illuminate\Http\Response
     */
    public function destroy (Log $log)
    {
        //
    }

    /**
     * 标记已读 add by gui
     * @param Log $log
     * @return \Illuminate\Http\JsonResponse
     */
    public function read (Log $log)
    {
        $user_id = get_login_user_id ();
        $insArr  = [
            'log_id'  => $log->id,
            'user_id' => $user_id,
            'is_read' => 1,
            'read_at' => now (),
        ];
        $logRead = LogRead::where ('log_id', $log->id)->where ('user_id', $user_id)->first ();
        if (isset($logRead->id)) {
            return ajax_success_result ('已读成功');
        }
        $ret = LogRead::create ($insArr);
        if ($ret) {
            return ajax_success_result ('已读成功');
        } else {
            return ajax_error_result ('已读失败');
        }
    }
}
