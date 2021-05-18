<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetConsumedDataAPIRequest;
use App\Http\Requests\API\UpdateBudgetConsumedDataAPIRequest;
use App\Models\BudgetConsumedData;
use App\Repositories\BudgetConsumedDataRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BudgetConsumedDataController
 * @package App\Http\Controllers\API
 */

class BudgetConsumedDataAPIController extends AppBaseController
{
    /** @var  BudgetConsumedDataRepository */
    private $budgetConsumedDataRepository;

    public function __construct(BudgetConsumedDataRepository $budgetConsumedDataRepo)
    {
        $this->budgetConsumedDataRepository = $budgetConsumedDataRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetConsumedDatas",
     *      summary="Get a listing of the BudgetConsumedDatas.",
     *      tags={"BudgetConsumedData"},
     *      description="Get all BudgetConsumedDatas",
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
     *                  @SWG\Items(ref="#/definitions/BudgetConsumedData")
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
        $this->budgetConsumedDataRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetConsumedDataRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetConsumedDatas = $this->budgetConsumedDataRepository->all();

        return $this->sendResponse($budgetConsumedDatas->toArray(), trans('custom.not_found', ['attribute' => trans('custom.budget_consumed_data')]));
    }

    /**
     * @param CreateBudgetConsumedDataAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/budgetConsumedDatas",
     *      summary="Store a newly created BudgetConsumedData in storage",
     *      tags={"BudgetConsumedData"},
     *      description="Store BudgetConsumedData",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetConsumedData that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetConsumedData")
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
     *                  ref="#/definitions/BudgetConsumedData"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBudgetConsumedDataAPIRequest $request)
    {
        $input = $request->all();

        $budgetConsumedDatas = $this->budgetConsumedDataRepository->create($input);

        return $this->sendResponse($budgetConsumedDatas->toArray(), trans('custom.save', ['attribute' => trans('custom.budget_consumed_data')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetConsumedDatas/{id}",
     *      summary="Display the specified BudgetConsumedData",
     *      tags={"BudgetConsumedData"},
     *      description="Get BudgetConsumedData",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetConsumedData",
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
     *                  ref="#/definitions/BudgetConsumedData"
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
        /** @var BudgetConsumedData $budgetConsumedData */
        $budgetConsumedData = $this->budgetConsumedDataRepository->findWithoutFail($id);

        if (empty($budgetConsumedData)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budget_consumed_data')]));
        }

        return $this->sendResponse($budgetConsumedData->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.budget_consumed_data')]));
    }

    /**
     * @param int $id
     * @param UpdateBudgetConsumedDataAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/budgetConsumedDatas/{id}",
     *      summary="Update the specified BudgetConsumedData in storage",
     *      tags={"BudgetConsumedData"},
     *      description="Update BudgetConsumedData",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetConsumedData",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetConsumedData that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetConsumedData")
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
     *                  ref="#/definitions/BudgetConsumedData"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudgetConsumedDataAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetConsumedData $budgetConsumedData */
        $budgetConsumedData = $this->budgetConsumedDataRepository->findWithoutFail($id);

        if (empty($budgetConsumedData)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budget_consumed_data')]));
        }

        $budgetConsumedData = $this->budgetConsumedDataRepository->update($input, $id);

        return $this->sendResponse($budgetConsumedData->toArray(), trans('custom.update', ['attribute' => trans('custom.budget_consumed_data')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/budgetConsumedDatas/{id}",
     *      summary="Remove the specified BudgetConsumedData from storage",
     *      tags={"BudgetConsumedData"},
     *      description="Delete BudgetConsumedData",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetConsumedData",
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
        /** @var BudgetConsumedData $budgetConsumedData */
        $budgetConsumedData = $this->budgetConsumedDataRepository->findWithoutFail($id);

        if (empty($budgetConsumedData)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budget_consumed_data')]));
        }

        $budgetConsumedData->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.budget_consumed_data')]));
    }
}
