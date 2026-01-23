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

class GPOSSalesTaxLedgerService
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

        $taxEntries = POSTaxGLEntries::where('shiftId',$masterModel["autoID"])->get();

        $tax = DB::table('pos_source_invoice')
            ->selectRaw('pos_source_invoice.*')
            ->where('pos_source_invoice.shiftID', $masterModel["autoID"])
            ->first();

        $masterDocumentDate = date('Y-m-d H:i:s');

        $ledgerData['documentCode'] = $taxEntries->documentCode;
        $ledgerData['documentDate'] = $masterDocumentDate;
        $ledgerData['documentFianlApprovedByEmpSystemID'] = $empID->employeeSystemID;
        $taxItems = DB::table('pos_source_invoicedetail')
            ->selectRaw('SUM(pos_source_taxledger.amount) as amount, pos_source_taxledger.taxMasterID as taxID, pos_source_invoice.companyReportingExchangeRate as rptER, pos_source_invoice.transactionExchangeRate as transER, pos_source_invoice.companyLocalExchangeRate as localER')
            ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_source_invoicedetail.invoiceID')
            ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_invoicedetail.itemAutoID')
            ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
            ->join('pos_source_taxledger', 'pos_source_taxledger.documentDetailAutoID', '=', 'pos_source_invoicedetail.invoiceDetailsID')
            ->join('erp_taxmaster_new', 'erp_taxmaster_new.taxMasterAutoID', '=', 'pos_source_taxledger.taxMasterID')
            ->where('pos_source_invoice.shiftID', $masterModel["autoID"])
            ->groupBy('pos_source_invoice.shiftID')
            ->groupBy('pos_source_invoice.invoiceID')
            ->where('pos_source_invoice.isCreditSales', 0)
            ->first();

        $ledgerData['subCategoryID'] = $taxItems->taxID;
        $ledgerData['masterCategoryID'] = $taxItems->taxID;
        $ledgerData['localAmount'] = $taxItems->amount;
        $ledgerData['rptAmount'] = $taxItems->amount/$taxItems->rptER;
        $ledgerData['transAmount'] = $taxItems->amount;
        $ledgerData['transER'] = $taxItems->transER;
        $ledgerData['localER'] = $taxItems->localER;
        $ledgerData['comRptER'] = $taxItems->rptER;
        $ledgerData['localCurrencyID'] = $taxItems->companyLocalCurrencyID;
        $ledgerData['rptCurrencyID'] = $taxItems->companyReportingCurrencyID;
        $ledgerData['transCurrencyID'] = $taxItems->transactionCurrencyID;

        array_push($finalData, $ledgerData);


        foreach ($taxEntries as $key => $value) {
            $ledgerDetailsData['documentSystemID'] = $value->documentSystemID;
            $ledgerDetailsData['documentMasterAutoID'] = $value->shiftId;
            $ledgerDetailsData['documentDate'] = $masterDocumentDate;
            $ledgerDetailsData['postedDate'] = date('Y-m-d H:i:s');
            $ledgerDetailsData['chartOfAccountSystemID'] = $value->glCode;

            $chartOfAccountData = ChartOfAccount::find($value->glCode);

            if ($chartOfAccountData) {
                $ledgerDetailsData['accountCode'] = $chartOfAccountData->AccountCode;
                $ledgerDetailsData['accountDescription'] = $chartOfAccountData->AccountDescription;
            }

            $ledgerDetailsData['transactionCurrencyID'] = $tax->transactionCurrencyID;
            $ledgerDetailsData['originalInvoice'] = null;
            $ledgerDetailsData['originalInvoiceDate'] = null;
            $ledgerDetailsData['dateOfSupply'] = null;
            $ledgerDetailsData['itemSystemCode'] = null;
            $ledgerDetailsData['itemCode'] = null;
            $ledgerDetailsData['itemDescription'] = null;
            $ledgerDetailsData['taxableAmount'] = $value->amount;
            $ledgerDetailsData['VATAmount'] = $value->amount;
            $ledgerDetailsData['recoverabilityAmount'] = $value->amount;
            $ledgerDetailsData['localER'] = $value->companyLocalExchangeRate;
            $ledgerDetailsData['reportingER'] = $value->companyReportingExchangeRate;
            $ledgerDetailsData['taxableAmountLocal'] = $value->amount;
            $ledgerDetailsData['taxableAmountReporting'] = $value->amount ;
            $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal;
            $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountLocal;
            $ledgerDetailsData['localCurrencyID'] = $value->companyLocalCurrencyID;
            $ledgerDetailsData['rptCurrencyID'] = $value->companyReportingCurrencyID;

            array_push($finalDetailData, $ledgerDetailsData);
        }

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'finalDetailData' => $finalDetailData]];
	}
}