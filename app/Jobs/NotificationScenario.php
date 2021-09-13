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

    public $scenario_id;
    public $scenario;

    public function __construct($scenario_id, $scenario)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

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
        Log::info("Processing {$this->scenario} . \t on file: " . __CLASS__ ." \tline no :".__LINE__);


        NotificationService::process($this->scenario_id);
    }
}
