<?php

namespace App\Repositories;

use App\Models\PurchaseRequestDetails;
use App\Models\ItemAssigned;
use App\Models\PurchaseRequest;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\ProcumentOrder;
use App\Models\CompanyPolicyMaster;
use App\Models\PurchaseOrderDetails;
use App\Models\GRVDetails;
use App\Models\ErpItemLedger;
use App\helper\Helper;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class PurchaseRequestDetailsRepository
 * @package App\Repositories
 * @version March 29, 2018, 11:41 am UTC
 *
 * @method PurchaseRequestDetails findWithoutFail($id, $columns = ['*'])
 * @method PurchaseRequestDetails find($id, $columns = ['*'])
 * @method PurchaseRequestDetails first($columns = ['*'])
*/
class PurchaseRequestDetailsRepository extends BaseRepository
{


    /**
     * @var array
     */
    protected $fieldSearchable = [
        'purchaseRequestID',
        'materialReqeuestID',
        'companySystemID',
        'companyID',
        'itemCategoryID',
        'itemCode',
        'itemPrimaryCode',
        'itemDescription',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'includePLForGRVYN',
        'partNumber',
        'quantityRequested',
        'estimatedCost',
        'totalCost',
        'budgetYear',
        'budjetAmtLocal',
        'budjetAmtRpt',
        'quantityOnOrder',
        'comments',
        'unitOfMeasure',
        'itemClientReferenceNumberMasterID',
        'clientReferenceNumber',
        'quantityInHand',
        'maxQty',
        'minQty',
        'poQuantity',
        'specificationGrade',
        'jobNo',
        'technicalDataSheetAttachment',
        'selectedForPO',
        'prClosedYN',
        'fullyOrdered',
        'poTrackingID',
        'timeStamp',
        'altUnit',
        'altUnitValue',
        'isMRPulled'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PurchaseRequestDetails::class;
    }

    public function storePrDetails($prDetailsArray, $purchaseRequestID, $totalItemsToUpload, $segmentAllocatedItemRepository)
    {
        $successCount = 0;
        $duplicateCount = 0;
        $notFoundCount = 0;
        $notUploadCount = [];
        $finalData = [];
        $nullValuesGlobal = false;
        $nonNumericUnitCost = false;
        $minusValueUnitCost = false;
        $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $purchaseRequestID)
                                          ->first();

        $companySystemID = $purchaseRequest->companySystemID;

        foreach ($prDetailsArray as $key => $input) {
            $lineError = false;
            $nullValues = false;
            if ((isset($input['item_code']) && !is_null($input['item_code'])) || isset($input['item_description']) && !is_null($input['item_description']) || isset($input['comment']) && !is_null($input['comment']) || isset($input['qty']) && !is_null($input['qty']) || isset($input['estimated_unit_cost']) && !is_null($input['estimated_unit_cost'])) {
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


                if ((isset($input['item_code']) && !$allowItemToTypePolicy) || $allowItemToTypePolicy) {
                    $item = false;
                    if (isset($input['item_code'])) {
                        $item = ItemAssigned::where('itemPrimaryCode', $input['item_code'])
                                        ->where('companySystemID', $companySystemID)
                                        ->where('isActive', 1)
                                        ->first();
                    }

                    if (!$item && $allowItemToTypePolicy && isset($input['item_description']) && !is_null($input['item_description'])) {
                        $insertData['budgetYear'] = $purchaseRequest->budgetYear;
                        $insertData['itemPrimaryCode'] = null;
                        $insertData['itemDescription'] = $input['item_description'];
                        $insertData['partNumber'] = null;
                        $insertData['itemFinanceCategoryID'] = null;
                        $insertData['itemFinanceCategorySubID'] = null;
                        $insertData['estimatedCost'] = $input['estimated_unit_cost'];
                        $insertData['companySystemID'] = $companySystemID;
                        $insertData['companyID'] = $purchaseRequest->companyID;
                        $insertData['unitOfMeasure'] = null;
                        $insertData['maxQty'] = 0;
                        $insertData['minQty'] = 0;
                        $insertData['poQuantity'] = 0;
                        $insertData['quantityOnOrder'] = 0;
                        $insertData['quantityInHand'] = 0;
                        $insertData['itemCode'] = null;
                        $insertData['itemCategoryID'] = 0;

                        $insertData['purchaseRequestID'] = $purchaseRequestID;

                        if (isset($input['qty']) && $input['qty'] > 0) {
                            $insertData['quantityRequested'] = $input['qty'];
                        } else {
                            $nullValues = true;
                        }

                        if (isset($input['comment'])) {
                            $insertData['comments'] = $input['comment'];
                        }

                        $requestedQty = $input['qty'];
                        $reorderQty = ItemAssigned::where('itemPrimaryCode', $input['item_code'])->where('companySystemID', $companySystemID)->sum('rolQuantity');
                        $itemFinanceCategoryID = $insertData['itemFinanceCategoryID'];
                        $requestAndReorderTotal = $requestedQty + $reorderQty;
                        if($insertData['quantityInHand'] > $requestAndReorderTotal && $itemFinanceCategoryID==1){
                            $insertData['is_eligible_mr'] = 1;
                        } else {
                            $insertData['is_eligible_mr'] = 0;
                        }


                           

                        if (!$nullValues) {
                            $purchaseRequestDetails = $this->model->create($insertData);
                            $successCount = $successCount +1;
                            $allocationData = [
                                'serviceLineSystemID' => $purchaseRequest->serviceLineSystemID,
                                'documentSystemID' => $purchaseRequest->documentSystemID,
                                'docAutoID' => $purchaseRequestID,
                                'docDetailID' => $purchaseRequestDetails->purchaseRequestDetailsID
                            ];
        
                            $segmentAllocatedItem = $segmentAllocatedItemRepository->allocateSegmentWiseItem($allocationData);
        
                            if (!$segmentAllocatedItem['status']) {
                                return $this->sendError($segmentAllocatedItem['message']);
                            }
                        }

                        if ($nullValues) {
                            $nullValuesGlobal = true;
                        }
                    } else {
                        if ($item) {
                            $insertData['budgetYear'] = $purchaseRequest->budgetYear;
                            $insertData['itemPrimaryCode'] = $item->itemPrimaryCode;
                            $insertData['itemCode'] = $item->itemCodeSystem;
                            $insertData['itemDescription'] = isset($input['item_description']) ? $input['item_description'] : $item->itemDescription;
                            $insertData['partNumber'] = $item->secondaryItemCode;
                            $insertData['itemFinanceCategoryID'] = $item->financeCategoryMaster;
                            $insertData['itemFinanceCategorySubID'] = $item->financeCategorySub;

                            $currencyConversion = \Helper::currencyConversion($item->companySystemID, $item->wacValueLocalCurrencyID, $purchaseRequest->currency, $item->wacValueLocal);


                            if (isset($input['estimated_unit_cost'])) {
                                $insertData['estimatedCost'] = $input['estimated_unit_cost'];
                            } else {
                                $insertData['estimatedCost'] = $currencyConversion['documentAmount'];
                            }

                            $insertData['companySystemID'] = $item->companySystemID;
                            $insertData['companyID'] = $item->companyID;
                            $insertData['unitOfMeasure'] = $item->itemUnitOfMeasure;
                            $insertData['altUnit'] = $item->itemUnitOfMeasure;
                            $insertData['maxQty'] = $item->maximunQty;
                            $insertData['minQty'] = $item->minimumQty;

                            $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $item->companySystemID)
                                                                                            ->where('mainItemCategoryID', $item->financeCategoryMaster)
                                                                                            ->where('itemCategorySubID', $item->financeCategorySub)
                                                                                            ->first();

                            if (empty($financeItemCategorySubAssigned)) {
                                $notUploadCount[] = $input['item_code'];
                                $lineError = true;
                            } else {
                                if ($item->financeCategoryMaster == 1) {
                                    $alreadyAdded = PurchaseRequest::where('purchaseRequestID', $purchaseRequestID)
                                        ->whereHas('details', function ($query) use ($companySystemID, $purchaseRequest, $item) {
                                            $query->where('itemPrimaryCode', $item->itemPrimaryCode);
                                        })
                                        ->first();

                                    if ($alreadyAdded) {
                                        $duplicateCount = $duplicateCount + 1;
                                        $lineError = true;
                                    }
                                }

                                $insertData['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                                $insertData['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                                $insertData['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                                $insertData['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                                $insertData['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;
                                $insertData['itemCategoryID'] = 0;


                                $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
                                                                        ->where('companySystemID', $purchaseRequest->companySystemID)
                                                                        ->first();

                                if ($allowFinanceCategory) {
                                    $policy = $allowFinanceCategory->isYesNO;

                                    if ($policy == 0) {
                                        if ($purchaseRequest->financeCategory == null || $purchaseRequest->financeCategory == 0) {
                                            $notUploadCount[] = $input['item_code'];
                                            $lineError = true;
                                        }

                                        //checking if item category is same or not
                                        $pRDetailExistSameItem = PurchaseRequestDetails::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                                                                                        ->where('purchaseRequestID', $purchaseRequest->purchaseRequestID)
                                                                                        ->first();

                                        if ($pRDetailExistSameItem) {
                                            if ($item->financeCategoryMaster != $pRDetailExistSameItem["itemFinanceCategoryID"]) {
                                                $notUploadCount[] = $input['item_code'];
                                                $lineError = true;
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
                                                                            })
                                                                            ->where('approved', 0)
                                                                            ->where('cancelledYN', 0)
                                                                            ->first();

                                        if (!empty($anyPendingApproval)) {
                                             $notUploadCount[] = $input['item_code'];
                                             $lineError = true;
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
                                                                                        })
                                                                                        ->where('approved', -1)
                                                                                        ->where('cancelledYN', 0)
                                                                                        ->first();

                                        if (!empty($anyApprovedPRButPONotProcessed)) {
                                             $notUploadCount[] = $input['item_code'];
                                             $lineError = true;
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
                                            })
                                            ->where('approved', -1)
                                            ->where('cancelledYN', 0)
                                            ->first();
                                        /* approved=-1 And cancelledYN=0 And selectedForPO=0 And prClosedYN=0 And fullyOrdered=1*/

                                        if (!empty($anyApprovedPRButPOPartiallyProcessed)) {
                                             $notUploadCount[] = $input['item_code'];
                                             $lineError = true;
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
                                            $notUploadCount[] = $input['item_code'];
                                            $lineError = true;
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
                                                            ->where('itemCode', $item->itemCodeSystem)
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

                                $quantityInHand = ErpItemLedger::where('itemSystemCode', $item->itemCodeSystem)
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
                                    ->where('itemCode', $item->itemCodeSystem)
                                    ->groupBy('erp_grvdetails.itemCode')
                                    ->select(
                                        [
                                            'erp_grvdetails.companySystemID',
                                            'erp_grvdetails.itemCode'
                                        ])
                                    ->sum('noQty');

                                $quantityOnOrder = $poQty - $grvQty;
                                $insertData['poQuantity'] = $poQty;
                                $insertData['quantityOnOrder'] = round($quantityOnOrder,2);
                                $insertData['quantityInHand'] = $quantityInHand;
                                $insertData['purchaseRequestID'] = $purchaseRequestID;

                                if (isset($input['qty']) && $input['qty'] > 0) {
                                    $insertData['quantityRequested'] = $input['qty'];
                                } else {
                                    $nullValues = true;
                                }

                                $insertData['totalCost'] = $insertData['estimatedCost']*$insertData['quantityRequested'];
                                $insertData['altUnitValue'] = $insertData['quantityRequested'];
                                if (isset($input['comment'])) {
                                    $insertData['comments'] = $input['comment'];
                                }

                                $requestedQty = $input['qty'];
                                $reorderQty = ItemAssigned::where('itemPrimaryCode', $input['item_code'])->where('companySystemID', $companySystemID)->sum('rolQuantity');
                                $itemFinanceCategoryID = $insertData['itemFinanceCategoryID'];
                                $requestAndReorderTotal = $requestedQty + $reorderQty;
                                if($quantityInHand > $requestAndReorderTotal && $itemFinanceCategoryID==1){
                                    $insertData['is_eligible_mr'] = 1;
                                } else {
                                    $insertData['is_eligible_mr'] = 0;
                                }


                                if (!$lineError && !$nullValues) {
                                    $purchaseRequestDetails = $this->model->create($insertData);
                                    $successCount = $successCount +1;

                                    $allocationData = [
                                        'serviceLineSystemID' => $purchaseRequest->serviceLineSystemID,
                                        'documentSystemID' => $purchaseRequest->documentSystemID,
                                        'docAutoID' => $purchaseRequestID,
                                        'docDetailID' => $purchaseRequestDetails->purchaseRequestDetailsID
                                    ];
                
                                    $segmentAllocatedItem = $segmentAllocatedItemRepository->allocateSegmentWiseItem($allocationData);
                
                                    if (!$segmentAllocatedItem['status']) {
                                        return $this->sendError($segmentAllocatedItem['message']);
                                    }
                                }

                                if ($nullValues) {
                                    $nullValuesGlobal = true;
                                }
                            }

                        } else {
                            if (!isset($input['item_code']) || (isset($input['item_code']) && is_null($input['item_code']))) {
                                $nullValuesGlobal = true;
                            } else {
                                $notFoundCount = $notFoundCount + 1;                
                            }
                        }
                    }

                } else {
                    $nullValuesGlobal = true;
                }

            }
        }
     
        $notUploadCountUnique = sizeof(array_unique($notUploadCount));

        if ($successCount == $totalItemsToUpload) {
            $message = "Upload successful";
        } else {
            $message = "Successfully uploaded ".$successCount." items out of ".$totalItemsToUpload.".";
            
            if ($notFoundCount > 0) {
                $message = $message." ".$notFoundCount." items cannot be found.";    
            }

            if ($duplicateCount > 0) {
                $message = $message." ".$duplicateCount." items is/are duplicated.";    
            }

            if ($nullValuesGlobal) {
                $message = $message." some items cannot be uploaded, as there are null values found";    
            } else {
                if ($notUploadCountUnique > 0) {
                    $message = $message." ".$notUploadCountUnique." items cannot be uploaded.";    
                }
            }
                        
        }

        return $message;
    }
}
