<?php

namespace App\Services\AccountReceivableLedger;

use App\Models\AccountsReceivableLedger;
use App\Models\CreditNote;
use App\Models\SalesReturn;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerReceivePayment;
use App\Models\Employee;
use App\Models\Taxdetail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


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

        $taxLocal = 0;
        $taxRpt = 0;
        $taxTrans = 0;

        if ($tax) {
            $taxLocal = $tax->localAmount;
            $taxRpt = $tax->rptAmount;
            $taxTrans = $tax->transAmount;
        }

        $masterDocumentDate = date('Y-m-d H:i:s');
        if ($masterData->finance_period_by->isActive == -1) {
            $masterDocumentDate = $masterData->creditNoteDate;
        }

        if ($masterData) {
            $data['companySystemID'] = $masterData->companySystemID;
            $data['companyID'] = $masterData->companyID;
            $data['documentSystemID'] = $masterData->documentSystemiD;
            $data['documentID'] = $masterData->documentID;
            $data['documentCodeSystem'] = $masterModel["autoID"];
            $data['documentCode'] = $masterData->creditNoteCode;
            $data['documentDate'] = $masterDocumentDate;
            $data['customerID'] = $masterData->customerID;
            $data['InvoiceNo'] = null;
            $data['InvoiceDate'] = null;
            $data['custTransCurrencyID'] = $masterData->customerCurrencyID;
            $data['custTransER'] = $masterData->customerCurrencyER;
            $data['custInvoiceAmount'] = ABS($masterData->details[0]->transAmount) * -1;
            $data['custDefaultCurrencyID'] = 0;
            $data['custDefaultCurrencyER'] = 0;
            $data['custDefaultAmount'] = 0;
            $data['localCurrencyID'] = $masterData->localCurrencyID;
            $data['localER'] = $masterData->localCurrencyER;
            $data['localAmount'] = \Helper::roundValue(ABS($masterData->details[0]->localAmount) * -1);
            $data['comRptCurrencyID'] = $masterData->companyReportingCurrencyID;
            $data['comRptER'] = $masterData->companyReportingER;
            $data['comRptAmount'] = \Helper::roundValue(ABS($masterData->details[0]->rptAmount) * -1);
            $data['isInvoiceLockedYN'] = 0;
            $data['documentType'] = $masterData->documentType;
            $data['selectedToPaymentInv'] = 0;
            $data['fullyInvoiced'] = 0;
            $data['createdDateTime'] = \Helper::currentDateTime();
            $data['createdUserID'] = $empID->empID;
            $data['createdUserSystemID'] = $empID->employeeSystemID;
            $data['createdPcID'] = gethostname();
            $data['timeStamp'] = \Helper::currentDateTime();
            array_push($finalData, $data);
        }

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData]];
	}
}