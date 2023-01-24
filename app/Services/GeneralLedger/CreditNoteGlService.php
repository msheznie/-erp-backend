<?php

namespace App\Services\GeneralLedger;

use App\helper\TaxService;
use App\Models\CreditNote;
use App\Models\CreditNoteDetails;
use App\Models\CurrencyConversion;
use App\Models\Employee;
use App\Models\Taxdetail;
use App\Models\Company;
use App\Models\ChartOfAccountsAssigned;
use App\Models\ChartOfAccount;
use App\Models\SystemGlCodeScenarioDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\GeneralLedger\GlPostedDateService;

class CreditNoteGlService
{
	public static function processEntry($masterModel)
	{
        $data = [];
        $taxLedgerData = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);

        $validatePostedDate = GlPostedDateService::validatePostedDate($masterModel["autoID"], $masterModel["documentSystemID"]);

        if (!$validatePostedDate['status']) {
            return ['status' => false, 'message' => $validatePostedDate['message']];
        }
        
        $masterData = CreditNote::with(['details' => function ($query) {
            $query->selectRaw('SUM(netAmountLocal) as localAmount, SUM(netAmountRpt) as rptAmount,SUM(netAmount) as transAmount,creditNoteAutoID,serviceLineSystemID,serviceLineCode,clientContractID,contractUID');
        }], 'finance_period_by')->find($masterModel["autoID"]);

        //all acoount
        $allAc = CreditNoteDetails::with(['chartofaccount'])
            ->selectRaw("SUM(netAmountLocal) as localAmount, SUM(netAmountRpt) as rptAmount,SUM(netAmount) as transAmount,chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,creditAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,creditAmountCurrencyER as transCurrencyER,serviceLineSystemID,serviceLineCode,clientContractID,contractUID,comments,chartOfAccountSystemID")
            ->WHERE('creditNoteAutoID', $masterModel["autoID"])
            ->groupBy('serviceLineSystemID', 'chartOfAccountSystemID', 'clientContractID', 'comments')
            ->get();

        $tax = Taxdetail::selectRaw("SUM(localAmount) as localAmount, SUM(rptAmount) as rptAmount,SUM(amount) as transAmount,localCurrencyID,rptCurrencyID as reportingCurrencyID,currency as supplierTransactionCurrencyID,currencyER as supplierTransactionER,rptCurrencyER as companyReportingER,localCurrencyER")
            ->WHERE('documentSystemCode', $masterModel["autoID"])
            ->WHERE('documentSystemID', $masterModel["documentSystemID"])
            ->groupBy('documentSystemCode')
            ->first();

        //all details
        $detailsCreditNote = CreditNoteDetails::with(['chartofaccount'])
            ->selectRaw("SUM(netAmountLocal) as localAmount, SUM(netAmountRpt) as rptAmount, SUM(netAmount) as transAmount, SUM(VATAmount) as transTax, SUM(VATAmountLocal) as localTax, SUM(VATAmountRpt) as rptTax, chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,creditAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,creditAmountCurrencyER as transCurrencyER,serviceLineSystemID,serviceLineCode,clientContractID,contractUID,comments,chartOfAccountSystemID")
            ->WHERE('creditNoteAutoID', $masterModel["autoID"])
            ->groupBy('serviceLineSystemID')
            ->get();

        $taxGLCode = Company::find($masterModel["companySystemID"]);

        $taxLocal = 0;
        $taxRpt = 0;
        $taxTrans = 0;

        if ($tax) {
            $taxLocal = $tax->localAmount;
            $taxRpt = $tax->rptAmount;
            $taxTrans = $tax->transAmount;
        }

        $masterDocumentDate = isset($masterModel['documentDateOveride']) ? $masterModel['documentDateOveride'] : $validatePostedDate['postedDate'];

        if ($masterData) {
            foreach ($detailsCreditNote as $detail) {
                $data['companySystemID'] = $masterData->companySystemID;
                $data['companyID'] = $masterData->companyID;
                $data['masterCompanyID'] = null;
                $data['documentSystemID'] = $masterData->documentSystemiD;
                $data['documentID'] = $masterData->documentID;
                $data['documentSystemCode'] = $masterModel["autoID"];
                $data['documentCode'] = $masterData->creditNoteCode;
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

                $data['chartOfAccountSystemID'] = $masterData->customerGLCodeSystemID;
                $data['glCode'] = $masterData->customerGLCode;
                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                $data['documentTransCurrencyID'] = $masterData->customerCurrencyID;
                $data['documentTransCurrencyER'] = $masterData->customerCurrencyER;
                $data['documentTransAmount'] = \Helper::roundValue(ABS($detail->transAmount + $detail->transTax)) * -1;
                $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                $data['documentLocalAmount'] = \Helper::roundValue(ABS($detail->localAmount + $detail->localTax)) * -1;
                $data['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                $data['documentRptCurrencyER'] = $masterData->companyReportingER;
                $data['documentRptAmount'] = \Helper::roundValue(ABS($detail->rptAmount + $detail->rptTax)) * -1;
                $data['serviceLineSystemID'] = $detail->serviceLineSystemID;
                $data['serviceLineCode'] = $detail->serviceLineCode;
                if ($detail->clientContractID) {
                    $data['clientContractID'] = $detail->clientContractID;
                    $data['contractUID'] = $detail->contractUID;
                } else {
                    $data['clientContractID'] = 'X';
                    $data['contractUID'] = 159;
                }
                $data['supplierCodeSystem'] = $masterData->customerID;
                $data['holdingShareholder'] = null;
                $data['holdingPercentage'] = 0;
                $data['nonHoldingPercentage'] = 0;
                $data['chequeNumber'] = 0;
                $data['invoiceNumber'] = 0;
                $data['documentType'] = $masterData->documentType;
                $data['createdDateTime'] = \Helper::currentDateTime();
                $data['createdUserID'] = $empID->empID;
                $data['createdUserSystemID'] = $empID->employeeSystemID;
                $data['createdUserPC'] = gethostname();
                $data['timestamp'] = \Helper::currentDateTime();
                array_push($finalData, $data);
            }

            if ($allAc) {
                foreach ($allAc as $val) {
                    if ($val->serviceLineSystemID) {
                        $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                        $data['serviceLineCode'] = $val->serviceLineCode;
                    } else {
                        $data['serviceLineSystemID'] = 24;
                        $data['serviceLineCode'] = 'X';
                    }

                    if ($val->clientContractID) {
                        $data['clientContractID'] = $val->clientContractID;
                        $data['contractUID'] = $val->contractUID;
                    } else {
                        $data['clientContractID'] = 'X';
                        $data['contractUID'] = 159;
                    }

                    $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                    $data['glCode'] = $val->financeGLcodePL;
                    $data['documentNarration'] = $val->comments;
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                    $data['documentNarration'] = $val->comments;
                    $data['documentTransCurrencyID'] = $val->transCurrencyID;
                    $data['documentTransCurrencyER'] = $val->transCurrencyER;
                    $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount));
                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                    $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                    $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount));
                    $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                    $data['documentRptCurrencyER'] = $val->reportingCurrencyER;
                    $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount));
                    $data['timestamp'] = \Helper::currentDateTime();
                    array_push($finalData, $data);
                }
            }

            if ($tax) {
                foreach ($detailsCreditNote as $detail) {

                    $taxConfigData = TaxService::getOutputVATGLAccount($masterModel["companySystemID"]);

                    if (!empty($taxConfigData)) {
                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxConfigData->outputVatGLAccountAutoID)
                            ->where('companySystemID', $masterData->companySystemID)
                            ->first();

                        if (!empty($chartOfAccountData)) {
                            $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                            $data['glCode'] = $chartOfAccountData->AccountCode;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                            $taxLedgerData['outputVatGLAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                        } else {
                            Log::info('Credit Note VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                            Log::info('Output Vat GL Account not assigned to company' . date('H:i:s'));
                        }
                    } else {
                        Log::info('Credit Note VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                        Log::info('Output Vat GL Account not configured' . date('H:i:s'));
                    }
                    $data['serviceLineSystemID'] = $detail->serviceLineSystemID;
                    $data['serviceLineCode'] = $detail->serviceLineCode;
                    if ($detail->clientContractID) {
                        $data['clientContractID'] = $detail->clientContractID;
                        $data['contractUID'] = $detail->contractUID;
                    } else {
                        $data['clientContractID'] = 'X';
                        $data['contractUID'] = 159;
                    }

                    $data['documentTransCurrencyID'] = $tax->supplierTransactionCurrencyID;
                    $data['documentTransCurrencyER'] = $tax->supplierTransactionER;
                    $data['documentTransAmount'] = \Helper::roundValue(ABS($detail->transTax));
                    $data['documentLocalCurrencyID'] = $tax->localCurrencyID;
                    $data['documentLocalCurrencyER'] = $tax->localCurrencyER;
                    $data['documentLocalAmount'] = \Helper::roundValue(ABS($detail->localTax));
                    $data['documentRptCurrencyID'] = $tax->reportingCurrencyID;
                    $data['documentRptCurrencyER'] = $tax->companyReportingER;
                    $data['documentRptAmount'] = \Helper::roundValue(ABS($detail->rptTax));
                    array_push($finalData, $data);
                }
            }

        }

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'taxLedgerData' => $taxLedgerData]];
    }
}