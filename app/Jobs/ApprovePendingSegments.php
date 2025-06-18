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

        DB::table('serviceline')->where('approved_yn', '!=', 1)->where('isDeleted', '!=' , 1)->orderBy('serviceLineSystemID')->chunk(50, function ($segments) use ($db) {
            foreach ($segments as $segment) {
                $tempData = (array) $segment;
                $tempData = is_array($tempData) ? $tempData : $tempData->toArray();
                $tempData['isAutoCreateDocument'] = true;
                if ($tempData['confirmed_yn'] == 0) {
                    $tempData['confirmed_yn'] = 1;
                    // Confirm & Approve
                    $controller = app(SegmentMasterAPIController::class);
                    $dataset = new Request();
                    $dataset->merge($db);
                    $response = $controller->updateSegmentMaster($dataset);
                    if ($response['status']) {
                        $this->approvePendingSegments($tempData['documentSystemID'],$tempData['serviceLineSystemID'], $db);
                    }
                }
                else {
                    // Approve
                    $this->approvePendingSegments($tempData['documentSystemID'],$tempData['serviceLineSystemID'], $tenantDb);
                }
            }
        });
    }

    public function approvePendingSegments($documentSystemID, $serviceLineSystemID, $db) {
        $autoApproveParams = DocumentAutoApproveService::getAutoApproveParams($documentSystemID,$serviceLineSystemID);
        $autoApproveParams['db'] = $db;

        return Helper::approveDocument($autoApproveParams);
    }
}
