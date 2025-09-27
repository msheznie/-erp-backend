<?php

namespace App\Jobs\Report;

use App\helper\CommonJobService;
use App\Models\Company;
use App\Models\CurrencyMaster;
use App\Models\GeneralLedger;
use App\Models\SystemGlCodeScenarioDetail;
use App\Report\PdfReport;
use App\Report\PrintPDFService;
use App\Services\WebPushNotificationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use File;

class AccountsPayableReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dispatch_db;
    public $requestData;
    public $userIds;
    public $languageCode;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $request, $userId, $languageCode)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        $this->dispatch_db = $dispatch_db;
        $this->requestData = $request;
        $this->userIds = $userId;
        $this->languageCode = $languageCode;
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
        Log::useFiles(storage_path() . '/logs/account_payable_report.log');
        $db = $this->dispatch_db;
        CommonJobService::db_switch($db);
        $reportTypeId = ($this->requestData->reportTypeID)  ? :null;
        $languageCode = $this->languageCode;
        app()->setLocale($languageCode);
        switch ($reportTypeId) {
            case "SS":
                $request = $this->requestData;
                $request->fromPath = 'pdf';
                $output = $this->getSupplierStatementQRY($request);
                $outputChunkData = collect($output)->chunk(300);
                $reportCount = 1;
                $rootPathDatandTime = strtotime(date("Y-m-d H:i:s"));
                $root = "supplier-payable/".$rootPathDatandTime;

                foreach ($outputChunkData as $output1)
                {
                    GeneratePdfJob::dispatch($db,$request,$reportCount,$this->userIds,$output1,count($outputChunkData), $root, $languageCode)->onQueue('single');

                    $reportCount++;
                }

                break;
        }


    }


    function getSupplierStatementQRY($request)
    {
        $asOfDate = new Carbon($request->fromDate);
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

        $controlAccountsSystemIDs = (array)$request->controlAccountsSystemID;
        $controlAccountsSystemID = collect($controlAccountsSystemIDs)->pluck('id')->toArray();

        $currency = $request->currencyID;

        $path = $request->fromPath;

        $filterOrderBy = 'documentDate';
        if ($path == 'pdf') {
            $filterOrderBy = 'companySystemID';
        }

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
            $invoiceAmountQry = "IFNULL(round( finalAgingDetail.documentAmountLocal, finalAgingDetail.documentLocalDecimalPlaces ),0) AS invoiceAmount";
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

        $results = \DB::select('SELECT
                                finalAgingDetail.companySystemID,
                                finalAgingDetail.companyID,
                                finalAgingDetail.CompanyName,
                                finalAgingDetail.documentSystemID,
                                finalAgingDetail.documentSystemCode,
                                finalAgingDetail.group as supplierGroupName,
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
                                CONCAT(finalAgingDetail.SupplierCode," - ",finalAgingDetail.suppliername) as concatSupplierName,
                                CONCAT(finalAgingDetail.companyID," - ",finalAgingDetail.CompanyName) as concatCompany,
                                finalAgingDetail.glCode,
                                finalAgingDetail.AccountDescription
                            FROM
                            (
                            SELECT
                                MAINQUERY.companySystemID,
                                MAINQUERY.companyID,
                                companymaster.CompanyName,
                                MAINQUERY.documentSystemID,
                                MAINQUERY.documentSystemCode,
                                MAINQUERY.group,
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
                                MAINQUERY.docTransAmount * -1 AS documentAmountTrans,
                                localCurrencyDet.CurrencyCode as localCurrencyCode,
                                localCurrencyDet.DecimalPlaces as documentLocalDecimalPlaces,
                                MAINQUERY.docLocalAmount * -1 AS documentAmountLocal,
                                rptCurrencyDet.CurrencyCode as rptCurrencyCode,
                                rptCurrencyDet.DecimalPlaces as documentRptDecimalPlaces,
                                MAINQUERY.docRptAmount * -1 AS documentAmountRpt,

                                (MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans) * -1 AS PaidAmountTrans,

                                (MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal) * -1 AS PaidAmountLocal,

                                (MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt) * -1 AS PaidAmountRpt,

                                (MAINQUERY.docTransAmount+MAINQUERY.debitNoteMatchedAmountTrans + MAINQUERY.PaidPaymentVoucherTransAmount - MAINQUERY.InvoiceMatchedINMatchingAmountTrans - MAINQUERY.InvoiceMatchedForpaymentAmountTrans) * -1  as balanceAmountTrans,

                                (MAINQUERY.docLocalAmount+MAINQUERY.debitNoteMatchedAmountLocal + MAINQUERY.PaidPaymentVoucherLocalAmount - MAINQUERY.InvoiceMatchedINMatchingAmountLocal - MAINQUERY.InvoiceMatchedForpaymentAmountLocal) * -1  as balanceAmountLocal,

                                (MAINQUERY.docRptAmount + MAINQUERY.debitNoteMatchedAmountRpt + MAINQUERY.PaidPaymentVoucherRptAmount - MAINQUERY.InvoiceMatchedINMatchingAmountRpt - MAINQUERY.InvoiceMatchedForpaymentAmountRpt) * -1 AS balanceAmountRpt,                                              
                                MAINQUERY.glCode,
                                chartofaccounts.AccountDescription
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
                                supplier_groups.group,
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
                                SUM(erp_generalledger.documentTransAmount) * - 1 AS docTransAmount,
                                erp_generalledger.documentLocalCurrencyID,
                                SUM(erp_generalledger.documentLocalAmount) * - 1 AS docLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                SUM(erp_generalledger.documentRptAmount) * - 1 AS docRptAmount,
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
                                LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                                LEFT JOIN supplier_groups ON suppliermaster.supplier_group_id = supplier_groups.id
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
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
                                AND erp_paysupplierinvoicedetail.isRetention = 0
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
                                AND erp_generalledger.chartOfAccountSystemID IN (' . join(',', $controlAccountsSystemID) . ')
                                AND erp_generalledger.supplierCodeSystem IN (' . join(',', $supplierSystemID) . ')
                                AND erp_generalledger.contraYN = 0
                                GROUP BY erp_generalledger.companySystemID, erp_generalledger.supplierCodeSystem,erp_generalledger.chartOfAccountSystemID,erp_generalledger.documentSystemID,erp_generalledger.documentSystemCode
                                ) AS MAINQUERY
                            LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = MAINQUERY.supplierCodeSystem
                            LEFT JOIN companymaster ON MAINQUERY.companySystemID = companymaster.companySystemID
                            LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = MAINQUERY.chartOfAccountSystemID
                            LEFT JOIN currencymaster as transCurrencyDet ON transCurrencyDet.currencyID=MAINQUERY.documentTransCurrencyID
                            LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=MAINQUERY.documentLocalCurrencyID
                            LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=MAINQUERY.documentRptCurrencyID) as finalAgingDetail WHERE ' . $whereQry . ' <> 0 ORDER BY ' . $filterOrderBy . ' ASC;');

        return $this->exchangeGainLoss($results, $currency);
    }

    public function exchangeGainLoss($results, $currency) {

        foreach ($results as $index => $result){
            $exchangeGainLossAccount = SystemGlCodeScenarioDetail::getGlByScenario($result->companySystemID, $result->documentSystemID , "exchange-gainloss-gl");
            $chartOfAccount = GeneralLedger::where('documentSystemCode', $result->documentSystemCode)->where('chartOfAccountSystemID', $exchangeGainLossAccount)->where('companySystemID', $result->companySystemID)->where('documentType', NULL)->where('matchDocumentMasterAutoID', "!=", NULL)->first();
            if(!empty($chartOfAccount)) {
                if ($currency == 1) {
                    $currencyMaster = CurrencyMaster::find($chartOfAccount->documentTransCurrencyID);
                    $decimal = $currencyMaster->DecimalPlaces;
                    $result->exchangeGL = $chartOfAccount->documentTransAmount;
                } else if ($currency == 2) {
                    $currencyMaster = CurrencyMaster::find($chartOfAccount->documentLocalCurrencyID);
                    $result->exchangeGL = $chartOfAccount->documentLocalAmount;
                    $decimal = $currencyMaster->DecimalPlaces;
                } else {
                    $currencyMaster = CurrencyMaster::find($chartOfAccount->documentRptCurrencyID);
                    $result->exchangeGL = $chartOfAccount->documentRptAmount;
                    $decimal = $currencyMaster->DecimalPlaces;
                }
            }
            else {
                $result->exchangeGL = 0;
                $decimal = 3;
            }

            $roundedExchangeGL = round($result->exchangeGL, $decimal);

            if (abs($result->balanceAmount - $roundedExchangeGL) < 0.00001) {

                unset($results[$index]);
            }
        }

        return $results;
    }
}
