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
use App\Models\Tax;
use App\Models\SupplierMaster;

class SupplierInvoiceAPLedgerService
{
	public static function processEntry($masterModel)
	{
        $data = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);
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

            $whtTrans = 0;
            $whtLocal = 0;
            $whtInvoiceAmount = 0;
            $whtRpt = 0;


            $whtAmountConTran = 0;
            $whtAmountConInvoicet = 0;
            $whtAmountConLocal = 0;
            $whtAmountConRpt = 0;


      

          

            if ($retentionPercentage > 0) {
                if ($masterData->documentType != 4) {
                    if ($masterData->documentType == 0) {
                        if (!TaxService::isSupplierInvoiceRcmActivated($masterModel["autoID"])) {

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
                        else {
                            $retentionInvoiceAmount = $data['supplierInvoiceAmount'] * ($retentionPercentage / 100);
                            $retentionTrans = $data['supplierDefaultAmount'] * ($retentionPercentage / 100);
                            $retentionLocal = $data['localAmount'] * ($retentionPercentage / 100);
                            $retentionRpt = $data['comRptAmount'] * ($retentionPercentage / 100);

                            $data['supplierInvoiceAmount'] = $data['supplierInvoiceAmount'] * (1 - ($retentionPercentage / 100));
                            $data['supplierDefaultAmount'] = $data['supplierDefaultAmount'] * (1 - ($retentionPercentage / 100));
                            $data['localAmount'] = $data['localAmount'] * (1 - ($retentionPercentage / 100));
                            $data['comRptAmount'] = $data['comRptAmount'] * (1 - ($retentionPercentage / 100));
                        }

                    }

                    else if ($masterData->documentType == 1) {
                        $directVATDetails = TaxService::processDirectSupplierInvoiceVAT($masterModel["autoID"],
                            $masterModel["documentSystemID"]);
                        $totalVATAmount = 0;
                        $totalVATAmountLocal = 0;
                        $totalVATAmountRpt = 0;
                        $totalVATAmount = \Helper::roundValue(ABS($directVATDetails['masterVATTrans']));
                        $totalVATAmountLocal = \Helper::roundValue(ABS($directVATDetails['masterVATLocal']));
                        $totalVATAmountRpt = \Helper::roundValue(ABS($directVATDetails['masterVATRpt']));
                        if ($masterData->rcmActivated != 1) {
                            $retentionInvoiceAmount = ($data['supplierInvoiceAmount'] - $totalVATAmount) * ($retentionPercentage / 100);
                            $retentionTrans = ($data['supplierDefaultAmount'] - $totalVATAmount) * ($retentionPercentage / 100);
                            $retentionLocal = ($data['localAmount'] - $totalVATAmountLocal) * ($retentionPercentage / 100);
                            $retentionRpt = ($data['comRptAmount'] - $totalVATAmountRpt) * ($retentionPercentage / 100);
                        } else {
                            $retentionInvoiceAmount = $data['supplierInvoiceAmount'] * ($retentionPercentage / 100);
                            $retentionTrans = $data['supplierDefaultAmount'] * ($retentionPercentage / 100);
                            $retentionLocal = $data['localAmount'] * ($retentionPercentage / 100);
                            $retentionRpt = $data['comRptAmount'] * ($retentionPercentage / 100);
                        }


                        $data['supplierInvoiceAmount'] = $data['supplierInvoiceAmount'] * (1 - ($retentionPercentage / 100));
                        $data['supplierDefaultAmount'] = $data['supplierDefaultAmount'] * (1 - ($retentionPercentage / 100));
                        $data['localAmount'] = $data['localAmount'] * (1 - ($retentionPercentage / 100));
                        $data['comRptAmount'] = $data['comRptAmount'] * (1 - ($retentionPercentage / 100));

                    } else if ($masterData->documentType == 3) {
                        $directVATDetails = TaxService::processSupplierInvoiceItemsVAT($masterModel["autoID"]);
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
                    } else {
                        $retentionInvoiceAmount = $data['supplierInvoiceAmount'] * ($retentionPercentage / 100);
                        $retentionTrans = $data['supplierDefaultAmount'] * ($retentionPercentage / 100);
                        $retentionLocal = $data['localAmount'] * ($retentionPercentage / 100);
                        $retentionRpt = $data['comRptAmount'] * ($retentionPercentage / 100);

                        $data['supplierInvoiceAmount'] = $data['supplierInvoiceAmount'] * (1 - ($retentionPercentage / 100));
                        $data['supplierDefaultAmount'] = $data['supplierDefaultAmount'] * (1 - ($retentionPercentage / 100));
                        $data['localAmount'] = $data['localAmount'] * (1 - ($retentionPercentage / 100));
                        $data['comRptAmount'] = $data['comRptAmount'] * (1 - ($retentionPercentage / 100));
                    }
                }
            }


            if ($masterData->whtApplicable) {

                if ($masterData->documentType != 4) {
                    if ($masterData->documentType == 0 || $masterData->documentType == 2 || $masterData->documentType == 1 || $masterData->documentType == 3) {

                        $currencyWht = \Helper::currencyConversion($masterData->companySystemID, $masterData->supplierTransactionCurrencyID, $masterData->supplierTransactionCurrencyID, $masterData->whtAmount);
                        $whtAmountConTran = $masterData->whtAmount;
                        $whtAmountConInvoicet = $masterData->whtAmount;
                        $whtAmountConLocal = \Helper::roundValue($currencyWht['localAmount']);
                        $whtAmountConRpt = \Helper::roundValue($currencyWht['reportingAmount']);
                        $whtSupplier = null;
                        $taxSetup = Tax::where('taxMasterAutoID',$masterData->whtType)->first();
                        $whtAuthority = null;
                        $currencyID= null;
                        $localER = null;
                        $comRptER = null;
                        if($taxSetup)
                        {
                            $whtAuthority = $taxSetup->authorityAutoID;
                            $supplier = SupplierMaster::where('supplierCodeSystem',$whtAuthority)->first();
                            $whtSupplier = $supplier->supplierCodeSystem;

                            $supplierCurrencies = DB::table('suppliercurrency')
                            ->leftJoin('currencymaster', 'suppliercurrency.currencyID', '=', 'currencymaster.currencyID')
                            ->where('supplierCodeSystem', '=', $whtSupplier)->where('isDefault',-1)->first();

                            $currencyID = $supplierCurrencies->currencyID;

                            $companyCurrencyConversion = \Helper::currencyConversion($masterData->companySystemID, $currencyID, $currencyID, 0);
                            $localER = $companyCurrencyConversion['trasToLocER'];
                            $comRptER = $companyCurrencyConversion['trasToRptER'];
                        }

         
                            $whtInvoiceAmount = ($whtAmountConInvoicet);
                            $whtTrans = ($whtAmountConTran);
                            $whtLocal = ($whtAmountConLocal);
                            $whtRpt = ($whtAmountConRpt);

                            $data['supplierInvoiceAmount'] = $data['supplierInvoiceAmount'] - $whtInvoiceAmount;
                            $data['supplierDefaultAmount'] = $data['supplierDefaultAmount'] - $whtTrans;
                            $data['localAmount'] = $data['localAmount'] - $whtLocal;
                            $data['comRptAmount'] = $data['comRptAmount'] - $whtRpt;
                        

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

            if ($masterData->whtApplicable) {
                if ($masterData->documentType == 0 || $masterData->documentType == 2 || $masterData->documentType == 1 || $masterData->documentType == 3){
                    $data['supplierCodeSystem'] = $whtSupplier;
                    $data['supplierTransCurrencyID']  = $currencyID;
                    $data['supplierDefaultCurrencyID'] = $currencyID;
                    $data['localER'] = $localER;
                    $data['comRptER'] = $comRptER;
                    $data['supplierInvoiceAmount'] = $whtInvoiceAmount;
                    $data['supplierDefaultAmount'] = $whtTrans;
                    $data['localAmount'] = $whtLocal;
                    $data['comRptAmount'] = $whtRpt;
                    array_push($finalData, $data);
                }
            }

        }

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData]];
	}
}
