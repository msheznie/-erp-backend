<?php
/**
 * =============================================
 * -- File Name : ExpenseClaimCategoriesAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Expense Claim
 * -- Author : Mohamed Fayas
 * -- Create date : 10 - September 2018
 * -- Description : This file contains the all CRUD for Expense Claim Categories
 * -- REVISION HISTORY
 * -- Date: 10- September 2018 By: Fayas Description: Added new function
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateExpenseClaimCategoriesAPIRequest;
use App\Http\Requests\API\UpdateExpenseClaimCategoriesAPIRequest;
use App\Models\ExpenseClaimCategories;
use App\Repositories\ExpenseClaimCategoriesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ExpenseClaimCategoriesController
 * @package App\Http\Controllers\API
 */

class ExpenseClaimCategoriesAPIController extends AppBaseController
{
    /** @var  ExpenseClaimCategoriesRepository */
    private $expenseClaimCategoriesRepository;

    public function __construct(ExpenseClaimCategoriesRepository $expenseClaimCategoriesRepo)
    {
        $this->expenseClaimCategoriesRepository = $expenseClaimCategoriesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseClaimCategories",
     *      summary="Get a listing of the ExpenseClaimCategories.",
     *      tags={"ExpenseClaimCategories"},
     *      description="Get all ExpenseClaimCategories",
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
     *                  @SWG\Items(ref="#/definitions/ExpenseClaimCategories")
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
        $this->expenseClaimCategoriesRepository->pushCriteria(new RequestCriteria($request));
        $this->expenseClaimCategoriesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $expenseClaimCategories = $this->expenseClaimCategoriesRepository->all();

        return $this->sendResponse($expenseClaimCategories->toArray(), trans('custom.expense_claim_categories_retrieved_successfully'));
    }

    /**
     * @param CreateExpenseClaimCategoriesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/expenseClaimCategories",
     *      summary="Store a newly created ExpenseClaimCategories in storage",
     *      tags={"ExpenseClaimCategories"},
     *      description="Store ExpenseClaimCategories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseClaimCategories that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseClaimCategories")
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
     *                  ref="#/definitions/ExpenseClaimCategories"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateExpenseClaimCategoriesAPIRequest $request)
    {
        $input = $request->all();

        $expenseClaimCategories = $this->expenseClaimCategoriesRepository->create($input);

        return $this->sendResponse($expenseClaimCategories->toArray(), trans('custom.expense_claim_categories_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseClaimCategories/{id}",
     *      summary="Display the specified ExpenseClaimCategories",
     *      tags={"ExpenseClaimCategories"},
     *      description="Get ExpenseClaimCategories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaimCategories",
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
     *                  ref="#/definitions/ExpenseClaimCategories"
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
        /** @var ExpenseClaimCategories $expenseClaimCategories */
        $expenseClaimCategories = $this->expenseClaimCategoriesRepository->findWithoutFail($id);

        if (empty($expenseClaimCategories)) {
            return $this->sendError(trans('custom.expense_claim_categories_not_found'));
        }

        return $this->sendResponse($expenseClaimCategories->toArray(), trans('custom.expense_claim_categories_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateExpenseClaimCategoriesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/expenseClaimCategories/{id}",
     *      summary="Update the specified ExpenseClaimCategories in storage",
     *      tags={"ExpenseClaimCategories"},
     *      description="Update ExpenseClaimCategories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaimCategories",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseClaimCategories that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseClaimCategories")
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
     *                  ref="#/definitions/ExpenseClaimCategories"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateExpenseClaimCategoriesAPIRequest $request)
    {
        $input = $request->all();

        /** @var ExpenseClaimCategories $expenseClaimCategories */
        $expenseClaimCategories = $this->expenseClaimCategoriesRepository->findWithoutFail($id);

        if (empty($expenseClaimCategories)) {
            return $this->sendError(trans('custom.expense_claim_categories_not_found'));
        }

        $expenseClaimCategories = $this->expenseClaimCategoriesRepository->update($input, $id);

        return $this->sendResponse($expenseClaimCategories->toArray(), trans('custom.expenseclaimcategories_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/expenseClaimCategories/{id}",
     *      summary="Remove the specified ExpenseClaimCategories from storage",
     *      tags={"ExpenseClaimCategories"},
     *      description="Delete ExpenseClaimCategories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaimCategories",
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
        /** @var ExpenseClaimCategories $expenseClaimCategories */
        $expenseClaimCategories = $this->expenseClaimCategoriesRepository->findWithoutFail($id);

        if (empty($expenseClaimCategories)) {
            return $this->sendError(trans('custom.expense_claim_categories_not_found'));
        }

        $expenseClaimCategories->delete();

        return $this->sendResponse($id, trans('custom.expense_claim_categories_deleted_successfully'));
    }
}
