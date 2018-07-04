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
            case 'APSS':
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
            case 'APSS': //Supplier Statement Report
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getSupplierStatementQRY($request);

                $outputArr = array();

                $balanceAmount = collect($output)->pluck('balanceAmount')->toArray();
                $balanceAmount = array_sum($balanceAmount);

                $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                $decimalPlace = array_unique($decimalPlace);

                if ($output) {
                    foreach ($output as $val) {
                        $outputArr[$val->concatSupplierName][$val->documentCurrency][] = $val;
                    }
                }
                return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'balanceAmount' => $balanceAmount, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2);
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


    function getSupplierStatementQRY($request)
    {
        $asOfDate = new Carbon($request->fromDate);
        //$asOfDate = $asOfDate->addDays(1);
        $asOfDate = $asOfDate->format('Y-m-d');

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
        $balanceAmountQry = '';
        $decimalPlaceQry = '';
        $whereQry = '';
        if ($currency == 1) {
            $currencyQry = "finalAgingDetail.transCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountTrans, finalAgingDetail.documentTransDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountTrans, finalAgingDetail.documentTransDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentTransDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountTrans, finalAgingDetail.documentTransDecimalPlaces )";
        } else if ($currency == 2) {
            $currencyQry = "finalAgingDetail.localCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentLocalAmount, finalAgingDetail.documentLocalDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountLocal, finalAgingDetail.documentLocalDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentLocalDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountLocal, finalAgingDetail.documentLocalDecimalPlaces )";
        } else {
            $currencyQry = "finalAgingDetail.rptCurrencyCode AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountRpt, finalAgingDetail.documentRptDecimalPlaces ),0) AS invoiceAmount";
            $balanceAmountQry = "IFNULL(round( finalAgingDetail.balanceAmountRpt, finalAgingDetail.documentRptDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "finalAgingDetail.documentRptDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( finalAgingDetail.balanceAmountRpt, finalAgingDetail.documentRptDecimalPlaces )";
        }
        $currencyID = $request->currencyID;
        //DB::enableQueryLog();
        $output = \DB::select('SELECT
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
    DATEDIFF("' . $asOfDate . '",DATE(finalAgingDetail.documentDate)) as ageDays,
	' . $invoiceAmountQry . ',
	' . $balanceAmountQry . ',
	' . $currencyQry . ',
	' . $decimalPlaceQry . ',
	CONCAT(finalAgingDetail.SupplierCode," - ",finalAgingDetail.suppliername) as concatSupplierName
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
	MAINQUERY.docRptAmount AS documentAmountRpt,

	(MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans) * -1 AS PaidAmountTrans,

	(MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal) * -1 AS PaidAmountLocal,

	(MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt) * -1 AS PaidAmountRpt,

	MAINQUERY.docTransAmount+MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans  as balanceAmountTrans,

	MAINQUERY.docLocalAmount+MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal  as balanceAmountLocal,

	MAINQUERY.docRptAmount + MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt AS balanceAmountRpt
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
	erp_generalledger.documentRptAmount * - 1 AS docRptAmount,
IF
	( paymentVoucherAmount.PaidPaymentVoucherTransAmount IS NULL, 0, paymentVoucherAmount.PaidPaymentVoucherTransAmount ) AS PaidPaymentVoucherTransAmount,
IF
	( paymentVoucherAmount.PaidPaymentVoucherLocalAmount IS NULL, 0, paymentVoucherAmount.PaidPaymentVoucherLocalAmount ) AS PaidPaymentVoucherLocalAmount,
IF
	( paymentVoucherAmount.PaidPaymentVoucherRptAmount IS NULL, 0, paymentVoucherAmount.PaidPaymentVoucherRptAmount ) AS PaidPaymentVoucherRptAmount,
IF
	( InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountTrans IS NULL, 0, InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountTrans ) AS InvoiceMatchedINMatchingAmountTrans,
IF
	( InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountLocal IS NULL, 0, InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountLocal ) AS InvoiceMatchedINMatchingAmountLocal,
IF
	( InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountRpt IS NULL, 0, InvoiceMatchedINMatching.InvoiceMatchedINMatchingAmountRpt ) AS InvoiceMatchedINMatchingAmountRpt,
IF
	( InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountTrans IS NULL, 0, InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountTrans ) AS InvoiceMatchedForpaymentAmountTrans,
IF
	( InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountLocal IS NULL, 0, InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountLocal ) AS InvoiceMatchedForpaymentAmountLocal,
IF
	( InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountRpt IS NULL, 0, InvoiceMatchedForpayment.InvoiceMatchedForpaymentAmountRpt ) AS InvoiceMatchedForpaymentAmountRpt,
IF
	( debitNoteMatched.debitNoteMatchedAmountTrans IS NULL, 0, debitNoteMatched.debitNoteMatchedAmountTrans ) AS debitNoteMatchedAmountTrans,
IF
	( debitNoteMatched.debitNoteMatchedAmountLocal IS NULL, 0, debitNoteMatched.debitNoteMatchedAmountLocal ) AS debitNoteMatchedAmountLocal,
IF
	( debitNoteMatched.debitNoteMatchedAmountRpt IS NULL, 0, debitNoteMatched.debitNoteMatchedAmountRpt ) AS debitNoteMatchedAmountRpt
FROM
	erp_generalledger
	LEFT JOIN (-- payment voucher matched with invoice or debit note
SELECT
	erp_paysupplierinvoicedetail.companySystemID,
	erp_paysupplierinvoicedetail.companyID,
	erp_paysupplierinvoicemaster.documentSystemID,
	erp_paysupplierinvoicemaster.documentID,
	erp_paysupplierinvoicemaster.PayMasterAutoId,
	erp_paysupplierinvoicemaster.BPVcode,
	sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS PaidPaymentVoucherTransAmount,
	sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS PaidPaymentVoucherLocalAmount,
	sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS PaidPaymentVoucherRptAmount
FROM
	erp_paysupplierinvoicedetail
	INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.PayMasterAutoId = erp_paysupplierinvoicemaster.PayMasterAutoId
WHERE
	erp_paysupplierinvoicedetail.matchingDocID = 0
	AND erp_paysupplierinvoicemaster.approved = -1
	AND erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyID) . ')
	AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
	AND DATE(erp_paysupplierinvoicemaster.postedDate) <= "' . $asOfDate . '"
GROUP BY
	companySystemID,
	documentSystemID,
	PayMasterAutoId
	) AS paymentVoucherAmount ON paymentVoucherAmount.companySystemID = erp_generalledger.companySystemID
	AND paymentVoucherAmount.documentSystemID = erp_generalledger.documentSystemID
	AND paymentVoucherAmount.PayMasterAutoId = erp_generalledger.documentSystemCode
	LEFT JOIN (-- invoice matched in a matching document
SELECT
	erp_paysupplierinvoicedetail.companySystemID,
	erp_paysupplierinvoicedetail.companyID,
	erp_paysupplierinvoicedetail.addedDocumentSystemID,
	erp_paysupplierinvoicedetail.addedDocumentID,
	erp_paysupplierinvoicedetail.bookingInvSystemCode,
	erp_paysupplierinvoicedetail.bookingInvDocCode,
	sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS InvoiceMatchedINMatchingAmountTrans,
	sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS InvoiceMatchedINMatchingAmountLocal,
	sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS InvoiceMatchedINMatchingAmountRpt
FROM
	erp_paysupplierinvoicedetail
	INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_paysupplierinvoicedetail.matchingDocID
WHERE
	erp_paysupplierinvoicedetail.matchingDocID > 0
	AND erp_matchdocumentmaster.matchingConfirmedYN = 1
	AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
	AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
	AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '"
GROUP BY
	companySystemID,
	addedDocumentSystemID,
	bookingInvSystemCode
	) AS InvoiceMatchedINMatching ON InvoiceMatchedINMatching.companySystemID = erp_generalledger.companySystemID
	AND InvoiceMatchedINMatching.addedDocumentSystemID = erp_generalledger.documentSystemID
	AND InvoiceMatchedINMatching.bookingInvSystemCode = erp_generalledger.documentSystemCode
	LEFT JOIN (-- invoice matched in a payment voucher document
SELECT
	erp_paysupplierinvoicedetail.companySystemID,
	erp_paysupplierinvoicedetail.companyID,
	erp_paysupplierinvoicedetail.addedDocumentSystemID,
	erp_paysupplierinvoicedetail.addedDocumentID,
	erp_paysupplierinvoicedetail.bookingInvSystemCode,
	erp_paysupplierinvoicedetail.bookingInvDocCode,
	sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS InvoiceMatchedForpaymentAmountTrans,
	sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS InvoiceMatchedForpaymentAmountLocal,
	sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS InvoiceMatchedForpaymentAmountRpt
FROM
	erp_paysupplierinvoicedetail
	INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.PayMasterAutoId = erp_paysupplierinvoicemaster.PayMasterAutoId
WHERE
	erp_paysupplierinvoicedetail.matchingDocID = 0
	AND erp_paysupplierinvoicemaster.approved = -1
	AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
	AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
	AND DATE(erp_paysupplierinvoicemaster.postedDate) <= "' . $asOfDate . '"
GROUP BY
	companySystemID,
	addedDocumentSystemID,
	bookingInvSystemCode
	) AS InvoiceMatchedForpayment ON InvoiceMatchedForpayment.companySystemID = erp_generalledger.companySystemID
	AND InvoiceMatchedForpayment.addedDocumentSystemID = erp_generalledger.documentSystemID
	AND InvoiceMatchedForpayment.bookingInvSystemCode = erp_generalledger.documentSystemCode
	LEFT JOIN (-- matched debit note
SELECT
	erp_matchdocumentmaster.companySystemID,
	erp_matchdocumentmaster.companyID,
	erp_matchdocumentmaster.documentSystemID,
	erp_matchdocumentmaster.documentID,
	erp_matchdocumentmaster.PayMasterAutoId,
	erp_matchdocumentmaster.BPVcode,
	sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ) AS debitNoteMatchedAmountTrans,
	sum( erp_paysupplierinvoicedetail.paymentLocalAmount ) AS debitNoteMatchedAmountLocal,
	sum( erp_paysupplierinvoicedetail.paymentComRptAmount ) AS debitNoteMatchedAmountRpt
FROM
	erp_matchdocumentmaster
	INNER JOIN erp_paysupplierinvoicedetail ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_paysupplierinvoicedetail.matchingDocID
WHERE
	erp_paysupplierinvoicedetail.matchingDocID > 0
	AND erp_matchdocumentmaster.matchingConfirmedYN = 1
	AND erp_paysupplierinvoicedetail.companySystemID IN (' . join(',', $companyID) . ')
	AND erp_paysupplierinvoicedetail.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
	AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '"
GROUP BY
	companySystemID,
	documentSystemID,
	PayMasterAutoId
	) AS debitNoteMatched ON debitNoteMatched.companySystemID = erp_generalledger.companySystemID
	AND debitNoteMatched.documentSystemID = erp_generalledger.documentSystemID
	AND debitNoteMatched.PayMasterAutoId = erp_generalledger.documentSystemCode
WHERE
	DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
	AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
	AND erp_generalledger.chartOfAccountSystemID = "' . $controlAccountsSystemID . '"
	AND erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
	) AS MAINQUERY
LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = MAINQUERY.supplierCodeSystem
LEFT JOIN currencymaster as transCurrencyDet ON transCurrencyDet.currencyID=MAINQUERY.documentTransCurrencyID
LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=MAINQUERY.documentLocalCurrencyID
LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=MAINQUERY.documentRptCurrencyID) as finalAgingDetail WHERE ' . $whereQry . ' <> 0 ORDER BY documentDate ASC;');
        //dd(DB::getQueryLog());
        return $output;
    }
}