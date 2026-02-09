<?php

namespace App\Services\AccountReceivableLedger;

use App\Models\AccountsReceivableLedger;
use App\Models\AdvanceReceiptDetails;
use App\Models\CreditNote;
use App\Models\CreditNoteDetails;
use App\Models\DirectReceiptDetail;
use App\Models\SalesReturn;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerReceivePayment;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\Employee;
use App\Models\Taxdetail;
use App\Models\CustomerInvoiceDirectDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\GeneralLedger\GlPostedDateService;
use App\helper\Helper;

class CreditNoteARLedgerService
{
	public static function processEntry($masterModel)
	{
        $data = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);

        $masterData = CreditNote::with(['details' => function ($query) {
            $query->selectRaw('SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(creditAmount) as transAmount,creditNoteAutoID,serviceLineSystemID,serviceLineCode,clientContractID,contractUID');
        }, 'finance_period_by'])->find($masterModel["autoID"]);

        $tax = Taxdetail::selectRaw("SUM(localAmount) as localAmount, SUM(rptAmount) as rptAmount,SUM(amount) as transAmount,localCurrencyID,rptCurrencyID as reportingCurrencyID,currency as supplierTransactionCurrencyID,currencyER as supplierTransactionER,rptCurrencyER as companyReportingER,localCurrencyER")->WHERE('documentSystemCode', $masterModel["autoID"])->WHERE('documentSystemID', $masterModel["documentSystemID"])->first();

        //all details
        $detailsCreditNote = CreditNoteDetails::with(['chartofaccount'])
            ->selectRaw("SUM(netAmountLocal) as localAmount, SUM(netAmountRpt) as rptAmount, SUM(netAmount) as transAmount, SUM(VATAmount) as transTax, SUM(VATAmountLocal) as localTax, SUM(VATAmountRpt) as rptTax, chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,creditAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,creditAmountCurrencyER as transCurrencyER,serviceLineSystemID,serviceLineCode,clientContractID,contractUID,comments,chartOfAccountSystemID")
            ->WHERE('creditNoteAutoID', $masterModel["autoID"])
            ->groupBy('serviceLineSystemID', 'clientContractID', 'comments')
            ->get();

        $taxLocal = 0;
        $taxRpt = 0;
        $taxTrans = 0;

        if ($tax) {
            $taxLocal = $tax->localAmount;
            $taxRpt = $tax->rptAmount;
            $taxTrans = $tax->transAmount;
        }

        $validatePostedDate = GlPostedDateService::validatePostedDate($masterModel["autoID"], $masterModel["documentSystemID"]);

        if (!$validatePostedDate['status']) {
            return ['status' => false, 'message' => $validatePostedDate['message']];
        }

        $masterDocumentDate = $validatePostedDate['postedDate'];

        if ($masterData) {
            foreach ($detailsCreditNote as $detail) {
                $data['companySystemID'] = $masterData->companySystemID;
                $data['companyID'] = $masterData->companyID;
                $data['documentSystemID'] = $masterData->documentSystemiD;
                $data['documentID'] = $masterData->documentID;
                $data['documentCodeSystem'] = $masterModel["autoID"];
                $data['documentCode'] = $masterData->creditNoteCode;
                $data['documentDate'] = $masterDocumentDate;
                $data['serviceLineSystemID'] = $detail->serviceLineSystemID;
                $data['serviceLineCode'] = $detail->serviceLineCode;
                $data['customerID'] = $masterData->customerID;
                $data['InvoiceNo'] = null;
                $data['InvoiceDate'] = null;
                $data['custTransCurrencyID'] = $masterData->customerCurrencyID;
                $data['custTransER'] = $masterData->customerCurrencyER;
                $data['custInvoiceAmount'] = ABS($detail->transAmount + $detail->transTax) * -1;
                $data['custDefaultCurrencyID'] = 0;
                $data['custDefaultCurrencyER'] = 0;
                $data['custDefaultAmount'] = 0;
                $data['localCurrencyID'] = $masterData->localCurrencyID;
                $data['localER'] = $masterData->localCurrencyER;
                $data['localAmount'] = Helper::roundValue(ABS($detail->localAmount + $detail->localTax) * -1);
                $data['comRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                $data['comRptER'] = $masterData->companyReportingER;
                $data['comRptAmount'] = Helper::roundValue(ABS($detail->rptAmount + $detail->rptTax) * -1);
                $data['isInvoiceLockedYN'] = 0;
                $data['documentType'] = $masterData->documentType;
                $data['selectedToPaymentInv'] = 0;
                $data['fullyInvoiced'] = 0;
                $data['createdDateTime'] = Helper::currentDateTime();
                $data['createdUserID'] = $empID->empID;
                $data['createdUserSystemID'] = $empID->employeeSystemID;
                $data['createdPcID'] = gethostname();
                $data['timeStamp'] = Helper::currentDateTime();
                array_push($finalData, $data);
            }
        }

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData]];
	}
}