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
 * -- Date: 04-july 2018 By: Fayas Description: Added new functions named as getPaymentSuppliersByYear()
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\AccountsPayableLedger;
use App\Models\ChartOfAccount;
use App\Models\CurrencyMaster;
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
            case 'APPSY':
                $validator = \Validator::make($request->all(), [
                    'reportTypeID' => 'required',
                    'suppliers' => 'required',
                    'year' => 'required',
                    'currencyID' => 'required'
                ]);

                if ($validator->fails()) {
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
            case 'APPSY':
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getPaymentSuppliersByYear($request);

                $currency = $request->currencyID;
                $currencyId = 2;

                if($currency == 2){
                    $decimalPlaceCollect = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                    $decimalPlaceUnique = array_unique($decimalPlaceCollect);
                }else{
                    $decimalPlaceCollect = collect($output)->pluck('documentRptCurrencyID')->toArray();
                    $decimalPlaceUnique = array_unique($decimalPlaceCollect);
                }

                if(!empty($decimalPlaceUnique) ){
                    $currencyId = $decimalPlaceUnique[0];
                }


                $requestCurrency = CurrencyMaster::where('currencyID',$currencyId )->first();

                $decimalPlace = !empty($requestCurrency) ? $requestCurrency->DecimalPlaces : 2;

                $total = array();

                $total['Jan'] = array_sum(collect($output)->pluck('Jan')->toArray());
                $total['Feb'] = array_sum(collect($output)->pluck('Feb')->toArray());
                $total['March'] = array_sum(collect($output)->pluck('March')->toArray());
                $total['April'] = array_sum(collect($output)->pluck('April')->toArray());
                $total['May'] = array_sum(collect($output)->pluck('May')->toArray());
                $total['June'] = array_sum(collect($output)->pluck('June')->toArray());
                $total['July'] = array_sum(collect($output)->pluck('July')->toArray());
                $total['Aug'] = array_sum(collect($output)->pluck('Aug')->toArray());
                $total['Sept'] = array_sum(collect($output)->pluck('Sept')->toArray());
                $total['Oct'] = array_sum(collect($output)->pluck('Oct')->toArray());
                $total['Nov'] = array_sum(collect($output)->pluck('Nov')->toArray());
                $total['Dece'] = array_sum(collect($output)->pluck('Dece')->toArray());
                $total['Total'] = array_sum(collect($output)->pluck('Total')->toArray());

                return array('reportData' => $output,
                    'companyName' => $checkIsGroup->CompanyName,
                    'total' => $total,
                    'decimalPlace' => $decimalPlace,
                    'currency' => $requestCurrency->CurrencyCode
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
            case 'APPSY':
                $reportTypeID = $request->reportTypeID;
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));

                $companyCurrency = \Helper::companyCurrency($request->companySystemID);

                $output = $this->getPaymentSuppliersByYear($request);
                $decimalPlace = 0;
                if ($output) {
                    $x = 0;
                    foreach ($output as $val) {
                        $data[$x]['Supplier Name'] = $val->supplierName;
                        $data[$x]['Jan'] =  round($val->Jan, $decimalPlace);
                        $data[$x]['Feb'] = round($val->Feb, $decimalPlace);
                        $data[$x]['March'] = round($val->March, $decimalPlace);
                        $data[$x]['April'] = round($val->April, $decimalPlace);
                        $data[$x]['May'] = round($val->May, $decimalPlace);
                        $data[$x]['Jun'] = round($val->June, $decimalPlace);
                        $data[$x]['July'] = round($val->July, $decimalPlace);
                        $data[$x]['Aug'] = round($val->Aug, $decimalPlace);
                        $data[$x]['Sept'] = round($val->Sept, $decimalPlace);
                        $data[$x]['Oct'] = round($val->Oct, $decimalPlace);
                        $data[$x]['Nov'] = round($val->Nov, $decimalPlace);
                        $data[$x]['Dec'] = round($val->Dece, $decimalPlace);
                        $data[$x]['Total'] = round($val->Total, $decimalPlace);
                        $x++;
                    }
                }else{
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


    function getPaymentSuppliersByYear($request)
    {

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $currency  = $request->currencyID;
        $suppliers = $request->suppliers;
        $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();
        $year      = $request->year;

        $currencyClm = "documentRptAmount";
        if($currency == 2){
            $currencyClm = "documentLocalAmount";
        }else if($currency == 3){
            $currencyClm = "documentRptAmount";
        }

        //DB::enableQueryLog();
        $output = \DB::select('SELECT
                                paymentsBySupplierSummary.companySystemID,
                                paymentsBySupplierSummary.companyID,
                                paymentsBySupplierSummary.supplierCodeSystem,
                                paymentsBySupplierSummary.supplierCode,
                                paymentsBySupplierSummary.supplierName,
                                paymentsBySupplierSummary.DocYEAR,
                                paymentsBySupplierSummary.documentLocalCurrencyID,
                                paymentsBySupplierSummary.documentRptCurrencyID,
                                sum(Jan) as Jan,
                                sum(Feb) as Feb,
                                sum(March) as March,
                                sum(April) as April,
                                sum(May) as May,
                                sum(June) as June,
                                sum(July) as July,
                                sum(Aug) as Aug,
                                sum(Sept) as Sept,
                                sum(Oct) as Oct,
                                sum(Nov) as Nov,
                                sum(Dece) as Dece,
                                sum(Total) as Total
                            FROM
                            (
                            SELECT
                                MAINQUERY.companyID,
                                MAINQUERY.companySystemID,
                                MAINQUERY.supplierCodeSystem,
                                MAINQUERY.supplierCode,
                                MAINQUERY.supplierName,
                                MAINQUERY.DocYEAR,
                                MAINQUERY.documentLocalCurrencyID,
                                MAINQUERY.documentRptCurrencyID,
                            IF
                                ( MAINQUERY.DocMONTH = 1, '.$currencyClm.', 0 ) AS Jan,
                            IF
                                ( MAINQUERY.DocMONTH = 2, '.$currencyClm.', 0 ) AS Feb,
                            IF
                                ( MAINQUERY.DocMONTH = 3, '.$currencyClm.', 0 ) AS March,
                            IF
                                ( MAINQUERY.DocMONTH = 4, '.$currencyClm.', 0 ) AS April,
                            IF
                                ( MAINQUERY.DocMONTH = 5, '.$currencyClm.', 0 ) AS May,
                            IF
                                ( MAINQUERY.DocMONTH = 6, '.$currencyClm.', 0 ) AS June,
                            IF
                                ( MAINQUERY.DocMONTH = 7, '.$currencyClm.', 0 ) AS July,
                            IF
                                ( MAINQUERY.DocMONTH = 8, '.$currencyClm.', 0 ) AS Aug,
                            IF
                                ( MAINQUERY.DocMONTH = 9, '.$currencyClm.', 0 ) AS Sept,
                            IF
                                ( MAINQUERY.DocMONTH = 10, '.$currencyClm.', 0 ) AS Oct,
                            IF
                                ( MAINQUERY.DocMONTH = 11, '.$currencyClm.', 0 ) AS Nov,
                            IF
                                ( MAINQUERY.DocMONTH = 12, '.$currencyClm.', 0 ) AS Dece,
                                 '.$currencyClm.' as Total
                            FROM
                                (
                            SELECT
                                erp_generalledger.companySystemID,
                                erp_generalledger.companyID,
                                erp_generalledger.documentSystemID,
                                erp_generalledger.documentID,
                                erp_generalledger.documentSystemCode,
                                erp_generalledger.documentCode,
                                erp_generalledger.supplierCodeSystem,
                                suppliermaster.primarySupplierCode AS supplierCode,
                                suppliermaster.supplierName,
                                erp_generalledger.documentDate,
                                MONTH ( erp_generalledger.documentDate ) AS DocMONTH,
                                YEAR ( erp_generalledger.documentDate ) AS DocYEAR,
                                erp_generalledger.documentTransCurrencyID,
                                erp_generalledger.documentTransAmount,
                                erp_generalledger.documentLocalCurrencyID,
                                erp_generalledger.documentLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                erp_generalledger.documentRptAmount,
                                erp_generalledger.documentType,
                                If(erp_generalledger.documentType=2,"Invoive Payment",If(erp_generalledger.documentType=3,"Direct Payment",If(erp_generalledger.documentType=5,"Advance Payment",""))) as PaymentType
                            FROM
                                erp_generalledger
                                LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                                 WHERE erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                            AND
                                erp_generalledger.documentSystemID = 4
                                AND erp_generalledger.supplierCodeSystem > 0 
                                AND companySystemID IN (' . join(',', $companyID) . ') 
                                AND YEAR ( erp_generalledger.documentDate ) = "' . $year . '"
                                AND erp_generalledger.documentTransAmount > 0 
                            ) AS MAINQUERY
                            ) AS paymentsBySupplierSummary
                                GROUP BY
                                paymentsBySupplierSummary.companySystemID,
                                paymentsBySupplierSummary.supplierCodeSystem;');

        return $output;

    }

}