<?php
/**
 * =============================================
 * -- File Name : ExpenseClaimTypeAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Expense Claim
 * -- Author : Mohamed Fayas
 * -- Create date : 10 - September 2018
 * -- Description : This file contains the all CRUD for Expense Claim Type
 * -- REVISION HISTORY
 * -- Date: 10- September 2018 By: Fayas Description: Added new function
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateExpenseClaimTypeAPIRequest;
use App\Http\Requests\API\UpdateExpenseClaimTypeAPIRequest;
use App\Models\ExpenseClaimType;
use App\Repositories\ExpenseClaimTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ExpenseClaimTypeController
 * @package App\Http\Controllers\API
 */

class ExpenseClaimTypeAPIController extends AppBaseController
{
    /** @var  ExpenseClaimTypeRepository */
    private $expenseClaimTypeRepository;

    public function __construct(ExpenseClaimTypeRepository $expenseClaimTypeRepo)
    {
        $this->expenseClaimTypeRepository = $expenseClaimTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseClaimTypes",
     *      summary="Get a listing of the ExpenseClaimTypes.",
     *      tags={"ExpenseClaimType"},
     *      description="Get all ExpenseClaimTypes",
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
     *                  @SWG\Items(ref="#/definitions/ExpenseClaimType")
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
        $this->expenseClaimTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->expenseClaimTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $expenseClaimTypes = $this->expenseClaimTypeRepository->all();

        return $this->sendResponse($expenseClaimTypes->toArray(), trans('custom.expense_claim_types_retrieved_successfully'));
    }

    /**
     * @param CreateExpenseClaimTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/expenseClaimTypes",
     *      summary="Store a newly created ExpenseClaimType in storage",
     *      tags={"ExpenseClaimType"},
     *      description="Store ExpenseClaimType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseClaimType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseClaimType")
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
     *                  ref="#/definitions/ExpenseClaimType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateExpenseClaimTypeAPIRequest $request)
    {
        $input = $request->all();

        $expenseClaimTypes = $this->expenseClaimTypeRepository->create($input);

        return $this->sendResponse($expenseClaimTypes->toArray(), trans('custom.expense_claim_type_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseClaimTypes/{id}",
     *      summary="Display the specified ExpenseClaimType",
     *      tags={"ExpenseClaimType"},
     *      description="Get ExpenseClaimType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaimType",
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
     *                  ref="#/definitions/ExpenseClaimType"
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
        /** @var ExpenseClaimType $expenseClaimType */
        $expenseClaimType = $this->expenseClaimTypeRepository->findWithoutFail($id);

        if (empty($expenseClaimType)) {
            return $this->sendError(trans('custom.expense_claim_type_not_found'));
        }

        return $this->sendResponse($expenseClaimType->toArray(), trans('custom.expense_claim_type_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateExpenseClaimTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/expenseClaimTypes/{id}",
     *      summary="Update the specified ExpenseClaimType in storage",
     *      tags={"ExpenseClaimType"},
     *      description="Update ExpenseClaimType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaimType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseClaimType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseClaimType")
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
     *                  ref="#/definitions/ExpenseClaimType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateExpenseClaimTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var ExpenseClaimType $expenseClaimType */
        $expenseClaimType = $this->expenseClaimTypeRepository->findWithoutFail($id);

        if (empty($expenseClaimType)) {
            return $this->sendError(trans('custom.expense_claim_type_not_found'));
        }

        $expenseClaimType = $this->expenseClaimTypeRepository->update($input, $id);

        return $this->sendResponse($expenseClaimType->toArray(), trans('custom.expenseclaimtype_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/expenseClaimTypes/{id}",
     *      summary="Remove the specified ExpenseClaimType from storage",
     *      tags={"ExpenseClaimType"},
     *      description="Delete ExpenseClaimType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaimType",
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
        /** @var ExpenseClaimType $expenseClaimType */
        $expenseClaimType = $this->expenseClaimTypeRepository->findWithoutFail($id);

        if (empty($expenseClaimType)) {
            return $this->sendError(trans('custom.expense_claim_type_not_found'));
        }

        $expenseClaimType->delete();

        return $this->sendResponse($id, trans('custom.expense_claim_type_deleted_successfully'));
    }
}
