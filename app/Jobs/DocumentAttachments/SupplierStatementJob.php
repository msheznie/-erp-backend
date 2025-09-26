<?php

namespace App\Jobs\DocumentAttachments;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\JobErrorLogService;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Company;
use App\Models\SupplierContactDetails;
use App\Models\SupplierMaster;

class SupplierStatementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    public $dataArr;
    public $inputData;
    public $languageCode;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $dataArr, $inputData, $languageCode)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        $this->dispatch_db = $dispatch_db;
        $this->dataArr = $dataArr;
        $this->inputData = $inputData;
        $this->languageCode = $languageCode;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {    

        $htmlData = $this->dataArr;
        $db = $this->dispatch_db;
        $input = $this->inputData;
        $languageCode = $this->languageCode;
        app()->setLocale($languageCode);
        Log::useFiles(storage_path() . '/logs/supplier_statement_sent.log');

        CommonJobService::db_switch($db);

        $html = view('print.supplier_statement', $htmlData);
        $path = public_path().'/uploads/emailAttachment';

        if (!file_exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
        $nowTime = time();

        $supplierID = $input['suppliers'][0]['supplierCodeSytem'];
        $fileName = 'supplier_statement_' . $nowTime.$supplierID. '.pdf';
        $filePath = $path . '/' . $fileName;

        // Configure mPDF for landscape A4 format
        $mpdfConfig = [
            'tempDir' => public_path('tmp'),
            'mode' => 'utf-8',
            'format' => 'A4-L', // Landscape format
            'setAutoTopMargin' => 'stretch',
            'autoMarginPadding' => -10,
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 16,
            'margin_bottom' => 16,
            'margin_header' => 9,
            'margin_footer' => 9
        ];

        $mpdf = new \Mpdf\Mpdf($mpdfConfig);
        $mpdf->AddPage('L');
        $mpdf->setAutoBottomMargin = 'stretch';

        try {
            // Clean HTML to make it mPDF compatible
            $html = $this->cleanHtmlForMpdf($html);
            $mpdf->WriteHTML($html);
            $mpdf->Output($filePath, 'F'); // Save to file
        } catch (\Exception $e) {
            Log::error('mPDF Error in SupplierStatementJob: ' . $e->getMessage());
            return; // Exit the job if PDF generation fails
        }


        $fetchSupEmail = SupplierContactDetails::where('supplierID', $supplierID)
            ->get();

        $supplierMaster = SupplierMaster::find($supplierID);

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        $emailSentTo = 0;

        $footer = "<font size='1.5'><i><p><br><br><br>" . trans('custom.save_paper_think_before_print') .
            "<br>" . trans('custom.auto_generated_email_no_reply') . "</font>";
        
        if ($fetchSupEmail) {
            foreach ($fetchSupEmail as $row) {
                if (!empty($row->contactPersonEmail)) {
                    $emailSentTo = 1;
                    $dataEmail['empEmail'] = $row->contactPersonEmail;

                    $dataEmail['companySystemID'] = $input['companySystemID'];

                    $temp = trans('custom.dear_supplier_statement_sent', [
                        'supplierName' => $supplierMaster->supplierName,
                        'companyName' => $company->CompanyName
                    ]) . $footer;

                    $pdfName = realpath($filePath);

                    $dataEmail['isEmailSend'] = 0;
                    $dataEmail['attachmentFileName'] = $pdfName;
                    $dataEmail['alertMessage'] = trans('custom.supplier_statement_report_from', ['companyName' => $company->CompanyName]);
                    $dataEmail['emailAlertMessage'] = $temp;
                    $sendEmail = \Email::sendEmailErp($dataEmail);
                    if (!$sendEmail["success"]) {
                        Log::error('Error');
                        Log::error($sendEmail["message"]);
                    }
                }
            }
        }

        if ($emailSentTo == 0) {
            if ($supplierMaster) {
                if (!empty($supplierMaster->supEmail)) {
                    $emailSentTo = 1;
                    $dataEmail['empEmail'] = $supplierMaster->supEmail;

                    $dataEmail['companySystemID'] = $input['companySystemID'];

                    $temp = trans('custom.dear_supplier_statement_sent', [
                        'supplierName' => $supplierMaster->supplierName,
                        'companyName' => $company->CompanyName
                    ]) . $footer;

                    $pdfName = realpath($path."/supplier_statement_" . $nowTime.$supplierID . ".pdf");

                    $dataEmail['isEmailSend'] = 0;
                    $dataEmail['attachmentFileName'] = $pdfName;
                    $dataEmail['alertMessage'] = trans('custom.supplier_statement_report', ['companyName' => $company->CompanyName]);
                    $dataEmail['emailAlertMessage'] = $temp;
                    $sendEmail = \Email::sendEmailErp($dataEmail);
                    if (!$sendEmail["success"]) {
                        Log::error('Error');
                        Log::error($sendEmail["message"]);
                    }
                }
            }

        }

    }

    /**
     * Clean HTML to make it compatible with mPDF
     * Fixes CSS issues that cause "Undefined index: style" errors
     */
    private function cleanHtmlForMpdf($html)
    {
        // Convert rgba() to hex colors (handle common cases)
        $html = preg_replace('/rgba\((\d+),\s*(\d+),\s*(\d+),\s*0\.1\)/', '#000000', $html);
        $html = preg_replace('/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d.]+)\)/', '#$1$2$3', $html);
        
        // Convert rgb() to hex colors
        $html = preg_replace('/rgb\((\d+),\s*(\d+),\s*(\d+)\)/', '#$1$2$3', $html);
        
        // Remove !important declarations that can cause issues
        $html = preg_replace('/\s*!important\s*/', '', $html);
        
        // Fix problematic CSS properties
        $html = str_replace('border-top: 1px solid #0000001', 'border-top: 1px solid #000000', $html);
        
        // Remove problematic CSS properties that mPDF doesn't handle well
        $html = preg_replace('/opacity\s*:\s*[\d.]+\s*;?/', '', $html);
        $html = preg_replace('/transform[^;]*;?/', '', $html);
        $html = preg_replace('/transform-origin[^;]*;?/', '', $html);
        
        // Fix font-family issues
        $html = preg_replace('/font-family:\s*[^;]*apple-system[^;]*;?/', 'font-family: Arial, sans-serif;', $html);
        
        return $html;
    }
}
