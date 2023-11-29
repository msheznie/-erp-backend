<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\helper\CustomerInvoiceService;
use App\Models\UploadCustomerInvoice;
use App\Services\WebPushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CustomerInvoiceUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $db;
    protected $uploadData;
    protected $employee;
    protected $decodeFile;

    public function __construct($db, $uploadData)
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
        $this->uploadData = $uploadData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('max_execution_time', 21600);
        ini_set('memory_limit', -1);
        $uploadData = $this->uploadData;
        $db = $this->db;

        CommonJobService::db_switch($db);
        Log::useFiles(storage_path().'/logs/customer_invoice_bulk_insert.log');

        DB::beginTransaction();
        try {
            Log::info('Customer Invoice Bulk Insert Started');

            
            $uploadCustomerInvoice = $uploadData['uploadCustomerInvoice'];
            $CustomerInvoiceCreate = CustomerInvoiceService::customerInvoiceCreate($uploadData);



            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            Log::info('Error Line No: ' . $e->getLine());
            Log::info('Error Line No: ' . $e->getFile());
            Log::info($e->getMessage());
            Log::info('---- Customer Invoicet Bulk Insert Error-----' . date('H:i:s'));
            DB::beginTransaction();
            try {
                UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                DB::commit();
            } catch (\Exception $e){
                UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                DB::commit();
            }
        }

    }
}
