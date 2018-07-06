<?php

/**
 * =============================================
 * -- File Name : FinancialReportAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report
 * -- Author : Nazir
 * -- Create date : 05 - July 2018
 * -- Description : This file contains the all the report generation code
 * -- REVISION HISTORY
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\CompanyFinanceYear;
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

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $companyFinanceYear = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear"));
        $companyFinanceYear = $companyFinanceYear->where('companySystemID', $companiesByGroup);
        if (isset($request['type']) && $request['type'] == 'add') {
            $companyFinanceYear = $companyFinanceYear->where('isActive', -1);
        }
        $companyFinanceYear = $companyFinanceYear->get();

        $output = array(
            'companyFinanceYear' => $companyFinanceYear
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
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }
}
