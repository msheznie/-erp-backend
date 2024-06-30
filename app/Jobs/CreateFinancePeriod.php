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
        $bigginingDate = new Carbon($financeYear->bigginingDate);
        $endingDate    = new Carbon($financeYear->endingDate);
        $currentMonth  = $bigginingDate->month;
        $currentYear   = $bigginingDate->year;

        for($i = $currentMonth;$i < ($currentMonth + 12);$i++){
            $firstDay = '01';
            if($i == $bigginingDate->month){
                //$firstDay = $bigginingDate->day;
            }

            if($i > 12){
                $month = ($i % 12);
                $currentYear = $bigginingDate->year + floor(($i / 12));
            }else{
                $month = $i;
            }


            $startDate = new Carbon($currentYear.'-'.$month.'-'.$firstDay);
            $endDate   = new Carbon(date("Y-m-t", strtotime($startDate)));

            $formatedEndDate = $endDate->format('Y-m-d'). " 23:59:59";

           // if($bigginingDate <= $startDate && $endingDate >= $endDate) {
                $dataArray = array(
                    'companySystemID' => $financeYear->companySystemID,
                    'companyID' => $financeYear->companyID,
                    'companyFinanceYearID' => $financeYear->companyFinanceYearID,
                    'dateFrom' => $startDate,
                    'dateTo' => $formatedEndDate);
                $financeYearPeriodMasterRepo->create($dataArray);
            //}else{
            //}
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
    }
}
