<?php

namespace App\Jobs;

use App\Jobs\Concerns\UsesDepreciationQueueConnection;
use App\Models\FixedAssetMaster;
use App\Models\FixedAssetDepreciationMaster;
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

class ProcessDepreciationQuery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use UsesDepreciationQueueConnection;

    protected $page;
    protected $dataBase;
    protected $depMasterAutoID;
    protected $chunkDataSizeCounts;
    protected $depDate;
    private $tag = "asset-depreciation";
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($page, $dataBase, $depMasterAutoID, $depDate, $chunkDataSizeCounts)
    {
        $this->configureQueueConnection();
        $this->page = $page;
        $this->dataBase = $dataBase;
        $this->depDate = $depDate;
        $this->depMasterAutoID = $depMasterAutoID;
        $this->chunkDataSizeCounts = $chunkDataSizeCounts;
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
        $db = $this->dataBase;
        $depDate = $this->depDate;
        $depMasterAutoID = $this->depMasterAutoID;
        $chunkDataSizeCounts = $this->chunkDataSizeCounts;
        $depMaster = FixedAssetDepreciationMaster::find($depMasterAutoID);
        if (!$depMaster) {
            return;
        }

        try {
            DB::transaction(function () use ($db, $depDate, $depMasterAutoID, $chunkDataSizeCounts, $depMaster) {
                $perPage = 100;
                $page = $this->page;
                $faIds = FixedAssetMaster::depreciationJobBaseQuery($depMaster->companySystemID, $depDate)
                    ->orderBy('faID', 'desc')
                    ->skip(($page - 1) * $perPage)
                    ->take($perPage)
                    ->pluck('faID')
                    ->values()
                    ->all();
                $faCounts = 1;
                ProcessDepreciation::dispatch($db, $faIds, $depMasterAutoID, $depDate, $faCounts, $chunkDataSizeCounts)->onQueue('single');
            });
        } catch (Throwable $e) {
            Log::error($e->getMessage(), ['exception' => $e, 'depMasterAutoID' => $depMasterAutoID]);
            DB::transaction(function () use ($depMasterAutoID, $depMaster, $e) {
                JobErrorLogService::storeError($this->dataBase, $depMaster->documentSystemID, $depMasterAutoID, $this->tag, 2, $this->formatException($e), "-****----Line No----:".$e->getLine()."-****----File Name----:".$e->getFile());
                FixedAssetDepreciationMaster::where('depMasterAutoID', $depMasterAutoID)->update(['isDepProcessingYN' => 1]);
            });
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error($exception->getMessage(), ['exception' => $exception, 'depMasterAutoID' => $this->depMasterAutoID]);
    }

    private function formatException(Throwable $exception): string
    {
        return $exception->getMessage();
    }
}
