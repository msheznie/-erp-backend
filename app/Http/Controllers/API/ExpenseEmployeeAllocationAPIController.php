<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateExpenseEmployeeAllocationAPIRequest;
use App\Http\Requests\API\UpdateExpenseEmployeeAllocationAPIRequest;
use App\Models\ExpenseAssetAllocation;
use App\Models\ExpenseEmployeeAllocation;
use App\Models\DirectPaymentDetails;
use App\Models\DirectInvoiceDetails;
use App\Models\ItemIssueDetails;
use App\Repositories\ExpenseEmployeeAllocationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Carbon\Carbon;

/**
 * Class ExpenseEmployeeAllocationController
 * @package App\Http\Controllers\API
 */

class ExpenseEmployeeAllocationAPIController extends AppBaseController
{
    /** @var  ExpenseEmployeeAllocationRepository */
    private $expenseEmployeeAllocationRepository;

    public function __construct(ExpenseEmployeeAllocationRepository $expenseEmployeeAllocationRepo)
    {
        $this->expenseEmployeeAllocationRepository = $expenseEmployeeAllocationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseEmployeeAllocations",
     *      summary="Get a listing of the ExpenseEmployeeAllocations.",
     *      tags={"ExpenseEmployeeAllocation"},
     *      description="Get all ExpenseEmployeeAllocations",
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
     *                  @SWG\Items(ref="#/definitions/ExpenseEmployeeAllocation")
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
        $this->expenseEmployeeAllocationRepository->pushCriteria(new RequestCriteria($request));
        $this->expenseEmployeeAllocationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $expenseEmployeeAllocations = $this->expenseEmployeeAllocationRepository->all();

        return $this->sendResponse($expenseEmployeeAllocations->toArray(), 'Expense Employee Allocations retrieved successfully');
    }

    /**
     * @param CreateExpenseEmployeeAllocationAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/expenseEmployeeAllocations",
     *      summary="Store a newly created ExpenseEmployeeAllocation in storage",
     *      tags={"ExpenseEmployeeAllocation"},
     *      description="Store ExpenseEmployeeAllocation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseEmployeeAllocation that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseEmployeeAllocation")
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
     *                  ref="#/definitions/ExpenseEmployeeAllocation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateExpenseEmployeeAllocationAPIRequest $request)
    {
        $input = $request->all();

        if (!isset($input['documentSystemID'])) {
            return $this->sendError("Document system ID not found");
        }

        $checkForAssetDuplicate = ExpenseEmployeeAllocation::where('documentDetailID', $input['documentDetailID'])
                                                  ->where('documentSystemID', $input['documentSystemID'])
                                                  ->where('employeeSystemID', $input['employeeSystemID'])
                                                  ->first();

        if ($checkForAssetDuplicate) {
            return $this->sendError("This employee is alreday allocated", 500);
        }

        $detailTotal = 0;
        $companySystemID = 0;
        $transactionCurrencyID = 0;
        if ($input['documentSystemID'] == 11) {
            $directDetail = DirectInvoiceDetails::with(['supplier_invoice_master','chartofaccount'])->find($input['documentDetailID']);

            if (!$directDetail) {
                return $this->sendError("Supplier invoice detail not found");
            }
            if($directDetail->chartofaccount->controlAccountsSystemID == 2)
            {
                $detailTotal = $directDetail->netAmount;
            }
            else
            {
                $detailTotal = $directDetail->netAmount + $directDetail->VATAmount;
                $input['dateOfDeduction'] = Carbon::parse($input['dateOfDeduction']);
            }

            $input['chartOfAccountSystemID'] = $directDetail->chartOfAccountSystemID;
            $companySystemID = isset($directDetail->supplier_invoice_master->companySystemID) ? $directDetail->supplier_invoice_master->companySystemID : null;
            $transactionCurrencyID = isset($directDetail->supplier_invoice_master->supplierTransactionCurrencyID) ? $directDetail->supplier_invoice_master->supplierTransactionCurrencyID : null;

            $currencyConversion = \Helper::currencyConversion($companySystemID, $transactionCurrencyID, $transactionCurrencyID, $input['amount']);

            $input['amountRpt'] = $currencyConversion['reportingAmount'];
            $input['amountLocal'] = $currencyConversion['localAmount'];
          
        } 
        else if($input['documentSystemID'] == 4) {
            $directDetail = DirectPaymentDetails::with(['master'])->find($input['documentDetailID']);
            
            if (!$directDetail) {
                return $this->sendError("Payment voucher detail not found");
            }

            $detailTotal = $directDetail->DPAmount;
            $input['chartOfAccountSystemID'] = $directDetail->chartOfAccountSystemID;
            $companySystemID = isset($directDetail->master->companySystemID) ? $directDetail->master->companySystemID : null;
            $transactionCurrencyID = isset($directDetail->master->supplierTransCurrencyID) ? $directDetail->master->supplierTransCurrencyID : null;

            $currencyConversion = \Helper::currencyConversion($companySystemID, $transactionCurrencyID, $transactionCurrencyID, $input['amount']);

            $input['amountRpt'] = $currencyConversion['reportingAmount'];
            $input['amountLocal'] = $currencyConversion['localAmount'];
            $input['dateOfDeduction'] = Carbon::parse($input['dateOfDeduction']);
        }
        else if($input['documentSystemID'] == 8) {

            $meterialissue = ItemIssueDetails::with(['master'])->find($input['documentDetailID']);
            if (!$meterialissue) {
                return $this->sendError("Meterial issues detail not found");
            }
            $detailTotal = $meterialissue->issueCostRptTotal;
            $input['chartOfAccountSystemID'] = $meterialissue->financeGLcodePLSystemID;
            $companySystemID = isset($meterialissue->master->companySystemID) ? $meterialissue->master->companySystemID : null;
            $issueDate = isset($meterialissue->master->issueDate) ? $meterialissue->master->issueDate : null;
            //$transactionCurrencyID = isset($meterialissue->localCurrencyID) ? $meterialissue->localCurrencyID : null;

            if(isset($input['assignedQty'])){
                $detailQtyIssuedTotal = $meterialissue->qtyIssued;
                $costPerQty = $meterialissue->issueCostRpt;
                $input['amount'] = $costPerQty * $input['assignedQty'];

                $allocatedQtySum = ExpenseEmployeeAllocation::where('documentDetailID', $input['documentDetailID'])
                    ->where('documentSystemID', $input['documentSystemID'])
                    ->sum('assignedQty');

                $newQtyTotal = $allocatedQtySum + $input['assignedQty'];


                if (($newQtyTotal - $detailQtyIssuedTotal) > 0) {
                    return $this->sendError("Assigned qty cannot be greater than detail qty.");
                }
            }

            $input['amountRpt'] = $input['amount'];
            $input['dateOfDeduction'] = $issueDate;

            if ($meterialissue->issueCostRptTotal == 0) {
                return $this->sendError("Total Value cannot be zero.");
            }

            if(is_numeric($input['amount']) != 1){
                return $this->sendError("Please enter a numeric value to the amount field.");
            }

            $input['amountLocal'] = ($meterialissue->issueCostLocalTotal/$meterialissue->issueCostRptTotal)*$input['amount'];


        }
        
        $allocatedSum = ExpenseEmployeeAllocation::where('documentDetailID', $input['documentDetailID'])
                                                  ->where('documentSystemID', $input['documentSystemID'])
                                                  ->sum('amount');

        $newTotal = $allocatedSum + floatval($input['amount']);


        if ($newTotal > $detailTotal) {
            return $this->sendError("Allocated amount cannot be greater than detail amount.");
        }

        $expenseEmployeeAllocation = $this->expenseEmployeeAllocationRepository->create($input);

        return $this->sendResponse($expenseEmployeeAllocation->toArray(), 'Expense Employee Allocation saved successfully');
    }

    public function getAllocatedEmployeesForExpense(Request $request)
    {
        $input = $request->all();

        $allocatedEmployees = ExpenseEmployeeAllocation::where('documentDetailID', $input['documentDetailID'])
                                                  ->where('documentSystemID', $input['documentSystemID'])
                                                  ->with(['employee'])
                                                  ->get();

        return $this->sendResponse($allocatedEmployees, 'Data retrieved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseEmployeeAllocations/{id}",
     *      summary="Display the specified ExpenseEmployeeAllocation",
     *      tags={"ExpenseEmployeeAllocation"},
     *      description="Get ExpenseEmployeeAllocation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseEmployeeAllocation",
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
     *                  ref="#/definitions/ExpenseEmployeeAllocation"
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
        /** @var ExpenseEmployeeAllocation $expenseEmployeeAllocation */
        $expenseEmployeeAllocation = $this->expenseEmployeeAllocationRepository->findWithoutFail($id);

        if (empty($expenseEmployeeAllocation)) {
            return $this->sendError('Expense Employee Allocation not found');
        }

        return $this->sendResponse($expenseEmployeeAllocation->toArray(), 'Expense Employee Allocation retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateExpenseEmployeeAllocationAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/expenseEmployeeAllocations/{id}",
     *      summary="Update the specified ExpenseEmployeeAllocation in storage",
     *      tags={"ExpenseEmployeeAllocation"},
     *      description="Update ExpenseEmployeeAllocation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseEmployeeAllocation",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseEmployeeAllocation that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseEmployeeAllocation")
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
     *                  ref="#/definitions/ExpenseEmployeeAllocation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateExpenseEmployeeAllocationAPIRequest $request)
    {
        $input = $request->all();

        /** @var ExpenseEmployeeAllocation $expenseEmployeeAllocation */
        $expenseEmployeeAllocation = $this->expenseEmployeeAllocationRepository->findWithoutFail($id);

        if (empty($expenseEmployeeAllocation)) {
            return $this->sendError('Expense Employee Allocation not found');
        }

        $expenseEmployeeAllocation = $this->expenseEmployeeAllocationRepository->update($input, $id);

        return $this->sendResponse($expenseEmployeeAllocation->toArray(), 'ExpenseEmployeeAllocation updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/expenseEmployeeAllocations/{id}",
     *      summary="Remove the specified ExpenseEmployeeAllocation from storage",
     *      tags={"ExpenseEmployeeAllocation"},
     *      description="Delete ExpenseEmployeeAllocation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseEmployeeAllocation",
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
        /** @var ExpenseEmployeeAllocation $expenseEmployeeAllocation */
        $expenseEmployeeAllocation = $this->expenseEmployeeAllocationRepository->findWithoutFail($id);

        if (empty($expenseEmployeeAllocation)) {
            return $this->sendError('Expense Employee Allocation not found');
        }

        $expenseEmployeeAllocation->delete();

        return $this->sendResponse([], 'Expense Employee Allocation deleted successfully');
    }

    public function getEmployeeRecentAllocation(Request $request) {

        $request->validate([
            'employeeSystemID' => 'required',
            'documentSystemID' => 'required',
            'itemCodeSystem' => 'required'
        ]);

        $input = $request->all();

        $data = DB::table('expense_employee_allocation')
            ->join('erp_itemissuedetails', 'expense_employee_allocation.documentDetailID', '=', 'erp_itemissuedetails.itemIssueDetailID')
            ->where('expense_employee_allocation.employeeSystemID', $input['employeeSystemID'])
            ->where('expense_employee_allocation.documentSystemID', $input['documentSystemID'])
            ->where('erp_itemissuedetails.itemCodeSystem', $input['itemCodeSystem'])
            ->where('expense_employee_allocation.documentSystemCode', '!=', $input['documentSystemCode'])
            ->select('expense_employee_allocation.created_at', 'expense_employee_allocation.assignedQty', 'erp_itemissuedetails.itemIssueCode')
            ->orderBy('erp_itemissuedetails.itemIssueAutoID', 'desc')
            ->take(3)
            ->get();

        return $this->sendResponse($data, 'Data retrieved successfully');
    }
}
