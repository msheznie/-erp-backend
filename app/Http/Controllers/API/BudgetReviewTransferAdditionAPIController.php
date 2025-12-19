<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetReviewTransferAdditionAPIRequest;
use App\Http\Requests\API\UpdateBudgetReviewTransferAdditionAPIRequest;
use App\Models\BudgetReviewTransferAddition;
use App\Repositories\BudgetReviewTransferAdditionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BudgetReviewTransferAdditionController
 * @package App\Http\Controllers\API
 */

class BudgetReviewTransferAdditionAPIController extends AppBaseController
{
    /** @var  BudgetReviewTransferAdditionRepository */
    private $budgetReviewTransferAdditionRepository;

    public function __construct(BudgetReviewTransferAdditionRepository $budgetReviewTransferAdditionRepo)
    {
        $this->budgetReviewTransferAdditionRepository = $budgetReviewTransferAdditionRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetReviewTransferAdditions",
     *      summary="Get a listing of the BudgetReviewTransferAdditions.",
     *      tags={"BudgetReviewTransferAddition"},
     *      description="Get all BudgetReviewTransferAdditions",
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
     *                  @SWG\Items(ref="#/definitions/BudgetReviewTransferAddition")
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
        $this->budgetReviewTransferAdditionRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetReviewTransferAdditionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetReviewTransferAdditions = $this->budgetReviewTransferAdditionRepository->all();

        return $this->sendResponse($budgetReviewTransferAdditions->toArray(), trans('custom.budget_review_transfer_additions_retrieved_success'));
    }

    /**
     * @param CreateBudgetReviewTransferAdditionAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/budgetReviewTransferAdditions",
     *      summary="Store a newly created BudgetReviewTransferAddition in storage",
     *      tags={"BudgetReviewTransferAddition"},
     *      description="Store BudgetReviewTransferAddition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetReviewTransferAddition that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetReviewTransferAddition")
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
     *                  ref="#/definitions/BudgetReviewTransferAddition"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBudgetReviewTransferAdditionAPIRequest $request)
    {
        $input = $request->all();

        $budgetReviewTransferAddition = $this->budgetReviewTransferAdditionRepository->create($input);

        return $this->sendResponse($budgetReviewTransferAddition->toArray(), trans('custom.budget_review_transfer_addition_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetReviewTransferAdditions/{id}",
     *      summary="Display the specified BudgetReviewTransferAddition",
     *      tags={"BudgetReviewTransferAddition"},
     *      description="Get BudgetReviewTransferAddition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetReviewTransferAddition",
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
     *                  ref="#/definitions/BudgetReviewTransferAddition"
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
        /** @var BudgetReviewTransferAddition $budgetReviewTransferAddition */
        $budgetReviewTransferAddition = $this->budgetReviewTransferAdditionRepository->findWithoutFail($id);

        if (empty($budgetReviewTransferAddition)) {
            return $this->sendError(trans('custom.budget_review_transfer_addition_not_found'));
        }

        return $this->sendResponse($budgetReviewTransferAddition->toArray(), trans('custom.budget_review_transfer_addition_retrieved_successf'));
    }

    /**
     * @param int $id
     * @param UpdateBudgetReviewTransferAdditionAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/budgetReviewTransferAdditions/{id}",
     *      summary="Update the specified BudgetReviewTransferAddition in storage",
     *      tags={"BudgetReviewTransferAddition"},
     *      description="Update BudgetReviewTransferAddition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetReviewTransferAddition",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetReviewTransferAddition that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetReviewTransferAddition")
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
     *                  ref="#/definitions/BudgetReviewTransferAddition"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudgetReviewTransferAdditionAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetReviewTransferAddition $budgetReviewTransferAddition */
        $budgetReviewTransferAddition = $this->budgetReviewTransferAdditionRepository->findWithoutFail($id);

        if (empty($budgetReviewTransferAddition)) {
            return $this->sendError(trans('custom.budget_review_transfer_addition_not_found'));
        }

        $budgetReviewTransferAddition = $this->budgetReviewTransferAdditionRepository->update($input, $id);

        return $this->sendResponse($budgetReviewTransferAddition->toArray(), trans('custom.budgetreviewtransferaddition_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/budgetReviewTransferAdditions/{id}",
     *      summary="Remove the specified BudgetReviewTransferAddition from storage",
     *      tags={"BudgetReviewTransferAddition"},
     *      description="Delete BudgetReviewTransferAddition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetReviewTransferAddition",
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
        /** @var BudgetReviewTransferAddition $budgetReviewTransferAddition */
        $budgetReviewTransferAddition = $this->budgetReviewTransferAdditionRepository->findWithoutFail($id);

        if (empty($budgetReviewTransferAddition)) {
            return $this->sendError(trans('custom.budget_review_transfer_addition_not_found'));
        }

        $budgetReviewTransferAddition->delete();

        return $this->sendSuccess('Budget Review Transfer Addition deleted successfully');
    }

    public function getBudgetReviewTransferAddition(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $budgetTransferType = $input['budgetTransferType'];

        $budgetAddition = BudgetReviewTransferAddition::where('budgetTransferType', $budgetTransferType)
            ->where('budgetTransferAdditionID', $id)
            ->with(['purchase_request' => function($query){
                    $query->with(['financeCategory', 'segment', 'location', 'priority','created_by', 'document_by'])
                    ->where('cancelledYN', 0)
                    ->where('approved', 0)
                    ->where('budgetBlockYN', -1);
            }, 'purchase_order' => function($query){
                    $query->with(['financeCategory', 'segment', 'supplier', 'created_by','currency', 'document_by'])
                    ->where('poCancelledYN', 0)
                    ->where('approved', 0)
                    ->where('budgetBlockYN', -1);
            }])
            ->get();
        return $this->sendResponse($budgetAddition->toArray(), trans('custom.budget_review_transfer_addition_retrieved_successf'));
    }
}
