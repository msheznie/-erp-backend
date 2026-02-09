<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankMemoTypesAPIRequest;
use App\Http\Requests\API\UpdateBankMemoTypesAPIRequest;
use App\Models\BankMemoTypes;
use App\Repositories\BankMemoTypesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BankMemoTypesController
 * @package App\Http\Controllers\API
 */

class BankMemoTypesAPIController extends AppBaseController
{
    /** @var  BankMemoTypesRepository */
    private $bankMemoTypesRepository;

    public function __construct(BankMemoTypesRepository $bankMemoTypesRepo)
    {
        $this->bankMemoTypesRepository = $bankMemoTypesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bankMemoTypes",
     *      summary="Get a listing of the BankMemoTypes.",
     *      tags={"BankMemoTypes"},
     *      description="Get all BankMemoTypes",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/BankMemoTypes")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->bankMemoTypesRepository->pushCriteria(new RequestCriteria($request));
        $this->bankMemoTypesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bankMemoTypes = $this->bankMemoTypesRepository->all();

        return $this->sendResponse($bankMemoTypes->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_memo_types')]));
    }

    /**
     * @param CreateBankMemoTypesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bankMemoTypes",
     *      summary="Store a newly created BankMemoTypes in storage",
     *      tags={"BankMemoTypes"},
     *      description="Store BankMemoTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BankMemoTypes that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BankMemoTypes")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/BankMemoTypes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBankMemoTypesAPIRequest $request)
    {
        $input = $request->all();

        $bankMemoTypes = $this->bankMemoTypesRepository->create($input);

        return $this->sendResponse($bankMemoTypes->toArray(), trans('custom.save', ['attribute' => trans('custom.bank_memo_types')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bankMemoTypes/{id}",
     *      summary="Display the specified BankMemoTypes",
     *      tags={"BankMemoTypes"},
     *      description="Get BankMemoTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankMemoTypes",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/BankMemoTypes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var BankMemoTypes $bankMemoTypes */
        $bankMemoTypes = $this->bankMemoTypesRepository->findWithoutFail($id);

        if (empty($bankMemoTypes)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_memo_types')]));
        }

        return $this->sendResponse($bankMemoTypes->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_memo_types')]));
    }

    /**
     * @param int $id
     * @param UpdateBankMemoTypesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bankMemoTypes/{id}",
     *      summary="Update the specified BankMemoTypes in storage",
     *      tags={"BankMemoTypes"},
     *      description="Update BankMemoTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankMemoTypes",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BankMemoTypes that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BankMemoTypes")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/BankMemoTypes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBankMemoTypesAPIRequest $request)
    {
        $input = $request->all();

        /** @var BankMemoTypes $bankMemoTypes */
        $bankMemoTypes = $this->bankMemoTypesRepository->findWithoutFail($id);

        if (empty($bankMemoTypes)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_memo_types')]));
        }

        $bankMemoTypes = $this->bankMemoTypesRepository->update($input, $id);

        return $this->sendResponse($bankMemoTypes->toArray(), trans('custom.update', ['attribute' => trans('custom.bank_memo_types')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bankMemoTypes/{id}",
     *      summary="Remove the specified BankMemoTypes from storage",
     *      tags={"BankMemoTypes"},
     *      description="Delete BankMemoTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankMemoTypes",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var BankMemoTypes $bankMemoTypes */
        $bankMemoTypes = $this->bankMemoTypesRepository->findWithoutFail($id);

        if (empty($bankMemoTypes)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_memo_types')]));
        }

        $bankMemoTypes->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.bank_memo_types')]));
    }
}
