<?php

namespace App\Jobs\AddBulkItem;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\MaterialRequestService;
use App\Models\ItemIssueMaster;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProcessMaterialIssueBulk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    public $outputData;
    public $companyId;
    public $requestID;
    protected $empID;
    protected $employeeSystemID;
    protected $chunkDataSizeCounts;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $outputData,$companyId,$empID,$employeeSystemID,$chunkDataSizeCounts,$requestID)
    {
        $this->dispatch_db = $dispatch_db;
        $this->outputData = $outputData;
        $this->companyId = $companyId;
        $this->requestID = $requestID;
        $this->empID = $empID;
        $this->employeeSystemID = $employeeSystemID;
        $this->chunkDataSizeCounts = $chunkDataSizeCounts;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $db = $this->dispatch_db;
        CommonJobService::db_switch($db);

        $companyId = $this->companyId;
        $requestID = $this->requestID;
        $empID = $this->empID;
        $employeeSystemID = $this->employeeSystemID;
        $chunkDataSizeCounts = $this->chunkDataSizeCounts;
        DB::beginTransaction();
        try {
            $output = $this->outputData;
            $requestMaster = ItemIssueMaster::find($requestID);
            foreach ($output as $value) {
                $res = MaterialRequestService::validateMaterialIssueItem($value['itemCodeSystem'], $companyId, $requestID);
                            
                if ($res['status']) {
                    MaterialRequestService::saveMaterialIssueItem($value['itemCodeSystem'], $companyId, $requestID, $empID, $employeeSystemID);
                } else {
                    $invalidItems[] = ['itemCodeSystem' => $value['itemCodeSystem'], 'message' => $res['message']];
                    Log::error('Invalid Items');
                    Log::error($value['itemCodeSystem']. " - " .$res['message']);
                }
            }

            $requestMaster->increment('counter');

            $requestMaster->save();

            $newCounterValue = $requestMaster->counter;

            if ($newCounterValue == $chunkDataSizeCounts) {

                ItemIssueMaster::where('itemIssueAutoID', $requestID)->update(['isBulkItemJobRun' => 0]);         
            }
            DB::commit();
        }
        catch (\Exception $e){
            DB::rollback();
            Log::error($e->getMessage());
        }

    }
}
