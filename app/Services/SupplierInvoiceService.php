<?php

namespace App\Services;

use App\helper\TaxService;
use App\Models\BookInvSuppMaster;
use App\Models\FinanceItemCategorySub;
use App\Models\ItemAssigned;
use App\Models\SupplierInvoiceDirectItem;
use Illuminate\Support\Facades\DB;

class SupplierInvoiceService
{
    public static function validateSupplierInvoiceItem($itemCode, $companySystemID, $supplierInvoiceID) {

        $invoice = BookInvSuppMaster::where('bookingSuppMasInvAutoID', $supplierInvoiceID)->first();
        if (empty($invoice)) {
            return ['status' => false, 'message' => 'Supplier Invoice not found'];
        }

        if(isset($input['type']) &&  $input['type'] != $invoice->documentType) {
            return ['status' => false, 'message' => 'The Supplier Invoice type has changed, unable to proceed'];
        }

        if (empty($invoice->supplierTransactionCurrencyID)) {
            return ['status' => false, 'message' => 'Please select a document currency'];
        }

        $itemAssign = ItemAssigned::with(['item_master'])->where('itemCodeSystem', $itemCode)->first();

        if (empty($itemAssign)) {
            return ['status' => false, 'message' => 'Item not assigned'];
        }

        $item = ItemAssigned::where('itemCodeSystem', $itemAssign->itemCodeSystem)
            ->where('companySystemID', $companySystemID)
            ->first();

        $sameItem = SupplierInvoiceDirectItem::select(DB::raw('itemCode'))
            ->where('bookingSuppMasInvAutoID', $supplierInvoiceID)
            ->where('itemCode', $itemAssign->itemCodeSystem)
            ->first();

        if ($item->financeCategoryMaster == 1) {
            if ($sameItem) {
                return ['status' => false, 'message' => 'Selected item is already added from the same supplier invoice.'];
            }
        }

        return ['status' => true];
    }

    public static function saveSupplierInvoiceItem($itemCode, $supplierInvoiceID, $employeeSystemID) {
        $invoice = BookInvSuppMaster::where('bookingSuppMasInvAutoID', $supplierInvoiceID)->first();

        $itemAssign = ItemAssigned::with(['item_master'])->where('itemCodeSystem', $itemCode)->first();

        $financeCategorySub = FinanceItemCategorySub::find($itemAssign->financeCategorySub);

        $input = [];
        $input['noQty'] = 0;
        $input['unitCost'] = 0;
        $input['comment'] = null;

        $currency = \Helper::currencyConversion($invoice->companySystemID,$invoice->supplierTransactionCurrencyID, $invoice->supplierTransactionCurrencyID ,$input['unitCost']);

        $detailArray['bookingSuppMasInvAutoID'] = $supplierInvoiceID;
        $detailArray['companySystemID'] = $invoice->companySystemID;
        $detailArray['itemCode'] = $itemAssign->itemCodeSystem;
        $detailArray['trackingType'] = (isset($itemAssign->item_master->trackingType)) ? $itemAssign->item_master->trackingType : null;
        $detailArray['itemPrimaryCode'] = $itemAssign->itemPrimaryCode;
        $detailArray['itemDescription'] = $itemAssign->itemDescription;
        $detailArray['itemFinanceCategoryID'] = $itemAssign->financeCategoryMaster;
        $detailArray['itemFinanceCategorySubID'] = $itemAssign->financeCategorySub;
        $detailArray['financeGLcodebBSSystemID'] = $financeCategorySub->financeGLcodebBSSystemID;
        $detailArray['financeGLcodePLSystemID'] = $financeCategorySub->financeGLcodePLSystemID;
        $detailArray['includePLForGRVYN'] = $financeCategorySub->includePLForGRVYN;
        $detailArray['supplierPartNumber'] = $itemAssign->secondaryItemCode;
        $detailArray['unitOfMeasure'] = $itemAssign->itemUnitOfMeasure;
        $detailArray['noQty'] = $input['noQty'];
        $totalNetcost = $input['unitCost'] * $input['noQty'];
        $detailArray['unitCost'] = $input['unitCost'];
        $detailArray['netAmount'] = $totalNetcost;
        $detailArray['comment'] = $input['comment'];
        $detailArray['supplierDefaultCurrencyID'] = $invoice->supplierTransactionCurrencyID;
        $detailArray['supplierDefaultER'] = $invoice->supplierTransactionCurrencyER;
        $detailArray['supplierItemCurrencyID'] = $invoice->supplierTransactionCurrencyID;
        $detailArray['foreignToLocalER'] = $invoice->supplierTransactionCurrencyER;
        $detailArray['companyReportingCurrencyID'] = $invoice->companyReportingCurrencyID;
        $detailArray['companyReportingER'] = $invoice->companyReportingER;
        $detailArray['localCurrencyID'] = $invoice->localCurrencyID;
        $detailArray['localCurrencyER'] = $invoice->localCurrencyER;

        $detailArray['costPerUnitLocalCur'] = \Helper::roundValue($currency['localAmount']);
        $detailArray['costPerUnitSupDefaultCur'] = \Helper::roundValue($input['unitCost']);
        $detailArray['costPerUnitSupTransCur'] = \Helper::roundValue($input['unitCost']);
        $detailArray['costPerUnitComRptCur'] = \Helper::roundValue($currency['reportingAmount']);

        $detailArray['VATAmount'] = 0;
        if ($invoice->isVatEligible) {
            $vatDetails = TaxService::getVATDetailsByItem($invoice->companySystemID, $detailArray['itemCode'], $invoice->supplierID);
            $detailArray['VATPercentage'] = $vatDetails['percentage'];
            $detailArray['VATApplicableOn'] = $vatDetails['applicableOn'];
            $detailArray['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
            $detailArray['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
            $detailArray['VATAmount'] = 0;
            $detailArray['VATAmountLocal'] = 0;
            $detailArray['VATAmountRpt'] = 0;
        }

        $detailArray['createdPcID'] = gethostname();
        $detailArray['createdUserID'] = $employeeSystemID;

        SupplierInvoiceDirectItem::create($detailArray);
    }

}
