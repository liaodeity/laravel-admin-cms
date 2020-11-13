<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Log;
use App\Entities\WxReply;
use App\Entities\WxReplyKeyword;
use App\Http\Controllers\Controller;
use App\Libs\QueryWhere;
use App\Presenters\WxReplyPresenter;
use App\Services\WxReplyService;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\WxReplyCreateRequest;
use App\Http\Requests\WxReplyUpdateRequest;
use App\Repositories\WxReplyRepositoryEloquent as WxReplyRepository;
use App\Validators\WxReplyValidator;

/**
 * Class WxRepliesController.
 * @package namespace App\Http\Controllers;
 */
class WxRepliesController extends Controller
{
    /**
     * @var WxReplyRepository
     */
    protected $repository;

    /**
     * @var WxReplyValidator
     */
    protected $validator;
    /**
     * @var WxReplyPresenter
     */
    protected $presenter;

    /**
     * WxRepliesController constructor.
     * @param WxReplyRepository $repository
     * @param WxReplyValidator  $validator
     */
    public function __construct (WxReplyRepository $repository, WxReplyValidator $validator, WxReplyPresenter $presenter)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->presenter  = $presenter;
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request)
    {
        if (!check_admin_permission ('show wxReplies')) {
            abort (403, trans ('禁止访问，无权限'));
        }

        $this->repository->pushCriteria (app ('Prettus\Repository\Criteria\RequestCriteria'));

        if (request ()->wantsJson ()) {
            $orderBy = $request->_order_by ?? 'updated_at desc';
            $keyword = $request->keyword ?? '';
            QueryWhere::setRequest ($request);
            $M = app ($this->repository->model ());
            QueryWhere::like ($M, 'content');
            QueryWhere::eq ($M, 'status');
            if ($keyword) {
                $M = $M->whereRaw(DB::raw(" id IN(SELECT reply_id FROM `DROP TABLE IF EXISTS `wx_reply_keywords` WHERE keyword like '%$keyword%' )"));
            }
            QueryWhere::orderBy ($M, $orderBy);
            $wxReplies = $M->paginate ();
            $html              = '';

            foreach ($wxReplies as $item) {
                $button = '';
                $button .= get_auth_show_button ('show wxReplies', route ('wxReplies.show', $item->id));
                $button .= get_auth_edit_button ('edit wxReplies', route ('wxReplies.edit', $item->id));
                if($this->repository->allowDelete($item->id)){
                    $button .= get_auth_delete_button ('delete wxReplies', route ('wxReplies.destroy', $item->id));
                }
                $keywords = $this->repository->getKeywordView($item);
                $html .= '<tr>
                                    <td><input class="check-item" type="checkbox" value="' . $item->id . '"></td>
                                    <td>' . $keywords . '</td>
                                    <td>' . $item->isSubscribeItem($item->is_subscribe) . '</td>
                                    <td>' . $item->ifLikeItem($item->if_like) . '</td>
                                    <td>' . $item->content . '</td>
                                    <td>' . $item->updated_at . '</td>
                                    <td>' . $item->statusItem($item->status) . '</td>
                                    <td>
                                        <div class="btn-group">
                                            ' . $button . '
                                        </div>
                                    </td>
                                </tr>';
            }
            $page = html_entity_decode ($wxReplies->links ());

            $total = $wxReplies->total ();

            return response ()->json ([
                'html'  => $html,
                'page'  => $page,
                'total' => $total,
            ]);
        }
        $buttonHtml      = '';
        $wxReply = app ($this->repository->model ());
        Log::createAdminLog (Log::SHOW_TYPE, '微信回复 查看记录');

        return view ('admin.wxReplies.index', compact ('wxReply', 'buttonHtml'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create ()
    {
        if (!check_admin_permission ('create wxReplies')) {
            abort (403, trans ('禁止访问，无权限'));
        }
        $method          = 'POST';
        $action_url      = route ('wxReplies.store');
        $wxReply = app ($this->repository->model ());
        $wxReply->is_subscribe = 0;
        $wxReply->if_like = 1;
        $wxReply->status = 1;
        return view ('admin.wxReplies.create_and_edit', compact ('wxReply', 'action_url', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     * @param WxReplyCreateRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store (WxReplyCreateRequest $request)
    {
        if (!check_admin_permission ('create wxReplies')) {
            if ($request->wantsJson ()) {

                return response ()->json ([
                    'error'   => true,
                    'message' => trans ('禁止访问，无权限'),
                ]);
            }

            return redirect ()->back ()->withErrors (trans ('禁止访问，无权限'))->withInput ();
        }
        try {
            $keywords = $request->input('keywords');
            $keywords = array_filter($keywords);
            if(empty($keywords)){
                return response ()->json ([
                    'error'   => true,
                    'message' => trans ('关键词 不能为空'),
                ]);
            }
            $input = $request->input ('WxReply');
            $this->validator->with ($input)->passesOrFail (ValidatorInterface::RULE_CREATE);
            if($input['is_subscribe'] == 1){
                WxReply::where('is_subscribe',1)->update(['is_subscribe'=>0]);
            }
            $wxReply = $this->repository->create ($input);

            $response = [
                'message' => trans ('添加成功'),
                'data'    => $wxReply->toArray (),
            ];
            Log::createAdminLog (Log::ADD_TYPE, '微信回复 创建记录');
            if ($request->wantsJson ()) {
                $this->repository->updateKeywords($wxReply->id, $keywords);
                return response ()->json ($response);
            }

            return redirect ()->back ()->with ('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson ()) {
                return response ()->json ([
                    'error'   => true,
                    'message' => $e->getMessageBag ()->first (),
                ]);
            }

            return redirect ()->back ()->withErrors ($e->getMessageBag ()->first ())->withInput ();
        } catch (\ErrorException $e) {
            if ($request->wantsJson ()) {

                return response ()->json ([
                    'error'   => true,
                    'message' => $e->getMessage (),
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show ($id)
    {
        if (!check_admin_permission ('show wxReplies')) {
            abort (403, trans ('禁止访问，无权限'));
        }
        $wxReply = $this->repository->find ($id);

        if (request ()->wantsJson ()) {

            return response ()->json ([
                'data' => $wxReply,
            ]);
        }
        $keywords = $this->repository->getKeywordView($wxReply);
        return view ('admin.wxReplies.show', compact ('wxReply','keywords'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit ($id)
    {
        if (!check_admin_permission ('edit wxReplies')) {
            abort (403, trans ('禁止访问，无权限'));
        }
        $wxReply = $this->repository->find ($id);
        $method          = 'PUT';
        $action_url      = route ('wxReplies.update', $id);

        return view ('admin.wxReplies.create_and_edit', compact ('wxReply', 'action_url', 'method'));
    }

    /**
     * Update the specified resource in storage.
     * @param WxReplyUpdateRequest $request
     * @param string                       $id
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update (WxReplyUpdateRequest $request, $id)
    {
        if (!check_admin_permission ('edit wxReplies')) {
            if ($request->wantsJson ()) {

                return response ()->json ([
                    'error'   => true,
                    'message' => trans ('禁止访问，无权限'),
                ]);
            }

            return redirect ()->back ()->withErrors (trans ('禁止访问，无权限'))->withInput ();
        }
        try {
            $keywords = $request->input('keywords');
            $keywords = array_filter($keywords);
            if(empty($keywords)){
                return response ()->json ([
                    'error'   => true,
                    'message' => trans ('关键词 不能为空'),
                ]);
            }
            $input = $request->input ('WxReply');
            $this->validator->with ($input)->passesOrFail (ValidatorInterface::RULE_UPDATE);
            if($input['is_subscribe'] == 1){
                WxReply::where('is_subscribe',1)->update(['is_subscribe'=>0]);
            }
            $wxReply = $this->repository->update ($input, $id);

            $response = [
                'message' => trans ('修改成功'),
                'data'    => $wxReply->toArray (),
            ];
            Log::createAdminLog (Log::EDIT_TYPE, '微信回复 修改记录');
            if ($request->wantsJson ()) {
                $this->repository->updateKeywords($wxReply->id, $keywords);
                return response ()->json ($response);
            }

            return redirect ()->back ()->with ('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson ()) {

                return response ()->json ([
                    'error'   => true,
                    'message' => $e->getMessageBag ()->first (),
                ]);
            }

            return redirect ()->back ()->withErrors ($e->getMessageBag ()->first ())->withInput ();
        } catch (\ErrorException $e) {
            if ($request->wantsJson ()) {

                return response ()->json ([
                    'error'   => true,
                    'message' => $e->getMessage (),
                ]);
            }
        }
    }


    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param int     $id
     * @return \Illuminate\Http\Response
     */
    public function destroy (Request $request, $id)
    {
        if (!check_admin_permission ('delete wxReplies')) {
            if ($request->wantsJson ()) {

                return response ()->json ([
                    'error'   => true,
                    'message' => trans ('禁止访问，无权限'),
                ]);
            }

            return redirect ()->back ()->withErrors (trans ('禁止访问，无权限'))->withInput ();
        }
        $id    = $request->id ?? $id;
        $idArr = explode (',', $id);
        foreach ($idArr as $_id) {
            if($this->repository->allowDelete($_id)){
                $deleted = $this->repository->delete ($_id);
            }
        }

        Log::createAdminLog (Log::DELETE_TYPE, '微信回复 删除记录');
        if (request ()->wantsJson ()) {

            return response ()->json ([
                'message' => trans ('删除成功'),
                'deleted' => $deleted,
            ]);
        }

        return redirect ()->back ()->with ('message', trans ('删除成功'));
    }
}
