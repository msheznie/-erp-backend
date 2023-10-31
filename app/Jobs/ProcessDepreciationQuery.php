<?php

namespace App\Jobs;

use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\FixedAssetDepreciationPeriod;
use App\Models\FixedAssetMaster;
use App\Models\FixedAssetDepreciationMaster;
use App\Jobs\ProcessDepreciation;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                self::onConnection('database_main');
            }else{
                self::onConnection('database');
            }
        }else{
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }

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
        Log::useFiles(storage_path() . '/logs/depreciation_jobs.log');
        $db = $this->dataBase;
        $depDate = $this->depDate;

        $depMasterAutoID = $this->depMasterAutoID;
        $chunkDataSizeCounts = $this->chunkDataSizeCounts;
        
        DB::beginTransaction();
        $depMaster = FixedAssetDepreciationMaster::find($depMasterAutoID);
        try {
            $perPage = 100; // Items per page
            $page = $this->page; // Page number 
            $faMaster = FixedAssetMaster::with(['depperiod_by' => function ($query) {
                            $query->selectRaw('SUM(depAmountRpt) as depAmountRpt,SUM(depAmountLocal) as depAmountLocal,faID');
                            $query->whereHas('master_by', function ($query) {
                                $query->where('approved', -1);
                            });
                            $query->groupBy('faID');
                        },'depperiod_period'])
                            ->where(function($q) use($depDate){
                                $q->isDisposed()
                                    ->orWhere(function ($q1) use($depDate){
                                        $q1->disposed(-1)
                                            ->WhereDate('disposedDate','>',$depDate);
                                    });
                            })
                            ->ofCompany([$depMaster->companySystemID])
                            ->isApproved()
                            ->assetType(1)
                            ->orderBy('faID', 'desc')
                            ->skip(($page - 1) * $perPage) // Skip the items on previous pages
                            ->take($perPage) 
                            ->get()
                            ->toArray();

             if (count($faMaster) > 0) {
                $faCounts = 1;
                ProcessDepreciation::dispatch($db, $faMaster, $depMasterAutoID, $depDate,$faCounts, $chunkDataSizeCounts)->onQueue('single');
            } else {
                $fixedAssetDepreciationMasterUpdate = FixedAssetDepreciationMaster::where('depMasterAutoID', $depMasterAutoID)->update(['isDepProcessingYN' => 1]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($this->failed($e));
            DB::beginTransaction();

            JobErrorLogService::storeError($this->dataBase, $depMaster->documentSystemID, $depMasterAutoID, $this->tag, 2, $this->failed($e), "-****----Line No----:".$e->getLine()."-****----File Name----:".$e->getFile());
            $fixedAssetDepreciationMasterUpdate = FixedAssetDepreciationMaster::where('depMasterAutoID', $depMasterAutoID)->update(['isDepProcessingYN' => 1]);
            DB::commit();
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}
