<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AttendanceCrossDayPullingInitiate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dispatchDb;
    public $signature;
    public $attDate;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatchDb, $signature, $attDate)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->dispatchDb = $dispatchDb;
        $this->signature = $signature;
        $this->attDate = $attDate;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        Log::useFiles( CommonJobService::get_specific_log_file('attendance-cross-day-clockOut') );

        CommonJobService::db_switch( $this->dispatchDb );
        $companies = CommonJobService::get_active_companies($this->signature);

        if(empty($companies)){
            $msg = "There is not a single company found for process the {$this->signature}";
            $msg .= " in {$this->dispatchDb} DB";
            Log::error("$msg \t on file: " . __CLASS__ ." \tline no :".__LINE__);

            return;
        }

        $seconds = 0;

        foreach ($companies as $companyId) {

            $seconds += 30;

            $msg = "Company id  {$companyId} added to the queue in {$this->dispatchDb} DB ( {$this->attDate} )";

            AttendanceCrossDayPulling::dispatch($this->dispatchDb, $companyId, $this->attDate)
                ->delay(now()->addSeconds($seconds));
        }

    }
}
