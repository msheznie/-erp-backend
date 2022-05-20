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


class CustomerInvoiceARLedgerService
{
	public static function processEntry($masterModel)
	{
        $data = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);

        $masterData = CustomerInvoiceDirect::with(['invoicedetails' => function ($query) {
            $query->selectRaw('SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(invoiceAmount) as transAmount,custInvoiceDirectID,serviceLineSystemID,serviceLineCode');
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

        if ($masterData) {
            $masterDocumentDate = date('Y-m-d H:i:s');
            if ($masterData->isPerforma == 1 || $masterData->isPerforma == 2 || $masterData->isPerforma == 4 || $masterData->isPerforma == 5) {
                $masterDocumentDate = date('Y-m-d H:i:s');
            } else {
                if ($masterData->finance_period_by->isActive == -1) {
                    $masterDocumentDate = $masterData->bookingDate;
                }
            }

            $data['companySystemID'] = $masterData->companySystemID;
            $data['companyID'] = $masterData->companyID;
            $data['documentSystemID'] = $masterData->documentSystemiD;
            $data['documentID'] = $masterData->documentID;
            $data['documentCodeSystem'] = $masterModel["autoID"];
            $data['documentCode'] = $masterData->bookingInvCode;
            $data['documentDate'] = $masterDocumentDate;
            $data['customerID'] = $masterData->customerID;
            $data['InvoiceNo'] = $masterData->customerInvoiceNo;
            $data['InvoiceDate'] = $masterData->customerInvoiceDate;
            $data['custTransCurrencyID'] = $masterData->custTransactionCurrencyID;
            $data['custTransER'] = $masterData->custTransactionCurrencyER;

            $data['custDefaultCurrencyID'] = 0;
            $data['custDefaultCurrencyER'] = 0;
            $data['custDefaultAmount'] = 0;
            $data['localCurrencyID'] = $masterData->localCurrencyID;
            $data['localER'] = $masterData->localCurrencyER;

            $data['comRptCurrencyID'] = $masterData->companyReportingCurrencyID;
            $data['comRptER'] = $masterData->companyReportingER;

            $data['isInvoiceLockedYN'] = 0;
            $data['documentType'] = $masterData->documentType;
            $data['selectedToPaymentInv'] = 0;
            $data['fullyInvoiced'] = 0;
            $data['createdDateTime'] = \Helper::currentDateTime();
            $data['createdUserID'] = $empID->empID;
            $data['createdUserSystemID'] = $empID->employeeSystemID;
            $data['createdPcID'] = gethostname();
            $data['timeStamp'] = \Helper::currentDateTime();

            if($masterData->isPerforma == 2 || $masterData->isPerforma == 3|| $masterData->isPerforma == 4|| $masterData->isPerforma == 5){// item sales invoice
                $data['custInvoiceAmount'] = ABS($masterData->bookingAmountTrans + $taxTrans);
                $data['localAmount'] = \Helper::roundValue(ABS($masterData->bookingAmountLocal + $taxLocal));
                $data['comRptAmount'] = \Helper::roundValue(ABS($masterData->bookingAmountRpt + $taxRpt));
            }else if( $masterData->isPerforma == 1){
                $data['custInvoiceAmount'] = ABS($masterData->invoicedetails[0]->transAmount);
                $data['localAmount'] = \Helper::roundValue(ABS($masterData->invoicedetails[0]->localAmount));
                $data['comRptAmount'] = \Helper::roundValue(ABS($masterData->invoicedetails[0]->rptAmount));
            }else{
                $data['custInvoiceAmount'] = ABS($masterData->invoicedetails[0]->transAmount + $taxTrans);
                $data['localAmount'] = \Helper::roundValue(ABS($masterData->invoicedetails[0]->localAmount + $taxLocal));
                $data['comRptAmount'] = \Helper::roundValue(ABS($masterData->invoicedetails[0]->rptAmount + $taxRpt));
            }
            array_push($finalData, $data);
        }

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData]];
	}
}