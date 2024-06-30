<?php

namespace App\Jobs;

use App\helper\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class NotificationScenario implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $dispatch_db;
    public $scenario_id;
    public $scenario;

    public function __construct($dispatch_db, $scenario_id, $scenario)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->dispatch_db = $dispatch_db;
        $this->scenario_id = $scenario_id;
        $this->scenario = $scenario;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles( NotificationService::log_file() );

        NotificationService::db_switch( $this->dispatch_db );

        NotificationService::process($this->scenario_id);
    }
}
