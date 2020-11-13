<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Log;
use App\Http\Controllers\Controller;
use App\Libs\QueryWhere;
use App\Presenters\ExpressDeliveryPresenter;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ExpressDeliveryCreateRequest;
use App\Http\Requests\ExpressDeliveryUpdateRequest;
use App\Repositories\ExpressDeliveryRepositoryEloquent as ExpressDeliveryRepository;
use App\Validators\ExpressDeliveryValidator;

/**
 * Class ExpressDeliveriesController.
 * @package namespace App\Http\Controllers;
 */
class ExpressDeliveriesController extends Controller
{
    /**
     * @var ExpressDeliveryRepository
     */
    protected $repository;

    /**
     * @var ExpressDeliveryValidator
     */
    protected $validator;
    /**
     * @var ExpressDeliveryPresenter
     */
    protected $presenter;

    /**
     * ExpressDeliveriesController constructor.
     * @param ExpressDeliveryRepository $repository
     * @param ExpressDeliveryValidator  $validator
     */
    public function __construct (ExpressDeliveryRepository $repository, ExpressDeliveryValidator $validator, ExpressDeliveryPresenter $presenter)
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
        if (!check_admin_permission ('show expressDeliveries')) {
            abort (403, trans ('禁止访问，无权限'));
        }

        $this->repository->pushCriteria (app ('Prettus\Repository\Criteria\RequestCriteria'));

        if (request ()->wantsJson ()) {
            $orderBy = $request->_order_by ?? 'sort asc';
            QueryWhere::setRequest ($request);
            $M = app ($this->repository->model ());
            QueryWhere::like ($M, 'name');
            QueryWhere::orderBy ($M, $orderBy);
            $expressDeliveries = $M->paginate ();
            $html              = '';

            foreach ($expressDeliveries as $item) {
                $button = '';
                $button .= get_auth_show_button ('show expressDeliveries', route ('expressDeliveries.show', $item->id));
                $button .= get_auth_edit_button ('edit expressDeliveries', route ('expressDeliveries.edit', $item->id));
                if($this->repository->allowDelete($item->id)){
                    $button .= get_auth_delete_button ('delete expressDeliveries', route ('expressDeliveries.destroy', $item->id));
                }

                $html .= '<tr>
                                    <td><input class="check-item" type="checkbox" value="' . $item->id . '"></td>
                                    <td>' . $item->name . '</td>
                                    <td>' . $item->sort . '</td>
                                    <td>' . $item->updated_at . '</td>
                                    <td>
                                        <div class="btn-group">
                                            ' . $button . '
                                        </div>
                                    </td>
                                </tr>';
            }
            $page = html_entity_decode ($expressDeliveries->links ());

            $total = $expressDeliveries->total ();

            return response ()->json ([
                'html'  => $html,
                'page'  => $page,
                'total' => $total,
            ]);
        }
        $buttonHtml      = '';
        $expressDelivery = app ($this->repository->model ());
        Log::createAdminLog (Log::SHOW_TYPE, '快递信息配置 查看记录');

        return view ('admin.expressDeliveries.index', compact ('expressDelivery', 'buttonHtml'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create ()
    {
        if (!check_admin_permission ('create expressDeliveries')) {
            abort (403, trans ('禁止访问，无权限'));
        }
        $method          = 'POST';
        $action_url      = route ('expressDeliveries.store');
        $expressDelivery = app ($this->repository->model ());

        return view ('admin.expressDeliveries.create_and_edit', compact ('expressDelivery', 'action_url', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     * @param ExpressDeliveryCreateRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store (ExpressDeliveryCreateRequest $request)
    {
        if (!check_admin_permission ('create expressDeliveries')) {
            if ($request->wantsJson ()) {

                return response ()->json ([
                    'error'   => true,
                    'message' => trans ('禁止访问，无权限'),
                ]);
            }

            return redirect ()->back ()->withErrors (trans ('禁止访问，无权限'))->withInput ();
        }
        try {
            $input = $request->input ('ExpressDelivery');
            $this->validator->with ($input)->passesOrFail (ValidatorInterface::RULE_CREATE);

            $expressDelivery = $this->repository->create ($input);

            $response = [
                'message' => trans ('添加成功'),
                'data'    => $expressDelivery->toArray (),
            ];
            Log::createAdminLog (Log::ADD_TYPE, '快递信息配置 创建记录');
            if ($request->wantsJson ()) {

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
        if (!check_admin_permission ('show expressDeliveries')) {
            abort (403, trans ('禁止访问，无权限'));
        }
        $expressDelivery = $this->repository->find ($id);

        if (request ()->wantsJson ()) {

            return response ()->json ([
                'data' => $expressDelivery,
            ]);
        }

        return view ('admin.expressDeliveries.show', compact ('expressDelivery'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit ($id)
    {
        if (!check_admin_permission ('edit expressDeliveries')) {
            abort (403, trans ('禁止访问，无权限'));
        }
        $expressDelivery = $this->repository->find ($id);
        $method          = 'PUT';
        $action_url      = route ('expressDeliveries.update', $id);

        return view ('admin.expressDeliveries.create_and_edit', compact ('expressDelivery', 'action_url', 'method'));
    }

    /**
     * Update the specified resource in storage.
     * @param ExpressDeliveryUpdateRequest $request
     * @param string                       $id
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update (ExpressDeliveryUpdateRequest $request, $id)
    {
        if (!check_admin_permission ('edit expressDeliveries')) {
            if ($request->wantsJson ()) {

                return response ()->json ([
                    'error'   => true,
                    'message' => trans ('禁止访问，无权限'),
                ]);
            }

            return redirect ()->back ()->withErrors (trans ('禁止访问，无权限'))->withInput ();
        }
        try {

            $input = $request->input ('ExpressDelivery');
            $this->validator->with ($input)->passesOrFail (ValidatorInterface::RULE_UPDATE);

            $expressDelivery = $this->repository->update ($input, $id);

            $response = [
                'message' => trans ('修改成功'),
                'data'    => $expressDelivery->toArray (),
            ];
            Log::createAdminLog (Log::EDIT_TYPE, '快递信息配置 修改记录');
            if ($request->wantsJson ()) {

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
        if (!check_admin_permission ('delete expressDeliveries')) {
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

        Log::createAdminLog (Log::DELETE_TYPE, '快递信息配置 删除记录');
        if (request ()->wantsJson ()) {

            return response ()->json ([
                'message' => trans ('删除成功'),
                'deleted' => $deleted,
            ]);
        }

        return redirect ()->back ()->with ('message', trans ('删除成功'));
    }

    /**
     * 所有对照表
     * add by gui
     */
    public function allCode()
    {
        $allCode = $this->repository->codeItem();
        return view ('admin.expressDeliveries.allCode', compact ('allCode'));
    }
}
