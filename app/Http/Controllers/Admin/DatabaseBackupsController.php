<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Log;
use App\Http\Controllers\Controller;
use App\Presenters\DatabaseBackupPresenter;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\DatabaseBackupCreateRequest;
use App\Http\Requests\DatabaseBackupUpdateRequest;
use App\Repositories\DatabaseBackupRepositoryEloquent as DatabaseBackupRepository;
use App\Validators\DatabaseBackupValidator;

/**
 * Class DatabaseBackupsController.
 * @package namespace App\Http\Controllers;
 */
class DatabaseBackupsController extends Controller
{
    /**
     * @var DatabaseBackupRepository
     */
    protected $repository;

    /**
     * @var DatabaseBackupValidator
     */
    protected $validator;
    /**
     * @var DatabaseBackupPresenter
     */
    protected $presenter;

    /**
     * DatabaseBackupsController constructor.
     * @param DatabaseBackupRepository $repository
     * @param DatabaseBackupValidator  $validator
     */
    public function __construct(DatabaseBackupRepository $repository, DatabaseBackupValidator $validator, DatabaseBackupPresenter $presenter)
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
        if (!check_admin_permission('show databaseBackups')) {
            abort(403, trans('禁止访问，无权限'));
        }

        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        if (request()->wantsJson()) {
            $name            = $request->name ?? '';
            $status          = $request->status ?? '';
            $orderBy         = $request->_order_by ?? 'start_at DESC';
            $databaseBackups = app($this->repository->model());
            if ($name) $databaseBackups = $databaseBackups->where('name', 'like', "%{$name}%");
            if ($status) $databaseBackups = $databaseBackups->where('status', $status);
            if ($orderBy) $databaseBackups = $databaseBackups->orderByRaw($orderBy);
            $databaseBackups = $databaseBackups->paginate(10);
            $html            = '';

            foreach ($databaseBackups as $item) {


                if ($item->status == 2) {
                    //检查是否备份成功
                    $this->repository->checkBakSuccess($item->id);
                }

                $button = '';
                if ($item->status == 1) $button .= get_auth_confirm_button('down databaseBackups', route('databaseBackups.down', $item->id), '确认进行下载，下载后请妥善保管，防止泄露？', '下载','down_db_fun');
                //$button .= get_auth_edit_button('edit databaseBackups', route('databaseBackups.edit', $item->id));
                if ($item->status != 2) $button .= get_auth_delete_button('delete databaseBackups', route('databaseBackups.destroy', $item->id));
                //dd($button);
                $html .= '<tr>
                                    <td><input class="check-item" type="checkbox" value="' . $item->id . '"></td>
                                    <td>' . $item->name . '</td>
                                    <td>' . $item->start_at . '</td>
                                    <td>' . $item->end_at . '</td>
                                    <td>' . filesize_formatted($item->file_size) . '</td>
                                    <td>' . $item->statusItem($item->status, true) . '</td>
                                    <td>
                                        <div class="btn-group">
                                            ' . $button . '
                                        </div>
                                    </td>
                                </tr>';
            }
            $page = html_entity_decode($databaseBackups->links());

            $total = $databaseBackups->total();

            return response()->json([
                'html'  => $html,
                'page'  => $page,
                'total' => $total,
            ]);
        }
        $databaseBackups = app($this->repository->model());

        return view('admin.databaseBackups.index', compact('databaseBackups'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (!check_admin_permission('create databaseBackups')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $method         = 'POST';
        $action_url     = route('databaseBackups.store');
        $databaseBackup = app($this->repository->model());

        return view('admin.databaseBackups.create_and_edit', compact('databaseBackup', 'action_url', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     * @param DatabaseBackupCreateRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(DatabaseBackupCreateRequest $request)
    {
        if (!check_admin_permission('create databaseBackups')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {
            /*进行数据库备份*/
            $date     = date('Ymd');
            $count    = app($this->repository->model())->whereRaw(DB::raw('date(start_at)=\'' . date('Y-m-d') . '\''))->count();
            $num      = intval($count) + 1;
            $filename = $date . '_' . str_pad($num, 4, '0', STR_PAD_LEFT) . '.zip';

            $input          = [
                'name'      => $filename,
                'path_file' => config('backup.backup.name'),
                'start_at'  => date('Y-m-d H:i:s'),
                'status'    => 2,
                'file_size' => 0,
            ];
            $databaseBackup = $this->repository->create($input);

            $ret      = Artisan::call('backup:run', [
                '--only-db'  => true,
                '--filename' => $filename,
            ]);
            $status   = $ret ? 3 : 2;
            $databaseBackup->status = $status;
            $databaseBackup->save();
            if ($databaseBackup) {
                $response = [
                    'message' => trans('备份成功'),
                    'data'    => $databaseBackup->toArray(),
                ];
            } else {
                $response = [
                    'error'   => true,
                    'message' => trans('备份失败'),
                ];
            }


            return response()->json($response);

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
        if (!check_admin_permission('show databaseBackups')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $databaseBackup = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $databaseBackup,
            ]);
        }

        return view('admin.databaseBackups.show', compact('databaseBackup'));
    }

    /**
     * 下载数据库备份文件 add by gui
     * @param $id
     * @return mixed
     */
    public function down($id)
    {
        if (!check_admin_permission('down databaseBackups')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $databaseBackup = $this->repository->find($id);
        $file           = ($databaseBackup->path_file . '/' . $databaseBackup->name);
        Log::createAdminLog(Log::LOG_TYPE,'下载数据库备份：'.$databaseBackup->name);

        return Storage::download($file);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!check_admin_permission('edit databaseBackups')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $databaseBackup = $this->repository->find($id);
        $method         = 'PUT';
        $action_url     = route('databaseBackups.update', $id);

        return view('admin.databaseBackups.create_and_edit', compact('databaseBackup', 'action_url', 'method'));
    }

    /**
     * Update the specified resource in storage.
     * @param DatabaseBackupUpdateRequest $request
     * @param string                      $id
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(DatabaseBackupUpdateRequest $request, $id)
    {
        if (!check_admin_permission('edit databaseBackups')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {

            $input = $request->input('DatabaseBackup');
            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $databaseBackup = $this->repository->update($input, $id);

            $response = [
                'message' => trans('修改成功'),
                'data'    => $databaseBackup->toArray(),
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
            $info    = $this->repository->find($_id);
            $deleted = $this->repository->delete($_id);
            //删除文件
            if ($deleted && $info) {
                Storage::delete($info->path_file . '/' . $info->name);
            }
            Log::createAdminLog(Log::DELETE_TYPE,'删除数据库备份:'.$info->name);
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
