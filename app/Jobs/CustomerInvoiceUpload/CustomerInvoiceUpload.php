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
    public function handle(): void
    {
        ini_set('max_execution_time', 21600);
        ini_set('memory_limit', -1);
        $uploadData = $this->uploadData;
        $db = $this->db;

        CommonJobService::db_switch($db);

        $uploadCustomerInvoice = $uploadData['uploadCustomerInvoice'];
        $logUploadCustomerInvoice = $uploadData['logUploadCustomerInvoice'];
        $uploadId = $uploadCustomerInvoice->id;

        Log::info('[CustomerInvoiceUpload] Job started', [
            'upload_id' => $uploadId,
            'db'        => $db,
        ]);

        $employee        = $uploadData['employee'];
        $objPHPExcel     = $uploadData['objPHPExcel'];
        $uploadedCompany = $uploadData['uploadedCompany'];

        $sheet         = $objPHPExcel->getActiveSheet();
        $startRow      = 13;
        $highestRow    = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        Log::info('[CustomerInvoiceUpload] Spreadsheet loaded', [
            'upload_id'      => $uploadId,
            'highest_row'    => $highestRow,
            'highest_column' => $highestColumn,
        ]);

        $detailRows = [];
        $rowNumber  = 13;

        for ($row = $startRow; $row <= $highestRow; ++$row) {
            $rowData = [];
            for ($col = 'A'; $col <= $highestColumn; ++$col) {
                $cellValue = $sheet->getCell($col . $row)->getValue();

                if ($col == 'E' || $col == 'F') {
                    if (is_numeric($cellValue) && $cellValue > 25569) {
                        $unixTimestamp = ($cellValue - 25569) * 86400;
                        $day           = date('d', $unixTimestamp);
                        $month         = date('m', $unixTimestamp);
                        $year          = date('Y', $unixTimestamp);
                        $cellValue     = sprintf('%02d/%02d/%04d', $month, $day, $year);
                    }
                }

                if ($col == 'G') {
                    $cellValue = (string) $cellValue;
                }

                $rowData[] = $cellValue;
            }

            $rowData[]    = $rowNumber;
            $detailRows[] = $rowData;
            $rowNumber++;
        }

        $detailRows           = collect($detailRows)->groupBy(6);
        $customerInvoiceCount = 0;

        Log::info('[CustomerInvoiceUpload] Validating invoice numbers for duplicates', [
            'upload_id'    => $uploadId,
            'group_count'  => $detailRows->count(),
        ]);

        foreach ($detailRows as $invoiceNo => $detailValue) {
            if ($invoiceNo != null) {
                $ifExistCustomerInvoiceDirect = CustomerInvoiceDirect::where('customerInvoiceNo', $invoiceNo)->first();

                if ($ifExistCustomerInvoiceDirect) {
                    $errorMsg = "Customer Invoice No $invoiceNo already exist.";
                    $rowData  = collect($detailValue)->first();

                    Log::warning('[CustomerInvoiceUpload] Duplicate invoice number found', [
                        'upload_id'  => $uploadId,
                        'invoice_no' => $invoiceNo,
                        'error_line' => isset($rowData[20]) ? $rowData[20] : null,
                    ]);

                    UploadCustomerInvoice::where('id', $uploadId)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update([
                        'is_failed'   => 1,
                        'error_line'  => isset($rowData[20]) ? $rowData[20] : "",
                        'log_message' => $errorMsg,
                    ]);

                    CustomerInvoiceService::processDeleteCustomerInvoiceUpload($uploadId);
                    return;
                }

                $customerInvoiceCount++;
            }
        }

        UploadCustomerInvoice::where('id', $uploadId)->update(['totalInvoices' => $customerInvoiceCount]);

        Log::info('[CustomerInvoiceUpload] Dispatching sub-jobs', [
            'upload_id'     => $uploadId,
            'invoice_count' => $customerInvoiceCount,
        ]);

        foreach ($detailRows as $invoiceNo => $ciData) {
            if ($invoiceNo != null) {
                CustomerInvoiceUploadSubJob::dispatch($db, $ciData, $uploadData)->onQueue('single');
            }
        }

        Log::info('[CustomerInvoiceUpload] Job completed successfully', [
            'upload_id'     => $uploadId,
            'invoice_count' => $customerInvoiceCount,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        $uploadData              = $this->uploadData;
        $uploadCustomerInvoice   = $uploadData['uploadCustomerInvoice'] ?? null;
        $logUploadCustomerInvoice = $uploadData['logUploadCustomerInvoice'] ?? null;
        $uploadId                = $uploadCustomerInvoice->id ?? null;

        Log::error('[CustomerInvoiceUpload] Job failed', [
            'upload_id' => $uploadId,
            'db'        => $this->db,
            'error'     => $exception->getMessage(),
            'file'      => $exception->getFile(),
            'line'      => $exception->getLine(),
            'trace'     => $exception->getTraceAsString(),
        ]);

        if ($uploadId) {
            UploadCustomerInvoice::where('id', $uploadId)->update(['uploadStatus' => 0]);
        }

        if ($logUploadCustomerInvoice) {
            LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update([
                'is_failed'   => 1,
                'log_message' => '[Job failed] ' . $exception->getMessage(),
            ]);
        }
    }
}
