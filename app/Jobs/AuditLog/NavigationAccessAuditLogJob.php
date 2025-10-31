<?php

namespace App\Jobs\AuditLog;

use App\Services\AuditLog\NavigationAuditLogService;
use App\helper\CommonJobService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class NavigationAccessAuditLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $parameters;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($parameters)
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
        
        $this->parameters = $parameters;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $requestData = $this->parameters['request'];
        $db = $requestData['db'] ?? null;
        CommonJobService::db_switch($db);

        $navigationMenuID = $this->parameters['navigationMenuID'];
        $companyID = $this->parameters['companyID'];
        $accessType = $this->parameters['accessType'];
        $userId = $this->parameters['userId'] ?? null;
        $tokenId = $this->parameters['tokenId'] ?? null;
        
        $fullNavigationData = NavigationAuditLogService::extractNavigationAccessDataInJob(
            $navigationMenuID,
            $companyID,
            $accessType,
            $userId,
            $tokenId,
            $requestData
        );
        
        if (empty($fullNavigationData)) {
            return;
        }

        // Get prepared audit data from service
        $auditDataArray = NavigationAuditLogService::prepareNavigationAccessData($fullNavigationData);
        
        if (empty($auditDataArray)) {
            return;
        }

        // Write to audit log - centralized logging point
        $this->writeToAuditLog($auditDataArray);
    }

    /**
     * Write event data to audit log (centralized logging point)
     *
     * @param array $eventDataArray
     * @return void
     */
    private function writeToAuditLog($eventDataArray)
    {
        try {
            Log::useFiles(storage_path() . '/logs/audit.log');
            
            // Write logs for each language
            foreach ($eventDataArray as $eventData) {
                Log::info('data:', $eventData);
            }
        } catch (\Exception $e) {
            Log::error('Failed to write to audit log: ' . $e->getMessage());
        }
    }
}

