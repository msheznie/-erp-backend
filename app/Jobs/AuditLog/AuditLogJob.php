<?php

namespace App\Jobs\AuditLog;

use App\Services\AuditLog\ItemFinanceCategorySubAssignedAuditService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\CommonJobService;
use App\Services\AuditLog\ItemFinanceCategoryAuditService;
use App\Services\AuditLog\ErpAttributeAuditService;
use Illuminate\Support\Facades\Log;

class AuditLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $db;
    protected $transactionID;
    protected $tenant_uuid;
    protected $table;
    protected $narration;
    protected $crudType;
    protected $newValue;
    protected $previosValue;
    protected $parentID;
    protected $parentTable;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dataBase, $transactionID, $tenant_uuid, $table, $narration, $crudType, $newValue = [], $previosValue = [], $parentID = null, $parentTable = null, $user)
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
        $this->db = $dataBase;
        $this->transactionID = $transactionID;
        $this->tenant_uuid = $tenant_uuid;
        $this->table = $table;
        $this->narration = $narration;
        $this->crudType = $crudType;
        $this->newValue = $newValue;
        $this->previosValue = $previosValue;
        $this->parentID = $parentID;
        $this->parentTable = $parentTable;
        $this->user = $user;
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


        $auditData = [
            'transactionID' => $this->transactionID,
            'crudType' => $this->crudType,
            'newValue' => $this->newValue,
            'previosValue' => $this->previosValue,
            'parentID' => $this->parentID,
            'parentTable' => $this->parentTable
        ];

        $data = [];
        switch ($this->table) {
            case 'financeitemcategorysub':
                $data = ItemFinanceCategoryAuditService::process($auditData);
                break;
            case 'erp_attributes':
                $data = ErpAttributeAuditService::process($auditData);
                break;
            case 'financeitemcategorysubassigned':
                $data = ItemFinanceCategorySubAssignedAuditService::process($auditData);
                break;
            
            default:
                // code...
                break;
        }

        if (!empty($data)) {
            Log::useFiles(storage_path() . '/logs/audit.log');

            Log::info('data:',[
                        'channel' => 'audit',
                        'transaction_id' => (string) $this->transactionID,
                        'table' => $this->table,
                        'user_name' => $this->user,
                        'tenant_uuid' => $this->tenant_uuid,
                        'crudType' => $this->crudType,
                        'narration' => $this->narration,
                        'date_time' => date('Y-m-d H:i:s'),
                        'parent_id' => (string) $this->parentID,
                        'parent_table' => $this->parentTable,
                        'data' => json_encode($data),
                    ]);
        }
    }
}
