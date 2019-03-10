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

use App\Http\Controllers\AppBaseController;
use App\Models\AccountsType;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\Contract;
use App\Models\CurrencyMaster;
use App\Models\GeneralLedger;
use App\Models\ReportTemplate;
use App\Models\ReportTemplateCashBank;
use App\Models\ReportTemplateColumnLink;
use App\Models\ReportTemplateColumns;
use App\Models\ReportTemplateDetails;
use App\Models\ReportTemplateDocument;
use App\Models\ReportTemplateLinks;
use App\Models\ReportTemplateNumbers;
use App\Models\SegmentMaster;
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
        if (\Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

        $company = Company::whereIN('companySystemID', $companiesByGroup)->get();

        $companyFinanceYear = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear,bigginingDate,endingDate"));
        $companyFinanceYear = $companyFinanceYear->whereIn('companySystemID', $companiesByGroup);
        if (isset($request['type']) && $request['type'] == 'add') {
            $companyFinanceYear = $companyFinanceYear->where('isActive', -1);
        }
        $companyFinanceYear = $companyFinanceYear->groupBy('bigginingDate')->orderBy('bigginingDate', 'DESC')->get();

        $departments1 = collect(\Helper::getCompanyServiceline($selectedCompanyId));
        $departments2 = collect(SegmentMaster::where('serviceLineSystemID',24)->get());
        $departments = $departments1->merge($departments2)->all();

        $controlAccount = ChartOfAccountsAssigned::whereIN('companySystemID', $companiesByGroup)->get(['chartOfAccountSystemID',
            'AccountCode', 'AccountDescription', 'catogaryBLorPL']);

        $contracts = Contract::whereIN('companySystemID', $companiesByGroup)->get(['contractUID', 'ContractNumber', 'contractDescription']);

        $accountType = AccountsType::all();

        $templateType = ReportTemplate::all();

        $financePeriod = CompanyFinancePeriod::select(DB::raw("companyFinancePeriodID,isCurrent,CONCAT(DATE_FORMAT(dateFrom, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(dateTo, '%d/%m/%Y')) as financePeriod,companyFinanceYearID"));
        $financePeriod = $financePeriod->where('companySystemID', $selectedCompanyId);
        $financePeriod = $financePeriod->where('departmentSystemID', 5);
        if (isset($request['type']) && $request['type'] == 'add') {
            $financePeriod = $financePeriod->where('isActive', -1);
        }
        $financePeriod = $financePeriod->get();

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
            'companiesByGroup' => $companiesByGroup
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

                if ($validator->fails()) {//echo 'in';exit;
                    return $this->sendError($validator->messages(), 422);
                }
                break;

            case 'FGL':
                $validator = \Validator::make($request->all(), [
                    'reportTypeID' => 'required',
                    'fromDate' => 'required',
                    'toDate' => 'required|date|after_or_equal:fromDate',
                    'glCodes' => 'required',
                    'departments' => 'required',
                    'contracts' => 'required'
                ]);

                if ($validator->fails()) {//echo 'in';exit;
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            case 'FTD':
                $validator = \Validator::make($request->all(), [
                    'reportTypeID' => 'required',
                    'fromDate' => 'required',
                    'tempType' => 'required'
                ]);

                if ($validator->fails()) {//echo 'in';exit;
                    return $this->sendError($validator->messages(), 422);
                }
                if ($request->tempType == 0) {//echo 'in';exit;
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

                if ($validator->fails()) {//echo 'in';exit;
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    /*generate report according to each report id*/
    public function generateFRReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'FTB': // Trial Balance

                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($request->companySystemID);
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

                $total = array();
                $total['documentLocalAmountDebit'] = array_sum(collect($output)->pluck('documentLocalAmountDebit')->toArray());
                $total['documentLocalAmountCredit'] = array_sum(collect($output)->pluck('documentLocalAmountCredit')->toArray());
                $total['documentRptAmountDebit'] = array_sum(collect($output)->pluck('documentRptAmountDebit')->toArray());
                $total['documentRptAmountCredit'] = array_sum(collect($output)->pluck('documentRptAmountCredit')->toArray());
                return array('reportData' => $output,
                    'companyName' => $checkIsGroup->CompanyName,
                    'isGroup' => $checkIsGroup->isGroup,
                    'total' => $total,
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
                return array('reportData' => $output,
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
                return array('reportData' => $output,
                    'companyName' => $checkIsGroup->CompanyName,
                    'isGroup' => $checkIsGroup->isGroup,
                    'total' => $total,
                    'decimalPlaceLocal' => $decimalPlaceLocal,
                    'decimalPlaceRpt' => $decimalPlaceRpt,
                    'currencyLocal' => $requestCurrencyLocal->CurrencyCode,
                    'currencyRpt' => $requestCurrencyRpt->CurrencyCode,
                );
                break;
            case 'FTD': // Tax Detail

                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getTaxDetailQry($request);

                $total = array();

                return array('reportData' => $output,
                    'companyName' => $checkIsGroup->CompanyName,
                    'isGroup' => $checkIsGroup->isGroup,
                    'total' => $total,
                    'tempType' => $request->tempType
                );
                break;
            case 'FCT': // Finance Customize reports (Income statement, P&L, Cash flow)
                $request = (object)$request->all();

                if ($request->accountType == 1) {
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

                // get generated customize column qury
                $generatedColumn = $this->getFinancialCustomizeRptColumnQry($request);
                $linkedcolumnQry = $generatedColumn['linkedcolumnQry'];
                $columnKeys = $generatedColumn['columnKeys'];
                $currencyColumn = $generatedColumn['currencyColumn'];
                $columnHeader = $generatedColumn['columnHeader'];
                $columnHeaderMapping = $generatedColumn['columnHeaderMapping'];
                $linkedcolumnQry2 = $generatedColumn['linkedcolumnQry2'];

                $outputCollect = collect($this->getCustomizeFinancialRptQry($request, $linkedcolumnQry,$linkedcolumnQry2, $columnKeys, $financeYear, $period));
                $outputDetail = collect($this->getCustomizeFinancialDetailRptQry($request, $linkedcolumnQry, $columnKeys, $financeYear, $period));
                $headers = $outputCollect->where('masterID', null)->sortBy('sortOrder')->values();
                $grandTotalUncatArr = [];
                $uncategorizeArr = [];
                $uncategorizeDetailArr = [];
                $grandTotal = [];
                if ($request->accountType == 1 || $request->accountType == 2) {
                    $uncategorizeData = collect($this->getCustomizeFinancialUncategorizeQry($request, $linkedcolumnQry,$linkedcolumnQry2, $financeYear, $period, $columnKeys));
                    $grandTotal = collect($this->getCustomizeFinancialGrandTotalQry($request, $linkedcolumnQry,$linkedcolumnQry2, $financeYear, $period, $columnKeys));
                    //$lastColumn = collect($headers)->last(); // considering net total
                    foreach ($columnKeys as $key => $val) {
                        //$grandTotalUncatArr[$val] = $lastColumn->$val + $uncategorizeData['output'][0]->$val;
                        $uncategorizeArr[$val] = $uncategorizeData['output'][0]->$val;
                    }
                    $uncategorizeDetailArr = $uncategorizeData['outputDetail'];
                }

                $outputOpeningBalance = '';
                $outputOpeningBalanceArr = [];
                $outputClosingBalanceArr = [];
                if ($request->accountType == 3) {
                    $outputOpeningBalance = $this->getCashflowOpeningBalanceQry($request, $currencyColumn);
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

                $removedFromArray = [];
                if (count($headers) > 0) {
                    foreach ($headers as $key => $val) {
                        $details = $outputCollect->where('masterID', $val->detID)->sortBy('sortOrder')->values();
                        $val->detail = $details;
                        foreach ($details as $key2 => $val2) {
                            $val2->glCodes = $outputDetail->where('templateDetailID', $val2->detID)->sortBy('sortOrder')->values();
                        }
                        if($val->itemType != 3) {
                            if (count($details) == 0) {
                                $removedFromArray[] = $key;
                            }
                        }
                    }
                }

                //remove records which has no detail except total
                $headers = collect($headers)->forget($removedFromArray)->values();

                $divisionValue = 1;
                if ($template) {
                    if ($template->showNumbersIn !== 1) {
                        $numbers = ReportTemplateNumbers::find($template->showNumbersIn);
                        $divisionValue = (float)$numbers->value;
                    }
                }

                return array('reportData' => $headers,
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
                    'grandTotalUncatArr' => $grandTotal[0],
                    'numbers' => $divisionValue,
                    'month' => $month,
                );
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }


    public
    function exportReport(Request $request)
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

                $csv = \Excel::create('trial_balance', function ($excel) use ($data) {
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

                        if ($checkIsGroup->isGroup == 0) {
                            $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = round($val->localDebit, $decimalPlaceLocal);
                            $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = round($val->localCredit, $decimalPlaceLocal);
                        }

                        $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->rptDebit, $decimalPlaceRpt);
                        $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->rptCredit, $decimalPlaceRpt);
                        $x++;
                    }
                }

                $csv = \Excel::create('trial_balance_details', function ($excel) use ($data) {
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
                            $data[$x]['Document ID'] = 'Document ID';
                            $data[$x]['Document Number'] = 'Document Number';
                            $data[$x]['Date'] = 'Date';
                            $data[$x]['Document Narration'] = 'Document Narration';
                            $data[$x]['Service Line'] = 'Service Line';
                            $data[$x]['Contract'] = 'Contract';
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
                                    $data[$x]['Document ID'] = $val->documentID;
                                    $data[$x]['Document Number'] = $val->documentCode;
                                    $data[$x]['Date'] = \Helper::dateFormat($val->documentDate);
                                    $data[$x]['Document Narration'] = $val->documentNarration;
                                    $data[$x]['Service Line'] = $val->serviceLineCode;
                                    $data[$x]['Contract'] = $val->clientContractID;
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
                                $data[$x]['Document ID'] = '';
                                $data[$x]['Document Number'] = '';
                                $data[$x]['Date'] = '';
                                $data[$x]['Document Narration'] = '';
                                $data[$x]['Service Line'] = '';
                                $data[$x]['Contract'] = '';
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
                        $data[$x]['Document ID'] = '';
                        $data[$x]['Document Number'] = '';
                        $data[$x]['Date'] = '';
                        $data[$x]['Document Narration'] = '';
                        $data[$x]['Service Line'] = '';
                        $data[$x]['Contract'] = '';
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
                            $data[$x]['Document ID'] = $val->documentID;
                            $data[$x]['Document Number'] = $val->documentCode;
                            $data[$x]['Date'] = \Helper::dateFormat($val->documentDate);
                            $data[$x]['Document Narration'] = $val->documentNarration;
                            $data[$x]['Service Line'] = $val->serviceLineCode;
                            $data[$x]['Contract'] = $val->clientContractID;
                            $data[$x]['Supplier/Customer'] = $val->isCustomer;

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

                $csv = \Excel::create('general_ledger', function ($excel) use ($data) {
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

                $csv = \Excel::create('trial_balance_details', function ($excel) use ($data) {
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


    public
    function getTrialBalance($request)
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

    public
    function getTrialBalanceDetails($request)
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
        if($chartOfAccount){
            if($chartOfAccount->catogaryBLorPLID == 2){
                $dateQry = 'DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"';
            }else{
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
                                    If ( documentLocalAmount> 0,documentLocalAmount, 0 ) as localDebit,
                                    If ( documentLocalAmount> 0,0, documentLocalAmount*-1 ) as localCredit,
                                    If ( documentRptAmount> 0,documentRptAmount, 0 ) as rptDebit,
                                    If ( documentRptAmount> 0,0, documentRptAmount*-1 ) as rptCredit
                                FROM
                                    erp_generalledger 
                                INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                                WHERE
                                    '.$dateQry.'	
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


    public
    function getGeneralLedger($request)
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
                        erp_templatesglcode.templatesDetailsAutoID as templatesDetailsAutoID,
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
                        AND erp_generalledger.glAccountType = "BS" 
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
        $output = \DB::select($query);
        //dd(DB::getQueryLog());
        return $output;

    }

    public
    function getGeneralLedgerQryForPDF($request)
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
                        ( erp_generalledger.documentSystemID = 20 OR erp_generalledger.documentSystemID = 21 OR erp_generalledger.documentSystemID = 19, customermaster.CustomerName, suppliermaster.supplierName ) AS isCustomer
                    FROM
                        erp_generalledger
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
                        "" AS isCustomer
                    FROM
                        erp_generalledger
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

    function getCustomizeFinancialRptQry($request, $linkedcolumnQry,$linkedcolumnQry2, $columnKeys, $financeYear, $period)
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
            }
        }

        $firstLinkedcolumnQry = !empty($linkedcolumnQry) ? $linkedcolumnQry . ',' : '';
        $secondLinkedcolumnQry = '';
        //$thirdLinkedcolumnQry = '';
        $fourthLinkedcolumnQry = !empty($linkedcolumnQry2) ? $linkedcolumnQry2 . ',' : '';
        $fifthLinkedcolumnQry = '';
        $whereQry = [];
        foreach ($columnKeys as $val) {
            $secondLinkedcolumnQry .= '((IFNULL(IFNULL( c.`' . $val . '`, e.`' . $val . '`),0))/' . $divisionValue . ') AS `' . $val . '`,';
            //$thirdLinkedcolumnQry .= 'IFNULL(SUM(d.`' . $val . '`),0) AS `' . $val . '`,';
            //$fourthLinkedcolumnQry .= 'IFNULL(SUM(`' . $val . '`),0) AS `' . $val . '`,';
            $fifthLinkedcolumnQry .= 'IFNULL(IF(linkCatType != templateCatType,`' . $val . '` * -1,`' . $val . '`),0) AS `' . $val . '`,';
            $whereQry[] .= 'IF(masterID is not null , d.`' . $val . '` != 0,d.`' . $val . '` IS NOT NULL)';
        }

        $sql = 'SELECT * FROM (SELECT
	c.detDescription,
	c.detID,
	' . $secondLinkedcolumnQry . '
	c.sortOrder,
	c.masterID,
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
			)
	) f
GROUP BY
	templateDetailID
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
			)
	) g
GROUP BY
	templateDetailID
	) d ON d.templateDetailID = erp_companyreporttemplatelinks.subCategory 
WHERE
	erp_companyreporttemplatelinks.templateMasterID = ' . $request->templateType . ' 
	AND subCategory IS NOT NULL 
GROUP BY
	erp_companyreporttemplatelinks.templateDetailID 
	) e ON e.templateDetailID = c.detID) d WHERE (' . join(' OR ', $whereQry) . ')';
        $output = \DB::select($sql);
        return $output;
    }

    function getCustomizeFinancialDetailRptQry($request, $linkedcolumnQry, $columnKeys, $financeYear, $period)
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
        if ($request->dateType == 1) {
            $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '") OR (DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $lastYearEndDate . '"))';
        } else {
            if ($request->accountType == 2) {
                $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '") OR (DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $lastYearEndDate . '"))';
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
            $secondLinkedcolumnQry .= '((IFNULL(IF(erp_companyreporttemplatelinks.categoryType != erp_companyreporttemplatedetails.categoryType,gl.`' . $val . '`*-1,gl.`' . $val . '`),0))/' . $divisionValue . ') AS `' . $val . '`,';
            $whereQry[] .= 'a.`' . $val . '` != 0';
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
        GROUP BY erp_generalledger.chartOfAccountSystemID) AS gl ON erp_companyreporttemplatelinks.glAutoID = gl.chartOfAccountSystemID
WHERE
	erp_companyreporttemplatelinks.templateMasterID = ' . $request->templateType . ' AND erp_companyreporttemplatelinks.glAutoID IS NOT NULL
ORDER BY
	erp_companyreporttemplatelinks.sortOrder) a WHERE (' . join(' OR ', $whereQry) . ')';
        $output = \DB::select($sql);
        return $output;
    }

    function getCashflowOpeningBalanceQry($request, $currency)
    {
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $companyID = collect($request->companySystemID)->pluck('companySystemID')->toArray();
        //$serviceline = collect($request->serviceLineSystemID)->pluck('serviceLineSystemID')->toArray();
        $documents = ReportTemplateDocument::pluck('documentSystemID')->toArray();

        $glCodes = ReportTemplateLinks::where('templateMasterID', $request->templateType)->whereNotNull('glAutoID')->pluck('glAutoID')->toArray();

        $output = GeneralLedger::selectRaw('SUM(' . $currency . ') as openingBalance')->whereIN('companySystemID', $companyID)->whereIN('documentSystemID', $documents)->whereIN('chartOfAccountSystemID', $glCodes)->whereRaw('(DATE(erp_generalledger.documentDate) < "' . $fromDate . '")')->first();

        return $output;
    }

    function getCustomizeFinancialDetailTOTQry($request, $linkedcolumnQry, $financeYear, $period, $columnKeys)
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
        if ($request->dateType == 1) {
            $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '") OR (DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $lastYearEndDate . '"))';
        } else {
            if ($request->accountType == 2) {
                $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '") OR (DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $lastYearEndDate . '"))';
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
            }
        }
        $secondLinkedcolumnQry = '';
        $thirdLinkedcolumnQry = '';
        $whereQry = [];
        foreach ($columnKeys as $key => $val) {
            $secondLinkedcolumnQry .= 'IFNULL(SUM(`' . $key . '`),0) AS `' . $key . '`,';
            $thirdLinkedcolumnQry .= 'IFNULL(IF(linkCatType != templateCatType,`' . $key . '` * -1,`' . $key . '`),0) AS `' . $key . '`,';
            $whereQry[] .= 'b.`' . $key . '` != 0';
        }

        $firstLinkedcolumnQry = !empty($linkedcolumnQry) ? $linkedcolumnQry . ',' : '';

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
			)
	) f
GROUP BY
	templateDetailID) b WHERE (' . join(' OR ', $whereQry) . ')';
        $output = \DB::select($sql);
        return $output;
    }


    function getCustomizeFinancialUncategorizeQry($request, $linkedcolumnQry,$linkedcolumnQry2, $financeYear, $period, $columnKeys)
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
            }
        }

        $reportTemplateMaster = ReportTemplate::find($request->templateType);
        $uncategorizeGL = ChartOfAccount::where('catogaryBLorPL', $reportTemplateMaster->categoryBLorPL)->where('isActive', 1)->where('isApproved', 1)->whereNotExists(function ($query) use ($request) {
            $query->selectRaw('*')
                ->from('erp_companyreporttemplatelinks')
                ->where('templateMasterID', $request->templateType)
                ->whereRaw('chartofaccounts.chartOfAccountSystemID = erp_companyreporttemplatelinks.glAutoID');
        })->pluck('chartOfAccountSystemID')->toArray();

        $firstLinkedcolumnQry = !empty($linkedcolumnQry) ? $linkedcolumnQry . ',' : '';

        $secondLinkedcolumnQry = '';
        foreach ($columnKeys as $key => $val) {
            $secondLinkedcolumnQry .= 'IFNULL(`' . $val . '`,0) AS `' . $val . '`,';
        }

        $thirdLinkedcolumnQry = !empty($linkedcolumnQry2) ? $linkedcolumnQry2 . ',' : '';
        $output = [];
        $outputDetail = [];
        if(count($uncategorizeGL) > 0) {
            $sql = 'SELECT  ' . $thirdLinkedcolumnQry . ' chartOfAccountSystemID,glCode,glDescription FROM (SELECT
            ' . $firstLinkedcolumnQry . '
            erp_generalledger.chartOfAccountSystemID,
            chartofaccounts.AccountCode as glCode,
	        chartofaccounts.AccountDescription as glDescription 
        FROM
            erp_generalledger 
            INNER JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
        WHERE
            erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') AND
            erp_generalledger.chartOfAccountSystemID IN (' . join(',', $uncategorizeGL) . ')
            ' . $servicelineQry . ' ' . $dateFilter . ' ' . $documentQry . '
        GROUP BY
            erp_generalledger.chartOfAccountSystemID) a';
            $output = \DB::select($sql);

            $sql = 'SELECT  ' . $secondLinkedcolumnQry . ' chartOfAccountSystemID,glCode,glDescription FROM (SELECT
            ' . $firstLinkedcolumnQry . '
            erp_generalledger.chartOfAccountSystemID,
            chartofaccounts.AccountCode as glCode,
	        chartofaccounts.AccountDescription as glDescription 
        FROM
            erp_generalledger 
            INNER JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
        WHERE
            erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') AND
            erp_generalledger.chartOfAccountSystemID IN (' . join(',', $uncategorizeGL) . ')
            ' . $servicelineQry . ' ' . $dateFilter . ' ' . $documentQry . '
        GROUP BY
            erp_generalledger.chartOfAccountSystemID) a';
            $outputDetail = \DB::select($sql);
        }


        return ['output' => $output, 'outputDetail' => $outputDetail];
    }

    function getCustomizeFinancialGrandTotalQry($request, $linkedcolumnQry,$linkedcolumnQry2, $financeYear, $period, $columnKeys){

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
        if ($request->dateType == 1) {
            $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '") OR (DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $lastYearEndDate . '"))';
        } else {
            if ($request->accountType == 2) {
                $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '") OR (DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $lastYearEndDate . '"))';
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
            $thirdLinkedcolumnQry .= 'IFNULL(IF(linkCatType != templateCatType,`' . $val . '` * -1,`' . $val . '`),0) AS `' . $val . '`,';
            $whereQry[] .= 'b.`' . $val . '` != 0';
        }

        $firstLinkedcolumnQry = !empty($linkedcolumnQry) ? $linkedcolumnQry . ',' : '';

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
			)
	) f
GROUP BY
	templateDetailID) b WHERE (' . join(' OR ', $whereQry) . ') UNION SELECT  ' . $secondLinkedcolumnQry . ' FROM (SELECT
            ' . $firstLinkedcolumnQry . '
            erp_generalledger.chartOfAccountSystemID
        FROM
            erp_generalledger 
            INNER JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
        WHERE
            erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') AND
            erp_generalledger.chartOfAccountSystemID IN (' . join(',', $uncategorizeGL) . ')
            ' . $servicelineQry . ' ' . $dateFilter . ' ' . $documentQry . '
        GROUP BY
            erp_generalledger.chartOfAccountSystemID) a) b';
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
    public function columnFormulaDecode($columnLinkID, $rowValues, $columnArray, $linkedRowHead = false,$type)
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
        $sepFormulaArr = $this->decodeColumnFormula($linkedColumns, $linkedRows, $rowValues, $columnArray,$type);
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
    public function decodeColumnFormula($linkedColumns, $linkedRows, $rowValues, $columnArray,$type)
    {
        global $globalFormula;
        $taxFormula = ReportTemplateColumnLink::whereIn('columnLinkID', explode(',', $linkedColumns))->get();
        if ($taxFormula) {
            foreach ($taxFormula as $val) {
                $searchVal = '#' . $val['columnLinkID'];
                if (!empty($val['formulaColumnID'])) {
                    $replaceVal = '|(~' . $val['formula'] . '~|)';
                    $globalFormula = str_replace($searchVal, $replaceVal, $globalFormula);
                    $return = $this->decodeColumnFormula($val['formulaColumnID'], $val['formulaRowID'], $rowValues, $columnArray,$type);
                    if (is_array($return)) {
                        if ($return[0] == 'e') {
                            return $return;
                            break;
                        }
                    }
                } else {
                    $replaceVal = '';
                    if($type == 1){
                        $replaceVal = '#' . $columnArray[$val['shortCode']];
                    }else{
                        $replaceVal = '#IFNULL(SUM(`'.$val->shortCode . '-' . $val->columnLinkID.'`),0)';
                    }

                    $globalFormula = str_replace_first($searchVal, $replaceVal, $globalFormula);
                    /*$replaceVal = '/'.$columnArray[$val['shortCode']].'/';
                    $globalFormula = preg_replace($searchVal, $replaceVal, $globalFormula,1);*/
                }
            }
        }

        if ($linkedRows) {
            $explodedLinkedColumns = explode(',', $linkedColumns);
            $linkedColumnsShortCode = ReportTemplateColumnLink::whereIN('columnLinkID', $explodedLinkedColumns)->get();
            foreach($linkedColumnsShortCode as $column){
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
                        }else{
                            $replaceVal = '$0';
                        }
                        $globalFormula = str_replace($searchVal, $replaceVal, $globalFormula);
                    }
                }
            }
        }
        return $globalFormula;
    }

    public function reportTemplateGLDrillDown(Request $request){
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');

        $financeYear = CompanyFinanceYear::find($request->companyFinanceYearID);
        $period = CompanyFinancePeriod::find($request->month);

        // get generated customized column
        $generatedColumn = $this->getFinancialCustomizeRptColumnQry($request);
        $linkedcolumnQry = $generatedColumn['linkedcolumnQry'];

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
            }
        }

        $sql = 'SELECT `' . $input['selectedColumn'] . '`,glCode,AccountDescription,documentCode,documentDate,ServiceLineDes,partyName,documentNarration,clientContractID FROM (SELECT
						' . $firstLinkedcolumnQry . ' 
						glCode,AccountDescription,documentCode,documentDate,serviceline.ServiceLineDes,
						erp_generalledger.documentNarration,
						erp_generalledger.clientContractID,
						IF
                        ( erp_generalledger.documentSystemID = 20 OR erp_generalledger.documentSystemID = 21 OR erp_generalledger.documentSystemID = 19, customermaster.CustomerName, suppliermaster.supplierName ) AS partyName 
					FROM
						erp_generalledger
					INNER JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
					LEFT JOIN serviceline ON serviceline.serviceLineSystemID = erp_generalledger.serviceLineSystemID
					LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                    LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem 
					WHERE
					    erp_generalledger.chartOfAccountSystemID = '.$input['glAutoID'].' AND
						erp_generalledger.companySystemID IN (
							' . join(',
							', $companyID) . '
						) ' . $servicelineQry . ' ' . $dateFilter . ' ' . $documentQry.' GROUP BY GeneralLedgerID) a';

        $output = DB::select($sql);

        $total = collect($output)->pluck($input['selectedColumn'])->toArray();
        $total = array_sum($total);

        $request->request->remove('search.value');

        return \DataTables::of($output)
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->with('total', $total)
            ->make(true);
    }


    function getFinancialCustomizeRptColumnQry($request){

        $toDate = '';
        $fromDate = '';
        $month = '';
        $period = '';
        $currencyColumn = '';
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

        //formula column decode
        if ($request->currency == 1) {
            $currencyColumn = 'documentLocalAmount';
        } else {
            $currencyColumn = 'documentRptAmount';
        }

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
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') > '" . $fromDate . "' AND DATE_FORMAT(documentDate,'%Y-%m-%d') < '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
	$currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && chartofaccounts.controlAccounts = 'BSL',$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    } else if ($request->accountType == 1) {
                        if ($request->dateType == 2) {
                            $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
                        }
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
	$currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && chartofaccounts.controlAccounts = 'BSL',$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    } else if ($request->accountType == 3) {
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') > '" . $fromDate . "' AND DATE_FORMAT(documentDate,'%Y-%m-%d') < '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
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
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') > '" . $fromDate . "' AND DATE_FORMAT(documentDate,'%Y-%m-%d') < '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
	$currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && chartofaccounts.controlAccounts = 'BSL',$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    } else if ($request->accountType == 1) {
                        if ($request->dateType == 2) {
                            $toDate = Carbon::parse($financeYear->endingDate)->subYear()->format('Y-m-d');
                        }
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
	$currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && chartofaccounts.controlAccounts = 'BSL',$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    } else if ($request->accountType == 3) {
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') > '" . $fromDate . "' AND DATE_FORMAT(documentDate,'%Y-%m-%d') < '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
	$currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && chartofaccounts.controlAccounts = 'BSL',$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    }
                    $columnHeaderArray[$val->shortCode] = $val->shortCode . '-' . $LYear;;
                }
            }
        }

        if (count($linkedColumn) > 0) {
            foreach ($linkedColumn as $val) {
                if ($val->shortCode == 'FCA' || $val->shortCode == 'FCP') {
                    $linkedcolumnArray2[$val->shortCode . '-' . $val->columnLinkID] = $this->columnFormulaDecode($val->columnLinkID, [], $columnArray, false,1);
                } else if ($val->shortCode == 'CYYTD' || $val->shortCode == 'LYYTD') {
                    $linkedcolumnArray2[$val->shortCode . '-' . $val->columnLinkID] = $columnArray[$val->shortCode];
                } else {
                    $linkedcolumnArray2[$val->shortCode . '-' . $val->columnLinkID] = $columnArray[$val->shortCode];
                }
            }
        }

        if (count($linkedcolumnArray2)) {
            foreach ($linkedcolumnArray2 as $key => $val) {
                if ($key == 'FCA' || $key == 'FCP') {
                    $linkedcolumnArrayFinal2[$key] = '(' . $val . ') as ' . '`' . $key . '`';
                } else {
                    $linkedcolumnArrayFinal2[$key] = $val . ' as ' . '`' . $key . '`';
                }
            }
        }

        $linkedcolumnQry2 = implode(',', $linkedcolumnArrayFinal2);
        $detTotCollect = collect($this->getCustomizeFinancialDetailTOTQry($request, $linkedcolumnQry2, $financeYear, $period, $linkedcolumnArray2));

        if (count($linkedColumn) > 0) {
            foreach ($linkedColumn as $val) {
                if ($val->shortCode == 'FCA' || $val->shortCode == 'FCP') {
                    $linkedcolumnArray[$val->shortCode . '-' . $val->columnLinkID] = $this->columnFormulaDecode($val->columnLinkID, $detTotCollect, $columnArray, true,1);
                    $columnHeader[] = ['description' => $val->description, 'bgColor' => $val->bgColor, $val->shortCode . '-' . $val->columnLinkID => $val->description, 'width' => $val->width];
                    $columnHeaderMapping[$val->shortCode . '-' . $val->columnLinkID] = $val->description;
                    $linkedcolumnArray3[$val->shortCode . '-' . $val->columnLinkID] = $this->columnFormulaDecode($val->columnLinkID, $detTotCollect, $columnArray, true,2);
                } else if ($val->shortCode == 'CYYTD' || $val->shortCode == 'LYYTD') {
                    $linkedcolumnArray[$val->shortCode . '-' . $val->columnLinkID] = $columnArray[$val->shortCode];
                    $columnHeader[] = ['description' => $columnHeaderArray[$val->shortCode], 'bgColor' => $val->bgColor,$val->shortCode . '-' . $val->columnLinkID => $columnHeaderArray[$val->shortCode],'width' => $val->width];
                    $columnHeaderMapping[$val->shortCode . '-' . $val->columnLinkID] = $columnHeaderArray[$val->shortCode];
                    $linkedcolumnArray3[$val->shortCode . '-' . $val->columnLinkID] = 'IFNULL(SUM(`'.$val->shortCode . '-' . $val->columnLinkID.'`),0)';
                } else {
                    $linkedcolumnArray[$val->shortCode . '-' . $val->columnLinkID] = $columnArray[$val->shortCode];
                    $columnHeader[] = ['description' => Carbon::parse($columnHeaderArray[$val->shortCode])->format('Y-M'), 'bgColor' => $val->bgColor, $val->shortCode . '-' . $val->columnLinkID => Carbon::parse($columnHeaderArray[$val->shortCode])->format('Y-M'), 'width' => $val->width];
                    $columnHeaderMapping[$val->shortCode . '-' . $val->columnLinkID] = Carbon::parse($columnHeaderArray[$val->shortCode])->format('Y-M');
                    $linkedcolumnArray3[$val->shortCode . '-' . $val->columnLinkID] = 'IFNULL(SUM(`'.$val->shortCode . '-' . $val->columnLinkID.'`),0)';
                }
            }
        }

        $columnKeys = collect($linkedcolumnArray)->keys()->all();

        if (count($linkedcolumnArray)) {
            foreach ($linkedcolumnArray as $key => $val) {
                if ($key == 'FCA' || $key == 'FCP') {
                    $linkedcolumnArrayFinal[$key] = '(' . $val . ') as ' . '`' . $key . '`';
                } else {
                    $linkedcolumnArrayFinal[$key] = $val . ' as ' . '`' . $key . '`';
                }
            }
        }

        if (count($linkedcolumnArray3)) {
            foreach ($linkedcolumnArray3 as $key => $val) {
                if ($key == 'FCA' || $key == 'FCP') {
                    $linkedcolumnArrayFinal3[$key] = '(' . $val . ') as ' . '`' . $key . '`';
                } else {
                    $linkedcolumnArrayFinal3[$key] = $val . ' as ' . '`' . $key . '`';
                }
            }
        }

        $linkedcolumnQry = implode(',', $linkedcolumnArrayFinal);
        $linkedcolumnQry2 = implode(',', $linkedcolumnArrayFinal3);

        return [
            'linkedcolumnQry' => $linkedcolumnQry,
            'linkedcolumnQry2' => $linkedcolumnQry2,
            'columnArray' => $columnArray,
            'columnKeys' => $columnKeys,
            'columnHeader' => $columnHeader,
            'columnHeaderMapping' => $columnHeaderMapping,
            'currencyColumn' => $currencyColumn];
    }

}
