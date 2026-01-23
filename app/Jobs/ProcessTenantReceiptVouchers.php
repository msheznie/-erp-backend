<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Models\CustomerReceivePayment;
use App\Services\UserTypeService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ProcessTenantReceiptVouchers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tenantDb;
    public $header;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenantDb, $header)
    {
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database') {
            if(env('IS_MULTI_TENANCY',false)) {
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
        $this->header = $header;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        Log::channel('receipt_voucher_api_confirmation_logs')->info('Processing receipt vouchers for tenant: ' . $this->tenantDb);
        
        // Switch to tenant database
        CommonJobService::db_switch($this->tenantDb);
        
        // Get system employee
        $employee = UserTypeService::getSystemEmployee();
        
        // Get all unconfirmed receipt vouchers for this tenant
        CustomerReceivePayment::where('confirmedYN', 0)
            ->where('createdUserSystemID', $employee->employeeSystemID)
            ->orderBy('custReceivePaymentAutoID')
            ->chunkById(5, function ($receiptVouchers) {
                $receiptIds = $receiptVouchers->pluck('custReceivePaymentAutoID')->toArray();
            
                // Dispatch batch job for processing 5 receipt vouchers
                ProcessReceiptVoucherBatch::dispatch($this->tenantDb, $receiptIds, $this->header);
            }, 'custReceivePaymentAutoID');
    }
}

