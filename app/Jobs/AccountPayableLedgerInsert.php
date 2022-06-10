<?php

namespace App\Jobs;

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

class AccountPayableLedgerInsert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $masterModel;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($masterModel)
    {
        $this->masterModel = $masterModel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path() . '/logs/accounts_payable_ledger_jobs.log');
        $masterModel = $this->masterModel;
        if (!empty($masterModel)) {
            DB::beginTransaction();
            try {
                $data = [];
                $finalData = [];
                $empID = Employee::find($masterModel['employeeSystemID']);
                switch ($masterModel["documentSystemID"]) {
                    case 15: // Debit Note
                        $masterData = DebitNote::with(['detail' => function ($query) {
                            $query->selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(debitAmount) as transAmount,debitNoteAutoID");
                        },'finance_period_by'])->find($masterModel["autoID"]);

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if($masterData->finance_period_by->isActive == -1){
                            $masterDocumentDate = $masterData->debitNoteDate;
                        }

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
                            $data['supplierDefaultAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->transAmount) * -1);
                            $data['localCurrencyID'] = $masterData->localCurrencyID;
                            $data['localER'] = $masterData->localCurrencyER;
                            $data['localAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->localAmount) * -1);
                            $data['comRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                            $data['comRptER'] = $masterData->companyReportingER;
                            $data['comRptAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->rptAmount) * -1);
                            $data['isInvoiceLockedYN'] = 0;
                            $data['invoiceType'] = $masterData->documentType;
                            $data['selectedToPaymentInv'] = 0;
                            $data['fullyInvoice'] = 0;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdPcID'] = gethostname();
                            $data['timeStamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);
                        }
                        break;
                    case 11: // SI - Supplier Invoice
                        $policyConfirmedToLinkPO = CompanyPolicyMaster::where('companyPolicyCategoryID', 36)
                                                                    ->where('companySystemID', $masterModel["companySystemID"])
                                                                    ->first();

                        $supplierInvoiceDetailLength = BookInvSuppDet::where('bookingSuppMasInvAutoID',$masterModel["autoID"])->groupBy('purchaseOrderID')->get();

                        
                        $masterData = BookInvSuppMaster::with(['detail' => function ($query) {
                            $query->selectRaw("SUM(totLocalAmount) as localAmount, SUM(totRptAmount) as rptAmount,SUM(totTransactionAmount) as transAmount,bookingSuppMasInvAutoID");
                        },'item_details' => function ($query) {
                            $query->selectRaw("SUM(netAmount) as transAmount, SUM(VATAmount*noQty) as transVATAmount,bookingSuppMasInvAutoID");
                        }, 'directdetail' => function ($query) {
                            $query->selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DIAmount) as transAmount,directInvoiceAutoID");
                        },'financeperiod_by'])->find($masterModel["autoID"]);

                        $tax = Taxdetail::selectRaw("SUM(localAmount) as localAmount, SUM(rptAmount) as rptAmount,SUM(amount) as transAmount,localCurrencyID,rptCurrencyID as reportingCurrencyID,currency as supplierTransactionCurrencyID,currencyER as supplierTransactionER,rptCurrencyER as companyReportingER,localCurrencyER")->WHERE('documentSystemCode', $masterModel["autoID"])->WHERE('documentSystemID', $masterModel["documentSystemID"])->first();

                        $taxLocal = 0;
                        $taxRpt = 0;
                        $taxTrans = 0;

                        $retentionPercentage = ($masterData->retentionPercentage > 0) ? $masterData->retentionPercentage : 0;

                        if ($tax) {
                            $taxLocal = $tax->localAmount;
                            $taxRpt = $tax->rptAmount;
                            $taxTrans = $tax->transAmount;
                        }

                        if ($masterData->documentType == 1 && $masterData->rcmActivated == 1) {
                            $taxLocal = 0;
                            $taxRpt = 0;
                            $taxTrans = 0;
                        }

                        $poInvoiceDirectLocalExtCharge = 0;
                        $poInvoiceDirectRptExtCharge = 0;
                        $poInvoiceDirectTransExtCharge = 0;

                        if(isset($masterData->directdetail[0])){
                            $poInvoiceDirectLocalExtCharge = $masterData->directdetail[0]->localAmount;
                            $poInvoiceDirectRptExtCharge = $masterData->directdetail[0]->rptAmount;
                            $poInvoiceDirectTransExtCharge = $masterData->directdetail[0]->transAmount;
                        }

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if($masterData->financeperiod_by->isActive == -1){
                            $masterDocumentDate = $masterData->bookingDate;
                        }

                        if ($masterData) {
                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['documentSystemID'] = $masterData->documentSystemID;
                            $data['documentID'] = $masterData->documentID;
                            $data['documentSystemCode'] = $masterModel["autoID"];
                            $data['documentCode'] = $masterData->bookingInvCode;
                            $data['documentDate'] = $masterDocumentDate;
                            $data['supplierCodeSystem'] = $masterData->supplierID;
                            $data['supplierInvoiceNo'] = $masterData->supplierInvoiceNo;
                            $data['supplierInvoiceDate'] = $masterData->supplierInvoiceDate;

                            if ($masterData->documentType == 0 || $masterData->documentType == 2) { // check if it is supplier invoice
                                $data['supplierTransCurrencyID'] = $masterData->supplierTransactionCurrencyID;
                                $data['supplierTransER'] = \Helper::roundValue(($masterData->detail[0]->transAmount + $poInvoiceDirectTransExtCharge + $taxTrans) / ($masterData->detail[0]->transAmount + $poInvoiceDirectTransExtCharge + $taxTrans));
                                $data['supplierInvoiceAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->transAmount + $poInvoiceDirectTransExtCharge + $taxTrans));
                                $data['supplierDefaultCurrencyID'] = $masterData->supplierTransactionCurrencyID;
                                $data['supplierDefaultCurrencyER'] = \Helper::roundValue(($masterData->detail[0]->transAmount + $poInvoiceDirectTransExtCharge + $taxTrans) / ($masterData->detail[0]->transAmount + $poInvoiceDirectTransExtCharge + $taxTrans));
                                $data['supplierDefaultAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->transAmount + $poInvoiceDirectTransExtCharge + $taxTrans));
                                $data['localCurrencyID'] = $masterData->localCurrencyID;
                                $data['localER'] = round(($masterData->detail[0]->transAmount + $poInvoiceDirectTransExtCharge + $taxTrans) / ($masterData->detail[0]->localAmount + $poInvoiceDirectLocalExtCharge + $taxLocal), 8);
                                $data['localAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->localAmount + $poInvoiceDirectLocalExtCharge + $taxLocal));
                                $data['comRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                                $data['comRptER'] = round(($masterData->detail[0]->transAmount + $poInvoiceDirectTransExtCharge + $taxTrans) / ($masterData->detail[0]->rptAmount + $poInvoiceDirectRptExtCharge + $taxRpt), 8);
                                $data['comRptAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->rptAmount + $poInvoiceDirectRptExtCharge + $taxRpt));
                                
                                if ($policyConfirmedToLinkPO['isYesNO'] == 1 && sizeof($supplierInvoiceDetailLength) == 1) {
                                    $data['purchaseOrderID'] = $supplierInvoiceDetailLength[0]['purchaseOrderID'];
                                }

                            } else if ($masterData->documentType == 3) { // check if it is supplier invoice

                                $transAmount = (isset($masterData->item_details[0]->transAmount) ? $masterData->item_details[0]->transAmount : 0) + (isset($masterData->item_details[0]->transVATAmount) ? $masterData->item_details[0]->transVATAmount : 0);

                                $directItemCurrencyConversion = \Helper::currencyConversion($masterData->companySystemID, $masterData->supplierTransactionCurrencyID, $masterData->supplierTransactionCurrencyID, $transAmount);

                                $data['supplierTransCurrencyID'] = $masterData->supplierTransactionCurrencyID;
                                $data['supplierTransER'] = \Helper::roundValue(($transAmount + $poInvoiceDirectTransExtCharge ) / ($transAmount + $poInvoiceDirectTransExtCharge ));
                                $data['supplierInvoiceAmount'] = \Helper::roundValue(ABS($transAmount + $poInvoiceDirectTransExtCharge ));
                                $data['supplierDefaultCurrencyID'] = $masterData->supplierTransactionCurrencyID;
                                $data['supplierDefaultCurrencyER'] = \Helper::roundValue(($transAmount + $poInvoiceDirectTransExtCharge ) / ($transAmount + $poInvoiceDirectTransExtCharge ));
                                $data['supplierDefaultAmount'] = \Helper::roundValue(ABS($transAmount + $poInvoiceDirectTransExtCharge ));
                                $data['localCurrencyID'] = $masterData->localCurrencyID;
                                $data['localER'] = round(($transAmount + $poInvoiceDirectTransExtCharge ) / ($directItemCurrencyConversion['localAmount'] + $poInvoiceDirectLocalExtCharge), 8);
                                $data['localAmount'] = \Helper::roundValue(ABS($directItemCurrencyConversion['localAmount'] + $poInvoiceDirectLocalExtCharge));
                                $data['comRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                                $data['comRptER'] = round(($transAmount + $poInvoiceDirectTransExtCharge ) / ($directItemCurrencyConversion['reportingAmount'] + $poInvoiceDirectRptExtCharge), 8);
                                $data['comRptAmount'] = \Helper::roundValue(ABS($directItemCurrencyConversion['reportingAmount'] + $poInvoiceDirectRptExtCharge));
                            } else {
                                $data['supplierTransCurrencyID'] = $masterData->supplierTransactionCurrencyID;
                                $data['supplierTransER'] = $masterData->supplierTransactionCurrencyER;
                                $data['supplierInvoiceAmount'] = \Helper::roundValue(ABS($masterData->directdetail[0]->transAmount + $taxTrans));
                                $data['supplierDefaultCurrencyID'] = $masterData->supplierTransactionCurrencyID;
                                $data['supplierDefaultCurrencyER'] = $masterData->supplierTransactionCurrencyER;
                                $data['supplierDefaultAmount'] = \Helper::roundValue(ABS($masterData->directdetail[0]->transAmount + $taxTrans));
                                $data['localCurrencyID'] = $masterData->localCurrencyID;
                                $data['localER'] = $masterData->localCurrencyER;
                                $data['localAmount'] = \Helper::roundValue(ABS($masterData->directdetail[0]->localAmount + $taxLocal));
                                $data['comRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                                $data['comRptER'] = $masterData->companyReportingER;
                                $data['comRptAmount'] = \Helper::roundValue(ABS($masterData->directdetail[0]->rptAmount + $taxRpt));
                            }
                            $data['isInvoiceLockedYN'] = 0;
                            $data['invoiceType'] = $masterData->documentType;
                            $data['selectedToPaymentInv'] = 0;
                            $data['fullyInvoice'] = 0;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdPcID'] = gethostname();
                            $data['timeStamp'] = \Helper::currentDateTime();

                            $retentionTrans = 0;
                            $retentionLocal = 0;
                            $retentionInvoiceAmount = 0;
                            $retentionRpt = 0;
                            if ($retentionPercentage > 0) {
                                if ($masterData->documentType != 4) {
                                    if (!TaxService::isSupplierInvoiceRcmActivated($masterModel["autoID"])) {
                                        if ($masterData->documentType == 0) {
                                            $vatDetails = TaxService::processPoBasedSupllierInvoiceVAT($masterModel["autoID"]);
                                            $totalVATAmount = 0;
                                            $totalVATAmountLocal = 0;
                                            $totalVATAmountRpt = 0;
                                            $totalVATAmount = $vatDetails['totalVAT'];
                                            $totalVATAmountLocal = $vatDetails['totalVATLocal'];
                                            $totalVATAmountRpt = $vatDetails['totalVATRpt'];

                                            $retentionInvoiceAmount = ($data['supplierInvoiceAmount'] - $totalVATAmount) * ($retentionPercentage / 100);
                                            $retentionTrans = ($data['supplierDefaultAmount'] - $totalVATAmount) * ($retentionPercentage / 100);
                                            $retentionLocal = ($data['localAmount'] - $totalVATAmountLocal) * ($retentionPercentage / 100);
                                            $retentionRpt = ($data['comRptAmount'] - $totalVATAmountRpt) * ($retentionPercentage / 100);


                                            $data['supplierInvoiceAmount'] = $data['supplierInvoiceAmount'] * (1 - ($retentionPercentage / 100));
                                            $data['supplierDefaultAmount'] = $data['supplierDefaultAmount'] * (1 - ($retentionPercentage / 100));
                                            $data['localAmount'] = $data['localAmount'] * (1 - ($retentionPercentage / 100));
                                            $data['comRptAmount'] = $data['comRptAmount'] * (1 - ($retentionPercentage / 100));
                                        }
                                        if ($masterData->documentType == 1) {
                                            $directVATDetails = TaxService::processDirectSupplierInvoiceVAT($masterModel["autoID"],
                                                $masterModel["documentSystemID"]);
                                            $totalVATAmount = 0;
                                            $totalVATAmountLocal = 0;
                                            $totalVATAmountRpt = 0;
                                            $totalVATAmount = \Helper::roundValue(ABS($directVATDetails['masterVATTrans']));
                                            $totalVATAmountLocal = \Helper::roundValue(ABS($directVATDetails['masterVATLocal']));
                                            $totalVATAmountRpt = \Helper::roundValue(ABS($directVATDetails['masterVATRpt']));

                                            $retentionInvoiceAmount = ($data['supplierInvoiceAmount'] - $totalVATAmount) * ($retentionPercentage / 100);
                                            $retentionTrans = ($data['supplierDefaultAmount'] - $totalVATAmount) * ($retentionPercentage / 100);
                                            $retentionLocal = ($data['localAmount'] - $totalVATAmountLocal) * ($retentionPercentage / 100);
                                            $retentionRpt = ($data['comRptAmount'] - $totalVATAmountRpt) * ($retentionPercentage / 100);


                                            $data['supplierInvoiceAmount'] = $data['supplierInvoiceAmount'] * (1 - ($retentionPercentage / 100));
                                            $data['supplierDefaultAmount'] = $data['supplierDefaultAmount'] * (1 - ($retentionPercentage / 100));
                                            $data['localAmount'] = $data['localAmount'] * (1 - ($retentionPercentage / 100));
                                            $data['comRptAmount'] = $data['comRptAmount'] * (1 - ($retentionPercentage / 100));
                                        }
                                    }
                                    if (TaxService::isSupplierInvoiceRcmActivated($masterModel["autoID"])) {

                                        $retentionInvoiceAmount = $data['supplierInvoiceAmount'] * ($retentionPercentage / 100);
                                        $retentionTrans = $data['supplierDefaultAmount'] - $totalVATAmount * ($retentionPercentage / 100);
                                        $retentionLocal = $data['localAmount'] - $totalVATAmountLocal * ($retentionPercentage / 100);
                                        $retentionRpt = $data['comRptAmount'] - $totalVATAmountRpt * ($retentionPercentage / 100);

                                        $data['supplierInvoiceAmount'] = $data['supplierInvoiceAmount'] * (1 - ($retentionPercentage / 100));
                                        $data['supplierDefaultAmount'] = $data['supplierDefaultAmount'] * (1 - ($retentionPercentage / 100));
                                        $data['localAmount'] = $data['localAmount'] * (1 - ($retentionPercentage / 100));
                                        $data['comRptAmount'] = $data['comRptAmount'] * (1 - ($retentionPercentage / 100));
                                    }
                                }
                            } 

                            array_push($finalData, $data);

                            if ($retentionPercentage > 0) {
                                if ($masterData->documentType != 4) {
                                    $data['supplierInvoiceAmount'] = $retentionInvoiceAmount;
                                    $data['supplierDefaultAmount'] = $retentionTrans;
                                    $data['localAmount'] = $retentionLocal;
                                    $data['comRptAmount'] = $retentionRpt;
                                    $data['isRetention'] = 1;
                                    array_push($finalData, $data);
                                }
                            } else {
                                $data['isRetention'] = 0;
                            }
                        }
                        break;
                    case 4: // Payment Voucher
                        $masterData = PaySupplierInvoiceMaster::with(['bank', 'supplierdetail' => function ($query) {
                            $query->selectRaw('SUM(paymentLocalAmount) as localAmount, SUM(paymentComRptAmount) as rptAmount,SUM(supplierPaymentAmount) as transAmount,localCurrencyID,comRptCurrencyID as reportingCurrencyID,supplierPaymentCurrencyID as transCurrencyID,comRptER as reportingCurrencyER,localER as localCurrencyER,supplierPaymentER as transCurrencyER,PayMasterAutoId');
                        }, 'advancedetail' => function ($query) {
                            $query->selectRaw('SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(supplierTransAmount) as transAmount,localCurrencyID,comRptCurrencyID as reportingCurrencyID,supplierTransCurrencyID as transCurrencyID,comRptER as reportingCurrencyER,localER as localCurrencyER,supplierTransER as transCurrencyER,PayMasterAutoId');
                        },'financeperiod_by'])->find($masterModel["autoID"]);

                        if($masterData->invoiceType != 3) {
                            $masterDocumentDate = date('Y-m-d H:i:s');
                            if ($masterData->financeperiod_by->isActive == -1) {
                                $masterDocumentDate = $masterData->BPVdate;
                            }
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
                                    $data['supplierInvoiceAmount'] = \Helper::roundValue(ABS($masterData->supplierdetail[0]->transAmount) * -1);
                                    $data['supplierDefaultCurrencyID'] = $masterData->supplierDefCurrencyID;
                                    $data['supplierDefaultCurrencyER'] = $masterData->supplierDefCurrencyER;
                                    $data['supplierDefaultAmount'] = \Helper::roundValue(ABS($masterData->supplierdetail[0]->transAmount) * -1);
                                    $data['localCurrencyID'] = $masterData->localCurrencyID;
                                    $data['localER'] = $masterData->localCurrencyER;
                                    $data['localAmount'] = \Helper::roundValue(ABS($masterData->supplierdetail[0]->localAmount) * -1);
                                    $data['comRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                    $data['comRptER'] = $masterData->companyRptCurrencyER;
                                    $data['comRptAmount'] = \Helper::roundValue(ABS($masterData->supplierdetail[0]->rptAmount) * -1);
                                } else if ($masterData->invoiceType == 5) { //Advance Payment
                                    $data['supplierTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                                    $data['supplierTransER'] = $masterData->supplierTransCurrencyER;
                                    $data['supplierInvoiceAmount'] = \Helper::roundValue(ABS($masterData->advancedetail[0]->transAmount) * -1);
                                    $data['supplierDefaultCurrencyID'] = $masterData->supplierDefCurrencyID;
                                    $data['supplierDefaultCurrencyER'] = $masterData->supplierDefCurrencyER;
                                    $data['supplierDefaultAmount'] = \Helper::roundValue(ABS($masterData->advancedetail[0]->transAmount) * -1);
                                    $data['localCurrencyID'] = $masterData->localCurrencyID;
                                    $data['localER'] = $masterData->localCurrencyER;
                                    $data['localAmount'] = \Helper::roundValue(ABS($masterData->advancedetail[0]->localAmount) * -1);
                                    $data['comRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                    $data['comRptER'] = $masterData->companyRptCurrencyER;
                                    $data['comRptAmount'] = \Helper::roundValue(ABS($masterData->advancedetail[0]->rptAmount) * -1);
                                }
                                $data['isInvoiceLockedYN'] = 0;
                                $data['invoiceType'] = $masterData->invoiceType;
                                $data['selectedToPaymentInv'] = 0;
                                $data['fullyInvoice'] = 0;
                                $data['createdDateTime'] = \Helper::currentDateTime();
                                $data['createdUserID'] = $empID->empID;
                                $data['createdUserSystemID'] = $empID->employeeSystemID;
                                $data['createdPcID'] = gethostname();
                                $data['timeStamp'] = \Helper::currentDateTime();
                                array_push($finalData, $data);
                            }
                        }
                        break;
                    case 24: // Purchase return
                        $masterData = PurchaseReturn::with(['details' => function ($query) {
                            $query->selectRaw("SUM(noQty * GRVcostPerUnitLocalCur) as localAmount, SUM(noQty * GRVcostPerUnitComRptCur) as rptAmount,SUM(GRVcostPerUnitSupTransCur*noQty) as transAmount,purhaseReturnAutoID, SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount");
                            $query->groupBy("purhaseReturnAutoID");
                        }])->find($masterModel["autoID"]);

                        $masterDocumentDate = date('Y-m-d H:i:s');

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
                            $data['supplierInvoiceAmount'] = \Helper::roundValue(ABS((($valEligible) ? $masterData->details[0]->transAmount + $masterData->details[0]->transVATAmount : $masterData->details[0]->transAmount)) * -1);
                            $data['supplierDefaultCurrencyID'] = $masterData->supplierTransactionCurrencyID;
                            $data['supplierDefaultCurrencyER'] = $masterData->supplierTransactionER;
                            $data['supplierDefaultAmount'] = \Helper::roundValue(ABS((($valEligible) ? $masterData->details[0]->transAmount + $masterData->details[0]->transVATAmount : $masterData->details[0]->transAmount)) * -1);
                            $data['localCurrencyID'] = $masterData->localCurrencyID;
                            $data['localER'] = $masterData->localCurrencyER;
                            $data['localAmount'] = \Helper::roundValue(ABS((($valEligible) ? $masterData->details[0]->localAmount + $masterData->details[0]->localVATAmount : $masterData->details[0]->localAmount)) * -1);
                            $data['comRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                            $data['comRptER'] = $masterData->companyReportingER;
                            $data['comRptAmount'] = \Helper::roundValue(ABS((($valEligible) ? $masterData->details[0]->rptAmount + $masterData->details[0]->rptVATAmount : $masterData->details[0]->rptAmount)) * -1);
                            $data['isInvoiceLockedYN'] = 0;
                            $data['invoiceType'] = 7;
                            $data['selectedToPaymentInv'] = 0;
                            $data['fullyInvoice'] = 0;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdPcID'] = gethostname();
                            $data['timeStamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);
                        }
                        break;
                    default:
                        Log::warning('Document ID not found ' . date('H:i:s'));
                }
                if ($finalData) {
                    Log::info($finalData);
                    //$apLedgerInsert = AccountsPayableLedger::insert($finalData);
                    foreach ($finalData as $data)
                    {
                        AccountsPayableLedger::create($data);
                    }

                    Log::info('Successfully inserted to AP table ' . date('H:i:s'));
                    DB::commit();
                }

            } catch
            (\Exception $e) {
                DB::rollback();
                Log::error($this->failed($e));
            }
        }
    }

    public
    function failed($exception)
    {
        return $exception->getMessage();
    }
}
