<?php

namespace App\Jobs\CustomerInvoiceUpload;

use App\helper\CommonJobService;
use App\helper\CustomerInvoiceService;
use App\Jobs\CustomerInvoiceUpload\DeleteCustomerInvoiceUpload;
use App\Jobs\CustomerInvoiceUpload\ApproveCustomerInvoiceUpload;
use App\Models\LogUploadCustomerInvoice;
use App\Models\UploadCustomerInvoice;
use App\Models\CustomerInvoiceDirect;
use App\Services\WebPushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomerInvoiceException;

class CustomerInvoiceUploadSubJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $db;
    protected $uploadData;
    protected $uploadMasterData;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($db, $uploadData, $uploadMasterData)
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
        $this->uploadMasterData = $uploadMasterData;
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
        Log::useFiles(storage_path().'/logs/customer_invoice_bulk_insert.log');
        
        ini_set('max_execution_time', 21600);
        ini_set('memory_limit', -1);
        $ciData = $this->uploadData;
        $uploadMasterData = $this->uploadMasterData;

        $uploadCustomerInvoice = $uploadMasterData['uploadCustomerInvoice'];
        $logUploadCustomerInvoice = $uploadMasterData['logUploadCustomerInvoice'];


        DB::beginTransaction();
        try {

            $uploadCICounter = UploadCustomerInvoice::find($uploadCustomerInvoice->id);
            $cICount = $uploadCICounter->counter;
            $totalInvoices = $uploadCICounter->totalInvoices;
            $CustomerInvoiceCreate = CustomerInvoiceService::customerInvoiceCreate($db,$uploadMasterData, $ciData);

            if(!$CustomerInvoiceCreate['status']){
                if (isset($CustomerInvoiceCreate['message'])) {
                    $errorMsg = isset($CustomerInvoiceCreate['message']) ? $CustomerInvoiceCreate['message'] : "Error occured";
                    $excelRow = isset($CustomerInvoiceCreate['excelRow']) ? $CustomerInvoiceCreate['excelRow'] : 0;
                    throw new CustomerInvoiceException($errorMsg, $excelRow);
                } 
            } else {
                $uploadCICounter->increment('counter');
                $uploadCICounter->save();
                $newCounterValue = $uploadCICounter->counter;

                if ($newCounterValue == $totalInvoices) {
                    ApproveCustomerInvoiceUpload::dispatch($db, $uploadCustomerInvoice->id, $logUploadCustomerInvoice->id)->onQueue('single');
                }
            }

            DB::commit();

        } catch (CustomerInvoiceException $e) {
            DB::rollback();
            $errorMessage = $e->getMessage();
            $excelRow = $e->getExcelRow();

            DB::beginTransaction();
            try {
                UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update([
                    'is_failed' => 1,
                    'error_line' => $excelRow,
                    'log_message' => $errorMessage
                ]);

                CustomerInvoiceService::processDeleteCustomerInvoiceUpload($uploadCustomerInvoice->id);

                // DeleteCustomerInvoiceUpload::dispatch($db, $uploadCustomerInvoice->id)->onQueue('single');
                DB::commit();
            } catch (\Exception $innerException) {
                // Log the inner exception
                Log::error('Inner Exception caught: ' . $innerException->getMessage());
                Log::error('Inner Exception Line No: ' . $innerException->getLine());
                Log::error('Inner Exception File: ' . $innerException->getFile());
                Log::error('Inner Exception Stack Trace: ' . $innerException->getTraceAsString());

                // Rollback in case of an exception during rollback
                DB::rollBack();
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Exception caught: ' . $e->getMessage());
            Log::error('Error Line No: ' . $e->getLine());
            Log::error('Error File: ' . $e->getFile());
            Log::error('Stack Trace: ' . $e->getTraceAsString());
            Log::error('---- Customer Invoice Bulk Insert Error ----- ' . date('H:i:s'));
            UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
            LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update([
                'is_failed' => 1,
                'error_line' => $e->getLine(),
                'log_message' => $e->getMessage()
            ]);

            CustomerInvoiceService::processDeleteCustomerInvoiceUpload($uploadCustomerInvoice->id);
            // DeleteCustomerInvoiceUpload::dispatch($db, $uploadCustomerInvoice->id)->onQueue('single');
        }
    }
}
