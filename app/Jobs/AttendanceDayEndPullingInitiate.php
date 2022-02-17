<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AttendanceDayEndPullingInitiate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dispatch_db;
    public $signature;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $signature)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->dispatch_db = $dispatch_db;
        $this->signature = $signature;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        CommonJobService::db_switch( $this->dispatch_db );
        

        Log::useFiles( CommonJobService::get_specific_log_file('attendance-clockOut') );

        CommonJobService::get_active_companies($this->signature);
         
    }
}
