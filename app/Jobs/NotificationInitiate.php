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

    public $dispatch_db;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->dispatch_db = $dispatch_db;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        NotificationService::db_switch( $this->dispatch_db );

        Log::useFiles( NotificationService::log_file() );
        $active_scenarios = NotificationService::all_active_scenarios();

        if(count($active_scenarios) == 0){
        }


        foreach ($active_scenarios as $scenario){
            $id = $scenario->id;
            $description = $scenario->scenarioDescription;


            NotificationScenario::dispatch($this->dispatch_db, $id, $description);
        }
    }
}
