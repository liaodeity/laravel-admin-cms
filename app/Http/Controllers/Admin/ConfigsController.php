<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Config;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WeiXinController;
use App\Presenters\ConfigPresenter;
use App\Services\WeiXinService;
use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ConfigCreateRequest;
use App\Http\Requests\ConfigUpdateRequest;
use App\Repositories\ConfigRepositoryEloquent as ConfigRepository;
use App\Validators\ConfigValidator;

/**
 * Class ConfigsController.
 * @package namespace App\Http\Controllers;
 */
class ConfigsController extends Controller
{
    /**
     * @var ConfigRepository
     */
    protected $repository;

    /**
     * @var ConfigValidator
     */
    protected $validator;
    /**
     * @var ConfigPresenter
     */
    protected $presenter;

    /**
     * ConfigsController constructor.
     * @param ConfigRepository $repository
     * @param ConfigValidator  $validator
     */
    public function __construct(ConfigRepository $repository, ConfigValidator $validator, ConfigPresenter $presenter)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->presenter  = $presenter;
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));


        if (request()->wantsJson()) {
            $title   = $request->title ?? '';
            $context = $request->context ?? '';
            $orderBy = $request->_order_by ?? '';
            $configs = app($this->repository->model());
            if ($title) $configs = $configs->where('title', 'like', "%{$title}%");
            if ($context) $configs = $configs->where('context', 'like', "%{$context}%");
            if ($orderBy) $configs = $configs->orderByRaw($orderBy);
            $configs = $configs->get();
            $html    = '';

            foreach ($configs as $item) {
                $button = '';
                $button = "<button type=\"button\" onclick=\"dialog_fun('查看配置信息','" . route('configs.show', $item->id) . "')\" class=\"btn btn-sm btn-info\">查看</button>";
                $button .= "<button type=\"button\" onclick=\"dialog_fun('编辑配置信息','" . route('configs.edit', $item->id) . "')\" class=\"btn btn-sm btn-info\">编辑</button>";
                if($item->name == 'wx_menu'){
                    $button .= "<button type=\"button\" onclick=\"confirm_fun('菜单更新','" . route('configs.send') . "','发布前可“查看”效果后再发布，确认发布菜单更新至微信？')\" class=\"btn btn-sm btn-info\">发布</button>";
                }

                $context = $item->getContextValue($item);

                $html   .= '<tr>
                                    <td><input class="check-item" type="checkbox" value="' . $item->id . '"></td>
                                    <td>' . $item->title . '</td>
                                    <td>' . $context . '</td>
                                    <td>' . $item->updated_at . '</td>
                                    <td>
                                        <div class="btn-group">

                                            ' . $button . '
                                        </div>
                                    </td>
                                </tr>';
            }
            $page = '';

            $total = count($configs);

            return response()->json([
                'html'  => $html,
                'page'  => $page,
                'total' => $total,
            ]);
        }

        return view('admin.configs.index');
    }

    public function create()
    {
        $method     = 'PUT';
        $action_url = route('configs.create');

        return view('admin.configs.create_and_edit', compact('action_url', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     * @param ConfigCreateRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(ConfigCreateRequest $request)
    {
        try {

            $this->validator->with($request->input('Configs'))->passesOrFail(ValidatorInterface::RULE_CREATE);

            $config = $this->repository->create($request->input('Configs'));

            $response = [
                'message' => '添加成功',
                'data'    => $config->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag(),
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $config = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $config,
            ]);
        }
        $context = Config::getContextFormat($config);
        if($config->name == 'wx_menu'){
            $context = Config::wxMenuToData(json_decode($config->context, true));
        }else{
            $context = $config->getContextValue($config);
        }
//        dd($context);
        return view('admin.configs.show', compact('config','context'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $config     = $this->repository->find($id);
        $method     = 'PUT';
        $action_url = route('configs.update', $id);

        $context = Config::getContextFormat($config);
        return view('admin.configs.create_and_edit', compact('config', 'method', 'action_url','context'));
    }

    /**
     * Update the specified resource in storage.
     * @param ConfigUpdateRequest $request
     * @param string              $id
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(ConfigUpdateRequest $request, $id)
    {
        try {
            $config       = $this->repository->find($id);
            if($config->name == 'wx_menu'){
                $arr = $request->input('menu');
                $input = $request->input('Configs');
                $input['context'] = json_encode($arr);
                $request->offsetSet('Configs', $input);
            }
            $this->validator->with($request->input('Configs'))->passesOrFail(ValidatorInterface::RULE_UPDATE);
            $data         = $request->input('Configs');

            if ($config->type == 'int' && !is_numeric($data['context'])) {

                return response()->json([
                    'error'   => true,
                    'message' => '配置值必须为数字',
                ]);
            }
            $config = $this->repository->update($data, $id);

            $this->repository->saveToEnv ($config->name, $data['context']);
            $response = [
                'message' => '修改成功',
                'data'    => $config->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()->first(),
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        } catch (\ErrorException $e) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessage ()
                ]);
            }

            return redirect ()->back ()->withErrors ($e->getMessage ())->withInput ();
        }
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (request()->wantsJson()) {

            return response()->json([
                'message' => '删除成功',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', '删除成功');
    }

    /**
     * 更新微信菜单 add by gui
     * @return \Illuminate\Http\JsonResponse
     */
    public function send()
    {
        $WeiXinService = new WeiXinService();
        try {
            $ret = $WeiXinService->menuUpdate();
            $response = [
                'message' => '发布菜单更新成功',
            ];
            return response()->json($response);
        } catch (\ErrorException $e) {
            return response()->json([
                'error'   => true,
                'message' => $e->getMessage ()
            ]);
        }
    }
}
