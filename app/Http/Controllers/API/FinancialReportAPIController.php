<?php

/**
 * =============================================
 * -- File Name : FinancialReportAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report
 * -- Author : Nazir
 * -- Create date : 05 - July 2018
 * -- Description : This file contains the all the report generation code
 * -- Date: 06-july 2018 By: Fayas Description: Added new functions named as getTrialBalance()
 * -- Date: 11-july 2018 By: Fayas Description: Added new functions named as getTrialBalanceDetails()
 * -- Date: 13-july 2018 By: Fayas Description: Added new functions named as getTrialBalanceCompanyWise()
 * -- Date: 07-january 2018 By: Fayas Description: Added new functions named as getAFRFilterChartOfAccounts()
 * -- REVISION HISTORY
 */

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Models\AccountsType;
use App\Models\BookInvSuppDet;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\Contract;
use App\Models\CurrencyMaster;
use App\Models\ExpenseClaimType;
use App\Models\GeneralLedger;
use App\Models\Months;
use App\Models\ReportTemplate;
use App\Models\ReportTemplateColumnLink;
use App\Models\ReportTemplateColumns;
use App\Models\ReportTemplateDetails;
use App\Models\ReportTemplateDocument;
use App\Models\ReportTemplateLinks;
use App\Models\ReportTemplateNumbers;
use App\Models\SegmentMaster;
use App\Models\BudgetConsumedData;
use App\Models\ProjectGlDetail;
use App\Models\ErpProjectMaster;
use App\Models\ProcumentOrder;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

ini_set('max_execution_time', 500);

class FinancialReportAPIController extends AppBaseController
{
    protected $globalFormula; //keep whole formula ro replace

    public function getFRFilterData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $companiesByGroup = "";
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);
        if ($isGroup) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

        $company = Company::whereIN('companySystemID', $companiesByGroup)->where('isGroup', 0)->get();

        $companyFinanceYear = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear,bigginingDate,endingDate"));
        $companyFinanceYear = $companyFinanceYear->whereIn('companySystemID', $companiesByGroup);
        if (isset($request['type']) && $request['type'] == 'add') {
            $companyFinanceYear = $companyFinanceYear->where('isActive', -1);
        }
        $companyFinanceYear = $companyFinanceYear->groupBy('bigginingDate')->orderBy('bigginingDate', 'DESC')->get();

        $departments1 = collect(\Helper::getCompanyServiceline($selectedCompanyId));
        $departments2 = collect(SegmentMaster::where('serviceLineSystemID', 24)->get());
        $departments = $departments1->merge($departments2)->all();

        $controlAccount = ChartOfAccountsAssigned::whereIN('companySystemID', $companiesByGroup)->get([
            'chartOfAccountSystemID',
            'AccountCode', 'AccountDescription', 'catogaryBLorPL'
        ]);

        $contracts = Contract::whereIN('companySystemID', $companiesByGroup)->get(['contractUID', 'ContractNumber', 'contractDescription']);

        $accountType = AccountsType::all();

        $templateType = ReportTemplate::where('isActive', 1)
            ->whereIN('companySystemID', $companiesByGroup)
            ->get();


        $financePeriod = CompanyFinancePeriod::select(DB::raw("companyFinancePeriodID,isCurrent,CONCAT(DATE_FORMAT(dateFrom, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(dateTo, '%d/%m/%Y')) as financePeriod,companyFinanceYearID,dateFrom,dateTo"));
        $financePeriod = $financePeriod->whereIN('companySystemID', $companiesByGroup);
        $financePeriod = $financePeriod->where('departmentSystemID', 5);
        if (isset($request['type']) && $request['type'] == 'add') {
            $financePeriod = $financePeriod->where('isActive', -1);
        }
        $financePeriod = $financePeriod->groupBy('dateFrom')->get();

        $output = array(
            'companyFinanceYear' => $companyFinanceYear,
            'departments' => $departments,
            'controlAccount' => $controlAccount,
            'contracts' => $contracts,
            'accountType' => $accountType,
            'templateType' => $templateType,
            'segment' => $departments,
            'company' => $company,
            'financePeriod' => $financePeriod,
            'companiesByGroup' => $companiesByGroup,
            'isGroup' => $isGroup,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getAFRFilterChartOfAccounts(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $companiesByGroup = "";
        if (\Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }
        $input = $request->all();
        $inCategoryBLorPLID = [];

        if (isset($input['isBS']) && $input['isBS'] == 'true') {
            array_push($inCategoryBLorPLID, 1);
        }

        if (isset($input['isPL']) && $input['isPL'] == 'true') {
            array_push($inCategoryBLorPLID, 2);
        }

        if (count($inCategoryBLorPLID) == 0) {
            $inCategoryBLorPLID = [1, 2];
        }

        $controlAccount = ChartOfAccountsAssigned::whereIN('companySystemID', $companiesByGroup)
            ->whereIN('catogaryBLorPLID', $inCategoryBLorPLID)
            ->get(['chartOfAccountSystemID', 'AccountCode', 'AccountDescription', 'catogaryBLorPL']);

        $output = array(
            'controlAccount' => $controlAccount
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function validateFRReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'FTB':
                $validator = \Validator::make($request->all(), [
                    'reportTypeID' => 'required',
                    'fromDate' => 'required',
                    'toDate' => 'required|date|after_or_equal:fromDate',
                    'companyFinanceYearID' => 'required'
                ]);

                $companyFinanceYearID = $request->companyFinanceYearID;

                $companyFinanceYear = CompanyFinanceYear::where("companyFinanceYearID", $companyFinanceYearID)->first();


                if (empty($companyFinanceYear)) {
                    return $this->sendError('Finance Year Not Found');
                }

                $bigginingDate = (new Carbon($companyFinanceYear->bigginingDate))->format('Y-m-d');
                $endingDate = (new Carbon($companyFinanceYear->endingDate))->format('Y-m-d');

                $fromDate = (new Carbon($request->fromDate))->format('Y-m-d');
                $toDate = (new   Carbon($request->toDate))->format('Y-m-d');


                if (!($fromDate >= $bigginingDate) || !($fromDate <= $endingDate)) {
                    return $this->sendError('From Date not between Financial year !', 500);
                } else if (!($toDate >= $bigginingDate) || !($toDate <= $endingDate)) {
                    return $this->sendError('To Date not between Financial year !', 500);
                } else if ($fromDate > $toDate) {
                    return $this->sendError('The To date must be greater than the From date !', 500);
                }

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                if ($request->reportTypeID == 'FTBM') {

                    $validator1 = \Validator::make($request->all(), [
                        'currencyID' => 'required',
                    ]);

                    if ($validator1->fails()) {
                        return $this->sendError($validator1->messages(), 422);
                    }
                }

                break;

            case 'FGL':
                $validator = \Validator::make($request->all(), [
                    'reportTypeID' => 'required',
                    'fromDate' => 'required',
                    'toDate' => 'required|date|after_or_equal:fromDate',
                    'glCodes' => 'required',
                    'departments' => 'required',
                    // 'contracts' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            case 'FTD':
                $validator = \Validator::make($request->all(), [
                    'reportTypeID' => 'required',
                    'fromDate' => 'required',
                    'tempType' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                if ($request->tempType == 0) {
                    return $this->sendError('Please select a type');
                }
                break;
            case 'FCT':
                $validator = \Validator::make($request->all(), [
                    'accountType' => 'required',
                    'companyFinanceYearID' => 'required',
                    'templateType' => 'required',
                    'companySystemID' => 'required',
                    'serviceLineSystemID' => 'required_if:accountType,2|nullable',
                    'currency' => 'required',
                    'fromDate' => 'required_if:dateType,1|nullable|date',
                    'toDate' => 'required_if:dateType,1|nullable|date|after_or_equal:fromDate',
                    'month' => 'required_if:dateType,2',
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                $input = $request->all();
                $checkDetails = ReportTemplateDetails::where('companyReportTemplateID', $input['templateType'])->first();

                if (!$checkDetails) {
                    return $this->sendError("Report rows are not configured");
                }

                $checkColoumns = ReportTemplateColumnLink::where('templateID', $input['templateType'])->first();

                if (!$checkColoumns) {
                    return $this->sendError("Report columns are not configured");
                }

                break;
            case 'JVD':
                $validator = \Validator::make($request->all(), [
                    'companySystemID' => 'required',
                    'jvType' => 'required|array',
                    'fromDate' => 'required_if:dateType,1|nullable|date',
                    'toDate' => 'required_if:dateType,1|nullable|date|after_or_equal:fromDate',
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    public function validatePUReport(Request $request)
    {
        $fromDate = (new Carbon($request->fromDate))->format('Y-m-d');
        $toDate = (new   Carbon($request->toDate))->format('Y-m-d');
        $projectID = $request->projectID;
        $projectDetail = ErpProjectMaster::with('currency', 'service_line')->where('id', $projectID)->first();

        $budgetConsumedData = BudgetConsumedData::with('purchase_order')->where('projectID', $projectID)->where('documentSystemID', 2)->get();


        $budgetAmount = BudgetConsumedData::where('projectID', $projectID)
            ->where('documentSystemID', 2)
            ->whereHas('purchase_order', function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('approvedDate', [$fromDate, $toDate]);
            })
            ->sum('consumedRptAmount');

        $budgetOpeningConsumption = BudgetConsumedData::where('projectID', $projectID)
            ->where('documentSystemID', 2)
            ->whereHas('purchase_order', function ($query) use ($fromDate, $toDate) {
                $query->whereDate('approvedDate', '<', $fromDate);
            })
            ->sum('consumedRptAmount');

            $detailsPOWise = BudgetConsumedData::with(['purchase_order_detail' => function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('approvedDate', [$fromDate, $toDate]);
            }])
                ->whereHas('purchase_order_detail', function ($query) use ($fromDate, $toDate) {
                    $query->whereBetween('approvedDate', [$fromDate, $toDate]);
                })
                ->where('projectID', $projectID)
                ->where('documentSystemID', 2)
                ->selectRaw('sum(consumedRptAmount) as documentAmount, documentCode, documentSystemCode')
                ->groupBy('documentSystemCode')
                ->get();

        $getProjectAmounts = ProjectGlDetail::where('projectID', $projectID)->get();
        $projectAmount = collect($getProjectAmounts)->sum('amount');

        if ($projectAmount > 0) {
            $projectAmount = $projectAmount;
        } else {
            $projectAmount = 0;
        }

        $openingBalance = $projectAmount - $budgetOpeningConsumption;


        $closingBalance = $openingBalance - $budgetAmount;

        $output = array(
            'projectDetail' => $projectDetail,
            'projectAmount' => $projectAmount,
            'budgetConsumedData' => $budgetConsumedData,
            'budgetConsumptionAmount' => $budgetAmount,
            'openingBalance' => $openingBalance,
            'closingBalance' => $closingBalance,
            'detailsPOWise' => $detailsPOWise
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function generateprojectUtilizationReport(Request $request)
    {

        $fromDate = (new Carbon($request->fromDate))->format('Y-m-d');
        $toDate = (new   Carbon($request->toDate))->format('Y-m-d');
        $projectID = $request->projectID;
        $projectDetail = ErpProjectMaster::with('currency', 'service_line')->where('id', $projectID)->first();

        $budgetConsumedData = BudgetConsumedData::with('purchase_order')->where('projectID', $projectID)->where('documentSystemID', 2)->get();

        $detailsPOWise = BudgetConsumedData::with(['purchase_order_detail' => function ($query) use ($fromDate, $toDate) {
            $query->whereBetween('approvedDate', [$fromDate, $toDate]);
        }])
            ->whereHas('purchase_order_detail', function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('approvedDate', [$fromDate, $toDate]);
            })
            ->where('projectID', $projectID)
            ->where('documentSystemID', 2)
            ->selectRaw('sum(consumedRptAmount) as documentAmount, documentCode, documentSystemCode')
            ->groupBy('documentSystemCode')
            ->get();

        $budgetAmount = BudgetConsumedData::where('projectID', $projectID)
            ->where('documentSystemID', 2)
            ->whereHas('purchase_order', function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('approvedDate', [$fromDate, $toDate]);
            })
            ->sum('consumedRptAmount');

        $budgetOpeningConsumption = BudgetConsumedData::where('projectID', $projectID)
            ->where('documentSystemID', 2)
            ->whereHas('purchase_order', function ($query) use ($fromDate, $toDate) {
                $query->whereDate('approvedDate', '<', $fromDate);
            })
            ->sum('consumedRptAmount');
        $getProjectAmounts = ProjectGlDetail::where('projectID', $projectID)->get();
        $projectAmount = collect($getProjectAmounts)->sum('amount');
        if ($projectAmount > 0) {
            $projectAmount = $projectAmount;
        } else {
            $projectAmount = 0;
        }

        $openingBalance = $projectAmount - $budgetOpeningConsumption;


        $closingBalance = $openingBalance - $budgetAmount;

        $output = array(
            'projectDetail' => $projectDetail,
            'projectAmount' => $projectAmount,
            'budgetConsumedData' => $budgetConsumedData,
            'budgetConsumptionAmount' => $budgetAmount,
            'openingBalance' => $openingBalance,
            'closingBalance' => $closingBalance,
            'detailsPOWise' => $detailsPOWise
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    /*generate report according to each report id*/
    public function generateFRReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'FTB': // Trial Balance

                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($request->companySystemID);

                $type = $request->reportTypeID;

                $output = array();
                $headers = array();

                if ($type == 'FTB') {
                    $output = $this->getTrialBalance($request);
                } else if ($type == 'FTBM') {
                    $result = $this->getTrialBalanceMonthWise($request);
                    $output = $result['data'];
                    $headers = $result['headers'];
                }

                $currencyIdLocal = 1;
                $currencyIdRpt = 2;

                $decimalPlaceCollectLocal = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                $decimalPlaceUniqueLocal = array_unique($decimalPlaceCollectLocal);

                $decimalPlaceCollectRpt = collect($output)->pluck('documentRptCurrencyID')->toArray();
                $decimalPlaceUniqueRpt = array_unique($decimalPlaceCollectRpt);


                if (!empty($decimalPlaceUniqueLocal)) {
                    $currencyIdLocal = $decimalPlaceUniqueLocal[0];
                }

                if (!empty($decimalPlaceUniqueRpt)) {
                    $currencyIdRpt = $decimalPlaceUniqueRpt[0];
                }

                $requestCurrencyLocal = CurrencyMaster::where('currencyID', $currencyIdLocal)->first();
                $requestCurrencyRpt = CurrencyMaster::where('currencyID', $currencyIdRpt)->first();

                $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

                $total = array();
                $total['documentLocalAmountDebit'] = array_sum(collect($output)->pluck('documentLocalAmountDebit')->toArray());
                $total['documentLocalAmountCredit'] = array_sum(collect($output)->pluck('documentLocalAmountCredit')->toArray());
                $total['documentRptAmountDebit'] = array_sum(collect($output)->pluck('documentRptAmountDebit')->toArray());
                $total['documentRptAmountCredit'] = array_sum(collect($output)->pluck('documentRptAmountCredit')->toArray());
                return array(
                    'reportData' => $output,
                    'companyName' => $checkIsGroup->CompanyName,
                    'isGroup' => $checkIsGroup->isGroup,
                    'total' => $total,
                    'headers' => $headers,
                    'decimalPlaceLocal' => $decimalPlaceLocal,
                    'decimalPlaceRpt' => $decimalPlaceRpt,
                    'currencyLocal' => $requestCurrencyLocal->CurrencyCode,
                    'currencyRpt' => $requestCurrencyRpt->CurrencyCode,
                );
                break;
            case 'FTBD': // Trial Balance

                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getTrialBalanceDetails($request);

                $currencyIdLocal = 1;
                $currencyIdRpt = 2;

                $decimalPlaceCollectLocal = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                $decimalPlaceUniqueLocal = array_unique($decimalPlaceCollectLocal);

                $decimalPlaceCollectRpt = collect($output)->pluck('documentRptCurrencyID')->toArray();
                $decimalPlaceUniqueRpt = array_unique($decimalPlaceCollectRpt);


                if (!empty($decimalPlaceUniqueLocal)) {
                    $currencyIdLocal = $decimalPlaceUniqueLocal[0];
                }

                if (!empty($decimalPlaceUniqueRpt)) {
                    $currencyIdRpt = $decimalPlaceUniqueRpt[0];
                }

                $requestCurrencyLocal = CurrencyMaster::where('currencyID', $currencyIdLocal)->first();
                $requestCurrencyRpt = CurrencyMaster::where('currencyID', $currencyIdRpt)->first();

                $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

                $total = array();
                $total['documentLocalAmountDebit'] = array_sum(collect($output)->pluck('localDebit')->toArray());
                $total['documentLocalAmountCredit'] = array_sum(collect($output)->pluck('localCredit')->toArray());
                $total['documentRptAmountDebit'] = array_sum(collect($output)->pluck('rptDebit')->toArray());
                $total['documentRptAmountCredit'] = array_sum(collect($output)->pluck('rptCredit')->toArray());
                return array(
                    'reportData' => $output,
                    'companyName' => $checkIsGroup->CompanyName,
                    'isGroup' => $checkIsGroup->isGroup,
                    'total' => $total,
                    'decimalPlaceLocal' => $decimalPlaceLocal,
                    'decimalPlaceRpt' => $decimalPlaceRpt,
                    'currencyLocal' => $requestCurrencyLocal->CurrencyCode,
                    'currencyRpt' => $requestCurrencyRpt->CurrencyCode,
                );
                break;
            case 'FGL': // General Ledger

                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getGeneralLedger($request);

                $currencyIdLocal = 1;
                $currencyIdRpt = 2;

                $decimalPlaceCollectLocal = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                $decimalPlaceUniqueLocal = array_unique($decimalPlaceCollectLocal);

                $decimalPlaceCollectRpt = collect($output)->pluck('documentRptCurrencyID')->toArray();
                $decimalPlaceUniqueRpt = array_unique($decimalPlaceCollectRpt);


                if (!empty($decimalPlaceUniqueLocal)) {
                    $currencyIdLocal = $decimalPlaceUniqueLocal[0];
                }

                if (!empty($decimalPlaceUniqueRpt)) {
                    $currencyIdRpt = $decimalPlaceUniqueRpt[0];
                }

                $requestCurrencyLocal = CurrencyMaster::where('currencyID', $currencyIdLocal)->first();
                $requestCurrencyRpt = CurrencyMaster::where('currencyID', $currencyIdRpt)->first();

                $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

                $total = array();
                $total['documentLocalAmountDebit'] = array_sum(collect($output)->pluck('localDebit')->toArray());
                $total['documentLocalAmountCredit'] = array_sum(collect($output)->pluck('localCredit')->toArray());
                $total['documentRptAmountDebit'] = array_sum(collect($output)->pluck('rptDebit')->toArray());
                $total['documentRptAmountCredit'] = array_sum(collect($output)->pluck('rptCredit')->toArray());

                $sort = 'asc';

                return \DataTables::of($output)
                    ->addIndexColumn()
                    ->with('companyName', $checkIsGroup->CompanyName)
                    ->with('isGroup', $checkIsGroup->isGroup)
                    ->with('total', $total)
                    ->with('decimalPlaceLocal', $decimalPlaceLocal)
                    ->with('decimalPlaceRpt', $decimalPlaceRpt)
                    ->with('currencyLocal', $requestCurrencyLocal->CurrencyCode)
                    ->with('currencyRpt', $requestCurrencyRpt->CurrencyCode)
                    ->addIndexColumn()
                    // ->with('orderCondition', $sort)
                    ->make(true);

                /*return array('reportData' => $output,
                    'companyName' => $checkIsGroup->CompanyName,
                    'isGroup' => $checkIsGroup->isGroup,
                    'total' => $total,
                    'decimalPlaceLocal' => $decimalPlaceLocal,
                    'decimalPlaceRpt' => $decimalPlaceRpt,
                    'currencyLocal' => $requestCurrencyLocal->CurrencyCode,
                    'currencyRpt' => $requestCurrencyRpt->CurrencyCode,
                );*/
                break;
            case 'FTD': // Tax Detail

                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getTaxDetailQry($request);

                $total = array();

                return array(
                    'reportData' => $output,
                    'companyName' => $checkIsGroup->CompanyName,
                    'isGroup' => $checkIsGroup->isGroup,
                    'total' => $total,
                    'tempType' => $request->tempType
                );
                break;
            case 'FCT': // Finance Customize reports (Income statement, P&L, Cash flow)
                $request = (object)$request->all();

                if ($request->accountType == 1) { // if account type is BS and if any new chart of account created automatically link the gl account
                    $detID = ReportTemplateDetails::ofMaster($request->templateType)->where('itemType', 4)->whereNotNull('masterID')->first();
                    if (!empty($detID->detID) && !is_null($detID->detID)) {
                        $notExistPLAccount = ChartOfAccount::where('isActive', 1)->where('isApproved', 1)->where('catogaryBLorPL', 'PL')->whereDoesntHave('templatelink', function ($query) use ($request, $detID) {
                            $query->where('templateMasterID', $request->templateType)->where('templateDetailID', $detID->detID);
                        })->get();
                        if (count($notExistPLAccount) > 0) {
                            $company = Company::find($request->selectedCompanyID);
                            if ($company) {
                                $data['companyID'] = $company->CompanyID;
                            }
                            foreach ($notExistPLAccount as $val) {
                                $data['templateMasterID'] = $request->templateType;
                                $data['templateDetailID'] = $detID->detID;
                                $data['sortOrder'] = 1;
                                $data['glAutoID'] = $val['chartOfAccountSystemID'];
                                $data['glCode'] = $val['AccountCode'];
                                $data['glDescription'] = $val['AccountDescription'];
                                $data['companySystemID'] = $val['selectedCompanyID'];
                                $data['createdPCID'] = gethostname();
                                $data['createdUserID'] = \Helper::getEmployeeID();
                                $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                                ReportTemplateLinks::create($data);
                            }
                        }
                    }
                }

                $financeYear = CompanyFinanceYear::find($request->companyFinanceYearID);

                $company = Company::find($request->selectedCompanyID);
                $template = ReportTemplate::find($request->templateType);
                $companyCurrency = \Helper::companyCurrency($request->companySystemID);

                $month = '';
                $period = '';
                if ($request->dateType != 1) {
                    $period = CompanyFinancePeriod::find($request->month);
                    $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
                    $month = Carbon::parse($toDate)->format('Y-m-d');
                }

                // get generated customize column query
                $generatedColumn = $this->getFinancialCustomizeRptColumnQry($request);
                $linkedcolumnQry = $generatedColumn['linkedcolumnQry']; // generated select statement
                $columnKeys = $generatedColumn['columnKeys'];
                $currencyColumn = $generatedColumn['currencyColumn']; // currency column whether local or reporting
                $columnHeader = $generatedColumn['columnHeader']; // column name with detail
                $columnHeaderMapping = $generatedColumn['columnHeaderMapping']; // column name
                $linkedcolumnQry2 = $generatedColumn['linkedcolumnQry2']; // generated select statement
                $budgetQuery = $generatedColumn['budgetQuery']; // generated select statement for budget query
                $budgetWhereQuery = $generatedColumn['budgetWhereQuery']; // generated select statement for budget query
                $columnTemplateID = $generatedColumn['columnTemplateID']; // customized coloumn from template

                $outputCollect = collect($this->getCustomizeFinancialRptQry($request, $linkedcolumnQry, $linkedcolumnQry2, $columnKeys, $financeYear, $period, $budgetQuery, $budgetWhereQuery, $columnTemplateID)); // main query
                $outputDetail = collect($this->getCustomizeFinancialDetailRptQry($request, $linkedcolumnQry, $columnKeys, $financeYear, $period, $budgetQuery, $budgetWhereQuery, $columnTemplateID)); // detail query
                $headers = $outputCollect->where('masterID', null)->sortBy('sortOrder')->values();
                $grandTotalUncatArr = [];
                $uncategorizeArr = [];
                $uncategorizeDetailArr = [];
                $grandTotal = [];
                if ($request->accountType == 1 || $request->accountType == 2) { // get uncategorized value
                    $uncategorizeData = collect($this->getCustomizeFinancialUncategorizeQry($request, $linkedcolumnQry, $linkedcolumnQry2, $financeYear, $period, $columnKeys, $budgetQuery, $budgetWhereQuery, $columnTemplateID));
                    $grandTotal = collect($this->getCustomizeFinancialGrandTotalQry($request, $linkedcolumnQry, $linkedcolumnQry2, $financeYear, $period, $columnKeys, $budgetQuery, $budgetWhereQuery, $columnTemplateID));
                    if ($uncategorizeData['output']) {
                        foreach ($columnKeys as $key => $val) {
                            $uncategorizeArr[$val] = $uncategorizeData['output'][0]->$val;
                        }
                    }
                    $uncategorizeDetailArr = $uncategorizeData['outputDetail'];
                } else {
                    $grandTotal[0] = [];
                }

                $outputOpeningBalance = '';
                $outputOpeningBalanceArr = [];
                $outputClosingBalanceArr = [];
                if ($request->accountType == 3) { // if report is cash flow type get opening and closing balance
                    $outputOpeningBalance = $this->getCashflowOpeningBalanceQry($request, $currencyColumn, $columnTemplateID);
                    if ($columnTemplateID == null) {
                        $outputOpeningBalance = !empty($outputOpeningBalance->openingBalance) ? $outputOpeningBalance->openingBalance : 0;

                        $lastColumn = collect($headers)->last(); // considering net total
                        foreach ($columnKeys as $key => $val) {
                            if ($key == 0) {
                                $outputOpeningBalanceArr[] = $outputOpeningBalance;
                                $outputClosingBalanceArr[] = $lastColumn->$val + $outputOpeningBalance;
                            } else {
                                $outputOpeningBalanceArr[] = $outputClosingBalanceArr[$key - 1];
                                $outputClosingBalanceArr[] = $lastColumn->$val + $outputClosingBalanceArr[$key - 1];
                            }
                        }
                    }
                }


                $companyID = collect($request->companySystemID)->pluck('companySystemID')->toArray();
                $removedFromArray = [];
                $companyHeaderColumns = [];
                if ($columnTemplateID == 1) {
                    if ($request->accountType == 1 || $request->accountType == 2) {
                        $companyWiseGrandTotal = $grandTotal->groupBy('compID');
                    } else {
                        $companyWiseGrandTotal = [];
                        $uncategorizeData = [];
                    }
                    $res = $this->processColumnTemplateData($headers, $outputCollect, $outputDetail, $columnKeys, $uncategorizeData, $companyWiseGrandTotal, $outputOpeningBalance, $request);
                    $headers = $res['headers'];
                    $companyHeaderColumns = $res['companyHeaderColumns'];
                    $uncategorizeDetailArr = $res['uncategorizeDetailArr'];
                    $uncategorizeArr = $res['uncategorizeArr'];
                    $companyWiseGrandTotalArray = $res['companyWiseGrandTotalArray'];
                    $outputOpeningBalanceArr = $res['outputOpeningBalanceArr'];
                    $outputClosingBalanceArr = $res['outputClosingBalanceArr'];
                    $firstLevel = $res['firstLevel'];
                    $secondLevel = $res['secondLevel'];
                    $thirdLevel = $res['thirdLevel'];
                    $fourthLevel = $res['fourthLevel'];
                } else {
                    $firstLevel = false;
                    $secondLevel = false;
                    $thirdLevel = false;
                    $fourthLevel = false;
                    $fifthLevel = false;
                    if (count($headers) > 0) {
                        foreach ($headers as $key => $val) {
                            $details = $outputCollect->where('masterID', $val->detID)->sortBy('sortOrder')->values();
                            $val->detail = $details;
                            $firstLevel = true;
                            foreach ($details as $key2 => $val2) {
                                if ($val2->isFinalLevel == 1) {
                                    $val2->glCodes = $outputDetail->where('templateDetailID', $val2->detID)->sortBy('sortOrder')->values();
                                } else {
                                    $detailLevelTwo = $outputCollect->where('masterID', $val2->detID)->sortBy('sortOrder')->values();
                                    $val2->detail = $detailLevelTwo;
                                    $secondLevel = true;
                                    foreach ($detailLevelTwo as $key3 => $val3) {
                                        if ($val3->isFinalLevel == 1) {
                                            $val3->glCodes = $outputDetail->where('templateDetailID', $val3->detID)->sortBy('sortOrder')->values();
                                        } else {
                                            $detailLevelThree = $outputCollect->where('masterID', $val3->detID)->sortBy('sortOrder')->values();
                                            $val3->detail = $detailLevelThree;
                                            $thirdLevel = true;
                                            foreach ($detailLevelThree as $key4 => $val4) {
                                                if ($val4->isFinalLevel == 1) {
                                                    $val4->glCodes = $outputDetail->where('templateDetailID', $val4->detID)->sortBy('sortOrder')->values();
                                                } else {
                                                    $detailLevelFour = $outputCollect->where('masterID', $val4->detID)->sortBy('sortOrder')->values();
                                                    $val4->detail = $detailLevelFour;
                                                    $fourthLevel = true;
                                                    foreach ($detailLevelFour as $key5 => $val5) {
                                                        if ($val5->isFinalLevel == 1) {
                                                            $val5->glCodes = $outputDetail->where('templateDetailID', $val5->detID)->sortBy('sortOrder')->values();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if ($val->itemType != 3) {
                                if (count($details) == 0) {
                                    $removedFromArray[] = $key;
                                }
                            }
                        }
                    }
                    $headers = collect($headers)->forget($removedFromArray)->values();
                }

                //remove records which has no detail except total
                // get devision value
                $divisionValue = 1;
                if ($template) {
                    if ($template->showNumbersIn !== 1) {
                        $numbers = ReportTemplateNumbers::find($template->showNumbersIn);
                        $divisionValue = (float)$numbers->value;
                    }
                }


                $grandTotal = ($columnTemplateID == 1) ? collect($companyWiseGrandTotalArray)->toArray() : $grandTotal[0];

                return array(
                    'reportData' => $headers,
                    'template' => $template,
                    'company' => $company,
                    'companyCurrency' => $companyCurrency,
                    'columns' => $columnKeys,
                    'columnHeader' => $columnHeader,
                    'columnHeaderMapping' => $columnHeaderMapping,
                    'openingBalance' => $outputOpeningBalanceArr,
                    'closingBalance' => $outputClosingBalanceArr,
                    'uncategorize' => $uncategorizeArr,
                    'uncategorizeDrillDown' => $uncategorizeDetailArr,
                    'grandTotalUncatArr' => $grandTotal,
                    'numbers' => $divisionValue,
                    'columnTemplateID' => $columnTemplateID,
                    'companyHeaderData' => $companyHeaderColumns,
                    'month' => $month,
                    'firstLevel' => $firstLevel,
                    'secondLevel' => $secondLevel,
                    'thirdLevel' => $thirdLevel,
                    'fourthLevel' => $fourthLevel
                );
                break;
            case 'JVD':
                $type = $request->reportTypeID;
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->jvDetailQry($request);

                $currencyIdLocal = 1;
                $currencyIdRpt = 2;

                $decimalPlaceCollectLocal = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                $decimalPlaceUniqueLocal = array_unique($decimalPlaceCollectLocal);

                $decimalPlaceCollectRpt = collect($output)->pluck('documentRptCurrencyID')->toArray();
                $decimalPlaceUniqueRpt = array_unique($decimalPlaceCollectRpt);


                if (!empty($decimalPlaceUniqueLocal)) {
                    $currencyIdLocal = $decimalPlaceUniqueLocal[0];
                }

                if (!empty($decimalPlaceUniqueRpt)) {
                    $currencyIdRpt = $decimalPlaceUniqueRpt[0];
                }

                $requestCurrencyLocal = CurrencyMaster::where('currencyID', $currencyIdLocal)->first();
                $requestCurrencyRpt = CurrencyMaster::where('currencyID', $currencyIdRpt)->first();

                $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

                $total = array();
                $total['documentLocalAmountDebit'] = array_sum(collect($output)->pluck('debitAmountLocal')->toArray());
                $total['documentLocalAmountCredit'] = array_sum(collect($output)->pluck('creditAmountLocal')->toArray());
                $total['documentRptAmountDebit'] = array_sum(collect($output)->pluck('debitAmountRpt')->toArray());
                $total['documentRptAmountCredit'] = array_sum(collect($output)->pluck('creditAmountRpt')->toArray());

                return array(
                    'reportData' => $output,
                    'companyName' => $checkIsGroup->CompanyName,
                    'isGroup' => $checkIsGroup->isGroup,
                    'total' => $total,
                    'decimalPlaceLocal' => $decimalPlaceLocal,
                    'decimalPlaceRpt' => $decimalPlaceRpt,
                    'currencyLocal' => $requestCurrencyLocal->CurrencyCode,
                    'currencyRpt' => $requestCurrencyRpt->CurrencyCode,
                );

                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    public function jvDetailQry($request)
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

        $jvType = collect($request->jvType)->pluck('id')->toArray();
        $jvType = array_unique($jvType);
        if ($request->reportTypeID == 'JVDD') {

            $query = 'SELECT
                      erp_generalledger.companySystemID,
                        erp_generalledger.companyID,
                        erp_generalledger.documentID,
                        erp_generalledger.documentSystemID,
                        erp_generalledger.documentLocalCurrencyID,
                        erp_generalledger.documentRptCurrencyID,
                        erp_generalledger.documentSystemCode,
                        erp_generalledger.documentCode,
                        erp_generalledger.documentDate,
                        erp_generalledger.documentFinalApprovedDate,
                        erp_jvmaster.confirmedDate,
                        YEAR ( erp_generalledger.documentDate ) AS YEAR,
                        erp_generalledger.documentNarration,
                        erp_generalledger.glCode,
                        erp_generalledger.glAccountType,
                        chartofaccounts.AccountDescription,
                        IF
                            ( documentLocalAmount < 0, documentLocalAmount *- 1, 0 ) AS creditAmountLocal,
                        IF
                            ( documentLocalAmount > 0, documentLocalAmount, 0 ) AS debitAmountLocal,
                            IF
                            ( documentRptAmount < 0, documentRptAmount *- 1, 0 ) AS creditAmountRpt,
                        IF
                            ( documentRptAmount > 0, documentRptAmount, 0 ) AS debitAmountRpt,
                        employees.empName AS FinalApprovedBy,
                        erp_jvmaster.createdUserID,
                        erp_jvmaster.confirmedByName FROM erp_generalledger 
                        LEFT JOIN chartofaccounts ON erp_generalledger.glCode = chartofaccounts.AccountCode
                        LEFT JOIN employees ON erp_generalledger.documentFinalApprovedBy = employees.empID
                        INNER JOIN erp_jvmaster ON erp_jvmaster.companySystemID = erp_generalledger.companySystemID 
                        AND erp_jvmaster.documentSystemID = erp_generalledger.documentSystemID 
                        AND erp_jvmaster.jvMasterAutoId = erp_generalledger.documentSystemCode
                        WHERE erp_generalledger.documentSystemID = 17 
                        AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND erp_jvmaster.jvType IN (' . join(',', $jvType) . ')
                        AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"';
        }
        if ($request->reportTypeID == 'JVDS') {
            $query = 'SELECT
                        companySystemID,
                        companyID,
                        documentSystemID,
                        documentRptCurrencyID,
                        documentLocalCurrencyID,
                        documentSystemCode,
                        documentCode,
                        documentDate,
                        YEAR,
                        documentNarration,
                        SUM( creditAmountLocal ) AS creditAmountLocal,
                        SUM( debitAmountLocal ) AS debitAmountLocal,
                        SUM( creditAmountRpt ) AS creditAmountRpt,
                        SUM( debitAmountRpt ) AS debitAmountRpt,
                        confirmedDate,
                        confirmedByName,
                        documentFinalApprovedDate,
                        FinalApprovedBy 
                    FROM
                        (
                     SELECT
                      erp_generalledger.companySystemID,
                        erp_generalledger.companyID,
                        erp_generalledger.documentID,
                        erp_generalledger.documentSystemID,
                        erp_generalledger.documentLocalCurrencyID,
                        erp_generalledger.documentRptCurrencyID,
                        erp_generalledger.documentSystemCode,
                        erp_generalledger.documentCode,
                        erp_generalledger.documentDate,
                        erp_generalledger.documentFinalApprovedDate,
                        erp_jvmaster.confirmedDate,
                        YEAR ( erp_generalledger.documentDate ) AS YEAR,
                        erp_generalledger.documentNarration,
                        erp_generalledger.glCode,
                        erp_generalledger.glAccountType,
                        chartofaccounts.AccountDescription,
                        IF
                            ( documentLocalAmount < 0, documentLocalAmount *- 1, 0 ) AS creditAmountLocal,
                        IF
                            ( documentLocalAmount > 0, documentLocalAmount, 0 ) AS debitAmountLocal,
                            IF
                            ( documentRptAmount < 0, documentRptAmount *- 1, 0 ) AS creditAmountRpt,
                        IF
                            ( documentRptAmount > 0, documentRptAmount, 0 ) AS debitAmountRpt,
                        employees.empName AS FinalApprovedBy,
                        erp_jvmaster.createdUserID,
                        erp_jvmaster.confirmedByName FROM erp_generalledger 
                        LEFT JOIN chartofaccounts ON erp_generalledger.glCode = chartofaccounts.AccountCode
                        LEFT JOIN employees ON erp_generalledger.documentFinalApprovedBy = employees.empID
                        INNER JOIN erp_jvmaster ON erp_jvmaster.companySystemID = erp_generalledger.companySystemID 
                        AND erp_jvmaster.documentSystemID = erp_generalledger.documentSystemID 
                        AND erp_jvmaster.jvMasterAutoId = erp_generalledger.documentSystemCode
                        WHERE erp_generalledger.documentSystemID = 17 
                        AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND erp_jvmaster.jvType IN (' . join(',', $jvType) . ')
                        AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '")
                        as a GROUP BY a.documentSystemCode';
        }

        $output = \DB::select($query);
        //dd(DB::getQueryLog());
        return $output;
    }

    public function processColumnTemplateData($headers, $outputCollect, $outputDetail, $columnKeys, $uncategorizeData, $companyWiseGrandTotal, $outputOpeningBalance, $request)
    {

        $companyData = Company::all();
        $companyCodes = [];
        $uncategorizeDetailArr = [];
        $uncategorizeArr = [];

        foreach ($companyData as $key => $value) {
            $companyCodes[$value->companySystemID] = $value->CompanyID;
        }

        $uncategorizeArr['columnData'] = [];
        if (isset($uncategorizeData['output'])) {
            foreach ($uncategorizeData['output'] as $key1 => $value1) {
                if (!is_null($value1->compID)) {
                    foreach ($columnKeys as $key => $val) {
                        $companyID = (isset($companyCodes[$value1->compID])) ? $companyCodes[$value1->compID] : $value1->compID;
                        $uncategorizeArr['columnData'][$companyID][$val] = $value1->$val;
                    }
                }
            }
        }

        if (isset($uncategorizeData['outputDetail'])) {
            $temp = [];
            foreach ($uncategorizeData['outputDetail'] as $key1 => $value1) {
                foreach ($columnKeys as $key => $val) {

                    $temp[$value1->chartOfAccountSystemID]['chartOfAccountSystemID'] = $value1->chartOfAccountSystemID;
                    $temp[$value1->chartOfAccountSystemID]['glCode'] = $value1->glCode;
                    $temp[$value1->chartOfAccountSystemID]['glDescription'] = $value1->glDescription;
                    $temp[$value1->chartOfAccountSystemID]['glAutoID'] = $value1->glAutoID;
                    $temp[$value1->chartOfAccountSystemID]['glAutoID'] = $value1->glAutoID;

                    foreach ($columnKeys as $key2 => $value) {
                        $companyID = (isset($companyCodes[$value1->compID])) ? $companyCodes[$value1->compID] : $value1->compID;
                        $temp[$value1->chartOfAccountSystemID]['columnData'][$companyID][$value] = $value1->$value;
                        if (isset($companyCodes[$value1->compID])) {
                            $companyHeaderData[$value1->compID]['companyCode'] = $companyCodes[$value1->compID];
                        }
                    }
                }
            }

            foreach ($temp as $key => $value) {
                $uncategorizeDetailArr[] = $value;
            }
        }

        $companyWiseGrandTotalArray = [];
        foreach ($companyWiseGrandTotal as $key => $value) {
            if ($key != "") {
                $companyID = (isset($companyCodes[$key])) ? $companyCodes[$key] : $key;
                $companyWiseGrandTotalArray[$companyID] = $value[0];
            }
        }

        $companyHeaderData = [];
        $newHeaders = [];
        $removedFromArray = [];
        foreach ($headers as $key => $value) {
            $newHeaders[$value->detID]['detDescription'] = $value->detDescription;
            $newHeaders[$value->detID]['detID'] = $value->detID;
            $newHeaders[$value->detID]['sortOrder'] = $value->sortOrder;
            $newHeaders[$value->detID]['masterID'] = $value->masterID;
            $newHeaders[$value->detID]['isFinalLevel'] = $value->isFinalLevel;
            $newHeaders[$value->detID]['bgColor'] = $value->bgColor;
            $newHeaders[$value->detID]['fontColor'] = $value->fontColor;
            $newHeaders[$value->detID]['itemType'] = $value->itemType;
            $newHeaders[$value->detID]['hideHeader'] = $value->hideHeader;
            $newHeaders[$value->detID]['expanded'] = $value->expanded;

            foreach ($columnKeys as $key1 => $value1) {
                $companyID = (isset($companyCodes[$value->CompanyID])) ? $companyCodes[$value->CompanyID] : $value->CompanyID;
                $newHeaders[$value->detID]['columnData'][$companyID][$value1] = $value->$value1;
                if (isset($companyCodes[$value->CompanyID])) {
                    $companyHeaderData[$value->CompanyID]['companyCode'] = $companyCodes[$value->CompanyID];
                }
            }
        }

        $newHeaders = collect($newHeaders)->sortBy('sortOrder');

        $newOutputCollect = [];
        foreach ($outputCollect as $key => $value) {
            $newOutputCollect[$value->detID]['detDescription'] = $value->detDescription;
            $newOutputCollect[$value->detID]['detID'] = $value->detID;
            $newOutputCollect[$value->detID]['sortOrder'] = $value->sortOrder;
            $newOutputCollect[$value->detID]['masterID'] = $value->masterID;
            $newOutputCollect[$value->detID]['isFinalLevel'] = $value->isFinalLevel;
            $newOutputCollect[$value->detID]['bgColor'] = $value->bgColor;
            $newOutputCollect[$value->detID]['fontColor'] = $value->fontColor;
            $newOutputCollect[$value->detID]['itemType'] = $value->itemType;
            $newOutputCollect[$value->detID]['hideHeader'] = $value->hideHeader;
            $newOutputCollect[$value->detID]['expanded'] = $value->expanded;

            foreach ($columnKeys as $key1 => $value1) {
                $companyID = (isset($companyCodes[$value->CompanyID])) ? $companyCodes[$value->CompanyID] : $value->CompanyID;
                $newOutputCollect[$value->detID]['columnData'][$companyID][$value1] = $value->$value1;
                if (isset($companyCodes[$value->CompanyID])) {
                    $companyHeaderData[$value->CompanyID]['companyCode'] = $companyCodes[$value->CompanyID];
                }
            }
        }


        $newOutputDetail = [];
        foreach ($outputDetail as $key => $value) {
            $newOutputDetail[$value->glAutoID]['glCode'] = $value->glCode;
            $newOutputDetail[$value->glAutoID]['glDescription'] = $value->glDescription;
            $newOutputDetail[$value->glAutoID]['glAutoID'] = $value->glAutoID;
            $newOutputDetail[$value->glAutoID]['templateDetailID'] = $value->templateDetailID;
            $newOutputDetail[$value->glAutoID]['linkCatType'] = $value->linkCatType;
            $newOutputDetail[$value->glAutoID]['templateCatType'] = $value->templateCatType;

            foreach ($columnKeys as $key1 => $value1) {
                $companyID = (isset($companyCodes[$value->compID])) ? $companyCodes[$value->compID] : $value->compID;
                $newOutputDetail[$value->glAutoID]['columnData'][$companyID][$value1] = $value->$value1;
                if (isset($companyCodes[$value->compID])) {
                    $companyHeaderData[$value->compID]['companyCode'] = $companyCodes[$value->compID];
                }
            }
        }

        $firstLevel = false;
        $secondLevel = false;
        $thirdLevel = false;
        $fourthLevel = false;
        $finalHeaders = [];
        foreach ($newHeaders as $key => $val) {
            $temp = [];
            foreach ($val as $key3 => $value3) {
                $temp[$key3] = $value3;
            }
            $details = collect($newOutputCollect)->where('masterID', $val['detID'])->sortBy('sortOrder')->values();
            foreach ($details as $key2 => $val2) {
                $temp2 = [];
                foreach ($val2 as $key4 => $value4) {
                    $temp2[$key4] = $value4;
                }
                $firstLevel = true;
                if ($val2['isFinalLevel'] == 1) {
                    $temp2['glCodes'] = collect($newOutputDetail)->where('templateDetailID', $val2['detID'])->sortBy('sortOrder')->values();
                } else {
                    $detailsTwo = collect($newOutputCollect)->where('masterID', $val2['detID'])->sortBy('sortOrder')->values();
                    $secondLevel = true;
                    foreach ($detailsTwo as $key7 => $val7) {
                        $temp3 = [];
                        foreach ($val7 as $key8 => $value8) {
                            $temp3[$key8] = $value8;
                        }
                        if ($val7['isFinalLevel'] == 1) {
                            $temp3['glCodes'] = collect($newOutputDetail)->where('templateDetailID', $val7['detID'])->sortBy('sortOrder')->values();
                        } else {
                            $detailsThree = collect($newOutputCollect)->where('masterID', $val7['detID'])->sortBy('sortOrder')->values();
                            $thirdLevel = true;
                            foreach ($detailsThree as $key9 => $val9) {
                                $temp4 = [];
                                foreach ($val9 as $key10 => $value10) {
                                    $temp4[$key10] = $value10;
                                }
                                if ($val9['isFinalLevel'] == 1) {
                                    $temp4['glCodes'] = collect($newOutputDetail)->where('templateDetailID', $val9['detID'])->sortBy('sortOrder')->values();
                                } else {
                                    $detailsFour = collect($newOutputCollect)->where('masterID', $val9['detID'])->sortBy('sortOrder')->values();
                                    $fourthLevel = true;
                                    foreach ($detailsFour as $key11 => $val11) {
                                        $temp5 = [];
                                        foreach ($val11 as $key12 => $value12) {
                                            $temp5[$key12] = $value12;
                                        }
                                        if ($val11['isFinalLevel'] == 1) {
                                            $temp5['glCodes'] = collect($newOutputDetail)->where('templateDetailID', $val11['detID'])->sortBy('sortOrder')->values();
                                        }
                                        $temp4['detail'][] = $temp5;
                                    }
                                }
                                $temp3['detail'][] = $temp4;
                            }
                        }
                        $temp2['detail'][] = $temp3;
                    }
                }
                $temp['detail'][] = $temp2;
            }
            if ($val['itemType'] != 3) {
                if (count($details) == 0) {
                    $removedFromArray[] = $key;
                }
            }
            $finalHeaders[] = $temp;
        }

        $headers = collect($finalHeaders)->forget($removedFromArray)->values();
        $companyHeaderData = collect($companyHeaderData)->sortBy('companyCode')->toArray();
        $companyHeaderColumns = [];
        foreach ($companyHeaderData as $key => $value) {
            $companyHeaderColumns[] = $value;
        }

        $outputOpeningBalanceArr = [];
        $outputClosingBalanceArr = [];
        if ($request->accountType == 3) {

            $lastColumn = collect($headers)->last();
            foreach ($outputOpeningBalance as $ke => $value) {
                $companyID = (isset($companyCodes[$value->companySystemID])) ? $companyCodes[$value->companySystemID] : $value->companySystemID;
                foreach ($columnKeys as $key => $val) {
                    if ($key == 0) {
                        $outputOpeningBalanceArr[$companyID][] = $value->openingBalance;
                        $outputClosingBalanceArr[$companyID][] = ((isset($lastColumn['columnData'][$companyID])) ? $lastColumn['columnData'][$companyID]->$val : 0) + $value->openingBalance;
                    } else {
                        $outputOpeningBalanceArr[$companyID][] = $outputClosingBalanceArr[$companyID][$key - 1];
                        $outputClosingBalanceArr[$companyID][] = ((isset($lastColumn['columnData'][$companyID])) ? $lastColumn['columnData'][$companyID]->$val : 0) + $outputClosingBalanceArr[$companyID][$key - 1];
                    }
                }
            }
        }

        return ['headers' => $headers, 'companyHeaderColumns' => $companyHeaderColumns, 'uncategorizeArr' => $uncategorizeArr, 'uncategorizeDetailArr' => $uncategorizeDetailArr, 'companyWiseGrandTotalArray' => $companyWiseGrandTotalArray, 'outputOpeningBalanceArr' => $outputOpeningBalanceArr, 'outputClosingBalanceArr' => $outputClosingBalanceArr, 'firstLevel' => $firstLevel, 'secondLevel' => $secondLevel, 'thirdLevel' => $thirdLevel, 'fourthLevel' => $fourthLevel];
    }

    public function downloadProjectUtilizationReport(Request $request)
    {
        $input = $request->all();

        $fromDate = (new Carbon($request->fromDate))->format('Y-m-d');
        $toDate = (new   Carbon($request->toDate))->format('Y-m-d');
        $projectID = $request->projectID;
        $projectDetail = ErpProjectMaster::with('currency', 'service_line')->where('id', $projectID)->first();

        $budgetConsumedData = BudgetConsumedData::with('purchase_order')->where('projectID', $projectID)->where('documentSystemID', 2)->get();

        $budgetAmount = BudgetConsumedData::where('projectID', $projectID)
            ->where('documentSystemID', 2)
            ->whereHas('purchase_order', function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('approvedDate', [$fromDate, $toDate]);
            })
            ->sum('consumedRptAmount');

        $budgetOpeningConsumption = BudgetConsumedData::where('projectID', $projectID)
            ->where('documentSystemID', 2)
            ->whereHas('purchase_order', function ($query) use ($fromDate, $toDate) {
                $query->whereDate('approvedDate', '<', $fromDate);
            })
            ->sum('consumedRptAmount');

        $detailsPOWise = BudgetConsumedData::with(['purchase_order_detail' => function ($query) use ($fromDate, $toDate) {
            $query->whereBetween('approvedDate', [$fromDate, $toDate]);
            }])
            ->whereHas('purchase_order_detail', function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('approvedDate', [$fromDate, $toDate]);
            })
            ->where('projectID', $projectID)
            ->where('documentSystemID', 2)
            ->selectRaw('sum(consumedRptAmount) as documentAmount, documentCode, documentSystemCode')
            ->groupBy('documentSystemCode')
            ->get();

        $getProjectAmounts = ProjectGlDetail::where('projectID', $projectID)->get();
        $projectAmount = collect($getProjectAmounts)->sum('amount');

        if ($projectAmount > 0) {
            $projectAmount = $projectAmount;
        } else {
            $projectAmount = 0;
        }


        if ($budgetAmount > 0) {
            $budgetAmount = $budgetAmount;
        } else {
            $budgetAmount = 0;
        }

        $openingBalance = $projectAmount - $budgetOpeningConsumption;

        $closingBalance = $openingBalance - $budgetAmount;

        $output = array(
            'projectDetail' => $projectDetail,
            'projectAmount' => $projectAmount,
            'budgetConsumedData' => $budgetConsumedData,
            'budgetConsumptionAmount' => $budgetAmount,
            'openingBalance' => $openingBalance,
            'closingBalance' => $closingBalance,
            'detailsPOWise' => $detailsPOWise
        );

        return \Excel::create('upload_budget_template', function ($excel) use ($output) {
            $excel->sheet('New sheet', function ($sheet) use ($output) {
                $sheet->loadView('export_report.project_utilization_report', $output);
            });
        })->download('xlsx');
    }

    public function exportReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'FTB':
                $reportTypeID = $request->reportTypeID;
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                $checkIsGroup = Company::find($request->companySystemID);
                $data = array();

                if ($reportTypeID == 'FTB') {
                    if ($request->reportSD == 'company_wise_summary') {
                        $companyID = "";
                        $checkIsGroup = Company::find($request->companySystemID);
                        if ($checkIsGroup->isGroup) {
                            $companyID = \Helper::getGroupCompany($request->companySystemID);
                        } else {
                            $companyID = (array)$request->companySystemID;
                        }

                        $subCompanies = Company::whereIn('companySystemID', $companyID)->get(['companySystemID', 'CompanyID', 'CompanyName']);

                        $output = $this->getTrialBalanceCompanyWise($request, $subCompanies);

                        if ($output) {
                            $x = 0;
                            foreach ($output as $val) {
                                $data[$x]['Account Code'] = $val->glCode;
                                $data[$x]['Account Description'] = $val->AccountDescription;
                                $data[$x]['Type'] = $val->glAccountType;
                                foreach ($subCompanies as $company) {
                                    $comCode = $company['CompanyID'];
                                    $data[$x][$comCode] = round($val->$comCode, 2);
                                }
                                $x++;
                            }
                        }
                    } else {
                        $output = $this->getTrialBalance($request);

                        $currencyIdLocal = 1;
                        $currencyIdRpt = 2;

                        $decimalPlaceCollectLocal = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                        $decimalPlaceUniqueLocal = array_unique($decimalPlaceCollectLocal);

                        $decimalPlaceCollectRpt = collect($output)->pluck('documentRptCurrencyID')->toArray();
                        $decimalPlaceUniqueRpt = array_unique($decimalPlaceCollectRpt);


                        if (!empty($decimalPlaceUniqueLocal)) {
                            $currencyIdLocal = $decimalPlaceUniqueLocal[0];
                        }

                        if (!empty($decimalPlaceUniqueRpt)) {
                            $currencyIdRpt = $decimalPlaceUniqueRpt[0];
                        }

                        $requestCurrencyLocal = CurrencyMaster::where('currencyID', $currencyIdLocal)->first();
                        $requestCurrencyRpt = CurrencyMaster::where('currencyID', $currencyIdRpt)->first();

                        $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                        $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

                        $currencyLocal = $requestCurrencyLocal->CurrencyCode;
                        $currencyRpt = $requestCurrencyRpt->CurrencyCode;

                        if ($output) {
                            $x = 0;
                            foreach ($output as $val) {
                                if ($request->reportSD == 'company_wise') {
                                    $data[$x]['Company ID'] = $val->companyID;
                                    $data[$x]['Company Name'] = $val->CompanyName;
                                }
                                $data[$x]['Account Code'] = $val->glCode;
                                $data[$x]['Account Description'] = $val->AccountDescription;
                                $data[$x]['Type'] = $val->glAccountType;

                                if ($checkIsGroup->isGroup == 0) {
                                    $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = round($val->documentLocalAmountDebit, $decimalPlaceLocal);
                                    $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = round($val->documentLocalAmountCredit, $decimalPlaceLocal);
                                }

                                $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->documentRptAmountDebit, $decimalPlaceRpt);
                                $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->documentRptAmountCredit, $decimalPlaceRpt);
                                $x++;
                            }
                        }
                    }
                } else if ($reportTypeID == 'FTBM') {
                    $result = $this->getTrialBalanceMonthWise($request);
                    $output = $result['data'];
                    $headers = $result['headers'];


                    $currencyIdLocal = 1;
                    $currencyIdRpt = 2;

                    $decimalPlaceCollectLocal = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                    $decimalPlaceUniqueLocal = array_unique($decimalPlaceCollectLocal);

                    $decimalPlaceCollectRpt = collect($output)->pluck('documentRptCurrencyID')->toArray();
                    $decimalPlaceUniqueRpt = array_unique($decimalPlaceCollectRpt);


                    if (!empty($decimalPlaceUniqueLocal)) {
                        $currencyIdLocal = $decimalPlaceUniqueLocal[0];
                    }

                    if (!empty($decimalPlaceUniqueRpt)) {
                        $currencyIdRpt = $decimalPlaceUniqueRpt[0];
                    }

                    $requestCurrencyLocal = CurrencyMaster::where('currencyID', $currencyIdLocal)->first();
                    $requestCurrencyRpt = CurrencyMaster::where('currencyID', $currencyIdRpt)->first();

                    $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                    $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

                    $currencyLocal = $requestCurrencyLocal->CurrencyCode;
                    $currencyRpt = $requestCurrencyRpt->CurrencyCode;

                    $decimalPlace = 2;
                    if ($request->currencyID == 1) {
                        $decimalPlace = $decimalPlaceLocal;
                    } else if ($request->currencyID == 2) {
                        $decimalPlace = $decimalPlaceRpt;
                    }

                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            /*if ($request->reportSD == 'company_wise') {
                                $data[$x]['Company ID'] = $val->companyID;
                                $data[$x]['Company Name'] = $val->CompanyName;
                            }*/
                            $data[$x]['Account Code'] = $val->glCode;
                            $data[$x]['Account Description'] = $val->AccountDescription;
                            $data[$x]['Type'] = $val->glAccountType;
                            $data[$x]['Opening Balance'] = round($val->Opening, $decimalPlace);
                            foreach ($headers as $header) {
                                $closing = $header . 'Closing';
                                $data[$x][$header] = round($val->$header, $decimalPlace);
                                $data[$x][$header . ' Closing'] = round($val->$closing, $decimalPlace);
                            }

                            $x++;
                        }
                    }
                }
                \Excel::create('trial_balance', function ($excel) use ($data) {
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
            case 'FTBD':
                $reportTypeID = $request->reportTypeID;
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));

                $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getTrialBalanceDetails($request);
                $currencyIdLocal = 1;
                $currencyIdRpt = 2;

                $decimalPlaceCollectLocal = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                $decimalPlaceUniqueLocal = array_unique($decimalPlaceCollectLocal);

                $decimalPlaceCollectRpt = collect($output)->pluck('documentRptCurrencyID')->toArray();
                $decimalPlaceUniqueRpt = array_unique($decimalPlaceCollectRpt);


                if (!empty($decimalPlaceUniqueLocal)) {
                    $currencyIdLocal = $decimalPlaceUniqueLocal[0];
                }

                if (!empty($decimalPlaceUniqueRpt)) {
                    $currencyIdRpt = $decimalPlaceUniqueRpt[0];
                }

                $requestCurrencyLocal = CurrencyMaster::where('currencyID', $currencyIdLocal)->first();
                $requestCurrencyRpt = CurrencyMaster::where('currencyID', $currencyIdRpt)->first();

                $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

                $currencyLocal = $requestCurrencyLocal->CurrencyCode;
                $currencyRpt = $requestCurrencyRpt->CurrencyCode;

                $data = array();
                if ($output) {
                    $x = 0;
                    foreach ($output as $val) {
                        if ($request->reportSD == 'company_wise') {
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Company Name'] = $val->CompanyName;
                        }
                        $data[$x]['Document Code'] = $val->documentCode;
                        $data[$x]['Document Date'] = \Helper::dateFormat($val->documentDate);
                        $data[$x]['Document Narration'] = $val->documentNarration;

                        if ($checkIsGroup->isGroup == 0) {
                            $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = round($val->localDebit, $decimalPlaceLocal);
                            $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = round($val->localCredit, $decimalPlaceLocal);
                        }

                        $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->rptDebit, $decimalPlaceRpt);
                        $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->rptCredit, $decimalPlaceRpt);
                        $x++;
                    }
                }

                \Excel::create('trial_balance_details', function ($excel) use ($data) {
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
            case 'FGL':
                $reportTypeID = $request->reportTypeID;
                $reportSD = $request->reportSD;
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                $checkIsGroup = Company::find($request->companySystemID);
                $data = array();
                $output = $this->getGeneralLedger($request);


                $currencyIdLocal = 1;
                $currencyIdRpt = 2;

                $decimalPlaceCollectLocal = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                $decimalPlaceUniqueLocal = array_unique($decimalPlaceCollectLocal);

                $decimalPlaceCollectRpt = collect($output)->pluck('documentRptCurrencyID')->toArray();
                $decimalPlaceUniqueRpt = array_unique($decimalPlaceCollectRpt);


                if (!empty($decimalPlaceUniqueLocal)) {
                    $currencyIdLocal = $decimalPlaceUniqueLocal[0];
                }

                if (!empty($decimalPlaceUniqueRpt)) {
                    $currencyIdRpt = $decimalPlaceUniqueRpt[0];
                }

                $extraColumns = [];
                if (isset($request->extraColoumns) && count($request->extraColoumns) > 0) {
                    $extraColumns = collect($request->extraColoumns)->pluck('id')->toArray();
                }

                $requestCurrencyLocal = CurrencyMaster::where('currencyID', $currencyIdLocal)->first();
                $requestCurrencyRpt = CurrencyMaster::where('currencyID', $currencyIdRpt)->first();

                $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

                $currencyLocal = $requestCurrencyLocal->CurrencyCode;
                $currencyRpt = $requestCurrencyRpt->CurrencyCode;

                if ($reportSD == "glCode_wise") {
                    if (!empty($output)) {
                        $outputArr = array();
                        foreach ($output as $val1) {
                            $outputArr[$val1->glCode . ' - ' . $val1->AccountDescription][] = $val1;
                        }

                        $x = 0;
                        $total = array();
                        $total['documentLocalAmountDebit'] = array_sum(collect($output)->pluck('localDebit')->toArray());
                        $total['documentLocalAmountCredit'] = array_sum(collect($output)->pluck('localCredit')->toArray());
                        $total['documentRptAmountDebit'] = array_sum(collect($output)->pluck('rptDebit')->toArray());
                        $total['documentRptAmountCredit'] = array_sum(collect($output)->pluck('rptCredit')->toArray());
                        foreach ($outputArr as $key => $values) {
                            $data[$x][''] = $key;
                            $x++;
                            $data[$x]['Company ID'] = 'Company ID';
                            $data[$x]['Company Name'] = 'Company Name';
                            $data[$x]['GL  Type'] = 'GL  Type';
                            $data[$x]['Template Description'] = 'Template Description';
                            $data[$x]['Document Type'] = 'Document Type';
                            $data[$x]['Document Number'] = 'Document Number';
                            $data[$x]['Date'] = 'Date';
                            $data[$x]['Document Narration'] = 'Document Narration';
                            $data[$x]['Service Line'] = 'Service Line';
                            $data[$x]['Contract'] = 'Contract';

                            if (in_array('confi_name', $extraColumns)) {
                                $data[$x]['Confirmed By'] = 'Confirmed By';
                            }

                            if (in_array('confi_date', $extraColumns)) {
                                $data[$x]['Confirmed Date'] = 'Confirmed Date';
                            }

                            if (in_array('app_name', $extraColumns)) {
                                $data[$x]['Approved By'] = 'Approved By';
                            }

                            if (in_array('app_date', $extraColumns)) {
                                $data[$x]['Approved Date'] = 'Approved Date';
                            }
                            $data[$x]['Supplier/Customer'] = 'Supplier/Customer';
                            if ($checkIsGroup->isGroup == 0) {
                                $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = 'Debit (Local Currency - ' . $currencyLocal . ')';
                                $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = 'Credit (Local Currency - ' . $currencyLocal . ')';
                            }
                            $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = 'Debit (Reporting Currency - ' . $currencyRpt . ')';
                            $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = 'Credit (Reporting Currency - ' . $currencyRpt . ')';
                            if (!empty($values)) {
                                $subTotalDebitRpt = 0;
                                $subTotalCreditRpt = 0;
                                $subTotalDebitLocal = 0;
                                $subTotalCreditRptLocal = 0;
                                foreach ($values as $val) {
                                    $x++;
                                    $data[$x]['Company ID'] = $val->companyID;
                                    $data[$x]['Company Name'] = $val->CompanyName;
                                    $data[$x]['GL  Type'] = $val->glAccountType;
                                    $data[$x]['Template Description'] = $val->templateDetailDescription;
                                    $data[$x]['Document Type'] = $val->documentID;
                                    $data[$x]['Document Number'] = $val->documentCode;
                                    $data[$x]['Date'] = \Helper::dateFormat($val->documentDate);
                                    $data[$x]['Document Narration'] = $val->documentNarration;
                                    $data[$x]['Service Line'] = $val->serviceLineCode;
                                    $data[$x]['Contract'] = $val->clientContractID;

                                    if (in_array('confi_name', $extraColumns)) {
                                        $data[$x]['Confirmed By'] = $val->confirmedBy;
                                    }

                                    if (in_array('confi_date', $extraColumns)) {
                                        $data[$x]['Confirmed Date'] = \Helper::dateFormat($val->documentConfirmedDate);
                                    }

                                    if (in_array('app_name', $extraColumns)) {
                                        $data[$x]['Approved By'] = $val->approvedBy;
                                    }

                                    if (in_array('app_date', $extraColumns)) {
                                        $data[$x]['Approved Date'] = \Helper::dateFormat($val->documentFinalApprovedDate);
                                    }
                                    $data[$x]['Supplier/Customer'] = $val->isCustomer;
                                    if ($checkIsGroup->isGroup == 0) {
                                        $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = round($val->localDebit, $decimalPlaceLocal);
                                        $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = round($val->localCredit, $decimalPlaceLocal);
                                    }

                                    $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->rptDebit, $decimalPlaceRpt);
                                    $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->rptCredit, $decimalPlaceRpt);
                                    $subTotalDebitRpt += $val->rptDebit;
                                    $subTotalCreditRpt += $val->rptCredit;

                                    $subTotalDebitLocal += $val->localDebit;
                                    $subTotalCreditRptLocal += $val->localCredit;
                                }
                                $x++;
                                $data[$x]['Company ID'] = '';
                                $data[$x]['Company Name'] = '';
                                $data[$x]['GL  Type'] = '';
                                $data[$x]['Template Description'] = '';
                                $data[$x]['Document Type'] = '';
                                $data[$x]['Document Number'] = '';
                                $data[$x]['Date'] = '';
                                $data[$x]['Document Narration'] = '';
                                $data[$x]['Service Line'] = '';
                                $data[$x]['Contract'] = '';

                                if (in_array('confi_name', $extraColumns)) {
                                    $data[$x]['Confirmed By'] = '';
                                }

                                if (in_array('confi_date', $extraColumns)) {
                                    $data[$x]['Confirmed Date'] = '';
                                }

                                if (in_array('app_name', $extraColumns)) {
                                    $data[$x]['Approved By'] = '';
                                }

                                if (in_array('app_date', $extraColumns)) {
                                    $data[$x]['Approved Date'] = '';
                                }
                                $data[$x]['Supplier/Customer'] = 'Total';
                                if ($checkIsGroup->isGroup == 0) {
                                    $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = round($subTotalDebitLocal, $decimalPlaceLocal);
                                    $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = round($subTotalCreditRptLocal, $decimalPlaceLocal);
                                }

                                $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = round($subTotalDebitRpt, $decimalPlaceRpt);
                                $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = round($subTotalCreditRpt, $decimalPlaceRpt);

                                $x++;
                                $data[$x][''] = '';
                                $data[$x][''] = '';
                                $data[$x][''] = '';
                                $data[$x][''] = '';
                                $data[$x][''] = '';
                                $data[$x][''] = '';
                                $data[$x][''] = '';
                                $data[$x][''] = '';
                                $data[$x][''] = '';
                                if ($checkIsGroup->isGroup == 0) {
                                    $data[$x][''] = '';
                                    $data[$x][''] = '';
                                }
                                $data[$x][''] = '';
                                $data[$x][''] = '';
                            }
                        }
                        $x++;
                        $data[$x]['Company ID'] = '';
                        $data[$x]['Company Name'] = '';
                        $data[$x]['GL  Type'] = '';
                        $data[$x]['Template Description'] = '';
                        $data[$x]['Document Type'] = '';
                        $data[$x]['Document Number'] = '';
                        $data[$x]['Date'] = '';
                        $data[$x]['Document Narration'] = '';
                        $data[$x]['Service Line'] = '';
                        $data[$x]['Contract'] = '';

                        if (in_array('confi_name', $extraColumns)) {
                            $data[$x]['Confirmed By'] = '';
                        }

                        if (in_array('confi_date', $extraColumns)) {
                            $data[$x]['Confirmed Date'] = '';
                        }

                        if (in_array('app_name', $extraColumns)) {
                            $data[$x]['Approved By'] = '';
                        }

                        if (in_array('app_date', $extraColumns)) {
                            $data[$x]['Approved Date'] = '';
                        }
                        $data[$x]['Supplier/Customer'] = 'Grand Total';
                        if ($checkIsGroup->isGroup == 0) {
                            $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = round($total['documentLocalAmountDebit'], $decimalPlaceLocal);
                            $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = round($total['documentLocalAmountCredit'], $decimalPlaceLocal);
                        }
                        $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = round($total['documentRptAmountDebit'], $decimalPlaceRpt);
                        $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = round($total['documentRptAmountCredit'], $decimalPlaceRpt);
                    }
                } else {
                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Company Name'] = $val->CompanyName;
                            $data[$x]['GL Code'] = $val->glCode;
                            $data[$x]['Account Description'] = $val->AccountDescription;
                            $data[$x]['GL  Type'] = $val->glAccountType;
                            $data[$x]['Template Description'] = $val->templateDetailDescription;
                            $data[$x]['Document Type'] = $val->documentID;
                            $data[$x]['Document Number'] = $val->documentCode;
                            $data[$x]['Date'] = \Helper::dateFormat($val->documentDate);
                            $data[$x]['Document Narration'] = $val->documentNarration;
                            $data[$x]['Service Line'] = $val->serviceLineCode;
                            $data[$x]['Contract'] = $val->clientContractID;
                            $data[$x]['Supplier/Customer'] = $val->isCustomer;
                            if (in_array('confi_name', $extraColumns)) {
                                $data[$x]['Confirmed By'] = $val->confirmedBy;
                            }

                            if (in_array('confi_date', $extraColumns)) {
                                $data[$x]['Confirmed Date'] = \Helper::dateFormat($val->documentConfirmedDate);
                            }

                            if (in_array('app_name', $extraColumns)) {
                                $data[$x]['Approved By'] = $val->approvedBy;
                            }

                            if (in_array('app_date', $extraColumns)) {
                                $data[$x]['Approved Date'] = \Helper::dateFormat($val->documentFinalApprovedDate);
                            }

                            if ($checkIsGroup->isGroup == 0) {
                                $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = round($val->localDebit, $decimalPlaceLocal);
                                $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = round($val->localCredit, $decimalPlaceLocal);
                            }

                            $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->rptDebit, $decimalPlaceRpt);
                            $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->rptCredit, $decimalPlaceRpt);
                            $x++;
                        }
                    }
                }

                \Excel::create('general_ledger', function ($excel) use ($data) {
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
            case 'FTD':
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));

                $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                $checkIsGroup = Company::find($request->companySystemID);

                $output = $this->getTaxDetailQry($request);
                $data = array();
                if ($request->tempType == 1) {
                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Document Code'] = $val->bookingInvCode;
                            $data[$x]['Document Date'] = $val->bookingDate;
                            $data[$x]['Invoice No'] = $val->supplierInvoiceNo;
                            $data[$x]['Invoice Date'] = $val->supplierInvoiceDate;
                            $data[$x]['Narration'] = $val->comments;
                            $data[$x]['Supplier Code'] = $val->primarySupplierCode;
                            $data[$x]['Supplier Name'] = $val->supplierName;
                            $data[$x]['Currency'] = $val->CurrencyCode;
                            $data[$x]['Value'] = ($val->bookingAmountTrans - $val->taxTotalAmount);
                            $data[$x]['Discount'] = 0;
                            $data[$x]['Net Value'] = ($val->bookingAmountTrans - $val->taxTotalAmount);
                            $data[$x]['VAT'] = $val->taxTotalAmount;
                            $data[$x]['Due Amount'] = $val->bookingAmountTrans;
                            $data[$x]['Posted Date'] = $val->postedDate;
                            $x++;
                        }
                    }
                } else {
                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Document Code'] = $val->bookingInvCode;
                            $data[$x]['Document Date'] = $val->bookingDate;
                            $data[$x]['Invoice No'] = $val->bookingInvCode;
                            $data[$x]['Invoice Date'] = $val->bookingDate;
                            $data[$x]['Narration'] = $val->comments;
                            $data[$x]['Customer Code'] = $val->CutomerCode;
                            $data[$x]['Customer Short Code'] = $val->customerShortCode;
                            $data[$x]['Customer Name'] = $val->CustomerName;
                            $data[$x]['Currency'] = $val->CurrencyCode;
                            $data[$x]['Value'] = $val->bookingAmountTrans;
                            $data[$x]['Discount'] = 0;
                            $data[$x]['Net Value'] = $val->bookingAmountTrans;
                            $data[$x]['VAT'] = $val->taxTotalAmount;
                            $data[$x]['Due Amount'] = ($val->bookingAmountTrans + $val->taxTotalAmount);
                            $data[$x]['Posted Date'] = $val->postedDate;
                            $x++;
                        }
                    }
                }

                \Excel::create('trial_balance_details', function ($excel) use ($data) {
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
            case 'JVD':

                $reportTypeID = $request->reportTypeID;
                $type = $request->type;
                $checkIsGroup = Company::find($request->companySystemID);

                $output = $this->jvDetailQry($request);
                $data = array();
                $currencyIdLocal = 1;
                $currencyIdRpt = 2;

                $decimalPlaceCollectLocal = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                $decimalPlaceUniqueLocal = array_unique($decimalPlaceCollectLocal);

                $decimalPlaceCollectRpt = collect($output)->pluck('documentRptCurrencyID')->toArray();
                $decimalPlaceUniqueRpt = array_unique($decimalPlaceCollectRpt);


                if (!empty($decimalPlaceUniqueLocal)) {
                    $currencyIdLocal = $decimalPlaceUniqueLocal[0];
                }

                if (!empty($decimalPlaceUniqueRpt)) {
                    $currencyIdRpt = $decimalPlaceUniqueRpt[0];
                }

                $requestCurrencyLocal = CurrencyMaster::where('currencyID', $currencyIdLocal)->first();
                $requestCurrencyRpt = CurrencyMaster::where('currencyID', $currencyIdRpt)->first();

                $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

                $currencyLocal = $requestCurrencyLocal->CurrencyCode;
                $currencyRpt = $requestCurrencyRpt->CurrencyCode;

                if ($output) {
                    $x = 0;
                    foreach ($output as $val) {

                        $data[$x]['Company ID'] = $val->companyID;
                        //$data[$x]['Company Name'] = $val->CompanyName;
                        $data[$x]['Document Code'] = $val->documentCode;
                        $data[$x]['Document Date'] = \Helper::dateFormat($val->documentDate);
                        $data[$x]['Year'] = $val->YEAR;
                        $data[$x]['Document Narration'] = $val->documentNarration;
                        if ($reportTypeID == 'JVDD') {
                            $data[$x]['Account Code'] = $val->glCode;
                            $data[$x]['Account Description'] = $val->AccountDescription;
                            $data[$x]['Type'] = $val->glAccountType;
                        }
                        if ($checkIsGroup->isGroup == 0) {
                            $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = round($val->debitAmountLocal, $decimalPlaceLocal);
                            $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = round($val->creditAmountLocal, $decimalPlaceLocal);
                        }

                        $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->debitAmountRpt, $decimalPlaceRpt);
                        $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->creditAmountRpt, $decimalPlaceRpt);
                        $data[$x]['Confirmed Date'] = \Helper::dateFormat($val->confirmedDate);
                        $data[$x]['Confirmed By'] = $val->confirmedByName;
                        $data[$x]['Approved Date'] = \Helper::dateFormat($val->documentFinalApprovedDate);
                        $data[$x]['Approved By'] = $val->FinalApprovedBy;
                        $x++;
                    }
                }

                \Excel::create('jv_detail', function ($excel) use ($data) {
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


    public function getTrialBalance($request)
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

        //DB::enableQueryLog();

        $isCompanyWise = '';
        $isCompanyWiseGL = '';
        $isCompanyWiseGLGroupBy = '';

        if ($request->reportSD == 'company_wise') {
            $isCompanyWise = 'companySystemID,';
            $isCompanyWiseGL = 'erp_generalledger.companySystemID,';
        }

        $serviceLines = join(',', array_map(function ($sl) {
            return $sl['serviceLineSystemID'];
        }, $request->selectedServicelines));

        $query = 'SELECT
                        companySystemID,
                        companyID,
                        CompanyName,
                        chartOfAccountSystemID,
                        glCode,
                        AccountDescription,
                        glAccountType,
                        documentLocalCurrencyID,
                        IF((SUM( documentLocalAmount))<0,0,(SUM(documentLocalAmount))) AS documentLocalAmountDebit,
                        IF((SUM( documentLocalAmount))<0,(SUM(documentLocalAmount*-1)),0) AS documentLocalAmountCredit,
                        documentRptCurrencyID,
                        IF((SUM( documentRptAmount))<0,0,(SUM(documentRptAmount))) AS documentRptAmountDebit,
                        IF((SUM( documentRptAmount))<0,(SUM(documentRptAmount*-1)),0) AS documentRptAmountCredit
                    FROM
                        (
                    SELECT
                        * 
                    FROM
                        (
                    SELECT
                        erp_generalledger.companySystemID,
                        erp_generalledger.companyID,
                        companymaster.CompanyName,
                        "" AS documentDate,
                        0 AS chartOfAccountSystemID,
                        "-" AS glCode,
                        "BS" AS glAccountType,
                        "Retained Earning" AS AccountDescription,
                        erp_generalledger.documentLocalCurrencyID,
                        erp_generalledger.documentLocalCurrencyER,
                        sum( erp_generalledger.documentLocalAmount *- 1 ) AS documentLocalAmount,
                        erp_generalledger.documentRptCurrencyID,
                        erp_generalledger.documentRptCurrencyER,
                        sum( erp_generalledger.documentRptAmount * - 1 ) documentRptAmount 
                    FROM
                        erp_generalledger
                        LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                        INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                    WHERE
                        erp_generalledger.glAccountType = "BS" 
                        AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                        AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '" -- filter by from date
                    GROUP BY
                        ' . $isCompanyWiseGL . '
                        glCode
                        ) AS ERP_qry_TBBS_BF_sum -- ERP_qry_TBBS_BF_sum
                    UNION ALL
                    SELECT
                        * 
                    FROM
                        (
                        SELECT
                            erp_generalledger.companySystemID,
                            erp_generalledger.companyID,
                            companymaster.CompanyName,
                            erp_generalledger.documentDate AS documentDate,
                            erp_generalledger.chartOfAccountSystemID,
                            erp_generalledger.glCode AS glCode,
                            erp_generalledger.glAccountType AS glAccountType,
                            chartofaccounts.AccountDescription AS AccountDescription,
                            erp_generalledger.documentLocalCurrencyID,
                            erp_generalledger.documentLocalCurrencyER,
                            erp_generalledger.documentLocalAmount AS documentLocalAmount,
                            erp_generalledger.documentRptCurrencyID,
                            erp_generalledger.documentRptCurrencyER,
                            erp_generalledger.documentRptAmount documentRptAmount 
                        FROM
                            erp_generalledger
                            LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                            INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                        WHERE
                            erp_generalledger.glAccountType = "BS" 
                            AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                               AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                            AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '"
                        ) AS ERP_qry_TBBS_BF -- filter by from date ERP_qry_TBBS_BF;
                    UNION ALL
                    SELECT
                        * 
                    FROM
                        (
                        SELECT
                            erp_generalledger.companySystemID,
                            erp_generalledger.companyID,
                            companymaster.CompanyName,
                            erp_generalledger.documentDate AS documentDate,
                            erp_generalledger.chartOfAccountSystemID,
                            erp_generalledger.glCode AS glCode,
                            erp_generalledger.glAccountType AS glAccountType,
                            chartofaccounts.AccountDescription AS AccountDescription,
                            erp_generalledger.documentLocalCurrencyID,
                            erp_generalledger.documentLocalCurrencyER,
                            erp_generalledger.documentLocalAmount AS documentLocalAmount,
                            erp_generalledger.documentRptCurrencyID,
                            erp_generalledger.documentRptCurrencyER,
                            erp_generalledger.documentRptAmount documentRptAmount 
                        FROM
                            erp_generalledger
                            LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                            INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                        WHERE
                            chartofaccounts.catogaryBLorPL = "BS" 
                            AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                               AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                            AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        ) AS ERP_qry_TBBS -- ERP_qry_TBBS
                    UNION ALL
                    SELECT
                        * 
                    FROM
                        (
                        SELECT
                            erp_generalledger.companySystemID,
                            erp_generalledger.companyID,
                            companymaster.CompanyName,
                            erp_generalledger.documentDate AS documentDate,
                            erp_generalledger.chartOfAccountSystemID,
                            erp_generalledger.glCode AS glCode,
                            erp_generalledger.glAccountType AS glAccountType,
                            chartofaccounts.AccountDescription AS AccountDescription,
                            erp_generalledger.documentLocalCurrencyID,
                            erp_generalledger.documentLocalCurrencyER,
                            erp_generalledger.documentLocalAmount AS documentLocalAmount,
                            erp_generalledger.documentRptCurrencyID,
                            erp_generalledger.documentRptCurrencyER,
                            erp_generalledger.documentRptAmount documentRptAmount 
                        FROM
                            erp_generalledger
                            LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                            INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                        WHERE
                            chartofaccounts.catogaryBLorPL = "PL" 
                            AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                               AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                            AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        ) AS ERP_qry_TBPL 
                        ) AS FINAL 
                    GROUP BY
                        ' . $isCompanyWise . 'chartOfAccountSystemID
                        order by glCode';


        $output = \DB::select($query);
        //dd(DB::getQueryLog());
        return $output;
    }

    public function getTrialBalanceMonthWise($request)
    {
        $fromDate1 = new Carbon($request->fromDate);
        $fromDate = $fromDate1->format('Y-m-d');

        $toDate1 = new Carbon($request->toDate);
        $toDate = $toDate1->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $serviceLines = join(',', array_map(function ($sl) {
            return $sl['serviceLineSystemID'];
        }, $request->selectedServicelines));

        //DB::enableQueryLog();

        $isCompanyWise = '';
        $isCompanyWiseGL = '';
        $isCompanyWiseGLGroupBy = '';

        if ($request->reportSD == 'company_wise') {
            $isCompanyWise = 'companySystemID,';
            $isCompanyWiseGL = 'erp_generalledger.companySystemID,';
        }

        $currencyClm = 'erp_generalledger.documentRptAmount';
        if ($request->currencyID == 1) {
            $currencyClm = 'erp_generalledger.documentLocalAmount';
        }

        $defaultMonth = array(
            'Jan',
            'Feb',
            'March',
            'April',
            'May',
            'June',
            'July',
            'Aug',
            'Sept',
            'Oct',
            'Nov',
            'Dece'
        );

        $defaultMonthSum = array(
            "IF
                    ( MONTH ( erp_generalledger.documentDate ) = 1, " . $currencyClm . ", 0 ) AS Jan",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 2, " . $currencyClm . ", 0 ) AS Feb",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 3, " . $currencyClm . ", 0 ) AS March",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 4, " . $currencyClm . ", 0 ) AS April",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 5, " . $currencyClm . ", 0 ) AS May",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 6, " . $currencyClm . ", 0 ) AS June",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 7, " . $currencyClm . ", 0 ) AS July",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 8, " . $currencyClm . ", 0 ) AS Aug",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 9, " . $currencyClm . ", 0 ) AS Sept",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 10, " . $currencyClm . ", 0 ) AS Oct",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 11, " . $currencyClm . ", 0 ) AS Nov",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 12, " . $currencyClm . ", 0 ) AS Dece"
        );

        $monthClosing = array(); //'sum(Opening + Jan) AS JanClosing';

        $availableMonth = array();
        $monthSum = array();
        $monthZero = array();
        $month = array();

        $totalMonth = "";

        foreach ($defaultMonth as $key => $value) {
            if (($key + 1) <= intval($toDate1->format('m')) && ($key + 1) >= intval($fromDate1->format('m'))) {
                array_push($availableMonth, $value);
            }
        }

        foreach ($defaultMonthSum as $key => $value) {
            if (($key + 1) <= intval($toDate1->format('m')) && ($key + 1) >= intval($fromDate1->format('m'))) {
                array_push($month, $value);
            }
        }


        foreach ($availableMonth as $value) {
            array_push($monthSum, 'sum(' . $value . ') as ' . $value);
            array_push($monthZero, '0 as ' . $value);
        }

        foreach ($availableMonth as $key => $value) {
            if ($key == 0) {
                $totalMonth = $totalMonth . $value;
            } else {
                $totalMonth = $totalMonth . '+' . $value;
            }
            $opening = "sum(Opening +" . $totalMonth . ") AS " . $value . 'Closing';

            array_push($monthClosing, $opening);
        }


        $monthArray = implode(",", $availableMonth);
        $monthSum = implode(",", $monthSum);
        $monthZero = implode(",", $monthZero);
        $month = implode(",", $month);
        $monthClosing = implode(",", $monthClosing);

        /*$monthArray = 'Jan,
                 Feb,
                 March,
                 April,
                 May,
                 June,
                 July,
                 Aug,
                 Sept,
                 Oct,
                 Nov,
                 Dece';

        $monthSum = 'sum(Jan) as Jan,
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
                    sum(Dece) as Dece';

        $monthZero = '0 AS Jan,
                        0 AS Feb,
                        0 AS March,
                        0 AS April,
                        0 AS May,
                        0 AS June,
                        0 AS July,
                        0 AS Aug,
                        0 AS Sept,
                        0 AS Oct,
                        0 AS Nov,
                        0 AS Dece';

        $month = 'IF
                    ( MONTH ( erp_generalledger.documentDate ) = 1, ' . $currencyClm . ', 0 ) AS Jan,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 2, ' . $currencyClm . ', 0 ) AS Feb,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 3, ' . $currencyClm . ', 0 ) AS March,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 4, ' . $currencyClm . ', 0 ) AS April,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 5, ' . $currencyClm . ', 0 ) AS May,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 6, ' . $currencyClm . ', 0 ) AS June,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 7, ' . $currencyClm . ', 0 ) AS July,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 8, ' . $currencyClm . ', 0 ) AS Aug,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 9, ' . $currencyClm . ', 0 ) AS Sept,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 10, ' . $currencyClm . ', 0 ) AS Oct,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 11, ' . $currencyClm . ', 0 ) AS Nov,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 12, ' . $currencyClm . ', 0 ) AS Dece';


        $monthClosing = 'sum(Opening + Jan) AS JanClosing,
                        sum(Opening + Jan + Feb) AS FebClosing,
                        sum(Opening + Jan + Feb + March) AS MarchClosing,
                        sum(Opening + Jan + Feb + March + April) AS AprilClosing,
                        sum(Opening + Jan + Feb + March + April + May) AS MayClosing,
                        sum(Opening + Jan + Feb + March + April + May +  June) AS JuneClosing,
                        sum(Opening + Jan + Feb + March + April + May +  June + July) AS JulyClosing,
                        sum(Opening + Jan + Feb + March + April + May +  June + July + Aug) AS AugClosing,
                        sum(Opening + Jan + Feb + March + April + May +  June + July + Aug + Sept) AS SeptClosing,
                        sum(Opening + Jan + Feb + March + April + May +  June + July + Aug + Sept + Oct) AS OctClosing,
                        sum(Opening + Jan + Feb + March + April + May +  June + July + Aug + Sept + Oct + Nov) AS NovClosing,
                        sum(Opening + Jan + Feb + March + April + May +  June + July + Aug + Sept + Oct + Nov + Dece) AS DeceClosing';*/


        $query = 'SELECT
                        companySystemID,
                        companyID,
                        CompanyName,
                        chartOfAccountSystemID,
                        glCode,
                        AccountDescription,
                        glAccountType,
                        documentLocalCurrencyID,
                        documentRptCurrencyID,
                        0 AS documentAmountOpening,
                        ' . $monthSum . '
                    FROM
                        (
      
                    SELECT
                        * 
                    FROM
                        (
                    SELECT
                        erp_generalledger.companySystemID,
                        erp_generalledger.companyID,
                        companymaster.CompanyName,
                        "" AS documentDate,
                        0 AS chartOfAccountSystemID,
                        "-" AS glCode,
                        "BS" AS glAccountType,
                        "Retained Earning" AS AccountDescription,
                        erp_generalledger.documentLocalCurrencyID,
                        erp_generalledger.documentRptCurrencyID,
                        MONTH ( erp_generalledger.documentDate ) AS DocMONTH,
                        ' . $monthZero . '
                    FROM
                        erp_generalledger
                        LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                        INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                    WHERE
                        erp_generalledger.glAccountType = "BS" 
                        AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                         AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                        AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                    GROUP BY
                        glCode
                        ) AS ERP_qry_TBBS_BF_sum 
                    UNION ALL
                    SELECT
                        * 
                    FROM
                        (
                        SELECT
                            erp_generalledger.companySystemID,
                            erp_generalledger.companyID,
                            companymaster.CompanyName,
                            erp_generalledger.documentDate AS documentDate,
                            erp_generalledger.chartOfAccountSystemID,
                            erp_generalledger.glCode AS glCode,
                            erp_generalledger.glAccountType AS glAccountType,
                            chartofaccounts.AccountDescription AS AccountDescription,
                            erp_generalledger.documentLocalCurrencyID,
                            erp_generalledger.documentRptCurrencyID,
                            MONTH ( erp_generalledger.documentDate ) AS DocMONTH,
                             ' . $month . '
                        FROM
                            erp_generalledger
                            LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                            INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                        WHERE
                            chartofaccounts.catogaryBLorPL = "BS" 
                            AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                             AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                            AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        ) AS ERP_qry_TBBS 
                    UNION ALL
                    SELECT
                        * 
                    FROM
                        (
                        SELECT
                            erp_generalledger.companySystemID,
                            erp_generalledger.companyID,
                            companymaster.CompanyName,
                            erp_generalledger.documentDate AS documentDate,
                            erp_generalledger.chartOfAccountSystemID,
                            erp_generalledger.glCode AS glCode,
                            erp_generalledger.glAccountType AS glAccountType,
                            chartofaccounts.AccountDescription AS AccountDescription,
                            erp_generalledger.documentLocalCurrencyID,
                            erp_generalledger.documentRptCurrencyID,
                            MONTH ( erp_generalledger.documentDate ) AS DocMONTH,
                             ' . $month . '
                        FROM
                            erp_generalledger
                            LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                            INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                        WHERE
                            chartofaccounts.catogaryBLorPL = "PL" 
                            AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                             AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                            AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        ) AS ERP_qry_TBPL 
                        ) AS FINAL 
                    GROUP BY
                        ' . $isCompanyWise . 'chartOfAccountSystemID
                        order by glAccountType,glCode';

        $query1 = 'SELECT
                        companySystemID,
                        companyID,
                        CompanyName,
                        chartOfAccountSystemID,
                        glCode,
                        AccountDescription,
                        glAccountType,
                        documentLocalCurrencyID,
                        documentRptCurrencyID,
                        SUM(documentAmount) AS documentAmountOpening,
                        ' . $monthZero . '
                    FROM
                        (
                    SELECT
                        * 
                    FROM
                        (
                    SELECT
                        erp_generalledger.companySystemID,
                        erp_generalledger.companyID,
                        companymaster.CompanyName,
                        "" AS documentDate,
                        0 AS chartOfAccountSystemID,
                        "-" AS glCode,
                        "BS" AS glAccountType,
                        "Retained Earning" AS AccountDescription,
                        erp_generalledger.documentLocalCurrencyID,
                        erp_generalledger.documentRptCurrencyID,
                        sum( ' . $currencyClm . ' * -1) AS documentAmount
                    FROM
                        erp_generalledger
                        LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                        INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                    WHERE
                        erp_generalledger.glAccountType = "BS" 
                        AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                         AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                        AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '"
                    GROUP BY
                        glCode
                        ) AS ERP_qry_TBBS_BF_sum 
                    UNION ALL
                    SELECT
                        * 
                    FROM
                        (
                        SELECT
                            erp_generalledger.companySystemID,
                            erp_generalledger.companyID,
                            companymaster.CompanyName,
                            erp_generalledger.documentDate AS documentDate,
                            erp_generalledger.chartOfAccountSystemID,
                            erp_generalledger.glCode AS glCode,
                            erp_generalledger.glAccountType AS glAccountType,
                            chartofaccounts.AccountDescription AS AccountDescription,
                            erp_generalledger.documentLocalCurrencyID,
                            erp_generalledger.documentRptCurrencyID,
                            ' . $currencyClm . ' AS documentAmount
                        FROM
                            erp_generalledger
                            LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                            INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                        WHERE
                            chartofaccounts.catogaryBLorPL = "BS" 
                            AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                             AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                            AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '"
                        ) AS ERP_qry_TBBS 
                    UNION ALL
                    SELECT
                        * 
                    FROM
                        (
                        SELECT
                            erp_generalledger.companySystemID,
                            erp_generalledger.companyID,
                            companymaster.CompanyName,
                            erp_generalledger.documentDate AS documentDate,
                            erp_generalledger.chartOfAccountSystemID,
                            erp_generalledger.glCode AS glCode,
                            erp_generalledger.glAccountType AS glAccountType,
                            chartofaccounts.AccountDescription AS AccountDescription,
                            erp_generalledger.documentLocalCurrencyID,
                            erp_generalledger.documentRptCurrencyID,
                            0 AS documentAmount
                        FROM
                            erp_generalledger
                            LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                            INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                        WHERE
                            chartofaccounts.catogaryBLorPL = "PL" 
                            AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                             AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                            AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '"
                        ) AS ERP_qry_TBPL 
                        ) AS FINAL 
                    GROUP BY
                        ' . $isCompanyWise . 'chartOfAccountSystemID
                        order by glAccountType,glCode';


        $finalQry = 'SELECT *,
                        ' . $monthClosing . '
                        FROM (SELECT  companySystemID,
                        companyID,
                        CompanyName,
                        chartOfAccountSystemID,
                        glCode,
                        AccountDescription,
                        glAccountType,
                        documentLocalCurrencyID,
                        documentRptCurrencyID,
                        sum(documentAmountOpening) As Opening,
                        ' . $monthArray . ' FROM (SELECT * FROM (' . $query . ') AS a UNION ALL SELECT * FROM (' . $query1 . ') AS b) AS c GROUP BY
                        ' . $isCompanyWise . 'chartOfAccountSystemID
                        order by glAccountType,glCode) f GROUP BY
                        ' . $isCompanyWise . 'chartOfAccountSystemID
                        order by glAccountType,glCode';

        //return $finalQry;
        $output = \DB::select($finalQry);
        //dd(DB::getQueryLog());
        return array('data' => $output, 'headers' => $availableMonth);
    }

    public function getTrialBalanceDetails($request)
    {
        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');

        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        $chartOfAccountID = $request->chartOfAccountSystemID;
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $chartOfAccount = ChartOfAccount::find($chartOfAccountID);
        $dateQry = '';
        if ($chartOfAccount) {
            if ($chartOfAccount->catogaryBLorPLID == 2) {
                $dateQry = 'DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"';
            } else {
                $dateQry = 'DATE(erp_generalledger.documentDate) <= "' . $toDate . '" ';
            }
        }

        $query = 'SELECT erp_generalledger.companyID,
                                    companymaster.CompanyName,
                                    erp_generalledger.documentLocalCurrencyID,
                                    erp_generalledger.documentRptCurrencyID,
                                    erp_generalledger.documentID,
                                    erp_generalledger.documentSystemID,
                                    erp_generalledger.documentSystemCode,
                                    erp_generalledger.documentCode,
                                    erp_generalledger.documentDate,
                                    erp_generalledger.glCode,
                                    erp_generalledger.documentNarration,
                                    If ( documentLocalAmount> 0,documentLocalAmount, 0 ) as localDebit,
                                    If ( documentLocalAmount> 0,0, documentLocalAmount*-1 ) as localCredit,
                                    If ( documentRptAmount> 0,documentRptAmount, 0 ) as rptDebit,
                                    If ( documentRptAmount> 0,0, documentRptAmount*-1 ) as rptCredit
                                FROM
                                    erp_generalledger 
                                INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                                WHERE
                                    ' . $dateQry . '	
                                    AND erp_generalledger.chartOfAccountSystemID  = ' . $chartOfAccountID . '
                                    AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')  ORDER BY erp_generalledger.documentDate;';

        $output = \DB::select($query);
        //dd(DB::getQueryLog());
        return $output;
    }

    public
    function getTrialBalanceCompanyWise($request, $subCompanies)
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

        $sumQry = "";
        $groupIfQry = "";

        foreach ($subCompanies as $company) {
            $sumQry = $sumQry . ' SUM(`' . $company['CompanyID'] . '`) AS `' . $company['CompanyID'] . '`,';
            $groupIfQry = $groupIfQry . ' IF ( companySystemID = ' . $company['companySystemID'] . ', documentRptAmount, 0 ) AS `' . $company['CompanyID'] . '`,';
        }

        $query = 'SELECT chartOfAccountSystemID,
                            glCode,
                            AccountDescription,
                            glAccountType,
                            ' . $sumQry . '
                            documentRptCurrencyID,
                            companySystemID FROM(SELECT chartOfAccountSystemID,
                            glCode,
                            AccountDescription,
                            glAccountType,
                            ' . $sumQry . '
                            documentRptCurrencyID,
                            companySystemID
                            FROM
                        (SELECT
                            chartOfAccountSystemID,
                            glCode,
                            AccountDescription,
                            glAccountType,
                            ' . $groupIfQry . '
                            documentRptCurrencyID,
                            companySystemID
                        FROM
                            (
                        SELECT
                            * 
                        FROM
                            (
                        SELECT
                            erp_generalledger.companySystemID,
                            erp_generalledger.companyID,
                            companymaster.CompanyName,
                            "" AS documentDate,
                            0 AS chartOfAccountSystemID,
                            "-" AS glCode,
                            "BS" AS glAccountType,
                            "Retained Earning" AS AccountDescription,
                            erp_generalledger.documentLocalCurrencyID,
                            erp_generalledger.documentLocalCurrencyER,
                            sum( erp_generalledger.documentLocalAmount *- 1 ) AS documentLocalAmount,
                            erp_generalledger.documentRptCurrencyID,
                            erp_generalledger.documentRptCurrencyER,
                            sum( erp_generalledger.documentRptAmount * - 1 ) documentRptAmount 
                        FROM
                            erp_generalledger
                            LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
                            INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                        WHERE
                            erp_generalledger.glAccountType = "BS" 
                            AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                            AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '"  
                            
                        GROUP BY
                            companySystemID,
                            glCode,
                            glAccountType 
                            ) AS ERP_qry_TBBS_BF_sum
                        UNION ALL
                        SELECT
                            * 
                        FROM
                            (
                            SELECT
                                erp_generalledger.companySystemID,
                                erp_generalledger.companyID,
                                companymaster.CompanyName,
                                erp_generalledger.documentDate AS documentDate,
                                erp_generalledger.chartOfAccountSystemID,
                                erp_generalledger.glCode AS glCode,
                                erp_generalledger.glAccountType AS glAccountType,
                                chartofaccounts.AccountDescription AS AccountDescription,
                                erp_generalledger.documentLocalCurrencyID,
                                erp_generalledger.documentLocalCurrencyER,
                                erp_generalledger.documentLocalAmount AS documentLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                erp_generalledger.documentRptCurrencyER,
                                erp_generalledger.documentRptAmount documentRptAmount 
                            FROM
                                erp_generalledger
                                LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
                                INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                            WHERE
                                erp_generalledger.glAccountType = "BS" 
                                AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                                AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '" 
                            ) AS ERP_qry_TBBS_BF 
                        UNION ALL
                        SELECT
                            * 
                        FROM
                            (
                            SELECT
                                erp_generalledger.companySystemID,
                                erp_generalledger.companyID,
                                companymaster.CompanyName,
                                erp_generalledger.documentDate AS documentDate,
                                erp_generalledger.chartOfAccountSystemID,
                                erp_generalledger.glCode AS glCode,
                                erp_generalledger.glAccountType AS glAccountType,
                                chartofaccounts.AccountDescription AS AccountDescription,
                                erp_generalledger.documentLocalCurrencyID,
                                erp_generalledger.documentLocalCurrencyER,
                                erp_generalledger.documentLocalAmount AS documentLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                erp_generalledger.documentRptCurrencyER,
                                erp_generalledger.documentRptAmount documentRptAmount 
                            FROM
                                erp_generalledger
                                LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
                                INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                            WHERE
                                chartofaccounts.catogaryBLorPL = "BS" 
                                AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                                AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                            ) AS ERP_qry_TBBS 
                        UNION ALL
                        SELECT
                            * 
                        FROM
                            (
                            SELECT
                                erp_generalledger.companySystemID,
                                erp_generalledger.companyID,
                                companymaster.CompanyName,
                                erp_generalledger.documentDate AS documentDate,
                                erp_generalledger.chartOfAccountSystemID,
                                erp_generalledger.glCode AS glCode,
                                erp_generalledger.glAccountType AS glAccountType,
                                chartofaccounts.AccountDescription AS AccountDescription,
                                erp_generalledger.documentLocalCurrencyID,
                                erp_generalledger.documentLocalCurrencyER,
                                erp_generalledger.documentLocalAmount AS documentLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                erp_generalledger.documentRptCurrencyER,
                                erp_generalledger.documentRptAmount documentRptAmount 
                            FROM
                                erp_generalledger
                                LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
                                INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                            WHERE
                                chartofaccounts.catogaryBLorPL = "PL" 
                                AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                                AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                            ) AS ERP_qry_TBPL 
                            ) AS FINAL ) AS FINALFINL GROUP BY chartOfAccountSystemID,companySystemID ORDER BY glCode) As fi GROUP BY chartOfAccountSystemID;';

        $output = \DB::select($query);
        //dd(DB::getQueryLog());
        return $output;
    }


    public function getGeneralLedger($request)
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

        $glCodes = (array)$request->glCodes;
        $type = $request->type;
        $chartOfAccountId = array_filter(collect($glCodes)->pluck('chartOfAccountSystemID')->toArray());

        $departments = (array)$request->departments;
        $serviceLineId = array_filter(collect($departments)->pluck('serviceLineSystemID')->toArray());

        array_push($serviceLineId, 24);

        $contracts = (array)$request->contracts;
        $contractsId = array_filter(collect($contracts)->pluck('contractUID')->toArray());

        array_push($contractsId, 159);
        //contracts

        $query = 'SELECT * 
                    FROM
                        (
                    SELECT
                        * 
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
                        erp_generalledger.supplierCodeSystem,
                        erp_generalledger.documentLocalCurrencyID,
                        chartofaccounts.AccountDescription,
                        companymaster.CompanyName,
                        erp_templatesglcode.templatesDetailsAutoID as templatesDetailsAutoID,
                        approveEmp.empName as approvedBy,
                        confirmEmp.empName as confirmedBy,
                        erp_generalledger.documentConfirmedDate,
                        erp_generalledger.documentFinalApprovedDate,
                        erp_templatesglcode.templateMasterID,
                        erp_templatesdetails.templateDetailDescription,
                    IF
                        ( documentLocalAmount > 0, documentLocalAmount, 0 ) AS localDebit,
                    IF
                        ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 ) AS localCredit,
                        erp_generalledger.documentRptCurrencyID,
                    IF
                        ( documentRptAmount > 0, documentRptAmount, 0 ) AS rptDebit,
                    IF
                        ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) AS rptCredit,
                    IF
                        ( erp_generalledger.documentSystemID = 20 OR erp_generalledger.documentSystemID = 21 OR erp_generalledger.documentSystemID = 19, customermaster.CustomerName, suppliermaster.supplierName ) AS isCustomer 
                    FROM
                        erp_generalledger
                        LEFT JOIN employees as approveEmp ON erp_generalledger.documentFinalApprovedByEmpSystemID = approveEmp.employeeSystemID
                        LEFT JOIN employees as confirmEmp ON erp_generalledger.documentConfirmedByEmpSystemID = confirmEmp.employeeSystemID
                        LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                        LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem 
                        LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID 
                        LEFT JOIN companymaster ON companymaster.companySystemID = erp_generalledger.companySystemID 
                        LEFT JOIN erp_templatesglcode ON erp_templatesglcode.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID AND erp_templatesglcode.templateMasterID IN (
                            SELECT erp_templatesmaster.templatesMasterAutoID FROM erp_templatesmaster
                                  WHERE erp_templatesmaster.isActive = -1 AND  erp_templatesmaster.isBudgetUpload = -1
                        )
                        LEFT JOIN erp_templatesdetails ON erp_templatesdetails.templatesDetailsAutoID = erp_templatesglcode.templatesDetailsAutoID 
                    WHERE
                        erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        AND  erp_generalledger.chartOfAccountSystemID IN (' . join(',', $chartOfAccountId) . ')
                        AND  erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceLineId) . ')
                        AND  erp_generalledger.contractUID IN (' . join(',', $contractsId) . ')
                        ) AS erp_qry_GL UNION ALL
                    SELECT
                        * 
                    FROM
                        (
                    SELECT
                        erp_generalledger.companySystemID,
                        erp_generalledger.companyID,
                        erp_generalledger.serviceLineSystemID,
                        erp_generalledger.serviceLineCode,
                        "" AS documentSystemID,
                        "" AS documentID,
                        "" AS documentSystemCode,
                        "" AS documentCode,
                        "" AS documentDate,
                        erp_generalledger.chartOfAccountSystemID,
                        erp_generalledger.glCode,
                        "BS" AS glAccountType,
                        "Opening Balance" AS documentNarration,
                        "" AS clientContractID,
                        "" AS supplierCodeSystem,
                        erp_generalledger.documentLocalCurrencyID,
                        chartofaccounts.AccountDescription,
                        companymaster.CompanyName,
                        erp_templatesglcode.templatesDetailsAutoID,
                        approveEmp.empName as approvedBy,
                        confirmEmp.empName as confirmedBy,
                        erp_generalledger.documentConfirmedDate,
                        erp_generalledger.documentFinalApprovedDate,
                        erp_templatesglcode.templateMasterID,
                        erp_templatesdetails.templateDetailDescription,
                        sum( IF ( documentLocalAmount > 0, documentLocalAmount, 0 ) ) AS localDebit,
                        sum( IF ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 ) ) AS localCredit,
                        erp_generalledger.documentRptCurrencyID,
                        sum( IF ( documentRptAmount > 0, documentRptAmount, 0 ) ) AS rptDebit,
                        sum( IF ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) ) AS rptCredit,
                        "" AS isCustomer
                    FROM
                        erp_generalledger
                        LEFT JOIN employees as approveEmp ON erp_generalledger.documentFinalApprovedByEmpSystemID = approveEmp.employeeSystemID
                        LEFT JOIN employees as confirmEmp ON erp_generalledger.documentConfirmedByEmpSystemID = confirmEmp.employeeSystemID
                        LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                        LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem 
                        LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID 
                        LEFT JOIN companymaster ON companymaster.companySystemID = erp_generalledger.companySystemID 
                        LEFT JOIN erp_templatesglcode ON erp_templatesglcode.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID AND erp_templatesglcode.templateMasterID IN (
                            SELECT erp_templatesmaster.templatesMasterAutoID FROM erp_templatesmaster
                                  WHERE erp_templatesmaster.isActive = -1 AND  erp_templatesmaster.isBudgetUpload = -1
                        )
                        LEFT JOIN erp_templatesdetails ON erp_templatesdetails.templatesDetailsAutoID = erp_templatesglcode.templatesDetailsAutoID
                        WHERE
                        erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND erp_generalledger.glAccountTypeID = 1
                        AND  erp_generalledger.chartOfAccountSystemID IN (' . join(',', $chartOfAccountId) . ')
                        AND  erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceLineId) . ')
                        AND  erp_generalledger.contractUID IN (' . join(',', $contractsId) . ')
                        AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '"
                    GROUP BY
                        erp_generalledger.companySystemID,
                        erp_generalledger.serviceLineSystemID,
                        erp_generalledger.chartOfAccountSystemID
                        ) AS erp_qry_gl_bf 
                        ) AS GL_final 
                    ORDER BY
                        documentDate ASC';

        return  \DB::select($query);
    }

    public function getGeneralLedgerQryForPDF($request)
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

        $glCodes = (array)$request->glCodes;
        $type = $request->type;
        $chartOfAccountId = array_filter(collect($glCodes)->pluck('chartOfAccountSystemID')->toArray());

        $departments = (array)$request->departments;
        $serviceLineId = array_filter(collect($departments)->pluck('serviceLineSystemID')->toArray());

        array_push($serviceLineId, 24);

        $contracts = (array)$request->contracts;
        $contractsId = array_filter(collect($contracts)->pluck('contractUID')->toArray());

        array_push($contractsId, 159);
        //contracts

        //DB::enableQueryLog();
        $query = 'SELECT *
                    FROM
                        (
                    SELECT
                        *
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
                        erp_generalledger.supplierCodeSystem,
                        erp_generalledger.documentLocalCurrencyID,
                        chartofaccounts.AccountDescription,
                        companymaster.CompanyName,
                    IF
                        ( documentLocalAmount > 0, documentLocalAmount, 0 ) AS localDebit,
                    IF
                        ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 ) AS localCredit,
                        erp_generalledger.documentRptCurrencyID,
                    IF
                        ( documentRptAmount > 0, documentRptAmount, 0 ) AS rptDebit,
                    IF
                        ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) AS rptCredit,
                    IF
                        ( erp_generalledger.documentSystemID = 20 OR erp_generalledger.documentSystemID = 21 OR erp_generalledger.documentSystemID = 19, customermaster.CustomerName, suppliermaster.supplierName ) AS isCustomer,
                        approveEmp.empName as approvedBy,
                        confirmEmp.empName as confirmedBy,
                        erp_generalledger.documentConfirmedDate,
                        erp_generalledger.documentFinalApprovedDate
                    FROM
                        erp_generalledger
                        LEFT JOIN employees as approveEmp ON erp_generalledger.documentFinalApprovedByEmpSystemID = approveEmp.employeeSystemID
                        LEFT JOIN employees as confirmEmp ON erp_generalledger.documentConfirmedByEmpSystemID = confirmEmp.employeeSystemID
                        LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                        LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem
                        LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
                        LEFT JOIN companymaster ON companymaster.companySystemID = erp_generalledger.companySystemID
                    WHERE
                        erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        AND  erp_generalledger.chartOfAccountSystemID IN (' . join(',', $chartOfAccountId) . ')
                        AND  erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceLineId) . ')
                        AND  erp_generalledger.contractUID IN (' . join(',', $contractsId) . ')
                        ) AS erp_qry_GL UNION ALL
                    SELECT
                        *
                    FROM
                        (
                    SELECT
                        erp_generalledger.companySystemID,
                        erp_generalledger.companyID,
                        erp_generalledger.serviceLineSystemID,
                        erp_generalledger.serviceLineCode,
                        "" AS documentSystemID,
                        "" AS documentID,
                        "" AS documentSystemCode,
                        "" AS documentCode,
                        "" AS documentDate,
                        erp_generalledger.chartOfAccountSystemID,
                        erp_generalledger.glCode,
                        "BS" AS glAccountType,
                        "Opening Balance" AS documentNarration,
                        "" AS clientContractID,
                        "" AS supplierCodeSystem,
                        erp_generalledger.documentLocalCurrencyID,
                        chartofaccounts.AccountDescription,
                        companymaster.CompanyName,
                        sum( IF ( documentLocalAmount > 0, documentLocalAmount, 0 ) ) AS localDebit,
                        sum( IF ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 ) ) AS localCredit,
                        erp_generalledger.documentRptCurrencyID,
                        sum( IF ( documentRptAmount > 0, documentRptAmount, 0 ) ) AS rptDebit,
                        sum( IF ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) ) AS rptCredit,
                        "" AS isCustomer,
                        approveEmp.empName as approvedBy,
                        confirmEmp.empName as confirmedBy,
                        erp_generalledger.documentConfirmedDate,
                        erp_generalledger.documentFinalApprovedDate
                    FROM
                        erp_generalledger
                        LEFT JOIN employees as approveEmp ON erp_generalledger.documentFinalApprovedByEmpSystemID = approveEmp.employeeSystemID
                        LEFT JOIN employees as confirmEmp ON erp_generalledger.documentConfirmedByEmpSystemID = confirmEmp.employeeSystemID
                        LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                        LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem
                        LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
                        LEFT JOIN companymaster ON companymaster.companySystemID = erp_generalledger.companySystemID
                    WHERE
                        erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND erp_generalledger.glAccountType = "BS"
                        AND  erp_generalledger.chartOfAccountSystemID IN (' . join(',', $chartOfAccountId) . ')
                        AND  erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceLineId) . ')
                        AND  erp_generalledger.contractUID IN (' . join(',', $contractsId) . ')
                        AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '"
                    GROUP BY
                        erp_generalledger.glCode,
                        erp_generalledger.companySystemID,
                        erp_generalledger.serviceLineSystemID,
                        erp_generalledger.chartOfAccountSystemID
                        ) AS erp_qry_gl_bf
                        ) AS GL_final
                    ORDER BY
                        documentDate,glCode ASC';
        $output = \DB::select($query);
        //dd(DB::getQueryLog());
        return $output;
    }

    public
    function getTaxDetailQry($request)
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
        if ($request->tempType == 1) {

            $query = 'SELECT
	MASTER .companyID,
	MASTER .bookingInvCode,
	DATE_FORMAT(
		MASTER .bookingDate,
		"%d/%m/%Y"
	) AS bookingDate,
	MASTER .supplierInvoiceNo,
	DATE_FORMAT(
		MASTER .supplierInvoiceDate,
		"%d/%m/%Y"
	) AS supplierInvoiceDate,
	MASTER .comments,
	suppliermaster.primarySupplierCode,
	suppliermaster.supplierName,
	currencymaster.CurrencyCode,
	currencymaster.DecimalPlaces,
	MASTER .bookingAmountTrans,
	IFNULL(tax.taxTotalAmount, 0) AS taxTotalAmount,
	DATE_FORMAT(
		MASTER .postedDate,
		"%d/%m/%Y"
	) AS postedDate
FROM
	erp_bookinvsuppmaster AS MASTER
INNER JOIN suppliermaster ON suppliermaster.supplierCodeSystem = MASTER .supplierID
INNER JOIN currencymaster ON currencymaster.currencyID = MASTER .supplierTransactionCurrencyID
LEFT JOIN (
	SELECT
		taxdetail.documentSystemID,
		taxdetail.companySystemID,
		taxdetail.documentSystemCode,
		IFNULL(Sum(taxdetail.amount), 0) AS taxTotalAmount
	FROM
		erp_taxdetail AS taxdetail
	GROUP BY
		taxdetail.documentSystemID,
		taxdetail.companySystemID,
		taxdetail.documentSystemCode
) tax ON tax.documentSystemID = MASTER .documentSystemID
AND tax.companySystemID = MASTER .companySystemID
AND tax.documentSystemCode = MASTER .bookingSuppMasInvAutoID
WHERE DATE(master.postedDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND
	MASTER.companySystemID IN (' . join(',', $companyID) . ')
AND MASTER.approved = - 1
AND MASTER.cancelYN = 0';
        } else {
            $query = 'SELECT
	MASTER .companyID,
	MASTER .bookingInvCode,
	DATE_FORMAT(
		MASTER .bookingDate,
		"%d/%m/%Y"
	) AS bookingDate,
	MASTER .comments,
	customermaster.CutomerCode,
	customermaster.customerShortCode,
	customermaster.CustomerName,
	currencymaster.CurrencyCode,
	currencymaster.DecimalPlaces,
MASTER.bookingAmountTrans,
	IFNULL(tax.taxTotalAmount, 0) AS taxTotalAmount,
	DATE_FORMAT(
		MASTER .postedDate,
		"%d/%m/%Y"
	) AS postedDate
FROM
	erp_custinvoicedirect AS MASTER
INNER JOIN customermaster ON customermaster.customerCodeSystem = MASTER.customerID
INNER JOIN currencymaster ON currencymaster.currencyID = MASTER.custTransactionCurrencyID
LEFT JOIN (
	SELECT
		taxdetail.documentSystemID,
		taxdetail.companySystemID,
		taxdetail.documentSystemCode,
		IFNULL(Sum(taxdetail.amount), 0) AS taxTotalAmount
	FROM
		erp_taxdetail AS taxdetail
	GROUP BY
		taxdetail.documentSystemID,
		taxdetail.companySystemID,
		taxdetail.documentSystemCode
) tax ON tax.documentSystemID = MASTER .documentSystemID
AND tax.companySystemID = MASTER .companySystemID
AND tax.documentSystemCode = MASTER .custInvoiceDirectAutoID
WHERE
	DATE(master.postedDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
AND MASTER .companySystemID IN (' . join(',', $companyID) . ')
AND MASTER .approved = - 1
AND MASTER .canceledYN = 0';
        }

        $output = \DB::select($query);
        //dd(DB::getQueryLog());
        return $output;
    }

    public
    function pdfExportReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'FGL':
                $reportTypeID = $request->reportTypeID;
                $reportSD = $request->reportSD;
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                $checkIsGroup = Company::find($request->companySystemID);
                $data = array();
                $output = $this->getGeneralLedgerQryForPDF($request);
                $decimalPlace = array();
                $currencyIdLocal = 1;
                $currencyIdRpt = 2;

                $decimalPlaceCollectLocal = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                $decimalPlaceUniqueLocal = array_unique($decimalPlaceCollectLocal);

                $decimalPlaceCollectRpt = collect($output)->pluck('documentRptCurrencyID')->toArray();
                $decimalPlaceUniqueRpt = array_unique($decimalPlaceCollectRpt);


                if (!empty($decimalPlaceUniqueLocal)) {
                    $currencyIdLocal = $decimalPlaceUniqueLocal[0];
                }

                if (!empty($decimalPlaceUniqueRpt)) {
                    $currencyIdRpt = $decimalPlaceUniqueRpt[0];
                }

                $extraColumns = [];
                if (isset($request->extraColoumns) && count($request->extraColoumns) > 0) {
                    $extraColumns = collect($request->extraColoumns)->pluck('id')->toArray();
                }

                $requestCurrencyLocal = CurrencyMaster::where('currencyID', $currencyIdLocal)->first();
                $requestCurrencyRpt = CurrencyMaster::where('currencyID', $currencyIdRpt)->first();

                $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

                $currencyLocal = $requestCurrencyLocal->CurrencyCode;
                $currencyRpt = $requestCurrencyRpt->CurrencyCode;

                $totaldocumentLocalAmountDebit = array_sum(collect($output)->pluck('localDebit')->toArray());
                $totaldocumentLocalAmountCredit = array_sum(collect($output)->pluck('localCredit')->toArray());
                $totaldocumentRptAmountDebit = array_sum(collect($output)->pluck('rptDebit')->toArray());
                $totaldocumentRptAmountCredit = array_sum(collect($output)->pluck('rptCredit')->toArray());

                $finalData = array();
                foreach ($output as $val) {
                    $finalData[$val->glCode . ' - ' . $val->AccountDescription][] = $val;
                }

                $dataArr = array(
                    'reportData' => $finalData,
                    'extraColumns' => $extraColumns,
                    'companyName' => $checkIsGroup->CompanyName,
                    'isGroup' => $checkIsGroup->isGroup,
                    'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2,
                    'currencyLocal' => $currencyLocal,
                    'decimalPlaceLocal' => $decimalPlaceLocal,
                    'decimalPlaceRpt' => $decimalPlaceRpt,
                    'currencyRpt' => $currencyRpt,
                    'reportDate' => date('d/m/Y H:i:s A'),
                    'fromDate' => \Helper::dateFormat($request->fromDate),
                    'toDate' => \Helper::dateFormat($request->toDate),
                    'totaldocumentLocalAmountDebit' => $totaldocumentLocalAmountDebit,
                    'totaldocumentLocalAmountCredit' => $totaldocumentLocalAmountCredit,
                    'totaldocumentRptAmountDebit' => $totaldocumentRptAmountDebit,
                    'totaldocumentRptAmountCredit' => $totaldocumentRptAmountCredit,
                );

                $html = view('print.report_general_ledger', $dataArr);

                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($html);

                return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();

                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    function getCustomizeFinancialRptQry($request, $linkedcolumnQry, $linkedcolumnQry2, $columnKeys, $financeYear, $period, $budgetQuery, $budgetWhereQuery, $columnTemplateID)
    {
        if ($request->dateType == 1) {
            $toDate = new Carbon($request->toDate);
            $toDate = $toDate->format('Y-m-d');
            $fromDate = new Carbon($request->fromDate);
            $fromDate = $fromDate->format('Y-m-d');
        } else {
            $period = CompanyFinancePeriod::find($request->month);
            $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
            $fromDate = Carbon::parse($period->dateFrom)->format('Y-m-d');
        }

        $companyID = collect($request->companySystemID)->pluck('companySystemID')->toArray();
        $serviceline = collect($request->serviceLineSystemID)->pluck('serviceLineSystemID')->toArray();

        $documents = ReportTemplateDocument::pluck('documentSystemID')->toArray();

        $isExpand = 0;
        $divisionValue = 1;
        $templateMaster = ReportTemplate::find($request->templateType);
        if ($templateMaster) {
            if ($templateMaster->showNumbersIn !== 1) {
                $numbers = ReportTemplateNumbers::find($templateMaster->showNumbersIn);
                $divisionValue = (float)$numbers->value;
            }

            if ($templateMaster->presentationType == 2) {
                $isExpand = 1;
            } else {
                $isExpand = 0;
            }
        }

        $lastYearStartDate = Carbon::parse($financeYear->bigginingDate);
        $lastYearStartDate = $lastYearStartDate->subYear()->format('Y-m-d');
        $lastYearEndDate = Carbon::parse($financeYear->endingDate);
        $lastYearEndDate = $lastYearEndDate->subYear()->format('Y-m-d');

        $dateFilter = '';
        $documentQry = '';
        $servicelineQry = '';
        $servicelineQryForBudget = '';
        if ($request->dateType == 1) {
            // $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
        } else {
            if ($request->accountType == 2) {
                // $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
            } else {
                $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
                // $dateFilter = 'AND (DATE(erp_generalledger.documentDate) <= "' . $toDate . '")';
            }
        }

        if ($request->accountType == 3) {
            if (count($documents) > 0) {
                $documentQry = 'AND erp_generalledger.documentSystemID IN (' . join(',', $documents) . ')';
            }
        }

        if ($request->accountType == 2) {
            if (count($serviceline) > 0) {
                $servicelineQry = 'AND erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
                $servicelineQryForBudget = 'AND erp_budjetdetails.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
            }
        }

        $firstLinkedcolumnQry = !empty($linkedcolumnQry) ? $linkedcolumnQry . ',' : '';
        $secondLinkedcolumnQry = '';
        //$thirdLinkedcolumnQry = '';
        $fourthLinkedcolumnQry = !empty($linkedcolumnQry2) ? $linkedcolumnQry2 . ',' : '';
        $fifthLinkedcolumnQry = '';
        $whereQry = [];
        foreach ($columnKeys as $key => $val) {
            $coloumnShortCode = explode('-', $val)[0];
            if ($coloumnShortCode == "BCM") {
                $fifthLinkedcolumnQry .= 'IFNULL( bAmountMonth,  0 ) AS `' . $val . '`,';
            } else if ($coloumnShortCode == "BYTD") {
                $fifthLinkedcolumnQry .= 'IFNULL( bAmountYear,  0 ) AS `' . $val . '`,';
            } else {
                $fifthLinkedcolumnQry .= 'IFNULL(IF(linkCatType != templateCatType,`' . $val . '` * -1,`' . $val . '`),0) AS `' . $val . '`,';
            }

            $secondLinkedcolumnQry .= '((IFNULL(IFNULL( c.`' . $val . '`, e.`' . $val . '`),0))/' . $divisionValue . ') AS `' . $val . '`,';
            //$thirdLinkedcolumnQry .= 'IFNULL(SUM(d.`' . $val . '`),0) AS `' . $val . '`,';
            //$fourthLinkedcolumnQry .= 'IFNULL(SUM(`' . $val . '`),0) AS `' . $val . '`,';
            $whereQry[] .= 'IF(masterID is not null AND isFinalLevel = 1 , d.`' . $val . '` != 0,d.`' . $val . '` IS NOT NULL)';
        }

        $budgetJoin = '';
        $generalLedgerGroup = '';
        $templateGroup = '';
        if ($columnTemplateID == 1) {
            $secondLinkedcolumnQry .= ' ((IFNULL(IFNULL(c.compID, e.compID),0))) AS CompanyID,';
            $fourthLinkedcolumnQry .= ' compID,';
            $fifthLinkedcolumnQry .= ' compID,';
            $firstLinkedcolumnQry .= ' erp_generalledger.companySystemID AS compID,';
            $budgetJoin = ' AND g.compID = budget.companySystemID';
            $generalLedgerGroup = ' ,erp_generalledger.companySystemID';
            $templateGroup = ', compID';
        }

        $sql = 'SELECT * FROM (SELECT
	c.detDescription,
	c.detID,
	' . $secondLinkedcolumnQry . '
	c.sortOrder,
	c.masterID,
    c.isFinalLevel,
	c.bgColor,
	c.fontColor,
	c.itemType,
	c.hideHeader,
	' . $isExpand . ' as expanded  
FROM
	(
SELECT
	b.*,
	erp_companyreporttemplatedetails.detID,
	erp_companyreporttemplatedetails.description AS detDescription,
	erp_companyreporttemplatedetails.sortOrder,
	erp_companyreporttemplatedetails.masterID,
    erp_companyreporttemplatedetails.isFinalLevel,
	erp_companyreporttemplatedetails.bgColor,
	erp_companyreporttemplatedetails.fontColor,
	erp_companyreporttemplatedetails.hideHeader,
	erp_companyreporttemplatedetails.itemType 
FROM
	erp_companyreporttemplatedetails
	LEFT JOIN (
SELECT
	' . $fourthLinkedcolumnQry . ' 
	templateDetailID,
	description
FROM
	(
		SELECT
			' . $fifthLinkedcolumnQry . ' 
			templateDetailID,
			description
			FROM
			(
				(
					SELECT
						' . $firstLinkedcolumnQry . ' 
						erp_generalledger.chartOfAccountSystemID
					FROM
						erp_generalledger
					INNER JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
					WHERE
						erp_generalledger.companySystemID IN (
							' . join(',
							', $companyID) . '
						) ' . $servicelineQry . ' ' . $dateFilter . ' ' . $documentQry . '
					GROUP BY
						erp_generalledger.chartOfAccountSystemID ' . $generalLedgerGroup . '
				) g
				INNER JOIN (
					SELECT
						erp_companyreporttemplatelinks.glAutoID,
						erp_companyreporttemplatelinks.templateDetailID,
						erp_companyreporttemplatelinks.categoryType AS linkCatType,
						erp_companyreporttemplatedetails.description,
						erp_companyreporttemplatedetails.categoryType AS templateCatType
					FROM
						erp_companyreporttemplatelinks
					INNER JOIN erp_companyreporttemplatedetails ON erp_companyreporttemplatelinks.templateDetailID = erp_companyreporttemplatedetails.detID
					WHERE
						erp_companyreporttemplatelinks.templateMasterID = ' . $request->templateType . '
					ORDER BY
						erp_companyreporttemplatedetails.sortOrder
				) AS a ON a.glAutoID = g.chartOfAccountSystemID
			)
            LEFT JOIN(
                    SELECT
                        ' . $budgetQuery . ' 
                    FROM
                        erp_budjetdetails
                    WHERE
                        erp_budjetdetails.companySystemID IN(' . join(',
                    ', $companyID) . '
                ) ' . $servicelineQryForBudget . ' ' . $budgetWhereQuery . '
                ) AS budget
            ON
                budget.chartOfAccountID = a.glAutoID ' . $budgetJoin . '
	) f
GROUP BY
	templateDetailID ' . $templateGroup . '
	) AS b ON b.templateDetailID = erp_companyreporttemplatedetails.detID 
WHERE
	erp_companyreporttemplatedetails.companyReportTemplateID = ' . $request->templateType . ' 
	) c
	LEFT JOIN (
SELECT
	' . $fourthLinkedcolumnQry . '
	erp_companyreporttemplatelinks.templateDetailID 
FROM
	erp_companyreporttemplatelinks
	LEFT JOIN (
            SELECT
	' . $fourthLinkedcolumnQry . ' 
	templateDetailID,
	description
FROM
	(
		SELECT
			' . $fifthLinkedcolumnQry . ' 
			templateDetailID,
			description
			FROM
			(
				(
					SELECT
						' . $firstLinkedcolumnQry . ' 
						erp_generalledger.chartOfAccountSystemID
					FROM
						erp_generalledger
                    
					INNER JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
					WHERE
						erp_generalledger.companySystemID IN (
							' . join(',
							', $companyID) . '
						) ' . $servicelineQry . ' ' . $dateFilter . ' ' . $documentQry . '
					GROUP BY
						erp_generalledger.chartOfAccountSystemID ' . $generalLedgerGroup . '
				) g
				INNER JOIN (
					SELECT
						erp_companyreporttemplatelinks.glAutoID,
						erp_companyreporttemplatelinks.templateDetailID,
						erp_companyreporttemplatelinks.categoryType AS linkCatType,
						erp_companyreporttemplatedetails.description,
						erp_companyreporttemplatedetails.categoryType AS templateCatType
					FROM
						erp_companyreporttemplatelinks
					INNER JOIN erp_companyreporttemplatedetails ON erp_companyreporttemplatelinks.templateDetailID = erp_companyreporttemplatedetails.detID
					WHERE
						erp_companyreporttemplatelinks.templateMasterID = ' . $request->templateType . '
					ORDER BY
						erp_companyreporttemplatedetails.sortOrder
				) AS a ON a.glAutoID = g.chartOfAccountSystemID
			)
            LEFT JOIN(
                    SELECT
                        ' . $budgetQuery . ' 
                    FROM
                        erp_budjetdetails
                    WHERE
                        erp_budjetdetails.companySystemID IN(' . join(',
                    ', $companyID) . '
                ) ' . $servicelineQryForBudget . ' ' . $budgetWhereQuery . '
                ) AS budget
            ON
                budget.chartOfAccountID = a.glAutoID ' . $budgetJoin . '
	) g
GROUP BY
	templateDetailID ' . $templateGroup . '
	) d ON d.templateDetailID = erp_companyreporttemplatelinks.subCategory 
WHERE
	erp_companyreporttemplatelinks.templateMasterID = ' . $request->templateType . ' 
	AND subCategory IS NOT NULL 
GROUP BY
	erp_companyreporttemplatelinks.templateDetailID ' . $templateGroup . '
	) e ON e.templateDetailID = c.detID) d WHERE (' . join(' OR ', $whereQry) . ')';

        $output = \DB::select($sql);
        return $output;
    }

    function getCustomizeFinancialDetailRptQry($request, $linkedcolumnQry, $columnKeys, $financeYear, $period, $budgetQuery, $budgetWhereQuery, $columnTemplateID)
    {
        if ($request->dateType == 1) {
            $toDate = new Carbon($request->toDate);
            $toDate = $toDate->format('Y-m-d');
            $fromDate = new Carbon($request->fromDate);
            $fromDate = $fromDate->format('Y-m-d');
        } else {
            $period = CompanyFinancePeriod::find($request->month);
            $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
            $fromDate = Carbon::parse($period->dateFrom)->format('Y-m-d');
        }

        $companyID = collect($request->companySystemID)->pluck('companySystemID')->toArray();
        $serviceline = collect($request->serviceLineSystemID)->pluck('serviceLineSystemID')->toArray();
        $documents = ReportTemplateDocument::pluck('documentSystemID')->toArray();

        $lastYearStartDate = Carbon::parse($financeYear->bigginingDate);
        $lastYearStartDate = $lastYearStartDate->subYear()->format('Y-m-d');
        $lastYearEndDate = Carbon::parse($financeYear->endingDate);
        $lastYearEndDate = $lastYearEndDate->subYear()->format('Y-m-d');

        $dateFilter = '';
        $documentQry = '';
        $servicelineQry = '';
        $servicelineQryForBudget = '';
        if ($request->dateType == 1) {
            //$dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '") OR (DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $lastYearEndDate . '"))';
            // $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
        } else {
            if ($request->accountType == 2) {
                //$dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '") OR (DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $lastYearEndDate . '"))';
                // $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
            } else {
                $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
                // $dateFilter = 'AND (DATE(erp_generalledger.documentDate) <= "' . $toDate . '")';
            }
        }

        if ($request->accountType == 3) {
            if (count($documents) > 0) {
                $documentQry = 'AND erp_generalledger.documentSystemID IN (' . join(',', $documents) . ')';
            }
        }

        if ($request->accountType == 2) {
            if (count($serviceline) > 0) {
                $servicelineQry = 'AND erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
                $servicelineQryForBudget = 'AND erp_budjetdetails.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
            }
        }

        $divisionValue = 1;
        $templateMaster = ReportTemplate::find($request->templateType);
        if ($templateMaster) {
            if ($templateMaster->showNumbersIn !== 1) {
                $numbers = ReportTemplateNumbers::find($templateMaster->showNumbersIn);
                $divisionValue = (float)$numbers->value;
            }
        }

        $firstLinkedcolumnQry = !empty($linkedcolumnQry) ? $linkedcolumnQry . ',' : '';
        $secondLinkedcolumnQry = '';
        $whereQry = [];
        foreach ($columnKeys as $val) {
            $coloumnShortCode = explode('-', $val)[0];
            if ($coloumnShortCode == "BCM") {
                $secondLinkedcolumnQry .= 'IFNULL( bAmountMonth,  0 ) AS `' . $val . '`,';
            } else if ($coloumnShortCode == "BYTD") {
                $secondLinkedcolumnQry .= 'IFNULL( bAmountYear,  0 ) AS `' . $val . '`,';
            } else {
                $secondLinkedcolumnQry .= '((IFNULL(IF(erp_companyreporttemplatelinks.categoryType != erp_companyreporttemplatedetails.categoryType,gl.`' . $val . '`*-1,gl.`' . $val . '`),0))/' . $divisionValue . ') AS `' . $val . '`,';
            }
            $whereQry[] .= 'a.`' . $val . '` != 0';
        }

        $budgetJoin = '';
        $generalLedgerGroup = '';
        if ($columnTemplateID == 1) {
            $secondLinkedcolumnQry .= ' gl.compID,';
            $firstLinkedcolumnQry .= ' erp_generalledger.companySystemID AS compID,';
            $budgetJoin = ' AND gl.compID = budget.companySystemID';
            $generalLedgerGroup = ' ,erp_generalledger.companySystemID';
        }

        $sql = 'SELECT * FROM (SELECT
	' . $secondLinkedcolumnQry . '
	erp_companyreporttemplatelinks.glCode,
	erp_companyreporttemplatelinks.glDescription,
	erp_companyreporttemplatelinks.glAutoID,
	erp_companyreporttemplatelinks.templateDetailID,
	erp_companyreporttemplatelinks.categoryType AS linkCatType,
	erp_companyreporttemplatedetails.categoryType AS templateCatType
FROM
	erp_companyreporttemplatelinks
	INNER JOIN erp_companyreporttemplatedetails ON erp_companyreporttemplatelinks.templateDetailID = erp_companyreporttemplatedetails.detID
	LEFT JOIN (
        SELECT
        ' . $firstLinkedcolumnQry . '
        erp_generalledger.chartOfAccountSystemID
    FROM
        erp_generalledger
        INNER JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
        WHERE
        erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') 
        ' . $servicelineQry . ' ' . $dateFilter . ' ' . $documentQry . '
        GROUP BY erp_generalledger.chartOfAccountSystemID ' . $generalLedgerGroup . ') AS gl ON erp_companyreporttemplatelinks.glAutoID = gl.chartOfAccountSystemID
    LEFT JOIN(
                SELECT
                    ' . $budgetQuery . ' 
                FROM
                    erp_budjetdetails
                WHERE
                    erp_budjetdetails.companySystemID IN(' . join(',
                ', $companyID) . '
            ) ' . $servicelineQryForBudget . ' ' . $budgetWhereQuery . '
            ) AS budget
        ON
            budget.chartOfAccountID = erp_companyreporttemplatelinks.glAutoID ' . $budgetJoin . '
WHERE
	erp_companyreporttemplatelinks.templateMasterID = ' . $request->templateType . ' AND erp_companyreporttemplatelinks.glAutoID IS NOT NULL
ORDER BY
	erp_companyreporttemplatelinks.sortOrder) a WHERE (' . join(' OR ', $whereQry) . ')';

        $output = \DB::select($sql);
        return $output;
    }

    function getCashflowOpeningBalanceQry($request, $currency, $columnTemplateID)
    {
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $companyID = collect($request->companySystemID)->pluck('companySystemID')->toArray();
        //$serviceline = collect($request->serviceLineSystemID)->pluck('serviceLineSystemID')->toArray();
        $documents = ReportTemplateDocument::pluck('documentSystemID')->toArray();

        $glCodes = ReportTemplateLinks::where('templateMasterID', $request->templateType)->whereNotNull('glAutoID')->pluck('glAutoID')->toArray();

        if ($columnTemplateID == 1) {
            $output = GeneralLedger::selectRaw('SUM(' . $currency . ') as openingBalance, companySystemID')->whereIN('companySystemID', $companyID)->whereIN('documentSystemID', $documents)->whereIN('chartOfAccountSystemID', $glCodes)->whereRaw('(DATE(erp_generalledger.documentDate) < "' . $fromDate . '")')->groupBy('companySystemID')->get();
        } else {
            $output = GeneralLedger::selectRaw('SUM(' . $currency . ') as openingBalance')->whereIN('companySystemID', $companyID)->whereIN('documentSystemID', $documents)->whereIN('chartOfAccountSystemID', $glCodes)->whereRaw('(DATE(erp_generalledger.documentDate) < "' . $fromDate . '")')->first();
        }


        return $output;
    }

    function getCustomizeFinancialDetailTOTQry($request, $linkedcolumnQry, $financeYear, $period, $columnKeys, $budgetQuery, $budgetWhereQuery, $changeSelect)
    {
        if ($request->dateType == 1) {
            $toDate = new Carbon($request->toDate);
            $toDate = $toDate->format('Y-m-d');
            $fromDate = new Carbon($request->fromDate);
            $fromDate = $fromDate->format('Y-m-d');
        } else {
            $period = CompanyFinancePeriod::find($request->month);
            $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
            $fromDate = Carbon::parse($period->dateFrom)->format('Y-m-d');
        }

        $companyID = collect($request->companySystemID)->pluck('companySystemID')->toArray();
        $serviceline = collect($request->serviceLineSystemID)->pluck('serviceLineSystemID')->toArray();
        $documents = ReportTemplateDocument::pluck('documentSystemID')->toArray();

        $lastYearStartDate = Carbon::parse($financeYear->bigginingDate);
        $lastYearStartDate = $lastYearStartDate->subYear()->format('Y-m-d');
        $lastYearEndDate = Carbon::parse($financeYear->endingDate);
        $lastYearEndDate = $lastYearEndDate->subYear()->format('Y-m-d');

        $dateFilter = '';
        $documentQry = '';
        $servicelineQry = '';
        $servicelineQryForBudget = '';
        if ($request->dateType == 1) {
            //$dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '") OR (DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $lastYearEndDate . '"))';
            // $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
        } else {
            if ($request->accountType == 2) {
                //$dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '") OR (DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $lastYearEndDate . '"))';
                // $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
            } else {
                $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
                // $dateFilter = 'AND (DATE(erp_generalledger.documentDate) <= "' . $toDate . '")';
            }
        }

        if ($request->accountType == 3) {
            if (count($documents) > 0) {
                $documentQry = 'AND erp_generalledger.documentSystemID IN (' . join(',', $documents) . ')';
            }
        }

        if ($request->accountType == 2) {
            if (count($serviceline) > 0) {
                $servicelineQry = 'AND erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
                $servicelineQryForBudget = 'AND erp_budjetdetails.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
            }
        }
        $secondLinkedcolumnQry = '';
        $thirdLinkedcolumnQry = '';
        $whereQry = [];
        foreach ($columnKeys as $key => $val) {
            $coloumnShortCode = explode('-', $key)[0];
            $secondLinkedcolumnQry .= 'IFNULL(SUM(`' . $key . '`),0) AS `' . $key . '`,';
            if ($coloumnShortCode == "BCM" && !$changeSelect) {
                $thirdLinkedcolumnQry .= 'IFNULL( bAmountMonth,  0 ) AS `' . $key . '`,';
            } else if ($coloumnShortCode == "BYTD" && !$changeSelect) {
                $thirdLinkedcolumnQry .= 'IFNULL( bAmountYear,  0 ) AS `' . $key . '`,';
            } else {
                $thirdLinkedcolumnQry .= 'IFNULL(IF(linkCatType != templateCatType,`' . $key . '` * -1,`' . $key . '`),0) AS `' . $key . '`,';
            }
            $whereQry[] .= 'b.`' . $key . '` != 0';
        }

        $firstLinkedcolumnQry = !empty($linkedcolumnQry) ? $linkedcolumnQry . ',' : '';

        $budgetJoinQuery1 = '';
        $budgetJoinQuery2 = '';
        if ($changeSelect) {
            $budgetJoinQuery2 = ' LEFT JOIN(
                            SELECT
                                ' . $budgetQuery . ' 
                            FROM
                                erp_budjetdetails
                            WHERE
                                erp_budjetdetails.companySystemID IN(' . join(',
                            ', $companyID) . '
                        ) ' . $servicelineQryForBudget . ' ' . $budgetWhereQuery . '
                        ) AS budget
                    ON
                        budget.chartOfAccountID = erp_generalledger.chartOfAccountSystemID
            ';
        } else {
            $budgetJoinQuery1 = ' LEFT JOIN(
                            SELECT
                                ' . $budgetQuery . ' 
                            FROM
                                erp_budjetdetails
                            WHERE
                                erp_budjetdetails.companySystemID IN(' . join(',
                            ', $companyID) . '
                        ) ' . $servicelineQryForBudget . ' ' . $budgetWhereQuery . '
                        ) AS budget
                    ON
                        budget.chartOfAccountID = a.glAutoID
            ';
        }


        $sql = 'SELECT * FROM (SELECT
	' . $secondLinkedcolumnQry . ' 
	templateDetailID,
	description
FROM
	(
		SELECT
			' . $thirdLinkedcolumnQry . ' 
			templateDetailID,
			description
			FROM
			(
				(
					SELECT
						' . $firstLinkedcolumnQry . ' 
						erp_generalledger.chartOfAccountSystemID
					FROM
						erp_generalledger
                    ' . $budgetJoinQuery2 . '
					INNER JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
					WHERE
						erp_generalledger.companySystemID IN (
							' . join(',
							', $companyID) . '
						) ' . $servicelineQry . ' ' . $dateFilter . ' ' . $documentQry . '
					GROUP BY
						erp_generalledger.chartOfAccountSystemID
				) g
				INNER JOIN (
					SELECT
						erp_companyreporttemplatelinks.glAutoID,
						erp_companyreporttemplatelinks.templateDetailID,
						erp_companyreporttemplatelinks.categoryType AS linkCatType,
						erp_companyreporttemplatedetails.description,
						erp_companyreporttemplatedetails.categoryType AS templateCatType
					FROM
						erp_companyreporttemplatelinks
					INNER JOIN erp_companyreporttemplatedetails ON erp_companyreporttemplatelinks.templateDetailID = erp_companyreporttemplatedetails.detID
					WHERE
						erp_companyreporttemplatelinks.templateMasterID = ' . $request->templateType . '
					ORDER BY
						erp_companyreporttemplatedetails.sortOrder
				) AS a ON a.glAutoID = g.chartOfAccountSystemID
                ' . $budgetJoinQuery1 . '
        )
	) f
GROUP BY
	templateDetailID) b WHERE (' . join(' OR ', $whereQry) . ')';

        $output = \DB::select($sql);
        return $output;
    }


    function getCustomizeFinancialUncategorizeQry($request, $linkedcolumnQry, $linkedcolumnQry2, $financeYear, $period, $columnKeys, $budgetQuery, $budgetWhereQuery, $columnTemplateID)
    {

        $reportTemplateMaster = ReportTemplate::find($request->templateType);
        $uncategorizeGL = ChartOfAccount::where('catogaryBLorPL', $reportTemplateMaster->categoryBLorPL)->where('isActive', 1)->where('isApproved', 1)->whereNotExists(function ($query) use ($request) {
            $query->selectRaw('*')
                ->from('erp_companyreporttemplatelinks')
                ->where('templateMasterID', $request->templateType)
                ->whereRaw('chartofaccounts.chartOfAccountSystemID = erp_companyreporttemplatelinks.glAutoID');
        })->pluck('chartOfAccountSystemID')->toArray();

        if (count($uncategorizeGL) > 0) {
            $newColumData = $this->getFinancialCustomizeRptColumnQry($request, true);
            $linkedcolumnQry = $newColumData['linkedcolumnQry'];
            $linkedcolumnQry2 = $newColumData['linkedcolumnQry2'];
            $columnKeys = $newColumData['columnKeys'];
            $budgetQuery = $newColumData['budgetQuery'];
            $budgetWhereQuery = $newColumData['budgetWhereQuery'];
        }

        if ($request->dateType == 1) {
            $toDate = new Carbon($request->toDate);
            $toDate = $toDate->format('Y-m-d');
            $fromDate = new Carbon($request->fromDate);
            $fromDate = $fromDate->format('Y-m-d');
        } else {
            $period = CompanyFinancePeriod::find($request->month);
            $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
            $fromDate = Carbon::parse($period->dateFrom)->format('Y-m-d');
        }

        $companyID = collect($request->companySystemID)->pluck('companySystemID')->toArray();
        $serviceline = collect($request->serviceLineSystemID)->pluck('serviceLineSystemID')->toArray();

        $documents = ReportTemplateDocument::pluck('documentSystemID')->toArray();

        $lastYearStartDate = Carbon::parse($financeYear->bigginingDate);
        $lastYearStartDate = $lastYearStartDate->subYear()->format('Y-m-d');
        $lastYearEndDate = Carbon::parse($financeYear->endingDate);
        $lastYearEndDate = $lastYearEndDate->subYear()->format('Y-m-d');

        $dateFilter = '';
        $documentQry = '';
        $servicelineQry = '';
        $servicelineQryForBudget = '';
        if ($request->dateType == 1) {
            $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
        } else {
            if ($request->accountType == 2) {
                $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
            } else {
                $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
                $dateFilter = 'AND (DATE(erp_generalledger.documentDate) <= "' . $toDate . '")';
            }
        }

        if ($request->accountType == 3) {
            if (count($documents) > 0) {
                $documentQry = 'AND erp_generalledger.documentSystemID IN (' . join(',', $documents) . ')';
            }
        }

        if ($request->accountType == 2) {
            if (count($serviceline) > 0) {
                $servicelineQry = 'AND erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
                $servicelineQryForBudget = 'AND erp_budjetdetails.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
            }
        }

        $firstLinkedcolumnQry = !empty($linkedcolumnQry) ? $linkedcolumnQry . ',' : '';

        $secondLinkedcolumnQry = '';
        $whereQry = [];
        foreach ($columnKeys as $key => $val) {
            $secondLinkedcolumnQry .= 'IFNULL(`' . $val . '`,0) AS `' . $val . '`,';
            $whereQry[] .= 'a.`' . $val . '` != 0';
        }

        $thirdLinkedcolumnQry = !empty($linkedcolumnQry2) ? $linkedcolumnQry2 . ',' : '';

        $budgetJoin = '';
        $groupByCompID = '';
        $generalLedgerGroup = '';
        if ($columnTemplateID == 1) {
            $firstLinkedcolumnQry .= ' erp_generalledger.companySystemID AS compID,';
            $thirdLinkedcolumnQry .= ' compID,';
            $secondLinkedcolumnQry .= ' compID,';
            $budgetJoin = ' AND erp_generalledger.companySystemID = budget.companySystemID';
            $generalLedgerGroup = ' ,erp_generalledger.companySystemID';
            $groupByCompID = ' GROUP BY compID';
        }


        $output = [];
        $outputDetail = [];
        if (count($uncategorizeGL) > 0) {
            $sql = 'SELECT  ' . $thirdLinkedcolumnQry . ' chartOfAccountSystemID,glCode,glDescription,glAutoID FROM (SELECT
            ' . $firstLinkedcolumnQry . '
            erp_generalledger.chartOfAccountSystemID,
            erp_generalledger.chartOfAccountSystemID as glAutoID,
            chartofaccounts.AccountCode as glCode,
	        chartofaccounts.AccountDescription as glDescription 
        FROM
            erp_generalledger
            LEFT JOIN(
                    SELECT
                        ' . $budgetQuery . ' 
                    FROM
                        erp_budjetdetails
                    WHERE
                        erp_budjetdetails.companySystemID IN(' . join(',
                    ', $companyID) . '
                ) ' . $servicelineQryForBudget . ' ' . $budgetWhereQuery . '
                ) AS budget
            ON
                budget.chartOfAccountID = erp_generalledger.chartOfAccountSystemID ' . $budgetJoin . '
            INNER JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
        WHERE
            erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') AND
            erp_generalledger.chartOfAccountSystemID IN (' . join(',', $uncategorizeGL) . ')
            ' . $servicelineQry . ' ' . $dateFilter . ' ' . $documentQry . '
        GROUP BY
            erp_generalledger.chartOfAccountSystemID ' . $generalLedgerGroup . ') a WHERE ' . join(' OR ', $whereQry) . $groupByCompID;

            $output = \DB::select($sql);

            $sql = 'SELECT  ' . $secondLinkedcolumnQry . ' chartOfAccountSystemID,glCode,glDescription,glAutoID FROM (SELECT
            ' . $firstLinkedcolumnQry . '
            erp_generalledger.chartOfAccountSystemID,
            erp_generalledger.chartOfAccountSystemID as glAutoID,
            chartofaccounts.AccountCode as glCode,
	        chartofaccounts.AccountDescription as glDescription 
        FROM
            erp_generalledger
            LEFT JOIN(
                    SELECT
                        ' . $budgetQuery . ' 
                    FROM
                        erp_budjetdetails
                    WHERE
                        erp_budjetdetails.companySystemID IN(' . join(',
                    ', $companyID) . '
                ) ' . $servicelineQryForBudget . ' ' . $budgetWhereQuery . '
                ) AS budget
            ON
                budget.chartOfAccountID = erp_generalledger.chartOfAccountSystemID ' . $budgetJoin . '
            INNER JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
        WHERE
            erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') AND
            erp_generalledger.chartOfAccountSystemID IN (' . join(',', $uncategorizeGL) . ')
            ' . $servicelineQry . ' ' . $dateFilter . ' ' . $documentQry . '
        GROUP BY
            erp_generalledger.chartOfAccountSystemID ' . $generalLedgerGroup . ') a WHERE ' . join(' OR ', $whereQry);

            $outputDetail = \DB::select($sql);
        }


        return ['output' => $output, 'outputDetail' => $outputDetail];
    }

    function getCustomizeFinancialGrandTotalQry($request, $linkedcolumnQry, $linkedcolumnQry2, $financeYear, $period, $columnKeys, $budgetQuery, $budgetWhereQuery, $columnTemplateID)
    {

        if ($request->dateType == 1) {
            $toDate = new Carbon($request->toDate);
            $toDate = $toDate->format('Y-m-d');
            $fromDate = new Carbon($request->fromDate);
            $fromDate = $fromDate->format('Y-m-d');
        } else {
            $period = CompanyFinancePeriod::find($request->month);
            $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
            $fromDate = Carbon::parse($period->dateFrom)->format('Y-m-d');
        }

        $companyID = collect($request->companySystemID)->pluck('companySystemID')->toArray();
        $serviceline = collect($request->serviceLineSystemID)->pluck('serviceLineSystemID')->toArray();
        $documents = ReportTemplateDocument::pluck('documentSystemID')->toArray();

        $lastYearStartDate = Carbon::parse($financeYear->bigginingDate);
        $lastYearStartDate = $lastYearStartDate->subYear()->format('Y-m-d');
        $lastYearEndDate = Carbon::parse($financeYear->endingDate);
        $lastYearEndDate = $lastYearEndDate->subYear()->format('Y-m-d');

        $dateFilter = '';
        $documentQry = '';
        $servicelineQry = '';
        $servicelineQryForBudget = '';
        if ($request->dateType == 1) {
            //$dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '") OR (DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $lastYearEndDate . '"))';
            // $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
        } else {
            if ($request->accountType == 2) {
                //$dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '") OR (DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $lastYearEndDate . '"))';
                // $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
            } else {
                $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
                // $dateFilter = 'AND (DATE(erp_generalledger.documentDate) <= "' . $toDate . '")';
            }
        }

        if ($request->accountType == 3) {
            if (count($documents) > 0) {
                $documentQry = 'AND erp_generalledger.documentSystemID IN (' . join(',', $documents) . ')';
            }
        }

        if ($request->accountType == 2) {
            if (count($serviceline) > 0) {
                $servicelineQry = 'AND erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
                $servicelineQryForBudget = 'AND erp_budjetdetails.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
            }
        }

        $reportTemplateMaster = ReportTemplate::find($request->templateType);
        $uncategorizeGL = ChartOfAccount::where('catogaryBLorPL', $reportTemplateMaster->categoryBLorPL)->where('isActive', 1)->where('isApproved', 1)->whereNotExists(function ($query) use ($request) {
            $query->selectRaw('*')
                ->from('erp_companyreporttemplatelinks')
                ->where('templateMasterID', $request->templateType)
                ->whereRaw('chartofaccounts.chartOfAccountSystemID = erp_companyreporttemplatelinks.glAutoID');
        })->pluck('chartOfAccountSystemID')->toArray();

        $thirdLinkedcolumnQry = '';
        $whereQry = [];
        $secondLinkedcolumnQry = !empty($linkedcolumnQry2) ? $linkedcolumnQry2 : '';
        foreach ($columnKeys as $key => $val) {
            $coloumnShortCode = explode('-', $val)[0];
            if ($coloumnShortCode == "BCM") {
                $thirdLinkedcolumnQry .= 'IFNULL( bAmountMonth,  0 ) AS `' . $val . '`,';
            } else if ($coloumnShortCode == "BYTD") {
                $thirdLinkedcolumnQry .= 'IFNULL( bAmountYear,  0 ) AS `' . $val . '`,';
            } else {
                $thirdLinkedcolumnQry .= 'IFNULL(IF(linkCatType != templateCatType,`' . $val . '` * -1,`' . $val . '`),0) AS `' . $val . '`,';
            }
            $whereQry[] .= 'b.`' . $val . '` != 0';
        }

        $firstLinkedcolumnQry = !empty($linkedcolumnQry) ? $linkedcolumnQry . ',' : '';

        $budgetJoin1 = '';
        $budgetJoin2 = '';
        $generalLedgerGroup = '';
        $unionGroupBy = '';
        $templateGroupBY = '';
        if ($columnTemplateID == 1) {
            $firstLinkedcolumnQry .= ' erp_generalledger.companySystemID AS compID,';
            $secondLinkedcolumnQry .= ' ,compID';
            $templateGroupBY .= ' ,compID';
            $thirdLinkedcolumnQry .= ' compID,';
            $budgetJoin1 = ' AND erp_generalledger.companySystemID = budget.companySystemID';
            $budgetJoin2 = ' AND g.compID = budget.companySystemID';
            $generalLedgerGroup = ' ,erp_generalledger.companySystemID';
            $unionGroupBy = ' GROUP BY compID';
        }

        $unionQry = '';
        if (count($uncategorizeGL) > 0) {
            $unionQry = ' UNION SELECT  ' . $secondLinkedcolumnQry . ' FROM (SELECT
            ' . $firstLinkedcolumnQry . '
            erp_generalledger.chartOfAccountSystemID
        FROM
            erp_generalledger
            LEFT JOIN(
                    SELECT
                        ' . $budgetQuery . ' 
                    FROM
                        erp_budjetdetails
                    WHERE
                        erp_budjetdetails.companySystemID IN(' . join(',
                    ', $companyID) . '
                ) ' . $servicelineQryForBudget . ' ' . $budgetWhereQuery . '
                ) AS budget
            ON
                budget.chartOfAccountID = erp_generalledger.chartOfAccountSystemID ' . $budgetJoin1 . '
            INNER JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
        WHERE
            erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') AND
            erp_generalledger.chartOfAccountSystemID IN (' . join(',', $uncategorizeGL) . ')
            ' . $servicelineQry . ' ' . $dateFilter . ' ' . $documentQry . '
        GROUP BY
            erp_generalledger.chartOfAccountSystemID ' . $generalLedgerGroup . ') a';
        }

        $sql = 'SELECT ' . $secondLinkedcolumnQry . ' FROM (SELECT * FROM (SELECT
	' . $secondLinkedcolumnQry . '
FROM
	(
		SELECT
			' . $thirdLinkedcolumnQry . ' 
			templateDetailID,
			description
			FROM
			(
				(
					SELECT
						' . $firstLinkedcolumnQry . ' 
						erp_generalledger.chartOfAccountSystemID
					FROM
						erp_generalledger
					INNER JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
					WHERE
						erp_generalledger.companySystemID IN (
							' . join(',
							', $companyID) . '
						) ' . $servicelineQry . ' ' . $dateFilter . ' ' . $documentQry . '
					GROUP BY
						erp_generalledger.chartOfAccountSystemID ' . $generalLedgerGroup . '
				) g
				INNER JOIN (
					SELECT
						erp_companyreporttemplatelinks.glAutoID,
						erp_companyreporttemplatelinks.templateDetailID,
						erp_companyreporttemplatelinks.categoryType AS linkCatType,
						erp_companyreporttemplatedetails.description,
						erp_companyreporttemplatedetails.categoryType AS templateCatType
					FROM
						erp_companyreporttemplatelinks
					INNER JOIN erp_companyreporttemplatedetails ON erp_companyreporttemplatelinks.templateDetailID = erp_companyreporttemplatedetails.detID
					WHERE
						erp_companyreporttemplatelinks.templateMasterID = ' . $request->templateType . '
					ORDER BY
						erp_companyreporttemplatedetails.sortOrder
				) AS a ON a.glAutoID = g.chartOfAccountSystemID
			)
            LEFT JOIN(
                    SELECT
                        ' . $budgetQuery . ' 
                    FROM
                        erp_budjetdetails
                    WHERE
                        erp_budjetdetails.companySystemID IN(' . join(',
                    ', $companyID) . '
                ) ' . $servicelineQryForBudget . ' ' . $budgetWhereQuery . '
                ) AS budget
            ON
                budget.chartOfAccountID = a.glAutoID ' . $budgetJoin2 . '
	) f
GROUP BY
	templateDetailID' . $templateGroupBY . ') b WHERE (' . join(' OR ', $whereQry) . ') ' . $unionQry . ') b' . $unionGroupBy;

        $output = \DB::select($sql);
        return $output;
    }

    /**
     * function to decode tax formula to multiple combined formula
     * @param $columnLinkID
     * @param $rowValues
     * @param $columnArray
     * @param $linkedRowHead
     * @param $type
     * @return string
     */
    public function columnFormulaDecode($columnLinkID, $rowValues, $columnArray, $linkedRowHead = false, $type)
    {
        global $globalFormula;
        $finalFormula = '';
        $taxFormula = ReportTemplateColumnLink::find($columnLinkID);
        $globalFormula = $taxFormula->formula;
        $linkedColumns = $taxFormula->formulaColumnID;
        $linkedRows = '';
        if ($linkedRowHead) {
            $linkedRows = $taxFormula->formulaRowID;
        }
        $sepFormulaArr = $this->decodeColumnFormula($linkedColumns, $linkedRows, $rowValues, $columnArray, $type);
        $globalFormula = '';
        if ($sepFormulaArr) {
            $fomulaFinal = '';
            $formulaArr = explode('~', $sepFormulaArr);
            if ($formulaArr) {
                foreach ($formulaArr as $val2) {
                    $removedFirstChar = substr($val2, 1);
                    $fomulaFinal .= $removedFirstChar;
                }
                $finalFormula = $fomulaFinal;
            }
        }

        return $finalFormula;
    }


    /**
     * function to decode customize report
     * @param $linkedColumns - connected formulas
     * @param $rowValues
     * @param $columnArray
     * @param $linkedRows
     * @param $type
     * @return mixed
     */
    public function decodeColumnFormula($linkedColumns, $linkedRows, $rowValues, $columnArray, $type)
    {
        global $globalFormula;
        $taxFormula = ReportTemplateColumnLink::whereIn('columnLinkID', explode(',', $linkedColumns))->get();
        if ($taxFormula) {
            foreach ($taxFormula as $val) {
                $searchVal = '#' . $val['columnLinkID'];
                if (!empty($val['formulaColumnID'])) {
                    $replaceVal = '|(~' . $val['formula'] . '~|)';
                    $globalFormula = str_replace($searchVal, $replaceVal, $globalFormula);
                    $return = $this->decodeColumnFormula($val['formulaColumnID'], $val['formulaRowID'], $rowValues, $columnArray, $type);
                    if (is_array($return)) {
                        if ($return[0] == 'e') {
                            return $return;
                            break;
                        }
                    }
                } else {
                    $replaceVal = '';
                    if ($type == 1) {
                        $replaceVal = '#' . $columnArray[$val['shortCode']];
                    } else {
                        $replaceVal = '#IFNULL(SUM(`' . $val->shortCode . '-' . $val->columnLinkID . '`),0)';
                    }

                    $globalFormula = str_replace_first($searchVal, $replaceVal, $globalFormula);
                    /*$replaceVal = '/'.$columnArray[$val['shortCode']].'/';
                    $globalFormula = preg_replace($searchVal, $replaceVal, $globalFormula,1);*/
                }
            }
        }

        // if there is a row linked to the formula calculation will be done here
        if ($linkedRows) {
            $explodedLinkedColumns = explode(',', $linkedColumns);
            $linkedColumnsShortCode = ReportTemplateColumnLink::whereIN('columnLinkID', $explodedLinkedColumns)->get();
            foreach ($linkedColumnsShortCode as $column) {
                $columnCustomeCode = $column->shortCode . '-' . $column->columnLinkID;
                $explodeLinkedRows = explode(',', $linkedRows);
                if ($explodeLinkedRows) {
                    foreach ($explodeLinkedRows as $val) {
                        $searchVal = '$' . $val;
                        $filtered = $rowValues->where('templateDetailID', $val);
                        $detValues = $filtered->values();
                        $replaceVal = '';
                        if (count($detValues) > 0) {
                            $replaceVal = '$' . $detValues[0]->$columnCustomeCode;
                        } else {
                            $replaceVal = '$0';
                        }
                        $globalFormula = str_replace($searchVal, $replaceVal, $globalFormula);
                    }
                }
            }
        }
        return $globalFormula;
    }

    public function reportTemplateGLDrillDown(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $output = $this->reportTemplateGLDrillDownQry($request);

        $total = collect($output)->pluck($input['selectedColumn'])->toArray();
        $total = array_sum($total);

        $request->request->remove('search.value');

        return \DataTables::of($output)
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->with('total', $total)
            ->make(true);
    }


    public function reportTemplateGLDrillDownQry($request)
    {

        $input = $request->all();
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');

        $financeYear = CompanyFinanceYear::find($request->companyFinanceYearID);
        $period = CompanyFinancePeriod::find($request->month);

        // get generated customized column
        $generatedColumn = $this->getFinancialCustomizeRptColumnQry($request);
        $linkedcolumnQry = $generatedColumn['linkedcolumnQry'];
        $budgetQuery = $generatedColumn['budgetQuery'];
        $budgetWhereQuery = $generatedColumn['budgetWhereQuery'];

        $firstLinkedcolumnQry = !empty($linkedcolumnQry) ? $linkedcolumnQry . ',' : '';

        $companyID = collect($request->companySystemID)->pluck('companySystemID')->toArray();
        $serviceline = collect($request->serviceLineSystemID)->pluck('serviceLineSystemID')->toArray();

        $documents = ReportTemplateDocument::pluck('documentSystemID')->toArray();

        $lastYearStartDate = Carbon::parse($financeYear->bigginingDate);
        $lastYearStartDate = $lastYearStartDate->subYear()->format('Y-m-d');
        $lastYearEndDate = Carbon::parse($financeYear->endingDate);
        $lastYearEndDate = $lastYearEndDate->subYear()->format('Y-m-d');

        $dateFilter = '';
        $documentQry = '';
        $servicelineQry = '';
        $servicelineQryForBudget = '';
        if ($request->dateType == 1) {
            $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
        } else {
            if ($request->accountType == 2) {
                $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
            } else {
                $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
                $dateFilter = 'AND (DATE(erp_generalledger.documentDate) <= "' . $toDate . '")';
            }
        }

        if ($request->accountType == 3) {
            if (count($documents) > 0) {
                $documentQry = 'AND erp_generalledger.documentSystemID IN (' . join(',', $documents) . ')';
            }
        }

        if ($request->accountType == 2) {
            if (count($serviceline) > 0) {
                $servicelineQry = 'AND erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
                $servicelineQryForBudget = 'AND erp_budjetdetails.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
            }
        }

        if ($request->columnTemplateID == 1) {
            $selectedCompanyData = Company::where('CompanyID', $request->selectedCompany)->first();

            $companyID = collect($selectedCompanyData->companySystemID)->toArray();
        }

        $sql = 'SELECT `' . $input['selectedColumn'] . '`,glCode,AccountDescription,documentCode,documentDate,ServiceLineDes,partyName,documentNarration,clientContractID,documentSystemCode,documentSystemID FROM (SELECT
						' . $firstLinkedcolumnQry . ' 
						glCode,AccountDescription,documentCode,documentDate,serviceline.ServiceLineDes,
						erp_generalledger.documentNarration,
						erp_generalledger.clientContractID,
						IF
                        ( erp_generalledger.documentSystemID = 20 OR erp_generalledger.documentSystemID = 21 OR erp_generalledger.documentSystemID = 19, customermaster.CustomerName, suppliermaster.supplierName ) AS partyName,
                         erp_generalledger.documentSystemCode,
                         erp_generalledger.documentSystemID
					FROM
						erp_generalledger
					INNER JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
					LEFT JOIN serviceline ON serviceline.serviceLineSystemID = erp_generalledger.serviceLineSystemID
					LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                    LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem 
					WHERE
					    erp_generalledger.chartOfAccountSystemID = ' . $input['glAutoID'] . ' AND
						erp_generalledger.companySystemID IN (
							' . join(',
							', $companyID) . '
						) ' . $servicelineQry . ' ' . $dateFilter . ' ' . $documentQry . ' GROUP BY GeneralLedgerID) a WHERE `' . $input['selectedColumn'] . '` != 0';
        return DB::select($sql);
    }

    public function reportTemplateGLDrillDownExport(Request $request)
    {

        $input = $request->all();
        $type = $request->type;
        $data = array();
        $output = $this->reportTemplateGLDrillDownQry($request);

        if ($output) {
            $total = collect($output)->pluck($input['selectedColumn'])->toArray();
            $total = array_sum($total);
            $x = 0;
            foreach ($output as $val) {
                $tem = (array)$val;
                $data[$x]['Document Number'] = $val->documentCode;
                $data[$x]['Date'] = \Helper::dateFormat($val->documentDate);
                $data[$x]['Document Narration'] = $val->documentNarration;
                $data[$x]['Service Line'] = $val->ServiceLineDes;
                $data[$x]['Contract'] = $val->clientContractID;
                $data[$x]['Supplier/Customer'] = $val->partyName;
                $data[$x][$input['selectedColumn']] = $tem[$input['selectedColumn']];
                $x++;
            }

            $data[$x]['Document Number'] = '';
            $data[$x]['Date'] = '';
            $data[$x]['Document Narration'] = '';
            $data[$x]['Service Line'] = '';
            $data[$x]['Contract'] = '';
            $data[$x]['Supplier/Customer'] = 'Total';
            $data[$x][$input['selectedColumn']] = $total;
        }


        \Excel::create('trial_balance', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);

        return $this->sendResponse(array(), 'successfully export');
    }

    function getFinancialCustomizeRptColumnQry($request, $changeSelect = false)
    {

        $toDate = '';
        $fromDate = '';
        $month = '';
        $period = '';
        $currencyColumn = '';
        $budgetColumn = '';
        $columnArray = [];
        $columnHeaderArray = [];
        $columnHeader = [];
        $columnHeaderMapping = [];
        $linkedcolumnArray = [];
        $linkedcolumnArray2 = [];
        $linkedcolumnArray3 = [];

        $financeYear = CompanyFinanceYear::find($request->companyFinanceYearID);
        if ($request->dateType == 1) {
            $toDate = new Carbon($request->toDate);
            $toDate = $toDate->format('Y-m-d');
            $fromDate = new Carbon($request->fromDate);
            $fromDate = $fromDate->format('Y-m-d');
        } else {
            $period = CompanyFinancePeriod::find($request->month);
            $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
            $month = Carbon::parse($toDate)->format('Y-m-d');
            $fromDate = Carbon::parse($period->dateFrom)->format('Y-m-d');
        }

        //assign db currency column
        if ($request->currency == 1) {
            $currencyColumn = 'documentLocalAmount';
            $budgetColumn = 'budjetAmtLocal';
        } else {
            $currencyColumn = 'documentRptAmount';
            $budgetColumn = 'budjetAmtRpt';
        }

        $reportTemplateMasterData = ReportTemplate::find($request->templateType);

        $columnTemplateID = $reportTemplateMasterData->columnTemplateID;

        $columns = ReportTemplateColumns::all();
        $linkedColumn = ReportTemplateColumnLink::ofTemplate($request->templateType)->where('hideColumn', 0)->orderBy('sortOrder')->get();
        if (count($columns) > 0) {
            $currentYearPeriod = CarbonPeriod::create($financeYear->bigginingDate, '1 month', $financeYear->endingDate);
            $currentYearPeriodArr = [];
            $lastYearStartDate = Carbon::parse($financeYear->bigginingDate);
            $lastYearStartDate = $lastYearStartDate->subYear()->format('Y-m-d');
            $lastYearEndDate = Carbon::parse($financeYear->endingDate);
            $lastYearEndDate = $lastYearEndDate->subYear()->format('Y-m-d');
            $lastYearPeriod = CarbonPeriod::create($lastYearStartDate, '1 month', $lastYearEndDate);
            $lastYearPeriodArr = [];
            $linkedcolumnArrayFinal = [];
            $linkedcolumnArrayFinal2 = [];
            $linkedcolumnArrayFinal3 = [];
            $currentMonth = Carbon::parse($toDate)->format('Y-m');
            $currentYear = Carbon::parse($toDate)->format('Y');
            $currentYearCurrentMonthOnly = (int)Carbon::parse($toDate)->format('m');
            $prevMonth = Carbon::parse($currentMonth)->subMonth()->format('Y-m');
            $prevMonth2 = Carbon::parse($currentMonth)->subMonth(2)->format('Y-m');
            $LCurrentMonth = Carbon::parse($toDate)->subYear()->format('Y-m');
            $LYear = Carbon::parse($toDate)->subYear()->format('Y');
            $LPrevMonth = Carbon::parse($LCurrentMonth)->subMonth()->format('Y-m');
            $LPrevMonth2 = Carbon::parse($LCurrentMonth)->subMonth(2)->format('Y-m');
            foreach ($currentYearPeriod as $val) {
                $currentYearPeriodArr[] = $val->format('Y-m');
            }

            foreach ($lastYearPeriod as $val) {
                $lastYearPeriodArr[] = $val->format('Y-m');
            }

            // link queries to selected column
            $currentMonthColumn = collect($columns)->where('type', 3)->values();
            $prevMonthColumn = collect($columns)->where('type', 6)->values();
            if (count($currentMonthColumn) > 0) {
                foreach ($currentMonthColumn as $key => $val) {
                    $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m') = '" . $currentYearPeriodArr[$key] . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
	$currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && chartofaccounts.controlAccounts = 'BSL',$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    $columnHeaderArray[$val->shortCode] = $currentYearPeriodArr[$key];
                }
            }


            if (count($prevMonthColumn) > 0) {
                foreach ($prevMonthColumn as $key => $val) {
                    $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m') = '" . $lastYearPeriodArr[$key] . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
	$currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && chartofaccounts.controlAccounts = 'BSL',$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    $columnHeaderArray[$val->shortCode] = $lastYearPeriodArr[$key];
                }
            }

            foreach ($columns as $val) {
                if ($val->shortCode == 'CM') {
                    $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m') = '" . $currentMonth . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
	$currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && chartofaccounts.controlAccounts = 'BSL',$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    $columnHeaderArray[$val->shortCode] = $currentMonth;
                }
                if ($val->shortCode == 'CM-1') {
                    $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m') = '" . $prevMonth . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
	$currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && chartofaccounts.controlAccounts = 'BSL',$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    $columnHeaderArray[$val->shortCode] = $prevMonth;
                }
                if ($val->shortCode == 'CM-2') {
                    $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m') = '" . $prevMonth2 . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
	$currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && chartofaccounts.controlAccounts = 'BSL',$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    $columnHeaderArray[$val->shortCode] = $prevMonth2;
                }
                if ($val->shortCode == 'LYCM') {
                    $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m') = '" . $LCurrentMonth . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
	$currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && chartofaccounts.controlAccounts = 'BSL',$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    $columnHeaderArray[$val->shortCode] = $LCurrentMonth;
                }
                if ($val->shortCode == 'LYCM-1') {
                    $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m') = '" . $LPrevMonth . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
	$currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && chartofaccounts.controlAccounts = 'BSL',$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    $columnHeaderArray[$val->shortCode] = $LPrevMonth;
                }
                if ($val->shortCode == 'LYCM-2') {
                    $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m') = '" . $LPrevMonth2 . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
	$currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && chartofaccounts.controlAccounts = 'BSL',$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    $columnHeaderArray[$val->shortCode] = $LPrevMonth2;
                }
                if ($val->shortCode == 'CYYTD') {
                    if ($request->accountType == 2) {
                        if ($request->dateType == 2) {
                            $fromDate = Carbon::parse($financeYear->bigginingDate)->format('Y-m-d');
                            $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
                        }
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') >= '" . $fromDate . "' AND DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
	$currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && chartofaccounts.controlAccounts = 'BSL',$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    } else if ($request->accountType == 1) {
                        if ($request->dateType == 2) {
                            $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
                        }
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
	$currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && chartofaccounts.controlAccounts = 'BSL',$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    } else if ($request->accountType == 3) {
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') >= '" . $fromDate . "' AND DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
	$currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && chartofaccounts.controlAccounts = 'BSL',$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    }

                    $columnHeaderArray[$val->shortCode] = $val->shortCode . '-' . $currentYear;
                }
                if ($val->shortCode == 'LYYTD') {
                    if ($request->accountType == 2) {
                        if ($request->dateType == 2) {
                            $fromDate = Carbon::parse($financeYear->bigginingDate)->subYear()->format('Y-m-d');
                            $toDate = Carbon::parse($period->dateTo)->subYear()->format('Y-m-d');
                        }
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') >= '" . $fromDate . "' AND DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
	$currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && chartofaccounts.controlAccounts = 'BSL',$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    } else if ($request->accountType == 1) {
                        if ($request->dateType == 2) {
                            $toDate = Carbon::parse($financeYear->endingDate)->subYear()->format('Y-m-d');
                        }
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
	$currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && chartofaccounts.controlAccounts = 'BSL',$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    } else if ($request->accountType == 3) {
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') >= '" . $fromDate . "' AND DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
	$currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && chartofaccounts.controlAccounts = 'BSL',$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    }
                    $columnHeaderArray[$val->shortCode] = $val->shortCode . '-' . $LYear;
                }
                if ($val->shortCode == 'BCM') {

                    if ($changeSelect) {
                        $columnArray[$val->shortCode] = "IFNULL(bAmountMonth, 0)";
                    } else {
                        $columnArray[$val->shortCode] = "0";
                    }
                    $columnHeaderArray[$val->shortCode] = $val->shortCode;
                }

                if ($val->shortCode == 'BYTD') {
                    if ($changeSelect) {
                        $columnArray[$val->shortCode] = "IFNULL(bAmountYear, 0)";
                    } else {
                        $columnArray[$val->shortCode] = "0";
                    }
                    $columnHeaderArray[$val->shortCode] = $val->shortCode;
                }
            }
        }


        // formatting queries
        if (count($linkedColumn) > 0) {
            foreach ($linkedColumn as $val) {
                if ($val->shortCode == 'FCA' || $val->shortCode == 'FCP') {
                    if ($val->formula == null) {
                        $linkedcolumnArray2[$val->shortCode . '-' . $val->columnLinkID] = 0;
                    } else {
                        // if column has a formula value decoding process is done here
                        $linkedcolumnArray2[$val->shortCode . '-' . $val->columnLinkID] = $this->columnFormulaDecode($val->columnLinkID, [], $columnArray, false, 1);
                    }
                } else if ($val->shortCode == 'CYYTD' || $val->shortCode == 'LYYTD') {
                    $linkedcolumnArray2[$val->shortCode . '-' . $val->columnLinkID] = $columnArray[$val->shortCode];
                } else {
                    $linkedcolumnArray2[$val->shortCode . '-' . $val->columnLinkID] = $columnArray[$val->shortCode];
                }
            }
        }


        // formatting queries
        if (count($linkedcolumnArray2)) {
            foreach ($linkedcolumnArray2 as $key => $val) {
                if ($key == 'FCA') {
                    $linkedcolumnArrayFinal2[$key] = '(' . $val . ') as ' . '`' . $key . '`';
                } else if ($key == 'FCP') {
                    $linkedcolumnArrayFinal2[$key] = 'ROUND(' . $val . ') as ' . '`' . $key . '`';
                } else {
                    $linkedcolumnArrayFinal2[$key] = $val . ' as ' . '`' . $key . '`';
                }
            }
        }

        $linkedcolumnQry2 = implode(',', $linkedcolumnArrayFinal2);

        $budgetQuery = "chartOfAccountID,
                        erp_budjetdetails.companySystemID,
                        IFNULL(
                            SUM(
                                IF(
                                    Year = '" . $currentYear . "' && month <= '" . $currentYearCurrentMonthOnly . "',
                                    $budgetColumn, 
                                    0
                                )
                            ),
                            0
                        ) AS `bAmountYear`,
                        IFNULL(
                            SUM(
                                IF(
                                    Year = '" . $currentYear . "' && month = '" . $currentYearCurrentMonthOnly . "',
                                    $budgetColumn, 
                                    0
                                )
                            ),
                            0
                        ) AS `bAmountMonth`";

        $budgetWhereQuery = " AND Year = " . $currentYear . " GROUP BY erp_budjetdetails.`chartOfAccountID`";

        if ($columnTemplateID == 1) {
            $budgetWhereQuery .= ', erp_budjetdetails.companySystemID';
        }

        //get linked row sum amount to the formula
        $detTotCollect = collect($this->getCustomizeFinancialDetailTOTQry($request, $linkedcolumnQry2, $financeYear, $period, $linkedcolumnArray2, $budgetQuery, $budgetWhereQuery, $changeSelect));

        // formatting queries
        if (count($linkedColumn) > 0) {
            foreach ($linkedColumn as $val) {
                if ($val->shortCode == 'FCA' || $val->shortCode == 'FCP') {
                    if ($val->formula == null) {
                        $linkedcolumnArray[$val->shortCode . '-' . $val->columnLinkID] = 0;
                        $columnHeader[] = ['description' => $val->description, 'bgColor' => $val->bgColor, $val->shortCode . '-' . $val->columnLinkID => $val->description, 'width' => $val->width];
                        $columnHeaderMapping[$val->shortCode . '-' . $val->columnLinkID] = $val->description;
                        $linkedcolumnArray3[$val->shortCode . '-' . $val->columnLinkID] = 0;
                    } else {
                        $linkedcolumnArray[$val->shortCode . '-' . $val->columnLinkID] = $this->columnFormulaDecode($val->columnLinkID, $detTotCollect, $columnArray, true, 1);
                        $columnHeader[] = ['description' => $val->description, 'bgColor' => $val->bgColor, $val->shortCode . '-' . $val->columnLinkID => $val->description, 'width' => $val->width];
                        $columnHeaderMapping[$val->shortCode . '-' . $val->columnLinkID] = $val->description;
                        $linkedcolumnArray3[$val->shortCode . '-' . $val->columnLinkID] = $this->columnFormulaDecode($val->columnLinkID, $detTotCollect, $columnArray, true, 2);
                    }
                } else if ($val->shortCode == 'CYYTD' || $val->shortCode == 'LYYTD') {
                    $linkedcolumnArray[$val->shortCode . '-' . $val->columnLinkID] = $columnArray[$val->shortCode];
                    $columnHeader[] = ['description' => $columnHeaderArray[$val->shortCode], 'bgColor' => $val->bgColor, $val->shortCode . '-' . $val->columnLinkID => $columnHeaderArray[$val->shortCode], 'width' => $val->width];
                    $columnHeaderMapping[$val->shortCode . '-' . $val->columnLinkID] = $columnHeaderArray[$val->shortCode];
                    $linkedcolumnArray3[$val->shortCode . '-' . $val->columnLinkID] = 'IFNULL(SUM(`' . $val->shortCode . '-' . $val->columnLinkID . '`),0)';
                } else if ($val->shortCode == 'BYTD' || $val->shortCode == 'BCM') {
                    $linkedcolumnArray[$val->shortCode . '-' . $val->columnLinkID] = $columnArray[$val->shortCode];
                    $columnHeader[] = ['description' => $columnHeaderArray[$val->shortCode], 'bgColor' => $val->bgColor, $val->shortCode . '-' . $val->columnLinkID => $columnHeaderArray[$val->shortCode], 'width' => $val->width];
                    $columnHeaderMapping[$val->shortCode . '-' . $val->columnLinkID] = $columnHeaderArray[$val->shortCode];
                    $linkedcolumnArray3[$val->shortCode . '-' . $val->columnLinkID] = 'IFNULL(SUM(`' . $val->shortCode . '-' . $val->columnLinkID . '`),0)';
                } else {
                    $linkedcolumnArray[$val->shortCode . '-' . $val->columnLinkID] = $columnArray[$val->shortCode];
                    $columnHeader[] = ['description' => Carbon::parse($columnHeaderArray[$val->shortCode])->format('Y-M'), 'bgColor' => $val->bgColor, $val->shortCode . '-' . $val->columnLinkID => Carbon::parse($columnHeaderArray[$val->shortCode])->format('Y-M'), 'width' => $val->width];
                    $columnHeaderMapping[$val->shortCode . '-' . $val->columnLinkID] = Carbon::parse($columnHeaderArray[$val->shortCode])->format('Y-M');
                    $linkedcolumnArray3[$val->shortCode . '-' . $val->columnLinkID] = 'IFNULL(SUM(`' . $val->shortCode . '-' . $val->columnLinkID . '`),0)';
                }
            }
        }

        $columnKeys = collect($linkedcolumnArray)->keys()->all();
        // formatting queries
        if (count($linkedcolumnArray)) {
            foreach ($linkedcolumnArray as $key => $val) {
                $explodedKey = explode('-', $key);
                if ($explodedKey[0] == 'FCA') {
                    $linkedcolumnArrayFinal[$key] = 'IFNULL(' . $val . ',0) as ' . '`' . $key . '`';
                } else if ($explodedKey[0] == 'FCP') {
                    $linkedcolumnArrayFinal[$key] = 'ROUND(IFNULL(' . $val . ',0)) as ' . '`' . $key . '`';
                } else {
                    $linkedcolumnArrayFinal[$key] = $val . ' as ' . '`' . $key . '`';
                }
            }
        }
        // formatting queries
        if (count($linkedcolumnArray3)) {
            foreach ($linkedcolumnArray3 as $key => $val) {
                $explodedKey = explode('-', $key);
                if ($explodedKey[0] == 'FCA') {
                    $linkedcolumnArrayFinal3[$key] = 'IFNULL(' . $val . ',0) as ' . '`' . $key . '`';
                } else if ($explodedKey[0] == 'FCP') {
                    $linkedcolumnArrayFinal3[$key] = 'ROUND(IFNULL(' . $val . ',0)) as ' . '`' . $key . '`';
                } else {
                    $linkedcolumnArrayFinal3[$key] = $val . ' as ' . '`' . $key . '`';
                }
            }
        }

        // final select statements
        $linkedcolumnQry = implode(',', $linkedcolumnArrayFinal);
        $linkedcolumnQry2 = implode(',', $linkedcolumnArrayFinal3);

        return [
            'linkedcolumnQry' => $linkedcolumnQry,
            'linkedcolumnQry2' => $linkedcolumnQry2,
            'columnArray' => $columnArray,
            'columnKeys' => $columnKeys,
            'columnHeader' => $columnHeader,
            'columnHeaderMapping' => $columnHeaderMapping,
            'budgetQuery' => $budgetQuery,
            'budgetWhereQuery' => $budgetWhereQuery,
            'columnTemplateID' => $columnTemplateID,
            'currencyColumn' => $currencyColumn
        ];
    }

    public function getTBUnmatchedData(Request $request)
    {
        $input = $request->all();
        $toDate = new Carbon($input['toDate']);
        $toDate = $toDate->format('Y-m-d');

        $unmatchedData = GeneralLedger::selectRaw('documentCode, round( sum( erp_generalledger.documentLocalAmount ), 3 ), round( sum( erp_generalledger.documentRptAmount ), 2 ), documentSystemCode, documentSystemID')
            ->where('companySystemID', $input['companySystemID'])
            ->whereDate('documentDate', '<=', $toDate)
            ->groupBy('companySystemID', 'documentSystemCode', 'documentSystemID')
            ->havingRaw('round( sum( erp_generalledger.documentRptAmount ), 2 ) != 0 OR round( sum( erp_generalledger.documentLocalAmount ), 3 ) != 0')
            ->get();

        $respondData = [
            'unMatchedData' => $unmatchedData
        ];

        return $this->sendResponse($respondData, "Unmatched data retrived successfully.");
    }

    public function getICFilterFormData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];

        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);
        if ($isGroup) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

        $companies = Company::whereIN('companySystemID', $companiesByGroup)->where('isGroup', 0)->get();

        $years = CompanyFinanceYear::select(DB::raw("YEAR(bigginingDate) as year"))
            ->whereIn('companySystemID', $companiesByGroup)
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->get();

        $expenseClaimTypes = ExpenseClaimType::all();

        $months = Months::all();

        $output = array(
            'years' => $years,
            'months' => $months,
            'companies' => $companies,
            'expenseClaimTypes' => $expenseClaimTypes
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function validateICReport(Request $request)
    {
        $reportTypeID = $request->reportTypeID;
        switch ($reportTypeID) {

            case 'ICR':
            case 'ICST':
            case 'ICAT':
                $validator = \Validator::make($request->all(), [
                    'reportID' => 'required',
                    'companies' => 'required|array',
                    'months' => 'required|array',
                    'years' => 'required|array'
                ]);
                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                break;
            case 'ICFT':
                $validator = \Validator::make($request->all(), [
                    'reportID' => 'required',
                    'companies' => 'required|array',
                    'transferType' => 'required|array',
                    'fromDate' => 'required_if:dateType,1|nullable|date',
                    'toDate' => 'required_if:dateType,1|nullable|date|after_or_equal:fromDate',
                ]);
                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    public function generateICReport(Request $request)
    {
        $reportTypeID = $request->reportTypeID;
        $company = Company::find($request->companySystemID);

        $output = $this->getICReportQuery($request);

        $requestCurrencyRpt = $this->getReportingCurrencyDetail($output, 'companyReportingCurrencyID');

        $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;
        $currencyCodeRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->CurrencyCode : 'USD';

        $total = array();

        switch ($reportTypeID) {
            case 'ICR': // Inter Company Report

                $total['poTotalComRptCurrency'] = array_sum(collect($output)->pluck('poTotalComRptCurrency')->toArray());
                $total['GRVAmount'] = array_sum(collect($output)->pluck('GRVAmount')->toArray());
                $total['APAmount'] = array_sum(collect($output)->pluck('APAmount')->toArray());
                $total['bookingAmountRpt'] = array_sum(collect($output)->pluck('bookingAmountRpt')->toArray());
                $total['receiveAmountRpt'] = array_sum(collect($output)->pluck('receiveAmountRpt')->toArray());

            case 'ICST':

                $total['bookingAmountRpt'] = array_sum(collect($output)->pluck('bookingAmountRpt')->toArray());
                $total['supplierAmountRpt'] = array_sum(collect($output)->pluck('supplierAmountRpt')->toArray());

            case 'ICAT':

                $total['bookingAmountRpt'] = array_sum(collect($output)->pluck('bookingAmountRpt')->toArray());
                $total['GrvAmount'] = array_sum(collect($output)->pluck('GrvAmount')->toArray());

                break;

            case 'ICFT':

                $total['payAmountCompRpt'] = array_sum(collect($output)->pluck('payAmountCompRpt')->toArray());
                $total['companyRptAmount'] = array_sum(collect($output)->pluck('companyRptAmount')->toArray());

                break;
            default:
                return $this->sendError('No report ID found');
        }

        return \DataTables::of($output)
            ->addIndexColumn()
            ->with('total', $total)
            ->with('companyName', $company->CompanyName)
            ->with('decimalPlaceRpt', $decimalPlaceRpt)
            ->with('currencyCodeRpt', $currencyCodeRpt)
            ->addIndexColumn()
            ->make(true);
    }

    public function getICReportQuery($request)
    {
        $reportTypeID = $request->reportTypeID;

        $where = '';
        $companyIDs = [];
        $yearIDs = [];
        $monthsIDs = [];
        $transferTypes = [];
        $fromDate = null;
        $toDate = null;
        if (isset($request->companies)) {
            $companies = (array)$request->companies;
            $companyIDs = array_filter(collect($companies)->pluck('companySystemID')->toArray());
        }

        if (isset($request->years)) {
            $years = (array)$request->years;
            $yearIDs = array_filter(collect($years)->pluck('year')->toArray());
        }

        if (isset($request->months)) {
            $months = (array)$request->months;
            $monthsIDs = array_filter(collect($months)->pluck('monthID')->toArray());
        }

        if (isset($request->transferType)) {
            $transferType = (array)$request->transferType;
            $transferTypes = array_filter(collect($transferType)->pluck('expenseClaimTypeID')->toArray());
        }

        if (isset($request->fromDate)) {
            $fromDate = new Carbon($request->fromDate);
            $fromDate = $fromDate->format('Y-m-d');
        }

        if (isset($request->toDate)) {
            $toDate = new Carbon($request->toDate);
            $toDate = $toDate->format('Y-m-d');
        }

        switch ($reportTypeID) {

            case 'ICR':

                if (count($companyIDs)) {
                    $where .= 'AND erp_purchaseordermaster.companySystemID IN (' . join(',', $companyIDs) . ')';
                }
                if (count($yearIDs)) {
                    $where .= 'AND YEAR(det.grvDate) IN (' . join(',', $yearIDs) . ')';
                }
                if (count($monthsIDs)) {
                    $where .= 'AND MONTH(det.grvDate) IN (' . join(',', $monthsIDs) . ')';
                }

                $query = 'SELECT
                        erp_purchaseordermaster.purchaseOrderID,
                        erp_purchaseordermaster.companyID,
                        erp_purchaseordermaster.companySystemID,
                        purchaseOrderCode AS POCode,
                        DATE(
                            erp_purchaseordermaster.approvedDate
                        ) AS PODate,
                        sum(IFNULL(poTotalComRptCurrency,0)) AS poTotalComRptCurrency,
                         IFNULL(det.netAmount,0) AS GRVAmount,
                        IFNULL(bsi.totRptAmount,0) AS APAmount,
                        suppliermaster.supplierName,
                        IFNULL(det.bookingAmountRpt,0) AS bookingAmountRpt,
                        IFNULL(det.receiveAmountRpt,0) AS receiveAmountRpt,
                    det.bookingInvCodeSystem as bookinkcode,
                    det.grvDate as grvDate,
                    erp_purchaseordermaster.companyReportingCurrencyID
                    FROM
                        erp_purchaseordermaster
                    LEFT JOIN (
                        SELECT
                            SUM(netAmount) AS netAmount,
                            purchaseOrderMastertID,
                            bookingAmountRpt,
                            custInvoiceDirectAutoID,
                            receiveAmountRpt,
                    bookingInvCodeSystem,
                    grvDate
                        FROM
                            (
                                SELECT
                                    *
                                FROM
                                    (
                                        SELECT
                                            purchaseOrderMastertID,
                                            erp_grvdetails.grvAutoID,
                                            SUM(
                                                netAmount / erp_grvdetails.companyReportingER
                                            ) AS netAmount,
                                            erp_grvdetails.companyReportingER,
                                            erp_grvmaster.grvDate
                                        FROM
                                            erp_grvdetails
                                        LEFT JOIN erp_grvmaster ON erp_grvdetails.grvAutoID = erp_grvmaster.grvAutoID
                                        GROUP BY
                                            erp_grvdetails.purchaseOrderMastertID,
                                            erp_grvdetails.grvAutoID
                                    ) grvID
                                LEFT JOIN (
                                    SELECT
                                        SUM(
                                            erp_custinvoicedirect.bookingAmountRpt
                                        ) AS bookingAmountRpt,
                                        custInvoiceDirectAutoID,
                                        customerGRVAutoID,
                                        SUM(receipt.receiveAmountRpt) AS receiveAmountRpt,
                                        GROUP_CONCAT(receipt.bookingInvCodeSystem) as bookingInvCodeSystem
                                    FROM
                                        erp_custinvoicedirect
                                    LEFT JOIN (
                                        SELECT
                                            erp_custreceivepaymentdet.bookingInvCodeSystem,
                                            SUM(
                                                erp_custreceivepaymentdet.receiveAmountRpt
                                            ) AS receiveAmountRpt
                    
                                        FROM
                                            erp_custreceivepaymentdet
                                        LEFT JOIN erp_customerreceivepayment ON erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
                                        WHERE
                                            (
                                                (
                                                    (
                                                        erp_customerreceivepayment.confirmedYN
                                                    ) = 1
                                                )
                                                AND (
                                                    (
                                                        erp_customerreceivepayment.approved
                                                    ) =- 1
                                                )
                                                AND (
                                                    erp_custreceivepaymentdet.addedDocumentID = "INV"
                                                )
                                            )
                                        GROUP BY
                                            bookingInvCodeSystem
                                    ) receipt ON receipt.bookingInvCodeSystem = erp_custinvoicedirect.custInvoiceDirectAutoID
                                    GROUP BY
                                        erp_custinvoicedirect.customerGRVAutoID
                                ) cgrv ON grvID.grvAutoID = cgrv.customerGRVAutoID
                            ) ful
                        GROUP BY
                            purchaseOrderMastertID
                    ) det ON (
                        det.purchaseOrderMastertID = erp_purchaseordermaster.purchaseOrderID
                    )
                    LEFT JOIN (
                        SELECT
                            purchaseOrderID,
                            bookingSuppMasInvAutoID,
                            SUM(totRptAmount) AS totRptAmount
                        FROM
                            erp_bookinvsuppdet
                        GROUP BY
                            erp_bookinvsuppdet.purchaseOrderID
                    ) bsi ON (
                        bsi.purchaseOrderID = erp_purchaseordermaster.purchaseOrderID
                    )
                    INNER JOIN supplierassigned ON erp_purchaseordermaster.supplierID = supplierassigned.supplierCodeSytem
                    AND erp_purchaseordermaster.companySystemID = supplierassigned.companySystemID
                    LEFT JOIN suppliermaster ON erp_purchaseordermaster.supplierID = suppliermaster.supplierCodeSystem
                    WHERE 
                        erp_purchaseordermaster.approved = - 1
                    AND supplierassigned.liabilityAccount = 9999963
                    ' . $where . '
                    GROUP BY
                        erp_purchaseordermaster.purchaseOrderID
                    ORDER BY erp_purchaseordermaster.companyID,erp_purchaseordermaster.purchaseOrderID ASC';
                break;

            case 'ICST':

                if (count($companyIDs)) {
                    $where .= 'AND erp_stocktransfer.companyFromSystemID IN (' . join(',', $companyIDs) . ')';
                }
                if (count($yearIDs)) {
                    $where .= 'AND YEAR(erp_stocktransfer.tranferDate) IN (' . join(',', $yearIDs) . ')';
                }
                if (count($monthsIDs)) {
                    $where .= 'AND MONTH(erp_stocktransfer.tranferDate) IN (' . join(',', $monthsIDs) . ')';
                }

                $query = "SELECT
                                erp_stocktransfer.stockTransferAutoID,
                                erp_stocktransfer.companyFrom,
                                erp_stocktransfer.companyTo,
                                erp_stocktransfer.stockTransferCode,
                                DATE( erp_stocktransfer.tranferDate ) AS tranferDate,
                                erp_custinvoicedirect.bookingInvCode,
                                DATE( erp_custinvoicedirect.bookingDate ) AS bookingDate,
                                erp_custinvoicedirect.bookingAmountRpt,
                                str.stockReceiveAutoID,
                                str.bookingSuppMasInvAutoID,
                                str.stockReceiveCode,
                                str.receivedDate,
                                str.Approved,
                                str.bookingInvCode AS supplierInvCode,
                                DATE( str.bookingDate ) AS supplierInvDate,
                                str.bookingAmountRpt AS supplierAmountRpt,
                                erp_custinvoicedirect.companyReportingCurrencyID,
                                erp_custinvoicedirectdet.glCode as invGLCode,
					            erp_custinvoicedirectdet.glCodeDes as invGLCodeDes,
					            invService.ServiceLineDes as invDepartment,
					            str.ServiceLineDes as strDepartment,
					            str.AccountCode as strGLCode,
					            str.AccountDescription as strGLCodeDes
                            FROM
                                ( erp_stocktransfer 
                                LEFT JOIN erp_custinvoicedirect ON erp_stocktransfer.stockTransferCode = erp_custinvoicedirect.customerInvoiceNo )
                                LEFT JOIN
                                erp_custinvoicedirectdet ON erp_custinvoicedirect.custInvoiceDirectAutoID = erp_custinvoicedirectdet.custInvoiceDirectID
                                LEFT JOIN
                                serviceline invService ON erp_custinvoicedirect.serviceLineSystemID = invService.serviceLineSystemID
                                LEFT JOIN (
                            SELECT
                                erp_stockreceive.stockReceiveAutoID,
                                erp_bookinvsuppmaster.bookingSuppMasInvAutoID,
                                erp_stockreceive.stockReceiveCode,
                                erp_stockreceive.receivedDate,
                            IF
                                ( erp_stockreceive.approved =- 1, 'Yes', 'No' ) AS Approved,
                                erp_bookinvsuppmaster.bookingInvCode,
                                erp_bookinvsuppmaster.bookingDate,
                                erp_stockreceive.refNo,
                                erp_bookinvsuppmaster.supplierInvoiceNo,
                                erp_bookinvsuppmaster.bookingAmountRpt,
                                erp_bookinvsuppmaster.companyReportingCurrencyID as companyReportingCurrencyID,
                                serviceline.ServiceLineDes,
                                chartofaccounts.AccountCode as AccountCode,
                                chartofaccounts.AccountDescription as AccountDescription
                            FROM
                                erp_stockreceive
                                LEFT JOIN 
                                erp_bookinvsuppmaster ON erp_stockreceive.refNo = erp_bookinvsuppmaster.supplierInvoiceNo
                                LEFT JOIN
                                erp_stockreceivedetails ON erp_stockreceive.stockReceiveAutoID = erp_stockreceivedetails.stockReceiveAutoID
                                LEFT JOIN
                                chartofaccounts ON erp_stockreceivedetails.financeGLcodebBSSystemID = chartofaccounts.chartOfAccountSystemID
                                LEFT JOIN
                                serviceline ON erp_stockreceive.serviceLineSystemID = serviceline.serviceLineSystemID 
                            WHERE
                                 erp_stockreceive.interCompanyTransferYN =- 1   
                                ) AS str ON erp_custinvoicedirect.bookingInvCode = str.refNo 
                            WHERE erp_stocktransfer.interCompanyTransferYN  =- 1  AND erp_stocktransfer.approved =- 1 " . $where . "
                            GROUP BY erp_stocktransfer.stockTransferAutoID,str.stockReceiveAutoID,str.bookingSuppMasInvAutoID
                            ORDER BY erp_stocktransfer.companyFrom,stockTransferCode ASC";
                break;

            case 'ICAT':

                if (count($companyIDs)) {
                    $where .= 'AND erp_fa_asset_disposalmaster.companySystemID IN (' . join(',', $companyIDs) . ')';
                }
                if (count($yearIDs)) {
                    $where .= 'AND YEAR(erp_fa_asset_disposalmaster.disposalDocumentDate) IN (' . join(',', $yearIDs) . ')';
                }
                if (count($monthsIDs)) {
                    $where .= 'AND MONTH(erp_fa_asset_disposalmaster.disposalDocumentDate) IN (' . join(',', $monthsIDs) . ')';
                }

                $query = "SELECT
                            erp_fa_asset_disposalmaster.assetdisposalMasterAutoID,
                            erp_fa_asset_disposalmaster.companyID AS Company,
                            erp_fa_asset_disposalmaster.toCompanyID AS toCompanyID,
                            erp_fa_asset_disposalmaster.disposalDocumentCode,
                            DATE(erp_fa_asset_disposalmaster.disposalDocumentDate) as disposalDocumentDate,
                            erp_custinvoicedirect.bookingInvCode,
                            DATE(erp_custinvoicedirect.bookingDate) as bookingDate,
                            erp_custinvoicedirect.bookingAmountRpt,
                            erp_custinvoicedirect.companyReportingCurrencyID,
                            erp_grvmaster.grvPrimaryCode,
                            DATE(erp_grvmaster.grvDate) AS grvDate,
                            erp_grvmaster.interCompanyTransferYN,
                            IF (
                                erp_grvmaster.approved =- 1,
                                'Yes',
                                'No'
                            ) AS Approved,
                            dtl.GrvAmount
                        FROM
                            erp_fa_asset_disposalmaster
                            INNER JOIN erp_custinvoicedirect
                            ON erp_fa_asset_disposalmaster.disposalDocumentCode = erp_custinvoicedirect.customerInvoiceNo
                            INNER JOIN erp_grvmaster
                            ON erp_custinvoicedirect.bookingInvCode = erp_grvmaster.grvDoRefNo
                            LEFT JOIN (
                            select
                                grvAutoID,
                                SUM(GRVcostPerUnitComRptCur*noQty) as GrvAmount
                            FROM
                                erp_grvdetails
                                GROUP BY grvAutoID
                        ) AS dtl ON erp_grvmaster.grvAutoID = dtl.grvAutoID
                        WHERE 
                            erp_fa_asset_disposalmaster.disposalType=1 AND erp_grvmaster.interCompanyTransferYN=-1 " . $where;
                break;

            case 'ICFT':
                $whereCompany2 = '';
                $whereDate = '';
                if (count($companyIDs)) {
                    $where .= 'AND erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyIDs) . ')';
                    $whereCompany2 .= 'AND erp_jvmaster.companySystemID IN (' . join(',', $companyIDs) . ')';
                }
                if (count($transferTypes)) {
                    $where .= 'AND erp_paysupplierinvoicemaster.expenseClaimOrPettyCash IN (' . join(',', $transferTypes) . ')';
                }
                if ($fromDate != null && $toDate != null) {
                    $where .= ' AND DATE(erp_paysupplierinvoicemaster.BPVdate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"';
                    $whereDate .= 'DATE(erp_jvmaster.JVdate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"';
                }

                $query = "SELECT
                                erp_paysupplierinvoicemaster.PayMasterAutoId,
                                erp_paysupplierinvoicemaster.companyID,
                                erp_paysupplierinvoicemaster.companySystemID as companySystemID,
                                erp_expenseclaimtype.expenseClaimTypeDescription,
                                erp_paysupplierinvoicemaster.BPVcode,
                                DATE(erp_paysupplierinvoicemaster.BPVdate) as BPVdate,
                                erp_paysupplierinvoicemaster.payAmountCompRpt,
                                erp_customerreceivepayment.companyID as companyIdTo,
                                erp_customerreceivepayment.custPaymentReceiveCode,
                                DATE(erp_customerreceivepayment.custPaymentReceiveDate) as custPaymentReceiveDate,
                                erp_customerreceivepayment.companyRptAmount,
                                IF (
                                    erp_customerreceivepayment.approved =- 1,
                                    'Yes',
                                    'No'
                                ) AS Approved,
                                erp_paysupplierinvoicemaster.companyRptCurrencyID as companyRptCurrencyID 
                            FROM
                                (
                                    erp_paysupplierinvoicemaster
                                    LEFT JOIN erp_expenseclaimtype ON erp_paysupplierinvoicemaster.expenseClaimOrPettyCash = erp_expenseclaimtype.expenseClaimTypeID
                                )
                            INNER JOIN erp_customerreceivepayment ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_customerreceivepayment.intercompanyPaymentID
                            WHERE
                            erp_paysupplierinvoicemaster.approved =- 1 " . $where . "
                            UNION
                            
                            SELECT
                                erp_jvmaster.jvMasterAutoId as PayMasterAutoId,
                                erp_jvmaster.companyID as companyID,
                                erp_jvmaster.companySystemID as companySystemID,
                                erp_jvmaster.JVNarration as expenseClaimTypeDescription,
                                erp_jvmaster.JVcode as BPVcode,
                                DATE(erp_jvmaster.JVdate) as BPVdate,
                                '0' AS payAmountCompRpt,
                                CONCAT(erp_jvdetail.glAccountDescription,' ',erp_jvdetail.glAccount) as companyIdTo,
                                '-' as custPaymentReceiveCode,
                                '-' as custPaymentReceiveDate,
                                if(Sum(erp_jvdetail.debitAmount) > 0,Sum(erp_jvdetail.debitAmount),Sum(erp_jvdetail.creditAmount)*-1) AS companyRptAmount,
                                IF (
                                    erp_jvmaster.approved =- 1,
                                    'Yes',
                                    'No'
                                ) AS Approved,
                                erp_jvmaster.rptCurrencyID as companyRptCurrencyID 
                            FROM ((companymaster
                                    INNER JOIN erp_jvmaster
                                    ON companymaster.companySystemID = erp_jvmaster.companySystemID)
                                    INNER JOIN erp_jvdetail
                                    ON erp_jvmaster.jvMasterAutoId = erp_jvdetail.jvMasterAutoId)
                                    INNER JOIN chartofaccounts
                                    ON erp_jvdetail.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
                            GROUP BY
                                erp_jvmaster.companySystemID,
                                erp_jvmaster.JVcode,
                                erp_jvmaster.JVNarration,
                                erp_jvmaster.JVdate,
                                erp_jvdetail.glAccount,
                                erp_jvdetail.glAccountDescription,
                                erp_jvmaster.confirmedByEmpID,
                                erp_jvmaster.confirmedByName,
                                companymaster.masterComapanyIDReporting,
                                erp_jvmaster.approved,
                                chartofaccounts.relatedPartyYN
                            
                            HAVING (" . $whereDate . ")
                                AND erp_jvmaster.approved = - 1
                                AND chartofaccounts.relatedPartyYN = 1 " . $whereCompany2;
                break;
            default:
                return '';
        }


        return  \DB::select($query);
    }

    public function getICReportDumpQuery($request)
    {
        $where = '';
        $companies = (array)$request->companies;
        $companyIDs = array_filter(collect($companies)->pluck('companySystemID')->toArray());
        if (count($companyIDs)) {
            $where .= 'AND erp_grvmaster.companySystemID IN (' . join(',', $companyIDs) . ')';
        }

        $years = (array)$request->years;
        $yearIDs = array_filter(collect($years)->pluck('year')->toArray());
        if (count($yearIDs)) {
            $where .= 'AND YEAR(grvDate) IN (' . join(',', $yearIDs) . ')';
        }

        $months = (array)$request->months;
        $monthsIDs = array_filter(collect($months)->pluck('monthID')->toArray());
        if (count($monthsIDs)) {
            $where .= 'AND MONTH(grvDate) IN (' . join(',', $monthsIDs) . ')';
        }

        $query = 'SELECT
                    det.grvDetailsID,
                    erp_grvmaster.companyID AS grvCompany,
                    erp_grvmaster.grvPrimaryCode AS grvCode,
                    det.GRVAmount AS GRVAmount,
                    DATE(erp_grvmaster.grvDate) AS grvDate,
                    erp_custinvoicedirect.companyID AS customer,
                    erp_custinvoicedirect.bookingInvCode,
                    DATE(erp_custinvoicedirect.bookingDate) AS BookingDate,
                    erp_custinvoicedirect.bookingAmountRpt,
					erp_custinvoicedirectdet.glCode as invGLCode,
					erp_custinvoicedirectdet.glCodeDes as invGLCodeDes,
					invService.ServiceLineDes as invDepartment,
					det.AccountCode as grvGLCode,
					det.AccountDescription as grvGLCodeDes,
					grvService.ServiceLineDes as grvDepartment
                FROM
                    erp_grvmaster
                    LEFT JOIN
                        (SELECT
                            grvAutoID,
                                grvDetailsID,
                                purchaseOrderMastertID,
                                companyID,
                                SUM((erp_grvdetails.netAmount / erp_grvdetails.companyReportingER)) AS GRVAmount,
                                chartofaccounts.AccountCode as AccountCode,
                                chartofaccounts.AccountDescription as AccountDescription
                        FROM
                            erp_grvdetails
                        LEFT JOIN
                        chartofaccounts ON erp_grvdetails.financeGLcodePLSystemID = chartofaccounts.chartOfAccountSystemID
                        GROUP BY erp_grvdetails.grvAutoID,erp_grvdetails.financeGLcodePLSystemID) det ON det.grvAutoID = erp_grvmaster.grvAutoID
                    LEFT JOIN
                        erp_custinvoicedirect ON erp_grvmaster.grvAutoID = erp_custinvoicedirect.customerGRVAutoID
                    LEFT JOIN
                        erp_custinvoicedirectdet ON erp_custinvoicedirect.custInvoiceDirectAutoID = erp_custinvoicedirectdet.custInvoiceDirectID 
					LEFT JOIN
                        serviceline invService ON erp_custinvoicedirectdet.serviceLineSystemID = invService.serviceLineSystemID
                    LEFT JOIN
                        serviceline grvService ON erp_grvmaster.serviceLineSystemID = grvService.serviceLineSystemID 
                    INNER JOIN
                        supplierassigned ON erp_grvmaster.supplierID = supplierassigned.supplierCodeSytem
                        AND erp_grvmaster.companySystemID = supplierassigned.companySystemID
                    WHERE  supplierassigned.liabilityAccount = 9999963 ' . $where . ' GROUP BY erp_grvmaster.grvAutoID,erp_custinvoicedirect.custInvoiceDirectAutoID,erp_custinvoicedirectdet.glCode
                    ORDER BY erp_grvmaster.grvAutoID DESC';

        return  \DB::select($query);
    }

    public function exportICReport(Request $request)
    {
        $reportTypeID = $request->reportTypeID;

        $output = $this->getICReportQuery($request);

        $requestCurrencyRpt = $this->getReportingCurrencyDetail($output, 'companyReportingCurrencyID');

        $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

        $type = $request->type;

        switch ($reportTypeID) {

            case 'ICR':

                $reportSD = $request->reportSD;


                if ($reportSD == "report") {

                    if (!empty($output)) {
                        $x = 0;

                        $subTotalPOAmount = 0;
                        $subTotalGRVAmount = 0;
                        $subTotalAPInvoiceAmount = 0;
                        $subTotalARInvoiceAmount = 0;
                        $subTotalAdjAmount = 0;

                        foreach ($output as $val) {
                            $x++;

                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['PO Code'] = $val->companyID;
                            $data[$x]['PO Date'] = Helper::dateFormat($val->PODate);
                            $data[$x]['Supplier Name'] = $val->supplierName;
                            $data[$x]['PO Amount'] = round($val->poTotalComRptCurrency, $decimalPlaceRpt);
                            $data[$x]['GRV Amount'] = round($val->GRVAmount, $decimalPlaceRpt);
                            $data[$x]['AP Invoice Amount'] = round($val->APAmount, $decimalPlaceRpt);
                            $data[$x]['AR Invoice Amount'] = round($val->bookingAmountRpt, $decimalPlaceRpt);
                            $data[$x]['Receipts/Adjustments'] = round($val->receiveAmountRpt, $decimalPlaceRpt);

                            $subTotalPOAmount += $val->poTotalComRptCurrency;
                            $subTotalGRVAmount += $val->GRVAmount;
                            $subTotalAPInvoiceAmount += $val->APAmount;
                            $subTotalARInvoiceAmount += $val->bookingAmountRpt;
                            $subTotalAdjAmount += $val->receiveAmountRpt;
                        }

                        $x++;
                        $data[$x]['Company ID'] = '';
                        $data[$x]['PO Code'] = '';
                        $data[$x]['PO Date'] = '';
                        $data[$x]['Supplier Name'] = 'Total';
                        $data[$x]['PO Amount'] = round($subTotalPOAmount, $decimalPlaceRpt);
                        $data[$x]['GRV Amount'] = round($subTotalGRVAmount, $decimalPlaceRpt);
                        $data[$x]['AP Invoice Amount'] = round($subTotalAPInvoiceAmount, $decimalPlaceRpt);
                        $data[$x]['AR Invoice Amount'] = round($subTotalARInvoiceAmount, $decimalPlaceRpt);
                        $data[$x]['Receipts/Adjustments'] = round($subTotalAdjAmount, $decimalPlaceRpt);
                    }
                } else { // DUMP

                    if ($reportSD == "dump") {

                        $output = $this->getICReportDumpQuery($request);

                        if (!empty($output)) {
                            $x = 0;

                            $subTotalGRVAmount = 0;
                            $subTotalBookingAmount = 0;

                            foreach ($output as $val) {
                                $x++;

                                $data[$x]['GRV Company'] = $val->grvCompany;
                                $data[$x]['GRV Code'] = $val->grvCode;
                                $data[$x]['GRV Amount'] = round($val->GRVAmount, $decimalPlaceRpt);
                                $data[$x]['GRV Date'] = Helper::dateFormat($val->grvDate);
                                $data[$x]['GL Code'] = $val->grvGLCode;
                                $data[$x]['GL Description'] = $val->grvGLCodeDes;
                                $data[$x]['Department'] = $val->grvDepartment;

                                $data[$x]['Customer'] = $val->customer;
                                $data[$x]['Booking Invoice Code'] = $val->bookingInvCode;
                                $data[$x]['Booking Date'] = Helper::dateFormat($val->BookingDate);
                                $data[$x]['Booking Amount Rpt'] = round($val->bookingAmountRpt, $decimalPlaceRpt);
                                $data[$x]['INV GL Code'] = $val->invGLCode;
                                $data[$x]['INV GL Description'] = $val->invGLCodeDes;
                                $data[$x]['INV Department'] = $val->invDepartment;

                                $subTotalGRVAmount += $val->GRVAmount;
                                $subTotalBookingAmount += $val->bookingAmountRpt;
                            }

                            $x++;

                            //                            $data[$x]['GRV Company'] = '';
                            //                            $data[$x]['GRV Code'] = '';
                            //                            $data[$x]['GRV Amount'] = round($subTotalGRVAmount,$decimalPlaceRpt);
                            //                            $data[$x]['GRV Date'] = '';
                            //                            $data[$x]['GL Code'] = '';
                            //                            $data[$x]['GL Description'] = '';
                            //                            $data[$x]['Department'] = '';
                            //                            $data[$x]['Customer'] = '';
                            //                            $data[$x]['Booking Invoice Code'] = '';
                            //                            $data[$x]['Booking Date'] = '';
                            //                            $data[$x]['Booking Amount Rpt'] = round($subTotalBookingAmount,$decimalPlaceRpt);
                            //                            $data[$x]['INV GL Code'] = '';
                            //                            $data[$x]['INV GL Description'] = '';
                            //                            $data[$x]['INV Department'] = '';
                        }
                    }
                }

                \Excel::create('inter_company', function ($excel) use ($data) {
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

            case 'ICST':

                if (!empty($output)) {
                    $x = 0;

                    $subTotalBookingAmountRpt = 0;
                    $subTotalSupplierAmountRpt = 0;

                    foreach ($output as $val) {
                        $x++;

                        $data[$x]['Company From'] = $val->companyFrom;
                        $data[$x]['Stock Transfer Code'] = $val->stockTransferCode;
                        $data[$x]['Stock Transfer Date'] = Helper::dateFormat($val->tranferDate);
                        $data[$x]['Customer INV Code'] = $val->bookingInvCode;
                        $data[$x]['Customer INV Date'] = Helper::dateFormat($val->bookingDate);
                        $data[$x]['Customer INV Amount'] = round($val->bookingAmountRpt, $decimalPlaceRpt);

                        $data[$x]['INV GL Code'] = $val->invGLCode;
                        $data[$x]['INV GL Description'] = $val->invGLCodeDes;
                        $data[$x]['INV Department'] = $val->invDepartment;

                        $data[$x]['Company To'] = $val->companyTo;
                        $data[$x]['Stock Receive Code'] = $val->stockReceiveCode;
                        $data[$x]['Approved'] = $val->Approved;
                        $data[$x]['Supplier Invoice Code'] = $val->supplierInvCode;
                        $data[$x]['Supplier Invoice Date'] = Helper::dateFormat($val->supplierInvDate);
                        $data[$x]['Supplier Invoice Amount'] = round($val->supplierAmountRpt, $decimalPlaceRpt);

                        $data[$x]['GL Code'] = $val->strGLCode;
                        $data[$x]['GL Description'] = $val->strGLCodeDes;
                        $data[$x]['Department'] = $val->strDepartment;

                        $subTotalBookingAmountRpt += $val->bookingAmountRpt;
                        $subTotalSupplierAmountRpt += $val->supplierAmountRpt;
                    }

                    $x++;
                    $data[$x]['Company From'] = '';
                    $data[$x]['Stock Transfer Code'] = '';
                    $data[$x]['Stock Transfer Date'] = '';
                    $data[$x]['Customer INV Code'] = '';
                    $data[$x]['Customer INV Date'] = 'Total';
                    $data[$x]['Customer INV Amount'] = round($subTotalBookingAmountRpt, $decimalPlaceRpt);
                    $data[$x]['INV GL Code'] = '';
                    $data[$x]['INV GL Description'] = '';
                    $data[$x]['INV Department'] = '';
                    $data[$x]['Company To'] = '';
                    $data[$x]['Stock Receive Code'] = '';
                    $data[$x]['Approved'] = '';
                    $data[$x]['Supplier Invoice Code'] = '';
                    $data[$x]['Supplier Invoice Date'] = '';
                    $data[$x]['Supplier Invoice Amount'] = round($subTotalSupplierAmountRpt, $decimalPlaceRpt);
                    $data[$x]['GL Code'] = '';
                    $data[$x]['GL Description'] = '';
                    $data[$x]['Department'] = '';
                }

                \Excel::create('inter_company_stock_transfer', function ($excel) use ($data) {
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

            case 'ICAT':

                if (!empty($output)) {
                    $x = 0;

                    $subTotalBookingAmountRpt = 0;
                    $subTotalGrvAmount = 0;

                    foreach ($output as $val) {
                        $x++;

                        $data[$x]['Company From'] = $val->Company;
                        $data[$x]['Disposal Document'] = $val->disposalDocumentCode;
                        $data[$x]['Disposal Date'] = Helper::dateFormat($val->disposalDocumentDate);
                        $data[$x]['Customer INV Code'] = $val->bookingInvCode;
                        $data[$x]['Customer INV Date'] = Helper::dateFormat($val->bookingDate);
                        $data[$x]['Customer INV Amount'] = round($val->bookingAmountRpt, $decimalPlaceRpt);
                        $data[$x]['Company To'] = $val->toCompanyID;
                        $data[$x]['Direct GRV Code'] = $val->grvPrimaryCode;
                        $data[$x]['GRV Date'] = Helper::dateFormat($val->grvDate);
                        $data[$x]['Direct GRV Approved'] = $val->Approved;
                        $data[$x]['GRV Amount'] = round($val->GrvAmount, $decimalPlaceRpt);

                        $subTotalBookingAmountRpt += $val->bookingAmountRpt;
                        $subTotalGrvAmount += $val->GrvAmount;
                    }

                    $x++;
                    $data[$x]['Company From'] = '';
                    $data[$x]['Disposal Document'] = '';
                    $data[$x]['Disposal Date'] = '';
                    $data[$x]['Customer INV Code'] = '';
                    $data[$x]['Customer INV Date'] = 'Total';
                    $data[$x]['Customer INV Amount'] = round($subTotalBookingAmountRpt, $decimalPlaceRpt);
                    $data[$x]['Company To'] = '';
                    $data[$x]['Direct GRV Code'] = '';
                    $data[$x]['GRV Date'] = '';
                    $data[$x]['Direct GRV Approved'] = '';
                    $data[$x]['GRV Amount'] = round($subTotalGrvAmount, $decimalPlaceRpt);
                }

                \Excel::create('inter_company_Asset_transfer', function ($excel) use ($data) {
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

            case 'ICFT':

                if (!empty($output)) {
                    $x = 0;

                    $subTotalPayAmountCompRpt = 0;
                    $subTotalCompanyRptAmount = 0;

                    foreach ($output as $val) {
                        $x++;

                        $data[$x]['Company From'] = $val->companyID;
                        $data[$x]['Transfer Type'] = $val->expenseClaimTypeDescription;
                        $data[$x]['PV Code'] = $val->BPVcode;
                        $data[$x]['PV Date'] = Helper::dateFormat($val->BPVdate);
                        $data[$x]['PV Amount'] = round($val->payAmountCompRpt, $decimalPlaceRpt);
                        $data[$x]['Company To'] = $val->companyIdTo;
                        $data[$x]['Receipt Code'] = $val->custPaymentReceiveCode;
                        $data[$x]['Receipt Date'] = Helper::dateFormat($val->custPaymentReceiveDate);
                        $data[$x]['Receipt Approved'] = $val->Approved;
                        $data[$x]['Receipt Amount'] = round($val->companyRptAmount, $decimalPlaceRpt);

                        $subTotalPayAmountCompRpt += $val->payAmountCompRpt;
                        $subTotalCompanyRptAmount += $val->companyRptAmount;
                    }

                    $x++;
                    $data[$x]['Company From'] = '';
                    $data[$x]['Transfer Type'] = '';
                    $data[$x]['PV Code'] = '';
                    $data[$x]['PV Date'] = 'Total';
                    $data[$x]['PV Amount'] = round($subTotalPayAmountCompRpt, $decimalPlaceRpt);
                    $data[$x]['Company To'] = '';
                    $data[$x]['Receipt Code'] = '';
                    $data[$x]['Receipt Date'] = '';
                    $data[$x]['Receipt Approved'] = '';
                    $data[$x]['Receipt Amount'] = round($subTotalCompanyRptAmount, $decimalPlaceRpt);
                }

                \Excel::create('inter_company_Fund_transfer', function ($excel) use ($data) {
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

    public function getICDrillDownData(Request $request)
    {

        $type = $request->type;
        $purchaseOrderID = $request->purchaseOrderID;
        $companySystemID = $request->companySystemID;

        switch ($type) {
            case 'grv':

                $query = "SELECT
                                det.grvDetailsID,
                                DATE(erp_grvmaster.grvDate) AS grvDate,
                                erp_grvmaster.grvPrimaryCode,
                                erp_grvmaster.companyReportingCurrencyID,
                                det.GRVAmount,
                             DATE(
                                    erp_custinvoicedirect.bookingDate
                                ) AS BookingDate,
                                erp_custinvoicedirect.bookingInvCode,
                                erp_custinvoicedirect.bookingAmountRpt
                            FROM
                                erp_grvmaster
                            LEFT JOIN (
                                SELECT
                                    grvAutoID,
                                    grvDetailsID,
                                    purchaseOrderMastertID,
                                    companySystemID,
                                    companyID,
                                    SUM(
                                        (
                                            erp_grvdetails.netAmount / erp_grvdetails.companyReportingER
                                        )
                                    ) AS GRVAmount
                                FROM
                                    erp_grvdetails
                                GROUP BY
                                    erp_grvdetails.grvAutoID
                            ) det ON det.grvAutoID = erp_grvmaster.grvAutoID
                            LEFT JOIN erp_custinvoicedirect ON erp_grvmaster.grvAutoID = erp_custinvoicedirect.customerGRVAutoID
                            WHERE
                                det.purchaseOrderMastertID = $purchaseOrderID
                            AND det.companySystemID = $companySystemID";

                $output = \DB::select($query);

                $requestCurrencyRpt = $this->getReportingCurrencyDetail($output, 'companyReportingCurrencyID');

                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;
                $currencyCodeRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->CurrencyCode : 'USD';

                $total = array();
                $total['GRVAmount'] = array_sum(collect($output)->pluck('GRVAmount')->toArray());
                $total['bookingAmountRpt'] = array_sum(collect($output)->pluck('bookingAmountRpt')->toArray());

                return array(
                    'reportData' => $output,
                    'total' => $total,
                    'decimalPlaceRpt' => $decimalPlaceRpt,
                    'currencyCodeRpt' => $currencyCodeRpt,
                );
                break;
            case 'ap':

                $output = BookInvSuppDet::where('companySystemID', $companySystemID)
                    ->where('purchaseOrderID', $purchaseOrderID)
                    ->groupBy('bookingSuppMasInvAutoID')
                    ->select('bookingSuppMasInvAutoID', 'companyReportingCurrencyID', DB::raw('SUM(erp_bookinvsuppdet.totRptAmount) AS totRptAmount'))
                    ->whereHas('suppinvmaster')
                    ->with(['suppinvmaster' => function ($query) {
                        $query->select('bookingDate', 'bookingInvCode', 'bookingSuppMasInvAutoID');
                    }])

                    ->get();

                $requestCurrencyRpt = $this->getReportingCurrencyDetail($output, 'companyReportingCurrencyID');
                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;
                $currencyCodeRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->CurrencyCode : 'USD';

                $total = array();
                $total['totRptAmount'] = array_sum(collect($output)->pluck('totRptAmount')->toArray());

                return array(
                    'reportData' => $output,
                    'total' => $total,
                    'decimalPlaceRpt' => $decimalPlaceRpt,
                    'currencyCodeRpt' => $currencyCodeRpt,
                );

                break;
            case 'receipt':

                $bookingInvCodeSystem = (isset($request->bookinkcode) && $request->bookinkcode > 0) ? $request->bookinkcode : 0;

                $query = "SELECT
                                erp_custreceivepaymentdet.custReceivePaymentAutoID,
                                erp_customerreceivepayment.custPaymentReceiveCode,
                                erp_custreceivepaymentdet.bookingInvCodeSystem,
                        
                            IF (
                                erp_custreceivepaymentdet.matchingDocID = 0
                                OR erp_custreceivepaymentdet.matchingDocID IS NULL,
                                erp_customerreceivepayment.custPaymentReceiveCode,
                                erp_matchdocumentmaster.matchingDocCode
                            ) AS docCode,
                        
                        DATE(IF (
                                erp_custreceivepaymentdet.matchingDocID = 0
                            OR erp_custreceivepaymentdet.matchingDocID IS NULL,
                            erp_customerreceivepayment.custPaymentReceiveDate,
                            erp_matchdocumentmaster.matchingDocdate
                        
                        )) AS docDate,
                            erp_custreceivepaymentdet.receiveAmountRpt,
                            erp_custreceivepaymentdet.companyReportingCurrencyID as companyReportingCurrencyID,
                            erp_creditnote.creditNoteCode,
                            erp_creditnote.comments
                        FROM
                            (
                                (
                                    erp_custreceivepaymentdet
                                    LEFT JOIN erp_customerreceivepayment ON erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
                                )
                                LEFT JOIN erp_matchdocumentmaster ON erp_custreceivepaymentdet.matchingDocID = erp_matchdocumentmaster.matchDocumentMasterAutoID
                            )
                        INNER JOIN currencymaster ON erp_custreceivepaymentdet.companyReportingCurrencyID = currencymaster.currencyID
                        LEFT JOIN erp_creditnote ON erp_creditnote.creditNoteCode = erp_matchdocumentmaster.BPVcode
                        WHERE bookingInvCodeSystem = $bookingInvCodeSystem
                        AND erp_customerreceivepayment.confirmedYN = 1
                        AND erp_customerreceivepayment.approved = -1
                        AND erp_custreceivepaymentdet.addedDocumentID = 'INV'";

                $output = \DB::select($query);

                $requestCurrencyRpt = $this->getReportingCurrencyDetail($output, 'companyReportingCurrencyID');

                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;
                $currencyCodeRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->CurrencyCode : 'USD';

                $total = array();
                $total['receiveAmountRpt'] = array_sum(collect($output)->pluck('receiveAmountRpt')->toArray());

                return array(
                    'reportData' => $output,
                    'total' => $total,
                    'decimalPlaceRpt' => $decimalPlaceRpt,
                    'currencyCodeRpt' => $currencyCodeRpt,
                );
                break;
            default:
                return $this->sendError('Drill down type not found');
        }
    }

    private function getReportingCurrencyDetail($result, $column)
    {
        $currencyIdRpt = 2;

        $decimalPlaceCollectRpt = collect($result)->pluck($column)->toArray();
        $decimalPlaceUniqueRpt = array_unique($decimalPlaceCollectRpt);

        if (!empty($decimalPlaceUniqueRpt)) {
            $currencyIdRpt = $decimalPlaceUniqueRpt[0];
        }

        return CurrencyMaster::where('currencyID', $currencyIdRpt)->first();
    }

    public function exportFinanceReport(Request $request)
    {
        $reportData = $this->generateFRReport($request);

        $input = $this->convertArrayToSelectedValue($request->all(), array('currency'));
        if ($reportData['template']['showDecimalPlaceYN']) {
            if ($input['currency'] === 1) {
                $reportData['decimalPlaces'] = $reportData['companyCurrency']['localcurrency']['DecimalPlaces'];
            } else {
                $reportData['decimalPlaces'] = $reportData['companyCurrency']['reportingcurrency']['DecimalPlaces'];
            }
        } else {
            $reportData['decimalPlaces'] = 0;
        }

        $reportData['accountType'] = $input['accountType'];

        if (is_array($reportData['uncategorize']) && $reportData['columnTemplateID'] == null) {
            $reportData['isUncategorize'] = false;
        } else {
            $reportData['isUncategorize'] = true;
        }

        if ($reportData['columnTemplateID'] == 1) {
            $templateName = "export_report.finance_coloumn_template_one";
        } else {
            $templateName = "export_report.finance";
        }

        return \Excel::create('finance', function ($excel) use ($reportData, $templateName) {
            $excel->sheet('New sheet', function ($sheet) use ($reportData, $templateName) {
                $sheet->loadView($templateName, $reportData);
            });
        })->download('xlsx');
    }
}
