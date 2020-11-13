<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libs\QueryWhere;
use App\Presenters\LogPresenter;
use function foo\func;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\LogCreateRequest;
use App\Http\Requests\LogUpdateRequest;
use App\Repositories\LogRepositoryEloquent as LogRepository;
use App\Validators\LogValidator;

/**
 * Class LogsController.
 * @package namespace App\Http\Controllers;
 */
class LogsController extends Controller
{
    /**
     * @var LogRepository
     */
    protected $repository;

    /**
     * @var LogValidator
     */
    protected $validator;
    /**
     * @var LogPresenter
     */
    protected $presenter;

    /**
     * LogsController constructor.
     * @param LogRepository $repository
     * @param LogValidator $validator
     */
    public function __construct(LogRepository $repository, LogValidator $validator, LogPresenter $presenter)
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
        if (!check_admin_permission('show logs')) {
            abort(403, trans('禁止访问，无权限'));
        }

        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        if (request()->wantsJson()) {
            $name    = $request->name ?? '';
            $orderBy = $request->_order_by ?? 'logs.id DESC';
            QueryWhere::setRequest($request);
            $M = app($this->repository->model())
                ->select('logs.*')
                ->leftJoin('admins', 'logs.admin_id', '=', 'admins.id')
                ->leftJoin('agents', 'logs.agent_id', '=', 'agents.id')
                ->leftJoin('members', 'logs.member_id', '=', 'members.id');
            QueryWhere::eq($M, 'logs.type');
            QueryWhere::like($M, 'content');
            QueryWhere::date($M, 'logs.created_at');

            if ($name) {
                $M = $M->where(function ($query) use ($name) {
                    return $query->where('admins.nickname', 'like', "%$name%")
                        ->orWhere('agents.agent_name', 'like', "%$name%")
                        ->orWhere('members.real_name', 'like', "%$name%");
                });
            }


            QueryWhere::orderBy($M, $orderBy);
            $logs = $M->paginate();
            $html = '';

            foreach ($logs as $item) {
                $button  = '';
                $content = $item->content;
                if(json_decode($content, true)){
                    //JSON
                    $arr = json_decode($content, true);
                    $content = ($arr['title'] ?? '').'<button onclick="showDetail(\''.route('logs.show',$item->id).'\')" class="btn-link btn ">JSON内容</button>';
                }
                $html    .= '<tr>
                                    <td>' . $item->type . '</td>
                                    <td>' . $content . '</td>
                                    <td>' . $item->getOperator($item->id) . '</td>
                                    <td>' . $item->created_at . '</td>
                                </tr>';
            }
            $page = html_entity_decode($logs->links());

            $total = $logs->total();

            return response()->json([
                'html'  => $html,
                'page'  => $page,
                'total' => $total,
            ]);
        }
        $logs = app($this->repository->model());

        return view('admin.logs.index', compact('logs'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (!check_admin_permission('create logs')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $method     = 'POST';
        $action_url = route('logs.store');
        $log        = app($this->repository->model());

        return view('admin.logs.create_and_edit', compact('log', 'action_url', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     * @param LogCreateRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(LogCreateRequest $request)
    {
        if (!check_admin_permission('create logs')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {
            $input = $request->input('Log');
            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_CREATE);

            $log = $this->repository->create($input);

            $response = [
                'message' => trans('添加成功'),
                'data'    => $log->toArray(),
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

            return redirect()->back()->withErrors($e->getMessageBag()->first())->withInput();
        }
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!check_admin_permission('show logs')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $log = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $log,
            ]);
        }
        $json_detail = '';
        if(json_decode($log->content) !== false){
            $arr = json_decode($log->content, true);
            $json_detail = json_encode($arr, JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT);
        }

        return view('admin.logs.show', compact('log','json_detail'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!check_admin_permission('edit logs')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $log        = $this->repository->find($id);
        $method     = 'PUT';
        $action_url = route('logs.update', $id);

        return view('admin.logs.create_and_edit', compact('log', 'action_url', 'method'));
    }

    /**
     * Update the specified resource in storage.
     * @param LogUpdateRequest $request
     * @param string $id
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(LogUpdateRequest $request, $id)
    {
        if (!check_admin_permission('edit logs')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {

            $input = $request->input('Log');
            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $log = $this->repository->update($input, $id);

            $response = [
                'message' => trans('修改成功'),
                'data'    => $log->toArray(),
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

            return redirect()->back()->withErrors($e->getMessageBag()->first())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $id    = $request->id ?? $id;
        $idArr = explode(',', $id);
        foreach ($idArr as $_id) {
            $deleted = $this->repository->delete($_id);
        }


        if (request()->wantsJson()) {

            return response()->json([
                'message' => trans('删除成功'),
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', trans('删除成功'));
    }
}
