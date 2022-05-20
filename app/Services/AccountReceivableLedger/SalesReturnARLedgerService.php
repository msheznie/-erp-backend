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


class SalesReturnARLedgerService
{
	public static function processEntry($masterModel)
	{
       	$data = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);

        $masterData = SalesReturn::with(['detail' => function ($query) {
            $query->selectRaw('SUM(companyLocalAmount) as localAmount, SUM(companyReportingAmount) as rptAmount,SUM(transactionAmount) as transAmount,salesReturnID');
        }, 'finance_period_by'])->find($masterModel["autoID"]);

        $masterDocumentDate = date('Y-m-d H:i:s');
        if ($masterData->finance_period_by->isActive == -1) {
            $masterDocumentDate = $masterData->salesReturnDate;
        }

        if ($masterData) {
            $data['companySystemID'] = $masterData->companySystemID;
            $data['companyID'] = $masterData->companyID;
            $data['documentSystemID'] = $masterData->documentSystemID;
            $data['documentID'] = $masterData->documentID;
            $data['documentCodeSystem'] = $masterModel["autoID"];
            $data['documentCode'] = $masterData->salesReturnCode;
            $data['documentDate'] = $masterDocumentDate;
            $data['customerID'] = $masterData->customerID;
            $data['InvoiceNo'] = null;
            $data['InvoiceDate'] = null;
            $data['custTransCurrencyID'] = $masterData->transactionCurrencyID;
            $data['custTransER'] = $masterData->transactionCurrencyER;
            $data['custInvoiceAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->transAmount) + ((!is_null($masterData->VATAmount)) ? $masterData->VATAmount : 0)) * -1;
            $data['custDefaultCurrencyID'] = 0;
            $data['custDefaultCurrencyER'] = 0;
            $data['custDefaultAmount'] = 0;
            $data['localCurrencyID'] = $masterData->companyLocalCurrencyID;
            $data['localER'] = $masterData->companyLocalCurrencyER;
            $data['localAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->localAmount) + ((!is_null($masterData->VATAmountLocal)) ? $masterData->VATAmountLocal : 0)) * -1;
            $data['comRptCurrencyID'] = $masterData->companyReportingCurrencyID;
            $data['comRptER'] = $masterData->companyReportingCurrencyER;
            $data['comRptAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->rptAmount) + ((!is_null($masterData->VATAmountRpt)) ? $masterData->VATAmountRpt : 0)) * -1;
            $data['isInvoiceLockedYN'] = 0;
            $data['documentType'] = null;
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