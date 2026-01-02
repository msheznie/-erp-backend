<?php

namespace App\Console\Commands;

use App\Jobs\AuditLog\SendToVictoriaLogJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateTestAuditLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:generate-test-logs 
                            {--count=1000 : Number of logs to generate}
                            {--transaction-id=3449 : Transaction ID to use}
                            {--table=itemmaster : Table name to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate test audit logs for performance testing';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = (int) $this->option('count');
        $transactionId = $this->option('transaction-id');
        $table = $this->option('table');
        
        $this->info("Generating {$count} test audit logs for transaction_id: {$transactionId}, table: {$table}");
        
        // Sample data for variety
        $locale = 'en'; // Only English locale
        $crudTypes = ['C', 'U', 'D'];
        $userNames = [
            'Administrator Guteck',
            'John Doe',
            'Jane Smith',
            'Ahmed Ali',
            'Maria Garcia',
            'Test User 1',
            'Test User 2',
            'Test User 3'
        ];
        $employeeIds = ['8888', '1001', '1002', '1003', '1004', '1005'];
        $roles = ['Administrator', 'Manager', 'User', 'Viewer', 'Editor'];
        $sessionIds = ['SID120', 'SID121', 'SID122', 'SID123', 'SID124'];
        $docCodes = ['INV002530', 'INV002531', 'INV002532', 'INV002533', 'INV002534'];
        $companySystemIds = ['1', '2', '3'];
        
        // Sample narration templates (English only)
        $narrationTemplates = [
            'has been created',
            'has been updated',
            'has been deleted',
            'has been modified',
            'status changed',
            'details updated',
            'information changed'
        ];
        
        // Sample data changes
        $dataChanges = [
            '[{"amended_field":"unit_of_measure","previous_value":"Each","new_value":"Ltr"}]',
            '[{"amended_field":"unit_of_measure","previous_value":"Ltr","new_value":"KG"}]',
            '[{"amended_field":"unit_of_measure","previous_value":"KG","new_value":"Day"}]',
            '[{"amended_field":"unit_of_measure","previous_value":"Day","new_value":"Dz"}]',
            '[{"amended_field":"item_name","previous_value":"Old Name","new_value":"New Name"}]',
            '[{"amended_field":"price","previous_value":"100","new_value":"150"}]',
            '[{"amended_field":"status","previous_value":"Active","new_value":"Inactive"}]',
            '[{"amended_field":"category","previous_value":"Category A","new_value":"Category B"}]',
        ];
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        $dispatched = 0;
        
        for ($i = 0; $i < $count; $i++) {
            // Randomize data for variety
            $crudType = $crudTypes[array_rand($crudTypes)];
            $userName = $userNames[array_rand($userNames)];
            $employeeId = $employeeIds[array_rand($employeeIds)];
            $role = $roles[array_rand($roles)];
            $sessionId = $sessionIds[array_rand($sessionIds)];
            $docCode = $docCodes[array_rand($docCodes)];
            $companySystemId = $companySystemIds[array_rand($companySystemIds)];
            
            // Generate narration (English only)
            $narration = $docCode . ' ' . $narrationTemplates[array_rand($narrationTemplates)];
            
            // Generate data changes (only for Update operations)
            $data = $crudType === 'U' ? $dataChanges[array_rand($dataChanges)] : '[]';
            
            // Generate date_time with slight variations
            $dateTime = date('Y-m-d H:i:s', time() - rand(0, 86400 * 30)); // Random time in last 30 days
            
            $logData = [
                'channel' => 'audit',
                'transaction_id' => (string) $transactionId,
                'table' => $table,
                'user_name' => $userName,
                'role' => $role,
                'employeeId' => $employeeId,
                'tenant_uuid' => 'local',
                'crudType' => $crudType,
                'narration' => $narration,
                'session_id' => $sessionId,
                'date_time' => $dateTime,
                'module' => 'finance',
                'parent_id' => null,
                'parent_table' => null,
                'data' => $data,
                'locale' => $locale,
                'company_system_id' => $companySystemId,
                'doc_code' => $docCode,
                'log_uuid' => bin2hex(random_bytes(16)),
            ];
            
            // Dispatch job
            SendToVictoriaLogJob::dispatch($logData);
            $dispatched++;
            
            $bar->advance();
            
            // Small delay to avoid overwhelming the queue
            if ($i % 100 === 0 && $i > 0) {
                usleep(100000); // 0.1 second delay every 100 logs
            }
        }
        
        $bar->finish();
        $this->line('');
        $this->info("Successfully dispatched {$dispatched} audit log jobs to queue.");
        $this->info("Logs will be sent to Victoria Logs as jobs are processed.");
        
        return 0;
    }
}

