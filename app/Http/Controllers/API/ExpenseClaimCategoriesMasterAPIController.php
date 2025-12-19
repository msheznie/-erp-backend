<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateExpenseClaimCategoriesMasterAPIRequest;
use App\Http\Requests\API\UpdateExpenseClaimCategoriesMasterAPIRequest;
use App\Models\ExpenseClaimCategoriesMaster;
use App\Repositories\ExpenseClaimCategoriesMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ExpenseClaimCategoriesMasterController
 * @package App\Http\Controllers\API
 */

class ExpenseClaimCategoriesMasterAPIController extends AppBaseController
{
    /** @var  ExpenseClaimCategoriesMasterRepository */
    private $expenseClaimCategoriesMasterRepository;

    public function __construct(ExpenseClaimCategoriesMasterRepository $expenseClaimCategoriesMasterRepo)
    {
        $this->expenseClaimCategoriesMasterRepository = $expenseClaimCategoriesMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseClaimCategoriesMasters",
     *      summary="Get a listing of the ExpenseClaimCategoriesMasters.",
     *      tags={"ExpenseClaimCategoriesMaster"},
     *      description="Get all ExpenseClaimCategoriesMasters",
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
     *                  @SWG\Items(ref="#/definitions/ExpenseClaimCategoriesMaster")
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
        $this->expenseClaimCategoriesMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->expenseClaimCategoriesMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $expenseClaimCategoriesMasters = $this->expenseClaimCategoriesMasterRepository->all();

        return $this->sendResponse($expenseClaimCategoriesMasters->toArray(), trans('custom.expense_claim_categories_masters_retrieved_success'));
    }

    /**
     * @param CreateExpenseClaimCategoriesMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/expenseClaimCategoriesMasters",
     *      summary="Store a newly created ExpenseClaimCategoriesMaster in storage",
     *      tags={"ExpenseClaimCategoriesMaster"},
     *      description="Store ExpenseClaimCategoriesMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseClaimCategoriesMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseClaimCategoriesMaster")
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
     *                  ref="#/definitions/ExpenseClaimCategoriesMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateExpenseClaimCategoriesMasterAPIRequest $request)
    {
        $input = $request->all();

        $expenseClaimCategoriesMaster = $this->expenseClaimCategoriesMasterRepository->create($input);

        return $this->sendResponse($expenseClaimCategoriesMaster->toArray(), trans('custom.expense_claim_categories_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseClaimCategoriesMasters/{id}",
     *      summary="Display the specified ExpenseClaimCategoriesMaster",
     *      tags={"ExpenseClaimCategoriesMaster"},
     *      description="Get ExpenseClaimCategoriesMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaimCategoriesMaster",
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
     *                  ref="#/definitions/ExpenseClaimCategoriesMaster"
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
        /** @var ExpenseClaimCategoriesMaster $expenseClaimCategoriesMaster */
        $expenseClaimCategoriesMaster = $this->expenseClaimCategoriesMasterRepository->findWithoutFail($id);

        if (empty($expenseClaimCategoriesMaster)) {
            return $this->sendError(trans('custom.expense_claim_categories_master_not_found'));
        }

        return $this->sendResponse($expenseClaimCategoriesMaster->toArray(), trans('custom.expense_claim_categories_master_retrieved_successf'));
    }

    /**
     * @param int $id
     * @param UpdateExpenseClaimCategoriesMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/expenseClaimCategoriesMasters/{id}",
     *      summary="Update the specified ExpenseClaimCategoriesMaster in storage",
     *      tags={"ExpenseClaimCategoriesMaster"},
     *      description="Update ExpenseClaimCategoriesMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaimCategoriesMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseClaimCategoriesMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseClaimCategoriesMaster")
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
     *                  ref="#/definitions/ExpenseClaimCategoriesMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateExpenseClaimCategoriesMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var ExpenseClaimCategoriesMaster $expenseClaimCategoriesMaster */
        $expenseClaimCategoriesMaster = $this->expenseClaimCategoriesMasterRepository->findWithoutFail($id);

        if (empty($expenseClaimCategoriesMaster)) {
            return $this->sendError(trans('custom.expense_claim_categories_master_not_found'));
        }

        $expenseClaimCategoriesMaster = $this->expenseClaimCategoriesMasterRepository->update($input, $id);

        return $this->sendResponse($expenseClaimCategoriesMaster->toArray(), trans('custom.expenseclaimcategoriesmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/expenseClaimCategoriesMasters/{id}",
     *      summary="Remove the specified ExpenseClaimCategoriesMaster from storage",
     *      tags={"ExpenseClaimCategoriesMaster"},
     *      description="Delete ExpenseClaimCategoriesMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaimCategoriesMaster",
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
        /** @var ExpenseClaimCategoriesMaster $expenseClaimCategoriesMaster */
        $expenseClaimCategoriesMaster = $this->expenseClaimCategoriesMasterRepository->findWithoutFail($id);

        if (empty($expenseClaimCategoriesMaster)) {
            return $this->sendError(trans('custom.expense_claim_categories_master_not_found'));
        }

        $expenseClaimCategoriesMaster->delete();

        return $this->sendSuccess('Expense Claim Categories Master deleted successfully');
    }
}
