<?php

namespace App\Services\Sales;

use App\helper\inventory;
use App\helper\TaxService;
use App\Models\AssetFinanceCategory;
use App\Models\CustomerInvoiceDirect;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderDetail;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\ItemAssigned;
use App\Models\ItemIssueMaster;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseReturn;
use App\Models\StockTransfer;

class DeliveryOrderService
{
    public static function validatePoItem($itemCode, $companySystemID, $deliveryOrderID)
    {
        $deliveryOrderMaster = DeliveryOrder::find($deliveryOrderID);

        $item = ItemAssigned::where('itemCodeSystem', $itemCode)
            ->where('companySystemID', $companySystemID)
            ->first();

        if(empty($deliveryOrderMaster)){
            return ['status' =>  false, 'message' => 'Delivery order not found',500];
        }

        $alreadyAdded = DeliveryOrder::where('deliveryOrderID', $deliveryOrderID)
            ->whereHas('detail', function ($query) use ($itemCode) {
                $query->where('itemCodeSystem', $itemCode);
            })
            ->exists();

        if ($alreadyAdded) {
            return ['status' =>  false, 'message' => "Selected item is already added. Please check again", 500];
        }

        $data = array(
            'companySystemID' => $companySystemID,
            'itemCodeSystem' => $item['itemCodeSystem'],
            'wareHouseId' => $deliveryOrderMaster->wareHouseSystemCode
        );

        $itemCurrentCostAndQty  = inventory::itemCurrentCostAndQty($data);

        if(isset($itemCurrentCostAndQty['currentWareHouseStockQty']) && ($itemCurrentCostAndQty['currentWareHouseStockQty'] <= 0))
        {
            return ['status' => false , 'message' => 'Stock Qty is 0. You cannot issue.'];
        }


        if($item->financeCategoryMaster==1){
            if ($itemCurrentCostAndQty['currentStockQty'] <= 0) {
                return ['status'=> false ,  'message' => "Stock Qty is 0. You cannot issue."];
            }

            if ($itemCurrentCostAndQty['currentWareHouseStockQty'] <= 0) {
                return ['status'=> false ,  'message' => "Warehouse stock Qty is 0. You cannot issue."];
            }

            if ($itemCurrentCostAndQty['wacValueLocal'] == 0 || $itemCurrentCostAndQty['wacValueReporting'] == 0) {
                return ['status'=> false ,  'message' => "Cost is 0. You cannot issue."];
            }

            if ($itemCurrentCostAndQty['wacValueLocal'] < 0 || $itemCurrentCostAndQty['wacValueReporting'] < 0) {
                return ['status'=> false ,  'message' => "Cost is negative. You cannot issue."];
            }
        }



        if(DeliveryOrderDetail::where('deliveryOrderID',$deliveryOrderID)->where('itemFinanceCategoryID','!=',$item->financeCategoryMaster)->exists()){
            return ['status' =>  false, 'message' => 'Different finance category found. You can not add different finance category items for same order',500];
        }

        if($item->financeCategoryMaster==1){
            // check the item pending pending for approval in other delivery orders

            $checkWhether = DeliveryOrder::where('deliveryOrderID', '!=', $deliveryOrderMaster->deliveryOrderID)
                ->where('companySystemID', $companySystemID)
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
            if (!empty($checkWhether)) {
                return ['status' =>  false, 'message' => "There is a Delivery Order (" . $checkWhether->deliveryOrderCode . ") pending for approval for the item you are trying to add. Please check again.", 500];
            }


            // check the item pending pending for approval in other modules
            $checkWhetherItemIssueMaster = ItemIssueMaster::where('companySystemID', $companySystemID)
//            ->where('wareHouseFrom', $customerInvoiceDirect->wareHouseSystemCode)
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

            if (!empty($checkWhetherItemIssueMaster)) {
                return ['status' =>  false, 'message' => "There is a Materiel Issue (" . $checkWhetherItemIssueMaster->itemIssueCode . ") pending for approval for the item you are trying to add. Please check again.", 500];
            }

            $checkWhetherStockTransfer = StockTransfer::where('companySystemID', $companySystemID)
//            ->where('locationFrom', $customerInvoiceDirect->wareHouseSystemCode)
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
                return ['status' =>  false, 'message' => "There is a Stock Transfer (" . $checkWhetherStockTransfer->stockTransferCode . ") pending for approval for the item you are trying to add. Please check again.", 500];
            }

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
                return ['status' =>  false, 'message' => "There is a Customer Invoice (" . $checkWhetherInvoice->bookingInvCode . ") pending for approval for the item you are trying to add. Please check again.", 500];
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

            if (!empty($checkWhetherPR)) {
                return ['status' =>  false, 'message' => "There is a Purchase Return (" . $checkWhetherPR->purchaseReturnCode . ") pending for approval for the item you are trying to add. Please check again.", 500];
            }


            $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
                ->where('mainItemCategoryID', $item->financeCategoryMaster)
                ->where('itemCategorySubID', $item->financeCategorySub)
                ->first();


            if(empty($financeItemCategorySubAssigned))
            {
                return ['success'=> false , 'messsage' => "Finance Item category sub assigned not found"];
            }

            if((!$financeItemCategorySubAssigned['financeGLcodebBS'] || !$financeItemCategorySubAssigned['financeGLcodebBSSystemID']) && $item->financeCategoryMaster!=2){
                return ['status' =>false , 'message' => 'BS account cannot be null for ' . $item->itemPrimaryCode . '-' . $item->itemDescription];
            }elseif (!$financeItemCategorySubAssigned['financeGLcodePL'] || !$financeItemCategorySubAssigned['financeGLcodePLSystemID']){
                return ['status' =>false , 'message' => 'Cost account cannot be null for ' . $item->itemPrimaryCode . '-' . $item->itemDescription];
            }elseif (!$financeItemCategorySubAssigned['financeCogsGLcodePL'] || !$financeItemCategorySubAssigned['financeCogsGLcodePLSystemID']){
                return ['status' =>false , 'message' => 'COGS gl account cannot be null for ' . $item->itemPrimaryCode . '-' . $item->itemDescription];
            }elseif (!$financeItemCategorySubAssigned['financeGLcodeRevenueSystemID'] || !$financeItemCategorySubAssigned['financeGLcodeRevenue']){
                return ['status' =>false , 'message' => 'Revenue account cannot be null for ' . $item->itemPrimaryCode . '-' . $item->itemDescription];
            }

        }

        return ['status' => true];
    }

    public static function savePoItem($itemCode, $companySystemID, $deliveryOrderID, $empID, $employeeSystemID)
    {
        $deliveryOrder = DeliveryOrder::where('deliveryOrderID', $deliveryOrderID)->first();

        $itemData = [];
        $itemData['qtyIssued'] = 0;
        $itemData['clientReferenceNumber'] = null;
        $itemData['comment'] = null;
        $itemData['companySystemID'] = $companySystemID;
        $itemData['discountAmount'] = 0;
        $itemData['discountPercentage'] = 0;
        $itemData['netAmount'] = 0;
        $itemData['itemCategoryID'] = null;
        $itemData['supplierPartNumber'] = null;

        $item = ItemAssigned::where('itemCodeSystem', $itemCode)
            ->where('companySystemID', $companySystemID)
            ->first();

        $itemData['unitTransactionAmount'] = $item->wacValueLocal;
        $itemAssigned = ItemAssigned::where('itemCodeSystem',$item->itemCodeSystem)->where('companySystemID',$companySystemID)->first();
        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $item->companySystemID)
            ->where('mainItemCategoryID', $itemAssigned->financeCategoryMaster)
            ->where('itemCategorySubID', $itemAssigned->financeCategorySub)
            ->first();

        $itemData['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
        $itemData['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
        if ($item->financeCategoryMaster == 3) {
            $assetCategory = AssetFinanceCategory::find($item->faFinanceCatID);
            $itemData['financeGLcodePLSystemID'] = $assetCategory->COSTGLCODESystemID;
            $itemData['financeGLcodePL'] = $assetCategory->COSTGLCODE;
        } else {
            $itemData['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
            $itemData['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
        }
        $itemData['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;
        $itemData['budgetYear'] = $deliveryOrder->budgetYear;

        $currencyConversion = \Helper::currencyConversion($item->companySystemID, $item->wacValueLocalCurrencyID, $deliveryOrder->supplierTransactionCurrencyID, $item->wacValueLocal);

        $itemData['unitCost'] =  \Helper::roundValue($currencyConversion['documentAmount']);

        $itemData['localCurrencyID'] = $deliveryOrder->localCurrencyID;
        $itemData['localCurrencyER'] = $deliveryOrder->localCurrencyER;

        $itemData['supplierItemCurrencyID'] = $deliveryOrder->supplierTransactionCurrencyID;
        $itemData['foreignToLocalER'] = $deliveryOrder->supplierTransactionER;

        $itemData['companyReportingCurrencyID'] = $deliveryOrder->companyReportingCurrencyID;
        $itemData['companyReportingER'] = $deliveryOrder->companyReportingER;

        $itemData['supplierDefaultCurrencyID'] = $deliveryOrder->supplierDefaultCurrencyID;
        $itemData['supplierDefaultER'] = $deliveryOrder->supplierDefaultER;
        $itemData['VATAmount'] = 0;
        if ($deliveryOrder->isVatEligible) {
            $vatDetails = TaxService::getVATDetailsByItem($deliveryOrder->companySystemID, $itemCode, $deliveryOrder->supplierID);
            $itemData['VATPercentage'] = $vatDetails['percentage'];
            $itemData['VATApplicableOn'] = $vatDetails['applicableOn'];
            $itemData['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
            $itemData['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
            $itemData['VATAmount'] = 0;
            if ($itemData['unitCost'] > 0) {
                $itemData['VATAmount'] = (($itemData['unitCost'] / 100) * $vatDetails['percentage']);
            }
            $currencyConversionVAT = \Helper::currencyConversion($deliveryOrder->companySystemID, $deliveryOrder->supplierTransactionCurrencyID, $deliveryOrder->supplierTransactionCurrencyID, 0);

            $itemData['VATAmount'] = 0;
            $itemData['VATAmountLocal'] = 0;
            $itemData['VATAmountRpt'] = 0;

        }

        $grvCost = $itemData['unitCost'];

        if ($grvCost > 0) {
            $currencyConversion = \Helper::currencyConversion($companySystemID, $deliveryOrder->supplierTransactionCurrencyID, $deliveryOrder->supplierTransactionCurrencyID, $grvCost);

            $itemData['GRVcostPerUnitLocalCur'] = \Helper::roundValue($currencyConversion['localAmount']);
            $itemData['GRVcostPerUnitSupTransCur'] = $grvCost;
            $itemData['GRVcostPerUnitComRptCur'] = \Helper::roundValue($currencyConversion['reportingAmount']);

            $itemData['purchaseRetcostPerUnitLocalCur'] = \Helper::roundValue($currencyConversion['localAmount']);
            $itemData['purchaseRetcostPerUnitTranCur'] = $itemData['unitCost'];
            $itemData['purchaseRetcostPerUnitRptCur'] = \Helper::roundValue($currencyConversion['reportingAmount']);
        }

        // adding supplier Default CurrencyID base currency conversion
        if ($grvCost > 0) {
            $currencyConversionDefault = \Helper::currencyConversion($companySystemID, $deliveryOrder->supplierTransactionCurrencyID, $deliveryOrder->supplierDefaultCurrencyID, $grvCost);
            $itemData['GRVcostPerUnitSupDefaultCur'] = \Helper::roundValue($currencyConversionDefault['documentAmount']);
            $itemData['purchaseRetcostPerUniSupDefaultCur'] = \Helper::roundValue($currencyConversionDefault['documentAmount']);
        }

        $itemData['deliveryOrderID'] = $deliveryOrderID;
        $itemData['itemCodeSystem'] = $item->itemCodeSystem;
        $itemData['itemPrimaryCode'] = $item->itemPrimaryCode;
        $itemData['supplierPartNumber'] = $item->secondaryItemCode;
        $itemData['itemDescription'] = $item->itemDescription;
        $itemData['itemUnitOfMeasure'] = $item->itemUnitOfMeasure;
        $itemData['unitOfMeasureIssued'] = $item->itemUnitOfMeasure;
        $itemData['itemFinanceCategoryID'] = $item->financeCategoryMaster;
        $itemData['itemFinanceCategorySubID'] = $item->financeCategorySub;

        $itemData['serviceLineSystemID'] = $deliveryOrder->serviceLineSystemID;
        $itemData['serviceLineCode'] = $deliveryOrder->serviceLine;
        $itemData['companySystemID'] = $item->companySystemID;
        $itemData['companyID'] =  \Helper::getCompanyById($item->companySystemID);

        $itemData['createdPcID'] = gethostname();
        $itemData['createdUserID'] = $empID;
        $itemData['createdUserSystemID'] = $employeeSystemID;
        $itemData['documentSystemID'] = $deliveryOrder->documentSystemID;


        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $itemData['companySystemID'])
            ->where('mainItemCategoryID', $itemData['itemFinanceCategoryID'])
            ->where('itemCategorySubID', $itemData['itemFinanceCategorySubID'])
            ->first();

        if (!empty($financeItemCategorySubAssigned)) {
            $itemData['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
            $itemData['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
            $itemData['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
            $itemData['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
            $itemData['financeCogsGLcodePL'] = $financeItemCategorySubAssigned->financeCogsGLcodePL;
            $itemData['financeCogsGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeCogsGLcodePLSystemID;
            $itemData['financeGLcodeRevenueSystemID'] = $financeItemCategorySubAssigned->financeGLcodeRevenueSystemID;
            $itemData['financeGLcodeRevenue'] = $financeItemCategorySubAssigned->financeGLcodeRevenue;
        }

        $data = array(
            'companySystemID' => $companySystemID,
            'itemCodeSystem' => $item['itemCodeSystem'],
            'wareHouseId' => $deliveryOrder->wareHouseSystemCode
        );

        $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);

        $itemData['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
        $itemData['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
        $itemData['currentStockQtyInDamageReturn'] = $itemCurrentCostAndQty['currentStockQtyInDamageReturn'];
        $itemData['wacValueLocal'] = $itemCurrentCostAndQty['wacValueLocal'];
        $itemData['wacValueReporting'] = $itemCurrentCostAndQty['wacValueReporting'];

        $deliveryOrderDetail = DeliveryOrderDetail::create($itemData);
    }

}
