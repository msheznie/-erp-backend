<?php

namespace App\Jobs;

use App\Models\DepartmentMaster;
use App\Repositories\CompanyFinancePeriodRepository;
use App\Repositories\CompanyFinanceYearperiodMasterRepository;
use App\Repositories\CompanyFinanceYearRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class CreateFinancePeriod implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $financeYear;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($financeYear)
    {
        $this->financeYear = $financeYear;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CompanyFinanceYearperiodMasterRepository $financeYearPeriodMasterRepo,
                           CompanyFinancePeriodRepository $financePeriodRepo,
                           CompanyFinanceYearRepository $financeYearRepo)
    {
        $financeYear = $this->financeYear;
        Log::useFiles(storage_path() . '/logs/create_finance_period_jobs.log');
        Log::info('Create Finance Period Jobs Start');
        Log::info($financeYear);
        $bigginingDate = new Carbon($financeYear->bigginingDate);
        $endingDate    = new Carbon($financeYear->endingDate);

        $currentMonth = $bigginingDate->month;
        for($i=1;$i <= 12;$i++){
            $firstDay = '01';
            if($i == $bigginingDate->month){
                //$firstDay = $bigginingDate->day;
            }
            $startDate = new Carbon($bigginingDate->year.'-'.$i.'-'.$firstDay);

            //if($i == $endingDate->month){
              //  $endDate   = $endingDate;
            //}else{
                $endDate   = new Carbon(date("Y-m-t", strtotime($startDate)));
            //}
            if($bigginingDate <= $startDate && $endingDate >= $endDate) {
                $dataArray = array(
                    'companySystemID' => $financeYear->companySystemID,
                    'companyID' => $financeYear->companyID,
                    'companyFinanceYearID' => $financeYear->companyFinanceYearID,
                    'dateFrom' => $startDate,
                    'dateTo' => $endDate);
                $financeYearPeriodMasterRepo->create($dataArray);
                Log::info('Created Finance Period : '.$i);
            }else{
                Log::info('Not Created Finance Period : '.$i);
            }
        }

        $departments = DepartmentMaster::where('isFinancialYearYN', 1)
                                        ->get(['departmentSystemID','DepartmentDescription','DepartmentID']);

        $financeYearPeriodMasters  = $financeYearPeriodMasterRepo->findWhere(['companySystemID' => $financeYear->companySystemID,
                                                                         'companyFinanceYearID' => $financeYear->companyFinanceYearID]);

        $percentage = 0;
        $totalPercentage = 0;

        if(count($departments) > 0){
            $percentage = 100/count($departments);
        }

        $count = 0;

        foreach ($departments as $department){
            foreach ($financeYearPeriodMasters as $financeYearPeriodMaster){
                $financePeriod = array(
                    'companySystemID' => $financeYearPeriodMaster->companySystemID,
                    'companyID' => $financeYearPeriodMaster->companyID,
                    'departmentSystemID' => $department->departmentSystemID,
                    'departmentID' => $department->DepartmentID,
                    'companyFinanceYearID' => $financeYearPeriodMaster->companyFinanceYearID,
                    'dateFrom' => $financeYearPeriodMaster->dateFrom,
                    'dateTo' => $financeYearPeriodMaster->dateTo,
                    'createdUserGroup' => $financeYear->createdUserGroup,
                    'createdUserID' => $financeYear->createdUserID,
                    'createdPcID' => $financeYear->createdPcID
                );
                $financePeriodRepo->create($financePeriod);
            }
            $count++;
            if($count == count($departments)){
                $totalPercentage = 100;
            }else{
                $totalPercentage = $totalPercentage + $percentage;
            }

            if( $totalPercentage > 100){
                $totalPercentage = 100;
            }

            $financeYearRepo->update([ 'generateStatus' => $totalPercentage],$financeYear->companyFinanceYearID);
        }
        Log::info($financeYearPeriodMasters);
        Log::info('Create Finance Period Jobs End');
    }
}
