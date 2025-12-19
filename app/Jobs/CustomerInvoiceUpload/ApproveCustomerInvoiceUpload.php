<?php

namespace App\Jobs\CustomerInvoiceUpload;

use App\Jobs\CustomerInvoiceUpload\DeleteCustomerInvoiceUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\DocumentApproved;
use App\Models\UploadCustomerInvoice;
use App\Models\CustomerInvoiceUploadDetail;
use App\helper\CommonJobService;
use App\Models\LogUploadCustomerInvoice;

class ApproveCustomerInvoiceUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $db;
    protected $ciUploadID;
    protected $logID;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($db, $ciUploadID, $logID)
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
        $this->ciUploadID = $ciUploadID;
        $this->logID = $logID;
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

        $ciData = CustomerInvoiceUploadDetail::where('customerInvoiceUploadID', $this->ciUploadID)->get();

        foreach ($ciData as $key => $value) {
            $documentApproveds = DocumentApproved::where('documentSystemCode', $value->custInvoiceDirectID)->where('documentSystemID', 20)->get();
            foreach ($documentApproveds as $documentApproved) {
                if ($value->approvedByUserSystemID > 0) {
                    $documentApproved["approvedComments"] = "Invoice created from customer invoice upload";
                    $documentApproved["db"] = $db;
                    $documentApproved["fromUpload"] = true;
                    $documentApproved["approvedBy"] = $value->approvedByUserSystemID;
                    $approve = \Helper::approveDocument($documentApproved);

                    if (!$approve["success"]) {
                        $errorMsg = $approve['message'];

                        UploadCustomerInvoice::where('id', $this->ciUploadID)->update(['uploadStatus' => 0]);
                        LogUploadCustomerInvoice::where('id', $this->logID)->update([
                            'is_failed' => 1,
                            'error_line' => 0,
                            'log_message' => $errorMsg
                        ]);

                        DeleteCustomerInvoiceUpload::dispatch($db, $uploadCustomerInvoice->id)->onQueue('single');

                        break;
                    }
                } 
            }
        }

        UploadCustomerInvoice::where('id', $this->ciUploadID)->update(['uploadStatus' => 1]);
    }
}
