<?php
/**
 * =============================================
 * -- File Name : ExpenseClaimDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Expense Claim
 * -- Author : Mohamed Fayas
 * -- Create date : 10 - September 2018
 * -- Description : This file contains the all CRUD for Expense Claim Details
 * -- REVISION HISTORY
 * -- Date: 10- September 2018 By: Fayas Description: Added new function getDetailsByExpenseClaim()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateExpenseClaimDetailsAPIRequest;
use App\Http\Requests\API\UpdateExpenseClaimDetailsAPIRequest;
use App\Models\ExpenseClaimDetails;
use App\Repositories\ExpenseClaimDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ExpenseClaimDetailsController
 * @package App\Http\Controllers\API
 */

class ExpenseClaimDetailsAPIController extends AppBaseController
{
    /** @var  ExpenseClaimDetailsRepository */
    private $expenseClaimDetailsRepository;

    public function __construct(ExpenseClaimDetailsRepository $expenseClaimDetailsRepo)
    {
        $this->expenseClaimDetailsRepository = $expenseClaimDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseClaimDetails",
     *      summary="Get a listing of the ExpenseClaimDetails.",
     *      tags={"ExpenseClaimDetails"},
     *      description="Get all ExpenseClaimDetails",
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
     *                  @SWG\Items(ref="#/definitions/ExpenseClaimDetails")
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
        $this->expenseClaimDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->expenseClaimDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $expenseClaimDetails = $this->expenseClaimDetailsRepository->all();

        return $this->sendResponse($expenseClaimDetails->toArray(), 'Expense Claim Details retrieved successfully');
    }

    /**
     * @param CreateExpenseClaimDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/expenseClaimDetails",
     *      summary="Store a newly created ExpenseClaimDetails in storage",
     *      tags={"ExpenseClaimDetails"},
     *      description="Store ExpenseClaimDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseClaimDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseClaimDetails")
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
     *                  ref="#/definitions/ExpenseClaimDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateExpenseClaimDetailsAPIRequest $request)
    {
        $input = $request->all();

        $expenseClaimDetails = $this->expenseClaimDetailsRepository->create($input);

        return $this->sendResponse($expenseClaimDetails->toArray(), 'Expense Claim Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseClaimDetails/{id}",
     *      summary="Display the specified ExpenseClaimDetails",
     *      tags={"ExpenseClaimDetails"},
     *      description="Get ExpenseClaimDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaimDetails",
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
     *                  ref="#/definitions/ExpenseClaimDetails"
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
        /** @var ExpenseClaimDetails $expenseClaimDetails */
        $expenseClaimDetails = $this->expenseClaimDetailsRepository->findWithoutFail($id);

        if (empty($expenseClaimDetails)) {
            return $this->sendError('Expense Claim Details not found');
        }

        return $this->sendResponse($expenseClaimDetails->toArray(), 'Expense Claim Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateExpenseClaimDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/expenseClaimDetails/{id}",
     *      summary="Update the specified ExpenseClaimDetails in storage",
     *      tags={"ExpenseClaimDetails"},
     *      description="Update ExpenseClaimDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaimDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseClaimDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseClaimDetails")
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
     *                  ref="#/definitions/ExpenseClaimDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateExpenseClaimDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var ExpenseClaimDetails $expenseClaimDetails */
        $expenseClaimDetails = $this->expenseClaimDetailsRepository->findWithoutFail($id);

        if (empty($expenseClaimDetails)) {
            return $this->sendError('Expense Claim Details not found');
        }

        $expenseClaimDetails = $this->expenseClaimDetailsRepository->update($input, $id);

        return $this->sendResponse($expenseClaimDetails->toArray(), 'ExpenseClaimDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/expenseClaimDetails/{id}",
     *      summary="Remove the specified ExpenseClaimDetails from storage",
     *      tags={"ExpenseClaimDetails"},
     *      description="Delete ExpenseClaimDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaimDetails",
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
        /** @var ExpenseClaimDetails $expenseClaimDetails */
        $expenseClaimDetails = $this->expenseClaimDetailsRepository->findWithoutFail($id);

        if (empty($expenseClaimDetails)) {
            return $this->sendError('Expense Claim Details not found');
        }

        $expenseClaimDetails->delete();

        return $this->sendResponse($id, 'Expense Claim Details deleted successfully');
    }

    public function getDetailsByExpenseClaim(Request $request)
    {
        $input = $request->all();
        $id = $input['expenseClaimMasterAutoID'];

        $items = ExpenseClaimDetails::where('expenseClaimMasterAutoID', $id)
                                ->with(['segment','chart_of_account','currency','category','local_currency'])
                                ->get();

        return $this->sendResponse($items->toArray(), 'Expense Claim Details retrieved successfully');
    }
}
