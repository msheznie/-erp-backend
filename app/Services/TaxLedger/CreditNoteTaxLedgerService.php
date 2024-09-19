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

class CreditNoteTaxLedgerService
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
            'createdDateTime' => \Helper::currentDateTime(),
            'modifiedPCID' => gethostname(),
            'modifiedUserID' => $empID->employeeSystemID,
            'modifiedDateTime' => \Helper::currentDateTime()
        ];

        $ledgerDetailsData = $ledgerData;
        $ledgerDetailsData['createdUserSystemID'] = $empID->employeeSystemID;

        $masterData = CreditNote::with(['finance_period_by', 'customer','details' => function ($query) {
            $query->selectRaw('SUM(netAmountLocal) as localAmount, SUM(netAmountRpt) as rptAmount,SUM(netAmount) as transAmount,creditNoteAutoID');
        }])->find($masterModel["autoID"]);

        $masterDocumentDate = date('Y-m-d H:i:s');
        if (isset($masterData->finance_period_by->isActive) && $masterData->finance_period_by->isActive == -1) {
            $masterDocumentDate = $masterData->creditNoteDate;
        }

        $ledgerData['documentCode'] = $masterData->creditNoteCode;
        $ledgerData['documentDate'] = $masterDocumentDate;
        $ledgerData['partyID'] = $masterData->customerID;
        $ledgerData['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;

        $currencyConversionAmount = \Helper::currencyConversion($masterData->companySystemID, $masterData->customerCurrencyID, $masterData->customerCurrencyID, $masterData->creditAmountTrans);

        $ledgerData['documentTransAmount'] = \Helper::roundValue($masterData->creditAmountTrans);
        $ledgerData['documentLocalAmount'] = \Helper::roundValue($currencyConversionAmount['localAmount']);
        $ledgerData['documentReportingAmount'] = \Helper::roundValue($currencyConversionAmount['reportingAmount']);
            

        $details = CreditNoteDetails::selectRaw('erp_tax_vat_sub_categories.subCatgeoryType,SUM(VATAmount) as transVATAmount,SUM(VATAmountLocal) as localVATAmount ,SUM(VATAmountRpt) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,creditAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER as localCurrencyER,creditAmountCurrencyER as transCurrencyER')
                                ->where('creditNoteAutoID', $masterModel["autoID"])
                                ->whereNotNull('vatSubCategoryID')
                                ->groupBy('vatSubCategoryID')
                                ->join('erp_tax_vat_sub_categories', 'erp_creditnotedetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                ->get();

        foreach ($details as $key => $value) {
            $subCategoryData = TaxVatCategories::with(['tax'])->find($value->vatSubCategoryID);

            if ($subCategoryData) {
                $ledgerData['taxAuthorityAutoID'] = isset($subCategoryData->tax->authorityAutoID) ? $subCategoryData->tax->authorityAutoID : null;
            }

            if($value->subCatgeoryType == 3)
            {
                $ledgerData['inputVATGlAccountID'] = null;
                $ledgerData['inputVatTransferAccountID'] = null;
                $ledgerData['outputVatTransferGLAccountID'] = null;
                $ledgerData['outputVatGLAccountID'] = null;
            }
            else
            {
                $ledgerData['inputVATGlAccountID'] = isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null;
                $ledgerData['inputVatTransferAccountID'] = isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null;
                $ledgerData['outputVatTransferGLAccountID'] = isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null;
                $ledgerData['outputVatGLAccountID'] = isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null;
            }

            $ledgerData['subCategoryID'] = $value->vatSubCategoryID;
            $ledgerData['masterCategoryID'] = $value->vatMasterCategoryID;
            $ledgerData['localAmount'] = $value->localVATAmount;
            $ledgerData['rptAmount'] = $value->rptVATAmount;
            $ledgerData['transAmount'] = $value->transVATAmount;
            $ledgerData['transER'] = $value->transCurrencyER;
            $ledgerData['localER'] = $value->localCurrencyER;
            $ledgerData['comRptER'] = $value->reportingCurrencyER;
            $ledgerData['localCurrencyID'] = $value->localCurrencyID;
            $ledgerData['rptCurrencyID'] = $value->reportingCurrencyID;
            $ledgerData['transCurrencyID'] = $value->transCurrencyID;

            array_push($finalData, $ledgerData);
        }

        $detailData = CreditNoteDetails::where('creditNoteAutoID', $masterModel["autoID"])
                                            ->whereNotNull('vatSubCategoryID')
                                            ->join('erp_tax_vat_sub_categories', 'erp_creditnotedetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                            ->get();

        foreach ($detailData as $key => $value) {
            $ledgerDetailsData['documentDetailID'] = $value->creditNoteDetailsID;
            $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
            $ledgerDetailsData['vatMasterCategoryID'] = $value->vatMasterCategoryID;
            $ledgerDetailsData['serviceLineSystemID'] = $value->serviceLineSystemID;
            $ledgerDetailsData['documentDate'] = $masterDocumentDate;
            $ledgerDetailsData['postedDate'] = date('Y-m-d H:i:s');
            $ledgerDetailsData['documentNumber'] = $masterData->creditNoteCode;
            $ledgerDetailsData['chartOfAccountSystemID'] = $value->chartOfAccountSystemID;

            $chartOfAccountData = ChartOfAccount::find($value->chartOfAccountSystemID);

            if ($chartOfAccountData) {
                $ledgerDetailsData['accountCode'] = $chartOfAccountData->AccountCode;
                $ledgerDetailsData['accountDescription'] = $chartOfAccountData->AccountDescription;
            }

            if($value->subCatgeoryType == 3)
            {
                $ledgerDetailsData['inputVATGlAccountID'] = null;
                $ledgerDetailsData['inputVatTransferAccountID'] = null;
                $ledgerDetailsData['outputVatTransferGLAccountID'] = null;
                $ledgerDetailsData['outputVatGLAccountID'] = null;
            }
            else
            {
                $ledgerDetailsData['inputVATGlAccountID'] = isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null;
                $ledgerDetailsData['inputVatTransferAccountID'] = isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null;
                $ledgerDetailsData['outputVatTransferGLAccountID'] = isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null;
                $ledgerDetailsData['outputVatGLAccountID'] = isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null;
            }

            $ledgerDetailsData['transactionCurrencyID'] = $value->creditAmountCurrency;
            $ledgerDetailsData['originalInvoice'] = null;
            $ledgerDetailsData['originalInvoiceDate'] = null;
            $ledgerDetailsData['dateOfSupply'] = null;
            $ledgerDetailsData['partyType'] = 2;
            $ledgerDetailsData['partyAutoID'] = $masterData->customerID;
            $ledgerDetailsData['partyVATRegisteredYN'] = isset($masterData->customer->vatEligible) ? $masterData->customer->vatEligible : 0;
            $ledgerDetailsData['partyVATRegNo'] = isset($masterData->customer->vatNumber) ? $masterData->customer->vatNumber : "";
            $ledgerDetailsData['countryID'] = isset($masterData->customer->customerCountry) ? $masterData->customer->customerCountry : "";
            $ledgerDetailsData['itemSystemCode'] = null;
            $ledgerDetailsData['itemCode'] = null;
            $ledgerDetailsData['itemDescription'] = null;
            $ledgerDetailsData['VATPercentage'] = $value->VATPercentage;
            $ledgerDetailsData['taxableAmount'] = $value->netAmount;
            $ledgerDetailsData['VATAmount'] = $value->VATAmount;
            $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount;
            $ledgerDetailsData['localER'] = $value->localCurrencyER;
            $ledgerDetailsData['reportingER'] = $value->comRptCurrencyER;
            $ledgerDetailsData['taxableAmountLocal'] = $value->netAmountLocal;
            $ledgerDetailsData['taxableAmountReporting'] = $value->netAmountRpt;
            $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal;
            $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt;
            $ledgerDetailsData['localCurrencyID'] = $value->localCurrency;
            $ledgerDetailsData['rptCurrencyID'] = $value->comRptCurrency;

            array_push($finalDetailData, $ledgerDetailsData);
        }

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'finalDetailData' => $finalDetailData]];
	}
}