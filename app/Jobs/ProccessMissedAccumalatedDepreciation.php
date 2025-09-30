<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\FixedAssetDepreciationPeriod;
use App\Models\FixedAssetMaster;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProccessMissedAccumalatedDepreciation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $tenantDb;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenantDb)
    {
        if (env('QUEUE_DRIVER_CHANGE','database') == 'database') {
            if (env('IS_MULTI_TENANCY',false)) {
                self::onConnection('database_main');
            }
            else {
                self::onConnection('database');
            }
        }
        else {
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }

        $this->tenantDb = $tenantDb;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $db = $this->tenantDb;
            CommonJobService::db_switch($db);

            $fixedAssets = FixedAssetDepreciationPeriod::whereNull('depForFYperiodEndDate')->get();


           foreach ($fixedAssets->chunk(100) as $chunk)
           {
                foreach ($chunk as $fixedAsset)
                {
                    $faMaster = FixedAssetMaster::find($fixedAsset->faID);
                    
                    if (!$faMaster) {
                        Log::warning("FixedAssetMaster not found for faID: " . $fixedAsset->faID, [
                            'tenant_db' => $db,
                            'faID' => $fixedAsset->faID
                        ]);
                        continue;
                    }

                    $accumulated_amount = $faMaster->accumulated_depreciation_amount_rpt;
                    $accumulated_date = $faMaster->accumulated_depreciation_date;

                    if (!$accumulated_date) {
                        Log::warning("Accumulated depreciation date is null for faID: " . $fixedAsset->faID, [
                            'tenant_db' => $db,
                            'faID' => $fixedAsset->faID
                        ]);
                        continue;
                    }

                    $output = CompanyFinancePeriod::where('dateFrom', '<=', $accumulated_date)
                        ->where('dateTo', '>=', $accumulated_date)
                        ->where('departmentSystemID', '=', 9)
                        ->where('companySystemID', '=', $faMaster->companySystemID)
                        ->first();

                    if (!$output) {
                        Log::warning("CompanyFinancePeriod not found for faID: " . $fixedAsset->faID, [
                            'tenant_db' => $db,
                            'faID' => $fixedAsset->faID,
                            'accumulated_date' => $accumulated_date,
                            'departmentSystemID' => $faMaster->departmentSystemID,
                            'companySystemID' => $faMaster->companySystemID
                        ]);
                        continue;
                    }

                    $companyFinanceYearID = $output->companyFinanceYearID;
                    $companyFinancePeriodID = $output->companyFinancePeriodID;

                    $finanicialYear = CompanyFinanceYear::find($companyFinanceYearID);
                    $financePeriod = CompanyFinancePeriod::find($companyFinancePeriodID);

                    if (!$finanicialYear || !$financePeriod) {
                        Log::warning("Financial year or period not found for faID: " . $fixedAsset->faID, [
                            'tenant_db' => $db,
                            'faID' => $fixedAsset->faID,
                            'companyFinanceYearID' => $companyFinanceYearID,
                            'companyFinancePeriodID' => $companyFinancePeriodID
                        ]);
                        continue;
                    }

                    $fixedAsset->FYID = $finanicialYear->companyFinanceYearID;
                    $fixedAsset->depForFYStartDate = $finanicialYear->bigginingDate;
                    $fixedAsset->depForFYEndDate = $finanicialYear->endingDate;
                    $fixedAsset->FYperiodID = $financePeriod->companyFinancePeriodID;
                    $fixedAsset->depForFYperiodStartDate = $financePeriod->dateFrom;
                    $fixedAsset->depForFYperiodEndDate = $financePeriod->dateTo;

                    $fixedAsset->save();

                   DB::table('migratedDocs')->insert([
                       'documentSystemID' => 3,
                       'documentSystemCode' => $fixedAsset->faID,
                       'documentCode' => $fixedAsset->faCode,
                       'comment' => "Update General Ledger",
                       'created_at' => Carbon::now()
                   ]);
               }
           }
        } catch (\Exception $e) {
            Log::error("Error in ProccessMissedAccumalatedDepreciation job: " . $e->getMessage(), [
                'tenant_db' => $db,
                'exception' => $e
            ]);
            throw $e;
        }
    }
}
