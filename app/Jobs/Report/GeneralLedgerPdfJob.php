<?php

namespace App\Jobs\Report;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Company;
use App\Models\CurrencyMaster;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Services\WebPushNotificationService;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;

class GeneralLedgerPdfJob implements ShouldQueue
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
        Log::useFiles(storage_path() . '/logs/geenral-ledger-pdf.log'); 
        $request = $this->requestData;
        $db = $this->dispatch_db;
        CommonJobService::db_switch($db);

        $reportID = $request->reportID;
        $reportTypeID = $request->reportTypeID;
        $type = $request->type;
        $companyCurrency = \Helper::companyCurrency($request->companySystemID);
        $checkIsGroup = Company::find($request->companySystemID);
        $data = array();
        $output = $this->getGeneralLedgerQryForPDF($request);
        $decimalPlace = array();
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

        $extraColumns = [];
        if (isset($request->extraColoumns) && count($request->extraColoumns) > 0) {
            $extraColumns = collect($request->extraColoumns)->pluck('id')->toArray();
        }

        $requestCurrencyLocal = CurrencyMaster::where('currencyID', $currencyIdLocal)->first();
        $requestCurrencyRpt = CurrencyMaster::where('currencyID', $currencyIdRpt)->first();

        $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
        $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

        $currencyLocal = $requestCurrencyLocal->CurrencyCode;
        $currencyRpt = $requestCurrencyRpt->CurrencyCode;

        $totaldocumentLocalAmountDebit = array_sum(collect($output)->pluck('localDebit')->toArray());
        $totaldocumentLocalAmountCredit = array_sum(collect($output)->pluck('localCredit')->toArray());
        $totaldocumentRptAmountDebit = array_sum(collect($output)->pluck('rptDebit')->toArray());
        $totaldocumentRptAmountCredit = array_sum(collect($output)->pluck('rptCredit')->toArray());

        $finalData = array();
        foreach ($output as $val) {
            $finalData[$val->glCode . ' - ' . $val->AccountDescription][] = $val;
        }

        $dataArr = array(
            'reportData' => $finalData,
            'extraColumns' => $extraColumns,
            'companyName' => $checkIsGroup->CompanyName,
            'isGroup' => $checkIsGroup->isGroup,
            'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2,
            'currencyLocal' => $currencyLocal,
            'decimalPlaceLocal' => $decimalPlaceLocal,
            'decimalPlaceRpt' => $decimalPlaceRpt,
            'currencyRpt' => $currencyRpt,
            'reportDate' => date('d/m/Y H:i:s A'),
            'fromDate' => \Helper::dateFormat($request->fromDate),
            'toDate' => \Helper::dateFormat($request->toDate),
            'totaldocumentLocalAmountDebit' =>  round((isset($totaldocumentLocalAmountDebit) ? $totaldocumentLocalAmountDebit : 0), 3),
            'totaldocumentLocalAmountCredit' => round((isset($totaldocumentLocalAmountCredit) ? $totaldocumentLocalAmountCredit : 0), 3),
            'totaldocumentRptAmountDebit' => round((isset($totaldocumentRptAmountDebit) ? $totaldocumentRptAmountDebit : 0), 3),
            'totaldocumentRptAmountCredit' => round((isset($totaldocumentRptAmountCredit) ? $totaldocumentRptAmountCredit : 0), 3),
        );

        $html = view('print.report_general_ledger', $dataArr);

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        $pdf_content =  $pdf->setPaper('a4', 'landscape')->setWarnings(false)->output();

        $fileName = 'general_ledger_'.strtotime(date("Y-m-d H:i:s")).'.pdf';
        $path = "general-ledger/repots/".$fileName;
        $disk = 's3';
        $result = Storage::disk($disk)->put($path, $pdf_content);
        
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');

        $webPushData = [
            'title' => "Financial General Ledger Report PDF has been generated",
            'body' => 'Period : '.$fromDate.' - '.$toDate,
            'url' => "",
            'path' => $path,
        ];

        WebPushNotificationService::sendNotification($webPushData, 3, $this->userIds);
    }

    function getGeneralLedgerQryForPDF($request)
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

        $glCodes = (array)$request->glCodes;
        $type = $request->type;
        $chartOfAccountId = array_filter(collect($glCodes)->pluck('chartOfAccountSystemID')->toArray());

        $departments = (array)$request->departments;
        $serviceLineId = array_filter(collect($departments)->pluck('serviceLineSystemID')->toArray());

        array_push($serviceLineId, 24);

        $contracts = (array)$request->contracts;
        $contractsId = array_filter(collect($contracts)->pluck('contractUID')->toArray());

        array_push($contractsId, 159);
        //contracts

        //DB::enableQueryLog();
        $query = 'SELECT *
                    FROM
                        (
                    SELECT
                        *
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
                        erp_generalledger.supplierCodeSystem,
                        erp_generalledger.documentLocalCurrencyID,
                        chartofaccounts.AccountDescription,
                        companymaster.CompanyName,
                    IF
                        ( documentLocalAmount > 0, documentLocalAmount, 0 ) AS localDebit,
                    IF
                        ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 ) AS localCredit,
                        erp_generalledger.documentRptCurrencyID,
                    IF
                        ( documentRptAmount > 0, documentRptAmount, 0 ) AS rptDebit,
                    IF
                        ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) AS rptCredit,
                    IF
                        ( erp_generalledger.documentSystemID = 20 OR erp_generalledger.documentSystemID = 21 OR erp_generalledger.documentSystemID = 19, customermaster.CustomerName, suppliermaster.supplierName ) AS isCustomer,
                        approveEmp.empName as approvedBy,
                        confirmEmp.empName as confirmedBy,
                        erp_generalledger.documentConfirmedDate,
                        erp_generalledger.documentFinalApprovedDate
                    FROM
                        erp_generalledger
                        LEFT JOIN employees as approveEmp ON erp_generalledger.documentFinalApprovedByEmpSystemID = approveEmp.employeeSystemID
                        LEFT JOIN employees as confirmEmp ON erp_generalledger.documentConfirmedByEmpSystemID = confirmEmp.employeeSystemID
                        LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                        LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem
                        LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
                        LEFT JOIN companymaster ON companymaster.companySystemID = erp_generalledger.companySystemID
                    WHERE
                        erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        AND  erp_generalledger.chartOfAccountSystemID IN (' . join(',', $chartOfAccountId) . ')
                        AND  erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceLineId) . ')
                        AND  erp_generalledger.contractUID IN (' . join(',', $contractsId) . ')
                        ) AS erp_qry_GL UNION ALL
                    SELECT
                        *
                    FROM
                        (
                    SELECT
                        erp_generalledger.companySystemID,
                        erp_generalledger.companyID,
                        erp_generalledger.serviceLineSystemID,
                        erp_generalledger.serviceLineCode,
                        "" AS documentSystemID,
                        "" AS documentID,
                        "" AS documentSystemCode,
                        "" AS documentCode,
                        "" AS documentDate,
                        erp_generalledger.chartOfAccountSystemID,
                        erp_generalledger.glCode,
                        "BS" AS glAccountType,
                        "Opening Balance" AS documentNarration,
                        "" AS clientContractID,
                        "" AS supplierCodeSystem,
                        erp_generalledger.documentLocalCurrencyID,
                        chartofaccounts.AccountDescription,
                        companymaster.CompanyName,
                        sum( IF ( documentLocalAmount > 0, documentLocalAmount, 0 ) ) AS localDebit,
                        sum( IF ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 ) ) AS localCredit,
                        erp_generalledger.documentRptCurrencyID,
                        sum( IF ( documentRptAmount > 0, documentRptAmount, 0 ) ) AS rptDebit,
                        sum( IF ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) ) AS rptCredit,
                        "" AS isCustomer,
                        approveEmp.empName as approvedBy,
                        confirmEmp.empName as confirmedBy,
                        erp_generalledger.documentConfirmedDate,
                        erp_generalledger.documentFinalApprovedDate
                    FROM
                        erp_generalledger
                        LEFT JOIN employees as approveEmp ON erp_generalledger.documentFinalApprovedByEmpSystemID = approveEmp.employeeSystemID
                        LEFT JOIN employees as confirmEmp ON erp_generalledger.documentConfirmedByEmpSystemID = confirmEmp.employeeSystemID
                        LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                        LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem
                        LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
                        LEFT JOIN companymaster ON companymaster.companySystemID = erp_generalledger.companySystemID
                    WHERE
                        erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND erp_generalledger.glAccountType = "BS"
                        AND  erp_generalledger.chartOfAccountSystemID IN (' . join(',', $chartOfAccountId) . ')
                        AND  erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceLineId) . ')
                        AND  erp_generalledger.contractUID IN (' . join(',', $contractsId) . ')
                        AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '"
                    GROUP BY
                        erp_generalledger.glCode,
                        erp_generalledger.companySystemID,
                        erp_generalledger.serviceLineSystemID,
                        erp_generalledger.chartOfAccountSystemID
                        ) AS erp_qry_gl_bf
                        ) AS GL_final
                    ORDER BY
                        documentDate,glCode ASC';
        $output = \DB::select($query);
        //dd(DB::getQueryLog());
        return $output;
    }
}
