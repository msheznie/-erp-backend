<?php

namespace App\Services\GeneralLedger;

use App\helper\ExchangeSetupConfig;
use App\helper\Helper;
use App\helper\TaxService;
use App\Models\AdvancePaymentDetails;
use App\Models\AdvanceReceiptDetails;
use App\Models\AssetCapitalization;
use App\Models\AssetDisposalDetail;
use App\Models\AssetDisposalMaster;
use App\Models\BookInvSuppDet;
use App\Models\BookInvSuppMaster;
use App\Models\CreditNote;
use App\Models\CreditNoteDetails;
use App\Models\CurrencyConversion;
use App\Models\StockCount;
use App\Models\StockCountDetail;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\CustomerMaster;
use App\Models\CustomerReceivePayment;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\DebitNote;
use App\Models\DebitNoteDetails;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderDetail;
use App\Models\DirectInvoiceDetails;
use App\Models\DirectPaymentDetails;
use App\Models\DirectReceiptDetail;
use App\Models\Employee;
use App\Models\FixedAssetDepreciationMaster;
use App\Models\FixedAssetDepreciationPeriod;
use App\Models\FixedAssetMaster;
use App\Models\PurchaseReturnLogistic;
use App\Models\GeneralLedger;
use App\Models\GRVDetails;
use App\Models\GRVMaster;
use App\Models\InventoryReclassification;
use App\Models\InventoryReclassificationDetail;
use App\Models\ItemIssueDetails;
use App\Models\ItemIssueMaster;
use App\Models\ItemReturnDetails;
use App\Models\ItemReturnMaster;
use App\Models\JvDetail;
use App\Models\JvMaster;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\PoAdvancePayment;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetails;
use App\Models\SegmentMaster;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetails;
use App\Models\StockReceive;
use App\Models\StockReceiveDetails;
use App\Models\StockTransfer;
use App\Models\StockTransferDetails;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\Taxdetail;
use App\Models\SupplierInvoiceDirectItem;
use App\Models\Company;
use App\Models\SupplierAssigned;
use App\Models\ChartOfAccountsAssigned;
use App\Models\ChartOfAccount;
use App\Models\SalesReturn;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\SalesReturnDetail;
use App\Models\TaxVatCategories;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\UnbilledGRVInsert;
use App\Jobs\TaxLedgerInsert;
use App\Services\GeneralLedger\GlPostedDateService;
use App\Models\Tax;
use App\Models\SupplierMaster;


class SupplierInvoiceGlService
{
    public static function processEntry($masterModel)
    {
        $data = [];
        $taxLedgerData = [];
        $finalData = [];
        if($masterModel['employeeSystemID'] == "SYSTEM"){
            $empID = Employee::where('empID',$masterModel['employeeSystemID'])->first();
        }
        else{
            $empID = Employee::find($masterModel['employeeSystemID']);
        }
        $masterData = BookInvSuppMaster::with(['detail' => function ($query) {
            $query->selectRaw("SUM(totLocalAmount) as localAmount, SUM(totRptAmount) as rptAmount,SUM(totTransactionAmount) as transAmount,SUM(VATAmount) as totalVATAmount,SUM(VATAmountLocal) as totalVATAmountLocal,SUM(VATAmountRpt) as totalVATAmountRpt,bookingSuppMasInvAutoID");
        }, 'item_details' => function ($query) {
            $query->selectRaw("SUM(netAmount) as netAmountTotal, SUM(VATAmount*noQty) as totalVATAmount,SUM(VATAmountLocal*noQty) as totalVATAmountLocal,SUM(VATAmountRpt*noQty) as totalVATAmountRpt, bookingSuppMasInvAutoID");
        }, 'directdetail' => function ($query) {
            $query->selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DIAmount) as transAmount,directInvoiceAutoID");
        }, 'financeperiod_by'])->find($masterModel["autoID"]);
        //get balansheet account
        $bs = DirectInvoiceDetails::with(['chartofaccount'])
            ->selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DIAmount) as transAmount, SUM(netAmountLocal) as netLocalAmount, SUM(netAmountRpt) as netRptAmount,SUM(netAmount) as netTransAmount,chartOfAccountSystemID as financeGLcodebBSSystemID,glCode as financeGLcodebBS,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,DIAmountCurrency as supplierTransactionCurrencyID,DIAmountCurrencyER as supplierTransactionER,comRptCurrencyER as companyReportingER,localCurrencyER,serviceLineSystemID,serviceLineCode,chartOfAccountSystemID,comments,directInvoiceDetailsID")
            ->WHERE('directInvoiceAutoID', $masterModel["autoID"])
            ->groupBy('chartOfAccountSystemID', 'serviceLineSystemID', 'comments')
            ->get();

        $tax = Taxdetail::selectRaw("SUM(localAmount) as localAmount, SUM(rptAmount) as rptAmount,SUM(amount) as transAmount,localCurrencyID,rptCurrencyID as reportingCurrencyID,currency as supplierTransactionCurrencyID,currencyER as supplierTransactionER,rptCurrencyER as companyReportingER,localCurrencyER,payeeSystemCode")
            ->WHERE('documentSystemCode', $masterModel["autoID"])
            ->WHERE('documentSystemID', $masterModel["documentSystemID"])
            ->groupBy('documentSystemCode')
            ->first();

        //get balansheet account
        $bsItemDirect = SupplierInvoiceDirectItem::selectRaw("SUM(costPerUnitLocalCur*noQty) as localAmount, SUM(costPerUnitComRptCur*noQty) as rptAmount,SUM(costPerUnitSupTransCur*noQty) as transAmount,financeGLcodebBSSystemID,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionER,companyReportingCurrencyID,companyReportingER,localCurrencyID,localCurrencyER, id")->WHERE('bookingSuppMasInvAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->where('financeGLcodebBSSystemID', '>', 0)->groupBy('financeGLcodebBSSystemID')->get();

        //get pnl account
        $plItemDirect = SupplierInvoiceDirectItem::selectRaw("SUM(costPerUnitLocalCur*noQty) as localAmount, SUM(costPerUnitComRptCur*noQty) as rptAmount,SUM(costPerUnitSupTransCur*noQty) as transAmount,financeGLcodePLSystemID,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionER,companyReportingCurrencyID,companyReportingER,localCurrencyID,localCurrencyER")->WHERE('bookingSuppMasInvAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodePLSystemID')->where('financeGLcodePLSystemID', '>', 0)->WHERE('includePLForGRVYN', -1)->groupBy('financeGLcodePLSystemID')->get();


        $taxLocal = 0;
        $taxRpt = 0;
        $taxTrans = 0;
        $retentionPercentage = ($masterData->retentionPercentage > 0) ? $masterData->retentionPercentage : 0;
        $whtPercentage = ($masterData->whtPercentage > 0) ? $masterData->whtPercentage : 0;
        $poInvoiceDirectLocalExtCharge = 0;
        $poInvoiceDirectRptExtCharge = 0;
        $poInvoiceDirectTransExtCharge = 0;

        $directVATDetails = TaxService::processDirectSupplierInvoiceVAT($masterModel["autoID"], $masterModel["documentSystemID"]);
        $rcmActivated = TaxService::isGRVRCMActivation($masterModel["autoID"]);


        $directItemVatDetails = [];
        if ($masterData->documentType == 3) {
            $directItemVatDetails = TaxService::processSupplierInvoiceItemsVAT($masterModel["autoID"]);
        }

        if ($tax) {
            $taxLocal = $tax->localAmount;
            $taxRpt = $tax->rptAmount;
            $taxTrans = $tax->transAmount;
        }



        if (isset($masterData->directdetail[0]) && count($masterData->directdetail) > 0) {
            $poInvoiceDirectLocalExtCharge = (isset($masterData->directdetail[0]->localAmount)) ? $masterData->directdetail[0]->localAmount : 0;
            $poInvoiceDirectRptExtCharge = (isset($masterData->directdetail[0]->rptAmount)) ? $masterData->directdetail[0]->rptAmount : 0;
            $poInvoiceDirectTransExtCharge = (isset($masterData->directdetail[0]->transAmount)) ? $masterData->directdetail[0]->transAmount : 0;
        }


        $validatePostedDate = GlPostedDateService::validatePostedDate($masterModel["autoID"], $masterModel["documentSystemID"]);

        if (!$validatePostedDate['status']) {
            return ['status' => false, 'message' => $validatePostedDate['message']];
        }

        $masterDocumentDate = isset($masterModel['documentDateOveride']) ? $masterModel['documentDateOveride'] : $validatePostedDate['postedDate'];

        if ($masterData) {

            $data['companySystemID'] = $masterData->companySystemID;
            $data['companyID'] = $masterData->companyID;
            $data['serviceLineSystemID'] = 24;
            $data['serviceLineCode'] = 'X';
            $data['masterCompanyID'] = null;
            $data['documentSystemID'] = $masterData->documentSystemID;
            $data['documentID'] = $masterData->documentID;
            $data['documentSystemCode'] = $masterModel["autoID"];
            $data['documentCode'] = $masterData->bookingInvCode;
            $data['documentDate'] = $masterDocumentDate;
            $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
            $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
            $data['documentConfirmedDate'] = $masterData->confirmedDate;
            $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
            $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
            $data['documentNarration'] = $masterData->comments;
            $data['clientContractID'] = 'X';
            $data['contractUID'] = 159;
            $data['supplierCodeSystem'] = $masterData->supplierID;
            $data['employeeSystemID'] = $masterData->employeeID;
            $data['chartOfAccountSystemID'] = ($masterData->documentType == 4) ? $masterData->employeeControlAcID : $masterData->supplierGLCodeSystemID;
            $data['glCode'] = ($masterData->documentType == 4) ? ChartOfAccount::getAccountCode($masterData->employeeControlAcID) : $masterData->supplierGLCode;
            $data['documentTransCurrencyID'] = $masterData->supplierTransactionCurrencyID;
            $data['documentTransCurrencyER'] = $masterData->supplierTransactionCurrencyER;
            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;

            $data['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
            $data['documentRptCurrencyER'] = $masterData->companyReportingER;
            $data['invoiceNumber'] = $masterData->supplierInvoiceNo;
            $data['invoiceDate'] = $masterData->supplierInvoiceDate;

            if ($masterData->documentType == 0 || $masterData->documentType == 2) { // check if it is supplier invoice
                $data['documentTransAmount'] = \Helper::roundValue($masterData->detail[0]->transAmount + $poInvoiceDirectTransExtCharge + $taxTrans) * -1;
                $data['documentLocalAmount'] = \Helper::roundValue($masterData->detail[0]->localAmount + $poInvoiceDirectLocalExtCharge + $taxLocal) * -1;
                $data['documentRptAmount'] = \Helper::roundValue($masterData->detail[0]->rptAmount + $poInvoiceDirectRptExtCharge + $taxRpt) * -1;
            } else if ($masterData->documentType == 3) { // check if it is supplier item invoice
                $directItemCurrencyConversion = \Helper::currencyConversion($masterData->companySystemID, $masterData->supplierTransactionCurrencyID, $masterData->supplierTransactionCurrencyID, $masterData->item_details[0]->netAmountTotal);
                $data['documentTransAmount'] = \Helper::roundValue($masterData->item_details[0]->netAmountTotal + $masterData->item_details[0]->totalVATAmount + $poInvoiceDirectTransExtCharge) * -1;
                $data['documentLocalAmount'] = \Helper::roundValue($directItemCurrencyConversion['localAmount'] + $masterData->item_details[0]->totalVATAmountLocal + $poInvoiceDirectLocalExtCharge) * -1;
                $data['documentRptAmount'] = \Helper::roundValue($directItemCurrencyConversion['reportingAmount'] + $masterData->item_details[0]->totalVATAmountRpt + $poInvoiceDirectRptExtCharge) * -1;
            } else { // check if it is direct invoice
                if(isset($masterData->directdetail[0])) {
                    if ($masterData->documentType == 1 && $masterData->rcmActivated) {
                        $data['documentTransAmount'] = \Helper::roundValue($masterData->directdetail[0]->transAmount) * -1;
                        $data['documentLocalAmount'] = \Helper::roundValue($masterData->directdetail[0]->localAmount) * -1;
                        $data['documentRptAmount'] = \Helper::roundValue($masterData->directdetail[0]->rptAmount) * -1;
                    } else {
                        $data['documentTransAmount'] = \Helper::roundValue($masterData->directdetail[0]->transAmount + $taxTrans) * -1;
                        $data['documentLocalAmount'] = \Helper::roundValue($masterData->directdetail[0]->localAmount + $taxLocal) * -1;
                        $data['documentRptAmount'] = \Helper::roundValue($masterData->directdetail[0]->rptAmount + $taxRpt) * -1;
                    }
                }
            }

            $whtTrans = 0;
            $whtLocal = 0;
            $whtRpt = 0;

            $retentionTrans = 0;
            $retentionLocal = 0;
            $retentionRpt = 0;

            $whtAmountCon = 0;
            $whtAmountConLocal = 0;
            $whtAmountConRpt = 0;


            $whtFullAmount = 0;
            $whtFullAmountLocal = 0;
            $whtFullAmountRpt = 0;

            if ($retentionPercentage > 0) {
                if ($masterData->documentType != 4) {

                    if ($masterData->documentType == 3) {
                        $directVATDetails = TaxService::processSupplierInvoiceItemsVAT($masterModel["autoID"]);
                        $totalVATAmount = 0;
                        $totalVATAmountLocal = 0;
                        $totalVATAmountRpt = 0;
                        $totalVATAmount = \Helper::roundValue(ABS($directVATDetails['masterVATTrans']));
                        $totalVATAmountLocal = \Helper::roundValue(ABS($directVATDetails['masterVATLocal']));
                        $totalVATAmountRpt = \Helper::roundValue(ABS($directVATDetails['masterVATRpt']));

                        $retentionTransWithoutVat = ($data['documentTransAmount'] + ABS($totalVATAmount)) * ($retentionPercentage / 100);
                        $retentionLocalWithoutVat = ($data['documentLocalAmount'] + ABS($totalVATAmountLocal)) * ($retentionPercentage / 100);
                        $retentionRptWithoutVat = ($data['documentRptAmount'] + ABS($totalVATAmountRpt)) * ($retentionPercentage / 100);

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
                            $retentionTransWithoutVat = ($data['documentTransAmount'] + ABS($totalVATAmount)) * ($retentionPercentage / 100);
                            $retentionLocalWithoutVat = ($data['documentLocalAmount'] + ABS($totalVATAmountLocal)) * ($retentionPercentage / 100);
                            $retentionRptWithoutVat = ($data['documentRptAmount'] + ABS($totalVATAmountRpt)) * ($retentionPercentage / 100);
                        } else {
                            $retentionTrans = $data['documentTransAmount'] * ($retentionPercentage / 100);
                            $retentionLocal = $data['documentLocalAmount'] * ($retentionPercentage / 100);
                            $retentionRpt = $data['documentRptAmount'] * ($retentionPercentage / 100);
                        }
                    }
                    else if ($masterData->documentType == 0 || $masterData->documentType == 2) {
                        $vatDetails = TaxService::processPoBasedSupllierInvoiceVAT($masterModel["autoID"]);
                        $totalVATAmount = 0;
                        $totalVATAmountLocal = 0;
                        $totalVATAmountRpt = 0;
                        $totalVATAmount = $vatDetails['totalVAT'];
                        $totalVATAmountLocal = $vatDetails['totalVATLocal'];
                        $totalVATAmountRpt = $vatDetails['totalVATRpt'];
                        if (!TaxService::isSupplierInvoiceRcmActivated($masterModel["autoID"])) {
                            $retentionTransWithoutVat = ($data['documentTransAmount'] + ABS($totalVATAmount)) * ($retentionPercentage / 100);
                            $retentionLocalWithoutVat = ($data['documentLocalAmount'] + ABS($totalVATAmountLocal)) * ($retentionPercentage / 100);
                            $retentionRptWithoutVat = ($data['documentRptAmount'] + ABS($totalVATAmountRpt)) * ($retentionPercentage / 100);
                        } else {
                            $retentionTrans = $data['documentTransAmount'] * ($retentionPercentage / 100);
                            $retentionLocal = $data['documentLocalAmount'] * ($retentionPercentage / 100);
                            $retentionRpt = $data['documentRptAmount'] * ($retentionPercentage / 100);
                        }
                    } else {
                        $retentionTrans = $data['documentTransAmount'] * ($retentionPercentage / 100);
                        $retentionLocal = $data['documentLocalAmount'] * ($retentionPercentage / 100);
                        $retentionRpt = $data['documentRptAmount'] * ($retentionPercentage / 100);
                    }

                    $data['documentTransAmount'] = $data['documentTransAmount'] * (1 - ($retentionPercentage / 100));
                    $data['documentLocalAmount'] = $data['documentLocalAmount'] * (1 - ($retentionPercentage / 100));
                    $data['documentRptAmount'] = $data['documentRptAmount'] * (1 - ($retentionPercentage / 100));
                }
            }

            if($masterData->whtApplicable)
            {
                if ($masterData->documentType != 4) {

                    if ($masterData->documentType == 0 || $masterData->documentType == 2 || $masterData->documentType == 1 || $masterData->documentType == 3) {

                        $currencyWht = \Helper::currencyConversion($masterData->companySystemID, $masterData->supplierTransactionCurrencyID, $masterData->supplierTransactionCurrencyID, $masterData->whtAmount);
                     
                      
                        $whtAmountCon =  -1 *$masterData->whtAmount;
                        $whtAmountConLocal =  -1 *\Helper::roundValue($currencyWht['localAmount']);
                        $whtAmountConRpt =  -1 *\Helper::roundValue($currencyWht['reportingAmount']);


                        $whtTrans = $whtAmountCon;
                        $whtLocal = $whtAmountConLocal;
                        $whtRpt = $whtAmountConRpt;

                        
                    }
                    $data['documentTransAmount'] = $data['documentTransAmount'] - $whtAmountCon;
                    $data['documentLocalAmount'] = $data['documentLocalAmount'] - $whtAmountConLocal;
                    $data['documentRptAmount'] = $data['documentRptAmount'] - $whtAmountConRpt;

                }
            }

            $data['holdingShareholder'] = null;
            $data['holdingPercentage'] = 0;
            $data['nonHoldingPercentage'] = 0;
            $data['documentType'] = $masterData->documentType;
            $data['createdDateTime'] = \Helper::currentDateTime();
            $data['createdUserID'] = $empID->empID;
            $data['createdUserSystemID'] = $empID->employeeSystemID;
            $data['createdUserPC'] = gethostname();
            $data['timestamp'] = \Helper::currentDateTime();
            array_push($finalData, $data);

            if ($retentionPercentage > 0) {
                if ($masterData->documentType != 4) {
                    $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "retention-control-account");
                    $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "retention-control-account");
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                    if ($masterData->documentType == 0 || $masterData->documentType == 2) {
                        if (!TaxService::isSupplierInvoiceRcmActivated($masterModel["autoID"])) {

                            $data['documentTransAmount'] = $retentionTransWithoutVat;
                            $data['documentLocalAmount'] = $retentionLocalWithoutVat;
                            $data['documentRptAmount'] = $retentionRptWithoutVat;
                        } else {
                            $data['documentTransAmount'] = $retentionTrans;
                            $data['documentLocalAmount'] = $retentionLocal;
                            $data['documentRptAmount'] = $retentionRpt;
                        }
                    } else if ($masterData->documentType == 1) {
                        if ($masterData->rcmActivated != 1) {
                            $data['documentTransAmount'] = $retentionTransWithoutVat;
                            $data['documentLocalAmount'] = $retentionLocalWithoutVat;
                            $data['documentRptAmount'] = $retentionRptWithoutVat;
                        } else {
                            $data['documentTransAmount'] = $retentionTrans;
                            $data['documentLocalAmount'] = $retentionLocal;
                            $data['documentRptAmount'] = $retentionRpt;
                        }

                    } else if ($masterData->documentType == 3) {
                        $data['documentTransAmount'] = $retentionTransWithoutVat;
                        $data['documentLocalAmount'] = $retentionLocalWithoutVat;
                        $data['documentRptAmount'] = $retentionRptWithoutVat;
                    } else {
                        $data['documentTransAmount'] = $retentionTrans;
                        $data['documentLocalAmount'] = $retentionLocal;
                        $data['documentRptAmount'] = $retentionRpt;
                    }
                    array_push($finalData, $data);
                }
            }


            if ($masterData->whtApplicable) {
                if ($masterData->documentType != 4) {

                    $taxSetup = Tax::where('taxMasterAutoID',$masterData->whtType)->first();
                    $whtAuthority = null;
                    if($taxSetup)
                    {
                        $whtAuthority = $taxSetup->authorityAutoID;
                        $supplier = SupplierMaster::where('supplierCodeSystem',$whtAuthority)->with('liablity_account')->first();
                        $data['supplierCodeSystem'] = $supplier->supplierCodeSystem;
                    }

                    $data['chartOfAccountSystemID'] = $whtAuthority != null?$supplier->liablity_account->chartOfAccountSystemID:null;
                    $data['glCode'] = $whtAuthority != null?$supplier->liablity_account->AccountCode:null;
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                    if ($masterData->documentType == 0 || $masterData->documentType == 2 || $masterData->documentType == 1 || $masterData->documentType == 3) {
             
                            $data['documentTransAmount'] = $whtTrans;
                            $data['documentLocalAmount'] = $whtLocal;
                            $data['documentRptAmount'] = $whtRpt;
                        
                    } 
                    }
                    array_push($finalData, $data);
                }
            
                $data['supplierCodeSystem'] = $masterData->supplierID;

            if ($masterData->documentType == 0 || $masterData->documentType == 2) {
                $data['chartOfAccountSystemID'] = $masterData->UnbilledGRVAccountSystemID;
                $data['glCode'] = $masterData->UnbilledGRVAccount;
                $data['documentTransAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->transAmount));
                $data['documentLocalAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->localAmount));
                $data['documentRptAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->rptAmount));
                array_push($finalData, $data);

                if ($bs) {
                    foreach ($bs as $val) {
                        $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                        $data['serviceLineCode'] = $val->serviceLineCode;
                        $data['chartOfAccountSystemID'] = $val->financeGLcodebBSSystemID;
                        $data['glCode'] = $val->financeGLcodebBS;
                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                        $data['documentNarration'] = $val->comments;
                        $data['documentTransCurrencyID'] = $val->supplierTransactionCurrencyID;
                        $data['documentTransCurrencyER'] = $val->supplierTransactionER;
                        $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount));
                        $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                        $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                        $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount));
                        $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                        $data['documentRptCurrencyER'] = $val->companyReportingER;
                        $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount));
                        $data['timestamp'] = \Helper::currentDateTime();
                        array_push($finalData, $data);
                    }
                }
            }
            else if ($masterData->documentType == 3) {

                $exemptExpenseDetails = TaxService::processSIExpenseVatItemInvoice($masterModel["autoID"]);
                $expenseCOA = TaxVatCategories::with(['tax'])->where('subCatgeoryType', 3)->whereHas('tax', function ($query) use ($masterData) {
                    $query->where('companySystemID', $masterData->companySystemID);
                })->where('isActive', 1)->first();
                if(!empty($exemptExpenseDetails) && !empty($expenseCOA) && $expenseCOA->expenseGL != null){
                    $exemptVatTrans = $exemptExpenseDetails->VATAmount;
                    $exemptVATLocal = $exemptExpenseDetails->VATAmountLocal;
                    $exemptVatRpt = $exemptExpenseDetails->VATAmountRpt;

                    $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $expenseCOA->expenseGL)->where('companySystemID', $masterData->companySystemID)->first();
                    $data['chartOfAccountSystemID'] = $expenseCOA->expenseGL;
                    $data['glCode'] = $chartOfAccountData->AccountCode;
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                    $data['documentTransAmount'] = $exemptVatTrans;
                    $data['documentLocalAmount'] = $exemptVATLocal;
                    $data['documentRptAmount'] = $exemptVatRpt;
                    $data['timestamp'] = \Helper::currentDateTime();
                    array_push($finalData, $data);
                }


                if ($bsItemDirect) {

                    foreach ($bsItemDirect as $val) {

                        $transBSVAT = isset($directItemVatDetails['bsVAT'][$val->financeGLcodebBSSystemID]['transVATAmount']) ? $directItemVatDetails['bsVAT'][$val->financeGLcodebBSSystemID]['transVATAmount'] : 0;
                        $rptBSVAT = isset($directItemVatDetails['bsVAT'][$val->financeGLcodebBSSystemID]['rptVATAmount']) ? $directItemVatDetails['bsVAT'][$val->financeGLcodebBSSystemID]['rptVATAmount'] : 0;
                        $localBSVAT = isset($directItemVatDetails['bsVAT'][$val->financeGLcodebBSSystemID]['localVATAmount']) ? $directItemVatDetails['bsVAT'][$val->financeGLcodebBSSystemID]['localVATAmount'] : 0;

                        $exemptVATTransAmount = isset($directItemVatDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID]['exemptVATTransAmount']) ? $directItemVatDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID]['exemptVATTransAmount'] : 0;
                        $exemptVATLocalAmount = isset($directItemVatDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID]['exemptVATLocalAmount']) ? $directItemVatDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID]['exemptVATLocalAmount'] : 0;
                        $exemptVATRptAmount = isset($directItemVatDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID]['exemptVATRptAmount']) ? $directItemVatDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID]['exemptVATRptAmount'] : 0;

                        $data['chartOfAccountSystemID'] = $val->financeGLcodebBSSystemID;
                        $data['glCode'] = ChartOfAccount::getAccountCode($val->financeGLcodebBSSystemID);
                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                        $data['documentTransCurrencyID'] = $val->supplierTransactionCurrencyID;
                        $data['documentTransCurrencyER'] = $val->supplierTransactionER;


                        $exemptExpenseDetails = TaxService::processSIExpenseVatItemInvoiceDetail($masterModel["autoID"], $data['chartOfAccountSystemID']);
                        $expenseCOA = TaxVatCategories::with(['tax'])->where('subCatgeoryType', 3)->whereHas('tax', function ($query) use ($masterData) {
                            $query->where('companySystemID', $masterData->companySystemID);
                        })->where('isActive', 1)->first();

                        if(!empty($exemptExpenseDetails) && !empty($expenseCOA) && $expenseCOA->expenseGL != null){
                            $exemptVatTrans = $exemptExpenseDetails->VATAmount;
                            $exemptVATLocal = $exemptExpenseDetails->VATAmountLocal;
                            $exemptVatRpt = $exemptExpenseDetails->VATAmountRpt;

                        } else {
                            $exemptVatTrans = 0;
                            $exemptVATLocal = 0;
                            $exemptVatRpt = 0;
                        }

                        $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount) + $transBSVAT + $exemptVATTransAmount - $exemptVatTrans);

                        $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                        $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                        $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount) + $localBSVAT + $exemptVATLocalAmount - $exemptVATLocal);

                        $data['documentRptCurrencyID'] = $val->companyReportingCurrencyID;
                        $data['documentRptCurrencyER'] = $val->companyReportingER;
                        $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount) + $rptBSVAT + $exemptVATRptAmount - $exemptVatRpt);
                        $data['timestamp'] = \Helper::currentDateTime();
                        array_push($finalData, $data);
                    }
                }

                if ($plItemDirect) {
                    foreach ($plItemDirect as $val) {

                        $transPLVAT = isset($directItemVatDetails['plVAT'][$val->financeGLcodePLSystemID]['transVATAmount']) ? $directItemVatDetails['plVAT'][$val->financeGLcodePLSystemID]['transVATAmount'] : 0;
                        $rptPLVAT = isset($directItemVatDetails['plVAT'][$val->financeGLcodePLSystemID]['rptVATAmount']) ? $directItemVatDetails['plVAT'][$val->financeGLcodePLSystemID]['rptVATAmount'] : 0;
                        $localPLVAT = isset($directItemVatDetails['plVAT'][$val->financeGLcodePLSystemID]['localVATAmount']) ? $directItemVatDetails['plVAT'][$val->financeGLcodePLSystemID]['localVATAmount'] : 0;

                        $exemptVATTransAmount = isset($directItemVatDetails['exemptVATportionPL'][$val->financeGLcodebBSSystemID]['exemptVATTransAmount']) ? $directItemVatDetails['exemptVATportionPL'][$val->financeGLcodebBSSystemID]['exemptVATTransAmount'] : 0;
                        $exemptVATLocalAmount = isset($directItemVatDetails['exemptVATportionPL'][$val->financeGLcodebBSSystemID]['exemptVATLocalAmount']) ? $directItemVatDetails['exemptVATportionPL'][$val->financeGLcodebBSSystemID]['exemptVATLocalAmount'] : 0;
                        $exemptVATRptAmount = isset($directItemVatDetails['exemptVATportionPL'][$val->financeGLcodebBSSystemID]['exemptVATRptAmount']) ? $directItemVatDetails['exemptVATportionPL'][$val->financeGLcodebBSSystemID]['exemptVATRptAmount'] : 0;

                        $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                        $data['glCode'] = ChartOfAccount::getAccountCode($val->financeGLcodePLSystemID);
                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                        $exemptExpenseDetails = TaxService::processSIExpenseVatItemInvoiceDetailForPL($masterModel["autoID"], $data['chartOfAccountSystemID']);
                        $expenseCOA = TaxVatCategories::with(['tax'])->where('subCatgeoryType', 3)->whereHas('tax', function ($query) use ($masterData) {
                            $query->where('companySystemID', $masterData->companySystemID);
                        })->where('isActive', 1)->first();

                        if(!empty($exemptExpenseDetails) && !empty($expenseCOA) && $expenseCOA->expenseGL != null){
                            $exemptVatTrans = $exemptExpenseDetails->VATAmount;
                            $exemptVATLocal = $exemptExpenseDetails->VATAmountLocal;
                            $exemptVatRpt = $exemptExpenseDetails->VATAmountRpt;

                        } else {
                            $exemptVatTrans = 0;
                            $exemptVATLocal = 0;
                            $exemptVatRpt = 0;
                        }



                        $data['documentTransCurrencyID'] = $val->supplierTransactionCurrencyID;
                        $data['documentTransCurrencyER'] = $val->supplierTransactionER;
                        $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount) + $transPLVAT + $exemptVATTransAmount - $exemptVatTrans);

                        $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                        $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                        $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount) + $localPLVAT + $exemptVATLocalAmount - $exemptVATLocal);

                        $data['documentRptCurrencyID'] = $val->companyReportingCurrencyID;
                        $data['documentRptCurrencyER'] = $val->companyReportingER;
                        $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount) + $rptPLVAT + $exemptVATRptAmount - $exemptVatRpt);
                        $data['timestamp'] = \Helper::currentDateTime();
                        array_push($finalData, $data);
                    }
                }


                if ($bs) {

                    foreach ($bs as $val) {
                        $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                        $data['serviceLineCode'] = $val->serviceLineCode;
                        $data['chartOfAccountSystemID'] = $val->financeGLcodebBSSystemID;
                        $data['glCode'] = $val->financeGLcodebBS;
                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                        $data['documentNarration'] = $val->comments;
                        $data['documentTransCurrencyID'] = $val->supplierTransactionCurrencyID;
                        $data['documentTransCurrencyER'] = $val->supplierTransactionER;
                        $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount));
                        $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                        $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                        $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount));
                        $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                        $data['documentRptCurrencyER'] = $val->companyReportingER;
                        $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount));
                        $data['timestamp'] = \Helper::currentDateTime();
                        array_push($finalData, $data);
                    }
                }
            }
            else {
                if ($masterData->rcmActivated != 1) {
                $exemptExpenseDetails = TaxService::processSIExemptVatDirectInvoice($masterModel["autoID"]);
                $expenseCOA = TaxVatCategories::with(['tax'])->where('subCatgeoryType', 3)->whereHas('tax', function ($query) use ($masterData) {
                    $query->where('companySystemID', $masterData->companySystemID);
                })->where('isActive', 1)->first();
                    if(!empty($exemptExpenseDetails) && !empty($expenseCOA) && $expenseCOA->expenseGL != null && $masterData->VATAmount != 0) {
                        $exemptVatTrans = $exemptExpenseDetails->VATAmount;
                        $exemptVATLocal = $exemptExpenseDetails->VATAmountLocal;
                        $exemptVatRpt = $exemptExpenseDetails->VATAmountRpt;

                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $expenseCOA->expenseGL)->where('companySystemID', $masterData->companySystemID)->first();
                        $data['chartOfAccountSystemID'] = $expenseCOA->expenseGL;
                        $data['glCode'] = $chartOfAccountData->AccountCode;
                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                        $data['documentTransAmount'] = $exemptVatTrans;
                        $data['documentLocalAmount'] = $exemptVATLocal;
                        $data['documentRptAmount'] = $exemptVatRpt;
                        $data['timestamp'] = \Helper::currentDateTime();
                        array_push($finalData, $data);
                    }
                }


                if ($bs) {

                    foreach ($bs as $val) {

                        $transBSVAT = isset($directVATDetails['bsVAT'][$val->financeGLcodebBSSystemID . $val->serviceLineSystemID . $val->comments]['transVATAmount']) ? $directVATDetails['bsVAT'][$val->financeGLcodebBSSystemID . $val->serviceLineSystemID . $val->comments]['transVATAmount'] : 0;
                        $rptBSVAT = isset($directVATDetails['bsVAT'][$val->financeGLcodebBSSystemID . $val->serviceLineSystemID . $val->comments]['rptVATAmount']) ? $directVATDetails['bsVAT'][$val->financeGLcodebBSSystemID . $val->serviceLineSystemID . $val->comments]['rptVATAmount'] : 0;
                        $localBSVAT = isset($directVATDetails['bsVAT'][$val->financeGLcodebBSSystemID . $val->serviceLineSystemID . $val->comments]['localVATAmount']) ? $directVATDetails['bsVAT'][$val->financeGLcodebBSSystemID . $val->serviceLineSystemID . $val->comments]['localVATAmount'] : 0;

                        $exemptVATTransAmount = isset($directVATDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID . $val->serviceLineSystemID . $val->comments]['exemptVATTransAmount']) ? $directVATDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID . $val->serviceLineSystemID . $val->comments]['exemptVATTransAmount'] : 0;
                        $exemptVATLocalAmount = isset($directVATDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID . $val->serviceLineSystemID . $val->comments]['exemptVATLocalAmount']) ? $directVATDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID . $val->serviceLineSystemID . $val->comments]['exemptVATLocalAmount'] : 0;
                        $exemptVATRptAmount = isset($directVATDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID . $val->serviceLineSystemID . $val->comments]['exemptVATRptAmount']) ? $directVATDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID . $val->serviceLineSystemID . $val->comments]['exemptVATRptAmount'] : 0;


                        $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                        $data['serviceLineCode'] = $val->serviceLineCode;
                        $data['chartOfAccountSystemID'] = $val->financeGLcodebBSSystemID;
                        $data['glCode'] = $val->financeGLcodebBS;
                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                        $data['documentNarration'] = $val->comments;

                        $exemptExpenseDIDetails = TaxService::checkSIExpenseVatDirectInvoice($masterModel["autoID"], $data['chartOfAccountSystemID'], $data['serviceLineSystemID']);
                        $expenseCOA = TaxVatCategories::with(['tax'])->where('subCatgeoryType', 3)->whereHas('tax', function ($query) use ($masterData) {
                            $query->where('companySystemID', $masterData->companySystemID);
                        })->where('isActive', 1)->first();

                        if(!empty($exemptExpenseDIDetails)){
                            if($exemptExpenseDIDetails->exempt_vat_portion > 0 && $exemptExpenseDIDetails->subCatgeoryType == 1 && $expenseCOA->expenseGL != null) {
                                $exemptVatTrans = $exemptExpenseDIDetails->VATAmount * $exemptExpenseDIDetails->exempt_vat_portion / 100;
                                $exemptVATLocal = $exemptExpenseDIDetails->VATAmountLocal * $exemptExpenseDIDetails->exempt_vat_portion / 100;
                                $exemptVatRpt = $exemptExpenseDIDetails->VATAmountRpt * $exemptExpenseDIDetails->exempt_vat_portion / 100;
                            }
                            else if($exemptExpenseDIDetails->recordType == 1){
                                $exemptVatTrans = $exemptExpenseDIDetails->VATAmount;
                                $exemptVATLocal = $exemptExpenseDIDetails->VATAmountLocal;
                                $exemptVatRpt = $exemptExpenseDIDetails->VATAmountRpt;
                            } else {
                                $exemptVatTrans = 0;
                                $exemptVATLocal = 0;
                                $exemptVatRpt = 0;
                            }
                        } else {
                            $exemptVatTrans = 0;
                            $exemptVATLocal = 0;
                            $exemptVatRpt = 0;
                        }
                        $data['documentTransCurrencyID'] = $val->supplierTransactionCurrencyID;
                        $data['documentTransCurrencyER'] = $val->supplierTransactionER;
                        if($exemptVatTrans > 0)
                        {
                            $data['documentTransAmount'] = \Helper::roundValue(($val->transAmount));
                        }else {
                            $data['documentTransAmount'] = \Helper::roundValue(($val->transAmount) + abs($transBSVAT) + abs($exemptVATTransAmount) - $exemptVatTrans);

                        }
                        $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                        $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                        if($exemptVATLocal > 0)
                        {
                            $data['documentLocalAmount'] = \Helper::roundValue($val->localAmount);
                        }else {
                            $data['documentLocalAmount'] = \Helper::roundValue(($val->localAmount) + abs($localBSVAT) + abs($exemptVATLocalAmount) - $exemptVATLocal);

                        }

                        $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                        $data['documentRptCurrencyER'] = $val->companyReportingER;

                        if($exemptVatRpt > 0)
                        {
                            $data['documentRptAmount'] = \Helper::roundValue($val->rptAmount);
                        }else {
                            $data['documentRptAmount'] = \Helper::roundValue(($val->rptAmount) + abs($rptBSVAT) + abs($exemptVATRptAmount) - $exemptVatRpt);

                        }
                        $data['timestamp'] = \Helper::currentDateTime();

                        array_push($finalData, $data);
                    }
                }
            }

            //VAT entries
            $vatDetails = TaxService::processPoBasedSupllierInvoiceVAT($masterModel["autoID"]);
            $totalVATAmount = $vatDetails['totalVAT'];
            $totalExemptVAT = $vatDetails['exemptVAT'];
            $totalVATAmountLocal = $vatDetails['totalVATLocal'];
            $totalVATAmountRpt = $vatDetails['totalVATRpt'];

            if (($masterData->documentType == 0 || $masterData->documentType == 2) && $masterData->detail && count($masterData->detail) > 0 && ($totalVATAmount > 0 || $vatDetails['exemptVAT'] > 0)) {

                if ($totalVATAmount > 0) {
                    // Input VAT control
                    $taxConfigData = TaxService::getInputVATGLAccount($masterModel["companySystemID"]);
                    if (!empty($taxConfigData)) {
                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxConfigData->inputVatGLAccountAutoID)
                            ->where('companySystemID', $masterData->companySystemID)
                            ->first();

                        if (!empty($chartOfAccountData)) {
                            $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                            $data['glCode'] = $chartOfAccountData->AccountCode;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            $data['documentTransAmount'] = \Helper::roundValue(ABS($totalVATAmount));
                            $data['documentLocalAmount'] = \Helper::roundValue(ABS($totalVATAmountLocal));
                            $data['documentRptAmount'] = \Helper::roundValue(ABS($totalVATAmountRpt));

                            if ($retentionPercentage > 0 && $masterData->documentType != 4) {
                                $data['documentTransAmount'] = $data['documentTransAmount'] * (1 - ($retentionPercentage / 100));
                                $data['documentLocalAmount'] = $data['documentLocalAmount'] * (1 - ($retentionPercentage / 100));
                                $data['documentRptAmount'] = $data['documentRptAmount'] * (1 - ($retentionPercentage / 100));
                            }


                            array_push($finalData, $data);

                            $taxLedgerData['inputVATGlAccountID'] = $chartOfAccountData->chartOfAccountSystemID;

                        } else {
                            Log::info('Supplier Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                            Log::info('Input Vat GL Account not assigned to company' . date('H:i:s'));
                        }
                    } else {
                        Log::info('Supplier Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                        Log::info('Input Vat Transfer GL Account not configured' . date('H:i:s'));
                    }


                    //Input VAT transfer
                    $taxConfigData = TaxService::getInputVATTransferGLAccount($masterModel["companySystemID"]);
                    if (!empty($taxConfigData)) {
                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxConfigData->inputVatTransferGLAccountAutoID)
                            ->where('companySystemID', $masterData->companySystemID)
                            ->first();

                        if (!empty($chartOfAccountData)) {
                            $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                            $data['glCode'] = $chartOfAccountData->AccountCode;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            $data['documentTransAmount'] = \Helper::roundValue(ABS($totalVATAmount)) * -1;
                            $data['documentLocalAmount'] = \Helper::roundValue(ABS($totalVATAmountLocal)) * -1;
                            $data['documentRptAmount'] = \Helper::roundValue(ABS($totalVATAmountRpt)) * -1;


                            if (TaxService::isSupplierInvoiceRcmActivated($masterModel["autoID"])) {
                                if ($retentionPercentage > 0 && $masterData->documentType != 4) {
                                    $data['documentTransAmount'] = $data['documentTransAmount'] * (1 - ($retentionPercentage / 100));
                                    $data['documentLocalAmount'] = $data['documentLocalAmount'] * (1 - ($retentionPercentage / 100));
                                    $data['documentRptAmount'] = $data['documentRptAmount'] * (1 - ($retentionPercentage / 100));
                                }
                            }

                            array_push($finalData, $data);

                            $taxLedgerData['inputVatTransferAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                        } else {
                            Log::info('Supplier Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                            Log::info('Input Vat GL Account not assigned to company' . date('H:i:s'));
                        }
                    } else {
                        Log::info('Supplier Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                        Log::info('Input Vat Transfer GL Account not configured' . date('H:i:s'));
                    }
                }

                if (TaxService::isSupplierInvoiceRcmActivated($masterModel["autoID"])) {
                    // output vat transfer entry
                    $taxOutputVATTransfer = TaxService::getOutputVATTransferGLAccount($masterModel["companySystemID"]);
                    if (!empty($taxOutputVATTransfer)) {
                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxOutputVATTransfer->outputVatTransferGLAccountAutoID)
                            ->where('companySystemID', $masterData->companySystemID)
                            ->first();

                        if (!empty($chartOfAccountData)) {
                            $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                            $data['glCode'] = $chartOfAccountData->AccountCode;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            $data['documentTransAmount'] = \Helper::roundValue(ABS(($vatDetails['totalVAT'] + $vatDetails['exemptVAT'])));
                            $data['documentLocalAmount'] = \Helper::roundValue(ABS(($vatDetails['totalVATLocal'] + $vatDetails['exemptVATLocal'])));
                            $data['documentRptAmount'] = \Helper::roundValue(ABS(($vatDetails['totalVATRpt'] + $vatDetails['exemptVATRpt'])));

                            if ($retentionPercentage > 0 && $masterData->documentType != 4) {
                                $data['documentTransAmount'] = $data['documentTransAmount'] * (1 - ($retentionPercentage / 100));
                                $data['documentLocalAmount'] = $data['documentLocalAmount'] * (1 - ($retentionPercentage / 100));
                                $data['documentRptAmount'] = $data['documentRptAmount'] * (1 - ($retentionPercentage / 100));
                            }

                            array_push($finalData, $data);

                            $taxLedgerData['outputVatTransferGLAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                        } else {
                            Log::info('Supplier Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                            Log::info('Output Vat transfer GL Account not assigned to company' . date('H:i:s'));
                        }
                    } else {
                        Log::info('Supplier Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                        Log::info('Output Vat transfer GL Account not configured' . date('H:i:s'));
                    }

                    //output vat entry
                    $taxOutputVAT = TaxService::getOutputVATGLAccount($masterModel["companySystemID"]);
                    if (!empty($taxOutputVAT)) {
                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxOutputVAT->outputVatGLAccountAutoID)
                            ->where('companySystemID', $masterData->companySystemID)
                            ->first();

                        if (!empty($chartOfAccountData)) {
                            $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                            $data['glCode'] = $chartOfAccountData->AccountCode;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            $data['documentTransAmount'] = \Helper::roundValue(ABS(($vatDetails['totalVAT'] + $vatDetails['exemptVAT']))) * -1;
                            $data['documentLocalAmount'] = \Helper::roundValue(ABS(($vatDetails['totalVATLocal'] + $vatDetails['exemptVATLocal']))) * -1;
                            $data['documentRptAmount'] = \Helper::roundValue(ABS(($vatDetails['totalVATRpt'] + $vatDetails['exemptVATRpt']))) * -1;

                            if ($retentionPercentage > 0 && $masterData->documentType != 4) {
                                $data['documentTransAmount'] = $data['documentTransAmount'] * (1 - ($retentionPercentage / 100));
                                $data['documentLocalAmount'] = $data['documentLocalAmount'] * (1 - ($retentionPercentage / 100));
                                $data['documentRptAmount'] = $data['documentRptAmount'] * (1 - ($retentionPercentage / 100));
                            }

                            array_push($finalData, $data);

                            $taxLedgerData['outputVatGLAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                        } else {
                            Log::info('Supplier Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                            Log::info('Output Vat GL Account not assigned to company' . date('H:i:s'));
                        }
                    } else {
                        Log::info('Supplier Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                        Log::info('Output Vat GL Account not configured' . date('H:i:s'));
                    }
                }
            } else if ($masterData->documentType == 3 && $masterData->item_details && count($masterData->item_details) > 0 && $masterData->item_details[0]->totalVATAmount > 0 && $directItemVatDetails['masterVATTrans']) {

                Log::info('Inside the Vat Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                $taxData = TaxService::getInputVATGLAccount($masterData->companySystemID);

                if ($directItemVatDetails['masterVATTrans'] > 0) {
                    if (!empty($taxData)) {
                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxData->inputVatGLAccountAutoID)
                            ->where('companySystemID', $masterData->companySystemID)
                            ->first();

                        if (!empty($chartOfAccountData)) {
                            $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                            $data['glCode'] = $chartOfAccountData->AccountCode;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                            $data['documentTransAmount'] = \Helper::roundValue($directItemVatDetails['masterVATTrans']);
                            $data['documentLocalAmount'] = \Helper::roundValue($directItemVatDetails['masterVATLocal']);
                            $data['documentRptAmount'] = \Helper::roundValue($directItemVatDetails['masterVATRpt']);

                            if ($retentionPercentage > 0 && $masterData->documentType != 4) {
                                $data['documentTransAmount'] = $data['documentTransAmount'] * (1 - ($retentionPercentage / 100));
                                $data['documentLocalAmount'] = $data['documentLocalAmount'] * (1 - ($retentionPercentage / 100));
                                $data['documentRptAmount'] = $data['documentRptAmount'] * (1 - ($retentionPercentage / 100));
                            }

                            array_push($finalData, $data);

                            $taxLedgerData['inputVATGlAccountID'] = $chartOfAccountData->chartOfAccountSystemID;

                            Log::info('Inside the Vat Entry InputVATTransferGLAccount Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                        } else {
                            Log::info('GRV VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                            Log::info('Input Vat Transfer GL Account not assigned to company' . date('H:i:s'));
                        }
                    } else {
                        Log::info('GRV VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                        Log::info('Input Vat Transfer GL Account not configured' . date('H:i:s'));
                    }

                    if (TaxService::isSupplierInvoiceRcmActivated($masterModel["autoID"])) {
                        if ($retentionPercentage > 0) {
                            $taxData = TaxService::getInputVATTransferGLAccount($masterData->companySystemID);
                            if (!empty($taxData)) {
                                $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxData->inputVatTransferGLAccountAutoID)
                                    ->where('companySystemID', $masterData->companySystemID)
                                    ->first();

                                if (!empty($chartOfAccountData)) {
                                    $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                    $data['glCode'] = $chartOfAccountData->AccountCode;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                                    $data['documentTransAmount'] = \Helper::roundValue($directItemVatDetails['masterVATTrans']);
                                    $data['documentLocalAmount'] = \Helper::roundValue($directItemVatDetails['masterVATLocal']);
                                    $data['documentRptAmount'] = \Helper::roundValue($directItemVatDetails['masterVATRpt']);

                                    if ($retentionPercentage > 0 && $masterData->documentType != 4) {
                                        $data['documentTransAmount'] = $data['documentTransAmount'] * ($retentionPercentage / 100);
                                        $data['documentLocalAmount'] = $data['documentLocalAmount'] * ($retentionPercentage / 100);
                                        $data['documentRptAmount'] = $data['documentRptAmount'] * ($retentionPercentage / 100);
                                    }

                                    array_push($finalData, $data);

                                    $taxLedgerData['inputVatTransferAccountID'] = $chartOfAccountData->chartOfAccountSystemID;

                                    Log::info('Inside the Vat Entry InputVATTransferGLAccount Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                } else {
                                    Log::info('GRV VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                    Log::info('Input Vat Transfer GL Account not assigned to company' . date('H:i:s'));
                                }
                            } else {
                                Log::info('GRV VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                Log::info('Input Vat Transfer GL Account not configured' . date('H:i:s'));
                            }
                        }
                    }

                }
            }


            if ($tax && $directVATDetails['masterVATTrans'] > 0) {
                //input vat entry
                $taxConfigData = TaxService::getInputVATGLAccount($masterModel["companySystemID"]);
                if (!empty($taxConfigData)) {
                    $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxConfigData->inputVatGLAccountAutoID)
                        ->where('companySystemID', $masterData->companySystemID)
                        ->first();

                    if (!empty($chartOfAccountData)) {
                        $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                        $data['glCode'] = $chartOfAccountData->AccountCode;
                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                        $data['documentTransAmount'] = \Helper::roundValue(ABS($directVATDetails['masterVATTrans']));
                        $data['documentLocalAmount'] = \Helper::roundValue(ABS($directVATDetails['masterVATLocal']));
                        $data['documentRptAmount'] = \Helper::roundValue(ABS($directVATDetails['masterVATRpt']));

                        if ($retentionPercentage > 0 && $masterData->documentType != 4 && !$masterData->rcmActivated) {
                            $data['documentTransAmount'] = $data['documentTransAmount'] * (1 - ($retentionPercentage / 100));
                            $data['documentLocalAmount'] = $data['documentLocalAmount'] * (1 - ($retentionPercentage / 100));
                            $data['documentRptAmount'] = $data['documentRptAmount'] * (1 - ($retentionPercentage / 100));
                        }

                        array_push($finalData, $data);

                        $taxLedgerData['inputVATGlAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                    } else {
                        Log::info('Supplier Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                        Log::info('Input Vat GL Account not assigned to company' . date('H:i:s'));
                    }
                } else {
                    Log::info('Supplier Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                    Log::info('Input Vat GL Account not configured' . date('H:i:s'));
                }

                if (TaxService::isSupplierInvoiceRcmActivated($masterModel["autoID"])) {
                    if ($retentionPercentage > 0 && $masterData->documentType == 1) {
                        $taxConfigData = TaxService::getInputVATTransferGLAccount($masterModel["companySystemID"]);
                        if (!empty($taxConfigData)) {
                            $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxConfigData->inputVatTransferGLAccountAutoID)
                                ->where('companySystemID', $masterData->companySystemID)
                                ->first();

                            if (!empty($chartOfAccountData)) {
                                $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                $data['glCode'] = $chartOfAccountData->AccountCode;
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                $data['documentTransAmount'] = \Helper::roundValue(ABS($directVATDetails['masterVATTrans']));
                                $data['documentLocalAmount'] = \Helper::roundValue(ABS($directVATDetails['masterVATLocal']));
                                $data['documentRptAmount'] = \Helper::roundValue(ABS($directVATDetails['masterVATRpt']));

                                // if ($retentionPercentage > 0 && $masterData->documentType != 4) {
                                //     $data['documentTransAmount'] = $data['documentTransAmount'] * ($retentionPercentage / 100);
                                //     $data['documentLocalAmount'] = $data['documentLocalAmount'] * ($retentionPercentage / 100);
                                //     $data['documentRptAmount'] = $data['documentRptAmount'] * ($retentionPercentage / 100);
                                // }

                                array_push($finalData, $data);

                                $taxLedgerData['inputVatTransferAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                            } else {
                                Log::info('Supplier Invoice VAT Transfer GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                Log::info('Input Vat Transfer GL Account not assigned to company' . date('H:i:s'));
                            }
                        } else {
                            Log::info('Supplier Invoice VAT Transfer GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                            Log::info('Input Vat Transfer GL Account not configured' . date('H:i:s'));
                        }
                    }
                }


                //if rcm activated tax entries
                if ($masterData->rcmActivated == 1) {
                    if ($masterData->documentType == 0 || $masterData->documentType == 2) {
                        // input vat transfer entry
                        $taxInputVATTransfer = TaxService::getInputVATTransferGLAccount($masterModel["companySystemID"]);
                        if (!empty($taxConfigData)) {
                            $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxInputVATTransfer->inputVatTransferGLAccountAutoID)
                                ->where('companySystemID', $masterData->companySystemID)
                                ->first();

                            if (!empty($chartOfAccountData)) {
                                $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                $data['glCode'] = $chartOfAccountData->AccountCode;
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                $data['documentTransAmount'] = \Helper::roundValue(ABS($taxTrans)) * -1;
                                $data['documentLocalAmount'] = \Helper::roundValue(ABS($taxLocal)) * -1;
                                $data['documentRptAmount'] = \Helper::roundValue(ABS($taxRpt)) * -1;

                                if ($retentionPercentage > 0 && $masterData->documentType != 4) {
                                    $data['documentTransAmount'] = $data['documentTransAmount'] * (1 - ($retentionPercentage / 100));
                                    $data['documentLocalAmount'] = $data['documentLocalAmount'] * (1 - ($retentionPercentage / 100));
                                    $data['documentRptAmount'] = $data['documentRptAmount'] * (1 - ($retentionPercentage / 100));
                                }

                                array_push($finalData, $data);

                                $taxLedgerData['inputVatTransferAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                            } else {
                                Log::info('Supplier Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                Log::info('Input Vat transfer GL Account not assigned to company' . date('H:i:s'));
                            }
                        } else {
                            Log::info('Supplier Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                            Log::info('Input Vat transfer GL Account not configured' . date('H:i:s'));
                        }

                        // output vat transfer entry
                        $taxOutputVATTransfer = TaxService::getOutputVATTransferGLAccount($masterModel["companySystemID"]);
                        if (!empty($taxConfigData)) {
                            $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxOutputVATTransfer->outputVatTransferGLAccountAutoID)
                                ->where('companySystemID', $masterData->companySystemID)
                                ->first();

                            if (!empty($chartOfAccountData)) {
                                $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                $data['glCode'] = $chartOfAccountData->AccountCode;
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                $data['documentTransAmount'] = \Helper::roundValue(ABS($taxTrans));
                                $data['documentLocalAmount'] = \Helper::roundValue(ABS($taxLocal));
                                $data['documentRptAmount'] = \Helper::roundValue(ABS($taxRpt));

                                // if ($retentionPercentage > 0 && $masterData->documentType != 4) {
                                //     $data['documentTransAmount'] = $data['documentTransAmount'] * (1 - ($retentionPercentage/100));
                                //     $data['documentLocalAmount'] = $data['documentLocalAmount'] * (1 - ($retentionPercentage/100));
                                //     $data['documentRptAmount'] = $data['documentRptAmount'] * (1 - ($retentionPercentage/100));
                                // }

                                array_push($finalData, $data);

                                $taxLedgerData['outputVatTransferGLAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                            } else {
                                Log::info('Supplier Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                Log::info('Output Vat transfer GL Account not assigned to company' . date('H:i:s'));
                            }
                        } else {
                            Log::info('Supplier Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                            Log::info('Output Vat transfer GL Account not configured' . date('H:i:s'));
                        }
                    }


                    //output vat entry
                    $taxOutputVAT = TaxService::getOutputVATGLAccount($masterModel["companySystemID"]);
                    if (!empty($taxConfigData)) {
                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxOutputVAT->outputVatGLAccountAutoID)
                            ->where('companySystemID', $masterData->companySystemID)
                            ->first();

                        if ($masterData->rcmActivated == 1) {
                            $exemptExpenseDetails = TaxService::processSIExemptVatDirectInvoice($masterModel["autoID"]);
                            if(!empty($exemptExpenseDetails)) {
                                $taxTrans = $taxTrans - $exemptExpenseDetails->VATAmount;
                                $taxLocal = $taxLocal - $exemptExpenseDetails->VATAmountLocal;
                                $taxRpt = $taxRpt - $exemptExpenseDetails->VATAmountRpt;
                            }
                        }

                        if (!empty($chartOfAccountData)) {
                            $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                            $data['glCode'] = $chartOfAccountData->AccountCode;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            $data['documentTransAmount'] = \Helper::roundValue(ABS($taxTrans)) * -1;
                            $data['documentLocalAmount'] = \Helper::roundValue(ABS($taxLocal)) * -1;
                            $data['documentRptAmount'] = \Helper::roundValue(ABS($taxRpt)) * -1;

                            // if ($retentionPercentage > 0 && $masterData->documentType == 1) {
                            //     $data['documentTransAmount'] = $data['documentTransAmount'] * (1 - ($retentionPercentage / 100));
                            //     $data['documentLocalAmount'] = $data['documentLocalAmount'] * (1 - ($retentionPercentage / 100));
                            //     $data['documentRptAmount'] = $data['documentRptAmount'] * (1 - ($retentionPercentage / 100));
                            // }

                            array_push($finalData, $data);

                            $taxLedgerData['outputVatGLAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                        } else {
                            Log::info('Supplier Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                            Log::info('Output Vat GL Account not assigned to company' . date('H:i:s'));
                        }
                    } else {
                        Log::info('Supplier Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                        Log::info('Output Vat GL Account not configured' . date('H:i:s'));
                    }

                }
            }

            if(($masterData->documentType == 0) || ($masterData->documentType == 2)) {
                for ($i = 0; $i < count($finalData); $i++) {
                    $finalData[$i]['documentLocalCurrencyER'] = ExchangeSetupConfig::calculateLocalER($finalData[$i]["documentTransAmount"],$finalData[$i]["documentLocalAmount"]);
                    $finalData[$i]['documentRptCurrencyER'] = ExchangeSetupConfig::calculateReportingER($finalData[$i]["documentTransAmount"],$finalData[$i]["documentRptAmount"]);
                }
            }
        }

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'taxLedgerData' => $taxLedgerData]];
    }
}
