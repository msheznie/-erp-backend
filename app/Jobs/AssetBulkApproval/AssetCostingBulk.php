<?php

namespace App\Jobs\AssetBulkApproval;

use App\helper\CommonJobService;
use App\Models\GRVMaster;
use App\Models\JobErrorLog;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class AssetCostingBulk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $db;
    protected $uploadData;
    public function __construct($db, $uploadData)
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
        $this->uploadData = $uploadData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('max_execution_time', 21600);
        ini_set('memory_limit', -1);
        $uploadData = $this->uploadData;
        $db = $this->db;
        Log::info('switching db '.$db);
        CommonJobService::db_switch($db);
        Log::useFiles(storage_path() . '/logs/approve_bulk_document.log');
        Log::info('switching db '.$db);
        $results = $uploadData['results'];
        $empID = $uploadData['empID'];
        $grvID = $uploadData['grvID'];
        $approvedComments = $uploadData['approvedComments'];

        if(count($results) == 0)
        {
            Log::info('There are no documents to approve');
        }

        foreach($results as $result) {

            $params = array(
                'documentApprovedID' => $result->documentApprovedID,
                'documentSystemCode' => $result->documentSystemCode,
                'documentSystemID' => $result->documentSystemID,
                'approvalLevelID' => $result->approvalLevelID,
                'rollLevelOrder' => $result->rollLevelOrder,
                'approvedComments' => $approvedComments,
                'db' => $db,
                'fromUpload' => true,
                'approvedBy' => $empID
            );
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

        }

        GRVMaster::where('grvAutoID', $grvID)->update(['isProcessing' => 0]);

    }
}
