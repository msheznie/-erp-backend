<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateExpenseAssetAllocationAPIRequest;
use App\Http\Requests\API\UpdateExpenseAssetAllocationAPIRequest;
use App\Models\ItemAssigned;
use App\Models\DirectInvoiceDetails;
use App\Models\DirectPaymentDetails;
use App\Models\ExpenseAssetAllocation;
use App\Models\FixedAssetMaster;
use App\Models\ItemIssueDetails;
use App\Repositories\ExpenseAssetAllocationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\GRVDetails;
use App\Models\FinanceItemCategorySub;
use App\Models\JvDetail;
/**
 * Class ExpenseAssetAllocationController
 * @package App\Http\Controllers\API
 */

class ExpenseAssetAllocationAPIController extends AppBaseController
{
    /** @var  ExpenseAssetAllocationRepository */
    private $expenseAssetAllocationRepository;

    public function __construct(ExpenseAssetAllocationRepository $expenseAssetAllocationRepo)
    {
        $this->expenseAssetAllocationRepository = $expenseAssetAllocationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseAssetAllocations",
     *      summary="Get a listing of the ExpenseAssetAllocations.",
     *      tags={"ExpenseAssetAllocation"},
     *      description="Get all ExpenseAssetAllocations",
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
     *                  @SWG\Items(ref="#/definitions/ExpenseAssetAllocation")
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
        $this->expenseAssetAllocationRepository->pushCriteria(new RequestCriteria($request));
        $this->expenseAssetAllocationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $expenseAssetAllocations = $this->expenseAssetAllocationRepository->all();

        return $this->sendResponse($expenseAssetAllocations->toArray(), 'Expense Asset Allocations retrieved successfully');
    }

    /**
     * @param CreateExpenseAssetAllocationAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/expenseAssetAllocations",
     *      summary="Store a newly created ExpenseAssetAllocation in storage",
     *      tags={"ExpenseAssetAllocation"},
     *      description="Store ExpenseAssetAllocation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseAssetAllocation that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseAssetAllocation")
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
     *                  ref="#/definitions/ExpenseAssetAllocation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $input = $request->all();

        if (!isset($input['documentSystemID'])) {
            return $this->sendError("Document system ID not found");
        }
        
       
        $checkForAssetDuplicate = ExpenseAssetAllocation::where('documentDetailID', $input['documentDetailID'])
                                                  ->where('documentSystemID', $input['documentSystemID'])
                                                  ->where('assetID', $input['assetID'])
                                                  ->first();
                                                      

        if ($checkForAssetDuplicate) {
            return $this->sendError("This asset alreday allocated", 500);
        }

        $detailTotal = 0;
        $companySystemID = 0;
        $transactionCurrencyID = 0;
        if ($input['documentSystemID'] == 11) {
            $directDetail = DirectInvoiceDetails::with(['supplier_invoice_master'])->find($input['documentDetailID']);

            if (!$directDetail) {
                return $this->sendError("Supplier invoice detail not found");
            }

            $detailTotal = $directDetail->netAmount;

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
        }
        else if($input['documentSystemID'] == 3) {
            $grvDetail = GRVDetails::with(['master'])->find($input['documentDetailID']);
            
            if (!$grvDetail) {
                return $this->sendError("GRV detail not found");
            }

            if(isset($grvDetail->itemFinanceCategorySubID))
            {
                $subCat = FinanceItemCategorySub::find($grvDetail->itemFinanceCategorySubID);
                $chart_of_account = $subCat->financeGLcodePLSystemID;
            }
            else
            {
                $chart_of_account = null;
            }
            
            $detailTotal = $grvDetail->netAmount;
            $input['chartOfAccountSystemID'] = $chart_of_account;
            $companySystemID = isset($grvDetail->master->companySystemID) ? $grvDetail->master->companySystemID : null;

            $transactionCurrencyID = isset($grvDetail->master->supplierTransactionCurrencyID) ? $grvDetail->master->supplierTransactionCurrencyID : null;
            $currencyConversion = \Helper::currencyConversion($companySystemID, $transactionCurrencyID, $transactionCurrencyID, $input['amount']);
        
            $input['amountRpt'] = $currencyConversion['reportingAmount'];
            $input['amountLocal'] = $currencyConversion['localAmount'];
        }
        else if($input['documentSystemID'] == 17) {
            $jvDetail = JvDetail::with(['master'])->find($input['documentDetailID']);

            if($jvDetail->creditAmount == 0)
            {
                $detailTotal = $jvDetail->debitAmount;
            }
            else
            {
                $detailTotal = $jvDetail->creditAmount;

           
            }

            if (!$jvDetail) {
                return $this->sendError("GRV detail not found");
            }

            
            
            $input['chartOfAccountSystemID'] = $jvDetail->chartOfAccountSystemID;
            $companySystemID = isset($jvDetail->master->companySystemID) ? $jvDetail->master->companySystemID : null;

            $transactionCurrencyID = isset($jvDetail->master->currencyID) ? $jvDetail->master->currencyID : null;
            $currencyConversion = \Helper::currencyConversion($companySystemID, $transactionCurrencyID, $transactionCurrencyID, $input['amount']);
        
            $input['amountRpt'] = $currencyConversion['reportingAmount'];
            $input['amountLocal'] = $currencyConversion['localAmount'];
        }
        else
        {
                $meterialissue = ItemIssueDetails::with(['master'])->find($input['documentDetailID']); 
                if (!$meterialissue) {
                    return $this->sendError("Meterial issues detail not found");
                }
                $detailTotal = round($meterialissue->issueCostRptTotal,2);
                $input['chartOfAccountSystemID'] = $meterialissue->financeGLcodePLSystemID;
                $companySystemID = isset($meterialissue->master->companySystemID) ? $meterialissue->master->companySystemID : null;
                //$transactionCurrencyID = isset($meterialissue->localCurrencyID) ? $meterialissue->localCurrencyID : null;

                if(isset($input['allocation_qty'])){
                    $detailQtyIssuedTotal = $meterialissue->qtyIssued;
                    $costPerQty = $meterialissue->issueCostRpt;
                    $input['amount'] = $costPerQty * $input['allocation_qty'];

                    $allocatedQtySum = ExpenseAssetAllocation::where('documentDetailID', $input['documentDetailID'])
                                                            ->where('documentSystemID', $input['documentSystemID'])
                                                            ->sum('allocation_qty');

                    $newQtyTotal = $allocatedQtySum + $input['allocation_qty'];


                    if (($newQtyTotal - $detailQtyIssuedTotal) > 0) {
                        return $this->sendError("Allocated qty cannot be greater than detail qty.");
                    }
                }
                
                $input['amountRpt'] = $input['amount'];

                if ($meterialissue->issueCostRptTotal == 0) {
                    return $this->sendError("Total Value cannot be zero.");
                }

                if(is_numeric($input['amount']) != 1){
                    return $this->sendError("Please enter a numeric value to the amount field.");
                }

                $input['amountLocal'] = ($meterialissue->issueCostLocalTotal/$meterialissue->issueCostRptTotal)*$input['amount'];
              
            
        }

       

        
        $allocatedSum = ExpenseAssetAllocation::where('documentDetailID', $input['documentDetailID'])
                                                  ->where('documentSystemID', $input['documentSystemID'])
                                                  ->sum('amount');

                                                 


        $newTotal = $allocatedSum + floatval($input['amount']);
        if($input['documentSystemID'] == 8){
            $newTotal = round($newTotal,2);

            if (($newTotal - $detailTotal) > 0.01) {
                return $this->sendError("Allocated amount cannot be greater than detail amount.");
            }
        }else{
            if (($newTotal - $detailTotal) > 0.00001) {
                return $this->sendError("Allocated amount cannot be greater than detail amount.");
            }
        }

        $expenseAssetAllocation = $this->expenseAssetAllocationRepository->create($input);

        return $this->sendResponse($expenseAssetAllocation->toArray(), 'Expense Asset Allocation saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseAssetAllocations/{id}",
     *      summary="Display the specified ExpenseAssetAllocation",
     *      tags={"ExpenseAssetAllocation"},
     *      description="Get ExpenseAssetAllocation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseAssetAllocation",
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
     *                  ref="#/definitions/ExpenseAssetAllocation"
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
        /** @var ExpenseAssetAllocation $expenseAssetAllocation */
        $expenseAssetAllocation = $this->expenseAssetAllocationRepository->findWithoutFail($id);

        if (empty($expenseAssetAllocation)) {
            return $this->sendError('Expense Asset Allocation not found');
        }

        return $this->sendResponse($expenseAssetAllocation->toArray(), 'Expense Asset Allocation retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateExpenseAssetAllocationAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/expenseAssetAllocations/{id}",
     *      summary="Update the specified ExpenseAssetAllocation in storage",
     *      tags={"ExpenseAssetAllocation"},
     *      description="Update ExpenseAssetAllocation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseAssetAllocation",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseAssetAllocation that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseAssetAllocation")
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
     *                  ref="#/definitions/ExpenseAssetAllocation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateExpenseAssetAllocationAPIRequest $request)
    {
        $input = $request->all();

        /** @var ExpenseAssetAllocation $expenseAssetAllocation */
        $expenseAssetAllocation = $this->expenseAssetAllocationRepository->findWithoutFail($id);

        if (empty($expenseAssetAllocation)) {
            return $this->sendError('Expense Asset Allocation not found');
        }

        $expenseAssetAllocation = $this->expenseAssetAllocationRepository->update($input, $id);

        return $this->sendResponse($expenseAssetAllocation->toArray(), 'ExpenseAssetAllocation updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/expenseAssetAllocations/{id}",
     *      summary="Remove the specified ExpenseAssetAllocation from storage",
     *      tags={"ExpenseAssetAllocation"},
     *      description="Delete ExpenseAssetAllocation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseAssetAllocation",
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
        /** @var ExpenseAssetAllocation $expenseAssetAllocation */
        $expenseAssetAllocation = $this->expenseAssetAllocationRepository->findWithoutFail($id);

        if (empty($expenseAssetAllocation)) {
            return $this->sendError('Expense Asset Allocation not found');
        }

        $expenseAssetAllocation->delete();

        return $this->sendResponse([], 'Expense Asset Allocation deleted successfully');
    }

     public function getCompanyAsset(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companyId'];


        $items = FixedAssetMaster::where('approved',-1)->where('confirmedYN',1)->where('companySystemID',$companyId);                    

        if (array_key_exists('search', $input)) {

            $search = $input['search'];

            $items = $items->where(function ($query) use ($search) {
                $query->where('faCode', 'LIKE', "%{$search}%")
                    ->orWhere('assetDescription', 'LIKE', "%{$search}%");
            });
        }

        $items = $items
            ->take(20)
            ->get();

        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');
    } 

    public function getAllocatedAssetsForExpense(Request $request)
    {
        $input = $request->all();

        $allocatedAsssets = ExpenseAssetAllocation::where('documentDetailID', $input['documentDetailID'])
                                                  ->where('documentSystemID', $input['documentSystemID'])
                                                  ->with(['asset'])
                                                  ->get();

        return $this->sendResponse($allocatedAsssets, 'Data retrieved successfully');
    }

    public function checkAssetAllocation(Request $request)
    {
       
        $input = $request->all();

        $checkForAsset = ExpenseAssetAllocation::where('assetID', $input['asset_id']);

     

        $data['details'] = null;
        if($checkForAsset->count()>0)
        {
            // $assets = $checkForAsset
            //           ->when(('documentSystemID') == 3,function($query){
            //             return $query->with('grv');
            //           })->get();

            $assets = $checkForAsset->get();

            foreach($assets as $key=>$val)
            {
                $document_id = $val->documentSystemID;
                switch ($document_id) {
                    case 3:
                        $checkForAssetss = ExpenseAssetAllocation::where('assetID', $val->assetID)->where('documentSystemID', 3)->where('documentSystemCode', $val->documentSystemCode)->with('grv')->first();
                        $assets[$key]['code'] = $checkForAssetss->grv->grvPrimaryCode;
                        $assets[$key]['doc'] = 'GRV';
                        break;
                    case 17:
                        $checkForAssetss = ExpenseAssetAllocation::where('assetID', $val->assetID)->where('documentSystemID', 17)->where('documentSystemCode', $val->documentSystemCode)->with('journal_voucher')->first();
                        $assets[$key]['code'] = $checkForAssetss->journal_voucher->JVcode;
                        $assets[$key]['doc'] = 'JV';
                        break; 
                    case 8:
                        $checkForAssetss = ExpenseAssetAllocation::where('assetID', $val->assetID)->where('documentSystemID', 8)->where('documentSystemCode', $val->documentSystemCode)->with('meterial_issue')->first();
                        $assets[$key]['code'] = $checkForAssetss->meterial_issue->itemIssueCode;
                        $assets[$key]['doc'] = 'Material Issue';
                        break;   
                    case 4:
                        $checkForAssetss = ExpenseAssetAllocation::where('assetID', $val->assetID)->where('documentSystemID', 4)->where('documentSystemCode', $val->documentSystemCode)->with('payment_voucher')->first();
                        $assets[$key]['code'] = $checkForAssetss->payment_voucher->BPVcode;
                        $assets[$key]['doc'] = 'Payment Voucher';
                        break; 
                    case 11:
                        $checkForAssetss = ExpenseAssetAllocation::where('assetID', $val->assetID)->where('documentSystemID', 11)->where('documentSystemCode', $val->documentSystemCode)->with('supplier_invoice')->first();
                        $assets[$key]['code'] = $checkForAssetss->supplier_invoice->bookingInvCode;
                        $assets[$key]['doc'] = 'Booking Supplier Invoice';
                        break;    
                }

            }

            $data['exit'] = true;
            $data['details'] = $assets;
        }
        else
        {
            $data['exit'] = false;
        }

        return $this->sendResponse($data, 'Data retrieved successfully');
    }

    public function validateDirectItemWithAssetExpense(Request $request)
    {
        $input = $request->all();
        $item = $input['item'];
        $validationSuccess = true;
        $allocatedAsssets = ExpenseAssetAllocation::where('documentDetailID', $input['documentDetailID'])
                                                  ->where('documentSystemID', $input['documentSystemID'])
                                                  ->with(['asset'])
                                                  ->get();
        
        if(empty($allocatedAsssets)) {
            $validationSuccess = true;
        }else {
            $total = 0;
            foreach($allocatedAsssets as $allocatedAssset) {
                $total += $allocatedAssset->amount;
                if($item['netAmount'] < $total) {
                    return $this->sendError("Detail amount cannot be less than allocated amount.");
                }
             }
        }

        return $this->sendResponse($validationSuccess, 'Data retrieved successfully');
    }
}
