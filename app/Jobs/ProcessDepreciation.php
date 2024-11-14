<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\FixedAssetDepreciationMaster;
use App\Models\FixedAssetDepreciationPeriod;
use App\Repositories\FixedAssetDepreciationMasterRepository;
use App\Services\JobErrorLogService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessDepreciation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    public $outputChunkData;
    public $outputData;
    public $depMasterAutoID;
    public $depDate;
    public $chunkDataSizeCounts;
    public $faCounts;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $outputData, $depMasterAutoID, $depDate, $faCounts, $chunkDataSizeCounts)
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

        $this->dispatch_db = $dispatch_db;
        $this->outputData = $outputData;
        $this->depMasterAutoID = $depMasterAutoID;
        $this->depDate = $depDate;
        $this->faCounts = $faCounts;
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
        Log::useFiles(storage_path() . '/logs/depreciation_jobs.log');
        $db = $this->dispatch_db;
        DB::beginTransaction();
        try {
           
            CommonJobService::db_switch($db);
      
            $output = $this->outputData;
            $depMasterAutoID = $this->depMasterAutoID;
            $depDate = $this->depDate;
            $faCounts = $this->faCounts;
            $chunkDataSizeCounts = $this->chunkDataSizeCounts;

            $depMaster = FixedAssetDepreciationMaster::find($depMasterAutoID);
            $finalData = [];

            foreach ($output as $val) {
                $val = (object) $val;
                $amount_local = 0;

                $count = count($val->depperiod_period);

                if($count == 0)
                {
                    $dep_start_date = $val->dateDEP;
                }
                else
                {
                    $offset = $count - 1;
                    $time = strtotime($val->depperiod_period[$offset]['depForFYperiodStartDate']);
                    $dep_start_date = date("Y-m-d h:i:s", strtotime("+1 month", $time));

                }

                $dateDEP = Carbon::parse($dep_start_date);
                $dateDEP1 = Carbon::parse($dep_start_date);

                if ($dateDEP->lessThanOrEqualTo($depDate)) {

                    $life_time_month = ($val->depMonth * 12) - 1;

                    $life_time_period = $dateDEP->addMonths($life_time_month);


                    if ($life_time_period < $depDate) // if deprecetion running month greater than deprecetion start month then different month is life time
                    {

                        $differentMonths = CarbonPeriod::create($dateDEP1->format('Y-m-d'), '1 month', $life_time_period->format('Y-m-d'));

                    } else {
                        $differentMonths = CarbonPeriod::create($dateDEP1->format('Y-m-d'), '1 month', $depDate->format('Y-m-d'));

                    }


                    if ($differentMonths) {

                        $depAmountRptOld = count($val->depperiod_by) > 0 ? $val->depperiod_by[0]['depAmountRpt'] : 0;
                        $depAmountLocalOld = count($val->depperiod_by) > 0 ? $val->depperiod_by[0]['depAmountLocal'] : 0;

                        foreach ($differentMonths as $dt) {

                            $companyFinanceYearID = CompanyFinanceYear::ofCompany($depMaster->companySystemID)->where('bigginingDate', '<=', $dt)->where('endingDate', '>=', $dt->format('Y-m-d'))->first();
                            if ($companyFinanceYearID) {

                                $currentData = collect($finalData);
                                $depAmountLocal = $depAmountLocalOld + $currentData->where('faID',$val->faID)->sum('depAmountLocal');
                                $depAmountRpt = $depAmountRptOld + $currentData->where('faID',$val->faID)->sum('depAmountRpt');

                                $nbvLocal = $val->COSTUNIT - $depAmountLocal;
                                $nbvRpt = $val->costUnitRpt - $depAmountRpt;
                                $monthlyLocal = (($val->COSTUNIT - $val->salvage_value) * ($val->DEPpercentage / 100)) / 12;
                                $monthlyRpt = (($val->costUnitRpt - $val->salvage_value_rpt) * ($val->DEPpercentage / 100)) / 12;

                                if (round($nbvLocal, 2) > $val->salvage_value || round($nbvRpt, 2) > $val->salvage_value_rpt) {
                                    $data['depMasterAutoID'] = $depMasterAutoID;
                                    $data['companySystemID'] = $depMaster->companySystemID;
                                    $data['companyID'] = $depMaster->companyID;
                                    $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                                    $data['serviceLineCode'] = $val->serviceLineCode;
                                    $data['faFinanceCatID'] = $val->AUDITCATOGARY;
                                    $data['faMainCategory'] = $val->faCatID;
                                    $data['faSubCategory'] = $val->faSubCatID;
                                    $data['faID'] = $val->faID;
                                    $data['faCode'] = $val->faCode;
                                    $data['assetDescription'] = $val->assetDescription;
                                    $data['depPercent'] = $val->DEPpercentage;
                                    $data['COSTUNIT'] = $val->COSTUNIT;
                                    $data['costUnitRpt'] = $val->costUnitRpt;
                                    $data['depDoneYN'] = -1;
                                    $data['createdPCid'] = gethostname();
                                    $data['createdBy'] = $depMaster->createdUserID;
                                    $data['createdUserSystemID'] = $depMaster->createdUserSystemID;
                                    $data['depAmountLocalCurr'] = $depMaster->depLocalCur;
                                    $data['depAmountRptCurr'] = $depMaster->depRptCur;

                                    if ($nbvLocal < $monthlyLocal) {
                                        $data['depAmountLocal'] = $nbvLocal;
                                        $amount_local = $nbvLocal;
                                    } else {
                                        $data['depAmountLocal'] = $monthlyLocal;
                                        $amount_local = $monthlyLocal;
                                    }


                                    if ($nbvRpt < $monthlyRpt) {
                                        $data['depAmountRpt'] = $nbvRpt;
                                    } else {
                                        $data['depAmountRpt'] = $monthlyRpt;
                                    }

                                    $data['FYID'] = $companyFinanceYearID->companyFinanceYearID;
                                    $data['depForFYStartDate'] = $companyFinanceYearID->bigginingDate;
                                    $data['depForFYEndDate'] = $companyFinanceYearID->endingDate;
                                    $companyFinancePeriodID1 = CompanyFinancePeriod::ofCompany($depMaster->companySystemID)->ofDepartment(9)->where('dateFrom', '<=', $dt)->where('dateTo', '>=', $dt->format('Y-m-d'))->first();
                                    $periodDate = Carbon::parse($companyFinancePeriodID1->dateFrom);

                                    $data['depMonth'] = $periodDate->format('m');
                                    $data['depMonthYear'] = $periodDate->format('m/Y');
                                    $data['FYperiodID'] = $companyFinancePeriodID1->companyFinancePeriodID;
                                    $data['depForFYperiodStartDate'] = $companyFinancePeriodID1->dateFrom;
                                    $data['depForFYperiodEndDate'] = $companyFinancePeriodID1->dateTo;
                                    $data['timestamp'] = NOW();

                                    array_push($finalData, $data);
                                }
                            }
                        }


                    }

                }

            }

            if (count($finalData) > 0) {
                foreach (array_chunk($finalData, 100) as $t) {
                    FixedAssetDepreciationPeriod::insert($t);
                }
            }

            $depMaster->counter = $depMaster->counter + 1;

            $depMaster->save();

            $newCounterValue = $depMaster->counter;
            $totalChunks = $depMaster->totalChunks;

            $depDetail = FixedAssetDepreciationPeriod::selectRaw('SUM(depAmountLocal) as depAmountLocal, SUM(depAmountRpt) as depAmountRpt')->OfDepreciation($depMasterAutoID)->first();
            if ($depDetail) {
                if ($newCounterValue == $totalChunks) {
                    $fixedAssetDepreciationMasters = FixedAssetDepreciationMaster::where('depMasterAutoID', $depMasterAutoID)->update(['depAmountLocal' => $depDetail->depAmountLocal, 'depAmountRpt' => $depDetail->depAmountRpt, 'isDepProcessingYN' => 1]);
                } else {
                    $fixedAssetDepreciationMasters = FixedAssetDepreciationMaster::where('depMasterAutoID', $depMasterAutoID)->update(['depAmountLocal' => $depDetail->depAmountLocal, 'depAmountRpt' => $depDetail->depAmountRpt]);
                }
            }
            DB::commit();
        }
        catch (\Exception $e){
            DB::rollback();
            Log::error($this->failed($e));
        }

    }
    public function failed($exception)
    {
        return $exception->getMessage();
    }
}
