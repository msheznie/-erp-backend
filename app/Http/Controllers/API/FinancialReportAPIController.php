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
use App\Models\CompanyFinanceYear;
use App\Models\Contract;
use App\Models\CurrencyMaster;
use App\Models\ReportTemplate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialReportAPIController extends AppBaseController
{
    public function getFRFilterData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $companiesByGroup = "";
        if (\Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

        $company = Company::whereIN('companySystemID',$companiesByGroup)->get();

        $companyFinanceYear = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear"));
        $companyFinanceYear = $companyFinanceYear->where('companySystemID', $companiesByGroup);
        if (isset($request['type']) && $request['type'] == 'add') {
            $companyFinanceYear = $companyFinanceYear->where('isActive', -1);
        }
        $companyFinanceYear = $companyFinanceYear->orderBy('bigginingDate', 'DESC')->get();

        $departments = \Helper::getCompanyServiceline($selectedCompanyId);
        //$departments[] = array("serviceLineSystemID" => 24, "ServiceLineCode" => 'X', "serviceLineMasterCode" => 'X', "ServiceLineDes" => 'X');

        $controlAccount = ChartOfAccountsAssigned::whereIN('companySystemID', $companiesByGroup)->get(['chartOfAccountSystemID',
            'AccountCode', 'AccountDescription', 'catogaryBLorPL']);

        $contracts = Contract::whereIN('companySystemID', $companiesByGroup)->get(['contractUID', 'ContractNumber', 'contractDescription']);

        $accountType = AccountsType::all();

        $templateType = ReportTemplate::all();

        $output = array(
            'companyFinanceYear' => $companyFinanceYear,
            'departments' => $departments,
            'controlAccount' => $controlAccount,
            'contracts' => $contracts,
            'accountType' => $accountType,
            'templateType' => $templateType,
            'segment' => $departments,
            'company' => $company,
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

        if(isset($input['isBS']) && $input['isBS'] == 'true'){
            array_push($inCategoryBLorPLID,1);
        }

        if(isset($input['isPL']) && $input['isPL'] == 'true'){
            array_push($inCategoryBLorPLID,2);
        }

        if(count($inCategoryBLorPLID) == 0){
            $inCategoryBLorPLID = [1,2];
        }
        
        $controlAccount = ChartOfAccountsAssigned::whereIN('companySystemID', $companiesByGroup)
                                                 ->whereIN('catogaryBLorPLID', $inCategoryBLorPLID)
                                                 ->get(['chartOfAccountSystemID','AccountCode', 'AccountDescription', 'catogaryBLorPL']);

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
            default:
                return $this->sendError('No report ID found');
        }
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

    public function getTrialBalanceDetails($request)
    {
        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        $chartOfAccountID = $request->chartOfAccountSystemID;
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
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
                                    DATE(erp_generalledger.documentDate) <= "' . $toDate . '"	
                                    AND erp_generalledger.chartOfAccountSystemID  = ' . $chartOfAccountID . '
                                    AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')  ORDER BY erp_generalledger.documentDate;';

        $output = \DB::select($query);
        //dd(DB::getQueryLog());
        return $output;

    }

    public function getTrialBalanceCompanyWise($request, $subCompanies)
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

    public function getTaxDetailQry($request)
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

    public function pdfExportReport(Request $request)
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
                foreach($output as $val){
                    $finalData[$val->glCode.' - '.$val->AccountDescription][] = $val;
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


}
