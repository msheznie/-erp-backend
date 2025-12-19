<?php

namespace App\Jobs\Report;

use App\Jobs\Report\GenerateGlPdfReport;
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
use ZipArchive;
use File;

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
        ini_set('max_execution_time', config('app.report_max_execution_limit'));
        ini_set('memory_limit', -1);
        Log::useFiles(storage_path() . '/logs/geenral-ledger-pdf.log'); 
        $request = $this->requestData;
        $db = $this->dispatch_db;
        CommonJobService::db_switch($db);

        $currentDate = strtotime(date("Y-m-d H:i:s"));
        $root = "general-ledger-pdf/".$currentDate;

        $output = $this->getGeneralLedgerQryForPDF($request);
        $outputChunkData = collect($output)->chunk(300);

        $reportCount = 1;

        foreach ($outputChunkData as $key1 => $output1) {
            GenerateGlPdfReport::dispatch($db, $request, $reportCount, $this->userIds, $output1, count($outputChunkData), $root)->onQueue('single');
            $reportCount++;
        }
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
                        erp_templatesglcode.templatesDetailsAutoID as templatesDetailsAutoID,
                        approveEmp.empName as approvedBy,
                        confirmEmp.empName as confirmedBy,
                        erp_generalledger.documentConfirmedDate,
                        erp_generalledger.documentFinalApprovedDate,
                        erp_templatesglcode.templateMasterID,
                        erp_templatesdetails.templateDetailDescription,
                    IF
                        ( documentLocalAmount > 0, documentLocalAmount, 0 ) AS localDebit,
                    IF
                        ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 ) AS localCredit,                        
                   CASE	
                        WHEN controlAccounts = "BSA" OR controlAccounts = "PLE" THEN
                        (
                            IF ( documentLocalAmount > 0, documentLocalAmount, 0 ) ) - (
                            IF ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 )) ELSE (
                            IF ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0) - (
                            IF ( documentLocalAmount > 0, documentLocalAmount, 0 ) ))
                    END AS localBalanceAmount,
                        erp_generalledger.documentRptCurrencyID,
                    IF
                        ( documentRptAmount > 0, documentRptAmount, 0 ) AS rptDebit,
                    IF
                        ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) AS rptCredit,
                    CASE
                        WHEN controlAccounts = "BSA" OR controlAccounts = "PLE" THEN
                        (
                            IF ( documentRptAmount > 0, documentRptAmount, 0 )) - (
                            IF ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 )) ELSE (
                            IF ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) - (
                            IF ( documentRptAmount > 0, documentRptAmount, 0 ))) 
                    END AS rptBalanceAmount,
                    IF
                        ( erp_generalledger.documentSystemID = 87 OR erp_generalledger.documentSystemID = 71 OR erp_generalledger.documentSystemID = 20 OR erp_generalledger.documentSystemID = 21 OR erp_generalledger.documentSystemID = 19, customermaster.CustomerName, suppliermaster.supplierName ) AS isCustomer 
                    FROM
                        erp_generalledger
                        LEFT JOIN employees as approveEmp ON erp_generalledger.documentFinalApprovedByEmpSystemID = approveEmp.employeeSystemID
                        LEFT JOIN employees as confirmEmp ON erp_generalledger.documentConfirmedByEmpSystemID = confirmEmp.employeeSystemID
                        LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                        LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem 
                        LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID 
                        LEFT JOIN companymaster ON companymaster.companySystemID = erp_generalledger.companySystemID 
                        LEFT JOIN erp_templatesglcode ON erp_templatesglcode.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID AND erp_templatesglcode.templateMasterID IN (
                            SELECT erp_templatesmaster.templatesMasterAutoID FROM erp_templatesmaster
                                  WHERE erp_templatesmaster.isActive = -1 AND  erp_templatesmaster.isBudgetUpload = -1
                        )
                        LEFT JOIN erp_templatesdetails ON erp_templatesdetails.templatesDetailsAutoID = erp_templatesglcode.templatesDetailsAutoID 
                    WHERE
                        erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        AND  erp_generalledger.chartOfAccountSystemID IN (' . join(',', $chartOfAccountId) . ')
                        AND  erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceLineId) . ')
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
                        erp_templatesglcode.templatesDetailsAutoID,
                        approveEmp.empName as approvedBy,
                        confirmEmp.empName as confirmedBy,
                        erp_generalledger.documentConfirmedDate,
                        erp_generalledger.documentFinalApprovedDate,
                        erp_templatesglcode.templateMasterID,
                        erp_templatesdetails.templateDetailDescription,
                        sum( IF ( documentLocalAmount > 0, documentLocalAmount, 0 ) ) AS localDebit,
                        sum( IF ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 ) ) AS localCredit,
                        CASE	
                            WHEN controlAccounts = "BSA" OR controlAccounts = "PLE" THEN
                            (
                                sum( IF ( documentLocalAmount > 0, documentLocalAmount, 0 ) )) - (
                                sum( IF ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 ) )) ELSE (
                                sum( IF ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 ) ) - (
                                sum( IF ( documentLocalAmount > 0, documentLocalAmount, 0 ) ))) 
                        END AS localBalanceAmount,
                        erp_generalledger.documentRptCurrencyID,
                        sum( IF ( documentRptAmount > 0, documentRptAmount, 0 ) ) AS rptDebit,
                        sum( IF ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) ) AS rptCredit,
                        CASE
                            WHEN controlAccounts = "BSA" OR controlAccounts = "PLE" THEN
                            (
                                sum( IF ( documentRptAmount > 0, documentRptAmount, 0 ) )) - (
                                sum( IF ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) )) ELSE (
                                sum( IF ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) ) - (
                                sum( IF ( documentRptAmount > 0, documentRptAmount, 0 ) ))) 
                        END AS rptBalanceAmount,
                        "" AS isCustomer
                    FROM
                        erp_generalledger
                        LEFT JOIN employees as approveEmp ON erp_generalledger.documentFinalApprovedByEmpSystemID = approveEmp.employeeSystemID
                        LEFT JOIN employees as confirmEmp ON erp_generalledger.documentConfirmedByEmpSystemID = confirmEmp.employeeSystemID
                        LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                        LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem 
                        LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID 
                        LEFT JOIN companymaster ON companymaster.companySystemID = erp_generalledger.companySystemID 
                        LEFT JOIN erp_templatesglcode ON erp_templatesglcode.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID AND erp_templatesglcode.templateMasterID IN (
                            SELECT erp_templatesmaster.templatesMasterAutoID FROM erp_templatesmaster
                                  WHERE erp_templatesmaster.isActive = -1 AND  erp_templatesmaster.isBudgetUpload = -1
                        )
                        LEFT JOIN erp_templatesdetails ON erp_templatesdetails.templatesDetailsAutoID = erp_templatesglcode.templatesDetailsAutoID
                        WHERE
                        erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND erp_generalledger.glAccountTypeID = 1
                        AND  erp_generalledger.chartOfAccountSystemID IN (' . join(',', $chartOfAccountId) . ')
                        AND  erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceLineId) . ')
                        AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '"
                    GROUP BY
                        erp_generalledger.companySystemID,
                        erp_generalledger.serviceLineSystemID,
                        erp_generalledger.chartOfAccountSystemID
                        ) AS erp_qry_gl_bf 
                        ) AS GL_final 
                    ORDER BY
                        documentDate, glCode ASC';
        $output = \DB::select($query);
        //dd(DB::getQueryLog());
        return $output;
    }
}
