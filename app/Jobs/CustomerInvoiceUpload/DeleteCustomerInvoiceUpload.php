<?php

namespace App\Jobs\CustomerInvoiceUpload;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\CommonJobService;
use App\helper\CustomerInvoiceService;

class DeleteCustomerInvoiceUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $db;
    protected $ciUploadID;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($db, $ciUploadID)
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

        CustomerInvoiceService::processDeleteCustomerInvoiceUpload($this->ciUploadID);
    }
}
