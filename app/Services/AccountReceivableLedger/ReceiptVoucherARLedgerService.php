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

        $directReceipts = DirectReceiptDetail::selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DRAmount) as transAmount,chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,DRAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,DDRAmountCurrencyER as transCurrencyER,serviceLineSystemID,serviceLineCode")
            ->WHERE('directReceiptAutoID', $masterModel["autoID"])
            ->groupBy('serviceLineSystemID', 'chartOfAccountSystemID')
            ->get();

        $directReceiptsBySegments = DirectReceiptDetail::selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DRAmount) as transAmount,chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,DRAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,DDRAmountCurrencyER as transCurrencyER,serviceLineSystemID,serviceLineCode")
            ->WHERE('directReceiptAutoID', $masterModel["autoID"])
            ->groupBy('serviceLineSystemID')
            ->get();

        $advReceiptsBySegments = AdvanceReceiptDetails::selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(paymentAmount) as transAmount,serviceLineSystemID,serviceLineCode")
            ->WHERE('custReceivePaymentAutoID', $masterModel["autoID"])
            ->groupBy('serviceLineSystemID')
            ->get();

        if ($masterData) {
            if ($masterData->documentType == 13 || $masterData->documentType == 15) {
                $validatePostedDate = GlPostedDateService::validatePostedDate($masterModel["autoID"], $masterModel["documentSystemID"]);

                if (!$validatePostedDate['status']) {
                    return ['status' => false, 'message' => $validatePostedDate['message']];
                }

                $masterDocumentDate = $validatePostedDate['postedDate'];

                $transAmount = 0;
                $transAmountLocal = 0;
                $transAmountRpt = 0;

                if (isset($masterData->details) && count($masterData->details) > 0) {
                    $transAmount = $transAmount + $masterData->details[0]->transAmount;
                    $transAmountLocal = $transAmountLocal + $masterData->details[0]->localAmount;
                    $transAmountRpt = $transAmountRpt + $masterData->details[0]->rptAmount;
                }

                if (isset($masterData->directdetails) && count($masterData->directdetails) > 0) {
                    $transAmount = $transAmount + $masterData->directdetails[0]->transAmount;
                    $transAmountLocal = $transAmountLocal + $masterData->directdetails[0]->localAmount;
                    $transAmountRpt = $transAmountRpt + $masterData->directdetails[0]->rptAmount;
                }

                if (isset($masterData->advance_receipt_details) && count($masterData->advance_receipt_details) > 0) {
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
                $data['custDefaultCurrencyID'] = 0;
                $data['custDefaultCurrencyER'] = 0;
                $data['custDefaultAmount'] = 0;
                $data['localCurrencyID'] = $masterData->localCurrencyID;
                $data['localER'] = $masterData->localCurrencyER;
                $data['comRptCurrencyID'] = $masterData->companyRptCurrencyID;
                $data['comRptER'] = $masterData->companyRptCurrencyER;
                $data['isInvoiceLockedYN'] = 0;
                $data['documentType'] = $masterData->documentType;
                $data['selectedToPaymentInv'] = 0;
                $data['fullyInvoiced'] = 0;
                $data['createdDateTime'] = \Helper::currentDateTime();
                $data['createdUserID'] = $empID->empID;
                $data['createdUserSystemID'] = $empID->employeeSystemID;
                $data['createdPcID'] = gethostname();
                $data['timeStamp'] = \Helper::currentDateTime();

                if ($masterData->documentType == 13) {
                    $receiptDetails = CustomerReceivePaymentDetail::selectRaw('SUM(receiveAmountTrans) as receiveAmountTrans, SUM(receiveAmountLocal) as receiveAmountLocal, SUM(receiveAmountRpt) as receiveAmountRpt, erp_accountsreceivableledger.serviceLineSystemID as serviceLineSystemID, erp_accountsreceivableledger.serviceLineCode as serviceLineCode')
                            ->join('erp_accountsreceivableledger', 'erp_accountsreceivableledger.arAutoID', '=', 'erp_custreceivepaymentdet.arAutoID')
                            ->WHERE('custReceivePaymentAutoID', $masterModel["autoID"])
                            ->groupBy('erp_accountsreceivableledger.serviceLineSystemID')
                            ->get();

                    foreach ($receiptDetails as $key => $valueRe) {

                        $data['serviceLineSystemID'] = $valueRe->serviceLineSystemID;
                        $data['serviceLineCode'] = $valueRe->serviceLineCode;

                        $data['custInvoiceAmount'] = $valueRe->receiveAmountTrans;
                        $data['localAmount'] = $valueRe->receiveAmountLocal;
                        $data['comRptAmount'] = $valueRe->receiveAmountRpt;
                        array_push($finalData, $data);
                    }

                } else if ($masterData->documentType == 15) {

                    foreach ($directReceiptsBySegments as $detail) {

                        $data['serviceLineSystemID'] = $detail->serviceLineSystemID;
                        $data['serviceLineCode'] = $detail->serviceLineCode;
                        $data['custInvoiceAmount'] = $detail->transAmount;
                        $data['localAmount'] = $detail->localAmount;
                        $data['comRptAmount'] = $detail->rptAmount;
                        array_push($finalData, $data);
                    }

                    foreach ($advReceiptsBySegments as $detail) {

                        $data['serviceLineSystemID'] = $detail->serviceLineSystemID;
                        $data['serviceLineCode'] = $detail->serviceLineCode;
                        $data['custInvoiceAmount'] = $detail->transAmount;
                        $data['localAmount'] = $detail->localAmount;
                        $data['comRptAmount'] = $detail->rptAmount;
                        array_push($finalData, $data);
                    }

                }


                else {
                    $data['custInvoiceAmount'] = ($masterData->documentType == 15) ? (ABS($transAmount) * -1) : $transAmount;
                    $data['localAmount'] = ($masterData->documentType == 15) ? (ABS($transAmountLocal) * -1) : $transAmountLocal;
                    $data['comRptAmount'] = ($masterData->documentType == 15) ? (ABS($transAmountRpt) * -1) : $transAmountRpt;
                    array_push($finalData, $data);
                }
            }
        }

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData]];
	}
}