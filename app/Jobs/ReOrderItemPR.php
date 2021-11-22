<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Repositories\ItemMasterRepository;
use App\Repositories\UserRepository;
use App\Repositories\PurchaseRequestRepository;
use App\Repositories\SegmentAllocatedItemRepository;
use App\Repositories\PurchaseRequestDetailsRepository;
use App\Models\ItemAssigned;
use App\Models\Company;
use App\Models\ItemMaster;
use App\Models\CompanyFinanceYear;
use App\Models\PurchaseRequest;
use App\Models\CompanyDocumentAttachment;
use App\Models\SegmentMaster;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\CompanyPolicyMaster;
use App\Models\ProcumentOrder;
use App\Models\ErpItemLedger;
use App\Models\GRVDetails;
use App\Models\Location;
use App\Models\PurchaseOrderDetails;
use App\helper\Helper;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\NotificationCompanyScenario;
use Illuminate\Support\Facades\Auth;
class ReOrderItemPR implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $itemMasterRepository;
    private $userRepository;
    private $purchaseRequestRepository;
    private $segmentAllocatedItemRepository;
    private $purchaseRequestDetailsRepository;
    private $user;
    public function __construct($user)
    {
        $this->user =$user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(PurchaseRequestDetailsRepository $purchaseRequestDetailsRepository,SegmentAllocatedItemRepository $segmentAllocatedItemRepository,PurchaseRequestRepository $purchaseRequestRepository,UserRepository $userRepository,ItemMasterRepository $itemMasterRepository)
    {


        
   
      
        $companies = Company::where('isActive',true)->pluck('companySystemID');

        
        
        $details = [];
        foreach($companies as $companyID)
        {


        $serivice_line_id = 1;    
        $segments = SegmentMaster::where("companySystemID", $companyID)->where('isActive', 1)->select('serviceLineSystemID','ServiceLineDes')->first();
        if(isset($segments))
        {
            $serivice_line_id =  $segments->serviceLineSystemID;   
        }
        $location = Location::first()->locationID;      
       
 

            $prPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 62)
            ->where('companySystemID', $companyID)
            ->first();


            if ($prPolicy) {
                $policy_pr = $prPolicy->isYesNO;
                if($policy_pr == 1)
                {
                    
                    $records = DB::table("itemassigned")
                    ->selectRaw("itemDescription, itemCodeSystem, rolQuantity, IFNULL(ledger.INoutQty,0) as INoutQty,itemPrimaryCode,secondaryItemCode,roQuantity")
                    ->join(DB::raw('(SELECT itemSystemCode, SUM(inOutQty) as INoutQty FROM erp_itemledger WHERE companySystemID = ' . $companyID . ' GROUP BY itemSystemCode) as ledger'), function ($query) {
                        $query->on('itemassigned.itemCodeSystem', '=', 'ledger.itemSystemCode');
                    })
                    ->where('companySystemID', '=', $companyID)
                    ->where('financeCategoryMaster', '=', 1)
                    ->where('isActive', '=', 1)
                    ->where('roQuantity', '>', 0)
                    ->whereRaw('ledger.INoutQty <= rolQuantity')->get();
        
                    $item_count_obj = count($records);
            
        
                    if($item_count_obj > 0)
                    {
        
        
                        $company = Company::where('companySystemID', $companyID)->first();
        
                        $request_data['PRRequestedDate'] = now();
            
                        $id = Auth::id();
            
                       
                        $user = $userRepository->with(['employee'])->findWithoutFail($id);
                
                        $request_data['createdPcID'] = gethostname();
                        $request_data['createdUserID'] = null;//$user->employee['empID'];
                        $request_data['createdUserSystemID'] = null;//$user->employee['employeeSystemID'];
                        
                        $currency = (isset($company)) ? $company->localCurrencyID : 0;
                        $request_data['currency'] = $currency;
                        $companyFinanceYear = \Helper::companyFinanceYear($companyID);
                                        
                        $budger_year_id = 1;
                        if(count($companyFinanceYear) > 0)
                        {
                            $budger_year_id = $companyFinanceYear[0]->companyFinanceYearID;
                        }           
                        $request_data['budgetYearID'] = $budger_year_id; 
                        $request_data['prBelongsYearID'] = $budger_year_id;  
                        
                        $budget_year = '';
                        $checkCompanyFinanceYear = CompanyFinanceYear::find($budger_year_id);
                        if ($checkCompanyFinanceYear) {
                            $budget_year = Carbon::parse($checkCompanyFinanceYear->bigginingDate)->format('Y');
                            $request_data['budgetYear'] = Carbon::parse($checkCompanyFinanceYear->bigginingDate)->format('Y');
                            $request_data['prBelongsYear'] = Carbon::parse($checkCompanyFinanceYear->bigginingDate)->format('Y');
                        }
            
                        $request_data['documentSystemID'] = 1;  
                        $request_data['companySystemID'] = $companyID;        
                        //$serivice_line_id = 1;
                        $request_data['serviceLineSystemID'] = $serivice_line_id;     
                        $segment = SegmentMaster::where('serviceLineSystemID', $serivice_line_id)->first();
                        if ($segment) {
                            $serivice_line_code = $segment->ServiceLineCode;
                            $request_data['serviceLineCode'] = $segment->ServiceLineCode;
                        }
            
                       // $request_data['serviceLineSystemID'] = 1;  
                        $request_data['comments'] = 'System Auto-Generated - Auto Re-order Purchase';  
                        $request_data['location'] = $location;  
                        $request_data['priority'] = 1;  
            
                        $request_data['departmentID'] = 'PROC';  
                        $doc_id = 'PR';
                        $request_data['documentID'] = $doc_id;
                     
            
                        $lastSerial = PurchaseRequest::where('companySystemID', $companyID)
                        ->where('documentSystemID', 1)
                        ->orderBy('purchaseRequestID', 'desc')
                        ->first();
                   
                        $lastSerialNumber = 1;
                        if ($lastSerial) {
                            $lastSerialNumber = intval($lastSerial->serialNumber) + 1;
                        }
                        
                        $company_id = '';
                        if ($company) {
                            $company_id = $company->CompanyID;
                            $request_data['companyID'] = $company->CompanyID;
                        }
                     
                        $dep_id = 'PROC';
                        $code = str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT);
                        $request_data['purchaseRequestCode'] = $company_id . '\\' . $dep_id . '\\' . $serivice_line_code . '\\' . $doc_id . $code;
                        
                        $request_data['serialNumber'] = $lastSerialNumber;
                         $companyDocumentAttachment = CompanyDocumentAttachment::where('companySystemID', $companyID)
                            ->where('documentSystemID', 1)
                            ->first();
            
                        if ($companyDocumentAttachment) {
                            $request_data['docRefNo'] = $companyDocumentAttachment->docRefNumber;
                        }
                     
                        $new_purchaseRequests = $purchaseRequestRepository->create($request_data);
                        //purchase request end
            
                        // item details echekc start
                        
        
                        $succes_item = 0;
                        $valid_items = [];
            
        
                        foreach($records as $itemVal)
                        {   
                            
                
                            $is_failed= false;
            
                            $allowItemToTypePolicy = false;
                            $itemNotound = false;
                            $companySystemID = $companyID;
                            
            
                      
            
                            $request_data_details['itemCode'] = $itemVal->itemCodeSystem;
                            $item = ItemAssigned::where('itemCodeSystem', $itemVal->itemCodeSystem)
                            ->where('companySystemID', $companySystemID)
                            ->first();
                
            
                      
                            if (empty($item)) {
                                if (!$allowItemToTypePolicy) {
                                    //return $this->sendError('Item not found');
                                    $is_failed= true;
                                   // continue;
                                } else {
                                    $itemNotound = true;
                                }
                            }
            
                       
                            // $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $itemVal->purchaseRequestID)
                            // ->first();
            
                    
            
                            $request_data_details['budgetYear'] = $budget_year;
                            $request_data_details['itemPrimaryCode'] = $itemVal->itemPrimaryCode;
                            $request_data_details['itemDescription'] = $itemVal->itemDescription;
                            $request_data_details['partNumber'] = $itemVal->itemPrimaryCode;
                            // $request_data_details['itemFinanceCategoryID'] = $itemVal->itemFinanceCategoryID;
                            // $request_data_details['itemFinanceCategorySubID'] = $itemVal->itemFinanceCategorySubID;
            
                    
                            //start
                    
                            if (!$itemNotound) {
            
                                
                         
                                $currencyConversion = \Helper::currencyConversion($companySystemID, $item->wacValueLocalCurrencyID, $currency, $item->wacValueLocal);
                                
                              
                         
                                $request_data_details['estimatedCost'] =$currencyConversion['documentAmount'];
                                $request_data_details['companySystemID'] = $item->companySystemID;
                                $request_data_details['companyID'] = $item->companyID;
                                $request_data_details['unitOfMeasure'] = $item->itemUnitOfMeasure;
                                $request_data_details['maxQty'] = $item->maximunQty;
                                $request_data_details['minQty'] = $item->minimumQty;
                                
            
                             
                                $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
                                    ->where('mainItemCategoryID', $item->financeCategoryMaster)
                                    ->where('itemCategorySubID', $item->financeCategorySub)
                                    ->first();
                    
                                if (empty($financeItemCategorySubAssigned)) {
                                    $is_failed= true;
                                    //continue;
                                   // return $this->sendError('Finance category not assigned for the selected item.');
                                }
                          
                                $request_data_details['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                                $request_data_details['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                            
                                if ($item->financeCategoryMaster == 3) {
                                    $assetCategory = AssetFinanceCategory::find($item->faFinanceCatID);
                                    if (!$assetCategory) {
                                        $is_failed= true;
                                       // continue;
                                        //return $this->sendError('Asset category not assigned for the selected item.');
                                    }
                                    $request_data_details['financeGLcodePLSystemID'] = $assetCategory->COSTGLCODESystemID;
                                    $request_data_details['financeGLcodePL'] = $assetCategory->COSTGLCODE;
                                } else {
                                    $request_data_details['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                                    $request_data_details['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                                }
                                
                                $request_data_details['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;
                                
            
                                $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
                                        ->where('companySystemID', $companySystemID)
                                        ->first();
            
                                 
                                if ($allowFinanceCategory) {
                                    $policy = $allowFinanceCategory->isYesNO;
                    
                                    if ($policy == 0) {
                                        if ($new_purchaseRequests->financeCategory == null || $new_purchaseRequests->financeCategory == 0) {
                                            $is_failed= true;
                                            //continue;
                                           // return $this->sendError('Category is not found.', 500);
                                        }
                    
                                        //checking if item category is same or not
                                        $pRDetailExistSameItem = PurchaseRequestDetails::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                                            ->where('purchaseRequestID', $new_purchaseRequests->purchaseRequestID)
                                            ->first();
                    
                                        if ($pRDetailExistSameItem) {
                                            if ($item->financeCategoryMaster != $pRDetailExistSameItem["itemFinanceCategoryID"]) {
                                                $is_failed= true;
                                               // continue;
                                               // return $this->sendError('You cannot add different category item', 500);
                                            }
                                        }
                                    }
                                }
                       
                             
                                  // check policy 18
                    
                                $allowPendingApproval = CompanyPolicyMaster::where('companyPolicyCategoryID', 18)
                                    ->where('companySystemID', $companySystemID)
                                    ->first();
                          
                                if ($allowPendingApproval && $item->financeCategoryMaster == 1) {
                                    
                              
                                    if ($allowPendingApproval->isYesNO == 0) {
                    
                                        $checkWhether = PurchaseRequest::where('purchaseRequestID', '!=', $new_purchaseRequests->purchaseRequestID)
                                            ->where('companySystemID', $companySystemID)
                                            ->where('serviceLineSystemID', $new_purchaseRequests->serviceLineSystemID)
                                            ->select([
                                                'erp_purchaserequest.purchaseRequestID',
                                                'erp_purchaserequest.companySystemID',
                                                'erp_purchaserequest.serviceLineCode',
                                                'erp_purchaserequest.purchaseRequestCode',
                                                'erp_purchaserequest.PRConfirmedYN',
                                                'erp_purchaserequest.approved',
                                                'erp_purchaserequest.cancelledYN'
                                            ])
                                            ->groupBy(
                                                'erp_purchaserequest.purchaseRequestID',
                                                'erp_purchaserequest.companySystemID',
                                                'erp_purchaserequest.serviceLineCode',
                                                'erp_purchaserequest.purchaseRequestCode',
                                                'erp_purchaserequest.PRConfirmedYN',
                                                'erp_purchaserequest.approved',
                                                'erp_purchaserequest.cancelledYN'
                                            );
            
                                     
                                            
                                        $anyPendingApproval = $checkWhether->whereHas('details', function ($query) use ($companySystemID, $new_purchaseRequests, $item) {
                                            $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                                                           ->where('manuallyClosed', 0);
                                      
                                        })
                                            ->where('approved', 0)
                                            ->where('cancelledYN', 0)
                                            ->first();
                                        /* approved=0 And cancelledYN=0*/
            
                                    
                    
                                        if (!empty($anyPendingApproval)) {
                                            $is_failed= true;
                                           // continue;
                                            //return $this->sendError("There is a purchase request (" . $anyPendingApproval->purchaseRequestCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                                        }
                                        
                                 
                                    
                                        $anyApprovedPRButPONotProcessed = PurchaseRequest::where('purchaseRequestID', '!=', $new_purchaseRequests->purchaseRequestID)
                                            ->where('companySystemID', $companySystemID)
                                            ->where('serviceLineSystemID', $new_purchaseRequests->serviceLineSystemID)
                                            ->select([
                                                'erp_purchaserequest.purchaseRequestID',
                                                'erp_purchaserequest.companySystemID',
                                                'erp_purchaserequest.serviceLineCode',
                                                'erp_purchaserequest.purchaseRequestCode',
                                                'erp_purchaserequest.PRConfirmedYN',
                                                'erp_purchaserequest.approved',
                                                'erp_purchaserequest.cancelledYN'
                                            ])
                                            ->groupBy(
                                                'erp_purchaserequest.purchaseRequestID',
                                                'erp_purchaserequest.companySystemID',
                                                'erp_purchaserequest.serviceLineCode',
                                                'erp_purchaserequest.purchaseRequestCode',
                                                'erp_purchaserequest.PRConfirmedYN',
                                                'erp_purchaserequest.approved',
                                                'erp_purchaserequest.cancelledYN'
                                            )
                                            ->whereHas('details', function ($query) use ($companySystemID, $new_purchaseRequests, $item) {
                                                $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                                                    ->where('selectedForPO', 0)
                                                    ->where('prClosedYN', 0)
                                                    ->where('fullyOrdered', 0)
                                                    ->where('manuallyClosed', 0);
                    
                                            })
                                            ->where('approved', -1)
                                            ->where('cancelledYN', 0)
                                            ->first();
                                        /* approved=-1 And cancelledYN=0 And selectedForPO=0 And prClosedYN=0 And fullyOrdered=0*/
                                        // return $this->sendResponse($anyApprovedPRButPONotProcessed, 'successfully export');
                                        // die();
                                        if (!empty($anyApprovedPRButPONotProcessed)) {
                                            $is_failed= true;
                                            //continue;
                                           // return $this->sendError("There is a purchase request (" . $anyApprovedPRButPONotProcessed->purchaseRequestCode . ") approved hense PO is not processed for the item you are trying to add. Please check again", 500);
                                        }
                                     
                                        $anyApprovedPRButPOPartiallyProcessed = PurchaseRequest::where('purchaseRequestID', '!=', $new_purchaseRequests->purchaseRequestID)
                                            ->where('companySystemID', $companySystemID)
                                            ->where('serviceLineSystemID', $new_purchaseRequests->serviceLineSystemID)
                                            ->select([
                                                'erp_purchaserequest.purchaseRequestID',
                                                'erp_purchaserequest.companySystemID',
                                                'erp_purchaserequest.serviceLineCode',
                                                'erp_purchaserequest.purchaseRequestCode',
                                                'erp_purchaserequest.PRConfirmedYN',
                                                'erp_purchaserequest.approved',
                                                'erp_purchaserequest.cancelledYN'
                                            ])
                                            ->groupBy(
                                                'erp_purchaserequest.purchaseRequestID',
                                                'erp_purchaserequest.companySystemID',
                                                'erp_purchaserequest.serviceLineCode',
                                                'erp_purchaserequest.purchaseRequestCode',
                                                'erp_purchaserequest.PRConfirmedYN',
                                                'erp_purchaserequest.approved',
                                                'erp_purchaserequest.cancelledYN'
                                            )->whereHas('details', function ($query) use ($companySystemID, $new_purchaseRequests, $item) {
                                                $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                                                    ->where('selectedForPO', 0)
                                                    ->where('prClosedYN', 0)
                                                    ->where('fullyOrdered', 1)
                                                    ->where('manuallyClosed', 0);
                                            
                                            })
                                            ->where('approved', -1)
                                            ->where('cancelledYN', 0)
                                            ->first();
                                        /* approved=-1 And cancelledYN=0 And selectedForPO=0 And prClosedYN=0 And fullyOrdered=1*/
                    
                                        if (!empty($anyApprovedPRButPOPartiallyProcessed)) {
                                            $is_failed= true;
                                           // continue;
                                           // return $this->sendError("There is a purchase request (" . $anyApprovedPRButPOPartiallyProcessed->purchaseRequestCode . ") approved and PO is partially processed for the item you are trying to add. Please check again", 500);
                                        }
                    
                                        /* PO check*/
                                   
                                        $checkPOPending = ProcumentOrder::where('companySystemID', $companySystemID)
                                            ->where('serviceLineSystemID', $new_purchaseRequests->serviceLineSystemID)
                                            ->whereHas('detail', function ($query) use ($item) {
                                                $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                                                       ->where('manuallyClosed', 0);
                                            })
                                            ->where('approved', 0)
                                            ->where('poCancelledYN', 0)
                                            ->first();
                                         
                                   
                                        if (!empty($checkPOPending)) {
                                            $is_failed= true;
                                           // continue;
                                           // return $this->sendError("There is a purchase order (" . $checkPOPending->purchaseOrderCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                                        }
                                        /* PO --> approved=-1 And cancelledYN=0 */
                                      
                                    }
                                }
                          
                          
                                $group_companies = Helper::getSimilarGroupCompanies($companySystemID);
                                $poQty = PurchaseOrderDetails::whereHas('order', function ($query) use ($group_companies) {
                                    $query->whereIn('companySystemID', $group_companies)
                                        ->where('approved', -1)
                                        ->where('poType_N', '!=',5)// poType_N = 5 =>work order
                                        ->where('poCancelledYN', 0)
                                        ->where('manuallyClosed', 0);
                                     })
                                    ->where('itemCode', $itemVal->itemCodeSystem)
                                    ->where('manuallyClosed',0)
                                    ->groupBy('erp_purchaseorderdetails.itemCode')
                                    ->select(
                                        [
                                            'erp_purchaseorderdetails.companySystemID',
                                            'erp_purchaseorderdetails.itemCode',
                                            'erp_purchaseorderdetails.itemPrimaryCode'
                                        ]
                                    )
                                    ->sum('noQty');
            
                                
                                            
                                $quantityInHand = ErpItemLedger::where('itemSystemCode', $itemVal->itemCodeSystem)
                                    ->where('companySystemID', $companySystemID)
                                    ->groupBy('itemSystemCode')
                                    ->sum('inOutQty');
                    
                                $grvQty = GRVDetails::whereHas('grv_master', function ($query) use ($group_companies) {
                                    $query->whereIn('companySystemID', $group_companies)
                                        ->where('grvTypeID', 2)
                                        ->where('approved', -1)
                                        ->groupBy('erp_grvmaster.companySystemID');
                                })->whereHas('po_detail', function ($query){
                                    $query->where('manuallyClosed',0)
                                    ->whereHas('order', function ($query){
                                        $query->where('manuallyClosed',0);
                                    });
                                })
                                    ->where('itemCode', $itemVal->itemCodeSystem)
                                    ->groupBy('erp_grvdetails.itemCode')
                                    ->select(
                                        [
                                            'erp_grvdetails.companySystemID',
                                            'erp_grvdetails.itemCode'
                                        ])
                                    ->sum('noQty');
                    
                                $quantityOnOrder = $poQty - $grvQty;
                                $request_data_details['poQuantity'] = $poQty;
                                $request_data_details['quantityOnOrder'] = $quantityOnOrder;
                                $request_data_details['quantityInHand'] = $quantityInHand;
                           
                     
                    
                            } else {
                                $request_data_details['estimatedCost'] = 0;
                                $request_data_details['companySystemID'] = $companySystemID;
                                $request_data_details['companyID'] = $new_purchaseRequests->companyID;
                                $request_data_details['unitOfMeasure'] = null;
                                $request_data_details['maxQty'] = 0;
                                $request_data_details['minQty'] = 0;
                                $request_data_details['poQuantity'] = 0;
                                $request_data_details['quantityOnOrder'] = 0;
                                $request_data_details['quantityInHand'] = 0;
                                $request_data_details['itemCode'] = null;
                            }
            
                     
                      
                       
                            $request_data_details['quantityRequested'] =$itemVal->roQuantity;  
                            $request_data_details['totalCost'] = 0;  
                            $request_data_details['comments'] = 'generated pr';  
                            $request_data_details['itemCategoryID'] = 0;
                            $request_data_details['isMRPulled'] = false;
                    
                            if($itemVal->roQuantity <= 0)
                            {
                                $is_failed= true;
                            }    

                            if(!$is_failed)
                            {
                                $succes_item++;
                                array_push($valid_items,$request_data_details);
                               // 
                            }
                           
                      
                      
                        }//end foreach
            
            
            
                       
                
            
                        $segment_success = true;
            
                        
                  
                        if($item_count_obj > 0)
                        {
            
                            if($succes_item == 0)
                            {   
                              
                                $new_purchaseRequests->delete();
            
                              
                                Log::info('Cannot copy this purchase request. Because all the items included in this document are pulled from pending PR/PO documents');
        
            
                                
                            }
                            else
                            {   
                  
                                foreach($valid_items as $valid_item)
                                {
                                  
                                    $valid_item['purchaseRequestID'] = $new_purchaseRequests['purchaseRequestID'];
            
                                    $purchaseRequestDetails = $purchaseRequestDetailsRepository->create($valid_item);
                                                     
                                    $checkAlreadyAllocated = SegmentAllocatedItem::where('serviceLineSystemID', '!=',$new_purchaseRequests->serviceLineSystemID)
                                                                             ->where('documentSystemID', $new_purchaseRequests->documentSystemID)
                                                                             ->where('documentMasterAutoID', $new_purchaseRequests->purchaseRequestID)
                                                                             ->where('documentDetailAutoID', $purchaseRequestDetails->purchaseRequestDetailsID)
                                                                             ->get();
            
            
                                                                                     
                    
                                    if (sizeof($checkAlreadyAllocated) == 0) {
                                        $checkAlreadyAllocated = SegmentAllocatedItem::where('serviceLineSystemID',$new_purchaseRequests->serviceLineSystemID)
                                                                             ->where('documentSystemID', $new_purchaseRequests->documentSystemID)
                                                                             ->where('documentMasterAutoID', $new_purchaseRequests->purchaseRequestID)
                                                                             ->where('documentDetailAutoID', $purchaseRequestDetails->purchaseRequestDetailsID)
                                                                             ->delete();
            
                                                                                     
                    
                                        $allocationData = [
                                            'serviceLineSystemID' => $new_purchaseRequests->serviceLineSystemID,
                                            'documentSystemID' => $new_purchaseRequests->documentSystemID,
                                            'docAutoID' => $new_purchaseRequests->purchaseRequestID,
                                            'docDetailID' => $purchaseRequestDetails->purchaseRequestDetailsID
                                        ];
                                        
            
                                     
                                        $segmentAllocatedItem = $segmentAllocatedItemRepository->allocateSegmentWiseItem($allocationData);
                    
                                        if (!$segmentAllocatedItem['status']) {
                                            $segment_success = false;
                                        }
                                    } else {
                                         $allocatedQty = SegmentAllocatedItem::where('documentSystemID', $new_purchaseRequests->documentSystemID)
                                                                     ->where('documentMasterAutoID', $new_purchaseRequests->purchaseRequestID)
                                                                     ->where('documentDetailAutoID', $purchaseRequestDetails->purchaseRequestDetailsID)
                                                                     ->sum('allocatedQty');
                    
                                        if ($allocatedQty > $purchaseRequestDetails->quantityRequested) {
                                            $segment_success = false;
                                        }
                                    }
                              
            
                                   
                                }
                          
                                $notification_users = NotificationCompanyScenario::with(['user'=>function($query){
                                    $query->with(['employee'=>function($query){
                                        $query->select('employeeSystemID','empEmail');
                                    }]);
                                }])->select('id','scenarioID')->where('scenarioID',10)->get();
                        
                          
                                
                                foreach($notification_users as $notification_user)
                                {
                                    foreach($notification_user->user as $notification_user)
                                    {
                                       
                                    
                                        $companyID = $companySystemID;
                                        $subject = 'Purchase Request Creation';
                                        $userEmail = $notification_user->employee->empEmail;
                                        $body = 'Purchase Request '.$new_purchaseRequests->purchaseRequestCode.' has been created for ROL reached items.';
                                        $emails = [
                                            'companySystemID' => $companyID,
                                            'alertMessage' => $subject,
                                            'empEmail' => $userEmail,
                                            'emailAlertMessage' => $body
                                        ];
                                        $sendEmail = \Email::sendEmailErp($emails);
                                       
                                    
                                    }
                                
                                }
                            }
             
                            
                        }
                        else
                        {
                            $new_purchaseRequests->delete();
                        }
            
        
        
                    }//item greater than zero

                }//end policy true
   

               

            }//end if policy
           
           

            
            //item count end




        } // for each company end



    }//hand end
}
