<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Models\Deligation;
use Carbon\Carbon;
use App\Models\EmployeesDepartment;
use App\helper\CommonJobService;

class DelegationActivation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tenantDb;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenantDb)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->tenantDb = $tenantDb;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $tenantDb = $this->tenantDb;
        CommonJobService::db_switch( $this->tenantDb );
        Log::info('started');
        $current_date = Carbon::parse(now())->format('Y-m-d');
        $deligate = Deligation::where('approved',-1)->where('end_date','<',$current_date);
        $dlegations_expire_ids = $deligate->pluck('id');
        $deligate->update(['is_active' => 0]);
        EmployeesDepartment::whereIn('approvalDeligated',$dlegations_expire_ids)->where('employeeSystemID','!=',null)->update(['isActive' => 0]);

        Log::info('pass date updated');

        $dlegationPeriod = Deligation::where('start_date', '<=', $current_date)->where('end_date', '>=', $current_date)->where('approved',-1);
        $dlegationPeriod->update(['is_active' => 1]);
        $dlegations_ids = $dlegationPeriod->pluck('id');
        EmployeesDepartment::whereIn('approvalDeligated',$dlegations_ids)->where('employeeSystemID','!=',null)->update(['isActive' => 1]);
    }
}