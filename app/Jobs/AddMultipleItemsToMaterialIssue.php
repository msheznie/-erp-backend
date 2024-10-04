<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Services\Inventory\MaterialIssueService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AddMultipleItemsToMaterialIssue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $record;
    public $materialIssue;
    public $timeout = 500;
    public $db;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($record,$materialIssue,$db)
    {

        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->record = $record;
        $this->materialIssue = $materialIssue;
        $this->db = $db;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        CommonJobService::db_switch($this->db);
        MaterialIssueService::addMultipleItems($this->record,$this->materialIssue);
    }
}
