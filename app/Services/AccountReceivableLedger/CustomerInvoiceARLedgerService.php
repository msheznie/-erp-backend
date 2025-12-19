<?php

namespace App\Services\AccountReceivableLedger;

use App\Models\AccountsReceivableLedger;
use App\Models\AdvanceReceiptDetails;
use App\Models\ChartOfAccount;
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
            $validatePostedDate = GlPostedDateService::validatePostedDate($masterModel["autoID"], $masterModel["documentSystemID"]);

            if (!$validatePostedDate['status']) {
                return ['status' => false, 'message' => $validatePostedDate['message']];
            }

            $masterDocumentDate = $validatePostedDate['postedDate'];

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
            $data['serviceLineSystemID'] = $masterData->serviceLineSystemID;
            $data['serviceLineCode'] = $masterData->serviceLineCode;

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
    
            if($masterData->isPerforma == 3|| $masterData->isPerforma == 4|| $masterData->isPerforma == 5){// item sales invoice
                $data['custInvoiceAmount'] = ABS($masterData->bookingAmountTrans + $taxTrans);
                $data['localAmount'] = \Helper::roundValue(ABS($masterData->bookingAmountLocal + $taxLocal));
                $data['comRptAmount'] = \Helper::roundValue(ABS($masterData->bookingAmountRpt + $taxRpt));
                array_push($finalData, $data);
            }else if($masterData->isPerforma == 2) {
                $processData = self::performDirectInvoiceDetails($masterModel,$data);
                $data['custInvoiceAmount'] = ABS($masterData->bookingAmountTrans + $taxTrans+$processData['_documentTransAmount']);
                $data['localAmount'] = \Helper::roundValue(ABS($masterData->bookingAmountLocal + $taxLocal+$processData['_documentLocalAmount']));
                $data['comRptAmount'] = \Helper::roundValue(ABS($masterData->bookingAmountRpt + $taxRpt+$processData['_documentRptAmount']));

                array_push($finalData, $data);
            }else if( $masterData->isPerforma == 1){
                 $detail = CustomerInvoiceDirectDetail::selectRaw("sum(comRptAmount) as comRptAmount, comRptCurrency, sum(localAmount) as localAmount , localCurrencyER, localCurrency, sum(invoiceAmount) as invoiceAmount, invoiceAmountCurrencyER, invoiceAmountCurrency,comRptCurrencyER, customerID, clientContractID, comments, glSystemID,   serviceLineSystemID,serviceLineCode, sum(VATAmount) as VATAmount, sum(VATAmountLocal) as VATAmountLocal, sum(VATAmountRpt) as VATAmountRpt, sum(VATAmount*invoiceQty) as VATAmountTotal, sum(VATAmountLocal*invoiceQty) as VATAmountLocalTotal, sum(VATAmountRpt*invoiceQty) as VATAmountRptTotal")->with(['contract'])->WHERE('custInvoiceDirectID', $masterModel["autoID"])->groupBy('serviceLineSystemID')->get();
                foreach ($detail as $item) {
                    $data['serviceLineSystemID'] = $item->serviceLineSystemID;
                    $data['serviceLineCode'] = $item->serviceLineCode;

                    $data['custInvoiceAmount'] = ABS($item->invoiceAmount);
                    $data['localAmount'] = \Helper::roundValue(ABS($item->localAmount));
                    $data['comRptAmount'] = \Helper::roundValue(ABS($item->comRptAmount));
                    array_push($finalData, $data);
                }

            }else if ($masterData->isPerforma == 0) {
                $detail = CustomerInvoiceDirectDetail::selectRaw("sum(comRptAmount) as comRptAmount, comRptCurrency, sum(localAmount) as localAmount , localCurrencyER, localCurrency, sum(invoiceAmount) as invoiceAmount, invoiceAmountCurrencyER, invoiceAmountCurrency,comRptCurrencyER, customerID, clientContractID, comments, glSystemID,   serviceLineSystemID,serviceLineCode, sum(VATAmount) as VATAmount, sum(VATAmountLocal) as VATAmountLocal, sum(VATAmountRpt) as VATAmountRpt, sum(VATAmount*invoiceQty) as VATAmountTotal, sum(VATAmountLocal*invoiceQty) as VATAmountLocalTotal, sum(VATAmountRpt*invoiceQty) as VATAmountRptTotal")->with(['contract'])->WHERE('custInvoiceDirectID', $masterModel["autoID"])->groupBy('serviceLineSystemID')->get();
                foreach ($detail as $item) {
                    $data['serviceLineSystemID'] = $item->serviceLineSystemID;
                    $data['serviceLineCode'] = $item->serviceLineCode;

                    $data['custInvoiceAmount'] = ABS($item->invoiceAmount + $item->VATAmountTotal);
                    $data['localAmount'] = \Helper::roundValue(ABS($item->localAmount + $item->VATAmountLocalTotal));
                    $data['comRptAmount'] = \Helper::roundValue(ABS($item->comRptAmount + $item->VATAmountRptTotal));
                    array_push($finalData, $data);
                }

            }else {
                $data['custInvoiceAmount'] = ABS($masterData->invoicedetails[0]->transAmount + $taxTrans);
                $data['localAmount'] = \Helper::roundValue(ABS($masterData->invoicedetails[0]->localAmount + $taxLocal));
                $data['comRptAmount'] = \Helper::roundValue(ABS($masterData->invoicedetails[0]->rptAmount + $taxRpt));
                array_push($finalData, $data);
            }

            
        }

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData]];
	}

    public static function performDirectInvoiceDetails($masterModel,$data){
        $_customerInvoiceDirectDetails = CustomerInvoiceDirectDetail::selectRaw("serviceLineSystemID,serviceLineCode ,sum(comRptAmount) as comRptAmount, comRptCurrency, sum(localAmount) as localAmount , localCurrencyER, localCurrency, sum(invoiceAmount) as invoiceAmount, invoiceAmountCurrencyER, invoiceAmountCurrency,comRptCurrencyER, customerID, clientContractID, comments, glSystemID,   serviceLineSystemID,serviceLineCode, sum(VATAmount) as VATAmount, sum(VATAmountLocal) as VATAmountLocal, sum(VATAmountRpt) as VATAmountRpt, sum(VATAmount*invoiceQty) as VATAmountTotal, sum(VATAmountLocal*invoiceQty) as VATAmountLocalTotal, sum(VATAmountRpt*invoiceQty) as VATAmountRptTotal")->WHERE('custInvoiceDirectID', $masterModel["autoID"])->groupBy('serviceLineSystemID')->get();
        $detailsArray = [];
        $_documentTransAmount = 0; 
        $_documentLocalAmount = 0;
        $_documentRptAmount = 0;


                    foreach ($_customerInvoiceDirectDetails as $item) {
                        $data['serviceLineSystemID'] = $item->serviceLineSystemID;
                        $data['serviceLineCode'] = $item->serviceLineCode;
                        
                        $data['custInvoiceAmount'] = ABS($item->invoiceAmount + $item->VATAmountTotal);
                        $data['localAmount'] = \Helper::roundValue(ABS($item->localAmount + $item->VATAmountLocalTotal));
                        $data['comRptAmount'] = \Helper::roundValue(ABS($item->comRptAmount + $item->VATAmountRptTotal));
                        $chart_Of_account = ChartOfAccount::where('chartOfAccountSystemID', $item->glSystemID)->first();

                        array_push($detailsArray, $data);

                        if($chart_Of_account->controlAccountsSystemID == 2 || $chart_Of_account->controlAccountsSystemID == 5 || $chart_Of_account->controlAccountsSystemID == 3) {
                            $_documentTransAmount -= ($item->invoiceAmount + $item->VATAmountTotal);
                            $_documentLocalAmount -= ($item->localAmount + $item->VATAmountLocalTotal);
                            $_documentRptAmount -= ($item->comRptAmount + $item->VATAmountRptTotal);
                            
                        }else if($chart_Of_account->controlAccountsSystemID == 4) {
                            $_documentTransAmount += $item->invoiceAmount + $item->VATAmountTotal;
                            $_documentLocalAmount += $item->localAmount + $item->VATAmountLocalTotal;
                            $_documentRptAmount += $item->comRptAmount + $item->VATAmountRptTotal;
                        }else{
                            $_documentTransAmount += $item->invoiceAmount + $item->VATAmountTotal;
                            $_documentLocalAmount += $item->localAmount + $item->VATAmountLocalTotal;
                            $_documentRptAmount += $item->comRptAmount + $item->VATAmountRptTotal;
                        }
                    }
        return ['detailsArray' => $detailsArray,'_documentTransAmount' => $_documentTransAmount,'_documentLocalAmount' => $_documentLocalAmount,'_documentRptAmount' =>$_documentRptAmount];

    }
}
