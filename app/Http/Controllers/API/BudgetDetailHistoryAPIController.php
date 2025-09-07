<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetDetailHistoryAPIRequest;
use App\Http\Requests\API\UpdateBudgetDetailHistoryAPIRequest;
use App\Models\BudgetDetailHistory;
use App\Repositories\BudgetDetailHistoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BudgetDetailHistoryController
 * @package App\Http\Controllers\API
 */

class BudgetDetailHistoryAPIController extends AppBaseController
{
    /** @var  BudgetDetailHistoryRepository */
    private $budgetDetailHistoryRepository;

    public function __construct(BudgetDetailHistoryRepository $budgetDetailHistoryRepo)
    {
        $this->budgetDetailHistoryRepository = $budgetDetailHistoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetDetailHistories",
     *      summary="Get a listing of the BudgetDetailHistories.",
     *      tags={"BudgetDetailHistory"},
     *      description="Get all BudgetDetailHistories",
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
     *                  @SWG\Items(ref="#/definitions/BudgetDetailHistory")
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
        $this->budgetDetailHistoryRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetDetailHistoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetDetailHistories = $this->budgetDetailHistoryRepository->all();

        return $this->sendResponse($budgetDetailHistories->toArray(), trans('custom.budget_detail_histories_retrieved_successfully'));
    }

    /**
     * @param CreateBudgetDetailHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/budgetDetailHistories",
     *      summary="Store a newly created BudgetDetailHistory in storage",
     *      tags={"BudgetDetailHistory"},
     *      description="Store BudgetDetailHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetDetailHistory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetDetailHistory")
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
     *                  ref="#/definitions/BudgetDetailHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBudgetDetailHistoryAPIRequest $request)
    {
        $input = $request->all();

        $budgetDetailHistory = $this->budgetDetailHistoryRepository->create($input);

        return $this->sendResponse($budgetDetailHistory->toArray(), trans('custom.budget_detail_history_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetDetailHistories/{id}",
     *      summary="Display the specified BudgetDetailHistory",
     *      tags={"BudgetDetailHistory"},
     *      description="Get BudgetDetailHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetDetailHistory",
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
     *                  ref="#/definitions/BudgetDetailHistory"
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
        /** @var BudgetDetailHistory $budgetDetailHistory */
        $budgetDetailHistory = $this->budgetDetailHistoryRepository->findWithoutFail($id);

        if (empty($budgetDetailHistory)) {
            return $this->sendError(trans('custom.budget_detail_history_not_found'));
        }

        return $this->sendResponse($budgetDetailHistory->toArray(), trans('custom.budget_detail_history_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateBudgetDetailHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/budgetDetailHistories/{id}",
     *      summary="Update the specified BudgetDetailHistory in storage",
     *      tags={"BudgetDetailHistory"},
     *      description="Update BudgetDetailHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetDetailHistory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetDetailHistory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetDetailHistory")
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
     *                  ref="#/definitions/BudgetDetailHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudgetDetailHistoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetDetailHistory $budgetDetailHistory */
        $budgetDetailHistory = $this->budgetDetailHistoryRepository->findWithoutFail($id);

        if (empty($budgetDetailHistory)) {
            return $this->sendError(trans('custom.budget_detail_history_not_found'));
        }

        $budgetDetailHistory = $this->budgetDetailHistoryRepository->update($input, $id);

        return $this->sendResponse($budgetDetailHistory->toArray(), trans('custom.budgetdetailhistory_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/budgetDetailHistories/{id}",
     *      summary="Remove the specified BudgetDetailHistory from storage",
     *      tags={"BudgetDetailHistory"},
     *      description="Delete BudgetDetailHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetDetailHistory",
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
        /** @var BudgetDetailHistory $budgetDetailHistory */
        $budgetDetailHistory = $this->budgetDetailHistoryRepository->findWithoutFail($id);

        if (empty($budgetDetailHistory)) {
            return $this->sendError(trans('custom.budget_detail_history_not_found'));
        }

        $budgetDetailHistory->delete();

        return $this->sendSuccess('Budget Detail History deleted successfully');
    }
}
