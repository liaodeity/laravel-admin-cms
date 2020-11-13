<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Log;
use App\Http\Controllers\Controller;
use App\Presenters\ArticlePresenter;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ArticleCreateRequest;
use App\Http\Requests\ArticleUpdateRequest;
use App\Repositories\ArticleRepositoryEloquent as ArticleRepository;
use App\Validators\ArticleValidator;

/**
 * Class ArticlesController.
 * @package namespace App\Http\Controllers;
 */
class ArticlesController extends Controller
{
    /**
     * @var ArticleRepository
     */
    protected $repository;

    /**
     * @var ArticleValidator
     */
    protected $validator;
    /**
     * @var ArticlePresenter
     */
    protected $presenter;

    /**
     * ArticlesController constructor.
     * @param ArticleRepository $repository
     * @param ArticleValidator  $validator
     */
    public function __construct(ArticleRepository $repository, ArticleValidator $validator, ArticlePresenter $presenter)
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
        if (!check_admin_permission('show articles')) {
            abort(403, trans('禁止访问，无权限'));
        }

        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        if (request()->wantsJson()) {
            $title       = $request->title ?? '';
            $push_source = $request->push_source ?? '';
            $status      = $request->status ?? '';
            $orderBy     = $request->_order_by ?? 'id DESC';
            $articles    = app($this->repository->model());
            if ($title) $articles = $articles->where('title', 'like', "%{$title}%");
            if ($push_source) $articles = $articles->where('push_source', 'like', "%{$push_source}%");
            if ($status) $articles = $articles->where('status', $status);
            if ($orderBy) $articles = $articles->orderByRaw($orderBy);
            $articles = $articles->paginate();
            $html     = '';

            foreach ($articles as $item) {
                $button = '';
                $button .= get_auth_show_button('show articles', route('articles.show', $item->id));
                $button .= get_auth_edit_button('edit articles', route('articles.edit', $item->id));
                if($this->repository->allowDelete($item->id)){
                    $button .= get_auth_delete_button('delete articles', route('articles.destroy', $item->id));
                }

                //dd($button);
                $html .= '<tr>
                                    <td><input class="check-item" type="checkbox" value="' . $item->id . '"></td>
                                    <td>' . $item->title . '</td>
                                    <td>' . $item->view_number . '</td>
                                    <td>' . $item->push_source . '</td>
                                    <td>' . $item->created_at . '</td>
                                    <td>' . $item->statusItem($item->status, true) . '</td>
                                    <td>
                                        <div class="btn-group">
                                            ' . $button . '
                                        </div>
                                    </td>
                                </tr>';
            }
            $page = html_entity_decode($articles->links());

            $total = $articles->total();

            return response()->json([
                'html'  => $html,
                'page'  => $page,
                'total' => $total,
            ]);
        }
        $articles = app($this->repository->model());
        Log::createAdminLog(Log::SHOW_TYPE, '公告管理 查看记录');

        return view('admin.articles.index', compact('articles'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (!check_admin_permission('create articles')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $method     = 'POST';
        $action_url = route('articles.store');
        $article    = app($this->repository->model());

        return view('admin.articles.create_and_edit', compact('article', 'action_url', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     * @param ArticleCreateRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(ArticleCreateRequest $request)
    {
        if (!check_admin_permission('create articles')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {
            $input = $request->input('Article');
            $content = $input['content'];
            $input['content']  =strip_tags($input['content'],'<img><a>');
            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_CREATE);
            $input['content'] = $content;
            $article = $this->repository->create($input);

            $response = [
                'message' => trans('添加成功'),
                'data'    => $article->toArray(),
            ];
            Log::createAdminLog(Log::ADD_TYPE, '公告管理 创建记录');
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
        if (!check_admin_permission('show articles')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $article = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $article,
            ]);
        }

        return view('admin.articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!check_admin_permission('edit articles')) {
            abort(403, trans('禁止访问，无权限'));
        }
        $article    = $this->repository->find($id);
        $method     = 'PUT';
        $action_url = route('articles.update', $id);

        return view('admin.articles.create_and_edit', compact('article', 'action_url', 'method'));
    }

    /**
     * Update the specified resource in storage.
     * @param ArticleUpdateRequest $request
     * @param string               $id
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(ArticleUpdateRequest $request, $id)
    {
        if (!check_admin_permission('edit articles')) {
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('禁止访问，无权限'),
                ]);
            }

            return redirect()->back()->withErrors(trans('禁止访问，无权限'))->withInput();
        }
        try {

            $input = $request->input('Article');
            $content = $input['content'];
            $input['content']  =strip_tags($input['content'],'<img><a>');
            $this->validator->with($input)->passesOrFail(ValidatorInterface::RULE_UPDATE);
            $input['content'] = $content;
            $article = $this->repository->update($input, $id);

            $response = [
                'message' => trans('修改成功'),
                'data'    => $article->toArray(),
            ];
            Log::createAdminLog(Log::EDIT_TYPE, '公告管理 修改记录');
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
        $error = 0;
        $success = 0;
        foreach ($idArr as $_id) {
            if ($this->repository->allowDelete ($_id)) {
                $deleted = $this->repository->delete ($_id);
                if ($deleted) {
                    $success++;
                }
            } else {
                $error++;
            }
        }
        if ($error) {
            return response ()->json ([
                'error'   => true,
                'message' => trans ('已删除' . $success . '条记录，存在' . $error . '条记录被使用无法删除'),
            ]);
        }
        Log::createAdminLog(Log::DELETE_TYPE, '公告管理 删除记录');
        if (request()->wantsJson()) {

            return response()->json([
                'message' => trans('删除成功'),
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', trans('删除成功'));
    }
}
