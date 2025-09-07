<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetAdditionDetailRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateBudgetAdditionDetailRefferedBackAPIRequest;
use App\Models\BudgetAdditionDetailRefferedBack;
use App\Repositories\BudgetAdditionDetailRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BudgetAdditionDetailRefferedBackController
 * @package App\Http\Controllers\API
 */

class BudgetAdditionDetailRefferedBackAPIController extends AppBaseController
{
    /** @var  BudgetAdditionDetailRefferedBackRepository */
    private $budgetAdditionDetailRefferedBackRepository;

    public function __construct(BudgetAdditionDetailRefferedBackRepository $budgetAdditionDetailRefferedBackRepo)
    {
        $this->budgetAdditionDetailRefferedBackRepository = $budgetAdditionDetailRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetAdditionDetailRefferedBacks",
     *      summary="Get a listing of the BudgetAdditionDetailRefferedBacks.",
     *      tags={"BudgetAdditionDetailRefferedBack"},
     *      description="Get all BudgetAdditionDetailRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/BudgetAdditionDetailRefferedBack")
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
        $this->budgetAdditionDetailRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetAdditionDetailRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetAdditionDetailRefferedBacks = $this->budgetAdditionDetailRefferedBackRepository->all();

        return $this->sendResponse($budgetAdditionDetailRefferedBacks->toArray(), trans('custom.budget_addition_detail_reffered_backs_retrieved_su'));
    }

    /**
     * @param CreateBudgetAdditionDetailRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/budgetAdditionDetailRefferedBacks",
     *      summary="Store a newly created BudgetAdditionDetailRefferedBack in storage",
     *      tags={"BudgetAdditionDetailRefferedBack"},
     *      description="Store BudgetAdditionDetailRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetAdditionDetailRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetAdditionDetailRefferedBack")
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
     *                  ref="#/definitions/BudgetAdditionDetailRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBudgetAdditionDetailRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $budgetAdditionDetailRefferedBack = $this->budgetAdditionDetailRefferedBackRepository->create($input);

        return $this->sendResponse($budgetAdditionDetailRefferedBack->toArray(), trans('custom.budget_addition_detail_reffered_back_saved_success'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetAdditionDetailRefferedBacks/{id}",
     *      summary="Display the specified BudgetAdditionDetailRefferedBack",
     *      tags={"BudgetAdditionDetailRefferedBack"},
     *      description="Get BudgetAdditionDetailRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetAdditionDetailRefferedBack",
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
     *                  ref="#/definitions/BudgetAdditionDetailRefferedBack"
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
        /** @var BudgetAdditionDetailRefferedBack $budgetAdditionDetailRefferedBack */
        $budgetAdditionDetailRefferedBack = $this->budgetAdditionDetailRefferedBackRepository->findWithoutFail($id);

        if (empty($budgetAdditionDetailRefferedBack)) {
            return $this->sendError(trans('custom.budget_addition_detail_reffered_back_not_found'));
        }

        return $this->sendResponse($budgetAdditionDetailRefferedBack->toArray(), trans('custom.budget_addition_detail_reffered_back_retrieved_suc'));
    }

    /**
     * @param int $id
     * @param UpdateBudgetAdditionDetailRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/budgetAdditionDetailRefferedBacks/{id}",
     *      summary="Update the specified BudgetAdditionDetailRefferedBack in storage",
     *      tags={"BudgetAdditionDetailRefferedBack"},
     *      description="Update BudgetAdditionDetailRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetAdditionDetailRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetAdditionDetailRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetAdditionDetailRefferedBack")
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
     *                  ref="#/definitions/BudgetAdditionDetailRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudgetAdditionDetailRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetAdditionDetailRefferedBack $budgetAdditionDetailRefferedBack */
        $budgetAdditionDetailRefferedBack = $this->budgetAdditionDetailRefferedBackRepository->findWithoutFail($id);

        if (empty($budgetAdditionDetailRefferedBack)) {
            return $this->sendError(trans('custom.budget_addition_detail_reffered_back_not_found'));
        }

        $budgetAdditionDetailRefferedBack = $this->budgetAdditionDetailRefferedBackRepository->update($input, $id);

        return $this->sendResponse($budgetAdditionDetailRefferedBack->toArray(), trans('custom.budgetadditiondetailrefferedback_updated_successfu'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/budgetAdditionDetailRefferedBacks/{id}",
     *      summary="Remove the specified BudgetAdditionDetailRefferedBack from storage",
     *      tags={"BudgetAdditionDetailRefferedBack"},
     *      description="Delete BudgetAdditionDetailRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetAdditionDetailRefferedBack",
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
        /** @var BudgetAdditionDetailRefferedBack $budgetAdditionDetailRefferedBack */
        $budgetAdditionDetailRefferedBack = $this->budgetAdditionDetailRefferedBackRepository->findWithoutFail($id);

        if (empty($budgetAdditionDetailRefferedBack)) {
            return $this->sendError(trans('custom.budget_addition_detail_reffered_back_not_found'));
        }

        $budgetAdditionDetailRefferedBack->delete();

        return $this->sendSuccess('Budget Addition Detail Reffered Back deleted successfully');
    }
}
