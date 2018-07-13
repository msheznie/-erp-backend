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
            case 'AMAD':
                $validator = \Validator::make($request->all(), [
                    'reportTypeID' => 'required',
                    'fromDate' => 'required',
                    'toDate' => 'required'
                ]);

                $fromDate = (new Carbon($request->fromDate))->format('Y-m-d');
                $toDate = (new   Carbon($request->toDate))->format('Y-m-d');

                if ($fromDate > $toDate) {
                    return $this->sendError('The To date must be greater than the From date !', 500);
                }

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
            case 'AMAD': //Asset Register
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
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
            default:
                return $this->sendError('No report ID found');
        }
    }

    /*export report to csv according to each report id*/
    public function exportReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'AMAR': //Asset Register

                break;
            case 'AMAD': //Asset Disposal

                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
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

                    $data[$x]['Asset Cost (Local)'] = round($val->AssetCostLocal,$decimalPlaceLocal);
                    $data[$x]['Asset Cost (Reporting)'] = round($val->AssetCostRPT,$decimalPlaceRpt);

                    $data[$x]['Accumulated Depreciation (Local)'] = round($val->AccumulatedDepreciationLocal,$decimalPlaceLocal);
                    $data[$x]['Accumulated Depreciation (Reporting)'] = round($val->AccumulatedDepreciationRPT,$decimalPlaceRpt);

                    $data[$x]['Net Book Value (Local)'] = round($val->NetBookVALUELocal,$decimalPlaceLocal);
                    $data[$x]['Net Book Value (Reporting)'] = round($val->NetBookVALUERPT,$decimalPlaceRpt);

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

            default:
                return $this->sendError('No report ID found');
        }
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

}
