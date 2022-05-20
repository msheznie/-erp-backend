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


class ReceiptVoucherARLedgerService
{
	public static function processEntry($masterModel)
	{
       	$data = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);

        $masterData = CustomerReceivePayment::with(['details' => function ($query) {
            $query->selectRaw('SUM(receiveAmountLocal) as localAmount, SUM(receiveAmountRpt) as rptAmount,SUM(receiveAmountTrans) as transAmount,custReceivePaymentAutoID');
        }, 'directdetails' => function ($query) {
            $query->selectRaw('SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DRAmount) as transAmount,directReceiptAutoID,serviceLineSystemID,serviceLineCode');
        },'advance_receipt_details' => function($query) {
            $query->selectRaw('SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(paymentAmount) as transAmount,custReceivePaymentAutoID');
        },'finance_period_by'])->find($masterModel["autoID"]);

        $masterDocumentDate = date('Y-m-d H:i:s');

        if ($masterData) {
            if ($masterData->documentType == 13 || $masterData->documentType == 15) {
                if ($masterData->finance_period_by->isActive == -1) {
                    $masterDocumentDate = $masterData->custPaymentReceiveDate;
                }

                $transAmount = 0;
                $transAmountLocal = 0;
                $transAmountRpt = 0;

                if(isset($masterData->details) && count($masterData->details) > 0){
                    $transAmount = $transAmount + $masterData->details[0]->transAmount;
                    $transAmountLocal = $transAmountLocal + $masterData->details[0]->localAmount;
                    $transAmountRpt = $transAmountRpt + $masterData->details[0]->rptAmount;
                }

                if(isset($masterData->directdetails) && count($masterData->directdetails) > 0){
                    $transAmount = $transAmount + $masterData->directdetails[0]->transAmount;
                    $transAmountLocal = $transAmountLocal + $masterData->directdetails[0]->localAmount;
                    $transAmountRpt = $transAmountRpt + $masterData->directdetails[0]->rptAmount;
                }

                if(isset($masterData->advance_receipt_details) && count($masterData->advance_receipt_details) > 0){
                    $transAmount = $transAmount + $masterData->advance_receipt_details[0]->transAmount;
                    $transAmountLocal = $transAmountLocal + $masterData->advance_receipt_details[0]->localAmount;
                    $transAmountRpt = $transAmountRpt + $masterData->advance_receipt_details[0]->rptAmount;
                }


                $transAmountLocal = \Helper::roundValue($transAmountLocal);
                $transAmountRpt = \Helper::roundValue($transAmountRpt);


                $data['companySystemID'] = $masterData->companySystemID;
                $data['companyID'] = $masterData->companyID;
                $data['documentSystemID'] = $masterData->documentSystemID;
                $data['documentID'] = $masterData->documentID;
                $data['documentCodeSystem'] = $masterModel["autoID"];
                $data['documentCode'] = $masterData->custPaymentReceiveCode;
                $data['documentDate'] = $masterDocumentDate;
                $data['customerID'] = $masterData->customerID;
                $data['InvoiceNo'] = null;
                $data['InvoiceDate'] = null;
                $data['custTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                $data['custTransER'] = $masterData->custTransactionCurrencyER;
                $data['custInvoiceAmount'] = ($masterData->documentType == 15) ? (ABS($transAmount) * -1) : $transAmount;
                $data['custDefaultCurrencyID'] = 0;
                $data['custDefaultCurrencyER'] = 0;
                $data['custDefaultAmount'] = 0;
                $data['localCurrencyID'] = $masterData->localCurrencyID;
                $data['localER'] = $masterData->localCurrencyER;
                $data['localAmount'] = ($masterData->documentType == 15) ? (ABS($transAmountLocal) * -1) : $transAmountLocal;
                $data['comRptCurrencyID'] = $masterData->companyRptCurrencyID;
                $data['comRptER'] = $masterData->companyRptCurrencyER;
                $data['comRptAmount'] = ($masterData->documentType == 15) ? (ABS($transAmountRpt) * -1) : $transAmountRpt;
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
        }

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData]];
	}
}