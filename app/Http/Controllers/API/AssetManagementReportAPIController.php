<?php
/**
 * =============================================
 * -- File Name : AssetManagementReportAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report
 * -- Author : Mubashir
 * -- Create date : 12 - July 2018
 * -- Description : This file contains the all the report generation code
 * -- REVISION HISTORY
 * -- Date: 12-july 2018 By: Mubashir Description: Added new functions named as getFilterData(),validateReport(),generateReport(),exportReport()
 */

namespace App\Http\Controllers\API;

use App\Models\AssetFinanceCategory;
use App\Models\Company;
use App\Models\Months;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class AssetManagementReportAPIController extends AppBaseController
{
    public function getFilterData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $companiesByGroup = "";
        if (\Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

        $assetCategory = AssetFinanceCategory::all();

        $years = Year::all();
        $months = Months::all();

        $output = array(
            'assetCategory' => $assetCategory,
            'years' => $years,
            'months' => $months,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function validateReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'AMAR':
                $validator = \Validator::make($request->all(), [
                    'reportTypeID' => 'required',
                    'fromDate' => 'required',
                    'assetCategory' => 'required',
                    'currencyID' => 'required'
                ]);

                if ($validator->fails()) {//echo 'in';exit;
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            case 'AMADR':
                $validator = \Validator::make($request->all(), [
                    'reportTypeID' => 'required',
                    'year' => 'required',
                    'month' => 'required',
                    'currencyID' => 'required'
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
    public function generateReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'AMAR': //Asset Register

                break;
            case 'AMADR': //Asset Depreciation Register
                $reportTypeID = $request->reportTypeID;
                if ($reportTypeID == 'ADRM') { //Asset Depreciation Register Monthly
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->assetDepreciationRegisterMonthlyQRY($request);

                    $grandTotalArr = array();
                    if ($output['month']) {
                        foreach ($output['month'] as $val) {
                            $total = collect($output['data'])->pluck($val)->toArray();
                            $grandTotalArr[$val] = array_sum($total);
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

                    return array('reportData' => $output['data'], 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces, 'month' => $output['month']);
                }else if($reportTypeID == 'ADDM'){ //Asset Depreciation Detail Monthly
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->assetDepreciationDetailMonthlyQRY($request);

                    $arrayMonth = array( 'Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

                    $grandTotalArr = array();
                    $currentMonthDepreciation = collect($output)->pluck('currentMonthDepreciation')->toArray();
                    $grandTotalArr['currentMonthDepreciation'] = array_sum($currentMonthDepreciation);

                    $cost = collect($output)->pluck('cost')->toArray();
                    $grandTotalArr['cost'] = array_sum($cost);

                    $accumulatedDepreciation = collect($output)->pluck('accumulatedDepreciation')->toArray();
                    $grandTotalArr['accumulatedDepreciation'] = array_sum($accumulatedDepreciation);

                    $netBookValue = collect($output)->pluck('netBookValue')->toArray();
                    $grandTotalArr['netBookValue'] = array_sum($netBookValue);

                    $currentYearDepAmount = collect($output)->pluck('currentYearDepAmount')->toArray();
                    $grandTotalArr['currentYearDepAmount'] = array_sum($currentYearDepAmount);

                    $decimalPlaces = 2;
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($companyCurrency) {
                        if ($request->currencyID == 2) {
                            $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                        } else if ($request->currencyID == 3) {
                            $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                        }
                    }

                    return array('reportData' => $output, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces,'month' => $arrayMonth);
                }
                else if($reportTypeID == 'ADDS'){ //Asset Depreciation Detail Summary
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->assetDepreciationDetailSummaryQRY($request);

                    $arrayMonth = array( 'Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

                    $grandTotalArr = array();
                    $currentMonthDepreciation = collect($output)->pluck('currentMonthDepreciation')->toArray();
                    $grandTotalArr['currentMonthDepreciation'] = array_sum($currentMonthDepreciation);

                    $cost = collect($output)->pluck('cost')->toArray();
                    $grandTotalArr['cost'] = array_sum($cost);

                    $accumulatedDepreciation = collect($output)->pluck('accumulatedDepreciation')->toArray();
                    $grandTotalArr['accumulatedDepreciation'] = array_sum($accumulatedDepreciation);

                    $netBookValue = collect($output)->pluck('netBookValue')->toArray();
                    $grandTotalArr['netBookValue'] = array_sum($netBookValue);

                    $currentYearDepAmount = collect($output)->pluck('currentYearDepAmount')->toArray();
                    $grandTotalArr['currentYearDepAmount'] = array_sum($currentYearDepAmount);

                    if ($arrayMonth) {
                        foreach ($arrayMonth as $val) {
                            $total = collect($output)->pluck($val)->toArray();
                            $grandTotalArr[$val] = array_sum($total);
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

                    return array('reportData' => $output, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces,'month' => $arrayMonth);
                }else if($reportTypeID == 'ADCS'){ //Asset Depreciation Category Summary
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->assetDepreciationCategorySummaryQRY($request);

                    $arrayMonth = array( 'Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

                    $grandTotalArr = array();
                    $currentMonthDepreciation = collect($output)->pluck('currentMonthDepreciation')->toArray();
                    $grandTotalArr['currentMonthDepreciation'] = array_sum($currentMonthDepreciation);

                    $cost = collect($output)->pluck('cost')->toArray();
                    $grandTotalArr['cost'] = array_sum($cost);

                    $accumulatedDepreciation = collect($output)->pluck('accumulatedDepreciation')->toArray();
                    $grandTotalArr['accumulatedDepreciation'] = array_sum($accumulatedDepreciation);

                    $netBookValue = collect($output)->pluck('netBookValue')->toArray();
                    $grandTotalArr['netBookValue'] = array_sum($netBookValue);

                    $currentYearDepAmount = collect($output)->pluck('currentYearDepAmount')->toArray();
                    $grandTotalArr['currentYearDepAmount'] = array_sum($currentYearDepAmount);

                    $decimalPlaces = 2;
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($companyCurrency) {
                        if ($request->currencyID == 2) {
                            $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                        } else if ($request->currencyID == 3) {
                            $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                        }
                    }

                    return array('reportData' => $output, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces,'month' => $arrayMonth);
                }else if($reportTypeID == 'ADCSM'){ //Asset Depreciation Category Summary Monthly
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->assetDepreciationCategorySummaryMonthlyQRY($request);

                    $arrayMonth = array( 'Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

                    $grandTotalArr = array();
                    $currentMonthDepreciation = collect($output)->pluck('currentMonthDepreciation')->toArray();
                    $grandTotalArr['currentMonthDepreciation'] = array_sum($currentMonthDepreciation);

                    $cost = collect($output)->pluck('cost')->toArray();
                    $grandTotalArr['cost'] = array_sum($cost);

                    $accumulatedDepreciation = collect($output)->pluck('accumulatedDepreciation')->toArray();
                    $grandTotalArr['accumulatedDepreciation'] = array_sum($accumulatedDepreciation);

                    $netBookValue = collect($output)->pluck('netBookValue')->toArray();
                    $grandTotalArr['netBookValue'] = array_sum($netBookValue);

                    $currentYearDepAmount = collect($output)->pluck('currentYearDepAmount')->toArray();
                    $grandTotalArr['currentYearDepAmount'] = array_sum($currentYearDepAmount);

                    if ($arrayMonth) {
                        foreach ($arrayMonth as $val) {
                            $total = collect($output)->pluck($val)->toArray();
                            $grandTotalArr[$val] = array_sum($total);
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

                    return array('reportData' => $output, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces,'month' => $arrayMonth);
                }
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    /*export report to csv according to each report id*/
    public function exportReport(Request $request)
    {
        $reportID = $request->reportID;
        $type = $request->type;
        switch ($reportID) {
            case 'AMAR': //Asset Register

                break;
            case 'AMADR': //Asset Depreciation Register
                $reportTypeID = $request->reportTypeID;
                if ($reportTypeID == 'ADRM') { //Asset Depreciation Register Monthly
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $output = $this->assetDepreciationRegisterMonthlyQRY($request);
                    if ($output['data']) {
                        $x = 0;
                        foreach ($output['data'] as $val) {
                            $data[$x]['Asset Code'] = $val->faCode;
                            $data[$x]['Asset Description'] = $val->assetDescription;
                            $data[$x]['Category'] = $val->Category;
                            foreach ($output['month'] as $val2) {
                                $data[$x][$val2] = $val->$val2;
                            }
                            $x++;
                        }
                    }
                    $csv = \Excel::create('asset_depreciation_register_month', function ($excel) use ($data) {
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
                else if ($reportTypeID == 'ADDM') { //Asset Depreciation Detail Monthly
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $output = $this->assetDepreciationDetailMonthlyQRY($request);
                    $arrayMonth = array( 'Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');
                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Asset Code'] = $val->faCode;
                            $data[$x]['Asset Description'] = $val->assetDescription;
                            $data[$x]['Category'] = $val->Category;
                            $data[$x]['Cost Amount'] = $val->cost;
                            $data[$x]['Dep %'] = $val->DEPpercentage;
                            $data[$x]['Dep Amount '.$arrayMonth[$request->month-1]] = $val->currentMonthDepreciation;
                            $data[$x]['Opeining Dep'] = 0;
                            $data[$x]['Current Year Dep'] = $val->currentYearDepAmount;
                            $data[$x]['Accumilated Dep '.$arrayMonth[$request->month-1]] = $val->accumulatedDepreciation;
                            $data[$x]['Net Book Value '.$arrayMonth[$request->month-1]] = $val->netBookValue;

                            $x++;
                        }
                    }
                    $csv = \Excel::create('asset_depreciation_detail_month', function ($excel) use ($data) {
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
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    public function assetDepreciationRegisterMonthlyQRY($request){

        $year = $request->year;
        $month = sprintf("%02d", $request->month);

        $firstDayOfMonth = new Carbon($year.'-'.$month.'-01');
        $lastDayOfMonth = $firstDayOfMonth->endOfMonth();
        $lastDayOfMonth = $lastDayOfMonth->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $currency = $request->currencyID;

        $arrayMonth = array( 'Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

        $monthField = '';
        if ($currency == 2) {
            if (!empty($arrayMonth)) { /* month wise in query*/
                foreach ($arrayMonth as $key => $val) {
                    $monthField .= "if(MONTH(erp_fa_depmaster.depDate) = ".($key+1). ",round(erp_fa_assetdepreciationperiods.depAmountLocal, 2),0) as `" . $val . "`,";
                }
            }
        } else {
            if (!empty($arrayMonth)) { /* month wise in query*/
                foreach ($arrayMonth as $key => $val) {
                    $monthField .= "if(MONTH(erp_fa_depmaster.depDate) = ".($key+1). ",round(erp_fa_assetdepreciationperiods.depAmountRpt, 2),0) as `" . $val . "`,";
                }
            }
        }

        $sql= 'SELECT
	erp_fa_asset_master.companySystemID,
	erp_fa_asset_master.companyID,
	erp_fa_asset_master.faID,
	erp_fa_asset_master.faCode,
	erp_fa_asset_master.assetDescription,
	erp_fa_financecategory.financeCatDescription AS AuditCategory,
	erp_fa_category.catDescription Category,
	sum( ( IF ( assetDepreciation.Jan IS NULL, 0, assetDepreciation.Jan ) ) ) AS Jan,
	sum( ( IF ( assetDepreciation.Feb IS NULL, 0, assetDepreciation.Feb ) ) ) AS Feb,
	sum( ( IF ( assetDepreciation.March IS NULL, 0, assetDepreciation.March ) ) ) AS March,
	sum( ( IF ( assetDepreciation.April IS NULL, 0, assetDepreciation.April ) ) ) AS April,
	sum( ( IF ( assetDepreciation.May IS NULL, 0, assetDepreciation.May ) ) ) AS May,
	sum( ( IF ( assetDepreciation.June IS NULL, 0, assetDepreciation.June ) ) ) AS June,
	sum( ( IF ( assetDepreciation.July IS NULL, 0, assetDepreciation.July ) ) ) AS July,
	sum( ( IF ( assetDepreciation.Aug IS NULL, 0, assetDepreciation.Aug ) ) ) AS Aug,
	sum( ( IF ( assetDepreciation.Sept IS NULL, 0, assetDepreciation.Sept ) ) ) AS Sept,
	sum( ( IF ( assetDepreciation.Oct IS NULL, 0, assetDepreciation.Oct ) ) ) AS Oct,
	sum( ( IF ( assetDepreciation.Nov IS NULL, 0, assetDepreciation.Nov ) ) ) AS Nov,
	sum( ( IF ( assetDepreciation.Dece IS NULL, 0, assetDepreciation.Dece ) ) ) AS Dece
FROM
	erp_fa_asset_master
	LEFT JOIN erp_fa_financecategory ON erp_fa_asset_master.AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID
	LEFT JOIN erp_fa_category ON erp_fa_asset_master.faCatID = erp_fa_category.faCatID
	INNER JOIN (-- assetDepreciation
SELECT
    '.$monthField.'
	erp_fa_depmaster.depMasterAutoID,
	erp_fa_depmaster.companySystemID,
	erp_fa_depmaster.companyID,
	erp_fa_assetdepreciationperiods.faID,
	Round( erp_fa_assetdepreciationperiods.depPercent ) AS depPercentage,
	YEAR ( erp_fa_depmaster.depDate ) AS YEAR,
	MONTH ( erp_fa_depmaster.depDate ) AS MONTH,
	erp_fa_assetdepreciationperiods.COSTUNIT AS CostAmountLocal,
	erp_fa_assetdepreciationperiods.costUnitRpt AS CostAmountRpt,
	erp_fa_assetdepreciationperiods.depAmountLocal,
	erp_fa_assetdepreciationperiods.depAmountRpt
FROM
	erp_fa_depmaster
	INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
WHERE
	erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ') 
	AND YEAR ( erp_fa_depmaster.depDate ) = '.$year.' -- year which is selected in filter option
	
	) AS assetDepreciation ON assetDepreciation.companySystemID = erp_fa_asset_master.companySystemID 
	AND assetDepreciation.faID = erp_fa_asset_master.faID
WHERE
	erp_fa_asset_master.companySystemID IN (' . join(',', $companyID) . ')
	AND DATE(erp_fa_asset_master.dateAQ) <= "'.$lastDayOfMonth.'" -- last date of the month which is selected in filter option
	AND erp_fa_asset_master.approved =- 1 
AND
IF
	(
		erp_fa_asset_master.DIPOSED =- 1,
	IF
		(
			( IF ( erp_fa_asset_master.disposedDate IS NULL, "1990-01-01", erp_fa_asset_master.disposedDate ) ) < "'.$lastDayOfMonth.'",
			1,
			0 
		),
		0 
	) = 0 
GROUP BY
	erp_fa_asset_master.companySystemID,
erp_fa_asset_master.faID';

        //DB::enableQueryLog();
        $output = \DB::select($sql);
        //dd(DB::getQueryLog());
        return ['data' => $output, 'month' => $arrayMonth];
    }

    public function assetDepreciationDetailMonthlyQRY($request){

        $year = $request->year;
        $month = sprintf("%02d", $request->month);

        $firstDayOfMonth = new Carbon($year.'-'.$month.'-01');
        $lastDayOfMonth = $firstDayOfMonth->endOfMonth();
        $lastDayOfMonth = $lastDayOfMonth->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $currency = $request->currencyID;
        $currentMonthDep = "";
        $cost = "";
        $accumilatedAmount = "";
        $netBookValue = "";
        $currentYearDep = "";
        if ($currency == 2) {
            $currentMonthDep = "sum( ( IF ( assetDepreciation.runningMonthDepreciationLocal IS NULL, 0, assetDepreciation.runningMonthDepreciationLocal ) ) ) AS currentMonthDepreciation";
            $cost="round( erp_fa_asset_master.COSTUNIT, 3 ) AS cost";
            $accumilatedAmount = "IF
	( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal ) -
IF
	( disposal.disposalAmountLocal IS NULL, 0, disposal.disposalAmountLocal ) AS accumulatedDepreciation";
            $netBookValue = "round( erp_fa_asset_master.COSTUNIT, 3 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal ) ) AS netBookValue";
            $currentYearDep = "IF
	( DepreciationTotalCurPrevYear.currentYearDepAmountLocal IS NULL, 0, DepreciationTotalCurPrevYear.currentYearDepAmountLocal ) AS currentYearDepAmount";
        } else {
            $currentMonthDep = "sum( ( IF ( assetDepreciation.runningMonthDepreciationRpt IS NULL, 0, assetDepreciation.runningMonthDepreciationRpt ) ) ) AS currentMonthDepreciation";
            $cost="round( erp_fa_asset_master.costUnitRpt, 2 ) AS cost";
            $accumilatedAmount = "IF
	( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt ) -
IF
	( disposal.disposalAmountRpt IS NULL, 0, disposal.disposalAmountRpt ) AS accumulatedDepreciation";
            $netBookValue = "round( erp_fa_asset_master.costUnitRpt, 2 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt ) ) AS netBookValue";
            $currentYearDep = "IF
	( DepreciationTotalCurPrevYear.currentYearDepAmountRpt IS NULL, 0, DepreciationTotalCurPrevYear.currentYearDepAmountRpt ) AS currentYearDepAmount";
        }

        $sql= 'SELECT
	erp_fa_asset_master.companySystemID,
	erp_fa_asset_master.companyID,
	erp_fa_asset_master.faID,
	erp_fa_asset_master.faCode,
	erp_fa_asset_master.assetDescription,
	erp_fa_financecategory.financeCatDescription AS AuditCategory,
	erp_fa_category.catDescription Category,
	'.$currentMonthDep.',
	'.$cost.',
	'.$accumilatedAmount.',
	'.$netBookValue.',
	'.$currentYearDep.',
	erp_fa_asset_master.DEPpercentage AS DEPpercentage
FROM
	erp_fa_asset_master
	LEFT JOIN erp_fa_financecategory ON erp_fa_asset_master.AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID
	LEFT JOIN erp_fa_category ON erp_fa_asset_master.faCatID = erp_fa_category.faCatID
	INNER JOIN (-- assetDepreciation
SELECT
	erp_fa_depmaster.depMasterAutoID,
	erp_fa_depmaster.companySystemID,
	erp_fa_depmaster.companyID,
	erp_fa_assetdepreciationperiods.faID,
	Round( erp_fa_assetdepreciationperiods.depPercent ) AS depPercentage,
	YEAR ( erp_fa_depmaster.depDate ) AS YEAR,
	MONTH ( erp_fa_depmaster.depDate ) AS MONTH,
	erp_fa_assetdepreciationperiods.COSTUNIT AS CostAmountLocal,
	erp_fa_assetdepreciationperiods.costUnitRpt AS CostAmountRpt,
	erp_fa_assetdepreciationperiods.depAmountLocal,
	erp_fa_assetdepreciationperiods.depAmountRpt,
      erp_fa_assetdepreciationperiods.depAmountLocal AS runningMonthDepreciationLocal,-- 7 is the month which is selected in the filter
        erp_fa_assetdepreciationperiods.depAmountRpt AS runningMonthDepreciationRpt -- 7 is the month which is selected in the filter
FROM
	erp_fa_depmaster
	INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
WHERE
	erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ') 
	AND DATE_FORMAT ( erp_fa_depmaster.depDate,"%Y-%m" ) = "'.$year.'-'.$month.'" -- year which is selected in filter option
	) AS assetDepreciation ON assetDepreciation.companySystemID = erp_fa_asset_master.companySystemID 
	AND assetDepreciation.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_depmaster.companySystemID,
		erp_fa_depmaster.companyID,
		erp_fa_assetdepreciationperiods.faID,
		sum( round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) ) AS AccumulatedDepreciationLocal,
		sum( round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) ) AS AccumulatedDepreciationRpt 
	FROM
		erp_fa_depmaster
		INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
	WHERE
		erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ')
		AND DATE( erp_fa_depmaster.depDate) <= "'.$lastDayOfMonth.'" -- last date of the month which is selected in filter option
	GROUP BY
		erp_fa_depmaster.companySystemID,
		erp_fa_assetdepreciationperiods.faID 
	) AS AccumulatedDepreciation ON AccumulatedDepreciation.companySystemID = erp_fa_asset_master.companySystemID 
	AND AccumulatedDepreciation.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_depmaster.companySystemID,
		erp_fa_depmaster.companyID,
		erp_fa_assetdepreciationperiods.faID,-- 2018 is the year selected in filter option
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = '.$year.', round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ), 0 ) ) AS currentYearDepAmountLocal,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = '.$year.', round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ), 0 ) ) AS currentYearDepAmountRpt,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = '.($year-1).', 0, round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) ) ) AS PreviousYearDepAmountLocal,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = '.($year-1).', 0, round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) ) ) AS PreviousYearDepAmountRpt 
	FROM
		erp_fa_depmaster
		LEFT JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
		AND erp_fa_depmaster.companySystemID = erp_fa_assetdepreciationperiods.companySystemID 
	WHERE
		erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ')
	GROUP BY
		erp_fa_depmaster.companySystemID,
		erp_fa_assetdepreciationperiods.faID 
	) AS DepreciationTotalCurPrevYear ON DepreciationTotalCurPrevYear.companySystemID = erp_fa_asset_master.companySystemID 
	AND DepreciationTotalCurPrevYear.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_asset_disposalmaster.companySystemID,
		erp_fa_asset_disposalmaster.companyID,
		erp_fa_asset_disposaldetail.faID,
		sum( round( erp_fa_asset_disposaldetail.depAmountLocal, 3 ) ) AS disposalAmountLocal,
		sum( round( erp_fa_asset_disposaldetail.depAmountRpt, 2 ) ) AS disposalAmountRpt 
	FROM
		erp_fa_asset_disposalmaster
		INNER JOIN erp_fa_asset_disposaldetail ON erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = erp_fa_asset_disposaldetail.assetdisposalMasterAutoID 
	WHERE
		erp_fa_asset_disposalmaster.companySystemID IN (' . join(',', $companyID) . ')
		AND DATE( erp_fa_asset_disposalmaster.disposalDocumentDate) <= "'.$lastDayOfMonth.'" -- last date of the month which is selected in filter option
		
	GROUP BY
		erp_fa_asset_disposalmaster.companySystemID,
		erp_fa_asset_disposaldetail.faID 
	) AS disposal ON disposal.companySystemID = erp_fa_asset_master.companySystemID 
WHERE
	erp_fa_asset_master.companySystemID IN (' . join(',', $companyID) . ')
	AND DATE(erp_fa_asset_master.dateAQ) <= "'.$lastDayOfMonth.'" -- last date of the month which is selected in filter option
	
	AND erp_fa_asset_master.approved =- 1 
AND
IF
	(
		erp_fa_asset_master.DIPOSED =- 1,
	IF
		(
			( IF ( erp_fa_asset_master.disposedDate IS NULL, "1990-01-01", erp_fa_asset_master.disposedDate ) ) < "'.$lastDayOfMonth.'",
			1,
			0 
		),
		0 
	) = 0 
GROUP BY
	erp_fa_asset_master.companySystemID,
erp_fa_asset_master.faID;';

        //DB::enableQueryLog();
        $output = \DB::select($sql);
        //dd(DB::getQueryLog());
        return $output;
    }

    public function assetDepreciationDetailSummaryQRY($request){

        $year = $request->year;
        $month = sprintf("%02d", $request->month);

        $firstDayOfMonth = new Carbon($year.'-'.$month.'-01');
        $lastDayOfMonth = $firstDayOfMonth->endOfMonth();
        $lastDayOfMonth = $lastDayOfMonth->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $currency = $request->currencyID;

        $arrayMonth = array( 'Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

        $currentMonthDep = "";
        $cost = "";
        $accumilatedAmount = "";
        $netBookValue = "";
        $currentYearDep = "";
        $monthField = "";
        if ($currency == 2) {
            $currentMonthDep = "sum( ( IF ( assetDepreciation.runningMonthDepreciationLocal IS NULL, 0, assetDepreciation.runningMonthDepreciationLocal ) ) ) AS currentMonthDepreciation";
            $cost="round( erp_fa_asset_master.COSTUNIT, 3 ) AS cost";
            $accumilatedAmount = "IF
	( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal ) -
IF
	( disposal.disposalAmountLocal IS NULL, 0, disposal.disposalAmountLocal ) AS accumulatedDepreciation";
            $netBookValue = "round( erp_fa_asset_master.COSTUNIT, 3 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal ) ) AS netBookValue";
            $currentYearDep = "IF
	( DepreciationTotalCurPrevYear.currentYearDepAmountLocal IS NULL, 0, DepreciationTotalCurPrevYear.currentYearDepAmountLocal ) AS currentYearDepAmount";
            foreach ($arrayMonth as $key => $val) {
                $monthField .= "if(MONTH(erp_fa_depmaster.depDate) = ".($key+1). ",round(erp_fa_assetdepreciationperiods.depAmountLocal, 2),0) as `" . $val . "`,";
            }
        } else {
            $currentMonthDep = "sum( ( IF ( assetDepreciation.runningMonthDepreciationRpt IS NULL, 0, assetDepreciation.runningMonthDepreciationRpt ) ) ) AS currentMonthDepreciation";
            $cost="round( erp_fa_asset_master.costUnitRpt, 2 ) AS cost";
            $accumilatedAmount = "IF
	( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt ) -
IF
	( disposal.disposalAmountRpt IS NULL, 0, disposal.disposalAmountRpt ) AS accumulatedDepreciation";
            $netBookValue = "round( erp_fa_asset_master.costUnitRpt, 2 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt ) ) AS netBookValue";
            $currentYearDep = "IF
	( DepreciationTotalCurPrevYear.currentYearDepAmountRpt IS NULL, 0, DepreciationTotalCurPrevYear.currentYearDepAmountRpt ) AS currentYearDepAmount";
            foreach ($arrayMonth as $key => $val) {
                $monthField .= "if(MONTH(erp_fa_depmaster.depDate) = ".($key+1). ",round(erp_fa_assetdepreciationperiods.depAmountRpt, 2),0) as `" . $val . "`,";
            }
        }

        $sql= 'SELECT
	erp_fa_asset_master.companySystemID,
	erp_fa_asset_master.companyID,
	erp_fa_asset_master.faID,
	erp_fa_asset_master.faCode,
	erp_fa_asset_master.assetDescription,
	erp_fa_financecategory.financeCatDescription AS AuditCategory,
	erp_fa_category.catDescription Category,
	'.$currentMonthDep.',
	'.$cost.',
	'.$accumilatedAmount.',
	'.$netBookValue.',
	'.$currentYearDep.',
	sum( ( IF ( assetDepreciation.Jan IS NULL, 0, assetDepreciation.Jan ) ) ) AS Jan,
	sum( ( IF ( assetDepreciation.Feb IS NULL, 0, assetDepreciation.Feb ) ) ) AS Feb,
	sum( ( IF ( assetDepreciation.March IS NULL, 0, assetDepreciation.March ) ) ) AS March,
	sum( ( IF ( assetDepreciation.April IS NULL, 0, assetDepreciation.April ) ) ) AS April,
	sum( ( IF ( assetDepreciation.May IS NULL, 0, assetDepreciation.May ) ) ) AS May,
	sum( ( IF ( assetDepreciation.June IS NULL, 0, assetDepreciation.June ) ) ) AS June,
	sum( ( IF ( assetDepreciation.July IS NULL, 0, assetDepreciation.July ) ) ) AS July,
	sum( ( IF ( assetDepreciation.Aug IS NULL, 0, assetDepreciation.Aug ) ) ) AS Aug,
	sum( ( IF ( assetDepreciation.Sept IS NULL, 0, assetDepreciation.Sept ) ) ) AS Sept,
	sum( ( IF ( assetDepreciation.Oct IS NULL, 0, assetDepreciation.Oct ) ) ) AS Oct,
	sum( ( IF ( assetDepreciation.Nov IS NULL, 0, assetDepreciation.Nov ) ) ) AS Nov,
	sum( ( IF ( assetDepreciation.Dece IS NULL, 0, assetDepreciation.Dece ) ) ) AS Dece,
	erp_fa_asset_master.DEPpercentage AS DEPpercentage
FROM
	erp_fa_asset_master
	LEFT JOIN erp_fa_financecategory ON erp_fa_asset_master.AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID
	LEFT JOIN erp_fa_category ON erp_fa_asset_master.faCatID = erp_fa_category.faCatID
	INNER JOIN (-- assetDepreciation
SELECT
	erp_fa_depmaster.depMasterAutoID,
	erp_fa_depmaster.companySystemID,
	erp_fa_depmaster.companyID,
	erp_fa_assetdepreciationperiods.faID,
	Round( erp_fa_assetdepreciationperiods.depPercent ) AS depPercentage,
	YEAR ( erp_fa_depmaster.depDate ) AS YEAR,
	MONTH ( erp_fa_depmaster.depDate ) AS MONTH,
	erp_fa_assetdepreciationperiods.COSTUNIT AS CostAmountLocal,
	erp_fa_assetdepreciationperiods.costUnitRpt AS CostAmountRpt,
	erp_fa_assetdepreciationperiods.depAmountLocal,
	erp_fa_assetdepreciationperiods.depAmountRpt,
	'.$monthField.'
     IF
	( MONTH ( erp_fa_depmaster.depDate ) = '.$month.', erp_fa_assetdepreciationperiods.depAmountLocal, 0 ) AS runningMonthDepreciationLocal,-- 7 is the month which is selected in the filter
IF
	( MONTH ( erp_fa_depmaster.depDate ) = '.$month.', erp_fa_assetdepreciationperiods.depAmountRpt, 0 ) AS runningMonthDepreciationRpt -- 7 is the month which is selected in the filter
FROM
	erp_fa_depmaster
	INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
WHERE
	erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ') 
	AND YEAR ( erp_fa_depmaster.depDate ) = '.$year.' -- year which is selected in filter option
	) AS assetDepreciation ON assetDepreciation.companySystemID = erp_fa_asset_master.companySystemID 
	AND assetDepreciation.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_depmaster.companySystemID,
		erp_fa_depmaster.companyID,
		erp_fa_assetdepreciationperiods.faID,
		sum( round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) ) AS AccumulatedDepreciationLocal,
		sum( round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) ) AS AccumulatedDepreciationRpt 
	FROM
		erp_fa_depmaster
		INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
	WHERE
		erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ')
		AND DATE( erp_fa_depmaster.depDate) <= "'.$lastDayOfMonth.'" -- last date of the month which is selected in filter option
	GROUP BY
		erp_fa_depmaster.companySystemID,
		erp_fa_assetdepreciationperiods.faID 
	) AS AccumulatedDepreciation ON AccumulatedDepreciation.companySystemID = erp_fa_asset_master.companySystemID 
	AND AccumulatedDepreciation.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_depmaster.companySystemID,
		erp_fa_depmaster.companyID,
		erp_fa_assetdepreciationperiods.faID,-- 2018 is the year selected in filter option
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = '.$year.', round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ), 0 ) ) AS currentYearDepAmountLocal,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = '.$year.', round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ), 0 ) ) AS currentYearDepAmountRpt,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = '.($year-1).', 0, round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) ) ) AS PreviousYearDepAmountLocal,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = '.($year-1).', 0, round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) ) ) AS PreviousYearDepAmountRpt 
	FROM
		erp_fa_depmaster
		LEFT JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
		AND erp_fa_depmaster.companySystemID = erp_fa_assetdepreciationperiods.companySystemID 
	WHERE
		erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ')
	GROUP BY
		erp_fa_depmaster.companySystemID,
		erp_fa_assetdepreciationperiods.faID 
	) AS DepreciationTotalCurPrevYear ON DepreciationTotalCurPrevYear.companySystemID = erp_fa_asset_master.companySystemID 
	AND DepreciationTotalCurPrevYear.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_asset_disposalmaster.companySystemID,
		erp_fa_asset_disposalmaster.companyID,
		erp_fa_asset_disposaldetail.faID,
		sum( round( erp_fa_asset_disposaldetail.depAmountLocal, 3 ) ) AS disposalAmountLocal,
		sum( round( erp_fa_asset_disposaldetail.depAmountRpt, 2 ) ) AS disposalAmountRpt 
	FROM
		erp_fa_asset_disposalmaster
		INNER JOIN erp_fa_asset_disposaldetail ON erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = erp_fa_asset_disposaldetail.assetdisposalMasterAutoID 
	WHERE
		erp_fa_asset_disposalmaster.companySystemID IN (' . join(',', $companyID) . ')
		AND DATE( erp_fa_asset_disposalmaster.disposalDocumentDate) <= "'.$lastDayOfMonth.'" -- last date of the month which is selected in filter option
		
	GROUP BY
		erp_fa_asset_disposalmaster.companySystemID,
		erp_fa_asset_disposaldetail.faID 
	) AS disposal ON disposal.companySystemID = erp_fa_asset_master.companySystemID 
WHERE
	erp_fa_asset_master.companySystemID IN (' . join(',', $companyID) . ')
	AND DATE(erp_fa_asset_master.dateAQ) <= "'.$lastDayOfMonth.'" -- last date of the month which is selected in filter option
	
	AND erp_fa_asset_master.approved =- 1 
AND
IF
	(
		erp_fa_asset_master.DIPOSED =- 1,
	IF
		(
			( IF ( erp_fa_asset_master.disposedDate IS NULL, "1990-01-01", erp_fa_asset_master.disposedDate ) ) < "'.$lastDayOfMonth.'",
			1,
			0 
		),
		0 
	) = 0 
GROUP BY
	erp_fa_asset_master.companySystemID,
erp_fa_asset_master.faID;';

        //DB::enableQueryLog();
        $output = \DB::select($sql);
        //dd(DB::getQueryLog());
        return $output;
    }

    public function assetDepreciationCategorySummaryQRY($request){

        $year = $request->year;
        $month = sprintf("%02d", $request->month);

        $firstDayOfMonth = new Carbon($year.'-'.$month.'-01');
        $lastDayOfMonth = $firstDayOfMonth->endOfMonth();
        $lastDayOfMonth = $lastDayOfMonth->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $currency = $request->currencyID;
        $currentMonthDep = "";
        $cost = "";
        $accumilatedAmount = "";
        $netBookValue = "";
        $currentYearDep = "";
        if ($currency == 2) {
            $currentMonthDep = "sum( ( IF ( assetDepreciation.runningMonthDepreciationLocal IS NULL, 0, assetDepreciation.runningMonthDepreciationLocal ) ) ) AS currentMonthDepreciation";
            $cost="round( erp_fa_asset_master.COSTUNIT, 3 ) AS cost";
            $accumilatedAmount = "IF
	( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal ) -
IF
	( disposal.disposalAmountLocal IS NULL, 0, disposal.disposalAmountLocal ) AS accumulatedDepreciation";
            $netBookValue = "round( erp_fa_asset_master.COSTUNIT, 3 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal ) ) AS netBookValue";
            $currentYearDep = "IF
	( DepreciationTotalCurPrevYear.currentYearDepAmountLocal IS NULL, 0, DepreciationTotalCurPrevYear.currentYearDepAmountLocal ) AS currentYearDepAmount";
        } else {
            $currentMonthDep = "sum( ( IF ( assetDepreciation.runningMonthDepreciationRpt IS NULL, 0, assetDepreciation.runningMonthDepreciationRpt ) ) ) AS currentMonthDepreciation";
            $cost="round( erp_fa_asset_master.costUnitRpt, 2 ) AS cost";
            $accumilatedAmount = "IF
	( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt ) -
IF
	( disposal.disposalAmountRpt IS NULL, 0, disposal.disposalAmountRpt ) AS accumulatedDepreciation";
            $netBookValue = "round( erp_fa_asset_master.costUnitRpt, 2 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt ) ) AS netBookValue";
            $currentYearDep = "IF
	( DepreciationTotalCurPrevYear.currentYearDepAmountRpt IS NULL, 0, DepreciationTotalCurPrevYear.currentYearDepAmountRpt ) AS currentYearDepAmount";
        }

        $sql= 'SELECT
	erp_fa_asset_master.companySystemID,
	erp_fa_asset_master.companyID,
	erp_fa_asset_master.faID,
	erp_fa_asset_master.faCode,
	erp_fa_asset_master.assetDescription,
	erp_fa_financecategory.financeCatDescription AS AuditCategory,
	erp_fa_category.catDescription Category,
	'.$currentMonthDep.',
	'.$cost.',
	'.$accumilatedAmount.',
	'.$netBookValue.',
	'.$currentYearDep.',
	erp_fa_asset_master.DEPpercentage AS DEPpercentage
FROM
	erp_fa_asset_master
	LEFT JOIN erp_fa_financecategory ON erp_fa_asset_master.AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID
	LEFT JOIN erp_fa_category ON erp_fa_asset_master.faCatID = erp_fa_category.faCatID
	INNER JOIN (-- assetDepreciation
SELECT
	erp_fa_depmaster.depMasterAutoID,
	erp_fa_depmaster.companySystemID,
	erp_fa_depmaster.companyID,
	erp_fa_assetdepreciationperiods.faID,
	Round( erp_fa_assetdepreciationperiods.depPercent ) AS depPercentage,
	YEAR ( erp_fa_depmaster.depDate ) AS YEAR,
	MONTH ( erp_fa_depmaster.depDate ) AS MONTH,
	erp_fa_assetdepreciationperiods.COSTUNIT AS CostAmountLocal,
	erp_fa_assetdepreciationperiods.costUnitRpt AS CostAmountRpt,
	erp_fa_assetdepreciationperiods.depAmountLocal,
	erp_fa_assetdepreciationperiods.depAmountRpt,
      erp_fa_assetdepreciationperiods.depAmountLocal AS runningMonthDepreciationLocal,-- 7 is the month which is selected in the filter
        erp_fa_assetdepreciationperiods.depAmountRpt AS runningMonthDepreciationRpt -- 7 is the month which is selected in the filter
FROM
	erp_fa_depmaster
	INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
WHERE
	erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ') 
	AND DATE_FORMAT ( erp_fa_depmaster.depDate,"%Y-%m" ) = "'.$year.'-'.$month.'" -- year which is selected in filter option
	) AS assetDepreciation ON assetDepreciation.companySystemID = erp_fa_asset_master.companySystemID 
	AND assetDepreciation.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_depmaster.companySystemID,
		erp_fa_depmaster.companyID,
		erp_fa_assetdepreciationperiods.faID,
		sum( round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) ) AS AccumulatedDepreciationLocal,
		sum( round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) ) AS AccumulatedDepreciationRpt 
	FROM
		erp_fa_depmaster
		INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
	WHERE
		erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ')
		AND DATE( erp_fa_depmaster.depDate) <= "'.$lastDayOfMonth.'" -- last date of the month which is selected in filter option
	GROUP BY
		erp_fa_depmaster.companySystemID,
		erp_fa_assetdepreciationperiods.faID 
	) AS AccumulatedDepreciation ON AccumulatedDepreciation.companySystemID = erp_fa_asset_master.companySystemID 
	AND AccumulatedDepreciation.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_depmaster.companySystemID,
		erp_fa_depmaster.companyID,
		erp_fa_assetdepreciationperiods.faID,-- 2018 is the year selected in filter option
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = '.$year.', round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ), 0 ) ) AS currentYearDepAmountLocal,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = '.$year.', round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ), 0 ) ) AS currentYearDepAmountRpt,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = '.($year-1).', 0, round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) ) ) AS PreviousYearDepAmountLocal,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = '.($year-1).', 0, round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) ) ) AS PreviousYearDepAmountRpt 
	FROM
		erp_fa_depmaster
		LEFT JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
		AND erp_fa_depmaster.companySystemID = erp_fa_assetdepreciationperiods.companySystemID 
	WHERE
		erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ')
	GROUP BY
		erp_fa_depmaster.companySystemID,
		erp_fa_assetdepreciationperiods.faID 
	) AS DepreciationTotalCurPrevYear ON DepreciationTotalCurPrevYear.companySystemID = erp_fa_asset_master.companySystemID 
	AND DepreciationTotalCurPrevYear.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_asset_disposalmaster.companySystemID,
		erp_fa_asset_disposalmaster.companyID,
		erp_fa_asset_disposaldetail.faID,
		sum( round( erp_fa_asset_disposaldetail.depAmountLocal, 3 ) ) AS disposalAmountLocal,
		sum( round( erp_fa_asset_disposaldetail.depAmountRpt, 2 ) ) AS disposalAmountRpt 
	FROM
		erp_fa_asset_disposalmaster
		INNER JOIN erp_fa_asset_disposaldetail ON erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = erp_fa_asset_disposaldetail.assetdisposalMasterAutoID 
	WHERE
		erp_fa_asset_disposalmaster.companySystemID IN (' . join(',', $companyID) . ')
		AND DATE( erp_fa_asset_disposalmaster.disposalDocumentDate) <= "'.$lastDayOfMonth.'" -- last date of the month which is selected in filter option
		
	GROUP BY
		erp_fa_asset_disposalmaster.companySystemID,
		erp_fa_asset_disposaldetail.faID 
	) AS disposal ON disposal.companySystemID = erp_fa_asset_master.companySystemID 
WHERE
	erp_fa_asset_master.companySystemID IN (' . join(',', $companyID) . ')
	AND DATE(erp_fa_asset_master.dateAQ) <= "'.$lastDayOfMonth.'" -- last date of the month which is selected in filter option
	
	AND erp_fa_asset_master.approved =- 1 
AND
IF
	(
		erp_fa_asset_master.DIPOSED =- 1,
	IF
		(
			( IF ( erp_fa_asset_master.disposedDate IS NULL, "1990-01-01", erp_fa_asset_master.disposedDate ) ) < "'.$lastDayOfMonth.'",
			1,
			0 
		),
		0 
	) = 0 
GROUP BY
	erp_fa_asset_master.companySystemID,
erp_fa_asset_master.faCatID;';

        //DB::enableQueryLog();
        $output = \DB::select($sql);
        //dd(DB::getQueryLog());
        return $output;
    }

    public function assetDepreciationCategorySummaryMonthlyQRY($request){

        $year = $request->year;
        $month = sprintf("%02d", $request->month);

        $firstDayOfMonth = new Carbon($year.'-'.$month.'-01');
        $lastDayOfMonth = $firstDayOfMonth->endOfMonth();
        $lastDayOfMonth = $lastDayOfMonth->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $currency = $request->currencyID;

        $arrayMonth = array( 'Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

        $currentMonthDep = "";
        $cost = "";
        $accumilatedAmount = "";
        $netBookValue = "";
        $currentYearDep = "";
        $monthField = "";
        if ($currency == 2) {
            $currentMonthDep = "sum( ( IF ( assetDepreciation.runningMonthDepreciationLocal IS NULL, 0, assetDepreciation.runningMonthDepreciationLocal ) ) ) AS currentMonthDepreciation";
            $cost="round( erp_fa_asset_master.COSTUNIT, 3 ) AS cost";
            $accumilatedAmount = "IF
	( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal ) -
IF
	( disposal.disposalAmountLocal IS NULL, 0, disposal.disposalAmountLocal ) AS accumulatedDepreciation";
            $netBookValue = "round( erp_fa_asset_master.COSTUNIT, 3 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal ) ) AS netBookValue";
            $currentYearDep = "IF
	( DepreciationTotalCurPrevYear.currentYearDepAmountLocal IS NULL, 0, DepreciationTotalCurPrevYear.currentYearDepAmountLocal ) AS currentYearDepAmount";
            foreach ($arrayMonth as $key => $val) {
                $monthField .= "if(MONTH(erp_fa_depmaster.depDate) = ".($key+1). ",round(erp_fa_assetdepreciationperiods.depAmountLocal, 2),0) as `" . $val . "`,";
            }
        } else {
            $currentMonthDep = "sum( ( IF ( assetDepreciation.runningMonthDepreciationRpt IS NULL, 0, assetDepreciation.runningMonthDepreciationRpt ) ) ) AS currentMonthDepreciation";
            $cost="round( erp_fa_asset_master.costUnitRpt, 2 ) AS cost";
            $accumilatedAmount = "IF
	( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt ) -
IF
	( disposal.disposalAmountRpt IS NULL, 0, disposal.disposalAmountRpt ) AS accumulatedDepreciation";
            $netBookValue = "round( erp_fa_asset_master.costUnitRpt, 2 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt ) ) AS netBookValue";
            $currentYearDep = "IF
	( DepreciationTotalCurPrevYear.currentYearDepAmountRpt IS NULL, 0, DepreciationTotalCurPrevYear.currentYearDepAmountRpt ) AS currentYearDepAmount";
            foreach ($arrayMonth as $key => $val) {
                $monthField .= "if(MONTH(erp_fa_depmaster.depDate) = ".($key+1). ",round(erp_fa_assetdepreciationperiods.depAmountRpt, 2),0) as `" . $val . "`,";
            }
        }

        $sql= 'SELECT
	erp_fa_asset_master.companySystemID,
	erp_fa_asset_master.companyID,
	erp_fa_asset_master.faID,
	erp_fa_asset_master.faCode,
	erp_fa_asset_master.assetDescription,
	erp_fa_financecategory.financeCatDescription AS AuditCategory,
	erp_fa_category.catDescription Category,
	'.$currentMonthDep.',
	'.$cost.',
	'.$accumilatedAmount.',
	'.$netBookValue.',
	'.$currentYearDep.',
	sum( ( IF ( assetDepreciation.Jan IS NULL, 0, assetDepreciation.Jan ) ) ) AS Jan,
	sum( ( IF ( assetDepreciation.Feb IS NULL, 0, assetDepreciation.Feb ) ) ) AS Feb,
	sum( ( IF ( assetDepreciation.March IS NULL, 0, assetDepreciation.March ) ) ) AS March,
	sum( ( IF ( assetDepreciation.April IS NULL, 0, assetDepreciation.April ) ) ) AS April,
	sum( ( IF ( assetDepreciation.May IS NULL, 0, assetDepreciation.May ) ) ) AS May,
	sum( ( IF ( assetDepreciation.June IS NULL, 0, assetDepreciation.June ) ) ) AS June,
	sum( ( IF ( assetDepreciation.July IS NULL, 0, assetDepreciation.July ) ) ) AS July,
	sum( ( IF ( assetDepreciation.Aug IS NULL, 0, assetDepreciation.Aug ) ) ) AS Aug,
	sum( ( IF ( assetDepreciation.Sept IS NULL, 0, assetDepreciation.Sept ) ) ) AS Sept,
	sum( ( IF ( assetDepreciation.Oct IS NULL, 0, assetDepreciation.Oct ) ) ) AS Oct,
	sum( ( IF ( assetDepreciation.Nov IS NULL, 0, assetDepreciation.Nov ) ) ) AS Nov,
	sum( ( IF ( assetDepreciation.Dece IS NULL, 0, assetDepreciation.Dece ) ) ) AS Dece,
	erp_fa_asset_master.DEPpercentage AS DEPpercentage
FROM
	erp_fa_asset_master
	LEFT JOIN erp_fa_financecategory ON erp_fa_asset_master.AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID
	LEFT JOIN erp_fa_category ON erp_fa_asset_master.faCatID = erp_fa_category.faCatID
	INNER JOIN (-- assetDepreciation
SELECT
	erp_fa_depmaster.depMasterAutoID,
	erp_fa_depmaster.companySystemID,
	erp_fa_depmaster.companyID,
	erp_fa_assetdepreciationperiods.faID,
	Round( erp_fa_assetdepreciationperiods.depPercent ) AS depPercentage,
	YEAR ( erp_fa_depmaster.depDate ) AS YEAR,
	MONTH ( erp_fa_depmaster.depDate ) AS MONTH,
	erp_fa_assetdepreciationperiods.COSTUNIT AS CostAmountLocal,
	erp_fa_assetdepreciationperiods.costUnitRpt AS CostAmountRpt,
	erp_fa_assetdepreciationperiods.depAmountLocal,
	erp_fa_assetdepreciationperiods.depAmountRpt,
	'.$monthField.'
      IF
	( MONTH ( erp_fa_depmaster.depDate ) = '.$month.', erp_fa_assetdepreciationperiods.depAmountLocal, 0 ) AS runningMonthDepreciationLocal,-- 7 is the month which is selected in the filter
IF
	( MONTH ( erp_fa_depmaster.depDate ) = '.$month.', erp_fa_assetdepreciationperiods.depAmountRpt, 0 ) AS runningMonthDepreciationRpt -- 7 is the month which is selected in the filter
FROM
	erp_fa_depmaster
	INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
WHERE
	erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ') 
	AND YEAR ( erp_fa_depmaster.depDate ) = '.$year.' -- year which is selected in filter option -- year which is selected in filter option
	) AS assetDepreciation ON assetDepreciation.companySystemID = erp_fa_asset_master.companySystemID 
	AND assetDepreciation.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_depmaster.companySystemID,
		erp_fa_depmaster.companyID,
		erp_fa_assetdepreciationperiods.faID,
		sum( round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) ) AS AccumulatedDepreciationLocal,
		sum( round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) ) AS AccumulatedDepreciationRpt 
	FROM
		erp_fa_depmaster
		INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
	WHERE
		erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ')
		AND DATE( erp_fa_depmaster.depDate) <= "'.$lastDayOfMonth.'" -- last date of the month which is selected in filter option
	GROUP BY
		erp_fa_depmaster.companySystemID,
		erp_fa_assetdepreciationperiods.faID 
	) AS AccumulatedDepreciation ON AccumulatedDepreciation.companySystemID = erp_fa_asset_master.companySystemID 
	AND AccumulatedDepreciation.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_depmaster.companySystemID,
		erp_fa_depmaster.companyID,
		erp_fa_assetdepreciationperiods.faID,-- 2018 is the year selected in filter option
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = '.$year.', round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ), 0 ) ) AS currentYearDepAmountLocal,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = '.$year.', round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ), 0 ) ) AS currentYearDepAmountRpt,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = '.($year-1).', 0, round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) ) ) AS PreviousYearDepAmountLocal,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = '.($year-1).', 0, round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) ) ) AS PreviousYearDepAmountRpt 
	FROM
		erp_fa_depmaster
		LEFT JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
		AND erp_fa_depmaster.companySystemID = erp_fa_assetdepreciationperiods.companySystemID 
	WHERE
		erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ')
	GROUP BY
		erp_fa_depmaster.companySystemID,
		erp_fa_assetdepreciationperiods.faID 
	) AS DepreciationTotalCurPrevYear ON DepreciationTotalCurPrevYear.companySystemID = erp_fa_asset_master.companySystemID 
	AND DepreciationTotalCurPrevYear.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_asset_disposalmaster.companySystemID,
		erp_fa_asset_disposalmaster.companyID,
		erp_fa_asset_disposaldetail.faID,
		sum( round( erp_fa_asset_disposaldetail.depAmountLocal, 3 ) ) AS disposalAmountLocal,
		sum( round( erp_fa_asset_disposaldetail.depAmountRpt, 2 ) ) AS disposalAmountRpt 
	FROM
		erp_fa_asset_disposalmaster
		INNER JOIN erp_fa_asset_disposaldetail ON erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = erp_fa_asset_disposaldetail.assetdisposalMasterAutoID 
	WHERE
		erp_fa_asset_disposalmaster.companySystemID IN (' . join(',', $companyID) . ')
		AND DATE( erp_fa_asset_disposalmaster.disposalDocumentDate) <= "'.$lastDayOfMonth.'" -- last date of the month which is selected in filter option
		
	GROUP BY
		erp_fa_asset_disposalmaster.companySystemID,
		erp_fa_asset_disposaldetail.faID 
	) AS disposal ON disposal.companySystemID = erp_fa_asset_master.companySystemID 
WHERE
	erp_fa_asset_master.companySystemID IN (' . join(',', $companyID) . ')
	AND DATE(erp_fa_asset_master.dateAQ) <= "'.$lastDayOfMonth.'" -- last date of the month which is selected in filter option
	
	AND erp_fa_asset_master.approved =- 1 
AND
IF
	(
		erp_fa_asset_master.DIPOSED =- 1,
	IF
		(
			( IF ( erp_fa_asset_master.disposedDate IS NULL, "1990-01-01", erp_fa_asset_master.disposedDate ) ) < "'.$lastDayOfMonth.'",
			1,
			0 
		),
		0 
	) = 0 
GROUP BY
	erp_fa_asset_master.companySystemID,erp_fa_asset_master.faCatID';

        //DB::enableQueryLog();
        $output = \DB::select($sql);
        //dd(DB::getQueryLog());
        return $output;
    }


}
