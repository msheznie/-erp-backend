<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetAdditionRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateBudgetAdditionRefferedBackAPIRequest;
use App\Models\BudgetAdditionRefferedBack;
use App\Repositories\BudgetAdditionRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\BudgetAdditionDetailRefferedBack;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BudgetAdditionRefferedBackController
 * @package App\Http\Controllers\API
 */

class BudgetAdditionRefferedBackAPIController extends AppBaseController
{
    /** @var  BudgetAdditionRefferedBackRepository */
    private $budgetAdditionRefferedBackRepository;

    public function __construct(BudgetAdditionRefferedBackRepository $budgetAdditionRefferedBackRepo)
    {
        $this->budgetAdditionRefferedBackRepository = $budgetAdditionRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetAdditionRefferedBacks",
     *      summary="Get a listing of the BudgetAdditionRefferedBacks.",
     *      tags={"BudgetAdditionRefferedBack"},
     *      description="Get all BudgetAdditionRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/BudgetAdditionRefferedBack")
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
        $this->budgetAdditionRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetAdditionRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetAdditionRefferedBacks = $this->budgetAdditionRefferedBackRepository->all();

        return $this->sendResponse($budgetAdditionRefferedBacks->toArray(), trans('custom.budget_addition_reffered_backs_retrieved_successfu'));
    }

    /**
     * @param CreateBudgetAdditionRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/budgetAdditionRefferedBacks",
     *      summary="Store a newly created BudgetAdditionRefferedBack in storage",
     *      tags={"BudgetAdditionRefferedBack"},
     *      description="Store BudgetAdditionRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetAdditionRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetAdditionRefferedBack")
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
     *                  ref="#/definitions/BudgetAdditionRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBudgetAdditionRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $budgetAdditionRefferedBack = $this->budgetAdditionRefferedBackRepository->create($input);

        return $this->sendResponse($budgetAdditionRefferedBack->toArray(), trans('custom.budget_addition_reffered_back_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetAdditionRefferedBacks/{id}",
     *      summary="Display the specified BudgetAdditionRefferedBack",
     *      tags={"BudgetAdditionRefferedBack"},
     *      description="Get BudgetAdditionRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetAdditionRefferedBack",
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
     *                  ref="#/definitions/BudgetAdditionRefferedBack"
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
        /** @var BudgetAdditionRefferedBack $budgetAdditionRefferedBack */
        $budgetAdditionRefferedBack = $this->budgetAdditionRefferedBackRepository->findWithoutFail($id);

        if (empty($budgetAdditionRefferedBack)) {
            return $this->sendError(trans('custom.budget_addition_reffered_back_not_found'));
        }

        return $this->sendResponse($budgetAdditionRefferedBack->toArray(), trans('custom.budget_addition_reffered_back_retrieved_successful'));
    }

    /**
     * @param int $id
     * @param UpdateBudgetAdditionRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/budgetAdditionRefferedBacks/{id}",
     *      summary="Update the specified BudgetAdditionRefferedBack in storage",
     *      tags={"BudgetAdditionRefferedBack"},
     *      description="Update BudgetAdditionRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetAdditionRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetAdditionRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetAdditionRefferedBack")
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
     *                  ref="#/definitions/BudgetAdditionRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudgetAdditionRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetAdditionRefferedBack $budgetAdditionRefferedBack */
        $budgetAdditionRefferedBack = $this->budgetAdditionRefferedBackRepository->findWithoutFail($id);

        if (empty($budgetAdditionRefferedBack)) {
            return $this->sendError(trans('custom.budget_addition_reffered_back_not_found'));
        }

        $budgetAdditionRefferedBack = $this->budgetAdditionRefferedBackRepository->update($input, $id);

        return $this->sendResponse($budgetAdditionRefferedBack->toArray(), trans('custom.budgetadditionrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/budgetAdditionRefferedBacks/{id}",
     *      summary="Remove the specified BudgetAdditionRefferedBack from storage",
     *      tags={"BudgetAdditionRefferedBack"},
     *      description="Delete BudgetAdditionRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetAdditionRefferedBack",
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
        /** @var BudgetAdditionRefferedBack $budgetAdditionRefferedBack */
        $budgetAdditionRefferedBack = $this->budgetAdditionRefferedBackRepository->findWithoutFail($id);

        if (empty($budgetAdditionRefferedBack)) {
            return $this->sendError(trans('custom.budget_addition_reffered_back_not_found'));
        }

        $budgetAdditionRefferedBack->delete();

        return $this->sendSuccess('Budget Addition Reffered Back deleted successfully');
    }

    public function getBudgetAdditionAmendHistory(Request $request)
    {
        $input = $request->all();
        $budgetAdditionID = $input['budgetAdditionID'];
        $budgetAdditionAmendHistory = BudgetAdditionRefferedBack::with(['created_by'])
            ->where('id', $budgetAdditionID)
            ->get();
        return $this->sendResponse($budgetAdditionAmendHistory, trans('custom.budget_addition_amend_retrieved_successfully'));
    }
    public function budget_addition_amend($id)
    {
        $erpBudgetAddition = $this->budgetAdditionRefferedBackRepository->fetchBudgetData($id);
        if (empty($erpBudgetAddition)) {
            return $this->sendError(trans('custom.erp_budget_addition_not_found'));
        }
        return $this->sendResponse($erpBudgetAddition->toArray(), trans('custom.erp_budget_addition_refferedback_retrieved_success'));
    }

    public function getDetailsByBudgetAdditionAmend(Request $request)
    {
        $input = $request->all();
        $amedBudgetAdditonAmendID = $input['id'];
        $amedBudgetAddtion = BudgetAdditionRefferedBack::where('budgetAdditionRefferedBackID', $amedBudgetAdditonAmendID)->first();
        $timesReferred = $amedBudgetAddtion->timesReferred;
        $budgetAddtionID = $amedBudgetAddtion->id;

        $items = BudgetAdditionDetailRefferedBack::where('budgetAdditionFormAutoID', $budgetAddtionID)
            ->where('timesReferred', $timesReferred)
            ->with(['segment', 'template'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.budget_addition_amen_form_detail_retrieved_success'));
    }
}
