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

class PurchaseReturnAPLedgerService
{
	public static function processEntry($masterModel)
	{
        $data = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);
        $masterData = PurchaseReturn::with(['details' => function ($query) {
            $query->selectRaw("SUM(noQty * GRVcostPerUnitLocalCur) as localAmount, SUM(noQty * GRVcostPerUnitComRptCur) as rptAmount,SUM(GRVcostPerUnitSupTransCur*noQty) as transAmount,purhaseReturnAutoID, SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount");
            $query->groupBy("purhaseReturnAutoID");
        }])->find($masterModel["autoID"]);

        $validatePostedDate = GlPostedDateService::validatePostedDate($masterModel["autoID"], $masterModel["documentSystemID"]);

        if (!$validatePostedDate['status']) {
            return ['status' => false, 'message' => $validatePostedDate['message']];
        }

        $masterDocumentDate = $validatePostedDate['postedDate'];

        $valEligible = TaxService::checkGRVVATEligible($masterData->companySystemID, $masterData->supplierID);

        if ($masterData) {
            $data['companySystemID'] = $masterData->companySystemID;
            $data['companyID'] = $masterData->companyID;
            $data['documentSystemID'] = $masterData->documentSystemID;
            $data['documentID'] = $masterData->documentID;
            $data['documentSystemCode'] = $masterModel["autoID"];
            $data['documentCode'] = $masterData->purchaseReturnCode;
            $data['documentDate'] = $masterDocumentDate;
            $data['supplierCodeSystem'] = $masterData->supplierID;
            $data['supplierInvoiceNo'] = 'NA';
            $data['supplierInvoiceDate'] = $masterData->purchaseReturnDate;
            $data['supplierTransCurrencyID'] = $masterData->supplierTransactionCurrencyID;
            $data['supplierTransER'] = $masterData->supplierTransactionER;
            $data['supplierInvoiceAmount'] = Helper::roundValue(ABS((($valEligible) ? $masterData->details[0]->transAmount + $masterData->details[0]->transVATAmount : $masterData->details[0]->transAmount)) * -1);
            $data['supplierDefaultCurrencyID'] = $masterData->supplierTransactionCurrencyID;
            $data['supplierDefaultCurrencyER'] = $masterData->supplierTransactionER;
            $data['supplierDefaultAmount'] = Helper::roundValue(ABS((($valEligible) ? $masterData->details[0]->transAmount + $masterData->details[0]->transVATAmount : $masterData->details[0]->transAmount)) * -1);
            $data['localCurrencyID'] = $masterData->localCurrencyID;
            $data['localER'] = $masterData->localCurrencyER;
            $data['localAmount'] = Helper::roundValue(ABS((($valEligible) ? $masterData->details[0]->localAmount + $masterData->details[0]->localVATAmount : $masterData->details[0]->localAmount)) * -1);
            $data['comRptCurrencyID'] = $masterData->companyReportingCurrencyID;
            $data['comRptER'] = $masterData->companyReportingER;
            $data['comRptAmount'] = Helper::roundValue(ABS((($valEligible) ? $masterData->details[0]->rptAmount + $masterData->details[0]->rptVATAmount : $masterData->details[0]->rptAmount)) * -1);
            $data['isInvoiceLockedYN'] = 0;
            $data['invoiceType'] = 7;
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