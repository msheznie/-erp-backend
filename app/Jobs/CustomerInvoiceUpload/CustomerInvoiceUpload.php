<?php

namespace App\Jobs\CustomerInvoiceUpload;

use App\helper\CommonJobService;
use App\helper\CustomerInvoiceService;
use App\Models\LogUploadCustomerInvoice;
use App\Models\UploadCustomerInvoice;
use App\Models\CustomerInvoiceDirect;
use App\Jobs\CustomerInvoiceUpload\CustomerInvoiceUploadSubJob;
use App\Services\WebPushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomerInvoiceException;
use AWS\CRT\HTTP\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

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
           
        Log::info('Customer Invoice Bulk Insert Started');

        $uploadCustomerInvoice = $uploadData['uploadCustomerInvoice'];
        $logUploadCustomerInvoice = $uploadData['logUploadCustomerInvoice'];

        $employee = $uploadData['employee'];
        $objPHPExcel = $uploadData['objPHPExcel'];
        $uploadedCompany = $uploadData['uploadedCompany'];

        $sheet  = $objPHPExcel->getActiveSheet();
        $startRow = 13;
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $detailRows = [];
        $rowNumber = 13;
        
        for ($row = $startRow; $row <= $highestRow; ++$row) {
            $rowData = [];
            for ($col = 'A'; $col <= $highestColumn; ++$col) {
                $cellValue = $sheet->getCell($col . $row)->getValue();

                if ($col == 'E' || $col == 'F') {
                    // Check if the value looks like a numeric date
                    if (is_numeric($cellValue) && $cellValue > 25569) {
                        // Convert the numeric date to day, month, year
                        $unixTimestamp = ($cellValue - 25569) * 86400;
                        $day = date('d', $unixTimestamp);
                        $month = date('m', $unixTimestamp);
                        $year = date('Y', $unixTimestamp);

                        // Format it as MM/DD/YYYY
                        $cellValue = sprintf('%02d/%02d/%04d', $month, $day, $year);
                    }
                }

                $rowData[] = $cellValue;
            }

            $rowData[] = $rowNumber;
            $detailRows[] = $rowData;
            $rowNumber ++;
        }

        $detailRows = collect($detailRows)->groupBy(6);
        $customerInvoiceCount = 0;
        foreach($detailRows as $invoiceNo => $detailValue){
            if($invoiceNo != null){
                $ifExistCustomerInvoiceDirect = CustomerInvoiceDirect::where('customerInvoiceNo',$invoiceNo)->first();
                if($ifExistCustomerInvoiceDirect){
                    $errorMsg = "Customer Invoice No $invoiceNo already exist.";
                    $rowData = collect($detailValue)->first();
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update([
                        'is_failed' => 1,
                        'error_line' => isset($rowData[20]) ? $rowData[20] : "",
                        'log_message' => $errorMsg
                    ]);

                    CustomerInvoiceService::processDeleteCustomerInvoiceUpload($uploadCustomerInvoice->id);
                    return;
                }

                $customerInvoiceCount++;
            }
        }
        
        UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['totalInvoices' => $customerInvoiceCount]);

        foreach($detailRows as $invoiceNo => $ciData){
            if($invoiceNo != null){
                CustomerInvoiceUploadSubJob::dispatch($db, $ciData, $uploadData)->onQueue('single');            
            }    
        }
    }
}
