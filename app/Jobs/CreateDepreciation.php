<?php

namespace App\Jobs;

use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\FixedAssetDepreciationMaster;
use App\Models\FixedAssetDepreciationPeriod;
use App\Models\FixedAssetMaster;
use App\Repositories\FixedAssetDepreciationMasterRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
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
    protected $depreciation;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($depreciation)
    {
        $this->depreciation = $depreciation;
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
            $depMasterAutoID = $this->depreciation;
            $depMaster = $faDepMaster->find($depMasterAutoID);
            if($depMaster) {
                $depDate = Carbon::parse($depMaster->FYPeriodDateTo);
                $faMaster = FixedAssetMaster::with(['depperiod_by' => function ($query) {
                    $query->selectRaw('SUM(depAmountRpt) as depAmountRpt,SUM(depAmountLocal) as depAmountLocal,faID');
                    $query->groupBy('faID');
                }])->isDisposed()->ofCompany([$depMaster->companySystemID])->orderBy('faID', 'desc')->get();
                $depAmountRptTotal = 0;
                $depAmountLocalTotal = 0;
                if ($faMaster) {
                    $finalData = [];
                    foreach ($faMaster as $val) {
                        $depAmountRpt = count($val->depperiod_by) > 0 ? $val->depperiod_by[0]->depAmountRpt : 0;
                        $depAmountLocal = count($val->depperiod_by) > 0 ? $val->depperiod_by[0]->depAmountLocal : 0;
                        $nbvLocal = $val->COSTUNIT - $depAmountLocal;
                        $nbvRpt = $val->costUnitRpt - $depAmountRpt;
                        $monthlyLocal = ($val->COSTUNIT * ($val->DEPpercentage / 100)) / 12;
                        $monthlyRpt = ($val->costUnitRpt * ($val->DEPpercentage / 100)) / 12;

                        if ($nbvLocal != 0 || $nbvRpt != 0) {
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
                            $data['createdBy'] = \Helper::getEmployeeID();
                            $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                            $data['depMonthYear'] = $depMaster->depMonthYear;
                            $data['depMonth'] = $val->depMonth;
                            $data['depAmountLocalCurr'] = $depMaster->depLocalCur;
                            $data['depAmountRptCurr'] = $depMaster->depRptCur;

                            if ($nbvLocal < $monthlyLocal) {
                                $data['depAmountLocal'] = $nbvLocal;
                            } else {
                                $data['depAmountLocal'] = $monthlyLocal;
                            }

                            if ($nbvRpt < $monthlyRpt) {
                                $data['depAmountRpt'] = $nbvRpt;
                            } else {
                                $data['depAmountRpt'] = $monthlyRpt;
                            }

                            if ($depAmountRpt == 0 && $depAmountLocal == 0) {
                                $dateDEP = Carbon::parse($val->dateDEP);
                                if ($dateDEP->lessThanOrEqualTo($depDate)) {
                                    $differentMonths = CarbonPeriod::create($dateDEP->format('Y-m-d'), '1 month', $depDate->format('Y-m-d'));
                                    if ($differentMonths) {
                                        foreach ($differentMonths as $dt) {
                                            $companyFinanceYearID = CompanyFinanceYear::ofCompany($depMaster->companySystemID)->where('bigginingDate', '<=', $dt)->where('endingDate', '>=', $dt->format('Y-m-d'))->first();
                                            if ($companyFinanceYearID) {
                                                $data['FYID'] = $companyFinanceYearID->companyFinanceYearID;
                                                $data['depForFYStartDate'] = $companyFinanceYearID->bigginingDate;
                                                $data['depForFYEndDate'] = $companyFinanceYearID->endingDate;
                                                $companyFinancePeriodID = CompanyFinancePeriod::ofCompany($depMaster->companySystemID)->ofDepartment(9)->where('dateFrom', '<=', $dt)->where('dateTo', '>=', $dt->format('Y-m-d'))->first();
                                                $data['FYperiodID'] = $companyFinancePeriodID->companyFinancePeriodID;
                                                $data['depForFYperiodStartDate'] = $companyFinancePeriodID->dateFrom;
                                                $data['depForFYperiodEndDate'] = $companyFinancePeriodID->dateTo;
                                                array_push($finalData, $data);
                                            }
                                        }
                                    }
                                }
                            } else {
                                if ($nbvRpt != 0 && $nbvLocal != 0) {
                                    $data['FYID'] = $depMaster->companyFinanceYearID;
                                    $data['depForFYStartDate'] = $depMaster->FYBiggin;
                                    $data['depForFYEndDate'] = $depMaster->FYEnd;
                                    $data['FYperiodID'] = $depMaster->companyFinancePeriodID;
                                    $data['depForFYperiodStartDate'] = $depMaster->FYPeriodDateFrom;
                                    $data['depForFYperiodEndDate'] = $depMaster->FYPeriodDateTo;
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
                }

                $depDetail = FixedAssetDepreciationPeriod::selectRaw('SUM(depAmountLocal) as depAmountLocal, SUM(depAmountRpt) as depAmountRpt')->OfDepreciation($depMasterAutoID)->first();

                if($depDetail) {
                    $fixedAssetDepreciationMasters = $faDepMaster->update(['depAmountLocal' => $depDetail->depAmountLocal, 'depAmountRpt' => $depDetail->depAmountRpt, 'isDepProcessingYN' => 1], $depMasterAutoID);
                }
                DB::commit();
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
