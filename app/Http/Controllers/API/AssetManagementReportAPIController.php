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
use App\Models\Months;
use App\Models\Year;
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
            default:
                return $this->sendError('No report ID found');
        }
    }
}
