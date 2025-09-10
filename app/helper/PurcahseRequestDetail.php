<?php
/**
 * =============================================
 * -- File Name : inventory.php
 * -- Project Name : ERP
 * -- Module Name :  email class
 * -- Author : Mohamed Fayas
 * -- Create date : 15 - August 2018
 * -- Description : This file contains the all the common inventory function
 * -- REVISION HISTORY
 */

namespace App\helper;

use App\Repositories\PurchaseRequestDetailsRepository;
use App\Models\Company;
use App\Models\CompanyPolicyMaster;
use App\Models\ErpItemLedger;
use App\Models\AssetFinanceCategory;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\GRVDetails;
use App\Models\SegmentAllocatedItem;
use App\Models\ItemAssigned;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetails;
use Illuminate\Support\Facades\DB;
use App\helper\Helper;
use App\Models\CompanyDocumentAttachment;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\EmployeesDepartment;
use App\Traits\AuditTrial;
use Response;

class PurcahseRequestDetail
{
    private $purchaseRequestDetailsRepository;

    
    public function __construct(PurchaseRequestDetailsRepository $purchaseRequestDetailsRepo)
    {
        $this->purchaseRequestDetailsRepository = $purchaseRequestDetailsRepo;
    }



    public function validateItem($params)
    {

        $input = $params;
        $companySystemID = $params['companySystemID'];
        $input['purchaseRequestID'] = $params['purcahseRequestID'];
        $allowItemToTypePolicy = false;
        $itemNotound = false;
        $allowItemToType = CompanyPolicyMaster::where('companyPolicyCategoryID', 53)
                                            ->where('companySystemID', $companySystemID)
                                            ->first();

        if ($allowItemToType) {
            if ($allowItemToType->isYesNO) {
                $allowItemToTypePolicy = true;
            }
        }


        if ($allowItemToTypePolicy) {
            $input['itemCode'] = $params['itemCodeSystem'];
        }

        $item = ItemAssigned::where('itemCodeSystem', $input['itemCodeSystem'])
            ->where('companySystemID', $companySystemID)
            ->first();

        if (empty($item)) {
            if (!$allowItemToTypePolicy) {
                return ['status' => false , 'message' => 'Item not found'];
            } else {
                $itemNotound = true;
            }
        }


        $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $input['purchaseRequestID'])
            ->first();


        if (empty($purchaseRequest)) {
            return ['status' => false , 'message' => trans('email.purchase_request_details_not_found')];
            // return $this->sendError('Purchase Request Details not found');
        }

        if ($purchaseRequest->cancelledYN == -1) {
            return ['status' => false , 'message' => trans('email.purchase_request_already_closed')];
            // return $this->sendError('This Purchase Request already closed. You can not add.', 500);
        }

        if ($purchaseRequest->approved == 1) {
            return ['status' => false , 'message' => trans('email.purchase_request_fully_approved')];

            // return $this->sendError('This Purchase Request fully approved. You can not add.', 500);
        }

        $input['budgetYear'] = $purchaseRequest->budgetYear;
        $input['itemPrimaryCode'] = (!$itemNotound) ? $item->itemPrimaryCode : null;
        $input['itemDescription'] = (!$itemNotound) ? $item->itemDescription : $input['itemCodeSystem'];
        $input['partNumber'] = (!$itemNotound) ? $item->secondaryItemCode : null;
        $input['itemFinanceCategoryID'] = (!$itemNotound) ? $item->financeCategoryMaster : null;
        $input['itemFinanceCategorySubID'] = (!$itemNotound) ? $item->financeCategorySub : null;
        //$input['estimatedCost'] = $item->wacValueLocal;

        if (!$itemNotound) {
            $currencyConversion = \Helper::currencyConversion($item->companySystemID, $item->wacValueLocalCurrencyID, $purchaseRequest->currency, $item->wacValueLocal);
            $input['estimatedCost'] = $currencyConversion['documentAmount'];
            $input['companySystemID'] = $item->companySystemID;
            $input['companyID'] = $item->companyID;
            $input['unitOfMeasure'] = $item->itemUnitOfMeasure;
            $input['maxQty'] = $item->maximunQty;
            $input['minQty'] = $item->minimumQty;
            
            $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $item->companySystemID)
                ->where('mainItemCategoryID', $item->financeCategoryMaster)
                ->where('itemCategorySubID', $item->financeCategorySub)
                ->first();

            if (empty($financeItemCategorySubAssigned)) {
                return ['status' => false , 'message' => 'Finance category not assigned for the selected item.'];

                // return $this->sendError('Finance category not assigned for the selected item.');
            }

            if ($item->financeCategoryMaster == 1) {

                // $alreadyAdded = PurchaseRequest::where('purchaseRequestID', $input['purchaseRequestID'])
                //     ->whereHas('details', function ($query) use ($companySystemID, $purchaseRequest, $item) {
                //         $query->where('itemPrimaryCode', $item->itemPrimaryCode);
                //         $query->groupBy('itemPrimaryCode');
                //         $query->selectRaw('sum(quantityRequested) as sum');
                //     })
                //     ->first();

                    $alreadyAdded = PurchaseRequestDetails::select(DB::raw('sum(quantityRequested) as sum'))
                    ->where('purchaseRequestID',  $input['purchaseRequestID'])
                    ->where('itemPrimaryCode', $item->itemPrimaryCode)
                    ->groupBy('itemPrimaryCode')
                    ->first();

                if ($alreadyAdded) {
                     $input['quantityRequested'] = (int) $alreadyAdded->sum + (int) $input['pr_qnty'];
                }else {
                    $input['quantityRequested'] = (int) $input['pr_qnty'];
                }
            }

            $input['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
            $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;

            if ($item->financeCategoryMaster == 3) {
                $assetCategory = AssetFinanceCategory::find($item->faFinanceCatID);
                if (!$assetCategory) {
                    return ['status' => false , 'message' => 'Asset category not assigned for the selected item.'];

                    // return $this->sendError('Asset category not assigned for the selected item.');
                }
                $input['financeGLcodePLSystemID'] = $assetCategory->COSTGLCODESystemID;
                $input['financeGLcodePL'] = $assetCategory->COSTGLCODE;
            } else {
                $input['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                $input['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
            }

            $input['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;
            
            $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
                    ->where('companySystemID', $purchaseRequest->companySystemID)
                    ->first();

            if ($allowFinanceCategory) {
                $policy = $allowFinanceCategory->isYesNO;

                if ($policy == 0) {
                    if ($purchaseRequest->financeCategory == null || $purchaseRequest->financeCategory == 0) {
                        // return $this->sendError('Category is not found.', 500);
                        return ['status' => false , 'message' => 'Category is not found.'];

                    }

                    //checking if item category is same or not
                    $pRDetailExistSameItem = PurchaseRequestDetails::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                        ->where('purchaseRequestID', $purchaseRequest->purchaseRequestID)
                        ->first();

                    if ($pRDetailExistSameItem) {
                        if ($item->financeCategoryMaster != $pRDetailExistSameItem["itemFinanceCategoryID"]) {
                            return ['status' => false , 'message' => 'You cannot add different category item'];
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

                    $checkWhether = PurchaseRequest::where('purchaseRequestID', '!=', $purchaseRequest->purchaseRequestID)
                        ->where('companySystemID', $companySystemID)
                        ->where('serviceLineSystemID', $purchaseRequest->serviceLineSystemID)
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

                    $anyPendingApproval = $checkWhether->whereHas('details', function ($query) use ($companySystemID, $purchaseRequest, $item) {
                        $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                                       ->where('manuallyClosed', 0);
                        /* $query->groupBy(
                             'erp_purchaserequestdetails.itemCode',
                             'erp_purchaserequestdetails.itemPrimaryCode',
                             'erp_purchaserequestdetails.selectedForPO',
                             'erp_purchaserequestdetails.prClosedYN',
                             'erp_purchaserequestdetails.fullyOrdered'
                         )->select([
                         'erp_purchaserequestdetails.itemCode',
                         'erp_purchaserequestdetails.itemPrimaryCode',
                         'erp_purchaserequestdetails.selectedForPO',
                         'erp_purchaserequestdetails.prClosedYN',
                         'erp_purchaserequestdetails.fullyOrdered'
                      ]);*/
                    })
                        ->where('approved', 0)
                        ->where('cancelledYN', 0)
                        ->first();
                    /* approved=0 And cancelledYN=0*/

                    if (!empty($anyPendingApproval)) {
                        return ['status' => false , 'message' => "There is a purchase request (" . $anyPendingApproval->purchaseRequestCode . ") pending for approval for the item you are trying to add. Please check again."];

                        // return $this->sendError("There is a purchase request (" . $anyPendingApproval->purchaseRequestCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                    }

                    $anyApprovedPRButPONotProcessed = PurchaseRequest::where('purchaseRequestID', '!=', $purchaseRequest->purchaseRequestID)
                        ->where('companySystemID', $companySystemID)
                        ->where('serviceLineSystemID', $purchaseRequest->serviceLineSystemID)
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
                        ->whereHas('details', function ($query) use ($companySystemID, $purchaseRequest, $item) {
                            $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                                ->where('selectedForPO', 0)
                                ->where('prClosedYN', 0)
                                ->where('fullyOrdered', 0)
                                ->where('manuallyClosed', 0);

                            /* $query->groupBy(
                                 'erp_purchaserequestdetails.itemCode',
                                 'erp_purchaserequestdetails.itemPrimaryCode',
                                 'erp_purchaserequestdetails.selectedForPO',
                                 'erp_purchaserequestdetails.prClosedYN',
                                 'erp_purchaserequestdetails.fullyOrdered'
                             )->select([
                             'erp_purchaserequestdetails.itemCode',
                             'erp_purchaserequestdetails.itemPrimaryCode',
                             'erp_purchaserequestdetails.selectedForPO',
                             'erp_purchaserequestdetails.prClosedYN',
                             'erp_purchaserequestdetails.fullyOrdered'
                          ]);*/
                        })
                        ->where('approved', -1)
                        ->where('cancelledYN', 0)
                        ->first();
                    /* approved=-1 And cancelledYN=0 And selectedForPO=0 And prClosedYN=0 And fullyOrdered=0*/

                    if (!empty($anyApprovedPRButPONotProcessed)) {
                        return ['status' => false , 'message' => "There is a purchase request (" . $anyApprovedPRButPONotProcessed->purchaseRequestCode . ") approved hense PO is not processed for the item you are trying to add. Please check again"];

                        // return $this->sendError("There is a purchase request (" . $anyApprovedPRButPONotProcessed->purchaseRequestCode . ") approved hense PO is not processed for the item you are trying to add. Please check again", 500);
                    }

                    $anyApprovedPRButPOPartiallyProcessed = PurchaseRequest::where('purchaseRequestID', '!=', $purchaseRequest->purchaseRequestID)
                        ->where('companySystemID', $companySystemID)
                        ->where('serviceLineSystemID', $purchaseRequest->serviceLineSystemID)
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
                        )->whereHas('details', function ($query) use ($companySystemID, $purchaseRequest, $item) {
                            $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                                ->where('selectedForPO', 0)
                                ->where('prClosedYN', 0)
                                ->where('fullyOrdered', 1)
                                ->where('manuallyClosed', 0);
                            /* $query->groupBy(
                                 'erp_purchaserequestdetails.itemCode',
                                 'erp_purchaserequestdetails.itemPrimaryCode',
                                 'erp_purchaserequestdetails.selectedForPO',
                                 'erp_purchaserequestdetails.prClosedYN',
                                 'erp_purchaserequestdetails.fullyOrdered'
                             )->select([
                             'erp_purchaserequestdetails.itemCode',
                             'erp_purchaserequestdetails.itemPrimaryCode',
                             'erp_purchaserequestdetails.selectedForPO',
                             'erp_purchaserequestdetails.prClosedYN',
                             'erp_purchaserequestdetails.fullyOrdered'
                          ]);*/
                        })
                        ->where('approved', -1)
                        ->where('cancelledYN', 0)
                        ->first();
                    /* approved=-1 And cancelledYN=0 And selectedForPO=0 And prClosedYN=0 And fullyOrdered=1*/

                    if (!empty($anyApprovedPRButPOPartiallyProcessed)) {
                        return ['status' => false , 'message' => "There is a purchase request (" . $anyApprovedPRButPOPartiallyProcessed->purchaseRequestCode . ") approved and PO is partially processed for the item you are trying to add. Please check again"];

                        // return $this->sendError("There is a purchase request (" . $anyApprovedPRButPOPartiallyProcessed->purchaseRequestCode . ") approved and PO is partially processed for the item you are trying to add. Please check again", 500);
                    }

                    /* PO check*/

                    $checkPOPending = ProcumentOrder::where('companySystemID', $companySystemID)
                        ->where('serviceLineSystemID', $purchaseRequest->serviceLineSystemID)
                        ->whereHas('detail', function ($query) use ($item) {
                            $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                                   ->where('manuallyClosed', 0);
                        })
                        ->where('approved', 0)
                        ->where('poCancelledYN', 0)
                        ->first();

                    if (!empty($checkPOPending)) {
                        return ['status' => false , 'message' => "There is a purchase order (" . $checkPOPending->purchaseOrderCode . ") pending for approval for the item you are trying to add. Please check again."];

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
                ->where('itemCode', $input['itemCodeSystem'])
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

            $quantityInHand = ErpItemLedger::where('itemSystemCode', $input['itemCodeSystem'])
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
                ->where('itemCode', $input['itemCodeSystem'])
                ->groupBy('erp_grvdetails.itemCode')
                ->select(
                    [
                        'erp_grvdetails.companySystemID',
                        'erp_grvdetails.itemCode'
                    ])
                ->sum('noQty');

            $quantityOnOrder = $poQty - $grvQty;
            $input['poQuantity'] = $poQty;
            $input['quantityOnOrder'] = $quantityOnOrder;
            $input['quantityInHand'] = $quantityInHand;


        } else {
            $input['estimatedCost'] = 0;
            $input['companySystemID'] = $companySystemID;
            $input['companyID'] = $purchaseRequest->companyID;
            $input['unitOfMeasure'] = null;
            $input['maxQty'] = 0;
            $input['minQty'] = 0;
            $input['poQuantity'] = 0;
            $input['quantityOnOrder'] = 0;
            $input['quantityInHand'] = 0;
        }

        $input['itemCode'] =  $item->itemCodeSystem;
        $input['itemCategoryID'] = 0;
        if($alreadyAdded) {
            $data = PurchaseRequestDetails::where('purchaseRequestID',  $input['purchaseRequestID'])
            ->where('itemCode', $item->itemCodeSystem)->first();
            $data->quantityRequested = $input['quantityRequested'];
            $data->save();
        }else {

           $data =  PurchaseRequestDetails::create($input);

        }
        
        return $data;

    }



 public function validateItemOnly($params)
    {

        $input = $params;
        $companySystemID = $params['companySystemID'];
        $input['purchaseRequestID'] = $params['purcahseRequestID'];
        $allowItemToTypePolicy = false;
        $itemNotound = false;
        $allowItemToType = CompanyPolicyMaster::where('companyPolicyCategoryID', 53)
                                            ->where('companySystemID', $companySystemID)
                                            ->first();

        if ($allowItemToType) {
            if ($allowItemToType->isYesNO) {
                $allowItemToTypePolicy = true;
            }
        }


        if ($allowItemToTypePolicy) {
            $input['itemCode'] = $params['itemCodeSystem'];
        }

        $item = ItemAssigned::where('itemCodeSystem', $input['itemCodeSystem'])
            ->where('companySystemID', $companySystemID)
            ->first();

        if (empty($item)) {
            if (!$allowItemToTypePolicy) {
                return ['status' => false , 'message' => 'Item not found'];
            } else {
                $itemNotound = true;
            }
        }


        $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $input['purchaseRequestID'])
            ->first();


        if (empty($purchaseRequest)) {
            return ['status' => false , 'message' => trans('email.purchase_request_details_not_found')];
            // return $this->sendError('Purchase Request Details not found');
        }

        if ($purchaseRequest->cancelledYN == -1) {
            return ['status' => false , 'message' => trans('email.purchase_request_already_closed')];
            // return $this->sendError('This Purchase Request already closed. You can not add.', 500);
        }

        if ($purchaseRequest->approved == 1) {
            return ['status' => false , 'message' => trans('email.purchase_request_fully_approved')];

            // return $this->sendError('This Purchase Request fully approved. You can not add.', 500);
        }

        $input['budgetYear'] = $purchaseRequest->budgetYear;
        $input['itemPrimaryCode'] = (!$itemNotound) ? $item->itemPrimaryCode : null;
        $input['itemDescription'] = (!$itemNotound) ? $item->itemDescription : $input['itemCodeSystem'];
        $input['partNumber'] = (!$itemNotound) ? $item->secondaryItemCode : null;
        $input['itemFinanceCategoryID'] = (!$itemNotound) ? $item->financeCategoryMaster : null;
        $input['itemFinanceCategorySubID'] = (!$itemNotound) ? $item->financeCategorySub : null;
        //$input['estimatedCost'] = $item->wacValueLocal;

        if (!$itemNotound) {
            $currencyConversion = \Helper::currencyConversion($item->companySystemID, $item->wacValueLocalCurrencyID, $purchaseRequest->currency, $item->wacValueLocal);
            $input['estimatedCost'] = $currencyConversion['documentAmount'];
            $input['companySystemID'] = $item->companySystemID;
            $input['companyID'] = $item->companyID;
            $input['unitOfMeasure'] = $item->itemUnitOfMeasure;
            $input['maxQty'] = $item->maximunQty;
            $input['minQty'] = $item->minimumQty;
            
            $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $item->companySystemID)
                ->where('mainItemCategoryID', $item->financeCategoryMaster)
                ->where('itemCategorySubID', $item->financeCategorySub)
                ->first();

            if (empty($financeItemCategorySubAssigned)) {
                return ['status' => false , 'message' => 'Finance category not assigned for the selected item.'];

                // return $this->sendError('Finance category not assigned for the selected item.');
            }

            if ($item->financeCategoryMaster == 1) {

                $alreadyAdded = PurchaseRequest::where('purchaseRequestID', $input['purchaseRequestID'])
                    ->whereHas('details', function ($query) use ($companySystemID, $purchaseRequest, $item) {
                        $query->where('itemPrimaryCode', $item->itemPrimaryCode);
                    })
                    ->first();

                if($alreadyAdded) {
                    return ['status' => false , 'message' => trans('email.item_already_added')];
                }
            }

            $input['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
            $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;

            if ($item->financeCategoryMaster == 3) {
                $assetCategory = AssetFinanceCategory::find($item->faFinanceCatID);
                if (!$assetCategory) {
                    return ['status' => false , 'message' => 'Asset category not assigned for the selected item.'];

                    // return $this->sendError('Asset category not assigned for the selected item.');
                }
                $input['financeGLcodePLSystemID'] = $assetCategory->COSTGLCODESystemID;
                $input['financeGLcodePL'] = $assetCategory->COSTGLCODE;
            } else {
                $input['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                $input['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
            }

            $input['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;
            
            $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
                    ->where('companySystemID', $purchaseRequest->companySystemID)
                    ->first();

            if ($allowFinanceCategory) {
                $policy = $allowFinanceCategory->isYesNO;

                if ($policy == 0) {
                    if ($purchaseRequest->financeCategory == null || $purchaseRequest->financeCategory == 0) {
                        // return $this->sendError('Category is not found.', 500);
                        return ['status' => false , 'message' => 'Category is not found.'];

                    }

                    //checking if item category is same or not
                    $pRDetailExistSameItem = PurchaseRequestDetails::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                        ->where('purchaseRequestID', $purchaseRequest->purchaseRequestID)
                        ->first();

                    if ($pRDetailExistSameItem) {
                        if ($item->financeCategoryMaster != $pRDetailExistSameItem["itemFinanceCategoryID"]) {
                            return ['status' => false , 'message' => 'You cannot add different category item'];
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

                    $checkWhether = PurchaseRequest::where('purchaseRequestID', '!=', $purchaseRequest->purchaseRequestID)
                        ->where('companySystemID', $companySystemID)
                        ->where('serviceLineSystemID', $purchaseRequest->serviceLineSystemID)
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

                    $anyPendingApproval = $checkWhether->whereHas('details', function ($query) use ($companySystemID, $purchaseRequest, $item) {
                        $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                                       ->where('manuallyClosed', 0);
                        /* $query->groupBy(
                             'erp_purchaserequestdetails.itemCode',
                             'erp_purchaserequestdetails.itemPrimaryCode',
                             'erp_purchaserequestdetails.selectedForPO',
                             'erp_purchaserequestdetails.prClosedYN',
                             'erp_purchaserequestdetails.fullyOrdered'
                         )->select([
                         'erp_purchaserequestdetails.itemCode',
                         'erp_purchaserequestdetails.itemPrimaryCode',
                         'erp_purchaserequestdetails.selectedForPO',
                         'erp_purchaserequestdetails.prClosedYN',
                         'erp_purchaserequestdetails.fullyOrdered'
                      ]);*/
                    })
                        ->where('approved', 0)
                        ->where('cancelledYN', 0)
                        ->first();
                    /* approved=0 And cancelledYN=0*/

                    if (!empty($anyPendingApproval)) {
                        return ['status' => false , 'message' => "There is a purchase request (" . $anyPendingApproval->purchaseRequestCode . ") pending for approval for the item you are trying to add. Please check again."];

                        // return $this->sendError("There is a purchase request (" . $anyPendingApproval->purchaseRequestCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                    }

                    $anyApprovedPRButPONotProcessed = PurchaseRequest::where('purchaseRequestID', '!=', $purchaseRequest->purchaseRequestID)
                        ->where('companySystemID', $companySystemID)
                        ->where('serviceLineSystemID', $purchaseRequest->serviceLineSystemID)
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
                        ->whereHas('details', function ($query) use ($companySystemID, $purchaseRequest, $item) {
                            $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                                ->where('selectedForPO', 0)
                                ->where('prClosedYN', 0)
                                ->where('fullyOrdered', 0)
                                ->where('manuallyClosed', 0);

                            /* $query->groupBy(
                                 'erp_purchaserequestdetails.itemCode',
                                 'erp_purchaserequestdetails.itemPrimaryCode',
                                 'erp_purchaserequestdetails.selectedForPO',
                                 'erp_purchaserequestdetails.prClosedYN',
                                 'erp_purchaserequestdetails.fullyOrdered'
                             )->select([
                             'erp_purchaserequestdetails.itemCode',
                             'erp_purchaserequestdetails.itemPrimaryCode',
                             'erp_purchaserequestdetails.selectedForPO',
                             'erp_purchaserequestdetails.prClosedYN',
                             'erp_purchaserequestdetails.fullyOrdered'
                          ]);*/
                        })
                        ->where('approved', -1)
                        ->where('cancelledYN', 0)
                        ->first();
                    /* approved=-1 And cancelledYN=0 And selectedForPO=0 And prClosedYN=0 And fullyOrdered=0*/

                    if (!empty($anyApprovedPRButPONotProcessed)) {
                        return ['status' => false , 'message' => "There is a purchase request (" . $anyApprovedPRButPONotProcessed->purchaseRequestCode . ") approved hense PO is not processed for the item you are trying to add. Please check again"];

                        // return $this->sendError("There is a purchase request (" . $anyApprovedPRButPONotProcessed->purchaseRequestCode . ") approved hense PO is not processed for the item you are trying to add. Please check again", 500);
                    }

                    $anyApprovedPRButPOPartiallyProcessed = PurchaseRequest::where('purchaseRequestID', '!=', $purchaseRequest->purchaseRequestID)
                        ->where('companySystemID', $companySystemID)
                        ->where('serviceLineSystemID', $purchaseRequest->serviceLineSystemID)
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
                        )->whereHas('details', function ($query) use ($companySystemID, $purchaseRequest, $item) {
                            $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                                ->where('selectedForPO', 0)
                                ->where('prClosedYN', 0)
                                ->where('fullyOrdered', 1)
                                ->where('manuallyClosed', 0);
                            /* $query->groupBy(
                                 'erp_purchaserequestdetails.itemCode',
                                 'erp_purchaserequestdetails.itemPrimaryCode',
                                 'erp_purchaserequestdetails.selectedForPO',
                                 'erp_purchaserequestdetails.prClosedYN',
                                 'erp_purchaserequestdetails.fullyOrdered'
                             )->select([
                             'erp_purchaserequestdetails.itemCode',
                             'erp_purchaserequestdetails.itemPrimaryCode',
                             'erp_purchaserequestdetails.selectedForPO',
                             'erp_purchaserequestdetails.prClosedYN',
                             'erp_purchaserequestdetails.fullyOrdered'
                          ]);*/
                        })
                        ->where('approved', -1)
                        ->where('cancelledYN', 0)
                        ->first();
                    /* approved=-1 And cancelledYN=0 And selectedForPO=0 And prClosedYN=0 And fullyOrdered=1*/

                    if (!empty($anyApprovedPRButPOPartiallyProcessed)) {
                        return ['status' => false , 'message' => "There is a purchase request (" . $anyApprovedPRButPOPartiallyProcessed->purchaseRequestCode . ") approved and PO is partially processed for the item you are trying to add. Please check again"];

                        // return $this->sendError("There is a purchase request (" . $anyApprovedPRButPOPartiallyProcessed->purchaseRequestCode . ") approved and PO is partially processed for the item you are trying to add. Please check again", 500);
                    }

                    /* PO check*/

                    $checkPOPending = ProcumentOrder::where('companySystemID', $companySystemID)
                        ->where('serviceLineSystemID', $purchaseRequest->serviceLineSystemID)
                        ->whereHas('detail', function ($query) use ($item) {
                            $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                                   ->where('manuallyClosed', 0);
                        })
                        ->where('approved', 0)
                        ->where('poCancelledYN', 0)
                        ->first();

                    if (!empty($checkPOPending)) {
                        return ['status' => false , 'message' => "There is a purchase order (" . $checkPOPending->purchaseOrderCode . ") pending for approval for the item you are trying to add. Please check again."];

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
                ->where('itemCode', $input['itemCodeSystem'])
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

            $quantityInHand = ErpItemLedger::where('itemSystemCode', $input['itemCodeSystem'])
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
                ->where('itemCode', $input['itemCodeSystem'])
                ->groupBy('erp_grvdetails.itemCode')
                ->select(
                    [
                        'erp_grvdetails.companySystemID',
                        'erp_grvdetails.itemCode'
                    ])
                ->sum('noQty');

            $quantityOnOrder = $poQty - $grvQty;
            $input['poQuantity'] = $poQty;
            $input['quantityOnOrder'] = $quantityOnOrder;
            $input['quantityInHand'] = $quantityInHand;


        } else {
            $input['estimatedCost'] = 0;
            $input['companySystemID'] = $companySystemID;
            $input['companyID'] = $purchaseRequest->companyID;
            $input['unitOfMeasure'] = null;
            $input['maxQty'] = 0;
            $input['minQty'] = 0;
            $input['poQuantity'] = 0;
            $input['quantityOnOrder'] = 0;
            $input['quantityInHand'] = 0;
        }

        $input['itemCode'] =   $input['itemCodeSystem'];
        $input['itemCategoryID'] = 0;
        
        return ['status' => true , 'message' => trans('email.validation_success')];


    }

    public function purchaseRequestReopen($input){
        $purchaseRequestId = $input['purchaseRequestId'];

        $purchaseRequest = PurchaseRequest::find($purchaseRequestId);
        $emails = array();
        if (empty($purchaseRequest)) {
            return ['status' => false, 'message' => trans('email.purchase_request_not_found')];
        }

        if ($purchaseRequest->RollLevForApp_curr > 1) {
            return ['status' => false, 'message' => trans('email.cannot_reopen_partially_approved')];
        }

        if ($purchaseRequest->approved == -1) {
            return ['status' => false, 'message' => trans('email.cannot_reopen_fully_approved')];
        }

        if ($purchaseRequest->PRConfirmedYN == 0) {
            return ['status' => false, 'message' => trans('email.cannot_reopen_not_confirmed')];
        }

        // updating fields
        $purchaseRequest->PRConfirmedYN = 0;
        $purchaseRequest->PRConfirmedBySystemID = null;
        $purchaseRequest->PRConfirmedBy = null;
        $purchaseRequest->PRConfirmedByEmpName = null;
        $purchaseRequest->PRConfirmedDate = null;
        $purchaseRequest->RollLevForApp_curr = 1;
        $purchaseRequest->save();

        $employee = Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $purchaseRequest->documentSystemID)->first();

        $cancelDocNameBody = $document->document_description_translated . ' <b>' . $purchaseRequest->purchaseRequestCode . '</b>';
        $cancelDocNameSubject = $document->document_description_translated . ' ' . $purchaseRequest->purchaseRequestCode;

        $subject = $cancelDocNameSubject . ' ' . trans('email.is_reopened');

        $body = '<p>' . $cancelDocNameBody . ' ' . trans('email.is_reopened_by', ['empID' => $employee->empID, 'empName' => $employee->empFullName]) . '</p><p>' . trans('email.comment') . ' : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $purchaseRequest->companySystemID)
            ->where('documentSystemCode', $purchaseRequest->purchaseRequestID)
            ->where('documentSystemID', $purchaseRequest->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $purchaseRequest->companySystemID)
                    ->where('documentSystemID', $purchaseRequest->documentSystemID)
                    ->first();

                if (empty($companyDocument)) {
                    return ['success' => false, 'message' => 'Policy not found for this document'];
                }

                $approvalList = EmployeesDepartment::where('employeeGroupID', $documentApproval->approvalGroupID)
                    ->where('companySystemID', $documentApproval->companySystemID)
                    ->where('documentSystemID', $documentApproval->documentSystemID);

                if ($companyDocument['isServiceLineApproval'] == -1) {
                    $approvalList = $approvalList->where('ServiceLineSystemID', $documentApproval->serviceLineSystemID);
                }

                $approvalList = $approvalList
                    ->with(['employee'])
                    ->groupBy('employeeSystemID')
                    ->get();

                foreach ($approvalList as $da) {
                    if ($da->employee) {
                        $emails[] = array('empSystemID' => $da->employee->employeeSystemID,
                            'companySystemID' => $documentApproval->companySystemID,
                            'docSystemID' => $documentApproval->documentSystemID,
                            'alertMessage' => $subject,
                            'emailAlertMessage' => $body,
                            'docSystemCode' => $documentApproval->documentSystemCode);
                    }
                }

                $sendEmail = \Email::sendEmail($emails);
                if (!$sendEmail["success"]) {
                    return ['success' => false, 'message' => $sendEmail["message"]];
                }
            }
        }

        DocumentApproved::where('documentSystemCode', $purchaseRequest->purchaseRequestID)
            ->where('companySystemID', $purchaseRequest->companySystemID)
            ->where('documentSystemID', $purchaseRequest->documentSystemID)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($purchaseRequest->documentSystemID,$purchaseRequest->purchaseRequestID,$input['reopenComments'],'Reopened');
    }



}