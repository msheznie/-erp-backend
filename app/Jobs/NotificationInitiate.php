<?php

namespace App\Jobs;

use App\helper\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class NotificationInitiate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        Log::useFiles( NotificationService::log_file() );
        $active_scenarios = NotificationService::all_active_scenarios();

        if(count($active_scenarios) == 0){
            Log::info("Active notification scenarios not found. \t on file: " . __CLASS__ ." \tline no :".__LINE__);
        }


        foreach ($active_scenarios as $scenario){
            $id = $scenario->id;
            $description = $scenario->scenarioDescription;

            Log::info("{$description} added to queue . \t on file: " . __CLASS__ ." \tline no :".__LINE__);

            NotificationScenario::dispatch($id, $description);
        }
    }
}
