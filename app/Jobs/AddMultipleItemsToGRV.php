<?php

namespace App\Jobs;


use App\helper\Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\helper\GRVService;

class AddMultipleItemsToGRV implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $record;
    public $grvMaster;
    public $timeout = 500;
    public $db;
    public $authID;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($record,$grvMaster,$db,$authID)
    {

        if (env('QUEUE_DRIVER_CHANGE','database') == 'database') {
            if (env('IS_MULTI_TENANCY',false)) {
                self::onConnection('database_main');
            }
            else {
                self::onConnection('database');
            }
        }
        else {
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }

        $this->record = $record;
        $this->grvMaster = $grvMaster;
        $this->db = $db;
        $this->authID = $authID;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        GRVService::addMultipleItems($this->record,$this->grvMaster,$this->db,$this->authID);
    }
}
