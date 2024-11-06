<?php
/**
 * =============================================
 * -- File Name : AccountsReceivableReportAPIControllerroller.php
 * -- Project Name : ERP
 * -- Module Name :  Report
 * -- Author : Mubashir
 * -- Create date : 9 - April 2018
 * -- Description : This file contains the all the report generation code
 * -- REVISION HISTORY
 * -- Date: 04-June 2018 By: Mubashir Description: Added Grvmaster approved filter from reports
 * -- Date: 06-June 2018 By: Mubashir Description: Removed Grvmaster approved filter for item analaysis report
 * -- Date: 08-june 2018 By: Mubashir Description: Added new functions named as getAcountReceivableFilterData(),
 * -- Date: 18-june 2018 By: Mubashir Description: Added new functions named as pdfExportReport(),
 * -- Date: 19-june 2018 By: Mubashir Description: Added new functions named as getCustomerStatementAccountQRY(),
 * -- Date: 19-june 2018 By: Mubashir Description: Added new functions named as getCustomerBalanceStatementQRY(),
 * -- Date: 20-june 2018 By: Mubashir Description: Added new functions named as getCustomerAgingDetailQRY(),
 * -- Date: 22-june 2018 By: Mubashir Description: Added new functions named as getCustomerAgingSummaryQRY(),
 * -- Date: 29-june 2018 By: Nazir Description: Added new functions named as getCustomerCollectionQRY(),
 * -- Date: 29-june 2018 By: Mubashir Description: Added new functions named as getCustomerLedgerTemplate1QRY(),
 * -- Date: 02-july 2018 By: Fayas Description: Added new functions named as getCustomerBalanceSummery(),getCustomerRevenueMonthlySummary(),
 * -- Date: 02-July 2018 By: Nazir Description: Added new functions named as getCustomerCollectionMonthlyQRY(),
 * -- Date: 02-july 2018 By: Mubashir Description: Added new functions named as getCustomerLedgerTemplate2QRY(),
 * -- Date: 03-july 2018 By: Mubashir Description: Added new functions named as getCustomerSalesRegisterQRY(),
 * -- Date: 03-july 2018 By: Nazir Description: Added new functions named as getCustomerCollectionCNExcelQRY(),
 * -- Date: 03-july 2018 By: Nazir Description: Added new functions named as getCustomerCollectionBRVExcelQRY()
 * -- Date: 03-july 2018 By: Fayas Description: Added new functions named as getRevenueByCustomer()
 * -- Date: 10-july 2018 By: Nazir Description: Added new functions named as getCustomerSummaryRevenueQRY()
 * -- Date: 10-july 2018 By: Nazir Description: Added new functions named as getCustomerSummaryRevenueQRY()
 * -- Date: 10-july 2018 By: Nazir Description: Added new functions named as getCustomerSummaryRevenueQRY()
 * -- Date: 10-july 2018 By: Nazir Description: Added new functions named as getCustomerSummaryCollectionQRY()
 * -- Date: 11-july 2018 By: Nazir Description: Added new functions named as getCustomerSummaryOutstandingQRY()
 * -- Date: 11-july 2018 By: Nazir Description: Added new functions named as getCustomerSummaryRevenueServiceLineBaseQRY()
 * -- Date: 13-February 2019 By: Nazir Description: Added new functions named as getCustomerSummaryOutstandingUpdatedQRY()
 */

namespace App\Http\Controllers\API;

use App\Exports\AccountsReceivable\CustomerAgingDetailReport;
use App\Exports\AccountsReceivable\CustomerBalanceSummaryReport;
use App\Exports\AccountsReceivable\CustomerStatement\CustomerBalanceStatementReport;
use App\Exports\AccountsReceivable\CustomerStatement\CustomerStatementOfAccountReport;
use App\Http\Controllers\AppBaseController;
use App\Jobs\SentCustomerLedger;
use App\Models\AccountsReceivableLedger;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\Contract;
use App\Models\CurrencyMaster;
use App\Models\CustomerAssigned;
use App\Models\CustomerContactDetails;
use App\Models\CustomerMasterCategory;
use App\Models\ReportTemplate;
use App\Models\CustomerMaster;
use App\Models\FreeBillingMasterPerforma;
use App\Models\GeneralLedger;
use App\Services\Currency\CurrencyService;
use App\Services\Excel\ExportReportToExcelService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\helper\CreateExcel;
use App\Jobs\DocumentAttachments\CustomerStatementJob;
use App\Models\CustomerMasterCategoryAssigned;
use App\Jobs\Report\AccountsReceivablePdfJob;

class AccountsReceivableReportAPIController extends AppBaseController
{
    /*validate each report*/
    public function validateReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'CS':
                $reportTypeID = '';
                if (isset($request->reportTypeID)) {
                    $reportTypeID = $request->reportTypeID;
                }
                if ($reportTypeID == 'CBS') {
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required',
                        'customers' => 'required',
                        'reportTypeID' => 'required',
                    ]);
                } else {
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required|date',
                        'toDate' => 'required|date|after_or_equal:fromDate',
                        'customers' => 'required',
                        'reportTypeID' => 'required',
                    ]);
                }

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                break;
            case 'CA':
                $reportTypeID = '';
                if (isset($request->reportTypeID)) {
                    $reportTypeID = $request->reportTypeID;
                }
                if ($reportTypeID == 'CAD' || $reportTypeID == 'CAS') {
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required',
                        'customers' => 'required',
                        'reportTypeID' => 'required',
                        'interval' => 'required',
                        'through' => 'required',
                    ]);
                }

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                break;
            case 'CC':
                $reportTypeID = '';
                if (isset($request->reportTypeID)) {
                    $reportTypeID = $request->reportTypeID;
                }
                if ($reportTypeID == 'CCR') {
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required',
                        'toDate' => 'required|date|after_or_equal:fromDate',
                        'customers' => 'required',
                        'reportTypeID' => 'required',
                        'currencyID' => 'required'
                    ]);
                } else if ($reportTypeID == 'CMR') {
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required',
                        'servicelines' => 'required',
                        'customers' => 'required',
                        'reportTypeID' => 'required',
                        'currencyID' => 'required',
                        'year' => 'required'
                    ]);

                    $fromDate = new Carbon($request->fromDate);
                    $fromDate = $fromDate->format('d/m/Y');
                    $year = explode("/", $fromDate);
                    if ($year['2'] != $request->year) {
                        return $this->sendError(trans('custom.not_in_selected_year'));
                    }
                }

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                break;
            case 'CL':
                $reportTypeID = '';
                if (isset($request->reportTypeID)) {
                    $reportTypeID = $request->reportTypeID;
                }
                if ($reportTypeID == 'CLT1') {
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required',
                        'customers' => 'required',
                        'reportTypeID' => 'required',
                        'controlAccountsSystemID' => 'required',
                    ]);
                } else if ($reportTypeID == 'CLT2') {
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required',
                        'toDate' => 'required|date|after_or_equal:fromDate',
                        'customers' => 'required',
                        'reportTypeID' => 'required',
                        'controlAccountsSystemID' => 'required'
                    ]);
                }

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                break;
            case 'CBSUM':

                $validator = \Validator::make($request->all(), [
                    'fromDate' => 'required',
                    'customers' => 'required',
                    'reportTypeID' => 'required',
                    'controlAccountsSystemID' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                break;
            case 'CR':

                $reportTypeID = '';
                if (isset($request->reportTypeID)) {
                    $reportTypeID = $request->reportTypeID;
                }
                if ($reportTypeID == 'RC') {
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required',
                        'toDate' => 'required|date|after_or_equal:fromDate',
                        'customers' => 'required',
                        'reportTypeID' => 'required',

                    ]);
                } else if ($reportTypeID == 'RMS') {
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required',
                        'customers' => 'required',
                        'reportTypeID' => 'required',
                        'year' => 'required',
                        'currencyID' => 'required',
                    ]);

                    $fromDate = new Carbon($request->fromDate);
                    $fromDate = $fromDate->format('d/m/Y');
                    $year = explode("/", $fromDate);
                    if ($year['2'] != $request->year) {
                        return $this->sendError(trans('custom.not_in_selected_year'));
                    }
                }

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }


                break;
            case 'CSR':
                $validator = \Validator::make($request->all(), [
                    'fromDate' => 'required',
                    'toDate' => 'required|date|after_or_equal:fromDate',
                    'customers' => 'required',
                    'reportTypeID' => 'required',
                    'controlAccountsSystemID' => 'required',
                    'currencyID' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                break;
            case 'CRCR':
                $validator = \Validator::make($request->all(), [
                    'fromDate' => 'required',
                    'reportTypeID' => 'required',
                    'currencyID' => 'required',
                    'customers' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }


                break;
            case 'CNR':
                $validator = \Validator::make($request->all(), [
                    'fromDate' => 'required',
                    'toDate' => 'required|date|after_or_equal:fromDate',
                    'customers' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            default:
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.report_id')]));
        }

    }

    /*generate report according to each report id*/
    public function generateReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'CS': //Customer Statement Report
                $reportTypeID = $request->reportTypeID;
                if ($reportTypeID == 'CBS') { //customer balance statement

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerBalanceStatementQRY($request);

                    $outputArr = array();
                    $grandTotal = collect($output)->pluck('balanceAmount')->toArray();
                    $grandTotal = array_sum($grandTotal);

                    $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                    $decimalPlace = array_unique($decimalPlace);

                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->customerName][$val->documentCurrency][] = $val;
                        }
                    }
                    return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotal, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2);
                } else {
                    //customer statement of account
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));

                    $checkIsGroup = Company::find($request->companySystemID);

                    if (empty($checkIsGroup)) {
                        return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company')]));
                    }

                    $output = $this->getCustomerStatementAccountQRY($request);

                    $balanceTotal = collect($output)->pluck('balanceAmount')->toArray();
                    $balanceTotal = array_sum($balanceTotal);

                    $receiptAmount = collect($output)->pluck('receiptAmount')->toArray();
                    $receiptAmount = array_sum($receiptAmount);

                    $invoiceAmount = collect($output)->pluck('invoiceAmount')->toArray();
                    $invoiceAmount = array_sum($invoiceAmount);

                    $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                    $decimalPlace = array_unique($decimalPlace);

                    $currencyCode = "";
                    $currency = \Helper::companyCurrency($request->companySystemID);

                    if ($request->currencyID == 2) {
                        $currencyCode = $currency->localcurrency->CurrencyCode;
                    }
                    if ($request->currencyID == 3) {
                        $currencyCode = $currency->reportingcurrency->CurrencyCode;
                    }

                    $outputArr = array();

                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->customerName][$val->documentCurrency][] = $val;
                        }
                    }

                    return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'balanceAmount' => $balanceTotal, 'receiptAmount' => $receiptAmount, 'invoiceAmount' => $invoiceAmount, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'reportDate' => date('d/m/Y H:i:s A'), 'currency' => 'Currency: ' . $currencyCode);
                }
                break;
            case 'CA': //Customer Aging
                $reportTypeID = $request->reportTypeID;
                if ($reportTypeID == 'CAD') { //customer aging detail

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerAgingDetailQRY($request);

                    $outputArr = array();
                    $customerCreditDays = array();
                    $grandTotalArr = array();
                    if ($output['aging']) {
                        foreach ($output['aging'] as $val) {
                            $total = collect($output['data'])->pluck($val)->toArray();
                            $grandTotalArr[$val] = array_sum($total);
                        }
                    }

                    if ($output['data']) {
                        foreach ($output['data'] as $val) {
                            $outputArr[$val->customerName][$val->documentCurrency][] = $val;
                            $customerCreditDays[$val->customerName] = $val->creditDays;
                        }
                    }

                    $decimalPlaces = 2;
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($companyCurrency) {
                        if ($request->currencyID == 2) {
                            $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                        } else if ($request->currencyID == 3) {
                            $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                        }
                    }

                    $invoiceAmountTotal = collect($output['data'])->pluck('invoiceAmount')->toArray();
                    $invoiceAmountTotal = array_sum($invoiceAmountTotal);

                    return array('reportData' => $outputArr, 'customerCreditDays' => $customerCreditDays, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces, 'agingRange' => $output['aging'], 'invoiceAmountTotal' => $invoiceAmountTotal);
                } else {
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerAgingSummaryQRY($request);

                    $outputArr = array();
                    $grandTotalArr = array();
                    if ($output['aging']) {
                        foreach ($output['aging'] as $val) {
                            $total = collect($output['data'])->pluck($val)->toArray();
                            $grandTotalArr[$val] = array_sum($total);
                        }
                    }

                    if ($output['data']) {
                        foreach ($output['data'] as $val) {
                            $outputArr[$val->concatCompanyName][$val->documentCurrency][] = $val;
                        }
                    }

                    $decimalPlaces = 2;
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($companyCurrency) {
                        if ($request->currencyID == 2) {
                            $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                        } else if ($request->currencyID == 3) {
                            $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                        }
                    }

                    return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces, 'agingRange' => $output['aging']);
                }
                break;
            case 'CC': //Customer Collection
                $reportTypeID = $request->reportTypeID;
                $selectedCurrency = '';

                $fromDate = new Carbon($request->fromDate);
                $fromDate = $fromDate->format('d/m/Y');

                $toDate = new Carbon($request->toDate);
                $toDate = $toDate->format('d/m/Y');

                $currencyMaster = CurrencyMaster::where('currencyID', $request->currencyID)->first();

                if ($currencyMaster) {
                    $selectedCurrency = $currencyMaster->CurrencyName;
                }

                if ($reportTypeID == 'CCR') { //Customer collection report

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerCollectionQRY($request);

                    $outputArr = array();

                    $bankPaymentTotal = collect($output)->pluck('BRVDocumentAmount')->toArray();
                    $bankPaymentTotal = array_sum($bankPaymentTotal);

                    $creditNoteTotal = collect($output)->pluck('CNDocumentAmount')->toArray();
                    $creditNoteTotal = array_sum($creditNoteTotal);

                    $decimalPlaces = 2;
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($companyCurrency) {
                        if ($request->currencyID == 2) {
                            $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                            $selectedCurrency = $companyCurrency->localcurrency->CurrencyCode;
                        } else if ($request->currencyID == 3) {
                            $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                            $selectedCurrency = $companyCurrency->reportingcurrency->CurrencyCode;
                        }
                    }

                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->CompanyName][$val->companyID][] = $val;
                        }
                    }

                    return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'currencyDecimalPlace' => $decimalPlaces, 'fromDate' => $fromDate, 'toDate' => $toDate, 'selectedCurrency' => $selectedCurrency, 'bankPaymentTotal' => $bankPaymentTotal, 'creditNoteTotal' => $creditNoteTotal);
                } else {

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerCollectionMonthlyQRY($request);

                    $decimalPlaces = 2;
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($companyCurrency) {
                        if ($request->currencyID == 2) {
                            $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                            $selectedCurrency = $companyCurrency->localcurrency->CurrencyCode;
                        } else if ($request->currencyID == 3) {
                            $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                            $selectedCurrency = $companyCurrency->reportingcurrency->CurrencyCode;
                        }
                    }

                    $outputArr = array();

                    $janTotal = collect($output)->pluck('Jan')->toArray();
                    $janTotal = array_sum($janTotal);

                    $febTotal = collect($output)->pluck('Feb')->toArray();
                    $febTotal = array_sum($febTotal);

                    $marTotal = collect($output)->pluck('March')->toArray();
                    $marTotal = array_sum($marTotal);

                    $aprTotal = collect($output)->pluck('April')->toArray();
                    $aprTotal = array_sum($aprTotal);

                    $mayTotal = collect($output)->pluck('May')->toArray();
                    $mayTotal = array_sum($mayTotal);

                    $juneTotal = collect($output)->pluck('June')->toArray();
                    $juneTotal = array_sum($juneTotal);

                    $julyTotal = collect($output)->pluck('July')->toArray();
                    $julyTotal = array_sum($julyTotal);

                    $augTotal = collect($output)->pluck('Aug')->toArray();
                    $augTotal = array_sum($augTotal);

                    $sepTotal = collect($output)->pluck('Sept')->toArray();
                    $sepTotal = array_sum($sepTotal);

                    $octTotal = collect($output)->pluck('Oct')->toArray();
                    $octTotal = array_sum($octTotal);

                    $novTotal = collect($output)->pluck('Nov')->toArray();
                    $novTotal = array_sum($novTotal);

                    $decTotal = collect($output)->pluck('Dece')->toArray();
                    $decTotal = array_sum($decTotal);

                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->CompanyName][$val->companyID][] = $val;
                        }
                    }

                    return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'currencyDecimalPlace' => $decimalPlaces, 'fromDate' => $fromDate, 'toDate' => $toDate, 'selectedCurrency' => $selectedCurrency, 'selectedYear' => $request->year, 'janTotal' => $janTotal, 'febTotal' => $febTotal, 'marTotal' => $marTotal, 'aprTotal' => $aprTotal, 'mayTotal' => $mayTotal, 'juneTotal' => $juneTotal, 'julyTotal' => $julyTotal, 'augTotal' => $augTotal, 'sepTotal' => $sepTotal, 'octTotal' => $octTotal, 'novTotal' => $novTotal, 'decTotal' => $decTotal);

                }
                break;
            case 'CL': //Customer Ledger
                $reportTypeID = $request->reportTypeID;
                if ($reportTypeID == 'CLT1') { //customer ledger template 1

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerLedgerTemplate1QRY($request);

                    $outputArr = array();
                    $invoiceAmount = collect($output)->pluck('invoiceAmount')->toArray();
                    $invoiceAmount = array_sum($invoiceAmount);

                    $paidAmount = collect($output)->pluck('paidAmount')->toArray();
                    $paidAmount = array_sum($paidAmount);

                    $balanceAmount = collect($output)->pluck('balanceAmount')->toArray();
                    $balanceAmount = array_sum($balanceAmount);

                    $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                    $decimalPlace = array_unique($decimalPlace);

                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->concatCustomerName][$val->documentCurrency][] = $val;
                        }
                    }
                    return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'balanceAmount' => $balanceAmount, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'paidAmount' => $paidAmount, 'invoiceAmount' => $invoiceAmount);
                } else {
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerLedgerTemplate2QRY($request);

                    $outputArr = array();
                    $invoiceAmount = collect($output)->pluck('invoiceAmount')->toArray();
                    $invoiceAmount = array_sum($invoiceAmount);

                    $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                    $decimalPlace = array_unique($decimalPlace);

                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->concatCustomerName][$val->documentCurrency][] = $val;
                        }
                    }
                    return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'invoiceAmount' => $invoiceAmount);
                }
                break;
            case 'CBSUM': //Customer Balance Summery
                $reportTypeID = $request->reportTypeID;
                if ($reportTypeID == 'CBSUM') { //customer ledger template 1

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerBalanceSummery($request);

                    $outputArr = array();
                    $localAmount = collect($output)->pluck('localAmount')->toArray();
                    $localAmountTotal = array_sum($localAmount);

                    $rptAmount = collect($output)->pluck('RptAmount')->toArray();
                    $rptAmountTotal = array_sum($rptAmount);

                    $decimalPlaceLocal = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                    $decimalPlaceL = array_unique($decimalPlaceLocal);

                    $decimalPlaceRpt = collect($output)->pluck('documentRptCurrencyID')->toArray();
                    $decimalPlaceR = array_unique($decimalPlaceRpt);

                    $localCurrencyId = 2;
                    $rptCurrencyId = 2;

                    if (!empty($decimalPlaceL)) {
                        $localCurrencyId = $decimalPlaceL[0];
                    }

                    if (!empty($decimalPlaceR)) {
                        $rptCurrencyId = $decimalPlaceR[0];
                    }


                    $localCurrency = CurrencyMaster::where('currencyID', $localCurrencyId)->first();
                    $rptCurrency = CurrencyMaster::where('currencyID', $rptCurrencyId)->first();


                    return array('reportData' => $output,
                        'companyName' => $checkIsGroup->CompanyName,
                        'decimalPlaceLocal' => !empty($localCurrency) ? $localCurrency->DecimalPlaces : 2,
                        'decimalPlaceRpt' => !empty($rptCurrency) ? $rptCurrency->DecimalPlaces : 2,
                        'localAmountTotal' => $localAmountTotal,
                        'rptAmountTotal' => $rptAmountTotal);
                }
                break;
            case 'CR': //Customer Revenue
                $reportTypeID = $request->reportTypeID;

                if ($reportTypeID == 'RMS') { //customer ledger template 1

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerRevenueMonthlySummary($request);
                    $currency = $request->currencyID;
                    $currencyId = 2;

                    if ($currency == 2) {
                        $decimalPlaceCollect = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                        $decimalPlaceUnique = array_unique($decimalPlaceCollect);
                    } else {
                        $decimalPlaceCollect = collect($output)->pluck('documentRptCurrencyID')->toArray();
                        $decimalPlaceUnique = array_unique($decimalPlaceCollect);
                    }

                    if (!empty($decimalPlaceUnique)) {
                        $currencyId = $decimalPlaceUnique[0];
                    }


                    $requestCurrency = CurrencyMaster::where('currencyID', $currencyId)->first();

                    if(!empty($requestCurrency)) {
                        $decimalPlace = $requestCurrency->DecimalPlaces;
                    }else{
                        $decimalPlace =  2;
                    }
                    $total = array();

                    $total['Jan'] = array_sum(collect($output)->pluck('Jan')->toArray());
                    $total['Feb'] = array_sum(collect($output)->pluck('Feb')->toArray());
                    $total['March'] = array_sum(collect($output)->pluck('March')->toArray());
                    $total['April'] = array_sum(collect($output)->pluck('April')->toArray());
                    $total['May'] = array_sum(collect($output)->pluck('May')->toArray());
                    $total['June'] = array_sum(collect($output)->pluck('June')->toArray());
                    $total['July'] = array_sum(collect($output)->pluck('July')->toArray());
                    $total['Aug'] = array_sum(collect($output)->pluck('Aug')->toArray());
                    $total['Sept'] = array_sum(collect($output)->pluck('Sept')->toArray());
                    $total['Oct'] = array_sum(collect($output)->pluck('Oct')->toArray());
                    $total['Nov'] = array_sum(collect($output)->pluck('Nov')->toArray());
                    $total['Dece'] = array_sum(collect($output)->pluck('Dece')->toArray());
                    $total['Total'] = array_sum(collect($output)->pluck('Total')->toArray());


                    $outputArr = array();
                    foreach ($output as $val) {
                        $outputArr[$val->CompanyName][] = $val;
                    }
                    return array('reportData' => $outputArr,
                        'companyName' => $checkIsGroup->CompanyName,
                        'decimalPlace' => $decimalPlace,
                        'total' => $total,
                        'currency' => $requestCurrency->CurrencyCode
                    );
                } else {

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getRevenueByCustomer($request);


                    $localAmount = collect($output)->pluck('localAmount')->toArray();
                    $localAmountTotal = array_sum($localAmount);

                    $rptAmount = collect($output)->pluck('RptAmount')->toArray();
                    $rptAmountTotal = array_sum($rptAmount);

                    $decimalPlaceLocal = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                    $decimalPlaceL = array_unique($decimalPlaceLocal);

                    $decimalPlaceRpt = collect($output)->pluck('documentRptCurrencyID')->toArray();
                    $decimalPlaceR = array_unique($decimalPlaceRpt);

                    $localCurrencyId = 2;
                    $rptCurrencyId = 2;

                    if (!empty($decimalPlaceL)) {
                        $localCurrencyId = $decimalPlaceL[0];
                    }

                    if (!empty($decimalPlaceR)) {
                        $rptCurrencyId = $decimalPlaceR[0];
                    }


                    $localCurrency = CurrencyMaster::where('currencyID', $localCurrencyId)->first();
                    $rptCurrency = CurrencyMaster::where('currencyID', $rptCurrencyId)->first();

                    $outputArr = array();
                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->CustomerName][] = $val;
                        }
                    }

                    return array('reportData' => $outputArr,
                        'companyName' => $checkIsGroup->CompanyName,
                        'decimalPlaceLocal' => !empty($localCurrency) ? $localCurrency->DecimalPlaces : 2,
                        'decimalPlaceRpt' => !empty($rptCurrency) ? $rptCurrency->DecimalPlaces : 2,
                        'localAmountTotal' => $localAmountTotal,
                        'rptAmountTotal' => $rptAmountTotal
                    );

                }
                break;
            case 'CSR': //Customer Sales Register
                $input = $request->all();
                if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
                    $sort = 'asc';
                } else {
                    $sort = 'desc';
                }

                $search = $request->input('search.value');

                $convertedRequest = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($convertedRequest->companySystemID);
                $output = $this->getCustomerSalesRegisterQRY($convertedRequest, $search);

                $outputArr = array();
                $invoiceAmount = collect($output)->pluck('invoiceAmount')->toArray();
                $invoiceAmount = array_sum($invoiceAmount);

                $paidAmount = collect($output)->pluck('receiptAmount')->toArray();
                $paidAmount = array_sum($paidAmount);

                $balanceAmount = collect($output)->pluck('balanceAmount')->toArray();
                $balanceAmount = array_sum($balanceAmount);

                $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                $decimalPlace = array_unique($decimalPlace);

                $request->request->remove('order');
                $data['order'] = [];
                $data['search']['value'] = '';
                $request->merge($data);
                $request->request->remove('search.value');

                return \DataTables::of($output)
                        ->order(function ($query) use ($input) {
                            if (request()->has('order')) {
                                if ($input['order'][0]['column'] == 0) {
                                    // $query->orderBy('quiz_usermaster.id', $input['order'][0]['dir']);
                                }
                            }
                        })
                        ->addIndexColumn()
                        ->with('orderCondition', $sort)
                        ->with('companyName', $checkIsGroup->CompanyName)
                        ->with('balanceAmount', $balanceAmount)
                        ->with('paidAmount', $paidAmount)
                        ->with('invoiceAmount', $invoiceAmount)
                        ->with('currencyDecimalPlace', !empty($decimalPlace) ? $decimalPlace[0] : 2)
                        ->make(true);
                break;
            case 'CRCR': //Customer Summary Report
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($request->companySystemID);
                $outputRevenue = $this->getCustomerSummaryRevenueQRY($request);
                $outputCollection = $this->getCustomerSummaryCollectionQRY($request);
                //$outputOutstanding = $this->getCustomerSummaryOutstandingQRY($request);
                $outputOutstanding = array(); //$this->getCustomerSummaryOutstandingUpdatedQRY($request);
                $outputServiceLine = $this->getCustomerSummaryRevenueServiceLineBaseQRY($request);

                $decimalPlaceCollect = collect($outputRevenue)->pluck('documentRptCurrencyID')->toArray();
                $decimalPlaceUnique = array_unique($decimalPlaceCollect);

                $selectedCurrency = $request->currencyID;
                if ($selectedCurrency == 2) {
                    $currencyId = $checkIsGroup->localCurrencyID;
                } elseif ($selectedCurrency == 3) {
                    $currencyId = $checkIsGroup->reportingCurrency;
                } else {
                    $currencyId = $checkIsGroup->localCurrencyID;
                }

                $requestCurrency = CurrencyMaster::where('currencyID', $currencyId)->first();

                $decimalPlace = !empty($requestCurrency) ? $requestCurrency->DecimalPlaces : 2;

                $revenueTotal = array();
                $collectionTotal = array();
                $outstandingTotal = array();
                $serviceLineTotal = array();

                //revenue total calculation
                $revenueTotal['Jan'] = array_sum(collect($outputRevenue)->pluck('Jan')->toArray());
                $revenueTotal['Feb'] = array_sum(collect($outputRevenue)->pluck('Feb')->toArray());
                $revenueTotal['March'] = array_sum(collect($outputRevenue)->pluck('March')->toArray());
                $revenueTotal['April'] = array_sum(collect($outputRevenue)->pluck('April')->toArray());
                $revenueTotal['May'] = array_sum(collect($outputRevenue)->pluck('May')->toArray());
                $revenueTotal['June'] = array_sum(collect($outputRevenue)->pluck('June')->toArray());
                $revenueTotal['July'] = array_sum(collect($outputRevenue)->pluck('July')->toArray());
                $revenueTotal['Aug'] = array_sum(collect($outputRevenue)->pluck('Aug')->toArray());
                $revenueTotal['Sept'] = array_sum(collect($outputRevenue)->pluck('Sept')->toArray());
                $revenueTotal['Oct'] = array_sum(collect($outputRevenue)->pluck('Oct')->toArray());
                $revenueTotal['Nov'] = array_sum(collect($outputRevenue)->pluck('Nov')->toArray());
                $revenueTotal['Dece'] = array_sum(collect($outputRevenue)->pluck('Dece')->toArray());
                $revenueTotal['Total'] = array_sum(collect($outputRevenue)->pluck('Total')->toArray());

                //collection total calculation
                $collectionTotal['Jan'] = array_sum(collect($outputCollection)->pluck('Jan')->toArray());
                $collectionTotal['Feb'] = array_sum(collect($outputCollection)->pluck('Feb')->toArray());
                $collectionTotal['March'] = array_sum(collect($outputCollection)->pluck('March')->toArray());
                $collectionTotal['April'] = array_sum(collect($outputCollection)->pluck('April')->toArray());
                $collectionTotal['May'] = array_sum(collect($outputCollection)->pluck('May')->toArray());
                $collectionTotal['June'] = array_sum(collect($outputCollection)->pluck('June')->toArray());
                $collectionTotal['July'] = array_sum(collect($outputCollection)->pluck('July')->toArray());
                $collectionTotal['Aug'] = array_sum(collect($outputCollection)->pluck('Aug')->toArray());
                $collectionTotal['Sept'] = array_sum(collect($outputCollection)->pluck('Sept')->toArray());
                $collectionTotal['Oct'] = array_sum(collect($outputCollection)->pluck('Oct')->toArray());
                $collectionTotal['Nov'] = array_sum(collect($outputCollection)->pluck('Nov')->toArray());
                $collectionTotal['Dece'] = array_sum(collect($outputCollection)->pluck('Dece')->toArray());
                $collectionTotal['Total'] = array_sum(collect($outputCollection)->pluck('Total')->toArray());

                //outstanding total calculation
                $outstandingTotal['Jan'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountJan')->toArray());
                $outstandingTotal['Feb'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountFeb')->toArray());
                $outstandingTotal['March'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountMar')->toArray());
                $outstandingTotal['April'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountApr')->toArray());
                $outstandingTotal['May'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountMay')->toArray());
                $outstandingTotal['June'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountJun')->toArray());
                $outstandingTotal['July'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountJul')->toArray());
                $outstandingTotal['Aug'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountAug')->toArray());
                $outstandingTotal['Sept'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountSep')->toArray());
                $outstandingTotal['Oct'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountOct')->toArray());
                $outstandingTotal['Nov'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountNov')->toArray());
                $outstandingTotal['Dece'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountDec')->toArray());
                $outstandingTotal['Total'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountTot')->toArray());

                //Revenue ServiceLine total calculation
                $serviceLineTotal['Jan'] = array_sum(collect($outputServiceLine)->pluck('Jan')->toArray());
                $serviceLineTotal['Feb'] = array_sum(collect($outputServiceLine)->pluck('Feb')->toArray());
                $serviceLineTotal['March'] = array_sum(collect($outputServiceLine)->pluck('March')->toArray());
                $serviceLineTotal['April'] = array_sum(collect($outputServiceLine)->pluck('April')->toArray());
                $serviceLineTotal['May'] = array_sum(collect($outputServiceLine)->pluck('May')->toArray());
                $serviceLineTotal['June'] = array_sum(collect($outputServiceLine)->pluck('June')->toArray());
                $serviceLineTotal['July'] = array_sum(collect($outputServiceLine)->pluck('July')->toArray());
                $serviceLineTotal['Aug'] = array_sum(collect($outputServiceLine)->pluck('Aug')->toArray());
                $serviceLineTotal['Sept'] = array_sum(collect($outputServiceLine)->pluck('Sept')->toArray());
                $serviceLineTotal['Oct'] = array_sum(collect($outputServiceLine)->pluck('Oct')->toArray());
                $serviceLineTotal['Nov'] = array_sum(collect($outputServiceLine)->pluck('Nov')->toArray());
                $serviceLineTotal['Dece'] = array_sum(collect($outputServiceLine)->pluck('Dece')->toArray());
                $serviceLineTotal['Total'] = array_sum(collect($outputServiceLine)->pluck('Total')->toArray());


                return array(
                    'revenueData' => $outputRevenue,
                    'outputCollection' => $outputCollection,
                    'outputOutstanding' => $outputOutstanding,
                    'outputServiceLine' => $outputServiceLine,
                    'companyName' => $checkIsGroup->CompanyName,
                    'decimalPlace' => $decimalPlace,
                    'revenueTotal' => $revenueTotal,
                    'collectionTotal' => $collectionTotal,
                    'outstandingTotal' => $outstandingTotal,
                    'serviceLineTotal' => $serviceLineTotal,
                    'currency' => $requestCurrency->CurrencyCode
                );
                break;
            case 'CNR': //Credit Note Register
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getCreditNoteRegisterQRY($request);
                return array('reportData' => $output, 'companyName' => $checkIsGroup->CompanyName);
                break;
            default:
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.report_id')]));
        }
    }

    public function exportReport(Request $request, ExportReportToExcelService $exportReportToExcelService)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'CS': //Customer Statement Report
                $reportTypeID = $request->reportTypeID;
                $data = array();
                $type = $request->type;


                $company = Company::find($request->companySystemID);
                $company_name = $company->CompanyName;

                if ($reportTypeID == 'CBS') {
                    $typ_re = 1;
                    $requestCurrency = NULL;
                    $from_date = $request->fromDate;
                    $toDate = $request->fromDate;

                    $from_date =  ((new Carbon($from_date))->format('d/m/Y'));

                    $fileName = 'Customer Balance Statement';
                    $title = 'Customer Balance Statement';
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $output = $this->getCustomerBalanceStatementQRY($request);

                    $outputArr = array();
                    $grandTotal = collect($output)->pluck('balanceAmount')->toArray();
                    $grandTotal = array_sum($grandTotal);

                    $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                    $decimalPlace = array_unique($decimalPlace);
                    $decimalPlace = !empty($decimalPlace) ? $decimalPlace[0] : 2;

                    if(empty($data))
                    {
                        $header = new CustomerBalanceStatementReport();
                        array_push($data, collect($header->getHeader())->toArray());
                    }

                    if ($output) {
                        foreach ($output as $val) {
                            $customerBalanceStatementReport = new CustomerBalanceStatementReport();
                            $customerBalanceStatementReport->setCompanyID($val->companyID);
                            $customerBalanceStatementReport->setCompanyName($val->CompanyName);
                            $customerBalanceStatementReport->setCustomerName($val->customerName);
                            $customerBalanceStatementReport->setDocumentCode($val->DocumentCode);
                            $customerBalanceStatementReport->setPostedDate($val->PostedDate);
                            $customerBalanceStatementReport->setNarration($val->DocumentNarration);
                            $customerBalanceStatementReport->setContract($val->Contract);
                            $customerBalanceStatementReport->setPoNumber($val->PONumber);
                            $customerBalanceStatementReport->setInvoiceNumber($val->invoiceNumber);
                            $customerBalanceStatementReport->setInvoiceDate(\Helper::dateFormat($val->InvoiceDate));
                            $customerBalanceStatementReport->setCurrency($val->documentCurrency);
                            $customerBalanceStatementReport->setBalanceAmount(round($val->balanceAmount, $val->balanceDecimalPlaces));
                            array_push($data, collect($customerBalanceStatementReport)->toArray());
                        }
                    }

                    $objCustomerBalanceStatementReport = new CustomerBalanceStatementReport();
                    $excelColumnFormat = $objCustomerBalanceStatementReport->getColumnFormat();
                }
                else if ($request->reportTypeID == 'CSA') {

                    $typ_re = 2;
                    $from_date = $request->fromDate;
                    $toDate = $request->toDate;
                    $requestCurrency = $request->currency;
                    $new_cu = explode(':',$requestCurrency);
                    $requestCurrency = $new_cu[1];


                    $from_date =  ((new Carbon($from_date))->format('d/m/Y'));
                    $toDate =  ((new Carbon($toDate))->format('d/m/Y'));

                    $fileName = 'Customer Statement of Account';
                    $title = 'Customer Statement of Account';
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $output = $this->getCustomerStatementAccountQRY($request);

                    if(empty($data))
                    {
                        $header = new CustomerStatementOfAccountReport();
                        array_push($data,collect($header->getHeader())->toArray());
                    }
                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $x++;
                            $body = new CustomerStatementOfAccountReport();

                            $body->setCompanyID($val->companyID);
                            $body->setCompanyName($val->CompanyName);
                            $body->setCustomerName($val->customerName);
                            $body->setDocumentCode($val->documentCode);
                            $body->setPostedDate($val->postedDate);
                            $body->setContract($val->clientContractID);
                            $body->setPoNumber($val->PONumber);
                            $body->setInvoiceDate(\Helper::dateFormat($val->invoiceDate));
                            $body->setNarration($val->documentNarration);
                            $body->setCurrency($val->documentCurrency);
                            $body->setInvoiceAmount(round($val->invoiceAmount, $val->balanceDecimalPlaces));
                            $body->setReceiptCNCode($val->ReceiptCode);
                            $body->setReceiptCNDate(\Helper::dateFormat($val->ReceiptDate));
                            $body->setReceiptAmount(round($val->receiptAmount, $val->balanceDecimalPlaces));
                            $body->setBalanceAmount(round($val->balanceAmount, $val->balanceDecimalPlaces));

                            array_push($data,collect($body)->toArray());
                        }
                    }

                    $objCustomerStatementOfAccountReport = new CustomerStatementOfAccountReport();
                    $excelColumnFormat = $objCustomerStatementOfAccountReport->getColumnFormat();
                }


                $companyCode = isset($company->CompanyID)?$company->CompanyID:'common';
                $path = 'accounts-receivable/report/customer_balance_statement/excel/';
                $exportToExcel = $exportReportToExcelService
                    ->setTitle($title)
                    ->setFileName($fileName)
                    ->setPath($path)
                    ->setCompanyCode($companyCode)
                    ->setCompanyName($company_name)
                    ->setFromDate($from_date)
                    ->setToDate($toDate)
                    ->setData($data)
                    ->setReportType(2)
                    ->setType($type)
                    ->setCurrency($requestCurrency,true)
                    ->setDateType(2)
                    ->setExcelFormat($excelColumnFormat)
                    ->setDetails()
                    ->generateExcel();

                if(!$exportToExcel['success'])
                    return $this->sendError('Unable to export excel');

                return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));


                break;
            case 'CA': //Customer Aging
                $reportTypeID = $request->reportTypeID;
                $type = $request->type;
                $from_date = $request->fromDate;
                $to_date = $request->toDate;
                $company = Company::find($request->companySystemID);
                $company_name = $company->CompanyName;
                if ($reportTypeID == 'CAD') {
                    $dataType = 2;
                    $objCustomerAgingDetailReport = new CustomerAgingDetailReport();
                    $excelColumnFormat = $objCustomerAgingDetailReport->getCloumnFormat();
                    $fileName = 'Customer Invoice Aging Report';
                    $title = 'Customer Invoice Aging Report';
                }else {
                    $dataType = 1;
                    $excelColumnFormat = [
                        'G' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'H' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'J' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'K' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'L' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    ];
                    $fileName = 'Customer Invoice Aging Summary';
                    $title = 'Customer Invoice Aging Summary';
                }
                $data = $this->getAgingReportRecordForExcel($request,$reportTypeID);
                $requestCurrency = NULL;
                $companyCode = isset($company->CompanyID)?$company->CompanyID:'common';
                $path = 'accounts-receivable/report/customer_aging/excel/';
                $exportToExcel = $exportReportToExcelService
                    ->setTitle($title)
                    ->setFileName($fileName)
                    ->setPath($path)
                    ->setCompanyCode($companyCode)
                    ->setCompanyName($company_name)
                    ->setFromDate($from_date)
                    ->setToDate($to_date)
                    ->setReportType(2)
                    ->setData($data)
                    ->setType('xls')
                    ->setDateType($dataType)
                    ->setExcelFormat($excelColumnFormat)
                    ->setCurrency($requestCurrency)
                    ->setDetails()
                    ->generateExcel();

                if(!$exportToExcel['success'])
                    return $this->sendError('Unable to export excel');

                return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));

                break;
            case 'CL': //Customer Ledger
                $reportTypeID = $request->reportTypeID;
                $type = $request->type;
                $data = array();
                if ($reportTypeID == 'CLT1') { //customer ledger template 1
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerLedgerTemplate1QRY($request);

                    $outputArr = array();
                    $invoiceAmount = collect($output)->pluck('invoiceAmount')->toArray();
                    $invoiceAmount = array_sum($invoiceAmount);

                    $paidAmount = collect($output)->pluck('paidAmount')->toArray();
                    $paidAmount = array_sum($paidAmount);

                    $balanceAmount = collect($output)->pluck('balanceAmount')->toArray();
                    $balanceAmount = array_sum($balanceAmount);

                    $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                    $decimalPlace = array_unique($decimalPlace);

                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->concatCustomerName][$val->documentCurrency][] = $val;
                        }
                    }

                    $outputData = array('reportData' => $outputArr,
                             'companyName' => $checkIsGroup->CompanyName,
                             'balanceAmount' => $balanceAmount,
                             'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 
                             'paidAmount' => $paidAmount,
                             'invoiceAmount' => $invoiceAmount,
                             'fromDate' =>  $request->fromDate,);

                    $excelColumnFormat = [
                        'C' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
                        'F' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
                        'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'J' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'k' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    ];

                    return \Excel::create('create_customer_ledger', function ($excel) use ($outputData,$excelColumnFormat) {
                        $excel->sheet('New sheet', function ($sheet) use ($outputData,$excelColumnFormat) {
                            $sheet->setColumnFormat($excelColumnFormat);
                            $sheet->loadView('export_report.customer_ledger_template1', $outputData);
                        });
                    })->download('xlsx');

                } else {
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerLedgerTemplate2QRY($request); //customer ledger template 2

                    $outputArr = array();
                    $invoiceAmount = collect($output)->pluck('invoiceAmount')->toArray();
                    $invoiceAmount = array_sum($invoiceAmount);

                    $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                    $decimalPlace = array_unique($decimalPlace);

                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->concatCustomerName][$val->documentCurrency][] = $val;
                        }
                    }
                    $outputData = array('reportData' => $outputArr,
                                    'companyName' => $checkIsGroup->CompanyName,
                                    'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2,
                                    'invoiceAmount' => $invoiceAmount,
                                    'fromDate' =>  $request->fromDate,
                                    'toDate' =>  $request->toDate);
                    $excelColumnFormat = [
                        'C' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
                        'E' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
                        'H' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    ];
                    return \Excel::create('create_customer_ledger_report', function ($excel) use ($outputData,$excelColumnFormat) {
                        $excel->sheet('New sheet', function ($sheet) use ($outputData,$excelColumnFormat) {
                            $sheet->setColumnFormat($excelColumnFormat);
                            $sheet->loadView('export_report.customer_ledger_template2', $outputData);
                        });
                    })->download('xlsx');
                }


                break;
            case 'CBSUM': //Customer Balance Summery
                $reportTypeID = $request->reportTypeID;

                $from_date = $request->fromDate;
                $to_date = $request->fromDate;
                $company = Company::find($request->companySystemID);
                $company_name = $company->CompanyName;
                $from_date =  ((new Carbon($from_date))->format('d/m/Y'));

                if ($reportTypeID == 'CBSUM') { //customer ledger template 1

                    $data = array();
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerBalanceSummery($request);

                    $localAmount = collect($output)->pluck('localAmount')->toArray();
                    $localAmountTotal = array_sum($localAmount);

                    $rptAmount = collect($output)->pluck('RptAmount')->toArray();
                    $rptAmountTotal = array_sum($rptAmount);

                    $decimalPlaceLocal = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                    $decimalPlaceL = array_unique($decimalPlaceLocal);

                    $decimalPlaceRpt = collect($output)->pluck('documentRptCurrencyID')->toArray();
                    $decimalPlaceR = array_unique($decimalPlaceRpt);

                    $localCurrencyId = 2;
                    $rptCurrencyId = 2;

                    if (!empty($decimalPlaceL)) {
                        $localCurrencyId = $decimalPlaceL[0];
                    }

                    if (!empty($decimalPlaceR)) {
                        $rptCurrencyId = $decimalPlaceR[0];
                    }


                    $localCurrency = CurrencyMaster::where('currencyID', $localCurrencyId)->first();
                    $rptCurrency = CurrencyMaster::where('currencyID', $rptCurrencyId)->first();

                    $currencyID = $request->currencyID;
                    $type = $request->type;

                    if(empty($data))
                    {
                        $customerBalanceSummaryReportHeader = new CustomerBalanceSummaryReport();
                        array_push($data,collect($customerBalanceSummaryReportHeader->getHeader())->toArray());
                    }

                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $customerBalanceSummaryReport = new CustomerBalanceSummaryReport();
                            $customerBalanceSummaryReport->setCompanyId($val->companyID);
                            $customerBalanceSummaryReport->setCompanyName($val->CompanyName);
                            $customerBalanceSummaryReport->setCustomerCode($val->CutomerCode);
                            $customerBalanceSummaryReport->setCustomerName($val->CustomerName);
                            $decimalPlace = 2;
                            if ($currencyID == '2') {
                                $decimalPlace = !empty($localCurrency) ? $localCurrency->DecimalPlaces : 2;
                                $customerBalanceSummaryReport->setCurrency($val->documentLocalCurrency);
                                $customerBalanceSummaryReport->setAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->localAmount, $decimalPlace)));
                            } else if ($currencyID == '3') {
                                $decimalPlace = !empty($rptCurrency) ? $rptCurrency->DecimalPlaces : 2;
                                $customerBalanceSummaryReport->setCurrency($val->documentRptCurrency);
                                $customerBalanceSummaryReport->setAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->RptAmount, $decimalPlace)));
                            } else {
                                $customerBalanceSummaryReport->setCurrency( $val->documentLocalCurrency);
                                $customerBalanceSummaryReport->setAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->localAmount, $decimalPlace)));
                            }

                            array_push($data,collect($customerBalanceSummaryReport)->toArray());
                        }
                    }

                    $companyCode = isset($company->CompanyID)?$company->CompanyID:'common';

                    $fileName = 'Customer Balance Summary';
                    $title = 'Customer Balance Summary';
                    $path = 'accounts-receivable/report/customer_balance_summary/excel/';
                    $requestCurrency = NULL;
                    $excelColumnFormat = [
                        'F' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    ];

                    $exportToExcel = $exportReportToExcelService
                        ->setTitle($title)
                        ->setFileName($fileName)
                        ->setPath($path)
                        ->setCompanyCode($companyCode)
                        ->setCompanyName($company_name)
                        ->setFromDate($from_date)
                        ->setToDate($to_date)
                        ->setReportType(2)
                        ->setData($data)
                        ->setType('xls')
                        ->setDateType(2)
                        ->setExcelFormat($excelColumnFormat)
                        ->setCurrency($requestCurrency)
                        ->setDetails()
                        ->generateExcel();

                    if(!$exportToExcel['success'])
                        return $this->sendError('Unable to export excel');

                    return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));
                    break;
                }
                break;
                case 'CRCR': //Customer Summary Report
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $outputRevenue = $this->getCustomerSummaryRevenueQRY($request);
                    $outputCollection = $this->getCustomerSummaryCollectionQRY($request);
                    //$outputOutstanding = $this->getCustomerSummaryOutstandingQRY($request);
                    $outputOutstanding = array(); //$this->getCustomerSummaryOutstandingUpdatedQRY($request);
                    $outputServiceLine = $this->getCustomerSummaryRevenueServiceLineBaseQRY($request);

                    $decimalPlaceCollect = collect($outputRevenue)->pluck('documentRptCurrencyID')->toArray();
                    $decimalPlaceUnique = array_unique($decimalPlaceCollect);

                    $selectedCurrency = $request->currencyID;
                    if ($selectedCurrency == 2) {
                        $currencyId = $checkIsGroup->localCurrencyID;
                    } elseif ($selectedCurrency == 3) {
                        $currencyId = $checkIsGroup->reportingCurrency;
                    } else {
                        $currencyId = $checkIsGroup->localCurrencyID;
                    }

                    $requestCurrency = CurrencyMaster::where('currencyID', $currencyId)->first();

                    $decimalPlace = !empty($requestCurrency) ? $requestCurrency->DecimalPlaces : 2;

                    $revenueTotal = array();
                    $collectionTotal = array();
                    $outstandingTotal = array();
                    $serviceLineTotal = array();

                    //revenue total calculation
                    $revenueTotal['Jan'] = array_sum(collect($outputRevenue)->pluck('Jan')->toArray());
                    $revenueTotal['Feb'] = array_sum(collect($outputRevenue)->pluck('Feb')->toArray());
                    $revenueTotal['March'] = array_sum(collect($outputRevenue)->pluck('March')->toArray());
                    $revenueTotal['April'] = array_sum(collect($outputRevenue)->pluck('April')->toArray());
                    $revenueTotal['May'] = array_sum(collect($outputRevenue)->pluck('May')->toArray());
                    $revenueTotal['June'] = array_sum(collect($outputRevenue)->pluck('June')->toArray());
                    $revenueTotal['July'] = array_sum(collect($outputRevenue)->pluck('July')->toArray());
                    $revenueTotal['Aug'] = array_sum(collect($outputRevenue)->pluck('Aug')->toArray());
                    $revenueTotal['Sept'] = array_sum(collect($outputRevenue)->pluck('Sept')->toArray());
                    $revenueTotal['Oct'] = array_sum(collect($outputRevenue)->pluck('Oct')->toArray());
                    $revenueTotal['Nov'] = array_sum(collect($outputRevenue)->pluck('Nov')->toArray());
                    $revenueTotal['Dece'] = array_sum(collect($outputRevenue)->pluck('Dece')->toArray());
                    $revenueTotal['Total'] = array_sum(collect($outputRevenue)->pluck('Total')->toArray());

                    //collection total calculation
                    $collectionTotal['Jan'] = array_sum(collect($outputCollection)->pluck('Jan')->toArray());
                    $collectionTotal['Feb'] = array_sum(collect($outputCollection)->pluck('Feb')->toArray());
                    $collectionTotal['March'] = array_sum(collect($outputCollection)->pluck('March')->toArray());
                    $collectionTotal['April'] = array_sum(collect($outputCollection)->pluck('April')->toArray());
                    $collectionTotal['May'] = array_sum(collect($outputCollection)->pluck('May')->toArray());
                    $collectionTotal['June'] = array_sum(collect($outputCollection)->pluck('June')->toArray());
                    $collectionTotal['July'] = array_sum(collect($outputCollection)->pluck('July')->toArray());
                    $collectionTotal['Aug'] = array_sum(collect($outputCollection)->pluck('Aug')->toArray());
                    $collectionTotal['Sept'] = array_sum(collect($outputCollection)->pluck('Sept')->toArray());
                    $collectionTotal['Oct'] = array_sum(collect($outputCollection)->pluck('Oct')->toArray());
                    $collectionTotal['Nov'] = array_sum(collect($outputCollection)->pluck('Nov')->toArray());
                    $collectionTotal['Dece'] = array_sum(collect($outputCollection)->pluck('Dece')->toArray());
                    $collectionTotal['Total'] = array_sum(collect($outputCollection)->pluck('Total')->toArray());

                    //outstanding total calculation
                    $outstandingTotal['Jan'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountJan')->toArray());
                    $outstandingTotal['Feb'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountFeb')->toArray());
                    $outstandingTotal['March'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountMar')->toArray());
                    $outstandingTotal['April'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountApr')->toArray());
                    $outstandingTotal['May'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountMay')->toArray());
                    $outstandingTotal['June'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountJun')->toArray());
                    $outstandingTotal['July'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountJul')->toArray());
                    $outstandingTotal['Aug'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountAug')->toArray());
                    $outstandingTotal['Sept'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountSep')->toArray());
                    $outstandingTotal['Oct'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountOct')->toArray());
                    $outstandingTotal['Nov'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountNov')->toArray());
                    $outstandingTotal['Dece'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountDec')->toArray());
                    $outstandingTotal['Total'] = array_sum(collect($outputOutstanding)->pluck('balanceAmountTot')->toArray());

                    //Revenue ServiceLine total calculation
                    $serviceLineTotal['Jan'] = array_sum(collect($outputServiceLine)->pluck('Jan')->toArray());
                    $serviceLineTotal['Feb'] = array_sum(collect($outputServiceLine)->pluck('Feb')->toArray());
                    $serviceLineTotal['March'] = array_sum(collect($outputServiceLine)->pluck('March')->toArray());
                    $serviceLineTotal['April'] = array_sum(collect($outputServiceLine)->pluck('April')->toArray());
                    $serviceLineTotal['May'] = array_sum(collect($outputServiceLine)->pluck('May')->toArray());
                    $serviceLineTotal['June'] = array_sum(collect($outputServiceLine)->pluck('June')->toArray());
                    $serviceLineTotal['July'] = array_sum(collect($outputServiceLine)->pluck('July')->toArray());
                    $serviceLineTotal['Aug'] = array_sum(collect($outputServiceLine)->pluck('Aug')->toArray());
                    $serviceLineTotal['Sept'] = array_sum(collect($outputServiceLine)->pluck('Sept')->toArray());
                    $serviceLineTotal['Oct'] = array_sum(collect($outputServiceLine)->pluck('Oct')->toArray());
                    $serviceLineTotal['Nov'] = array_sum(collect($outputServiceLine)->pluck('Nov')->toArray());
                    $serviceLineTotal['Dece'] = array_sum(collect($outputServiceLine)->pluck('Dece')->toArray());
                    $serviceLineTotal['Total'] = array_sum(collect($outputServiceLine)->pluck('Total')->toArray());
                    $companyCode = isset($checkIsGroup->CompanyID) ? $checkIsGroup->CompanyID: null;


                    $reportData = array(
                        'fromDate' =>$request->fromDate,
                        'year' =>date('Y', strtotime($request->fromDate)),
                        'revenueData' => $outputRevenue,
                        'outputCollection' => $outputCollection,
                        'outputOutstanding' => $outputOutstanding,
                        'outputServiceLine' => $outputServiceLine,
                        'companyName' => $checkIsGroup->CompanyName,
                        'decimalPlace' => $decimalPlace,
                        'revenueTotal' => $revenueTotal,
                        'collectionTotal' => $collectionTotal,
                        'outstandingTotal' => $outstandingTotal,
                        'serviceLineTotal' => $serviceLineTotal,
                        'currency' => $requestCurrency->CurrencyCode,
                        'companyCode'=>$companyCode
                    );
                    $templateName = "export_report.customer-summary-report";
                    $fileName = 'customer_summary_report';
                    $path = 'accounts-receivable/report/customer-summary/excel/';
                    $file_type = $request->type;
                    $basePath = CreateExcel::loadView($reportData,$file_type,$fileName,$path,$templateName);

                    if($basePath == '')
                    {
                        return $this->sendError('Unable to export excel');
                    }
                    else
                    {
                        return $this->sendResponse($basePath, trans('custom.success_export'));
                    }
                    break;

            case 'CSR': //Customer Sales Register
                $type = $request->type;


                $from_date = $request->fromDate;
                $to_date = $request->toDate;
                $company = Company::find($request->companySystemID);
                $company_name = $company->CompanyName;
                $from_date =  ((new Carbon($from_date))->format('d/m/Y'));
                $to_date =  ((new Carbon($to_date))->format('d/m/Y'));

                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $output = $this->getCustomerSalesRegisterQRY($request);
                $data = array();
                if ($output) {
                    $x = 0;
                    foreach ($output as $val) {
                        $data[$x]['Invoice Type'] = $val->invoiceType;
                        $data[$x]['Company ID'] = $val->companyID;
                        $data[$x]['Company Name'] = $val->CompanyName;
                        $data[$x]['Customer Code'] = $val->CutomerCode;
                        $data[$x]['Customer Name'] = $val->CustomerName;
                        $data[$x]['Document Code'] = $val->documentCode;
                        $data[$x]['Posted Date'] = \Helper::dateFormat($val->PostedDate);
                        $data[$x]['Service Line'] = $val->serviceLineCode;
                        $data[$x]['Contract'] = $val->clientContractID;
                        $data[$x]['PO Number'] = $val->PONumber;
                        $data[$x]['SE No'] = $val->wanNO;
                        $data[$x]['Rig No'] = $val->rigNo;
                        $data[$x]['Service Period'] = $val->servicePeriod;
                        $data[$x]['Start Date'] = \Helper::dateFormat($val->serviceStartDate);
                        $data[$x]['End Date'] = \Helper::dateFormat($val->serviceEndDate);
                        $data[$x]['Invoice Number'] = $val->invoiceNumber;
                        $data[$x]['Invoice Date'] = \Helper::dateFormat($val->invoiceDate);
                        $data[$x]['Narration'] = $val->documentNarration;
                        $data[$x]['Currency'] = $val->documentCurrency;
                        $data[$x]['Invoice Amount'] = $val->invoiceAmount;
                        $data[$x]['Receipt Code'] = $val->ReceiptCode;
                        $data[$x]['Receipt Date'] = \Helper::dateFormat($val->ReceiptDate);
                        $data[$x]['Amount Matched'] = $val->receiptAmount;
                        $data[$x]['Balance'] = $val->balanceAmount;
                        $x++;
                    }
                }

                $fileName = 'Sales Register';
                $title = 'Sales Register';
                $path = 'accounts-receivable/report/customer_sales_register/excel/';
                $requestCurrency = NULL;
                $companyCode = isset($company->CompanyID)?$company->CompanyID:'common';

                $detail_array = array('type' => 1,'from_date'=>$from_date,'to_date'=>$to_date,'company_name'=>$company_name,'cur'=>$requestCurrency,'company_code'=>$companyCode,'title'=>$title);
                $basePath = CreateExcel::process($data,$type,$fileName,$path,$detail_array);

                if($basePath == '')
                {
                     return $this->sendError('Unable to export excel');
                }
                else
                {
                     return $this->sendResponse($basePath, trans('custom.success_export'));
                }
                break;

            case 'CC': //Customer Collection
                $reportTypeID = $request->reportTypeID;
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $requestCurrency = $request->currency;
                $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                if ($companyCurrency) {
                    if ($request->currencyID == 2) {
                        $selectedCurrency = $companyCurrency->localcurrency->CurrencyCode;
                    } else if ($request->currencyID == 3) {
                        $selectedCurrency = $companyCurrency->reportingcurrency->CurrencyCode;
                    }
                }
                $data = [];
                if ($reportTypeID == 'CCR') { //customer aging detail
                    $fileName = 'Collection Report';
                    $title = 'Collection Report';
                    $from_date = $request->fromDate;
                    $to_date = $request->toDate;
                    $company = Company::find($request->companySystemID);
                    $company_name = $company->CompanyName;
                    $from_date =  ((new Carbon($from_date))->format('d/m/Y'));
                    $to_date =  ((new Carbon($to_date))->format('d/m/Y'));
                    $typ_re = 1;
                    if ($request->excelForm == 'bankReport') {

                        $output = $this->getCustomerCollectionBRVExcelQRY($request);

                        if ($output) {
                            $x = 0;
                            foreach ($output as $val) {
                                $data[$x]['Company ID'] = $val->companyID;
                                $data[$x]['Company Name'] = $val->CompanyName;
                                $data[$x]['Customer Code'] = $val->CutomerCode;
                                $data[$x]['Customer Short Code'] = $val->customerShortCode;
                                $data[$x]['Customer Name'] = $val->CustomerName;
                                $data[$x]['Document Code'] = $val->documentCode;
                                $data[$x]['Document Date'] = \Helper::dateFormat($val->documentDate);
                                $data[$x]['Bank Name'] = $val->bankName;
                                $data[$x]['Account No'] = $val->AccountNo;
                                $data[$x]['Bank Currency'] = $val->bankCurrencyCode;
                                $data[$x]['Document Narration'] = $val->documentNarration;
                                $data[$x]['Currency Code'] = $selectedCurrency;
                                $data[$x]['BRV Document Amount'] = $val->BRVDocumentAmount;
                                $x++;
                            }
                        }

                    } else if ($request->excelForm == 'creditNoteReport') {

                        $output = $this->getCustomerCollectionCNExcelQRY($request);

                        if ($output) {
                            $x = 0;
                            foreach ($output as $val) {
                                $data[$x]['Company ID'] = $val->companyID;
                                $data[$x]['Company Name'] = $val->CompanyName;
                                $data[$x]['Customer Code'] = $val->CutomerCode;
                                $data[$x]['Customer Short Code'] = $val->customerShortCode;
                                $data[$x]['Customer Name'] = $val->CustomerName;
                                $data[$x]['Document Code'] = $val->documentCode;
                                $data[$x]['Document Date'] = \Helper::dateFormat($val->documentDate);
                                $data[$x]['Document Narration'] = $val->documentNarration;
                                $data[$x]['Currency Code'] = $selectedCurrency;
                                $data[$x]['CN Document Amount'] = $val->CNDocumentAmount;
                                $x++;
                            }
                        }
                    }

                } else {
                    $output = $this->getCustomerCollectionMonthlyQRY($request);
                    $year = $request->year;
                    $fileName = 'Collection Report By Year -'.$year;
                    $title = 'Collection Report By Year -'.$year;
                    $from_date = \App\helper\Helper::dateFormat($request->fromDate);
                    $to_date = $request->fromDate;
                    $company = Company::find($request->companySystemID);
                    $company_name = $company->CompanyName;
                    $typ_re = 2;
                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Company ID'] = $val->companyCode;
                            $data[$x]['Company Name'] = $val->CompanyName;
                            $data[$x]['Customer Name'] = $val->CustomerName;
                            $data[$x]['Jan'] = $val->Jan;
                            $data[$x]['Feb'] = $val->Feb;
                            $data[$x]['March'] = $val->March;
                            $data[$x]['April'] = $val->April;
                            $data[$x]['May'] = $val->May;
                            $data[$x]['Jun'] = $val->June;
                            $data[$x]['July'] = $val->July;
                            $data[$x]['Aug'] = $val->Aug;
                            $data[$x]['Sept'] = $val->Sept;
                            $data[$x]['Oct'] = $val->Oct;
                            $data[$x]['Nov'] = $val->Nov;
                            $data[$x]['Dec'] = $val->Dece;
                            $data[$x]['Tot'] = ($val->Jan + $val->Feb + $val->March + $val->April + $val->May + $val->June + $val->July + $val->Aug + $val->Sept + $val->Oct + $val->Nov + $val->Dece);
                            $x++;
                        }
                    }
                }

                $companyCode = isset($company->CompanyID)?$company->CompanyID:'common';

                $path = 'accounts-receivable/report/customer_collection/excel/';
                if($typ_re == 1)
                {
                    $detail_array = array('type' => 4,'from_date'=>$from_date,'to_date'=>$to_date,'company_name'=>$company_name,'company_code'=>$companyCode,'cur'=>$requestCurrency,'title'=>$title);

                    $basePath = CreateExcel::process($data,$type,$fileName,$path,$detail_array);
                }
                else
                {
                    $detail_array = array('type' => 5,'from_date'=>$from_date,'to_date'=>$to_date,'company_name'=>$company_name, 'company_code'=>$companyCode,'cur'=>$requestCurrency,'title'=>$title);

                    $basePath = CreateExcel::process($data,$type,$fileName,$path,$detail_array);
                }


                if($basePath == '')
                {
                     return $this->sendError('Unable to export excel');
                }
                else
                {
                     return $this->sendResponse($basePath, trans('custom.success_export'));
                }
                break;
            case 'CR': //Customer Revenue
                $reportTypeID = $request->reportTypeID;

                if ($reportTypeID == 'RC') {


                    $fileName = 'Revenue Detail';
                    $title = 'Revenue Detail';
                    $company = Company::find($request->companySystemID);
                    $company_name = $company->CompanyName;

                    $from_date = $request->fromDate;
                    $toDate = $request->toDate;

                    $from_date =  ((new Carbon($from_date))->format('d/m/Y'));
                    $toDate =  ((new Carbon($toDate))->format('d/m/Y'));

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getRevenueByCustomer($request);

                    $decimalPlaceLocal = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                    $decimalPlaceL = array_unique($decimalPlaceLocal);

                    $decimalPlaceRpt = collect($output)->pluck('documentRptCurrencyID')->toArray();
                    $decimalPlaceR = array_unique($decimalPlaceRpt);

                    $localCurrencyId = 2;
                    $rptCurrencyId = 2;

                    if (!empty($decimalPlaceL)) {
                        $localCurrencyId = $decimalPlaceL[0];
                    }

                    if (!empty($decimalPlaceR)) {
                        $rptCurrencyId = $decimalPlaceR[0];
                    }

                    $localCurrency = CurrencyMaster::where('currencyID', $localCurrencyId)->first();
                    $rptCurrency = CurrencyMaster::where('currencyID', $rptCurrencyId)->first();

                    $currencyID = $request->currencyID;
                    $type = $request->type;

                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Company Name'] = $val->CompanyName;
                            $data[$x]['Customer Code'] = $val->CutomerCode;
                            $data[$x]['Customer Name'] = $val->CustomerName;
                            $data[$x]['Document Code'] = $val->documentCode;
                            $data[$x]['Segment'] = $val->serviceLineCode;
                            // $data[$x]['Contract No'] = $val->ContractNumber;
                            // $data[$x]['Contract Description'] = $val->contractDescription;
                            // $data[$x]['Contract/PO'] = $val->CONTRACT_PO;
                            // $data[$x]['Contract End Date'] = \Helper::dateFormat($val->ContEndDate);
                            $data[$x]['GL Code'] = $val->glCode;
                            $data[$x]['GL Desc'] = $val->AccountDescription;
                            $data[$x]['Document Date'] = \Helper::dateFormat($val->documentDate);
                            $data[$x]['Posting Month'] = Carbon::parse($val->documentDate)->shortEnglishMonth;
                            $data[$x]['Posting Year'] = $val->PostingYear;
                            $data[$x]['Narration'] = $val->documentNarration;

                            $decimalPlace = 0;
                            if ($currencyID == '2') {
                                $decimalPlace = !empty($val->documentLocalDecimalPlaces) ? $val->documentLocalDecimalPlaces : 2;
                                $data[$x]['Currency'] = $val->documentLocalCurrency;
                                $data[$x]['Amount'] = round($val->localAmount, $decimalPlace);
                            } else if ($currencyID == '3') {
                                $decimalPlace = !empty($val->documentRptDecimalPlaces) ? $val->documentRptDecimalPlaces : 2;
                                $data[$x]['Currency'] = $val->documentRptCurrency;
                                $data[$x]['Amount'] = round($val->RptAmount, $decimalPlace);
                            } else {
                                $data[$x]['Currency'] = $val->documentLocalCurrency;
                                $data[$x]['Amount'] = $val->localAmount;
                                $data[$x]['Amount'] = round($val->localAmount, $decimalPlace);
                            }
                            $x++;
                        }
                    } else {
                        $data = array();
                    }
                    $companyCode = isset($company->CompanyID)?$company->CompanyID:'common';
                    $requestCurrency = NULL;
                    $fileName = 'Revenue Detail';
                    $path = 'accounts-receivable/report/revenue_by_customer/excel/';
                    $detail_array = array('type' => 1,'from_date'=>$from_date,'to_date'=>$toDate,'company_name'=>$company_name,'company_code'=>$companyCode,'cur'=>$requestCurrency,'title'=>$title);
                    $basePath = CreateExcel::process($data,$type,$fileName,$path,$detail_array);

                    if($basePath == '')
                    {
                         return $this->sendError('Unable to export excel');
                    }
                    else
                    {
                         return $this->sendResponse($basePath, trans('custom.success_export'));
                    }

                } elseif ($reportTypeID == 'RMS') {

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerRevenueMonthlySummary($request);
                    $type = $request->type;
                    $year = $request->year;

                    $fileName = 'Revenue Report '.$year;
                    $title = 'Revenue Report '.$year;
                    $company = Company::find($request->companySystemID);
                    $company_name = $company->CompanyName;

                    $from_date = $request->fromDate;

                    $from_date =  ((new Carbon($from_date))->format('d/m/Y'));


                    $currency = $request->currencyID;
                    $currencyId = 2;
                    $cure = $request->currency;

                    if ($currency == 2) {
                        $decimalPlaceCollect = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                        $decimalPlaceUnique = array_unique($decimalPlaceCollect);
                    } else {
                        $decimalPlaceCollect = collect($output)->pluck('documentRptCurrencyID')->toArray();
                        $decimalPlaceUnique = array_unique($decimalPlaceCollect);
                    }

                    if (!empty($decimalPlaceUnique)) {
                        $currencyId = $decimalPlaceUnique[0];
                    }

                    $requestCurrency = CurrencyMaster::where('currencyID', $currencyId)->first();

                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Company Name'] = $val->CompanyName;
                            $data[$x]['Customer Name'] = $val->CustomerName;
                            $data[$x]['Currency'] = $requestCurrency->CurrencyCode;
                            $data[$x]['Jan'] = $val->Jan;
                            $data[$x]['Feb'] = $val->Feb;
                            $data[$x]['March'] = $val->March;
                            $data[$x]['April'] = $val->April;
                            $data[$x]['May'] = $val->May;
                            $data[$x]['June'] = $val->June;
                            $data[$x]['July'] = $val->July;
                            $data[$x]['Aug'] = $val->Aug;
                            $data[$x]['Sept'] = $val->Sept;
                            $data[$x]['Oct'] = $val->Oct;
                            $data[$x]['Nov'] = $val->Nov;
                            $data[$x]['Dec'] = $val->Dece;
                            $data[$x]['Total'] = $val->Total;
                            $x++;
                        }
                    } else {
                        $data = array();
                    }

                    $companyCode = isset($company->CompanyID)?$company->CompanyID:'common';
                    $fileName = 'Revenue Report '.$year;
                    $path = 'accounts-receivable/report/revenue_by_customer/excel/';
                    $detail_array = array('type' => 5,'from_date'=>$from_date,'to_date'=>$from_date,'company_name'=>$company_name,'company_code'=>$companyCode,'cur'=>$cure,'title'=>$title);

                    $basePath = CreateExcel::process($data,$type,$fileName,$path,$detail_array);

                    if($basePath == '')
                    {
                         return $this->sendError('Unable to export excel');
                    }
                    else
                    {
                         return $this->sendResponse($basePath, trans('custom.success_export'));
                    }

                }
                break;
            case 'CNR': //Credit Note Register
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $output = $this->getCreditNoteRegisterQRY($request);
                $type = $request->type;

                $from_date = $request->fromDate;
                $toDate = $request->toDate;
                $company = Company::find($request->companySystemID);
                $company_name = $company->CompanyName;
                $from_date =  ((new Carbon($from_date))->format('d/m/Y'));
                $toDate =  ((new Carbon($toDate))->format('d/m/Y'));

                $data = array();
                if ($output) {
                    $x = 0;
                    foreach ($output as $val) {
                        $x++;
                        $matchingDocdate = '';
                        $matchingDocCode = '';
                        if ($val->custReceiptDate == null && $val->matchingDocdate == null) {
                            $matchingDocCode = $val->matchingDocCode;
                        } else if ($val->custReceiptDate != null && $val->matchingDocdate == null) {
                            $matchingDocCode = $val->custReceiptCode;
                        } else if ($val->custReceiptDate == null && $val->matchingDocdate != null) {
                            $matchingDocCode = $val->matchingDocCode;
                        } else if ($val->custReceiptDate > $val->matchingDocdate) {
                            $matchingDocCode = $val->custReceiptCode;
                        } else if ($val->matchingDocdate > $val->custReceiptDate) {
                            $matchingDocCode = $val->matchingDocCode;
                        }
                        if ($val->custReceiptDate == null && $val->matchingDocdate == null) {
                            $matchingDocdate = $val->matchingDocdate;
                        } else if ($val->custReceiptDate != null && $val->matchingDocdate == null) {
                            $matchingDocdate = $val->custReceiptDate;
                        } else if ($val->custReceiptDate == null && $val->matchingDocdate != null) {
                            $matchingDocdate = $val->matchingDocdate;
                        } else if ($val->custReceiptDate > $val->matchingDocdate) {
                            $matchingDocdate = $val->custReceiptDate;
                        } else if ($val->matchingDocdate > $val->custReceiptDate) {
                            $matchingDocdate = $val->matchingDocdate;
                        }
                        $data[$x]['Company ID'] = $val->companyID;
                        $data[$x]['Company Name'] = $val->CompanyName;
                        $data[$x]['Customer Short Code'] = $val->CutomerCode;
                        $data[$x]['Customer Name'] = $val->CustomerName;
                        $data[$x]['Document Code'] = $val->documentCode;
                        $data[$x]['Posted Date'] = \Helper::dateFormat($val->postedDate);
                        $data[$x]['Comments'] = $val->documentNarration;
                        $data[$x]['Department'] = $val->ServiceLineDes;
                        $data[$x]['Client Contract ID'] = $val->clientContractID;
                        $data[$x]['GL Code'] = $val->AccountCode;
                        $data[$x]['GL Description'] = $val->AccountDescription;
                        $data[$x]['Currency'] = $val->CurrencyCode;
                        $data[$x]['Credit Note Total Amount'] = round($val->documentRptAmount, $val->DecimalPlaces);
                        $data[$x]['Receipt Matching Code'] = $matchingDocCode;
                        $data[$x]['Receipt Matching Date'] = $matchingDocdate;
                        $data[$x]['Receipt Amount'] = round(($val->detailSum + $val->custReceiptSum), $val->DecimalPlaces);
                    }
                }


                $fileName = 'Credit Note Register';
                $title = 'Credit Note Register';
                $path = 'accounts-receivable/report/credit_note_register/excel/';
                $requestCurrency = NULL;
                $companyCode = isset($company->CompanyID)?$company->CompanyID:'common';
                $detail_array = array('type' => 1,'from_date'=>$from_date,'to_date'=>$toDate,'company_name'=>$company_name,'company_code'=>$companyCode,'cur'=>$requestCurrency,'title'=>$title);

                $basePath = CreateExcel::process($data,$type,$fileName,$path,$detail_array);

                if($basePath == '')
                {
                     return $this->sendError('Unable to export excel');
                }
                else
                {
                     return $this->sendResponse($basePath, trans('custom.success_export'));
                }
                break;
            case 'INVTRACK': //Customer Invoice Tracker
                $type = 'xls';
                $input = $request->all();

                $validator = \Validator::make($input, [
                    'customerID' => 'required',
                    'contractID' => 'required',
                    'yearID' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                $output = $this->getInvoiceTrackerQRY($request);

                if ($output) {
                    $x = 0;
                    foreach ($output as $val) {
                        $data[$x]['Rig'] = $val->RigDescription." ".$val->regNo;
                        $data[$x]['Year'] = $val->myRentYear;
                        $data[$x]['Month'] = $val->myRentMonth;
                        $data[$x]['Start Date'] = \Helper::dateFormat($val->rentalStartDate);
                        $data[$x]['End Date'] = \Helper::dateFormat($val->rentalEndDate);
                        $data[$x]['Rental'] = $val->billingCode;
                        $data[$x]['Amount'] = $val->performaValue;
                        $data[$x]['Proforma'] = $val->PerformaCode;
                        $data[$x]['Pro Date'] = \Helper::dateFormat($val->performaOpConfirmedDate);
                        $data[$x]['Client Status'] = $val->description;
                        $data[$x]['Client App Date'] = \Helper::dateFormat($val->myClientapprovedDate);
                        $data[$x]['Batch No'] = $val->batchNo;
                        $data[$x]['Submitted Date'] = \Helper::dateFormat($val->mySubmittedDate);
                        $data[$x]['Invoice No'] = $val->bookingInvCode;
                        $data[$x]['Invoice App Date'] = \Helper::dateFormat($val->myApprovedDate);
                        $data[$x]['Status'] = $val->status;
                        $data[$x]['Receipt Code'] = $val->ReceiptCode;
                        $data[$x]['Receipt Date'] = \Helper::dateFormat($val->ReceiptDate);
                        $data[$x]['Receipt Amount'] = $val->ReceiptAmount;
                        $x++;
                    }
                } else {
                    $data = [];
                }
                $companyMaster = Company::find(isset($request->companySystemID)?$request->companySystemID: null);
                $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
                $detail_array = array(
                    'company_code'=>$companyCode,
                );
                $fileName = 'invoice_tracker_';
                $path = 'accounts-receivable/report/invoice_tracker_/excel/';
                $basePath = CreateExcel::process($data,$type,$fileName,$path,$detail_array);

                if($basePath == '')
                {
                     return $this->sendError('Unable to export excel');
                }
                else
                {
                     return $this->sendResponse($basePath, trans('custom.success_export'));
                }
                break;
            default:
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.report_id')]));
        }
    }

    private function getAgingReportRecordForExcel($request,$reportTypeID) : Array{
        $data = array();
        if ($reportTypeID == 'CAD') { //customer aging detail
            $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
            $output = $this->getCustomerAgingDetailQRY($request);

            if ($output['data'] && $output['aging']) {
                $x = 0;
                if(empty($data)) {
                    $ObjCustomerAgingDetailReportHeader =  new CustomerAgingDetailReport();
                    array_push($data,collect($ObjCustomerAgingDetailReportHeader->getHeader($output['aging']))->toArray());
                }

                foreach ($output['data'] as $val) {
                    $lineTotal = 0;

                    foreach ($output['aging'] as $val2) {
                        $lineTotal += $val->$val2;
                    }

                    $objCustomerAgingDetailReport = new CustomerAgingDetailReport();
                    $objCustomerAgingDetailReport->setCompanyID($val->companyID);
                    $objCustomerAgingDetailReport->setCompanyName($val->CompanyName);
                    $objCustomerAgingDetailReport->setDocumentCode($val->DocumentCode);
                    $objCustomerAgingDetailReport->setDocumentDate($val->PostedDate);
                    $objCustomerAgingDetailReport->setGlCode($val->glCode);
                    $objCustomerAgingDetailReport->setCustomerCode($val->CutomerCode);
                    $objCustomerAgingDetailReport->setCustomerName($val->customerName2);
                    $objCustomerAgingDetailReport->setCreditDays($val->creditDays);
                    $objCustomerAgingDetailReport->setDepartment($val->serviceLineName);
                    $objCustomerAgingDetailReport->setContractID($val->Contract);
                    $objCustomerAgingDetailReport->setInvoiceNumber($val->invoiceNumber);
                    $objCustomerAgingDetailReport->setPoNumber($val->PONumber);
                    $objCustomerAgingDetailReport->setInvoiceDate($val->InvoiceDate);
                    $objCustomerAgingDetailReport->setAgeDays($val->age);
                    $objCustomerAgingDetailReport->setInvoiceDueDate($val->invoiceDueDate);
                    $objCustomerAgingDetailReport->setDocumentNarration($val->DocumentNarration);
                    $objCustomerAgingDetailReport->setCurrency($val->documentCurrency);
                    $objCustomerAgingDetailReport->setInvoiceAmount($val->invoiceAmount);
                    $objCustomerAgingDetailReport->setOutStanding($lineTotal);

                    array_push($data,collect($objCustomerAgingDetailReport)->toArray());
                }

                foreach ($output['data'] as $index => $val) {

                    foreach ($output['aging'] as $val2) {
                        $data[$index + 1][$val2] = $val->$val2;
                    }


                    $data[$index + 1]['Current Outstanding'] = $val->subsequentBalanceAmount;
                    $data[$index + 1]['Subsequent Collection Amount'] = $val->subsequentAmount;
                    $data[$index + 1]['Receipt Matching/BRVNo'] = $val->brvInv;
                    $data[$index + 1]['Collection Tracker Status'] = $val->commentAndStatus;

                }
            }
        } else {
            $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
            $output = $this->getCustomerAgingSummaryQRY($request);
            if ($output['data']) {
                $x = 0;
                foreach ($output['data'] as $val) {
                    $lineTotal = 0;
                    $data[$x]['Company ID'] = $val->companyID;
                    $data[$x]['Company Name'] = $val->CompanyName;
                    $data[$x]['Credit Days'] = $val->creditDays;
                    $data[$x]['Cust. Code'] = $val->CustomerCode;
                    $data[$x]['Customer Name'] = $val->CustomerName;
                    $data[$x]['Currency'] = $val->documentCurrency;
                    foreach ($output['aging'] as $val2) {
                        $lineTotal += $val->$val2;
                    }
                    $data[$x]['Amount'] = $lineTotal;
                    foreach ($output['aging'] as $val2) {
                        $data[$x][$val2] = $val->$val2;
                    }
                    $x++;
                }
            }
        }

        return $data;
    }

    public function pdfExportReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'CS':
                return $this->customerStatementExportPdf($request);
                break;
            case 'CR':
                if ($request->reportTypeID == 'RMS') {

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerRevenueMonthlySummary($request);
                    $companyLogo = $checkIsGroup->logo_url;

                    $currency = $request->currencyID;
                    $currencyId = 2;

                    if ($currency == 2) {
                        $decimalPlaceCollect = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                        $decimalPlaceUnique = array_unique($decimalPlaceCollect);
                    } else {
                        $decimalPlaceCollect = collect($output)->pluck('documentRptCurrencyID')->toArray();
                        $decimalPlaceUnique = array_unique($decimalPlaceCollect);
                    }

                    if (!empty($decimalPlaceUnique)) {
                        $currencyId = $decimalPlaceUnique[0];
                    }


                    $requestCurrency = CurrencyMaster::where('currencyID', $currencyId)->first();

                    $decimalPlace = !empty($requestCurrency) ? $requestCurrency->DecimalPlaces : 2;

                    $total = array();

                    $total['Jan'] = array_sum(collect($output)->pluck('Jan')->toArray());
                    $total['Feb'] = array_sum(collect($output)->pluck('Feb')->toArray());
                    $total['March'] = array_sum(collect($output)->pluck('March')->toArray());
                    $total['April'] = array_sum(collect($output)->pluck('April')->toArray());
                    $total['May'] = array_sum(collect($output)->pluck('May')->toArray());
                    $total['June'] = array_sum(collect($output)->pluck('June')->toArray());
                    $total['July'] = array_sum(collect($output)->pluck('July')->toArray());
                    $total['Aug'] = array_sum(collect($output)->pluck('Aug')->toArray());
                    $total['Sept'] = array_sum(collect($output)->pluck('Sept')->toArray());
                    $total['Oct'] = array_sum(collect($output)->pluck('Oct')->toArray());
                    $total['Nov'] = array_sum(collect($output)->pluck('Nov')->toArray());
                    $total['Dece'] = array_sum(collect($output)->pluck('Dece')->toArray());
                    $total['Total'] = array_sum(collect($output)->pluck('Total')->toArray());


                    $outputArr = array();
                    foreach ($output as $val) {
                        $outputArr[$val->CompanyName][] = $val;
                    }
                    $dataArr = array('reportData' => (object)$outputArr, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'decimalPlace' => $decimalPlace, 'total' => $total, 'currency' => $requestCurrency->CurrencyCode, 'year' => $request->year, 'fromDate' => \Helper::dateFormat($request->fromDate));

                    $html = view('print.revenue_monthly_summary', $dataArr);

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($html);

                    return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();
                }
                break;
            case 'CA':
                if ($request->reportTypeID == 'CAS') {

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerAgingSummaryQRY($request);

                    $companyLogo = $checkIsGroup->logo_url;

                    $outputArr = array();
                    $grandTotalArr = array();
                    if ($output['aging']) {
                        foreach ($output['aging'] as $val) {
                            $total = collect($output['data'])->pluck($val)->toArray();
                            $grandTotalArr[$val] = array_sum($total);
                        }
                    }

                    if ($output['data']) {
                        foreach ($output['data'] as $val) {
                            $outputArr[$val->concatCompanyName][$val->documentCurrency][] = $val;
                        }
                    }

                    $decimalPlaces = 2;
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($companyCurrency) {
                        if ($request->currencyID == 2) {
                            $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                        } else if ($request->currencyID == 3) {
                            $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                        }
                    }

                    $dataArr = array('reportData' => (object)$outputArr, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'decimalPlace' => $decimalPlaces, 'grandTotal' => $grandTotalArr, 'agingRange' => $output['aging'], 'fromDate' => \Helper::dateFormat($request->fromDate));

                    $html = view('print.customer_aging_summary', $dataArr);

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($html);

                    return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();

                } elseif ($request->reportTypeID == 'CAD') {

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerAgingDetailQRY($request);

                    $companyLogo = $checkIsGroup->logo_url;

                    $outputArr = array();
                    $customerCreditDays = array();
                    $grandTotalArr = array();
                    if ($output['aging']) {
                        foreach ($output['aging'] as $val) {
                            $total = collect($output['data'])->pluck($val)->toArray();
                            $grandTotalArr[$val] = array_sum($total);
                        }
                    }

                    if ($output['data']) {
                        foreach ($output['data'] as $val) {
                            $outputArr[$val->customerName][$val->documentCurrency][] = $val;
                            $customerCreditDays[$val->customerName] = $val->creditDays;
                        }
                    }

                    $decimalPlaces = 2;
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($companyCurrency) {
                        if ($request->currencyID == 2) {
                            $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                        } else if ($request->currencyID == 3) {
                            $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                        }
                    }

                    $invoiceAmountTotal = collect($output['data'])->pluck('invoiceAmount')->toArray();
                    $invoiceAmountTotal = array_sum($invoiceAmountTotal);

                    $dataArr = array('reportData' => (object)$outputArr, 'customerCreditDays' => $customerCreditDays, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'currencyDecimalPlace' => $decimalPlaces, 'grandTotal' => $grandTotalArr, 'agingRange' => $output['aging'], 'fromDate' => \Helper::dateFormat($request->fromDate), 'invoiceAmountTotal' => $invoiceAmountTotal);

                    $html = view('print.customer_aging_detail', $dataArr);

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($html);

                    return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();
                }
                break;
            case 'CC':
                if ($request->reportTypeID == 'CCR') {

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerCollectionQRY($request);

                    $companyLogo = $checkIsGroup->logo_url;

                    $outputArr = array();

                    $bankPaymentTotal = collect($output)->pluck('BRVDocumentAmount')->toArray();
                    $bankPaymentTotal = array_sum($bankPaymentTotal);

                    $creditNoteTotal = collect($output)->pluck('CNDocumentAmount')->toArray();
                    $creditNoteTotal = array_sum($creditNoteTotal);

                    $decimalPlaces = 2;
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($companyCurrency) {
                        if ($request->currencyID == 2) {
                            $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                            $selectedCurrency = $companyCurrency->localcurrency->CurrencyCode;
                        } else if ($request->currencyID == 3) {
                            $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                            $selectedCurrency = $companyCurrency->reportingcurrency->CurrencyCode;
                        }
                    }

                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->CompanyName][$val->companyID][] = $val;
                        }
                    }

                    $dataArr = array('reportData' => (object)$outputArr, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'decimalPlaces' => $decimalPlaces, 'fromDate' => \Helper::dateFormat($request->fromDate), 'toDate' => \Helper::dateFormat($request->toDate), 'selectedCurrency' => $selectedCurrency, 'bankPaymentTotal' => $bankPaymentTotal, 'creditNoteTotal' => $creditNoteTotal);

                    $html = view('print.customer_collection', $dataArr);

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($html);

                    return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();
                }
                break;
            default:
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.report_id')]));
        }
    }

    public function customerStatementExportPdf($request, $sentTo = false)
    {
        if ($request->reportTypeID == 'CSA') {
            $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
            $checkIsGroup = Company::find($request->companySystemID);

            $companyLogo = $checkIsGroup->logo_url;

            $output = $this->getCustomerStatementAccountQRY($request);

            $balanceTotal = collect($output)->pluck('balanceAmount')->toArray();
            $balanceTotal = array_sum($balanceTotal);

            $receiptAmount = collect($output)->pluck('receiptAmount')->toArray();
            $receiptAmount = array_sum($receiptAmount);

            $invoiceAmount = collect($output)->pluck('invoiceAmount')->toArray();
            $invoiceAmount = array_sum($invoiceAmount);

            $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
            $decimalPlace = array_unique($decimalPlace);

            $currencyCode = "";
            $currency = \Helper::companyCurrency($request->companySystemID);

            if ($request->currencyID == 2) {
                $currencyCode = $currency->localcurrency->CurrencyCode;
            }
            if ($request->currencyID == 3) {
                $currencyCode = $currency->reportingcurrency->CurrencyCode;
            }

            $outputArr = array();

            if ($output) {
                foreach ($output as $val) {
                    $outputArr[$val->customerName][$val->documentCurrency][] = $val;
                }
            }

            $dataArr = array('reportData' => (object)$outputArr, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'balanceAmount' => $balanceTotal, 'receiptAmount' => $receiptAmount, 'invoiceAmount' => $invoiceAmount, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'reportDate' => date('d/m/Y H:i:s A'), 'currency' => 'Currency: ' . $currencyCode, 'fromDate' => \Helper::dateFormat($request->fromDate), 'toDate' => \Helper::dateFormat($request->toDate), 'currencyID' => $request->currencyID);


            if ($sentTo) {
                return $dataArr;
            }

            $html = view('print.customer_statement_of_account_pdf', $dataArr);

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($html);

            return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();
        } elseif ($request->reportTypeID == 'CBS') {

            $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
            $checkIsGroup = Company::find($request->companySystemID);
            $output = $this->getCustomerBalanceStatementQRY($request);

            $companyLogo = $checkIsGroup->logo_url;

            $outputArr = array();
            $grandTotal = collect($output)->pluck('balanceAmount')->toArray();
            $grandTotal = array_sum($grandTotal);

            $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
            $decimalPlace = array_unique($decimalPlace);

            if ($output) {
                foreach ($output as $val) {
                    $outputArr[$val->customerName][$val->documentCurrency][] = $val;
                }
            }

            $dataArr = array('reportData' => (object)$outputArr, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'grandTotal' => $grandTotal, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'fromDate' => \Helper::dateFormat($request->fromDate));


            if ($sentTo) {
                return $dataArr;
            }

            $html = view('print.customer_balance_statement', $dataArr);
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($html);

            return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();
        }
    }

     public function customerLedgerExportPdf($request, $sentTo = false)
    {
        $reportTypeID = $request['reportTypeID'];
        if ($reportTypeID == 'CLT1') { //customer ledger template 1

            $request = (object)$this->convertArrayToSelectedValue($request, array('currencyID'));
            $checkIsGroup = Company::find($request->companySystemID);

            $companyLogo = $checkIsGroup->logo_url;
            $output = $this->getCustomerLedgerTemplate1QRY($request);

            $outputArr = array();
            $invoiceAmount = collect($output)->pluck('invoiceAmount')->toArray();
            $invoiceAmount = array_sum($invoiceAmount);

            $paidAmount = collect($output)->pluck('paidAmount')->toArray();
            $paidAmount = array_sum($paidAmount);

            $balanceAmount = collect($output)->pluck('balanceAmount')->toArray();
            $balanceAmount = array_sum($balanceAmount);

            $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
            $decimalPlace = array_unique($decimalPlace);

            if ($output) {
                foreach ($output as $val) {
                    $outputArr[$val->concatCustomerName][$val->documentCurrency][] = $val;
                }
            }
            $dataArr = array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'balanceAmount' => $balanceAmount, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'paidAmount' => $paidAmount, 'invoiceAmount' => $invoiceAmount, 'fromDate' => \Helper::dateFormat($request->fromDate),'companyLogo' => $companyLogo);

            $html = view('print.customer_ledger_template_one', $dataArr);

            return ['html' => $html, 'output' => $output];
        } else {
            $request = (object)$this->convertArrayToSelectedValue($request, array('currencyID'));
            $checkIsGroup = Company::find($request->companySystemID);

            $companyLogo = $checkIsGroup->logo_url;
            $output = $this->getCustomerLedgerTemplate2QRY($request);

            $outputArr = array();
            $invoiceAmount = collect($output)->pluck('invoiceAmount')->toArray();
            $invoiceAmount = array_sum($invoiceAmount);

            $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
            $decimalPlace = array_unique($decimalPlace);

            if ($output) {
                foreach ($output as $val) {
                    $outputArr[$val->concatCustomerName][$val->documentCurrency][] = $val;
                }
            }
            $dataArr = array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'invoiceAmount' => $invoiceAmount, 'fromDate' => \Helper::dateFormat($request->fromDate), 'toDate' => \Helper::dateFormat($request->toDate),'companyLogo' => $companyLogo);

            $html = view('print.customer_ledger_template_two', $dataArr);

            return ['html' => $html, 'output' => $output];
        }
    }

    public function sentCustomerStatement(Request $request)
    {
        $input = $request->all();

        if (isset($input['customers']) && count($input['customers']) != 1 && $request->reportTypeID == 'CBS') {
            return $this->sendError("customer Statement cannot be sent to multiple customers",500);
        }

        if ($request->reportTypeID == 'CSA')
        {
            $this->sendEmailToMutipleCustomers($request,$input);
        }
        else {
            $html = $this->customerStatementExportPdf($request, true);

            $customerCodeSystem = ($request->reportTypeID == 'CSA') ? $request->singleCustomer : $input['customers'][0]['customerCodeSystem'];

            $fetchCusEmail = CustomerContactDetails::where('customerID', $customerCodeSystem)
                ->get();

            $customerMaster = CustomerMaster::find($customerCodeSystem);

            $emailSentTo = 0;

            if ($fetchCusEmail) {
                foreach ($fetchCusEmail as $row) {
                    if (!empty($row->contactPersonEmail)) {
                        $emailSentTo = 1;
                    }
                }
            }

            if ($emailSentTo == 0) {
                return $this->sendResponse($emailSentTo, 'Customer email is not updated. report is not sent');
            } else {
                CustomerStatementJob::dispatch($request->db, $html, $customerCodeSystem, $input['companySystemID'], $request->reportTypeID);
                return $this->sendResponse($emailSentTo, 'Customer statement report sent');
            }
        }

    }

    public function sendEmailToMutipleCustomers($request,$input)
    {
       $html = $this->customerStatementExportPdf($request, true);
       $customers = $request->customers;
       $customerSystemCodes = collect($customers)->pluck(['customerCodeSystem']);
        $data =  array();
       foreach ($customerSystemCodes as $customerSystemCode)
       {
           $reportDataCopy = $html;
           $fetchCusEmail = CustomerContactDetails::where('customerID', $customerSystemCode)
               ->get();

           $customerMaster = CustomerMaster::find($customerSystemCode);

           $customerKey = $customerMaster->CutomerCode." - ".$customerMaster->CustomerName;
           if (isset($html['reportData']->$customerKey)) {
               $customerData = $html['reportData']->$customerKey;
               $data[$customerKey] = $customerData;
               $totalBalanceAmount = 0;
               $totalInvoiceAmount = 0;
               $totalReceiptAmount = 0;
               foreach ($data[$customerKey] as $key=>$reportByCurrency)
               {
                   $data[$customerKey][$key]['balanceAmount'] = collect($reportByCurrency)->sum('balanceAmount');
                   $data[$customerKey][$key]['invoiceAmount'] = collect($reportByCurrency)->sum('invoiceAmount');
                   $data[$customerKey][$key]['receiptAmount'] = collect($reportByCurrency)->sum('receiptAmount');

                   $totalBalanceAmount += collect($reportByCurrency)->sum('balanceAmount');
                   $totalInvoiceAmount += collect($reportByCurrency)->sum('invoiceAmount');
                   $totalReceiptAmount += collect($reportByCurrency)->sum('receiptAmount');
               }

               $data['balanceAmount'] = $totalBalanceAmount;
               $data['invoiceAmount'] = $totalInvoiceAmount;
               $data['receiptAmount'] = $totalReceiptAmount;
               $data['companylogo'] = $reportDataCopy['companylogo'];
               $data['companyName'] = $reportDataCopy['companyName'];
               $data['currencyID'] = $reportDataCopy['currencyID'];
               $data['fromDate'] = $reportDataCopy['fromDate'];
               $data['currency'] = $reportDataCopy['currency'];
               $data['toDate'] = $reportDataCopy['toDate'];
               $data['reportData'] = $reportDataCopy['reportData'];
               $data['currencyDecimalPlace'] = $reportDataCopy['currencyDecimalPlace'];
               CustomerStatementJob::dispatch($request->db, $data, $customerSystemCode, $input['companySystemID'], $request->reportTypeID);

           }
       }

        return $this->sendResponse("", 'Customer statement report sent');
    }

    public function sentCustomerLedger(Request $request)
    {
        $input = $request->all();
        $db = isset($request->db) ? $request->db : "";
        SentCustomerLedger::dispatch($input, $db);
        return $this->sendResponse([], 'Customer ledger report sent to queue');
    }

    public function getAcountReceivableFilterData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $customerCategoryID = $request['customerCategoryID'];
        $companiesByGroup = "";
        if (\Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

        $controlAccount = CustomerMaster::groupBy('custGLAccountSystemID')->pluck('custGLAccountSystemID')->toArray();
        $controlAccount1 = CustomerMaster::groupBy('custAdvanceAccountSystemID')->pluck('custAdvanceAccountSystemID')->toArray();

        $mergedArray = array_merge($controlAccount, $controlAccount1);
        $uniqueArray = array_unique($mergedArray);
        $uniqueArray = array_values($uniqueArray);
        $controlAccount = ChartOfAccount::whereIN('chartOfAccountSystemID', $uniqueArray)->get();

        $departments = \Helper::getCompanyServiceline($selectedCompanyId);

        $departments[] = array("serviceLineSystemID" => 24, "ServiceLineCode" => 'X', "serviceLineMasterCode" => 'X', "ServiceLineDes" => 'X');

        $customerMaster = '';

        if ($request['reportID'] == 'CR') {
            $customerMaster = CustomerAssigned::whereIN('companySystemID', $companiesByGroup)
                                              ->groupBy('customerCodeSystem')
                                              ->orderBy('CustomerName', 'ASC')
                                              ->WhereNotNull('customerCodeSystem');

            if (!is_null($customerCategoryID) && $customerCategoryID > 0) {
                $customerMaster = $customerMaster->whereHas('customer_master', function($query) use ($customerCategoryID) {
                                                        $query->where('customerCategoryID', $customerCategoryID);
                                                });
            }
            $customerMaster = $customerMaster->get();
        } else {
            $customerMaster = CustomerAssigned::whereIN('companySystemID', $companiesByGroup)
                                            ->groupBy('customerCodeSystem')
                                            ->orderBy('CustomerName', 'ASC')
                                            ->WhereNotNull('customerCodeSystem');

            if (!is_null($customerCategoryID) && $customerCategoryID > 0) {
                $customerMaster = $customerMaster->whereHas('customer_master', function($query) use ($customerCategoryID) {
                                                        $query->where('customerCategoryID', $customerCategoryID);
                                                });
            }

            $customerMaster = $customerMaster->get();
        }
        $years = GeneralLedger::select(DB::raw("YEAR(documentDate) as year"))
            ->whereNotNull('documentDate')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get(['year']);

        $customerCategories = CustomerMasterCategoryAssigned::whereIN('companySystemID', $companiesByGroup)
                                                                ->where('isAssigned',1)
                                                                ->where('isActive',1)
                                                                ->get();

        $output = array(
            'controlAccount' => $controlAccount,
            'customers' => $customerMaster,
            'customerCategories' => $customerCategories,
            'departments' => $departments,
            'years' => $years,
        );

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    function getCustomerStatementAccountQRY($request)
    {
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $controlAccounts = (array)$request->controlAccountsSystemID;
        $controlAccountsSystemID = collect($controlAccounts)->pluck('id')->toArray();
        $currency = $request->currencyID;

        $customers = (array)$request->customers;
        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $balanceAmountQry = '';
        $balanceAmountWhere = '';
        $receiptAmountQry = '';
        $decimalPlaceQry = '';
        $invoiceAmountQry = '';
        $currencyQry = '';
        if ($currency == 1) {
            $balanceAmountQry = "round(IFNULL(MainQuery.documentTransAmount,0),MainQuery.documentTransDecimalPlaces) - round(IFNULL(InvoiceFromBRVAndMatching.InvoiceTransAmount,0),MainQuery.documentTransDecimalPlaces) - round(IFNULL(MainQuery.sumReturnTransactionAmount,0),MainQuery.documentLocalDecimalPlaces) - round(IFNULL(MainQuery.sumReturnDEOLocalAmount,0),MainQuery.documentLocalDecimalPlaces) AS balanceAmount";
            $balanceAmountWhere = "round(IFNULL(MainQuery.documentTransAmount,0),MainQuery.documentTransDecimalPlaces) - round(IFNULL(InvoiceFromBRVAndMatching.InvoiceTransAmount,0),MainQuery.documentTransDecimalPlaces) - round(IFNULL(MainQuery.sumReturnTransactionAmount,0),MainQuery.documentLocalDecimalPlaces) - round(IFNULL(MainQuery.sumReturnDEOLocalAmount,0),MainQuery.documentLocalDecimalPlaces)";
            $receiptAmountQry = "round(IFNULL(InvoiceFromBRVAndMatching.InvoiceTransAmount, 0 ),MainQuery.documentTransDecimalPlaces) + round(IFNULL(MainQuery.sumReturnTransactionAmount,0),MainQuery.documentLocalDecimalPlaces) + round(IFNULL(MainQuery.sumReturnDEOLocalAmount,0),MainQuery.documentLocalDecimalPlaces) AS receiptAmount";
            $invoiceAmountQry = "round(IFNULL(MainQuery.documentTransAmount, 0 ),MainQuery.documentTransDecimalPlaces) AS invoiceAmount";
            $decimalPlaceQry = "MainQuery.documentTransDecimalPlaces AS balanceDecimalPlaces";
            $currencyQry = "MainQuery.documentTransCurrency AS documentCurrency";
        } else if ($currency == 2) {
            $balanceAmountQry = "round(IFNULL(MainQuery.documentLocalAmount,0),MainQuery.documentLocalDecimalPlaces) - round(IFNULL(InvoiceFromBRVAndMatching.InvoiceLocalAmount,0),MainQuery.documentLocalDecimalPlaces) - round(IFNULL(MainQuery.sumReturnLocalAmount,0),MainQuery.documentLocalDecimalPlaces) - round(IFNULL(MainQuery.sumReturnDEOLocalAmount,0),MainQuery.documentLocalDecimalPlaces) AS balanceAmount";
            $balanceAmountWhere = "round(IFNULL(MainQuery.documentLocalAmount,0),MainQuery.documentLocalDecimalPlaces) - round(IFNULL(InvoiceFromBRVAndMatching.InvoiceLocalAmount,0),MainQuery.documentLocalDecimalPlaces) - round(IFNULL(MainQuery.sumReturnLocalAmount,0),MainQuery.documentLocalDecimalPlaces) - round(IFNULL(MainQuery.sumReturnDEOLocalAmount,0),MainQuery.documentLocalDecimalPlaces)";
            $receiptAmountQry = "round(IFNULL(InvoiceFromBRVAndMatching.InvoiceLocalAmount, 0 ),MainQuery.documentLocalDecimalPlaces) + round(IFNULL(MainQuery.sumReturnLocalAmount,0),MainQuery.documentLocalDecimalPlaces) + round(IFNULL(MainQuery.sumReturnDEOLocalAmount,0),MainQuery.documentLocalDecimalPlaces) AS receiptAmount";
            $invoiceAmountQry = "round(IFNULL(MainQuery.documentLocalAmount, 0 ),MainQuery.documentLocalDecimalPlaces) AS invoiceAmount";
            $decimalPlaceQry = "MainQuery.documentLocalDecimalPlaces AS balanceDecimalPlaces";
            $currencyQry = "MainQuery.documentLocalCurrency AS documentCurrency";
        } else {
            $balanceAmountQry = "round(IFNULL(MainQuery.documentRptAmount,0),MainQuery.documentRptDecimalPlaces) - round(IFNULL(InvoiceFromBRVAndMatching.InvoiceRptAmount,0),MainQuery.documentRptDecimalPlaces) - round(IFNULL(MainQuery.sumReturnRptAmount,0),MainQuery.documentLocalDecimalPlaces) - round(IFNULL(MainQuery.sumReturnDEORptAmount,0),MainQuery.documentLocalDecimalPlaces) AS balanceAmount";
            $balanceAmountWhere = "round(IFNULL(MainQuery.documentRptAmount,0),MainQuery.documentRptDecimalPlaces) - round(IFNULL(InvoiceFromBRVAndMatching.InvoiceRptAmount,0),MainQuery.documentRptDecimalPlaces) - round(IFNULL(MainQuery.sumReturnRptAmount,0),MainQuery.documentLocalDecimalPlaces) - round(IFNULL(MainQuery.sumReturnDEORptAmount,0),MainQuery.documentLocalDecimalPlaces)";
            $receiptAmountQry = "round(IFNULL(InvoiceFromBRVAndMatching.InvoiceRptAmount, 0 ),MainQuery.documentRptDecimalPlaces) + round(IFNULL(MainQuery.sumReturnRptAmount,0),MainQuery.documentLocalDecimalPlaces) + round(IFNULL(MainQuery.sumReturnDEORptAmount,0),MainQuery.documentLocalDecimalPlaces) AS receiptAmount";
            $invoiceAmountQry = "round(IFNULL(MainQuery.documentRptAmount, 0 ),MainQuery.documentRptDecimalPlaces) AS invoiceAmount";
            $decimalPlaceQry = "MainQuery.documentRptDecimalPlaces AS balanceDecimalPlaces";
            $currencyQry = "MainQuery.documentRptCurrency AS documentCurrency";
        }
        return \DB::select('SELECT
    MainQuery.companyID,
    MainQuery.CompanyName,
    MainQuery.documentCode,
    MainQuery.documentDate AS postedDate,
    MainQuery.clientContractID,
    MainQuery.invoiceDate,
    MainQuery.documentNarration,
    InvoiceFromBRVAndMatching.ReceiptCode,
    InvoiceFromBRVAndMatching.ReceiptDate,
' . $balanceAmountQry . ',
    ' . $receiptAmountQry . ',
    ' . $invoiceAmountQry . ',
    ' . $currencyQry . ',
    ' . $decimalPlaceQry . ',
    MainQuery.customerName,
    MainQuery.PONumber,
    MainQuery.documentSystemID,
    MainQuery.documentSystemCode
FROM
    (
SELECT
    erp_generalledger.companySystemID,
    erp_generalledger.companyID,
    companymaster.CompanyName,
    erp_generalledger.serviceLineSystemID,
    erp_generalledger.serviceLineCode,
    erp_generalledger.documentSystemID,
    erp_generalledger.documentID,
    erp_generalledger.documentSystemCode,
    erp_generalledger.documentCode,
    erp_generalledger.documentDate,
    DATE_FORMAT( erp_generalledger.documentDate, "%d/%m/%Y" ) AS documentDateFilter,
    erp_generalledger.invoiceNumber,
    erp_generalledger.invoiceDate,
    erp_generalledger.chartOfAccountSystemID,
    erp_generalledger.glCode,
    erp_generalledger.documentNarration,
    erp_generalledger.clientContractID,
    erp_generalledger.supplierCodeSystem,
    erp_generalledger.documentTransCurrencyID,
    erp_generalledger.documentTransAmount,
    erp_generalledger.documentLocalCurrencyID,
    erp_generalledger.documentLocalAmount,
    erp_generalledger.documentRptCurrencyID,
    erp_generalledger.documentRptAmount,
    currLocal.DecimalPlaces AS documentLocalDecimalPlaces,
    currRpt.DecimalPlaces AS documentRptDecimalPlaces,
    currTrans.DecimalPlaces AS documentTransDecimalPlaces,
    currRpt.CurrencyCode AS documentRptCurrency,
    currLocal.CurrencyCode AS documentLocalCurrency,
    currTrans.CurrencyCode AS documentTransCurrency,
    erp_custinvoicedirect.PONumber,
    CONCAT( customermaster.CutomerCode, " - ", customermaster.CustomerName ) AS customerName,
    IFNULL(srInvoiced.sumReturnTransactionAmount, 0) AS sumReturnTransactionAmount,
    IFNULL(srInvoiced.sumReturnLocalAmount, 0) AS sumReturnLocalAmount,
    IFNULL(srInvoiced.sumReturnRptAmount, 0) AS sumReturnRptAmount,
    IFNULL(srDEO.sumReturnDEOTransactionAmount, 0) AS sumReturnDEOTransactionAmount,
    IFNULL(srDEO.sumReturnDEOLocalAmount, 0) AS sumReturnDEOLocalAmount,
    IFNULL(srDEO.sumReturnDEORptAmount, 0) AS sumReturnDEORptAmount
FROM
    erp_generalledger
    INNER JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem
    LEFT JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
    LEFT JOIN currencymaster currTrans ON erp_generalledger.documentTransCurrencyID = currTrans.currencyID
    LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
    LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
    LEFT JOIN erp_custinvoicedirect ON erp_generalledger.documentSystemCode = erp_custinvoicedirect.custInvoiceDirectAutoID AND erp_generalledger.documentSystemID = erp_custinvoicedirect.documentSystemiD AND erp_generalledger.companySystemID = erp_custinvoicedirect.companySystemID
    LEFT JOIN (
    SELECT 
        salesreturndetails.custInvoiceDirectAutoID,
        salesreturndetails.salesReturnID,
        salesreturndetails.companySystemID,
        sum(salesreturndetails.transactionAmount + (salesreturndetails.transactionAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnTransactionAmount,
        sum(salesreturndetails.companyLocalAmount + (salesreturndetails.companyLocalAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnLocalAmount,
        sum(salesreturndetails.companyReportingAmount + (salesreturndetails.companyReportingAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnRptAmount
        FROM 
            salesreturndetails
            LEFT JOIN salesreturn ON salesReturnID = salesreturn.id
        WHERE
            salesreturndetails.companySystemID IN (' . join(',', $companyID) . ')
            AND salesreturn.approvedYN = -1
            GROUP BY salesreturndetails.custInvoiceDirectAutoID
    ) srInvoiced ON srInvoiced.custInvoiceDirectAutoID = erp_generalledger.documentSystemCode AND erp_generalledger.documentSystemID = 20
    LEFT JOIN (
    SELECT 
       salesreturndetails.deliveryOrderDetailID,
       erp_customerinvoiceitemdetails.custInvoiceDirectAutoID,
       salesreturndetails.salesReturnID,
       salesreturndetails.companySystemID,
       sum(salesreturndetails.transactionAmount + (salesreturndetails.transactionAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEOTransactionAmount,
       sum(salesreturndetails.companyLocalAmount + (salesreturndetails.companyLocalAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEOLocalAmount,
       sum(salesreturndetails.companyReportingAmount + (salesreturndetails.companyReportingAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEORptAmount
       FROM 
            salesreturndetails
            LEFT JOIN salesreturn ON salesReturnID = salesreturn.id
            INNER JOIN erp_customerinvoiceitemdetails ON salesreturndetails.deliveryOrderDetailID = erp_customerinvoiceitemdetails.deliveryOrderDetailID
       WHERE
            salesreturndetails.companySystemID IN (' . join(',', $companyID) . ')
            AND salesreturn.approvedYN = -1
            AND salesreturndetails.deliveryOrderDetailID <> 0
            GROUP BY salesreturndetails.deliveryOrderDetailID
    ) srDEO ON srDEO.custInvoiceDirectAutoID = erp_generalledger.documentSystemCode AND erp_generalledger.documentSystemID = 20
WHERE
    erp_generalledger.documentSystemID = 20 
    AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
    AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '"
    AND "' . $toDate . '"
    AND ( erp_generalledger.chartOfAccountSystemID IN (' . join(',', $controlAccountsSystemID) . '))
    AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
    ) AS MainQuery
    LEFT JOIN (
SELECT
    InvoiceFromUNION.companySystemID,
    InvoiceFromUNION.companyID,
    max( InvoiceFromUNION.custPaymentReceiveCode ) AS ReceiptCode,
    max( InvoiceFromUNION.postedDate ) AS ReceiptDate,
    InvoiceFromUNION.addedDocumentSystemID,
    InvoiceFromUNION.addedDocumentID,
    InvoiceFromUNION.bookingInvCodeSystem,
    InvoiceFromUNION.bookingInvCode,
    sum( InvoiceFromUNION.receiveAmountTrans ) AS InvoiceTransAmount,
    sum( InvoiceFromUNION.receiveAmountLocal ) AS InvoiceLocalAmount,
    sum( InvoiceFromUNION.receiveAmountRpt ) AS InvoiceRptAmount 
FROM
    (
SELECT
    * 
FROM
    (
SELECT
    erp_customerreceivepayment.custPaymentReceiveCode,
    erp_customerreceivepayment.postedDate,
    erp_custreceivepaymentdet.companySystemID,
    erp_custreceivepaymentdet.companyID,
    erp_custreceivepaymentdet.addedDocumentSystemID,
    erp_custreceivepaymentdet.addedDocumentID,
    erp_custreceivepaymentdet.bookingInvCodeSystem,
    erp_custreceivepaymentdet.bookingInvCode,
    erp_custreceivepaymentdet.receiveAmountTrans,
    erp_custreceivepaymentdet.receiveAmountLocal,
    erp_custreceivepaymentdet.receiveAmountRpt 
FROM
    erp_customerreceivepayment
    INNER JOIN erp_custreceivepaymentdet ON erp_customerreceivepayment.custReceivePaymentAutoID = erp_custreceivepaymentdet.custReceivePaymentAutoID 
    AND erp_custreceivepaymentdet.matchingDocID = 0 
    AND erp_customerreceivepayment.approved =- 1 
WHERE
    erp_custreceivepaymentdet.bookingInvCode <> "0" 
    AND erp_custreceivepaymentdet.matchingDocID = 0 
    AND erp_customerreceivepayment.approved =- 1 
    AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
                /*AND DATE(erp_customerreceivepayment.postedDate) <= "' . $toDate . '"*/
    ) AS InvoiceFromBRV UNION ALL
SELECT
    * 
FROM
    (
SELECT
    erp_matchdocumentmaster.matchingDocCode,
    erp_matchdocumentmaster.matchingDocdate,
    erp_custreceivepaymentdet.companySystemID,
    erp_custreceivepaymentdet.companyID,
    erp_custreceivepaymentdet.addedDocumentSystemID,
    erp_custreceivepaymentdet.addedDocumentID,
    erp_custreceivepaymentdet.bookingInvCodeSystem,
    erp_custreceivepaymentdet.bookingInvCode,
    erp_custreceivepaymentdet.receiveAmountTrans,
    erp_custreceivepaymentdet.receiveAmountLocal,
    erp_custreceivepaymentdet.receiveAmountRpt 
FROM
    erp_custreceivepaymentdet
    INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID 
    AND erp_custreceivepaymentdet.companySystemID = erp_matchdocumentmaster.companySystemID 
WHERE
    erp_matchdocumentmaster.matchingConfirmedYN = 1 
    AND erp_custreceivepaymentdet.companySystemID  IN (' . join(',', $companyID) . ')
                /*AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $toDate . '"*/
    ) AS InvoiceFromMatching 
    ) AS InvoiceFromUNION 
GROUP BY
    bookingInvCode 
    ) AS InvoiceFromBRVAndMatching ON InvoiceFromBRVAndMatching.addedDocumentSystemID = MainQuery.documentSystemID 
    AND MainQuery.documentSystemCode = InvoiceFromBRVAndMatching.bookingInvCodeSystem WHERE '.$balanceAmountWhere.' <> 0 ORDER BY postedDate ASC;');
    }

    function getCustomerBalanceStatementQRY($request)
    {
        $asOfDate = new Carbon($request->fromDate);
        $asOfDate = $asOfDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $customers = (array)$request->customers;
        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $controlAccounts = (array)$request->controlAccountsSystemID;
        $controlAccountsSystemID = collect($controlAccounts)->pluck('id')->toArray();

        $fullyMatchedDocuments = $this->getFullyMatchedInvoices($companyID,$asOfDate,$customerSystemID);

        $currency = $request->currencyID;
        $currencyQry = '';
        $amountQry = '';
        $decimalPlaceQry = '';
        $whereQry = '';
        if ($currency == 1) {
            $currencyQry = "final.documentTransCurrency AS documentCurrency";
            $amountQry = "round( final.balanceTrans, final.documentTransDecimalPlaces ) AS balanceAmount";
            $decimalPlaceQry = "final.documentTransDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( final.balanceTrans, final.documentTransDecimalPlaces )";
        } else if ($currency == 2) {
            $currencyQry = "final.documentLocalCurrency AS documentCurrency";
            $amountQry = "round( final.balanceLocal, final.documentLocalDecimalPlaces ) AS balanceAmount";
            $decimalPlaceQry = "final.documentLocalDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( final.balanceLocal, final.documentLocalDecimalPlaces )";
        } else {
            $currencyQry = "final.documentRptCurrency AS documentCurrency";
            $amountQry = "round( final.balanceRpt, final.documentRptDecimalPlaces ) AS balanceAmount";
            $decimalPlaceQry = "final.documentRptDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( final.balanceRpt, final.documentRptDecimalPlaces )";
        }
        $currencyID = $request->currencyID;
        $output = \DB::select('SELECT
    final.documentCode AS DocumentCode,
    final.documentDate AS PostedDate,
    final.documentNarration AS DocumentNarration,
    final.clientContractID AS Contract,
    final.invoiceNumber AS invoiceNumber,
    final.invoiceDate AS InvoiceDate,
    ' . $amountQry . ',
    ' . $currencyQry . ',
    ' . $decimalPlaceQry . ',
    final.customerName AS customerName, 
    final.PONumber,
    final.companyID,
    final.CompanyName,
    final.documentSystemCode,
    final.documentSystemID
FROM
    (
SELECT
    mainQuery.companySystemID,
    mainQuery.companyID,
    mainQuery.CompanyName,
    mainQuery.serviceLineSystemID,
    mainQuery.serviceLineCode,
    mainQuery.documentSystemID,
    mainQuery.documentID,
    mainQuery.documentSystemCode,
    mainQuery.documentCode,
    mainQuery.documentDate,
    mainQuery.documentDateFilter,
    mainQuery.invoiceNumber,
    mainQuery.invoiceDate,
    mainQuery.chartOfAccountSystemID,
    mainQuery.glCode,
    mainQuery.documentNarration,
    mainQuery.clientContractID,
    mainQuery.supplierCodeSystem,
    mainQuery.documentTransCurrencyID,
    mainQuery.documentTransCurrency,
    mainQuery.documentTransAmount,
    mainQuery.documentTransDecimalPlaces,
    mainQuery.documentLocalCurrencyID,
    mainQuery.documentLocalCurrency,
    mainQuery.documentLocalAmount,
    mainQuery.documentLocalDecimalPlaces,
    mainQuery.documentRptCurrencyID,
    mainQuery.documentRptCurrency,
    mainQuery.documentRptAmount,
    mainQuery.documentRptDecimalPlaces,
IF( matchedBRV.MatchedBRVTransAmount IS NULL, 0, matchedBRV.MatchedBRVTransAmount ) AS MatchedBRVTransAmount,
IF( matchedBRV.MatchedBRVLocalAmount IS NULL, 0, matchedBRV.MatchedBRVLocalAmount ) AS MatchedBRVLocalAmount,
IF( matchedBRV.MatchedBRVRptAmount IS NULL, 0, matchedBRV.MatchedBRVRptAmount ) AS MatchedBRVRptAmount,
IF( InvoicedBRV.BRVTransAmount IS NULL, 0, InvoicedBRV.BRVTransAmount ) AS BRVTransAmount,
IF( InvoicedBRV.BRVLocalAmount IS NULL, 0, InvoicedBRV.BRVLocalAmount ) AS BRVLocalAmount,
IF( InvoicedBRV.BRVRptAmount IS NULL, 0, InvoicedBRV.BRVRptAmount ) AS BRVRptAmount,
IF( InvoiceFromBRVAndMatching.InvoiceTransAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceTransAmount *- 1 ) AS InvoiceTransAmount,
IF( InvoiceFromBRVAndMatching.InvoiceLocalAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceLocalAmount *- 1 ) AS InvoiceLocalAmount,
IF( InvoiceFromBRVAndMatching.InvoiceRptAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceRptAmount *- 1 ) AS InvoiceRptAmount,
    (
    mainQuery.documentRptAmount + ( IF ( InvoicedBRV.BRVRptAmount IS NULL, 0, InvoicedBRV.BRVRptAmount ) ) + ( IF ( srInvoiced.sumReturnRptAmount IS NULL, 0, srInvoiced.sumReturnRptAmount * -1) )  + ( IF ( srDEO.sumReturnDEORptAmount IS NULL, 0, srDEO.sumReturnDEORptAmount * -1) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceRptAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceRptAmount *- 1 ) ) 
    ) AS balanceRpt,
    (
    mainQuery.documentLocalAmount + ( IF ( InvoicedBRV.BRVLocalAmount IS NULL, 0, InvoicedBRV.BRVLocalAmount ) ) + ( IF ( srDEO.sumReturnDEOLocalAmount IS NULL, 0, srDEO.sumReturnDEOLocalAmount * -1) ) + ( IF ( srInvoiced.sumReturnLocalAmount  IS NULL, 0, srInvoiced.sumReturnLocalAmount  * -1) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceLocalAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceLocalAmount *- 1 ) ) 
    ) AS balanceLocal,
    (
    mainQuery.documentTransAmount  + ( IF ( InvoicedBRV.BRVTransAmount IS NULL, 0, InvoicedBRV.BRVTransAmount ) ) + ( IF ( srInvoiced.sumReturnTransactionAmount IS NULL, 0, srInvoiced.sumReturnTransactionAmount * -1) ) + ( IF ( srDEO.sumReturnDEOTransactionAmount IS NULL, 0, srDEO.sumReturnDEOTransactionAmount * -1) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceTransAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceTransAmount *- 1 ) ) 
    ) AS balanceTrans,
    mainQuery.customerName,   
    mainQuery.PONumber 
FROM
    (
SELECT
    erp_generalledger.companySystemID,
    erp_generalledger.companyID,
    companymaster.CompanyName,
    erp_generalledger.serviceLineSystemID,
    erp_generalledger.serviceLineCode,
    erp_generalledger.documentSystemID,
    erp_generalledger.documentID,
    erp_generalledger.documentSystemCode,
    erp_generalledger.documentCode,
    erp_generalledger.documentDate,
    DATE_FORMAT( documentDate, "%d/%m/%Y" ) AS documentDateFilter,
    erp_generalledger.documentYear,
    erp_generalledger.documentMonth,
    erp_generalledger.chequeNumber,
    erp_generalledger.invoiceNumber,
    erp_generalledger.invoiceDate,
    erp_generalledger.chartOfAccountSystemID,
    erp_generalledger.glCode,
    erp_generalledger.documentNarration,
    erp_generalledger.clientContractID,
    erp_generalledger.supplierCodeSystem,
    erp_generalledger.documentTransCurrencyID,
    currTrans.CurrencyCode as documentTransCurrency,
    currTrans.DecimalPlaces as documentTransDecimalPlaces,
CASE 
        WHEN erp_generalledger.documentSystemID = 19 THEN 
        SUM(erp_generalledger.documentLocalAmount) + 
IFNULL(
    (
     SELECT 
            SUM(IFNULL(erp_custreceivepaymentdet.receiveAmountLocal, 0)) 
        FROM 
            erp_custreceivepaymentdet 
        INNER JOIN 
            erp_matchdocumentmaster 
        ON 
            erp_matchdocumentmaster.companyID = erp_custreceivepaymentdet.companyID 
            AND 
            erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID 
        WHERE 
            erp_matchdocumentmaster.matchingConfirmedYN = 1 
            AND erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_generalledger.documentSystemCode 
            AND erp_matchdocumentmaster.documentSystemID = erp_generalledger.documentSystemID
        GROUP BY 
            erp_custreceivepaymentdet.custReceivePaymentAutoID 
    ), 
    0
) ELSE SUM(erp_generalledger.documentLocalAmount)
    END AS documentLocalAmount,
    CASE 
        WHEN erp_generalledger.documentSystemID = 19 THEN 
        SUM(erp_generalledger.documentTransAmount) + 
IFNULL(
    (
        SELECT 
            SUM(IFNULL(erp_custreceivepaymentdet.receiveAmountTrans, 0)) 
        FROM 
            erp_custreceivepaymentdet 
        INNER JOIN 
            erp_matchdocumentmaster 
        ON 
            erp_matchdocumentmaster.companyID = erp_custreceivepaymentdet.companyID 
            AND erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID 
        WHERE 
            erp_matchdocumentmaster.matchingConfirmedYN = 1 
            AND erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_generalledger.documentSystemCode
            AND erp_matchdocumentmaster.documentSystemID = erp_generalledger.documentSystemID 
        GROUP BY 
            erp_custreceivepaymentdet.custReceivePaymentAutoID
    ), 
    0
)  ELSE SUM(erp_generalledger.documentTransAmount)
    END AS documentTransAmount,
    CASE 
        WHEN erp_generalledger.documentSystemID = 19 THEN 
        SUM(erp_generalledger.documentRptAmount) + 
IFNULL(
    (
        SELECT 
            SUM(IFNULL(erp_custreceivepaymentdet.receiveAmountRpt, 0)) 
        FROM 
            erp_custreceivepaymentdet 
        INNER JOIN 
            erp_matchdocumentmaster 
        ON 
            erp_matchdocumentmaster.companyID = erp_custreceivepaymentdet.companyID 
            AND erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID 
        WHERE 
            erp_matchdocumentmaster.matchingConfirmedYN = 1 
            AND erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_generalledger.documentSystemCode 
            AND erp_matchdocumentmaster.documentSystemID = erp_generalledger.documentSystemID 
        GROUP BY 
            erp_custreceivepaymentdet.custReceivePaymentAutoID
    ), 
    0
) ELSE SUM(erp_generalledger.documentRptAmount)
    END AS documentRptAmount,
    erp_generalledger.documentLocalCurrencyID,
    currLocal.CurrencyCode as documentLocalCurrency,
    currLocal.DecimalPlaces as documentLocalDecimalPlaces,
    erp_generalledger.documentRptCurrencyID,
    currRpt.CurrencyCode as documentRptCurrency,
    currRpt.DecimalPlaces as documentRptDecimalPlaces,
    erp_generalledger.documentType,
    erp_custinvoicedirect.PONumber,
    CONCAT(customermaster.CutomerCode," - ",customermaster.CustomerName) as customerName
FROM
    erp_generalledger 
    LEFT JOIN currencymaster currTrans ON erp_generalledger.documentTransCurrencyID = currTrans.currencyID
    LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
    LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
    LEFT JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
    LEFT JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
    LEFT JOIN erp_custinvoicedirect ON erp_generalledger.documentSystemCode = erp_custinvoicedirect.custInvoiceDirectAutoID AND erp_generalledger.documentSystemID = erp_custinvoicedirect.documentSystemiD AND erp_generalledger.companySystemID = erp_custinvoicedirect.companySystemID
WHERE
    ( erp_generalledger.documentSystemID = "20" OR erp_generalledger.documentSystemID = "19" OR erp_generalledger.documentSystemID = "21" ) 
    AND DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
    AND ( erp_generalledger.chartOfAccountSystemID IN (' . join(',', $controlAccountsSystemID) . '))
    AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') 
    AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
    GROUP BY erp_generalledger.companySystemID, erp_generalledger.supplierCodeSystem,erp_generalledger.chartOfAccountSystemID,erp_generalledger.documentSystemID,erp_generalledger.documentSystemCode
    ) AS mainQuery
    LEFT JOIN (
    SELECT
        erp_matchdocumentmaster.companySystemID,
        erp_matchdocumentmaster.documentSystemID,
        erp_matchdocumentmaster.PayMasterAutoId,
        erp_matchdocumentmaster.BPVcode,
        sum( erp_custreceivepaymentdet.receiveAmountTrans ) AS MatchedBRVTransAmount,
        sum( erp_custreceivepaymentdet.receiveAmountLocal ) AS MatchedBRVLocalAmount,
        sum( erp_custreceivepaymentdet.receiveAmountRpt ) AS MatchedBRVRptAmount 
    FROM
        erp_matchdocumentmaster
        INNER JOIN erp_custreceivepaymentdet ON erp_matchdocumentmaster.companyID = erp_custreceivepaymentdet.companyID 
        AND erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID 
    WHERE
        erp_matchdocumentmaster.matchingConfirmedYN = 1 
        AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '" 
        AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
        AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
    GROUP BY
        erp_matchdocumentmaster.PayMasterAutoId,
        erp_matchdocumentmaster.BPVcode 
    ) AS matchedBRV ON mainQuery.documentSystemID = matchedBRV.documentSystemID 
    AND mainQuery.companySystemID = matchedBRV.companySystemID 
    AND matchedBRV.PayMasterAutoId = mainQuery.documentSystemCode
    LEFT JOIN (
    SELECT
        erp_customerreceivepayment.custReceivePaymentAutoID,
        erp_customerreceivepayment.companySystemID,
        erp_customerreceivepayment.documentSystemID,
        erp_customerreceivepayment.custPaymentReceiveCode,
        sum( erp_custreceivepaymentdet.receiveAmountTrans ) AS BRVTransAmount,
        sum( erp_custreceivepaymentdet.receiveAmountLocal ) AS BRVLocalAmount,
        sum( erp_custreceivepaymentdet.receiveAmountRpt ) AS BRVRptAmount 
    FROM
        erp_customerreceivepayment
        INNER JOIN erp_custreceivepaymentdet ON erp_customerreceivepayment.custReceivePaymentAutoID = erp_custreceivepaymentdet.custReceivePaymentAutoID 
    WHERE
        erp_custreceivepaymentdet.bookingInvCode <> "0" 
        AND erp_custreceivepaymentdet.matchingDocID = 0 
        AND erp_customerreceivepayment.approved =- 1 
        AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
        AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '"
        AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
    GROUP BY
        custReceivePaymentAutoID 
    ) AS InvoicedBRV ON mainQuery.documentSystemID = InvoicedBRV.documentSystemID 
    AND mainQuery.documentSystemCode = InvoicedBRV.custReceivePaymentAutoID
    LEFT JOIN (
    SELECT 
        salesreturndetails.custInvoiceDirectAutoID,
        salesreturndetails.salesReturnID,
        salesreturndetails.companySystemID,
        sum(salesreturndetails.transactionAmount + (salesreturndetails.transactionAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnTransactionAmount,
        sum(salesreturndetails.companyLocalAmount + (salesreturndetails.companyLocalAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnLocalAmount,
        sum(salesreturndetails.companyReportingAmount + (salesreturndetails.companyReportingAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnRptAmount
        FROM 
            salesreturndetails
            LEFT JOIN salesreturn ON salesReturnID = salesreturn.id
        WHERE
            salesreturndetails.companySystemID IN (' . join(',', $companyID) . ')
            AND salesreturn.approvedYN = -1
            GROUP BY salesreturndetails.custInvoiceDirectAutoID
    ) srInvoiced ON srInvoiced.custInvoiceDirectAutoID = mainQuery.documentSystemCode AND mainQuery.documentSystemID = 20 
     LEFT JOIN (
    SELECT 
       salesreturndetails.deliveryOrderDetailID,
       erp_customerinvoiceitemdetails.custInvoiceDirectAutoID,
       salesreturndetails.salesReturnID,
       salesreturndetails.companySystemID,
       sum(salesreturndetails.transactionAmount + (salesreturndetails.transactionAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEOTransactionAmount,
       sum(salesreturndetails.companyLocalAmount + (salesreturndetails.companyLocalAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEOLocalAmount,
       sum(salesreturndetails.companyReportingAmount + (salesreturndetails.companyReportingAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEORptAmount
       FROM 
            salesreturndetails
            LEFT JOIN salesreturn ON salesReturnID = salesreturn.id
            INNER JOIN erp_customerinvoiceitemdetails ON salesreturndetails.deliveryOrderDetailID = erp_customerinvoiceitemdetails.deliveryOrderDetailID
       WHERE
            salesreturndetails.companySystemID IN (' . join(',', $companyID) . ')
            AND salesreturn.approvedYN = -1
            AND salesreturndetails.deliveryOrderDetailID <> 0
            GROUP BY salesreturndetails.deliveryOrderDetailID
    ) srDEO ON srDEO.custInvoiceDirectAutoID = mainQuery.documentSystemCode AND mainQuery.documentSystemID = 20     
    LEFT JOIN (
    SELECT
        companySystemID,
        companyID,
        addedDocumentSystemID,
        addedDocumentID,
        bookingInvCodeSystem,
        bookingInvCode,
        sum( receiveAmountTrans ) AS InvoiceTransAmount,
        sum( receiveAmountLocal ) AS InvoiceLocalAmount,
        sum( receiveAmountRpt ) AS InvoiceRptAmount 
    FROM
        (
        SELECT
            * 
        FROM
            (
            SELECT
                erp_customerreceivepayment.custPaymentReceiveCode,
                erp_custreceivepaymentdet.companySystemID,
                erp_custreceivepaymentdet.companyID,
                erp_custreceivepaymentdet.addedDocumentSystemID,
                erp_custreceivepaymentdet.addedDocumentID,
                erp_custreceivepaymentdet.bookingInvCodeSystem,
                erp_custreceivepaymentdet.bookingInvCode,
                erp_custreceivepaymentdet.receiveAmountTrans,
                erp_custreceivepaymentdet.receiveAmountLocal,
                erp_custreceivepaymentdet.receiveAmountRpt 
            FROM
                erp_customerreceivepayment
                INNER JOIN erp_custreceivepaymentdet ON erp_customerreceivepayment.custReceivePaymentAutoID = erp_custreceivepaymentdet.custReceivePaymentAutoID 
                AND erp_custreceivepaymentdet.matchingDocID = 0 
                AND erp_customerreceivepayment.approved =- 1 
            WHERE
                erp_custreceivepaymentdet.bookingInvCode <> "0" 
                AND erp_custreceivepaymentdet.matchingDocID = 0 
                AND erp_customerreceivepayment.approved =- 1 
                AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
                AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '" 
                AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
            ) AS InvoiceFromBRV UNION ALL
        SELECT
            * 
        FROM
            (
            SELECT
                erp_matchdocumentmaster.matchingDocCode,
                erp_custreceivepaymentdet.companySystemID,
                erp_custreceivepaymentdet.companyID,
                erp_custreceivepaymentdet.addedDocumentSystemID,
                erp_custreceivepaymentdet.addedDocumentID,
                erp_custreceivepaymentdet.bookingInvCodeSystem,
                erp_custreceivepaymentdet.bookingInvCode,
                erp_custreceivepaymentdet.receiveAmountTrans,
                erp_custreceivepaymentdet.receiveAmountLocal,
                erp_custreceivepaymentdet.receiveAmountRpt 
            FROM
                erp_custreceivepaymentdet
                INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID 
                AND erp_custreceivepaymentdet.companySystemID = erp_matchdocumentmaster.companySystemID 
            WHERE
                erp_matchdocumentmaster.matchingConfirmedYN = 1 
                AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
                AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '" 
                AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
            ) AS InvoiceFromMatching 
        ) AS InvoiceFromUNION 
    GROUP BY
        bookingInvCode 
    ) AS InvoiceFromBRVAndMatching ON InvoiceFromBRVAndMatching.addedDocumentSystemID = mainQuery.documentSystemID 
    AND mainQuery.documentSystemCode = InvoiceFromBRVAndMatching.bookingInvCodeSystem 
    ) AS final 
WHERE
' . $whereQry . ' <> 0 ORDER BY PostedDate ASC;');


        $output = collect($output)->filter(function ($item) {
            return !str_contains($item->DocumentNarration, 'Matching');
        });

        $excludedDocumentCodes = array_flatten($fullyMatchedDocuments);
        $filteredData = collect($output)->reject(function ($item) use ($excludedDocumentCodes) {
            return in_array($item->DocumentCode, $excludedDocumentCodes);
        });
        return $filteredData;
    }

    // Customer Aging detail report
    function getCustomerAgingDetailQRY($request)
    {
        $asOfDate = new Carbon($request->fromDate);
        $asOfDate = $asOfDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $customers = (array)$request->customers;
        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $controlAccounts = (array)$request->controlAccountsSystemID;
        $controlAccountsSystemID = collect($controlAccounts)->pluck('id')->toArray();

        $currency = $request->currencyID;

        $z = 1;
        $aging = array();
        $interval = $request->interval;
        $through = $request->through;
        $agingRange = range(0, $through, $interval);
        $rangeAmount = $interval;
        $agingAgeCount = count($agingRange);
        foreach ($agingRange as $val) {
            if ($z == $agingAgeCount) {
                $aging[] = $val + 1 . "-" . $through;
            } else {
                if ($z == 1) {
                    $aging[] = $val . "-" . $rangeAmount;
                } else {
                    $aging[] = $val + 1 . "-" . $rangeAmount;
                }
                $rangeAmount += $interval;
            }
            $z++;
        }

        $aging[] = "> " . ($through);
        $agingField = '';
        if (!empty($aging)) { /*calculate aging range in query*/
            $count = count($aging);
            $c = 1;
            foreach ($aging as $val) {
                if ($count == $c) {
                    $agingField .= "if(grandFinal.age > " . $through . ",grandFinal.balanceAmount,0) as `" . $val . "`,";
                } else {
                    $list = explode("-", $val);
                    $agingField .= "if(grandFinal.age >= " . $list[0] . " AND grandFinal.age <= " . $list[1] . ",grandFinal.balanceAmount,0) as `" . $val . "`,";
                }
                $c++;
            }
        }
        $agingField .= "if(grandFinal.age <= 0,grandFinal.balanceAmount,0) as `current`";

        $fullyMatchedDocuments = $this->getFullyMatchedInvoices($companyID,$asOfDate,$customerSystemID);


        $currencyQry = '';
        $amountQry = '';
        $decimalPlaceQry = '';
        $whereQry = '';
        $subsequentBalanceQry = '';
        $subsequentQry = '';
        $invoiceQry = '';
        if ($currency == 1) {
            $currencyQry = "final.documentTransCurrency AS documentCurrency";
            $amountQry = "round( final.balanceTrans, final.documentTransDecimalPlaces ) AS balanceAmount";
            $decimalPlaceQry = "final.documentTransDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( final.balanceTrans, final.documentTransDecimalPlaces )";
            $subsequentBalanceQry = "round( final.balanceSubsequentCollectionTrans, final.documentTransDecimalPlaces ) as subsequentBalanceAmount";
            $subsequentQry = "round( final.SubsequentCollectionTransAmount, final.documentTransDecimalPlaces ) AS subsequentAmount";
            $invoiceQry = "round( final.documentTransAmount, final.documentTransDecimalPlaces ) AS invoiceAmount";
        } else if ($currency == 2) {
            $currencyQry = "final.documentLocalCurrency AS documentCurrency";
            $amountQry = "round( final.balanceLocal, final.documentLocalDecimalPlaces ) AS balanceAmount";
            $decimalPlaceQry = "final.documentLocalDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( final.balanceLocal, final.documentLocalDecimalPlaces )";
            $subsequentBalanceQry = "round( final.balanceSubsequentCollectionLocal, final.documentLocalDecimalPlaces ) as subsequentBalanceAmount";
            $subsequentQry = "round( final.SubsequentCollectionLocalAmount, final.documentLocalDecimalPlaces ) AS subsequentAmount";
            $invoiceQry = "round( final.documentLocalAmount, final.documentLocalDecimalPlaces ) AS invoiceAmount";
        } else {
            $currencyQry = "final.documentRptCurrency AS documentCurrency";
            $amountQry = "round( final.balanceRpt, final.documentRptDecimalPlaces ) AS balanceAmount";
            $decimalPlaceQry = "final.documentRptDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( final.balanceRpt, final.documentRptDecimalPlaces )";
            $subsequentBalanceQry = "round( final.balanceSubsequentCollectionRpt, final.documentRptDecimalPlaces ) as subsequentBalanceAmount";
            $subsequentQry = "round( final.SubsequentCollectionRptAmount, final.documentRptDecimalPlaces ) AS subsequentAmount";
            $invoiceQry = "round( final.documentRptAmount, final.documentRptDecimalPlaces ) AS invoiceAmount";
        }
        $currencyID = $request->currencyID;

        $query = 'SELECT documentTransAmount2,balanceSubsequentCollectionTrans,InvoiceTransAmount,DocumentCode,commentAndStatus,PostedDate,DocumentNarration,Contract,invoiceNumber,InvoiceDate,' . $agingField . ',documentCurrency,balanceDecimalPlaces,customerName,creditDays,age,glCode,customerName2,CutomerCode,PONumber,invoiceDueDate,subsequentBalanceAmount,brvInv,subsequentAmount,companyID,invoiceAmount,companyID,CompanyName,serviceLineName,documentSystemCode,documentSystemID FROM (SELECT
    final.documentCode AS DocumentCode,
    final.comments AS commentAndStatus,
    final.documentDate AS PostedDate,
    final.documentNarration AS DocumentNarration,
    final.clientContractID AS Contract,
    final.invoiceNumber AS invoiceNumber,
    final.invoiceDate AS InvoiceDate,
    ' . $amountQry . ',
    ' . $subsequentQry . ',
    ' . $subsequentBalanceQry . ',
    ' . $currencyQry . ',
    ' . $decimalPlaceQry . ',
    ' . $invoiceQry . ',
    final.customerName AS customerName,
    final.creditDays AS creditDays,
    final.customerName2 AS customerName2,
    final.CutomerCode AS CutomerCode,
    DATEDIFF("' . $asOfDate . '",DATE(final.documentDate)) as age,
    final.glCode,
    final.PONumber,
    final.invoiceDueDate,
    final.brvInv,
    final.companyID,
    final.CompanyName,
    final.serviceLineName,
    final.documentSystemCode,
    final.documentSystemID,
    final.InvoiceTransAmount,
    final.balanceSubsequentCollectionTrans,
    final.documentLocalAmount2,
    final.documentTransAmount2
FROM
    (
SELECT
    mainQuery.companySystemID,
    mainQuery.companyID,
    mainQuery.CompanyName,
    mainQuery.serviceLineSystemID,
    mainQuery.serviceLineCode,
    mainQuery.serviceLineName,
    mainQuery.documentSystemID,
    mainQuery.documentID,
    mainQuery.documentSystemCode,
    mainQuery.documentCode,
    mainQuery.comments,
    mainQuery.documentDate,
    mainQuery.documentDateFilter,
    mainQuery.invoiceNumber,
    mainQuery.invoiceDate,
    mainQuery.chartOfAccountSystemID,
    mainQuery.glCode,
    mainQuery.documentNarration,
    mainQuery.clientContractID,
    mainQuery.supplierCodeSystem,
    mainQuery.documentTransCurrencyID,
    mainQuery.documentTransCurrency,
    mainQuery.documentTransAmount,
    mainQuery.documentTransDecimalPlaces,
    mainQuery.documentLocalCurrencyID,
    mainQuery.documentLocalCurrency,
    mainQuery.documentLocalAmount,
    mainQuery.documentLocalDecimalPlaces,
    mainQuery.documentRptCurrencyID,
    mainQuery.documentRptCurrency,
    mainQuery.documentRptAmount,
    mainQuery.documentRptDecimalPlaces,
IF( matchedBRV.MatchedBRVTransAmount IS NULL, 0, matchedBRV.MatchedBRVTransAmount ) AS MatchedBRVTransAmount,
IF( matchedBRV.MatchedBRVLocalAmount IS NULL, 0, matchedBRV.MatchedBRVLocalAmount ) AS MatchedBRVLocalAmount,
IF( matchedBRV.MatchedBRVRptAmount IS NULL, 0, matchedBRV.MatchedBRVRptAmount ) AS MatchedBRVRptAmount,
IF( InvoicedBRV.BRVTransAmount IS NULL, 0, InvoicedBRV.BRVTransAmount ) AS BRVTransAmount,
IF( InvoicedBRV.BRVLocalAmount IS NULL, 0, InvoicedBRV.BRVLocalAmount ) AS BRVLocalAmount,
IF( InvoicedBRV.BRVRptAmount IS NULL, 0, InvoicedBRV.BRVRptAmount ) AS BRVRptAmount,
IF( InvoiceFromBRVAndMatching.InvoiceTransAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceTransAmount *- 1 ) AS InvoiceTransAmount,
IF( InvoiceFromBRVAndMatching.InvoiceLocalAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceLocalAmount *- 1 ) AS InvoiceLocalAmount,
IF( InvoiceFromBRVAndMatching.InvoiceRptAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceRptAmount *- 1 ) AS InvoiceRptAmount,
IF( srInvoiced.sumReturnTransactionAmount IS NULL, 0, srInvoiced.sumReturnTransactionAmount * -1) AS sumReturnTransactionAmount,
IF( srInvoiced.sumReturnLocalAmount IS NULL, 0, srInvoiced.sumReturnLocalAmount * -1) AS sumReturnLocalAmount,
IF( srInvoiced.sumReturnRptAmount IS NULL, 0, srInvoiced.sumReturnRptAmount * -1) AS sumReturnRptAmount,
IF( srDEO.sumReturnDEOTransactionAmount IS NULL, 0, srDEO.sumReturnDEOTransactionAmount * -1) AS sumReturnDEOTransactionAmount,
IF( srDEO.sumReturnDEOLocalAmount IS NULL, 0, srDEO.sumReturnDEOLocalAmount * -1) AS sumReturnDEOLocalAmount,
IF( srDEO.sumReturnDEORptAmount IS NULL, 0, srDEO.sumReturnDEORptAmount * -1) AS sumReturnDEORptAmount,
    (
    mainQuery.documentRptAmount2 + ( IF ( InvoicedBRV.BRVRptAmount IS NULL, 0, InvoicedBRV.BRVRptAmount ) ) + ( IF ( srInvoiced.sumReturnRptAmount IS NULL, 0, srInvoiced.sumReturnRptAmount * -1) ) + ( IF ( srDEO.sumReturnDEORptAmount IS NULL, 0, srDEO.sumReturnDEORptAmount * -1) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceRptAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceRptAmount *- 1 ) )
    ) AS balanceRpt,
    (
    mainQuery.documentLocalAmount2  + ( IF ( InvoicedBRV.BRVLocalAmount IS NULL, 0, InvoicedBRV.BRVLocalAmount ) ) + ( IF ( srInvoiced.sumReturnLocalAmount IS NULL, 0, srInvoiced.sumReturnLocalAmount * -1) ) + ( IF ( srDEO.sumReturnDEOLocalAmount IS NULL, 0, srDEO.sumReturnDEOLocalAmount * -1) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceLocalAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceLocalAmount *- 1 ) )
    ) AS balanceLocal,
    (
    mainQuery.documentTransAmount2  + ( IF ( InvoicedBRV.BRVTransAmount IS NULL, 0, InvoicedBRV.BRVTransAmount ) ) + ( IF ( srInvoiced.sumReturnTransactionAmount IS NULL, 0, srInvoiced.sumReturnTransactionAmount * -1) ) + ( IF ( srDEO.sumReturnDEOTransactionAmount IS NULL, 0, srDEO.sumReturnDEOTransactionAmount * -1) )  + ( IF ( InvoiceFromBRVAndMatching.InvoiceTransAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceTransAmount *- 1 ) )
    ) AS balanceTrans,

    (
    mainQuery.documentRptAmount2 + ( IF ( InvoicedBRV.BRVRptAmount IS NULL, 0, InvoicedBRV.BRVRptAmount ) ) + ( IF ( srInvoiced.sumReturnRptAmount IS NULL, 0, srInvoiced.sumReturnRptAmount * -1) ) + ( IF ( srDEO.sumReturnDEORptAmount IS NULL, 0, srDEO.sumReturnDEORptAmount * -1) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceRptAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceRptAmount *- 1 ) -  IFNULL(Subsequentcollection.SubsequentCollectionRptAmount,0))
    ) AS balanceSubsequentCollectionRpt,
    (
    mainQuery.documentLocalAmount2 +  ( IF ( InvoicedBRV.BRVLocalAmount IS NULL, 0, InvoicedBRV.BRVLocalAmount ) ) + ( IF ( srInvoiced.sumReturnLocalAmount IS NULL, 0, srInvoiced.sumReturnLocalAmount * -1) ) + ( IF ( srDEO.sumReturnDEOLocalAmount IS NULL, 0, srDEO.sumReturnDEOLocalAmount * -1) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceLocalAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceLocalAmount *- 1 ) -  IFNULL(Subsequentcollection.SubsequentCollectionLocalAmount,0))
    ) AS balanceSubsequentCollectionLocal,
    (
    mainQuery.documentTransAmount2 + ( IF ( InvoicedBRV.BRVTransAmount IS NULL, 0, InvoicedBRV.BRVTransAmount ) )+ ( IF ( srInvoiced.sumReturnRptAmount IS NULL, 0, srInvoiced.sumReturnRptAmount * -1) ) + ( IF ( srDEO.sumReturnDEORptAmount IS NULL, 0, srDEO.sumReturnDEORptAmount * -1) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceTransAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceTransAmount *- 1 ) -  IFNULL(Subsequentcollection.SubsequentCollectionTransAmount,0))
    ) AS balanceSubsequentCollectionTrans,

    mainQuery.customerName,
    mainQuery.customerName2,
    mainQuery.creditDays,
    mainQuery.CutomerCode,
    mainQuery.PONumber,
    mainQuery.invoiceDueDate,
    IFNULL(Subsequentcollection.SubsequentCollectionRptAmount,0) as SubsequentCollectionRptAmount,
    IFNULL(Subsequentcollection.SubsequentCollectionLocalAmount,0) as SubsequentCollectionLocalAmount,
    IFNULL(Subsequentcollection.SubsequentCollectionTransAmount,0) as SubsequentCollectionTransAmount,
    Subsequentcollection.docCode as brvInv,
    mainQuery.documentLocalAmount2,
    mainQuery.documentTransAmount2,
    mainQuery.documentRptAmount2
FROM
    (
SELECT
    erp_generalledger.companySystemID,
    erp_generalledger.companyID,
    companymaster.CompanyName,
    erp_generalledger.serviceLineSystemID,
    erp_generalledger.serviceLineCode,
    erp_generalledger.documentSystemID,
    erp_generalledger.documentID,
    erp_generalledger.documentSystemCode,
    erp_generalledger.documentCode,
    collectionTrackerDetail.comments,
    erp_generalledger.documentDate,
    DATE_FORMAT( documentDate, "%d/%m/%Y" ) AS documentDateFilter,
    erp_generalledger.documentYear,
    erp_generalledger.documentMonth,
    erp_generalledger.chequeNumber,
    erp_generalledger.invoiceNumber,
    erp_generalledger.invoiceDate,
    erp_generalledger.chartOfAccountSystemID,
    erp_generalledger.glCode,
    erp_generalledger.documentNarration,
    erp_generalledger.clientContractID,
    erp_generalledger.supplierCodeSystem,
    erp_generalledger.documentTransCurrencyID,
    currTrans.CurrencyCode as documentTransCurrency,
    currTrans.DecimalPlaces as documentTransDecimalPlaces,
    SUM(erp_generalledger.documentTransAmount) as documentTransAmount,
    erp_generalledger.documentLocalCurrencyID,
    currLocal.CurrencyCode as documentLocalCurrency,
    currLocal.DecimalPlaces as documentLocalDecimalPlaces,
    SUM(erp_generalledger.documentLocalAmount) as documentLocalAmount,
    CASE 
        WHEN erp_generalledger.documentSystemID = 19 THEN 
        (SUM(erp_generalledger.documentLocalAmount) + IFNULL((SELECT IFNULL(SUM(erp_custreceivepaymentdet.receiveAmountLocal),0) from erp_custreceivepaymentdet INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.companyID = erp_custreceivepaymentdet.companyID AND erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID WHERE erp_matchdocumentmaster.matchingConfirmedYN = 1 AND erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_generalledger.documentSystemCode AND erp_matchdocumentmaster.documentSystemID = erp_generalledger.documentSystemID GROUP BY erp_custreceivepaymentdet.custReceivePaymentAutoID),0)) 
        ELSE SUM(erp_generalledger.documentLocalAmount)
    END AS documentLocalAmount2,
    CASE 
        WHEN erp_generalledger.documentSystemID = 19 THEN 
        (SUM(erp_generalledger.documentTransAmount) + IFNULL((SELECT IFNULL(SUM(erp_custreceivepaymentdet.receiveAmountTrans),0) from erp_custreceivepaymentdet INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.companyID = erp_custreceivepaymentdet.companyID AND erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID WHERE erp_matchdocumentmaster.matchingConfirmedYN = 1 AND erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_generalledger.documentSystemCode AND erp_matchdocumentmaster.documentSystemID = erp_generalledger.documentSystemID GROUP BY erp_custreceivepaymentdet.custReceivePaymentAutoID),0))
        ELSE SUM(erp_generalledger.documentTransAmount)
    END AS documentTransAmount2,
    CASE 
        WHEN erp_generalledger.documentSystemID = 19 THEN 
        (SUM(erp_generalledger.documentRptAmount) + IFNULL((SELECT IFNULL(SUM(erp_custreceivepaymentdet.receiveAmountRpt),0) from erp_custreceivepaymentdet INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.companyID = erp_custreceivepaymentdet.companyID AND erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID WHERE erp_matchdocumentmaster.matchingConfirmedYN = 1 AND erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_generalledger.documentSystemCode AND erp_matchdocumentmaster.documentSystemID = erp_generalledger.documentSystemID GROUP BY erp_custreceivepaymentdet.custReceivePaymentAutoID),0)) 
        ELSE SUM(erp_generalledger.documentRptAmount)
    END AS documentRptAmount2,
    erp_generalledger.documentRptCurrencyID,
    currRpt.CurrencyCode as documentRptCurrency,
    currRpt.DecimalPlaces as documentRptDecimalPlaces,
    SUM(erp_generalledger.documentRptAmount) as documentRptAmount,
    erp_generalledger.documentType,
    CONCAT(customermaster.CutomerCode," - ",customermaster.CustomerName) as customerName,
    customermaster.CustomerName as customerName2,
    customermaster.CutomerCode,
    customermaster.creditDays,
    erp_custinvoicedirect.PONumber,
    erp_custinvoicedirect.invoiceDueDate,
    serviceline.ServiceLineDes AS serviceLineName
FROM
    erp_generalledger
    LEFT JOIN currencymaster currTrans ON erp_generalledger.documentTransCurrencyID = currTrans.currencyID
    LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
    LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
    LEFT JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
    LEFT JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
    LEFT JOIN serviceline ON erp_generalledger.serviceLineSystemID = serviceline.serviceLineSystemID
    LEFT JOIN erp_custinvoicedirect ON erp_generalledger.documentSystemCode = erp_custinvoicedirect.custInvoiceDirectAutoID AND erp_generalledger.documentSystemID = erp_custinvoicedirect.documentSystemiD AND erp_generalledger.companySystemID = erp_custinvoicedirect.companySystemID
    LEFT JOIN  (            
            SELECT customerinvoicecollectiondetail.customerInvoiceID, customerinvoicecollectiondetail.comments FROM erp_customerinvoicecollectiondetail  customerinvoicecollectiondetail
            JOIN (
                SELECT max(collectionDetailID) AS maxcollectionDetailID, customerInvoiceID  FROM `erp_customerinvoicecollectiondetail` #where customerInvoiceID = 63415 
                GROUP BY customerInvoiceID
            ) AS lastCollectionTrackerData on customerinvoicecollectiondetail.collectionDetailID = lastCollectionTrackerData.maxcollectionDetailID AND customerinvoicecollectiondetail.comments IS NOT NULL
    ) AS collectionTrackerDetail
    ON erp_custinvoicedirect.custInvoiceDirectAutoID = collectionTrackerDetail.customerInvoiceID
WHERE
    ( erp_generalledger.documentSystemID = "20" OR erp_generalledger.documentSystemID = "19" OR erp_generalledger.documentSystemID = "21" )
    AND DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
    AND ( erp_generalledger.chartOfAccountSystemID IN (' . join(',', $controlAccountsSystemID) . '))
    AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') 
    AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
    GROUP BY erp_generalledger.companySystemID, erp_generalledger.supplierCodeSystem,erp_generalledger.chartOfAccountSystemID,erp_generalledger.documentSystemID,erp_generalledger.documentSystemCode
    ) AS mainQuery
        LEFT JOIN (
    SELECT 
        salesreturndetails.custInvoiceDirectAutoID,
        salesreturndetails.salesReturnID,
        salesreturndetails.companySystemID,
        sum(salesreturndetails.transactionAmount + (salesreturndetails.transactionAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnTransactionAmount,
        sum(salesreturndetails.companyLocalAmount + (salesreturndetails.companyLocalAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnLocalAmount,
        sum(salesreturndetails.companyReportingAmount + (salesreturndetails.companyReportingAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnRptAmount
        FROM 
            salesreturndetails
            LEFT JOIN salesreturn ON salesReturnID = salesreturn.id
        WHERE
            salesreturndetails.companySystemID IN (' . join(',', $companyID) . ')
            AND salesreturn.approvedYN = -1
            GROUP BY salesreturndetails.custInvoiceDirectAutoID
    ) srInvoiced ON srInvoiced.custInvoiceDirectAutoID = mainQuery.documentSystemCode AND mainQuery.documentSystemID = 20
     LEFT JOIN (
    SELECT 
       salesreturndetails.deliveryOrderDetailID,
       erp_customerinvoiceitemdetails.custInvoiceDirectAutoID,
       salesreturndetails.salesReturnID,
       salesreturndetails.companySystemID,
       sum(salesreturndetails.transactionAmount + (salesreturndetails.transactionAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEOTransactionAmount,
       sum(salesreturndetails.companyLocalAmount + (salesreturndetails.companyLocalAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEOLocalAmount,
       sum(salesreturndetails.companyReportingAmount + (salesreturndetails.companyReportingAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEORptAmount
       FROM 
            salesreturndetails
            LEFT JOIN salesreturn ON salesReturnID = salesreturn.id
            INNER JOIN erp_customerinvoiceitemdetails ON salesreturndetails.deliveryOrderDetailID = erp_customerinvoiceitemdetails.deliveryOrderDetailID
       WHERE
            salesreturndetails.companySystemID IN (' . join(',', $companyID) . ')
            AND salesreturn.approvedYN = -1
            AND salesreturndetails.deliveryOrderDetailID <> 0
            GROUP BY salesreturndetails.deliveryOrderDetailID
    ) srDEO ON srDEO.custInvoiceDirectAutoID = mainQuery.documentSystemCode AND mainQuery.documentSystemID = 20       
    LEFT JOIN (
    SELECT
        erp_matchdocumentmaster.companySystemID,
        erp_matchdocumentmaster.documentSystemID,
        erp_matchdocumentmaster.PayMasterAutoId,
        erp_matchdocumentmaster.BPVcode,
        sum( erp_custreceivepaymentdet.receiveAmountTrans ) AS MatchedBRVTransAmount,
        sum( erp_custreceivepaymentdet.receiveAmountLocal ) AS MatchedBRVLocalAmount,
        sum( erp_custreceivepaymentdet.receiveAmountRpt ) AS MatchedBRVRptAmount
    FROM
        erp_matchdocumentmaster
        INNER JOIN erp_custreceivepaymentdet ON erp_matchdocumentmaster.companyID = erp_custreceivepaymentdet.companyID
        AND erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID
    WHERE
        erp_matchdocumentmaster.matchingConfirmedYN = 1
        AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '"
        AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
        AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
    GROUP BY
        erp_matchdocumentmaster.PayMasterAutoId,
        erp_matchdocumentmaster.BPVcode
    ) AS matchedBRV ON mainQuery.documentSystemID = matchedBRV.documentSystemID
    AND mainQuery.companySystemID = matchedBRV.companySystemID
    AND matchedBRV.PayMasterAutoId = mainQuery.documentSystemCode
    LEFT JOIN (
    SELECT
        erp_customerreceivepayment.custReceivePaymentAutoID,
        erp_customerreceivepayment.companySystemID,
        erp_customerreceivepayment.documentSystemID,
        erp_customerreceivepayment.custPaymentReceiveCode,
        sum( erp_custreceivepaymentdet.receiveAmountTrans ) AS BRVTransAmount,
        sum( erp_custreceivepaymentdet.receiveAmountLocal ) AS BRVLocalAmount,
        sum( erp_custreceivepaymentdet.receiveAmountRpt ) AS BRVRptAmount
    FROM
        erp_customerreceivepayment
        INNER JOIN erp_custreceivepaymentdet ON erp_customerreceivepayment.custReceivePaymentAutoID = erp_custreceivepaymentdet.custReceivePaymentAutoID
    WHERE
        erp_custreceivepaymentdet.bookingInvCode <> "0"
        AND erp_custreceivepaymentdet.matchingDocID = 0
        AND erp_customerreceivepayment.approved =- 1
        AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
        AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '"
        AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
    GROUP BY
        custReceivePaymentAutoID
    ) AS InvoicedBRV ON mainQuery.documentSystemID = InvoicedBRV.documentSystemID
    AND mainQuery.documentSystemCode = InvoicedBRV.custReceivePaymentAutoID
    LEFT JOIN (
    SELECT
        companySystemID,
        companyID,
        addedDocumentSystemID,
        addedDocumentID,
        bookingInvCodeSystem,
        bookingInvCode,
        sum( receiveAmountTrans ) AS InvoiceTransAmount,
        sum( receiveAmountLocal ) AS InvoiceLocalAmount,
        sum( receiveAmountRpt ) AS InvoiceRptAmount
    FROM
        (
        SELECT
            *
        FROM
            (
            SELECT
                erp_customerreceivepayment.custPaymentReceiveCode,
                erp_custreceivepaymentdet.companySystemID,
                erp_custreceivepaymentdet.companyID,
                erp_custreceivepaymentdet.addedDocumentSystemID,
                erp_custreceivepaymentdet.addedDocumentID,
                erp_custreceivepaymentdet.bookingInvCodeSystem,
                erp_custreceivepaymentdet.bookingInvCode,
                erp_custreceivepaymentdet.receiveAmountTrans,
                erp_custreceivepaymentdet.receiveAmountLocal,
                erp_custreceivepaymentdet.receiveAmountRpt
            FROM
                erp_customerreceivepayment
                INNER JOIN erp_custreceivepaymentdet ON erp_customerreceivepayment.custReceivePaymentAutoID = erp_custreceivepaymentdet.custReceivePaymentAutoID
                AND erp_custreceivepaymentdet.matchingDocID = 0
                AND erp_customerreceivepayment.approved =- 1
            WHERE
                erp_custreceivepaymentdet.bookingInvCode <> "0"
                AND erp_custreceivepaymentdet.matchingDocID = 0
                AND erp_customerreceivepayment.approved =- 1
                AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
                AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '"
                AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
            ) AS InvoiceFromBRV UNION ALL
        SELECT
            *
        FROM
            (
            SELECT
                erp_matchdocumentmaster.matchingDocCode,
                erp_custreceivepaymentdet.companySystemID,
                erp_custreceivepaymentdet.companyID,
                erp_custreceivepaymentdet.addedDocumentSystemID,
                erp_custreceivepaymentdet.addedDocumentID,
                erp_custreceivepaymentdet.bookingInvCodeSystem,
                erp_custreceivepaymentdet.bookingInvCode,
                erp_custreceivepaymentdet.receiveAmountTrans,
                erp_custreceivepaymentdet.receiveAmountLocal,
                erp_custreceivepaymentdet.receiveAmountRpt
            FROM
                erp_custreceivepaymentdet
                INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID
                AND erp_custreceivepaymentdet.companySystemID = erp_matchdocumentmaster.companySystemID
            WHERE
                erp_matchdocumentmaster.matchingConfirmedYN = 1
                AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
                AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '"
                AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
            ) AS InvoiceFromMatching
        ) AS InvoiceFromUNION
    GROUP BY
        bookingInvCode
    ) AS InvoiceFromBRVAndMatching ON InvoiceFromBRVAndMatching.addedDocumentSystemID = mainQuery.documentSystemID
    AND mainQuery.documentSystemCode = InvoiceFromBRVAndMatching.bookingInvCodeSystem
    LEFT JOIN (
    SELECT
        erp_custreceivepaymentdet.companySystemID,
        erp_custreceivepaymentdet.companyID,
        max( erp_custreceivepaymentdet.custReceivePaymentAutoID ) AS ReceiptSystemID,
        max( IF ( erp_custreceivepaymentdet.matchingDocID = 0 OR erp_custreceivepaymentdet.matchingDocID IS NULL, erp_customerreceivepayment.custPaymentReceiveCode, erp_matchdocumentmaster.matchingDocCode ) ) AS docCode,
        erp_custreceivepaymentdet.addedDocumentSystemID,
        erp_custreceivepaymentdet.addedDocumentID,
        erp_custreceivepaymentdet.bookingInvCodeSystem,
        sum( erp_custreceivepaymentdet.receiveAmountTrans ) AS SubsequentCollectionTransAmount,
        sum( erp_custreceivepaymentdet.receiveAmountLocal ) AS SubsequentCollectionLocalAmount,
        sum( erp_custreceivepaymentdet.receiveAmountRpt ) AS SubsequentCollectionRptAmount
    FROM
        erp_custreceivepaymentdet
        LEFT JOIN erp_customerreceivepayment ON erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
        LEFT JOIN erp_matchdocumentmaster ON erp_custreceivepaymentdet.matchingDocID = erp_matchdocumentmaster.matchDocumentMasterAutoID
    WHERE
        erp_custreceivepaymentdet.bookingInvCodeSystem > 0
        AND DATE(
                ( IF ( erp_custreceivepaymentdet.matchingDocID = 0 OR erp_custreceivepaymentdet.matchingDocID IS NULL, erp_customerreceivepayment.postedDate, erp_matchdocumentmaster.matchingDocdate ) )
            )
         > "' . $asOfDate . '"
        AND ( IF ( erp_custreceivepaymentdet.matchingDocID = 0 OR erp_custreceivepaymentdet.matchingDocID IS NULL, erp_customerreceivepayment.approved, erp_matchdocumentmaster.matchingConfirmedYN ) ) <> 0
    GROUP BY
        addedDocumentSystemID,
        bookingInvCodeSystem
    ) AS Subsequentcollection ON Subsequentcollection.addedDocumentSystemID = mainQuery.documentSystemID
    AND mainQuery.documentSystemCode = Subsequentcollection.bookingInvCodeSystem
    ) AS final
WHERE
' . $whereQry . ' <> 0) as grandFinal ORDER BY PostedDate ASC';
        $output = \DB::select($query);

        $output = collect($output)->filter(function ($item) {
            return !str_contains($item->DocumentNarration, 'Matching');
        });

        $excludedDocumentCodes = array_flatten($fullyMatchedDocuments);
        $filteredData = collect($output)->reject(function ($item) use ($excludedDocumentCodes) {
            return in_array($item->DocumentCode, $excludedDocumentCodes);
        });
        return ['data' => $filteredData, 'aging' => $aging];
    }


    public function getFullyMatchedInvoices($companyID,$asOfDate,$customerSystemID)
    {
        $invoiceQuery = 'SELECT ec2.bookingInvCode,ABS(ROUND(receivedAmount,3)) as receivedAmount ,ABS(matchedAmount) as matchedAmount,ROUND((ci.bookingAmountTrans + ci.VATAmount),3) as invoiceAmount from erp_customerreceivepayment ec 
        LEFT JOIN erp_matchdocumentmaster em ON ec.custReceivePaymentAutoID  = em.PayMasterAutoId  
        LEFT JOIN erp_custreceivepaymentdet ec2 ON ec2.matchingDocID  = em.matchDocumentMasterAutoID 
        LEFT JOIN erp_custinvoicedirect ci ON ci.custInvoiceDirectAutoID = ec2.bookingInvCodeSystem
        WHERE 
        custPaymentReceiveCode IS NOT NULL
        AND ec2.bookingInvCode IS NOT NULL
        AND ec.matchingConfirmedYN = -1
        AND ec2.companySystemID IN (' . join(',', $companyID) . ')
        AND DATE(ec.postedDate)  <= "' . $asOfDate . '"
        AND ec.customerID IN (' . join(',', $customerSystemID) . ')
        HAVING matchedAmount=invoiceAmount';

        $fullyMatchedInvoices = \DB::select($invoiceQuery);



        $brvQuery = 'SELECT custPaymentReceiveCode,ABS(ROUND(receivedAmount,3)) as receivedAmount ,ABS(matchedAmount) as matchedAmount,ROUND((ci.bookingAmountTrans + ci.VATAmount),3) as invoiceAmount from erp_customerreceivepayment ec
        LEFT JOIN erp_matchdocumentmaster em ON ec.custReceivePaymentAutoID  = em.PayMasterAutoId
        LEFT JOIN erp_custreceivepaymentdet ec2 ON ec2.matchingDocID  = em.matchDocumentMasterAutoID
        LEFT JOIN erp_custinvoicedirect ci ON ci.custInvoiceDirectAutoID = ec2.bookingInvCodeSystem
        WHERE
        custPaymentReceiveCode IS NOT NULL
        AND ec2.bookingInvCode IS NOT NULL
        AND ec.matchingConfirmedYN = -1
        AND ec2.companySystemID IN (' . join(',', $companyID) . ')
        AND DATE(ec.postedDate)  <= "' . $asOfDate . '"
        AND ec.customerID IN (' . join(',', $customerSystemID) . ')
        HAVING matchedAmount=receivedAmount';

        $fullyMatchedBrvs = \DB::select($brvQuery);


        // get fully paid credit notes

        $creditNoteQuery = 'SELECT 
            ec.creditNoteCode ,
            ABS(ROUND(ec.creditAmountTrans , 3)) AS receivedAmount,
            ABS(em.matchedAmount) AS matchedAmount,
            ABS(ROUND(ar.custInvoiceAmount,3)) as invoiceAmount
        FROM 
            erp_creditnote ec
        LEFT JOIN 
            erp_matchdocumentmaster em ON ec.creditNoteAutoID = em.PayMasterAutoId
        LEFT JOIN 
            erp_custreceivepaymentdet ec2 ON ec2.matchingDocID = em.matchDocumentMasterAutoID
        LEFT JOIN erp_accountsreceivableledger ar ON ar.arAutoID  = ec2.arAutoID
        WHERE
            ec.creditNoteAutoID IS NOT NULL
            AND em.matchingConfirmedYN = 1
            AND ec2.companySystemID IN (' . join(',', $companyID) . ')
            AND DATE(ec.postedDate)  <= "' . $asOfDate . '"
            AND ec.customerID IN (' . join(',', $customerSystemID) . ')
            HAVING matchedAmount=receivedAmount
        ';

        $fullyMatchedCreditNotes = \DB::select($creditNoteQuery);

        $array = array();
        foreach ($fullyMatchedBrvs as $item) {
            $array[][] = $item->custPaymentReceiveCode;
        }

        foreach ($fullyMatchedInvoices as $item) {
            $array[][] = $item->bookingInvCode;
        }


        foreach ($fullyMatchedCreditNotes as $item)
        {
            $array[][] = $item->creditNoteCode;
        }

        return $array;
    }

    function getCustomerAgingSummaryQRY($request)
    {
        $asOfDate = new Carbon($request->fromDate);
        $asOfDate = $asOfDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $customers = (array)$request->customers;
        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $controlAccounts = (array)$request->controlAccountsSystemID;
        $controlAccountsSystemID = collect($controlAccounts)->pluck('id')->toArray();

        $currency = $request->currencyID;

        $z = 1;
        $aging = array();
        $interval = $request->interval;
        $through = $request->through;
        $agingRange = range(0, $through, $interval);
        $rangeAmount = $interval;
        $agingAgeCount = count($agingRange);
        foreach ($agingRange as $val) {
            if ($z == $agingAgeCount) {
                $aging[] = $val + 1 . "-" . $through;
            } else {
                if ($z == 1) {
                    $aging[] = $val . "-" . $rangeAmount;
                } else {
                    $aging[] = $val + 1 . "-" . $rangeAmount;
                }
                $rangeAmount += $interval;
            }
            $z++;
        }

        $aging[] = "> " . ($through);
        $agingField = '';
        if (!empty($aging)) { /*calculate aging range in query*/
            $count = count($aging);
            $c = 1;
            foreach ($aging as $val) {
                if ($count == $c) {
                    $agingField .= "SUM(if(grandFinal.age > " . $through . ",grandFinal.balanceAmount,0)) as `" . $val . "`,";
                } else {
                    $list = explode("-", $val);
                    $agingField .= "SUM(if(grandFinal.age >= " . $list[0] . " AND grandFinal.age <= " . $list[1] . ",grandFinal.balanceAmount,0)) as `" . $val . "`,";
                }
                $c++;
            }
        }
        $agingField .= "SUM(if(grandFinal.age <= 0,grandFinal.balanceAmount,0)) as `current`";


        $currencyQry = '';
        $amountQry = '';
        $decimalPlaceQry = '';
        $whereQry = '';
        if ($currency == 1) {
            $currencyQry = "final.documentTransCurrency AS documentCurrency";
            $amountQry = "round( final.balanceTrans, final.documentTransDecimalPlaces ) AS balanceAmount";
            $decimalPlaceQry = "final.documentTransDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( final.balanceTrans, final.documentTransDecimalPlaces )";
        } else if ($currency == 2) {
            $currencyQry = "final.documentLocalCurrency AS documentCurrency";
            $amountQry = "round( final.balanceLocal, final.documentLocalDecimalPlaces ) AS balanceAmount";
            $decimalPlaceQry = "final.documentLocalDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( final.balanceLocal, final.documentLocalDecimalPlaces )";
        } else {
            $currencyQry = "final.documentRptCurrency AS documentCurrency";
            $amountQry = "round( final.balanceRpt, final.documentRptDecimalPlaces ) AS balanceAmount";
            $decimalPlaceQry = "final.documentRptDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( final.balanceRpt, final.documentRptDecimalPlaces )";
        }
        $currencyID = $request->currencyID;
        $fullyMatchedDocuments = $this->getFullyMatchedInvoices($companyID,$asOfDate,$customerSystemID);

        $output = \DB::select('SELECT DocumentCode,PostedDate,DocumentNarration,Contract,invoiceNumber,InvoiceDate,' . $agingField . ',documentCurrency,balanceDecimalPlaces,CustomerName, creditDays,CustomerCode,customerCodeSystem,companyID,CompanyName,concatCompanyName FROM (SELECT
    final.documentCode AS DocumentCode,
    final.documentDate AS PostedDate,
    final.documentNarration AS DocumentNarration,
    final.clientContractID AS Contract,
    final.invoiceNumber AS invoiceNumber,
    final.invoiceDate AS InvoiceDate,
    ' . $amountQry . ',
    ' . $currencyQry . ',
    ' . $decimalPlaceQry . ',
    final.CustomerName,
    final.creditDays,
    final.CutomerCode as CustomerCode,
    final.supplierCodeSystem AS customerCodeSystem,
    DATEDIFF("' . $asOfDate . '",DATE(final.documentDate)) as age,
    final.companyID, 
    final.CompanyName,
    CONCAT(final.companyID," - " ,final.CompanyName) as concatCompanyName
FROM
    (
SELECT
    mainQuery.companySystemID,
    mainQuery.companyID,
    mainQuery.CompanyName,
    mainQuery.serviceLineSystemID,
    mainQuery.serviceLineCode,
    mainQuery.documentSystemID,
    mainQuery.documentID,
    mainQuery.documentSystemCode,
    mainQuery.documentCode,
    mainQuery.documentDate,
    mainQuery.documentDateFilter,
    mainQuery.invoiceNumber,
    mainQuery.invoiceDate,
    mainQuery.chartOfAccountSystemID,
    mainQuery.glCode,
    mainQuery.documentNarration,
    mainQuery.clientContractID,
    mainQuery.supplierCodeSystem,
    mainQuery.documentTransCurrencyID,
    mainQuery.documentTransCurrency,
    mainQuery.documentTransAmount,
    mainQuery.documentTransDecimalPlaces,
    mainQuery.documentLocalCurrencyID,
    mainQuery.documentLocalCurrency,
    mainQuery.documentLocalAmount,
    mainQuery.documentLocalDecimalPlaces,
    mainQuery.documentRptCurrencyID,
    mainQuery.documentRptCurrency,
    mainQuery.documentRptAmount,
    mainQuery.documentRptDecimalPlaces,
IF( matchedBRV.MatchedBRVTransAmount IS NULL, 0, matchedBRV.MatchedBRVTransAmount ) AS MatchedBRVTransAmount,
IF( matchedBRV.MatchedBRVLocalAmount IS NULL, 0, matchedBRV.MatchedBRVLocalAmount ) AS MatchedBRVLocalAmount,
IF( matchedBRV.MatchedBRVRptAmount IS NULL, 0, matchedBRV.MatchedBRVRptAmount ) AS MatchedBRVRptAmount,
IF( InvoicedBRV.BRVTransAmount IS NULL, 0, InvoicedBRV.BRVTransAmount ) AS BRVTransAmount,
IF( InvoicedBRV.BRVLocalAmount IS NULL, 0, InvoicedBRV.BRVLocalAmount ) AS BRVLocalAmount,
IF( InvoicedBRV.BRVRptAmount IS NULL, 0, InvoicedBRV.BRVRptAmount ) AS BRVRptAmount,
IF( InvoiceFromBRVAndMatching.InvoiceTransAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceTransAmount *- 1 ) AS InvoiceTransAmount,
IF( InvoiceFromBRVAndMatching.InvoiceLocalAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceLocalAmount *- 1 ) AS InvoiceLocalAmount,
IF( InvoiceFromBRVAndMatching.InvoiceRptAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceRptAmount *- 1 ) AS InvoiceRptAmount,
	(
	mainQuery.documentRptAmount2 + ( IF ( InvoicedBRV.BRVRptAmount IS NULL, 0, InvoicedBRV.BRVRptAmount ) ) + ( IF ( srInvoiced.sumReturnRptAmount IS NULL, 0, srInvoiced.sumReturnRptAmount * -1) ) + ( IF ( srDEO.sumReturnDEORptAmount IS NULL, 0, srDEO.sumReturnDEORptAmount * -1) )  + ( IF ( InvoiceFromBRVAndMatching.InvoiceRptAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceRptAmount *- 1 ) ) 
	) AS balanceRpt,
	(
	mainQuery.documentLocalAmount2 + ( IF ( InvoicedBRV.BRVLocalAmount IS NULL, 0, InvoicedBRV.BRVLocalAmount ) ) + ( IF ( srInvoiced.sumReturnLocalAmount IS NULL, 0, srInvoiced.sumReturnLocalAmount * -1) ) + ( IF ( srDEO.sumReturnDEOLocalAmount IS NULL, 0, srDEO.sumReturnDEOLocalAmount * -1) )  + ( IF ( InvoiceFromBRVAndMatching.InvoiceLocalAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceLocalAmount *- 1 ) ) 
	) AS balanceLocal,
	(
	mainQuery.documentTransAmount2 + ( IF ( InvoicedBRV.BRVTransAmount IS NULL, 0, InvoicedBRV.BRVTransAmount ) ) + ( IF ( srInvoiced.sumReturnTransactionAmount IS NULL, 0, srInvoiced.sumReturnTransactionAmount * -1) ) + ( IF ( srDEO.sumReturnDEOTransactionAmount IS NULL, 0, srDEO.sumReturnDEOTransactionAmount * -1) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceTransAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceTransAmount *- 1 ) ) 
	) AS balanceTrans,
	mainQuery.CustomerName,
    mainQuery.creditDays,
    mainQuery.CutomerCode
FROM
    (
SELECT
	erp_generalledger.companySystemID,
	erp_generalledger.companyID,
	companymaster.CompanyName,
	erp_generalledger.serviceLineSystemID,
	erp_generalledger.serviceLineCode,
	erp_generalledger.documentSystemID,
	erp_generalledger.documentID,
	erp_generalledger.documentSystemCode,
	erp_generalledger.documentCode,
    collectionTrackerDetail.comments,
	erp_generalledger.documentDate,
	DATE_FORMAT( documentDate, "%d/%m/%Y" ) AS documentDateFilter,
	erp_generalledger.documentYear,
	erp_generalledger.documentMonth,
	erp_generalledger.chequeNumber,
	erp_generalledger.invoiceNumber,
	erp_generalledger.invoiceDate,
	erp_generalledger.chartOfAccountSystemID,
	erp_generalledger.glCode,
	erp_generalledger.documentNarration,
	erp_generalledger.clientContractID,
	erp_generalledger.supplierCodeSystem,
	erp_generalledger.documentTransCurrencyID,
	currTrans.CurrencyCode as documentTransCurrency,
	currTrans.DecimalPlaces as documentTransDecimalPlaces,
	SUM(erp_generalledger.documentTransAmount) as documentTransAmount,
	erp_generalledger.documentLocalCurrencyID,
	currLocal.CurrencyCode as documentLocalCurrency,
	currLocal.DecimalPlaces as documentLocalDecimalPlaces,
	SUM(erp_generalledger.documentLocalAmount) as documentLocalAmount,
	CASE 
        WHEN erp_generalledger.documentSystemID = 19 THEN 
        (SUM(erp_generalledger.documentLocalAmount) + IFNULL((SELECT IFNULL(SUM(erp_custreceivepaymentdet.receiveAmountLocal),0) from erp_custreceivepaymentdet INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.companyID = erp_custreceivepaymentdet.companyID AND erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID WHERE erp_matchdocumentmaster.matchingConfirmedYN = 1 AND erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_generalledger.documentSystemCode AND erp_matchdocumentmaster.documentSystemID = erp_generalledger.documentSystemID GROUP BY erp_custreceivepaymentdet.custReceivePaymentAutoID),0)) 
        ELSE SUM(erp_generalledger.documentLocalAmount)
    END AS documentLocalAmount2,
	CASE 
        WHEN erp_generalledger.documentSystemID = 19 THEN 
        (SUM(erp_generalledger.documentTransAmount) + IFNULL((SELECT IFNULL(SUM(erp_custreceivepaymentdet.receiveAmountTrans),0) from erp_custreceivepaymentdet INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.companyID = erp_custreceivepaymentdet.companyID AND erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID WHERE erp_matchdocumentmaster.matchingConfirmedYN = 1 AND erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_generalledger.documentSystemCode AND erp_matchdocumentmaster.documentSystemID = erp_generalledger.documentSystemID GROUP BY erp_custreceivepaymentdet.custReceivePaymentAutoID),0))
        ELSE SUM(erp_generalledger.documentTransAmount)
    END AS documentTransAmount2,
	CASE 
        WHEN erp_generalledger.documentSystemID = 19 THEN 
        (SUM(erp_generalledger.documentRptAmount) + IFNULL((SELECT IFNULL(SUM(erp_custreceivepaymentdet.receiveAmountRpt),0) from erp_custreceivepaymentdet INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.companyID = erp_custreceivepaymentdet.companyID AND erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID WHERE erp_matchdocumentmaster.matchingConfirmedYN = 1 AND erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_generalledger.documentSystemCode AND erp_matchdocumentmaster.documentSystemID = erp_generalledger.documentSystemID GROUP BY erp_custreceivepaymentdet.custReceivePaymentAutoID),0)) 
        ELSE SUM(erp_generalledger.documentRptAmount)
    END AS documentRptAmount2,
	erp_generalledger.documentRptCurrencyID,
	currRpt.CurrencyCode as documentRptCurrency,
	currRpt.DecimalPlaces as documentRptDecimalPlaces,
	SUM(erp_generalledger.documentRptAmount) as documentRptAmount,
	erp_generalledger.documentType,
	CONCAT(customermaster.CutomerCode," - ",customermaster.CustomerName) as customerName,
	customermaster.CustomerName as customerName2,
	customermaster.CutomerCode,
    customermaster.creditDays,
	erp_custinvoicedirect.PONumber,
	erp_custinvoicedirect.invoiceDueDate
FROM
	erp_generalledger 
	LEFT JOIN currencymaster currTrans ON erp_generalledger.documentTransCurrencyID = currTrans.currencyID
	LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
	LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
	LEFT JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
	LEFT JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
	LEFT JOIN erp_custinvoicedirect ON erp_generalledger.documentSystemCode = erp_custinvoicedirect.custInvoiceDirectAutoID AND erp_generalledger.documentSystemID = erp_custinvoicedirect.documentSystemiD AND erp_generalledger.companySystemID = erp_custinvoicedirect.companySystemID
    LEFT JOIN  (            
            SELECT customerinvoicecollectiondetail.customerInvoiceID, customerinvoicecollectiondetail.comments FROM erp_customerinvoicecollectiondetail  customerinvoicecollectiondetail
            JOIN (
                SELECT max(collectionDetailID) AS maxcollectionDetailID, customerInvoiceID  FROM `erp_customerinvoicecollectiondetail` #where customerInvoiceID = 63415 
                GROUP BY customerInvoiceID
            ) AS lastCollectionTrackerData on customerinvoicecollectiondetail.collectionDetailID = lastCollectionTrackerData.maxcollectionDetailID AND customerinvoicecollectiondetail.comments IS NOT NULL
    ) AS collectionTrackerDetail
    ON erp_custinvoicedirect.custInvoiceDirectAutoID = collectionTrackerDetail.customerInvoiceID
WHERE
    ( erp_generalledger.documentSystemID = "20" OR erp_generalledger.documentSystemID = "19" OR erp_generalledger.documentSystemID = "21" ) 
    AND DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
    AND ( erp_generalledger.chartOfAccountSystemID IN (' . join(',', $controlAccountsSystemID) . '))
    AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') 
    AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
    GROUP BY erp_generalledger.companySystemID, erp_generalledger.supplierCodeSystem,erp_generalledger.chartOfAccountSystemID,erp_generalledger.documentSystemID,erp_generalledger.documentSystemCode
    ) AS mainQuery
            LEFT JOIN (
    SELECT 
        salesreturndetails.custInvoiceDirectAutoID,
        salesreturndetails.salesReturnID,
        salesreturndetails.companySystemID,
        sum(salesreturndetails.transactionAmount + (salesreturndetails.transactionAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnTransactionAmount,
        sum(salesreturndetails.companyLocalAmount + (salesreturndetails.companyLocalAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnLocalAmount,
        sum(salesreturndetails.companyReportingAmount + (salesreturndetails.companyReportingAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnRptAmount
        FROM 
            salesreturndetails
            LEFT JOIN salesreturn ON salesReturnID = salesreturn.id
        WHERE
            salesreturndetails.companySystemID IN (' . join(',', $companyID) . ')
            AND salesreturn.approvedYN = -1
            GROUP BY salesreturndetails.custInvoiceDirectAutoID
    ) srInvoiced ON srInvoiced.custInvoiceDirectAutoID = mainQuery.documentSystemCode AND mainQuery.documentSystemID = 20
     LEFT JOIN (
    SELECT 
       salesreturndetails.deliveryOrderDetailID,
       erp_customerinvoiceitemdetails.custInvoiceDirectAutoID,
       salesreturndetails.salesReturnID,
       salesreturndetails.companySystemID,
       sum(salesreturndetails.transactionAmount + (salesreturndetails.transactionAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEOTransactionAmount,
       sum(salesreturndetails.companyLocalAmount + (salesreturndetails.companyLocalAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEOLocalAmount,
       sum(salesreturndetails.companyReportingAmount + (salesreturndetails.companyReportingAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEORptAmount
       FROM 
            salesreturndetails
            LEFT JOIN salesreturn ON salesReturnID = salesreturn.id
            INNER JOIN erp_customerinvoiceitemdetails ON salesreturndetails.deliveryOrderDetailID = erp_customerinvoiceitemdetails.deliveryOrderDetailID
       WHERE
            salesreturndetails.companySystemID IN (' . join(',', $companyID) . ')
            AND salesreturn.approvedYN = -1
            AND salesreturndetails.deliveryOrderDetailID <> 0
            GROUP BY salesreturndetails.deliveryOrderDetailID
    ) srDEO ON srDEO.custInvoiceDirectAutoID = mainQuery.documentSystemCode AND mainQuery.documentSystemID = 20     
    LEFT JOIN (
    SELECT
        erp_matchdocumentmaster.companySystemID,
        erp_matchdocumentmaster.documentSystemID,
        erp_matchdocumentmaster.PayMasterAutoId,
        erp_matchdocumentmaster.BPVcode,
        sum( erp_custreceivepaymentdet.receiveAmountTrans ) AS MatchedBRVTransAmount,
        sum( erp_custreceivepaymentdet.receiveAmountLocal ) AS MatchedBRVLocalAmount,
        sum( erp_custreceivepaymentdet.receiveAmountRpt ) AS MatchedBRVRptAmount 
    FROM
        erp_matchdocumentmaster
        INNER JOIN erp_custreceivepaymentdet ON erp_matchdocumentmaster.companyID = erp_custreceivepaymentdet.companyID 
        AND erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID 
    WHERE
        erp_matchdocumentmaster.matchingConfirmedYN = 1 
        AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '" 
        AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
        AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
    GROUP BY
        erp_matchdocumentmaster.PayMasterAutoId,
        erp_matchdocumentmaster.BPVcode 
    ) AS matchedBRV ON mainQuery.documentSystemID = matchedBRV.documentSystemID 
    AND mainQuery.companySystemID = matchedBRV.companySystemID 
    AND matchedBRV.PayMasterAutoId = mainQuery.documentSystemCode
    LEFT JOIN (
    SELECT
        erp_customerreceivepayment.custReceivePaymentAutoID,
        erp_customerreceivepayment.companySystemID,
        erp_customerreceivepayment.documentSystemID,
        erp_customerreceivepayment.custPaymentReceiveCode,
        sum( erp_custreceivepaymentdet.receiveAmountTrans ) AS BRVTransAmount,
        sum( erp_custreceivepaymentdet.receiveAmountLocal ) AS BRVLocalAmount,
        sum( erp_custreceivepaymentdet.receiveAmountRpt ) AS BRVRptAmount 
    FROM
        erp_customerreceivepayment
        INNER JOIN erp_custreceivepaymentdet ON erp_customerreceivepayment.custReceivePaymentAutoID = erp_custreceivepaymentdet.custReceivePaymentAutoID 
    WHERE
        erp_custreceivepaymentdet.bookingInvCode <> "0" 
        AND erp_custreceivepaymentdet.matchingDocID = 0 
        AND erp_customerreceivepayment.approved =- 1 
        AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
        AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '"
        AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
    GROUP BY
        custReceivePaymentAutoID 
    ) AS InvoicedBRV ON mainQuery.documentSystemID = InvoicedBRV.documentSystemID 
    AND mainQuery.documentSystemCode = InvoicedBRV.custReceivePaymentAutoID
    LEFT JOIN (
    SELECT
        companySystemID,
        companyID,
        addedDocumentSystemID,
        addedDocumentID,
        bookingInvCodeSystem,
        bookingInvCode,
        sum( receiveAmountTrans ) AS InvoiceTransAmount,
        sum( receiveAmountLocal ) AS InvoiceLocalAmount,
        sum( receiveAmountRpt ) AS InvoiceRptAmount 
    FROM
        (
        SELECT
            * 
        FROM
            (
            SELECT
                erp_customerreceivepayment.custPaymentReceiveCode,
                erp_custreceivepaymentdet.companySystemID,
                erp_custreceivepaymentdet.companyID,
                erp_custreceivepaymentdet.addedDocumentSystemID,
                erp_custreceivepaymentdet.addedDocumentID,
                erp_custreceivepaymentdet.bookingInvCodeSystem,
                erp_custreceivepaymentdet.bookingInvCode,
                erp_custreceivepaymentdet.receiveAmountTrans,
                erp_custreceivepaymentdet.receiveAmountLocal,
                erp_custreceivepaymentdet.receiveAmountRpt 
            FROM
                erp_customerreceivepayment
                INNER JOIN erp_custreceivepaymentdet ON erp_customerreceivepayment.custReceivePaymentAutoID = erp_custreceivepaymentdet.custReceivePaymentAutoID 
                AND erp_custreceivepaymentdet.matchingDocID = 0 
                AND erp_customerreceivepayment.approved =- 1 
            WHERE
                erp_custreceivepaymentdet.bookingInvCode <> "0" 
                AND erp_custreceivepaymentdet.matchingDocID = 0 
                AND erp_customerreceivepayment.approved =- 1 
                AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
                AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '" 
                AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
            ) AS InvoiceFromBRV UNION ALL
        SELECT
            * 
        FROM
            (
            SELECT
                erp_matchdocumentmaster.matchingDocCode,
                erp_custreceivepaymentdet.companySystemID,
                erp_custreceivepaymentdet.companyID,
                erp_custreceivepaymentdet.addedDocumentSystemID,
                erp_custreceivepaymentdet.addedDocumentID,
                erp_custreceivepaymentdet.bookingInvCodeSystem,
                erp_custreceivepaymentdet.bookingInvCode,
                erp_custreceivepaymentdet.receiveAmountTrans,
                erp_custreceivepaymentdet.receiveAmountLocal,
                erp_custreceivepaymentdet.receiveAmountRpt 
            FROM
                erp_custreceivepaymentdet
                INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID 
                AND erp_custreceivepaymentdet.companySystemID = erp_matchdocumentmaster.companySystemID 
            WHERE
                erp_matchdocumentmaster.matchingConfirmedYN = 1 
                AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
                AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '" 
                AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
            ) AS InvoiceFromMatching 
        ) AS InvoiceFromUNION 
    GROUP BY
        bookingInvCode 
    ) AS InvoiceFromBRVAndMatching ON InvoiceFromBRVAndMatching.addedDocumentSystemID = mainQuery.documentSystemID 
    AND mainQuery.documentSystemCode = InvoiceFromBRVAndMatching.bookingInvCodeSystem 
    ) AS final 
WHERE
' . $whereQry . ' <> 0 ORDER BY PostedDate ASC) as grandFinal GROUP BY customerCodeSystem,companyID ORDER BY CustomerName');


        $output = collect($output)->filter(function ($item) {
            return !str_contains($item->DocumentNarration, 'Matching');
        });

        $excludedDocumentCodes = array_flatten($fullyMatchedDocuments);
        $filteredData = collect($output)->reject(function ($item) use ($excludedDocumentCodes) {
            return in_array($item->DocumentCode, $excludedDocumentCodes);
        });

        return ['data' => $output, 'aging' => $aging];
    }

    // Customer Collection report
    function getCustomerCollectionQRY($request)
    {
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $customers = (array)$request->customers;

        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $currency = $request->currencyID;

        if ($currency == 1) {
            $currencyBRVAmount = "SUM( collectionDetail.BRVTransAmount) AS BRVDocumentAmount";
            $currencyCNAmount = "SUM( collectionDetail.CNTransAmount) AS CNDocumentAmount";
        } else if ($currency == 2) {
            $currencyBRVAmount = "SUM( collectionDetail.BRVLocalAmount) AS BRVDocumentAmount";
            $currencyCNAmount = "SUM( collectionDetail.CNLocalAmount) AS CNDocumentAmount";
        } else {
            $currencyBRVAmount = "SUM( collectionDetail.BRVRptAmount) AS BRVDocumentAmount";
            $currencyCNAmount = "SUM( collectionDetail.CNRptAmount) AS CNDocumentAmount";
        }

        $output = \DB::select('SELECT
    collectionDetail.companyID,
    collectionDetail.CompanyName,
    collectionDetail.CutomerCode,
    collectionDetail.CustomerName,
    ' . $currencyBRVAmount . ',
    ' . $currencyCNAmount . '
FROM
    (
        SELECT
            erp_generalledger.companyID,
            erp_generalledger.documentID,
            erp_generalledger.serviceLineCode,
            erp_generalledger.documentSystemCode,
            erp_generalledger.documentCode,
            erp_generalledger.documentDate,
            MONTH (
                erp_generalledger.documentDate
            ) AS DocMONTH,
            YEAR (
                erp_generalledger.documentDate
            ) AS DocYEAR,
            erp_generalledger.supplierCodeSystem,
            customermaster.CutomerCode,
            customermaster.customerShortCode,
            customermaster.CustomerName,
            companymaster.CompanyName,

        IF (
            erp_generalledger.documentSystemID = "21",
            ROUND(documentTransAmount, 0),
            0
        ) BRVTransAmount,

    IF (
        erp_generalledger.documentSystemID = "21",
        ROUND(documentLocalAmount, 0),
        0
    ) BRVLocalAmount,

IF (
    erp_generalledger.documentSystemID = "21",
    ROUND(documentRptAmount, 0),
    0
) BRVRptAmount,

IF (
    erp_generalledger.documentSystemID = "19",
    ROUND(documentTransAmount, 0),
    0
) CNTransAmount,

IF (
    erp_generalledger.documentSystemID = "19",
    ROUND(documentLocalAmount, 0),
    0
) CNLocalAmount,

IF (
    erp_generalledger.documentSystemID = "19",
    ROUND(documentRptAmount, 0),
    0
) CNRptAmount
FROM
    erp_generalledger
INNER JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
WHERE
    (
        erp_generalledger.documentSystemID = 21
        OR erp_generalledger.documentSystemID = 19
    )
 AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
AND erp_generalledger.documentRptAmount > 0 AND erp_generalledger.glaccounttypeID=1
    ) AS collectionDetail
GROUP BY
    collectionDetail.companyID,
    collectionDetail.CutomerCode;');
        return $output;
    }


    function getCustomerLedgerTemplate1QRY($request)
    {
        $asOfDate = new Carbon($request->fromDate);
        $asOfDate = $asOfDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $customers = (array)$request->customers;
        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $controlAccountsSystemIDs = (array)$request->controlAccountsSystemID;
        $controlAccountsSystemID = collect($controlAccountsSystemIDs)->pluck('id')->toArray();

        $currency = $request->currencyID;
        $currencyQry = '';
        $invoiceAmountQry = '';
        $paidAmountQry = '';
        $balanceAmountQry = '';
        $decimalPlaceQry = '';
        if ($currency == 1) {
            $currencyQry = "final.documentTransCurrency AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( final.documentTransAmount, final.documentTransDecimalPlaces ),0) AS invoiceAmount";
            $paidAmountQry = "IFNULL(round( final.documentLocalAmount, final.documentLocalDecimalPlaces ),0) AS invoiceAmount";
            $paidAmountQry = "CASE 
        WHEN final.receivedAmountTrans IS NULL THEN 
            IFNULL(round( final.paidTransAmount, final.documentTransDecimalPlaces ),0)
        ELSE 
            final.receivedAmountTrans 
    END AS paidAmount
    ";
            $balanceAmountQry = "IFNULL(round( final.balanceTrans, final.documentTransDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "final.documentTransDecimalPlaces AS balanceDecimalPlaces";
        } else if ($currency == 2) {
            $currencyQry = "final.documentLocalCurrency AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( final.documentLocalAmount, final.documentLocalDecimalPlaces ),0) AS invoiceAmount";
            $paidAmountQry = "CASE 
        WHEN final.receivedAmountLocal IS NULL THEN 
            IFNULL(ROUND(final.paidLocalAmount, final.documentLocalDecimalPlaces), 0) 
        ELSE 
            final.receivedAmountLocal 
    END AS paidAmount";
            $balanceAmountQry = "IFNULL(round( final.balanceLocal, final.documentLocalDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "final.documentLocalDecimalPlaces AS balanceDecimalPlaces";
        } else {
            $currencyQry = "final.documentRptCurrency AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( final.documentRptAmount, final.documentRptDecimalPlaces ),0) AS invoiceAmount";
            $paidAmountQry = "
            CASE 
        WHEN final.receivedAmountRpt IS NULL THEN 
            IFNULL(round( final.paidRptAmount, final.documentRptDecimalPlaces ),0)
        ELSE 
            final.receivedAmountRpt 
    END AS paidAmount";
            $balanceAmountQry = "IFNULL(round( final.balanceRpt, final.documentRptDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "final.documentRptDecimalPlaces AS balanceDecimalPlaces";
        }

        $query = 'SELECT
    final.documentCode AS DocumentCode,
    final.documentDate AS PostedDate,
    final.documentNarration AS DocumentNarration,
    final.clientContractID AS Contract,
    final.invoiceNumber AS invoiceNumber,
    final.invoiceDate AS InvoiceDate,
    ' . $invoiceAmountQry . ',
    ' . $paidAmountQry . ',
    ' . $balanceAmountQry . ',
    ' . $currencyQry . ',
    ' . $decimalPlaceQry . ',
    final.concatCustomerName, 
    final.CutomerCode,
    final.CustomerName,  
    final.PONumber,
    DATEDIFF("' . $asOfDate . '",DATE(final.documentDate)) as ageDays,
    final.companyID,
    final.CompanyName,
    final.documentSystemCode,
    final.documentSystemID,
    final.chartOfAccountSystemID,
    final.AccountDescription,
    final.FullyMatched,
    final.receivedAmountLocal,
    final.receivedAmountRpt,
    final.receivedAmountTrans,
    final.documentLocalAmount,
    final.paidLocalAmount
FROM
    (
SELECT
    mainQuery.companySystemID,
    mainQuery.companyID,
    mainQuery.CompanyName,
    mainQuery.serviceLineSystemID,
    mainQuery.serviceLineCode,
    mainQuery.documentSystemID,
    mainQuery.documentID,
    mainQuery.documentSystemCode,
    mainQuery.documentCode,
    mainQuery.documentDate,
    mainQuery.documentDateFilter,
    mainQuery.invoiceNumber,
    mainQuery.invoiceDate,
    mainQuery.chartOfAccountSystemID as chartOfAccountSystemID,
    mainQuery.glCode,
    mainQuery.documentNarration,
    mainQuery.clientContractID,
    mainQuery.supplierCodeSystem,
    mainQuery.documentTransCurrencyID,
    mainQuery.documentTransCurrency,
    mainQuery.documentTransAmount,
    mainQuery.documentTransDecimalPlaces,
    mainQuery.documentLocalCurrencyID,
    mainQuery.documentLocalCurrency,
    mainQuery.documentLocalAmount,
    mainQuery.documentLocalDecimalPlaces,
    mainQuery.documentRptCurrencyID,
    mainQuery.documentRptCurrency,
    mainQuery.documentRptAmount,
    mainQuery.documentRptDecimalPlaces,
IF( matchedBRV.MatchedBRVTransAmount IS NULL, 0, matchedBRV.MatchedBRVTransAmount ) AS MatchedBRVTransAmount,
IF( matchedBRV.MatchedBRVLocalAmount IS NULL, 0, matchedBRV.MatchedBRVLocalAmount ) AS MatchedBRVLocalAmount,
IF( matchedBRV.MatchedBRVRptAmount IS NULL, 0, matchedBRV.MatchedBRVRptAmount ) AS MatchedBRVRptAmount,
IF( InvoicedBRV.BRVTransAmount IS NULL, 0, InvoicedBRV.BRVTransAmount ) AS BRVTransAmount,
IF( InvoicedBRV.BRVLocalAmount IS NULL, 0, InvoicedBRV.BRVLocalAmount ) AS BRVLocalAmount,
IF( InvoicedBRV.BRVRptAmount IS NULL, 0, InvoicedBRV.BRVRptAmount ) AS BRVRptAmount,
IF( InvoiceFromBRVAndMatching.InvoiceTransAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceTransAmount *- 1 ) AS InvoiceTransAmount,
IF( InvoiceFromBRVAndMatching.InvoiceLocalAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceLocalAmount *- 1 ) AS InvoiceLocalAmount,
IF( InvoiceFromBRVAndMatching.InvoiceRptAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceRptAmount *- 1 ) AS InvoiceRptAmount,
IF( srInvoiced.sumReturnTransactionAmount IS NULL, 0, srInvoiced.sumReturnTransactionAmount * -1) AS sumReturnTransactionAmount,
IF( srInvoiced.sumReturnLocalAmount IS NULL, 0, srInvoiced.sumReturnLocalAmount * -1) AS sumReturnLocalAmount,
IF( srInvoiced.sumReturnRptAmount IS NULL, 0, srInvoiced.sumReturnRptAmount * -1) AS sumReturnRptAmount,
IF( srAmount.sumSRTransactionAmount IS NULL, 0, srAmount.sumSRTransactionAmount) AS sumSRTransactionAmount,
IF( srAmount.sumSRLocalAmount IS NULL, 0, srAmount.sumSRLocalAmount) AS sumSRLocalAmount,
IF( srAmount.sumSRRptAmount IS NULL, 0, srAmount.sumSRRptAmount) AS sumSRRptAmount,
IF( srDEO.sumReturnDEOTransactionAmount IS NULL, 0, srDEO.sumReturnDEOTransactionAmount * -1) AS sumReturnDEOTransactionAmount,
IF( srDEO.sumReturnDEOLocalAmount IS NULL, 0, srDEO.sumReturnDEOLocalAmount * -1) AS sumReturnDEOLocalAmount,
IF( srDEO.sumReturnDEORptAmount IS NULL, 0, srDEO.sumReturnDEORptAmount * -1) AS sumReturnDEORptAmount,
    (
    mainQuery.documentRptAmount + ( IF(mainQuery.receivedAmountRpt IS NULL,IF ( matchedBRV.MatchedBRVRptAmount IS NULL, 0, matchedBRV.MatchedBRVRptAmount ),mainQuery.receivedAmountRpt) ) + ( IF ( InvoicedBRV.BRVRptAmount IS NULL, 0, InvoicedBRV.BRVRptAmount ) ) + ( IF ( srInvoiced.sumReturnRptAmount IS NULL, 0, srInvoiced.sumReturnRptAmount * -1) ) + ( IF ( srAmount.sumSRRptAmount IS NULL, 0, srAmount.sumSRRptAmount) ) + ( IF ( srDEO.sumReturnDEORptAmount IS NULL, 0, srDEO.sumReturnDEORptAmount * -1) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceRptAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceRptAmount *- 1 ) ) 
    ) AS balanceRpt,
    (
    mainQuery.documentLocalAmount +(IF (mainQuery.receivedAmountLocal IS NULL,( IF ( matchedBRV.MatchedBRVLocalAmount IS NULL, 0, matchedBRV.MatchedBRVLocalAmount ) ) , mainQuery.receivedAmountLocal)) + ( IF ( InvoicedBRV.BRVLocalAmount IS NULL, 0, InvoicedBRV.BRVLocalAmount ) )  + ( IF ( srInvoiced.sumReturnLocalAmount IS NULL, 0, srInvoiced.sumReturnLocalAmount * -1) ) + ( IF ( srAmount.sumSRLocalAmount IS NULL, 0, srAmount.sumSRLocalAmount) ) + ( IF ( srDEO.sumReturnDEOLocalAmount IS NULL, 0, srDEO.sumReturnDEOLocalAmount * -1) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceLocalAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceLocalAmount *- 1 ) ) 
    ) AS balanceLocal,
    (
    mainQuery.documentTransAmount + ( IF(mainQuery.receivedAmountTrans IS NULL,IF ( matchedBRV.MatchedBRVTransAmount IS NULL, 0, matchedBRV.MatchedBRVTransAmount ),mainQuery.receivedAmountTrans) ) + ( IF ( InvoicedBRV.BRVTransAmount IS NULL, 0, InvoicedBRV.BRVTransAmount ) ) + ( IF ( srInvoiced.sumReturnTransactionAmount IS NULL, 0, srInvoiced.sumReturnTransactionAmount * -1) ) + ( IF ( srAmount.sumSRTransactionAmount IS NULL, 0, srAmount.sumSRTransactionAmount) ) + ( IF ( srDEO.sumReturnDEOTransactionAmount IS NULL, 0, srDEO.sumReturnDEOTransactionAmount * -1) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceTransAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceTransAmount *- 1 ) ) 
    ) AS balanceTrans,
    (( IF ( matchedBRV.MatchedBRVRptAmount IS NULL, 0, matchedBRV.MatchedBRVRptAmount ) ) + ( IF ( InvoicedBRV.BRVRptAmount IS NULL, 0, InvoicedBRV.BRVRptAmount ) ) + ( IF ( srInvoiced.sumReturnRptAmount IS NULL, 0, srInvoiced.sumReturnRptAmount * -1) ) + ( IF ( srAmount.sumSRRptAmount IS NULL, 0, srAmount.sumSRRptAmount) ) + ( IF ( srDEO.sumReturnDEORptAmount IS NULL, 0, srDEO.sumReturnDEORptAmount * -1) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceRptAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceRptAmount *- 1 ))) as paidRptAmount,
    (( IF ( matchedBRV.MatchedBRVLocalAmount IS NULL, 0, matchedBRV.MatchedBRVLocalAmount ) ) + ( IF ( InvoicedBRV.BRVLocalAmount IS NULL, 0, InvoicedBRV.BRVLocalAmount ))  + ( IF ( srInvoiced.sumReturnLocalAmount IS NULL, 0, srInvoiced.sumReturnLocalAmount * -1) ) + ( IF ( srAmount.sumSRLocalAmount IS NULL, 0, srAmount.sumSRLocalAmount) ) + ( IF ( srDEO.sumReturnDEOLocalAmount IS NULL, 0, srDEO.sumReturnDEOLocalAmount * -1) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceLocalAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceLocalAmount *- 1 ) )) as paidLocalAmount,
    (( IF ( matchedBRV.MatchedBRVTransAmount IS NULL, 0, matchedBRV.MatchedBRVTransAmount ) ) + ( IF ( InvoicedBRV.BRVTransAmount IS NULL, 0, InvoicedBRV.BRVTransAmount ) )  + ( IF ( srInvoiced.sumReturnTransactionAmount IS NULL, 0, srInvoiced.sumReturnTransactionAmount * -1) ) + ( IF ( srAmount.sumSRTransactionAmount IS NULL, 0, srAmount.sumSRTransactionAmount) ) + ( IF ( srDEO.sumReturnDEOTransactionAmount IS NULL, 0, srDEO.sumReturnDEOTransactionAmount * -1) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceTransAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceTransAmount *- 1 ) )) as paidTransAmount,
    mainQuery.concatCustomerName, 
    matchedBRV.FullyMatched,
    mainQuery.CutomerCode,
    mainQuery.CustomerName,  
    mainQuery.PONumber,
    chartofaccounts.AccountDescription,
    mainQuery.receivedAmountLocal,
    mainQuery.receivedAmountRpt,
    mainQuery.receivedAmountTrans
FROM
    (
SELECT
    erp_generalledger.companySystemID,
    erp_generalledger.companyID,
    companymaster.CompanyName,
    erp_generalledger.serviceLineSystemID,
    erp_generalledger.serviceLineCode,
    erp_generalledger.documentSystemID,
    erp_generalledger.documentID,
    erp_generalledger.documentSystemCode,
    erp_generalledger.documentCode,
    erp_generalledger.documentDate,
    DATE_FORMAT(documentDate, "%d/%m/%Y") AS documentDateFilter,
    erp_generalledger.documentYear,
    erp_generalledger.documentMonth,
    erp_generalledger.chequeNumber,
    erp_generalledger.invoiceNumber,
    erp_generalledger.invoiceDate,
    erp_generalledger.chartOfAccountSystemID AS chartOfAccountSystemID,
    erp_generalledger.glCode,
    erp_generalledger.documentNarration,
    erp_generalledger.clientContractID,
    erp_generalledger.supplierCodeSystem,
    erp_generalledger.documentTransCurrencyID,
    currTrans.CurrencyCode AS documentTransCurrency,
    currTrans.DecimalPlaces AS documentTransDecimalPlaces,
    erp_generalledger.documentTransAmount,
    erp_generalledger.documentLocalCurrencyID,
    currLocal.CurrencyCode AS documentLocalCurrency,
    currLocal.DecimalPlaces AS documentLocalDecimalPlaces,
    erp_generalledger.documentLocalAmount,
    erp_generalledger.documentRptCurrencyID,
    currRpt.CurrencyCode AS documentRptCurrency,
    currRpt.DecimalPlaces AS documentRptDecimalPlaces,
    erp_generalledger.documentRptAmount,
    erp_generalledger.documentType,
    erp_custinvoicedirect.PONumber,
    customermaster.CutomerCode,
    customermaster.CustomerName,
    CONCAT(customermaster.CutomerCode, " - ", customermaster.CustomerName) AS concatCustomerName,
    CASE
     WHEN erp_generalledger.documentNarration LIKE  "Matching %" AND erp_generalledger.documentSystemID = 19
     THEN
        -(erp_generalledger.documentLocalAmount)
     ELSE
         CASE 
           WHEN erp_generalledger.documentLocalAmount = (
                SELECT 
                    CASE 
                        WHEN erp_generalledger.documentLocalCurrencyID = 1 THEN 
                            matchingAmount / localCurrencyER
                        ELSE 
                            matchingAmount / companyRptCurrencyER
                    END
                FROM erp_matchdocumentmaster
                WHERE 
                    erp_matchdocumentmaster.PayMasterAutoId = erp_generalledger.documentSystemCode
                    AND erp_matchdocumentmaster.documentSystemID = erp_generalledger.documentSystemID 
                    AND erp_generalledger.chartOfAccountSystemID != customermaster.custGLAccountSystemID
                    AND erp_matchdocumentmaster.matchingConfirmedYN = 1
            ) THEN -(
                SELECT 
                    CASE 
                        WHEN erp_generalledger.documentLocalCurrencyID = 1 THEN 
                            matchingAmount / localCurrencyER
                        ELSE 
                            matchingAmount / companyRptCurrencyER
                    END
                FROM erp_matchdocumentmaster
                WHERE 
                    erp_matchdocumentmaster.PayMasterAutoId = erp_generalledger.documentSystemCode
                    AND erp_matchdocumentmaster.documentSystemID = erp_generalledger.documentSystemID 
                    AND erp_generalledger.chartOfAccountSystemID != customermaster.custGLAccountSystemID
                    AND erp_matchdocumentmaster.matchingConfirmedYN = 1
            )
            ELSE (
                SELECT 
                    CASE 
                        WHEN erp_generalledger.documentLocalCurrencyID = 1 THEN 
                            matchingAmount / localCurrencyER
                        ELSE 
                            matchingAmount / companyRptCurrencyER
                    END
                FROM erp_matchdocumentmaster
                WHERE 
                    erp_matchdocumentmaster.PayMasterAutoId = erp_generalledger.documentSystemCode
                    AND erp_matchdocumentmaster.documentSystemID = erp_generalledger.documentSystemID 
                    AND erp_generalledger.chartOfAccountSystemID != customermaster.custGLAccountSystemID
                    AND erp_matchdocumentmaster.matchingConfirmedYN = 1
            )
        END
    END AS receivedAmountLocal,
    CASE
     WHEN erp_generalledger.documentNarration LIKE  "Matching %" AND erp_generalledger.documentSystemID = 19
     THEN
         -(erp_generalledger.documentRptAmount)
     ELSE
         CASE
            WHEN erp_generalledger.documentRptAmount = (
                SELECT matchingAmount / companyRptCurrencyER
                FROM erp_matchdocumentmaster
                WHERE erp_matchdocumentmaster.PayMasterAutoId = erp_generalledger.documentSystemCode
                  AND erp_matchdocumentmaster.documentSystemID = erp_generalledger.documentSystemID 
                  AND erp_generalledger.chartOfAccountSystemID != customermaster.custGLAccountSystemID
                  AND erp_matchdocumentmaster.matchingConfirmedYN = 1
            ) THEN -(
                SELECT matchingAmount / companyRptCurrencyER
                FROM erp_matchdocumentmaster
                WHERE erp_matchdocumentmaster.PayMasterAutoId = erp_generalledger.documentSystemCode
                  AND erp_matchdocumentmaster.documentSystemID = erp_generalledger.documentSystemID 
                  AND erp_generalledger.chartOfAccountSystemID != customermaster.custGLAccountSystemID
                  AND erp_matchdocumentmaster.matchingConfirmedYN = 1
            )
            ELSE (
                SELECT matchingAmount / companyRptCurrencyER
                FROM erp_matchdocumentmaster
                WHERE erp_matchdocumentmaster.PayMasterAutoId = erp_generalledger.documentSystemCode
                  AND erp_matchdocumentmaster.documentSystemID = erp_generalledger.documentSystemID 
                  AND erp_generalledger.chartOfAccountSystemID != customermaster.custGLAccountSystemID
                  AND erp_matchdocumentmaster.matchingConfirmedYN = 1
            )
        END 
    END AS receivedAmountRpt,
    CASE
     WHEN erp_generalledger.documentNarration LIKE  "Matching %" AND erp_generalledger.documentSystemID = 19
     THEN
        -(erp_generalledger.documentTransAmount)
     ELSE
            CASE
            WHEN erp_generalledger.documentTransAmount = (
                    SELECT matchingAmount / supplierDefCurrencyER
                    FROM erp_matchdocumentmaster
                    WHERE erp_matchdocumentmaster.PayMasterAutoId = erp_generalledger.documentSystemCode
                      AND erp_matchdocumentmaster.documentSystemID = erp_generalledger.documentSystemID 
                      AND erp_generalledger.chartOfAccountSystemID != customermaster.custGLAccountSystemID
                      AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                ) THEN -(
                    SELECT matchingAmount / supplierDefCurrencyER
                    FROM erp_matchdocumentmaster
                    WHERE erp_matchdocumentmaster.PayMasterAutoId = erp_generalledger.documentSystemCode
                      AND erp_matchdocumentmaster.documentSystemID = erp_generalledger.documentSystemID 
                      AND erp_generalledger.chartOfAccountSystemID != customermaster.custGLAccountSystemID
                      AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                )
                ELSE (
                    SELECT matchingAmount / supplierDefCurrencyER
                    FROM erp_matchdocumentmaster
                    WHERE erp_matchdocumentmaster.PayMasterAutoId = erp_generalledger.documentSystemCode
                      AND erp_matchdocumentmaster.documentSystemID = erp_generalledger.documentSystemID 
                      AND erp_generalledger.chartOfAccountSystemID != customermaster.custGLAccountSystemID
                      AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                )
            END 
    END AS receivedAmountTrans
FROM
    erp_generalledger
LEFT JOIN currencymaster currTrans ON erp_generalledger.documentTransCurrencyID = currTrans.currencyID
LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
LEFT JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
LEFT JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
LEFT JOIN erp_custinvoicedirect ON 
    erp_generalledger.documentSystemCode = erp_custinvoicedirect.custInvoiceDirectAutoID AND 
    erp_generalledger.documentSystemID = erp_custinvoicedirect.documentSystemiD AND 
    erp_generalledger.companySystemID = erp_custinvoicedirect.companySystemID
WHERE
    ( erp_generalledger.documentSystemID = "20" OR erp_generalledger.documentSystemID = "19" OR erp_generalledger.documentSystemID = "21" OR erp_generalledger.documentSystemID = "87" ) 
    AND DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
    AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') 
    AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
    AND erp_generalledger.chartOfAccountSystemID IN (' . join(',', $controlAccountsSystemID) . ')
    ) AS mainQuery
    LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = mainQuery.chartOfAccountSystemID
    LEFT JOIN (
    SELECT
        erp_matchdocumentmaster.companySystemID,
        erp_matchdocumentmaster.documentSystemID,
        erp_matchdocumentmaster.PayMasterAutoId,
        erp_matchdocumentmaster.serviceLineSystemID,
        erp_matchdocumentmaster.BPVcode,
        CASE
            WHEN erp_matchdocumentmaster.documentSystemID = 19
            THEN
                (SELECT (sum( curcp.receiveAmountTrans )/erp_matchdocumentmaster.supplierTransCurrencyER) 
                FROM erp_custreceivepaymentdet AS curcp
                WHERE curcp.custReceivePaymentAutoID = erp_matchdocumentmaster.PayMasterAutoId AND curcp.matchingDocID != 0 AND curcp.serviceLineSystemID = erp_custreceivepaymentdet.serviceLineSystemID)
            ELSE 
            sum( erp_custreceivepaymentdet.receiveAmountTrans )
        END 
        AS MatchedBRVTransAmount,
        CASE
            WHEN erp_matchdocumentmaster.documentSystemID = 19
            THEN
                (SELECT (sum( curcp.receiveAmountTrans )/erp_matchdocumentmaster.localCurrencyER) 
                FROM erp_custreceivepaymentdet AS curcp
                WHERE curcp.custReceivePaymentAutoID = erp_matchdocumentmaster.PayMasterAutoId AND curcp.matchingDocID != 0 AND curcp.serviceLineSystemID = erp_custreceivepaymentdet.serviceLineSystemID)
            ELSE 
            sum( erp_custreceivepaymentdet.receiveAmountLocal )
        END 
        AS MatchedBRVLocalAmount,
        CASE
            WHEN erp_matchdocumentmaster.documentSystemID = 19
            THEN
                (SELECT (sum( curcp.receiveAmountTrans )/erp_matchdocumentmaster.companyRptCurrencyER) 
                FROM erp_custreceivepaymentdet AS curcp
                WHERE curcp.custReceivePaymentAutoID = erp_matchdocumentmaster.PayMasterAutoId AND curcp.matchingDocID != 0 AND curcp.serviceLineSystemID = erp_custreceivepaymentdet.serviceLineSystemID)
            ELSE 
            sum( erp_custreceivepaymentdet.receiveAmountRpt )
        END 
        AS MatchedBRVRptAmount,
        IF ((erp_matchdocumentmaster.payAmountSuppTrans - matchBalanceAmount),true,false) AS FullyMatched
    FROM
        erp_matchdocumentmaster
        INNER JOIN erp_custreceivepaymentdet ON erp_matchdocumentmaster.companyID = erp_custreceivepaymentdet.companyID 
        AND erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID 
    WHERE
        erp_matchdocumentmaster.matchingConfirmedYN = 1 
        AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '" 
        AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
        AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
    GROUP BY
        erp_matchdocumentmaster.PayMasterAutoId,
        erp_matchdocumentmaster.BPVcode,
        erp_matchdocumentmaster.serviceLineSystemID
    ) AS matchedBRV ON mainQuery.documentSystemID = matchedBRV.documentSystemID 
    AND mainQuery.companySystemID = matchedBRV.companySystemID 
    AND matchedBRV.PayMasterAutoId = mainQuery.documentSystemCode
    AND matchedBRV.serviceLineSystemID = mainQuery.serviceLineSystemID
    LEFT JOIN (
    SELECT
        erp_customerreceivepayment.custReceivePaymentAutoID,
        erp_accountsreceivableledger.serviceLineSystemID,
        erp_customerreceivepayment.companySystemID,
        erp_customerreceivepayment.documentSystemID,
        erp_customerreceivepayment.custPaymentReceiveCode,
        sum( erp_custreceivepaymentdet.receiveAmountTrans ) AS BRVTransAmount,
        sum( erp_custreceivepaymentdet.receiveAmountLocal ) AS BRVLocalAmount,
        sum( erp_custreceivepaymentdet.receiveAmountRpt ) AS BRVRptAmount 
    FROM
        erp_customerreceivepayment
        INNER JOIN erp_custreceivepaymentdet ON erp_customerreceivepayment.custReceivePaymentAutoID = erp_custreceivepaymentdet.custReceivePaymentAutoID
        LEFT JOIN erp_accountsreceivableledger ON erp_accountsreceivableledger.arAutoID = erp_custreceivepaymentdet.arAutoID 
    WHERE
        erp_custreceivepaymentdet.bookingInvCode <> "0" 
        AND erp_custreceivepaymentdet.matchingDocID = 0 
        AND erp_customerreceivepayment.approved =- 1 
        AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
        AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '"
        AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
    GROUP BY
        custReceivePaymentAutoID, serviceLineSystemID 
    ) AS InvoicedBRV ON mainQuery.documentSystemID = InvoicedBRV.documentSystemID 
    AND mainQuery.documentSystemCode = InvoicedBRV.custReceivePaymentAutoID AND mainQuery.serviceLineSystemID = InvoicedBRV.serviceLineSystemID
    LEFT JOIN (
    SELECT 
        salesreturndetails.custInvoiceDirectAutoID,
        salesreturndetails.salesReturnID,
        salesreturndetails.companySystemID,
        sum(salesreturndetails.transactionAmount + (salesreturndetails.transactionAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnTransactionAmount,
        sum(salesreturndetails.companyLocalAmount + (salesreturndetails.companyLocalAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnLocalAmount,
        sum(salesreturndetails.companyReportingAmount + (salesreturndetails.companyReportingAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnRptAmount
        FROM 
            salesreturndetails
            LEFT JOIN salesreturn ON salesReturnID = salesreturn.id
        WHERE
            salesreturndetails.companySystemID IN (' . join(',', $companyID) . ')
            AND salesreturn.approvedYN = -1
            GROUP BY salesreturndetails.custInvoiceDirectAutoID
    ) srInvoiced ON srInvoiced.custInvoiceDirectAutoID = mainQuery.documentSystemCode AND mainQuery.documentSystemID = 20  
    LEFT JOIN (
    SELECT 
        salesreturndetails.custInvoiceDirectAutoID,
        salesreturndetails.salesReturnID,
        salesreturndetails.companySystemID,
        salesreturn.documentSystemID,
        sum(salesreturndetails.transactionAmount + (salesreturndetails.transactionAmount * salesreturndetails.VATPercentage / 100)) AS sumSRTransactionAmount,
        sum(salesreturndetails.companyLocalAmount + (salesreturndetails.companyLocalAmount * salesreturndetails.VATPercentage / 100)) AS sumSRLocalAmount,
        sum(salesreturndetails.companyReportingAmount + (salesreturndetails.companyReportingAmount * salesreturndetails.VATPercentage / 100)) AS sumSRRptAmount
        FROM 
            salesreturndetails
            LEFT JOIN salesreturn ON salesReturnID = salesreturn.id
        WHERE
            salesreturndetails.companySystemID IN (' . join(',', $companyID) . ')
            AND salesreturn.approvedYN = -1
            GROUP BY salesreturndetails.salesReturnID
    ) srAmount ON srAmount.salesReturnID = mainQuery.documentSystemCode AND mainQuery.documentSystemID = srAmount.documentSystemID       
    LEFT JOIN (
    SELECT 
       salesreturndetails.deliveryOrderDetailID,
       erp_customerinvoiceitemdetails.custInvoiceDirectAutoID,
       salesreturndetails.salesReturnID,
       salesreturndetails.companySystemID,
       sum(salesreturndetails.transactionAmount + (salesreturndetails.transactionAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEOTransactionAmount,
       sum(salesreturndetails.companyLocalAmount + (salesreturndetails.companyLocalAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEOLocalAmount,
       sum(salesreturndetails.companyReportingAmount + (salesreturndetails.companyReportingAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEORptAmount
       FROM 
            salesreturndetails
            LEFT JOIN salesreturn ON salesReturnID = salesreturn.id
            INNER JOIN erp_customerinvoiceitemdetails ON salesreturndetails.deliveryOrderDetailID = erp_customerinvoiceitemdetails.deliveryOrderDetailID
       WHERE
            salesreturndetails.companySystemID IN (' . join(',', $companyID) . ')
            AND salesreturn.approvedYN = -1
            AND salesreturndetails.deliveryOrderDetailID <> 0
            GROUP BY salesreturndetails.deliveryOrderDetailID
    ) srDEO ON srDEO.custInvoiceDirectAutoID = mainQuery.documentSystemCode AND mainQuery.documentSystemID = 20                
    LEFT JOIN  (
    SELECT
        companySystemID,
        companyID,
        addedDocumentSystemID,
        addedDocumentID,
        bookingInvCodeSystem,
        serviceLineSystemID,
        bookingInvCode,
        sum( receiveAmountTrans ) AS InvoiceTransAmount,
        sum( receiveAmountLocal ) AS InvoiceLocalAmount,
        sum( receiveAmountRpt ) AS InvoiceRptAmount 
    FROM
        (
        SELECT
            * 
        FROM
            (
            SELECT
                erp_customerreceivepayment.custPaymentReceiveCode,
                erp_accountsreceivableledger.serviceLineSystemID,
                erp_custreceivepaymentdet.companySystemID,
                erp_custreceivepaymentdet.companyID,
                erp_custreceivepaymentdet.addedDocumentSystemID,
                erp_custreceivepaymentdet.addedDocumentID,
                erp_custreceivepaymentdet.bookingInvCodeSystem,
                erp_custreceivepaymentdet.bookingInvCode,
                erp_custreceivepaymentdet.receiveAmountTrans,
                erp_custreceivepaymentdet.receiveAmountLocal,
                erp_custreceivepaymentdet.receiveAmountRpt 
            FROM
                erp_customerreceivepayment
                INNER JOIN erp_custreceivepaymentdet ON erp_customerreceivepayment.custReceivePaymentAutoID = erp_custreceivepaymentdet.custReceivePaymentAutoID
                LEFT JOIN erp_accountsreceivableledger ON erp_accountsreceivableledger.arAutoID = erp_custreceivepaymentdet.arAutoID 
                AND erp_custreceivepaymentdet.matchingDocID = 0 
                AND erp_customerreceivepayment.approved =- 1 
            WHERE
                erp_custreceivepaymentdet.bookingInvCode <> "0" 
                AND erp_custreceivepaymentdet.matchingDocID = 0 
                AND erp_customerreceivepayment.approved =- 1 
                AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
                AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '" 
                AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
            ) AS InvoiceFromBRV UNION ALL
        SELECT
            * 
        FROM
            (
            SELECT
                erp_matchdocumentmaster.matchingDocCode,
                erp_accountsreceivableledger.serviceLineSystemID,
                erp_custreceivepaymentdet.companySystemID,
                erp_custreceivepaymentdet.companyID,
                erp_custreceivepaymentdet.addedDocumentSystemID,
                erp_custreceivepaymentdet.addedDocumentID,
                erp_custreceivepaymentdet.bookingInvCodeSystem,
                erp_custreceivepaymentdet.bookingInvCode,
                erp_custreceivepaymentdet.receiveAmountTrans,
                erp_custreceivepaymentdet.receiveAmountLocal,
                erp_custreceivepaymentdet.receiveAmountRpt 
            FROM
                erp_custreceivepaymentdet
                INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID 
                AND erp_custreceivepaymentdet.companySystemID = erp_matchdocumentmaster.companySystemID
                LEFT JOIN erp_accountsreceivableledger ON erp_accountsreceivableledger.arAutoID = erp_custreceivepaymentdet.arAutoID  
            WHERE
                erp_matchdocumentmaster.matchingConfirmedYN = 1 
                AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
                AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '" 
                AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
            ) AS InvoiceFromMatching 
        ) AS InvoiceFromUNION 
    GROUP BY
        bookingInvCode, serviceLineSystemID 
    ) AS InvoiceFromBRVAndMatching ON InvoiceFromBRVAndMatching.addedDocumentSystemID = mainQuery.documentSystemID 
    AND mainQuery.documentSystemCode = InvoiceFromBRVAndMatching.bookingInvCodeSystem AND mainQuery.serviceLineSystemID = InvoiceFromBRVAndMatching.serviceLineSystemID
    ) AS final 
 ORDER BY PostedDate ASC;';
        $data =  \DB::select($query);


        return $data;
    }

    function getCustomerLedgerTemplate2QRY($request)
    {
        $fromDate = new Carbon($request->fromDate);
        //$fromDate = $fromDate->addDays(1);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        //$toDate = $toDate->addDays(1);
        $toDate = $toDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $customers = (array)$request->customers;
        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();


        $controlAccountsSystemIDs = (array)$request->controlAccountsSystemID;
        $controlAccountsSystemID = collect($controlAccountsSystemIDs)->pluck('id')->toArray();

        $currencyID = $request->currencyID;
        $currencyQry = '';
        $invoiceAmountQry = '';
        $decimalPlaceQry = '';
        if ($currencyID == 1) {
            $currencyQry = "CustomerBalanceSummary_Detail.documentTransCurrency AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( CustomerBalanceSummary_Detail.documentTransAmount, CustomerBalanceSummary_Detail.documentTransDecimalPlaces ),0) - round(IFNULL(CustomerBalanceSummary_Detail.sumReturnTransactionAmount,0),CustomerBalanceSummary_Detail.documentLocalDecimalPlaces) - round(IFNULL(CustomerBalanceSummary_Detail.sumReturnDEOLocalAmount,0),CustomerBalanceSummary_Detail.documentLocalDecimalPlaces) AS invoiceAmount";
            $decimalPlaceQry = "CustomerBalanceSummary_Detail.documentTransDecimalPlaces AS balanceDecimalPlaces";
        } else if ($currencyID == 2) {
            $currencyQry = "CustomerBalanceSummary_Detail.documentLocalCurrency AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( CustomerBalanceSummary_Detail.documentLocalAmount, CustomerBalanceSummary_Detail.documentLocalDecimalPlaces ),0) - round(IFNULL(CustomerBalanceSummary_Detail.sumReturnLocalAmount,0),CustomerBalanceSummary_Detail.documentLocalDecimalPlaces) - round(IFNULL(CustomerBalanceSummary_Detail.sumReturnDEOLocalAmount,0),CustomerBalanceSummary_Detail.documentLocalDecimalPlaces) AS invoiceAmount";
            $decimalPlaceQry = "CustomerBalanceSummary_Detail.documentLocalDecimalPlaces AS balanceDecimalPlaces";
        } else {
            $currencyQry = "CustomerBalanceSummary_Detail.documentRptCurrency AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( CustomerBalanceSummary_Detail.documentRptAmount, CustomerBalanceSummary_Detail.documentRptDecimalPlaces ),0) - round(IFNULL(CustomerBalanceSummary_Detail.sumReturnRptAmount,0),CustomerBalanceSummary_Detail.documentLocalDecimalPlaces) - round(IFNULL(CustomerBalanceSummary_Detail.sumReturnDEORptAmount,0),CustomerBalanceSummary_Detail.documentLocalDecimalPlaces) AS invoiceAmount";
            $decimalPlaceQry = "CustomerBalanceSummary_Detail.documentRptDecimalPlaces AS balanceDecimalPlaces";
        }

        //DB::enableQueryLog();
        $output = \DB::select('SELECT
    CustomerBalanceSummary_Detail.documentCode AS DocumentCode,
    CustomerBalanceSummary_Detail.documentDate AS PostedDate,
    CustomerBalanceSummary_Detail.documentNarration AS DocumentNarration,
    CustomerBalanceSummary_Detail.invoiceNumber AS invoiceNumber,
    CustomerBalanceSummary_Detail.invoiceDate AS InvoiceDate,
    CustomerBalanceSummary_Detail.CutomerCode,
    CustomerBalanceSummary_Detail.CustomerName,
    CustomerBalanceSummary_Detail.documentLocalCurrencyID,
    CustomerBalanceSummary_Detail.concatCustomerName,
    CustomerBalanceSummary_Detail.companyID,
    CustomerBalanceSummary_Detail.CompanyName,
    CustomerBalanceSummary_Detail.documentSystemCode,
    CustomerBalanceSummary_Detail.documentSystemID,
     ' . $currencyQry . ',
    ' . $decimalPlaceQry . ',
    ' . $invoiceAmountQry . ',
    CustomerBalanceSummary_Detail.AccountDescription    
FROM
(
SELECT
    erp_generalledger.companySystemID,
    erp_generalledger.companyID,
    companymaster.CompanyName,
    erp_generalledger.documentID,
    erp_generalledger.documentSystemCode,
    erp_generalledger.documentCode,
    erp_generalledger.documentSystemID,
    erp_generalledger.documentDate,
    erp_generalledger.glCode,
    erp_generalledger.supplierCodeSystem,
    customermaster.CutomerCode,
    customermaster.CustomerName,
    erp_generalledger.invoiceNumber,
    erp_generalledger.invoiceDate,
    erp_generalledger.chartOfAccountSystemID,
    erp_generalledger.documentNarration,
    erp_generalledger.documentTransCurrencyID,
    currTrans.CurrencyCode as documentTransCurrency,
    currTrans.DecimalPlaces as documentTransDecimalPlaces,
    erp_generalledger.documentTransAmount,
    erp_generalledger.documentLocalCurrencyID,
    currLocal.CurrencyCode as documentLocalCurrency,
    currLocal.DecimalPlaces as documentLocalDecimalPlaces,
    erp_generalledger.documentLocalAmount,
    erp_generalledger.documentRptCurrencyID,
    currRpt.CurrencyCode as documentRptCurrency,
    currRpt.DecimalPlaces as documentRptDecimalPlaces,
    erp_generalledger.documentRptAmount,
    erp_generalledger.documentType,
    CONCAT(customermaster.CutomerCode," - ",customermaster.CustomerName) as concatCustomerName,
    IFNULL(srInvoiced.sumReturnTransactionAmount, 0) AS sumReturnTransactionAmount,
    IFNULL(srInvoiced.sumReturnLocalAmount, 0) AS sumReturnLocalAmount,
    IFNULL(srInvoiced.sumReturnRptAmount, 0) AS sumReturnRptAmount,
    IFNULL(srDEO.sumReturnDEOTransactionAmount, 0) AS sumReturnDEOTransactionAmount,
    IFNULL(srDEO.sumReturnDEOLocalAmount, 0) AS sumReturnDEOLocalAmount,
    IFNULL(srDEO.sumReturnDEORptAmount, 0) AS sumReturnDEORptAmount,
    chartofaccounts.AccountDescription as AccountDescription
FROM
    erp_generalledger
    INNER JOIN customermaster ON customermaster.customerCodeSystem=erp_generalledger.supplierCodeSystem
    LEFT JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
    LEFT JOIN currencymaster currTrans ON erp_generalledger.documentTransCurrencyID = currTrans.currencyID
    LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
    LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
    LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
    LEFT JOIN (
    SELECT 
        salesreturndetails.custInvoiceDirectAutoID,
        salesreturndetails.salesReturnID,
        salesreturndetails.companySystemID,
        sum(salesreturndetails.transactionAmount + (salesreturndetails.transactionAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnTransactionAmount,
        sum(salesreturndetails.companyLocalAmount + (salesreturndetails.companyLocalAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnLocalAmount,
        sum(salesreturndetails.companyReportingAmount + (salesreturndetails.companyReportingAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnRptAmount
        FROM 
            salesreturndetails
            LEFT JOIN salesreturn ON salesReturnID = salesreturn.id
        WHERE
            salesreturndetails.companySystemID IN (' . join(',', $companyID) . ')
            AND salesreturn.approvedYN = -1
            GROUP BY salesreturndetails.custInvoiceDirectAutoID
    ) srInvoiced ON srInvoiced.custInvoiceDirectAutoID = erp_generalledger.documentSystemCode AND erp_generalledger.documentSystemID = 20
    LEFT JOIN (
    SELECT 
       salesreturndetails.deliveryOrderDetailID,
       erp_customerinvoiceitemdetails.custInvoiceDirectAutoID,
       salesreturndetails.salesReturnID,
       salesreturndetails.companySystemID,
       sum(salesreturndetails.transactionAmount + (salesreturndetails.transactionAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEOTransactionAmount,
       sum(salesreturndetails.companyLocalAmount + (salesreturndetails.companyLocalAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEOLocalAmount,
       sum(salesreturndetails.companyReportingAmount + (salesreturndetails.companyReportingAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEORptAmount
       FROM 
            salesreturndetails
            LEFT JOIN salesreturn ON salesReturnID = salesreturn.id
            INNER JOIN erp_customerinvoiceitemdetails ON salesreturndetails.deliveryOrderDetailID = erp_customerinvoiceitemdetails.deliveryOrderDetailID
       WHERE
            salesreturndetails.companySystemID IN (' . join(',', $companyID) . ')
            AND salesreturn.approvedYN = -1
            AND salesreturndetails.deliveryOrderDetailID <> 0
            GROUP BY salesreturndetails.deliveryOrderDetailID
    ) srDEO ON srDEO.custInvoiceDirectAutoID = erp_generalledger.documentSystemCode AND erp_generalledger.documentSystemID = 20
WHERE
    (erp_generalledger.documentSystemID = "20" OR erp_generalledger.documentSystemID = "19" OR erp_generalledger.documentSystemID = "21")
    AND DATE( erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
    AND erp_generalledger.chartOfAccountSystemID IN (' . join(',', $controlAccountsSystemID) . ')
    AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') 
    AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
    UNION ALL 
    SELECT
    erp_generalledger.companySystemID,
    erp_generalledger.companyID,
    companymaster.CompanyName,
    erp_generalledger.documentID,
    erp_generalledger.documentSystemCode,
    "Opening Balance" as documentCode,
    erp_generalledger.documentSystemID,
    "1970-01-01" as documentDate,
    erp_generalledger.glCode,
    erp_generalledger.supplierCodeSystem,
    customermaster.CutomerCode,
    customermaster.CustomerName,
    "" as invoiceNumber,
    "" as invoiceDate,
    erp_generalledger.chartOfAccountSystemID,
    "" as documentNarration,
    erp_generalledger.documentTransCurrencyID,
    currTrans.CurrencyCode as documentTransCurrency,
    currTrans.DecimalPlaces as documentTransDecimalPlaces,
    SUM(erp_generalledger.documentTransAmount) as documentTransAmount,
    erp_generalledger.documentLocalCurrencyID,
    currLocal.CurrencyCode as documentLocalCurrency,
    currLocal.DecimalPlaces as documentLocalDecimalPlaces,
    SUM(erp_generalledger.documentLocalAmount) as documentLocalAmount,
    erp_generalledger.documentRptCurrencyID,
    currRpt.CurrencyCode as documentRptCurrency,
    currRpt.DecimalPlaces as documentRptDecimalPlaces,
    SUM(erp_generalledger.documentRptAmount) as documentRptAmount,
    erp_generalledger.documentType,
    CONCAT(customermaster.CutomerCode," - ",customermaster.CustomerName) as concatCustomerName,
    0 AS sumReturnTransactionAmount,
    0 AS sumReturnLocalAmount,
    0 AS sumReturnRptAmount,
    0 AS sumReturnDEOTransactionAmount,
    0 AS sumReturnDEOLocalAmount,
    0 AS sumReturnDEORptAmount,
    chartofaccounts.AccountDescription as AccountDescription
FROM
    erp_generalledger
    INNER JOIN customermaster ON customermaster.customerCodeSystem=erp_generalledger.supplierCodeSystem
    LEFT JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
    LEFT JOIN currencymaster currTrans ON erp_generalledger.documentTransCurrencyID = currTrans.currencyID
    LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
    LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
    LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
WHERE
    (erp_generalledger.documentSystemID = "20" OR erp_generalledger.documentSystemID = "19" OR erp_generalledger.documentSystemID = "21")
    AND DATE( erp_generalledger.documentDate) < "' . $fromDate . '"
    AND erp_generalledger.chartOfAccountSystemID IN (' . join(',', $controlAccountsSystemID) . ')
    AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') 
    AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
    GROUP BY erp_generalledger.supplierCodeSystem) AS CustomerBalanceSummary_Detail ORDER BY CustomerBalanceSummary_Detail.documentDate ASC');

        return $output;
    }

    function getCustomerBalanceSummery($request)
    {
        $asOfDate = new Carbon($request->fromDate);
        $asOfDate = $asOfDate->format('Y-m-d');
        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $customers = (array)$request->customers;
        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $controlAccounts = (array)$request->controlAccountsSystemID;
        $controlAccountsSystemID = collect($controlAccounts)->pluck('id')->toArray();


        return \DB::select('SELECT
                    CustomerBalanceSummary_Detail.companySystemID,
                    CustomerBalanceSummary_Detail.companyID,
                    CustomerBalanceSummary_Detail.CompanyName,
                    CustomerBalanceSummary_Detail.supplierCodeSystem,
                    CustomerBalanceSummary_Detail.CutomerCode,
                    CustomerBalanceSummary_Detail.CustomerName,
                    CustomerBalanceSummary_Detail.documentLocalCurrencyID,
                    sum(CustomerBalanceSummary_Detail.documentLocalAmount - CustomerBalanceSummary_Detail.sumReturnLocalAmount - CustomerBalanceSummary_Detail.sumReturnDEOLocalAmount) as localAmount,
                    CustomerBalanceSummary_Detail.documentRptCurrencyID,
                    sum(CustomerBalanceSummary_Detail.documentRptAmount - CustomerBalanceSummary_Detail.sumReturnRptAmount - CustomerBalanceSummary_Detail.sumReturnDEORptAmount) as RptAmount,
                    CustomerBalanceSummary_Detail.documentLocalCurrency,
                    CustomerBalanceSummary_Detail.documentRptCurrency
                FROM
                (
                SELECT
                    erp_generalledger.companySystemID,
                    erp_generalledger.companyID,
                    erp_generalledger.documentID,
                    erp_generalledger.documentSystemID,
                    erp_generalledger.documentSystemCode,
                    erp_generalledger.documentCode,
                    erp_generalledger.documentDate,
                    erp_generalledger.glCode,
                    erp_generalledger.supplierCodeSystem,
                    customermaster.CutomerCode,
                    customermaster.CustomerName,
                    erp_generalledger.documentLocalCurrencyID,
                    erp_generalledger.documentLocalAmount,
                    erp_generalledger.documentRptCurrencyID,
                    erp_generalledger.documentRptAmount,
                    currLocal.CurrencyCode as documentLocalCurrency,
                    currRpt.CurrencyCode as documentRptCurrency,
                    companymaster.CompanyName,
                    IFNULL(srInvoiced.sumReturnLocalAmount, 0) AS sumReturnLocalAmount,
                    IFNULL(srInvoiced.sumReturnRptAmount, 0) AS sumReturnRptAmount,
                    IFNULL(srDEO.sumReturnDEOLocalAmount, 0) AS sumReturnDEOLocalAmount,
                    IFNULL(srDEO.sumReturnDEORptAmount, 0) AS sumReturnDEORptAmount
                FROM
                    erp_generalledger
                    INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                    INNER JOIN customermaster ON customermaster.customerCodeSystem=erp_generalledger.supplierCodeSystem
                    LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
                    LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
                    LEFT JOIN (
                    SELECT 
                        salesreturndetails.custInvoiceDirectAutoID,
                        salesreturndetails.salesReturnID,
                        salesreturndetails.companySystemID,
                        sum(salesreturndetails.companyLocalAmount + (salesreturndetails.companyLocalAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnLocalAmount,
                        sum(salesreturndetails.companyReportingAmount + (salesreturndetails.companyReportingAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnRptAmount
                        FROM 
                            salesreturndetails
                            LEFT JOIN salesreturn ON salesReturnID = salesreturn.id
                        WHERE
                            salesreturndetails.companySystemID IN (' . join(',', $companyID) . ')
                            AND salesreturn.approvedYN = -1
                            GROUP BY salesreturndetails.custInvoiceDirectAutoID
                    ) srInvoiced ON srInvoiced.custInvoiceDirectAutoID = erp_generalledger.documentSystemCode AND erp_generalledger.documentSystemID = 20
                     LEFT JOIN (
                SELECT 
                   salesreturndetails.deliveryOrderDetailID,
                   erp_customerinvoiceitemdetails.custInvoiceDirectAutoID,
                   salesreturndetails.salesReturnID,
                   salesreturndetails.companySystemID,
                   sum(salesreturndetails.companyLocalAmount + (salesreturndetails.companyLocalAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEOLocalAmount,
                   sum(salesreturndetails.companyReportingAmount + (salesreturndetails.companyReportingAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEORptAmount
                   FROM 
                        salesreturndetails
                        LEFT JOIN salesreturn ON salesReturnID = salesreturn.id
                        INNER JOIN erp_customerinvoiceitemdetails ON salesreturndetails.deliveryOrderDetailID = erp_customerinvoiceitemdetails.deliveryOrderDetailID
                   WHERE
                        salesreturndetails.companySystemID IN (' . join(',', $companyID) . ')
                        AND salesreturn.approvedYN = -1
                        AND salesreturndetails.deliveryOrderDetailID <> 0
                        GROUP BY salesreturndetails.deliveryOrderDetailID
                ) srDEO ON srDEO.custInvoiceDirectAutoID = erp_generalledger.documentSystemCode AND erp_generalledger.documentSystemID = 20
                WHERE
                    (erp_generalledger.documentSystemID = "20" OR erp_generalledger.documentSystemID = "19" OR erp_generalledger.documentSystemID = "21")
                    AND ( erp_generalledger.chartOfAccountSystemID IN (' . join(',', $controlAccountsSystemID) . '))
                    AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                    AND DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
                    AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . '))
                    AS CustomerBalanceSummary_Detail
                    GROUP BY CustomerBalanceSummary_Detail.companySystemID,CustomerBalanceSummary_Detail.supplierCodeSystem
                    ORDER BY CustomerBalanceSummary_Detail.documentDate ASC;');
    }

    function getCustomerRevenueMonthlySummary($request)
    {
        $asOfDate = new Carbon($request->fromDate);
        $asOfDate = $asOfDate->format('Y-m-d');
        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }


        $local = CurrencyMaster::select('DecimalPlaces')->where('currencyID',$checkIsGroup->localCurrencyID)->first();
        $reporting = CurrencyMaster::select('DecimalPlaces')->where('currencyID',$checkIsGroup->reportingCurrency)->first();

        $customers = (array)$request->customers;
        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $controlAccountsSystemID = $request->controlAccountsSystemID;

        $currency = $request->currencyID;
        $year = $request->year;
        $showVAT = (isset($request->showVAT) && $request->showVAT) ? 1 : 0;

        $currencyClm = "MyRptAmount";

        if ($currency == 2) {
            $currencyClm = ($showVAT == 1) ? "MyLocalAmount + VATAmountLocal" : "MyLocalAmount";
        } else if ($currency == 3) {
            $currencyClm = ($showVAT == 1) ? "MyRptAmount + VATAmountRPT" : "MyRptAmount";
        }

        $isAllCustomerSelected = $request->isAllCustomerSelected;

        $nullCustomer = '';
        if ($isAllCustomerSelected == 1) {
            $nullCustomer = 'OR revenueDetailData.mySupplierCode IS NULL  OR revenueDetailData.mySupplierCode = ""';
        }


        //DB::enableQueryLog();
        $output = \DB::select('SELECT
                    revenueDataSummary.companyID,
                    revenueDataSummary.CutomerCode,
                    revenueDataSummary.CustomerName,
                    revenueDataSummary.CompanyName,
                    revenueDataSummary.DocYEAR,
                    documentLocalCurrencyID,
                    documentRptCurrencyID,
                    sum(Jan) as Jan,
                    sum(Feb) as Feb,
                    sum(March) as March,
                    sum(April) as April,
                    sum(May) as May,
                    sum(June) as June,
                    sum(July) as July,
                    sum(Aug) as Aug,
                    sum(Sept) as Sept,
                    sum(Oct) as Oct,
                    sum(Nov) as Nov,
                    sum(Dece) as Dece,
                    sum(Total) as Total
                FROM
                (
                SELECT
                    revenueDetailData.documentLocalCurrencyID,
                    revenueDetailData.documentRptCurrencyID,
                    revenueDetailData.companySystemID,
                    revenueDetailData.companyID,
                    revenueDetailData.CompanyName,
                    revenueDetailData.mySupplierCode,
                    customermaster.CutomerCode,
                    customermaster.CustomerName,
                    revenueDetailData.DocYEAR,
                IF
                    ( revenueDetailData.DocMONTH = 1, ' . $currencyClm . ', 0 ) AS Jan,
                IF
                    ( revenueDetailData.DocMONTH = 2, ' . $currencyClm . ', 0 ) AS Feb,
                IF
                    ( revenueDetailData.DocMONTH = 3, ' . $currencyClm . ', 0 ) AS March,
                IF
                    ( revenueDetailData.DocMONTH = 4, ' . $currencyClm . ', 0 ) AS April,
                IF
                    ( revenueDetailData.DocMONTH = 5, ' . $currencyClm . ', 0 ) AS May,
                IF
                    ( revenueDetailData.DocMONTH = 6, ' . $currencyClm . ', 0 ) AS June,
                IF
                    ( revenueDetailData.DocMONTH = 7, ' . $currencyClm . ', 0 ) AS July,
                IF
                    ( revenueDetailData.DocMONTH = 8, ' . $currencyClm . ', 0 ) AS Aug,
                IF
                    ( revenueDetailData.DocMONTH = 9, ' . $currencyClm . ', 0 ) AS Sept,
                IF
                    ( revenueDetailData.DocMONTH = 10, ' . $currencyClm . ', 0 ) AS Oct,
                IF
                    ( revenueDetailData.DocMONTH = 11, ' . $currencyClm . ', 0 ) AS Nov,
                IF
                    ( revenueDetailData.DocMONTH = 12, ' . $currencyClm . ', 0 ) AS Dece,
                    ' . $currencyClm . ' as Total
                FROM
                    (
                SELECT
                    erp_generalledger.companySystemID,
                    erp_generalledger.companyID,
                    companymaster.CompanyName,
                    erp_generalledger.serviceLineSystemID,
                    erp_generalledger.serviceLineCode,
                    erp_generalledger.clientContractID,
                    contractmaster.ContractNumber,
                    erp_generalledger.documentID,
                    erp_generalledger.documentSystemCode,
                    erp_generalledger.documentCode,
                    erp_generalledger.documentDate,
                    MONTH ( erp_generalledger.documentDate ) AS DocMONTH,
                    YEAR ( erp_generalledger.documentDate ) AS DocYEAR,
                    erp_generalledger.documentNarration,
                    erp_generalledger.chartOfAccountSystemID,
                    erp_generalledger.glCode,
                    erp_generalledger.glAccountType,
                    chartofaccounts.controlAccounts,
                    erp_generalledger.supplierCodeSystem,
                IF
                    (
                    erp_generalledger.clientContractID = "X"
                    AND erp_generalledger.supplierCodeSystem = 0,
                    0,
                IF
                    (
                    erp_generalledger.clientContractID <> "X"
                    AND erp_generalledger.supplierCodeSystem = 0,
                    contractmaster.clientID,
                IF
                    ( erp_generalledger.documentSystemID = 11 OR erp_generalledger.documentSystemID = 15 OR erp_generalledger.documentSystemID = 4, contractmaster.clientID, erp_generalledger.supplierCodeSystem )
                    )
                    ) AS mySupplierCode,
                    erp_generalledger.documentLocalCurrencyID,
                    erp_generalledger.documentRptCurrencyID,
                    erp_generalledger.documentLocalAmount,
                    round((erp_generalledger.documentLocalAmount *- 1),"'.$local->DecimalPlaces.'") AS MyLocalAmount,
                    erp_generalledger.documentRptAmount,
                    round((erp_generalledger.documentRptAmount *- 1),"'.$reporting->DecimalPlaces.'") AS MyRptAmount
                FROM
                    erp_generalledger
                    INNER JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
                    LEFT JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                    LEFT JOIN contractmaster ON erp_generalledger.clientContractID = contractmaster.ContractNumber
                    LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
                    LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
                    AND erp_generalledger.companyID = contractmaster.CompanyID
                WHERE
                    DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
                    AND YEAR ( erp_generalledger.documentDate ) = "' . $year . '"
                    AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                    AND chartofaccounts.controlAccountsSystemID = 1
                    ) AS revenueDetailData
                    LEFT JOIN customermaster ON customermaster.customerCodeSystem = revenueDetailData.mySupplierCode
                    WHERE (revenueDetailData.mySupplierCode IN (' . join(',', $customerSystemID) . ')
                     ' . $nullCustomer . ')
                    ) AS revenueDataSummary
                    GROUP BY
                    revenueDataSummary.companySystemID,
                    revenueDataSummary.mySupplierCode
                    ORDER BY Total DESC');


        // DB::getQueryLog();

        return $output;
    }

    // Customer Collection Monthly report
    function getCustomerCollectionMonthlyQRY($request)
    {
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $fromYear = $request->year;

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $customers = (array)$request->customers;
        $servicelines = (array)$request->servicelines;
        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();
        $serviceLineSystemID = collect($servicelines)->pluck('serviceLineSystemID')->toArray();


        $currency = $request->currencyID;

        if ($currency == 2) {
            $currencyDocAmount = "IF (erp_generalledger.documentSystemID = '21',documentLocalAmount,0) AS BRVDocumentAmount";

        } else if ($currency == 3) {
            $currencyDocAmount = "IF (erp_generalledger.documentSystemID = '21',documentRptAmount,0) AS BRVDocumentAmount";
        }

        $output = \DB::select('SELECT
        collectionMonthWise.companyID as companyCode,
    collectionMonthWise.CompanyName as CompanyName,
    CustomerName,
    collectionMonthWise.companyID,
    collectionMonthWise.CompanyName,
    DocYEAR,
    sum(Jan) AS Jan,
    sum(Feb) AS Feb,
    sum(March) AS March,
    sum(April) AS April,
    sum(May) AS May,
    sum(June) AS June,
    sum(July) AS July,
    sum(Aug) AS Aug,
    sum(Sept) AS Sept,
    sum(Oct) AS Oct,
    sum(Nov) AS Nov,
    sum(Dece) AS Dece
FROM
    (
        SELECT
            collectionDetail.companyID,
            collectionDetail.CutomerCode,
            collectionDetail.CustomerName,
            collectionDetail.DocYEAR,
            collectionDetail.CompanyName,

        IF (
            collectionDetail.DocMONTH = 1,
            BRVDocumentAmount,
            0
        ) AS Jan,

    IF (
        collectionDetail.DocMONTH = 2,
        BRVDocumentAmount,
        0
    ) AS Feb,

IF (
    collectionDetail.DocMONTH = 3,
    BRVDocumentAmount,
    0
) AS March,

IF (
    collectionDetail.DocMONTH = 4,
    BRVDocumentAmount,
    0
) AS April,

IF (
    collectionDetail.DocMONTH = 5,
    BRVDocumentAmount,
    0
) AS May,

IF (
    collectionDetail.DocMONTH = 6,
    BRVDocumentAmount,
    0
) AS June,

IF (
    collectionDetail.DocMONTH = 7,
    BRVDocumentAmount,
    0
) AS July,

IF (
    collectionDetail.DocMONTH = 8,
    BRVDocumentAmount,
    0
) AS Aug,

IF (
    collectionDetail.DocMONTH = 9,
    BRVDocumentAmount,
    0
) AS Sept,

IF (
    collectionDetail.DocMONTH = 10,
    BRVDocumentAmount,
    0
) AS Oct,

IF (
    collectionDetail.DocMONTH = 11,
    BRVDocumentAmount,
    0
) AS Nov,

IF (
    collectionDetail.DocMONTH = 12,
    BRVDocumentAmount,
    0
) AS Dece
FROM
    (
        SELECT
            erp_generalledger.companyID,
            erp_generalledger.documentID,
            erp_generalledger.serviceLineCode,
            erp_generalledger.documentSystemCode,
            erp_generalledger.documentCode,
            erp_generalledger.documentDate,
            companymaster.CompanyName,
            MONTH (
                erp_generalledger.documentDate
            ) AS DocMONTH,
            YEAR (
                erp_generalledger.documentDate
            ) AS DocYEAR,
            erp_generalledger.supplierCodeSystem,
            customermaster.CutomerCode,
            customermaster.customerShortCode,
            customermaster.CustomerName,
            ' . $currencyDocAmount . '
FROM
    erp_generalledger
INNER JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
WHERE
    erp_generalledger.documentSystemID = 21 AND erp_generalledger.glaccounttypeID = 1
AND DATE(erp_generalledger.documentDate) <= "' . $fromDate . '"
AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
AND erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceLineSystemID) . ')
AND erp_generalledger.documentRptAmount > 0
AND YEAR (
    erp_generalledger.documentDate
) = ' . $fromYear . '
    ) AS collectionDetail
    ) AS collectionMonthWise
GROUP BY
    collectionMonthWise.companyID,
    collectionMonthWise.CutomerCode,
    collectionMonthWise.DocYEAR;');

        return $output;

    }

    // Customer Collection report
    function getCustomerCollectionCNExcelQRY($request)
    {
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $customers = (array)$request->customers;

        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $currency = $request->currencyID;

        if ($currency == 2) {
            $currencyBRVAmount = " collectionDetail.BRVLocalAmount AS BRVDocumentAmount";
            $currencyCNAmount = " collectionDetail.CNLocalAmount AS CNDocumentAmount";
        } else if ($currency == 3) {
            $currencyBRVAmount = " collectionDetail.BRVRptAmount AS BRVDocumentAmount";
            $currencyCNAmount = " collectionDetail.CNRptAmount AS CNDocumentAmount";
        }

        $output = \DB::select('SELECT
    collectionDetail.companyID,
    collectionDetail.CompanyName,
    collectionDetail.CutomerCode,
    collectionDetail.customerShortCode,
    collectionDetail.CustomerName,
    collectionDetail.documentCode,
    collectionDetail.documentDate,
    collectionDetail.documentNarration,
    ' . $currencyCNAmount . '
FROM
    (
        SELECT
            erp_generalledger.companyID,
            erp_generalledger.documentID,
            erp_generalledger.serviceLineCode,
            erp_generalledger.documentSystemCode,
            erp_generalledger.documentCode,
            erp_generalledger.documentDate,
            erp_generalledger.documentNarration,
            companymaster.CompanyName,
            MONTH (
                erp_generalledger.documentDate
            ) AS DocMONTH,
            YEAR (
                erp_generalledger.documentDate
            ) AS DocYEAR,
            erp_generalledger.supplierCodeSystem,
            customermaster.CutomerCode,
            customermaster.customerShortCode,
            customermaster.CustomerName,
IF (
    erp_generalledger.documentSystemID = "19",
    ROUND(documentLocalAmount, 0),
    0
) CNLocalAmount,

IF (
    erp_generalledger.documentSystemID = "19",
    ROUND(documentRptAmount, 0),
    0
) CNRptAmount
FROM
    erp_generalledger
INNER JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
WHERE erp_generalledger.documentSystemID = 19
 AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
AND erp_generalledger.documentRptAmount > 0 ORDER BY erp_generalledger.documentDate ASC
    ) AS collectionDetail');

        return $output;

    }


    // Customer Collection report
    function getCustomerCollectionBRVExcelQRY($request)
    {
        $fromDate = new Carbon($request->fromDate);
        //$fromDate = $fromDate->addDays(1);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $customers = (array)$request->customers;

        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $currency = $request->currencyID;

        if ($currency == 2) {
            $currencyBRVAmount = " collectionDetail.BRVLocalAmount AS BRVDocumentAmount";
        } else if ($currency == 3) {
            $currencyBRVAmount = " collectionDetail.BRVRptAmount AS BRVDocumentAmount";
        }

        $output = \DB::select('SELECT
    collectionDetail.companyID,
    collectionDetail.CompanyName,
    collectionDetail.CutomerCode,
    collectionDetail.customerShortCode,
    collectionDetail.CustomerName,
    collectionDetail.documentCode,
    collectionDetail.documentDate,
    collectionDetail.documentNarration,
    collectionDetail.bankName,
    collectionDetail.AccountNo,
    collectionDetail.CurrencyCode AS bankCurrencyCode,
    ' . $currencyBRVAmount . '
FROM
    (
        SELECT
            erp_generalledger.companyID,
            erp_generalledger.documentID,
            erp_generalledger.serviceLineCode,
            erp_generalledger.documentSystemCode,
            erp_generalledger.documentCode,
            erp_generalledger.documentDate,
            erp_generalledger.documentNarration,
            companymaster.CompanyName,
            MONTH (
                erp_generalledger.documentDate
            ) AS DocMONTH,
            YEAR (
                erp_generalledger.documentDate
            ) AS DocYEAR,
            erp_generalledger.supplierCodeSystem,
            customermaster.CutomerCode,
            customermaster.customerShortCode,
            customermaster.CustomerName,
            erp_bankmaster.bankName,
            erp_bankaccount.AccountNo,
            currencymaster.CurrencyCode,
    IF (
        erp_generalledger.documentSystemID = "21",
        ROUND(documentLocalAmount, 0),
        0
    ) BRVLocalAmount,

IF (
    erp_generalledger.documentSystemID = "21",
    ROUND(documentRptAmount, 0),
    0
) BRVRptAmount
FROM
    erp_generalledger
INNER JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
INNER JOIN erp_customerreceivepayment ON erp_generalledger.documentSystemCode = erp_customerreceivepayment.custReceivePaymentAutoID AND erp_generalledger.companySystemID = erp_customerreceivepayment.companySystemID AND erp_generalledger.documentSystemID = erp_customerreceivepayment.documentSystemID
INNER JOIN erp_bankmaster ON erp_customerreceivepayment.bankID = erp_bankmaster.bankmasterAutoID
INNER JOIN erp_bankaccount ON erp_customerreceivepayment.bankAccount = erp_bankaccount.bankAccountAutoID
INNER JOIN currencymaster ON erp_bankaccount.accountCurrencyID = currencymaster.currencyID
WHERE erp_generalledger.documentSystemID = 21 AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
AND erp_generalledger.documentRptAmount > 0 AND erp_generalledger.glAccountTypeID=1 ORDER BY erp_generalledger.documentDate ASC
    ) AS collectionDetail');

        return $output;

    }


    // Revenue By Customer
    function getRevenueByCustomer($request)
    {
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $customers = (array)$request->customers;

        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $currency = $request->currencyID;

        $isAllCustomerSelected = $request->isAllCustomerSelected;

        $nullCustomer = '';
        if ($isAllCustomerSelected == 1) {
            $nullCustomer = 'OR revenueCustomerDetail.mySupplierCode IS NULL  OR revenueCustomerDetail.mySupplierCode = ""';
        }

        $showVAT = (isset($request->showVAT) && $request->showVAT) ? 1 : 0;

        $output = \DB::select('SELECT
                                revenueCustomerDetail.GeneralLedgerID,
                                revenueCustomerDetail.companySystemID,
                                revenueCustomerDetail.companyID,
                                revenueCustomerDetail.CompanyName,
                                customermaster.CutomerCode,
                                customermaster.CustomerName,
                                revenueCustomerDetail.documentCode,
                                revenueCustomerDetail.documentSystemCode,
                                revenueCustomerDetail.documentSystemID,
                                revenueCustomerDetail.serviceLineCode,
                                revenueCustomerDetail.ContractNumber,
                                revenueCustomerDetail.contractDescription,
                                revenueCustomerDetail.CONTRACT_PO,
                                revenueCustomerDetail.ContEndDate,
                                revenueCustomerDetail.glCode,
                                revenueCustomerDetail.AccountDescription,
                                revenueCustomerDetail.documentDate,
                                IF('.$showVAT.' = 1, revenueCustomerDetail.MyRptAmount + VATAmountRPT , revenueCustomerDetail.MyRptAmount) as RptAmount,
                                IF('.$showVAT.' = 1, revenueCustomerDetail.MyLocalAmount + VATAmountLocal , revenueCustomerDetail.MyLocalAmount) as localAmount,
                                documentLocalCurrency,
                                documentLocalDecimalPlaces,
                                documentRptCurrency,
                                documentRptDecimalPlaces,
                                month(revenueCustomerDetail.documentDate) as PostingMonth,
                                year(revenueCustomerDetail.documentDate) as PostingYear,
                                revenueCustomerDetail.documentNarration
                            FROM
                            (
                            SELECT
                                erp_generalledger.GeneralLedgerID,
                                erp_generalledger.companySystemID,
                                erp_generalledger.companyID,
                                companymaster.CompanyName,
                                erp_generalledger.serviceLineCode,
                                erp_generalledger.clientContractID,
                                contractmaster.ContractNumber,
                                contractmaster.contractDescription,
                                contractmaster.ContEndDate,
                                erp_generalledger.documentID,
                                erp_generalledger.documentSystemCode,
                                erp_generalledger.documentCode,
                                erp_generalledger.documentSystemID,
                                erp_generalledger.documentDate,
                                erp_generalledger.documentNarration,
                                erp_generalledger.serviceLineSystemID,
                                erp_generalledger.chartOfAccountSystemID,
                                erp_generalledger.glCode,
                                erp_generalledger.glAccountType,
                                chartofaccounts.controlAccounts,
                                chartofaccounts.AccountDescription,
                                erp_generalledger.supplierCodeSystem,
                                IF(ISNULL(tax_ledger_details.VATAmountRpt), 0, tax_ledger_details.VATAmountRpt) as VATAmountRPT,
                                IF(ISNULL(tax_ledger_details.VATAmountLocal), 0, tax_ledger_details.VATAmountLocal) as VATAmountLocal,
                                currLocal.CurrencyCode as documentLocalCurrency,
                                currLocal.DecimalPlaces as documentLocalDecimalPlaces,
                                currRpt.CurrencyCode as documentRptCurrency,
                                currRpt.DecimalPlaces as documentRptDecimalPlaces,
                            IF
                                (
                                erp_generalledger.clientContractID = "X" 
                                AND erp_generalledger.supplierCodeSystem = 0,
                                0,
                            IF
                                (
                                erp_generalledger.clientContractID <> "X" 
                                AND erp_generalledger.supplierCodeSystem = 0,
                                contractmaster.clientID,
                            IF
                                ( erp_generalledger.documentSystemID = 11 OR erp_generalledger.documentSystemID = 15 OR erp_generalledger.documentSystemID = 4, contractmaster.clientID, erp_generalledger.supplierCodeSystem ) 
                                ) 
                                ) AS mySupplierCode,
                                erp_generalledger.documentLocalCurrencyID,
                                erp_generalledger.documentLocalAmount,
                                (documentLocalAmount * -1) AS MyLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                erp_generalledger.documentRptAmount,
                                (documentRptAmount * -1) AS MyRptAmount,
                            IF
                                ( contractmaster.isContract = 1, "Contract", "PO" ) AS CONTRACT_PO 
                            FROM
                                erp_generalledger
                                INNER JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
                                INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                                LEFT JOIN tax_ledger_details ON erp_generalledger.chartOfAccountSystemID = tax_ledger_details.chartOfAccountSystemID AND erp_generalledger.documentSystemID = tax_ledger_details.documentSystemID AND  erp_generalledger.documentSystemCode = tax_ledger_details.documentMasterAutoID
                                LEFT JOIN contractmaster ON erp_generalledger.companyID = contractmaster.CompanyID 
                                AND erp_generalledger.clientContractID = contractmaster.ContractNumber
                                LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
                                LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
                                WHERE erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                                AND chartofaccounts.controlAccountsSystemID = 1
                                AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" 
                                AND "' . $toDate . '"
                                ) AS revenueCustomerDetail
                                LEFT JOIN customermaster ON revenueCustomerDetail.mySupplierCode = customermaster.customerCodeSystem
                                WHERE (revenueCustomerDetail.mySupplierCode IN (' . join(',', $customerSystemID) . ')' . $nullCustomer . ')
                                GROUP BY GeneralLedgerID,customermaster.customerCodeSystem, serviceLineSystemID, chartOfAccountSystemID,documentSystemID,documentSystemCode, documentNarration');

        return $output;
    }

    function getCustomerSalesRegisterQRY($request, $search = "")
    {
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $controlAccounts = (array)$request->controlAccountsSystemID;
        $controlAccountsSystemID = collect($controlAccounts)->pluck('id')->toArray();
        $currency = $request->currencyID;

        $customers = (array)$request->customers;
        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $balanceAmountQry = '';
        $receiptAmountQry = '';
        $decimalPlaceQry = '';
        $invoiceAmountQry = '';
        $currencyQry = '';
        if ($currency == 1) {
            $balanceAmountQry = "round(IFNULL(MainQuery.documentTransAmount,0),MainQuery.documentTransDecimalPlaces) - round(IFNULL(InvoiceFromBRVAndMatching.InvoiceTransAmount,0),MainQuery.documentTransDecimalPlaces) AS balanceAmount";
            $receiptAmountQry = "round(IFNULL(InvoiceFromBRVAndMatching.InvoiceTransAmount, 0 ),MainQuery.documentTransDecimalPlaces) AS receiptAmount";
            $invoiceAmountQry = "round(IFNULL(MainQuery.documentTransAmount, 0 ),MainQuery.documentTransDecimalPlaces) AS invoiceAmount";
            $decimalPlaceQry = "MainQuery.documentTransDecimalPlaces AS balanceDecimalPlaces";
            $currencyQry = "MainQuery.documentTransCurrency AS documentCurrency";
        } else if ($currency == 2) {
            $balanceAmountQry = "round(IFNULL(MainQuery.documentLocalAmount,0),MainQuery.documentLocalDecimalPlaces) - round(IFNULL(InvoiceFromBRVAndMatching.InvoiceLocalAmount,0),MainQuery.documentLocalDecimalPlaces) AS balanceAmount";
            $receiptAmountQry = "round(IFNULL(InvoiceFromBRVAndMatching.InvoiceLocalAmount, 0 ),MainQuery.documentLocalDecimalPlaces) AS receiptAmount";
            $invoiceAmountQry = "round(IFNULL(MainQuery.documentLocalAmount, 0 ),MainQuery.documentLocalDecimalPlaces) AS invoiceAmount";
            $decimalPlaceQry = "MainQuery.documentLocalDecimalPlaces AS balanceDecimalPlaces";
            $currencyQry = "MainQuery.documentLocalCurrency AS documentCurrency";
        } else {
            $balanceAmountQry = "round(IFNULL(MainQuery.documentRptAmount,0),MainQuery.documentRptDecimalPlaces) - round(IFNULL(InvoiceFromBRVAndMatching.InvoiceRptAmount,0),MainQuery.documentRptDecimalPlaces) AS balanceAmount";
            $receiptAmountQry = "round(IFNULL(InvoiceFromBRVAndMatching.InvoiceRptAmount, 0 ),MainQuery.documentRptDecimalPlaces) AS receiptAmount";
            $invoiceAmountQry = "round(IFNULL(MainQuery.documentRptAmount, 0 ),MainQuery.documentRptDecimalPlaces) AS invoiceAmount";
            $decimalPlaceQry = "MainQuery.documentRptDecimalPlaces AS balanceDecimalPlaces";
            $currencyQry = "MainQuery.documentRptCurrency AS documentCurrency";
        }

        $filter='';
        if($search){
            $search = str_replace("\\", "\\\\\\\\", $search);
            $filter = " AND (( erp_generalledger.documentCode LIKE '%{$search}%' OR customermaster.CustomerName LIKE '%{$search}%' OR erp_generalledger.invoiceNumber LIKE '%{$search}%' )) ";
        }

        //DB::enableQueryLog();
        $output = \DB::select('SELECT
                MainQuery.companyID,
                MainQuery.CompanyName,
                MainQuery.documentCode,
                MainQuery.documentSystemID,
                MainQuery.documentSystemCode,
                MainQuery.documentDate AS PostedDate,
                MainQuery.clientContractID,
                MainQuery.invoiceDate,
                MainQuery.documentNarration,
                InvoiceFromBRVAndMatching.ReceiptCode,
                InvoiceFromBRVAndMatching.ReceiptDate,
                ' . $balanceAmountQry . ',
                ' . $receiptAmountQry . ',
                ' . $invoiceAmountQry . ',
                ' . $currencyQry . ',
                ' . $decimalPlaceQry . ',
                MainQuery.CutomerCode,
                MainQuery.concatCustomerName,
                MainQuery.CustomerName,
                MainQuery.PONumber,
                MainQuery.serviceLineCode,
                MainQuery.rigNo,
                MainQuery.servicePeriod,
                MainQuery.serviceStartDate,
                MainQuery.serviceEndDate,
                MainQuery.wanNO,
                MainQuery.invoiceNumber,
                MainQuery.invoiceDate,
                MainQuery.invoiceType
            FROM
                (
            SELECT
                erp_generalledger.companySystemID,
                erp_generalledger.companyID,
                companymaster.CompanyName,
                erp_generalledger.serviceLineSystemID,
                erp_generalledger.serviceLineCode,
                erp_generalledger.documentSystemID,
                erp_generalledger.documentID,
                erp_generalledger.documentSystemCode,
                erp_generalledger.documentCode,
                erp_generalledger.documentDate,
                DATE_FORMAT( erp_generalledger.documentDate, "%d/%m/%Y" ) AS documentDateFilter,
                erp_generalledger.invoiceNumber,
                erp_generalledger.invoiceDate,
                erp_generalledger.chartOfAccountSystemID,
                erp_generalledger.glCode,
                erp_generalledger.documentNarration,
                erp_generalledger.clientContractID,
                erp_generalledger.supplierCodeSystem,
                erp_generalledger.documentTransCurrencyID,
                erp_generalledger.documentTransAmount,
                erp_generalledger.documentLocalCurrencyID,
                erp_generalledger.documentLocalAmount,
                erp_generalledger.documentRptCurrencyID,
                erp_generalledger.documentRptAmount,
                currLocal.DecimalPlaces AS documentLocalDecimalPlaces,
                currRpt.DecimalPlaces AS documentRptDecimalPlaces,
                currTrans.DecimalPlaces AS documentTransDecimalPlaces,
                currRpt.CurrencyCode AS documentRptCurrency,
                currLocal.CurrencyCode AS documentLocalCurrency,
                currTrans.CurrencyCode AS documentTransCurrency,
                erp_custinvoicedirect.PONumber,
                erp_custinvoicedirect.rigNo,
                erp_custinvoicedirect.servicePeriod,
                erp_custinvoicedirect.serviceStartDate,
                erp_custinvoicedirect.serviceEndDate,
                erp_custinvoicedirect.wanNO,
                customermaster.CutomerCode,
                customermaster.CustomerName,
                CONCAT( customermaster.CutomerCode, " - ", customermaster.CustomerName ) AS concatCustomerName,
                 IF(erp_custinvoicedirect.isPerforma = 1,"PROFORMA","DIRECT") AS invoiceType
            FROM
                erp_generalledger
                INNER JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem
                LEFT JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                LEFT JOIN currencymaster currTrans ON erp_generalledger.documentTransCurrencyID = currTrans.currencyID
                LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
                LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
                LEFT JOIN erp_custinvoicedirect ON erp_generalledger.documentSystemCode = erp_custinvoicedirect.custInvoiceDirectAutoID AND erp_generalledger.documentSystemID = erp_custinvoicedirect.documentSystemiD AND erp_generalledger.companySystemID = erp_custinvoicedirect.companySystemID
            WHERE
                erp_generalledger.documentSystemID = 20 
                AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '"
                AND "' . $toDate . '"
                 AND ( erp_generalledger.chartOfAccountSystemID IN (' . join(',', $controlAccountsSystemID) . '))
                ' . $filter . '
                AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
                ) AS MainQuery
                LEFT JOIN (
            SELECT
                InvoiceFromUNION.companySystemID,
                InvoiceFromUNION.companyID,
                max( InvoiceFromUNION.custPaymentReceiveCode ) AS ReceiptCode,
                max( InvoiceFromUNION.postedDate ) AS ReceiptDate,
                InvoiceFromUNION.addedDocumentSystemID,
                InvoiceFromUNION.addedDocumentID,
                InvoiceFromUNION.bookingInvCodeSystem,
                InvoiceFromUNION.bookingInvCode,
                sum( InvoiceFromUNION.receiveAmountTrans ) AS InvoiceTransAmount,
                sum( InvoiceFromUNION.receiveAmountLocal ) AS InvoiceLocalAmount,
                sum( InvoiceFromUNION.receiveAmountRpt ) AS InvoiceRptAmount 
            FROM
                (
            SELECT
                * 
            FROM
                (
            SELECT
                erp_customerreceivepayment.custPaymentReceiveCode,
                erp_customerreceivepayment.postedDate,
                erp_custreceivepaymentdet.companySystemID,
                erp_custreceivepaymentdet.companyID,
                erp_custreceivepaymentdet.addedDocumentSystemID,
                erp_custreceivepaymentdet.addedDocumentID,
                erp_custreceivepaymentdet.bookingInvCodeSystem,
                erp_custreceivepaymentdet.bookingInvCode,
                erp_custreceivepaymentdet.receiveAmountTrans,
                erp_custreceivepaymentdet.receiveAmountLocal,
                erp_custreceivepaymentdet.receiveAmountRpt 
            FROM
                erp_customerreceivepayment
                INNER JOIN erp_custreceivepaymentdet ON erp_customerreceivepayment.custReceivePaymentAutoID = erp_custreceivepaymentdet.custReceivePaymentAutoID 
                AND erp_custreceivepaymentdet.matchingDocID = 0 
                AND erp_customerreceivepayment.approved =- 1 
            WHERE
                erp_custreceivepaymentdet.bookingInvCode <> "0" 
                AND erp_custreceivepaymentdet.matchingDocID = 0 
                AND erp_customerreceivepayment.approved =- 1 
                AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
                            AND DATE(erp_customerreceivepayment.postedDate) <= "' . $toDate . '"
                ) AS InvoiceFromBRV UNION ALL
            SELECT
                * 
            FROM
                (
            SELECT
                erp_matchdocumentmaster.matchingDocCode,
                erp_matchdocumentmaster.matchingDocdate,
                erp_custreceivepaymentdet.companySystemID,
                erp_custreceivepaymentdet.companyID,
                erp_custreceivepaymentdet.addedDocumentSystemID,
                erp_custreceivepaymentdet.addedDocumentID,
                erp_custreceivepaymentdet.bookingInvCodeSystem,
                erp_custreceivepaymentdet.bookingInvCode,
                erp_custreceivepaymentdet.receiveAmountTrans,
                erp_custreceivepaymentdet.receiveAmountLocal,
                erp_custreceivepaymentdet.receiveAmountRpt 
            FROM
                erp_custreceivepaymentdet
                INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID 
                AND erp_custreceivepaymentdet.companySystemID = erp_matchdocumentmaster.companySystemID 
            WHERE
                erp_matchdocumentmaster.matchingConfirmedYN = 1 
                AND erp_custreceivepaymentdet.companySystemID  IN (' . join(',', $companyID) . ')
                            AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $toDate . '"
                ) AS InvoiceFromMatching 
                ) AS InvoiceFromUNION 
            GROUP BY
                bookingInvCode 
                ) AS InvoiceFromBRVAndMatching ON InvoiceFromBRVAndMatching.addedDocumentSystemID = MainQuery.documentSystemID 
                AND MainQuery.documentSystemCode = InvoiceFromBRVAndMatching.bookingInvCodeSystem ORDER BY postedDate ASC;');

        return $output;
    }

    function getCustomerSummaryRevenueQRY($request)
    {
        $asOfDate = new Carbon($request->fromDate);
        $asOfDate = $asOfDate->format('Y-m-d');
        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $customers = (array)$request->customers;
        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $controlAccountsSystemID = $request->controlAccountsSystemID;

        $currency = $request->currencyID;
        $year = date('Y', strtotime($asOfDate));


        $currencyClm = "MyRptAmount";

        if ($currency == 2) {
            $currencyClm = "MyLocalAmount";
        } else if ($currency == 3) {
            $currencyClm = "MyRptAmount";
        }

        $output = \DB::select('SELECT
                    revenueDataSummary.companyID,
                    revenueDataSummary.CutomerCode,
                    revenueDataSummary.CustomerName,
                    revenueDataSummary.CompanyName,
                    revenueDataSummary.DocYEAR,
                    documentLocalCurrencyID,
                    documentRptCurrencyID,
                    sum(Jan) as Jan,
                    sum(Feb) as Feb,
                    sum(March) as March,
                    sum(April) as April,
                    sum(May) as May,
                    sum(June) as June,
                    sum(July) as July,
                    sum(Aug) as Aug,
                    sum(Sept) as Sept,
                    sum(Oct) as Oct,
                    sum(Nov) as Nov,
                    sum(Dece) as Dece,
                    sum(Total) as Total
                FROM
                (
                SELECT
                    revenueDetailData.documentLocalCurrencyID,
                    revenueDetailData.documentRptCurrencyID,
                    revenueDetailData.companySystemID,
                    revenueDetailData.companyID,
                    revenueDetailData.CompanyName,
                    revenueDetailData.mySupplierCode,
                    customermaster.CutomerCode,
                    customermaster.CustomerName,
                    revenueDetailData.DocYEAR,
                IF
                    ( revenueDetailData.DocMONTH = 1, ' . $currencyClm . ', 0 ) AS Jan,
                IF
                    ( revenueDetailData.DocMONTH = 2, ' . $currencyClm . ', 0 ) AS Feb,
                IF
                    ( revenueDetailData.DocMONTH = 3, ' . $currencyClm . ', 0 ) AS March,
                IF
                    ( revenueDetailData.DocMONTH = 4, ' . $currencyClm . ', 0 ) AS April,
                IF
                    ( revenueDetailData.DocMONTH = 5, ' . $currencyClm . ', 0 ) AS May,
                IF
                    ( revenueDetailData.DocMONTH = 6, ' . $currencyClm . ', 0 ) AS June,
                IF
                    ( revenueDetailData.DocMONTH = 7, ' . $currencyClm . ', 0 ) AS July,
                IF
                    ( revenueDetailData.DocMONTH = 8, ' . $currencyClm . ', 0 ) AS Aug,
                IF
                    ( revenueDetailData.DocMONTH = 9, ' . $currencyClm . ', 0 ) AS Sept,
                IF
                    ( revenueDetailData.DocMONTH = 10, ' . $currencyClm . ', 0 ) AS Oct,
                IF
                    ( revenueDetailData.DocMONTH = 11, ' . $currencyClm . ', 0 ) AS Nov,
                IF
                    ( revenueDetailData.DocMONTH = 12, ' . $currencyClm . ', 0 ) AS Dece,
                    ' . $currencyClm . ' as Total
                FROM
                    (
                SELECT
                    erp_generalledger.companySystemID,
                    erp_generalledger.companyID,
                    companymaster.CompanyName,
                    erp_generalledger.serviceLineSystemID,
                    erp_generalledger.serviceLineCode,
                    erp_generalledger.clientContractID,
                    contractmaster.ContractNumber,
                    erp_generalledger.documentID,
                    erp_generalledger.documentSystemCode,
                    erp_generalledger.documentCode,
                    erp_generalledger.documentDate,
                    MONTH ( erp_generalledger.documentDate ) AS DocMONTH,
                    YEAR ( erp_generalledger.documentDate ) AS DocYEAR,
                    erp_generalledger.documentNarration,
                    erp_generalledger.chartOfAccountSystemID,
                    erp_generalledger.glCode,
                    erp_generalledger.glAccountType,
                    chartofaccounts.controlAccounts,
                    erp_generalledger.supplierCodeSystem,
                IF
                    (
                    erp_generalledger.clientContractID = "X"
                    AND erp_generalledger.supplierCodeSystem = 0,
                    0,
                IF
                    (
                    erp_generalledger.clientContractID <> "X"
                    AND erp_generalledger.supplierCodeSystem = 0,
                    contractmaster.clientID,
                IF
                    ( erp_generalledger.documentID = "SI" OR erp_generalledger.documentID = "DN" OR erp_generalledger.documentID = "PV", contractmaster.clientID, erp_generalledger.supplierCodeSystem )
                    )
                    ) AS mySupplierCode,
                    erp_generalledger.documentLocalCurrencyID,
                    erp_generalledger.documentRptCurrencyID,
                    erp_generalledger.documentLocalAmount,
                    round((erp_generalledger.documentLocalAmount *- 1),0) AS MyLocalAmount,
                    erp_generalledger.documentRptAmount,
                    round((erp_generalledger.documentRptAmount *- 1),0) AS MyRptAmount
                FROM
                    erp_generalledger
                    INNER JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
                    LEFT JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                    LEFT JOIN contractmaster ON erp_generalledger.clientContractID = contractmaster.ContractNumber
                    AND erp_generalledger.companyID = contractmaster.CompanyID
                WHERE
                    DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
                    AND YEAR ( erp_generalledger.documentDate ) = "' . $year . '"
                    AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                    AND chartofaccounts.controlAccountsSystemID = 1
                    ) AS revenueDetailData
                    LEFT JOIN customermaster ON customermaster.customerCodeSystem = revenueDetailData.mySupplierCode
                WHERE
                    (
                        revenueDetailData.mySupplierCode IN (' . join(',', $customerSystemID) . ')
                    )
                    ) AS revenueDataSummary
                GROUP BY
                    revenueDataSummary.companySystemID,
                    revenueDataSummary.mySupplierCode
                ORDER BY
                    Total DESC');
        return $output;
    }

    // Customer Collection Monthly report
    function getCustomerSummaryCollectionQRY($request)
    {
        $fromDate = new Carbon($request->fromDate);
        //$fromDate = $fromDate->addDays(1);
        $fromDate = $fromDate->format('Y-m-d');

        $fromYear = date('Y', strtotime($fromDate));

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $currency = $request->currencyID;

        $customers = (array)$request->customers;
        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        if ($currency == 2) {
            $currencyDocAmount = "IF (erp_generalledger.documentSystemID = 21,documentLocalAmount,0) AS BRVDocumentAmount";

        } else if ($currency == 3) {
            $currencyDocAmount = "IF (erp_generalledger.documentSystemID = 21,documentRptAmount,0) AS BRVDocumentAmount";
        }

        $output = \DB::select('SELECT
        collectionMonthWise.companyID as companyCode,
    collectionMonthWise.CompanyName as CompanyName,
    CustomerName,
    CutomerCode,
    collectionMonthWise.companyID,
    collectionMonthWise.CompanyName,
    DocYEAR,
    sum(Jan) AS Jan,
    sum(Feb) AS Feb,
    sum(March) AS March,
    sum(April) AS April,
    sum(May) AS May,
    sum(June) AS June,
    sum(July) AS July,
    sum(Aug) AS Aug,
    sum(Sept) AS Sept,
    sum(Oct) AS Oct,
    sum(Nov) AS Nov,
    sum(Dece) AS Dece,
    sum(Total) AS Total
FROM
    (
        SELECT
            collectionDetail.companyID,
            collectionDetail.CutomerCode,
            collectionDetail.CustomerName,
            collectionDetail.DocYEAR,
            collectionDetail.CompanyName,

        IF (
            collectionDetail.DocMONTH = 1,
            BRVDocumentAmount,
            0
        ) AS Jan,

    IF (
        collectionDetail.DocMONTH = 2,
        BRVDocumentAmount,
        0
    ) AS Feb,

IF (
    collectionDetail.DocMONTH = 3,
    BRVDocumentAmount,
    0
) AS March,

IF (
    collectionDetail.DocMONTH = 4,
    BRVDocumentAmount,
    0
) AS April,

IF (
    collectionDetail.DocMONTH = 5,
    BRVDocumentAmount,
    0
) AS May,

IF (
    collectionDetail.DocMONTH = 6,
    BRVDocumentAmount,
    0
) AS June,

IF (
    collectionDetail.DocMONTH = 7,
    BRVDocumentAmount,
    0
) AS July,

IF (
    collectionDetail.DocMONTH = 8,
    BRVDocumentAmount,
    0
) AS Aug,

IF (
    collectionDetail.DocMONTH = 9,
    BRVDocumentAmount,
    0
) AS Sept,

IF (
    collectionDetail.DocMONTH = 10,
    BRVDocumentAmount,
    0
) AS Oct,

IF (
    collectionDetail.DocMONTH = 11,
    BRVDocumentAmount,
    0
) AS Nov,

IF (
    collectionDetail.DocMONTH = 12,
    BRVDocumentAmount,
    0
) AS Dece,
BRVDocumentAmount as Total
FROM
    (
        SELECT
            erp_generalledger.companyID,
            erp_generalledger.documentID,
            erp_generalledger.serviceLineCode,
            erp_generalledger.documentSystemCode,
            erp_generalledger.documentCode,
            erp_generalledger.documentDate,
            companymaster.CompanyName,
            MONTH (
                erp_generalledger.documentDate
            ) AS DocMONTH,
            YEAR (
                erp_generalledger.documentDate
            ) AS DocYEAR,
            erp_generalledger.supplierCodeSystem,
            customermaster.CutomerCode,
            customermaster.customerShortCode,
            customermaster.CustomerName,
            ' . $currencyDocAmount . '
FROM
    erp_generalledger
INNER JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
WHERE
    erp_generalledger.documentSystemID = 21
AND DATE(erp_generalledger.documentDate) <= "' . $fromDate . '"
AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
AND erp_generalledger.documentRptAmount > 0
AND erp_generalledger.glaccounttypeID = 1 
AND YEAR (
    erp_generalledger.documentDate
) = ' . $fromYear . '
    ) AS collectionDetail
    ) AS collectionMonthWise
GROUP BY
    collectionMonthWise.companyID,
    collectionMonthWise.DocYEAR,collectionMonthWise.CutomerCode;');

        return $output;

    }

    function getCustomerSummaryOutstandingQRY($request)
    {
        $asOfDate = new Carbon($request->fromDate);
        $asOfDate = $asOfDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $customers = (array)$request->customers;
        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $controlAccountsSystemID = $request->controlAccountsSystemID;
        $year = $request->year;

        $currency = $request->currencyID;
        $currencyQry = '';
        $amountQry = '';
        $decimalPlaceQry = '';
        $whereQry = '';

        $headerMonth = '';
        $mainqueryMonth = '';
        $generalLedgerMonth = '';
        $matchedMonth = '';
        $customeRreceiveMonth = '';
        $invoiceMonth = '';

        $monthArray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');

        foreach ($monthArray as $key => $mon) {
            $headerMonth .= "round(final.balanceRpt" . $mon . ",final.documentRptDecimalPlaces) AS balanceAmount" . $mon . ",";
            $mainqueryMonth .= "(
                mainQuery.documentRptAmount" . $mon . " + (

                    IF (
                        matchedBRV.MatchedBRVRptAmount" . $mon . " IS NULL,
                        0,
                        matchedBRV.MatchedBRVRptAmount" . $mon . "
                    )
                ) + (

                    IF (
                        InvoicedBRV.BRVRptAmount" . $mon . " IS NULL,
                        0,
                        InvoicedBRV.BRVRptAmount" . $mon . "
                    )
                ) + (

                    IF (
                        InvoiceFromBRVAndMatching.InvoiceRptAmount" . $mon . " IS NULL,
                        0,
                        InvoiceFromBRVAndMatching.InvoiceRptAmount" . $mon . " *- 1
                    )
                )
            ) AS balanceRpt" . $mon . ",";
            $generalLedgerMonth .= "IF (
                    MONTH (
                        erp_generalledger.documentDate
                    ) = " . $key . ",
                    erp_generalledger.documentRptAmount,
                    0
                ) AS documentRptAmount" . $mon . ",";
            $matchedMonth .= "sum(
                    IF (
                        MONTH (
                            erp_matchdocumentmaster.matchingDocdate
                        ) = " . $key . ",
                        erp_custreceivepaymentdet.receiveAmountRpt,
                        0
                    )
                ) AS MatchedBRVRptAmount" . $mon . ",";
            $customeRreceiveMonth .= "sum(
                    IF (
                        MONTH (
                            erp_customerreceivepayment.postedDate
                        ) = " . $key . ",
                        erp_custreceivepaymentdet.receiveAmountRpt,
                        0
                    )
                ) AS BRVRptAmount" . $mon . ",";
            $invoiceMonth .= "sum(
                    IF (
                        docMonth = " . $key . ",
                        receiveAmountRpt,
                        0
                    )
                ) AS InvoiceRptAmount" . $mon . ",";
        }

        $query = 'SELECT
    final.companyID,
    final.chartOfAccountSystemID,
    final.CompanyName,
    final.AccountDescription,
    final.glAccountType,
    ' . $headerMonth . '
    round(final.balanceRptTot,final.documentRptDecimalPlaces) AS balanceAmountTot,
    final.documentRptCurrency AS documentCurrency,
    final.documentRptDecimalPlaces AS balanceDecimalPlaces
FROM
    (
        SELECT
            mainQuery.companySystemID,
            mainQuery.companyID,
            mainQuery.CompanyName,
            mainQuery.serviceLineSystemID,
            mainQuery.serviceLineCode,
            mainQuery.documentSystemID,
            mainQuery.documentID,
            mainQuery.documentSystemCode,
            mainQuery.documentCode,
            mainQuery.documentDate,
            mainQuery.documentDateFilter,
            mainQuery.invoiceNumber,
            mainQuery.invoiceDate,
            mainQuery.glCode,
            mainQuery.glAccountType,
            mainQuery.documentNarration,
            mainQuery.clientContractID,
            mainQuery.supplierCodeSystem,
            mainQuery.documentTransCurrencyID,
            mainQuery.documentTransCurrency,
            mainQuery.documentTransAmount,
            mainQuery.documentTransDecimalPlaces,
            mainQuery.documentLocalCurrencyID,
            mainQuery.documentLocalCurrency,
            mainQuery.documentLocalAmount,
            mainQuery.documentLocalDecimalPlaces,
            mainQuery.documentRptCurrencyID,
            mainQuery.documentRptCurrency,
            mainQuery.documentRptDecimalPlaces,
            mainQuery.AccountDescription,
            ' . $mainqueryMonth . '
            (
                mainQuery.documentRptAmountTot + (

                    IF (
                        matchedBRV.MatchedBRVRptAmountTot IS NULL,
                        0,
                        matchedBRV.MatchedBRVRptAmountTot
                    )
                ) + (

                    IF (
                        InvoicedBRV.BRVRptAmountTot IS NULL,
                        0,
                        InvoicedBRV.BRVRptAmountTot
                    )
                ) + (

                    IF (
                        InvoiceFromBRVAndMatching.InvoiceRptAmountTot IS NULL,
                        0,
                        InvoiceFromBRVAndMatching.InvoiceRptAmountTot *- 1
                    )
                )
            ) AS balanceRptTot,
            mainQuery.customerName,
            mainQuery.chartOfAccountSystemID,
            mainQuery.PONumber
        FROM
            (
                SELECT
                    erp_generalledger.companySystemID,
                    erp_generalledger.companyID,
                    companymaster.CompanyName,
                    erp_generalledger.serviceLineSystemID,
                    erp_generalledger.serviceLineCode,
                    erp_generalledger.documentSystemID,
                    erp_generalledger.documentID,
                    erp_generalledger.documentSystemCode,
                    erp_generalledger.documentCode,
                    erp_generalledger.documentDate,
                    DATE_FORMAT(documentDate, "%d/%m/%Y") AS documentDateFilter,
                    erp_generalledger.documentYear,
                    erp_generalledger.documentMonth,
                    erp_generalledger.chequeNumber,
                    erp_generalledger.invoiceNumber,
                    erp_generalledger.glAccountType,
                    erp_generalledger.invoiceDate,
                    erp_generalledger.chartOfAccountSystemID,
                    erp_generalledger.glCode,
                    erp_generalledger.documentNarration,
                    erp_generalledger.clientContractID,
                    erp_generalledger.supplierCodeSystem,
                    erp_generalledger.documentTransCurrencyID,
                    currTrans.CurrencyCode AS documentTransCurrency,
                    currTrans.DecimalPlaces AS documentTransDecimalPlaces,
                    erp_generalledger.documentTransAmount,
                    erp_generalledger.documentLocalCurrencyID,
                    currLocal.CurrencyCode AS documentLocalCurrency,
                    currLocal.DecimalPlaces AS documentLocalDecimalPlaces,
                    erp_generalledger.documentLocalAmount,
                    erp_generalledger.documentRptCurrencyID,
                    currRpt.CurrencyCode AS documentRptCurrency,
                    currRpt.DecimalPlaces AS documentRptDecimalPlaces,
                    ' . $generalLedgerMonth . '
                    erp_generalledger.documentRptAmount as documentRptAmountTot,
                erp_generalledger.documentType,
                chartofaccounts.AccountDescription,
                MONTH (
                    erp_generalledger.documentDate
                ) AS DocMONTH,
                YEAR (
                    erp_generalledger.documentDate
                ) AS DocYEAR,
                erp_custinvoicedirect.PONumber,
                CONCAT(
                    customermaster.CutomerCode,
                    " - ",
                    customermaster.CustomerName
                ) AS customerName
            FROM
                erp_generalledger
            LEFT JOIN currencymaster currTrans ON erp_generalledger.documentTransCurrencyID = currTrans.currencyID
            LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
            LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
            LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
            LEFT JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
            LEFT JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
            LEFT JOIN erp_custinvoicedirect ON erp_generalledger.documentSystemCode = erp_custinvoicedirect.custInvoiceDirectAutoID
            AND erp_generalledger.documentSystemID = erp_custinvoicedirect.documentSystemiD
            AND erp_generalledger.companySystemID = erp_custinvoicedirect.companySystemID
            WHERE
                (
                    erp_generalledger.documentSystemID = "20"
                    OR erp_generalledger.documentSystemID = "19"
                    OR erp_generalledger.documentSystemID = "21"
                )
        AND DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
        AND YEAR (erp_generalledger.documentDate) = "' . $year . '"
        AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
        AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
            ) AS mainQuery
        LEFT JOIN (
            SELECT
                erp_matchdocumentmaster.companySystemID,
                erp_matchdocumentmaster.documentSystemID,
                erp_matchdocumentmaster.PayMasterAutoId,
                erp_matchdocumentmaster.BPVcode,
                 ' . $matchedMonth . '
                sum(erp_custreceivepaymentdet.receiveAmountRpt) AS MatchedBRVRptAmountTot,
                sum(
                    erp_custreceivepaymentdet.receiveAmountTrans
                ) AS MatchedBRVTransAmount,
                sum(
                    erp_custreceivepaymentdet.receiveAmountLocal
                ) AS MatchedBRVLocalAmount
            FROM
                erp_matchdocumentmaster
            INNER JOIN erp_custreceivepaymentdet ON erp_matchdocumentmaster.companyID = erp_custreceivepaymentdet.companyID
            AND erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID
            WHERE
                erp_matchdocumentmaster.matchingConfirmedYN = 1
            AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '"
            AND YEAR (erp_matchdocumentmaster.matchingDocdate) = "' . $year . '"
            AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
            AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
            GROUP BY
                erp_matchdocumentmaster.PayMasterAutoId,
                erp_matchdocumentmaster.BPVcode
        ) AS matchedBRV ON mainQuery.documentSystemID = matchedBRV.documentSystemID
        AND mainQuery.companySystemID = matchedBRV.companySystemID
        AND matchedBRV.PayMasterAutoId = mainQuery.documentSystemCode
        LEFT JOIN (
            SELECT
                erp_customerreceivepayment.custReceivePaymentAutoID,
                erp_customerreceivepayment.companySystemID,
                erp_customerreceivepayment.documentSystemID,
                ' . $customeRreceiveMonth . '
                sum(erp_custreceivepaymentdet.receiveAmountRpt) AS BRVRptAmountTot,
                erp_customerreceivepayment.custPaymentReceiveCode
            FROM
                erp_customerreceivepayment
            INNER JOIN erp_custreceivepaymentdet ON erp_customerreceivepayment.custReceivePaymentAutoID = erp_custreceivepaymentdet.custReceivePaymentAutoID
            WHERE
                erp_custreceivepaymentdet.bookingInvCode <> "0"
            AND erp_custreceivepaymentdet.matchingDocID = 0
            AND erp_customerreceivepayment.approved =- 1
            AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '"
            AND YEAR (erp_customerreceivepayment.postedDate) = "' . $year . '"
            AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
            AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
            GROUP BY
                custReceivePaymentAutoID
        ) AS InvoicedBRV ON mainQuery.documentSystemID = InvoicedBRV.documentSystemID
        AND mainQuery.documentSystemCode = InvoicedBRV.custReceivePaymentAutoID
        LEFT JOIN (
            SELECT
                companySystemID,
                companyID,
                addedDocumentSystemID,
                addedDocumentID,
                bookingInvCodeSystem,
                bookingInvCode,
                ' . $invoiceMonth . '
                sum(receiveAmountRpt) AS InvoiceRptAmountTot,
                sum(receiveAmountTrans) AS InvoiceTransAmount,
                sum(receiveAmountLocal) AS InvoiceLocalAmount
            FROM
                (
                    SELECT
                        *
                    FROM
                        (
                            SELECT
                                erp_customerreceivepayment.custPaymentReceiveCode,
                                erp_custreceivepaymentdet.companySystemID,
                                erp_custreceivepaymentdet.companyID,
                                erp_custreceivepaymentdet.addedDocumentSystemID,
                                erp_custreceivepaymentdet.addedDocumentID,
                                erp_custreceivepaymentdet.bookingInvCodeSystem,
                                erp_custreceivepaymentdet.bookingInvCode,
                                erp_custreceivepaymentdet.receiveAmountTrans,
                                erp_custreceivepaymentdet.receiveAmountLocal,
                                erp_custreceivepaymentdet.receiveAmountRpt,
                                MONTH (
                                    erp_customerreceivepayment.postedDate
                                ) AS docMonth
                            FROM
                                erp_customerreceivepayment
                            INNER JOIN erp_custreceivepaymentdet ON erp_customerreceivepayment.custReceivePaymentAutoID = erp_custreceivepaymentdet.custReceivePaymentAutoID
                            AND erp_custreceivepaymentdet.matchingDocID = 0
                            AND erp_customerreceivepayment.approved =- 1
                            WHERE
                                erp_custreceivepaymentdet.bookingInvCode <> "0"
                            AND erp_custreceivepaymentdet.matchingDocID = 0
                            AND erp_customerreceivepayment.approved =- 1
                            AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '"
                            AND YEAR (erp_customerreceivepayment.postedDate) = "' . $year . '"
                            AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
                            AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
                        ) AS InvoiceFromBRV
                    UNION ALL
                        SELECT
                            *
                        FROM
                            (
                                SELECT
                                    erp_matchdocumentmaster.matchingDocCode,
                                    erp_custreceivepaymentdet.companySystemID,
                                    erp_custreceivepaymentdet.companyID,
                                    erp_custreceivepaymentdet.addedDocumentSystemID,
                                    erp_custreceivepaymentdet.addedDocumentID,
                                    erp_custreceivepaymentdet.bookingInvCodeSystem,
                                    erp_custreceivepaymentdet.bookingInvCode,
                                    erp_custreceivepaymentdet.receiveAmountTrans,
                                    erp_custreceivepaymentdet.receiveAmountLocal,
                                    erp_custreceivepaymentdet.receiveAmountRpt,
                                    MONTH (
                                        erp_matchdocumentmaster.matchingDocdate
                                    ) AS docMonth
                                FROM
                                    erp_custreceivepaymentdet
                                INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID
                                AND erp_custreceivepaymentdet.companySystemID = erp_matchdocumentmaster.companySystemID
                                WHERE
                                    erp_matchdocumentmaster.matchingConfirmedYN = 1
                                AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '"
                                AND YEAR (erp_matchdocumentmaster.matchingDocdate) = "' . $year . '"
                                AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
                            ) AS InvoiceFromMatching
                ) AS InvoiceFromUNION
            GROUP BY
                bookingInvCode
        ) AS InvoiceFromBRVAndMatching ON InvoiceFromBRVAndMatching.addedDocumentSystemID = mainQuery.documentSystemID
        AND mainQuery.documentSystemCode = InvoiceFromBRVAndMatching.bookingInvCodeSystem
        LEFT JOIN chartofaccounts ON mainQuery.documentSystemCode = chartofaccounts.chartOfAccountSystemID
    ) AS final
WHERE
    final.glAccountType = "BS"
GROUP BY
    final.companyID,
    final.chartOfAccountSystemID
ORDER BY
    final.companyID ASC;';

        return \DB::select($query);
    }

    function getCustomerSummaryOutstandingUpdatedQRY($request)
    {

        $asOfDate = new Carbon($request->fromDate);
        $asOfDate = $asOfDate->format('Y-m-d');
        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $customers = (array)$request->customers;
        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $currency = $request->currencyID;
        $year = $request->year;

        $currencyClm = "CustomerBalanceSummary_Detail.documentRptAmount";

        if ($currency == 2) {
            $currencyClm = "CustomerBalanceSummary_Detail.documentLocalAmount";
        } else if ($currency == 3) {
            $currencyClm = "CustomerBalanceSummary_Detail.documentRptAmount";
        }

        return \DB::select('SELECT
    CustomerBalanceSummary_Summary.companySystemID,
    CustomerBalanceSummary_Summary.companyID,
    CustomerBalanceSummary_Summary.CompanyName,
    CustomerBalanceSummary_Summary.chartOfAccountSystemID,
    CustomerBalanceSummary_Summary.AccountDescription,
    sum(Jan) AS balanceAmountJan,
    sum(Feb) AS balanceAmountFeb,
    sum(March) AS balanceAmountMar,
    sum(April) AS balanceAmountApr,
    sum(May) AS balanceAmountMay,
    sum(June) AS balanceAmountJun,
    sum(July) AS balanceAmountJul,
    sum(Aug) AS balanceAmountAug,
    sum(Sept) AS balanceAmountSep,
    sum(Oct) AS balanceAmountOct,
    sum(Nov) AS balanceAmountNov,
    sum(Dece) AS balanceAmountDec,
    sum(balanceTotal) AS balanceAmountTot
FROM
    (
        SELECT
            CustomerBalanceSummary_Detail.companySystemID,
            CustomerBalanceSummary_Detail.companyID,
            CustomerBalanceSummary_Detail.CompanyName,
            CustomerBalanceSummary_Detail.supplierCodeSystem,
            CustomerBalanceSummary_Detail.CutomerCode,
            CustomerBalanceSummary_Detail.CustomerName,
            CustomerBalanceSummary_Detail.documentLocalCurrencyID,
            CustomerBalanceSummary_Detail.documentDate,
            CustomerBalanceSummary_Detail.DocMONTH,

        IF (
            CustomerBalanceSummary_Detail.DocMONTH = 1,
            ' . $currencyClm . ',
            0
        ) AS Jan,

    IF (
        CustomerBalanceSummary_Detail.DocMONTH = 2,
        ' . $currencyClm . ',
        0
    ) AS Feb,

IF (
    CustomerBalanceSummary_Detail.DocMONTH = 3,
    ' . $currencyClm . ',
    0
) AS March,

IF (
    CustomerBalanceSummary_Detail.DocMONTH = 4,
    ' . $currencyClm . ',
    0
) AS April,

IF (
    CustomerBalanceSummary_Detail.DocMONTH = 5,
    ' . $currencyClm . ',
    0
) AS May,

IF (
    CustomerBalanceSummary_Detail.DocMONTH = 6,
    ' . $currencyClm . ',
    0
) AS June,

IF (
    CustomerBalanceSummary_Detail.DocMONTH = 7,
    ' . $currencyClm . ',
    0
) AS July,

IF (
    CustomerBalanceSummary_Detail.DocMONTH = 8,
    ' . $currencyClm . ',
    0
) AS Aug,

IF (
    CustomerBalanceSummary_Detail.DocMONTH = 9,
    ' . $currencyClm . ',
    0
) AS Sept,

IF (
    CustomerBalanceSummary_Detail.DocMONTH = 10,
    ' . $currencyClm . ',
    0
) AS Oct,

IF (
    CustomerBalanceSummary_Detail.DocMONTH = 11,
    ' . $currencyClm . ',
    0
) AS Nov,

IF (
    CustomerBalanceSummary_Detail.DocMONTH = 12,
    ' . $currencyClm . ',
    0
) AS Dece,
 ' . $currencyClm . ' AS balanceTotal,
 CustomerBalanceSummary_Detail.documentLocalCurrency,
 CustomerBalanceSummary_Detail.documentRptCurrency,
 CustomerBalanceSummary_Detail.chartOfAccountSystemID,
 CustomerBalanceSummary_Detail.AccountDescription
FROM
    (
        SELECT
            erp_generalledger.companySystemID,
            erp_generalledger.companyID,
            erp_generalledger.documentID,
            erp_generalledger.documentSystemCode,
            erp_generalledger.documentCode,
            erp_generalledger.documentDate,
            MONTH (
                erp_generalledger.documentDate
            ) AS DocMONTH,
            erp_generalledger.glCode,
            erp_generalledger.supplierCodeSystem,
            customermaster.CutomerCode,
            customermaster.CustomerName,
            erp_generalledger.documentLocalCurrencyID,
            erp_generalledger.documentLocalAmount,
            erp_generalledger.documentRptCurrencyID,
            erp_generalledger.documentRptAmount,
            erp_generalledger.chartOfAccountSystemID,
            currLocal.CurrencyCode AS documentLocalCurrency,
            currRpt.CurrencyCode AS documentRptCurrency,
            companymaster.CompanyName,
            chartofaccounts.AccountDescription
        FROM
            erp_generalledger
        INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
        INNER JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem
        AND customermaster.custGLAccountSystemID = erp_generalledger.chartOfAccountSystemID
        LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
        LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
        LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
        WHERE
            (
                erp_generalledger.documentSystemID = 20
                OR erp_generalledger.documentSystemID = 19
                OR erp_generalledger.documentSystemID = 21
            )
        AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
        AND DATE(
            erp_generalledger.documentDate
        ) <= "' . $asOfDate . '"
        AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
        AND YEAR (
            erp_generalledger.documentDate
        ) = "' . $year . '"
    ) AS CustomerBalanceSummary_Detail
    ) AS CustomerBalanceSummary_Summary
GROUP BY
    CustomerBalanceSummary_Summary.companySystemID,
    CustomerBalanceSummary_Summary.chartOfAccountSystemID');
    }


    function getCustomerSummaryRevenueServiceLineBaseQRY($request)
    {
        $asOfDate = new Carbon($request->fromDate);
        $asOfDate = $asOfDate->format('Y-m-d');
        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $customers = (array)$request->customers;
        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();


        $currency = $request->currencyID;
        $year = date('Y', strtotime($asOfDate));

        $currencyClm = "MyRptAmount";

        if ($currency == 2) {
            $currencyClm = "MyLocalAmount";
        } else if ($currency == 3) {
            $currencyClm = "MyRptAmount";
        }

        return \DB::select('SELECT
    revenueDataSummary.companyID,
    revenueDataSummary.CompanyName,
    revenueDataSummary.DocYEAR,
    revenueDataSummary.serviceLineSystemID,
    revenueDataSummary.ServiceLineDes,
    documentRptCurrencyID,
    sum(Jan) AS Jan,
    sum(Feb) AS Feb,
    sum(March) AS March,
    sum(April) AS April,
    sum(May) AS May,
    sum(June) AS June,
    sum(July) AS July,
    sum(Aug) AS Aug,
    sum(Sept) AS Sept,
    sum(Oct) AS Oct,
    sum(Nov) AS Nov,
    sum(Dece) AS Dece,
    sum(Total) AS Total
FROM
    (
        SELECT
            revenueDetailData.documentLocalCurrencyID,
            revenueDetailData.documentRptCurrencyID,
            revenueDetailData.companySystemID,
            revenueDetailData.companyID,
            revenueDetailData.serviceLineSystemID,
            revenueDetailData.ServiceLineDes,
            revenueDetailData.CompanyName,
            revenueDetailData.mySupplierCode,
            customermaster.CutomerCode,
            customermaster.CustomerName,
            revenueDetailData.DocYEAR,
                IF
                    ( revenueDetailData.DocMONTH = 1, ' . $currencyClm . ', 0 ) AS Jan,
                IF
                    ( revenueDetailData.DocMONTH = 2, ' . $currencyClm . ', 0 ) AS Feb,
                IF
                    ( revenueDetailData.DocMONTH = 3, ' . $currencyClm . ', 0 ) AS March,
                IF
                    ( revenueDetailData.DocMONTH = 4, ' . $currencyClm . ', 0 ) AS April,
                IF
                    ( revenueDetailData.DocMONTH = 5, ' . $currencyClm . ', 0 ) AS May,
                IF
                    ( revenueDetailData.DocMONTH = 6, ' . $currencyClm . ', 0 ) AS June,
                IF
                    ( revenueDetailData.DocMONTH = 7, ' . $currencyClm . ', 0 ) AS July,
                IF
                    ( revenueDetailData.DocMONTH = 8, ' . $currencyClm . ', 0 ) AS Aug,
                IF
                    ( revenueDetailData.DocMONTH = 9, ' . $currencyClm . ', 0 ) AS Sept,
                IF
                    ( revenueDetailData.DocMONTH = 10, ' . $currencyClm . ', 0 ) AS Oct,
                IF
                    ( revenueDetailData.DocMONTH = 11, ' . $currencyClm . ', 0 ) AS Nov,
                IF
                    ( revenueDetailData.DocMONTH = 12, ' . $currencyClm . ', 0 ) AS Dece,
                    ' . $currencyClm . ' as Total
FROM
    (
        SELECT
            erp_generalledger.companySystemID,
            erp_generalledger.companyID,
            companymaster.CompanyName,
            erp_generalledger.serviceLineSystemID,
            erp_generalledger.serviceLineCode,
            erp_generalledger.clientContractID,
            contractmaster.ContractNumber,
            erp_generalledger.documentID,
            erp_generalledger.documentSystemCode,
            erp_generalledger.documentCode,
            erp_generalledger.documentDate,
            MONTH (
                erp_generalledger.documentDate
            ) AS DocMONTH,
            YEAR (
                erp_generalledger.documentDate
            ) AS DocYEAR,
            erp_generalledger.documentNarration,
            erp_generalledger.chartOfAccountSystemID,
            erp_generalledger.glCode,
            erp_generalledger.glAccountType,
            serviceline.ServiceLineDes,
            chartofaccounts.controlAccounts,
            revenueGLCodes.controlAccountID,
            erp_generalledger.supplierCodeSystem,

        IF (
            erp_generalledger.clientContractID = "X"
            AND erp_generalledger.supplierCodeSystem = 0,
            0,

        IF (
            erp_generalledger.clientContractID <> "X"
            AND erp_generalledger.supplierCodeSystem = 0,
            contractmaster.clientID,

        IF (
            erp_generalledger.documentID = "SI"
            OR erp_generalledger.documentID = "DN"
            OR erp_generalledger.documentID = "PV",
            contractmaster.clientID,
            erp_generalledger.supplierCodeSystem
        )
        )
        ) AS mySupplierCode,
        erp_generalledger.documentLocalCurrencyID,
        erp_generalledger.documentRptCurrencyID,
        erp_generalledger.documentLocalAmount,
        round(
            (
                erp_generalledger.documentLocalAmount *- 1
            ),
            0
        ) AS MyLocalAmount,
        erp_generalledger.documentRptAmount,
        round(
            (
                erp_generalledger.documentRptAmount *- 1
            ),
            0
        ) AS MyRptAmount
    FROM
        erp_generalledger
    INNER JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
    LEFT JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
    LEFT JOIN serviceline ON erp_generalledger.serviceLineSystemID = serviceline.serviceLineSystemID
    LEFT JOIN contractmaster ON erp_generalledger.clientContractID = contractmaster.ContractNumber
    AND erp_generalledger.companyID = contractmaster.CompanyID
    INNER JOIN (
        SELECT
            erp_templatesdetails.templatesDetailsAutoID,
            erp_templatesdetails.templatesMasterAutoID,
            erp_templatesdetails.templateDetailDescription,
            erp_templatesdetails.controlAccountID,
            erp_templatesdetails.controlAccountSubID,
            erp_templatesglcode.chartOfAccountSystemID,
            erp_templatesglcode.glCode
        FROM
            erp_templatesdetails
        INNER JOIN erp_templatesglcode ON erp_templatesdetails.templatesDetailsAutoID = erp_templatesglcode.templatesDetailsAutoID
        WHERE
            (
                (
                    (
                        erp_templatesdetails.templatesMasterAutoID
                    ) = 15
                )
                AND (
                    (
                        erp_templatesdetails.controlAccountID
                    ) = "PLI"
                )
            )
    ) AS revenueGLCodes ON erp_generalledger.chartOfAccountSystemID = revenueGLCodes.chartOfAccountSystemID
    WHERE
        DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
        AND YEAR ( erp_generalledger.documentDate ) = "' . $year . '"
        AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
    ) AS revenueDetailData
    LEFT JOIN customermaster ON customermaster.customerCodeSystem = revenueDetailData.mySupplierCode
    WHERE
        (
          revenueDetailData.mySupplierCode IN (' . join(',', $customerSystemID) . ')
        )
    ) AS revenueDataSummary
GROUP BY
    revenueDataSummary.companySystemID,
    revenueDataSummary.serviceLineSystemID
ORDER BY
        Total DESC');
    }

    // Credit Note Register
    function getCreditNoteRegisterQRY($request)
    {
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $customers = (array)$request->customers;

        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $qry = 'SELECT
            erp_generalledger.companyID,
            erp_generalledger.documentID,
            erp_generalledger.serviceLineCode,
            erp_generalledger.documentSystemCode,
            erp_generalledger.documentCode,
            erp_generalledger.documentSystemID,
            erp_generalledger.documentDate as postedDate,
            erp_generalledger.documentNarration,
            companymaster.CompanyName,
            MONTH (
                erp_generalledger.documentDate
            ) AS DocMONTH,
            YEAR (
                erp_generalledger.documentDate
            ) AS DocYEAR,
            erp_generalledger.supplierCodeSystem,
            erp_generalledger.clientContractID,
            erp_generalledger.documentRptAmount,
            customermaster.CutomerCode,
            customermaster.customerShortCode,
            customermaster.CustomerName,
            serviceline.ServiceLineDes,
            chartofaccounts.AccountCode,
            chartofaccounts.AccountDescription,
            currencymaster.CurrencyCode,
            currencymaster.DecimalPlaces,
            matchMaster.matchingDocCode,
            DATE_FORMAT(matchMaster.matchingDocdate, "%d/%m/%Y") as matchingDocdate,
            matchMaster.detailSum,
            custReciptMaster.detailSum AS custReceiptSum,
            custReciptMaster.custPaymentReceiveCode AS custReceiptCode,
            DATE_FORMAT(custReciptMaster.custPaymentReceiveDate, "%d/%m/%Y") AS custReceiptDate
FROM
    erp_generalledger
INNER JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
INNER JOIN serviceline ON erp_generalledger.serviceLineSystemID = serviceline.serviceLineSystemID
INNER JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
INNER JOIN currencymaster ON erp_generalledger.documentRptCurrencyID = currencymaster.currencyID
LEFT JOIN (
    SELECT
        erp_matchdocumentmaster.matchDocumentMasterAutoID,
        erp_matchdocumentmaster.PayMasterAutoId,
        erp_matchdocumentmaster.matchingDocCode,
        erp_matchdocumentmaster.matchingDocdate,
        erp_matchdocumentmaster.companySystemID,
        erp_matchdocumentmaster.documentSystemID,
        custDetailRec.detailSum
    FROM
        erp_matchdocumentmaster
    LEFT JOIN (
        SELECT
            matchingDocID,
            erp_custreceivepaymentdet.companySystemID,
            SUM(erp_custreceivepaymentdet.receiveAmountRpt) AS detailSum
        FROM
            erp_custreceivepaymentdet GROUP BY matchingDocID
    ) AS custDetailRec ON erp_matchdocumentmaster.matchDocumentMasterAutoID = custDetailRec.matchingDocID AND erp_matchdocumentmaster.companySystemID = custDetailRec.companySystemID
    WHERE
        matchingConfirmedYN = 1
    ORDER BY
        matchDocumentMasterAutoID
) AS matchMaster ON erp_generalledger.documentSystemCode = matchMaster.PayMasterAutoId AND erp_generalledger.companySystemID = matchMaster.companySystemID AND erp_generalledger.documentSystemID = matchMaster.documentSystemID
LEFT JOIN (
    SELECT
        erp_custreceivepaymentdet.custReceivePaymentAutoID,
        erp_custreceivepaymentdet.bookingInvCodeSystem,
        erp_custreceivepaymentdet.companySystemID,
        erp_custreceivepaymentdet.addedDocumentSystemID,
        erp_customerreceivepayment.custPaymentReceiveCode,
        erp_customerreceivepayment.custPaymentReceiveDate,
        (SUM(
            erp_custreceivepaymentdet.receiveAmountRpt
        ) * -1) AS detailSum
    FROM
        erp_custreceivepaymentdet
    INNER JOIN erp_customerreceivepayment ON erp_customerreceivepayment.custReceivePaymentAutoID = erp_custreceivepaymentdet.custReceivePaymentAutoID
WHERE matchingDocID = 0 AND erp_customerreceivepayment.confirmedYN = 1 AND erp_customerreceivepayment.approved = -1
GROUP BY
        bookingInvCodeSystem,
        companySystemID,
        addedDocumentSystemID
) AS custReciptMaster ON erp_generalledger.documentSystemCode = custReciptMaster.bookingInvCodeSystem
AND erp_generalledger.companySystemID = custReciptMaster.companySystemID
AND erp_generalledger.documentSystemID = custReciptMaster.addedDocumentSystemID
WHERE erp_generalledger.documentSystemID = 19 AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
AND erp_generalledger.documentTransAmount > 0 AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ') ORDER BY erp_generalledger.documentDate ASC';

        return \DB::select($qry);
    }

    public function getInvoiceTrackerReportFilterData(Request $request){
        $companyId = $request['selectedCompanyId'];
        $customerCategoryID = $request['customerCategoryID'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $customers = CustomerAssigned::select(DB::raw("customerCodeSystem,CONCAT(CutomerCode, ' | ' ,CustomerName) as CustomerName"))
            ->whereIN('companySystemID', $childCompanies)
            ->where('isActive', 1)
            ->where('isAssigned', -1);

        if (!is_null($customerCategoryID) && $customerCategoryID > 0) {
            $customers = $customers->whereHas('customer_master', function($query) use ($customerCategoryID) {
                                                    $query->where('customerCategoryID', $customerCategoryID);
                                            });
        }

        $output['customer'] = $customers->get();

        $output['years'] = FreeBillingMasterPerforma::select(DB::raw("YEAR(rentalStartDate) as year"))
            ->whereNotNull('rentalStartDate')
            ->whereIn('companySystemID', $childCompanies)
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $output['customerCategories'] = CustomerMasterCategoryAssigned::whereIN('companySystemID', $childCompanies)
                                                                        ->where('isAssigned',1)
                                                                        ->where('isActive',1)
                                                                        ->get();

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    public function getContractByCustomer(Request $request){

        $customerIDArray = $request['customerIDArray'];
        $companyId = $request['companyId'];
        $output = [];
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        if (is_array($customerIDArray)) {
            $output['contracts'] = Contract::whereIN('companySystemID', $childCompanies)->whereIN('clientID', $customerIDArray)->get();
        }

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    public function generateInvoiceTrackingReport(Request $request)
    {

        $input = $request->all();

        $validator = \Validator::make($input, [
            'customerID' => 'required',
            'contractID' => 'required',
            'yearID' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $result = $this->getInvoiceTrackerQRY($request);

        $finalResult = [];

        foreach ($result as $key => $value) {
            $finalResult[$value->clientID.'-'.$value->CustomerName][] = $value;
        }

        return $this->sendResponse($finalResult, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    public function getInvoiceTrackerQRY($request)
    {
        $input = $request->all();
        $where = '';
        if(isset($input['customerID']) && count($input['customerID'])>0){
            $cusList = implode(', ', $input['customerID']);
            $where.=' AND freebillingmasterperforma.clientSystemID IN ('.$cusList.')';
        }

        if(isset($input['contractID']) && count($input['contractID'])>0){
            $conList = implode(', ', $input['contractID']);
            $where.=' AND freebillingmasterperforma.contractUID IN ('.$conList.')';
        }

        if(isset($input['yearID']) && count($input['yearID'])>0){
            $yearList = implode(', ', $input['yearID']);
            $where.=' AND YEAR(freebillingmasterperforma.rentalStartDate) IN ('.$yearList.')';
        }

        $whereStatus = '';
        if (isset($input['status'])) {
            switch ($input['status']) {
                case 'Collected':
                    $whereStatus.=' WHERE final.ReceiptDate IS NOT NULL';
                    break;
                case 'Rig Approval Pending':
                    $whereStatus.=' WHERE (final.myClientapprovedDate IS NULL OR final.myClientapprovedDate = "") 
                            AND (final.mySubmittedDate IS NULL OR final.mySubmittedDate = "") 
                            AND (final.myApprovedDate IS NULL OR final.myApprovedDate = "")';
                    break;
                case 'Submission Pending':
                    $whereStatus.=' WHERE (final.myClientapprovedDate IS NOT NULL) 
                                    AND (final.mySubmittedDate IS NULL OR final.mySubmittedDate = "") 
                                    AND (final.myApprovedDate IS NULL OR final.myApprovedDate = "")';
                    break;
                case 'CH Approval Pending':
                    $whereStatus.=' WHERE final.myClientapprovedDate IS NOT NULL 
                                        AND final.mySubmittedDate IS NOT NULL 
                                        AND (final.myApprovedDate IS NULL OR final.myApprovedDate = "")';
                    break;
                case 'CH Approved':
                    $whereStatus.=' WHERE final.myClientapprovedDate IS NOT NULL AND final.mySubmittedDate IS NOT NULL AND final.myApprovedDate IS NOT NULL AND final.ReceiptDate IS NULL';
                    break;

                default:
                    $whereStatus = '';
                    break;
            }
        }

        $sql = "SELECT
                companyID,
                clientID,
                contractUID,
                contractID,
                PerformaMasterID,
                RigDescription,
                regNo,
                myRentalStartDate,
                myRentMonth,
                myRentYear,
                myRentYear AS checkStatusYear,
                rentalStartDate,
                rentalEndDate,
                billingCode,
                performaValue,
                PerformaCode,
                performaOpConfirmedDate,
                description,
                clientapprovedDate,
                myClientapprovedDate,
                batchNo,
                manualTrackingNo,
                mySubmittedDate,
                performaSerialNO,
                bookingInvCode,
                myApprovedDate,
                myDescription,
                ReceiptCode,
                ReceiptDate,
                ReceiptAmount,
                CustomerName,
            IF
                (
                ReceiptDate IS NOT NULL,
                \"Collected\",
            IF
                (
                ( myClientapprovedDate IS NULL OR myClientapprovedDate = \"\" ) 
                AND ( mySubmittedDate IS NULL OR mySubmittedDate = \"\" ) 
                AND ( myApprovedDate IS NULL OR myApprovedDate = \"\" ),
                \"Rig Approval Pending\",
            IF
                (
                myClientapprovedDate IS NOT NULL 
                AND ( mySubmittedDate IS NULL OR mySubmittedDate = \"\" ) 
                AND ( myApprovedDate IS NULL OR myApprovedDate = \"\" ),
                \"Submission Pending\",
            IF
                (
                myClientapprovedDate IS NOT NULL 
                AND mySubmittedDate IS NOT NULL 
                AND ( myApprovedDate IS NULL OR myApprovedDate = \"\" ),
                \"CH Approval Pending\",
            IF
                ( myClientapprovedDate IS NOT NULL AND mySubmittedDate IS NOT NULL AND myApprovedDate IS NOT NULL, \"CH Approved\" ,\"\") 
                ) 
                ) 
                ) 
                ) AS status
            FROM
                (
            SELECT
                performamaster.companyID,
                performamaster.clientID,
                qry_performaClientApproval_Billing.contractUID,
                performamaster.contractID,
                performamaster.PerformaMasterID,
                qry_performaClientApproval_Billing.RigDescription,
                qry_performaClientApproval_Billing.regNo,
                qry_performaClientApproval_Billing.myRentalStartDate,
                MONTH ( qry_performaClientApproval_Billing.myRentalStartDate ) AS myRentMonth,
                YEAR ( qry_performaClientApproval_Billing.myRentalStartDate ) AS myRentYear,
                DATE_FORMAT( qry_performaClientApproval_Billing.myRentalStartDate, \"%d/%m/%Y\" ) AS rentalStartDate,
                DATE_FORMAT( qry_performaClientApproval_Billing.myRentalEndDate, \"%d/%m/%Y\" ) AS rentalEndDate,
                qry_performaClientApproval_Billing.billingCode,
                qry_performaClientApproval_Billing.CustomerName,
                performamaster.performaValue,
                performamaster.PerformaCode,
                performamaster.performaOpConfirmedDate,
                clientperformaapptype.description,
                performamaster.clientapprovedDate,
            IF
                ( description = \"Not Approved\" AND myApprovedDate IS NOT NULL, myApprovedDate, clientapprovedDate ) AS myClientapprovedDate,
                qry_ProformaClientApproval_CustomerInvoices.customerInvoiceTrackingCode AS batchNo,
                qry_ProformaClientApproval_CustomerInvoices.manualTrackingNo,
                qry_ProformaClientApproval_CustomerInvoices.submittedDate,
            IF
                ( myApprovedDate IS NOT NULL AND submittedDate IS NULL, myApprovedDate, submittedDate ) AS mySubmittedDate,
                performamaster.performaSerialNO,
                qry_ProformaClientApproval_CustomerInvoices.custInvoiceDirectAutoID,
                qry_ProformaClientApproval_CustomerInvoices.bookingInvCode,
                qry_ProformaClientApproval_CustomerInvoices.myApprovedDate,
            IF
                (
                ( description = \"Not Approved\" AND ( DATE_FORMAT( clientapprovedDate, \"%d/%m/%Y\" ) IS NULL OR DATE_FORMAT( clientapprovedDate, \"%d/%m/%Y\" ) = \"\" ) ) 
                AND ( myApprovedDate IS NULL OR myApprovedDate = \"\" ),
                \"Not Approved\",
            IF
                (
                description = \"Not Approved\" 
                AND ( DATE_FORMAT( clientapprovedDate, \"%d/%m/%Y\" ) IS NULL OR DATE_FORMAT( clientapprovedDate, \"%d/%m/%Y\" ) = \"\" ) 
                AND ( myApprovedDate IS NOT NULL OR myApprovedDate <> \"\" ),
                \"Approved\",
                description 
                ) 
                ) AS myDescription,
                AR_SubLedger_InvoicesMatchedSum_2_InvoiceTracker.custPaymentReceiveCode AS ReceiptCode,
                AR_SubLedger_InvoicesMatchedSum_2_InvoiceTracker.postedDate AS ReceiptDate,
                AR_SubLedger_InvoicesMatchedSum_2_InvoiceTracker.ReceiptAmountRpt AS ReceiptAmount 
            FROM
                performamaster
                INNER JOIN clientperformaapptype ON performamaster.clientAppPerformaType = clientperformaapptype.performaAppTypeID
                INNER JOIN (
            SELECT
                freebillingmasterperforma.idbillingMasterPerforma,
                freebillingmasterperforma.companyID,
                freebillingmasterperforma.clientID,
                freebillingmasterperforma.contractID,
                ticketmaster.contractUID,
                freebillingmasterperforma.Ticketno,
                ticketmaster.ticketNo AS myTicketCode,
                ticketmaster.regName,
                rigmaster.RigDescription,
                ticketmaster.regNo,
                freebillingmasterperforma.BillProcessNO,
                freebillingmasterperforma.billingCode,
                ticketmaster.Timedatejobstra,
                ticketmaster.Timedatejobend,
                customermaster.CustomerName,
            IF
                ( billingCode = \"0\", Timedatejobstra, rentalStartDate ) AS myRentalStartDate,
            IF
                ( billingCode = \"0\", Timedatejobend, rentalEndDate ) AS myRentalEndDate,
                freebillingmasterperforma.performaMasterID,
                freebillingmasterperforma.PerformaInvoiceNo 
            FROM
                freebillingmasterperforma
                INNER JOIN ticketmaster ON freebillingmasterperforma.Ticketno = ticketmaster.ticketidAtuto
                INNER JOIN rigmaster ON ticketmaster.regName = rigmaster.idrigmaster 
                INNER JOIN customermaster ON freebillingmasterperforma.clientSystemID = customermaster.customerCodeSystem 
            WHERE
                freebillingmasterperforma.performaMasterID > 0 ".$where."
                ) AS qry_performaClientApproval_Billing ON qry_performaClientApproval_Billing.performaMasterID = performamaster.PerformaMasterID
                LEFT JOIN (
            SELECT
                erp_performadetails.companyID,
                erp_performadetails.serviceLine,
                erp_performadetails.customerID,
                erp_performadetails.contractID,
                erp_performadetails.performaMasterID,
                erp_performadetails.invoiceSsytemCode,
                qry_CustomerInvoiced_byProforma.custInvoiceDirectAutoID,
                qry_CustomerInvoiced_byProforma.bookingInvCode,
                qry_CustomerInvoiced_byProforma.myApprovedDate,
                qry_PerformaClientApproval_batchDetails.customerInvoiceTrackingCode,
                qry_PerformaClientApproval_batchDetails.manualTrackingNo,
                qry_PerformaClientApproval_batchDetails.submittedDate 
            FROM
                erp_performadetails
                INNER JOIN (
            SELECT
                erp_custinvoicedirect.custInvoiceDirectAutoID,
                erp_custinvoicedirect.companyID,
                erp_custinvoicedirect.documentID,
                erp_custinvoicedirect.bookingInvCode,
                DATE_FORMAT( approvedDate, \"%d/%m/%Y\" ) AS myApprovedDate,
                erp_custinvoicedirect.isPerforma 
            FROM
                erp_custinvoicedirect 
            WHERE
                erp_custinvoicedirect.isPerforma = 1 
                ) AS qry_CustomerInvoiced_byProforma ON qry_CustomerInvoiced_byProforma.custInvoiceDirectAutoID = erp_performadetails.invoiceSsytemCode
                LEFT JOIN (
            SELECT
                erp_customerinvoicetracking.customerInvoiceTrackingID,
                erp_customerinvoicetracking.customerInvoiceTrackingCode,
                erp_customerinvoicetracking.manualTrackingNo,
                erp_customerinvoicetracking.submittedDate,
                erp_customerinvoicetrackingdetail.companyID,
                erp_customerinvoicetrackingdetail.custInvoiceDirectAutoID 
            FROM
                erp_customerinvoicetracking
                INNER JOIN erp_customerinvoicetrackingdetail ON erp_customerinvoicetracking.customerInvoiceTrackingID = erp_customerinvoicetrackingdetail.customerInvoiceTrackingID 
                ) AS qry_PerformaClientApproval_batchDetails ON qry_PerformaClientApproval_batchDetails.custInvoiceDirectAutoID = qry_CustomerInvoiced_byProforma.custInvoiceDirectAutoID 
            WHERE
                erp_performadetails.invoiceSsytemCode > 0 
            GROUP BY
                erp_performadetails.companyID,
                erp_performadetails.customerID,
                erp_performadetails.performaMasterID,
                erp_performadetails.invoiceSsytemCode 
                ) AS qry_ProformaClientApproval_CustomerInvoices ON qry_ProformaClientApproval_CustomerInvoices.companyID = performamaster.companyID 
                AND qry_ProformaClientApproval_CustomerInvoices.performaMasterID = performamaster.performaSerialNO 
                AND qry_ProformaClientApproval_CustomerInvoices.contractID = performamaster.contractID
                LEFT JOIN (
            SELECT
                max( custReceivePaymentAutoID ) AS custReceivePaymentAutoID,
                max( custPaymentReceiveCode ) AS custPaymentReceiveCode,
                max( postedDate ) AS postedDate,
                bookingInvCodeSystem,
                bookingInvCode,
                sum( receiveAmountTrans ),
                sum( receiveAmountLocal ),
                sum( receiveAmountRpt ) AS ReceiptAmountRpt 
            FROM
                (
            SELECT
                erp_customerreceivepayment.custReceivePaymentAutoID,
                erp_customerreceivepayment.custPaymentReceiveCode,
                erp_customerreceivepayment.postedDate,
                erp_custreceivepaymentdet.bookingInvCodeSystem,
                erp_custreceivepaymentdet.bookingInvCode,
                erp_custreceivepaymentdet.receiveAmountTrans,
                erp_custreceivepaymentdet.receiveAmountLocal,
                erp_custreceivepaymentdet.receiveAmountRpt 
            FROM
                erp_customerreceivepayment
                INNER JOIN erp_custreceivepaymentdet ON erp_customerreceivepayment.custReceivePaymentAutoID = erp_custreceivepaymentdet.custReceivePaymentAutoID 
            WHERE
                erp_custreceivepaymentdet.bookingInvCode <> \"0\" 
                AND erp_customerreceivepayment.approved =- 1 
                AND erp_custreceivepaymentdet.matchingDocID = 0 UNION
            SELECT
                erp_matchdocumentmaster.matchDocumentMasterAutoID,
                erp_matchdocumentmaster.matchingDocCode,
                erp_matchdocumentmaster.matchingDocdate,
                erp_custreceivepaymentdet.bookingInvCodeSystem,
                erp_custreceivepaymentdet.bookingInvCode,
                erp_custreceivepaymentdet.receiveAmountTrans,
                erp_custreceivepaymentdet.receiveAmountLocal,
                erp_custreceivepaymentdet.receiveAmountRpt 
            FROM
                erp_matchdocumentmaster
                INNER JOIN erp_custreceivepaymentdet ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID 
                AND erp_custreceivepaymentdet.matchingDocID > 0 
            WHERE
                erp_matchdocumentmaster.matchingConfirmedYN = 1 
                ) AS final 
            GROUP BY
                bookingInvCodeSystem 
                ) AS AR_SubLedger_InvoicesMatchedSum_2_InvoiceTracker ON AR_SubLedger_InvoicesMatchedSum_2_InvoiceTracker.bookingInvCodeSystem = qry_ProformaClientApproval_CustomerInvoices.custInvoiceDirectAutoID 
                ) AS final".$whereStatus;


        return DB::select($sql);
    }

    function pdfExportCAReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'CA':
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));

                $db = isset($request->db) ? $request->db : "";


                $employeeID = \Helper::getEmployeeSystemID();
                AccountsReceivablePdfJob::dispatch($db, $request, [$employeeID])->onQueue('reporting');

                return $this->sendResponse([], "Account receivable customer aging PDF report has been sent to queue");
                break;

            default:
                return $this->sendError('No report ID found');
        }
    }
}
