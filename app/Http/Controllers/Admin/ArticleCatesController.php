<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ArticleCateCreateRequest;
use App\Http\Requests\ArticleCateUpdateRequest;
use App\Repositories\ArticleCateRepository;
use App\Validators\ArticleCateValidator;

/**
 * Class ArticleCatesController.
 *
 * @package namespace App\Http\Controllers;
 */
class ArticleCatesController extends Controller
{
    /**
     * @var ArticleCateRepository
     */
    protected $repository;

    /**
     * @var ArticleCateValidator
     */
    protected $validator;

    /**
     * ArticleCatesController constructor.
     *
     * @param ArticleCateRepository $repository
     * @param ArticleCateValidator $validator
     */
    public function __construct(ArticleCateRepository $repository, ArticleCateValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $articleCates = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $articleCates,
            ]);
        }

        return view('articleCates.index', compact('articleCates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ArticleCateCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(ArticleCateCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $articleCate = $this->repository->create($request->all());

            $response = [
                'message' => 'ArticleCate created.',
                'data'    => $articleCate->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $articleCate = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $articleCate,
            ]);
        }

        return view('articleCates.show', compact('articleCate'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $articleCate = $this->repository->find($id);

        return view('articleCates.edit', compact('articleCate'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ArticleCateUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(ArticleCateUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $articleCate = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'ArticleCate updated.',
                'data'    => $articleCate->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (request()->wantsJson()) {

            return response()->json([
                'message' => 'ArticleCate deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'ArticleCate deleted.');
    }
}
