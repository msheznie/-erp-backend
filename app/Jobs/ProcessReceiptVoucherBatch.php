<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Models\CustomerReceivePayment;
use App\Models\DocumentApproved;
use App\Traits\DocumentSystemMappingTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ProcessReceiptVoucherBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use DocumentSystemMappingTrait;

    public $tenantDb;
    public $receiptIds;
    public $header;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenantDb, $receiptIds, $header)
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
        $this->receiptIds = $receiptIds;
        $this->header = $header;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path() . '/logs/receipt_voucher_api_confirmation_logs.log');
        
        Log::info('Processing batch of ' . count($this->receiptIds) . ' receipt vouchers for tenant: ' . $this->tenantDb);
        
        // Switch to tenant database
        CommonJobService::db_switch($this->tenantDb);
        
        // Get receipt vouchers by IDs
        $receiptVouchers = CustomerReceivePayment::whereIn('custReceivePaymentAutoID', $this->receiptIds)->get();
        
        foreach ($receiptVouchers as $receipt) {
            try {
                // Confirm document
                $params = array(
                    'autoID' => $receipt->custReceivePaymentAutoID,
                    'company' => $receipt->companySystemID,
                    'document' => $receipt->documentSystemID,
                    'segment' => '',
                    'category' => '',
                    'amount' => '',
                    'receipt' => true,
                    'sendMail' => false,
                    'sendNotication' => false,
                    'isAutoCreateDocument' => true,
                    'fromUpload' => true
                );
                
                $confirmation = \Helper::confirmDocument($params);
                
                if(!$confirmation['success']) {
                    Log::error('Document confirmation failed ('.$receipt->custPaymentReceiveCode.') : ' . ($confirmation['message'] ?? 'Unknown error'));
                    continue;
                }
                
                // Get document approvals
                $documentApproveds = DocumentApproved::where('documentSystemCode', $receipt->custReceivePaymentAutoID)
                    ->where('documentSystemID', $receipt->documentSystemID)
                    ->get();
                
                // Approve documents
                foreach ($documentApproveds as $documentApproved) {
                    $documentApproved["approvedComments"] = "Generated Receipt Voucher through API";
                    $documentApproved["db"] = $this->tenantDb;
                    $documentApproved['empID'] = $receipt->approvedByUserSystemID;
                    $documentApproved['documentSystemID'] = $receipt->documentSystemID;
                    $documentApproved['approvedDate'] = $receipt->approvedDate;
                    $documentApproved['sendMail'] = false;
                    $documentApproved['sendNotication'] = false;
                    $documentApproved['isCheckPrivilages'] = false;
                    $documentApproved['isAutoCreateDocument'] = true;
                    
                    $approval = \Helper::approveDocument($documentApproved);
                    
                    if(!$approval['success']) {
                        Log::error('Document approval failed ('.$receipt->custPaymentReceiveCode.') : ' . ($approval['message'] ?? 'Unknown error'));
                        continue;
                    }
                }
                
                // Store to document system mapping
                $this->storeToDocumentSystemMapping(
                    $receipt->documentSystemID,
                    $receipt->custReceivePaymentAutoID,
                    $this->header
                );
                
                Log::info('Successfully processed receipt voucher: ' . $receipt->custPaymentReceiveCode);
                
            } catch (\Exception $e) {
                Log::error('Error processing receipt voucher ('.$receipt->custPaymentReceiveCode.') : ' . $e->getMessage());
                continue;
            }
        }
        
        Log::info('Completed processing batch of ' . count($this->receiptIds) . ' receipt vouchers for tenant: ' . $this->tenantDb);
    }
}

