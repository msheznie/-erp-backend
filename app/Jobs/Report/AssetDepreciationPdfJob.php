<?php

namespace App\Jobs\Report;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\CommonJobService;
use App\Models\FixedAssetDepreciationPeriod;

class AssetDepreciationPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    public $depMasterAutoID;
    public $userIds;
    public $languageCode;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $depMasterAutoID, $userId, $languageCode)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        $this->dispatch_db = $dispatch_db;
        $this->depMasterAutoID = $depMasterAutoID;
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
        $depMasterAutoID = $this->depMasterAutoID;
        $db = $this->dispatch_db;
        CommonJobService::db_switch($db);
        $languageCode = $this->languageCode;
        app()->setLocale($languageCode);
        $currentDate = strtotime(date("Y-m-d H:i:s"));
        $root = "asset-depreciation-pdf/".$currentDate;

        $depreciationPeriods = FixedAssetDepreciationPeriod::with([
            'maincategory_by',
            'financecategory_by',
            'serviceline_by'
        ])->where('depMasterAutoID', $depMasterAutoID)->get();

        if ($depreciationPeriods->isEmpty()) {
            return false;
        }

        // Convert to array to avoid serialization issues with Eloquent collections
        $depreciationPeriodsArray = $depreciationPeriods->map(function($period) {
            $data = $period->toArray();

            if ($period->maincategory_by) {
                $data['maincategory_by'] = $period->maincategory_by->toArray();
            }
            if ($period->financecategory_by) {
                $data['financecategory_by'] = $period->financecategory_by->toArray();
            }
            if ($period->serviceline_by) {
                $data['serviceline_by'] = $period->serviceline_by->toArray();
            }

            return $data;
        })->toArray();
        $grandTotalDepAmountLocal = $depreciationPeriods->sum('depAmountLocal');
        $grandTotalDepAmountRpt = $depreciationPeriods->sum('depAmountRpt');
        // Chunk the array data (300 records per PDF)
        $outputChunkData = array_chunk($depreciationPeriodsArray, 300);
        $totalRecords = count($depreciationPeriodsArray);

        $reportCount = 1;

        foreach ($outputChunkData as $output1) {
            GenerateAssetDepreciationPdf::dispatch($db, $depMasterAutoID, $reportCount, $this->userIds, $output1, count($outputChunkData), $root, $this->languageCode, $totalRecords, $grandTotalDepAmountLocal, $grandTotalDepAmountRpt)->onQueue('single');
            $reportCount++;
        }
    }
}
