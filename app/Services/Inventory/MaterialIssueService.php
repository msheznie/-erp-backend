<?php

namespace App\Services\Inventory;

use App\helper\CommonJobService;
use App\helper\inventory;
use App\Models\Company;
use App\Models\CustomerInvoiceDirect;
use App\Models\DeliveryOrder;
use App\Models\ErpProjectMaster;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\ItemAssigned;
use App\Models\ItemCategoryTypeMaster;
use App\Models\ItemClientReferenceNumberMaster;
use App\Models\ItemIssueDetails;
use App\Models\ItemIssueMaster;
use App\Models\ItemMaster;
use App\Models\ItemMasterCategoryType;
use App\Models\MaterielRequest;
use App\Models\MiBulkUploadErrorLog;
use App\Models\PurchaseReturn;
use App\Models\StockTransfer;
use App\Models\WarehouseMaster;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\Array_;
use function foo\func;

class MaterialIssueService
{

    public static  function validateRequestWithQty($input):Array {
        $materielRequest = MaterielRequest::where('RequestID',$input['reqDocID'])->first();
        $totalQuantityRequested = $materielRequest->details->sum('quantityRequested');
        $materielIssue = ItemIssueMaster::with(['details'])->where('reqDocID',$input['reqDocID'])->get();
        $totalIssuedQty = 0;
        foreach ($materielIssue as $mi) {
            $totalIssuedQty += $mi->details->sum('qtyIssued');
        }

        if($totalQuantityRequested != 0 && ($totalQuantityRequested == $totalIssuedQty)) {
            return ['message' => 'Item/s fully issued for this request'];
        }
        return [];
    }

    public static  function getMaterialRequest($subCompanies,$request,$input,$confirmYn):Array {

        $search = $input['search'];

        $materielRequests = MaterielRequest::whereIn('companySystemID', $subCompanies)
            ->where("approved", -1)
            ->where("cancelledYN", 0)
            ->where("serviceLineSystemID", $request['serviceLineSystemID']);

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $materielRequests = $materielRequests->where(function ($query) use ($search) {
                $query->where('itemIssueCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        $materielRequests = $materielRequests->get(['RequestID', 'RequestCode']);
        $data = array();
        foreach ($materielRequests as $mr) {
            $totalQuantityRequested = $mr->details->sum('quantityRequested');
            $materielIssue = ItemIssueMaster::with(['details'])->where('reqDocID',$mr->RequestID)->get();
            $totalIssuedQty = 0;
            foreach ($materielIssue as $mi) {
                $totalIssuedQty += $mi->details->sum('qtyIssued');
            }

            if($confirmYn == 1) {
                array_push($data,$mr->only(['RequestCode','RequestID']));
            }else {
                if($totalQuantityRequested != 0 && ($totalQuantityRequested != $totalIssuedQty)) {
                    array_push($data,$mr->only(['RequestCode','RequestID']));
                }
            }


        }

        return $data;
    }



    public static function getItemDetailsForMaterialIssue($input):Array {
        $materielRequest = MaterielRequest::select(['RequestID'])->where('RequestID', $input['reqDocID'])->first();
        if(isset($materielRequest)) {
            $issuedQty = 0;
            $materielIssue = ItemIssueMaster::with(['details'])->where('reqDocID',$materielRequest->RequestID)->get();
            if($input['issueType'] == 2) {
                foreach($materielIssue as $mi) {
                    $item = $mi->details()->where('itemCodeSystem',$input['itemCodeSystem'])->first();
                    $issuedQty += isset($item->qtyIssued) ? (int) $item->qtyIssued : 0;
                }

                $input['issuedQty'] = $issuedQty;
                $input['qtyAvailableToIssue'] = (int) ($issuedQty == 0) ? $input['qtyRequested']: ($input['qtyRequested'] - $issuedQty);
                $input['qtyIssued'] = $input['qtyAvailableToIssue'];
                $input['qtyIssuedDefaultMeasure'] = $input['qtyAvailableToIssue'];
                return $input;

            }
        }
        return $input;
    }

     public static function getItemDetailsForMaterialIssueUpdate($input):Array {
        $materielIssueParent = ItemIssueMaster::where('itemIssueAutoID',$input['itemIssueAutoID'])->first();
        $materielRequest = MaterielRequest::select(['RequestID'])->where('RequestID', $materielIssueParent->reqDocID)->first();
        if($materielIssueParent->issueType ==   2) {
            $materielAllIssues = ItemIssueMaster::with(['details'])->where('reqDocID',$materielRequest->RequestID)->get();

            $issuedQty = 0;
            if(count($materielAllIssues) == 1 ) {
                $issuedQty = $input['qtyIssued'] ;
            }else {
                $materielIssue = ItemIssueMaster::with(['details'])->where('reqDocID',$materielRequest->RequestID)->whereNotIn('itemIssueAutoID',[$input['itemIssueAutoID']])->get();
                foreach($materielIssue as $mi) {
                    $item = $mi->details()->where('itemCodeSystem',$input['itemCodeSystem'])->first();
                    $issuedQty += isset($item->qtyIssued) ? (int) $item->qtyIssued : 0;
                }
            }

            $input['qtyAvailableToIssue'] = (int) ($issuedQty == 0) ? $input['qtyRequested']: ($input['qtyRequested'] - $issuedQty);
            return $input;

        }else {
            return $input;

        }

    }

    public static function  addMultipleItems($items,$materialIssue,$db,$authID) {
        CommonJobService::db_switch($db);

        $materialIssue = ItemIssueMaster::find($materialIssue['itemIssueAutoID']);
        $materialIssue->upload_job_status = 0;
        $materialIssue->successDetailsCount = 0;
        $materialIssue->excelRowCount = 0;
        $materialIssue->save();

        $validatedItems = self::uploadValidations($items, $materialIssue, $authID);

        if (!empty($validatedItems['itemDetails'])) {
            foreach ($validatedItems['itemDetails'] as $key => $value) {
                ItemIssueDetails::create($value);
            }
        }

        if (!empty($validatedItems['errorLog'])) {
            self::errorLogUpdate($validatedItems['errorLog'], $materialIssue['itemIssueAutoID']);
        }

        Log::info('Add Material Issue Multiple Items End');
        $materialIssue = ItemIssueMaster::find($materialIssue['itemIssueAutoID']);
        $materialIssue->upload_job_status = 1;
        $materialIssue->isBulkItemJobRun = 0;
        $materialIssue->successDetailsCount = $validatedItems['successCount'];
        $materialIssue->excelRowCount = $validatedItems['excelRowCount'];
        $materialIssue->save();
    }

    public static function uploadValidations($excelRows, $materialIssue, $authID) {
        $rowNumber = 6;
        $validationErrorMsg = $validatedItemsArray = [];
        $successCount = $excelRowCount = 0;

        foreach ($excelRows as $rowData) {
            $isValidationError = 0;

            if (array_key_exists('item_code',$rowData) && !is_null($rowData['item_code'])) {
                $companyId = $materialIssue['companySystemID'];
                $categoryType = ItemMaster::whereHas('itemAssigned', function ($query) use ($companyId) {
                    return $query->where('companySystemID', '=', $companyId)->where('isAssigned', -1);
                })->where('isActive',1)
                    ->where('itemApprovedYN',1)
                    ->where('primaryCode', trim($rowData['item_code']))
                    ->first();

                if ($categoryType) {
                    $checkTheCategoryType = ItemMasterCategoryType::whereIn('categoryTypeID', ItemCategoryTypeMaster::purchaseItems())
                        ->where('itemCodeSystem', $categoryType->itemCodeSystem)
                        ->first();

                    if (!$checkTheCategoryType) {
                        $validationErrorMsg[] = 'The inventory items added should only be of Item Type: Purchase or Purchase & Sales for Excel row: ' . $rowNumber;
                        $isValidationError = 1;
                    }

                    $data = array(
                        'companySystemID' => $materialIssue['companySystemID'],
                        'itemCodeSystem' => $categoryType->itemCodeSystem,
                        'wareHouseId' => $materialIssue['wareHouseFrom']
                    );
                    $itemCurrentCostAndQty = Inventory::itemCurrentCostAndQty($data);

                    if (($rowData['qty'] > $itemCurrentCostAndQty['currentStockQty']) || ($rowData['qty'] > $itemCurrentCostAndQty['currentWareHouseStockQty'])) {
                        $validationErrorMsg[] = 'Stock Qty is 0. You cannot issue. for Excel row: ' . $rowNumber;
                        $isValidationError = 1;
                    }

                    $checkMaterialIssue = ItemIssueMaster::where('itemIssueAutoID', '!=', $materialIssue['itemIssueAutoID'])
                        ->where('companySystemID', $materialIssue['companySystemID'])
                        ->where('wareHouseFrom', $materialIssue['wareHouseFrom'])
                        ->select([
                            'erp_itemissuemaster.itemIssueAutoID',
                            'erp_itemissuemaster.companySystemID',
                            'erp_itemissuemaster.wareHouseFromCode',
                            'erp_itemissuemaster.itemIssueCode',
                            'erp_itemissuemaster.approved'
                        ])
                        ->groupBy(
                            'erp_itemissuemaster.itemIssueAutoID',
                            'erp_itemissuemaster.companySystemID',
                            'erp_itemissuemaster.wareHouseFromCode',
                            'erp_itemissuemaster.itemIssueCode',
                            'erp_itemissuemaster.approved'
                        )
                        ->whereHas('details', function ($query) use ($rowData) {
                            $query->where('itemCodeSystem', $rowData['item_code']);
                        })
                        ->where('approved', 0)
                        ->first();

                    if (!empty($checkMaterialIssue)) {
                        $validationErrorMsg[] = 'There is a Material Issue pending for approval for the item you are trying to add. Please check again. for Excel row: ' . $rowNumber;
                        $isValidationError = 1;
                    }

                    $checkStockTransfer = StockTransfer::where('companySystemID', $materialIssue['companySystemID'])
                        ->where('locationFrom', $materialIssue['wareHouseFrom'])
                        ->select([
                            'erp_stocktransfer.stockTransferAutoID',
                            'erp_stocktransfer.companySystemID',
                            'erp_stocktransfer.locationFrom',
                            'erp_stocktransfer.stockTransferCode',
                            'erp_stocktransfer.approved'
                        ])
                        ->groupBy(
                            'erp_stocktransfer.stockTransferAutoID',
                            'erp_stocktransfer.companySystemID',
                            'erp_stocktransfer.locationFrom',
                            'erp_stocktransfer.stockTransferCode',
                            'erp_stocktransfer.approved'
                        )
                        ->whereHas('details', function ($query) use ($rowData) {
                            $query->where('itemCodeSystem', $rowData['item_code']);
                        })
                        ->where('approved', 0)
                        ->first();

                    if (!empty($checkStockTransfer)) {
                        $validationErrorMsg[] = 'There is a Stock Transfer pending for approval for the item you are trying to add. Please check again. for Excel row: ' . $rowNumber;
                        $isValidationError = 1;
                    }

                    $checkInvoice = CustomerInvoiceDirect::where('companySystemID', $materialIssue['companySystemID'])
                        ->select([
                            'erp_custinvoicedirect.custInvoiceDirectAutoID',
                            'erp_custinvoicedirect.bookingInvCode',
                            'erp_custinvoicedirect.wareHouseSystemCode',
                            'erp_custinvoicedirect.approved'
                        ])
                        ->groupBy(
                            'erp_custinvoicedirect.custInvoiceDirectAutoID',
                            'erp_custinvoicedirect.companySystemID',
                            'erp_custinvoicedirect.bookingInvCode',
                            'erp_custinvoicedirect.wareHouseSystemCode',
                            'erp_custinvoicedirect.approved'
                        )
                        ->whereHas('issue_item_details', function ($query) use ($rowData) {
                            $query->where('itemCodeSystem', $rowData['item_code']);
                        })
                        ->where('approved', 0)
                        ->where('canceledYN', 0)
                        ->first();

                    if (!empty($checkInvoice)) {
                        $validationErrorMsg[] = 'There is a Customer Invoice pending for approval for the item you are trying to add. Please check again. for Excel row: ' . $rowNumber;
                        $isValidationError = 1;
                    }

                    $checkDeliveryOrder = DeliveryOrder::where('companySystemID', $materialIssue['companySystemID'])
                        ->select([
                            'erp_delivery_order.deliveryOrderID',
                            'erp_delivery_order.deliveryOrderCode'
                        ])
                        ->groupBy(
                            'erp_delivery_order.deliveryOrderID',
                            'erp_delivery_order.companySystemID'
                        )
                        ->whereHas('detail', function ($query) use ($rowData) {
                            $query->where('itemCodeSystem', $rowData['item_code']);
                        })
                        ->where('approvedYN', 0)
                        ->first();

                    if (!empty($checkDeliveryOrder)) {
                        $validationErrorMsg[] = 'There is a Delivery Order pending for approval for the item you are trying to add. Please check again. for Excel row: ' . $rowNumber;
                        $isValidationError = 1;
                    }

                    $checkPurchaseReturn = PurchaseReturn::where('companySystemID', $materialIssue['companySystemID'])
                        ->select([
                            'erp_purchasereturnmaster.purhaseReturnAutoID',
                            'erp_purchasereturnmaster.companySystemID',
                            'erp_purchasereturnmaster.purchaseReturnLocation',
                            'erp_purchasereturnmaster.purchaseReturnCode',
                        ])
                        ->groupBy(
                            'erp_purchasereturnmaster.purhaseReturnAutoID',
                            'erp_purchasereturnmaster.companySystemID',
                            'erp_purchasereturnmaster.purchaseReturnLocation'
                        )
                        ->whereHas('details', function ($query) use ($rowData) {
                            $query->where('itemCode', $rowData['item_code']);
                        })
                        ->where('approved', 0)
                        ->first();

                    if (!empty($checkPurchaseReturn)) {
                        $validationErrorMsg[] = 'There is a Purchase Return pending for approval for the item you are trying to add. Please check again. for Excel row: ' . $rowNumber;
                        $isValidationError = 1;
                    }
                }
                else {
                    $validationErrorMsg[] = 'The item code does not match with a system for Excel row: ' . $rowNumber;
                    $isValidationError = 1;
                }
            }
            else {
                $validationErrorMsg[] = 'The item code has not been updated for Excel row: ' . $rowNumber;
                $isValidationError = 1;
            }

            if (!array_key_exists('item_description',$rowData) && is_null($rowData['item_description'])) {
                $validationErrorMsg[] = 'The item description has not been updated for Excel row: ' . $rowNumber;
                $isValidationError = 1;
            }

            if (array_key_exists('project',$rowData) && $rowData['project'] !== null) {
                $projectId = ErpProjectMaster::where('projectCode', trim($rowData['project']))->first();
                if (!$projectId) {
                    $validationErrorMsg[] = 'The Project Code not match with system for Excel row: ' . $rowNumber;
                    $isValidationError = 1;
                }
            }

            if (!array_key_exists('qty',$rowData) || is_null($rowData['qty'])) {
                $validationErrorMsg[] = 'The item Qty has not been updated for Excel row: ' . $rowNumber;
                $isValidationError = 1;
            }
            else if (!is_numeric($rowData['qty'])) {
                $validationErrorMsg[] = 'The quantity should be a numeric value for Excel row: ' . $rowNumber;
                $isValidationError = 1;
            }
            else if ($rowData['qty'] < 0) {
                $validationErrorMsg[] = 'The quantity should be a positive value for Excel row: ' . $rowNumber;
                $isValidationError = 1;
            }

            if($isValidationError == 0) {

                $item['itemIssueAutoID'] = $materialIssue['itemIssueAutoID'];
                $item['itemIssueCode'] = $materialIssue['itemIssueCode'];
                $item['p1'] =  $materialIssue['purchaseOrderNo'];
                $item['comments'] = $rowData['comment'] ?? null;

                $company = Company::where('companySystemID', $materialIssue['companySystemID'])->first();
                if($company) {
                    $item['localCurrencyID'] = $company->localCurrencyID;
                    $item['reportingCurrencyID'] = $company->reportingCurrency;
                }

                $item['clientReferenceNumber'] = NULL;
                $item['selectedForBillingOP'] = 0;
                $item['selectedForBillingOPtemp'] = 0;
                $item['opTicketNo'] = 0;

                $itemData = ItemAssigned::where('itemPrimaryCode', $rowData['item_code'])
                    ->where('companySystemID', $materialIssue['companySystemID'])
                    ->first();

                if($itemData) {
                    $item['itemCodeSystem'] = $itemData->itemCodeSystem;
                    $item['itemPrimaryCode'] = $itemData->itemPrimaryCode;
                    $item['itemUnitOfMeasure'] = $itemData->itemUnitOfMeasure;
                    $item['unitOfMeasureIssued'] = $itemData->itemUnitOfMeasure;

                    if ($itemData->maximunQty) {
                        $item['maxQty'] = $itemData->maximunQty;
                    } else {
                        $item['maxQty'] = 0;
                    }

                    if ($itemData->minimumQty) {
                        $item['minQty'] = $itemData->minimumQty;
                    } else {
                        $item['minQty'] = 0;
                    }

                    $item['itemFinanceCategoryID'] = $itemData->financeCategoryMaster;
                    $item['itemFinanceCategorySubID'] = $itemData->financeCategorySub;

                    $item['trackingType'] = $itemData->trackingType ?? null;

                    $item['itemDescription'] = $rowData['item_description'];

                    $item['convertionMeasureVal'] = 1;
                    $item['qtyRequested'] = 0;
                    $item['qtyIssued'] = $rowData['qty'];
                    $item['qtyIssuedDefaultMeasure'] = $rowData['qty'];

                    $mfq_no = $materialIssue['mfqJobID'];

                    $data = array(
                        'companySystemID' => $materialIssue['companySystemID'],
                        'itemCodeSystem' => $itemData['itemCodeSystem'] ?: null,
                        'wareHouseId' => $materialIssue['wareHouseFrom']
                    );

                    $itemCurrentCostAndQty = Inventory::itemCurrentCostAndQty($data);

                    $item['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
                    $item['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                    $item['currentStockQtyInDamageReturn'] = $itemCurrentCostAndQty['currentStockQtyInDamageReturn'];
                    $item['issueCostLocal'] = $itemCurrentCostAndQty['wacValueLocal'];
                    $item['issueCostRpt'] = $itemCurrentCostAndQty['wacValueReporting'];
                    $item['issueCostLocalTotal'] = $item['issueCostLocal'] * $item['qtyIssuedDefaultMeasure'];
                    $item['issueCostRptTotal'] = $item['issueCostRpt'] * $item['qtyIssuedDefaultMeasure'];

                    $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $materialIssue['companySystemID'])
                        ->where('mainItemCategoryID', $item['itemFinanceCategoryID'])
                        ->where('itemCategorySubID', $item['itemFinanceCategorySubID'])
                        ->first();

                    if ($financeItemCategorySubAssigned) {
                        if(!empty($mfq_no) && WarehouseMaster::checkManuefactoringWareHouse($materialIssue['wareHouseFrom'])) {
                            $item['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                            $item['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                            $item['financeGLcodePLSystemID'] = WarehouseMaster::getWIPGLSystemID($materialIssue['wareHouseFrom']);
                            $item['financeGLcodePL'] = WarehouseMaster::getWIPGLCode($materialIssue['wareHouseFrom']);
                        }
                        else {
                            $item['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                            $item['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                            $item['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                            $item['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                        }

                        $input['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;
                    }

                    if ($materialIssue['customerSystemID'] && $materialIssue['companySystemID'] && $materialIssue['contractUIID']) {
                        $clientReferenceNumber = ItemClientReferenceNumberMaster::where('companySystemID', $materialIssue['companySystemID'])
                            ->where('itemSystemCode', $item['itemCodeSystem'])
                            ->where('customerID', $materialIssue['customerSystemID'])
                            ->where('contractUIID', $materialIssue['contractUIID'])
                            ->first();

                        if (!empty($clientReferenceNumber)) {
                            $item['clientReferenceNumber'] = $clientReferenceNumber->clientReferenceNumber;
                        }
                    }

                    $projectId = ErpProjectMaster::where('projectCode', trim($rowData['project']))->first();
                    if($projectId) {
                        $item['detail_project_id'] = $projectId->id;
                    }

                    array_push($validatedItemsArray,$item);

                    $successCount += 1;
                }
            }
            $rowNumber++;
            $excelRowCount++;
        }

        $data = [
            'itemDetails' => $validatedItemsArray,
            'errorLog' => $validationErrorMsg,
            'successCount' => $successCount,
            'excelRowCount' => $excelRowCount
        ];

        return $data;
    }

    public static function errorLogUpdate($errorData, $documentSystemId)
    {
        foreach ($errorData as $details) {
            $insertError = [
                'documentSystemID' => $documentSystemId,
                'error' => $details
            ];

            MiBulkUploadErrorLog::create($insertError);
        }
    }

}
