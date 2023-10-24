<?php

namespace App\Jobs;

use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\FixedAssetDepreciationPeriod;
use App\Models\FixedAssetMaster;
use App\Jobs\ProcessDepreciation;
use App\Repositories\FixedAssetDepreciationMasterRepository;
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

class CreateDepreciation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
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
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                self::onConnection('database_main');
            }else{
                self::onConnection('database');
            }
        }else{
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }

        $this->dataBase = $dataBase;
        $this->depAutoID = $depAutoID;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(fixedAssetDepreciationMasterRepository $faDepMaster)
    {

        ini_set('max_execution_time', 21600);
        ini_set('memory_limit', -1);

        CommonJobService::db_switch($this->dataBase);
        Log::useFiles(storage_path() . '/logs/depreciation_jobs.log');

        $depMasterAutoID = $this->depAutoID;
        $depMaster = $faDepMaster->find($depMasterAutoID);

        if($depMaster && !$depMaster->is_acc_dep) {
            DB::beginTransaction();
            try {
                Log::info('Depreciation Started');
                Log::info('Depreciation ID - '.$depMasterAutoID);

                if($depMaster) {
                    $depDate = Carbon::parse($depMaster->FYPeriodDateTo);
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
                        ->get();

                    $db = $this->dataBase;

                    $depAmountRptTotal = 0;
                    $depAmountLocalTotal = 0;

                    if (count($faMaster) > 0) {
                        $chunkSize = 100;
                        $totalDataSize = count($faMaster);
                        $chunkDataSizeCounts = ceil($totalDataSize / $chunkSize);
                        $faCounts = 1;

                        $faMaster->chunk($chunkSize)->each(function ($chunk) use ($db, $depMasterAutoID, $depMaster, $depDate, &$faCounts, $chunkDataSizeCounts) {
                            ProcessDepreciation::dispatch($db, $chunk, $depMasterAutoID, $depMaster, $depDate,$faCounts, $chunkDataSizeCounts)->onQueue('single');
                            $faCounts++;
                        });
                    } else {
                        $fixedAssetDepreciationMasterUpdate = $faDepMaster->update(['isDepProcessingYN' => 1], $depMasterAutoID);
                    }
                    DB::commit();
                    Log::info('Depreciation End');
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($this->failed($e));
                DB::beginTransaction();

                JobErrorLogService::storeError($this->dataBase, $depMaster->documentSystemID, $depMasterAutoID, $this->tag, 2, $this->failed($e), "-****----Line No----:".$e->getLine()."-****----File Name----:".$e->getFile());
                $fixedAssetDepreciationMasterUpdate = $faDepMaster->update(['isDepProcessingYN' => 1], $depMasterAutoID);
                DB::commit();
            }
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}
