<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateContingencyBudgetRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateContingencyBudgetRefferedBackAPIRequest;
use App\Models\ContingencyBudgetRefferedBack;
use App\Repositories\ContingencyBudgetRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ContingencyBudgetRefferedBackController
 * @package App\Http\Controllers\API
 */

class ContingencyBudgetRefferedBackAPIController extends AppBaseController
{
    /** @var  ContingencyBudgetRefferedBackRepository */
    private $contingencyBudgetRefferedBackRepository;

    public function __construct(ContingencyBudgetRefferedBackRepository $contingencyBudgetRefferedBackRepo)
    {
        $this->contingencyBudgetRefferedBackRepository = $contingencyBudgetRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/contingencyBudgetRefferedBacks",
     *      summary="Get a listing of the ContingencyBudgetRefferedBacks.",
     *      tags={"ContingencyBudgetRefferedBack"},
     *      description="Get all ContingencyBudgetRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/ContingencyBudgetRefferedBack")
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
        $this->contingencyBudgetRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->contingencyBudgetRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $contingencyBudgetRefferedBacks = $this->contingencyBudgetRefferedBackRepository->all();

        return $this->sendResponse($contingencyBudgetRefferedBacks->toArray(), trans('custom.contingency_budget_reffered_backs_retrieved_succes'));
    }

    /**
     * @param CreateContingencyBudgetRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/contingencyBudgetRefferedBacks",
     *      summary="Store a newly created ContingencyBudgetRefferedBack in storage",
     *      tags={"ContingencyBudgetRefferedBack"},
     *      description="Store ContingencyBudgetRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ContingencyBudgetRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ContingencyBudgetRefferedBack")
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
     *                  ref="#/definitions/ContingencyBudgetRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateContingencyBudgetRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $contingencyBudgetRefferedBack = $this->contingencyBudgetRefferedBackRepository->create($input);

        return $this->sendResponse($contingencyBudgetRefferedBack->toArray(), trans('custom.contingency_budget_reffered_back_saved_successfull'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/contingencyBudgetRefferedBacks/{id}",
     *      summary="Display the specified ContingencyBudgetRefferedBack",
     *      tags={"ContingencyBudgetRefferedBack"},
     *      description="Get ContingencyBudgetRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ContingencyBudgetRefferedBack",
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
     *                  ref="#/definitions/ContingencyBudgetRefferedBack"
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
        /** @var ContingencyBudgetRefferedBack $contingencyBudgetRefferedBack */
        $contingencyBudgetRefferedBack = $this->contingencyBudgetRefferedBackRepository->findWithoutFail($id);

        if (empty($contingencyBudgetRefferedBack)) {
            return $this->sendError(trans('custom.contingency_budget_reffered_back_not_found'));
        }

        return $this->sendResponse($contingencyBudgetRefferedBack->toArray(), trans('custom.contingency_budget_reffered_back_retrieved_success'));
    }

    /**
     * @param int $id
     * @param UpdateContingencyBudgetRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/contingencyBudgetRefferedBacks/{id}",
     *      summary="Update the specified ContingencyBudgetRefferedBack in storage",
     *      tags={"ContingencyBudgetRefferedBack"},
     *      description="Update ContingencyBudgetRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ContingencyBudgetRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ContingencyBudgetRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ContingencyBudgetRefferedBack")
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
     *                  ref="#/definitions/ContingencyBudgetRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateContingencyBudgetRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var ContingencyBudgetRefferedBack $contingencyBudgetRefferedBack */
        $contingencyBudgetRefferedBack = $this->contingencyBudgetRefferedBackRepository->findWithoutFail($id);

        if (empty($contingencyBudgetRefferedBack)) {
            return $this->sendError(trans('custom.contingency_budget_reffered_back_not_found'));
        }

        $contingencyBudgetRefferedBack = $this->contingencyBudgetRefferedBackRepository->update($input, $id);

        return $this->sendResponse($contingencyBudgetRefferedBack->toArray(), trans('custom.contingencybudgetrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/contingencyBudgetRefferedBacks/{id}",
     *      summary="Remove the specified ContingencyBudgetRefferedBack from storage",
     *      tags={"ContingencyBudgetRefferedBack"},
     *      description="Delete ContingencyBudgetRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ContingencyBudgetRefferedBack",
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
        /** @var ContingencyBudgetRefferedBack $contingencyBudgetRefferedBack */
        $contingencyBudgetRefferedBack = $this->contingencyBudgetRefferedBackRepository->findWithoutFail($id);

        if (empty($contingencyBudgetRefferedBack)) {
            return $this->sendError(trans('custom.contingency_budget_reffered_back_not_found'));
        }

        $contingencyBudgetRefferedBack->delete();

        return $this->sendSuccess('Contingency Budget Reffered Back deleted successfully');
    }
    public function getContingencyAmendHistory(Request $request)
    {
        $input = $request->all();
        $contingencyID = $input['id'];
        $contingency = ContingencyBudgetRefferedBack::where('ID', $contingencyID)
            ->with(['segment_by', 'template_master'])
            ->with([
                'confirmed_by' => function ($q) {
                    $q->select('employeeSystemID', 'empID', 'empName');
                }, 'currency_by' => function ($q) {
                    $q->select('currencyID', 'DecimalPlaces');
                }
            ])->get();
        return $this->sendResponse($contingency, trans('custom.contingency_budget_amend_retrieved_successfully'));
    }
    public function contingencyBudgetAmend($id)
    {
        $contingencyBudgetPlan = $this->contingencyBudgetRefferedBackRepository->with(['confirmed_by', 'currency_by'])->findWithoutFail($id);

        if (empty($contingencyBudgetPlan)) {
            return $this->sendError(trans('custom.contingency_budget_plan_amend_not_found'));
        }

        return $this->sendResponse($contingencyBudgetPlan->toArray(), trans('custom.contingency_budget_plan_amend_retrieved_successful'));
    }
}
