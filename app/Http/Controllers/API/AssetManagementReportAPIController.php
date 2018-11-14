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
 * -- Date: 12-july 2018 By: Nazir Description: Added new functions named as getAssetAdditionsQRY()
 * -- Date: 12-july 2018 By: Fayas Description: Added new functions named as getAssetDisposal()
 */

namespace App\Http\Controllers\API;

use App\Models\AssetFinanceCategory;
use App\Models\Company;
use App\Models\Months;
use App\Models\Year;
use App\Models\AssetType;
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

        /*load asset type dropdown*/
        $aasetType = AssetType::all();

        $output = array(
            'assetCategory' => $assetCategory,
            'years' => $years,
            'months' => $months,
            'assetType' => $aasetType
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
                    'typeID' => 'required'
                ]);

                if ($validator->fails()) {//echo 'in';exit;
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            case 'AMAA':
                $validator = \Validator::make($request->all(), [
                    'fromDate' => 'required',
                    'toDate' => 'required|date|after_or_equal:fromDate',
                    'reportTypeID' => 'required',
                ]);

                if ($validator->fails()) {//echo 'in';exit;
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            case 'AMAD':
                $validator = \Validator::make($request->all(), [
                    'reportTypeID' => 'required',
                    'fromDate' => 'required',
                    'toDate' => 'required|date|after_or_equal:fromDate'
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

                /*shahmy*/

                $typeID = $request->typeID;
                $asOfDate = (new Carbon($request->fromDate))->format('Y-m-d');
                $assetCategory = collect($request->assetCategory)->pluck('faFinanceCatID')->toArray();
                $assetCategory = join(',', $assetCategory);

                $qry = "SELECT groupTO,faUnitSerialNo,faID, erp_fa_assettype.typeDes, erp_fa_financecategory.financeCatDescription, final.COSTGLCODE, final.ACCDEPGLCODE, assetType, serviceline.ServiceLineDes, final.serviceLineCode, docOrigin, AUDITCATOGARY, faCode, assetDescription, DEPpercentage, dateAQ, dateDEP, COSTUNIT, IFNULL(depAmountLocal,0) as depAmountLocal, COSTUNIT - IFNULL(depAmountLocal,0) as localnbv, costUnitRpt,
 IFNULL(depAmountRpt,0) as depAmountRpt , costUnitRpt - IFNULL(depAmountRpt,0) as rptnbv FROM ( SELECT 		t.groupTO,erp_fa_asset_master.faUnitSerialNo ,erp_fa_asset_master.faID, COSTGLCODE, ACCDEPGLCODE, assetType, erp_fa_asset_master.serviceLineCode, docOrigin, AUDITCATOGARY, erp_fa_asset_master.faCode, erp_fa_asset_master.assetDescription, DEPpercentage, dateAQ, dateDEP, IFNULL( t.COSTUNIT, 0 ) AS COSTUNIT, IFNULL( depAmountLocal, 0 ) AS depAmountLocal, IFNULL( t.costUnitRpt, 0 ) AS costUnitRpt, IFNULL( depAmountRpt, 0 ) AS depAmountRpt FROM ( SELECT groupTO, SUM( erp_fa_asset_master.COSTUNIT ) AS COSTUNIT, SUM( depAmountLocal ) AS depAmountLocal, SUM( costUnitRpt ) AS costUnitRpt, SUM( depAmountRpt ) AS depAmountRpt FROM erp_fa_asset_master LEFT JOIN ( SELECT faID, SUM( depAmountLocal ) AS depAmountLocal, SUM( depAmountRpt ) AS depAmountRpt FROM ( SELECT faID, erp_fa_assetdepreciationperiods.depMasterAutoID, sum( erp_fa_assetdepreciationperiods.depAmountLocal ) AS depAmountLocal, sum( erp_fa_assetdepreciationperiods.depAmountRpt ) AS depAmountRpt FROM erp_fa_assetdepreciationperiods INNER JOIN erp_fa_depmaster ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID WHERE erp_fa_depmaster.approved =- 1 GROUP BY faID ) t GROUP BY faID ) erp_fa_assetdepreciationperiods ON erp_fa_assetdepreciationperiods.faID = erp_fa_asset_master.faID
  
  WHERE companySystemID = $request->companySystemID AND AUDITCATOGARY IN($assetCategory) AND erp_fa_asset_master.dateAQ <= '$asOfDate' AND assetType = $typeID AND ( disposedDate IS NULL OR disposedDate > '$asOfDate' OR DIPOSED = - 1 ) AND groupTO IS NOT NULL GROUP BY groupTO ) t INNER JOIN erp_fa_asset_master ON erp_fa_asset_master.faID = t.groupTO
  
  UNION ALL SELECT groupTO,erp_fa_asset_master.faUnitSerialNo,
erp_fa_asset_master.faID, COSTGLCODE, ACCDEPGLCODE, assetType, erp_fa_asset_master.serviceLineCode, docOrigin, AUDITCATOGARY, erp_fa_asset_master.faCode, erp_fa_asset_master.assetDescription, DEPpercentage, dateAQ, dateDEP, ( erp_fa_asset_master.COSTUNIT ) AS COSTUNIT, ( depAmountLocal ) AS depAmountLocal, ( costUnitRpt ) AS costUnitRpt, ( depAmountRpt ) AS depAmountRpt FROM erp_fa_asset_master LEFT JOIN ( SELECT faID, IFNULL( SUM( depAmountLocal ), 0 ) AS depAmountLocal, IFNULL( SUM( depAmountRpt ), 0 ) AS depAmountRpt FROM ( SELECT faID, erp_fa_assetdepreciationperiods.depMasterAutoID, sum( erp_fa_assetdepreciationperiods.depAmountLocal ) AS depAmountLocal, sum( erp_fa_assetdepreciationperiods.depAmountRpt ) AS depAmountRpt FROM erp_fa_assetdepreciationperiods INNER JOIN erp_fa_depmaster ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID WHERE erp_fa_depmaster.approved =- 1 GROUP BY faID ) t GROUP BY faID ) erp_fa_assetdepreciationperiods ON erp_fa_assetdepreciationperiods.faID = erp_fa_asset_master.faID WHERE companySystemID = $request->companySystemID AND AUDITCATOGARY IN($assetCategory) AND erp_fa_asset_master.dateAQ <= '$asOfDate' AND assetType = $typeID AND ( disposedDate IS NULL OR disposedDate > '$asOfDate' OR DIPOSED = - 1 ) AND groupTO IS NULL ORDER BY dateDEP DESC ) final INNER JOIN serviceline ON serviceline.ServiceLineCode = final.serviceLineCode INNER JOIN erp_fa_financecategory ON AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID INNER JOIN erp_fa_assettype ON erp_fa_assettype.typeID = final.assetType";

                //$output = \DB::select($qry);
                $output = $this->getAssetRegisterDetail($request);
                $outputArr = [];

                $COSTUNIT = 0;
                $costUnitRpt = 0;
                $depAmountLocal = 0;
                $depAmountRpt = 0;
                $localnbv = 0;
                $rptnbv = 0;
                if ($output) {
                    foreach ($output as $val) {
                        $localnbv += $val->localnbv;
                        $COSTUNIT += $val->COSTUNIT;
                        $costUnitRpt += $val->costUnitRpt;
                        $depAmountRpt += $val->depAmountRpt;
                        $depAmountLocal += $val->depAmountLocal;
                        $rptnbv += $val->rptnbv;
                        $outputArr[$val->financeCatDescription][] = $val;
                    }
                }


                return array('reportData' => $outputArr, 'localnbv' => $localnbv, 'rptnbv' => $rptnbv, 'COSTUNIT' => $COSTUNIT, 'costUnitRpt' => $costUnitRpt, 'depAmountLocal' => $depAmountLocal, 'depAmountRpt' => $depAmountRpt);


                break;
            case 'AMAA': //Asset Additions

                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getAssetAdditionsQRY($request);

                $outputArr = array();
                $assetCostLocal = collect($output)->pluck('AssetCostLocal')->toArray();
                $assetCostLocal = array_sum($assetCostLocal);

                $assetCostRpt = collect($output)->pluck('AssetCostRpt')->toArray();
                $assetCostRpt = array_sum($assetCostRpt);

                if ($output) {
                    foreach ($output as $val) {
                        $outputArr[$val->CompanyName][$val->companyID][] = $val;
                    }
                }

                return array('reportData' => $outputArr, 'assetCostLocal' => $assetCostLocal, 'assetCostRpt' => $assetCostRpt);

                break;
            case 'AMAD': //Asset Disposal
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'year', 'month'));
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getAssetDisposal($request);

                $outputArr = array();
                if ($output) {
                    foreach ($output as $val) {
                        $outputArr[$val->CompanyName][] = $val;
                    }
                }

                $currency = \Helper::companyCurrency($request->companySystemID);

                $total = array();
                $total['AssetCostLocal'] = array_sum(collect($output)->pluck('AssetCostLocal')->toArray());
                $total['AssetCostRPT'] = array_sum(collect($output)->pluck('AssetCostRPT')->toArray());
                $total['AccumulatedDepreciationLocal'] = array_sum(collect($output)->pluck('AccumulatedDepreciationLocal')->toArray());
                $total['AccumulatedDepreciationRPT'] = array_sum(collect($output)->pluck('AccumulatedDepreciationRPT')->toArray());
                $total['NetBookVALUELocal'] = array_sum(collect($output)->pluck('NetBookVALUELocal')->toArray());
                $total['NetBookVALUERPT'] = array_sum(collect($output)->pluck('NetBookVALUERPT')->toArray());
                return array('reportData' => $outputArr,
                    'companyName' => $checkIsGroup->CompanyName,
                    'isGroup' => $checkIsGroup->isGroup,
                    'total' => $total,
                    'decimalPlaceLocal' => $currency->reportingcurrency->DecimalPlaces,
                    'decimalPlaceRpt' => $currency->reportingcurrency->DecimalPlaces,
                    'currencyLocal' => $currency->localcurrency->CurrencyCode,
                    'currencyRpt' => $currency->reportingcurrency->CurrencyCode,
                );

                break;
            case 'AMADR': //Asset Depreciation Register
                $reportTypeID = $request->reportTypeID;
                if ($reportTypeID == 'ADRM') { //Asset Depreciation Register Monthly
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'year', 'month'));
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
                } else if ($reportTypeID == 'ADDM') { //Asset Depreciation Detail Monthly
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'year', 'month'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->assetDepreciationDetailMonthlyQRY($request);

                    $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

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

                    return array('reportData' => $output, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces, 'month' => $arrayMonth);

                } else if ($reportTypeID == 'ADDS') { //Asset Depreciation Detail Summary
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'year', 'month'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->assetDepreciationDetailSummaryQRY($request);

                    $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

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

                    return array('reportData' => $output, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces, 'month' => $arrayMonth);
                } else if ($reportTypeID == 'ADCS') { //Asset Depreciation Category Summary
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'year', 'month'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->assetDepreciationCategorySummaryQRY($request);

                    $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

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

                    return array('reportData' => $output, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces, 'month' => $arrayMonth);
                } else if ($reportTypeID == 'ADCSM') { //Asset Depreciation Category Summary Monthly
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'year', 'month'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->assetDepreciationCategorySummaryMonthlyQRY($request);

                    $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

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

                    return array('reportData' => $output, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces, 'month' => $arrayMonth);
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

                $output = $this->getAssetRegisterDetail($request);

                $outputArr = [];

          /*      if ($request->excelType == 1) {
                    if ($output) {
                        foreach ($output as $val) {
                            if($val->groupTO==1){
                                $outputArr[$val->groupTO][$val->groupbydesc][$val->financeCatDescription][] = (array)$val;
                            }else{
                                $outputArr[0][$val->financeCatDescription][] = (array)$val;
                            }

                        }
                    }

                } else {*/
                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->financeCatDescription][] = (array)$val;
                        }
                    }

              /*  }*/






                $x = 0;


                    if (!empty($outputArr)) {
                        $TotalCOSTUNIT = 0;
                        $TotaldepAmountLocal = 0;
                        $Totallocalnbv = 0;
                        $TotalcostUnitRpt = 0;
                        $TotaldepAmountRpt = 0;
                        $Totalrptnbv = 0;
                        foreach ($outputArr as $key => $val) {

                            $data[$x]['Cost GL'] = $key;
                            $data[$x]['Acc Dep GL'] = '';
                            $data[$x]['Type'] = '';

                            $data[$x]['Segment'] = '';
                            $data[$x]['category'] = '';
                            $data[$x]['FA Code'] = '';
                            $data[$x]['Grouped YN'] = '';
                            $data[$x]['Serial Number'] = '';
                            $data[$x]['Asset Description'] = '';
                            $data[$x]['DEP %'] = '';
                            $data[$x]['Date Aquired'] = '';
                            $data[$x]['Dep Start Date'] = '';
                            $data[$x]['Local Amount unitcost'] = '';
                            $data[$x]['Local Amount accDep'] = '';
                            $data[$x]['Local Amount net Value'] = '';
                            $data[$x]['Rpt Amount unit cost'] = '';
                            $data[$x]['Rpt Amount acc dep'] = '';
                            $data[$x]['Rpt Amount acc net value'] = '';

                            $x++;

                            $data[$x]['Cost GL'] = 'Cost GL';
                            $data[$x]['Acc Dep GL'] = 'Acc Dep GL';
                            $data[$x]['Type'] = 'Type';
                            $data[$x]['Segment'] = 'Segment';
                            $data[$x]['category'] = 'Finance Category';
                            $data[$x]['FA Code'] = 'FA Code';
                            $data[$x]['Grouped YN'] = 'Grouped FA Code';
                            $data[$x]['Serial Number'] = 'Serial Number';
                            $data[$x]['Asset Description'] = 'Asset Description';
                            $data[$x]['DEP %'] = 'DEP %';
                            $data[$x]['Date Aquired'] = 'Date Aquired';
                            $data[$x]['Dep Start Date'] = 'Dep Start Date';
                            $data[$x]['Local Amount unitcost'] = '';
                            $data[$x]['Local Amount accDep'] = '';
                            $data[$x]['Local Amount net Value'] = 'Local Amount';
                            $data[$x]['Rpt Amount unit cost'] = '';
                            $data[$x]['Rpt Amount acc dep'] = 'Rpt Amount';
                            $data[$x]['Rpt Amount acc net value'] = '';

                            $x++;

                            $data[$x]['Cost GL'] = '';
                            $data[$x]['Acc Dep GL'] = '';
                            $data[$x]['Type'] = '';
                            $data[$x]['Segment'] = '';
                            $data[$x]['category'] = '';
                            $data[$x]['FA Code'] = '';
                            $data[$x]['Grouped YN'] = '';
                            $data[$x]['Serial Number'] = '';
                            $data[$x]['Asset Description'] = '';
                            $data[$x]['DEP %'] = '';
                            $data[$x]['Date Aquired'] = '';
                            $data[$x]['Dep Start Date'] = '';

                            $data[$x]['Local Amount unitcost'] = 'Unit Cost';
                            $data[$x]['Local Amount accDep'] = 'AccDep Amount';
                            $data[$x]['Local Amount net Value'] = 'Net Book Value';
                            $data[$x]['Rpt Amount unit cost'] = 'Unit Cost';
                            $data[$x]['Rpt Amount acc dep'] = 'AccDep Amount';
                            $data[$x]['Rpt Amount acc net value'] = 'Net Book Value';

                            $x++;
                            $COSTUNIT = 0;
                            $depAmountLocal = 0;
                            $localnbv = 0;
                            $costUnitRpt = 0;
                            $depAmountRpt = 0;
                            $rptnbv = 0;


                            foreach ($outputArr[$key] as $value) {
                                $x++;
                                $COSTUNIT += $value['COSTUNIT'];
                                $depAmountLocal += $value['depAmountLocal'];
                                $localnbv += $value['localnbv'];
                                $costUnitRpt += $value['costUnitRpt'];
                                $depAmountRpt += $value['depAmountRpt'];
                                $rptnbv += $value['rptnbv'];

                                $TotalCOSTUNIT += $value['COSTUNIT'];
                                $TotaldepAmountLocal += $value['depAmountLocal'];
                                $Totallocalnbv += $value['localnbv'];
                                $TotalcostUnitRpt += $value['costUnitRpt'];
                                $TotaldepAmountRpt += $value['depAmountRpt'];
                                $Totalrptnbv += $value['rptnbv'];

                                $data[$x]['Cost GL'] = $value['COSTGLCODE'];
                                $data[$x]['Acc Dep GL'] = $value['ACCDEPGLCODE'];
                                $data[$x]['Type'] = $value['typeDes'];
                                $data[$x]['Segment'] = $value['ServiceLineDes'];
                                $data[$x]['category'] = $key;
                                $data[$x]['FA Code'] = $value['faCode'];
                                $data[$x]['Grouped YN'] = $value['groupbydesc'];
                                $data[$x]['Serial Number'] = $value['faUnitSerialNo'];
                                $data[$x]['Asset Description'] = $value['assetDescription'];
                                $data[$x]['DEP %'] = round($value['DEPpercentage'], 2);
                                $data[$x]['Date Aquired'] = \Helper::dateFormat($value['dateAQ']);
                                $data[$x]['Dep Start Date'] = \Helper::dateFormat($value['dateDEP']);

                                $data[$x]['Local Amount unitcost'] = round($value['COSTUNIT'], 2);
                                $data[$x]['Local Amount accDep'] = round($value['depAmountLocal'], 2);
                                $data[$x]['Local Amount net Value'] = round($value['localnbv'], 2);
                                $data[$x]['Rpt Amount unit cost'] = round($value['costUnitRpt'], 2);
                                $data[$x]['Rpt Amount acc dep'] = round($value['depAmountRpt'], 2);
                                $data[$x]['Rpt Amount acc net value'] = round($value['rptnbv'], 2);

                            }

                            $x++;


                            $data[$x]['Cost GL'] = '';
                            $data[$x]['Acc Dep GL'] = '';
                            $data[$x]['Type'] = '';
                            $data[$x]['Segment'] = '';
                            $data[$x]['category'] = '';
                            $data[$x]['FA Code'] = '';
                            $data[$x]['Grouped YN'] = '';
                            $data[$x]['Serial Number'] = '';
                            $data[$x]['Asset Description'] = '';
                            $data[$x]['DEP %'] = '';
                            $data[$x]['Date Aquired'] = '';
                            $data[$x]['Dep Start Date'] = 'Sub Total';

                            $data[$x]['Local Amount unitcost'] = $COSTUNIT;
                            $data[$x]['Local Amount accDep'] = $depAmountLocal;
                            $data[$x]['Local Amount net Value'] = $localnbv;
                            $data[$x]['Rpt Amount unit cost'] = $costUnitRpt;
                            $data[$x]['Rpt Amount acc dep'] = $depAmountRpt;
                            $data[$x]['Rpt Amount acc net value'] = $rptnbv;

$x++;

                        }


                        $x++;

                        $data[$x]['Cost GL'] = '';
                        $data[$x]['Acc Dep GL'] = '';
                        $data[$x]['Type'] = '';
                        $data[$x]['Segment'] = '';
                        $data[$x]['category'] = '';
                        $data[$x]['FA Code'] = '';
                        $data[$x]['Grouped YN'] = '';
                        $data[$x]['Serial Number'] = '';
                        $data[$x]['Asset Description'] = '';
                        $data[$x]['DEP %'] = '';
                        $data[$x]['Date Aquired'] = '';
                        $data[$x]['Dep Start Date'] = 'Total';
                        $data[$x]['Local Amount unitcost'] = $TotalCOSTUNIT;
                        $data[$x]['Local Amount accDep'] = $TotaldepAmountLocal;
                        $data[$x]['Local Amount net Value'] = $Totallocalnbv;
                        $data[$x]['Rpt Amount unit cost'] = $TotalcostUnitRpt;
                        $data[$x]['Rpt Amount acc dep'] = $TotaldepAmountRpt;
                        $data[$x]['Rpt Amount acc net value'] = $Totalrptnbv;
                    }




                $csv = \Excel::create('payment_suppliers_by_year', function ($excel) use ($data) {
                    $excel->sheet('asset register', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', true, false);
                        $sheet->setAutoSize(true);
                        $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                    });

                    $lastrow = $excel->getActiveSheet()->getHighestRow();
                    $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                })->download($type);

                return $this->sendResponse(array(), 'successfully export');

                break;
            case 'AMAA': //Asset Additions
                $type = $request->type;
                $output = $this->getAssetAdditionsQRY($request);
                if ($output) {
                    $x = 0;
                    foreach ($output as $val) {
                        $data[$x]['Company ID'] = $val->companyID;
                        $data[$x]['Company Name'] = $val->CompanyName;
                        $data[$x]['Asset Category'] = $val->AssetCategory;
                        $data[$x]['Asset Type'] = $val->AssetType;
                        $data[$x]['Asset Code'] = $val->AssetCODE;
                        $data[$x]['Serial Number'] = $val->SerialNumber;
                        $data[$x]['Asset Description'] = $val->AssetDescription;
                        $data[$x]['DEP percentage'] = $val->DEPpercentage;
                        $data[$x]['Date Acquired'] = \Helper::dateFormat($val->DateAquired);
                        $data[$x]['GRV Code'] = $val->GRVCODE;
                        $data[$x]['PO Code'] = $val->POCODE;
                        $data[$x]['Service Line'] = $val->ServiceLineDes;
                        $data[$x]['Supplier'] = $val->Supplier;
                        $data[$x]['Asset Cost Local'] = $val->AssetCostLocal;
                        $data[$x]['Asset Cost Rpt'] = $val->AssetCostRpt;
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
            case 'AMAD': //Asset Disposal

                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'year', 'month'));
                $currency = \Helper::companyCurrency($request->companySystemID);
                $output = $this->getAssetDisposal($request);

                $decimalPlaceLocal = $currency->reportingcurrency->DecimalPlaces;
                $decimalPlaceRpt = $currency->reportingcurrency->DecimalPlaces;
                $data = array();
                $x = 0;
                foreach ($output as $val) {
                    $data[$x]['Company ID'] = $val->companyID;
                    $data[$x]['Company Name'] = $val->CompanyName;

                    $data[$x]['Disposal Date'] = \Helper::dateFormat($val->disposalDate);
                    $data[$x]['Doc.Code'] = $val->disposalDocumentCode;
                    $data[$x]['Narration'] = $val->narration;
                    $data[$x]['Category'] = $val->AssetCategory;
                    $data[$x]['Asset Code'] = $val->AssetCODE;
                    $data[$x]['Serial Number'] = $val->AssetSerialNumber;
                    $data[$x]['Asset Description'] = $val->AssetDescription;

                    $data[$x]['Asset Cost (Local)'] = round($val->AssetCostLocal, $decimalPlaceLocal);
                    $data[$x]['Asset Cost (Reporting)'] = round($val->AssetCostRPT, $decimalPlaceRpt);

                    $data[$x]['Accumulated Depreciation (Local)'] = round($val->AccumulatedDepreciationLocal, $decimalPlaceLocal);
                    $data[$x]['Accumulated Depreciation (Reporting)'] = round($val->AccumulatedDepreciationRPT, $decimalPlaceRpt);

                    $data[$x]['Net Book Value (Local)'] = round($val->NetBookVALUELocal, $decimalPlaceLocal);
                    $data[$x]['Net Book Value (Reporting)'] = round($val->NetBookVALUERPT, $decimalPlaceRpt);

                    $x++;
                }
                $csv = \Excel::create('asset_disposal', function ($excel) use ($data) {
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
            case 'AMADR': //Asset Depreciation Register
                $data = [];
                $reportTypeID = $request->reportTypeID;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'year', 'month'));
                if ($reportTypeID == 'ADRM') { //Asset Depreciation Register Monthly
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
                } else if ($reportTypeID == 'ADDM') { //Asset Depreciation Detail Monthly
                    $output = $this->assetDepreciationDetailMonthlyQRY($request);
                    $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Asset Code'] = $val->faCode;
                            $data[$x]['Asset Description'] = $val->assetDescription;
                            $data[$x]['Category'] = $val->Category;
                            $data[$x]['Cost Amount'] = $val->cost;
                            $data[$x]['Dep %'] = $val->DEPpercentage;
                            $data[$x]['Dep Amount ' . $arrayMonth[$request->month - 1]] = $val->currentMonthDepreciation;
                            $data[$x]['Opeining Dep'] = 0;
                            $data[$x]['Current Year Dep'] = $val->currentYearDepAmount;
                            $data[$x]['Accumilated Dep ' . $arrayMonth[$request->month - 1]] = $val->accumulatedDepreciation;
                            $data[$x]['Net Book Value ' . $arrayMonth[$request->month - 1]] = $val->netBookValue;
                            foreach ($arrayMonth as $val2) {
                                $data[$x][$val2] = $val->$val2;
                            }
                            $x++;
                        }
                    }
                } else if ($reportTypeID == 'ADDS') { //Depreciation Detail Summary
                    $output = $this->assetDepreciationDetailSummaryQRY($request);
                    $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');
                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Asset Code'] = $val->faCode;
                            $data[$x]['Asset Description'] = $val->assetDescription;
                            $data[$x]['Category'] = $val->Category;
                            $data[$x]['Cost Amount'] = $val->cost;
                            $data[$x]['Dep %'] = $val->DEPpercentage;
                            $data[$x]['Dep Amount ' . $arrayMonth[$request->month - 1]] = $val->currentMonthDepreciation;
                            $data[$x]['Opeining Dep'] = 0;
                            $data[$x]['Current Year Dep'] = $val->currentYearDepAmount;
                            $data[$x]['Accumilated Dep ' . $arrayMonth[$request->month - 1]] = $val->accumulatedDepreciation;
                            $data[$x]['Net Book Value ' . $arrayMonth[$request->month - 1]] = $val->netBookValue;

                            $x++;
                        }
                    }
                } else if ($reportTypeID == 'ADCS') { //Depreciation Category Summary
                    $output = $this->assetDepreciationCategorySummaryQRY($request);
                    $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');
                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Category'] = $val->Category;
                            $data[$x]['Cost Amount'] = $val->cost;
                            $data[$x]['Dep %'] = $val->DEPpercentage;
                            $data[$x]['Current Year Dep'] = $val->currentYearDepAmount;
                            $data[$x]['Accumilated Dep' . $arrayMonth[$request->month - 1]] = $val->accumulatedDepreciation;
                            $data[$x]['Net Book Value ' . $arrayMonth[$request->month - 1]] = $val->netBookValue;
                            $x++;
                        }
                    }
                } else if ($reportTypeID == 'ADCSM') { //Depreciation Category Monthly Summary
                    $output = $this->assetDepreciationCategorySummaryMonthlyQRY($request);
                    $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');
                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Category'] = $val->Category;
                            $data[$x]['Cost Amount'] = $val->cost;
                            $data[$x]['Dep %'] = $val->DEPpercentage;
                            $data[$x]['Current Year Dep'] = $val->currentYearDepAmount;
                            $data[$x]['Accumilated Dep' . $arrayMonth[$request->month - 1]] = $val->accumulatedDepreciation;
                            $data[$x]['Net Book Value ' . $arrayMonth[$request->month - 1]] = $val->netBookValue;
                            foreach ($arrayMonth as $val2) {
                                $data[$x][$val2] = $val->$val2;
                            }
                            $x++;
                        }
                    }
                }
                $csv = \Excel::create('asset_depreciation', function ($excel) use ($data) {
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

    // Asset Additions Query
    function getAssetAdditionsQRY($request)
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

        $query = 'SELECT
                    erp_fa_asset_master.companySystemID,
                    erp_fa_asset_master.companyID,
                    companymaster.CompanyName,
                    erp_fa_asset_master.faID,
                    erp_fa_financecategory.financeCatDescription AS AssetCategory,
                    erp_fa_assettype.typeDes AS AssetType,
                    erp_fa_asset_master.faCode AS AssetCODE,
                    erp_fa_asset_master.faUnitSerialNo AS SerialNumber,
                    erp_fa_asset_master.assetDescription AS AssetDescription,
                    ROUND(erp_fa_asset_master.DEPpercentage, 0) AS DEPpercentage,
                    serviceline.ServiceLineDes AS ServiceLineDes,
                    erp_fa_asset_master.dateAQ AS DateAquired,
                    erp_fa_asset_master.docOrigin AS GRVCODE,
                    erp_purchaseordermaster.purchaseOrderCode AS POCODE,
                    erp_fa_asset_master.serviceLineCode AS ServiceLine,
                    erp_fa_asset_master.MANUFACTURE AS Supplier,
                    erp_fa_assetcost.localAmount AS AssetCostLocal,
                    erp_fa_assetcost.rptAmount AS AssetCostRpt,
                    locCur.CurrencyCode as localCurrency,
                    locCur.DecimalPlaces as localCurrencyDeci,
                    repCur.CurrencyCode as reportCurrency,
                    repCur.DecimalPlaces as reportCurrencyDeci
                FROM
                    erp_fa_asset_master
                LEFT JOIN erp_fa_assettype ON erp_fa_assettype.typeID = erp_fa_asset_master.assetType
                LEFT JOIN erp_fa_assetcost ON erp_fa_asset_master.faID = erp_fa_assetcost.faID
                LEFT JOIN erp_fa_financecategory ON erp_fa_asset_master.AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID
                LEFT JOIN erp_grvmaster ON erp_fa_asset_master.docOriginSystemCode = erp_grvmaster.grvAutoID
                LEFT JOIN erp_grvdetails ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID
                LEFT JOIN erp_purchaseordermaster ON erp_grvdetails.purchaseOrderMastertID = erp_purchaseordermaster.purchaseOrderID
                LEFT JOIN companymaster ON companymaster.companySystemID = erp_fa_asset_master.companySystemID
                LEFT JOIN serviceline ON erp_fa_asset_master.serviceLineSystemID = serviceline.serviceLineSystemID
                LEFT JOIN currencymaster as locCur ON locCur.currencyID = companymaster.localCurrencyID
                LEFT JOIN currencymaster as repCur ON repCur.currencyID = companymaster.reportingCurrency
                WHERE erp_fa_asset_master.companySystemID IN (' . join(',', $companyID) . ')
                    AND DATE(erp_fa_asset_master.dateAQ) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                AND erp_fa_asset_master.approved = -1
                GROUP BY
                    erp_fa_asset_master.companySystemID,
                    erp_fa_asset_master.faID ORDER BY erp_fa_asset_master.companyID ASC';
        //echo $query;
        //exit();
        $output = \DB::select($query);
        return $output;
    }

    function getAssetDisposal($request)
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

        //DB::enableQueryLog();

        $qry = 'SELECT
                    erp_fa_asset_disposalmaster.companyID,
                    companymaster.CompanyName,
                    erp_fa_asset_disposalmaster.disposalDocumentDate AS disposalDate,
                    erp_fa_asset_disposalmaster.disposalDocumentCode,
                    erp_fa_asset_disposalmaster.disposalType,
                    erp_fa_asset_disposalmaster.narration,
                    erp_fa_financecategory.financeCatDescription AS AssetCategory,
                    erp_fa_asset_disposaldetail.faCode AS AssetCODE,
                    erp_fa_asset_disposaldetail.faUnitSerialNo AS AssetSerialNumber,
                    erp_fa_asset_disposaldetail.assetDescription AS AssetDescription,
                    erp_fa_asset_disposaldetail.COSTUNIT AS AssetCostLocal,
                    erp_fa_asset_disposaldetail.COSTUNIT - erp_fa_asset_disposaldetail.netBookValueLocal AS AccumulatedDepreciationLocal,
                    erp_fa_asset_disposaldetail.netBookValueLocal AS NetBookVALUELocal,
                    erp_fa_asset_disposaldetail.costUnitRpt AS AssetCostRPT,
                    erp_fa_asset_disposaldetail.depAmountRpt AS AccumulatedDepreciationRPT,
                    erp_fa_asset_disposaldetail.netBookValueRpt AS NetBookVALUERPT
                FROM
                    erp_fa_asset_disposaldetail
                    INNER JOIN erp_fa_asset_master ON erp_fa_asset_disposaldetail.faID = erp_fa_asset_master.faID
                    INNER JOIN erp_fa_financecategory ON erp_fa_asset_master.AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID
                    INNER JOIN erp_fa_asset_disposalmaster ON erp_fa_asset_disposaldetail.assetdisposalMasterAutoID = erp_fa_asset_disposalmaster.assetdisposalMasterAutoID 
                    INNER JOIN companymaster ON erp_fa_asset_disposalmaster.companySystemID = companymaster.companySystemID
                WHERE
                    DATE(erp_fa_asset_disposalmaster.disposalDocumentDate)
                    BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                    AND erp_fa_asset_disposalmaster.companySystemID IN (' . join(',', $companyID) . ')
                    AND erp_fa_asset_disposalmaster.approvedYN =- 1;';

        $output = \DB::select($qry);

        return $output;
    }

    public function assetDepreciationRegisterMonthlyQRY($request)
    {

        $year = $request->year;
        $month = sprintf("%02d", $request->month);

        $firstDayOfMonth = new Carbon($year . '-' . $month . '-01');
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

        $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

        $monthField = '';
        if ($currency == 2) {
            if (!empty($arrayMonth)) { /* month wise in query*/
                foreach ($arrayMonth as $key => $val) {
                    $monthField .= "if(MONTH(erp_fa_depmaster.depDate) = " . ($key + 1) . ",round(erp_fa_assetdepreciationperiods.depAmountLocal, 2),0) as `" . $val . "`,";
                }
            }
        } else {
            if (!empty($arrayMonth)) { /* month wise in query*/
                foreach ($arrayMonth as $key => $val) {
                    $monthField .= "if(MONTH(erp_fa_depmaster.depDate) = " . ($key + 1) . ",round(erp_fa_assetdepreciationperiods.depAmountRpt, 2),0) as `" . $val . "`,";
                }
            }
        }

        $sql = 'SELECT
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
    ' . $monthField . '
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
	AND YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ' -- year which is selected in filter option
	
	) AS assetDepreciation ON assetDepreciation.companySystemID = erp_fa_asset_master.companySystemID 
	AND assetDepreciation.faID = erp_fa_asset_master.faID
WHERE
	erp_fa_asset_master.companySystemID IN (' . join(',', $companyID) . ')
	AND DATE(erp_fa_asset_master.dateAQ) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
	AND erp_fa_asset_master.approved =- 1 
AND
IF
	(
		erp_fa_asset_master.DIPOSED =- 1,
	IF
		(
			( IF ( erp_fa_asset_master.disposedDate IS NULL, "1990-01-01", erp_fa_asset_master.disposedDate ) ) < "' . $lastDayOfMonth . '",
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

    public function assetDepreciationDetailSummaryQRY($request)
    {

        $year = $request->year;
        $month = sprintf("%02d", $request->month);

        $firstDayOfMonth = new Carbon($year . '-' . $month . '-01');
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
            $cost = "SUM(round( erp_fa_asset_master.COSTUNIT, 3 )) AS cost";
            $accumilatedAmount = "SUM(IF
	( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal ) -
IF
	( disposal.disposalAmountLocal IS NULL, 0, disposal.disposalAmountLocal )) AS accumulatedDepreciation";
            $netBookValue = "SUM(round( erp_fa_asset_master.COSTUNIT, 3 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal ) )) AS netBookValue";
            $currentYearDep = "SUM(IF
	( DepreciationTotalCurPrevYear.currentYearDepAmountLocal IS NULL, 0, DepreciationTotalCurPrevYear.currentYearDepAmountLocal )) AS currentYearDepAmount";
        } else {
            $currentMonthDep = "sum( ( IF ( assetDepreciation.runningMonthDepreciationRpt IS NULL, 0, assetDepreciation.runningMonthDepreciationRpt ) ) ) AS currentMonthDepreciation";
            $cost = "SUM(round( erp_fa_asset_master.costUnitRpt, 2 )) AS cost";
            $accumilatedAmount = "SUM(IF
	( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt ) -
IF
	( disposal.disposalAmountRpt IS NULL, 0, disposal.disposalAmountRpt )) AS accumulatedDepreciation";
            $netBookValue = "SUM(round( erp_fa_asset_master.costUnitRpt, 2 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt ) )) AS netBookValue";
            $currentYearDep = "SUM(IF
	( DepreciationTotalCurPrevYear.currentYearDepAmountRpt IS NULL, 0, DepreciationTotalCurPrevYear.currentYearDepAmountRpt )) AS currentYearDepAmount";
        }

        $sql = 'SELECT
	erp_fa_asset_master.companySystemID,
	erp_fa_asset_master.companyID,
	erp_fa_asset_master.faID,
	erp_fa_asset_master.faCode,
	erp_fa_asset_master.assetDescription,
	erp_fa_financecategory.financeCatDescription AS AuditCategory,
	erp_fa_category.catDescription Category,
	' . $currentMonthDep . ',
	' . $cost . ',
	' . $accumilatedAmount . ',
	' . $netBookValue . ',
	' . $currentYearDep . ',
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
      IF
	( MONTH ( erp_fa_depmaster.depDate ) = ' . $month . ', erp_fa_assetdepreciationperiods.depAmountLocal, 0 ) AS runningMonthDepreciationLocal,-- 7 is the month which is selected in the filter
IF
	( MONTH ( erp_fa_depmaster.depDate ) = ' . $month . ', erp_fa_assetdepreciationperiods.depAmountRpt, 0 ) AS runningMonthDepreciationRpt -- 7 is the month which is selected in the filter
FROM
	erp_fa_depmaster
	INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
WHERE
	erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ') 
	AND YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ' -- year which is selected in filter option
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
		AND DATE( erp_fa_depmaster.depDate) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
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
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ', round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ), 0 ) ) AS currentYearDepAmountLocal,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ', round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ), 0 ) ) AS currentYearDepAmountRpt,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . ($year - 1) . ', 0, round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) ) ) AS PreviousYearDepAmountLocal,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . ($year - 1) . ', 0, round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) ) ) AS PreviousYearDepAmountRpt 
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
		AND DATE( erp_fa_asset_disposalmaster.disposalDocumentDate) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
		
	GROUP BY
		erp_fa_asset_disposalmaster.companySystemID,
		erp_fa_asset_disposaldetail.faID 
	) AS disposal ON disposal.companySystemID = erp_fa_asset_master.companySystemID 
WHERE
	erp_fa_asset_master.companySystemID IN (' . join(',', $companyID) . ')
	AND DATE(erp_fa_asset_master.dateAQ) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
	
	AND erp_fa_asset_master.approved =- 1 
AND
IF
	(
		erp_fa_asset_master.DIPOSED =- 1,
	IF
		(
			( IF ( erp_fa_asset_master.disposedDate IS NULL, "1990-01-01", erp_fa_asset_master.disposedDate ) ) < "' . $lastDayOfMonth . '",
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

    public function assetDepreciationDetailMonthlyQRY($request)
    {

        $year = $request->year;
        $month = sprintf("%02d", $request->month);

        $firstDayOfMonth = new Carbon($year . '-' . $month . '-01');
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

        $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

        $currentMonthDep = "";
        $cost = "";
        $accumilatedAmount = "";
        $netBookValue = "";
        $currentYearDep = "";
        $monthField = "";
        if ($currency == 2) {
            $currentMonthDep = "sum( ( IF ( assetDepreciation.runningMonthDepreciationLocal IS NULL, 0, assetDepreciation.runningMonthDepreciationLocal ) ) ) AS currentMonthDepreciation";
            $cost = "SUM(round( erp_fa_asset_master.COSTUNIT, 3 )) AS cost";
            $accumilatedAmount = "SUM(IF
	( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal ) -
IF
	( disposal.disposalAmountLocal IS NULL, 0, disposal.disposalAmountLocal )) AS accumulatedDepreciation";
            $netBookValue = "SUM(round( erp_fa_asset_master.COSTUNIT, 3 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal ) )) AS netBookValue";
            $currentYearDep = "SUM(IF
	( DepreciationTotalCurPrevYear.currentYearDepAmountLocal IS NULL, 0, DepreciationTotalCurPrevYear.currentYearDepAmountLocal )) AS currentYearDepAmount";
            foreach ($arrayMonth as $key => $val) {
                $monthField .= "if(MONTH(erp_fa_depmaster.depDate) = " . ($key + 1) . ",round(erp_fa_assetdepreciationperiods.depAmountLocal, 2),0) as `" . $val . "`,";
            }
        } else {
            $currentMonthDep = "sum( ( IF ( assetDepreciation.runningMonthDepreciationRpt IS NULL, 0, assetDepreciation.runningMonthDepreciationRpt ) ) ) AS currentMonthDepreciation";
            $cost = "SUM(round( erp_fa_asset_master.costUnitRpt, 2 )) AS cost";
            $accumilatedAmount = "SUM(IF
	( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt ) -
IF
	( disposal.disposalAmountRpt IS NULL, 0, disposal.disposalAmountRpt )) AS accumulatedDepreciation";
            $netBookValue = "SUM(round( erp_fa_asset_master.costUnitRpt, 2 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt ) )) AS netBookValue";
            $currentYearDep = "SUM(IF
	( DepreciationTotalCurPrevYear.currentYearDepAmountRpt IS NULL, 0, DepreciationTotalCurPrevYear.currentYearDepAmountRpt )) AS currentYearDepAmount";
            foreach ($arrayMonth as $key => $val) {
                $monthField .= "if(MONTH(erp_fa_depmaster.depDate) = " . ($key + 1) . ",round(erp_fa_assetdepreciationperiods.depAmountRpt, 2),0) as `" . $val . "`,";
            }
        }

        $sql = 'SELECT
	erp_fa_asset_master.companySystemID,
	erp_fa_asset_master.companyID,
	erp_fa_asset_master.faID,
	erp_fa_asset_master.faCode,
	erp_fa_asset_master.assetDescription,
	erp_fa_financecategory.financeCatDescription AS AuditCategory,
	erp_fa_category.catDescription Category,
	' . $currentMonthDep . ',
	' . $cost . ',
	' . $accumilatedAmount . ',
	' . $netBookValue . ',
	' . $currentYearDep . ',
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
	' . $monthField . '
     IF
	( MONTH ( erp_fa_depmaster.depDate ) = ' . $month . ', erp_fa_assetdepreciationperiods.depAmountLocal, 0 ) AS runningMonthDepreciationLocal,-- 7 is the month which is selected in the filter
IF
	( MONTH ( erp_fa_depmaster.depDate ) = ' . $month . ', erp_fa_assetdepreciationperiods.depAmountRpt, 0 ) AS runningMonthDepreciationRpt -- 7 is the month which is selected in the filter
FROM
	erp_fa_depmaster
	INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
WHERE
	erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ') 
	AND YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ' -- year which is selected in filter option
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
		AND DATE( erp_fa_depmaster.depDate) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
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
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ', round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ), 0 ) ) AS currentYearDepAmountLocal,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ', round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ), 0 ) ) AS currentYearDepAmountRpt,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . ($year - 1) . ', 0, round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) ) ) AS PreviousYearDepAmountLocal,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . ($year - 1) . ', 0, round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) ) ) AS PreviousYearDepAmountRpt 
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
		AND DATE( erp_fa_asset_disposalmaster.disposalDocumentDate) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
		
	GROUP BY
		erp_fa_asset_disposalmaster.companySystemID,
		erp_fa_asset_disposaldetail.faID 
	) AS disposal ON disposal.companySystemID = erp_fa_asset_master.companySystemID 
WHERE
	erp_fa_asset_master.companySystemID IN (' . join(',', $companyID) . ')
	AND DATE(erp_fa_asset_master.dateAQ) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
	
	AND erp_fa_asset_master.approved =- 1 
AND
IF
	(
		erp_fa_asset_master.DIPOSED =- 1,
	IF
		(
			( IF ( erp_fa_asset_master.disposedDate IS NULL, "1990-01-01", erp_fa_asset_master.disposedDate ) ) < "' . $lastDayOfMonth . '",
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

    public function assetDepreciationCategorySummaryQRY($request)
    {

        $year = $request->year;
        $month = sprintf("%02d", $request->month);

        $firstDayOfMonth = new Carbon($year . '-' . $month . '-01');
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
            $cost = "SUM(round( erp_fa_asset_master.COSTUNIT, 3 )) AS cost";
            $accumilatedAmount = "SUM(IF
	( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal ) -
IF
	( disposal.disposalAmountLocal IS NULL, 0, disposal.disposalAmountLocal )) AS accumulatedDepreciation";
            $netBookValue = "SUM(round( erp_fa_asset_master.COSTUNIT, 3 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal ) )) AS netBookValue";
            $currentYearDep = "SUM(IF
	( DepreciationTotalCurPrevYear.currentYearDepAmountLocal IS NULL, 0, DepreciationTotalCurPrevYear.currentYearDepAmountLocal )) AS currentYearDepAmount";
        } else {
            $currentMonthDep = "sum( ( IF ( assetDepreciation.runningMonthDepreciationRpt IS NULL, 0, assetDepreciation.runningMonthDepreciationRpt ) ) ) AS currentMonthDepreciation";
            $cost = "SUM(round( erp_fa_asset_master.costUnitRpt, 2 )) AS cost";
            $accumilatedAmount = "SUM(IF
	( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt ) -
IF
	( disposal.disposalAmountRpt IS NULL, 0, disposal.disposalAmountRpt )) AS accumulatedDepreciation";
            $netBookValue = "SUM(round( erp_fa_asset_master.costUnitRpt, 2 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt ) )) AS netBookValue";
            $currentYearDep = "SUM(IF
	( DepreciationTotalCurPrevYear.currentYearDepAmountRpt IS NULL, 0, DepreciationTotalCurPrevYear.currentYearDepAmountRpt )) AS currentYearDepAmount";
        }

        $sql = 'SELECT
	erp_fa_asset_master.companySystemID,
	erp_fa_asset_master.companyID,
	erp_fa_asset_master.faID,
	erp_fa_asset_master.faCode,
	erp_fa_asset_master.assetDescription,
	erp_fa_financecategory.financeCatDescription AS AuditCategory,
	erp_fa_category.catDescription Category,
	' . $currentMonthDep . ',
	' . $cost . ',
	' . $accumilatedAmount . ',
	' . $netBookValue . ',
	' . $currentYearDep . ',
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
      IF
	( MONTH ( erp_fa_depmaster.depDate ) = ' . $month . ', erp_fa_assetdepreciationperiods.depAmountLocal, 0 ) AS runningMonthDepreciationLocal,-- 7 is the month which is selected in the filter
IF
	( MONTH ( erp_fa_depmaster.depDate ) = ' . $month . ', erp_fa_assetdepreciationperiods.depAmountRpt, 0 ) AS runningMonthDepreciationRpt -- 7 is the month which is selected in the filter
FROM
	erp_fa_depmaster
	INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
WHERE
	erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ') 
	AND YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ' -- year which is selected in filter option
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
		AND DATE( erp_fa_depmaster.depDate) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
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
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ', round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ), 0 ) ) AS currentYearDepAmountLocal,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ', round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ), 0 ) ) AS currentYearDepAmountRpt,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . ($year - 1) . ', 0, round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) ) ) AS PreviousYearDepAmountLocal,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . ($year - 1) . ', 0, round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) ) ) AS PreviousYearDepAmountRpt 
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
		AND DATE( erp_fa_asset_disposalmaster.disposalDocumentDate) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
		
	GROUP BY
		erp_fa_asset_disposalmaster.companySystemID,
		erp_fa_asset_disposaldetail.faID 
	) AS disposal ON disposal.companySystemID = erp_fa_asset_master.companySystemID 
WHERE
	erp_fa_asset_master.companySystemID IN (' . join(',', $companyID) . ')
	AND DATE(erp_fa_asset_master.dateAQ) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
	
	AND erp_fa_asset_master.approved =- 1 
AND
IF
	(
		erp_fa_asset_master.DIPOSED =- 1,
	IF
		(
			( IF ( erp_fa_asset_master.disposedDate IS NULL, "1990-01-01", erp_fa_asset_master.disposedDate ) ) < "' . $lastDayOfMonth . '",
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

    public function assetDepreciationCategorySummaryMonthlyQRY($request)
    {

        $year = $request->year;
        $month = sprintf("%02d", $request->month);

        $firstDayOfMonth = new Carbon($year . '-' . $month . '-01');
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

        $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

        $currentMonthDep = "";
        $cost = "";
        $accumilatedAmount = "";
        $netBookValue = "";
        $currentYearDep = "";
        $monthField = "";
        if ($currency == 2) {
            $currentMonthDep = "sum( ( IF ( assetDepreciation.runningMonthDepreciationLocal IS NULL, 0, assetDepreciation.runningMonthDepreciationLocal ) ) ) AS currentMonthDepreciation";
            $cost = "SUM(round( erp_fa_asset_master.COSTUNIT, 3 )) AS cost";
            $accumilatedAmount = "SUM(IF
	( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal ) -
IF
	( disposal.disposalAmountLocal IS NULL, 0, disposal.disposalAmountLocal )) AS accumulatedDepreciation";
            $netBookValue = "SUM(round( erp_fa_asset_master.COSTUNIT, 3 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal ) )) AS netBookValue";
            $currentYearDep = "SUM(IF
	( DepreciationTotalCurPrevYear.currentYearDepAmountLocal IS NULL, 0, DepreciationTotalCurPrevYear.currentYearDepAmountLocal )) AS currentYearDepAmount";
            foreach ($arrayMonth as $key => $val) {
                $monthField .= "if(MONTH(erp_fa_depmaster.depDate) = " . ($key + 1) . ",round(erp_fa_assetdepreciationperiods.depAmountLocal, 2),0) as `" . $val . "`,";
            }
        } else {
            $currentMonthDep = "sum( ( IF ( assetDepreciation.runningMonthDepreciationRpt IS NULL, 0, assetDepreciation.runningMonthDepreciationRpt ) ) ) AS currentMonthDepreciation";
            $cost = "SUM(round( erp_fa_asset_master.costUnitRpt, 2 )) AS cost";
            $accumilatedAmount = "SUM(IF
	( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt ) -
IF
	( disposal.disposalAmountRpt IS NULL, 0, disposal.disposalAmountRpt )) AS accumulatedDepreciation";
            $netBookValue = "SUM(round( erp_fa_asset_master.costUnitRpt, 2 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt ) )) AS netBookValue";
            $currentYearDep = "SUM(IF
	( DepreciationTotalCurPrevYear.currentYearDepAmountRpt IS NULL, 0, DepreciationTotalCurPrevYear.currentYearDepAmountRpt )) AS currentYearDepAmount";
            foreach ($arrayMonth as $key => $val) {
                $monthField .= "if(MONTH(erp_fa_depmaster.depDate) = " . ($key + 1) . ",round(erp_fa_assetdepreciationperiods.depAmountRpt, 2),0) as `" . $val . "`,";
            }
        }

        $sql = 'SELECT
	erp_fa_asset_master.companySystemID,
	erp_fa_asset_master.companyID,
	erp_fa_asset_master.faID,
	erp_fa_asset_master.faCode,
	erp_fa_asset_master.assetDescription,
	erp_fa_financecategory.financeCatDescription AS AuditCategory,
	erp_fa_category.catDescription Category,
	' . $currentMonthDep . ',
	' . $cost . ',
	' . $accumilatedAmount . ',
	' . $netBookValue . ',
	' . $currentYearDep . ',
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
	' . $monthField . '
      IF
	( MONTH ( erp_fa_depmaster.depDate ) = ' . $month . ', erp_fa_assetdepreciationperiods.depAmountLocal, 0 ) AS runningMonthDepreciationLocal,-- 7 is the month which is selected in the filter
IF
	( MONTH ( erp_fa_depmaster.depDate ) = ' . $month . ', erp_fa_assetdepreciationperiods.depAmountRpt, 0 ) AS runningMonthDepreciationRpt -- 7 is the month which is selected in the filter
FROM
	erp_fa_depmaster
	INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
WHERE
	erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ') 
	AND YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ' -- year which is selected in filter option -- year which is selected in filter option
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
		AND DATE( erp_fa_depmaster.depDate) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
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
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ', round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ), 0 ) ) AS currentYearDepAmountLocal,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ', round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ), 0 ) ) AS currentYearDepAmountRpt,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . ($year - 1) . ', 0, round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) ) ) AS PreviousYearDepAmountLocal,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . ($year - 1) . ', 0, round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) ) ) AS PreviousYearDepAmountRpt 
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
		AND DATE( erp_fa_asset_disposalmaster.disposalDocumentDate) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
		
	GROUP BY
		erp_fa_asset_disposalmaster.companySystemID,
		erp_fa_asset_disposaldetail.faID 
	) AS disposal ON disposal.companySystemID = erp_fa_asset_master.companySystemID 
WHERE
	erp_fa_asset_master.companySystemID IN (' . join(',', $companyID) . ')
	AND DATE(erp_fa_asset_master.dateAQ) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
	
	AND erp_fa_asset_master.approved =- 1 
AND
IF
	(
		erp_fa_asset_master.DIPOSED =- 1,
	IF
		(
			( IF ( erp_fa_asset_master.disposedDate IS NULL, "1990-01-01", erp_fa_asset_master.disposedDate ) ) < "' . $lastDayOfMonth . '",
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

    public function generateAssetDetailDrilldown(Request $request)
    {

        $typeID = $request->typeID;
        $asOfDate = (new Carbon($request->fromDate))->format('Y-m-d');
        $assetCategory = collect($request->assetCategory)->pluck('faFinanceCatID')->toArray();
        $assetCategory = join(',', $assetCategory);
        $faID = $request->faID;
        $input = $request->all();





        $qry="SELECT
	groupTO,
	faUnitSerialNo,
	erp_fa_asset_master.faID,
	erp_fa_assettype.typeDes,
	erp_fa_financecategory.financeCatDescription,
	erp_fa_asset_master.COSTGLCODE,
	erp_fa_asset_master.ACCDEPGLCODE,
	assetType,
	serviceline.ServiceLineDes,
	erp_fa_asset_master.serviceLineCode,
	docOrigin,
	AUDITCATOGARY,
	faCode,
	assetDescription,
	DEPpercentage,
	dateAQ,
	dateDEP,
	COSTUNIT,
	IFNULL( depAmountLocal, 0 ) AS depAmountLocal,
	COSTUNIT - IFNULL( depAmountLocal, 0 ) AS localnbv,
	costUnitRpt,
	IFNULL( depAmountRpt, 0 ) AS depAmountRpt,
	costUnitRpt - IFNULL( depAmountRpt, 0 ) AS rptnbv 
FROM
	erp_fa_asset_master
	LEFT JOIN (
	SELECT
		faID,
		erp_fa_assetdepreciationperiods.depMasterAutoID,
		sum( erp_fa_assetdepreciationperiods.depAmountLocal ) AS depAmountLocal,
		sum( erp_fa_assetdepreciationperiods.depAmountRpt ) AS depAmountRpt 
	FROM
		erp_fa_assetdepreciationperiods
		INNER JOIN erp_fa_depmaster ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
	WHERE
		erp_fa_depmaster.approved =- 1 
	GROUP BY
		faID 
	) t ON erp_fa_asset_master.faID = t.faID
	INNER JOIN erp_fa_assettype ON erp_fa_assettype.typeID = erp_fa_asset_master.assetType
	INNER JOIN erp_fa_financecategory ON AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID
	INNER JOIN serviceline ON serviceline.ServiceLineCode = erp_fa_asset_master.serviceLineCode 
WHERE
	erp_fa_asset_master.companySystemID = $request->companySystemID 
	AND erp_fa_asset_master.dateAQ <= '$asOfDate' AND assetType = $typeID AND ( disposedDate IS NULL OR disposedDate > '$asOfDate' 
		OR DIPOSED = - 1 ) AND
	(erp_fa_asset_master.faID = $faID OR 	erp_fa_asset_master.groupTO = $faID )
	
	
	
	
";



        $output = \DB::select($qry);

        $outputArr = [];


        $COSTUNIT = 0;
        $costUnitRpt = 0;
        $depAmountLocal = 0;
        $depAmountRpt = 0;
        $localnbv = 0;
        $rptnbv = 0;
        if ($output) {
            foreach ($output as $val) {
                $localnbv += $val->localnbv;
                $COSTUNIT += $val->COSTUNIT;
                $costUnitRpt += $val->costUnitRpt;
                $depAmountRpt += $val->depAmountRpt;
                $depAmountLocal += $val->depAmountLocal;
                $rptnbv += $val->rptnbv;
                $outputArr[$val->financeCatDescription][] = $val;
            }
        }


        return array('reportData' => $outputArr, 'localnbv' => $localnbv, 'rptnbv' => $rptnbv, 'COSTUNIT' => $COSTUNIT, 'costUnitRpt' => $costUnitRpt, 'depAmountLocal' => $depAmountLocal, 'depAmountRpt' => $depAmountRpt);


    }


    function getAssetRegisterDetail($request)
    {
        $typeID = $request->typeID;
        $asOfDate = (new Carbon($request->fromDate))->format('Y-m-d');
        $assetCategory = collect($request->assetCategory)->pluck('faFinanceCatID')->toArray();
        $assetCategory = join(',', $assetCategory);
        $searchText = $request->searchText;


        $where="";
        if($searchText !=''){
            $searchText = str_replace("\\", "\\\\", $searchText);
            $where=" AND ( assetGroup.faCode LIKE '%$searchText%' OR erp_fa_asset_master.assetDescription LIKE '%$searchText%' OR  
            erp_fa_asset_master.faCode LIKE '%$searchText%' )  ";
        }


        if ($request->excelType == 1) {

            $qry="SELECT
IF(groupTO,1,0) as groupTO,
	assetGroup.assetDescription as groupbydesc,
	faUnitSerialNo,
	erp_fa_asset_master.faID,
	erp_fa_assettype.typeDes,
	erp_fa_financecategory.financeCatDescription,
	erp_fa_asset_master.COSTGLCODE,
	erp_fa_asset_master.ACCDEPGLCODE,
	assetType,
	serviceline.ServiceLineDes,
	erp_fa_asset_master.serviceLineCode,
	docOrigin,
	AUDITCATOGARY,
	faCode,
	erp_fa_asset_master.assetDescription,
	DEPpercentage,
	dateAQ,
	dateDEP,
	COSTUNIT,
	IFNULL( depAmountLocal, 0 ) AS depAmountLocal,
	COSTUNIT - IFNULL( depAmountLocal, 0 ) AS localnbv,
	costUnitRpt,
	IFNULL( depAmountRpt, 0 ) AS depAmountRpt,
	costUnitRpt - IFNULL( depAmountRpt, 0 ) AS rptnbv 
FROM
	erp_fa_asset_master
	LEFT JOIN (
	SELECT
		faID,
		erp_fa_assetdepreciationperiods.depMasterAutoID,
		sum( erp_fa_assetdepreciationperiods.depAmountLocal ) AS depAmountLocal,
		sum( erp_fa_assetdepreciationperiods.depAmountRpt ) AS depAmountRpt 
	FROM
		erp_fa_assetdepreciationperiods
		INNER JOIN erp_fa_depmaster ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
	WHERE
		erp_fa_depmaster.approved =- 1 
	GROUP BY
		faID 
	) t ON erp_fa_asset_master.faID = t.faID
	INNER JOIN erp_fa_assettype ON erp_fa_assettype.typeID = erp_fa_asset_master.assetType
	INNER JOIN erp_fa_financecategory ON AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID
	INNER JOIN serviceline ON serviceline.ServiceLineCode = erp_fa_asset_master.serviceLineCode
LEFT JOIN (SELECT assetDescription , faID FROM erp_fa_asset_master WHERE erp_fa_asset_master.companySystemID = 31   )	 assetGroup ON erp_fa_asset_master.groupTO= assetGroup.faID
WHERE
	erp_fa_asset_master.companySystemID = $request->companySystemID
	AND erp_fa_asset_master.dateAQ <= '$asOfDate' AND assetType = $typeID AND ( disposedDate IS NULL OR disposedDate > '$asOfDate'
	OR DIPOSED = - 1 
	)";




        } else {
            $qry = "SELECT groupTO,faUnitSerialNo,faID, erp_fa_assettype.typeDes, erp_fa_financecategory.financeCatDescription, final.COSTGLCODE, final.ACCDEPGLCODE, assetType, serviceline.ServiceLineDes, final.serviceLineCode, docOrigin, AUDITCATOGARY, faCode, assetDescription, DEPpercentage, dateAQ, dateDEP, COSTUNIT, IFNULL(depAmountLocal,0) as depAmountLocal, COSTUNIT - IFNULL(depAmountLocal,0) as localnbv, costUnitRpt,
 IFNULL(depAmountRpt,0) as depAmountRpt , costUnitRpt - IFNULL(depAmountRpt,0) as rptnbv FROM ( SELECT 		t.groupTO,erp_fa_asset_master.faUnitSerialNo ,erp_fa_asset_master.faID, COSTGLCODE, ACCDEPGLCODE, assetType, erp_fa_asset_master.serviceLineCode, docOrigin, AUDITCATOGARY, erp_fa_asset_master.faCode, erp_fa_asset_master.assetDescription, DEPpercentage, dateAQ, dateDEP, IFNULL( t.COSTUNIT, 0 ) AS COSTUNIT, IFNULL( depAmountLocal, 0 ) AS depAmountLocal, IFNULL( t.costUnitRpt, 0 ) AS costUnitRpt, IFNULL( depAmountRpt, 0 ) AS depAmountRpt FROM ( SELECT groupTO, SUM( erp_fa_asset_master.COSTUNIT ) AS COSTUNIT, SUM( depAmountLocal ) AS depAmountLocal, SUM( costUnitRpt ) AS costUnitRpt, SUM( depAmountRpt ) AS depAmountRpt FROM erp_fa_asset_master LEFT JOIN ( SELECT faID, SUM( depAmountLocal ) AS depAmountLocal, SUM( depAmountRpt ) AS depAmountRpt FROM ( SELECT faID, erp_fa_assetdepreciationperiods.depMasterAutoID, sum( erp_fa_assetdepreciationperiods.depAmountLocal ) AS depAmountLocal, sum( erp_fa_assetdepreciationperiods.depAmountRpt ) AS depAmountRpt FROM erp_fa_assetdepreciationperiods INNER JOIN erp_fa_depmaster ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID WHERE erp_fa_depmaster.approved =- 1 GROUP BY faID ) t GROUP BY faID ) erp_fa_assetdepreciationperiods ON erp_fa_assetdepreciationperiods.faID = erp_fa_asset_master.faID WHERE companySystemID = $request->companySystemID AND AUDITCATOGARY IN($assetCategory) AND erp_fa_asset_master.dateAQ <= '$asOfDate' AND assetType = $typeID AND ( disposedDate IS NULL OR disposedDate > '$asOfDate' OR DIPOSED = - 1 ) AND groupTO IS NOT NULL GROUP BY groupTO ) t INNER JOIN erp_fa_asset_master ON erp_fa_asset_master.faID = t.groupTO
  
  UNION ALL SELECT groupTO,erp_fa_asset_master.faUnitSerialNo,
erp_fa_asset_master.faID, COSTGLCODE, ACCDEPGLCODE, assetType, erp_fa_asset_master.serviceLineCode, docOrigin, AUDITCATOGARY, erp_fa_asset_master.faCode, erp_fa_asset_master.assetDescription, DEPpercentage, dateAQ, dateDEP, ( erp_fa_asset_master.COSTUNIT ) AS COSTUNIT, ( depAmountLocal ) AS depAmountLocal, ( costUnitRpt ) AS costUnitRpt, ( depAmountRpt ) AS depAmountRpt FROM erp_fa_asset_master LEFT JOIN ( SELECT faID, IFNULL( SUM( depAmountLocal ), 0 ) AS depAmountLocal, IFNULL( SUM( depAmountRpt ), 0 ) AS depAmountRpt FROM ( SELECT faID, erp_fa_assetdepreciationperiods.depMasterAutoID, sum( erp_fa_assetdepreciationperiods.depAmountLocal ) AS depAmountLocal, sum( erp_fa_assetdepreciationperiods.depAmountRpt ) AS depAmountRpt FROM erp_fa_assetdepreciationperiods INNER JOIN erp_fa_depmaster ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID WHERE erp_fa_depmaster.approved =- 1 GROUP BY faID ) t GROUP BY faID ) erp_fa_assetdepreciationperiods ON erp_fa_assetdepreciationperiods.faID = erp_fa_asset_master.faID WHERE companySystemID = $request->companySystemID AND AUDITCATOGARY IN($assetCategory) AND erp_fa_asset_master.dateAQ <= '$asOfDate' AND assetType = $typeID AND ( disposedDate IS NULL OR disposedDate > '$asOfDate' OR DIPOSED = - 1 ) AND groupTO IS NULL ORDER BY dateDEP DESC ) final INNER JOIN serviceline ON serviceline.ServiceLineCode = final.serviceLineCode INNER JOIN erp_fa_financecategory ON AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID INNER JOIN erp_fa_assettype ON erp_fa_assettype.typeID = final.assetType";



        }

        $qry="
SELECT * FROM ( SELECT
IF(groupTO IS NOT  NULL ,groupTO , erp_fa_asset_master.faID ) as sortfaID,
    
  groupTO,
	assetGroup.faCode as groupbydesc,
	erp_fa_asset_master.faUnitSerialNo,
	erp_fa_asset_master.faID,
	erp_fa_assettype.typeDes,
	erp_fa_financecategory.financeCatDescription,
	erp_fa_asset_master.COSTGLCODE,
	erp_fa_asset_master.ACCDEPGLCODE,
	assetType,
	serviceline.ServiceLineDes,
	erp_fa_asset_master.serviceLineCode,
	docOrigin,
	AUDITCATOGARY,
	erp_fa_asset_master.faCode,
	erp_fa_asset_master.assetDescription,
	DEPpercentage,
	dateAQ,
	dateDEP,
	COSTUNIT,
	IFNULL( depAmountLocal, 0 ) AS depAmountLocal,
	IFNULL(COSTUNIT,0) - IFNULL( depAmountLocal, 0 ) AS localnbv,
	costUnitRpt,
	IFNULL( depAmountRpt, 0 ) AS depAmountRpt,
	IFNULL(costUnitRpt,0) - IFNULL( depAmountRpt, 0 ) AS rptnbv 
FROM
	erp_fa_asset_master
	LEFT JOIN (
	SELECT
		faID,
		erp_fa_assetdepreciationperiods.depMasterAutoID,
		sum( erp_fa_assetdepreciationperiods.depAmountLocal ) AS depAmountLocal,
		sum( erp_fa_assetdepreciationperiods.depAmountRpt ) AS depAmountRpt 
	FROM
		erp_fa_assetdepreciationperiods
		INNER JOIN erp_fa_depmaster ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
	WHERE
		erp_fa_depmaster.approved =- 1 
	GROUP BY
		faID 
	) t ON erp_fa_asset_master.faID = t.faID
	INNER JOIN erp_fa_assettype ON erp_fa_assettype.typeID = erp_fa_asset_master.assetType
	INNER JOIN erp_fa_financecategory ON AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID
	INNER JOIN serviceline ON serviceline.ServiceLineCode = erp_fa_asset_master.serviceLineCode
LEFT JOIN (SELECT assetDescription , faID ,faUnitSerialNo,faCode FROM erp_fa_asset_master WHERE erp_fa_asset_master.companySystemID = $request->companySystemID   )	 assetGroup ON erp_fa_asset_master.groupTO= assetGroup.faID
WHERE
	erp_fa_asset_master.companySystemID = $request->companySystemID  AND AUDITCATOGARY IN($assetCategory) AND approved =-1
	AND erp_fa_asset_master.dateAQ <= '$asOfDate' AND assetType = $typeID AND  ((DIPOSED = - 1  AND (   disposedDate > '$asOfDate')) OR DIPOSED <>  -1)
	$where
	) t  ORDER BY sortfaID desc  ";






        $output = \DB::select($qry);

        return $output;
    }
}
