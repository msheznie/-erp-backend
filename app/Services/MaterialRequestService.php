<?php

namespace App\Services;

use App\Models\ItemCategoryTypeMaster;
use App\Models\ProcumentOrder;
use App\Models\ItemAssigned;
use App\Models\PurchaseOrderDetails;
use App\Models\ProcumentOrderDetail;
use App\Models\CompanyPolicyMaster;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\SupplierAssigned;
use App\Models\User;
use App\Models\AssetFinanceCategory;
use Illuminate\Support\Facades\DB;
use App\helper\Helper;
use Response;
use Illuminate\Support\Facades\Auth;
use App\helper\TaxService;
use App\Models\Company;
use App\Models\CustomerInvoiceDirect;
use App\Models\DeliveryOrder;
use App\Models\ErpItemLedger;
use App\Models\GRVDetails;
use App\Models\ItemClientReferenceNumberMaster;
use App\Models\ItemIssueDetails;
use App\Models\ItemIssueMaster;
use App\Models\ItemMaster;
use App\Models\MaterielRequest;
use App\Models\MaterielRequestDetails;
use App\Models\PurchaseReturn;
use App\Models\StockTransfer;
use App\Models\WarehouseMaster;
use App\Repositories\MaterielRequestDetailsRepository;

class MaterialRequestService
{
    public static function validateMaterialRequestItem($itemCode, $companySystemID, $RequestID)
    {

        $allowItemToTypePolicy = false;
        $itemNotound = false;
        $allowItemToType = CompanyPolicyMaster::where('companyPolicyCategoryID', 54)
                                            ->where('companySystemID', $companySystemID)
                                            ->first();

        if ($allowItemToType) {
            $allowItemToTypePolicy = true;
        }


        $item = ItemAssigned::where('itemCodeSystem', $itemCode)
            ->where('companySystemID', $companySystemID)
            ->where('isAssigned', '=', -1)
            ->whereHas('item_category_type', function ($q) {
                $q->whereIn('categoryTypeID',ItemCategoryTypeMaster::purchaseItems());
            })
            ->first();

        if (empty($item)) {
            if (!$allowItemToTypePolicy) {
                return ['status' => false, 'message' => 'Item not found'];
            } else {
                $itemNotound = true;
            }
        }

        $materielRequest = MaterielRequest::where('RequestID', $RequestID)->first();


        if (empty($materielRequest)) {
            return ['status' => false, 'message' => 'Materiel Request Details not found'];

        }

        if($materielRequest->ClosedYN == -1){
                return ['status' => false, 'message' => 'This Materiel Request already closed. You can not add.'];
        }

        if($materielRequest->approved == -1){
                return ['status' => false, 'message' => 'This Materiel Request fully approved. You can not add.'];
        }


        
        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $item->companySystemID)
        ->where('mainItemCategoryID', $item->financeCategoryMaster)
        ->where('itemCategorySubID', $item->financeCategorySub)
        ->first();

        if (empty($financeItemCategorySubAssigned)) {
                return ['status' => false, 'message' => 'Finance Category not found'];
        }

        if ($item->financeCategoryMaster == 1) {

            $alreadyAdded = MaterielRequest::where('RequestID', $RequestID)
                ->whereHas('details', function ($query) use ($item) {
                    $query->where('itemCode', $item->itemCodeSystem);
                })
                ->first();

            if ($alreadyAdded) {
                return ['status' => false, 'message' => 'Selected item is already added. Please check again'];

            }
        }

        return ['status' => true];

    }

    public static function saveMaterialRequestItem($itemCode, $companySystemID, $RequestID, $empID, $employeeSystemID)
    {
        $allowItemToTypePolicy = false;
        $itemNotound = false;
        $allowItemToType = CompanyPolicyMaster::where('companyPolicyCategoryID', 54)
                                            ->where('companySystemID', $companySystemID)
                                            ->first();

        if ($allowItemToType) {
            $allowItemToTypePolicy = true;
        }


        if ($allowItemToTypePolicy) {
            $input['itemCode'] = isset($input['itemCode']['id']) ? $input['itemCode']['id'] : $itemCode;
        }

        $item = ItemAssigned::where('itemCodeSystem', $itemCode)
            ->where('companySystemID', $companySystemID)
            ->where('isAssigned', '=', -1)
            ->whereHas('item_category_type', function ($q) {
                $q->whereIn('categoryTypeID',ItemCategoryTypeMaster::purchaseItems());
            })
            ->first();

        if (empty($item)) {
            if (!$allowItemToTypePolicy) {
                $itemNotound = false;
            } else {
                $itemNotound = true;
            }
        }

        $materielRequest = MaterielRequest::where('RequestID', $RequestID)->first();

        $input['qtyIssuedDefaultMeasure'] = 0;
        $input['RequestID'] = $RequestID;
        if (!$itemNotound) {
            $input['itemCode'] = $item->itemCodeSystem;
            $input['itemDescription'] = $item->itemDescription;
            $input['partNumber'] = $item->secondaryItemCode;
            $input['itemFinanceCategoryID'] = $item->financeCategoryMaster;
            $input['itemFinanceCategorySubID'] = $item->financeCategorySub;
            $input['unitOfMeasure'] = $item->itemUnitOfMeasure;
            $input['unitOfMeasureIssued'] = $item->itemUnitOfMeasure;
            if($item->maximunQty){
                $input['maxQty'] = $item->maximunQty;
            }else{
                $input['maxQty'] = 0;
            }

            if($item->minimumQty){
                $input['minQty'] = $item->minimumQty;
            }else{
                $input['minQty'] = 0;
            }

            $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
            ->where('mainItemCategoryID', $item->financeCategoryMaster)
            ->where('itemCategorySubID', $item->financeCategorySub)
            ->first();

            $input['financeGLcodebBS']  = $financeItemCategorySubAssigned->financeGLcodebBS;
            $input['financeGLcodePL']   = $financeItemCategorySubAssigned->financeGLcodePL;
            $input['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;


             $poQty = PurchaseOrderDetails::whereHas('order' , function ($query) use ($companySystemID,$materielRequest) {
                                                $query->where('companySystemID', $companySystemID)
                                                    ->where('poLocation', $materielRequest->location)
                                                    ->where('approved', -1)
                                                    ->where('poCancelledYN', 0);
                                         })
                                        ->where('itemCode', $input['itemCode'])
                                        ->groupBy('erp_purchaseorderdetails.companySystemID',
                                            'erp_purchaseorderdetails.itemCode')
                                        ->select(
                                            [
                                                'erp_purchaseorderdetails.companySystemID',
                                                'erp_purchaseorderdetails.itemCode',
                                                'erp_purchaseorderdetails.itemPrimaryCode'
                                            ]
                                        )
                                        ->sum('noQty');

            $quantityInHand = ErpItemLedger::where('itemSystemCode', $input['itemCode'])
                                    ->where('companySystemID', $companySystemID)
                                    ->groupBy('itemSystemCode')
                                    ->sum('inOutQty');

            $grvQty = GRVDetails::whereHas('grv_master' , function ($query) use ($companySystemID,$materielRequest) {
                                $query->where('companySystemID', $companySystemID)
                                    ->where('grvTypeID', 2)
                                    ->groupBy('erp_grvmaster.companySystemID');
                                 })
                                ->where('itemCode', $input['itemCode'])
                                ->groupBy('erp_grvdetails.itemCode')
                                ->select(
                                    [
                                        'erp_grvdetails.companySystemID',
                                        'erp_grvdetails.itemCode'
                                    ])
                                ->sum('noQty');

            $quantityOnOrder = $poQty - $grvQty;
            $input['quantityOnOrder'] = $quantityOnOrder;
            $input['quantityInHand']  = $quantityInHand;

        } else {
            $input['itemDescription'] = $input['itemCode'];
            $input['itemCode'] = null;
            $input['partNumber'] = null;
            $input['itemFinanceCategoryID'] = null;
            $input['itemFinanceCategorySubID'] = null;
            $input['unitOfMeasure'] = null;
            $input['unitOfMeasureIssued'] = null;
            $input['maxQty'] = 0;
            $input['minQty'] = 0;
            $input['quantityOnOrder'] = 0;
            $input['quantityInHand'] = 0;

        }

        $input['estimatedCost'] = 0;
        $input['quantityRequested'] = 0;
        
        $input['ClosedYN'] = 0;
        $input['selectedForIssue'] = 0;
        $input['comments'] = null;
        $input['convertionMeasureVal'] = 1;

        $input['allowCreatePR']      = 0;
        $input['selectedToCreatePR'] = 0;


        $materielRequestDetails = MaterielRequestDetails::create($input);
    }

    public static function validateMaterialIssueItem($itemCode, $companySystemID, $itemIssueAutoID)
    { 

        $itemIssue = ItemIssueMaster::where('itemIssueAutoID', $itemIssueAutoID)->first();

        $item = ItemAssigned::where('itemCodeSystem', $itemCode)
            ->where('companySystemID', $companySystemID)
            ->where('isAssigned', '=', -1)
            ->whereHas('item_category_type', function ($q) {
                $q->whereIn('categoryTypeID',ItemCategoryTypeMaster::purchaseItems());
            })
            ->first();

        if (empty($item)) {
            return ['status' => false, 'message' => 'Item not found'];
        }

        $itemMaster = ItemMaster::find($itemCode);

        $itemIssueMaster = ItemIssueMaster::where('itemIssueAutoID', $itemIssueAutoID)->first();
        $mfq_no = $itemIssueMaster->mfqJobID;


        $data = array('companySystemID' => $companySystemID,
        'itemCodeSystem' => $itemCode,
        'wareHouseId' => $itemIssue->wareHouseFrom);

        $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);


        $input['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
        $input['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
        $input['issueCostLocal'] = $itemCurrentCostAndQty['wacValueLocal'];
        $input['issueCostRpt'] = $itemCurrentCostAndQty['wacValueReporting'];


        if ($input['currentStockQty'] <= 0) {
            return ['status' => false, 'message' => 'Stock Qty is 0. You cannot issue.'];
        }

        if ($input['currentWareHouseStockQty'] <= 0) {
            return ['status' => false, 'message' => 'Warehouse stock Qty is 0. You cannot issue'];
        }


        if ($input['issueCostLocal'] < 0 || $input['issueCostRpt'] < 0) {
            return ['status' => false, 'message' => 'Cost is negative. You cannot issue.'];
        }

        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
        ->where('mainItemCategoryID', $item->financeCategoryMaster)
        ->where('itemCategorySubID', $item->financeCategorySub)
        ->first();

        if (!empty($financeItemCategorySubAssigned)) {


            if(!empty($mfq_no) && WarehouseMaster::checkManuefactoringWareHouse($itemIssueMaster->wareHouseFrom))
            {
                $input['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                $input['financeGLcodePLSystemID'] = WarehouseMaster::getWIPGLSystemID($itemIssueMaster->wareHouseFrom);
                $input['financeGLcodePL'] = WarehouseMaster::getWIPGLCode($itemIssueMaster->wareHouseFrom);

            }   
            else
            {
                
                $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                $input['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                $input['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                $input['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
            }

            $input['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;

        } else {
            return ['status' => false, 'message' => 'Account code not updated.'];
        }

        if (!$input['financeGLcodebBS'] || !$input['financeGLcodebBSSystemID'] || !$input['financeGLcodePL'] || !$input['financeGLcodePLSystemID']) {
            return ['status' => false, 'message' => 'Account code not updated.'];
        }

        if ($item->financeCategoryMaster == 1) {
            $alreadyAdded = ItemIssueMaster::where('itemIssueAutoID', $itemIssueAutoID)
                ->whereHas('details', function ($query) use ($itemCode) {
                    $query->where('itemCodeSystem', $itemCode);
                })
                ->first();

            if ($alreadyAdded) {
            return ['status' => false, 'message' => 'Selected item is already added. Please check again'];
            }
        }


                // check policy 18

                $allowPendingApproval = CompanyPolicyMaster::where('companyPolicyCategoryID', 18)
                ->where('companySystemID', $companySystemID)
                ->first();
    
            $checkWhether = ItemIssueMaster::where('itemIssueAutoID', '!=', $itemIssueMaster->itemIssueAutoID)
                ->where('companySystemID', $companySystemID)
                ->where('wareHouseFrom', $itemIssueMaster->wareHouseFrom)
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
                ->whereHas('details', function ($query) use ($companySystemID, $itemCode) {
                    $query->where('itemCodeSystem', $itemCode);
                })
                ->where('approved', 0)
                ->first();
            /* approved=0*/
    
            if (!empty($checkWhether)) {
                return ['status' => false, 'message' => "There is a Materiel Issue (" . $checkWhether->itemIssueCode . ") pending for approval for the item you are trying to add. Please check again."];
            }
    
            $checkWhetherStockTransfer = StockTransfer::where('companySystemID', $companySystemID)
                ->where('locationFrom', $itemIssueMaster->wareHouseFrom)
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
                ->whereHas('details', function ($query) use ($companySystemID, $itemCode) {
                    $query->where('itemCodeSystem', $itemCode);
                })
                ->where('approved', 0)
                ->first();
            /* approved=0*/
    
            if (!empty($checkWhetherStockTransfer)) {
                return ['status' => false, 'message' => "There is a Stock Transfer (" . $checkWhetherStockTransfer->stockTransferCode . ") pending for approval for the item you are trying to add. Please check again."];
            }
    
            /*check item sales invoice*/
            $checkWhetherInvoice = CustomerInvoiceDirect::where('companySystemID', $companySystemID)
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
                ->whereHas('issue_item_details', function ($query) use ($companySystemID, $itemCode) {
                    $query->where('itemCodeSystem', $itemCode);
                })
                ->where('approved', 0)
                ->where('canceledYN', 0)
                ->first();
            /* approved=0*/
    
            if (!empty($checkWhetherInvoice)) {
                return ['status' => false, 'message' => "There is a Customer Invoice (" . $checkWhetherInvoice->bookingInvCode . ") pending for approval for the item you are trying to add. Please check again."];
            }
    
            // check in delivery order
            $checkWhetherDeliveryOrder = DeliveryOrder::where('companySystemID', $companySystemID)
                ->select([
                    'erp_delivery_order.deliveryOrderID',
                    'erp_delivery_order.deliveryOrderCode'
                ])
                ->groupBy(
                    'erp_delivery_order.deliveryOrderID',
                    'erp_delivery_order.companySystemID'
                )
                ->whereHas('detail', function ($query) use ($companySystemID, $itemCode) {
                    $query->where('itemCodeSystem', $itemCode);
                })
                ->where('approvedYN', 0)
                ->first();
    
            if (!empty($checkWhetherDeliveryOrder)) {
                return ['status' => false, 'message' => "There is a Delivery Order (" . $checkWhetherDeliveryOrder->deliveryOrderCode . ") pending for approval for the item you are trying to add. Please check again."];
            }
    
            /*Check in purchase return*/
            $checkWhetherPR = PurchaseReturn::where('companySystemID', $companySystemID)
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
                ->whereHas('details', function ($query) use ($itemCode) {
                    $query->where('itemCode', $itemCode);
                })
                ->where('approved', 0)
                ->first();
            /* approved=0*/
    
            if (!empty($checkWhetherPR)) {
                return ['status' => false, 'message' => "There is a Purchase Return (" . $checkWhetherPR->purchaseReturnCode . ") pending for approval for the item you are trying to add. Please check again."];
            }
    
    
            if ($itemIssue->customerSystemID && $itemIssue->companySystemID && $itemIssue->contractUIID) {
    
                $clientReferenceNumber = ItemClientReferenceNumberMaster::where('companySystemID', $itemIssue->companySystemID)
                    ->where('itemSystemCode', $itemCode)
                    ->where('customerID', $itemIssue->customerSystemID)
                    ->where('contractUIID', $itemIssue->contractUIID)
                    ->first();
    
                if (!empty($clientReferenceNumber)) {
                    $input['clientReferenceNumber'] = $clientReferenceNumber->clientReferenceNumber;
                }
            }

        return ['status' => true];

    }

    public static function saveMaterialIssueItem($itemCode, $companySystemID, $itemIssueAutoID, $empID, $employeeSystemID)
    { 
        $itemIssue = ItemIssueMaster::where('itemIssueAutoID', $itemIssueAutoID)->first();

        $item = ItemAssigned::where('itemCodeSystem', $itemCode)
            ->where('isAssigned', '=', -1)
            ->whereHas('item_category_type', function ($q) {
                $q->whereIn('categoryTypeID',ItemCategoryTypeMaster::purchaseItems());
            })
            ->where('companySystemID', $companySystemID)
            ->first();

        $itemIssueMaster = ItemIssueMaster::where('itemIssueAutoID', $itemIssueAutoID)->first();
        $company = Company::where('companySystemID', $companySystemID)->first();

        $input['itemIssueCode'] = $itemIssueMaster->itemIssueCode;
        $input['p1'] =  $itemIssueMaster->purchaseOrderNo;
        $input['comments'] = null;
        $input['localCurrencyID'] = $company->localCurrencyID;
        $input['reportingCurrencyID'] = $company->reportingCurrency;
        $input['clientReferenceNumber'] = NULL;
        $input['selectedForBillingOP'] = 0;
        $input['selectedForBillingOPtemp'] = 0;
        $input['opTicketNo'] = 0;
        $input['issueCostRpt'] = 0;

        $input['itemCodeSystem'] = $itemCode;
        $input['itemIssueAutoID'] = $itemIssueAutoID;
        $input['itemPrimaryCode'] = $item->itemPrimaryCode;
        $input['itemDescription'] = $item->itemDescription;
        $input['itemUnitOfMeasure'] = $item->itemUnitOfMeasure;
        $input['unitOfMeasureIssued'] = $item->itemUnitOfMeasure;

        if ($item->maximunQty) {
            $input['maxQty'] = $item->maximunQty;
        } else {
            $input['maxQty'] = 0;
        }

        if ($item->minimumQty) {
            $input['minQty'] = $item->minimumQty;
        } else {
            $input['minQty'] = 0;
        }

        $input['itemFinanceCategoryID'] = $item->financeCategoryMaster;
        $input['itemFinanceCategorySubID'] = $item->financeCategorySub;

        $input['convertionMeasureVal'] = 1;
        $input['qtyRequested'] = 0;
        $input['qtyIssued'] = 0;
        $input['qtyIssuedDefaultMeasure'] = 0;

        $itemMaster = ItemMaster::find($itemCode);
        
        $mfq_no = $itemIssueMaster->mfqJobID;

        $input['trackingType'] = (isset($itemMaster->trackingType)) ? $itemMaster->trackingType : null;

        $data = array('companySystemID' => $companySystemID,
        'itemCodeSystem' => $itemCode,
        'wareHouseId' => $itemIssue->wareHouseFrom);

        $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);


        $input['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
        $input['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
        $input['currentStockQtyInDamageReturn'] = $itemCurrentCostAndQty['currentStockQtyInDamageReturn'];
        $input['issueCostLocal'] = $itemCurrentCostAndQty['wacValueLocal'];
        $input['issueCostRpt'] = $itemCurrentCostAndQty['wacValueReporting'];
        $input['issueCostLocalTotal'] = $input['issueCostLocal'] * $input['qtyIssuedDefaultMeasure'];
        $input['issueCostRptTotal'] = $input['issueCostRpt'] * $input['qtyIssuedDefaultMeasure'];
        
        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
            ->where('mainItemCategoryID', $input['itemFinanceCategoryID'])
            ->where('itemCategorySubID', $input['itemFinanceCategorySubID'])
            ->first();

        if(!empty($mfq_no) && WarehouseMaster::checkManuefactoringWareHouse($itemIssueMaster->wareHouseFrom))
        {
            $input['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
            $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
            $input['financeGLcodePLSystemID'] = WarehouseMaster::getWIPGLSystemID($itemIssueMaster->wareHouseFrom);
            $input['financeGLcodePL'] = WarehouseMaster::getWIPGLCode($itemIssueMaster->wareHouseFrom);

        }   
        else
        {
            
            $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
            $input['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
            $input['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
            $input['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
        }

        $input['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;

        if ($itemIssue->customerSystemID && $itemIssue->companySystemID && $itemIssue->contractUIID) {

            $clientReferenceNumber = ItemClientReferenceNumberMaster::where('companySystemID', $itemIssue->companySystemID)
                ->where('itemSystemCode', $itemCode)
                ->where('customerID', $itemIssue->customerSystemID)
                ->where('contractUIID', $itemIssue->contractUIID)
                ->first();

            if (!empty($clientReferenceNumber)) {
                $input['clientReferenceNumber'] = $clientReferenceNumber->clientReferenceNumber;
            }
        }

        $materielIssueDetails = ItemIssueDetails::create($input);

    }

}
