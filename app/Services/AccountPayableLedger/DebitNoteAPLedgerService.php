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

class DebitNoteAPLedgerService
{
	public static function processEntry($masterModel)
	{
        $data = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);
        $masterData = DebitNote::with(['detail' => function ($query) {
            $query->selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(debitAmount) as transAmount,debitNoteAutoID");
        },'finance_period_by'])->find($masterModel["autoID"]);

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
            $data['documentCode'] = $masterData->debitNoteCode;
            $data['documentDate'] = $masterDocumentDate;
            $data['supplierCodeSystem'] = $masterData->supplierID;
            $data['supplierInvoiceNo'] = 'NA';
            $data['supplierInvoiceDate'] = $masterData->debitNoteDate;
            $data['supplierTransCurrencyID'] = $masterData->supplierTransactionCurrencyID;
            $data['supplierTransER'] = $masterData->supplierTransactionCurrencyER;
            $data['supplierInvoiceAmount'] = ABS($masterData->detail[0]->transAmount) * -1;
            $data['supplierDefaultCurrencyID'] = $masterData->supplierTransactionCurrencyID;
            $data['supplierDefaultCurrencyER'] = $masterData->supplierTransactionCurrencyER;
            $data['supplierDefaultAmount'] = Helper::roundValue(ABS($masterData->detail[0]->transAmount) * -1);
            $data['localCurrencyID'] = $masterData->localCurrencyID;
            $data['localER'] = $masterData->localCurrencyER;
            $data['localAmount'] = Helper::roundValue(ABS($masterData->detail[0]->localAmount) * -1);
            $data['comRptCurrencyID'] = $masterData->companyReportingCurrencyID;
            $data['comRptER'] = $masterData->companyReportingER;
            $data['comRptAmount'] = Helper::roundValue(ABS($masterData->detail[0]->rptAmount) * -1);
            $data['isInvoiceLockedYN'] = 0;
            $data['invoiceType'] = $masterData->documentType;
            $data['selectedToPaymentInv'] = 0;
            $data['fullyInvoice'] = 0;
            $data['createdDateTime'] = Helper::currentDateTime();
            $data['createdUserID'] = $empID->empID;
            $data['createdUserSystemID'] = $empID->employeeSystemID;
            $data['createdPcID'] = gethostname();
            $data['timeStamp'] = Helper::currentDateTime();
            array_push($finalData, $data);
        }

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData]];
	}
}