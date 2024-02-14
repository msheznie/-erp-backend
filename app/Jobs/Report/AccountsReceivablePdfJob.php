<?php

namespace App\Jobs\Report;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Jobs\Report\GenerateARCAPdfReport;
use App\Models\Company;
use App\Models\CurrencyMaster;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Services\WebPushNotificationService;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use File;

class AccountsReceivablePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    public $requestData;
    public $userIds;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $request, $userId)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        $this->dispatch_db = $dispatch_db;
        $this->requestData = $request;
        $this->userIds = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('max_execution_time', config('app.report_max_execution_limit'));
        ini_set('memory_limit', -1);
        Log::useFiles(storage_path() . '/logs/accounts_receivable_ledger_jobs.log'); 
        $request = $this->requestData;
        $db = $this->dispatch_db;
        CommonJobService::db_switch($db);

        $currentDate = strtotime(date("Y-m-d H:i:s"));
        $root = "account-recivable-pdf/".$currentDate;

        $output = $this->getCustomerAgingForPDF($request);
        $outputChunkData = collect($output)->chunk(300);

        $reportCount = 1;

        foreach ($outputChunkData as $key1 => $output1) {
            GenerateARCAPdfReport::dispatch($db, $request, $reportCount, $this->userIds, $output1, count($outputChunkData), $root);
            $reportCount++;
        }
    }

    function getCustomerAgingForPDF($request)
    {
        if ($request->reportTypeID == 'CAS') {

            $asOfDate = new Carbon($request->fromDate);
            $asOfDate = $asOfDate->format('Y-m-d');
    
            $companyID = "";
            $checkIsGroup = Company::find($request->companySystemID);
            if ($checkIsGroup->isGroup) {
                $companyID = \Helper::getGroupCompany($request->companySystemID);
            } else {
                $companyID = (array)$request->companySystemID;
            }
    
            $customers = (array)$request->customers;
            $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();
    
            $controlAccountsSystemID = $request->controlAccountsSystemID;
    
            $currency = $request->currencyID;
    
            $z = 1;
            $aging = array();
            $interval = $request->interval;
            $through = $request->through;
            $agingRange = range(0, $through, $interval);
            $rangeAmount = $interval;
            $agingAgeCount = count($agingRange);
            foreach ($agingRange as $val) {
                if ($z == $agingAgeCount) {
                    $aging[] = $val + 1 . "-" . $through;
                } else {
                    if ($z == 1) {
                        $aging[] = $val . "-" . $rangeAmount;
                    } else {
                        $aging[] = $val + 1 . "-" . $rangeAmount;
                    }
                    $rangeAmount += $interval;
                }
                $z++;
            }
    
            $aging[] = "> " . ($through);
            $agingField = '';
            if (!empty($aging)) { /*calculate aging range in query*/
                $count = count($aging);
                $c = 1;
                foreach ($aging as $val) {
                    if ($count == $c) {
                        $agingField .= "SUM(if(grandFinal.age > " . $through . ",grandFinal.balanceAmount,0)) as `" . $val . "`,";
                    } else {
                        $list = explode("-", $val);
                        $agingField .= "SUM(if(grandFinal.age >= " . $list[0] . " AND grandFinal.age <= " . $list[1] . ",grandFinal.balanceAmount,0)) as `" . $val . "`,";
                    }
                    $c++;
                }
            }
            $agingField .= "SUM(if(grandFinal.age <= 0,grandFinal.balanceAmount,0)) as `current`";
    
    
            $currencyQry = '';
            $amountQry = '';
            $decimalPlaceQry = '';
            $whereQry = '';
            if ($currency == 1) {
                $currencyQry = "final.documentTransCurrency AS documentCurrency";
                $amountQry = "round( final.balanceTrans, final.documentTransDecimalPlaces ) AS balanceAmount";
                $decimalPlaceQry = "final.documentTransDecimalPlaces AS balanceDecimalPlaces";
                $whereQry = "round( final.balanceTrans, final.documentTransDecimalPlaces )";
            } else if ($currency == 2) {
                $currencyQry = "final.documentLocalCurrency AS documentCurrency";
                $amountQry = "round( final.balanceLocal, final.documentLocalDecimalPlaces ) AS balanceAmount";
                $decimalPlaceQry = "final.documentLocalDecimalPlaces AS balanceDecimalPlaces";
                $whereQry = "round( final.balanceLocal, final.documentLocalDecimalPlaces )";
            } else {
                $currencyQry = "final.documentRptCurrency AS documentCurrency";
                $amountQry = "round( final.balanceRpt, final.documentRptDecimalPlaces ) AS balanceAmount";
                $decimalPlaceQry = "final.documentRptDecimalPlaces AS balanceDecimalPlaces";
                $whereQry = "round( final.balanceRpt, final.documentRptDecimalPlaces )";
            }
            $currencyID = $request->currencyID;
            $output = \DB::select('SELECT DocumentCode,PostedDate,DocumentNarration,Contract,invoiceNumber,InvoiceDate,' . $agingField . ',documentCurrency,balanceDecimalPlaces,CustomerName, creditDays,CustomerCode,customerCodeSystem,companyID,CompanyName,concatCompanyName FROM (SELECT
                    final.documentCode AS DocumentCode,
                    final.documentDate AS PostedDate,
                    final.documentNarration AS DocumentNarration,
                    final.clientContractID AS Contract,
                    final.invoiceNumber AS invoiceNumber,
                    final.invoiceDate AS InvoiceDate,
                    ' . $amountQry . ',
                    ' . $currencyQry . ',
                    ' . $decimalPlaceQry . ',
                    final.CustomerName,
                    final.creditDays,
                    final.CutomerCode as CustomerCode,
                    final.supplierCodeSystem AS customerCodeSystem,
                    DATEDIFF("' . $asOfDate . '",DATE(final.documentDate)) as age,
                    final.companyID, 
                    final.CompanyName,
                    CONCAT(final.companyID," - " ,final.CompanyName) as concatCompanyName
                FROM
                    (
                SELECT
                    mainQuery.companySystemID,
                    mainQuery.companyID,
                    mainQuery.CompanyName,
                    mainQuery.serviceLineSystemID,
                    mainQuery.serviceLineCode,
                    mainQuery.documentSystemID,
                    mainQuery.documentID,
                    mainQuery.documentSystemCode,
                    mainQuery.documentCode,
                    mainQuery.documentDate,
                    mainQuery.documentDateFilter,
                    mainQuery.invoiceNumber,
                    mainQuery.invoiceDate,
                    mainQuery.chartOfAccountSystemID,
                    mainQuery.glCode,
                    mainQuery.documentNarration,
                    mainQuery.clientContractID,
                    mainQuery.supplierCodeSystem,
                    mainQuery.documentTransCurrencyID,
                    mainQuery.documentTransCurrency,
                    mainQuery.documentTransAmount,
                    mainQuery.documentTransDecimalPlaces,
                    mainQuery.documentLocalCurrencyID,
                    mainQuery.documentLocalCurrency,
                    mainQuery.documentLocalAmount,
                    mainQuery.documentLocalDecimalPlaces,
                    mainQuery.documentRptCurrencyID,
                    mainQuery.documentRptCurrency,
                    mainQuery.documentRptAmount,
                    mainQuery.documentRptDecimalPlaces,
                IF( matchedBRV.MatchedBRVTransAmount IS NULL, 0, matchedBRV.MatchedBRVTransAmount ) AS MatchedBRVTransAmount,
                IF( matchedBRV.MatchedBRVLocalAmount IS NULL, 0, matchedBRV.MatchedBRVLocalAmount ) AS MatchedBRVLocalAmount,
                IF( matchedBRV.MatchedBRVRptAmount IS NULL, 0, matchedBRV.MatchedBRVRptAmount ) AS MatchedBRVRptAmount,
                IF( InvoicedBRV.BRVTransAmount IS NULL, 0, InvoicedBRV.BRVTransAmount ) AS BRVTransAmount,
                IF( InvoicedBRV.BRVLocalAmount IS NULL, 0, InvoicedBRV.BRVLocalAmount ) AS BRVLocalAmount,
                IF( InvoicedBRV.BRVRptAmount IS NULL, 0, InvoicedBRV.BRVRptAmount ) AS BRVRptAmount,
                IF( InvoiceFromBRVAndMatching.InvoiceTransAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceTransAmount *- 1 ) AS InvoiceTransAmount,
                IF( InvoiceFromBRVAndMatching.InvoiceLocalAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceLocalAmount *- 1 ) AS InvoiceLocalAmount,
                IF( InvoiceFromBRVAndMatching.InvoiceRptAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceRptAmount *- 1 ) AS InvoiceRptAmount,
                    (
                    mainQuery.documentRptAmount + ( IF ( matchedBRV.MatchedBRVRptAmount IS NULL, 0, matchedBRV.MatchedBRVRptAmount ) ) + ( IF ( InvoicedBRV.BRVRptAmount IS NULL, 0, InvoicedBRV.BRVRptAmount ) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceRptAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceRptAmount *- 1 ) ) 
                    ) AS balanceRpt,
                    (
                    mainQuery.documentLocalAmount + ( IF ( matchedBRV.MatchedBRVLocalAmount IS NULL, 0, matchedBRV.MatchedBRVLocalAmount ) ) + ( IF ( InvoicedBRV.BRVLocalAmount IS NULL, 0, InvoicedBRV.BRVLocalAmount ) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceLocalAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceLocalAmount *- 1 ) ) 
                    ) AS balanceLocal,
                    (
                    mainQuery.documentTransAmount + ( IF ( matchedBRV.MatchedBRVTransAmount IS NULL, 0, matchedBRV.MatchedBRVTransAmount ) ) + ( IF ( InvoicedBRV.BRVTransAmount IS NULL, 0, InvoicedBRV.BRVTransAmount ) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceTransAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceTransAmount *- 1 ) ) 
                    ) AS balanceTrans,
                    mainQuery.CustomerName,
                    mainQuery.creditDays,
                    mainQuery.CutomerCode
                FROM
                    (
                SELECT
                    erp_generalledger.companySystemID,
                    erp_generalledger.companyID,
                    companymaster.CompanyName,
                    erp_generalledger.serviceLineSystemID,
                    erp_generalledger.serviceLineCode,
                    erp_generalledger.documentSystemID,
                    erp_generalledger.documentID,
                    erp_generalledger.documentSystemCode,
                    erp_generalledger.documentCode,
                    erp_generalledger.documentDate,
                    DATE_FORMAT( documentDate, "%d/%m/%Y" ) AS documentDateFilter,
                    erp_generalledger.documentYear,
                    erp_generalledger.documentMonth,
                    erp_generalledger.chequeNumber,
                    erp_generalledger.invoiceNumber,
                    erp_generalledger.invoiceDate,
                    erp_generalledger.chartOfAccountSystemID,
                    erp_generalledger.glCode,
                    erp_generalledger.documentNarration,
                    erp_generalledger.clientContractID,
                    erp_generalledger.supplierCodeSystem,
                    erp_generalledger.documentTransCurrencyID,
                    currTrans.CurrencyCode as documentTransCurrency,
                    currTrans.DecimalPlaces as documentTransDecimalPlaces,
                    SUM(erp_generalledger.documentTransAmount) as documentTransAmount,
                    erp_generalledger.documentLocalCurrencyID,
                    currLocal.CurrencyCode as documentLocalCurrency,
                    currLocal.DecimalPlaces as documentLocalDecimalPlaces,
                    SUM(erp_generalledger.documentLocalAmount) as documentLocalAmount,
                    erp_generalledger.documentRptCurrencyID,
                    currRpt.CurrencyCode as documentRptCurrency,
                    currRpt.DecimalPlaces as documentRptDecimalPlaces,
                    SUM(erp_generalledger.documentRptAmount) as documentRptAmount,
                    erp_generalledger.documentType,
                    customermaster.CustomerName,
                    customermaster.creditDays,
                    customermaster.CutomerCode
                FROM
                    erp_generalledger 
                    LEFT JOIN currencymaster currTrans ON erp_generalledger.documentTransCurrencyID = currTrans.currencyID
                    LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
                    LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
                    LEFT JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
                    LEFT JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                WHERE
                    ( erp_generalledger.documentSystemID = "20" OR erp_generalledger.documentSystemID = "19" OR erp_generalledger.documentSystemID = "21" ) 
                    AND DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
                    AND ( erp_generalledger.chartOfAccountSystemID = ' . $controlAccountsSystemID . ' )
                    AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') 
                    AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
                    GROUP BY erp_generalledger.companySystemID, erp_generalledger.supplierCodeSystem,erp_generalledger.chartOfAccountSystemID,erp_generalledger.documentSystemID,erp_generalledger.documentSystemCode
                    ) AS mainQuery
                    LEFT JOIN (
                    SELECT
                        erp_matchdocumentmaster.companySystemID,
                        erp_matchdocumentmaster.documentSystemID,
                        erp_matchdocumentmaster.PayMasterAutoId,
                        erp_matchdocumentmaster.BPVcode,
                        sum( erp_custreceivepaymentdet.receiveAmountTrans ) AS MatchedBRVTransAmount,
                        sum( erp_custreceivepaymentdet.receiveAmountLocal ) AS MatchedBRVLocalAmount,
                        sum( erp_custreceivepaymentdet.receiveAmountRpt ) AS MatchedBRVRptAmount 
                    FROM
                        erp_matchdocumentmaster
                        INNER JOIN erp_custreceivepaymentdet ON erp_matchdocumentmaster.companyID = erp_custreceivepaymentdet.companyID 
                        AND erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID 
                    WHERE
                        erp_matchdocumentmaster.matchingConfirmedYN = 1 
                        AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '" 
                        AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
                        AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
                    GROUP BY
                        erp_matchdocumentmaster.PayMasterAutoId,
                        erp_matchdocumentmaster.BPVcode 
                    ) AS matchedBRV ON mainQuery.documentSystemID = matchedBRV.documentSystemID 
                    AND mainQuery.companySystemID = matchedBRV.companySystemID 
                    AND matchedBRV.PayMasterAutoId = mainQuery.documentSystemCode
                    LEFT JOIN (
                    SELECT
                        erp_customerreceivepayment.custReceivePaymentAutoID,
                        erp_customerreceivepayment.companySystemID,
                        erp_customerreceivepayment.documentSystemID,
                        erp_customerreceivepayment.custPaymentReceiveCode,
                        sum( erp_custreceivepaymentdet.receiveAmountTrans ) AS BRVTransAmount,
                        sum( erp_custreceivepaymentdet.receiveAmountLocal ) AS BRVLocalAmount,
                        sum( erp_custreceivepaymentdet.receiveAmountRpt ) AS BRVRptAmount 
                    FROM
                        erp_customerreceivepayment
                        INNER JOIN erp_custreceivepaymentdet ON erp_customerreceivepayment.custReceivePaymentAutoID = erp_custreceivepaymentdet.custReceivePaymentAutoID 
                    WHERE
                        erp_custreceivepaymentdet.bookingInvCode <> "0" 
                        AND erp_custreceivepaymentdet.matchingDocID = 0 
                        AND erp_customerreceivepayment.approved =- 1 
                        AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
                        AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '"
                        AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
                    GROUP BY
                        custReceivePaymentAutoID 
                    ) AS InvoicedBRV ON mainQuery.documentSystemID = InvoicedBRV.documentSystemID 
                    AND mainQuery.documentSystemCode = InvoicedBRV.custReceivePaymentAutoID
                    LEFT JOIN (
                    SELECT
                        companySystemID,
                        companyID,
                        addedDocumentSystemID,
                        addedDocumentID,
                        bookingInvCodeSystem,
                        bookingInvCode,
                        sum( receiveAmountTrans ) AS InvoiceTransAmount,
                        sum( receiveAmountLocal ) AS InvoiceLocalAmount,
                        sum( receiveAmountRpt ) AS InvoiceRptAmount 
                    FROM
                        (
                        SELECT
                            * 
                        FROM
                            (
                            SELECT
                                erp_customerreceivepayment.custPaymentReceiveCode,
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
                                AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '" 
                                AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
                            ) AS InvoiceFromBRV UNION ALL
                        SELECT
                            * 
                        FROM
                            (
                            SELECT
                                erp_matchdocumentmaster.matchingDocCode,
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
                                AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
                                AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '" 
                                AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
                            ) AS InvoiceFromMatching 
                        ) AS InvoiceFromUNION 
                    GROUP BY
                        bookingInvCode 
                    ) AS InvoiceFromBRVAndMatching ON InvoiceFromBRVAndMatching.addedDocumentSystemID = mainQuery.documentSystemID 
                    AND mainQuery.documentSystemCode = InvoiceFromBRVAndMatching.bookingInvCodeSystem 
                    ) AS final 
                WHERE
                ' . $whereQry . ' <> 0 ORDER BY PostedDate ASC) as grandFinal GROUP BY customerCodeSystem,companyID ORDER BY CustomerName');
    
            return ['data' => $output, 'aging' => $aging];

        } elseif ($request->reportTypeID == 'CAD') {

            $asOfDate = new Carbon($request->fromDate);
            $asOfDate = $asOfDate->format('Y-m-d');
    
            $companyID = "";
            $checkIsGroup = Company::find($request->companySystemID);
            if ($checkIsGroup->isGroup) {
                $companyID = \Helper::getGroupCompany($request->companySystemID);
            } else {
                $companyID = (array)$request->companySystemID;
            }
    
            $customers = (array)$request->customers;
            $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();
    
            $controlAccountsSystemID = $request->controlAccountsSystemID;
    
            $currency = $request->currencyID;
    
            $z = 1;
            $aging = array();
            $interval = $request->interval;
            $through = $request->through;
            $agingRange = range(0, $through, $interval);
            $rangeAmount = $interval;
            $agingAgeCount = count($agingRange);
            foreach ($agingRange as $val) {
                if ($z == $agingAgeCount) {
                    $aging[] = $val + 1 . "-" . $through;
                } else {
                    if ($z == 1) {
                        $aging[] = $val . "-" . $rangeAmount;
                    } else {
                        $aging[] = $val + 1 . "-" . $rangeAmount;
                    }
                    $rangeAmount += $interval;
                }
                $z++;
            }
    
            $aging[] = "> " . ($through);
            $agingField = '';
            if (!empty($aging)) { /*calculate aging range in query*/
                $count = count($aging);
                $c = 1;
                foreach ($aging as $val) {
                    if ($count == $c) {
                        $agingField .= "if(grandFinal.age > " . $through . ",grandFinal.balanceAmount,0) as `" . $val . "`,";
                    } else {
                        $list = explode("-", $val);
                        $agingField .= "if(grandFinal.age >= " . $list[0] . " AND grandFinal.age <= " . $list[1] . ",grandFinal.balanceAmount,0) as `" . $val . "`,";
                    }
                    $c++;
                }
            }
            $agingField .= "if(grandFinal.age <= 0,grandFinal.balanceAmount,0) as `current`";
    
    
            $currencyQry = '';
            $amountQry = '';
            $decimalPlaceQry = '';
            $whereQry = '';
            $subsequentBalanceQry = '';
            $subsequentQry = '';
            $invoiceQry = '';
            if ($currency == 1) {
                $currencyQry = "final.documentTransCurrency AS documentCurrency";
                $amountQry = "round( final.balanceTrans, final.documentTransDecimalPlaces ) AS balanceAmount";
                $decimalPlaceQry = "final.documentTransDecimalPlaces AS balanceDecimalPlaces";
                $whereQry = "round( final.balanceTrans, final.documentTransDecimalPlaces )";
                $subsequentBalanceQry = "round( final.balanceSubsequentCollectionTrans, final.documentTransDecimalPlaces ) as subsequentBalanceAmount";
                $subsequentQry = "round( final.SubsequentCollectionTransAmount, final.documentTransDecimalPlaces ) AS subsequentAmount";
                $invoiceQry = "round( final.documentTransAmount, final.documentTransDecimalPlaces ) AS invoiceAmount";
            } else if ($currency == 2) {
                $currencyQry = "final.documentLocalCurrency AS documentCurrency";
                $amountQry = "round( final.balanceLocal, final.documentLocalDecimalPlaces ) AS balanceAmount";
                $decimalPlaceQry = "final.documentLocalDecimalPlaces AS balanceDecimalPlaces";
                $whereQry = "round( final.balanceLocal, final.documentLocalDecimalPlaces )";
                $subsequentBalanceQry = "round( final.balanceSubsequentCollectionLocal, final.documentLocalDecimalPlaces ) as subsequentBalanceAmount";
                $subsequentQry = "round( final.SubsequentCollectionLocalAmount, final.documentLocalDecimalPlaces ) AS subsequentAmount";
                $invoiceQry = "round( final.documentLocalAmount, final.documentLocalDecimalPlaces ) AS invoiceAmount";
            } else {
                $currencyQry = "final.documentRptCurrency AS documentCurrency";
                $amountQry = "round( final.balanceRpt, final.documentRptDecimalPlaces ) AS balanceAmount";
                $decimalPlaceQry = "final.documentRptDecimalPlaces AS balanceDecimalPlaces";
                $whereQry = "round( final.balanceRpt, final.documentRptDecimalPlaces )";
                $subsequentBalanceQry = "round( final.balanceSubsequentCollectionRpt, final.documentRptDecimalPlaces ) as subsequentBalanceAmount";
                $subsequentQry = "round( final.SubsequentCollectionRptAmount, final.documentRptDecimalPlaces ) AS subsequentAmount";
                $invoiceQry = "round( final.documentRptAmount, final.documentRptDecimalPlaces ) AS invoiceAmount";
            }
            $currencyID = $request->currencyID;
                    $output = \DB::select('SELECT
                    DocumentCode,commentAndStatus,PostedDate,DocumentNarration,Contract,invoiceNumber,InvoiceDate,' . $agingField . ',documentCurrency,balanceDecimalPlaces,customerName,creditDays,age,glCode,customerName2,CutomerCode,PONumber,invoiceDueDate,subsequentBalanceAmount,brvInv,subsequentAmount,companyID,invoiceAmount,companyID,CompanyName,serviceLineName,documentSystemCode,documentSystemID FROM (SELECT
                final.documentCode AS DocumentCode,
                final.comments AS commentAndStatus,
                final.documentDate AS PostedDate,
                final.documentNarration AS DocumentNarration,
                final.clientContractID AS Contract,
                final.invoiceNumber AS invoiceNumber,
                final.invoiceDate AS InvoiceDate,
                ' . $amountQry . ',
                ' . $subsequentQry . ',
                ' . $subsequentBalanceQry . ',
                ' . $currencyQry . ',
                ' . $decimalPlaceQry . ',
                ' . $invoiceQry . ',
                final.customerName AS customerName,
                final.creditDays AS creditDays,
                final.customerName2 AS customerName2,
                final.CutomerCode AS CutomerCode,
                DATEDIFF("' . $asOfDate . '",DATE(final.documentDate)) as age,
                final.glCode,
                final.PONumber,
                final.invoiceDueDate,
                final.brvInv,
                final.companyID,
                final.CompanyName,
                final.serviceLineName,
                final.documentSystemCode,
                final.documentSystemID
            FROM
                (
            SELECT
                mainQuery.companySystemID,
                mainQuery.companyID,
                mainQuery.CompanyName,
                mainQuery.serviceLineSystemID,
                mainQuery.serviceLineCode,
                mainQuery.serviceLineName,
                mainQuery.documentSystemID,
                mainQuery.documentID,
                mainQuery.documentSystemCode,
                mainQuery.documentCode,
                mainQuery.comments,
                mainQuery.documentDate,
                mainQuery.documentDateFilter,
                mainQuery.invoiceNumber,
                mainQuery.invoiceDate,
                mainQuery.chartOfAccountSystemID,
                mainQuery.glCode,
                mainQuery.documentNarration,
                mainQuery.clientContractID,
                mainQuery.supplierCodeSystem,
                mainQuery.documentTransCurrencyID,
                mainQuery.documentTransCurrency,
                mainQuery.documentTransAmount,
                mainQuery.documentTransDecimalPlaces,
                mainQuery.documentLocalCurrencyID,
                mainQuery.documentLocalCurrency,
                mainQuery.documentLocalAmount,
                mainQuery.documentLocalDecimalPlaces,
                mainQuery.documentRptCurrencyID,
                mainQuery.documentRptCurrency,
                mainQuery.documentRptAmount,
                mainQuery.documentRptDecimalPlaces,
            IF( matchedBRV.MatchedBRVTransAmount IS NULL, 0, matchedBRV.MatchedBRVTransAmount ) AS MatchedBRVTransAmount,
            IF( matchedBRV.MatchedBRVLocalAmount IS NULL, 0, matchedBRV.MatchedBRVLocalAmount ) AS MatchedBRVLocalAmount,
            IF( matchedBRV.MatchedBRVRptAmount IS NULL, 0, matchedBRV.MatchedBRVRptAmount ) AS MatchedBRVRptAmount,
            IF( InvoicedBRV.BRVTransAmount IS NULL, 0, InvoicedBRV.BRVTransAmount ) AS BRVTransAmount,
            IF( InvoicedBRV.BRVLocalAmount IS NULL, 0, InvoicedBRV.BRVLocalAmount ) AS BRVLocalAmount,
            IF( InvoicedBRV.BRVRptAmount IS NULL, 0, InvoicedBRV.BRVRptAmount ) AS BRVRptAmount,
            IF( InvoiceFromBRVAndMatching.InvoiceTransAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceTransAmount *- 1 ) AS InvoiceTransAmount,
            IF( InvoiceFromBRVAndMatching.InvoiceLocalAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceLocalAmount *- 1 ) AS InvoiceLocalAmount,
            IF( InvoiceFromBRVAndMatching.InvoiceRptAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceRptAmount *- 1 ) AS InvoiceRptAmount,
                (
                mainQuery.documentRptAmount + ( IF ( matchedBRV.MatchedBRVRptAmount IS NULL, 0, matchedBRV.MatchedBRVRptAmount ) ) + ( IF ( InvoicedBRV.BRVRptAmount IS NULL, 0, InvoicedBRV.BRVRptAmount ) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceRptAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceRptAmount *- 1 ) )
                ) AS balanceRpt,
                (
                mainQuery.documentLocalAmount + ( IF ( matchedBRV.MatchedBRVLocalAmount IS NULL, 0, matchedBRV.MatchedBRVLocalAmount ) ) + ( IF ( InvoicedBRV.BRVLocalAmount IS NULL, 0, InvoicedBRV.BRVLocalAmount ) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceLocalAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceLocalAmount *- 1 ) )
                ) AS balanceLocal,
                (
                mainQuery.documentTransAmount + ( IF ( matchedBRV.MatchedBRVTransAmount IS NULL, 0, matchedBRV.MatchedBRVTransAmount ) ) + ( IF ( InvoicedBRV.BRVTransAmount IS NULL, 0, InvoicedBRV.BRVTransAmount ) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceTransAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceTransAmount *- 1 ) )
                ) AS balanceTrans,
            
                (
                mainQuery.documentRptAmount + ( IF ( matchedBRV.MatchedBRVRptAmount IS NULL, 0, matchedBRV.MatchedBRVRptAmount ) ) + ( IF ( InvoicedBRV.BRVRptAmount IS NULL, 0, InvoicedBRV.BRVRptAmount ) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceRptAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceRptAmount *- 1 ) -  IFNULL(Subsequentcollection.SubsequentCollectionRptAmount,0))
                ) AS balanceSubsequentCollectionRpt,
                (
                mainQuery.documentLocalAmount + ( IF ( matchedBRV.MatchedBRVLocalAmount IS NULL, 0, matchedBRV.MatchedBRVLocalAmount ) ) + ( IF ( InvoicedBRV.BRVLocalAmount IS NULL, 0, InvoicedBRV.BRVLocalAmount ) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceLocalAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceLocalAmount *- 1 ) -  IFNULL(Subsequentcollection.SubsequentCollectionLocalAmount,0))
                ) AS balanceSubsequentCollectionLocal,
                (
                mainQuery.documentTransAmount + ( IF ( matchedBRV.MatchedBRVTransAmount IS NULL, 0, matchedBRV.MatchedBRVTransAmount ) ) + ( IF ( InvoicedBRV.BRVTransAmount IS NULL, 0, InvoicedBRV.BRVTransAmount ) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceTransAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceTransAmount *- 1 ) -  IFNULL(Subsequentcollection.SubsequentCollectionTransAmount,0))
                ) AS balanceSubsequentCollectionTrans,
            
                mainQuery.customerName,
                mainQuery.customerName2,
                mainQuery.creditDays,
                mainQuery.CutomerCode,
                mainQuery.PONumber,
                mainQuery.invoiceDueDate,
                IFNULL(Subsequentcollection.SubsequentCollectionRptAmount,0) as SubsequentCollectionRptAmount,
                IFNULL(Subsequentcollection.SubsequentCollectionLocalAmount,0) as SubsequentCollectionLocalAmount,
                IFNULL(Subsequentcollection.SubsequentCollectionTransAmount,0) as SubsequentCollectionTransAmount,
                Subsequentcollection.docCode as brvInv
            FROM
                (
            SELECT
                erp_generalledger.companySystemID,
                erp_generalledger.companyID,
                companymaster.CompanyName,
                erp_generalledger.serviceLineSystemID,
                erp_generalledger.serviceLineCode,
                erp_generalledger.documentSystemID,
                erp_generalledger.documentID,
                erp_generalledger.documentSystemCode,
                erp_generalledger.documentCode,
                collectionTrackerDetail.comments,
                erp_generalledger.documentDate,
                DATE_FORMAT( documentDate, "%d/%m/%Y" ) AS documentDateFilter,
                erp_generalledger.documentYear,
                erp_generalledger.documentMonth,
                erp_generalledger.chequeNumber,
                erp_generalledger.invoiceNumber,
                erp_generalledger.invoiceDate,
                erp_generalledger.chartOfAccountSystemID,
                erp_generalledger.glCode,
                erp_generalledger.documentNarration,
                erp_generalledger.clientContractID,
                erp_generalledger.supplierCodeSystem,
                erp_generalledger.documentTransCurrencyID,
                currTrans.CurrencyCode as documentTransCurrency,
                currTrans.DecimalPlaces as documentTransDecimalPlaces,
                SUM(erp_generalledger.documentTransAmount) as documentTransAmount,
                erp_generalledger.documentLocalCurrencyID,
                currLocal.CurrencyCode as documentLocalCurrency,
                currLocal.DecimalPlaces as documentLocalDecimalPlaces,
                SUM(erp_generalledger.documentLocalAmount) as documentLocalAmount,
                erp_generalledger.documentRptCurrencyID,
                currRpt.CurrencyCode as documentRptCurrency,
                currRpt.DecimalPlaces as documentRptDecimalPlaces,
                SUM(erp_generalledger.documentRptAmount) as documentRptAmount,
                erp_generalledger.documentType,
                CONCAT(customermaster.CutomerCode," - ",customermaster.CustomerName) as customerName,
                customermaster.CustomerName as customerName2,
                customermaster.CutomerCode,
                customermaster.creditDays,
                erp_custinvoicedirect.PONumber,
                erp_custinvoicedirect.invoiceDueDate,
                serviceline.ServiceLineDes AS serviceLineName
            FROM
                erp_generalledger
                LEFT JOIN currencymaster currTrans ON erp_generalledger.documentTransCurrencyID = currTrans.currencyID
                LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
                LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
                LEFT JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
                LEFT JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                LEFT JOIN serviceline ON erp_generalledger.serviceLineSystemID = serviceline.serviceLineSystemID
                LEFT JOIN erp_custinvoicedirect ON erp_generalledger.documentSystemCode = erp_custinvoicedirect.custInvoiceDirectAutoID AND erp_generalledger.documentSystemID = erp_custinvoicedirect.documentSystemiD AND erp_generalledger.companySystemID = erp_custinvoicedirect.companySystemID
                LEFT JOIN  (            
                        SELECT customerinvoicecollectiondetail.customerInvoiceID, customerinvoicecollectiondetail.comments FROM erp_customerinvoicecollectiondetail  customerinvoicecollectiondetail
                        JOIN (
                            SELECT max(collectionDetailID) AS maxcollectionDetailID, customerInvoiceID  FROM `erp_customerinvoicecollectiondetail` #where customerInvoiceID = 63415 
                            GROUP BY customerInvoiceID
                        ) AS lastCollectionTrackerData on customerinvoicecollectiondetail.collectionDetailID = lastCollectionTrackerData.maxcollectionDetailID AND customerinvoicecollectiondetail.comments IS NOT NULL
                ) AS collectionTrackerDetail
                ON erp_custinvoicedirect.custInvoiceDirectAutoID = collectionTrackerDetail.customerInvoiceID
            WHERE
                ( erp_generalledger.documentSystemID = "20" OR erp_generalledger.documentSystemID = "19" OR erp_generalledger.documentSystemID = "21" )
                AND DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
                AND ( erp_generalledger.chartOfAccountSystemID = ' . $controlAccountsSystemID . ' )
                AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
                GROUP BY erp_generalledger.companySystemID, erp_generalledger.supplierCodeSystem,erp_generalledger.chartOfAccountSystemID,erp_generalledger.documentSystemID,erp_generalledger.documentSystemCode
                ) AS mainQuery
                LEFT JOIN (
                SELECT
                    erp_matchdocumentmaster.companySystemID,
                    erp_matchdocumentmaster.documentSystemID,
                    erp_matchdocumentmaster.PayMasterAutoId,
                    erp_matchdocumentmaster.BPVcode,
                    sum( erp_custreceivepaymentdet.receiveAmountTrans ) AS MatchedBRVTransAmount,
                    sum( erp_custreceivepaymentdet.receiveAmountLocal ) AS MatchedBRVLocalAmount,
                    sum( erp_custreceivepaymentdet.receiveAmountRpt ) AS MatchedBRVRptAmount
                FROM
                    erp_matchdocumentmaster
                    INNER JOIN erp_custreceivepaymentdet ON erp_matchdocumentmaster.companyID = erp_custreceivepaymentdet.companyID
                    AND erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID
                WHERE
                    erp_matchdocumentmaster.matchingConfirmedYN = 1
                    AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '"
                    AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
                    AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
                GROUP BY
                    erp_matchdocumentmaster.PayMasterAutoId,
                    erp_matchdocumentmaster.BPVcode
                ) AS matchedBRV ON mainQuery.documentSystemID = matchedBRV.documentSystemID
                AND mainQuery.companySystemID = matchedBRV.companySystemID
                AND matchedBRV.PayMasterAutoId = mainQuery.documentSystemCode
                LEFT JOIN (
                SELECT
                    erp_customerreceivepayment.custReceivePaymentAutoID,
                    erp_customerreceivepayment.companySystemID,
                    erp_customerreceivepayment.documentSystemID,
                    erp_customerreceivepayment.custPaymentReceiveCode,
                    sum( erp_custreceivepaymentdet.receiveAmountTrans ) AS BRVTransAmount,
                    sum( erp_custreceivepaymentdet.receiveAmountLocal ) AS BRVLocalAmount,
                    sum( erp_custreceivepaymentdet.receiveAmountRpt ) AS BRVRptAmount
                FROM
                    erp_customerreceivepayment
                    INNER JOIN erp_custreceivepaymentdet ON erp_customerreceivepayment.custReceivePaymentAutoID = erp_custreceivepaymentdet.custReceivePaymentAutoID
                WHERE
                    erp_custreceivepaymentdet.bookingInvCode <> "0"
                    AND erp_custreceivepaymentdet.matchingDocID = 0
                    AND erp_customerreceivepayment.approved =- 1
                    AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
                    AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '"
                    AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
                GROUP BY
                    custReceivePaymentAutoID
                ) AS InvoicedBRV ON mainQuery.documentSystemID = InvoicedBRV.documentSystemID
                AND mainQuery.documentSystemCode = InvoicedBRV.custReceivePaymentAutoID
                LEFT JOIN (
                SELECT
                    companySystemID,
                    companyID,
                    addedDocumentSystemID,
                    addedDocumentID,
                    bookingInvCodeSystem,
                    bookingInvCode,
                    sum( receiveAmountTrans ) AS InvoiceTransAmount,
                    sum( receiveAmountLocal ) AS InvoiceLocalAmount,
                    sum( receiveAmountRpt ) AS InvoiceRptAmount
                FROM
                    (
                    SELECT
                        *
                    FROM
                        (
                        SELECT
                            erp_customerreceivepayment.custPaymentReceiveCode,
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
                            AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '"
                            AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
                        ) AS InvoiceFromBRV UNION ALL
                    SELECT
                        *
                    FROM
                        (
                        SELECT
                            erp_matchdocumentmaster.matchingDocCode,
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
                            AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
                            AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '"
                            AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
                        ) AS InvoiceFromMatching
                    ) AS InvoiceFromUNION
                GROUP BY
                    bookingInvCode
                ) AS InvoiceFromBRVAndMatching ON InvoiceFromBRVAndMatching.addedDocumentSystemID = mainQuery.documentSystemID
                AND mainQuery.documentSystemCode = InvoiceFromBRVAndMatching.bookingInvCodeSystem
                LEFT JOIN (
                SELECT
                    erp_custreceivepaymentdet.companySystemID,
                    erp_custreceivepaymentdet.companyID,
                    max( erp_custreceivepaymentdet.custReceivePaymentAutoID ) AS ReceiptSystemID,
                    max( IF ( erp_custreceivepaymentdet.matchingDocID = 0 OR erp_custreceivepaymentdet.matchingDocID IS NULL, erp_customerreceivepayment.custPaymentReceiveCode, erp_matchdocumentmaster.matchingDocCode ) ) AS docCode,
                    erp_custreceivepaymentdet.addedDocumentSystemID,
                    erp_custreceivepaymentdet.addedDocumentID,
                    erp_custreceivepaymentdet.bookingInvCodeSystem,
                    sum( erp_custreceivepaymentdet.receiveAmountTrans ) AS SubsequentCollectionTransAmount,
                    sum( erp_custreceivepaymentdet.receiveAmountLocal ) AS SubsequentCollectionLocalAmount,
                    sum( erp_custreceivepaymentdet.receiveAmountRpt ) AS SubsequentCollectionRptAmount
                FROM
                    erp_custreceivepaymentdet
                    LEFT JOIN erp_customerreceivepayment ON erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
                    LEFT JOIN erp_matchdocumentmaster ON erp_custreceivepaymentdet.matchingDocID = erp_matchdocumentmaster.matchDocumentMasterAutoID
                WHERE
                    erp_custreceivepaymentdet.bookingInvCodeSystem > 0
                    AND DATE(
                            ( IF ( erp_custreceivepaymentdet.matchingDocID = 0 OR erp_custreceivepaymentdet.matchingDocID IS NULL, erp_customerreceivepayment.postedDate, erp_matchdocumentmaster.matchingDocdate ) )
                        )
                    > "' . $asOfDate . '"
                    AND ( IF ( erp_custreceivepaymentdet.matchingDocID = 0 OR erp_custreceivepaymentdet.matchingDocID IS NULL, erp_customerreceivepayment.approved, erp_matchdocumentmaster.matchingConfirmedYN ) ) <> 0
                GROUP BY
                    addedDocumentSystemID,
                    bookingInvCodeSystem
                ) AS Subsequentcollection ON Subsequentcollection.addedDocumentSystemID = mainQuery.documentSystemID
                AND mainQuery.documentSystemCode = Subsequentcollection.bookingInvCodeSystem
                ) AS final
            WHERE
            ' . $whereQry . ' <> 0) as grandFinal ORDER BY PostedDate ASC');
            return ['data' => $output, 'aging' => $aging];
        }
    }
}
