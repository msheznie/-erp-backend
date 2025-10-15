<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetMasterRefferedHistoryAPIRequest;
use App\Http\Requests\API\UpdateBudgetMasterRefferedHistoryAPIRequest;
use App\Models\BudgetMasterRefferedHistory;
use App\Repositories\BudgetMasterRefferedHistoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BudgetMasterRefferedHistoryController
 * @package App\Http\Controllers\API
 */

class BudgetMasterRefferedHistoryAPIController extends AppBaseController
{
    /** @var  BudgetMasterRefferedHistoryRepository */
    private $budgetMasterRefferedHistoryRepository;

    public function __construct(BudgetMasterRefferedHistoryRepository $budgetMasterRefferedHistoryRepo)
    {
        $this->budgetMasterRefferedHistoryRepository = $budgetMasterRefferedHistoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetMasterRefferedHistories",
     *      summary="Get a listing of the BudgetMasterRefferedHistories.",
     *      tags={"BudgetMasterRefferedHistory"},
     *      description="Get all BudgetMasterRefferedHistories",
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
     *                  @SWG\Items(ref="#/definitions/BudgetMasterRefferedHistory")
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
        $this->budgetMasterRefferedHistoryRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetMasterRefferedHistoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetMasterRefferedHistories = $this->budgetMasterRefferedHistoryRepository->all();

        return $this->sendResponse($budgetMasterRefferedHistories->toArray(), trans('custom.budget_master_reffered_histories_retrieved_success'));
    }

    /**
     * @param CreateBudgetMasterRefferedHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/budgetMasterRefferedHistories",
     *      summary="Store a newly created BudgetMasterRefferedHistory in storage",
     *      tags={"BudgetMasterRefferedHistory"},
     *      description="Store BudgetMasterRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetMasterRefferedHistory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetMasterRefferedHistory")
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
     *                  ref="#/definitions/BudgetMasterRefferedHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBudgetMasterRefferedHistoryAPIRequest $request)
    {
        $input = $request->all();

        $budgetMasterRefferedHistory = $this->budgetMasterRefferedHistoryRepository->create($input);

        return $this->sendResponse($budgetMasterRefferedHistory->toArray(), trans('custom.budget_master_reffered_history_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetMasterRefferedHistories/{id}",
     *      summary="Display the specified BudgetMasterRefferedHistory",
     *      tags={"BudgetMasterRefferedHistory"},
     *      description="Get BudgetMasterRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetMasterRefferedHistory",
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
     *                  ref="#/definitions/BudgetMasterRefferedHistory"
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
        /** @var BudgetMasterRefferedHistory $budgetMasterRefferedHistory */
        $budgetMasterRefferedHistory = $this->budgetMasterRefferedHistoryRepository->with(['confirmed_by','segment_by', 'template_master', 'finance_year_by'])->findWithoutFail($id);

        if (empty($budgetMasterRefferedHistory)) {
            return $this->sendError(trans('custom.budget_master_reffered_history_not_found'));
        }

        return $this->sendResponse($budgetMasterRefferedHistory->toArray(), trans('custom.budget_master_reffered_history_retrieved_successfu'));
    }

    /**
     * @param int $id
     * @param UpdateBudgetMasterRefferedHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/budgetMasterRefferedHistories/{id}",
     *      summary="Update the specified BudgetMasterRefferedHistory in storage",
     *      tags={"BudgetMasterRefferedHistory"},
     *      description="Update BudgetMasterRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetMasterRefferedHistory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetMasterRefferedHistory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetMasterRefferedHistory")
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
     *                  ref="#/definitions/BudgetMasterRefferedHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudgetMasterRefferedHistoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetMasterRefferedHistory $budgetMasterRefferedHistory */
        $budgetMasterRefferedHistory = $this->budgetMasterRefferedHistoryRepository->findWithoutFail($id);

        if (empty($budgetMasterRefferedHistory)) {
            return $this->sendError(trans('custom.budget_master_reffered_history_not_found'));
        }

        $budgetMasterRefferedHistory = $this->budgetMasterRefferedHistoryRepository->update($input, $id);

        return $this->sendResponse($budgetMasterRefferedHistory->toArray(), trans('custom.budgetmasterrefferedhistory_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/budgetMasterRefferedHistories/{id}",
     *      summary="Remove the specified BudgetMasterRefferedHistory from storage",
     *      tags={"BudgetMasterRefferedHistory"},
     *      description="Delete BudgetMasterRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetMasterRefferedHistory",
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
        /** @var BudgetMasterRefferedHistory $budgetMasterRefferedHistory */
        $budgetMasterRefferedHistory = $this->budgetMasterRefferedHistoryRepository->findWithoutFail($id);

        if (empty($budgetMasterRefferedHistory)) {
            return $this->sendError(trans('custom.budget_master_reffered_history_not_found'));
        }

        $budgetMasterRefferedHistory->delete();

        return $this->sendSuccess('Budget Master Reffered History deleted successfully');
    }


    public function getBudgetAmendHistory(Request $request)
    {
        $input = $request->all();

        $budgetMasterRefferedHistory = BudgetMasterRefferedHistory::where('budgetmasterID', $input['budgetMasterID'])
            ->with(['segment_by', 'template_master', 'finance_year_by'])
            ->get();

        return $this->sendResponse($budgetMasterRefferedHistory, trans('custom.budget_retrieved_successfully'));
    }
}
