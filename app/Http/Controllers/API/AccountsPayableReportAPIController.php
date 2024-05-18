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
 * -- Date: 27-December 2018 By: Nazir Description: Added new functions named as getSupplierBalanceStatementReconcileQRY()
 * -- Date: 30-January 2019 By: Nazir Description: Added new functions named as pdfExportReport()
 */

namespace App\Http\Controllers\API;

use App\Exports\AccountsPayable\SupplierAging\SupplierAgingDetailAdvanceReport;
use App\Exports\AccountsPayable\SupplierAging\SupplierAgingDetailReport;
use App\Exports\AccountsPayable\SupplierAging\SupplierAgingSummaryAdvanceReport;
use App\Exports\AccountsPayable\SupplierAging\SupplierAgingSummaryReport;
use App\Exports\AccountsPayable\SupplierBalanceSummary;
use App\Exports\AccountsPayable\SupplierLedgerReport;
use App\Exports\AccountsPayable\SupplierStatement\SupplierBalanceStatement;
use App\Exports\AccountsPayable\SupplierStatement\SupplierStatementDetails;
use App\Exports\AccountsPayable\SupplierStatement\SupplierStatementReport;
use App\Exports\AccountsPayable\UnbilledGRV\UnbilledGrvAgingSummaryReport;
use App\Exports\AccountsPayable\UnbilledGRV\UnbilledGrvDetailsReport;
use App\Exports\AccountsPayable\UnbilledGRV\UnbilledGrvDetailsSummaryReport;
use App\Exports\AccountsPayable\UnbilledGRV\UnbilledGrvLogisticDetails;
use App\helper\CreateExcel;
use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Jobs\DocumentAttachments\SupplierStatementJob;
use App\Models\AccountsPayableLedger;
use App\Models\BookInvSuppDet;
use App\Models\BookInvSuppMaster;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CountryMaster;
use App\Models\CurrencyMaster;
use App\Models\Employee;
use App\Models\FinanceItemCategoryMaster;
use App\Models\GeneralLedger;
use App\Models\SegmentMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierContactDetails;
use App\Models\SupplierMaster;
use App\Models\SystemGlCodeScenario;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\UnbilledGrvGroupBy;
use App\Models\Year;
use App\Services\AccountPayableLedger\Report\SupplierAgingReportService;
use App\Services\AccountPayableLedger\Report\UnbilledGrvReportService;
use App\Services\Currency\CurrencyService;
use App\Services\Excel\ExportReportToExcelService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\SupplierGroup;
class AccountsPayableReportAPIController extends AppBaseController
{
    public function getAPFilterData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $supplierGroups = SupplierGroup::onlyNotDeletedAndActive();
        $type = $request->get('type');

        if($type == 1)
        {
            $supplierGroupsIds = collect($supplierGroups)->pluck('id');
        }
        else
        {
            $supplierGroupsIds = $request->get('selectedGroup');
        }

   

        $companiesByGroup = "";
        if (\Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

        if ($request['reportID'] == 'TS') {
            $controlAccount = SupplierMaster::groupBy('liabilityAccountSysemID')->pluck('liabilityAccountSysemID');
            $controlAccount = ChartOfAccount::whereIN('chartOfAccountSystemID', $controlAccount)->get();
            $supplierMaster = array();
            $employeeMaster = array();
            $departments = array();
        } else if ($request['reportID'] == 'APUGRV') {

            $controlAccount = SupplierMaster::groupBy('UnbilledGRVAccountSystemID')->pluck('UnbilledGRVAccountSystemID');
            $controlAccount = ChartOfAccount::whereIN('chartOfAccountSystemID', $controlAccount)->get();

            $departments = array();

            $unbilledGrvSup = collect(UnbilledGrvGroupBy::select('supplierID')->groupBy('supplierID')->get())->pluck('supplierID')->toArray();
            $erp_bookinvsuppmaster = collect(BookInvSuppMaster::select('supplierID')->groupBy('supplierID')->get())->pluck('supplierID')->toArray();
            $filterSuppliers = array_merge($unbilledGrvSup,$erp_bookinvsuppmaster);

            $employeeMaster = array();

            $supplierMaster = SupplierAssigned::whereIN('companySystemID', $companiesByGroup)
                ->whereIN('supplierCodeSytem', $filterSuppliers)
                ->whereHas('master',function($q) use($supplierGroupsIds)
                {
                    $q->whereIN('supplier_group_id', $supplierGroupsIds);
                })   
                ->groupBy('supplierCodeSytem')
                ->get();

        } else {
            $controlAccount = SupplierMaster::groupBy('liabilityAccountSysemID')->pluck('liabilityAccountSysemID');
            $controlAccountAdv = SupplierMaster::groupBy('advanceAccountSystemID')->pluck('advanceAccountSystemID');

            $merged = $controlAccountAdv->merge($controlAccount);

            $unique = $merged->unique();
 
            $allAc = $unique->values()->all();

            $controlAccount = ChartOfAccount::whereIN('chartOfAccountSystemID', $allAc)->get();

            $departments = \Helper::getCompanyServiceline($selectedCompanyId);

            $filterSuppliers = AccountsPayableLedger::whereIN('companySystemID', $companiesByGroup)
                ->select('supplierCodeSystem')
                ->groupBy('supplierCodeSystem')
                ->pluck('supplierCodeSystem');

            $supplierMaster = SupplierAssigned::whereIN('companySystemID', $companiesByGroup)->whereIN('supplierCodeSytem', $filterSuppliers)->groupBy('supplierCodeSytem')
                                            ->whereHas('master',function($q) use($supplierGroupsIds)
                                            {
                                                $q->whereIN('supplier_group_id', $supplierGroupsIds);
                                            })                    
                                            ->get();

            $employeeMaster = DB::table('employees')
                ->select('employees.*')
                ->leftJoin('erp_bookinvsuppmaster', 'erp_bookinvsuppmaster.employeeID', '=', 'employees.employeeSystemID')
                ->leftJoin('erp_paysupplierinvoicemaster', 'erp_paysupplierinvoicemaster.directPaymentPayeeEmpID', '=', 'employees.employeeSystemID')
                ->leftJoin('erp_debitnote', 'erp_debitnote.empID', '=', 'employees.employeeSystemID')
                ->whereIn('employees.empCompanySystemID', $companiesByGroup)
                ->groupBy('employees.employeeSystemID')
                ->where(function($query) {
                    $query->whereNotNull('erp_bookinvsuppmaster.employeeID')
                        ->where('erp_bookinvsuppmaster.approved', -1)
                        ->orWhereNotNull('erp_paysupplierinvoicemaster.directPaymentPayeeEmpID')
                        ->where('erp_paysupplierinvoicemaster.approved', -1)
                        ->orWhereNotNull('erp_debitnote.empID')
                        ->where('erp_debitnote.approved', -1);
                })
                ->get();
        }


        $years = Year::orderby('year', 'desc')->get();


        $countries = CountryMaster::all();
        $segment = SegmentMaster::ofCompany($companiesByGroup)->get();

        $isConfigured = SystemGlCodeScenario::where('isActive', 1)->where('id',12)->first();
        $isDetailConfigured = SystemGlCodeScenarioDetail::where('systemGLScenarioID', 12)->where('companySystemID', $companiesByGroup)->first();

        if(!empty($isConfigured) && !empty($isDetailConfigured)) {
            $isChartOfAccountConfigured = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $isDetailConfigured->chartOfAccountSystemID)->where('companySystemID', $isDetailConfigured->companySystemID)->where('isActive', 1)->where('isAssigned', -1)->first();
            if(!empty($isChartOfAccountConfigured)) {
                $controlAccountEmployeeID = $isDetailConfigured->chartOfAccountSystemID;
                $controlAccountEmployee = ChartOfAccount::where('chartOfAccountSystemID', $controlAccountEmployeeID)->get();
            } else {
                $controlAccountEmployee = [];
            }
        } else {
            $controlAccountEmployee = [];
        }

        
        $categories = FinanceItemCategoryMaster::all();
        $output = array(
            'controlAccount' => $controlAccount,
            'controlAccountEmployee' => $controlAccountEmployee,
            'suppliers' => $supplierMaster,
            'employees' => $employeeMaster,
            'departments' => $departments,
            'years' => $years,
            'countries' => $countries,
            'categories' => $categories,
            'segment' => $segment,
            'supplierGroups' => $supplierGroups
        );

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
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
                    'currencyID' => 'required',
                    'supplierGroup' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            case 'APSS':

                $reportTypeID = '';
                if (isset($request->reportTypeID)) {
                    $reportTypeID = $request->reportTypeID;
                }
                if ($reportTypeID == 'SS') {
                    $validator = \Validator::make($request->all(), [
                        'reportTypeID' => 'required',
                        'fromDate' => 'required',
                        'suppliers' => 'required',
                        'controlAccountsSystemID' => 'required',
                        'currencyID' => 'required',
                        'supplierGroup' => 'required'
                    ]);

                    if ($validator->fails()) {
                        return $this->sendError($validator->messages(), 422);
                    }
                } else if ($reportTypeID == 'SSD') {
                    $validator = \Validator::make($request->all(), [
                        'reportTypeID' => 'required',
                        'fromDate' => 'required',
                        'suppliers' => 'required',
                        'supplierGroup' => 'required'
                    ]);

                    if ($validator->fails()) {
                        return $this->sendError($validator->messages(), 422);
                    }
                } else if ($reportTypeID == 'SBSR') {
                    $validator = \Validator::make($request->all(), [
                        'reportTypeID' => 'required',
                        'fromDate' => 'required',
                        'suppliers' => 'required',
                        'controlAccountsSystemID' => 'required',
                        'supplierGroup' => 'required'
                    ]);

                    if ($validator->fails()) {
                        return $this->sendError($validator->messages(), 422);
                    }
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
                    return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.report_type')]));
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
                    'suppliers' => 'required',
                    'supplierGroup' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            case 'APSA':
                $request = (array)$this->convertArrayToSelectedValue($request->all(), array('supEmpId'));

                $validator = \Validator::make($request, [
                        'reportTypeID' => 'required',
                        'fromDate' => 'required',
                        'suppliers' => 'required',
                        'controlAccountsSystemID' => 'required',
                        'currencyID' => 'required',
                        'interval' => 'required',
                        'through' => 'required',
                        'supplierGroup' => ['required_if:supEmpId,1']
                ], [
                    'suppliers.required' => 'The supplier/employee field is required.'
                ]);


                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            case 'TS':
                $validator = \Validator::make($request->all(), [
                    'reportTypeID' => 'required',
                    'year' => 'required',
                    'countries' => 'required',
                    'controlAccountsSystemID' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            case 'APUGRV':

                $reportTypeID = '';
                if (isset($request->reportTypeID)) {
                    $reportTypeID = $request->reportTypeID;
                }

                if ($reportTypeID == 'ULD') {
                    $validator = \Validator::make($request->all(), [
                        'reportTypeID' => 'required',
                        'fromDate' => 'required',
                        'suppliers' => 'required'
                    ]);
                } else {
                    $validator = \Validator::make($request->all(), [
                        'reportTypeID' => 'required',
                        'fromDate' => 'required',
                        'suppliers' => 'required',
                        'controlAccountsSystemID' => 'required',
                        'currencyID' => 'required',
                        'localOrForeign' => 'required'
                    ]);
                }

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            case 'APITP':
                $validator = \Validator::make($request->all(), [
                    'reportTypeID' => 'required',
                    'fromDate' => 'required',
                    'suppliers' => 'required',
                    'currencyID' => 'required'
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
    public function generateAPReport(Request $request)
    {
        try {
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
                $checkIsGroup = Company::find($request->companySystemID);
                $reportTypeID = $request->reportTypeID;

                if ($reportTypeID == 'SS') {
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $request->fromPath = 'view';
                    $output = $this->getSupplierStatementQRY($request);

                    $outputArr = array();

                    $balanceAmount = collect($output)->pluck('balanceAmount')->toArray();

                    $exchangeGL = collect($output)->pluck('exchangeGL')->toArray();
                    $balanceAmount = array_sum($balanceAmount)- array_sum($exchangeGL);

                    $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                    $decimalPlace = array_unique($decimalPlace);

                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->concatSupplierName][$val->documentCurrency][] = $val;
                        }
                    }
                    return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'balanceAmount' => $balanceAmount, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2);
                } else if ($reportTypeID == 'SSD') {
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    [$outputArr, $decimalPlace, $selectedCurrency] = $this->getSupplierStatementDetails($request);

                    return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'currency' => $request->currencyID, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2);
                } else if ($reportTypeID == 'SBSR') {
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'controlAccountsSystemID'));
                    $output = $this->getSupplierBalanceStatementReconcileQRY($request);
                    $outputArr = array();

                    $balanceAmount = collect($output)->pluck('balanceAmount')->toArray();
                    $balanceAmount = array_sum($balanceAmount);

                    $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                    $decimalPlace = array_unique($decimalPlace);
                    return array('reportData' => $output, 'companyName' => $checkIsGroup->CompanyName, 'balanceAmount' => $balanceAmount, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2);
                }

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

                    return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces, 'agingRange' => $output['aging'], 'unAllocatedAmount' => $unAllocatedAmount, 'lineGrandTotal' => $lineGrandTotal + $unAllocatedAmount);
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

                    return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces, 'agingRange' => $output['aging'], 'unAllocatedAmount' => $unAllocatedAmount, 'lineGrandTotal' => $lineGrandTotal + $unAllocatedAmount);
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
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'controlAccountsSystemID'));
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
                } else if ($reportTypeID == 'TSC') {
                    $finalArray['reportData'] = $output;
                }

                return $finalArray;
                break;
            case 'APUGRV': //Unbilled GRV
                $reportTypeID = $request->reportTypeID;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'localOrForeign', 'controlAccountsSystemID'));
                $checkIsGroup = Company::find($request->companySystemID);
                $outputArr = array();
                $grandTotalArr = array('documentLocalAmount' => 0,
                    'matchedLocalAmount' => 0,
                    'balanceLocalAmount' => 0,
                    'documentRptAmount' => 0,
                    'matchedRptAmount' => 0,
                    'balanceRptAmount' => 0,
                );
                $decimalPlaces = 2;
                $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                if ($companyCurrency) {
                    if ($request->currencyID == 2) {
                        $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                    } else if ($request->currencyID == 3) {
                        $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                    }
                }

                $output = array();

                if ($reportTypeID == 'UGRVD' || $reportTypeID == 'UGRVS') { //Unbilled Detail

                    $output = $this->getUnbilledDetailQRY($request);

                    if ($reportTypeID == 'UGRVD') {
                        if ($output) {
                            foreach ($output as $val) {
                                $outputArr[$val->supplierName][] = $val;
                            }
                        }
                    } else {
                        $outputArr = $output;
                    }

                } else if ($reportTypeID == 'UGRVAD') {
                    $output = $this->getUnbilledGRVDetailAgingQRY($request);
                    if ($reportTypeID == 'UGRVAD' && $output) {
                        foreach ($output as $val) {
                            $outputArr[$val->supplierName][] = $val;
                        }
                    }
                } else if ($reportTypeID == 'UGRVAS') {
                    $output = $this->getUnbilledGRVSummaryAgingQRY($request);
                    $outputArr = $output;
                }
                else if ($reportTypeID == 'ULD') {
                    $output = $this->getUnbilledLogisticsDetailQRY($request);
                    $outputArr = $output;

                    $grandTotalArr['LogisticAmountRpt'] = array_sum(collect($output)->pluck('LogisticAmountRpt')->toArray());
                    $grandTotalArr['PaidAmountRpt'] = array_sum(collect($output)->pluck('PaidAmountRpt')->toArray());
                    $grandTotalArr['BalanceRptAmount'] = array_sum(collect($output)->pluck('BalanceRptAmount')->toArray());
                }

                $grandTotalArr['documentLocalAmount'] = array_sum(collect($output)->pluck('documentLocalAmount')->toArray());
                $grandTotalArr['matchedLocalAmount'] = array_sum(collect($output)->pluck('matchedLocalAmount')->toArray());
                $grandTotalArr['balanceLocalAmount'] = array_sum(collect($output)->pluck('balanceLocalAmount')->toArray());
                $grandTotalArr['documentRptAmount'] = array_sum(collect($output)->pluck('documentRptAmount')->toArray());
                $grandTotalArr['matchedRptAmount'] = array_sum(collect($output)->pluck('matchedRptAmount')->toArray());
                $grandTotalArr['balanceRptAmount'] = array_sum(collect($output)->pluck('balanceRptAmount')->toArray());

                $grandTotalArr['case1'] = array_sum(collect($output)->pluck('case1')->toArray());
                $grandTotalArr['case2'] = array_sum(collect($output)->pluck('case2')->toArray());
                $grandTotalArr['case3'] = array_sum(collect($output)->pluck('case3')->toArray());
                $grandTotalArr['case4'] = array_sum(collect($output)->pluck('case4')->toArray());
                $grandTotalArr['case5'] = array_sum(collect($output)->pluck('case5')->toArray());
                $grandTotalArr['case6'] = array_sum(collect($output)->pluck('case6')->toArray());
                $grandTotalArr['case7'] = array_sum(collect($output)->pluck('case7')->toArray());
                $grandTotalArr['case8'] = array_sum(collect($output)->pluck('case8')->toArray());
                $grandTotalArr['case9'] = array_sum(collect($output)->pluck('case9')->toArray());
                $grandTotalArr['case10'] = array_sum(collect($output)->pluck('case10')->toArray());

                return array('reportData' => $outputArr,
                    'companyName' => $checkIsGroup->CompanyName,
                    'company' => $checkIsGroup,
                    'grandTotal' => $grandTotalArr,
                    'currencyDecimalPlace' => $decimalPlaces,
                    'count' => count($output));

                break;
            case 'APITP':
                $outputArr = array();
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getInvoiceToPaymentQry($request);
                if($output){
                    foreach ($output as $data){
                        array_push($outputArr,$data);
                    }
                }
                $companyCurrency = Helper::companyCurrency($request->companySystemID);
                if ($companyCurrency && $companyCurrency->reportingcurrency) {
                    $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                }
                return array('reportData' => $outputArr,
                    'companyName' => $checkIsGroup->CompanyName,
                    'company' => $checkIsGroup,
                    'currencyDecimalPlace' => $decimalPlaces,
                    'count' => count($output));
                break;
            default:
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.report_id')]));
        }
        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 500);
        }
    }

    public function getSupplierStatementDetails($request){
        $output = $this->getSupplierStatementDetailsQRY($request);
        $outputArr = array();
        $selectedCurrecny = null;
        $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
        $decimalPlace = array_unique($decimalPlace);
        if ($output) {
            $selectedCurrecny = $output[0]->documentCurrency;
            foreach ($output as $val) {
                $supplierName = $val->suppliername;
                $documentSystemId = $val->documentSystemID;
                $balanceAmount = $val->balanceAmount;

                if (!isset($outputArr[$supplierName])) {
                    $outputArr[$supplierName] = [
                        'supplier_currency' => $val->CurrencyName,
                        'payable_account' => $val->payableAccount,
                        'prePayment_account' => $val->prePaymentAccount,
                        'open_invoices' => 0,
                        'open_advances' => 0,
                        'open_debit_notes' => 0,
                    ];
                }

                switch ($documentSystemId) {
                    case 4:
                        $outputArr[$supplierName]['open_advances'] += $balanceAmount;
                        break;
                    case 11:
                        $outputArr[$supplierName]['open_invoices'] += $balanceAmount;
                        break;
                    case 15:
                        $outputArr[$supplierName]['open_debit_notes'] += $balanceAmount;
                        break;
                }
            }
        }

        return [$outputArr, $decimalPlace, $selectedCurrecny];
    }
    public function exportReport(Request $request, SupplierAgingReportService $supplierAgingReportService, ExportReportToExcelService $exportReportToExcelService, UnbilledGrvReportService $unbilledGrvReportService)
    {
        try {
            $reportID = $request->reportID;
            switch ($reportID) {
                case 'APPSY':
                    $type = $request->type;
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $output = $this->getPaymentSuppliersByYear($request);


                    $from_date = $request->fromDate;
                    $to_date = $request->toDate;
                    $company = Company::find($request->companySystemID);
                    $company_name = $company->CompanyName;
                    $year = $request->year;
                    $from_date = ((new Carbon($from_date))->format('d/m/Y'));
                    $decimalPlace = 0;
                    $data = array();
                    if ($request->reportTypeID == 'APPSY') {
                        $typ_re = 1;
                        $fileName = 'Payment Suppliers By Year -' . $year;
                        $title = 'Payment Suppliers By Year -' . $year;
                        $requestCurrency = $request->currency;

                    } else if ($request->reportTypeID == 'APDPY') {
                        $typ_re = 1;
                        $fileName = 'Direct Payments By Year -' . $year;
                        $title = 'Direct Payments By Year -' . $year;
                        $requestCurrency = $request->currency;

                    } else if ($request->reportTypeID == 'APAPY') {
                        $typ_re = 1;
                        $fileName = 'All Payments By Year -' . $year;
                        $title = 'All Payments By Year -' . $year;
                        $requestCurrency = $request->currency;

                    } else if ($request->reportTypeID == 'APLWS' && $request->reportSD != 'detail') {
                        $typ_re = 2;
                        $fileName = 'Payments Lists Status By Year';
                        $title = 'Payments Lists Status By Year';
                        $requestCurrency = NULL;
                        $to_date = $request->toDate;
                        $to_date = ((new Carbon($to_date))->format('d/m/Y'));
                    }


                    if ($output) {
                        $reportSD = $request->reportSD;
                        $currency = $request->currencyID;
                        $reportTypeID = $request->reportTypeID;


                        if ($reportTypeID == 'APPSY') {
                            $typ_re = 1;


                            $fileName = 'Payment Suppliers By Year -' . $year;
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
                            $typ_re = 1;
                            $fileName = 'Direct Payments By Year -' . $year;
                            if ($reportSD == 'detail') {
                                $x = 0;
                                foreach ($output as $val) {
                                    $data[$x]['Company ID'] = $val->companyID;
                                    $data[$x]['Company Name'] = $val->CompanyName;
                                    $data[$x]['Posted Date'] = \Helper::dateFormat($val->documentDate);
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
                            $typ_re = 1;
                            $fileName = 'All Payments By Year -' . $year;
                            if ($reportSD != 'detail') {
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
                        } else if ($reportTypeID == 'APLWS' && $reportSD != 'detail') {
                            $typ_re = 2;
                            $to_date = $request->toDate;
                            $to_date = ((new Carbon($to_date))->format('d/m/Y'));


                            $fileName = 'Payments Lists Status By Year';
                            $x = 0;
                            foreach ($output as $val) {

                                $data[$x]['Company ID'] = $val->companyID;
                                $data[$x]['Company Name'] = $val->CompanyName;
                                $data[$x]['BPVcode'] = $val->BPVcode;
                                $data[$x]['Doc.Date'] = \Helper::dateFormat($val->BPVdate);
                                $data[$x]['Doc.Confirmed Date'] = \Helper::dateFormat($val->confirmedDate);
                                $data[$x]['Payee Name'] = $val->PayeeName;
                                $data[$x]['Credit Period'] = $val->creditPeriod;
                                $data[$x]['Bank'] = $val->bankName;
                                $data[$x]['Bank Account No'] = $val->AccountNo;
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
                                    $status = trans('custom.fully_approved');
                                } else if (($val->chequeSentToTreasury) == -1) {
                                    $status = trans('custom.payment_sent_treasury');
                                } else if (($val->chequeSentToTreasury == 0) && ($val->chequePaymentYN == 0)) {
                                    $status = trans('custom.payment_not_print');
                                }

                                $data[$x]['Approval Status'] = $status;
                                $x++;
                            }
                        }
                    }


                    $path = 'accounts-payable/report/payment_suppliers_by_year/excel/';

                    $companyCode = isset($company->CompanyID) ? $company->CompanyID : 'common';


                    if ($typ_re == 1) {
                        $detail_array = array('type' => 3, 'from_date' => $from_date, 'to_date' => $to_date, 'company_name' => $company_name, 'cur' => $requestCurrency, 'title' => $title, 'company_code' => $companyCode);
                        $basePath = CreateExcel::process($data, $type, $fileName, $path, $detail_array);
                    } else {
                        $detail_array = array('type' => 1, 'from_date' => $from_date, 'to_date' => $to_date, 'company_name' => $company_name, 'cur' => $requestCurrency, 'title' => $title, 'company_code' => $companyCode);
                        $basePath = CreateExcel::process($data, $type, $fileName, $path, $detail_array);
                    }


                    if ($basePath == '') {
                        return $this->sendError('Unable to export excel');
                    } else {
                        return $this->sendResponse($basePath, trans('custom.success_export'));
                    }

                    break;
                case 'APSS':
                    $type = $request->type;
                    $reportTypeID = $request->reportTypeID;

                    $from_date = $request->fromDate;
                    $to_date = $request->fromDate;
                    $company = Company::find($request->companySystemID);
                    $company_name = $company->CompanyName;
                    $data = array();
                    $from_date = ((new Carbon($from_date))->format('d/m/Y'));


                    if ($reportTypeID == 'SS')
                    {
                        $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                        $request->fromPath = 'view';
                        $output = $this->getSupplierStatementQRY($request);

                        if(empty($data))
                        {
                            $supplierStatementReportHeader = new SupplierStatementReport();
                            array_push($data,collect($supplierStatementReportHeader->getHeader())->toArray());
                        }

                        if ($output) {
                            foreach ($output as $val) {

                                $supplierStatementReport = new SupplierStatementReport();
                                $supplierStatementReport->setCompanyId($val->companyID);
                                $supplierStatementReport->setCompanyName($val->CompanyName);
                                $supplierStatementReport->setSupplierCode($val->SupplierCode);
                                $supplierStatementReport->setSupplierName($val->suppliername);
                                $supplierStatementReport->setDocumentId($val->documentID);
                                $supplierStatementReport->setDocumentCode($val->documentCode);
                                $supplierStatementReport->setDocumentDate($val->documentDate);
                                $supplierStatementReport->setAccount($val->glCode . " - " . $val->AccountDescription);
                                $supplierStatementReport->setNarration($val->documentNarration);
                                $supplierStatementReport->setInvoiceNumber($val->invoiceNumber);
                                $supplierStatementReport->setInvoiceDate($val->invoiceDate);
                                $supplierStatementReport->setCurrency($val->documentCurrency);
                                $supplierStatementReport->setAgeDays($val->ageDays);
                                $supplierStatementReport->setDocAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->invoiceAmount,2)));
                                $supplierStatementReport->setBalanceAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->balanceAmount,2)));

                                array_push($data,collect($supplierStatementReport)->toArray());
                            }
                        }

                        $fileName = 'Supplier Statement';
                        $title = 'Supplier Statement';
                        $excelColumnFormat = $supplierStatementReportHeader->getCloumnFormat();


                    }
                    else if ($reportTypeID == 'SSD')
                    {
                        $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                        $request->fromPath = 'view';
                        [$outputArr, $decimalPlace, $selectedCurrency] = $this->getSupplierStatementDetails($request);

                        if(empty($data))
                        {
                            $supplierStatementDetailsHeader = new SupplierStatementDetails();
                            array_push($data,collect($supplierStatementDetailsHeader->getHeader())->toArray());

                        }

                        if ($outputArr) {
                            foreach ($outputArr as $key => $val) {
                                $supplierStatementDetails = new SupplierStatementDetails();
                                $supplierStatementDetails->setPayableAccount($val['payable_account']);
                                $supplierStatementDetails->setPrepaymentAccount($val['prePayment_account']);
                                $supplierStatementDetails->setCurrency($val['supplier_currency']);
                                $supplierStatementDetails->setSupplierName($key);
                                $supplierStatementDetails->setOpenSupplierInvoices(CurrencyService::convertNumberFormatToNumber(number_format($val['open_invoices'],2)));
                                $supplierStatementDetails->setOpenAdvanceToSuppliers(CurrencyService::convertNumberFormatToNumber(number_format($val['open_advances'],2)));
                                $supplierStatementDetails->setOpenDebitNotes(CurrencyService::convertNumberFormatToNumber(number_format($val['open_debit_notes'],2)));
                                $supplierStatementDetails->setTotalPayable(CurrencyService::convertNumberFormatToNumber(number_format($val['open_invoices'],2)));
                                $supplierStatementDetails->setTotalPrepayment(CurrencyService::convertNumberFormatToNumber(number_format(($val['open_advances'] + $val['open_debit_notes']),2)));
                                $supplierStatementDetails->setNetOutstanding(CurrencyService::convertNumberFormatToNumber(number_format(($val['open_invoices'] + $val['open_advances'] + $val['open_debit_notes']),2)));
                                array_push($data,collect($supplierStatementDetails)->toArray());
                            }

                            $totalInvoices = array_sum(array_column($data, 'openSupplierInvoices'));
                            $totalAdvances = array_sum(array_column($data, 'openAdvanceToSuppliers'));
                            $totalDebitNotes = array_sum(array_column($data, 'openDebitNotes'));
                            $totalPayable = array_sum(array_column($data, 'totalPayable'));
                            $totalPrepayment = array_sum(array_column($data, 'totalPrepayment'));
                            $totalNetOutstanding = array_sum(array_column($data, 'netOutstanding'));


                            $supplierStatementDetailsFooter = new SupplierStatementDetails();

                            $supplierStatementDetailsFooter->setSupplierName("Total");
                            $supplierStatementDetailsFooter->setOpenSupplierInvoices(CurrencyService::convertNumberFormatToNumber(number_format($totalInvoices,2)));
                            $supplierStatementDetailsFooter->setOpenAdvanceToSuppliers(CurrencyService::convertNumberFormatToNumber(number_format($totalAdvances,2)));
                            $supplierStatementDetailsFooter->setOpenDebitNotes(CurrencyService::convertNumberFormatToNumber(number_format($totalDebitNotes,2)));
                            $supplierStatementDetailsFooter->setTotalPayable(CurrencyService::convertNumberFormatToNumber(number_format($totalPayable,2)));
                            $supplierStatementDetailsFooter->setTotalPrepayment(CurrencyService::convertNumberFormatToNumber(number_format($totalPrepayment,2)));
                            $supplierStatementDetailsFooter->setNetOutstanding(CurrencyService::convertNumberFormatToNumber(number_format($totalNetOutstanding,2)));

                            array_push($data,collect($supplierStatementDetailsFooter)->toArray());
                        }


                        $excelColumnFormat = $supplierStatementDetailsFooter->getCloumnFormat();
                        $fileName = 'Supplier Statement Details';
                        $title = 'Supplier Statement Details';

                    }
                    else if ($reportTypeID == 'SBSR') {
                        $request = (object)$this->convertArrayToSelectedValue($request->all(), array('controlAccountsSystemID'));
                        $output = $this->getSupplierBalanceStatementReconcileQRY($request);

                        if(empty($data))
                        {
                            $supplierBalanceStatementHeader = new SupplierBalanceStatement();
                            array_push($data,collect($supplierBalanceStatementHeader)->toArray());
                        }

                        if ($output) {
                            foreach ($output as $val) {
                                $supplierBalanceStatement = new SupplierBalanceStatement();
                                $supplierBalanceStatement->setCompanyId($val->companyID);
                                $supplierBalanceStatement->setCompanyName($val->CompanyName);
                                $supplierBalanceStatement->setDocumentDate($val->documentDate);
                                $supplierBalanceStatement->setDocumentCode($val->documentCode);
                                $supplierBalanceStatement->setSupplierCode($val->SupplierCode);
                                $supplierBalanceStatement->setSupplierName($val->suppliername);
                                $supplierBalanceStatement->setInvoiceNumber($val->invoiceNumber);
                                $supplierBalanceStatement->setInvoiceDate($val->invoiceDate);
                                $supplierBalanceStatement->setCurrency($val->documentCurrency);
                                $supplierBalanceStatement->setAmount($val->invoiceAmountDoc);
                                $supplierBalanceStatement->setBalanceAmount($val->balanceAmountDoc);
                                $supplierBalanceStatement->setLocalCurrency($val->documentCurrencyLoc);
                                $supplierBalanceStatement->setLocalAmount($val->invoiceAmountLoc);
                                $supplierBalanceStatement->setLocalBalanceAmount($val->balanceAmountLoc);
                                $supplierBalanceStatement->setReportingCurrency($val->documentCurrencyRpt);
                                $supplierBalanceStatement->setReportingAmount($val->invoiceAmountRpt);
                                $supplierBalanceStatement->setReportingBalanceAmount($val->balanceAmountRpt);

                                array_push($data,collect($supplierBalanceStatement)->toArray());
                            }
                        }

                        $fileName = 'Supplier Balance Statement';
                        $title = 'Supplier Balance Statement - Reconcile';
                        $excelColumnFormat = [];


                    }

                    $path = 'accounts-payable/report/supplier-statement/excel/';
                    $cur = NULL;

                    $companyCode = isset($company->CompanyID) ? $company->CompanyID : 'common';
//                    $cur = isset($selectedCurrency) ? $selectedCurrency : null;

                    $exportToExcel = $exportReportToExcelService
                        ->setTitle($title)
                        ->setFileName($fileName)
                        ->setPath($path)
                        ->setCompanyCode($companyCode)
                        ->setCompanyName($company_name)
                        ->setFromDate($from_date)
                        ->setToDate($to_date)
                        ->setData($data)
                        ->setReportType(2)
                        ->setType('xls')
                        ->setCurrency($cur)
                        ->setDateType(2)
                        ->setExcelFormat($excelColumnFormat)
                        ->setDetails()
                        ->generateExcel();

                    if(!$exportToExcel['success'])
                        return $this->sendError('Unable to export excel');

                    return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));
                    break;

                case 'APSL':
                    $type = $request->type;
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getSupplierLedgerQRY($request);
                    $fromDate = $request->fromDate;
                    $toDate = $request->toDate;

                    $outputArr = array();
                    $invoiceAmount = collect($output)->pluck('invoiceAmount')->toArray();
                    $invoiceAmount = array_sum($invoiceAmount);

                    $paidAmount = collect($output)->pluck('paidAmount')->toArray();
                    $paidAmount = array_sum($paidAmount);

                    $balanceAmount = collect($output)->pluck('balanceAmount')->toArray();
                    $balanceAmount = array_sum($balanceAmount);

                    $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                    $decimalPlace = array_unique($decimalPlace);

                    $data = $this->mapOutputWithSupplierLedgerReportObj($output);
                    if ($data) {
                        foreach ($data as $val) {
                            $outputArr[$val->supplierCode . " - " . $val->supplierName][$val->currency][] = $val;
                        }
                    }

                    $companyCode = isset($checkIsGroup->CompanyID) ? $checkIsGroup->CompanyID : null;
                    $templateName = "export_report.payment_suppliers";

                    $reportData = ['reportData' => $outputArr, 'Title' => 'Supplier Ledger', 'companyName' => $checkIsGroup->CompanyName, 'companyCode' => $companyCode, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'invoiceAmount' => $invoiceAmount, 'paidAmount' => $paidAmount, 'balanceAmount' => $balanceAmount, 'fromDate' => $fromDate, 'toDate' => $toDate];

                    $fileName = 'Supplier Ledger';
                    $path = 'accounts-payable/report/supplier_ledger/excel/';

                    $excelColumnFormat = [
                        'E' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
                        'B' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
                        'H' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    ];

                    $basePath = CreateExcel::loadView($reportData, $type, $fileName, $path, $templateName, $excelColumnFormat);

                    if ($basePath == '') {
                        return $this->sendError('Unable to export excel');
                    } else {
                        return $this->sendResponse($basePath, trans('custom.success_export'));
                    }

                    break;
                case 'APSBS':
                    $type = $request->type;
                    $from_date = $request->fromDate;
                    $to_date = $request->fromDate;
                    $company = Company::find($request->companySystemID);
                    $company_name = $company->CompanyName;
                    $from_date = ((new Carbon($from_date))->format('d/m/Y'));
                    $data = array();
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $output = $this->getSupplierBalanceSummeryQRY($request);

                    if(empty($data))
                    {
                        $supplierBalanceSummaryHeader = new SupplierBalanceSummary();
                        array_push($data,collect($supplierBalanceSummaryHeader->getHeader())->toArray());

                    }
                    if ($output) {
                        foreach ($output as $val) {
                            $supplierBalanceSummary = new SupplierBalanceSummary();
                            $supplierBalanceSummary->setCompanyID($val->companyID);
                            $supplierBalanceSummary->setCompanyName($val->CompanyName);
                            $supplierBalanceSummary->setAccount($val->AccountCode . "-" . $val->AccountDescription);
                            $supplierBalanceSummary->setSupplierCode($val->SupplierCode);
                            $supplierBalanceSummary->setSupplierName($val->supplierName);
                            $supplierBalanceSummary->setCurrency( $val->documentCurrency);
                            $supplierBalanceSummary->setAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->documentAmount,$val->balanceDecimalPlaces)));
                            array_push($data,collect($supplierBalanceSummary)->toArray());
                        }
                    }
                    $companyCode = isset($company->CompanyID) ? $company->CompanyID : 'common';
                    $cur = NULL;
                    $fileName = 'Supplier Balance Summary';
                    $title = 'Supplier Balance Summary';
                    $path = 'accounts-payable/report/supplier_balance_summary/excel/';
                    $excelColumnFormat = [
                        'G' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    ];
                    $exportToExcel = $exportReportToExcelService
                        ->setTitle($title)
                        ->setFileName($fileName)
                        ->setPath($path)
                        ->setCompanyCode($companyCode)
                        ->setCompanyName($company_name)
                        ->setFromDate($from_date)
                        ->setToDate($to_date)
                        ->setData($data)
                        ->setReportType(2)
                        ->setType('xls')
                        ->setCurrency($cur)
                        ->setDateType(2)
                        ->setExcelFormat($excelColumnFormat)
                        ->setDetails()
                        ->generateExcel();

                    if(!$exportToExcel['success'])
                        return $this->sendError('Unable to export excel');

                    return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));


                    break;
                case 'APSA':// Supplier Aging
                    $reportTypeID = $request->reportTypeID;
                    $type = $request->type;
                    $typeAging = isset($request->supEmpId[0]) ? $request->supEmpId[0]: $request->supEmpId;

                    $from_date = $request->fromDate;
                    $to_date = $request->toDate;
                    $company = Company::find($request->companySystemID);
                    $company_name = $company->CompanyName;

                    if ($reportTypeID == 'SAD') { //supplier aging detail
                        if($typeAging == 1){
                            $fileName = 'Supplier Aging Detail Report';
                            $title = 'Supplier Aging Detail Report';
                        } else {
                            $fileName = 'Employee Aging Detail Report';
                            $title = 'Employee Aging Detail Report';
                        }
                        $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                        $output = $this->getSupplierAgingDetailQRY($request);
                        $data = $supplierAgingReportService->getSupplierAgingExportToExcelData($output, $typeAging);
                        $objSupplierAgingDetail = new SupplierAgingDetailReport();
                        $excelColumnFormat = $objSupplierAgingDetail->getCloumnFormat();
                    }
                    else if ($reportTypeID == 'SAS') { //supplier aging summary
                        $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                        $output = $this->getSupplierAgingSummaryQRY($request);
                        if($typeAging == 1){
                            $fileName = 'Supplier Aging Summary Report';
                            $title = 'Supplier Aging Summary Report';
                        } else {
                            $fileName = 'Employee Aging Summary Report';
                            $title = 'Employee Aging Summary Report';
                        }
                        $data = $supplierAgingReportService->getSupplierAgingSummaryExportToExcelData($output, $typeAging);
                        $objSupplierAgingDetail = new SupplierAgingSummaryReport();
                        $excelColumnFormat = $objSupplierAgingDetail->getCloumnFormat();
                    }
                    else if ($reportTypeID == 'SADA') { //supplier aging detail advance
                        $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                        $output = $this->getSupplierAgingDetailAdvanceQRY($request);
                        if($typeAging == 1){
                            $fileName = 'Supplier Aging Detail Advance';
                            $title = 'Supplier Aging Detail Advance Report';
                        } else {
                            $fileName = 'Employee Aging Detail Advance';
                            $title = 'Employee Aging Detail Advance Report';
                        }
                        $data = $supplierAgingReportService->getSupplierAgingDetailAdvanceExportToExcelData($output, $typeAging);
                        $objSupplierAgingDetail = new SupplierAgingDetailAdvanceReport();
                        $excelColumnFormat = $objSupplierAgingDetail->getCloumnFormat();
                    }
                    else if ($reportTypeID == 'SASA') { //supplier aging summary
                        $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                        $output = $this->getSupplierAgingSummaryAdvanceQRY($request);
                        if($typeAging == 1) {
                            $fileName = 'Supplier Aging Summary Advance';
                            $title = 'Supplier Aging Summary Advance Report';
                        } else {
                            $fileName = 'Employee Aging Summary Advance';
                            $title = 'Employee Aging Summary Advance Report';
                        }
                        $data = $supplierAgingReportService->getSupplierAgingSummaryAdvanceExportToExcelData($output, $typeAging);
                        $objSupplierAgingDetail = new SupplierAgingSummaryAdvanceReport();
                        $excelColumnFormat = $objSupplierAgingDetail->getCloumnFormat();
                    }
                    $companyCode = isset($company->CompanyID) ? $company->CompanyID : 'common';
                    $cur = NULL;
                    $path = 'accounts-payable/report/supplier_aging/excel/';
                    $exportToExcel = $exportReportToExcelService
                        ->setTitle($title)
                        ->setFileName($fileName)
                        ->setPath($path)
                        ->setCompanyCode($companyCode)
                        ->setCompanyName($company_name)
                        ->setFromDate($from_date)
                        ->setToDate($to_date)
                        ->setData($data)
                        ->setReportType(2)
                        ->setType('xls')
                        ->setCurrency($cur)
                        ->setDateType(2)
                        ->setExcelFormat($excelColumnFormat)
                        ->setDetails()
                        ->generateExcel();

                    if(!$exportToExcel['success'])
                        return $this->sendError('Unable to export excel');

                    return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));


                    break;
                case 'TS':// Top Suppliers
                    $reportTypeID = $request->reportTypeID;
                    $type = $request->type;
                    $name = "";
                    if ($reportTypeID == 'TSCW' || $reportTypeID == 'TSC') {
                        $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'controlAccountsSystemID'));
                        $output = $this->getTopSupplierQRY($request);

                        if ($reportTypeID == 'TSCW') {
                            $name = "company_wise";
                        } else if ($reportTypeID == 'TSC') {
                            $name = "consolidated";
                        }

                        if ($output) {
                            $x = 0;
                            foreach ($output as $val) {

                                if ($reportTypeID == 'TSCW') {
                                    $data[$x]['Company ID'] = $val->companyID;
                                    $data[$x]['Company Name'] = $val->CompanyName;
                                }
                                $data[$x]['Supplier Code'] = $val->supplierPrimaryCode;
                                $data[$x]['Supplier Name'] = $val->supplierName;
                                $data[$x]['Supplier Country'] = $val->supplierCountry;
                                $data[$x]['Amount'] = round($val->Amount, 2);
                                $x++;
                            }
                        } else {
                            $data = array();
                        }
                    }
                    \Excel::create('top_suppliers_by_year_' . $name, function ($excel) use ($data) {
                        $excel->sheet('sheet name', function ($sheet) use ($data) {
                            $sheet->fromArray($data, null, 'A1', true);
                            $sheet->setAutoSize(true);
                            $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                        });
                        $lastrow = $excel->getActiveSheet()->getHighestRow();
                        $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                    })->download($type);
                    return $this->sendResponse(array(), trans('custom.success_export'));
                    break;
                case 'APUGRV':// Unbilled GRV
                    $reportTypeID = $request->reportTypeID;
                    $type = $request->type;
                    $name = "";
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'localOrForeign', 'controlAccountsSystemID'));

                    $from_date = $request->fromDate;
                    $to_date = $request->fromDate;
                    $company = Company::find($request->companySystemID);
                    $company_name = $company->CompanyName;
                    $from_date = ((new Carbon($from_date))->format('d/m/Y'));
                    $dataType=2;
                    if ($reportTypeID == 'UGRVD') { //Unbilled GRV details
                        $fileName = 'Unbilled GRV Detail Report ';
                        $title = 'Unbilled GRV Detail Report ';
                        $output = $this->getUnbilledDetailQRY($request);
                        $name = "detail";
                        $data = $unbilledGrvReportService->getUnbilledGrvExportToExcelData($output);
                        $objUnbilledGrvDetailsReport = new UnbilledGrvDetailsReport();
                        $excelColumnFormat = $objUnbilledGrvDetailsReport->getCloumnFormat();
                    }
                    else if ($reportTypeID == 'UGRVS') {  //Unbilled GRV summary
                        $output = $this->getUnbilledDetailQRY($request);
                        $fileName = 'Unbilled GRV Summary Report';
                        $title = 'Unbilled GRV Summary Report';
                        $name = "summary";
                        $data = $unbilledGrvReportService->getUnbilledGrvSummaryExportToExcelData($output);
                        $objUnbilledGrvDetailsReport = new UnbilledGrvDetailsSummaryReport();
                        $excelColumnFormat = $objUnbilledGrvDetailsReport->getCloumnFormat();
                    }
                    else if ($reportTypeID == 'UGRVAD') { //Unbilled GRV aging detail
                        $fileName = 'Unbilled GRV Aging Detail';
                        $title = 'Unbilled GRV Aging Detail Report';
                        $dataType = 1;
                        $output = $this->getUnbilledGRVDetailAgingQRY($request);
                        $name = "aging_detail";
                        $data = $unbilledGrvReportService->getUnbilledGrvAgingDetailExportToExcelData($output,$request);
                        $objUnbilledGrvAgingSummaryReport = new UnbilledGrvAgingSummaryReport();
                        $excelColumnFormat = $objUnbilledGrvAgingSummaryReport->getCloumnFormat();
                    }
                    else if ($reportTypeID == 'UGRVAS') {//Unbilled GRV aging summary
                        $fileName = 'Unbilled GRV Aging Summary';
                        $title = 'Unbilled GRV Aging Summary Report';
                        $output = $this->getUnbilledGRVSummaryAgingQRY($request);
                        $name = "aging_summary";
                        $data = $unbilledGrvReportService->getUnbilledGrvAgingSummaryExportToExcelData($output,$request);
                        $objUnbilledGrvAgingSummaryReport = new UnbilledGrvAgingSummaryReport();
                        $excelColumnFormat = $objUnbilledGrvAgingSummaryReport->getCloumnFormat();
                    }
                    else if ($reportTypeID == 'ULD') {

                        $fileName = 'Unbilled Logistics Detail';
                        $title = 'Unbilled Logistics Detail Report';
                        $output = $this->getUnbilledLogisticsDetailQRY($request);
                        $name = "logistics_detail";
                        $data = $unbilledGrvReportService->getUnbilledGrvLogisticDetailExportToExcelData($output);
                        $objUnbilledGrvAgingSummaryReport = new UnbilledGrvLogisticDetails();
                        $excelColumnFormat = $objUnbilledGrvAgingSummaryReport->getCloumnFormat();
                    }

                    $requestCurrency = NULL;
                    $path = $name . '/';
                    $path = 'accounts-payable/report/unbilled_grv_/' . $path . 'excel/';
                    $companyCode = isset($company->CompanyID) ? $company->CompanyID : 'common';

                    $exportToExcel = $exportReportToExcelService
                        ->setTitle($title)
                        ->setFileName($fileName)
                        ->setPath($path)
                        ->setCompanyCode($companyCode)
                        ->setCompanyName($company_name)
                        ->setFromDate($from_date)
                        ->setToDate($to_date)
                        ->setData($data)
                        ->setReportType(2)
                        ->setType('xls')
                        ->setCurrency($requestCurrency)
                        ->setDateType($dataType)
                        ->setExcelFormat($excelColumnFormat)
                        ->setDetails()
                        ->generateExcel();

                    if(!$exportToExcel['success'])
                        return $this->sendError('Unable to export excel');

                    return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));


                    break;
                case 'APITP':
                    $type = $request->type;
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));

                    $output = $this->getInvoiceToPaymentQry($request);
                    $companyCurrency = Helper::companyCurrency($request->companySystemID);
                    $decimalPlaces = 2;
                    if ($companyCurrency) {
                        $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                    }
                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Document No'] = $val->documentCode;
                            $data[$x]['Supplier Name'] = $val->supplierName;
                            $data[$x]['Supplier Invoice No'] = $val->supplierInvoiceNo;
                            $data[$x]['Supplier Invoice Date'] = Helper::dateFormat($val->supplierInvoiceDate);
                            $data[$x]['Currency'] = $val->CurrencyCode;
                            $data[$x]['Total Amount'] = number_format($val->rptAmount, $decimalPlaces);
                            $data[$x]['Confirmed Date'] = Helper::dateFormat($val->confirmedDate);
                            $data[$x]['Final Approved Date'] = Helper::dateFormat($val->approvedDate);
                            $data[$x]['Posted Date'] = Helper::dateFormat($val->postedDate);

                            $data[$x]['Payment Voucher No'] = $val->BPVcode;
                            $data[$x]['Paid Amount'] = number_format($val->paidRPTAmount, $decimalPlaces);
                            $data[$x]['Cheque No'] = $val->BPVchequeNo;
                            $data[$x]['Cheque Date'] = Helper::dateFormat($val->BPVchequeDate);
                            $data[$x]['Cheque Printed By'] = $val->chequePrintedByEmpName;
                            $data[$x]['Cheque Printed Date'] = Helper::dateFormat($val->chequePrintedDateTime);
                            $data[$x]['Payment Cleared Date'] = Helper::dateFormat($val->trsClearedDate);
                            $x++;
                        }
                    } else {
                        $data = array();
                    }
                    \Excel::create('invoice_to_paymentpayment', function ($excel) use ($data) {
                        $excel->sheet('sheet name', function ($sheet) use ($data) {
                            $sheet->fromArray($data, null, 'A1', true);
                            $sheet->setAutoSize(true);
                            $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                        });
                        $lastrow = $excel->getActiveSheet()->getHighestRow();
                        $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                    })->download($type);

                    break;
                default:
                    return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.report_id')]));
            }
        } catch(\Exception $e){
            return $this->sendError($e->getMessage(), 500);
        }
    }

    private function mapOutputWithSupplierLedgerReportObj($output)
    {
        $outputConverted = collect($output)->map(function($dt) {
            $supplierLedgerReport = new SupplierLedgerReport();
            $supplierLedgerReport->setDocumentCode($dt->documentCode);
            $supplierLedgerReport->setAccount($dt->AccountDescription);
            $supplierLedgerReport->setPostedDate($dt->documentSystemCode);
            $supplierLedgerReport->setDocumentSystemCode($dt->documentSystemCode);
            $supplierLedgerReport->setInvoiceDate($dt->invoiceDate);
            $supplierLedgerReport->setInvoiceNumber($dt->invoiceNumber);
            $supplierLedgerReport->setCurrency($dt->documentCurrency);
            $supplierLedgerReport->setSupplierName($dt->suppliername);
            $supplierLedgerReport->setSupplierCode($dt->SupplierCode);
            $supplierLedgerReport->setDocumentAmount((float) $dt->invoiceAmount);
            $supplierLedgerReport->setGlCode($dt->glCode);
            $supplierLedgerReport->setAccountDescription($dt->AccountDescription);
            $supplierLedgerReport->setDocumentCurrency($dt->documentCurrency);
            $supplierLedgerReport->setInvoiceAmount(CurrencyService::convertNumberFormatToNumber(number_format($dt->invoiceAmount,$dt->balanceDecimalPlaces)));
            $supplierLedgerReport->setInvoiceAmountOrg($dt->invoiceAmount);
            $supplierLedgerReport->setBalanceDecimalPlaces($dt->balanceDecimalPlaces);
            if($dt->documentSystemCode != "1970-01-01") {
                $supplierLedgerReport->setDocumentDate($dt->documentDate);
            }else {
                $supplierLedgerReport->setDocumentDate(null);
            }
            $supplierLedgerReport->setDocumentNarration($dt->documentNarration);
            return $supplierLedgerReport;
        });

        return $outputConverted;
    }

    function getSupplierLedgerQRY($request)
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

        $suppliers = (array)$request->suppliers;
        $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();

        $controlAccountsSystemIDs = (array)$request->controlAccountsSystemID;
        $controlAccountsSystemID = collect($controlAccountsSystemIDs)->pluck('id')->toArray();

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

        $query = 'SELECT
                    finalAgingDetail.companySystemID,
                    finalAgingDetail.companyID,
                    finalAgingDetail.CompanyName,
                    finalAgingDetail.documentSystemID,
                    finalAgingDetail.documentID,
                    finalAgingDetail.documentCode,
                    finalAgingDetail.documentSystemCode,
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
                    ' . $decimalPlaceQry . ',
                    finalAgingDetail.glCode,
                    finalAgingDetail.AccountDescription
                FROM
                (
                SELECT
                    MAINQUERY.companySystemID,
                    MAINQUERY.companyID,
                    companymaster.CompanyName,
                    MAINQUERY.documentSystemID,
                    MAINQUERY.documentID,
                    MAINQUERY.documentCode,
                    MAINQUERY.documentSystemCode,
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
                    MAINQUERY.chartOfAccountSystemID AS chartOfAccountSystemID,
                    MAINQUERY.glCode AS glCode,
                    chartofaccounts.AccountDescription
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
                    AND erp_generalledger.chartOfAccountSystemID IN (' . join(',', $controlAccountsSystemID) . ')
                    AND erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                    AND erp_generalledger.documentSystemID IN (4,11,15,16,18,24)
                    ) AS MAINQUERY
                LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = MAINQUERY.supplierCodeSystem
                LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = MAINQUERY.chartOfAccountSystemID
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
                    MAINQUERY.documentSystemCode,
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
                    SUM(IFNULL(MAINQUERY.docRptAmount,0)) AS documentAmountRpt,
                    MAINQUERY.chartOfAccountSystemID,
                    MAINQUERY.glCode,
                    chartofaccounts.AccountDescription
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
                    AND erp_generalledger.chartOfAccountSystemID IN (' . join(',', $controlAccountsSystemID) . ')
                    AND erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                    AND erp_generalledger.documentSystemID IN (4,11,15,16,18,24)
                     ) AS MAINQUERY
                LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = MAINQUERY.supplierCodeSystem
                LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = MAINQUERY.chartOfAccountSystemID
                LEFT JOIN currencymaster as transCurrencyDet ON transCurrencyDet.currencyID=MAINQUERY.documentTransCurrencyID
                LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=MAINQUERY.documentLocalCurrencyID
                LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=MAINQUERY.documentRptCurrencyID
                 LEFT JOIN companymaster ON companymaster.companySystemID = MAINQUERY.companySystemID
                 GROUP BY MAINQUERY.supplierCodeSystem, MAINQUERY.chartOfAccountSystemID ) as finalAgingDetail ORDER BY documentDate,suppliername';

        return \DB::select($query);
    }
    public function exchangeGainLoss($results, $currency) {

        foreach ($results as $index => $result){
            $exchangeGainLossAccount = SystemGlCodeScenarioDetail::getGlByScenario($result->companySystemID, $result->documentSystemID , 14);
            $chartOfAccount = GeneralLedger::where('documentSystemCode', $result->documentSystemCode)->where('chartOfAccountSystemID', $exchangeGainLossAccount)->where('companySystemID', $result->companySystemID)->where('documentType', NULL)->where('matchDocumentMasterAutoID', "!=", NULL)->first();
            if(!empty($chartOfAccount)) {
                if ($currency == 1) {
                    $currencyMaster = CurrencyMaster::find($chartOfAccount->documentTransCurrencyID);
                    $decimal = $currencyMaster->DecimalPlaces;
                    $result->exchangeGL = $chartOfAccount->documentTransAmount;
                } else if ($currency == 2) {
                    $currencyMaster = CurrencyMaster::find($chartOfAccount->documentLocalCurrencyID);
                    $result->exchangeGL = $chartOfAccount->documentLocalAmount;
                    $decimal = $currencyMaster->DecimalPlaces;
                } else {
                    $currencyMaster = CurrencyMaster::find($chartOfAccount->documentRptCurrencyID);
                    $result->exchangeGL = $chartOfAccount->documentRptAmount;
                    $decimal = $currencyMaster->DecimalPlaces;
                }
            }
            else {
                $result->exchangeGL = 0;
                $decimal = 3;
            }

            $roundedExchangeGL = round($result->exchangeGL, $decimal);

            if (abs($result->balanceAmount - $roundedExchangeGL) < 0.00001) {

                unset($results[$index]);
            }
        }

        return $results;
    }

    function getSupplierStatementQRY($request)
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

        $suppliers = (array)$request->suppliers;
        $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();

        $controlAccountsSystemIDs = (array)$request->controlAccountsSystemID;
        $controlAccountsSystemID = collect($controlAccountsSystemIDs)->pluck('id')->toArray();

        $currency = $request->currencyID;

        $path = $request->fromPath;

        $filterOrderBy = 'documentDate';
        if ($path == 'pdf') {
            $filterOrderBy = 'companySystemID';
        }

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

        $results = \DB::select('SELECT
                                finalAgingDetail.companySystemID,
                                finalAgingDetail.companyID,
                                finalAgingDetail.CompanyName,
                                finalAgingDetail.documentSystemID,
                                finalAgingDetail.documentSystemCode,
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
                                CONCAT(finalAgingDetail.SupplierCode," - ",finalAgingDetail.suppliername) as concatSupplierName,
                                CONCAT(finalAgingDetail.companyID," - ",finalAgingDetail.CompanyName) as concatCompany,
                                finalAgingDetail.glCode,
                                finalAgingDetail.AccountDescription
                            FROM
                            (
                            SELECT
                                MAINQUERY.companySystemID,
                                MAINQUERY.companyID,
                                companymaster.CompanyName,
                                MAINQUERY.documentSystemID,
                                MAINQUERY.documentSystemCode,
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
                                MAINQUERY.docTransAmount * -1 AS documentAmountTrans,
                                localCurrencyDet.CurrencyCode as localCurrencyCode,
                                localCurrencyDet.DecimalPlaces as documentLocalDecimalPlaces,
                                MAINQUERY.docLocalAmount * -1 AS documentAmountLocal,
                                rptCurrencyDet.CurrencyCode as rptCurrencyCode,
                                rptCurrencyDet.DecimalPlaces as documentRptDecimalPlaces,
                                MAINQUERY.docRptAmount * -1 AS documentAmountRpt,

                                (MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans) * -1 AS PaidAmountTrans,

                                (MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal) * -1 AS PaidAmountLocal,

                                (MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt) * -1 AS PaidAmountRpt,

                                (MAINQUERY.docTransAmount+MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans) * -1  as balanceAmountTrans,

                                (MAINQUERY.docLocalAmount+MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal) * -1  as balanceAmountLocal,

                                (MAINQUERY.docRptAmount + MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt) * -1 AS balanceAmountRpt,                                              
                                MAINQUERY.glCode,
                                chartofaccounts.AccountDescription
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
                                SUM(erp_generalledger.documentTransAmount) * - 1 AS docTransAmount,
                                erp_generalledger.documentLocalCurrencyID,
                                SUM(erp_generalledger.documentLocalAmount) * - 1 AS docLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                SUM(erp_generalledger.documentRptAmount) * - 1 AS docRptAmount,
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
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
                                AND erp_generalledger.chartOfAccountSystemID IN (' . join(',', $controlAccountsSystemID) . ')
                                AND erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND erp_generalledger.contraYN = 0
                                GROUP BY erp_generalledger.companySystemID, erp_generalledger.supplierCodeSystem,erp_generalledger.chartOfAccountSystemID,erp_generalledger.documentSystemID,erp_generalledger.documentSystemCode
                                ) AS MAINQUERY
                            LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = MAINQUERY.supplierCodeSystem
                            LEFT JOIN companymaster ON MAINQUERY.companySystemID = companymaster.companySystemID
                            LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = MAINQUERY.chartOfAccountSystemID
                            LEFT JOIN currencymaster as transCurrencyDet ON transCurrencyDet.currencyID=MAINQUERY.documentTransCurrencyID
                            LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=MAINQUERY.documentLocalCurrencyID
                            LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=MAINQUERY.documentRptCurrencyID) as finalAgingDetail WHERE ' . $whereQry . ' <> 0 ORDER BY ' . $filterOrderBy . ' ASC;');

        return $this->exchangeGainLoss($results, $currency);
    }

    function getSupplierStatementDetailsQRY($request)
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

        $suppliers = (array)$request->suppliers;
        $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();

        $controlAccountsSystemIDs = (array)$request->controlAccountsSystemID;
        $controlAccountsSystemID = collect($controlAccountsSystemIDs)->pluck('id')->toArray();

        $currency = $request->currencyID;
        $documentSystemID = array(4, 11, 15);

        $currencyQry = '';
        $balanceAmountQry = '';
        $decimalPlaceQry = '';
        $whereQry = '';
        if ($currency == 1) {
            $currencyQry = "finalAgingDetail.transCurrencyName AS documentCurrency";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountTrans, finalAgingDetail.documentTransDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentTransDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountTrans, finalAgingDetail.documentTransDecimalPlaces )";
        } else if ($currency == 2) {
            $currencyQry = "finalAgingDetail.localCurrencyName AS documentCurrency";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountLocal, finalAgingDetail.documentLocalDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentLocalDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountLocal, finalAgingDetail.documentLocalDecimalPlaces )";
        } else {
            $currencyQry = "finalAgingDetail.rptCurrencyName AS documentCurrency";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountRpt, finalAgingDetail.documentRptDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentRptDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountRpt, finalAgingDetail.documentRptDecimalPlaces )";
        }

        $results = \DB::select('SELECT
                                finalAgingDetail.companySystemID,
                                finalAgingDetail.documentSystemID,
                                finalAgingDetail.documentSystemCode,
                                finalAgingDetail.supplierCodeSystem,
                                finalAgingDetail.SupplierCode,
                                finalAgingDetail.suppliername,
                                supplierCurrency.CurrencyName,
                                liabilityAccount.AccountDescription as payableAccount,
                                advanceAccount.AccountDescription as prePaymentAccount,
                                ' . $balanceAmountQry . ',
                                ' . $decimalPlaceQry . ',
                                ' . $currencyQry . ',
                                CONCAT(finalAgingDetail.SupplierCode," - ",finalAgingDetail.suppliername) as concatSupplierName 
                            FROM
                            (
                            SELECT
                                MAINQUERY.companySystemID,
                                MAINQUERY.documentSystemID,
                                MAINQUERY.documentSystemCode,
                                MAINQUERY.supplierCodeSystem,
                                MAINQUERY.chartOfAccountSystemID,
                                suppliermaster.primarySupplierCode AS SupplierCode,
                                suppliermaster.suppliername,
                                suppliermaster.currency AS suppliercurrency,
                                suppliermaster.advanceAccountSystemID AS advanceAccount,
                                suppliermaster.liabilityAccountSysemID AS liabilityAccount,
                                transCurrencyDet.CurrencyName as transCurrencyName,
                                transCurrencyDet.DecimalPlaces as documentTransDecimalPlaces,
                                MAINQUERY.docTransAmount * -1 AS documentAmountTrans,
                                localCurrencyDet.CurrencyName as localCurrencyName,
                                localCurrencyDet.DecimalPlaces as documentLocalDecimalPlaces,
                                MAINQUERY.docLocalAmount * -1 AS documentAmountLocal,
                                rptCurrencyDet.CurrencyName as rptCurrencyName,
                                rptCurrencyDet.DecimalPlaces as documentRptDecimalPlaces,
                                MAINQUERY.docRptAmount * -1 AS documentAmountRpt,

                                (MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans) * -1 AS PaidAmountTrans,

                                (MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal) * -1 AS PaidAmountLocal,

                                (MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt) * -1 AS PaidAmountRpt,

                                (MAINQUERY.docTransAmount+MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans) * -1  as balanceAmountTrans,

                                (MAINQUERY.docLocalAmount+MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal) * -1  as balanceAmountLocal,

                                (MAINQUERY.docRptAmount + MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt) * -1 AS balanceAmountRpt                                            
                            FROM
                                (
                            SELECT
                                erp_generalledger.companySystemID,
                                erp_generalledger.chartOfAccountSystemID,
                                erp_generalledger.documentSystemCode,
                                erp_generalledger.documentSystemID,
                                erp_generalledger.supplierCodeSystem,
                                erp_generalledger.documentTransCurrencyID,
                                SUM(erp_generalledger.documentTransAmount) * - 1 AS docTransAmount,
                                erp_generalledger.documentLocalCurrencyID,
                                SUM(erp_generalledger.documentLocalAmount) * - 1 AS docLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                SUM(erp_generalledger.documentRptAmount) * - 1 AS docRptAmount,
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
                                
                                erp_paysupplierinvoicemaster.documentSystemID,
                               
                                erp_paysupplierinvoicemaster.PayMasterAutoId,
                               
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS PaidPaymentVoucherTransAmount,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS PaidPaymentVoucherLocalAmount,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS PaidPaymentVoucherRptAmount
                            FROM
                                erp_paysupplierinvoicedetail
                                INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.PayMasterAutoId = erp_paysupplierinvoicemaster.PayMasterAutoId
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID = 0
                                AND erp_paysupplierinvoicedetail.isRetention = 0
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
                                
                                erp_paysupplierinvoicedetail.addedDocumentSystemID,
                              
                                erp_paysupplierinvoicedetail.bookingInvSystemCode,
                               
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS InvoiceMatchedINMatchingAmountTrans,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS InvoiceMatchedINMatchingAmountLocal,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS InvoiceMatchedINMatchingAmountRpt
                            FROM
                                erp_paysupplierinvoicedetail
                                INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_paysupplierinvoicedetail.matchingDocID
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID > 0
                                AND erp_paysupplierinvoicedetail.isRetention = 0
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
                               
                                erp_paysupplierinvoicedetail.addedDocumentSystemID,
                              
                                erp_paysupplierinvoicedetail.bookingInvSystemCode,
                          
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS InvoiceMatchedForpaymentAmountTrans,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS InvoiceMatchedForpaymentAmountLocal,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS InvoiceMatchedForpaymentAmountRpt
                            FROM
                                erp_paysupplierinvoicedetail
                                INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.PayMasterAutoId = erp_paysupplierinvoicemaster.PayMasterAutoId
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID = 0
                                AND erp_paysupplierinvoicedetail.isRetention = 0
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
                          
                                erp_matchdocumentmaster.documentSystemID,
                            
                                erp_matchdocumentmaster.PayMasterAutoId,
                          
                                sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS debitNoteMatchedAmountTrans,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS debitNoteMatchedAmountLocal,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS debitNoteMatchedAmountRpt
                            FROM
                                erp_matchdocumentmaster
                                INNER JOIN erp_paysupplierinvoicedetail ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_paysupplierinvoicedetail.matchingDocID
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID > 0
                                AND erp_paysupplierinvoicedetail.isRetention = 0
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
                                AND erp_generalledger.chartOfAccountSystemID IN (' . join(',', $controlAccountsSystemID) . ')
                                AND erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND erp_generalledger.contraYN = 0
                                GROUP BY erp_generalledger.companySystemID, erp_generalledger.supplierCodeSystem,erp_generalledger.chartOfAccountSystemID,erp_generalledger.documentSystemID,erp_generalledger.documentSystemCode
                                ) AS MAINQUERY
                            LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = MAINQUERY.supplierCodeSystem
                            LEFT JOIN currencymaster as transCurrencyDet ON transCurrencyDet.currencyID=MAINQUERY.documentTransCurrencyID
                            LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=MAINQUERY.documentLocalCurrencyID
                            LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=MAINQUERY.documentRptCurrencyID) as finalAgingDetail 
                            LEFT JOIN currencymaster as supplierCurrency ON supplierCurrency.currencyID = finalAgingDetail.suppliercurrency
                            LEFT JOIN chartofaccounts as liabilityAccount ON liabilityAccount.chartOfAccountSystemID = finalAgingDetail.liabilityAccount
                            LEFT JOIN chartofaccounts as advanceAccount ON advanceAccount.chartOfAccountSystemID = finalAgingDetail.advanceAccount
                            WHERE ' . $whereQry . ' <> 0 
                            AND (
                                (finalAgingDetail.documentSystemID = 11 AND finalAgingDetail.liabilityAccount = finalAgingDetail.chartOfAccountSystemID) 
                                OR (finalAgingDetail.documentSystemID = 4 AND finalAgingDetail.advanceAccount = finalAgingDetail.chartOfAccountSystemID)
                                OR finalAgingDetail.documentSystemID = 15
                            )
                            ORDER BY finalAgingDetail.supplierCodeSystem ASC;');

        return $this->exchangeGainLoss($results, $currency);
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

        $currency = isset($request->currencyID) ? $request->currencyID : 3;

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
                                    AND erp_generalledger.contraYN = 0
                                ) AS MAINQUERY
                                ) AS paymentsBySupplierSummary
                                ORDER BY paymentsBySupplierSummary.documentRptAmount DESC');
            } else {
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
                                AND erp_generalledger.contraYN = 0 
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
                                AND erp_generalledger.contraYN = 0
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
                                        AND chartofaccounts.isBank = 0
                                        AND (erp_generalledger.supplierCodeSystem IS NULL 
                                        OR erp_generalledger.supplierCodeSystem = 0) -- hard code filers
                                        AND YEAR ( erp_generalledger.documentDate ) = "' . $year . '" 
                                        AND erp_generalledger.contraYN = 0
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
                    erp_paysupplierinvoicemaster.documentSystemID,
                    companymaster.CompanyName,
                    erp_paysupplierinvoicemaster.BPVcode,
                    erp_paysupplierinvoicemaster.BPVdate,
                    erp_paysupplierinvoicemaster.confirmedDate,
                    If(suppliermaster.primarySupplierCode Is Null,erp_paysupplierinvoicemaster.directPaymentPayee,suppliermaster.supplierName) as PayeeName,
                    suppliermaster.creditPeriod,
                    bank.bankName,
	                bankAct.AccountNo,
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
                    LEFT JOIN erp_bankmaster bank ON erp_paysupplierinvoicemaster.BPVbank = bank.bankmasterAutoID 
	                LEFT JOIN erp_bankaccount bankAct ON erp_paysupplierinvoicemaster.BPVAccount = bankAct.bankAccountAutoID
                    		WHERE 	erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyID) . ')
                    		AND  DATE_FORMAT(erp_paysupplierinvoicemaster.BPVdate,"%Y-%m-%d") BETWEEN "' . $fromDate . '" AND "' . $toDate . '" 
                    		AND erp_paysupplierinvoicemaster.confirmedYN=1';
            $output = \DB::select($qry);
        } else {
            $output = array();
        }

        return $output;

    }

    function getSupplierBalanceSummeryQRY($request)
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

        $suppliers = (array)$request->suppliers;
        $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();

        $controlAccountsSystemIDs = (array)$request->controlAccountsSystemID;
        $controlAccountsSystemID = collect($controlAccountsSystemIDs)->pluck('id')->toArray();

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
                        ' . $decimalPlaceQry . ',
                        chartofaccounts.AccountCode,
                        chartofaccounts.AccountDescription
                    FROM
                        erp_generalledger
                    LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                    LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
                    LEFT JOIN currencymaster AS localCurrencyDet ON localCurrencyDet.currencyID = erp_generalledger.documentLocalCurrencyID
                    LEFT JOIN currencymaster AS rptCurrencyDet ON rptCurrencyDet.currencyID = erp_generalledger.documentRptCurrencyID
                    LEFT JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                    WHERE
                        DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
                    AND erp_generalledger.chartOfAccountSystemID IN (' . join(',', $controlAccountsSystemID) . ')
                    AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                    AND erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                    GROUP BY
                        erp_generalledger.companySystemID,
                        erp_generalledger.chartOfAccountSystemID,
                        erp_generalledger.supplierCodeSystem;';

        return \DB::select($query);
    }

    function getSupplierAgingDetailQRY($request)
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

        $suppliers = (array)$request->suppliers;

        $type = isset($request->supEmpId[0]) ? $request->supEmpId[0]: $request->supEmpId;

        if($type == 1) {
            $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();
        } else {
            $supplierSystemID = collect($suppliers)->pluck('employeeSystemID')->toArray();
        }

        $controlAccountsSystemIDs = (array)$request->controlAccountsSystemID;
        $controlAccountsSystemID = collect($controlAccountsSystemIDs)->pluck('id')->toArray();


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
                    $agingField .= "if(grandFinal.ageDays > " . $through . ",if(grandFinal.balanceAmount < 0,grandFinal.balanceAmount,0),0) as `" . $val . "`,";
                } else {
                    $list = explode("-", $val);
                    $agingField .= "if(grandFinal.ageDays >= " . $list[0] . " AND grandFinal.ageDays <= " . $list[1] . ",if(grandFinal.balanceAmount < 0,grandFinal.balanceAmount,0),0) as `" . $val . "`,";
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
            $whereQry = "round( finalAgingDetail.balanceAmountTrans, finalAgingDetail.documentTransDecimalPlaces  )";
            $unAllocatedAmountQry = "if(finalAgingDetail.balanceAmountTrans>0,finalAgingDetail.balanceAmountTrans,0) as unAllocatedAmount";
        } else if ($currency == 2) {
            $currencyQry = "finalAgingDetail.localCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountLocal, finalAgingDetail.documentLocalDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountLocal, finalAgingDetail.documentLocalDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentLocalDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountLocal, finalAgingDetail.documentLocalDecimalPlaces  )";
            $unAllocatedAmountQry = "if(finalAgingDetail.balanceAmountLocal>0,finalAgingDetail.balanceAmountLocal,0) as unAllocatedAmount";
        } else {
            $currencyQry = "finalAgingDetail.rptCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountRpt, finalAgingDetail.documentRptDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountRpt, finalAgingDetail.documentRptDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentRptDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountRpt, finalAgingDetail.documentRptDecimalPlaces)";
            $unAllocatedAmountQry = "if(finalAgingDetail.balanceAmountRpt>0,finalAgingDetail.balanceAmountRpt,0) as unAllocatedAmount";
        }
        if($type == 1) {
            $typeSupplierQry = "erp_paysupplierinvoicedetail.supplierCodeSystem";
            $typeGeneralQry = "erp_generalledger.supplierCodeSystem";
            $typeQry = "LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = MAINQUERY.supplierCodeSystem";
            $typeSupEmpQry1 = "finalAgingDetail.supplierCodeSystem";
            $typeSupEmpQry2 = "finalAgingDetail.SupplierCode";
            $typeSupEmpQry3 = "finalAgingDetail.suppliername";
            $typeSupEmpQry4 = "CONCAT(finalAgingDetail.SupplierCode,' - ',finalAgingDetail.suppliername) as concatSupplierName";
            $typeSupEmpQryMain1 = "MAINQUERY.supplierCodeSystem";
            $typeSupEmpQryMain2 = "suppliermaster.primarySupplierCode";
            $typeSupEmpQryMain3 = "suppliermaster.suppliername";
        } else {
            $typeSupplierQry = "erp_paysupplierinvoicedetail.employeeSystemID";
            $typeGeneralQry = "erp_generalledger.employeeSystemID";
            $typeQry = "LEFT JOIN employees ON employees.employeeSystemID = MAINQUERY.employeeSystemID";
            $typeSupEmpQry1 = "finalAgingDetail.supplierCodeSystem";
            $typeSupEmpQry2 = "finalAgingDetail.SupplierCode";
            $typeSupEmpQry3 = "finalAgingDetail.suppliername";
            $typeSupEmpQry4 = "CONCAT(finalAgingDetail.SupplierCode,' - ',finalAgingDetail.suppliername) as concatSupplierName";
            $typeSupEmpQryMain1 = "MAINQUERY.employeeSystemID as supplierCodeSystem";
            $typeSupEmpQryMain2 = "employees.empID";
            $typeSupEmpQryMain3 = "employees.empName as suppliername";
        }

        $output =  \DB::select('SELECT *,' . $agingField . ' FROM (SELECT
                                finalAgingDetail.companySystemID,
                                finalAgingDetail.companyID,
                                finalAgingDetail.CompanyName,
                                finalAgingDetail.documentSystemID,
                                finalAgingDetail.documentSystemCode,
                                finalAgingDetail.documentID,
                                finalAgingDetail.documentCode,
                                finalAgingDetail.documentDate,
                                finalAgingDetail.documentNarration,
                                '.$typeSupEmpQry1.',
                                '.$typeSupEmpQry2.',
                                '.$typeSupEmpQry3.',
                                finalAgingDetail.invoiceNumber,
                                finalAgingDetail.invoiceDate,
                                CURDATE() as runDate,
                                DATEDIFF("' . $asOfDate . '",DATE(finalAgingDetail.documentDate)) as ageDays,
                                ' . $invoiceAmountQry . ',
                                ' . $balanceAmountQry . ',
                                ' . $unAllocatedAmountQry . ',
                                ' . $currencyQry . ',
                                ' . $decimalPlaceQry . ',
                                ' . $typeSupEmpQry4 . ',
                                finalAgingDetail.glCode,
                                finalAgingDetail.AccountDescription
                            FROM
                            (
                            SELECT
                                MAINQUERY.companySystemID,
                                MAINQUERY.companyID,
                                companymaster.CompanyName,
                                MAINQUERY.documentSystemID,
                                MAINQUERY.documentSystemCode,
                                MAINQUERY.documentID,
                                MAINQUERY.documentCode,
                                MAINQUERY.documentDate,
                                MAINQUERY.documentNarration,
                                '.$typeSupEmpQryMain1.',
                                '.$typeSupEmpQryMain2.' AS SupplierCode,
                                '.$typeSupEmpQryMain3.',
                                MAINQUERY.invoiceNumber,
                                MAINQUERY.invoiceDate,
                                transCurrencyDet.CurrencyCode as transCurrencyCode,
                                transCurrencyDet.DecimalPlaces as documentTransDecimalPlaces,
                                MAINQUERY.docTransAmount * -1 AS documentAmountTrans,
                                localCurrencyDet.CurrencyCode as localCurrencyCode,
                                localCurrencyDet.DecimalPlaces as documentLocalDecimalPlaces,
                                MAINQUERY.docLocalAmount * -1 AS documentAmountLocal,
                                rptCurrencyDet.CurrencyCode as rptCurrencyCode,
                                rptCurrencyDet.DecimalPlaces as documentRptDecimalPlaces,
                                MAINQUERY.docRptAmount * -1 AS documentAmountRpt,
                            
                                (MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans) * -1 AS PaidAmountTrans,
                            
                                (MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal) * -1 AS PaidAmountLocal,
                            
                                (MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt) * -1 AS PaidAmountRpt,
                            
                                (MAINQUERY.docTransAmount+MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans) * -1  as balanceAmountTrans,
                            
                                (MAINQUERY.docLocalAmount+MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal) * -1  as balanceAmountLocal,
                            
                                (MAINQUERY.docRptAmount + MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt) * -1 AS balanceAmountRpt,
                                MAINQUERY.glCode,
                                chartofaccounts.AccountDescription
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
                                erp_generalledger.employeeSystemID,
                                erp_generalledger.documentTransCurrencyID,
                                sum(erp_generalledger.documentTransAmount) * - 1 AS docTransAmount,
                                erp_generalledger.documentLocalCurrencyID,
                                sum(erp_generalledger.documentLocalAmount) * - 1 AS docLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                sum(erp_generalledger.documentRptAmount) * - 1 AS docRptAmount,
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
                                AND erp_paysupplierinvoicemaster.approved = -1
                                AND erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyID) . ')
                                AND DATE(erp_paysupplierinvoicemaster.postedDate) <= "' . $asOfDate . '"
                                AND '.$typeSupplierQry.' IN (' . join(',', $supplierSystemID) . ')
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
                                AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND '.$typeSupplierQry.' IN (' . join(',', $supplierSystemID) . ')
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
                                AND erp_paysupplierinvoicemaster.approved = -1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND '.$typeSupplierQry.' IN (' . join(',', $supplierSystemID) . ')
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
                                AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND '.$typeSupplierQry.' IN (' . join(',', $supplierSystemID) . ')
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
                                AND erp_generalledger.chartOfAccountSystemID IN (' . join(',', $controlAccountsSystemID) . ')
                                AND '.$typeGeneralQry.' IN (' . join(',', $supplierSystemID) . ')
                                AND erp_generalledger.contraYN = 0
                                GROUP BY erp_generalledger.companySystemID, erp_generalledger.supplierCodeSystem,erp_generalledger.chartOfAccountSystemID,erp_generalledger.documentSystemID,erp_generalledger.documentSystemCode
                                ) AS MAINQUERY
                            LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = MAINQUERY.chartOfAccountSystemID
                            LEFT JOIN companymaster ON MAINQUERY.companySystemID = companymaster.companySystemID
                            '.$typeQry.'
                            LEFT JOIN currencymaster as transCurrencyDet ON transCurrencyDet.currencyID=MAINQUERY.documentTransCurrencyID
                            LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=MAINQUERY.documentLocalCurrencyID
                            LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=MAINQUERY.documentRptCurrencyID) as finalAgingDetail WHERE ' . $whereQry . ' <> 0 ORDER BY documentDate ASC) as grandFinal;');



        $output = $this->exchangeGainLoss($output, $currency);


        return ['data' => $output, 'aging' => $aging];
    }

    function getSupplierAgingSummaryQRY($request)
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

        $suppliers = (array)$request->suppliers;

        $type = isset($request->supEmpId[0]) ? $request->supEmpId[0]: $request->supEmpId;
        if($type == 1) {
            $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();
        } else {
            $supplierSystemID = collect($suppliers)->pluck('employeeSystemID')->toArray();
        }

        $controlAccountsSystemIDs = (array)$request->controlAccountsSystemID;
        $controlAccountsSystemID = collect($controlAccountsSystemIDs)->pluck('id')->toArray();

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
                    $agingField .= "SUM(if(grandFinal.ageDays > " . $through . ",if(grandFinal.balanceAmount < 0,grandFinal.balanceAmount,0),0)) as `" . $val . "`,";
                } else {
                    $list = explode("-", $val);
                    $agingField .= "SUM(if(grandFinal.ageDays >= " . $list[0] . " AND grandFinal.ageDays <= " . $list[1] . ",if(grandFinal.balanceAmount < 0,grandFinal.balanceAmount,0),0)) as `" . $val . "`,";
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
            $unAllocatedAmountQry = "if(finalAgingDetail.balanceAmountTrans>0,finalAgingDetail.balanceAmountTrans,0) as unAllocatedAmount";
        } else if ($currency == 2) {
            $currencyQry = "finalAgingDetail.localCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountLocal, finalAgingDetail.documentLocalDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountLocal, finalAgingDetail.documentLocalDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentLocalDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountLocal, finalAgingDetail.documentLocalDecimalPlaces )";
            $unAllocatedAmountQry = "if(finalAgingDetail.balanceAmountLocal>0,finalAgingDetail.balanceAmountLocal,0) as unAllocatedAmount";
        } else {
            $currencyQry = "finalAgingDetail.rptCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountRpt, finalAgingDetail.documentRptDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountRpt, finalAgingDetail.documentRptDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentRptDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountRpt, finalAgingDetail.documentRptDecimalPlaces )";
            $unAllocatedAmountQry = "if(finalAgingDetail.balanceAmountRpt>0,finalAgingDetail.balanceAmountRpt,0) as unAllocatedAmount";
        }

        if($type == 1) {
            $typeSupplierQry = "erp_paysupplierinvoicedetail.supplierCodeSystem";
            $typeGeneralQry = "erp_generalledger.supplierCodeSystem";
            $typeQry = "LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = MAINQUERY.supplierCodeSystem";
            $typeSupEmpQry1 = "finalAgingDetail.supplierCodeSystem";
            $typeSupEmpQry2 = "finalAgingDetail.SupplierCode";
            $typeSupEmpQry3 = "finalAgingDetail.suppliername";
            $typeSupEmpQry4 = "CONCAT(finalAgingDetail.SupplierCode,' - ',finalAgingDetail.suppliername) as concatSupplierName";
            $typeSupEmpQryMain1 = "MAINQUERY.supplierCodeSystem";
            $typeSupEmpQryMain2 = "suppliermaster.primarySupplierCode";
            $typeSupEmpQryMain3 = "suppliermaster.suppliername";
            $typeCreditPeriod = "suppliermaster.creditPeriod";
            $typeAgeCreditPeriod = "finalAgingDetail.creditPeriod";
        } else {
            $typeSupplierQry = "erp_paysupplierinvoicedetail.employeeSystemID";
            $typeGeneralQry = "erp_generalledger.employeeSystemID";
            $typeQry = "LEFT JOIN employees ON employees.employeeSystemID = MAINQUERY.employeeSystemID";
            $typeSupEmpQry1 = "finalAgingDetail.supplierCodeSystem";
            $typeSupEmpQry2 = "finalAgingDetail.SupplierCode";
            $typeSupEmpQry3 = "finalAgingDetail.suppliername";
            $typeSupEmpQry4 = "CONCAT(finalAgingDetail.SupplierCode,' - ',finalAgingDetail.suppliername) as concatSupplierName";
            $typeSupEmpQryMain1 = "MAINQUERY.employeeSystemID as supplierCodeSystem";
            $typeSupEmpQryMain2 = "employees.empID";
            $typeSupEmpQryMain3 = "employees.empName as suppliername";
            $typeCreditPeriod = "'0' as creditPeriod";
            $typeAgeCreditPeriod = "'0' as creditPeriod";
        }

        $output = \DB::select('SELECT *,SUM(grandFinal.unAllocatedAmount) as unAllocatedAmount,' . $agingField . ' FROM (SELECT
                                finalAgingDetail.companySystemID,
                                finalAgingDetail.companyID,
                                finalAgingDetail.CompanyName,
                                finalAgingDetail.documentSystemID,
                                finalAgingDetail.documentID,
                                finalAgingDetail.documentCode,
                                finalAgingDetail.documentSystemCode,
                                finalAgingDetail.documentDate,
                                finalAgingDetail.documentNarration,
                                '.$typeSupEmpQry1.',
                                '.$typeSupEmpQry2.',
                                '.$typeSupEmpQry3.',
                                '.$typeAgeCreditPeriod.',
                                finalAgingDetail.invoiceNumber,
                                finalAgingDetail.invoiceDate,
                                CURDATE() as runDate,
                                DATEDIFF("' . $asOfDate . '",DATE(finalAgingDetail.documentDate)) as ageDays,
                                ' . $invoiceAmountQry . ',
                                ' . $balanceAmountQry . ',
                                ' . $unAllocatedAmountQry . ',
                                ' . $currencyQry . ',
                                ' . $decimalPlaceQry . ',
                                ' . $typeSupEmpQry4 . ',
                                CONCAT(finalAgingDetail.companyID," - ",finalAgingDetail.CompanyName) as concatCompanyName,
                                finalAgingDetail.glCode,
                                finalAgingDetail.AccountDescription,
                                finalAgingDetail.chartOfAccountSystemID
                            FROM
                            (
                            SELECT
                                MAINQUERY.companySystemID,
                                MAINQUERY.companyID,
                                companymaster.CompanyName,
                                MAINQUERY.documentSystemID,
                                MAINQUERY.documentID,
                                MAINQUERY.documentCode,
                                MAINQUERY.documentSystemCode,
                                MAINQUERY.documentDate,
                                MAINQUERY.documentNarration,
                                '.$typeSupEmpQryMain1.',
                                '.$typeSupEmpQryMain2.' AS SupplierCode,
                                '.$typeSupEmpQryMain3.',
                                '.$typeCreditPeriod.',
                                MAINQUERY.invoiceNumber,
                                MAINQUERY.invoiceDate,
                                transCurrencyDet.CurrencyCode as transCurrencyCode,
                                transCurrencyDet.DecimalPlaces as documentTransDecimalPlaces,
                                MAINQUERY.docTransAmount  * -1 AS documentAmountTrans,
                                localCurrencyDet.CurrencyCode as localCurrencyCode,
                                localCurrencyDet.DecimalPlaces as documentLocalDecimalPlaces,
                                MAINQUERY.docLocalAmount  * -1 AS documentAmountLocal,
                                rptCurrencyDet.CurrencyCode as rptCurrencyCode,
                                rptCurrencyDet.DecimalPlaces as documentRptDecimalPlaces,
                                MAINQUERY.docRptAmount  * -1 AS documentAmountRpt,
                            
                                (MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans) * -1 AS PaidAmountTrans,
                            
                                (MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal) * -1 AS PaidAmountLocal,
                            
                                (MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt) * -1 AS PaidAmountRpt,
                            
                                (MAINQUERY.docTransAmount+MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans) * -1  as balanceAmountTrans,
                            
                                (MAINQUERY.docLocalAmount+MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal) * -1  as balanceAmountLocal,
                            
                                (MAINQUERY.docRptAmount + MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt) * -1 AS balanceAmountRpt,
                                MAINQUERY.glCode,
                                chartofaccounts.AccountDescription,
                                chartofaccounts.chartOfAccountSystemID
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
                                erp_generalledger.employeeSystemID,
                                erp_generalledger.documentTransCurrencyID,
                                SUM(erp_generalledger.documentTransAmount) * - 1 AS docTransAmount,
                                erp_generalledger.documentLocalCurrencyID,
                                SUM(erp_generalledger.documentLocalAmount) * - 1 AS docLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                SUM(erp_generalledger.documentRptAmount) * - 1 AS docRptAmount,
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
                                AND erp_paysupplierinvoicemaster.approved = -1
                                AND erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyID) . ')
                                AND '.$typeSupplierQry.' IN (' . join(',', $supplierSystemID) . ')
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
                                AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND '.$typeSupplierQry.' IN (' . join(',', $supplierSystemID) . ')
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
                                AND erp_paysupplierinvoicemaster.approved = -1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND '.$typeSupplierQry.' IN (' . join(',', $supplierSystemID) . ')
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
                                AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND '.$typeSupplierQry.' IN (' . join(',', $supplierSystemID) . ')
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
                                AND erp_generalledger.chartOfAccountSystemID IN (' . join(',', $controlAccountsSystemID) . ')
                                AND '.$typeGeneralQry.' IN (' . join(',', $supplierSystemID) . ')
                                AND erp_generalledger.contraYN = 0
                                GROUP BY erp_generalledger.companySystemID, erp_generalledger.supplierCodeSystem,erp_generalledger.chartOfAccountSystemID,erp_generalledger.documentSystemID,erp_generalledger.documentSystemCode
                                ) AS MAINQUERY
                            '.$typeQry.'
                            LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = MAINQUERY.chartOfAccountSystemID
                            LEFT JOIN companymaster ON MAINQUERY.companySystemID = companymaster.companySystemID
                            LEFT JOIN currencymaster as transCurrencyDet ON transCurrencyDet.currencyID=MAINQUERY.documentTransCurrencyID
                            LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=MAINQUERY.documentLocalCurrencyID
                            LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=MAINQUERY.documentRptCurrencyID) as finalAgingDetail WHERE ' . $whereQry . ' <> 0 ORDER BY documentDate ASC) as grandFinal GROUP BY supplierCodeSystem,companyID, chartOfAccountSystemID ORDER BY suppliername;');

        $output = $this->exchangeGainLoss($output, $currency);

        return ['data' => $output, 'aging' => $aging];
    }

    function getSupplierAgingDetailAdvanceQRY($request)
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

        $suppliers = (array)$request->suppliers;
        $type = isset($request->supEmpId[0]) ? $request->supEmpId[0]: $request->supEmpId;
        if($type == 1) {
            $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();
        } else {
            $supplierSystemID = collect($suppliers)->pluck('employeeSystemID')->toArray();
        }

        $controlAccountsSystemIDs = (array)$request->controlAccountsSystemID;
        $controlAccountsSystemID = collect($controlAccountsSystemIDs)->pluck('id')->toArray();

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

        if($type == 1) {
            $typeSupplierQry = "erp_paysupplierinvoicedetail.supplierCodeSystem";
            $typeGeneralQry = "erp_generalledger.supplierCodeSystem";
            $typeQry = "LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = MAINQUERY.supplierCodeSystem";
            $typeSupEmpQry1 = "finalAgingDetail.supplierCodeSystem";
            $typeSupEmpQry2 = "finalAgingDetail.SupplierCode";
            $typeSupEmpQry3 = "finalAgingDetail.suppliername";
            $typeSupEmpQry4 = "CONCAT(finalAgingDetail.SupplierCode,' - ',finalAgingDetail.suppliername) as concatSupplierName";
            $typeSupEmpQryMain1 = "MAINQUERY.supplierCodeSystem";
            $typeSupEmpQryMain2 = "suppliermaster.primarySupplierCode";
            $typeSupEmpQryMain3 = "suppliermaster.suppliername";
        } else {
            $typeSupplierQry = "erp_paysupplierinvoicedetail.employeeSystemID";
            $typeGeneralQry = "erp_generalledger.employeeSystemID";
            $typeQry = "LEFT JOIN employees ON employees.employeeSystemID = MAINQUERY.employeeSystemID";
            $typeSupEmpQry1 = "finalAgingDetail.supplierCodeSystem";
            $typeSupEmpQry2 = "finalAgingDetail.SupplierCode";
            $typeSupEmpQry3 = "finalAgingDetail.suppliername";
            $typeSupEmpQry4 = "CONCAT(finalAgingDetail.SupplierCode,' - ',finalAgingDetail.suppliername) as concatSupplierName";
            $typeSupEmpQryMain1 = "MAINQUERY.employeeSystemID as supplierCodeSystem";
            $typeSupEmpQryMain2 = "employees.empID";
            $typeSupEmpQryMain3 = "employees.empName as suppliername";
        }

        $output = \DB::select('SELECT *,' . $agingField . ' FROM (SELECT
                                finalAgingDetail.companySystemID,
                                finalAgingDetail.companyID,
                                finalAgingDetail.CompanyName,
                                finalAgingDetail.documentSystemID,
                                finalAgingDetail.documentSystemCode,
                                finalAgingDetail.documentID,
                                finalAgingDetail.documentCode,
                                finalAgingDetail.documentDate,
                                finalAgingDetail.documentNarration,
                                '.$typeSupEmpQry1.',
                                '.$typeSupEmpQry2.',
                                '.$typeSupEmpQry3.',
                                finalAgingDetail.invoiceNumber,
                                finalAgingDetail.invoiceDate,
                                CURDATE() as runDate,
                                DATEDIFF("' . $asOfDate . '",DATE(finalAgingDetail.documentDate)) as ageDays,
                                ' . $invoiceAmountQry . ',
                                ' . $balanceAmountQry . ',
                                ' . $currencyQry . ',
                                ' . $decimalPlaceQry . ',
                                ' . $typeSupEmpQry4 . ',
                                finalAgingDetail.glCode,
                                finalAgingDetail.AccountDescription,
                                finalAgingDetail.chartOfAccountSystemID
                            FROM
                            (
                            SELECT
                                MAINQUERY.companySystemID,
                                MAINQUERY.companyID,
                                companymaster.CompanyName,
                                MAINQUERY.documentSystemID,
                                MAINQUERY.documentSystemCode,
                                MAINQUERY.documentID,
                                MAINQUERY.documentCode,
                                MAINQUERY.documentDate,
                                MAINQUERY.documentNarration,
                                '.$typeSupEmpQryMain1.',
                                '.$typeSupEmpQryMain2.' AS SupplierCode,
                                '.$typeSupEmpQryMain3.',
                                MAINQUERY.invoiceNumber,
                                MAINQUERY.invoiceDate,
                                transCurrencyDet.CurrencyCode as transCurrencyCode,
                                transCurrencyDet.DecimalPlaces as documentTransDecimalPlaces,
                                MAINQUERY.docTransAmount * -1 AS documentAmountTrans,
                                localCurrencyDet.CurrencyCode as localCurrencyCode,
                                localCurrencyDet.DecimalPlaces as documentLocalDecimalPlaces,
                                MAINQUERY.docLocalAmount * -1 AS documentAmountLocal,
                                rptCurrencyDet.CurrencyCode as rptCurrencyCode,
                                rptCurrencyDet.DecimalPlaces as documentRptDecimalPlaces,
                                MAINQUERY.docRptAmount * -1 AS documentAmountRpt,
                            
                                (MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans) * -1 AS PaidAmountTrans,
                            
                                (MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal) * -1 AS PaidAmountLocal,
                            
                                (MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt) * -1 AS PaidAmountRpt,
                            
                                (MAINQUERY.docTransAmount+MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans) * -1  as balanceAmountTrans,
                            
                                (MAINQUERY.docLocalAmount+MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal) * -1  as balanceAmountLocal,
                            
                                (MAINQUERY.docRptAmount + MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt) * -1 AS balanceAmountRpt,
                                MAINQUERY.glCode,
                                chartofaccounts.AccountDescription,
                                chartofaccounts.chartOfAccountSystemID
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
                                erp_generalledger.employeeSystemID,
                                erp_generalledger.documentTransCurrencyID,
                                SUM(erp_generalledger.documentTransAmount) * - 1 AS docTransAmount,
                                erp_generalledger.documentLocalCurrencyID,
                                SUM(erp_generalledger.documentLocalAmount) * - 1 AS docLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                SUM(erp_generalledger.documentRptAmount) * - 1 AS docRptAmount,
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
                                AND erp_paysupplierinvoicemaster.approved = -1
                                AND erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyID) . ')
                                AND '.$typeSupplierQry.' IN (' . join(',', $supplierSystemID) . ')
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
                                AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND '.$typeSupplierQry.' IN (' . join(',', $supplierSystemID) . ')
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
                                AND erp_paysupplierinvoicemaster.approved = -1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND '.$typeSupplierQry.' IN (' . join(',', $supplierSystemID) . ')
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
                                AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND '.$typeSupplierQry.' IN (' . join(',', $supplierSystemID) . ')
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
                                AND erp_generalledger.chartOfAccountSystemID IN (' . join(',', $controlAccountsSystemID) . ')
                                AND '.$typeGeneralQry.' IN (' . join(',', $supplierSystemID) . ')
                                AND erp_generalledger.contraYN = 0
                                GROUP BY erp_generalledger.companySystemID, erp_generalledger.supplierCodeSystem,erp_generalledger.chartOfAccountSystemID,erp_generalledger.documentSystemID,erp_generalledger.documentSystemCode
                                ) AS MAINQUERY
                            '.$typeQry.'
                            LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = MAINQUERY.chartOfAccountSystemID
                            LEFT JOIN companymaster ON MAINQUERY.companySystemID = companymaster.companySystemID
                            LEFT JOIN currencymaster as transCurrencyDet ON transCurrencyDet.currencyID=MAINQUERY.documentTransCurrencyID
                            LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=MAINQUERY.documentLocalCurrencyID
                            LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=MAINQUERY.documentRptCurrencyID) as finalAgingDetail WHERE ' . $whereQry . ' > 0 ORDER BY documentDate ASC) as grandFinal;');

        $output = $this->exchangeGainLoss($output, $currency);

        return ['data' => $output, 'aging' => $aging];
    }

    function getSupplierAgingSummaryAdvanceQRY($request)
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

        $suppliers = (array)$request->suppliers;
        $type = isset($request->supEmpId[0]) ? $request->supEmpId[0]: $request->supEmpId;
        if($type == 1) {
            $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();
        } else {
            $supplierSystemID = collect($suppliers)->pluck('employeeSystemID')->toArray();
        }
        $controlAccountsSystemIDs = (array)$request->controlAccountsSystemID;
        $controlAccountsSystemID = collect($controlAccountsSystemIDs)->pluck('id')->toArray();

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

        if($type == 1) {
            $typeSupplierQry = "erp_paysupplierinvoicedetail.supplierCodeSystem";
            $typeGeneralQry = "erp_generalledger.supplierCodeSystem";
            $typeQry = "LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = MAINQUERY.supplierCodeSystem";
            $typeSupEmpQry1 = "finalAgingDetail.supplierCodeSystem";
            $typeSupEmpQry2 = "finalAgingDetail.SupplierCode";
            $typeSupEmpQry3 = "finalAgingDetail.suppliername";
            $typeSupEmpQry4 = "CONCAT(finalAgingDetail.SupplierCode,' - ',finalAgingDetail.suppliername) as concatSupplierName";
            $typeSupEmpQryMain1 = "MAINQUERY.supplierCodeSystem";
            $typeSupEmpQryMain2 = "suppliermaster.primarySupplierCode";
            $typeSupEmpQryMain3 = "suppliermaster.suppliername";
            $typeCreditPeriod = "suppliermaster.creditPeriod";
            $typeAgeCreditPeriod = "finalAgingDetail.creditPeriod";
        } else {
            $typeSupplierQry = "erp_paysupplierinvoicedetail.employeeSystemID";
            $typeGeneralQry = "erp_generalledger.employeeSystemID";
            $typeQry = "LEFT JOIN employees ON employees.employeeSystemID = MAINQUERY.employeeSystemID";
            $typeSupEmpQry1 = "finalAgingDetail.supplierCodeSystem";
            $typeSupEmpQry2 = "finalAgingDetail.SupplierCode";
            $typeSupEmpQry3 = "finalAgingDetail.suppliername";
            $typeSupEmpQry4 = "CONCAT(finalAgingDetail.SupplierCode,' - ',finalAgingDetail.suppliername) as concatSupplierName";
            $typeSupEmpQryMain1 = "MAINQUERY.employeeSystemID as supplierCodeSystem";
            $typeSupEmpQryMain2 = "employees.empID";
            $typeSupEmpQryMain3 = "employees.empName as suppliername";
            $typeCreditPeriod = "'0' as creditPeriod";
            $typeAgeCreditPeriod = "'0' as creditPeriod";
        }

        $output = \DB::select('SELECT *, ' . $agingField . ' FROM (SELECT
                                finalAgingDetail.companySystemID,
                                finalAgingDetail.companyID,
                                finalAgingDetail.CompanyName,
                                finalAgingDetail.documentSystemID,
                                finalAgingDetail.documentID,
                                finalAgingDetail.documentCode,
                                finalAgingDetail.documentSystemCode,
                                finalAgingDetail.documentDate,
                                finalAgingDetail.documentNarration,
                                '.$typeSupEmpQry1.',
                                '.$typeSupEmpQry2.',
                                '.$typeSupEmpQry3.',
                                '.$typeAgeCreditPeriod.',
                                finalAgingDetail.invoiceNumber,
                                finalAgingDetail.invoiceDate,
                                CURDATE() as runDate,
                                DATEDIFF("' . $asOfDate . '",DATE(finalAgingDetail.documentDate)) as ageDays,
                                ' . $invoiceAmountQry . ',
                                ' . $balanceAmountQry . ',
                                ' . $currencyQry . ',
                                ' . $decimalPlaceQry . ',
                                ' . $typeSupEmpQry4 . ',
                                CONCAT(finalAgingDetail.companyID," - ",finalAgingDetail.CompanyName) as concatCompanyName,
                                finalAgingDetail.glCode,
                                finalAgingDetail.AccountDescription,
                                finalAgingDetail.chartOfAccountSystemID
                            FROM
                            (
                            SELECT
                                MAINQUERY.companySystemID,
                                MAINQUERY.companyID,
                                companymaster.CompanyName,
                                MAINQUERY.documentSystemID,
                                MAINQUERY.documentID,
                                MAINQUERY.documentCode,
                                MAINQUERY.documentSystemCode,
                                MAINQUERY.documentDate,
                                MAINQUERY.documentNarration,
                                '.$typeSupEmpQryMain1.',
                                '.$typeSupEmpQryMain2.' AS SupplierCode,
                                '.$typeSupEmpQryMain3.',
                                '.$typeCreditPeriod.',
                                MAINQUERY.invoiceNumber,
                                MAINQUERY.invoiceDate,
                                transCurrencyDet.CurrencyCode as transCurrencyCode,
                                transCurrencyDet.DecimalPlaces as documentTransDecimalPlaces,
                                MAINQUERY.docTransAmount * -1 AS documentAmountTrans,
                                localCurrencyDet.CurrencyCode as localCurrencyCode,
                                localCurrencyDet.DecimalPlaces as documentLocalDecimalPlaces,
                                MAINQUERY.docLocalAmount * -1 AS documentAmountLocal,
                                rptCurrencyDet.CurrencyCode as rptCurrencyCode,
                                rptCurrencyDet.DecimalPlaces as documentRptDecimalPlaces,
                                MAINQUERY.docRptAmount * -1 AS documentAmountRpt,
                            
                                (MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans) * -1 AS PaidAmountTrans,
                            
                                (MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal) * -1 AS PaidAmountLocal,
                            
                                (MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt) * -1 AS PaidAmountRpt,
                            
                                (MAINQUERY.docTransAmount+MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans) * -1  as balanceAmountTrans,
                            
                                (MAINQUERY.docLocalAmount+MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal) * -1  as balanceAmountLocal,
                            
                                (MAINQUERY.docRptAmount + MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt) * -1 AS balanceAmountRpt,
                                MAINQUERY.glCode,
                                chartofaccounts.AccountDescription,
                                chartofaccounts.chartOfAccountSystemID
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
                                erp_generalledger.employeeSystemID,
                                erp_generalledger.documentTransCurrencyID,
                                SUM(erp_generalledger.documentTransAmount) * - 1 AS docTransAmount,
                                erp_generalledger.documentLocalCurrencyID,
                                SUM(erp_generalledger.documentLocalAmount) * - 1 AS docLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                SUM(erp_generalledger.documentRptAmount) * - 1 AS docRptAmount,
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
                                AND erp_paysupplierinvoicemaster.approved = -1
                                AND erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyID) . ')
                                AND '.$typeSupplierQry.' IN (' . join(',', $supplierSystemID) . ')
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
                                AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND '.$typeSupplierQry.' IN (' . join(',', $supplierSystemID) . ')
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
                                AND erp_paysupplierinvoicemaster.approved = -1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND '.$typeSupplierQry.' IN (' . join(',', $supplierSystemID) . ')
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
                                AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                                AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
                                AND '.$typeSupplierQry.' IN (' . join(',', $supplierSystemID) . ')
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
                                AND erp_generalledger.chartOfAccountSystemID IN (' . join(',', $controlAccountsSystemID) . ')
                                AND '.$typeGeneralQry.' IN (' . join(',', $supplierSystemID) . ')
                                AND erp_generalledger.contraYN = 0
                                GROUP BY erp_generalledger.companySystemID, erp_generalledger.supplierCodeSystem,erp_generalledger.chartOfAccountSystemID,erp_generalledger.documentSystemID,erp_generalledger.documentSystemCode
                                ) AS MAINQUERY
                            '.$typeQry.'
                            LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = MAINQUERY.chartOfAccountSystemID
                            LEFT JOIN companymaster ON MAINQUERY.companySystemID = companymaster.companySystemID
                            LEFT JOIN currencymaster as transCurrencyDet ON transCurrencyDet.currencyID=MAINQUERY.documentTransCurrencyID
                            LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=MAINQUERY.documentLocalCurrencyID
                            LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=MAINQUERY.documentRptCurrencyID) as finalAgingDetail WHERE ' . $whereQry . ' > 0 ORDER BY documentDate ASC) as grandFinal GROUP BY supplierCodeSystem,companyID, chartOfAccountSystemID ORDER BY suppliername;');

        $output = $this->exchangeGainLoss($output, $currency);

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

        $year = $request->year;
        $companyWise = '';

        $reportTypeID = $request->reportTypeID;
        if ($reportTypeID == 'TSCW') {
            $companyWise = 'erp_purchaseordermaster.companySystemID,';
        }

        $countries = (array)$request->countries;
        $countrySystemID = collect($countries)->pluck('countryID')->toArray();
        $segmentFilter = '';
        $categoryFilter = '';
        if (isset($request->segments)) {
            $segments = (array)$request->segments;
            $serviceLineSystemID = collect($segments)->pluck('serviceLineSystemID')->toArray();

            if (sizeof($serviceLineSystemID) > 0) {
                $segmentFilter = 'AND erp_purchaseordermaster.serviceLineSystemID IN (' . join(',', $serviceLineSystemID) . ')';
            }
        }

         if (isset($request->selectedCategories)) {
            $selectedCategories = (array)$request->selectedCategories;
            $financeCategory = collect($selectedCategories)->pluck('id')->toArray();

            if (sizeof($financeCategory) > 0) {
                $categoryFilter = 'AND erp_purchaseordermaster.financeCategory IN (' . join(',', $financeCategory) . ')';
            }
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
                        AND erp_purchaseordermaster.poCancelledYN = 0 ' . $categoryFilter . '
                        AND poType_N <> 5 ' . $segmentFilter . '
                        AND YEAR ( erp_purchaseordermaster.approvedDate ) = ' . $year . ' 
                        AND erp_purchaseordermaster.companySystemID IN (' . join(',', $companyID) . ')
                        AND suppliermaster.supplierCountryID IN (' . join(',', $countrySystemID) . ')
                        AND suppliermaster.liabilityAccountSysemID = ' . $request->controlAccountsSystemID . ' 
                    GROUP BY
                        ' . $companyWise . '
                        erp_purchaseordermaster.supplierID 	Order BY Amount DESC;';
        return \DB::select($qry);
    }

    function getUnbilledDetailQRY($request)
    {
        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $controlAccountsSystemID = $request->controlAccountsSystemID;
        $localOrForeign = $request->localOrForeign;
        $reportTypeID = $request->reportTypeID;
        $asOfDate = new Carbon($request->fromDate);
        $asOfDate = $asOfDate->format('Y-m-d');

        $suppliers = (array)$request->suppliers;
        $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();

        $countryFilter = '';

        if ($localOrForeign == 2) {
            $countryFilter = 'AND countryID = ' . $checkIsGroup->companyCountry;
        } else if ($localOrForeign == 3) {
            $countryFilter = 'AND countryID != ' . $checkIsGroup->companyCountry;
        }

        $supplierGroup = "";
        $finalSelect = "final.*";

        if ($reportTypeID == 'UGRVS') {
            $supplierGroup = "GROUP BY final.supplierID,final.companySystemID";
            $finalSelect = "final.companySystemID,
                final.companyID,
                final.documentSystemID,
                final.documentID,
                final.documentCode,
                final.documentSystemCode,
                final.documentDate,
                final.supplierID,
                final.supplierCode,
                final.supplierName,
                final.documentRptCurrencyID,
                final.documentLocalCurrencyID,
                SUM(final.documentLocalAmount) as documentLocalAmount,
                SUM(final.documentRptAmount) as documentRptAmount,
                SUM(final.matchedLocalAmount) as matchedLocalAmount,
                SUM(final.matchedRptAmount) as matchedRptAmount,
                SUM(final.balanceLocalAmount) as balanceLocalAmount,
                SUM(final.balanceRptAmount) as balanceRptAmount";
        }

        $qry = 'SELECT ' . $finalSelect . ',
                suppliermaster.countryID FROM (SELECT
                finalUnbilled.companySystemID,
                finalUnbilled.companyID,
                finalUnbilled.documentSystemID,
                finalUnbilled.documentID,
                finalUnbilled.documentCode,
                finalUnbilled.documentSystemCode,
                docDate.documentDate,
                finalUnbilled.supplierID,
                finalUnbilled.supplierCode,
                finalUnbilled.supplierName,
                finalUnbilled.documentRptCurrencyID,
                finalUnbilled.documentLocalCurrencyID,
                finalUnbilled.localAmount AS documentLocalAmount,
                finalUnbilled.rptAmount AS documentRptAmount,
                   IF
                ( finalUnbilled.matchedLocalAmount IS NULL, 0, finalUnbilled.matchedLocalAmount ) AS matchedLocalAmount,
            IF
                ( finalUnbilled.matchedRptAmount IS NULL, 0, finalUnbilled.matchedRptAmount ) AS matchedRptAmount,
                round( ( finalUnbilled.localAmount - ( IF ( finalUnbilled.matchedLocalAmount IS NULL, 0, finalUnbilled.matchedLocalAmount ) ) ), 3 ) AS balanceLocalAmount,
                round( ( finalUnbilled.rptAmount - ( IF ( finalUnbilled.matchedRptAmount IS NULL, 0, finalUnbilled.matchedRptAmount ) ) ), 2 ) AS balanceRptAmount 
            FROM
                (
            SELECT
                erp_generalledger.companySystemID,
                erp_generalledger.companyID,
                erp_generalledger.glCode,
                erp_generalledger.documentID,
                erp_generalledger.documentSystemID,
                erp_generalledger.documentSystemCode,
                erp_generalledger.documentCode,
                erp_generalledger.documentRptCurrencyID,
                erp_generalledger.documentLocalCurrencyID,
                sum( erp_generalledger.documentLocalAmount * - 1 ) AS localAmount,
                sum( erp_generalledger.documentRptAmount * - 1 ) AS rptAmount,
                erp_generalledger.supplierCodeSystem AS supplierID,
                SupplierForGeneralLedger.primarySupplierCode AS supplierCode,
                SupplierForGeneralLedger.supplierName AS supplierName,
                MatchedGRVAndInvoice.totLocalAmount1 AS matchedLocalAmount,
                MatchedGRVAndInvoice.totRptAmount1 AS matchedRptAmount 
            FROM
                erp_generalledger
                LEFT JOIN erp_grvmaster ON erp_generalledger.companySystemID = erp_grvmaster.companySystemID 
                AND erp_generalledger.documentSystemID = erp_grvmaster.documentSystemID 
                AND erp_generalledger.documentSystemCode = erp_grvmaster.grvAutoID
                LEFT JOIN erp_bookinvsuppmaster ON erp_generalledger.documentSystemID = erp_bookinvsuppmaster.documentSystemID 
                AND erp_generalledger.companySystemID = erp_bookinvsuppmaster.companySystemID 
                AND erp_generalledger.documentSystemCode = erp_bookinvsuppmaster.bookingSuppMasInvAutoID
                LEFT JOIN erp_purchasereturnmaster ON erp_generalledger.documentSystemID = erp_purchasereturnmaster.documentSystemID
                AND erp_generalledger.companySystemID = erp_purchasereturnmaster.companySystemID
                AND erp_generalledger.documentSystemCode = erp_purchasereturnmaster.purhaseReturnAutoID
                LEFT JOIN suppliermaster AS SupplierForGeneralLedger ON erp_generalledger.supplierCodeSystem = SupplierForGeneralLedger.supplierCodeSystem
                LEFT JOIN (
                (
            SELECT
                erp_bookinvsuppdet.companySystemID,
                3 AS documentSystemID,
                erp_bookinvsuppdet.grvAutoID AS documentSystemCode,
                grvGL.documentCode,
                SUM( erp_bookinvsuppdet.totLocalAmount ) AS totLocalAmount1,
                SUM( erp_bookinvsuppdet.totRptAmount ) AS totRptAmount1 
            FROM
                erp_bookinvsuppdet
                INNER JOIN (
            SELECT
                companySystemID,
                documentSystemID,
                documentSystemCode,
                documentCode 
            FROM
                erp_generalledger 
            WHERE
                erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
                AND STR_TO_DATE( DATE_FORMAT( erp_generalledger.documentDate, "%d/%m/%Y" ), "%d/%m/%Y" ) <= "' . $asOfDate . '" 
                AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                AND erp_generalledger.documentSystemID = 11 
            GROUP BY
                companySystemID,
                documentSystemID,
                documentSystemCode 
                ) AS grvGL ON grvGL.companySystemID = erp_bookinvsuppdet.companySystemID 
                AND grvGL.documentSystemCode = erp_bookinvsuppdet.bookingSuppMasInvAutoID 
            GROUP BY
                erp_bookinvsuppdet.companySystemID,
                erp_bookinvsuppdet.grvAutoID 
                ) UNION ALL
                (
            SELECT
                erp_bookinvsuppdet.companySystemID,
                11 AS documentSystemID,
                erp_bookinvsuppdet.bookingSuppMasInvAutoID AS documentSystemCode,
                BsiGL.documentCode,
                SUM( erp_bookinvsuppdet.totLocalAmount * - 1 ) AS totLocalAmount1,
                SUM( erp_bookinvsuppdet.totRptAmount * - 1 ) AS totRptAmount1 
            FROM
                erp_bookinvsuppdet
                INNER JOIN (
            SELECT
                companySystemID,
                documentSystemID,
                documentSystemCode,
                documentCode 
            FROM
                erp_generalledger 
            WHERE
                erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                AND erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
                AND STR_TO_DATE( DATE_FORMAT( erp_generalledger.documentDate, "%d/%m/%Y" ), "%d/%m/%Y" ) <= "' . $asOfDate . '"
                AND erp_generalledger.documentSystemID = 11 
                ) AS BsiGL ON BsiGL.companySystemID = erp_bookinvsuppdet.companySystemID 
                AND BsiGL.documentSystemCode = erp_bookinvsuppdet.bookingSuppMasInvAutoID 
            GROUP BY
                erp_bookinvsuppdet.companySystemID,
                erp_bookinvsuppdet.bookingSuppMasInvAutoID 
                ) 
                ) AS MatchedGRVAndInvoice ON erp_generalledger.companySystemID = MatchedGRVAndInvoice.companySystemID 
                AND erp_generalledger.documentSystemID = MatchedGRVAndInvoice.documentSystemID 
                AND erp_generalledger.documentSystemCode = MatchedGRVAndInvoice.documentSystemCode 
            WHERE
                erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                AND erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
                AND erp_generalledger.contraYN = 0 
                AND STR_TO_DATE( DATE_FORMAT( erp_generalledger.documentDate, "%d/%m/%Y" ), "%d/%m/%Y" ) <= "' . $asOfDate . '"
            GROUP BY
                erp_generalledger.companySystemID,
                erp_generalledger.documentSystemID,
                erp_generalledger.documentSystemCode,
                erp_generalledger.supplierCodeSystem  
                ) AS finalUnbilled
                LEFT JOIN (
            SELECT
                companySystemID,
                documentSystemID,
                documentSystemCode,
                documentDate 
            FROM
                erp_generalledger 
            WHERE
                erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                AND erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
            GROUP BY
                companySystemID,
                documentSystemID,
                documentSystemCode 
                ) AS docDate ON docDate.companySystemID = finalUnbilled.companySystemID 
                AND docDate.documentSystemID = finalUnbilled.documentSystemID 
                AND docDate.documentSystemCode = finalUnbilled.documentSystemCode 
            WHERE
                (
                round( ( finalUnbilled.rptAmount - ( IF ( finalUnbilled.matchedRptAmount IS NULL, 0, finalUnbilled.matchedRptAmount ) ) ), 2 ) 
                ) <>0 ) as final 
                INNER JOIN suppliermaster ON suppliermaster.supplierCodeSystem = final.supplierID
                WHERE supplierID IN (' . join(',', $supplierSystemID) . ')' . $countryFilter . ' ' . $supplierGroup;

        $results = \DB::select($qry);

        foreach ($results as $index => $result) {
            $result->matchedLocalAmount = BookInvSuppDet::where('grvAutoID', $result->documentSystemCode)->where('companySystemID', $result->companySystemID)->where('supplierID', $result->supplierID)->sum('totLocalAmount');

            $result->matchedRptAmount = BookInvSuppDet::where('grvAutoID', $result->documentSystemCode)->where('companySystemID', $result->companySystemID)->where('supplierID', $result->supplierID)->sum('totRptAmount');


            $result->balanceLocalAmount = $result->documentLocalAmount - $result->matchedLocalAmount;
            $result->balanceRptAmount = $result->documentRptAmount - $result->matchedRptAmount;

            if (abs($result->balanceLocalAmount) < 0.00001 || abs($result->balanceRptAmount) < 0.00001) {
                unset($results[$index]);
            }
        }

        return array_values($results);
    }

    function getUnbilledLogisticsDetailQRY($request)
    {

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $asOfDate = new Carbon($request->fromDate);
        $asOfDate = $asOfDate->format('Y-m-d');

        $suppliers = (array)$request->suppliers;
        $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();

        $qry = 'SELECT
                erp_grvmaster.companyID,
                erp_grvdetails.purchaseOrderMastertID,
                erp_purchaseordermaster.purchaseOrderCode,
                erp_purchaseordermaster.documentSystemID as poDocumentSystemID,
                erp_grvmaster.grvPrimaryCode,
                erp_grvmaster.grvAutoID,
                erp_grvmaster.documentSystemID,
                erp_grvmaster.grvDate,
                suppliermaster.primarySupplierCode,
                suppliermaster.supplierName,
                currencymaster.CurrencyCode as TransactionCurrencyCode,
                currencymaster.DecimalPlaces as TransactionCurrencyDecimalPlaces,
                erp_unbilledgrvgroupby.totTransactionAmount AS LogisticAmountTransaction,
                currencymaster_1.CurrencyCode as RptCurrencyCode,
                currencymaster_1.DecimalPlaces as RptCurrencyDecimalPlaces,
                erp_unbilledgrvgroupby.totRptAmount AS LogisticAmountRpt,
                IFNULL(logisticGRV_BookingDetails.SumOftotTransactionAmount,0) AS PaidAmountTrans,
                IFNULL(logisticGRV_BookingDetails.SumOftotRptAmount,0) AS PaidAmountRpt,
                IFNULL(erp_unbilledgrvgroupby.totTransactionAmount-logisticGRV_BookingDetails.SumOftotTransactionAmount,0) as BalanceTransAmount,
                IFNULL(erp_unbilledgrvgroupby.totRptAmount-logisticGRV_BookingDetails.SumOftotRptAmount,0) as BalanceRptAmount
            FROM
                erp_grvmaster 
                INNER JOIN erp_grvdetails ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID
                LEFT JOIN  erp_purchaseordermaster ON erp_grvdetails.purchaseOrderMastertID = erp_purchaseordermaster.purchaseOrderID
                INNER JOIN erp_unbilledgrvgroupby ON erp_grvmaster.grvAutoID = erp_unbilledgrvgroupby.grvAutoID
                INNER JOIN suppliermaster ON erp_unbilledgrvgroupby.supplierID = suppliermaster.supplierCodeSystem 
                INNER JOIN currencymaster ON erp_unbilledgrvgroupby.supplierTransactionCurrencyID = currencymaster.currencyID 
                INNER JOIN currencymaster AS currencymaster_1 ON erp_unbilledgrvgroupby.companyReportingCurrencyID = currencymaster_1.currencyID 
                LEFT JOIN (
                                SELECT erp_bookinvsuppdet.unbilledgrvAutoID,
                                Sum(erp_bookinvsuppdet.totTransactionAmount) AS SumOftotTransactionAmount,
                                Sum(erp_bookinvsuppdet.totRptAmount) AS SumOftotRptAmount
                                FROM erp_bookinvsuppdet
                                GROUP BY erp_bookinvsuppdet.unbilledgrvAutoID
            ) AS logisticGRV_BookingDetails	ON erp_unbilledgrvgroupby.unbilledgrvAutoID = logisticGRV_BookingDetails.unbilledgrvAutoID 
            WHERE
                erp_grvmaster.grvConfirmedYN = 1
                AND erp_grvmaster.approved =- 1  
                AND erp_grvmaster.grvTotalSupplierTransactionCurrency > 0  
                AND erp_grvdetails.logisticsChargest_RptCur > 0  
                AND erp_unbilledgrvgroupby.logisticYN = 1  
                AND erp_grvmaster.companySystemID IN (' . join(',', $companyID) . ')
                AND STR_TO_DATE( DATE_FORMAT( erp_grvmaster.grvDate, "%d/%m/%Y" ), "%d/%m/%Y" ) <= "' . $asOfDate . '"
                AND erp_unbilledgrvgroupby.supplierID IN (' . join(',', $supplierSystemID) . ')
                GROUP BY
                erp_unbilledgrvgroupby.unbilledgrvAutoID,
                erp_grvmaster.grvAutoID,
                erp_grvmaster.companySystemID;';
        return  \DB::select($qry);
    }


    function getUnbilledGRVDetailAgingQRY($request)
    {
        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $controlAccountsSystemID = $request->controlAccountsSystemID;
        $localOrForeign = $request->localOrForeign;
        $currencyID = $request->currencyID;
        $asOfDate = new Carbon($request->fromDate);
        $asOfDate = $asOfDate->format('Y-m-d');

        $suppliers = (array)$request->suppliers;
        $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();

        $countryFilter = '';

        if ($localOrForeign == 2) {
            $countryFilter = 'AND countryID = ' . $checkIsGroup->companyCountry;
        } else if ($localOrForeign == 3) {
            $countryFilter = 'AND countryID != ' . $checkIsGroup->companyCountry;
        }

        $supplierGroup = "";
        $finalSelect = "final.*";
        $caseColumn = 'balanceRptAmount';

        if ($currencyID == 2) {
            $caseColumn = 'balanceLocalAmount';
        }

        $aging = ['0-30', '31-60', '61-90', '91-120', '121-150', '151-180', '181-210', '211-240', '241-365', '> 365'];
        $agingField = '';
        if (!empty($aging)) { /*calculate aging range in query*/
            $count = count($aging);
            $c = 1;
            foreach ($aging as $val) {
                if ($count == $c) {
                    $agingField .= "if(agingFinal.ageDays   > " . 365 . ",agingFinal." . $caseColumn . ",0) as `case" . $c . "`,";
                } else {
                    $list = explode("-", $val);
                    $agingField .= "if(agingFinal.ageDays >= " . $list[0] . " AND agingFinal.ageDays <= " . $list[1] . ",agingFinal." . $caseColumn . ",0) as `case" . $c . "`,";
                }
                $c++;
            }
        }

        $agingField .= "if(agingFinal.ageDays <= 0,agingFinal." . $caseColumn . ",0) as `current`";

        $qry = 'SELECT *,' . $agingField . ' FROM (SELECT ' . $finalSelect . ',
                suppliermaster.countryID FROM (SELECT
                finalUnbilled.companySystemID,
                finalUnbilled.companyID,
                finalUnbilled.documentSystemID,
                finalUnbilled.documentID,
                finalUnbilled.documentCode,
                finalUnbilled.documentSystemCode,
                docDate.documentDate,
                finalUnbilled.supplierID,
                finalUnbilled.supplierCode,
                finalUnbilled.supplierName,
                finalUnbilled.localAmount AS documentLocalAmount,
                finalUnbilled.rptAmount AS documentRptAmount,
            IF
                ( finalUnbilled.matchedLocalAmount IS NULL, 0, finalUnbilled.matchedLocalAmount ) AS matchedLocalAmount,
            IF
                ( finalUnbilled.matchedRptAmount IS NULL, 0, finalUnbilled.matchedRptAmount ) AS matchedRptAmount,
                round( ( finalUnbilled.localAmount - ( IF ( finalUnbilled.matchedLocalAmount IS NULL, 0, finalUnbilled.matchedLocalAmount ) ) ), 3 ) AS balanceLocalAmount,
                round( ( finalUnbilled.rptAmount - ( IF ( finalUnbilled.matchedRptAmount IS NULL, 0, finalUnbilled.matchedRptAmount ) ) ), 2 ) AS balanceRptAmount ,
                DATEDIFF("' . $asOfDate . '",DATE(docDate.documentDate)) as ageDays
            FROM
                (
            SELECT
                erp_generalledger.companySystemID,
                erp_generalledger.companyID,
                erp_generalledger.glCode,
                erp_generalledger.documentID,
                erp_generalledger.documentSystemID,
                erp_generalledger.documentSystemCode,
                erp_generalledger.documentCode,
                sum( erp_generalledger.documentLocalAmount * - 1 ) AS localAmount,
                sum( erp_generalledger.documentRptAmount * - 1 ) AS rptAmount,
                erp_generalledger.supplierCodeSystem AS supplierID,
                SupplierForGeneralLedger.primarySupplierCode AS supplierCode,
                SupplierForGeneralLedger.supplierName AS supplierName,
                MatchedGRVAndInvoice.totLocalAmount1 AS matchedLocalAmount,
                MatchedGRVAndInvoice.totRptAmount1 AS matchedRptAmount 
            FROM
                erp_generalledger
                LEFT JOIN erp_grvmaster ON erp_generalledger.companySystemID = erp_grvmaster.companySystemID 
                AND erp_generalledger.documentSystemID = erp_grvmaster.documentSystemID 
                AND erp_generalledger.documentSystemCode = erp_grvmaster.grvAutoID
                LEFT JOIN erp_bookinvsuppmaster ON erp_generalledger.documentSystemID = erp_bookinvsuppmaster.documentSystemID 
                AND erp_generalledger.companySystemID = erp_bookinvsuppmaster.companySystemID 
                AND erp_generalledger.documentSystemCode = erp_bookinvsuppmaster.bookingSuppMasInvAutoID
                LEFT JOIN erp_purchasereturnmaster ON erp_generalledger.documentSystemID = erp_purchasereturnmaster.documentSystemID
                AND erp_generalledger.companySystemID = erp_purchasereturnmaster.companySystemID
                AND erp_generalledger.documentSystemCode = erp_purchasereturnmaster.purhaseReturnAutoID
                LEFT JOIN suppliermaster AS SupplierForGeneralLedger ON erp_generalledger.supplierCodeSystem = SupplierForGeneralLedger.supplierCodeSystem
                LEFT JOIN (
                (
            SELECT
                erp_bookinvsuppdet.companySystemID,
                3 AS documentSystemID,
                erp_bookinvsuppdet.grvAutoID AS documentSystemCode,
                grvGL.documentCode,
                SUM( erp_bookinvsuppdet.totLocalAmount ) AS totLocalAmount1,
                SUM( erp_bookinvsuppdet.totRptAmount ) AS totRptAmount1 
            FROM
                erp_bookinvsuppdet
                INNER JOIN (
            SELECT
                companySystemID,
                documentSystemID,
                documentSystemCode,
                documentCode 
            FROM
                erp_generalledger 
            WHERE
                erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
                AND STR_TO_DATE( DATE_FORMAT( erp_generalledger.documentDate, "%d/%m/%Y" ), "%d/%m/%Y" ) <= "' . $asOfDate . '" 
                AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                AND erp_generalledger.documentSystemID = 11 
            GROUP BY
                companySystemID,
                documentSystemID,
                documentSystemCode 
                ) AS grvGL ON grvGL.companySystemID = erp_bookinvsuppdet.companySystemID 
                AND grvGL.documentSystemCode = erp_bookinvsuppdet.bookingSuppMasInvAutoID 
            GROUP BY
                erp_bookinvsuppdet.companySystemID,
                erp_bookinvsuppdet.grvAutoID 
                ) UNION ALL
                (
            SELECT
                erp_bookinvsuppdet.companySystemID,
                11 AS documentSystemID,
                erp_bookinvsuppdet.bookingSuppMasInvAutoID AS documentSystemCode,
                BsiGL.documentCode,
                SUM( erp_bookinvsuppdet.totLocalAmount * - 1 ) AS totLocalAmount1,
                SUM( erp_bookinvsuppdet.totRptAmount * - 1 ) AS totRptAmount1 
            FROM
                erp_bookinvsuppdet
                INNER JOIN (
            SELECT
                companySystemID,
                documentSystemID,
                documentSystemCode,
                documentCode 
            FROM
                erp_generalledger 
            WHERE
                erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                AND erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
                AND STR_TO_DATE( DATE_FORMAT( erp_generalledger.documentDate, "%d/%m/%Y" ), "%d/%m/%Y" ) <= "' . $asOfDate . '"
                AND erp_generalledger.documentSystemID = 11 
                ) AS BsiGL ON BsiGL.companySystemID = erp_bookinvsuppdet.companySystemID 
                AND BsiGL.documentSystemCode = erp_bookinvsuppdet.bookingSuppMasInvAutoID 
            GROUP BY
                erp_bookinvsuppdet.companySystemID,
                erp_bookinvsuppdet.bookingSuppMasInvAutoID 
                ) 
                ) AS MatchedGRVAndInvoice ON erp_generalledger.companySystemID = MatchedGRVAndInvoice.companySystemID 
                AND erp_generalledger.documentSystemID = MatchedGRVAndInvoice.documentSystemID 
                AND erp_generalledger.documentSystemCode = MatchedGRVAndInvoice.documentSystemCode 
            WHERE
                erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                AND erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
                AND erp_generalledger.contraYN = 0 
                AND STR_TO_DATE( DATE_FORMAT( erp_generalledger.documentDate, "%d/%m/%Y" ), "%d/%m/%Y" ) <= "' . $asOfDate . '"
            GROUP BY
                erp_generalledger.companySystemID,
                erp_generalledger.documentSystemID,
                erp_generalledger.documentSystemCode,
                erp_generalledger.supplierCodeSystem 
                ) AS finalUnbilled
                LEFT JOIN (
            SELECT
                companySystemID,
                documentSystemID,
                documentSystemCode,
                documentDate 
            FROM
                erp_generalledger 
            WHERE
                erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                AND erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
            GROUP BY
                companySystemID,
                documentSystemID,
                documentSystemCode 
                ) AS docDate ON docDate.companySystemID = finalUnbilled.companySystemID 
                AND docDate.documentSystemID = finalUnbilled.documentSystemID 
                AND docDate.documentSystemCode = finalUnbilled.documentSystemCode 
            WHERE
                (
                round( ( finalUnbilled.rptAmount - ( IF ( finalUnbilled.matchedRptAmount IS NULL, 0, finalUnbilled.matchedRptAmount ) ) ), 2 ) 
                ) <>0 ) as final 
                INNER JOIN suppliermaster ON suppliermaster.supplierCodeSystem = final.supplierID
                WHERE supplierID IN (' . join(',', $supplierSystemID) . ')' . $countryFilter . ' ' . $supplierGroup . ') as agingFinal';

        $results = \DB::select($qry);

        foreach ($results as $index => $result) {
            $result->matchedLocalAmount = BookInvSuppDet::where('grvAutoID', $result->documentSystemCode)->where('companySystemID', $result->companySystemID)->where('supplierID', $result->supplierID)->sum('totLocalAmount');

            $result->matchedRptAmount = BookInvSuppDet::where('grvAutoID', $result->documentSystemCode)->where('companySystemID', $result->companySystemID)->where('supplierID', $result->supplierID)->sum('totRptAmount');


            $result->balanceLocalAmount = $result->documentLocalAmount - $result->matchedLocalAmount;
            $result->balanceRptAmount = $result->documentRptAmount - $result->matchedRptAmount;

            if (abs($result->balanceLocalAmount) < 0.00001 || abs($result->balanceRptAmount) < 0.00001) {
                unset($results[$index]);
            }
        }

        return array_values($results);
    }


    function getUnbilledGRVSummaryAgingQRY($request)
    {
        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $controlAccountsSystemID = $request->controlAccountsSystemID;
        $localOrForeign = $request->localOrForeign;
        $reportTypeID = $request->reportTypeID;
        $currencyID = $request->currencyID;
        $asOfDate = new Carbon($request->fromDate);
        $asOfDate = $asOfDate->format('Y-m-d');

        $suppliers = (array)$request->suppliers;
        $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();

        $countryFilter = '';

        if ($localOrForeign == 2) {
            $countryFilter = 'AND countryID = ' . $checkIsGroup->companyCountry;
        } else if ($localOrForeign == 3) {
            $countryFilter = 'AND countryID != ' . $checkIsGroup->companyCountry;
        }

        $supplierGroup = "";
        $finalSelect = "agingFinal.*";

        if ($reportTypeID == 'UGRVAS') {
            $supplierGroup = "GROUP BY supplierID,companySystemID";
            $finalSelect = "agingFinal.companySystemID,
                agingFinal.companyID,
                agingFinal.documentSystemID,
                agingFinal.documentID,
                agingFinal.documentCode,
                agingFinal.documentSystemCode,
                agingFinal.documentDate,
                agingFinal.supplierID,
                agingFinal.supplierCode,
                agingFinal.supplierName,
                SUM(agingFinal.documentLocalAmount) as documentLocalAmount,
                SUM(agingFinal.documentRptAmount) as documentRptAmount,
                SUM(agingFinal.matchedLocalAmount) as matchedLocalAmount,
                SUM(agingFinal.matchedRptAmount) as matchedRptAmount,
                SUM(agingFinal.balanceLocalAmount) as balanceLocalAmount,
                SUM(agingFinal.balanceRptAmount) as balanceRptAmount";
        }

        $caseColumn = 'balanceRptAmount';

        if ($currencyID == 2) {
            $caseColumn = 'balanceLocalAmount';
        }

        $aging = ['0-30', '31-60', '61-90', '91-120', '121-150', '151-180', '181-210', '211-240', '241-365', '> 365'];
        $agingField = '';
        if (!empty($aging)) { /*calculate aging range in query*/
            $count = count($aging);
            $c = 1;
            foreach ($aging as $val) {
                if ($count == $c) {
                    $agingField .= "SUM(if(agingFinal.ageDays   > " . 365 . ",agingFinal." . $caseColumn . ",0)) as `case" . $c . "`,";
                } else {
                    $list = explode("-", $val);
                    $agingField .= "SUM(if(agingFinal.ageDays >= " . $list[0] . " AND agingFinal.ageDays <= " . $list[1] . ",agingFinal." . $caseColumn . ",0)) as `case" . $c . "`,";
                }
                $c++;
            }
        }

        $agingField .= "SUM(if(agingFinal.ageDays <= 0,agingFinal." . $caseColumn . ",0)) as `current`";

        $qry = 'SELECT ' . $finalSelect . ',' . $agingField . ' FROM (SELECT final.*,
                suppliermaster.countryID FROM (SELECT
                finalUnbilled.companySystemID,
                finalUnbilled.companyID,
                finalUnbilled.documentSystemID,
                finalUnbilled.documentID,
                finalUnbilled.documentCode,
                finalUnbilled.documentSystemCode,
                docDate.documentDate,
                finalUnbilled.supplierID,
                finalUnbilled.supplierCode,
                finalUnbilled.supplierName,
                finalUnbilled.localAmount AS documentLocalAmount,
                finalUnbilled.rptAmount AS documentRptAmount,
            IF
                ( finalUnbilled.matchedLocalAmount IS NULL, 0, finalUnbilled.matchedLocalAmount ) AS matchedLocalAmount,
            IF
                ( finalUnbilled.matchedRptAmount IS NULL, 0, finalUnbilled.matchedRptAmount ) AS matchedRptAmount,
                round( ( finalUnbilled.localAmount - ( IF ( finalUnbilled.matchedLocalAmount IS NULL, 0, finalUnbilled.matchedLocalAmount ) ) ), 3 ) AS balanceLocalAmount,
                round( ( finalUnbilled.rptAmount - ( IF ( finalUnbilled.matchedRptAmount IS NULL, 0, finalUnbilled.matchedRptAmount ) ) ), 2 ) AS balanceRptAmount ,
                DATEDIFF("' . $asOfDate . '",DATE(docDate.documentDate)) as ageDays
            FROM
                (
            SELECT
                erp_generalledger.companySystemID,
                erp_generalledger.companyID,
                erp_generalledger.glCode,
                erp_generalledger.documentID,
                erp_generalledger.documentSystemID,
                erp_generalledger.documentSystemCode,
                erp_generalledger.documentCode,
                sum( erp_generalledger.documentLocalAmount * - 1 ) AS localAmount,
                sum( erp_generalledger.documentRptAmount * - 1 ) AS rptAmount,
                erp_generalledger.supplierCodeSystem AS supplierID,
                SupplierForGeneralLedger.primarySupplierCode AS supplierCode,
                SupplierForGeneralLedger.supplierName AS supplierName,
                MatchedGRVAndInvoice.totLocalAmount1 AS matchedLocalAmount,
                MatchedGRVAndInvoice.totRptAmount1 AS matchedRptAmount 
            FROM
                erp_generalledger
                LEFT JOIN erp_grvmaster ON erp_generalledger.companySystemID = erp_grvmaster.companySystemID 
                AND erp_generalledger.documentSystemID = erp_grvmaster.documentSystemID 
                AND erp_generalledger.documentSystemCode = erp_grvmaster.grvAutoID
                LEFT JOIN erp_bookinvsuppmaster ON erp_generalledger.documentSystemID = erp_bookinvsuppmaster.documentSystemID 
                AND erp_generalledger.companySystemID = erp_bookinvsuppmaster.companySystemID 
                AND erp_generalledger.documentSystemCode = erp_bookinvsuppmaster.bookingSuppMasInvAutoID
                LEFT JOIN erp_purchasereturnmaster ON erp_generalledger.documentSystemID = erp_purchasereturnmaster.documentSystemID
                AND erp_generalledger.companySystemID = erp_purchasereturnmaster.companySystemID
                AND erp_generalledger.documentSystemCode = erp_purchasereturnmaster.purhaseReturnAutoID
                LEFT JOIN suppliermaster AS SupplierForGeneralLedger ON erp_generalledger.supplierCodeSystem = SupplierForGeneralLedger.supplierCodeSystem
                LEFT JOIN (
                (
            SELECT
                erp_bookinvsuppdet.companySystemID,
                3 AS documentSystemID,
                erp_bookinvsuppdet.grvAutoID AS documentSystemCode,
                grvGL.documentCode,
                SUM( erp_bookinvsuppdet.totLocalAmount ) AS totLocalAmount1,
                SUM( erp_bookinvsuppdet.totRptAmount ) AS totRptAmount1 
            FROM
                erp_bookinvsuppdet
                INNER JOIN (
            SELECT
                companySystemID,
                documentSystemID,
                documentSystemCode,
                documentCode 
            FROM
                erp_generalledger 
            WHERE
                erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
                AND STR_TO_DATE( DATE_FORMAT( erp_generalledger.documentDate, "%d/%m/%Y" ), "%d/%m/%Y" ) <= "' . $asOfDate . '" 
                AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                AND erp_generalledger.documentSystemID = 11 
            GROUP BY
                companySystemID,
                documentSystemID,
                documentSystemCode 
                ) AS grvGL ON grvGL.companySystemID = erp_bookinvsuppdet.companySystemID 
                AND grvGL.documentSystemCode = erp_bookinvsuppdet.bookingSuppMasInvAutoID 
            GROUP BY
                erp_bookinvsuppdet.companySystemID,
                erp_bookinvsuppdet.grvAutoID 
                ) UNION ALL
                (
            SELECT
                erp_bookinvsuppdet.companySystemID,
                11 AS documentSystemID,
                erp_bookinvsuppdet.bookingSuppMasInvAutoID AS documentSystemCode,
                BsiGL.documentCode,
                SUM( erp_bookinvsuppdet.totLocalAmount * - 1 ) AS totLocalAmount1,
                SUM( erp_bookinvsuppdet.totRptAmount * - 1 ) AS totRptAmount1 
            FROM
                erp_bookinvsuppdet
                INNER JOIN (
            SELECT
                companySystemID,
                documentSystemID,
                documentSystemCode,
                documentCode 
            FROM
                erp_generalledger 
            WHERE
                erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                AND erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
                AND STR_TO_DATE( DATE_FORMAT( erp_generalledger.documentDate, "%d/%m/%Y" ), "%d/%m/%Y" ) <= "' . $asOfDate . '"
                AND erp_generalledger.documentSystemID = 11 
                ) AS BsiGL ON BsiGL.companySystemID = erp_bookinvsuppdet.companySystemID 
                AND BsiGL.documentSystemCode = erp_bookinvsuppdet.bookingSuppMasInvAutoID 
            GROUP BY
                erp_bookinvsuppdet.companySystemID,
                erp_bookinvsuppdet.bookingSuppMasInvAutoID 
                ) 
                ) AS MatchedGRVAndInvoice ON erp_generalledger.companySystemID = MatchedGRVAndInvoice.companySystemID 
                AND erp_generalledger.documentSystemID = MatchedGRVAndInvoice.documentSystemID 
                AND erp_generalledger.documentSystemCode = MatchedGRVAndInvoice.documentSystemCode 
            WHERE
                erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                AND erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
                AND erp_generalledger.contraYN = 0 
                AND STR_TO_DATE( DATE_FORMAT( erp_generalledger.documentDate, "%d/%m/%Y" ), "%d/%m/%Y" ) <= "' . $asOfDate . '"
            GROUP BY
                erp_generalledger.companySystemID,
                erp_generalledger.documentSystemID,
                erp_generalledger.documentSystemCode,
                erp_generalledger.supplierCodeSystem
                ) AS finalUnbilled
                LEFT JOIN (
            SELECT
                companySystemID,
                documentSystemID,
                documentSystemCode,
                documentDate 
            FROM
                erp_generalledger 
            WHERE
                erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                AND erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
            GROUP BY
                companySystemID,
                documentSystemID,
                documentSystemCode 
                ) AS docDate ON docDate.companySystemID = finalUnbilled.companySystemID 
                AND docDate.documentSystemID = finalUnbilled.documentSystemID 
                AND docDate.documentSystemCode = finalUnbilled.documentSystemCode 
            WHERE
                (
                round( ( finalUnbilled.rptAmount - ( IF ( finalUnbilled.matchedRptAmount IS NULL, 0, finalUnbilled.matchedRptAmount ) ) ), 2 ) 
                ) <>0 ) as final 
                INNER JOIN suppliermaster ON suppliermaster.supplierCodeSystem = final.supplierID
                WHERE supplierID IN (' . join(',', $supplierSystemID) . ')' . $countryFilter . ') as agingFinal ' . $supplierGroup;

        $results = \DB::select($qry);

        foreach ($results as $index => $result) {
            $result->matchedLocalAmount = BookInvSuppDet::where('grvAutoID', $result->documentSystemCode)->where('companySystemID', $result->companySystemID)->where('supplierID', $result->supplierID)->sum('totLocalAmount');

            $result->matchedRptAmount = BookInvSuppDet::where('grvAutoID', $result->documentSystemCode)->where('companySystemID', $result->companySystemID)->where('supplierID', $result->supplierID)->sum('totRptAmount');


            $result->balanceLocalAmount = $result->documentLocalAmount - $result->matchedLocalAmount;
            $result->balanceRptAmount = $result->documentRptAmount - $result->matchedRptAmount;

            if (abs($result->balanceLocalAmount) < 0.00001 || abs($result->balanceRptAmount) < 0.00001) {
                unset($results[$index]);
            }
        }

        return array_values($results);
    }

    function getSupplierBalanceStatementReconcileQRY($request)
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
        $suppliers = (array)$request->suppliers;
        $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();

        $controlAccountsSystemID = $request->controlAccountsSystemID;

        $qry = 'SELECT
                              finalAgingDetail.companySystemID,
                                finalAgingDetail.companyID,
                                finalAgingDetail.CompanyName,
                                finalAgingDetail.documentSystemID,
                                finalAgingDetail.documentSystemCode,
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
                                finalAgingDetail.transCurrencyCode AS documentCurrency,
                                IFNULL(round( finalAgingDetail.documentAmountTrans, finalAgingDetail.documentTransDecimalPlaces ),0) AS invoiceAmountDoc,
                                IFNULL(round( finalAgingDetail.balanceAmountTrans, finalAgingDetail.documentTransDecimalPlaces ),0) AS balanceAmountDoc,
                                finalAgingDetail.documentTransDecimalPlaces AS balanceDecimalPlacesDoc,
                                if(finalAgingDetail.balanceAmountTrans<0,finalAgingDetail.balanceAmountTrans,0) as unAllocatedAmountDoc,
                                finalAgingDetail.localCurrencyCode AS documentCurrencyLoc,
                                IFNULL(round( finalAgingDetail.documentAmountLocal, finalAgingDetail.documentLocalDecimalPlaces ),0) AS invoiceAmountLoc,
                                IFNULL(round( finalAgingDetail.balanceAmountLocal, finalAgingDetail.documentLocalDecimalPlaces ),0) AS balanceAmountLoc,
                                finalAgingDetail.documentLocalDecimalPlaces AS balanceDecimalPlacesLoc,
                                if(finalAgingDetail.balanceAmountLocal<0,finalAgingDetail.balanceAmountLocal,0) as unAllocatedAmountLoc,
                                finalAgingDetail.rptCurrencyCode AS documentCurrencyRpt,
                                IFNULL(round( finalAgingDetail.documentAmountRpt, finalAgingDetail.documentRptDecimalPlaces ),0) AS invoiceAmountRpt,
                                IFNULL(round( finalAgingDetail.balanceAmountRpt, finalAgingDetail.documentRptDecimalPlaces ),0) AS balanceAmountRpt,
                                finalAgingDetail.documentRptDecimalPlaces AS balanceDecimalPlacesRpt,
                                if(finalAgingDetail.balanceAmountRpt<0,finalAgingDetail.balanceAmountRpt,0) as unAllocatedAmountRpt,
                                CONCAT(finalAgingDetail.SupplierCode," - ",finalAgingDetail.suppliername) as concatSupplierName
                            FROM
                            (
                            SELECT
                                MAINQUERY.companySystemID,
                                MAINQUERY.companyID,
                                companymaster.CompanyName,
                                MAINQUERY.documentSystemID,
                                MAINQUERY.documentSystemCode,
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
                                sum(erp_generalledger.documentTransAmount) * - 1 AS docTransAmount,
                                erp_generalledger.documentLocalCurrencyID,
                                sum(erp_generalledger.documentLocalAmount) * - 1 AS docLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                sum(erp_generalledger.documentRptAmount) * - 1 AS docRptAmount,
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
                                AND erp_generalledger.chartOfAccountSystemID  = "' . $controlAccountsSystemID . '"
                                AND erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND erp_generalledger.contraYN = 0
                                GROUP BY erp_generalledger.companySystemID, erp_generalledger.supplierCodeSystem,erp_generalledger.chartOfAccountSystemID,erp_generalledger.documentSystemID,erp_generalledger.documentSystemCode
                                ) AS MAINQUERY
                            LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = MAINQUERY.supplierCodeSystem
                            LEFT JOIN companymaster ON MAINQUERY.companySystemID = companymaster.companySystemID
                            LEFT JOIN currencymaster as transCurrencyDet ON transCurrencyDet.currencyID=MAINQUERY.documentTransCurrencyID
                            LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=MAINQUERY.documentLocalCurrencyID
                            LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=MAINQUERY.documentRptCurrencyID) as finalAgingDetail WHERE
	(
		round(
			finalAgingDetail.balanceAmountTrans,
			0
		) = 0
		AND round(
			finalAgingDetail.balanceAmountLocal,
			0
		) = 0
		AND round(
			finalAgingDetail.balanceAmountRpt,
			0
		) <> 0
	)
OR (
	(
		round(
			finalAgingDetail.balanceAmountTrans,
			0
		) = 0
		AND round(
			finalAgingDetail.balanceAmountLocal,
			0
		) <> 0
		AND round(
			finalAgingDetail.balanceAmountRpt,
			0
		) = 0
	)
)
OR (
	(
		round(
			finalAgingDetail.balanceAmountTrans,
			0
		) = 0
		AND round(
			finalAgingDetail.balanceAmountLocal,
			0
		) <> 0
		AND round(
			finalAgingDetail.balanceAmountRpt,
			0
		) <> 0
	)
)
ORDER BY
	documentDate ASC';

        $results = \DB::select($qry);

        foreach ($results as $index => $result){
            $exchangeGainLossAccount = SystemGlCodeScenarioDetail::getGlByScenario($result->companySystemID, $result->documentSystemID , 14);
            $chartOfAccount = GeneralLedger::where('documentSystemCode', $result->documentSystemCode)->where('chartOfAccountSystemID', $exchangeGainLossAccount)->where('companySystemID', $result->companySystemID)->where('documentType', NULL)->where('matchDocumentMasterAutoID', "!=", NULL)->first();
            if(!empty($chartOfAccount)) {
                    $result->exchangeGLTrans = $chartOfAccount->documentTransAmount;
                    $result->exchangeGLLocal = $chartOfAccount->documentLocalAmount;
                    $result->exchangeGLRpt = $chartOfAccount->documentRptAmount;
            }
            else {
                $result->exchangeGLTrans = 0;
                $result->exchangeGLLocal = 0;
                $result->exchangeGLRpt = 0;
            }

            $difTrans = round($result->unAllocatedAmountDoc,2) + round($result->exchangeGLTrans,2);
            $difLocal = round($result->unAllocatedAmountLoc,2) + round($result->exchangeGLLocal,2);
            $difRpt = round($result->unAllocatedAmountRpt,2) + round($result->exchangeGLRpt,2);

            if ($difTrans == 0 && $difLocal == 0 && $difRpt == 0) {
                unset($results[$index]);
            }
        }

        return $results;
    }

    public function pdfExportReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'APSS':
                if ($request->reportTypeID == 'SS') {

                    $html = $this->supplierStatementPdf($request->all())['html'];

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($html);

                    return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();
                } else if ($request->reportTypeID == 'SSD') {

                    $html = $this->supplierStatementDetailsPdf($request->all())['html'];

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($html);

                    return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();
                }
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    public function supplierStatementDetailsPdf($request, $sentEmail = false)
    {
        $request = (object)$this->convertArrayToSelectedValue($request, array('currencyID'));

        $checkIsGroup = Company::find($request->companySystemID);

        $companyLogo = $checkIsGroup->logo_url;

        $request->fromPath = 'pdf';
        [$outputArr, $decimalPlace, $selectedCurrency] = $this->getSupplierStatementDetails($request);

        $totalInvoices = 0;
        $totalAdvances = 0;
        $totalDebitNotes = 0;
        $totalPrepayment = 0;
        $totalNetOutstanding = 0;
        $totalArray = array();

        if ($outputArr) {
            foreach ($outputArr as $val) {
                $totalInvoices += $val['open_invoices'];
                $totalAdvances += $val['open_advances'];
                $totalDebitNotes += $val['open_debit_notes'];
                $totalPrepayment += ($val['open_advances'] + $val['open_debit_notes']);
                $totalNetOutstanding += ($val['open_invoices'] + $val['open_advances'] + $val['open_debit_notes']);
            }
        }

        $totalArray['totalInvoices'] = $totalInvoices;
        $totalArray['totalAdvances'] = $totalAdvances;
        $totalArray['totalDebitNotes'] = $totalDebitNotes;
        $totalArray['totalPrepayment'] = $totalPrepayment;
        $totalArray['totalNetOutstanding'] = $totalNetOutstanding;

        $dataArr = array('reportData' => (object)$outputArr, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'fromDate' => \Helper::dateFormat($request->fromDate), 'sentEmail' => $sentEmail, 'totalArray' => $totalArray);

        $html = view('print.supplier_statement_details', $dataArr);

        return ['html' => $html, 'dataArr' => $dataArr];
    }
    public function supplierStatementPdf($request, $sentEmail = false)
    {
        $request = (object)$this->convertArrayToSelectedValue($request, array('currencyID'));

        $checkIsGroup = Company::find($request->companySystemID);

        $companyLogo = $checkIsGroup->logo_url;

        $request->fromPath = 'pdf';
        $output = $this->getSupplierStatementQRY($request);

        $grandTotal = collect($output)->pluck('balanceAmount')->toArray();
        $grandTotal = array_sum($grandTotal);

        $outputArr = array();

        $balanceAmount = collect($output)->pluck('balanceAmount')->toArray();
        $balanceAmount = array_sum($balanceAmount);

        $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
        $decimalPlace = array_unique($decimalPlace);

        if ($output) {
            foreach ($output as $val) {
                $outputArr[$val->concatCompany][$val->concatSupplierName][] = $val;
            }
        }


        $dataArr = array('reportData' => (object)$outputArr, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'balanceAmount' => $balanceAmount, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'fromDate' => \Helper::dateFormat($request->fromDate), 'grandTotal' => $grandTotal, 'sentEmail' => $sentEmail);

        $html = view('print.supplier_statement', $dataArr);

        return ['html' => $html, 'output' => $output, 'dataArr' => $dataArr];
    }

    public function sentSupplierStatement(Request $request)
    {
        $input = $request->all();

        if (!isset($input['suppliers'])) {
            return $this->sendError("suppliers not found");
        }

        $suplliers = $input['suppliers'];
        $errorMessage = [];
        foreach ($suplliers as $key => $value) {
            $input['suppliers'] = [];
            $input['suppliers'][] = $value;

            $resHtml = $this->supplierStatementPdf($input, true);

            if (count($resHtml['output']) > 0) {
                $supplierID = $input['suppliers'][0]['supplierCodeSytem'];
                $fetchSupEmail = SupplierContactDetails::where('supplierID', $supplierID)
                    ->get();

                $supplierMaster = SupplierMaster::find($supplierID);

                $emailSentTo = 0;
                
                if ($fetchSupEmail) {
                    foreach ($fetchSupEmail as $row) {
                        if (!empty($row->contactPersonEmail)) {
                            $emailSentTo = 1;
                        }
                    }
                }

                if ($emailSentTo == 0) {
                    if ($supplierMaster) {
                        if (!empty($supplierMaster->supEmail)) {
                            $emailSentTo = 1;
                        }
                    }

                }

                if ($emailSentTo == 0) {
                    $errorMessage[] = "Supplier email is not updated for ".$supplierMaster->supplierName.". report is not sent";
                } 

                SupplierStatementJob::dispatch($request->db, $resHtml['dataArr'], $input);
            }
        }

        if (count($errorMessage) > 0) {
            return $this->sendError($errorMessage,500);
        } else {
            return $this->sendResponse([], 'Supplier statement report sent');
        }
    }

     public function sentSupplierLedger(Request $request)
    {
        $input = $request->all();

        if (!isset($input['suppliers'])) {
            return $this->sendError("suppliers not found");
        }

        $suplliers = $input['suppliers'];
        $errorMessage = [];
        foreach ($suplliers as $key => $value) {
            $input['suppliers'] = [];
            $input['suppliers'][] = $value;

            $resHtml = $this->supplierLedgerPdf($input, true);

            if (count($resHtml['output']) > 0) {
                $html = $resHtml['html'];

                $pdf = \App::make('dompdf.wrapper');
                $path = public_path().'/uploads/emailAttachment';

                if (!file_exists($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }
                $nowTime = time();

                $supplierID = $input['suppliers'][0]['supplierCodeSytem'];
                $pdf->loadHTML($html)->setPaper('a4', 'landscape')->save('uploads/emailAttachment/supplier_ledger_' . $nowTime.$supplierID . '.pdf');


                $fetchSupEmail = SupplierContactDetails::where('supplierID', $supplierID)
                    ->get();

                $supplierMaster = SupplierMaster::find($supplierID);

                $company = Company::where('companySystemID', $input['companySystemID'])->first();
                $emailSentTo = 0;

                $footer = "<font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!" .
                    "<br>This is an auto generated email. Please do not reply to this email because we are not " .
                    "monitoring this inbox.</font>";
                
                if ($fetchSupEmail) {
                    foreach ($fetchSupEmail as $row) {
                        if (!empty($row->contactPersonEmail)) {
                            $emailSentTo = 1;
                            $dataEmail['empEmail'] = $row->contactPersonEmail;

                            $dataEmail['companySystemID'] = $input['companySystemID'];

                            $temp = "Dear " . $supplierMaster->supplierName . ',<p> Supplier ledger report has been sent from ' . $company->CompanyName . $footer;

                            $pdfName = realpath("uploads/emailAttachment/supplier_ledger_" . $nowTime.$supplierID . ".pdf");

                            $dataEmail['isEmailSend'] = 0;
                            $dataEmail['attachmentFileName'] = $pdfName;
                            $dataEmail['alertMessage'] = "Supplier ledger report from " . $company->CompanyName;
                            $dataEmail['emailAlertMessage'] = $temp;
                            $sendEmail = \Email::sendEmailErp($dataEmail);
                            if (!$sendEmail["success"]) {
                                $errorMessage[] = $sendEmail["message"];
                            }
                        }
                    }
                }

                if ($emailSentTo == 0) {
                    if ($supplierMaster) {
                        if (!empty($supplierMaster->supEmail)) {
                            $emailSentTo = 1;
                            $dataEmail['empEmail'] = $supplierMaster->supEmail;

                            $dataEmail['companySystemID'] = $input['companySystemID'];

                            $temp = "Dear " . $supplierMaster->supplierName . ',<p> Supplier ledger report has been sent from ' . $company->CompanyName . $footer;

                            $pdfName = realpath("uploads/emailAttachment/supplier_ledger_" . $nowTime.$supplierID . ".pdf");

                            $dataEmail['isEmailSend'] = 0;
                            $dataEmail['attachmentFileName'] = $pdfName;
                            $dataEmail['alertMessage'] = "Supplier ledger report " . $company->CompanyName;
                            $dataEmail['emailAlertMessage'] = $temp;
                            $sendEmail = \Email::sendEmailErp($dataEmail);
                            if (!$sendEmail["success"]) {
                                $errorMessage[] = $sendEmail["message"];
                            }
                        }
                    }

                }

                if ($emailSentTo == 0) {
                    $errorMessage[] = "Supplier email is not updated for ".$supplierMaster->supplierName.". report is not sent";
                } 
            }
        }

        if (count($errorMessage) > 0) {
            return $this->sendError($errorMessage,500);
        } else {
            return $this->sendResponse([], 'Supplier ledger report sent');
        }
    }


    public function supplierLedgerPdf($request, $sentEmail = false)
    {
        $request = (object)$this->convertArrayToSelectedValue($request, array('currencyID'));
        $checkIsGroup = Company::find($request->companySystemID);

        $companyLogo = $checkIsGroup->logo_url;
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
        $dataArr = array('reportData' => (object)$outputArr, 'companyName' => $checkIsGroup->CompanyName, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'invoiceAmount' => $invoiceAmount, 'paidAmount' => $paidAmount, 'balanceAmount' => $balanceAmount, 'companylogo' => $companyLogo, 'fromDate' => \Helper::dateFormat($request->fromDate), 'toDate' => \Helper::dateFormat($request->toDate));
        
        $html = view('print.supplier_ledger', $dataArr);

        return ['html' => $html, 'output' => $output];
    }

    private function getInvoiceToPaymentQry($request)
    {

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }
        $asOfDate = new Carbon($request->fromDate);
        $asOfDate = $asOfDate->format('Y-m-d');

        if (isset($request->suppliers)) {
            $suppliers = $request->suppliers;
            $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();
        } else {
            $supplierSystemID = [];
        }


        return \DB::select('SELECT
	erp_generalledger.documentCode,
	suppliermaster.supplierName,
	erp_bookinvsuppmaster.supplierInvoiceNo,
	erp_bookinvsuppmaster.supplierInvoiceDate,
	currencymaster.CurrencyCode,
	round( erp_generalledger.documentLocalAmount *- 1, 3 ) AS localAmount,
	round( erp_generalledger.documentRptAmount *- 1, 2 ) AS rptAmount,
	erp_bookinvsuppmaster.confirmedDate,
	erp_bookinvsuppmaster.approvedDate,
	erp_generalledger.documentDate AS postedDate,
	paymentinfor.BPVcode,
	paymentinfor.paidRPTAmount,
	erp_paysupplierinvoicemaster.BPVchequeNo,
	erp_paysupplierinvoicemaster.BPVchequeDate,
	erp_paysupplierinvoicemaster.chequePrintedByEmpName,
	erp_paysupplierinvoicemaster.chequePrintedDateTime,
	erp_bankledger.trsClearedDate 
FROM
	erp_generalledger
	INNER JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem 
	INNER JOIN currencymaster ON erp_generalledger.documentRptCurrencyID = currencymaster.currencyID 
	AND suppliermaster.liabilityAccountSysemID = erp_generalledger.chartOfAccountSystemID
	INNER JOIN erp_bookinvsuppmaster ON erp_bookinvsuppmaster.documentSystemID = erp_generalledger.documentSystemID 
	AND erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_generalledger.documentSystemCode
	LEFT JOIN (
SELECT
	erp_paysupplierinvoicemaster.PayMasterAutoId,
	erp_paysupplierinvoicemaster.BPVcode,
	erp_paysupplierinvoicedetail.addedDocumentSystemID,
	erp_paysupplierinvoicedetail.bookingInvSystemCode,
	sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS paidLocalAmount,
	sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS paidRPTAmount 
FROM
	erp_paysupplierinvoicedetail
	INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_paysupplierinvoicedetail.PayMasterAutoId 
WHERE
	erp_paysupplierinvoicedetail.matchingDocID = 0 
	AND erp_paysupplierinvoicemaster.approved =- 1 
	AND erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyID) . ')
	AND erp_paysupplierinvoicemaster.cancelYN = 0 
	AND erp_paysupplierinvoicedetail.addedDocumentSystemID = 11 
	AND DATE(erp_paysupplierinvoicemaster.postedDate) <= "' . $asOfDate . '"
                            GROUP BY
                                erp_paysupplierinvoicedetail.bookingInvSystemCode,erp_paysupplierinvoicemaster.PayMasterAutoId 
                                UNION ALL
                            SELECT
                                erp_matchdocumentmaster.matchDocumentMasterAutoID,
                                erp_matchdocumentmaster.matchingDocCode,
                                erp_paysupplierinvoicedetail.addedDocumentSystemID,
                                erp_paysupplierinvoicedetail.bookingInvSystemCode,
                                sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS paidLocalAmount,
                                sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS paidRPTAmount 
                            FROM
                                erp_paysupplierinvoicedetail
                                INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_paysupplierinvoicedetail.matchingDocID 
                            WHERE
                                erp_paysupplierinvoicedetail.matchingDocID > 0 
                                AND erp_matchdocumentmaster.matchingConfirmedYN = 1 
                                AND erp_paysupplierinvoicedetail.addedDocumentSystemID = 11 
                                AND erp_matchdocumentmaster.companySystemID IN (' . join(',', $companyID) . ')
                                AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '"
                            GROUP BY
                                erp_paysupplierinvoicedetail.bookingInvSystemCode,erp_matchdocumentmaster.matchDocumentMasterAutoID 
                                ) AS paymentinfor ON paymentinfor.addedDocumentSystemID = erp_generalledger.documentSystemID 
                                AND paymentinfor.bookingInvSystemCode = erp_generalledger.documentSystemCode
                                LEFT JOIN erp_paysupplierinvoicemaster ON paymentinfor.PayMasterAutoId = erp_paysupplierinvoicemaster.PayMasterAutoId
                                LEFT JOIN erp_bankledger ON erp_bankledger.documentSystemID = erp_paysupplierinvoicemaster.documentSystemID 
                                AND erp_bankledger.documentSystemCode = erp_paysupplierinvoicemaster.PayMasterAutoId 
                            WHERE
                                erp_generalledger.documentSystemID = 11 
                                AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                                AND erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
                            ORDER BY
                                erp_generalledger.documentDate ASC');
    }
}
