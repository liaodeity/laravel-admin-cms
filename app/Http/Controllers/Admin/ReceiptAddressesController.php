<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ReceiptAddressRepositoryEloquent;
use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ReceiptAddressCreateRequest;
use App\Http\Requests\ReceiptAddressUpdateRequest;
use App\Repositories\ReceiptAddressRepository;
use App\Validators\ReceiptAddressValidator;

/**
 * Class ReceiptAddressesController.
 *
 * @package namespace App\Http\Controllers;
 */
class ReceiptAddressesController extends Controller
{
    /**
     * @var ReceiptAddressRepository
     */
    protected $repository;

    /**
     * @var ReceiptAddressValidator
     */
    protected $validator;

    /**
     * ReceiptAddressesController constructor.
     *
     * @param ReceiptAddressRepository $repository
     * @param ReceiptAddressValidator $validator
     */
    public function __construct(ReceiptAddressRepositoryEloquent $repository, ReceiptAddressValidator $validator)
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
        $receiptAddresses = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $receiptAddresses,
            ]);
        }

        return view('receiptAddresses.index', compact('receiptAddresses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ReceiptAddressCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(ReceiptAddressCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $receiptAddress = $this->repository->create($request->all());

            $response = [
                'message' => 'ReceiptAddress created.',
                'data'    => $receiptAddress->toArray(),
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
        $receiptAddress = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $receiptAddress,
            ]);
        }

        return view('receiptAddresses.show', compact('receiptAddress'));
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
        $receiptAddress = $this->repository->find($id);

        return view('receiptAddresses.edit', compact('receiptAddress'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ReceiptAddressUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(ReceiptAddressUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $receiptAddress = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'ReceiptAddress updated.',
                'data'    => $receiptAddress->toArray(),
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
                'message' => 'ReceiptAddress deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'ReceiptAddress deleted.');
    }
}
