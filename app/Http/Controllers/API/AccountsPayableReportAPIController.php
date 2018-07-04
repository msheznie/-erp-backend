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
                        $outputArr[$val->customerName][$val->documentCurrency][] = $val;
                    }
                }
                return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'balanceAmount' => $balanceAmount, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'paidAmount' => $paidAmount, 'invoiceAmount' => $invoiceAmount);
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }


    function getSupplierLedgerQRY($request)
    {
        $fromDate = new Carbon($request->fromDate);
        //$fromDate = $fromDate->addDays(1);
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

        $controlAccountsSystemID = $request->controlAccountsSystemID;
        $currency = $request->currencyID;
        $customer = $request->singleCustomer;

        $balanceAmountQry = '';
        $receiptAmountQry = '';
        $decimalPlaceQry = '';
        $invoiceAmountQry = '';
        $currencyQry = '';
        if ($currency == 1) {
            $balanceAmountQry = "round(IFNULL(MainQuery.documentTransAmount,0),MainQuery.documentTransDecimalPlaces) - round(IFNULL(InvoiceFromBRVAndMatching.InvoiceTransAmount,0),MainQuery.documentTransDecimalPlaces) AS balanceAmount";
            $receiptAmountQry = "round(IFNULL(InvoiceFromBRVAndMatching.InvoiceTransAmount, 0 ),MainQuery.documentTransDecimalPlaces) AS receiptAmount";
            $invoiceAmountQry = "round(IFNULL(MainQuery.documentTransAmount, 0 ),MainQuery.documentTransDecimalPlaces) AS invoiceAmount";
            $decimalPlaceQry = "MainQuery.documentTransDecimalPlaces AS balanceDecimalPlaces";
            $currencyQry = "MainQuery.documentTransCurrency AS documentCurrency";
        } else if ($currency == 2) {
            $balanceAmountQry = "round(IFNULL(MainQuery.documentLocalAmount,0),MainQuery.documentLocalDecimalPlaces) - round(IFNULL(InvoiceFromBRVAndMatching.InvoiceLocalAmount,0),MainQuery.documentLocalDecimalPlaces) AS balanceAmount";
            $receiptAmountQry = "round(IFNULL(InvoiceFromBRVAndMatching.InvoiceLocalAmount, 0 ),MainQuery.documentLocalDecimalPlaces) AS receiptAmount";
            $invoiceAmountQry = "round(IFNULL(MainQuery.documentLocalAmount, 0 ),MainQuery.documentLocalDecimalPlaces) AS invoiceAmount";
            $decimalPlaceQry = "MainQuery.documentLocalDecimalPlaces AS balanceDecimalPlaces";
            $currencyQry = "MainQuery.documentLocalCurrency AS documentCurrency";
        } else {
            $balanceAmountQry = "round(IFNULL(MainQuery.documentRptAmount,0),MainQuery.documentRptDecimalPlaces) - round(IFNULL(InvoiceFromBRVAndMatching.InvoiceRptAmount,0),MainQuery.documentRptDecimalPlaces) AS balanceAmount";
            $receiptAmountQry = "round(IFNULL(InvoiceFromBRVAndMatching.InvoiceRptAmount, 0 ),MainQuery.documentRptDecimalPlaces) AS receiptAmount";
            $invoiceAmountQry = "round(IFNULL(MainQuery.documentRptAmount, 0 ),MainQuery.documentRptDecimalPlaces) AS invoiceAmount";
            $decimalPlaceQry = "MainQuery.documentRptDecimalPlaces AS balanceDecimalPlaces";
            $currencyQry = "MainQuery.documentRptCurrency AS documentCurrency";
        }
        //DB::enableQueryLog();
        $output = \DB::select('SELECT
	MainQuery.companyID,
	MainQuery.documentCode,
	MainQuery.documentDate AS postedDate,
	MainQuery.clientContractID,
	MainQuery.invoiceDate,
	MainQuery.documentNarration,
	InvoiceFromBRVAndMatching.ReceiptCode,
	InvoiceFromBRVAndMatching.ReceiptDate,
' . $balanceAmountQry . ',
	' . $receiptAmountQry . ',
	' . $invoiceAmountQry . ',
	' . $currencyQry . ',
    ' . $decimalPlaceQry . ',
    MainQuery.customerName,
    MainQuery.PONumber
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
	DATE_FORMAT( erp_generalledger.documentDate, "%d/%m/%Y" ) AS documentDateFilter,
	erp_generalledger.invoiceNumber,
	erp_generalledger.invoiceDate,
	erp_generalledger.chartOfAccountSystemID,
	erp_generalledger.glCode,
	erp_generalledger.documentNarration,
	erp_generalledger.clientContractID,
	erp_generalledger.supplierCodeSystem,
	erp_generalledger.documentTransCurrencyID,
	erp_generalledger.documentTransAmount,
	erp_generalledger.documentLocalCurrencyID,
	erp_generalledger.documentLocalAmount,
	erp_generalledger.documentRptCurrencyID,
	erp_generalledger.documentRptAmount,
	currLocal.DecimalPlaces AS documentLocalDecimalPlaces,
	currRpt.DecimalPlaces AS documentRptDecimalPlaces,
	currTrans.DecimalPlaces AS documentTransDecimalPlaces,
	currRpt.CurrencyCode AS documentRptCurrency,
	currLocal.CurrencyCode AS documentLocalCurrency,
	currTrans.CurrencyCode AS documentTransCurrency,
	erp_custinvoicedirect.PONumber,
	CONCAT( customermaster.CutomerCode, " - ", customermaster.CustomerName ) AS customerName
FROM
	erp_generalledger
	INNER JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem
	LEFT JOIN currencymaster currTrans ON erp_generalledger.documentTransCurrencyID = currTrans.currencyID
	LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
	LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
	LEFT JOIN erp_custinvoicedirect ON erp_generalledger.documentSystemCode = erp_custinvoicedirect.custInvoiceDirectAutoID AND erp_generalledger.documentSystemID = erp_custinvoicedirect.documentSystemiD AND erp_generalledger.companySystemID = erp_custinvoicedirect.companySystemID
WHERE
	erp_generalledger.documentSystemID = 20
	AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
	AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '"
	AND "' . $toDate . '"
	AND ( erp_generalledger.chartOfAccountSystemID = ' . $controlAccountsSystemID . ')
	AND erp_generalledger.supplierCodeSystem = ' . $customer . '
	) AS MainQuery
	LEFT JOIN (
SELECT
	InvoiceFromUNION.companySystemID,
	InvoiceFromUNION.companyID,
	max( InvoiceFromUNION.custPaymentReceiveCode ) AS ReceiptCode,
	max( InvoiceFromUNION.postedDate ) AS ReceiptDate,
	InvoiceFromUNION.addedDocumentSystemID,
	InvoiceFromUNION.addedDocumentID,
	InvoiceFromUNION.bookingInvCodeSystem,
	InvoiceFromUNION.bookingInvCode,
	sum( InvoiceFromUNION.receiveAmountTrans ) AS InvoiceTransAmount,
	sum( InvoiceFromUNION.receiveAmountLocal ) AS InvoiceLocalAmount,
	sum( InvoiceFromUNION.receiveAmountRpt ) AS InvoiceRptAmount
FROM
	(
SELECT
	*
FROM
	(
SELECT
	erp_customerreceivepayment.custPaymentReceiveCode,
	erp_customerreceivepayment.postedDate,
	erp_custreceivepaymentdet.companySystemID,
	erp_custreceivepaymentdet.companyID,
	erp_custreceivepaymentdet.addedDocumentSystemID,
	erp_custreceivepaymentdet.addedDocumentID,
	erp_custreceivepaymentdet.bookingInvCodeSystem,
	erp_custreceivepaymentdet.bookingInvCode,
	erp_custreceivepaymentdet.receiveAmountTrans,
	erp_custreceivepaymentdet.receiveAmountLocal,
	erp_custreceivepaymentdet.receiveAmountRpt
FROM
	erp_customerreceivepayment
	INNER JOIN erp_custreceivepaymentdet ON erp_customerreceivepayment.custReceivePaymentAutoID = erp_custreceivepaymentdet.custReceivePaymentAutoID
	AND erp_custreceivepaymentdet.matchingDocID = 0
	AND erp_customerreceivepayment.approved =- 1
WHERE
	erp_custreceivepaymentdet.bookingInvCode <> "0"
	AND erp_custreceivepaymentdet.matchingDocID = 0
	AND erp_customerreceivepayment.approved =- 1
	AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
				/*AND DATE(erp_customerreceivepayment.postedDate) <= "' . $toDate . '"*/
	) AS InvoiceFromBRV UNION ALL
SELECT
	*
FROM
	(
SELECT
	erp_matchdocumentmaster.matchingDocCode,
	erp_matchdocumentmaster.matchingDocdate,
	erp_custreceivepaymentdet.companySystemID,
	erp_custreceivepaymentdet.companyID,
	erp_custreceivepaymentdet.addedDocumentSystemID,
	erp_custreceivepaymentdet.addedDocumentID,
	erp_custreceivepaymentdet.bookingInvCodeSystem,
	erp_custreceivepaymentdet.bookingInvCode,
	erp_custreceivepaymentdet.receiveAmountTrans,
	erp_custreceivepaymentdet.receiveAmountLocal,
	erp_custreceivepaymentdet.receiveAmountRpt
FROM
	erp_custreceivepaymentdet
	INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID
	AND erp_custreceivepaymentdet.companySystemID = erp_matchdocumentmaster.companySystemID
WHERE
	erp_matchdocumentmaster.matchingConfirmedYN = 1
	AND erp_custreceivepaymentdet.companySystemID  IN (' . join(',', $companyID) . ')
				/*AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $toDate . '"*/
	) AS InvoiceFromMatching
	) AS InvoiceFromUNION
GROUP BY
	bookingInvCode
	) AS InvoiceFromBRVAndMatching ON InvoiceFromBRVAndMatching.addedDocumentSystemID = mainQuery.documentSystemID
	AND mainQuery.documentSystemCode = InvoiceFromBRVAndMatching.bookingInvCodeSystem ORDER BY postedDate ASC;');
        //dd(DB::getQueryLog());
        return $output;
    }
}