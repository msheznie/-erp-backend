<?php

namespace App\Jobs;

use App\Models\DepreciationPeriodsReferredHistory;
use App\Models\FixedAssetDepreciationMaster;
use App\Models\FixedAssetDepreciationPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateDepreciationAmend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $depAutoID;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($depAutoID)
    {
        $this->depAutoID = $depAutoID;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path() . '/logs/depreciation_amend_jobs.log');
        DB::beginTransaction();
        try {
            $depMasterAutoID = $this->depAutoID;
            $depMaster = FixedAssetDepreciationMaster::find($depMasterAutoID);
            $fetchDepDetails = FixedAssetDepreciationPeriod::ofDepreciation($depMasterAutoID)
                ->get();

            if (!empty($fetchDepDetails)) {
                foreach ($fetchDepDetails as $val) {
                    $val['timesReferred'] = $depMaster->timesReferred;
                    $val['createdDateTime'] = NOW();
                    $val['timestamp'] = NOW();
                }
            }

            $depDetailArray = $fetchDepDetails->toArray();

            if (count($depDetailArray) > 0) {
                foreach (array_chunk($depDetailArray, 1000) as $t) {
                    $storeDepPeriodHistory = DepreciationPeriodsReferredHistory::insert($t);
                }
            }
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            Log::error($this->failed($e));
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}
