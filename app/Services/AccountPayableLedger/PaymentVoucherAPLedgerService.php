<?php

namespace App\Services\AccountPayableLedger;

use App\helper\TaxService;
use App\Models\AccountsPayableLedger;
use App\Models\BookInvSuppMaster;
use App\Models\DebitNote;
use App\Models\Employee;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\PurchaseReturn;
use App\Models\Taxdetail;
use App\Models\CompanyPolicyMaster;
use App\Models\BookInvSuppDet;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\GeneralLedger\GlPostedDateService;
use App\helper\Helper;

class PaymentVoucherAPLedgerService
{
	public static function processEntry($masterModel)
	{
        $data = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);
        $masterData = PaySupplierInvoiceMaster::with(['bank', 'supplierdetail' => function ($query) {
            $query->selectRaw('SUM(paymentLocalAmount) as localAmount, SUM(paymentComRptAmount) as rptAmount,SUM(supplierPaymentAmount) as transAmount,localCurrencyID,comRptCurrencyID as reportingCurrencyID,supplierPaymentCurrencyID as transCurrencyID,comRptER as reportingCurrencyER,localER as localCurrencyER,supplierPaymentER as transCurrencyER,PayMasterAutoId');
        }, 'advancedetail' => function ($query) {
            $query->selectRaw('SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(supplierTransAmount) as transAmount,localCurrencyID,comRptCurrencyID as reportingCurrencyID,supplierTransCurrencyID as transCurrencyID,comRptER as reportingCurrencyER,localER as localCurrencyER,supplierTransER as transCurrencyER,PayMasterAutoId');
        },'financeperiod_by'])->find($masterModel["autoID"]);

        if($masterData->invoiceType != 3) {
             $validatePostedDate = GlPostedDateService::validatePostedDate($masterModel["autoID"], $masterModel["documentSystemID"]);

            if (!$validatePostedDate['status']) {
                return ['status' => false, 'message' => $validatePostedDate['message']];
            }

            $masterDocumentDate = $validatePostedDate['postedDate'];
            if ($masterData) {
                $data['companySystemID'] = $masterData->companySystemID;
                $data['companyID'] = $masterData->companyID;
                $data['documentSystemID'] = $masterData->documentSystemID;
                $data['documentID'] = $masterData->documentID;
                $data['documentSystemCode'] = $masterModel["autoID"];
                $data['documentCode'] = $masterData->BPVcode;
                $data['documentDate'] = $masterDocumentDate;
                $data['supplierCodeSystem'] = $masterData->BPVsupplierID;
                $data['supplierInvoiceNo'] = 'NA';
                $data['supplierInvoiceDate'] = $masterData->BPVdate;
                if ($masterData->invoiceType == 2) {  //Supplier Payment
                    $data['supplierTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                    $data['supplierTransER'] = $masterData->supplierTransCurrencyER;
                    $data['supplierInvoiceAmount'] = Helper::roundValue(ABS($masterData->supplierdetail[0]->transAmount) * -1);
                    $data['supplierDefaultCurrencyID'] = $masterData->supplierDefCurrencyID;
                    $data['supplierDefaultCurrencyER'] = $masterData->supplierDefCurrencyER;
                    $data['supplierDefaultAmount'] = Helper::roundValue(ABS($masterData->supplierdetail[0]->transAmount) * -1);
                    $data['localCurrencyID'] = $masterData->localCurrencyID;
                    $data['localER'] = $masterData->localCurrencyER;
                    $data['localAmount'] = Helper::roundValue(ABS($masterData->supplierdetail[0]->localAmount) * -1);
                    $data['comRptCurrencyID'] = $masterData->companyRptCurrencyID;
                    $data['comRptER'] = $masterData->companyRptCurrencyER;
                    $data['comRptAmount'] = Helper::roundValue(ABS($masterData->supplierdetail[0]->rptAmount) * -1);
                } else if ($masterData->invoiceType == 5) { //Advance Payment
                    $data['supplierTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                    $data['supplierTransER'] = $masterData->supplierTransCurrencyER;
                    $data['supplierInvoiceAmount'] = Helper::roundValue(ABS($masterData->advancedetail[0]->transAmount) * -1);
                    $data['supplierDefaultCurrencyID'] = $masterData->supplierDefCurrencyID;
                    $data['supplierDefaultCurrencyER'] = $masterData->supplierDefCurrencyER;
                    $data['supplierDefaultAmount'] = Helper::roundValue(ABS($masterData->advancedetail[0]->transAmount) * -1);
                    $data['localCurrencyID'] = $masterData->localCurrencyID;
                    $data['localER'] = $masterData->localCurrencyER;
                    $data['localAmount'] = Helper::roundValue(ABS($masterData->advancedetail[0]->localAmount) * -1);
                    $data['comRptCurrencyID'] = $masterData->companyRptCurrencyID;
                    $data['comRptER'] = $masterData->companyRptCurrencyER;
                    $data['comRptAmount'] = Helper::roundValue(ABS($masterData->advancedetail[0]->rptAmount) * -1);
                }
                $data['isInvoiceLockedYN'] = 0;
                $data['invoiceType'] = $masterData->invoiceType;
                $data['selectedToPaymentInv'] = 0;
                $data['fullyInvoice'] = 0;
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