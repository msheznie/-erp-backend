<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\helper\Helper;
use App\Http\Controllers\API\SegmentMasterAPIController;
use App\Services\DocumentAutoApproveService;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovePendingSegments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $tenantDb;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenantDb)
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

        $this->tenantDb = $tenantDb;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        CommonJobService::db_switch($this->tenantDb);

        try {
            // Use chunkById instead of chunk to avoid issues with orderBy during data modification
            // This ensures we don't miss records when they are updated during processing
            DB::table('serviceline')
                ->where('approved_yn', '!=', 1)
                ->where('isDeleted', '!=', 1)
                ->where('serviceLineSystemID', '!=', 24)
                ->orderBy('serviceLineSystemID')
                ->chunkById(100, function ($segments) use ($db) {
                    foreach ($segments as $segment) {
                        try {
                            $this->processSegment($segment, $db);
                        } catch (\Exception $e) {
                            // Log the error but continue processing other segments
                            Log::error("Error processing segment {$segment->serviceLineSystemID}: " . $e->getMessage(), [
                                'segment_id' => $segment->serviceLineSystemID,
                                'document_id' => $segment->documentSystemID ?? null,
                                'tenant_db' => $db,
                                'exception' => $e
                            ]);
                        }
                    }
                }, 'serviceLineSystemID'); // Specify the column for chunkById
        } catch (\Exception $e) {
            Log::error("Error in ApprovePendingSegments job: " . $e->getMessage(), [
                'tenant_db' => $db,
                'exception' => $e
            ]);
            throw $e;
        }
    }

    /**
     * Process individual segment with proper transaction handling
     */
    private function processSegment($segment, $db)
    {
        DB::beginTransaction();
        
        try {
            $tempData = (array) $segment;
            $tempData = is_array($tempData) ? $tempData : $tempData->toArray();
            $tempData['isAutoCreateDocument'] = true;
            
            if ($tempData['confirmed_yn'] == 0) {
                $tempData['confirmed_yn'] = 1;
                // Confirm & Approve
                $controller = app(SegmentMasterAPIController::class);
                $dataset = new Request();
                $dataset->merge($tempData);
                $response = $controller->updateSegmentMaster($dataset);
                
                if ($response['status']) {
                    $approvalResult = $this->approvePendingSegments(
                        $tempData['documentSystemID'],
                        $tempData['serviceLineSystemID'], 
                        $db
                    );
                    
                    if (!$approvalResult['success']) {
                        throw new \Exception("Approval failed: " . $approvalResult['message']);
                    }
                } else {
                    throw new \Exception("Update segment master failed - ".(isset($response['message']) ? $response['message'] : ""));
                }
            } else {
                // Approve only
                $approvalResult = $this->approvePendingSegments(
                    $tempData['documentSystemID'],
                    $tempData['serviceLineSystemID'], 
                    $db
                );
                
                if (!$approvalResult['success']) {
                    throw new \Exception("Approval failed: " . $approvalResult['message']);
                }
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function approvePendingSegments($documentSystemID, $serviceLineSystemID, $db) {
        $autoApproveParams = DocumentAutoApproveService::getAutoApproveParams($documentSystemID,$serviceLineSystemID);
        $autoApproveParams['db'] = $db;

        return Helper::approveDocument($autoApproveParams);
    }
}
