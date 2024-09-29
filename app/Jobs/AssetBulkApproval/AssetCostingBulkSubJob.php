<?php

namespace App\Jobs\AssetBulkApproval;

use App\helper\CommonJobService;
use App\Models\JobErrorLog;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssetCostingBulkSubJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $db;
    protected $data;
    public function __construct($db, $data)
    {
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                self::onConnection('database_main');
            }else{
                self::onConnection('database');
            }
        }else{
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }
        $this->db = $db;
        $this->data = $data;
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
        Log::useFiles(storage_path() . '/logs/approve_bulk_document.log');
        ini_set('max_execution_time', 21600);
        ini_set('memory_limit', -1);
        $data = $this->data;
        $params = $data['params'];
        $errorData = [];
        try {
            Log::info("Sub Job Starting");

            $approve = \Helper::approveDocument($params);

            if (!$approve["success"]) {
                $errorData[] = [
                    'documentSystemID' => 22,
                    'documentSystemCode' => $params['documentSystemCode'],
                    'tag' => 'general-ledger',
                    'errorType' => 2,
                    'errorMessage' => $approve["message"],
                    'error' => null
                ];
                Log::info($errorData);
                JobErrorLog::insert($errorData);
            }

        } catch(\Exception $e) {
            Log::error('Error Message' . $e->getMessage());
            Log::error('Error Message Line' . $e->getLine());
        }


    }
}
