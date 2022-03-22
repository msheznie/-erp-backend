<?php

namespace App\Jobs;

use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\FixedAssetDepreciationPeriod;
use App\Models\FixedAssetMaster;
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

class CreateDepreciation implements ShouldQueue
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
    public function handle(fixedAssetDepreciationMasterRepository $faDepMaster)
    {
        Log::useFiles(storage_path() . '/logs/depreciation_jobs.log');
        DB::beginTransaction();
        try {
            Log::info('Depreciation Started');
            $depMasterAutoID = $this->depAutoID;
            $depMaster = $faDepMaster->find($depMasterAutoID);
            if($depMaster) {
                if(!$depMaster->is_acc_dep)
                {

                
                $depDate = Carbon::parse($depMaster->FYPeriodDateTo);
                $faMaster = FixedAssetMaster::with(['depperiod_by' => function ($query) {
                    $query->selectRaw('SUM(depAmountRpt) as depAmountRpt,SUM(depAmountLocal) as depAmountLocal,faID');
                    $query->whereHas('master_by', function ($query) {
                        $query->where('approved', -1);
                    });
                    $query->groupBy('faID');
                }])
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

                $depAmountRptTotal = 0;
                $depAmountLocalTotal = 0;
                if (count($faMaster) > 0) {
                    $finalData = [];
                    foreach ($faMaster as $val) {

                        $amount_local = 0;
                
                        $depAmountRpt = count($val->depperiod_by) > 0 ? $val->depperiod_by[0]->depAmountRpt : 0;
                        $depAmountLocal = count($val->depperiod_by) > 0 ? $val->depperiod_by[0]->depAmountLocal : 0;
                        $nbvLocal = $val->COSTUNIT - $depAmountLocal;
                        $nbvRpt = $val->costUnitRpt - $depAmountRpt;
                        $monthlyLocal = (($val->COSTUNIT - $val->salvage_value) * ($val->DEPpercentage / 100)) / 12;
                        $monthlyRpt = (($val->costUnitRpt - $val->salvage_value_rpt) * ($val->DEPpercentage / 100)) / 12;

                
                        if (round($nbvLocal,2) > $val->salvage_value || round($nbvRpt,2) > $val->salvage_value_rpt) {
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
                            $data['depMonthYear'] = $depMaster->depMonthYear;
                            $data['depMonth'] = $val->depMonth;
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

                            if (round($depAmountRpt,2) == 0 && round($depAmountLocal,2) == 0) {

                               
                                $dateDEP = Carbon::parse($val->dateDEP);
                                $dateDEP1 = Carbon::parse($val->dateDEP);

                          
                                if ($dateDEP->lessThanOrEqualTo($depDate)) {


                                    $life_time_month = ($val->depMonth*12) - 1;
                              
                                    $life_time_period = $dateDEP->addMonths($life_time_month);

                               
                                    if($life_time_period < $depDate) // if deprecetion running month greater than deprecetion start month then different month is life time
                                    {
                                        
                                        $differentMonths = CarbonPeriod::create($dateDEP1->format('Y-m-d'), '1 month', $life_time_period->format('Y-m-d'));
                                     
                                    }
                                    else
                                    {
                                        $differentMonths = CarbonPeriod::create($dateDEP1->format('Y-m-d'), '1 month', $depDate->format('Y-m-d'));
                                     
                                    }
                                    
                                  
                                    if ($differentMonths) {
                                        foreach ($differentMonths as $dt) {
                                           
                                            $companyFinanceYearID = CompanyFinanceYear::ofCompany($depMaster->companySystemID)->where('bigginingDate', '<=', $dt)->where('endingDate', '>=', $dt->format('Y-m-d'))->first();
                                            if ($companyFinanceYearID) {
                                               
                                               
                                                $data['FYID'] = $companyFinanceYearID->companyFinanceYearID;
                                                $data['depForFYStartDate'] = $companyFinanceYearID->bigginingDate;
                                                $data['depForFYEndDate'] = $companyFinanceYearID->endingDate;
                                                $companyFinancePeriodID1 = CompanyFinancePeriod::ofCompany($depMaster->companySystemID)->ofDepartment(9)->where('dateFrom', '<=', $dt)->where('dateTo', '>=', $dt->format('Y-m-d'))->first();

                                                
                                                $data['FYperiodID'] = $companyFinancePeriodID1->companyFinancePeriodID;
                                                $data['depForFYperiodStartDate'] = $companyFinancePeriodID1->dateFrom;
                                                $data['depForFYperiodEndDate'] = $companyFinancePeriodID1->dateTo;
                                                $data['timestamp'] = NOW();
                                                array_push($finalData, $data);
                                            }
                                        }

                                        
                                    
                                    }

                                }
                            } else {
                                if (round($nbvRpt,2) != 0 && round($nbvLocal,2) != 0) {
                                    $data['FYID'] = $depMaster->companyFinanceYearID;
                                    $data['depForFYStartDate'] = $depMaster->FYBiggin;
                                    $data['depForFYEndDate'] = $depMaster->FYEnd;
                                    $data['FYperiodID'] = $depMaster->companyFinancePeriodID;
                                    $data['depForFYperiodStartDate'] = $depMaster->FYPeriodDateFrom;
                                    $data['depForFYperiodEndDate'] = $depMaster->FYPeriodDateTo;
                                    $data['timestamp'] = NOW();
                                    array_push($finalData, $data);
                                }
                            }
                        }

                       
                        
                    
                    }
                    if (count($finalData) > 0) {
                        foreach (array_chunk($finalData,1000) as $t) {
                            FixedAssetDepreciationPeriod::insert($t);
                        }
                    }

                    $depDetail = FixedAssetDepreciationPeriod::selectRaw('SUM(depAmountLocal) as depAmountLocal, SUM(depAmountRpt) as depAmountRpt')->OfDepreciation($depMasterAutoID)->first();
                    Log::info('Depreciation processing');
                    if($depDetail) {
                        $fixedAssetDepreciationMasters = $faDepMaster->update(['depAmountLocal' => $depDetail->depAmountLocal, 'depAmountRpt' => $depDetail->depAmountRpt, 'isDepProcessingYN' => 1], $depMasterAutoID);
                    }
                }

                DB::commit();
                Log::info('Depreciation End');
            }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($this->failed($e));
        }
    }


    public function failed($exception)
    {
        return $exception->getMessage();
    }
}
