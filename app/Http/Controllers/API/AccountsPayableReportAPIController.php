<?php

/**
 * =============================================
 * -- File Name : AccountsPayableReportAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report
 * -- Author : Nazir
 * -- Create date : 3 - July 2018
 * -- Description : This file contains the all the report generation code
 * -- REVISION HISTORY
 * -- Date: 04-july 2018 By: Fayas Description: Added new functions named as getPaymentSuppliersByYear()
 * -- Date: 04-july 2018 By: Nazir Description: Added new functions named as getSupplierLedgerQRY()
 * -- Date: 04-july 2018 By: Mubashir Description: Added new functions named as getSupplierStatementQRY()
 * -- Date: 05-july 2018 By: Nazir Description: Added new functions named as getSupplierBalanceSummeryQRY()
 * -- Date: 11-july 2018 By: Fayas Description: Added new functions named as getTopSupplierQRY()
 * -- Date: 05-july 2018 By: Mubashir Description: Added new functions named as getSupplierAgingDetailQRY()
 * -- Date: 05-july 2018 By: Mubashir Description: Added new functions named as getSupplierAgingSummaryQRY()
 * -- Date: 06-july 2018 By: Mubashir Description: Added new functions named as getSupplierAgingDetailAdvanceQRY()
 * -- Date: 06-july 2018 By: Mubashir Description: Added new functions named as getSupplierAgingSummaryAdvanceQRY()
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\AccountsPayableLedger;
use App\Models\ChartOfAccount;
use App\Models\CurrencyMaster;
use App\Models\GeneralLedger;
use App\Models\SupplierAssigned;
use App\Models\SupplierMaster;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountsPayableReportAPIController extends AppBaseController
{
    public function getAPFilterData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $companiesByGroup = "";
        if (\Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

        $controlAccount = SupplierMaster::groupBy('liabilityAccountSysemID')->pluck('liabilityAccountSysemID');
        $controlAccount = ChartOfAccount::whereIN('chartOfAccountSystemID', $controlAccount)->get();

        $departments = \Helper::getCompanyServiceline($selectedCompanyId);

        $filterSuppliers = AccountsPayableLedger::whereIN('companySystemID', $companiesByGroup)
            ->select('supplierCodeSystem')
            ->groupBy('supplierCodeSystem')
            ->pluck('supplierCodeSystem');

        $supplierMaster = SupplierAssigned::whereIN('companySystemID', $companiesByGroup)->whereIN('supplierCodeSytem', $filterSuppliers)->groupBy('supplierCodeSytem')->get();

        $years = GeneralLedger::select(DB::raw("YEAR(documentDate) as year"))
            ->whereNotNull('documentDate')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get(['year']);

        $output = array(
            'controlAccount' => $controlAccount,
            'suppliers' => $supplierMaster,
            'departments' => $departments,
            'years' => $years,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function validateAPReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'APSL':
                $validator = \Validator::make($request->all(), [
                    'reportTypeID' => 'required',
                    'fromDate' => 'required',
                    'toDate' => 'required|date|after_or_equal:fromDate',
                    'suppliers' => 'required',
                    'controlAccountsSystemID' => 'required',
                    'currencyID' => 'required'
                ]);

                if ($validator->fails()) {//echo 'in';exit;
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            case 'APSS':
                $validator = \Validator::make($request->all(), [
                    'reportTypeID' => 'required',
                    'fromDate' => 'required',
                    'suppliers' => 'required',
                    'controlAccountsSystemID' => 'required',
                    'currencyID' => 'required'
                ]);

                if ($validator->fails()) {//echo 'in';exit;
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            case 'APPSY':

                $reportTypeID = '';
                if (isset($request->reportTypeID)) {
                    $reportTypeID = $request->reportTypeID;
                }

                if ($reportTypeID == 'APPSY') {
                    $validator = \Validator::make($request->all(), [
                        'reportTypeID' => 'required',
                        'suppliers' => 'required',
                        'year' => 'required',
                        'currencyID' => 'required'
                    ]);

                } else if ($reportTypeID == 'APDPY' || $reportTypeID == 'APAPY') {
                    $validator = \Validator::make($request->all(), [
                        'reportTypeID' => 'required',
                        'year' => 'required',
                        'currencyID' => 'required'
                    ]);
                } else if ($reportTypeID == 'APLWS') {
                    $validator = \Validator::make($request->all(), [
                        'reportTypeID' => 'required',
                        'fromDate' => 'required',
                        'toDate' => 'required',
                        'currencyID' => 'required'
                    ]);
                } else {
                    return $this->sendError('No report type found');
                }

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            case 'APSBS':
                $validator = \Validator::make($request->all(), [
                    'reportTypeID' => 'required',
                    'fromDate' => 'required',
                    'currencyID' => 'required',
                    'controlAccountsSystemID' => 'required',
                    'suppliers' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            case 'APSA':
                $validator = \Validator::make($request->all(), [
                    'reportTypeID' => 'required',
                    'fromDate' => 'required',
                    'suppliers' => 'required',
                    'controlAccountsSystemID' => 'required',
                    'currencyID' => 'required',
                    'interval' => 'required',
                    'through' => 'required'
                ]);

                if ($validator->fails()) {//echo 'in';exit;
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            case 'TS':
                $validator = \Validator::make($request->all(), [
                    'reportTypeID' => 'required',
                    'year' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    /*generate report according to each report id*/
    public function generateAPReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'APSL': //Supplier Ledger Report
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getSupplierLedgerQRY($request);

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
                        $outputArr[$val->SupplierCode . " - " . $val->suppliername][$val->documentCurrency][] = $val;
                    }
                }
                return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'invoiceAmount' => $invoiceAmount, 'paidAmount' => $paidAmount, 'balanceAmount' => $balanceAmount);
                break;
            case 'APSS': //Supplier Statement Report
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getSupplierStatementQRY($request);

                $outputArr = array();

                $balanceAmount = collect($output)->pluck('balanceAmount')->toArray();
                $balanceAmount = array_sum($balanceAmount);

                $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                $decimalPlace = array_unique($decimalPlace);

                if ($output) {
                    foreach ($output as $val) {
                        $outputArr[$val->concatSupplierName][$val->documentCurrency][] = $val;
                    }
                }
                return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'balanceAmount' => $balanceAmount, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2);
                break;
            case 'APPSY':
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getPaymentSuppliersByYear($request);
                $reportTypeID = $request->reportTypeID;
                $outputArr = array();
                foreach ($output as $val) {
                    $outputArr[$val->CompanyName][] = $val;
                }
                if ($reportTypeID == 'APLWS') {

                    $total['payAmountSuppTrans'] = array_sum(collect($output)->pluck('payAmountSuppTrans')->toArray());
                    $total['payAmountCompLocal'] = array_sum(collect($output)->pluck('payAmountSuppTrans')->toArray());
                    $total['payAmountCompRpt'] = array_sum(collect($output)->pluck('payAmountSuppTrans')->toArray());

                    return array('reportData' => $outputArr,
                        'companyName' => $checkIsGroup->CompanyName,
                        //'total' => $total,
                        //'decimalPlace' => $decimalPlace,
                        // 'currency' => $requestCurrency->CurrencyCode
                    );

                } else {
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

                    return array('reportData' => $outputArr,
                        'companyName' => $checkIsGroup->CompanyName,
                        'total' => $total,
                        'decimalPlace' => $decimalPlace,
                        'currency' => $requestCurrency->CurrencyCode
                    );
                }
                break;
            case 'APSA': //Supplier Aging
                $reportTypeID = $request->reportTypeID;
                if ($reportTypeID == 'SAD') { //Supplier aging detail

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getSupplierAgingDetailQRY($request);

                    $outputArr = array();
                    $grandTotalArr = array();
                    $lineGrandTotal = 0;
                    if ($output['aging']) {
                        foreach ($output['aging'] as $val) {
                            $total = collect($output['data'])->pluck($val)->toArray();
                            $grandTotalArr[$val] = array_sum($total);
                            $lineGrandTotal += array_sum($total);
                        }
                    }

                    if ($output['data']) {
                        foreach ($output['data'] as $val) {
                            $outputArr[$val->suppliername][$val->documentCurrency][] = $val;
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

                    $unAllocatedAmount = collect($output['data'])->pluck('unAllocatedAmount')->toArray();
                    $unAllocatedAmount = array_sum($unAllocatedAmount);

                    return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces, 'agingRange' => $output['aging'], 'unAllocatedAmount' => $unAllocatedAmount, 'lineGrandTotal' => $lineGrandTotal);
                } else if ($reportTypeID == 'SAS') { //Supplier aging Summary
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getSupplierAgingSummaryQRY($request);

                    $outputArr = array();
                    $grandTotalArr = array();
                    $lineGrandTotal = 0;
                    if ($output['aging']) {
                        foreach ($output['aging'] as $val) {
                            $total = collect($output['data'])->pluck($val)->toArray();
                            $grandTotalArr[$val] = array_sum($total);
                            $lineGrandTotal += array_sum($total);
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

                    $unAllocatedAmount = collect($output['data'])->pluck('unAllocatedAmount')->toArray();
                    $unAllocatedAmount = array_sum($unAllocatedAmount);

                    return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces, 'agingRange' => $output['aging'], 'unAllocatedAmount' => $unAllocatedAmount, 'lineGrandTotal' => $lineGrandTotal);
                } else if ($reportTypeID == 'SADA') { //Supplier aging detail advance

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getSupplierAgingDetailAdvanceQRY($request);

                    $outputArr = array();
                    $grandTotalArr = array();
                    $lineGrandTotal = 0;
                    if ($output['aging']) {
                        foreach ($output['aging'] as $val) {
                            $total = collect($output['data'])->pluck($val)->toArray();
                            $grandTotalArr[$val] = array_sum($total);
                            $lineGrandTotal += array_sum($total);
                        }
                    }

                    if ($output['data']) {
                        foreach ($output['data'] as $val) {
                            $outputArr[$val->suppliername][$val->documentCurrency][] = $val;
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

                    return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces, 'agingRange' => $output['aging'], 'lineGrandTotal' => $lineGrandTotal);
                } else if ($reportTypeID == 'SASA') { //Supplier aging Summary
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getSupplierAgingSummaryAdvanceQRY($request);

                    $outputArr = array();
                    $grandTotalArr = array();
                    $lineGrandTotal = 0;
                    if ($output['aging']) {
                        foreach ($output['aging'] as $val) {
                            $total = collect($output['data'])->pluck($val)->toArray();
                            $grandTotalArr[$val] = array_sum($total);
                            $lineGrandTotal += array_sum($total);
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

                    return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces, 'agingRange' => $output['aging'], 'lineGrandTotal' => $lineGrandTotal);
                }
                break;
            case 'APSBS': //Supplier Balance Summary Report
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getSupplierBalanceSummeryQRY($request);

                $documentAmount = collect($output)->pluck('documentAmount')->toArray();
                $documentAmount = array_sum($documentAmount);

                $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                $decimalPlace = array_unique($decimalPlace);

                return array('reportData' => $output, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $documentAmount, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2);
                break;
            case 'TS': //Supplier Balance Summary Report
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getTopSupplierQRY($request);

                $reportTypeID = $request->reportTypeID;

                $total = array_sum(collect($output)->pluck('Amount')->toArray());

                $finalArray = array('companyName' => $checkIsGroup->CompanyName,
                                    'grandTotal' => $total,
                                    //'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2
                                    );

                if ($reportTypeID == 'TSCW') {

                    $outputArr = array();

                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->CompanyName][] = $val;
                        }
                    }

                    $finalArray['reportData'] = $outputArr;
                }else if($reportTypeID == 'TSC'){
                    $finalArray['reportData'] = $output;
                }

                return $finalArray;
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    public function exportReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'APPSY':
                $reportTypeID = $request->reportTypeID;
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));

                $companyCurrency = \Helper::companyCurrency($request->companySystemID);

                $output = $this->getPaymentSuppliersByYear($request);
                $decimalPlace = 0;
                $data = array();
                if ($output) {
                    $reportSD = $request->reportSD;
                    $currency = $request->currencyID;
                    $reportTypeID = $request->reportTypeID;

                    if ($reportTypeID == 'APPSY') {
                        if ($reportSD == 'detail') {
                            $x = 0;
                            foreach ($output as $val) {
                                $data[$x]['Company ID'] = $val->companyID;
                                $data[$x]['Company Name'] = $val->CompanyName;
                                $data[$x]['Posted Date'] = \Helper::dateFormat($val->documentDate);
                                $data[$x]['Payment Type'] = $val->PaymentType;
                                $data[$x]['Payment Document Number'] = $val->documentCode;
                                $data[$x]['Supplier Code'] = $val->supplierCode;
                                $data[$x]['Supplier Name'] = $val->supplierName;

                                if ($currency == 2) {
                                    $data[$x]['Currency'] = $val->documentLocalCurrency;
                                    $data[$x]['Amount'] = round($val->documentLocalAmount, $decimalPlace);
                                } else if ($currency == 3) {
                                    $data[$x]['Currency'] = $val->documentRptCurrency;
                                    $data[$x]['Amount'] = round($val->documentRptAmount, $decimalPlace);
                                }
                                $x++;
                            }
                        } else {
                            $x = 0;
                            foreach ($output as $val) {
                                $data[$x]['Company ID'] = $val->companyID;
                                $data[$x]['Company Name'] = $val->CompanyName;
                                $data[$x]['Supplier Code'] = $val->supplierCode;
                                $data[$x]['Supplier Name'] = $val->supplierName;
                                $data[$x]['Jan'] = round($val->Jan, $decimalPlace);
                                $data[$x]['Feb'] = round($val->Feb, $decimalPlace);
                                $data[$x]['March'] = round($val->March, $decimalPlace);
                                $data[$x]['April'] = round($val->April, $decimalPlace);
                                $data[$x]['May'] = round($val->May, $decimalPlace);
                                $data[$x]['Jun'] = round($val->June, $decimalPlace);
                                $data[$x]['July'] = round($val->July, $decimalPlace);
                                $data[$x]['Aug'] = round($val->Aug, $decimalPlace);
                                $data[$x]['Sept'] = round($val->Sept, $decimalPlace);
                                $data[$x]['Oct'] = round($val->Oct, $decimalPlace);
                                $data[$x]['Nov'] = round($val->Nov, $decimalPlace);
                                $data[$x]['Dec'] = round($val->Dece, $decimalPlace);
                                $data[$x]['Total'] = round($val->Total, $decimalPlace);
                                $x++;
                            }
                        }
                    } else if ($reportTypeID == 'APDPY') {

                        if ($reportSD == 'detail') {
                            $x = 0;
                            foreach ($output as $val) {
                                $data[$x]['Company ID'] = $val->companyID;
                                $data[$x]['Company Name'] = $val->CompanyName;
                                $data[$x]['Posted Date'] = \Helper::dateFormat($val->documentDate);
                                //$data[$x]['Payment Type'] = $val->PaymentType;
                                $data[$x]['Payment Document Number'] = $val->documentCode;
                                $data[$x]['GL Code'] = $val->glCode;
                                $data[$x]['Account Description'] = $val->AccountDescription;

                                if ($currency == 2) {
                                    $data[$x]['Currency'] = $val->documentLocalCurrency;
                                    $data[$x]['Amount'] = round($val->documentLocalAmount, $decimalPlace);
                                } else if ($currency == 3) {
                                    $data[$x]['Currency'] = $val->documentRptCurrency;
                                    $data[$x]['Amount'] = round($val->documentRptAmount, $decimalPlace);
                                }
                                $x++;
                            }
                        } else {
                            $x = 0;
                            foreach ($output as $val) {
                                $data[$x]['Company ID'] = $val->companyID;
                                $data[$x]['Company Name'] = $val->CompanyName;
                                $data[$x]['GL Code'] = $val->glCode;
                                $data[$x]['Account Description'] = $val->AccountDescription;
                                $data[$x]['Jan'] = round($val->Jan, $decimalPlace);
                                $data[$x]['Feb'] = round($val->Feb, $decimalPlace);
                                $data[$x]['March'] = round($val->March, $decimalPlace);
                                $data[$x]['April'] = round($val->April, $decimalPlace);
                                $data[$x]['May'] = round($val->May, $decimalPlace);
                                $data[$x]['Jun'] = round($val->June, $decimalPlace);
                                $data[$x]['July'] = round($val->July, $decimalPlace);
                                $data[$x]['Aug'] = round($val->Aug, $decimalPlace);
                                $data[$x]['Sept'] = round($val->Sept, $decimalPlace);
                                $data[$x]['Oct'] = round($val->Oct, $decimalPlace);
                                $data[$x]['Nov'] = round($val->Nov, $decimalPlace);
                                $data[$x]['Dec'] = round($val->Dece, $decimalPlace);
                                $data[$x]['Total'] = round($val->Total, $decimalPlace);
                                $x++;
                            }
                        }
                    } else if ($reportTypeID == 'APAPY') {

                        if ($reportSD == 'detail') {
                        } else {
                            $x = 0;
                            foreach ($output as $val) {
                                $data[$x]['Company ID'] = $val->companyID;
                                $data[$x]['Company Name'] = $val->CompanyName;
                                $data[$x]['Supplier Code / GL Code'] = $val->docCode;
                                $data[$x]['Supplier Name / Account Description'] = $val->docDes;
                                $data[$x]['Jan'] = round($val->Jan, $decimalPlace);
                                $data[$x]['Feb'] = round($val->Feb, $decimalPlace);
                                $data[$x]['March'] = round($val->March, $decimalPlace);
                                $data[$x]['April'] = round($val->April, $decimalPlace);
                                $data[$x]['May'] = round($val->May, $decimalPlace);
                                $data[$x]['Jun'] = round($val->June, $decimalPlace);
                                $data[$x]['July'] = round($val->July, $decimalPlace);
                                $data[$x]['Aug'] = round($val->Aug, $decimalPlace);
                                $data[$x]['Sept'] = round($val->Sept, $decimalPlace);
                                $data[$x]['Oct'] = round($val->Oct, $decimalPlace);
                                $data[$x]['Nov'] = round($val->Nov, $decimalPlace);
                                $data[$x]['Dec'] = round($val->Dece, $decimalPlace);
                                $data[$x]['Total'] = round($val->Total, $decimalPlace);
                                $x++;
                            }
                        }
                    } else if ($reportTypeID == 'APLWS') {

                        if ($reportSD == 'detail') {
                        } else {
                            $x = 0;
                            foreach ($output as $val) {

                                $data[$x]['Company ID'] = $val->companyID;
                                $data[$x]['Company Name'] = $val->CompanyName;
                                $data[$x]['BPVcode'] = $val->BPVcode;
                                $data[$x]['Doc.Date'] = \Helper::dateFormat($val->BPVdate);
                                $data[$x]['Doc.Confirmed Date'] = \Helper::dateFormat($val->confirmedDate);
                                $data[$x]['Payee Name'] = $val->PayeeName;
                                $data[$x]['Credit Period'] = $val->creditPeriod;
                                $data[$x]['Cheque No'] = $val->BPVchequeNo;
                                $data[$x]['Cheque Date'] = \Helper::dateFormat($val->ChequeDate);
                                $data[$x]['Cheque Printed By'] = $val->chequePrintedByEmpName;
                                $data[$x]['Cheque Printed Date'] = \Helper::dateFormat($val->chequePrintedDate);

                                if ($currency == 1) {
                                    $data[$x]['Currency'] = $val->documentTransCurrency;
                                    $data[$x]['Amount'] = round($val->payAmountSuppTrans, $val->documentTransDecimalPlaces);
                                } else if ($currency == 2) {
                                    $data[$x]['Currency'] = $val->documentLocalCurrency;
                                    $data[$x]['Amount'] = round($val->payAmountCompLocal, $val->documentLocalDecimalPlaces);
                                } else if ($currency == 3) {
                                    $data[$x]['Currency'] = $val->documentRptCurrency;
                                    $data[$x]['Amount'] = round($val->payAmountCompRpt, $val->documentRptDecimalPlaces);
                                }

                                $status = "";

                                if ($val->approved == -1) {
                                    $status = "Fully Approved";
                                } else if ((($val->chequeSentToTreasury) == -1)) {
                                    $status = "Payment Sent to Treasury";
                                } else if (($val->chequeSentToTreasury == 0) && ($val->chequePaymentYN == 0)) {
                                    $status = "Payment Not Printed and Not Sent to Treasury";
                                }

                                $data[$x]['Approval Status'] = $status;
                                $x++;
                            }
                        }
                    }

                }
                $csv = \Excel::create('payment_suppliers_by_year', function ($excel) use ($data) {
                    $excel->sheet('sheet name', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', true);
                        $sheet->setAutoSize(true);
                        $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                    });
                    $lastrow = $excel->getActiveSheet()->getHighestRow();
                    $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                })->download($type);

                return $this->sendResponse(array(), 'successfully export');
                break;
            case 'APSS':
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $output = $this->getSupplierStatementQRY($request);
                if ($output) {
                    $x = 0;
                    foreach ($output as $val) {
                        $data[$x]['Company ID'] = $val->companyID;
                        $data[$x]['Company Name'] = $val->CompanyName;
                        $data[$x]['Supplier Code'] = $val->SupplierCode;
                        $data[$x]['Supplier Name'] = $val->suppliername;
                        $data[$x]['Document ID'] = $val->documentID;
                        $data[$x]['Document Code'] = $val->documentCode;
                        $data[$x]['Document Date'] = \Helper::dateFormat($val->documentDate);
                        $data[$x]['Narration'] = $val->documentNarration;
                        $data[$x]['Invoice Number'] = $val->invoiceNumber;
                        $data[$x]['Invoice Date'] = \Helper::dateFormat($val->invoiceDate);
                        $data[$x]['Currency'] = $val->documentCurrency;
                        $data[$x]['Age Days'] = $val->ageDays;
                        $data[$x]['Doc Amount'] = $val->invoiceAmount;
                        $data[$x]['Balance Amount'] = $val->balanceAmount;

                        $x++;
                    }
                } else {
                    $data = array();
                }
                $csv = \Excel::create('payment_suppliers_by_year', function ($excel) use ($data) {
                    $excel->sheet('sheet name', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', true);
                        $sheet->setAutoSize(true);
                        $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                    });
                    $lastrow = $excel->getActiveSheet()->getHighestRow();
                    $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                })->download($type);

                return $this->sendResponse(array(), 'successfully export');
                break;
            case 'APSL':
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $output = $this->getSupplierLedgerQRY($request);
                if ($output) {
                    $x = 0;
                    foreach ($output as $val) {
                        $data[$x]['Company ID'] = $val->companyID;
                        $data[$x]['Company Name'] = $val->CompanyName;
                        $data[$x]['Document Code'] = $val->documentCode;
                        $data[$x]['Posted Date'] = $val->documentDate != '1970-01-01' ? \Helper::dateFormat($val->documentDate) : null;
                        $data[$x]['Supplier Code'] = $val->SupplierCode;
                        $data[$x]['Supplier Name'] = $val->suppliername;
                        $data[$x]['Invoice Number'] = $val->invoiceNumber;
                        $data[$x]['Invoice Date'] = \Helper::dateFormat($val->invoiceDate);
                        $data[$x]['Document Narration'] = $val->documentNarration;
                        $data[$x]['Currency'] = $val->documentCurrency;
                        $data[$x]['Document Amount'] = $val->invoiceAmount;
                        $x++;
                    }
                } else {
                    $data = array();
                }
                $csv = \Excel::create('payment_suppliers_by_year', function ($excel) use ($data) {
                    $excel->sheet('sheet name', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', true);
                        $sheet->setAutoSize(true);
                        $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                    });
                    $lastrow = $excel->getActiveSheet()->getHighestRow();
                    $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                })->download($type);

                return $this->sendResponse(array(), 'successfully export');
                break;
            case 'APSBS':
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $output = $this->getSupplierBalanceSummeryQRY($request);
                if ($output) {
                    $x = 0;
                    foreach ($output as $val) {
                        $data[$x]['Company ID'] = $val->companyID;
                        $data[$x]['Company Name'] = $val->CompanyName;
                        $data[$x]['Supplier Code'] = $val->SupplierCode;
                        $data[$x]['Supplier Name'] = $val->supplierName;
                        $data[$x]['Currency'] = $val->documentCurrency;
                        $data[$x]['Amount'] = $val->documentAmount;
                        $x++;
                    }
                } else {
                    $data = array();
                }
                $csv = \Excel::create('payment_suppliers_by_year', function ($excel) use ($data) {
                    $excel->sheet('sheet name', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', true);
                        $sheet->setAutoSize(true);
                        $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                    });
                    $lastrow = $excel->getActiveSheet()->getHighestRow();
                    $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                })->download($type);

                return $this->sendResponse(array(), 'successfully export');
                break;
            case 'APSA':// Supplier Aging
                $reportTypeID = $request->reportTypeID;
                $type = $request->type;
                if ($reportTypeID == 'SAD') { //supplier aging detail
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $output = $this->getSupplierAgingDetailQRY($request);
                    if ($output['data']) {
                        $x = 0;
                        foreach ($output['data'] as $val) {
                            $lineTotal = 0;
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Company Name'] = $val->CompanyName;
                            $data[$x]['Document Date'] = \Helper::dateFormat($val->documentDate);
                            $data[$x]['Document Code'] = $val->documentCode;
                            $data[$x]['Supplier Code'] = $val->SupplierCode;
                            $data[$x]['Supplier Name'] = $val->suppliername;
                            $data[$x]['Invoice Number'] = $val->invoiceNumber;
                            $data[$x]['Invoice Date'] = \Helper::dateFormat($val->invoiceDate);
                            $data[$x]['Currency'] = $val->documentCurrency;
                            $data[$x]['Aging Days'] = $val->ageDays;
                            foreach ($output['aging'] as $val2) {
                                $data[$x][$val2] = $val->$val2;
                                $lineTotal += $val->$val2;
                            }
                            $data[$x]['Advance/UnAllocated Amount'] = $val->unAllocatedAmount;
                            $data[$x]['Total'] = $lineTotal;
                            $x++;
                        }
                    } else {
                        $data = array();
                    }
                } else if ($reportTypeID == 'SAS') { //supplier aging summary
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $output = $this->getSupplierAgingSummaryQRY($request);
                    if ($output['data']) {
                        $x = 0;
                        foreach ($output['data'] as $val) {
                            $lineTotal = 0;
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Company Name'] = $val->CompanyName;
                            $data[$x]['Supplier Code'] = $val->SupplierCode;
                            $data[$x]['Supplier Name'] = $val->suppliername;
                            $data[$x]['Credit Period'] = $val->creditPeriod;
                            $data[$x]['Currency'] = $val->documentCurrency;
                            $data[$x]['Aging Days'] = $val->ageDays;
                            foreach ($output['aging'] as $val2) {
                                $data[$x][$val2] = $val->$val2;
                                $lineTotal += $val->$val2;
                            }
                            $data[$x]['Advance/UnAllocated Amount'] = $val->unAllocatedAmount;
                            $data[$x]['Total'] = $lineTotal;
                            $x++;
                        }
                    } else {
                        $data = array();
                    }
                } else if ($reportTypeID == 'SADA') { //supplier aging detail advance
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $output = $this->getSupplierAgingDetailAdvanceQRY($request);
                    if ($output['data']) {
                        $x = 0;
                        foreach ($output['data'] as $val) {
                            $lineTotal = 0;
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Company Name'] = $val->CompanyName;
                            $data[$x]['Document Date'] = \Helper::dateFormat($val->documentDate);
                            $data[$x]['Document Code'] = $val->documentCode;
                            $data[$x]['Supplier Code'] = $val->SupplierCode;
                            $data[$x]['Supplier Name'] = $val->suppliername;
                            $data[$x]['Invoice Number'] = $val->invoiceNumber;
                            $data[$x]['Invoice Date'] = \Helper::dateFormat($val->invoiceDate);
                            $data[$x]['Currency'] = $val->documentCurrency;
                            $data[$x]['Aging Days'] = $val->ageDays;
                            foreach ($output['aging'] as $val2) {
                                $data[$x][$val2] = $val->$val2;
                                $lineTotal += $val->$val2;
                            }
                            $data[$x]['Advance/UnAllocated Amount'] = $lineTotal;
                            $x++;
                        }
                    } else {
                        $data = array();
                    }
                } else if ($reportTypeID == 'SASA') { //supplier aging summary
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $output = $this->getSupplierAgingSummaryAdvanceQRY($request);
                    if ($output['data']) {
                        $x = 0;
                        foreach ($output['data'] as $val) {
                            $lineTotal = 0;
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Company Name'] = $val->CompanyName;
                            $data[$x]['Supplier Code'] = $val->SupplierCode;
                            $data[$x]['Supplier Name'] = $val->suppliername;
                            $data[$x]['Credit Period'] = $val->creditPeriod;
                            $data[$x]['Currency'] = $val->documentCurrency;
                            $data[$x]['Aging Days'] = $val->ageDays;
                            foreach ($output['aging'] as $val2) {
                                $data[$x][$val2] = $val->$val2;
                                $lineTotal += $val->$val2;
                            }
                            $data[$x]['Total'] = $lineTotal;
                            $x++;
                        }
                    } else {
                        $data = array();
                    }
                }
                $csv = \Excel::create('supplier_aging', function ($excel) use ($data) {
                    $excel->sheet('sheet name', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', true);
                        $sheet->setAutoSize(true);
                        $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                    });
                    $lastrow = $excel->getActiveSheet()->getHighestRow();
                    $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                })->download($type);
                return $this->sendResponse(array(), 'successfully export');
                break;
            case 'TS':// Top Suppliers
                $reportTypeID = $request->reportTypeID;
                $type = $request->type;
                $name = "";
                if ($reportTypeID == 'TSCW' || $reportTypeID == 'TSC') {
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $output = $this->getTopSupplierQRY($request);

                    if($reportTypeID == 'TSCW'){
                        $name = "company_wise";
                    }else if($reportTypeID == 'TSC'){
                        $name = "consolidated";
                    }

                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {

                            if($reportTypeID == 'TSCW'){
                                $data[$x]['Company ID'] = $val->companyID;
                                $data[$x]['Company Name'] = $val->CompanyName;
                            }
                            $data[$x]['Supplier Code'] = $val->supplierPrimaryCode;
                            $data[$x]['Supplier Name'] = $val->supplierName;
                            $data[$x]['Supplier Country'] = $val->supplierCountry;
                            $data[$x]['Amount'] = round($val->Amount,2);
                            $x++;
                        }
                    } else {
                        $data = array();
                    }
                }
                $csv = \Excel::create('top_suppliers_by_year_'.$name, function ($excel) use ($data) {
                    $excel->sheet('sheet name', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', true);
                        $sheet->setAutoSize(true);
                        $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                    });
                    $lastrow = $excel->getActiveSheet()->getHighestRow();
                    $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                })->download($type);
                return $this->sendResponse(array(), 'successfully export');
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }


    function getSupplierLedgerQRY($request)
    {
        $fromDate = new Carbon($request->fromDate);
        //$fromDate = $asOfDate->addDays(1);
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

        $suppliers = (array)$request->suppliers;
        $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();

        $controlAccountsSystemID = $request->controlAccountsSystemID;

        $currency = $request->currencyID;
        $currencyQry = '';
        $invoiceAmountQry = '';
        $decimalPlaceQry = '';
        if ($currency == 1) {
            $currencyQry = "finalAgingDetail.transCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountTrans, finalAgingDetail.documentTransDecimalPlaces ),0) AS invoiceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentTransDecimalPlaces AS balanceDecimalPlaces";
        } else if ($currency == 2) {
            $currencyQry = "finalAgingDetail.localCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountLocal, finalAgingDetail.documentLocalDecimalPlaces ),0) AS invoiceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentLocalDecimalPlaces AS balanceDecimalPlaces";
        } else {
            $currencyQry = "finalAgingDetail.rptCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountRpt, finalAgingDetail.documentRptDecimalPlaces ),0) AS invoiceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentRptDecimalPlaces AS balanceDecimalPlaces";
        }
        $currencyID = $request->currencyID;

        $query = 'SELECT
                    finalAgingDetail.companySystemID,
                    finalAgingDetail.companyID,
                    finalAgingDetail.CompanyName,
                    finalAgingDetail.documentSystemID,
                    finalAgingDetail.documentID,
                    finalAgingDetail.documentCode,
                    finalAgingDetail.documentDate,
                    finalAgingDetail.documentNarration,
                    finalAgingDetail.supplierCodeSystem,
                    finalAgingDetail.SupplierCode,
                    finalAgingDetail.suppliername,
                    finalAgingDetail.invoiceNumber,
                    finalAgingDetail.invoiceDate,
                    CURDATE() as runDate,
                    ' . $invoiceAmountQry . ',
                    ' . $currencyQry . ',
                    ' . $decimalPlaceQry . '
                FROM
                (
                SELECT
                    MAINQUERY.companySystemID,
                    MAINQUERY.companyID,
                    companymaster.CompanyName,
                    MAINQUERY.documentSystemID,
                    MAINQUERY.documentID,
                    MAINQUERY.documentCode,
                    MAINQUERY.documentDate,
                    MAINQUERY.documentNarration,
                    MAINQUERY.supplierCodeSystem,
                    suppliermaster.primarySupplierCode AS SupplierCode,
                    suppliermaster.suppliername,
                    MAINQUERY.invoiceNumber,
                    MAINQUERY.invoiceDate,
                    transCurrencyDet.CurrencyCode as transCurrencyCode,
                    transCurrencyDet.DecimalPlaces as documentTransDecimalPlaces,
                    MAINQUERY.docTransAmount AS documentAmountTrans,
                    localCurrencyDet.CurrencyCode as localCurrencyCode,
                    localCurrencyDet.DecimalPlaces as documentLocalDecimalPlaces,
                    MAINQUERY.docLocalAmount AS documentAmountLocal,
                    rptCurrencyDet.CurrencyCode as rptCurrencyCode,
                    rptCurrencyDet.DecimalPlaces as documentRptDecimalPlaces,
                    MAINQUERY.docRptAmount AS documentAmountRpt
                FROM
                    (
                SELECT
                    erp_generalledger.companySystemID,
                    erp_generalledger.companyID,
                    erp_generalledger.serviceLineSystemID,
                    erp_generalledger.serviceLineCode,
                    erp_generalledger.documentSystemID,
                    erp_generalledger.documentID,
                    erp_generalledger.documentSystemCode,
                    erp_generalledger.documentCode,
                    erp_generalledger.documentDate,
                    erp_generalledger.chartOfAccountSystemID,
                    erp_generalledger.glCode,
                    erp_generalledger.glAccountType,
                    erp_generalledger.documentNarration,
                    erp_generalledger.clientContractID,
                    erp_generalledger.invoiceNumber,
                    erp_generalledger.invoiceDate,
                    erp_generalledger.supplierCodeSystem,
                    erp_generalledger.documentTransCurrencyID,
                    erp_generalledger.documentTransAmount AS docTransAmount,
                    erp_generalledger.documentLocalCurrencyID,
                    erp_generalledger.documentLocalAmount AS docLocalAmount,
                    erp_generalledger.documentRptCurrencyID,
                    erp_generalledger.documentRptAmount AS docRptAmount
                FROM
                    erp_generalledger
                WHERE
                    DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                    AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                    AND erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
                    AND erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                    ) AS MAINQUERY
                LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = MAINQUERY.supplierCodeSystem
                LEFT JOIN currencymaster as transCurrencyDet ON transCurrencyDet.currencyID=MAINQUERY.documentTransCurrencyID
                LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=MAINQUERY.documentLocalCurrencyID
                LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=MAINQUERY.documentRptCurrencyID
                LEFT JOIN companymaster ON companymaster.companySystemID = MAINQUERY.companySystemID
                UNION ALL
                SELECT
                    MAINQUERY.companySystemID,
                    MAINQUERY.companyID,
                    companymaster.CompanyName,
                    MAINQUERY.documentSystemID,
                    MAINQUERY.documentID,
                    "" as documentCode,
                    "1970-01-01" as documentDate,
                    "Opening Balance" as documentNarration,
                    MAINQUERY.supplierCodeSystem,
                    suppliermaster.primarySupplierCode AS SupplierCode,
                    suppliermaster.suppliername,
                    "" as invoiceNumber,
                    "" as invoiceDate,
                    transCurrencyDet.CurrencyCode as transCurrencyCode,
                    transCurrencyDet.DecimalPlaces as documentTransDecimalPlaces,
                    SUM(IFNULL(MAINQUERY.docTransAmount,0)) AS documentAmountTrans,
                    localCurrencyDet.CurrencyCode as localCurrencyCode,
                    localCurrencyDet.DecimalPlaces as documentLocalDecimalPlaces,
                    SUM(IFNULL(MAINQUERY.docLocalAmount,0)) AS documentAmountLocal,
                    rptCurrencyDet.CurrencyCode as rptCurrencyCode,
                    rptCurrencyDet.DecimalPlaces as documentRptDecimalPlaces,
                    SUM(IFNULL(MAINQUERY.docRptAmount,0)) AS documentAmountRpt
                FROM
                    (
                SELECT
                    erp_generalledger.companySystemID,
                    erp_generalledger.companyID,
                    erp_generalledger.serviceLineSystemID,
                    erp_generalledger.serviceLineCode,
                    erp_generalledger.documentSystemID,
                    erp_generalledger.documentID,
                    erp_generalledger.documentSystemCode,
                    erp_generalledger.documentCode,
                    erp_generalledger.documentDate,
                    erp_generalledger.chartOfAccountSystemID,
                    erp_generalledger.glCode,
                    erp_generalledger.glAccountType,
                    erp_generalledger.documentNarration,
                    erp_generalledger.clientContractID,
                    erp_generalledger.invoiceNumber,
                    erp_generalledger.invoiceDate,
                    erp_generalledger.supplierCodeSystem,
                    erp_generalledger.documentTransCurrencyID,
                    erp_generalledger.documentTransAmount AS docTransAmount,
                    erp_generalledger.documentLocalCurrencyID,
                    erp_generalledger.documentLocalAmount AS docLocalAmount,
                    erp_generalledger.documentRptCurrencyID,
                    erp_generalledger.documentRptAmount AS docRptAmount
                FROM
                    erp_generalledger
                WHERE
                    DATE(erp_generalledger.documentDate) < "' . $fromDate . '"
                    AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                    AND erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
                    AND erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                     ) AS MAINQUERY
                LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = MAINQUERY.supplierCodeSystem
                LEFT JOIN currencymaster as transCurrencyDet ON transCurrencyDet.currencyID=MAINQUERY.documentTransCurrencyID
                LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=MAINQUERY.documentLocalCurrencyID
                LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=MAINQUERY.documentRptCurrencyID
                 LEFT JOIN companymaster ON companymaster.companySystemID = MAINQUERY.companySystemID
                 GROUP BY MAINQUERY.supplierCodeSystem ) as finalAgingDetail ORDER BY documentDate,suppliername';
        $output = \DB::select($query);
        //dd(DB::getQueryLog());
        return $output;
    }

    function getSupplierStatementQRY($request)
    {
        $asOfDate = new Carbon($request->fromDate);
        //$asOfDate = $asOfDate->addDays(1);
        $asOfDate = $asOfDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $suppliers = (array)$request->suppliers;
        $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();

        $controlAccountsSystemID = $request->controlAccountsSystemID;

        $currency = $request->currencyID;
        $currencyQry = '';
        $invoiceAmountQry = '';
        $balanceAmountQry = '';
        $decimalPlaceQry = '';
        $whereQry = '';
        if ($currency == 1) {
            $currencyQry = "finalAgingDetail.transCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountTrans, finalAgingDetail.documentTransDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountTrans, finalAgingDetail.documentTransDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentTransDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountTrans, finalAgingDetail.documentTransDecimalPlaces )";
        } else if ($currency == 2) {
            $currencyQry = "finalAgingDetail.localCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountLocal, finalAgingDetail.documentLocalDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountLocal, finalAgingDetail.documentLocalDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentLocalDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountLocal, finalAgingDetail.documentLocalDecimalPlaces )";
        } else {
            $currencyQry = "finalAgingDetail.rptCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountRpt, finalAgingDetail.documentRptDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountRpt, finalAgingDetail.documentRptDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentRptDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountRpt, finalAgingDetail.documentRptDecimalPlaces )";
        }
        $currencyID = $request->currencyID;
        //DB::enableQueryLog();
        $output = \DB::select('SELECT
                                finalAgingDetail.companySystemID,
                                finalAgingDetail.companyID,
                                finalAgingDetail.CompanyName,
                                finalAgingDetail.documentSystemID,
                                finalAgingDetail.documentID,
                                finalAgingDetail.documentCode,
                                finalAgingDetail.documentDate,
                                finalAgingDetail.documentNarration,
                                finalAgingDetail.supplierCodeSystem,
                                finalAgingDetail.SupplierCode,
                                finalAgingDetail.suppliername,
                                finalAgingDetail.invoiceNumber,
                                finalAgingDetail.invoiceDate,
                                CURDATE() as runDate,
                                DATEDIFF("' . $asOfDate . '",DATE(finalAgingDetail.documentDate)) as ageDays,
                                ' . $invoiceAmountQry . ',
                                ' . $balanceAmountQry . ',
                                ' . $currencyQry . ',
                                ' . $decimalPlaceQry . ',
                                CONCAT(finalAgingDetail.SupplierCode," - ",finalAgingDetail.suppliername) as concatSupplierName
                            FROM
                            (
                            SELECT
                                MAINQUERY.companySystemID,
                                MAINQUERY.companyID,
                                companymaster.CompanyName,
                                MAINQUERY.documentSystemID,
                                MAINQUERY.documentID,
                                MAINQUERY.documentCode,
                                MAINQUERY.documentDate,
                                MAINQUERY.documentNarration,
                                MAINQUERY.supplierCodeSystem,
                                suppliermaster.primarySupplierCode AS SupplierCode,
                                suppliermaster.suppliername,
                                MAINQUERY.invoiceNumber,
                                MAINQUERY.invoiceDate,
                                transCurrencyDet.CurrencyCode as transCurrencyCode,
                                transCurrencyDet.DecimalPlaces as documentTransDecimalPlaces,
                                MAINQUERY.docTransAmount AS documentAmountTrans,
                                localCurrencyDet.CurrencyCode as localCurrencyCode,
                                localCurrencyDet.DecimalPlaces as documentLocalDecimalPlaces,
                                MAINQUERY.docLocalAmount AS documentAmountLocal,
                                rptCurrencyDet.CurrencyCode as rptCurrencyCode,
                                rptCurrencyDet.DecimalPlaces as documentRptDecimalPlaces,
                                MAINQUERY.docRptAmount AS documentAmountRpt,
                            
                                (MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans) * -1 AS PaidAmountTrans,
                            
                                (MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal) * -1 AS PaidAmountLocal,
                            
                                (MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt) * -1 AS PaidAmountRpt,
                            
                                MAINQUERY.docTransAmount+MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans  as balanceAmountTrans,
                            
                                MAINQUERY.docLocalAmount+MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal  as balanceAmountLocal,
                            
                                MAINQUERY.docRptAmount + MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt AS balanceAmountRpt
                            FROM
                                (
                            SELECT
                                erp_generalledger.companySystemID,
                                erp_generalledger.companyID,
                                erp_generalledger.serviceLineSystemID,
                                erp_generalledger.serviceLineCode,
                                erp_generalledger.documentSystemID,
                                erp_generalledger.documentID,
                                erp_generalledger.documentSystemCode,
                                erp_generalledger.documentCode,
                                erp_generalledger.documentDate,
                                erp_generalledger.chartOfAccountSystemID,
                                erp_generalledger.glCode,
                                erp_generalledger.glAccountType,
                                erp_generalledger.documentNarration,
                                erp_generalledger.clientContractID,
                                erp_generalledger.invoiceNumber,
                                erp_generalledger.invoiceDate,
                                erp_generalledger.supplierCodeSystem,
                                erp_generalledger.documentTransCurrencyID,
                                erp_generalledger.documentTransAmount * - 1 AS docTransAmount,
                                erp_generalledger.documentLocalCurrencyID,
                                erp_generalledger.documentLocalAmount * - 1 AS docLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                erp_generalledger.documentRptAmount * - 1 AS docRptAmount,
                            IF
                                ( paymentVoucherAmount.PaidPaymentVoucherTransAmount IS NULL, 0, paymentVoucherAmount.PaidPaymentVoucherTransAmount ) AS PaidPaymentVoucherTransAmount,
                            IF
                                ( paymentVoucherAmount.PaidPaymentVoucherLocalAmount IS NULL, 0, paymentVoucherAmount.PaidPaymentVoucherLocalAmount ) AS PaidPaymentVoucherLocalAmount,
                            IF
                                ( paymentVoucherAmount.PaidPaymentVoucherRptAmount IS NULL, 0, paymentVoucherAmount.PaidPaymentVoucherRptAmount ) AS PaidPaymentVoucherRptAmount,
                            IF
                                ( InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountTrans IS NULL, 0, InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountTrans ) AS InvoiceMatchedINMatchingAmountTrans,
                            IF
                                ( InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountLocal IS NULL, 0, InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountLocal ) AS InvoiceMatchedINMatchingAmountLocal,
                            IF
                                ( InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountRpt IS NULL, 0, InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountRpt ) AS InvoiceMatchedINMatchingAmountRpt,
                            IF
                                ( InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountTrans IS NULL, 0, InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountTrans ) AS InvoiceMatchedForpaymentAmountTrans,
                            IF
                                ( InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountLocal IS NULL, 0, InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountLocal ) AS InvoiceMatchedForpaymentAmountLocal,
                            IF
                                ( InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountRpt IS NULL, 0, InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountRpt ) AS InvoiceMatchedForpaymentAmountRpt,
                            IF
                                ( debitNoteMatched.debitNoteMatchedAmountTrans IS NULL, 0, debitNoteMatched.debitNoteMatchedAmountTrans ) AS debitNoteMatchedAmountTrans,
                            IF
                                ( debitNoteMatched.debitNoteMatchedAmountLocal IS NULL, 0, debitNoteMatched.debitNoteMatchedAmountLocal ) AS debitNoteMatchedAmountLocal,
                            IF
                                ( debitNoteMatched.debitNoteMatchedAmountRpt IS NULL, 0, debitNoteMatched.debitNoteMatchedAmountRpt ) AS debitNoteMatchedAmountRpt
                            FROM
                                erp_generalledger
                                LEFT JOIN (-- payment voucher matched with invoice or debit note
                            SELECT
                                erp_paysupplierinvoicedetail.companySystemID,
                                erp_paysupplierinvoicedetail.companyID,
                                erp_paysupplierinvoicemaster.documentSystemID,
                                erp_paysupplierinvoicemaster.documentID,
                                erp_paysupplierinvoicemaster.PayMasterAutoId,
                                erp_paysupplierinvoicemaster.BPVcode,
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS PaidPaymentVoucherTransAmount,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS PaidPaymentVoucherLocalAmount,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS PaidPaymentVoucherRptAmount
                            FROM
                                erp_paysupplierinvoicedetail
                                INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.PayMasterAutoId = erp_paysupplierinvoicemaster.PayMasterAutoId
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID = 0
                                AND erp_paysupplierinvoicemaster.approved = -1
                                AND erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND DATE(erp_paysupplierinvoicemaster.postedDate) <= "' . $asOfDate . '"
                            GROUP BY
                                companySystemID,
                                documentSystemID,
                                PayMasterAutoId
                                ) AS paymentVoucherAmount ON paymentVoucherAmount.companySystemID = erp_generalledger.companySystemID
                                AND paymentVoucherAmount.documentSystemID = erp_generalledger.documentSystemID
                                AND paymentVoucherAmount.PayMasterAutoId = erp_generalledger.documentSystemCode
                                LEFT JOIN (-- invoice matched in a matching document
                            SELECT
                                erp_paysupplierinvoicedetail.companySystemID,
                                erp_paysupplierinvoicedetail.companyID,
                                erp_paysupplierinvoicedetail.addedDocumentSystemID,
                                erp_paysupplierinvoicedetail.addedDocumentID,
                                erp_paysupplierinvoicedetail.bookingInvSystemCode,
                                erp_paysupplierinvoicedetail.bookingInvDocCode,
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS InvoiceMatchedINMatchingAmountTrans,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS InvoiceMatchedINMatchingAmountLocal,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS InvoiceMatchedINMatchingAmountRpt
                            FROM
                                erp_paysupplierinvoicedetail
                                INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_paysupplierinvoicedetail.matchingDocID
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID > 0
                                AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '"
                            GROUP BY
                                companySystemID,
                                addedDocumentSystemID,
                                bookingInvSystemCode
                                ) AS InvoiceMatchedINMatching ON InvoiceMatchedINMatching.companySystemID = erp_generalledger.companySystemID
                                AND InvoiceMatchedINMatching.addedDocumentSystemID = erp_generalledger.documentSystemID
                                AND InvoiceMatchedINMatching.bookingInvSystemCode = erp_generalledger.documentSystemCode
                                LEFT JOIN (-- invoice matched in a payment voucher document
                            SELECT
                                erp_paysupplierinvoicedetail.companySystemID,
                                erp_paysupplierinvoicedetail.companyID,
                                erp_paysupplierinvoicedetail.addedDocumentSystemID,
                                erp_paysupplierinvoicedetail.addedDocumentID,
                                erp_paysupplierinvoicedetail.bookingInvSystemCode,
                                erp_paysupplierinvoicedetail.bookingInvDocCode,
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS InvoiceMatchedForpaymentAmountTrans,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS InvoiceMatchedForpaymentAmountLocal,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS InvoiceMatchedForpaymentAmountRpt
                            FROM
                                erp_paysupplierinvoicedetail
                                INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.PayMasterAutoId = erp_paysupplierinvoicemaster.PayMasterAutoId
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID = 0
                                AND erp_paysupplierinvoicemaster.approved = -1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND DATE(erp_paysupplierinvoicemaster.postedDate) <= "' . $asOfDate . '"
                            GROUP BY
                                companySystemID,
                                addedDocumentSystemID,
                                bookingInvSystemCode
                                ) AS InvoiceMatchedForpayment ON InvoiceMatchedForpayment.companySystemID = erp_generalledger.companySystemID
                                AND InvoiceMatchedForpayment.addedDocumentSystemID = erp_generalledger.documentSystemID
                                AND InvoiceMatchedForpayment.bookingInvSystemCode = erp_generalledger.documentSystemCode
                                LEFT JOIN (-- matched debit note
                            SELECT
                                erp_matchdocumentmaster.companySystemID,
                                erp_matchdocumentmaster.companyID,
                                erp_matchdocumentmaster.documentSystemID,
                                erp_matchdocumentmaster.documentID,
                                erp_matchdocumentmaster.PayMasterAutoId,
                                erp_matchdocumentmaster.BPVcode,
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS debitNoteMatchedAmountTrans,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS debitNoteMatchedAmountLocal,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS debitNoteMatchedAmountRpt
                            FROM
                                erp_matchdocumentmaster
                                INNER JOIN erp_paysupplierinvoicedetail ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_paysupplierinvoicedetail.matchingDocID
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID > 0
                                AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '"
                            GROUP BY
                                companySystemID,
                                documentSystemID,
                                PayMasterAutoId
                                ) AS debitNoteMatched ON debitNoteMatched.companySystemID = erp_generalledger.companySystemID
                                AND debitNoteMatched.documentSystemID = erp_generalledger.documentSystemID
                                AND debitNoteMatched.PayMasterAutoId = erp_generalledger.documentSystemCode
                            WHERE
                                DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
                                AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
                                AND erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                ) AS MAINQUERY
                            LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = MAINQUERY.supplierCodeSystem
                            LEFT JOIN companymaster ON MAINQUERY.companySystemID = companymaster.companySystemID
                            LEFT JOIN currencymaster as transCurrencyDet ON transCurrencyDet.currencyID=MAINQUERY.documentTransCurrencyID
                            LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=MAINQUERY.documentLocalCurrencyID
                            LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=MAINQUERY.documentRptCurrencyID) as finalAgingDetail WHERE ' . $whereQry . ' <> 0 ORDER BY documentDate ASC;');
        //dd(DB::getQueryLog());
        return $output;
    }

    function getPaymentSuppliersByYear($request)
    {

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $currency = $request->currencyID;

        if (isset($request->suppliers)) {
            $suppliers = $request->suppliers;
            $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();
        } else {
            $supplierSystemID = [];
        }


        if (isset($request->year)) {
            $year = $request->year;
        } else {
            $year = 0;
        }

        $currencyClm = "documentRptAmount";
        if ($currency == 2) {
            $currencyClm = "documentLocalAmount";
        } else if ($currency == 3) {
            $currencyClm = "documentRptAmount";
        }

        $reportSD = $request->reportSD;
        $reportTypeID = $request->reportTypeID;

        if ($reportTypeID == 'APPSY') {
            if ($reportSD == 'detail') {
                //DB::enableQueryLog();
                $output = \DB::select('SELECT
                                    paymentsBySupplierSummary.companySystemID,
                                    paymentsBySupplierSummary.companyID,
                                    paymentsBySupplierSummary.supplierCodeSystem,
                                    paymentsBySupplierSummary.supplierCode,
                                    paymentsBySupplierSummary.supplierName,
                                    paymentsBySupplierSummary.PaymentType,
                                    paymentsBySupplierSummary.documentDate,
                                    paymentsBySupplierSummary.documentCode,
                                    paymentsBySupplierSummary.documentLocalCurrency,
                                    paymentsBySupplierSummary.documentRptCurrency,
                                    paymentsBySupplierSummary.documentLocalAmount,
                                    paymentsBySupplierSummary.documentRptAmount,
                                    paymentsBySupplierSummary.CompanyName
                                FROM
                                (
                                SELECT
                                    MAINQUERY.companyID,
                                    MAINQUERY.companySystemID,
                                    MAINQUERY.supplierCodeSystem,
                                    MAINQUERY.supplierCode,
                                    MAINQUERY.supplierName,
                                    MAINQUERY.PaymentType,
                                    MAINQUERY.documentDate,
                                    MAINQUERY.documentCode,
                                    MAINQUERY.documentLocalCurrency,
                                    MAINQUERY.documentRptCurrency,
                                    MAINQUERY.documentLocalAmount,
                                    MAINQUERY.documentRptAmount,
                                    MAINQUERY.CompanyName
                                FROM
                                    (
                                SELECT
                                    erp_generalledger.companySystemID,
                                    erp_generalledger.companyID,
                                    erp_generalledger.documentSystemID,
                                    erp_generalledger.documentID,
                                    erp_generalledger.documentSystemCode,
                                    erp_generalledger.documentCode,
                                    erp_generalledger.supplierCodeSystem,
                                    suppliermaster.primarySupplierCode AS supplierCode,
                                    suppliermaster.supplierName,
                                    erp_generalledger.documentDate,
                                    erp_generalledger.documentTransCurrencyID,
                                    erp_generalledger.documentTransAmount,
                                    erp_generalledger.documentLocalCurrencyID,
                                    round(erp_generalledger.documentLocalAmount,0) as documentLocalAmount,
                                    erp_generalledger.documentRptCurrencyID,
                                    round(erp_generalledger.documentRptAmount,0) as documentRptAmount,
                                    erp_generalledger.documentType,
                                    companymaster.CompanyName,
                                    If(erp_generalledger.documentType=2,"Invoive Payment",If(erp_generalledger.documentType=3,"Direct Payment",If(erp_generalledger.documentType=5,"Advance Payment",""))) as PaymentType,
                                     currLocal.CurrencyCode as documentLocalCurrency,
                                   currRpt.CurrencyCode as documentRptCurrency
                                FROM
                                    erp_generalledger
                                    INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                                    LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
                                    LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
                                    LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem 
                                    WHERE erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                    AND erp_generalledger.documentSystemID = 4
                                    AND erp_generalledger.supplierCodeSystem > 0 
                                    AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') 
                                    AND YEAR ( erp_generalledger.documentDate ) = "' . $year . '"
                                    AND erp_generalledger.documentTransAmount > 0 
                                ) AS MAINQUERY
                                ) AS paymentsBySupplierSummary
                                ORDER BY paymentsBySupplierSummary.documentRptAmount DESC');
            } else {
                //DB::enableQueryLog();
                $output = \DB::select('SELECT
                                paymentsBySupplierSummary.companySystemID,
                                paymentsBySupplierSummary.companyID,
                                paymentsBySupplierSummary.supplierCodeSystem,
                                paymentsBySupplierSummary.supplierCode,
                                paymentsBySupplierSummary.supplierName,
                                paymentsBySupplierSummary.DocYEAR,
                                paymentsBySupplierSummary.documentLocalCurrencyID,
                                paymentsBySupplierSummary.documentRptCurrencyID,
                                paymentsBySupplierSummary.CompanyName,
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
                                MAINQUERY.companyID,
                                MAINQUERY.companySystemID,
                                MAINQUERY.supplierCodeSystem,
                                MAINQUERY.supplierCode,
                                MAINQUERY.supplierName,
                                MAINQUERY.DocYEAR,
                                MAINQUERY.documentLocalCurrencyID,
                                MAINQUERY.documentRptCurrencyID,
                                MAINQUERY.CompanyName,
                            IF
                                ( MAINQUERY.DocMONTH = 1, ' . $currencyClm . ', 0 ) AS Jan,
                            IF
                                ( MAINQUERY.DocMONTH = 2, ' . $currencyClm . ', 0 ) AS Feb,
                            IF
                                ( MAINQUERY.DocMONTH = 3, ' . $currencyClm . ', 0 ) AS March,
                            IF
                                ( MAINQUERY.DocMONTH = 4, ' . $currencyClm . ', 0 ) AS April,
                            IF
                                ( MAINQUERY.DocMONTH = 5, ' . $currencyClm . ', 0 ) AS May,
                            IF
                                ( MAINQUERY.DocMONTH = 6, ' . $currencyClm . ', 0 ) AS June,
                            IF
                                ( MAINQUERY.DocMONTH = 7, ' . $currencyClm . ', 0 ) AS July,
                            IF
                                ( MAINQUERY.DocMONTH = 8, ' . $currencyClm . ', 0 ) AS Aug,
                            IF
                                ( MAINQUERY.DocMONTH = 9, ' . $currencyClm . ', 0 ) AS Sept,
                            IF
                                ( MAINQUERY.DocMONTH = 10, ' . $currencyClm . ', 0 ) AS Oct,
                            IF
                                ( MAINQUERY.DocMONTH = 11, ' . $currencyClm . ', 0 ) AS Nov,
                            IF
                                ( MAINQUERY.DocMONTH = 12, ' . $currencyClm . ', 0 ) AS Dece,
                                 ' . $currencyClm . ' as Total
                            FROM
                                (
                            SELECT
                                erp_generalledger.companySystemID,
                                erp_generalledger.companyID,
                                erp_generalledger.documentSystemID,
                                erp_generalledger.documentID,
                                erp_generalledger.documentSystemCode,
                                erp_generalledger.documentCode,
                                erp_generalledger.supplierCodeSystem,
                                suppliermaster.primarySupplierCode AS supplierCode,
                                suppliermaster.supplierName,
                                erp_generalledger.documentDate,
                                MONTH ( erp_generalledger.documentDate ) AS DocMONTH,
                                YEAR ( erp_generalledger.documentDate ) AS DocYEAR,
                                erp_generalledger.documentTransCurrencyID,
                                erp_generalledger.documentTransAmount,
                                erp_generalledger.documentLocalCurrencyID,
                                round(erp_generalledger.documentLocalAmount,0) as documentLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                round(erp_generalledger.documentRptAmount,0) as documentRptAmount,
                                erp_generalledger.documentType,
                                companymaster.CompanyName,
                                If(erp_generalledger.documentType=2,"Invoive Payment",If(erp_generalledger.documentType=3,"Direct Payment",If(erp_generalledger.documentType=5,"Advance Payment",""))) as PaymentType
                            FROM
                                erp_generalledger
                                INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                                LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                                WHERE erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND erp_generalledger.documentSystemID = 4
                                AND erp_generalledger.supplierCodeSystem > 0 
                                AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') 
                                AND YEAR ( erp_generalledger.documentDate ) = "' . $year . '"
                                AND erp_generalledger.documentTransAmount > 0 
                            ) AS MAINQUERY
                            ) AS paymentsBySupplierSummary
                                GROUP BY
                                paymentsBySupplierSummary.companySystemID,
                                paymentsBySupplierSummary.supplierCodeSystem
                                ORDER BY Total DESC;');

            }
        } else if ($reportTypeID == 'APDPY') {

            if ($reportSD == 'detail') {

                $output = \DB::select('SELECT
                                        directPaymentsSummary.companySystemID,
                                        directPaymentsSummary.companyID,
                                        directPaymentsSummary.chartOfAccountSystemID,
                                        directPaymentsSummary.glCode,
                                        directPaymentsSummary.glAccountType,
                                        directPaymentsSummary.AccountDescription,
                                        directPaymentsSummary.DocYEAR,
                                        directPaymentsSummary.documentLocalCurrencyID,
                                        directPaymentsSummary.documentRptCurrencyID,
                                        directPaymentsSummary.CompanyName,
                                        directPaymentsSummary.documentLocalAmount as documentLocalAmount,
                                        directPaymentsSummary.documentRptAmount as documentRptAmount,
                                        directPaymentsSummary.localCurrencyCode as documentLocalCurrency,
                                        directPaymentsSummary.rptCurrencyCode as documentRptCurrency,
                                        directPaymentsSummary.documentCode,
                                        directPaymentsSummary.documentDate
                                    FROM
                                        (
                                    SELECT
                                        MAINQUERY.companyID,
                                        MAINQUERY.companySystemID,
                                        MAINQUERY.chartOfAccountSystemID,
                                        MAINQUERY.glCode,
                                        MAINQUERY.glAccountType,
                                        MAINQUERY.AccountDescription,
                                        MAINQUERY.DocYEAR,
                                        MAINQUERY.documentLocalCurrencyID,
                                        MAINQUERY.documentRptCurrencyID,
                                        MAINQUERY.documentLocalAmount,
                                        MAINQUERY.documentRptAmount,
                                        MAINQUERY.CompanyName,
                                        MAINQUERY.localCurrencyCode,
                                        MAINQUERY.rptCurrencyCode,
                                        MAINQUERY.documentCode,
                                        MAINQUERY.documentDate
                                    FROM
                                        (
                                    SELECT
                                        erp_generalledger.companySystemID,
                                        erp_generalledger.companyID,
                                        erp_generalledger.documentCode,
                                        erp_generalledger.chartOfAccountSystemID,
                                        erp_generalledger.glCode,
                                        chartofaccounts.AccountDescription,
                                        erp_generalledger.glAccountType,
                                        MONTH ( erp_generalledger.documentDate ) AS DocMONTH,
                                        YEAR ( erp_generalledger.documentDate ) AS DocYEAR,
                                        localCurrency.CurrencyCode AS localCurrencyCode,
                                        erp_generalledger.documentLocalCurrencyID,
                                        round(erp_generalledger.documentLocalAmount,0) as documentLocalAmount,
                                        rptCurrency.CurrencyCode AS rptCurrencyCode,
                                        erp_generalledger.documentRptCurrencyID,
                                        round(erp_generalledger.documentRptAmount,0) as documentRptAmount,
                                        companymaster.CompanyName,
                                        erp_generalledger.documentDate
                                    FROM
                                        erp_generalledger
                                        INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                                        LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
                                        AND chartofaccounts.relatedPartyYN = 0
                                        LEFT JOIN currencymaster AS localCurrency ON erp_generalledger.documentLocalCurrencyID = localCurrency.currencyID
                                        LEFT JOIN currencymaster AS rptCurrency ON erp_generalledger.documentRptCurrencyID = rptCurrency.currencyID 
                                    WHERE
                                        erp_generalledger.documentSystemID = 4 -- hard code as 4
                                        AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')  
                                        AND chartofaccounts.relatedPartyYN = 0
                                        AND (erp_generalledger.supplierCodeSystem IS NULL 
                                        OR erp_generalledger.supplierCodeSystem = 0) -- hard code filers
                                        AND YEAR ( erp_generalledger.documentDate ) = "' . $year . '" 
                                        AND erp_generalledger.documentTransAmount > 0 -- hard code this filter
                                        
                                        ) AS MAINQUERY 
                                        ) AS directPaymentsSummary;');

            } else {

                $output = \DB::select('SELECT
                                        directPaymentsSummary.companySystemID,
                                        directPaymentsSummary.companyID,
                                        directPaymentsSummary.chartOfAccountSystemID,
                                        directPaymentsSummary.glCode,
                                        directPaymentsSummary.glAccountType,
                                        directPaymentsSummary.AccountDescription,
                                        directPaymentsSummary.DocYEAR,
                                        directPaymentsSummary.documentLocalCurrencyID,
                                        directPaymentsSummary.documentRptCurrencyID,
                                        directPaymentsSummary.CompanyName,
                                        sum( Jan ) AS Jan,
                                        sum( Feb ) AS Feb,
                                        sum( March ) AS March,
                                        sum( April ) AS April,
                                        sum( May ) AS May,
                                        sum( June ) AS June,
                                        sum( July ) AS July,
                                        sum( Aug ) AS Aug,
                                        sum( Sept ) AS Sept,
                                        sum( Oct ) AS Oct,
                                        sum( Nov ) AS Nov,
                                        sum( Dece ) AS Dece ,
                                        sum(Total) as Total
                                    FROM
                                        (
                                    SELECT
                                        MAINQUERY.companyID,
                                        MAINQUERY.companySystemID,
                                        MAINQUERY.chartOfAccountSystemID,
                                        MAINQUERY.glCode,
                                        MAINQUERY.glAccountType,
                                        MAINQUERY.AccountDescription,
                                        MAINQUERY.DocYEAR,
                                        MAINQUERY.documentLocalCurrencyID,
                                        MAINQUERY.documentRptCurrencyID,
                                        MAINQUERY.CompanyName,
                                    IF
                                        ( MAINQUERY.DocMONTH = 1, documentRptAmount, 0 ) AS Jan,
                                    IF
                                        ( MAINQUERY.DocMONTH = 2, documentRptAmount, 0 ) AS Feb,
                                    IF
                                        ( MAINQUERY.DocMONTH = 3, documentRptAmount, 0 ) AS March,
                                    IF
                                        ( MAINQUERY.DocMONTH = 4, documentRptAmount, 0 ) AS April,
                                    IF
                                        ( MAINQUERY.DocMONTH = 5, documentRptAmount, 0 ) AS May,
                                    IF
                                        ( MAINQUERY.DocMONTH = 6, documentRptAmount, 0 ) AS June,
                                    IF
                                        ( MAINQUERY.DocMONTH = 7, documentRptAmount, 0 ) AS July,
                                    IF
                                        ( MAINQUERY.DocMONTH = 8, documentRptAmount, 0 ) AS Aug,
                                    IF
                                        ( MAINQUERY.DocMONTH = 9, documentRptAmount, 0 ) AS Sept,
                                    IF
                                        ( MAINQUERY.DocMONTH = 10, documentRptAmount, 0 ) AS Oct,
                                    IF
                                        ( MAINQUERY.DocMONTH = 11, documentRptAmount, 0 ) AS Nov,
                                    IF
                                        ( MAINQUERY.DocMONTH = 12, documentRptAmount, 0 ) AS Dece ,
                                        ' . $currencyClm . ' as Total
                                    FROM
                                        (
                                    SELECT
                                        erp_generalledger.companySystemID,
                                        erp_generalledger.companyID,
                                        erp_generalledger.documentCode,
                                        erp_generalledger.chartOfAccountSystemID,
                                        erp_generalledger.glCode,
                                        chartofaccounts.AccountDescription,
                                        erp_generalledger.glAccountType,
                                        MONTH ( erp_generalledger.documentDate ) AS DocMONTH,
                                        YEAR ( erp_generalledger.documentDate ) AS DocYEAR,
                                        localCurrency.CurrencyCode AS localCurrencyCode,
                                        erp_generalledger.documentLocalCurrencyID,
                                        round(erp_generalledger.documentLocalAmount,0) as documentLocalAmount,
                                        rptCurrency.CurrencyCode AS rptCurrencyCode,
                                        erp_generalledger.documentRptCurrencyID,
                                        round(erp_generalledger.documentRptAmount,0) as documentRptAmount,
                                        companymaster.CompanyName
                                    FROM
                                        erp_generalledger
                                        INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                                        LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
                                        LEFT JOIN currencymaster AS localCurrency ON erp_generalledger.documentLocalCurrencyID = localCurrency.currencyID
                                        LEFT JOIN currencymaster AS rptCurrency ON erp_generalledger.documentRptCurrencyID = rptCurrency.currencyID 
                                    WHERE
                                        erp_generalledger.documentSystemID = 4 -- hard code as 4
                                        AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')  
                                        AND chartofaccounts.relatedPartyYN = 0
                                        AND (erp_generalledger.supplierCodeSystem IS NULL 
                                        OR erp_generalledger.supplierCodeSystem = 0) -- hard code filers
                                        AND YEAR ( erp_generalledger.documentDate ) = "' . $year . '" 
                                        AND erp_generalledger.documentTransAmount > 0 -- hard code this filter
                                        
                                        ) AS MAINQUERY 
                                        ) AS directPaymentsSummary 
                                    GROUP BY
                                    directPaymentsSummary.companySystemID,
                                    directPaymentsSummary.chartOfAccountSystemID ORDER BY Total DESC;');
            }
        } else if ($reportTypeID == 'APAPY') {


            $bySupplierQry = 'SELECT
                                paymentsBySupplierSummary.companySystemID,
                                paymentsBySupplierSummary.companyID,
                                paymentsBySupplierSummary.CompanyName,
                                
                                paymentsBySupplierSummary.supplierCodeSystem as docSystemId,
                                paymentsBySupplierSummary.supplierCode as docCode,
                                paymentsBySupplierSummary.supplierName as docDes,
                                "" as docType,
                                
                                paymentsBySupplierSummary.DocYEAR,
                                paymentsBySupplierSummary.documentLocalCurrencyID,
                                paymentsBySupplierSummary.documentRptCurrencyID,
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
                                MAINQUERY.companyID,
                                MAINQUERY.companySystemID,
                                MAINQUERY.supplierCodeSystem,
                                MAINQUERY.supplierCode,
                                MAINQUERY.supplierName,
                                MAINQUERY.DocYEAR,
                                MAINQUERY.documentLocalCurrencyID,
                                MAINQUERY.documentRptCurrencyID,
                                MAINQUERY.CompanyName,
                            IF
                                ( MAINQUERY.DocMONTH = 1, ' . $currencyClm . ', 0 ) AS Jan,
                            IF
                                ( MAINQUERY.DocMONTH = 2, ' . $currencyClm . ', 0 ) AS Feb,
                            IF
                                ( MAINQUERY.DocMONTH = 3, ' . $currencyClm . ', 0 ) AS March,
                            IF
                                ( MAINQUERY.DocMONTH = 4, ' . $currencyClm . ', 0 ) AS April,
                            IF
                                ( MAINQUERY.DocMONTH = 5, ' . $currencyClm . ', 0 ) AS May,
                            IF
                                ( MAINQUERY.DocMONTH = 6, ' . $currencyClm . ', 0 ) AS June,
                            IF
                                ( MAINQUERY.DocMONTH = 7, ' . $currencyClm . ', 0 ) AS July,
                            IF
                                ( MAINQUERY.DocMONTH = 8, ' . $currencyClm . ', 0 ) AS Aug,
                            IF
                                ( MAINQUERY.DocMONTH = 9, ' . $currencyClm . ', 0 ) AS Sept,
                            IF
                                ( MAINQUERY.DocMONTH = 10, ' . $currencyClm . ', 0 ) AS Oct,
                            IF
                                ( MAINQUERY.DocMONTH = 11, ' . $currencyClm . ', 0 ) AS Nov,
                            IF
                                ( MAINQUERY.DocMONTH = 12, ' . $currencyClm . ', 0 ) AS Dece,
                                 ' . $currencyClm . ' as Total
                            FROM
                                (
                            SELECT
                                erp_generalledger.companySystemID,
                                erp_generalledger.companyID,
                                erp_generalledger.documentSystemID,
                                erp_generalledger.documentID,
                                erp_generalledger.documentSystemCode,
                                erp_generalledger.documentCode,
                                erp_generalledger.supplierCodeSystem,
                                suppliermaster.primarySupplierCode AS supplierCode,
                                suppliermaster.supplierName,
                                erp_generalledger.documentDate,
                                MONTH ( erp_generalledger.documentDate ) AS DocMONTH,
                                YEAR ( erp_generalledger.documentDate ) AS DocYEAR,
                                erp_generalledger.documentTransCurrencyID,
                                erp_generalledger.documentTransAmount,
                                erp_generalledger.documentLocalCurrencyID,
                                round(erp_generalledger.documentLocalAmount,0) as documentLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                round(erp_generalledger.documentRptAmount,0) as documentRptAmount,
                                erp_generalledger.documentType,
                                companymaster.CompanyName,
                                If(erp_generalledger.documentType=2,"Invoive Payment",If(erp_generalledger.documentType=3,"Direct Payment",If(erp_generalledger.documentType=5,"Advance Payment",""))) as PaymentType
                            FROM
                                erp_generalledger
                                INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                                LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                                WHERE erp_generalledger.documentSystemID = 4
                                AND erp_generalledger.supplierCodeSystem > 0 
                                AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') 
                                AND YEAR ( erp_generalledger.documentDate ) = "' . $year . '"
                                AND erp_generalledger.documentTransAmount > 0 
                            ) AS MAINQUERY
                            ) AS paymentsBySupplierSummary
                                GROUP BY
                                paymentsBySupplierSummary.companySystemID,
                                paymentsBySupplierSummary.supplierCodeSystem';

            $directQry = 'SELECT
                                        directPaymentsSummary.companySystemID,
                                        directPaymentsSummary.companyID,
                                        directPaymentsSummary.CompanyName,
                                         
                                        directPaymentsSummary.chartOfAccountSystemID as docSystemId,
                                        directPaymentsSummary.glCode as docCode,
                                        directPaymentsSummary.AccountDescription as docDes,
                                        directPaymentsSummary.glAccountType as docType,
                                        
                                        directPaymentsSummary.DocYEAR,
                                        directPaymentsSummary.documentLocalCurrencyID,
                                        directPaymentsSummary.documentRptCurrencyID,
                                        sum( Jan ) AS Jan,
                                        sum( Feb ) AS Feb,
                                        sum( March ) AS March,
                                        sum( April ) AS April,
                                        sum( May ) AS May,
                                        sum( June ) AS June,
                                        sum( July ) AS July,
                                        sum( Aug ) AS Aug,
                                        sum( Sept ) AS Sept,
                                        sum( Oct ) AS Oct,
                                        sum( Nov ) AS Nov,
                                        sum( Dece ) AS Dece ,
                                        sum(Total) as Total
                                    FROM
                                        (
                                    SELECT
                                        MAINQUERY.companyID,
                                        MAINQUERY.companySystemID,
                                        MAINQUERY.chartOfAccountSystemID,
                                        MAINQUERY.glCode,
                                        MAINQUERY.glAccountType,
                                        MAINQUERY.AccountDescription,
                                        MAINQUERY.DocYEAR,
                                        MAINQUERY.documentLocalCurrencyID,
                                        MAINQUERY.documentRptCurrencyID,
                                        MAINQUERY.CompanyName,
                                    IF
                                        ( MAINQUERY.DocMONTH = 1, documentRptAmount, 0 ) AS Jan,
                                    IF
                                        ( MAINQUERY.DocMONTH = 2, documentRptAmount, 0 ) AS Feb,
                                    IF
                                        ( MAINQUERY.DocMONTH = 3, documentRptAmount, 0 ) AS March,
                                    IF
                                        ( MAINQUERY.DocMONTH = 4, documentRptAmount, 0 ) AS April,
                                    IF
                                        ( MAINQUERY.DocMONTH = 5, documentRptAmount, 0 ) AS May,
                                    IF
                                        ( MAINQUERY.DocMONTH = 6, documentRptAmount, 0 ) AS June,
                                    IF
                                        ( MAINQUERY.DocMONTH = 7, documentRptAmount, 0 ) AS July,
                                    IF
                                        ( MAINQUERY.DocMONTH = 8, documentRptAmount, 0 ) AS Aug,
                                    IF
                                        ( MAINQUERY.DocMONTH = 9, documentRptAmount, 0 ) AS Sept,
                                    IF
                                        ( MAINQUERY.DocMONTH = 10, documentRptAmount, 0 ) AS Oct,
                                    IF
                                        ( MAINQUERY.DocMONTH = 11, documentRptAmount, 0 ) AS Nov,
                                    IF
                                        ( MAINQUERY.DocMONTH = 12, documentRptAmount, 0 ) AS Dece ,
                                        ' . $currencyClm . ' as Total
                                    FROM
                                        (
                                    SELECT
                                        erp_generalledger.companySystemID,
                                        erp_generalledger.companyID,
                                        erp_generalledger.documentCode,
                                        erp_generalledger.chartOfAccountSystemID,
                                        erp_generalledger.glCode,
                                        chartofaccounts.AccountDescription,
                                        erp_generalledger.glAccountType,
                                        MONTH ( erp_generalledger.documentDate ) AS DocMONTH,
                                        YEAR ( erp_generalledger.documentDate ) AS DocYEAR,
                                        localCurrency.CurrencyCode AS localCurrencyCode,
                                        erp_generalledger.documentLocalCurrencyID,
                                        round(erp_generalledger.documentLocalAmount,0) as documentLocalAmount,
                                        rptCurrency.CurrencyCode AS rptCurrencyCode,
                                        erp_generalledger.documentRptCurrencyID,
                                        round(erp_generalledger.documentRptAmount,0) as documentRptAmount,
                                        companymaster.CompanyName
                                    FROM
                                        erp_generalledger
                                        INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                                        LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
                                        LEFT JOIN currencymaster AS localCurrency ON erp_generalledger.documentLocalCurrencyID = localCurrency.currencyID
                                        LEFT JOIN currencymaster AS rptCurrency ON erp_generalledger.documentRptCurrencyID = rptCurrency.currencyID 
                                    WHERE
                                        erp_generalledger.documentSystemID = 4 -- hard code as 4
                                        AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                                        AND chartofaccounts.relatedPartyYN = 0
                                        AND (erp_generalledger.supplierCodeSystem IS NULL 
                                        OR erp_generalledger.supplierCodeSystem = 0) -- hard code filers
                                        AND YEAR ( erp_generalledger.documentDate ) = "' . $year . '" 
                                        AND erp_generalledger.documentTransAmount > 0 -- hard code this filter
                                        ) AS MAINQUERY 
                                        ) AS directPaymentsSummary 
                                    GROUP BY
                                        directPaymentsSummary.companySystemID,
                                    directPaymentsSummary.chartOfAccountSystemID';


            $finalQry = 'SELECT * FROM (' . $bySupplierQry . ' UNION ALL ' . $directQry . ') as main ORDER BY Total DESC';

            $output = \DB::select($finalQry);

        } else if ($reportTypeID == 'APLWS') {
            $fromDate = new Carbon($request->fromDate);
            $fromDate = $fromDate->format('Y-m-d');

            $toDate = new Carbon($request->toDate);
            $toDate = $toDate->format('Y-m-d');

            $qry = 'SELECT
                    erp_paysupplierinvoicemaster.PayMasterAutoId,
                    currTrans.CurrencyCode as documentTransCurrency,
                    currTrans.DecimalPlaces as documentTransDecimalPlaces,
                    currLocal.CurrencyCode as documentLocalCurrency,
                    currLocal.DecimalPlaces as documentLocalDecimalPlaces,
                    currRpt.CurrencyCode as documentRptCurrency,
                    currRpt.DecimalPlaces as documentRptDecimalPlaces,
                    erp_paysupplierinvoicemaster.companySystemID,
                    erp_paysupplierinvoicemaster.companyID,
                    companymaster.CompanyName,
                    erp_paysupplierinvoicemaster.BPVcode,
                    erp_paysupplierinvoicemaster.BPVdate,
                    erp_paysupplierinvoicemaster.confirmedDate,
                    If(suppliermaster.primarySupplierCode Is Null,erp_paysupplierinvoicemaster.directPaymentPayee,suppliermaster.supplierName) as PayeeName,
                    suppliermaster.creditPeriod,
                    erp_paysupplierinvoicemaster.BPVchequeNo,
                    If(BPVchequeDate Is Null,Null,BPVchequeDate) as ChequeDate,
                    erp_paysupplierinvoicemaster.chequePrintedByEmpName,
                    If(chequePrintedDateTime Is Null,Null,chequePrintedDateTime) as chequePrintedDate,
                    payAmountSuppTrans,
                    payAmountCompLocal,
                    payAmountCompRpt,
                    chequeSentToTreasury,
                    chequePaymentYN,
                    If(chequePaymentYN=-1,"Yes","No") as chequePayment,
                    approved,
                    If(erp_paysupplierinvoicemaster.approved=-1,"Fully Approved",If(approved=0 And erp_paysupplierinvoicemaster.RollLevForApp_curr=1,"1st Level Approval Pending",If(erp_paysupplierinvoicemaster.approved=0 And erp_paysupplierinvoicemaster.RollLevForApp_curr=2,"2nd Level Approval Pending",""))) as ApprovalStatus
                FROM
                    erp_paysupplierinvoicemaster
                    LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem=erp_paysupplierinvoicemaster.BPVsupplierID
                    INNER JOIN companymaster ON erp_paysupplierinvoicemaster.companySystemID = companymaster.companySystemID
                    LEFT JOIN currencymaster currTrans ON erp_paysupplierinvoicemaster.supplierTransCurrencyID = currTrans.currencyID
                    LEFT JOIN currencymaster currLocal ON erp_paysupplierinvoicemaster.localCurrencyID = currLocal.currencyID
                    LEFT JOIN currencymaster currRpt ON erp_paysupplierinvoicemaster.companyRptCurrencyID = currRpt.currencyID
                    		WHERE 	erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyID) . ')
                    		AND  erp_paysupplierinvoicemaster.BPVdate BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND erp_paysupplierinvoicemaster.confirmedYN=1';
            $output = \DB::select($qry);
        } else {
            $output = array();
        }

        return $output;

    }

    function getSupplierBalanceSummeryQRY($request)
    {
        $asOfDate = new Carbon($request->fromDate);
        //$fromDate = $asOfDate->addDays(1);
        $asOfDate = $asOfDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $suppliers = (array)$request->suppliers;
        $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();

        $controlAccountsSystemID = $request->controlAccountsSystemID;

        $currency = $request->currencyID;
        $currencyQry = '';
        $invoiceAmountQry = '';
        $decimalPlaceQry = '';
        if ($currency == 2) {
            $currencyQry = "localCurrencyDet.CurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( sum(erp_generalledger.documentLocalAmount), localCurrencyDet.DecimalPlaces ),0) AS documentAmount";
            $decimalPlaceQry = "localCurrencyDet.DecimalPlaces AS balanceDecimalPlaces";
        } else {
            $currencyQry = "rptCurrencyDet.CurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( sum(erp_generalledger.documentRptAmount), rptCurrencyDet.DecimalPlaces ),0) AS documentAmount";
            $decimalPlaceQry = "rptCurrencyDet.DecimalPlaces AS balanceDecimalPlaces";
        }

        $query = 'SELECT
                        erp_generalledger.companySystemID,
                        erp_generalledger.companyID,
                        companymaster.CompanyName,
                        erp_generalledger.supplierCodeSystem,
                        suppliermaster.primarySupplierCode AS SupplierCode,
                        suppliermaster.supplierName,
                        erp_generalledger.documentLocalCurrencyID,
                        ' . $invoiceAmountQry . ',
                        ' . $currencyQry . ',
                        ' . $decimalPlaceQry . '
                    FROM
                        erp_generalledger
                    LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                    LEFT JOIN currencymaster AS localCurrencyDet ON localCurrencyDet.currencyID = erp_generalledger.documentLocalCurrencyID
                    LEFT JOIN currencymaster AS rptCurrencyDet ON rptCurrencyDet.currencyID = erp_generalledger.documentRptCurrencyID
                    LEFT JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                    WHERE
                        DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
                    AND erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
                    AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                    AND erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                    GROUP BY
                        erp_generalledger.companySystemID,
                        erp_generalledger.supplierCodeSystem;';

        $output = \DB::select($query);
        //dd(DB::getQueryLog());
        return $output;
    }

    function getSupplierAgingDetailQRY($request)
    {
        $asOfDate = new Carbon($request->fromDate);
        //$asOfDate = $asOfDate->addDays(1);
        $asOfDate = $asOfDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $suppliers = (array)$request->suppliers;
        $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();

        $controlAccountsSystemID = $request->controlAccountsSystemID;

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
                    $agingField .= "if(grandFinal.ageDays > " . $through . ",grandFinal.balanceAmount,0) as `" . $val . "`,";
                } else {
                    $list = explode("-", $val);
                    $agingField .= "if(grandFinal.ageDays >= " . $list[0] . " AND grandFinal.ageDays <= " . $list[1] . ",grandFinal.balanceAmount,0) as `" . $val . "`,";
                }
                $c++;
            }
        }
        $agingField .= "if(grandFinal.ageDays <= 0,grandFinal.balanceAmount,0) as `current`";

        $currencyQry = '';
        $invoiceAmountQry = '';
        $balanceAmountQry = '';
        $unAllocatedAmountQry = '';
        $decimalPlaceQry = '';
        $whereQry = '';
        if ($currency == 1) {
            $currencyQry = "finalAgingDetail.transCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountTrans, finalAgingDetail.documentTransDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountTrans, finalAgingDetail.documentTransDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentTransDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountTrans, finalAgingDetail.documentTransDecimalPlaces )";
            $unAllocatedAmountQry = "if(finalAgingDetail.balanceAmountTrans<0,finalAgingDetail.balanceAmountTrans,0) as unAllocatedAmount";
        } else if ($currency == 2) {
            $currencyQry = "finalAgingDetail.localCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountLocal, finalAgingDetail.documentLocalDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountLocal, finalAgingDetail.documentLocalDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentLocalDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountLocal, finalAgingDetail.documentLocalDecimalPlaces )";
            $unAllocatedAmountQry = "if(finalAgingDetail.balanceAmountLocal<0,finalAgingDetail.balanceAmountLocal,0) as unAllocatedAmount";
        } else {
            $currencyQry = "finalAgingDetail.rptCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountRpt, finalAgingDetail.documentRptDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountRpt, finalAgingDetail.documentRptDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentRptDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountRpt, finalAgingDetail.documentRptDecimalPlaces )";
            $unAllocatedAmountQry = "if(finalAgingDetail.balanceAmountRpt<0,finalAgingDetail.balanceAmountRpt,0) as unAllocatedAmount";
        }

        //DB::enableQueryLog();
        $output = \DB::select('SELECT *,' . $agingField . ' FROM (SELECT
                                finalAgingDetail.companySystemID,
                                finalAgingDetail.companyID,
                                finalAgingDetail.CompanyName,
                                finalAgingDetail.documentSystemID,
                                finalAgingDetail.documentID,
                                finalAgingDetail.documentCode,
                                finalAgingDetail.documentDate,
                                finalAgingDetail.documentNarration,
                                finalAgingDetail.supplierCodeSystem,
                                finalAgingDetail.SupplierCode,
                                finalAgingDetail.suppliername,
                                finalAgingDetail.invoiceNumber,
                                finalAgingDetail.invoiceDate,
                                CURDATE() as runDate,
                                DATEDIFF("' . $asOfDate . '",DATE(finalAgingDetail.documentDate)) as ageDays,
                                ' . $invoiceAmountQry . ',
                                ' . $balanceAmountQry . ',
                                ' . $unAllocatedAmountQry . ',
                                ' . $currencyQry . ',
                                ' . $decimalPlaceQry . ',
                                CONCAT(finalAgingDetail.SupplierCode," - ",finalAgingDetail.suppliername) as concatSupplierName
                            FROM
                            (
                            SELECT
                                MAINQUERY.companySystemID,
                                MAINQUERY.companyID,
                                companymaster.CompanyName,
                                MAINQUERY.documentSystemID,
                                MAINQUERY.documentID,
                                MAINQUERY.documentCode,
                                MAINQUERY.documentDate,
                                MAINQUERY.documentNarration,
                                MAINQUERY.supplierCodeSystem,
                                suppliermaster.primarySupplierCode AS SupplierCode,
                                suppliermaster.suppliername,
                                MAINQUERY.invoiceNumber,
                                MAINQUERY.invoiceDate,
                                transCurrencyDet.CurrencyCode as transCurrencyCode,
                                transCurrencyDet.DecimalPlaces as documentTransDecimalPlaces,
                                MAINQUERY.docTransAmount AS documentAmountTrans,
                                localCurrencyDet.CurrencyCode as localCurrencyCode,
                                localCurrencyDet.DecimalPlaces as documentLocalDecimalPlaces,
                                MAINQUERY.docLocalAmount AS documentAmountLocal,
                                rptCurrencyDet.CurrencyCode as rptCurrencyCode,
                                rptCurrencyDet.DecimalPlaces as documentRptDecimalPlaces,
                                MAINQUERY.docRptAmount AS documentAmountRpt,
                            
                                (MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans) * -1 AS PaidAmountTrans,
                            
                                (MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal) * -1 AS PaidAmountLocal,
                            
                                (MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt) * -1 AS PaidAmountRpt,
                            
                                MAINQUERY.docTransAmount+MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans  as balanceAmountTrans,
                            
                                MAINQUERY.docLocalAmount+MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal  as balanceAmountLocal,
                            
                                MAINQUERY.docRptAmount + MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt AS balanceAmountRpt
                            FROM
                                (
                            SELECT
                                erp_generalledger.companySystemID,
                                erp_generalledger.companyID,
                                erp_generalledger.serviceLineSystemID,
                                erp_generalledger.serviceLineCode,
                                erp_generalledger.documentSystemID,
                                erp_generalledger.documentID,
                                erp_generalledger.documentSystemCode,
                                erp_generalledger.documentCode,
                                erp_generalledger.documentDate,
                                erp_generalledger.chartOfAccountSystemID,
                                erp_generalledger.glCode,
                                erp_generalledger.glAccountType,
                                erp_generalledger.documentNarration,
                                erp_generalledger.clientContractID,
                                erp_generalledger.invoiceNumber,
                                erp_generalledger.invoiceDate,
                                erp_generalledger.supplierCodeSystem,
                                erp_generalledger.documentTransCurrencyID,
                                erp_generalledger.documentTransAmount * - 1 AS docTransAmount,
                                erp_generalledger.documentLocalCurrencyID,
                                erp_generalledger.documentLocalAmount * - 1 AS docLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                erp_generalledger.documentRptAmount * - 1 AS docRptAmount,
                            IF
                                ( paymentVoucherAmount.PaidPaymentVoucherTransAmount IS NULL, 0, paymentVoucherAmount.PaidPaymentVoucherTransAmount ) AS PaidPaymentVoucherTransAmount,
                            IF
                                ( paymentVoucherAmount.PaidPaymentVoucherLocalAmount IS NULL, 0, paymentVoucherAmount.PaidPaymentVoucherLocalAmount ) AS PaidPaymentVoucherLocalAmount,
                            IF
                                ( paymentVoucherAmount.PaidPaymentVoucherRptAmount IS NULL, 0, paymentVoucherAmount.PaidPaymentVoucherRptAmount ) AS PaidPaymentVoucherRptAmount,
                            IF
                                ( InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountTrans IS NULL, 0, InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountTrans ) AS InvoiceMatchedINMatchingAmountTrans,
                            IF
                                ( InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountLocal IS NULL, 0, InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountLocal ) AS InvoiceMatchedINMatchingAmountLocal,
                            IF
                                ( InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountRpt IS NULL, 0, InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountRpt ) AS InvoiceMatchedINMatchingAmountRpt,
                            IF
                                ( InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountTrans IS NULL, 0, InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountTrans ) AS InvoiceMatchedForpaymentAmountTrans,
                            IF
                                ( InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountLocal IS NULL, 0, InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountLocal ) AS InvoiceMatchedForpaymentAmountLocal,
                            IF
                                ( InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountRpt IS NULL, 0, InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountRpt ) AS InvoiceMatchedForpaymentAmountRpt,
                            IF
                                ( debitNoteMatched.debitNoteMatchedAmountTrans IS NULL, 0, debitNoteMatched.debitNoteMatchedAmountTrans ) AS debitNoteMatchedAmountTrans,
                            IF
                                ( debitNoteMatched.debitNoteMatchedAmountLocal IS NULL, 0, debitNoteMatched.debitNoteMatchedAmountLocal ) AS debitNoteMatchedAmountLocal,
                            IF
                                ( debitNoteMatched.debitNoteMatchedAmountRpt IS NULL, 0, debitNoteMatched.debitNoteMatchedAmountRpt ) AS debitNoteMatchedAmountRpt
                            FROM
                                erp_generalledger
                                LEFT JOIN (-- payment voucher matched with invoice or debit note
                            SELECT
                                erp_paysupplierinvoicedetail.companySystemID,
                                erp_paysupplierinvoicedetail.companyID,
                                erp_paysupplierinvoicemaster.documentSystemID,
                                erp_paysupplierinvoicemaster.documentID,
                                erp_paysupplierinvoicemaster.PayMasterAutoId,
                                erp_paysupplierinvoicemaster.BPVcode,
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS PaidPaymentVoucherTransAmount,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS PaidPaymentVoucherLocalAmount,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS PaidPaymentVoucherRptAmount
                            FROM
                                erp_paysupplierinvoicedetail
                                INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.PayMasterAutoId = erp_paysupplierinvoicemaster.PayMasterAutoId
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID = 0
                                AND erp_paysupplierinvoicemaster.approved = -1
                                AND erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND DATE(erp_paysupplierinvoicemaster.postedDate) <= "' . $asOfDate . '"
                            GROUP BY
                                companySystemID,
                                documentSystemID,
                                PayMasterAutoId
                                ) AS paymentVoucherAmount ON paymentVoucherAmount.companySystemID = erp_generalledger.companySystemID
                                AND paymentVoucherAmount.documentSystemID = erp_generalledger.documentSystemID
                                AND paymentVoucherAmount.PayMasterAutoId = erp_generalledger.documentSystemCode
                                LEFT JOIN (-- invoice matched in a matching document
                            SELECT
                                erp_paysupplierinvoicedetail.companySystemID,
                                erp_paysupplierinvoicedetail.companyID,
                                erp_paysupplierinvoicedetail.addedDocumentSystemID,
                                erp_paysupplierinvoicedetail.addedDocumentID,
                                erp_paysupplierinvoicedetail.bookingInvSystemCode,
                                erp_paysupplierinvoicedetail.bookingInvDocCode,
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS InvoiceMatchedINMatchingAmountTrans,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS InvoiceMatchedINMatchingAmountLocal,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS InvoiceMatchedINMatchingAmountRpt
                            FROM
                                erp_paysupplierinvoicedetail
                                INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_paysupplierinvoicedetail.matchingDocID
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID > 0
                                AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '"
                            GROUP BY
                                companySystemID,
                                addedDocumentSystemID,
                                bookingInvSystemCode
                                ) AS InvoiceMatchedINMatching ON InvoiceMatchedINMatching.companySystemID = erp_generalledger.companySystemID
                                AND InvoiceMatchedINMatching.addedDocumentSystemID = erp_generalledger.documentSystemID
                                AND InvoiceMatchedINMatching.bookingInvSystemCode = erp_generalledger.documentSystemCode
                                LEFT JOIN (-- invoice matched in a payment voucher document
                            SELECT
                                erp_paysupplierinvoicedetail.companySystemID,
                                erp_paysupplierinvoicedetail.companyID,
                                erp_paysupplierinvoicedetail.addedDocumentSystemID,
                                erp_paysupplierinvoicedetail.addedDocumentID,
                                erp_paysupplierinvoicedetail.bookingInvSystemCode,
                                erp_paysupplierinvoicedetail.bookingInvDocCode,
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS InvoiceMatchedForpaymentAmountTrans,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS InvoiceMatchedForpaymentAmountLocal,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS InvoiceMatchedForpaymentAmountRpt
                            FROM
                                erp_paysupplierinvoicedetail
                                INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.PayMasterAutoId = erp_paysupplierinvoicemaster.PayMasterAutoId
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID = 0
                                AND erp_paysupplierinvoicemaster.approved = -1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND DATE(erp_paysupplierinvoicemaster.postedDate) <= "' . $asOfDate . '"
                            GROUP BY
                                companySystemID,
                                addedDocumentSystemID,
                                bookingInvSystemCode
                                ) AS InvoiceMatchedForpayment ON InvoiceMatchedForpayment.companySystemID = erp_generalledger.companySystemID
                                AND InvoiceMatchedForpayment.addedDocumentSystemID = erp_generalledger.documentSystemID
                                AND InvoiceMatchedForpayment.bookingInvSystemCode = erp_generalledger.documentSystemCode
                                LEFT JOIN (-- matched debit note
                            SELECT
                                erp_matchdocumentmaster.companySystemID,
                                erp_matchdocumentmaster.companyID,
                                erp_matchdocumentmaster.documentSystemID,
                                erp_matchdocumentmaster.documentID,
                                erp_matchdocumentmaster.PayMasterAutoId,
                                erp_matchdocumentmaster.BPVcode,
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS debitNoteMatchedAmountTrans,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS debitNoteMatchedAmountLocal,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS debitNoteMatchedAmountRpt
                            FROM
                                erp_matchdocumentmaster
                                INNER JOIN erp_paysupplierinvoicedetail ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_paysupplierinvoicedetail.matchingDocID
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID > 0
                                AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '"
                            GROUP BY
                                companySystemID,
                                documentSystemID,
                                PayMasterAutoId
                                ) AS debitNoteMatched ON debitNoteMatched.companySystemID = erp_generalledger.companySystemID
                                AND debitNoteMatched.documentSystemID = erp_generalledger.documentSystemID
                                AND debitNoteMatched.PayMasterAutoId = erp_generalledger.documentSystemCode
                            WHERE
                                DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
                                AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
                                AND erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                ) AS MAINQUERY
                            LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = MAINQUERY.supplierCodeSystem
                            LEFT JOIN companymaster ON MAINQUERY.companySystemID = companymaster.companySystemID
                            LEFT JOIN currencymaster as transCurrencyDet ON transCurrencyDet.currencyID=MAINQUERY.documentTransCurrencyID
                            LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=MAINQUERY.documentLocalCurrencyID
                            LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=MAINQUERY.documentRptCurrencyID) as finalAgingDetail WHERE ' . $whereQry . ' <> 0 ORDER BY documentDate ASC) as grandFinal;');
        //dd(DB::getQueryLog());
        return ['data' => $output, 'aging' => $aging];
    }

    function getSupplierAgingSummaryQRY($request)
    {
        $asOfDate = new Carbon($request->fromDate);
        //$asOfDate = $asOfDate->addDays(1);
        $asOfDate = $asOfDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $suppliers = (array)$request->suppliers;
        $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();

        $controlAccountsSystemID = $request->controlAccountsSystemID;

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
                    $agingField .= "SUM(if(grandFinal.ageDays > " . $through . ",grandFinal.balanceAmount,0)) as `" . $val . "`,";
                } else {
                    $list = explode("-", $val);
                    $agingField .= "SUM(if(grandFinal.ageDays >= " . $list[0] . " AND grandFinal.ageDays <= " . $list[1] . ",grandFinal.balanceAmount,0)) as `" . $val . "`,";
                }
                $c++;
            }
        }
        $agingField .= "SUM(if(grandFinal.ageDays <= 0,grandFinal.balanceAmount,0)) as `current`";

        $currencyQry = '';
        $invoiceAmountQry = '';
        $balanceAmountQry = '';
        $unAllocatedAmountQry = '';
        $decimalPlaceQry = '';
        $whereQry = '';
        if ($currency == 1) {
            $currencyQry = "finalAgingDetail.transCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountTrans, finalAgingDetail.documentTransDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountTrans, finalAgingDetail.documentTransDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentTransDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountTrans, finalAgingDetail.documentTransDecimalPlaces )";
            $unAllocatedAmountQry = "if(finalAgingDetail.balanceAmountTrans<0,finalAgingDetail.balanceAmountTrans,0) as unAllocatedAmount";
        } else if ($currency == 2) {
            $currencyQry = "finalAgingDetail.localCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountLocal, finalAgingDetail.documentLocalDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountLocal, finalAgingDetail.documentLocalDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentLocalDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountLocal, finalAgingDetail.documentLocalDecimalPlaces )";
            $unAllocatedAmountQry = "if(finalAgingDetail.balanceAmountLocal<0,finalAgingDetail.balanceAmountLocal,0) as unAllocatedAmount";
        } else {
            $currencyQry = "finalAgingDetail.rptCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountRpt, finalAgingDetail.documentRptDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountRpt, finalAgingDetail.documentRptDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentRptDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountRpt, finalAgingDetail.documentRptDecimalPlaces )";
            $unAllocatedAmountQry = "if(finalAgingDetail.balanceAmountRpt<0,finalAgingDetail.balanceAmountRpt,0) as unAllocatedAmount";
        }
        //DB::enableQueryLog();
        $output = \DB::select('SELECT *,SUM(grandFinal.unAllocatedAmount) as unAllocatedAmount,' . $agingField . ' FROM (SELECT
                                finalAgingDetail.companySystemID,
                                finalAgingDetail.companyID,
                                finalAgingDetail.CompanyName,
                                finalAgingDetail.documentSystemID,
                                finalAgingDetail.documentID,
                                finalAgingDetail.documentCode,
                                finalAgingDetail.documentDate,
                                finalAgingDetail.documentNarration,
                                finalAgingDetail.supplierCodeSystem,
                                finalAgingDetail.SupplierCode,
                                finalAgingDetail.suppliername,
                                finalAgingDetail.creditPeriod,
                                finalAgingDetail.invoiceNumber,
                                finalAgingDetail.invoiceDate,
                                CURDATE() as runDate,
                                DATEDIFF("' . $asOfDate . '",DATE(finalAgingDetail.documentDate)) as ageDays,
                                ' . $invoiceAmountQry . ',
                                ' . $balanceAmountQry . ',
                                ' . $unAllocatedAmountQry . ',
                                ' . $currencyQry . ',
                                ' . $decimalPlaceQry . ',
                                CONCAT(finalAgingDetail.SupplierCode," - ",finalAgingDetail.suppliername) as concatSupplierName,
                                CONCAT(finalAgingDetail.companyID," - ",finalAgingDetail.CompanyName) as concatCompanyName
                            FROM
                            (
                            SELECT
                                MAINQUERY.companySystemID,
                                MAINQUERY.companyID,
                                companymaster.CompanyName,
                                MAINQUERY.documentSystemID,
                                MAINQUERY.documentID,
                                MAINQUERY.documentCode,
                                MAINQUERY.documentDate,
                                MAINQUERY.documentNarration,
                                MAINQUERY.supplierCodeSystem,
                                suppliermaster.primarySupplierCode AS SupplierCode,
                                suppliermaster.suppliername,
                                suppliermaster.creditPeriod,
                                MAINQUERY.invoiceNumber,
                                MAINQUERY.invoiceDate,
                                transCurrencyDet.CurrencyCode as transCurrencyCode,
                                transCurrencyDet.DecimalPlaces as documentTransDecimalPlaces,
                                MAINQUERY.docTransAmount AS documentAmountTrans,
                                localCurrencyDet.CurrencyCode as localCurrencyCode,
                                localCurrencyDet.DecimalPlaces as documentLocalDecimalPlaces,
                                MAINQUERY.docLocalAmount AS documentAmountLocal,
                                rptCurrencyDet.CurrencyCode as rptCurrencyCode,
                                rptCurrencyDet.DecimalPlaces as documentRptDecimalPlaces,
                                MAINQUERY.docRptAmount AS documentAmountRpt,
                            
                                (MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans) * -1 AS PaidAmountTrans,
                            
                                (MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal) * -1 AS PaidAmountLocal,
                            
                                (MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt) * -1 AS PaidAmountRpt,
                            
                                MAINQUERY.docTransAmount+MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans  as balanceAmountTrans,
                            
                                MAINQUERY.docLocalAmount+MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal  as balanceAmountLocal,
                            
                                MAINQUERY.docRptAmount + MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt AS balanceAmountRpt
                            FROM
                                (
                            SELECT
                                erp_generalledger.companySystemID,
                                erp_generalledger.companyID,
                                erp_generalledger.serviceLineSystemID,
                                erp_generalledger.serviceLineCode,
                                erp_generalledger.documentSystemID,
                                erp_generalledger.documentID,
                                erp_generalledger.documentSystemCode,
                                erp_generalledger.documentCode,
                                erp_generalledger.documentDate,
                                erp_generalledger.chartOfAccountSystemID,
                                erp_generalledger.glCode,
                                erp_generalledger.glAccountType,
                                erp_generalledger.documentNarration,
                                erp_generalledger.clientContractID,
                                erp_generalledger.invoiceNumber,
                                erp_generalledger.invoiceDate,
                                erp_generalledger.supplierCodeSystem,
                                erp_generalledger.documentTransCurrencyID,
                                erp_generalledger.documentTransAmount * - 1 AS docTransAmount,
                                erp_generalledger.documentLocalCurrencyID,
                                erp_generalledger.documentLocalAmount * - 1 AS docLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                erp_generalledger.documentRptAmount * - 1 AS docRptAmount,
                            IF
                                ( paymentVoucherAmount.PaidPaymentVoucherTransAmount IS NULL, 0, paymentVoucherAmount.PaidPaymentVoucherTransAmount ) AS PaidPaymentVoucherTransAmount,
                            IF
                                ( paymentVoucherAmount.PaidPaymentVoucherLocalAmount IS NULL, 0, paymentVoucherAmount.PaidPaymentVoucherLocalAmount ) AS PaidPaymentVoucherLocalAmount,
                            IF
                                ( paymentVoucherAmount.PaidPaymentVoucherRptAmount IS NULL, 0, paymentVoucherAmount.PaidPaymentVoucherRptAmount ) AS PaidPaymentVoucherRptAmount,
                            IF
                                ( InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountTrans IS NULL, 0, InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountTrans ) AS InvoiceMatchedINMatchingAmountTrans,
                            IF
                                ( InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountLocal IS NULL, 0, InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountLocal ) AS InvoiceMatchedINMatchingAmountLocal,
                            IF
                                ( InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountRpt IS NULL, 0, InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountRpt ) AS InvoiceMatchedINMatchingAmountRpt,
                            IF
                                ( InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountTrans IS NULL, 0, InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountTrans ) AS InvoiceMatchedForpaymentAmountTrans,
                            IF
                                ( InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountLocal IS NULL, 0, InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountLocal ) AS InvoiceMatchedForpaymentAmountLocal,
                            IF
                                ( InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountRpt IS NULL, 0, InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountRpt ) AS InvoiceMatchedForpaymentAmountRpt,
                            IF
                                ( debitNoteMatched.debitNoteMatchedAmountTrans IS NULL, 0, debitNoteMatched.debitNoteMatchedAmountTrans ) AS debitNoteMatchedAmountTrans,
                            IF
                                ( debitNoteMatched.debitNoteMatchedAmountLocal IS NULL, 0, debitNoteMatched.debitNoteMatchedAmountLocal ) AS debitNoteMatchedAmountLocal,
                            IF
                                ( debitNoteMatched.debitNoteMatchedAmountRpt IS NULL, 0, debitNoteMatched.debitNoteMatchedAmountRpt ) AS debitNoteMatchedAmountRpt
                            FROM
                                erp_generalledger
                                LEFT JOIN (-- payment voucher matched with invoice or debit note
                            SELECT
                                erp_paysupplierinvoicedetail.companySystemID,
                                erp_paysupplierinvoicedetail.companyID,
                                erp_paysupplierinvoicemaster.documentSystemID,
                                erp_paysupplierinvoicemaster.documentID,
                                erp_paysupplierinvoicemaster.PayMasterAutoId,
                                erp_paysupplierinvoicemaster.BPVcode,
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS PaidPaymentVoucherTransAmount,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS PaidPaymentVoucherLocalAmount,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS PaidPaymentVoucherRptAmount
                            FROM
                                erp_paysupplierinvoicedetail
                                INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.PayMasterAutoId = erp_paysupplierinvoicemaster.PayMasterAutoId
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID = 0
                                AND erp_paysupplierinvoicemaster.approved = -1
                                AND erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND DATE(erp_paysupplierinvoicemaster.postedDate) <= "' . $asOfDate . '"
                            GROUP BY
                                companySystemID,
                                documentSystemID,
                                PayMasterAutoId
                                ) AS paymentVoucherAmount ON paymentVoucherAmount.companySystemID = erp_generalledger.companySystemID
                                AND paymentVoucherAmount.documentSystemID = erp_generalledger.documentSystemID
                                AND paymentVoucherAmount.PayMasterAutoId = erp_generalledger.documentSystemCode
                                LEFT JOIN (-- invoice matched in a matching document
                            SELECT
                                erp_paysupplierinvoicedetail.companySystemID,
                                erp_paysupplierinvoicedetail.companyID,
                                erp_paysupplierinvoicedetail.addedDocumentSystemID,
                                erp_paysupplierinvoicedetail.addedDocumentID,
                                erp_paysupplierinvoicedetail.bookingInvSystemCode,
                                erp_paysupplierinvoicedetail.bookingInvDocCode,
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS InvoiceMatchedINMatchingAmountTrans,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS InvoiceMatchedINMatchingAmountLocal,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS InvoiceMatchedINMatchingAmountRpt
                            FROM
                                erp_paysupplierinvoicedetail
                                INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_paysupplierinvoicedetail.matchingDocID
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID > 0
                                AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '"
                            GROUP BY
                                companySystemID,
                                addedDocumentSystemID,
                                bookingInvSystemCode
                                ) AS InvoiceMatchedINMatching ON InvoiceMatchedINMatching.companySystemID = erp_generalledger.companySystemID
                                AND InvoiceMatchedINMatching.addedDocumentSystemID = erp_generalledger.documentSystemID
                                AND InvoiceMatchedINMatching.bookingInvSystemCode = erp_generalledger.documentSystemCode
                                LEFT JOIN (-- invoice matched in a payment voucher document
                            SELECT
                                erp_paysupplierinvoicedetail.companySystemID,
                                erp_paysupplierinvoicedetail.companyID,
                                erp_paysupplierinvoicedetail.addedDocumentSystemID,
                                erp_paysupplierinvoicedetail.addedDocumentID,
                                erp_paysupplierinvoicedetail.bookingInvSystemCode,
                                erp_paysupplierinvoicedetail.bookingInvDocCode,
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS InvoiceMatchedForpaymentAmountTrans,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS InvoiceMatchedForpaymentAmountLocal,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS InvoiceMatchedForpaymentAmountRpt
                            FROM
                                erp_paysupplierinvoicedetail
                                INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.PayMasterAutoId = erp_paysupplierinvoicemaster.PayMasterAutoId
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID = 0
                                AND erp_paysupplierinvoicemaster.approved = -1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND DATE(erp_paysupplierinvoicemaster.postedDate) <= "' . $asOfDate . '"
                            GROUP BY
                                companySystemID,
                                addedDocumentSystemID,
                                bookingInvSystemCode
                                ) AS InvoiceMatchedForpayment ON InvoiceMatchedForpayment.companySystemID = erp_generalledger.companySystemID
                                AND InvoiceMatchedForpayment.addedDocumentSystemID = erp_generalledger.documentSystemID
                                AND InvoiceMatchedForpayment.bookingInvSystemCode = erp_generalledger.documentSystemCode
                                LEFT JOIN (-- matched debit note
                            SELECT
                                erp_matchdocumentmaster.companySystemID,
                                erp_matchdocumentmaster.companyID,
                                erp_matchdocumentmaster.documentSystemID,
                                erp_matchdocumentmaster.documentID,
                                erp_matchdocumentmaster.PayMasterAutoId,
                                erp_matchdocumentmaster.BPVcode,
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS debitNoteMatchedAmountTrans,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS debitNoteMatchedAmountLocal,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS debitNoteMatchedAmountRpt
                            FROM
                                erp_matchdocumentmaster
                                INNER JOIN erp_paysupplierinvoicedetail ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_paysupplierinvoicedetail.matchingDocID
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID > 0
                                AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '"
                            GROUP BY
                                companySystemID,
                                documentSystemID,
                                PayMasterAutoId
                                ) AS debitNoteMatched ON debitNoteMatched.companySystemID = erp_generalledger.companySystemID
                                AND debitNoteMatched.documentSystemID = erp_generalledger.documentSystemID
                                AND debitNoteMatched.PayMasterAutoId = erp_generalledger.documentSystemCode
                            WHERE
                                DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
                                AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
                                AND erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                ) AS MAINQUERY
                            LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = MAINQUERY.supplierCodeSystem
                            LEFT JOIN companymaster ON MAINQUERY.companySystemID = companymaster.companySystemID
                            LEFT JOIN currencymaster as transCurrencyDet ON transCurrencyDet.currencyID=MAINQUERY.documentTransCurrencyID
                            LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=MAINQUERY.documentLocalCurrencyID
                            LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=MAINQUERY.documentRptCurrencyID) as finalAgingDetail WHERE ' . $whereQry . ' <> 0 ORDER BY documentDate ASC) as grandFinal GROUP BY supplierCodeSystem,companyID ORDER BY suppliername;');
        //dd(DB::getQueryLog());
        return ['data' => $output, 'aging' => $aging];
    }

    function getSupplierAgingDetailAdvanceQRY($request)
    {
        $asOfDate = new Carbon($request->fromDate);
        //$asOfDate = $asOfDate->addDays(1);
        $asOfDate = $asOfDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $suppliers = (array)$request->suppliers;
        $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();

        $controlAccountsSystemID = $request->controlAccountsSystemID;

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
                    $agingField .= "if(grandFinal.ageDays > " . $through . ",grandFinal.balanceAmount,0) as `" . $val . "`,";
                } else {
                    $list = explode("-", $val);
                    $agingField .= "if(grandFinal.ageDays >= " . $list[0] . " AND grandFinal.ageDays <= " . $list[1] . ",grandFinal.balanceAmount,0) as `" . $val . "`,";
                }
                $c++;
            }
        }
        $agingField .= "if(grandFinal.ageDays <= 0,grandFinal.balanceAmount,0) as `current`";

        $currencyQry = '';
        $invoiceAmountQry = '';
        $balanceAmountQry = '';
        $decimalPlaceQry = '';
        $whereQry = '';
        if ($currency == 1) {
            $currencyQry = "finalAgingDetail.transCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountTrans, finalAgingDetail.documentTransDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountTrans, finalAgingDetail.documentTransDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentTransDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountTrans, finalAgingDetail.documentTransDecimalPlaces )";
        } else if ($currency == 2) {
            $currencyQry = "finalAgingDetail.localCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountLocal, finalAgingDetail.documentLocalDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountLocal, finalAgingDetail.documentLocalDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentLocalDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountLocal, finalAgingDetail.documentLocalDecimalPlaces )";
        } else {
            $currencyQry = "finalAgingDetail.rptCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountRpt, finalAgingDetail.documentRptDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountRpt, finalAgingDetail.documentRptDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentRptDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountRpt, finalAgingDetail.documentRptDecimalPlaces )";
        }

        //DB::enableQueryLog();
        $output = \DB::select('SELECT *,' . $agingField . ' FROM (SELECT
                                finalAgingDetail.companySystemID,
                                finalAgingDetail.companyID,
                                finalAgingDetail.CompanyName,
                                finalAgingDetail.documentSystemID,
                                finalAgingDetail.documentID,
                                finalAgingDetail.documentCode,
                                finalAgingDetail.documentDate,
                                finalAgingDetail.documentNarration,
                                finalAgingDetail.supplierCodeSystem,
                                finalAgingDetail.SupplierCode,
                                finalAgingDetail.suppliername,
                                finalAgingDetail.invoiceNumber,
                                finalAgingDetail.invoiceDate,
                                CURDATE() as runDate,
                                DATEDIFF("' . $asOfDate . '",DATE(finalAgingDetail.documentDate)) as ageDays,
                                ' . $invoiceAmountQry . ',
                                ' . $balanceAmountQry . ',
                                ' . $currencyQry . ',
                                ' . $decimalPlaceQry . ',
                                CONCAT(finalAgingDetail.SupplierCode," - ",finalAgingDetail.suppliername) as concatSupplierName
                            FROM
                            (
                            SELECT
                                MAINQUERY.companySystemID,
                                MAINQUERY.companyID,
                                companymaster.CompanyName,
                                MAINQUERY.documentSystemID,
                                MAINQUERY.documentID,
                                MAINQUERY.documentCode,
                                MAINQUERY.documentDate,
                                MAINQUERY.documentNarration,
                                MAINQUERY.supplierCodeSystem,
                                suppliermaster.primarySupplierCode AS SupplierCode,
                                suppliermaster.suppliername,
                                MAINQUERY.invoiceNumber,
                                MAINQUERY.invoiceDate,
                                transCurrencyDet.CurrencyCode as transCurrencyCode,
                                transCurrencyDet.DecimalPlaces as documentTransDecimalPlaces,
                                MAINQUERY.docTransAmount AS documentAmountTrans,
                                localCurrencyDet.CurrencyCode as localCurrencyCode,
                                localCurrencyDet.DecimalPlaces as documentLocalDecimalPlaces,
                                MAINQUERY.docLocalAmount AS documentAmountLocal,
                                rptCurrencyDet.CurrencyCode as rptCurrencyCode,
                                rptCurrencyDet.DecimalPlaces as documentRptDecimalPlaces,
                                MAINQUERY.docRptAmount AS documentAmountRpt,
                            
                                (MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans) * -1 AS PaidAmountTrans,
                            
                                (MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal) * -1 AS PaidAmountLocal,
                            
                                (MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt) * -1 AS PaidAmountRpt,
                            
                                MAINQUERY.docTransAmount+MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans  as balanceAmountTrans,
                            
                                MAINQUERY.docLocalAmount+MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal  as balanceAmountLocal,
                            
                                MAINQUERY.docRptAmount + MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt AS balanceAmountRpt
                            FROM
                                (
                            SELECT
                                erp_generalledger.companySystemID,
                                erp_generalledger.companyID,
                                erp_generalledger.serviceLineSystemID,
                                erp_generalledger.serviceLineCode,
                                erp_generalledger.documentSystemID,
                                erp_generalledger.documentID,
                                erp_generalledger.documentSystemCode,
                                erp_generalledger.documentCode,
                                erp_generalledger.documentDate,
                                erp_generalledger.chartOfAccountSystemID,
                                erp_generalledger.glCode,
                                erp_generalledger.glAccountType,
                                erp_generalledger.documentNarration,
                                erp_generalledger.clientContractID,
                                erp_generalledger.invoiceNumber,
                                erp_generalledger.invoiceDate,
                                erp_generalledger.supplierCodeSystem,
                                erp_generalledger.documentTransCurrencyID,
                                erp_generalledger.documentTransAmount * - 1 AS docTransAmount,
                                erp_generalledger.documentLocalCurrencyID,
                                erp_generalledger.documentLocalAmount * - 1 AS docLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                erp_generalledger.documentRptAmount * - 1 AS docRptAmount,
                            IF
                                ( paymentVoucherAmount.PaidPaymentVoucherTransAmount IS NULL, 0, paymentVoucherAmount.PaidPaymentVoucherTransAmount ) AS PaidPaymentVoucherTransAmount,
                            IF
                                ( paymentVoucherAmount.PaidPaymentVoucherLocalAmount IS NULL, 0, paymentVoucherAmount.PaidPaymentVoucherLocalAmount ) AS PaidPaymentVoucherLocalAmount,
                            IF
                                ( paymentVoucherAmount.PaidPaymentVoucherRptAmount IS NULL, 0, paymentVoucherAmount.PaidPaymentVoucherRptAmount ) AS PaidPaymentVoucherRptAmount,
                            IF
                                ( InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountTrans IS NULL, 0, InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountTrans ) AS InvoiceMatchedINMatchingAmountTrans,
                            IF
                                ( InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountLocal IS NULL, 0, InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountLocal ) AS InvoiceMatchedINMatchingAmountLocal,
                            IF
                                ( InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountRpt IS NULL, 0, InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountRpt ) AS InvoiceMatchedINMatchingAmountRpt,
                            IF
                                ( InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountTrans IS NULL, 0, InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountTrans ) AS InvoiceMatchedForpaymentAmountTrans,
                            IF
                                ( InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountLocal IS NULL, 0, InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountLocal ) AS InvoiceMatchedForpaymentAmountLocal,
                            IF
                                ( InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountRpt IS NULL, 0, InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountRpt ) AS InvoiceMatchedForpaymentAmountRpt,
                            IF
                                ( debitNoteMatched.debitNoteMatchedAmountTrans IS NULL, 0, debitNoteMatched.debitNoteMatchedAmountTrans ) AS debitNoteMatchedAmountTrans,
                            IF
                                ( debitNoteMatched.debitNoteMatchedAmountLocal IS NULL, 0, debitNoteMatched.debitNoteMatchedAmountLocal ) AS debitNoteMatchedAmountLocal,
                            IF
                                ( debitNoteMatched.debitNoteMatchedAmountRpt IS NULL, 0, debitNoteMatched.debitNoteMatchedAmountRpt ) AS debitNoteMatchedAmountRpt
                            FROM
                                erp_generalledger
                                LEFT JOIN (-- payment voucher matched with invoice or debit note
                            SELECT
                                erp_paysupplierinvoicedetail.companySystemID,
                                erp_paysupplierinvoicedetail.companyID,
                                erp_paysupplierinvoicemaster.documentSystemID,
                                erp_paysupplierinvoicemaster.documentID,
                                erp_paysupplierinvoicemaster.PayMasterAutoId,
                                erp_paysupplierinvoicemaster.BPVcode,
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS PaidPaymentVoucherTransAmount,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS PaidPaymentVoucherLocalAmount,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS PaidPaymentVoucherRptAmount
                            FROM
                                erp_paysupplierinvoicedetail
                                INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.PayMasterAutoId = erp_paysupplierinvoicemaster.PayMasterAutoId
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID = 0
                                AND erp_paysupplierinvoicemaster.approved = -1
                                AND erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND DATE(erp_paysupplierinvoicemaster.postedDate) <= "' . $asOfDate . '"
                            GROUP BY
                                companySystemID,
                                documentSystemID,
                                PayMasterAutoId
                                ) AS paymentVoucherAmount ON paymentVoucherAmount.companySystemID = erp_generalledger.companySystemID
                                AND paymentVoucherAmount.documentSystemID = erp_generalledger.documentSystemID
                                AND paymentVoucherAmount.PayMasterAutoId = erp_generalledger.documentSystemCode
                                LEFT JOIN (-- invoice matched in a matching document
                            SELECT
                                erp_paysupplierinvoicedetail.companySystemID,
                                erp_paysupplierinvoicedetail.companyID,
                                erp_paysupplierinvoicedetail.addedDocumentSystemID,
                                erp_paysupplierinvoicedetail.addedDocumentID,
                                erp_paysupplierinvoicedetail.bookingInvSystemCode,
                                erp_paysupplierinvoicedetail.bookingInvDocCode,
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS InvoiceMatchedINMatchingAmountTrans,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS InvoiceMatchedINMatchingAmountLocal,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS InvoiceMatchedINMatchingAmountRpt
                            FROM
                                erp_paysupplierinvoicedetail
                                INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_paysupplierinvoicedetail.matchingDocID
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID > 0
                                AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '"
                            GROUP BY
                                companySystemID,
                                addedDocumentSystemID,
                                bookingInvSystemCode
                                ) AS InvoiceMatchedINMatching ON InvoiceMatchedINMatching.companySystemID = erp_generalledger.companySystemID
                                AND InvoiceMatchedINMatching.addedDocumentSystemID = erp_generalledger.documentSystemID
                                AND InvoiceMatchedINMatching.bookingInvSystemCode = erp_generalledger.documentSystemCode
                                LEFT JOIN (-- invoice matched in a payment voucher document
                            SELECT
                                erp_paysupplierinvoicedetail.companySystemID,
                                erp_paysupplierinvoicedetail.companyID,
                                erp_paysupplierinvoicedetail.addedDocumentSystemID,
                                erp_paysupplierinvoicedetail.addedDocumentID,
                                erp_paysupplierinvoicedetail.bookingInvSystemCode,
                                erp_paysupplierinvoicedetail.bookingInvDocCode,
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS InvoiceMatchedForpaymentAmountTrans,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS InvoiceMatchedForpaymentAmountLocal,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS InvoiceMatchedForpaymentAmountRpt
                            FROM
                                erp_paysupplierinvoicedetail
                                INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.PayMasterAutoId = erp_paysupplierinvoicemaster.PayMasterAutoId
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID = 0
                                AND erp_paysupplierinvoicemaster.approved = -1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND DATE(erp_paysupplierinvoicemaster.postedDate) <= "' . $asOfDate . '"
                            GROUP BY
                                companySystemID,
                                addedDocumentSystemID,
                                bookingInvSystemCode
                                ) AS InvoiceMatchedForpayment ON InvoiceMatchedForpayment.companySystemID = erp_generalledger.companySystemID
                                AND InvoiceMatchedForpayment.addedDocumentSystemID = erp_generalledger.documentSystemID
                                AND InvoiceMatchedForpayment.bookingInvSystemCode = erp_generalledger.documentSystemCode
                                LEFT JOIN (-- matched debit note
                            SELECT
                                erp_matchdocumentmaster.companySystemID,
                                erp_matchdocumentmaster.companyID,
                                erp_matchdocumentmaster.documentSystemID,
                                erp_matchdocumentmaster.documentID,
                                erp_matchdocumentmaster.PayMasterAutoId,
                                erp_matchdocumentmaster.BPVcode,
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS debitNoteMatchedAmountTrans,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS debitNoteMatchedAmountLocal,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS debitNoteMatchedAmountRpt
                            FROM
                                erp_matchdocumentmaster
                                INNER JOIN erp_paysupplierinvoicedetail ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_paysupplierinvoicedetail.matchingDocID
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID > 0
                                AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '"
                            GROUP BY
                                companySystemID,
                                documentSystemID,
                                PayMasterAutoId
                                ) AS debitNoteMatched ON debitNoteMatched.companySystemID = erp_generalledger.companySystemID
                                AND debitNoteMatched.documentSystemID = erp_generalledger.documentSystemID
                                AND debitNoteMatched.PayMasterAutoId = erp_generalledger.documentSystemCode
                            WHERE
                                DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
                                AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
                                AND erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                ) AS MAINQUERY
                            LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = MAINQUERY.supplierCodeSystem
                            LEFT JOIN companymaster ON MAINQUERY.companySystemID = companymaster.companySystemID
                            LEFT JOIN currencymaster as transCurrencyDet ON transCurrencyDet.currencyID=MAINQUERY.documentTransCurrencyID
                            LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=MAINQUERY.documentLocalCurrencyID
                            LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=MAINQUERY.documentRptCurrencyID) as finalAgingDetail WHERE ' . $whereQry . ' < 0 ORDER BY documentDate ASC) as grandFinal;');
        //dd(DB::getQueryLog());
        return ['data' => $output, 'aging' => $aging];
    }

    function getSupplierAgingSummaryAdvanceQRY($request)
    {
        $asOfDate = new Carbon($request->fromDate);
        //$asOfDate = $asOfDate->addDays(1);
        $asOfDate = $asOfDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $suppliers = (array)$request->suppliers;
        $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();

        $controlAccountsSystemID = $request->controlAccountsSystemID;

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
                    $agingField .= "SUM(if(grandFinal.ageDays > " . $through . ",grandFinal.balanceAmount,0)) as `" . $val . "`,";
                } else {
                    $list = explode("-", $val);
                    $agingField .= "SUM(if(grandFinal.ageDays >= " . $list[0] . " AND grandFinal.ageDays <= " . $list[1] . ",grandFinal.balanceAmount,0)) as `" . $val . "`,";
                }
                $c++;
            }
        }
        $agingField .= "SUM(if(grandFinal.ageDays <= 0,grandFinal.balanceAmount,0)) as `current`";

        $currencyQry = '';
        $invoiceAmountQry = '';
        $balanceAmountQry = '';
        $decimalPlaceQry = '';
        $whereQry = '';
        if ($currency == 1) {
            $currencyQry = "finalAgingDetail.transCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountTrans, finalAgingDetail.documentTransDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountTrans, finalAgingDetail.documentTransDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentTransDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountTrans, finalAgingDetail.documentTransDecimalPlaces )";
        } else if ($currency == 2) {
            $currencyQry = "finalAgingDetail.localCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountLocal, finalAgingDetail.documentLocalDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountLocal, finalAgingDetail.documentLocalDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentLocalDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountLocal, finalAgingDetail.documentLocalDecimalPlaces )";
        } else {
            $currencyQry = "finalAgingDetail.rptCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountRpt, finalAgingDetail.documentRptDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountRpt, finalAgingDetail.documentRptDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentRptDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountRpt, finalAgingDetail.documentRptDecimalPlaces )";
        }
        //DB::enableQueryLog();
        $output = \DB::select('SELECT *, ' . $agingField . ' FROM (SELECT
                                finalAgingDetail.companySystemID,
                                finalAgingDetail.companyID,
                                finalAgingDetail.CompanyName,
                                finalAgingDetail.documentSystemID,
                                finalAgingDetail.documentID,
                                finalAgingDetail.documentCode,
                                finalAgingDetail.documentDate,
                                finalAgingDetail.documentNarration,
                                finalAgingDetail.supplierCodeSystem,
                                finalAgingDetail.SupplierCode,
                                finalAgingDetail.suppliername,
                                finalAgingDetail.creditPeriod,
                                finalAgingDetail.invoiceNumber,
                                finalAgingDetail.invoiceDate,
                                CURDATE() as runDate,
                                DATEDIFF("' . $asOfDate . '",DATE(finalAgingDetail.documentDate)) as ageDays,
                                ' . $invoiceAmountQry . ',
                                ' . $balanceAmountQry . ',
                                ' . $currencyQry . ',
                                ' . $decimalPlaceQry . ',
                                CONCAT(finalAgingDetail.SupplierCode," - ",finalAgingDetail.suppliername) as concatSupplierName,
                                CONCAT(finalAgingDetail.companyID," - ",finalAgingDetail.CompanyName) as concatCompanyName
                            FROM
                            (
                            SELECT
                                MAINQUERY.companySystemID,
                                MAINQUERY.companyID,
                                companymaster.CompanyName,
                                MAINQUERY.documentSystemID,
                                MAINQUERY.documentID,
                                MAINQUERY.documentCode,
                                MAINQUERY.documentDate,
                                MAINQUERY.documentNarration,
                                MAINQUERY.supplierCodeSystem,
                                suppliermaster.primarySupplierCode AS SupplierCode,
                                suppliermaster.suppliername,
                                suppliermaster.creditPeriod,
                                MAINQUERY.invoiceNumber,
                                MAINQUERY.invoiceDate,
                                transCurrencyDet.CurrencyCode as transCurrencyCode,
                                transCurrencyDet.DecimalPlaces as documentTransDecimalPlaces,
                                MAINQUERY.docTransAmount AS documentAmountTrans,
                                localCurrencyDet.CurrencyCode as localCurrencyCode,
                                localCurrencyDet.DecimalPlaces as documentLocalDecimalPlaces,
                                MAINQUERY.docLocalAmount AS documentAmountLocal,
                                rptCurrencyDet.CurrencyCode as rptCurrencyCode,
                                rptCurrencyDet.DecimalPlaces as documentRptDecimalPlaces,
                                MAINQUERY.docRptAmount AS documentAmountRpt,
                            
                                (MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans) * -1 AS PaidAmountTrans,
                            
                                (MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal) * -1 AS PaidAmountLocal,
                            
                                (MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt) * -1 AS PaidAmountRpt,
                            
                                MAINQUERY.docTransAmount+MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans  as balanceAmountTrans,
                            
                                MAINQUERY.docLocalAmount+MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal  as balanceAmountLocal,
                            
                                MAINQUERY.docRptAmount + MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt AS balanceAmountRpt
                            FROM
                                (
                            SELECT
                                erp_generalledger.companySystemID,
                                erp_generalledger.companyID,
                                erp_generalledger.serviceLineSystemID,
                                erp_generalledger.serviceLineCode,
                                erp_generalledger.documentSystemID,
                                erp_generalledger.documentID,
                                erp_generalledger.documentSystemCode,
                                erp_generalledger.documentCode,
                                erp_generalledger.documentDate,
                                erp_generalledger.chartOfAccountSystemID,
                                erp_generalledger.glCode,
                                erp_generalledger.glAccountType,
                                erp_generalledger.documentNarration,
                                erp_generalledger.clientContractID,
                                erp_generalledger.invoiceNumber,
                                erp_generalledger.invoiceDate,
                                erp_generalledger.supplierCodeSystem,
                                erp_generalledger.documentTransCurrencyID,
                                erp_generalledger.documentTransAmount * - 1 AS docTransAmount,
                                erp_generalledger.documentLocalCurrencyID,
                                erp_generalledger.documentLocalAmount * - 1 AS docLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                erp_generalledger.documentRptAmount * - 1 AS docRptAmount,
                            IF
                                ( paymentVoucherAmount.PaidPaymentVoucherTransAmount IS NULL, 0, paymentVoucherAmount.PaidPaymentVoucherTransAmount ) AS PaidPaymentVoucherTransAmount,
                            IF
                                ( paymentVoucherAmount.PaidPaymentVoucherLocalAmount IS NULL, 0, paymentVoucherAmount.PaidPaymentVoucherLocalAmount ) AS PaidPaymentVoucherLocalAmount,
                            IF
                                ( paymentVoucherAmount.PaidPaymentVoucherRptAmount IS NULL, 0, paymentVoucherAmount.PaidPaymentVoucherRptAmount ) AS PaidPaymentVoucherRptAmount,
                            IF
                                ( InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountTrans IS NULL, 0, InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountTrans ) AS InvoiceMatchedINMatchingAmountTrans,
                            IF
                                ( InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountLocal IS NULL, 0, InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountLocal ) AS InvoiceMatchedINMatchingAmountLocal,
                            IF
                                ( InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountRpt IS NULL, 0, InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountRpt ) AS InvoiceMatchedINMatchingAmountRpt,
                            IF
                                ( InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountTrans IS NULL, 0, InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountTrans ) AS InvoiceMatchedForpaymentAmountTrans,
                            IF
                                ( InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountLocal IS NULL, 0, InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountLocal ) AS InvoiceMatchedForpaymentAmountLocal,
                            IF
                                ( InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountRpt IS NULL, 0, InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountRpt ) AS InvoiceMatchedForpaymentAmountRpt,
                            IF
                                ( debitNoteMatched.debitNoteMatchedAmountTrans IS NULL, 0, debitNoteMatched.debitNoteMatchedAmountTrans ) AS debitNoteMatchedAmountTrans,
                            IF
                                ( debitNoteMatched.debitNoteMatchedAmountLocal IS NULL, 0, debitNoteMatched.debitNoteMatchedAmountLocal ) AS debitNoteMatchedAmountLocal,
                            IF
                                ( debitNoteMatched.debitNoteMatchedAmountRpt IS NULL, 0, debitNoteMatched.debitNoteMatchedAmountRpt ) AS debitNoteMatchedAmountRpt
                            FROM
                                erp_generalledger
                                LEFT JOIN (-- payment voucher matched with invoice or debit note
                            SELECT
                                erp_paysupplierinvoicedetail.companySystemID,
                                erp_paysupplierinvoicedetail.companyID,
                                erp_paysupplierinvoicemaster.documentSystemID,
                                erp_paysupplierinvoicemaster.documentID,
                                erp_paysupplierinvoicemaster.PayMasterAutoId,
                                erp_paysupplierinvoicemaster.BPVcode,
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS PaidPaymentVoucherTransAmount,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS PaidPaymentVoucherLocalAmount,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS PaidPaymentVoucherRptAmount
                            FROM
                                erp_paysupplierinvoicedetail
                                INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.PayMasterAutoId = erp_paysupplierinvoicemaster.PayMasterAutoId
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID = 0
                                AND erp_paysupplierinvoicemaster.approved = -1
                                AND erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND DATE(erp_paysupplierinvoicemaster.postedDate) <= "' . $asOfDate . '"
                            GROUP BY
                                companySystemID,
                                documentSystemID,
                                PayMasterAutoId
                                ) AS paymentVoucherAmount ON paymentVoucherAmount.companySystemID = erp_generalledger.companySystemID
                                AND paymentVoucherAmount.documentSystemID = erp_generalledger.documentSystemID
                                AND paymentVoucherAmount.PayMasterAutoId = erp_generalledger.documentSystemCode
                                LEFT JOIN (-- invoice matched in a matching document
                            SELECT
                                erp_paysupplierinvoicedetail.companySystemID,
                                erp_paysupplierinvoicedetail.companyID,
                                erp_paysupplierinvoicedetail.addedDocumentSystemID,
                                erp_paysupplierinvoicedetail.addedDocumentID,
                                erp_paysupplierinvoicedetail.bookingInvSystemCode,
                                erp_paysupplierinvoicedetail.bookingInvDocCode,
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS InvoiceMatchedINMatchingAmountTrans,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS InvoiceMatchedINMatchingAmountLocal,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS InvoiceMatchedINMatchingAmountRpt
                            FROM
                                erp_paysupplierinvoicedetail
                                INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_paysupplierinvoicedetail.matchingDocID
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID > 0
                                AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '"
                            GROUP BY
                                companySystemID,
                                addedDocumentSystemID,
                                bookingInvSystemCode
                                ) AS InvoiceMatchedINMatching ON InvoiceMatchedINMatching.companySystemID = erp_generalledger.companySystemID
                                AND InvoiceMatchedINMatching.addedDocumentSystemID = erp_generalledger.documentSystemID
                                AND InvoiceMatchedINMatching.bookingInvSystemCode = erp_generalledger.documentSystemCode
                                LEFT JOIN (-- invoice matched in a payment voucher document
                            SELECT
                                erp_paysupplierinvoicedetail.companySystemID,
                                erp_paysupplierinvoicedetail.companyID,
                                erp_paysupplierinvoicedetail.addedDocumentSystemID,
                                erp_paysupplierinvoicedetail.addedDocumentID,
                                erp_paysupplierinvoicedetail.bookingInvSystemCode,
                                erp_paysupplierinvoicedetail.bookingInvDocCode,
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS InvoiceMatchedForpaymentAmountTrans,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS InvoiceMatchedForpaymentAmountLocal,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS InvoiceMatchedForpaymentAmountRpt
                            FROM
                                erp_paysupplierinvoicedetail
                                INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.PayMasterAutoId = erp_paysupplierinvoicemaster.PayMasterAutoId
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID = 0
                                AND erp_paysupplierinvoicemaster.approved = -1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND DATE(erp_paysupplierinvoicemaster.postedDate) <= "' . $asOfDate . '"
                            GROUP BY
                                companySystemID,
                                addedDocumentSystemID,
                                bookingInvSystemCode
                                ) AS InvoiceMatchedForpayment ON InvoiceMatchedForpayment.companySystemID = erp_generalledger.companySystemID
                                AND InvoiceMatchedForpayment.addedDocumentSystemID = erp_generalledger.documentSystemID
                                AND InvoiceMatchedForpayment.bookingInvSystemCode = erp_generalledger.documentSystemCode
                                LEFT JOIN (-- matched debit note
                            SELECT
                                erp_matchdocumentmaster.companySystemID,
                                erp_matchdocumentmaster.companyID,
                                erp_matchdocumentmaster.documentSystemID,
                                erp_matchdocumentmaster.documentID,
                                erp_matchdocumentmaster.PayMasterAutoId,
                                erp_matchdocumentmaster.BPVcode,
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS debitNoteMatchedAmountTrans,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS debitNoteMatchedAmountLocal,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS debitNoteMatchedAmountRpt
                            FROM
                                erp_matchdocumentmaster
                                INNER JOIN erp_paysupplierinvoicedetail ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_paysupplierinvoicedetail.matchingDocID
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID > 0
                                AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '"
                            GROUP BY
                                companySystemID,
                                documentSystemID,
                                PayMasterAutoId
                                ) AS debitNoteMatched ON debitNoteMatched.companySystemID = erp_generalledger.companySystemID
                                AND debitNoteMatched.documentSystemID = erp_generalledger.documentSystemID
                                AND debitNoteMatched.PayMasterAutoId = erp_generalledger.documentSystemCode
                            WHERE
                                DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
                                AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
                                AND erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                ) AS MAINQUERY
                            LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = MAINQUERY.supplierCodeSystem
                            LEFT JOIN companymaster ON MAINQUERY.companySystemID = companymaster.companySystemID
                            LEFT JOIN currencymaster as transCurrencyDet ON transCurrencyDet.currencyID=MAINQUERY.documentTransCurrencyID
                            LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=MAINQUERY.documentLocalCurrencyID
                            LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=MAINQUERY.documentRptCurrencyID) as finalAgingDetail WHERE ' . $whereQry . ' < 0 ORDER BY documentDate ASC) as grandFinal GROUP BY supplierCodeSystem,companyID ORDER BY suppliername;');
        //dd(DB::getQueryLog());
        return ['data' => $output, 'aging' => $aging];
    }

    function getTopSupplierQRY($request)
    {
        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $currency = $request->currencyID;
        $year = $request->year;
        $companyWise = '';

        $reportTypeID = $request->reportTypeID;
        if ($reportTypeID == 'TSCW') {
            $companyWise = 'erp_purchaseordermaster.companySystemID,';
        }else if($reportTypeID == 'TSC'){

        }

        $qry = 'SELECT
                        erp_purchaseordermaster.companySystemID,
                        erp_purchaseordermaster.companyID,
                        companymaster.CompanyName,
                        erp_purchaseordermaster.supplierID,
                        erp_purchaseordermaster.supplierPrimaryCode,
                        erp_purchaseordermaster.supplierName,
                        countrymaster.countryName AS supplierCountry,
                        sum( ( erp_purchaseorderdetails.noQty * erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) ) AS Amount 
                    FROM
                        erp_purchaseordermaster
                        INNER JOIN erp_purchaseorderdetails ON erp_purchaseorderdetails.purchaseOrderMasterID = erp_purchaseordermaster.purchaseOrderID
                        INNER JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_purchaseordermaster.supplierID
                        LEFT JOIN countrymaster ON suppliermaster.supplierCountryID = countrymaster.countryID 
                        INNER JOIN companymaster ON erp_purchaseordermaster.companySystemID = companymaster.companySystemID
                    WHERE
                        erp_purchaseordermaster.approved =- 1 
                        AND erp_purchaseordermaster.poCancelledYN = 0 
                        AND poType_N <> 5 
                        AND YEAR ( erp_purchaseordermaster.approvedDate ) = '.$year.' 
                    GROUP BY
                        '.$companyWise.'
                        erp_purchaseordermaster.supplierID 	Order BY Amount DESC;';
                //DB::enableQueryLog();
         $output = \DB::select($qry);

        return $output;

    }


}