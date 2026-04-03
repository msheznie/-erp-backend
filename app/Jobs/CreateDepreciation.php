<?php

namespace App\Jobs;

use App\Jobs\Concerns\UsesDepreciationQueueConnection;
use App\Models\FixedAssetMaster;
use App\Models\FixedAssetDepreciationMaster;
use Carbon\Carbon;
use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\helper\CommonJobService;
use App\Services\JobErrorLogService;

class CreateDepreciation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use UsesDepreciationQueueConnection;

    protected $depAutoID;
    protected $dataBase;
    private $tag = "asset-depreciation";

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($depAutoID, $dataBase)
    {
        $this->configureQueueConnection();
        $this->dataBase = $dataBase;
        $this->depAutoID = $depAutoID;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('max_execution_time', 21600);
        ini_set('memory_limit', -1);
        CommonJobService::db_switch($this->dataBase);

        $depMasterAutoID = $this->depAutoID;
        $depMaster = FixedAssetDepreciationMaster::find($depMasterAutoID);

        if ($depMaster && !$depMaster->is_acc_dep) {
            try {
                DB::transaction(function () use ($depMaster, $depMasterAutoID) {
                    $chunkSize = 100;
                    $db = $this->dataBase;
                    $depDate = Carbon::parse($depMaster->FYPeriodDateTo);
                    $checkTotalRec = FixedAssetMaster::depreciationJobBaseQuery($depMaster->companySystemID, $depDate)
                        ->orderBy('faID', 'desc')
                        ->count();
                    $chunkDataSizeCounts = (int) ceil($checkTotalRec / $chunkSize);

                    $depMaster->totalChunks = $chunkDataSizeCounts;
                    $depMaster->save();

                    for ($i = 1; $i <= $chunkDataSizeCounts; $i++) {
                        ProcessDepreciationQuery::dispatch($i, $db, $depMasterAutoID, $depDate, $chunkDataSizeCounts)->onQueue('single');
                    }
                });
            } catch (Throwable $e) {
                Log::error($e->getMessage(), ['exception' => $e, 'depMasterAutoID' => $depMasterAutoID]);
                DB::transaction(function () use ($depMaster, $depMasterAutoID, $e) {
                    JobErrorLogService::storeError($this->dataBase, $depMaster->documentSystemID, $depMasterAutoID, $this->tag, 2, $this->formatException($e), "-****----Line No----:".$e->getLine()."-****----File Name----:".$e->getFile());
                    FixedAssetDepreciationMaster::where('depMasterAutoID', $depMasterAutoID)->update(['isDepProcessingYN' => 1]);
                });
            }
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error($exception->getMessage(), ['exception' => $exception, 'depMasterAutoID' => $this->depAutoID]);
    }

    private function formatException(Throwable $exception): string
    {
        return $exception->getMessage();
    }
}
