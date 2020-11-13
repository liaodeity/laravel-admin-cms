<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\MenuCreateRequest;
use App\Http\Requests\MenuUpdateRequest;
use App\Repositories\MenuRepositoryEloquent as MenuRepository;
use App\Validators\MenuValidator;

/**
 * Class MenusController.
 *
 * @package namespace App\Http\Controllers;
 */
class MenusController extends Controller
{
    /**
     * @var MenuRepository
     */
    protected $repository;

    /**
     * @var MenuValidator
     */
    protected $validator;

    /**
     * MenusController constructor.
     *
     * @param MenuRepository $repository
     * @param MenuValidator $validator
     */
    public function __construct (MenuRepository $repository, MenuValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index ()
    {
        $this->repository->pushCriteria (app ('Prettus\Repository\Criteria\RequestCriteria'));
        $menus = $this->repository->all ();

        if (request ()->wantsJson ()) {

            return response ()->json ([
                'data' => $menus,
            ]);
        }

        return view ('admin.menus.index', compact ('menus'));
    }

    public function create ()
    {
        $menus = $this->repository->all ();
        return view ('admin.menus.create_and_edit', compact ('menus'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MenuCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store (MenuCreateRequest $request)
    {
        try {
            $data              = $request->input ('Menu');
            $data['auth_name'] = '';
            $data['icon'] = '';
            //$data['auth_name'] = trim ('_', str_replace ('/', '_', $data['route_url']));
            $this->validator->with ($data)->passesOrFail (ValidatorInterface::RULE_CREATE);

            $menu = $this->repository->create ($data);
            $response = [
                'message' => '菜单创建成功',
                'data'    => $menu->toArray (),
            ];

            if ($request->wantsJson ()) {

                return response ()->json ($response);
            }

            return redirect ()->back ()->with ('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson ()) {
                return response ()->json ([
                    'error'   => true,
                    'message' => $e->getMessageBag ()->first ()
                ]);
            }

            return redirect ()->back ()->withErrors ($e->getMessageBag ()->first ())->withInput ();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show ($id)
    {
        $menu = $this->repository->find ($id);

        if (request ()->wantsJson ()) {

            return response ()->json ([
                'data' => $menu,
            ]);
        }

        return view ('admin.menus.show', compact ('menu'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit ($id)
    {
        $menu = $this->repository->find ($id);

        return view ('admin.menus.edit', compact ('menu'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MenuUpdateRequest $request
     * @param string $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update (MenuUpdateRequest $request, $id)
    {
        try {

            $this->validator->with ($request->all ())->passesOrFail (ValidatorInterface::RULE_UPDATE);

            $menu = $this->repository->update ($request->all (), $id);

            $response = [
                'message' => 'Menu updated.',
                'data'    => $menu->toArray (),
            ];

            if ($request->wantsJson ()) {

                return response ()->json ($response);
            }

            return redirect ()->back ()->with ('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson ()) {

                return response ()->json ([
                    'error'   => true,
                    'message' => $e->getMessageBag ()
                ]);
            }

            return redirect ()->back ()->withErrors ($e->getMessageBag ())->withInput ();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy ($id)
    {
        $deleted = $this->repository->delete ($id);

        if (request ()->wantsJson ()) {

            return response ()->json ([
                'message' => 'Menu deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect ()->back ()->with ('message', 'Menu deleted.');
    }
}
