<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SentCustomerLedger implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $input;
    public $db;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($input, $db)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->input = $input;
        $this->db = $db;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $db = $this->db;
        CommonJobService::db_switch($db);
        Log::info('Starting sendCustomerLedger Job');
        $input = $this->input;
        $customers = $input['customers'];
        $errorMessage = [];

        foreach ($customers as $key => $value) {
            $input['customers'] = [];
            $input['customers'][] = $value;

            SentCustomerLedgerSubJob::dispatch($db, $input);
        }
        if (count($errorMessage) > 0) {
            Log::info($errorMessage);
        } else {
            Log::info('Customer ledger report sent');
        }
    }
}
