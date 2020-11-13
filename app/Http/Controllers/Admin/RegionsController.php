<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Presenters\RegionPresenter;
use function foo\func;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\RegionCreateRequest;
use App\Http\Requests\RegionUpdateRequest;
use App\Repositories\RegionRepositoryEloquent as RegionRepository;
use App\Validators\RegionValidator;

/**
 * Class RegionsController.
 * @package namespace App\Http\Controllers;
 */
class RegionsController extends Controller
{
    /**
     * @var RegionRepository
     */
    protected $repository;

    /**
     * @var RegionValidator
     */
    protected $validator;
    /**
     * @var RegionPresenter
     */
    protected $presenter;

    /**
     * RegionsController constructor.
     * @param RegionRepository $repository
     * @param RegionValidator  $validator
     */
    public function __construct(RegionRepository $repository, RegionValidator $validator, RegionPresenter $presenter)
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
        if (!check_admin_permission('show regions')) {
            abort(403, trans('禁止访问，无权限'));
        }

        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $this->repository->syncAreaRegion();//同步区域内容
        if (request()->wantsJson()) {
            $level   = $request->level ?? '';
            $status  = $request->status ?? '';
            $name    = $request->name ?? '';
            $pidName = $request->pidName ?? '';
            $orderBy = $request->_order_by ?? 'id asc';
            $regions = app($this->repository->model());
            if ($name) $regions = $regions->where(function ($query) use ($name){
                return $query->where('name', 'like', "%{$name}%")
                    ->orWhere('area_region_name','like',"%{$name}");
            });
            if ($level) $regions = $regions->where('level', $level);
            if ($status) $regions = $regions->where('status', $status);
            if($pidName){
                $regions = $regions->whereRaw(DB::raw(" pid IN (select id from tb_regions where ( name LIKE '%$pidName%' OR area_region_name LIKE '%$pidName%' ) ) "));
            }
            //dd($regions->toSql());

            if ($orderBy) $regions = $regions->orderByRaw($orderBy);
            $regions = $regions->paginate();
            $html    = '';

            foreach ($regions as $item) {
                $button = '';
                $button .= get_auth_edit_button('edit regions', route('regions.edit', $item->id));
                $button .= get_auth_edit_button('edit regions', route('regions.create') . '?pid=' . $item->id, '添加【' . $item->name . '】下级区域信息', '添加下级');
                if($this->repository->allowDelete($item->id)){
                    $button .= get_auth_delete_button('delete regions', route('regions.destroy', $item->id));
                }

                //dd($button);
                $pid_name = $item->getLevelName($item->id);
                $html     .= '<tr>
                                    <td><input class="check-item" type="checkbox" value="' . $item->id . '"></td>
                                    <td>' . $item->levelItem($item->level) . '</td>
                                    <td>' . $item->name . '</td>
                                    <td><button onclick="searchPidName(\'' . $item->pid . '\',\'' . $pid_name . '\')" class="btn-link btn ">' . $pid_name . '</button></td>
                                    <td>' . $item->updated_at . '</td>
                                    <td>' . $item->statusItem($item->status) . '</td>
                                    <td>
                                        <div class="btn-group">
                                            ' . $button . '
                                        </div>
                                    </td>
                                </tr>';
            }
            $page = html_entity_decode($regions->links());

            $total = $regions->total();

            return response()->json([
                'html'  => $html,
                'page'  => $page,
                'total' => $total,
            ]);
        }
        $this->repository->syncAreaRegion ();

        $region = app($this->repository->model());

        return view('admin.regions.index', compact('region'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        $pid = $request->pid ?? 0;
        if (!check_admin_permission('create regions')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $method     = 'POST';
        $action_url = route('regions.store');
        $region     = app($this->repository->model());
        if ($pid) {
            $regionPid        = $this->repository->find($pid);
            $region->pid      = $regionPid->id;
            $region->pid_name = $regionPid->name;
            $region->level    = $regionPid->level;

        }else{
            $region->level = 100;
            $region->_province_level = 1;
        }

        return view('admin.regions.create_and_edit', compact('region', 'action_url', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     * @param RegionCreateRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(RegionCreateRequest $request)
    {
        if (!check_admin_permission('create regions')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {
            $input = $request->input('Region');
            input_default($input);
            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_CREATE);

            $region = $this->repository->create($input);

            $response = [
                'message' => trans('添加成功'),
                'data'    => $region->toArray(),
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
        if (!check_admin_permission('show regions')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $region = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $region,
            ]);
        }

        return view('admin.regions.show', compact('region'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!check_admin_permission('edit regions')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $region     = $this->repository->find($id);
        $method     = 'PUT';
        $action_url = route('regions.update', $id);

        return view('admin.regions.create_and_edit', compact('region', 'action_url', 'method'));
    }

    /**
     * Update the specified resource in storage.
     * @param RegionUpdateRequest $request
     * @param string              $id
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(RegionUpdateRequest $request, $id)
    {
        if (!check_admin_permission('edit regions')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {

            $input = $request->input('Region');
            input_default($input);
            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $region = $this->repository->update($input, $id);

            $response = [
                'message' => trans('修改成功'),
                'data'    => $region->toArray(),
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
     * @param int     $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $id    = $request->id ?? $id;
        $idArr = explode(',', $id);
        foreach ($idArr as $_id) {
            if($this->repository->allowDelete($_id)) {
                $deleted = $this->repository->delete($_id);
            }
        }
        if(!isset($deleted)){
            return response()->json([
                'message' => trans('不存在可删除记录'),
                'error' => true,
            ]);
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
