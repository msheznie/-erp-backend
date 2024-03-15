<?php

namespace App\Jobs;

use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\RecurringVoucherSetup;
use App\Repositories\RecurringVoucherSetupDetailRepository;
use App\Repositories\RecurringVoucherSetupScheduleRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class CreateRecurringVoucherSetupSchedules implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recurringVoucherSetupModel;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(RecurringVoucherSetup $recurringVoucherSetup)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        $this->recurringVoucherSetupModel = $recurringVoucherSetup;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(RecurringVoucherSetupDetailRepository $recurringVoucherSetupDetailRepository, RecurringVoucherSetupScheduleRepository $recurringVoucherSetupScheduleRepository)
    {
        try {
            DB::beginTransaction();
            $recurringVoucher = $this->recurringVoucherSetupModel;

            $processDate = Carbon::parse($recurringVoucher->startDate);
            $noOfDayMonthYear = $recurringVoucher->noOfDayMonthYear;

            $rrvDebitSum = $recurringVoucherSetupDetailRepository->where('recurringVoucherAutoId', $recurringVoucher->recurringVoucherAutoId)->sum('debitAmount');

            for($i = 0; $i < $noOfDayMonthYear; $i++){
                $processDate = $i == 0 ? $processDate : $processDate->addMonth();
                $financeYear = CompanyFinanceYear::whereYear('bigginingDate',$processDate->year)
                    ->whereYear('endingDate',$processDate->year)
                    ->where('companySystemID',$recurringVoucher->companySystemID)
                    ->first();
                $financePeriod = CompanyFinancePeriod::where('companySystemID',$recurringVoucher->companySystemID)
                    ->where('companyFinanceYearID',$financeYear->companyFinanceYearID)
                    ->whereMonth('dateFrom',$processDate->month)
                    ->whereMonth('dateTo',$processDate->month)
                    ->where('departmentSystemID',5)
                    ->first();
                $recurringVoucherSetupScheduleRepository->create([
                    'recurringVoucherAutoId' => $recurringVoucher->recurringVoucherAutoId,
                    'processDate' => $processDate,
                    'amount' => $rrvDebitSum,
                    'companyFinanceYearID' => $financeYear->companyFinanceYearID,
                    'companyFinancePeriodID' => $financePeriod->companyFinancePeriodID,
                    'createdUserSystemID' => $recurringVoucher->createdUserSystemID,
                    'createdUserID' => $recurringVoucher->createdUserID,
                    'createdPcID' => $recurringVoucher->createdPcID
                ]);
            }
            DB::commit();
        }
        catch (\Exception $e)
        {
            DB::rollback();
        }
    }
}
