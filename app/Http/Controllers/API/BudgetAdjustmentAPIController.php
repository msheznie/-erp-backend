<?php
/**
 * =============================================
 * -- File Name : BudgetAdjustmentAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Budget Adjustment
 * -- Author : Mohamed Fayas
 * -- Create date : 22 - October 2018
 * -- Description : This file contains the all CRUD for Budget Adjustment
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetAdjustmentAPIRequest;
use App\Http\Requests\API\UpdateBudgetAdjustmentAPIRequest;
use App\Models\BudgetAdjustment;
use App\Repositories\BudgetAdjustmentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BudgetAdjustmentController
 * @package App\Http\Controllers\API
 */

class BudgetAdjustmentAPIController extends AppBaseController
{
    /** @var  BudgetAdjustmentRepository */
    private $budgetAdjustmentRepository;

    public function __construct(BudgetAdjustmentRepository $budgetAdjustmentRepo)
    {
        $this->budgetAdjustmentRepository = $budgetAdjustmentRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetAdjustments",
     *      summary="Get a listing of the BudgetAdjustments.",
     *      tags={"BudgetAdjustment"},
     *      description="Get all BudgetAdjustments",
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
     *                  @SWG\Items(ref="#/definitions/BudgetAdjustment")
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
        $this->budgetAdjustmentRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetAdjustmentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetAdjustments = $this->budgetAdjustmentRepository->all();

        return $this->sendResponse($budgetAdjustments->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.budget_adjustments')]));
    }

    /**
     * @param CreateBudgetAdjustmentAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/budgetAdjustments",
     *      summary="Store a newly created BudgetAdjustment in storage",
     *      tags={"BudgetAdjustment"},
     *      description="Store BudgetAdjustment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetAdjustment that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetAdjustment")
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
     *                  ref="#/definitions/BudgetAdjustment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBudgetAdjustmentAPIRequest $request)
    {
        $input = $request->all();

        $budgetAdjustments = $this->budgetAdjustmentRepository->create($input);

        return $this->sendResponse($budgetAdjustments->toArray(), trans('custom.save', ['attribute' => trans('custom.budget_adjustments')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetAdjustments/{id}",
     *      summary="Display the specified BudgetAdjustment",
     *      tags={"BudgetAdjustment"},
     *      description="Get BudgetAdjustment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetAdjustment",
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
     *                  ref="#/definitions/BudgetAdjustment"
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
        /** @var BudgetAdjustment $budgetAdjustment */
        $budgetAdjustment = $this->budgetAdjustmentRepository->findWithoutFail($id);

        if (empty($budgetAdjustment)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budget_adjustments')]));
        }

        return $this->sendResponse($budgetAdjustment->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.budget_adjustments')]));
    }

    /**
     * @param int $id
     * @param UpdateBudgetAdjustmentAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/budgetAdjustments/{id}",
     *      summary="Update the specified BudgetAdjustment in storage",
     *      tags={"BudgetAdjustment"},
     *      description="Update BudgetAdjustment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetAdjustment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetAdjustment that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetAdjustment")
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
     *                  ref="#/definitions/BudgetAdjustment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudgetAdjustmentAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetAdjustment $budgetAdjustment */
        $budgetAdjustment = $this->budgetAdjustmentRepository->findWithoutFail($id);

        if (empty($budgetAdjustment)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budget_adjustments')]));
        }

        $budgetAdjustment = $this->budgetAdjustmentRepository->update($input, $id);

        return $this->sendResponse($budgetAdjustment->toArray(), trans('custom.update', ['attribute' => trans('custom.budget_adjustments')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/budgetAdjustments/{id}",
     *      summary="Remove the specified BudgetAdjustment from storage",
     *      tags={"BudgetAdjustment"},
     *      description="Delete BudgetAdjustment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetAdjustment",
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
        /** @var BudgetAdjustment $budgetAdjustment */
        $budgetAdjustment = $this->budgetAdjustmentRepository->findWithoutFail($id);

        if (empty($budgetAdjustment)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budget_adjustments')]));
        }

        $budgetAdjustment->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.budget_adjustments')]));
    }
}
