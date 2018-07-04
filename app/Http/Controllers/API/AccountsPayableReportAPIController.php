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
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\AccountsPayableLedger;
use App\Models\ChartOfAccount;
use App\Models\GeneralLedger;
use App\Models\SupplierAssigned;
use App\Models\SupplierMaster;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountsPayableReportAPIController extends AppBaseController
{
    public function getAPFilterData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $companiesByGroup = "";
        if (\Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

        $controlAccount = SupplierMaster::groupBy('liabilityAccountSysemID')->pluck('liabilityAccountSysemID');
        $controlAccount = ChartOfAccount::whereIN('chartOfAccountSystemID', $controlAccount)->get();

        $departments = \Helper::getCompanyServiceline($selectedCompanyId);

        $filterSuppliers = AccountsPayableLedger::whereIN('companySystemID', $companiesByGroup)
            ->select('supplierCodeSystem')
            ->groupBy('supplierCodeSystem')
            ->pluck('supplierCodeSystem');

        $supplierMaster = SupplierAssigned::whereIN('companySystemID', $companiesByGroup)->whereIN('supplierCodeSytem', $filterSuppliers)->groupBy('supplierCodeSytem')->get();

        $years = GeneralLedger::select(DB::raw("YEAR(documentDate) as year"))
            ->whereNotNull('documentDate')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get(['year']);

        $output = array(
            'controlAccount' => $controlAccount,
            'suppliers' => $supplierMaster,
            'departments' => $departments,
            'years' => $years,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
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
    public function generateAPReport(Request $request)
    {
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
            default:
                return $this->sendError('No report ID found');
        }
    }


    function getSupplierLedgerQRY($request)
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

        $suppliers = (array)$request->suppliers;
        $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();

        $controlAccountsSystemID = $request->controlAccountsSystemID;

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
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentLocalAmount, finalAgingDetail.documentLocalDecimalPlaces ),0) AS invoiceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentLocalDecimalPlaces AS balanceDecimalPlaces";
        } else {
            $currencyQry = "finalAgingDetail.rptCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountRpt, finalAgingDetail.documentRptDecimalPlaces ),0) AS invoiceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentRptDecimalPlaces AS balanceDecimalPlaces";
        }
        $currencyID = $request->currencyID;

        $query = 'SELECT
	finalAgingDetail.companySystemID,
	finalAgingDetail.companyID,
	finalAgingDetail.documentSystemID,
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
	' . $invoiceAmountQry . ',
	' . $currencyQry . ',
	' . $decimalPlaceQry . '
FROM
(
SELECT
	MAINQUERY.companySystemID,
	MAINQUERY.companyID,
	MAINQUERY.documentSystemID,
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
	MAINQUERY.docRptAmount AS documentAmountRpt
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
	erp_generalledger.documentTransAmount * - 1 AS docTransAmount,
	erp_generalledger.documentLocalCurrencyID,
	erp_generalledger.documentLocalAmount * - 1 AS docLocalAmount,
	erp_generalledger.documentRptCurrencyID,
	erp_generalledger.documentRptAmount * - 1 AS docRptAmount
FROM
	erp_generalledger
WHERE
	DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
	AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
	AND erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
	AND erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
	) AS MAINQUERY
LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = MAINQUERY.supplierCodeSystem
LEFT JOIN currencymaster as transCurrencyDet ON transCurrencyDet.currencyID=MAINQUERY.documentTransCurrencyID
LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=MAINQUERY.documentLocalCurrencyID
LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=MAINQUERY.documentRptCurrencyID
UNION ALL
SELECT
	MAINQUERY.companySystemID,
	MAINQUERY.companyID,
	MAINQUERY.documentSystemID,
	MAINQUERY.documentID,
	"" as documentCode,
	"1970-01-01" as documentDate,
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
	SUM(IFNULL(MAINQUERY.docRptAmount,0)) AS documentAmountRpt
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
	erp_generalledger.documentTransAmount * - 1 AS docTransAmount,
	erp_generalledger.documentLocalCurrencyID,
	erp_generalledger.documentLocalAmount * - 1 AS docLocalAmount,
	erp_generalledger.documentRptCurrencyID,
	erp_generalledger.documentRptAmount * - 1 AS docRptAmount
FROM
	erp_generalledger
WHERE
	DATE(erp_generalledger.documentDate) < "' . $fromDate . '"
	AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
	AND erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
	AND erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
	 ) AS MAINQUERY
LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = MAINQUERY.supplierCodeSystem
LEFT JOIN currencymaster as transCurrencyDet ON transCurrencyDet.currencyID=MAINQUERY.documentTransCurrencyID
LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=MAINQUERY.documentLocalCurrencyID
LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=MAINQUERY.documentRptCurrencyID GROUP BY MAINQUERY.supplierCodeSystem ) as finalAgingDetail ORDER BY documentDate';
        $output = \DB::select($query);
        //dd(DB::getQueryLog());
        return $output;
    }
}