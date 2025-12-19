<?php

namespace App\Jobs\AuditLog;

use App\Services\AuditLog\AuthAuditService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\helper\CommonJobService;
use Illuminate\Support\Str;

class AuthAuditLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $event;
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
        $request = $this->parameters['request'];
        $db = $request['db'] ?? null;
        CommonJobService::db_switch($db);

        // Get prepared audit data from service
        $auditDataArray = AuthAuditService::prepareAuditData($this->parameters['event'], $this->parameters);
        
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
                $locale = $eventData['locale'] ?? 'en';
                $translatedData = AuthAuditService::translateEventData($eventData, $locale);
                $translatedData['log_uuid'] = (string) bin2hex(random_bytes(16));
                Log::info('data:', $translatedData);
            }
        } catch (\Exception $e) {
            Log::error('Failed to write to audit log: ' . $e->getMessage());
        }
    }
}
