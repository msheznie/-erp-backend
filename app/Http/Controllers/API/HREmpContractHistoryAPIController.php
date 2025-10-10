<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHREmpContractHistoryAPIRequest;
use App\Http\Requests\API\UpdateHREmpContractHistoryAPIRequest;
use App\Models\HREmpContractHistory;
use App\Repositories\HREmpContractHistoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HREmpContractHistoryController
 * @package App\Http\Controllers\API
 */

class HREmpContractHistoryAPIController extends AppBaseController
{
    /** @var  HREmpContractHistoryRepository */
    private $hREmpContractHistoryRepository;

    public function __construct(HREmpContractHistoryRepository $hREmpContractHistoryRepo)
    {
        $this->hREmpContractHistoryRepository = $hREmpContractHistoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/hREmpContractHistories",
     *      summary="Get a listing of the HREmpContractHistories.",
     *      tags={"HREmpContractHistory"},
     *      description="Get all HREmpContractHistories",
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
     *                  @SWG\Items(ref="#/definitions/HREmpContractHistory")
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
        $this->hREmpContractHistoryRepository->pushCriteria(new RequestCriteria($request));
        $this->hREmpContractHistoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hREmpContractHistories = $this->hREmpContractHistoryRepository->all();

        return $this->sendResponse($hREmpContractHistories->toArray(), trans('custom.h_r_emp_contract_histories_retrieved_successfully'));
    }

    /**
     * @param CreateHREmpContractHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/hREmpContractHistories",
     *      summary="Store a newly created HREmpContractHistory in storage",
     *      tags={"HREmpContractHistory"},
     *      description="Store HREmpContractHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HREmpContractHistory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HREmpContractHistory")
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
     *                  ref="#/definitions/HREmpContractHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHREmpContractHistoryAPIRequest $request)
    {
        $input = $request->all();

        $hREmpContractHistory = $this->hREmpContractHistoryRepository->create($input);

        return $this->sendResponse($hREmpContractHistory->toArray(), trans('custom.h_r_emp_contract_history_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/hREmpContractHistories/{id}",
     *      summary="Display the specified HREmpContractHistory",
     *      tags={"HREmpContractHistory"},
     *      description="Get HREmpContractHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HREmpContractHistory",
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
     *                  ref="#/definitions/HREmpContractHistory"
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
        /** @var HREmpContractHistory $hREmpContractHistory */
        $hREmpContractHistory = $this->hREmpContractHistoryRepository->findWithoutFail($id);

        if (empty($hREmpContractHistory)) {
            return $this->sendError(trans('custom.h_r_emp_contract_history_not_found'));
        }

        return $this->sendResponse($hREmpContractHistory->toArray(), trans('custom.h_r_emp_contract_history_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateHREmpContractHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/hREmpContractHistories/{id}",
     *      summary="Update the specified HREmpContractHistory in storage",
     *      tags={"HREmpContractHistory"},
     *      description="Update HREmpContractHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HREmpContractHistory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HREmpContractHistory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HREmpContractHistory")
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
     *                  ref="#/definitions/HREmpContractHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHREmpContractHistoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var HREmpContractHistory $hREmpContractHistory */
        $hREmpContractHistory = $this->hREmpContractHistoryRepository->findWithoutFail($id);

        if (empty($hREmpContractHistory)) {
            return $this->sendError(trans('custom.h_r_emp_contract_history_not_found'));
        }

        $hREmpContractHistory = $this->hREmpContractHistoryRepository->update($input, $id);

        return $this->sendResponse($hREmpContractHistory->toArray(), trans('custom.hrempcontracthistory_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/hREmpContractHistories/{id}",
     *      summary="Remove the specified HREmpContractHistory from storage",
     *      tags={"HREmpContractHistory"},
     *      description="Delete HREmpContractHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HREmpContractHistory",
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
        /** @var HREmpContractHistory $hREmpContractHistory */
        $hREmpContractHistory = $this->hREmpContractHistoryRepository->findWithoutFail($id);

        if (empty($hREmpContractHistory)) {
            return $this->sendError(trans('custom.h_r_emp_contract_history_not_found'));
        }

        $hREmpContractHistory->delete();

        return $this->sendSuccess('H R Emp Contract History deleted successfully');
    }
}
