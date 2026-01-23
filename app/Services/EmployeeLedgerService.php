<?php

namespace App\Services;

use App\helper\TaxService;
use App\Models\EmployeeLedger;
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
use App\helper\Helper;

class EmployeeLedgerService
{
	public static function postLedgerEntry($masterModel)
	{
        $data = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);
        switch ($masterModel["documentSystemID"]) {
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
                    $data['employeeSystemID'] = $masterData->employeeID;
                    $data['supplierInvoiceNo'] = $masterData->supplierInvoiceNo;
                    $data['supplierInvoiceDate'] = $masterData->supplierInvoiceDate;

                   
                    $data['supplierTransCurrencyID'] = $masterData->supplierTransactionCurrencyID;
                    $data['supplierTransER'] = $masterData->supplierTransactionCurrencyER;
                    $data['supplierInvoiceAmount'] = Helper::roundValue(ABS($masterData->directdetail[0]->transAmount + $taxTrans));
                    $data['supplierDefaultCurrencyID'] = $masterData->supplierTransactionCurrencyID;
                    $data['supplierDefaultCurrencyER'] = $masterData->supplierTransactionCurrencyER;
                    $data['supplierDefaultAmount'] = Helper::roundValue(ABS($masterData->directdetail[0]->transAmount + $taxTrans));
                    $data['localCurrencyID'] = $masterData->localCurrencyID;
                    $data['localER'] = $masterData->localCurrencyER;
                    $data['localAmount'] = Helper::roundValue(ABS($masterData->directdetail[0]->localAmount + $taxLocal));
                    $data['comRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                    $data['comRptER'] = $masterData->companyReportingER;
                    $data['comRptAmount'] = Helper::roundValue(ABS($masterData->directdetail[0]->rptAmount + $taxRpt));
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
                break;
            case 4: // Payment Voucher
                $masterData = PaySupplierInvoiceMaster::with(['bank', 'supplierdetail' => function ($query) {
                    $query->selectRaw('SUM(paymentLocalAmount) as localAmount, SUM(paymentComRptAmount) as rptAmount,SUM(supplierPaymentAmount) as transAmount,localCurrencyID,comRptCurrencyID as reportingCurrencyID,supplierPaymentCurrencyID as transCurrencyID,comRptER as reportingCurrencyER,localER as localCurrencyER,supplierPaymentER as transCurrencyER,PayMasterAutoId');
                }, 'advancedetail' => function ($query) {
                    $query->selectRaw('SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(supplierTransAmount) as transAmount,localCurrencyID,comRptCurrencyID as reportingCurrencyID,supplierTransCurrencyID as transCurrencyID,comRptER as reportingCurrencyER,localER as localCurrencyER,supplierTransER as transCurrencyER,PayMasterAutoId');
                },'financeperiod_by'])->find($masterModel["autoID"]);

                if($masterData->invoiceType == 6 || $masterData->invoiceType == 7) {
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
                        $data['employeeSystemID'] = $masterData->directPaymentPayeeEmpID;
                        $data['supplierInvoiceNo'] = 'NA';
                        $data['supplierInvoiceDate'] = $masterData->BPVdate;
                        if ($masterData->invoiceType == 6) {  //employee Payment
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
                        } else if ($masterData->invoiceType == 7) { //Advance Payment
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
                break;
            case 15: // Debite Note

                // $masterData = DebitNote::with(['detail' => function ($query) {
                //     $query->selectRaw('SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount');
                // }])->find($masterModel["autoID"]);

                $masterData = DebitNote::with(['detail'])->find($masterModel["autoID"]);

                $localAmount = 0;
                $rptAmount = 0;
                foreach($masterData['detail'] as $detail)
                {
                    $localAmount+=$detail->localAmount;
                    $rptAmount+=$detail->comRptAmount;
                }

                if($masterData->type == 2) {
                    $masterDocumentDate = date('Y-m-d H:i:s');

                    if ($masterData) {
                        $data['companySystemID'] = $masterData->companySystemID;
                        $data['companyID'] = $masterData->companyID;
                        $data['documentSystemID'] = $masterData->documentSystemID;
                        $data['documentID'] = $masterData->documentID;
                        $data['documentSystemCode'] = $masterModel["autoID"];
                        $data['documentCode'] = $masterData->debitNoteCode;
                        $data['documentDate'] = $masterData->debitNoteDate;
                        $data['employeeSystemID'] = $masterData->empID;
                        $data['supplierInvoiceNo'] = 'NA';
                        $data['supplierInvoiceDate'] = $masterData->debitNoteDate;
                        $data['supplierTransCurrencyID'] = $masterData->supplierTransactionCurrencyID;
                        $data['supplierTransER'] = $masterData->supplierTransactionCurrencyER;
                        $data['supplierInvoiceAmount'] = NULL;
                        $data['supplierDefaultCurrencyID'] = NULL;
                        $data['supplierDefaultCurrencyER'] = NULL;
                        $data['supplierDefaultAmount'] = NULL;
                        $data['localCurrencyID'] = $masterData->localCurrencyID;
                        $data['localER'] = $masterData->localCurrencyER;
                        $data['localAmount'] = Helper::roundValue(ABS($localAmount) * -1);
                        $data['comRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                        $data['comRptER'] = $masterData->companyReportingER;
                        $data['comRptAmount'] = Helper::roundValue(ABS($rptAmount) * -1);
                        $data['isInvoiceLockedYN'] = 0;
                        $data['invoiceType'] = $masterData->type;
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
                break;
           default:
                Log::warning('Document ID not found ' . date('H:i:s'));
        }
        if ($finalData) {
            foreach ($finalData as $data)
            {
                EmployeeLedger::create($data);
            }

            Log::info('Successfully inserted to AP table ' . date('H:i:s'));
        }

        return ['status' => true];
	}
}
