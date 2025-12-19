<?php

namespace App\Jobs\DocumentAttachments;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Company;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\JobErrorLogService;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PaymentReleasedToSupplierJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    public $orderData;
    public $empEmail;
    public $mailData;
    public $pdfName;
    private $tag = "payment-released-to-supplier";


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $orderData, $mailData, $pdfName)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        $this->dispatch_db = $dispatch_db;
        $this->orderData = $orderData;
        $this->mailData = $mailData;
        $this->pdfName = $pdfName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order = $this->orderData;
        $db = $this->dispatch_db;
        $dataEmail = $this->mailData;

        Log::useFiles(storage_path() . '/logs/payment_released_to_supplier.log');

        CommonJobService::db_switch($db);


        $html = view('print.payment_remittance_report_treasury_email', $order);
        $pdf = \App::make('dompdf.wrapper');

        $path = public_path() . '/uploads/emailAttachment';
        if (!file_exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $pdf->loadHTML($html)->save($path.$this->pdfName);


        $dataEmail['attachmentFileName'] = realpath($path.$this->pdfName);

        $sendEmail = \Email::sendEmailErp($dataEmail);
        if (!$sendEmail["success"]) {
            Log::error('Error');
            Log::error($sendEmail["message"]);
        }
    }
}
