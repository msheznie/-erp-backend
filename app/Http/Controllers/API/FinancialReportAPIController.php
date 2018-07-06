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
 * -- REVISION HISTORY
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use App\Models\CompanyFinanceYear;
use App\Models\CurrencyMaster;
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

                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getTrialBalance($request);

                $outputArr = array();
                foreach ($output as $val) {
                    $outputArr[$val->CompanyName][] = $val;
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
                return array('reportData' => $outputArr,
                    'companyName' => $checkIsGroup->CompanyName,
                    'total' => $total,
                    'decimalPlaceLocal' => $decimalPlaceLocal,
                    'decimalPlaceRpt' => $decimalPlaceRpt,
                    'currencyLocal' => $requestCurrencyLocal->CurrencyCode,
                    'currencyRpt' => $requestCurrencyRpt->CurrencyCode,
                );
                break;

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

                $currencyLocal  = $requestCurrencyLocal->CurrencyCode;
                $currencyRpt    = $requestCurrencyRpt->CurrencyCode ;

                $data = array();
                if ($output) {
                    $x = 0;
                    foreach ($output as $val) {
                        $data[$x]['Company ID'] = $val->companyID;
                        $data[$x]['Company Name'] = $val->CompanyName;
                        $data[$x]['Account Code'] = $val->glCode;
                        $data[$x]['Account Description'] = $val->AccountDescription;
                        $data[$x]['Type'] = $val->glAccountType;
                        $data[$x]['Debit (Local Currency - '.$currencyLocal.')'] = round($val->documentLocalAmountDebit, $decimalPlaceLocal);
                        $data[$x]['Credit (Local Currency - '.$currencyLocal.')'] = round($val->documentLocalAmountCredit, $decimalPlaceLocal);
                        $data[$x]['Debit (Reporting Currency - '.$currencyRpt.')'] = round($val->documentRptAmountDebit, $decimalPlaceRpt);
                        $data[$x]['Credit (Reporting Currency - '.$currencyRpt.')'] = round($val->documentRptAmountCredit, $decimalPlaceRpt);
                        $x++;
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
                        erp_generalledger.companySystemID,
                        glCode,
                        glAccountType 
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
                        companySystemID,
                        chartOfAccountSystemID
                        order by glCode';

        $output = \DB::select($query);
        //dd(DB::getQueryLog());
        return $output;

    }


}
