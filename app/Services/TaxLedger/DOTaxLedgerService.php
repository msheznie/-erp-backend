<?php

namespace App\Services\TaxLedger;

use App\Models\DirectPaymentDetails;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\POSTaxGLEntries;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Taxdetail;
use App\Models\Company;
use App\Models\PoAdvancePayment;
use App\Models\GRVMaster;
use App\Models\GRVDetails;
use App\Models\CreditNote;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnLogistic;
use App\Models\PurchaseReturnDetails;
use App\Models\SupplierInvoiceItemDetail;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\DeliveryOrder;
use App\Models\CreditNoteDetails;
use App\Models\DeliveryOrderDetail;
use App\Models\TaxLedger;
use App\Models\DebitNote;
use App\Models\TaxLedgerDetail;
use App\Models\DebitNoteDetails;
use App\Models\TaxVatCategories;
use App\helper\TaxService;
use App\Models\Employee;
use App\Models\SalesReturn;
use App\Models\ChartOfAccount;
use App\Models\SalesReturnDetail;
use App\Models\BookInvSuppMaster;
use App\Models\DirectInvoiceDetails;
use App\helper\Helper;

class DOTaxLedgerService
{
	public static function processEntry($taxLedgerData, $masterModel)
	{
        $finalData = [];
        $finalDetailData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);
        $ledgerData = [
            'documentSystemID' => $masterModel["documentSystemID"],
            'documentMasterAutoID' => $masterModel["autoID"],
            'inputVATGlAccountID' => isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null,
            'inputVatTransferAccountID' => isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null,
            'outputVatTransferGLAccountID' => isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null,
            'outputVatGLAccountID' => isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null,
            'companySystemID' => $masterModel['companySystemID'],
            'createdPCID' =>  gethostname(),
            'createdUserID' => $empID->employeeSystemID,
            'createdDateTime' => Helper::currentDateTime(),
            'modifiedPCID' => gethostname(),
            'modifiedUserID' => $empID->employeeSystemID,
            'modifiedDateTime' => Helper::currentDateTime()
        ];

        $ledgerDetailsData = $ledgerData;
        $ledgerDetailsData['createdUserSystemID'] = $empID->employeeSystemID;
        
        $masterData = DeliveryOrder::with(['finance_period_by', 'customer'])->find($masterModel["autoID"]);

        $masterDocumentDate = date('Y-m-d H:i:s');
        if (isset($masterData->finance_period_by->isActive) && $masterData->finance_period_by->isActive == -1) {
            $masterDocumentDate = $masterData->deliveryOrderDate;
        }

        $ledgerData['documentCode'] = $masterData->deliveryOrderCode;
        $ledgerData['documentDate'] = $masterDocumentDate;
        $ledgerData['partyID'] = $masterData->customerID;
        $ledgerData['documentFinalApprovedByEmpSystemID'] = $masterData->approvedEmpSystemID;

        $ledgerData['documentTransAmount'] = floatval($masterData->transactionAmount) + floatval($masterData->VATAmount);
        $ledgerData['documentLocalAmount'] = floatval($masterData->companyLocalAmount) + floatval($masterData->VATAmountLocal);
        $ledgerData['documentReportingAmount'] = floatval($masterData->companyReportingAmount) + floatval($masterData->VATAmountRpt);

        $details = DeliveryOrderDetail::selectRaw('SUM(VATAmount*qtyIssuedDefaultMeasure) as transVATAmount,SUM(VATAmountLocal*qtyIssuedDefaultMeasure) as localVATAmount ,SUM(VATAmountRpt*qtyIssuedDefaultMeasure) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, companyLocalCurrencyID,companyLocalCurrencyER,companyReportingCurrencyER,companyReportingCurrencyID, transactionCurrencyID, transactionCurrencyER')
                                ->where('deliveryOrderID', $masterModel["autoID"])
                                ->whereNotNull('vatSubCategoryID')
                                ->groupBy('vatSubCategoryID')
                                ->get();

        foreach ($details as $key => $value) {
            $subCategoryData = TaxVatCategories::with(['tax'])->find($value->vatSubCategoryID);

            if ($subCategoryData) {
                $ledgerData['taxAuthorityAutoID'] = isset($subCategoryData->tax->authorityAutoID) ? $subCategoryData->tax->authorityAutoID : null;
            }

            $ledgerData['subCategoryID'] = $value->vatSubCategoryID;
            $ledgerData['masterCategoryID'] = $value->vatMasterCategoryID;
            $ledgerData['localAmount'] = $value->localVATAmount;
            $ledgerData['rptAmount'] = $value->rptVATAmount;
            $ledgerData['transAmount'] = $value->transVATAmount;
            $ledgerData['transER'] = $value->transactionCurrencyER;
            $ledgerData['localER'] = $value->companyLocalCurrencyER;
            $ledgerData['comRptER'] = $value->companyReportingCurrencyER;
            $ledgerData['localCurrencyID'] = $value->companyLocalCurrencyID;
            $ledgerData['rptCurrencyID'] = $value->companyReportingCurrencyID;
            $ledgerData['transCurrencyID'] = $value->transactionCurrencyID;

            array_push($finalData, $ledgerData);
        }

        $detailData = DeliveryOrderDetail::where('deliveryOrderID', $masterModel["autoID"])
                                            ->whereNotNull('vatSubCategoryID')
                                            ->get();

        foreach ($detailData as $key => $value) {
            $ledgerDetailsData['documentDetailID'] = $value->deliveryOrderDetailID;
            $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
            $ledgerDetailsData['vatMasterCategoryID'] = $value->vatMasterCategoryID;
            $ledgerDetailsData['serviceLineSystemID'] = $masterData->serviceLineSystemID;
            $ledgerDetailsData['documentDate'] = $masterDocumentDate;
            $ledgerDetailsData['postedDate'] = date('Y-m-d H:i:s');
            $ledgerDetailsData['documentNumber'] = $masterData->deliveryOrderCode;
            $ledgerDetailsData['chartOfAccountSystemID'] = $value->financeGLcodeRevenueSystemID;

            $chartOfAccountData = ChartOfAccount::find($value->financeGLcodeRevenueSystemID);

            if ($chartOfAccountData) {
                $ledgerDetailsData['accountCode'] = $chartOfAccountData->AccountCode;
                $ledgerDetailsData['accountDescription'] = $chartOfAccountData->AccountDescription;
            }

            $ledgerDetailsData['transactionCurrencyID'] = $value->transactionCurrencyID;
            $ledgerDetailsData['originalInvoice'] = null;
            $ledgerDetailsData['originalInvoiceDate'] = null;
            $ledgerDetailsData['dateOfSupply'] = null;
            $ledgerDetailsData['partyType'] = 2;
            $ledgerDetailsData['partyAutoID'] = $masterData->customerID;
            $ledgerDetailsData['partyVATRegisteredYN'] = $masterData->customerVATEligible;
            $ledgerDetailsData['partyVATRegNo'] = isset($masterData->customer->vatNumber) ? $masterData->customer->vatNumber : "";
            $ledgerDetailsData['countryID'] = isset($masterData->customer->customerCountry) ? $masterData->customer->customerCountry : "";
            $ledgerDetailsData['itemSystemCode'] = $value->itemCodeSystem;
            $ledgerDetailsData['itemCode'] = $value->itemPrimaryCode;
            $ledgerDetailsData['itemDescription'] = $value->itemDescription;
            $ledgerDetailsData['VATPercentage'] = $value->VATPercentage;
            $ledgerDetailsData['taxableAmount'] = $value->transactionAmount;
            $ledgerDetailsData['VATAmount'] = $value->VATAmount * $value->qtyIssued;
            $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount * $value->qtyIssued;
            $ledgerDetailsData['localER'] = $value->companyLocalCurrencyER;
            $ledgerDetailsData['reportingER'] = $value->companyReportingCurrencyER;
            $ledgerDetailsData['taxableAmountLocal'] = $value->companyLocalAmount;
            $ledgerDetailsData['taxableAmountReporting'] = $value->companyReportingAmount;
            $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal * $value->qtyIssued;
            $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt * $value->qtyIssued;
            $ledgerDetailsData['localCurrencyID'] = $value->companyLocalCurrencyID;
            $ledgerDetailsData['rptCurrencyID'] = $value->companyReportingCurrencyID;

            array_push($finalDetailData, $ledgerDetailsData);
        }

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'finalDetailData' => $finalDetailData]];

	}
}