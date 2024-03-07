<?php

namespace App\Jobs;

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
                $recurringVoucherSetupScheduleRepository->create([
                    'recurringVoucherAutoId' => $recurringVoucher->recurringVoucherAutoId,
                    'processDate' => $i == 0 ? $processDate : $processDate->addMonth(),
                    'RRVcode' => $recurringVoucher->RRVcode,
                    'currencyID' => $recurringVoucher->currencyID,
                    'amount' => $rrvDebitSum,
                    'documentStatus' => $recurringVoucher->documentStatus,
                    'documentSystemID' => $recurringVoucher->documentSystemID,
                    'documentID' => $recurringVoucher->documentID,
                    'companySystemID' => $recurringVoucher->companySystemID,
                    'companyFinanceYearID' => $recurringVoucher->companyFinanceYearID,
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
