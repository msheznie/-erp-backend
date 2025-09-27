<?php

namespace App\Jobs\Report;

use App\helper\CommonJobService;
use App\Models\Company;
use App\Report\PdfReport;
use App\Report\PrintPDFService;
use App\Services\WebPushNotificationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use File;
use ZipArchive;
class GeneratePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dispatch_db;
    public $reportCount;
    public $requestData;
    public $userIds;
    public $outputChunkData;
    public $outputData;
    public $rootPath;
    public $dataArr;
    public $languageCode;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $request, $reportCount, $userId, $outputData, $outputChunkData, $rootPath, $languageCode)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        $this->dispatch_db = $dispatch_db;
        $this->requestData = $request;
        $this->reportCount = $reportCount;
        $this->userIds = $userId;
        $this->outputChunkData = $outputChunkData;
        $this->outputData = $outputData;
        $this->rootPath = $rootPath;
        $this->languageCode = $languageCode;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('max_execution_time', config('app.report_max_execution_limit'));
        ini_set('memory_limit', -1);
        $languageCode = $this->languageCode;
        app()->setLocale($languageCode);
        $output = $this->outputData;
        $request = $this->requestData;

        $db = $this->dispatch_db;
        CommonJobService::db_switch($db);

        $checkIsGroup = Company::find($request->companySystemID);
        $companyLogo = $checkIsGroup->logo_url;

        $grandTotal = collect($output)->pluck('balanceAmount')->toArray();
        $grandTotal = array_sum($grandTotal);

        $balanceAmount = collect($output)->pluck('balanceAmount')->toArray();
        $balanceAmount = array_sum($balanceAmount);

        $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
        $decimalPlace = array_unique($decimalPlace);

        $outputArr = array();
        foreach ($output as $val) {
            if(isset($val->supplierGroupName)){
                $outputArr[$val->concatCompany][$val->concatSupplierName][$val->supplierGroupName][] = $val;
            }
        }

        $dataArr = array('reportData' => (object)$outputArr, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'balanceAmount' => $balanceAmount, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'fromDate' => \Helper::dateFormat($request->fromDate), 'grandTotal' => $grandTotal, 'sentEmail' => false);


        $html = view('print.supplier_statement',$dataArr);
        
        // Configure mPDF for landscape A4 format
        $mpdfConfig = [
            'tempDir' => public_path('tmp'),
            'mode' => 'utf-8',
            'format' => 'A4-L', // Landscape format
            'setAutoTopMargin' => 'stretch',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 32,
            'margin_bottom' => 16,
            'margin_header' => 9,
            'margin_footer' => 9
        ];

        $rootPaths = $this->rootPath;
        $fileName = 'supplier_statement_'.strtotime(date("Y-m-d H:i:s")).'_Part_'.$this->reportCount.'.pdf';
        $path = $rootPaths.'/'.$fileName;

        try {
            $html = $this->cleanHtmlForMpdf($html);
            $mpdf = new \Mpdf\Mpdf($mpdfConfig);
            $mpdf->AddPage('L');
            $mpdf->setAutoBottomMargin = 'stretch';
            $mpdf->WriteHTML($html);
            $pdf_content = $mpdf->Output('', 'S'); 
            $result = Storage::disk('local_public')->put($path, $pdf_content);
        } catch (\Exception $e) {
            \Log::error('mPDF Error in GeneratePdfJob: ' . $e->getMessage());
            return false; // Exit the job if PDF generation fails
        }

            $files = File::files(public_path($this->rootPath));

            if (count($files) == $this->outputChunkData) {
                $fromDate = new Carbon($request->fromDate);
                $fromDate = $fromDate->format('Y-m-d');

                $toDate = new Carbon($request->toDate);
                $toDate = $toDate->format('Y-m-d');

                $companyCode = isset($checkIsGroup->CompanyID) ? $checkIsGroup->CompanyID : 'common';


                $zip = new ZipArchive;
                $fileName = $companyCode . '_' . 'supplier_statement(' . $fromDate . '_' . $toDate . ')_' . strtotime(date("Y-m-d H:i:s")) . '.zip';
                if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE) {
                    foreach ($files as $key => $value) {
                        $relativeNameInZipFile = basename($value);
                        $zip->addFile($value, $relativeNameInZipFile);
                    }
                    $zip->close();
                }

                $contents = Storage::disk('local_public')->get($fileName);
                $zipPath = $companyCode . "/accounts-payable/reports/" . $fileName;
                $fileMoved = Storage::disk('s3')->put($zipPath, $contents);

                if ($fileMoved) {
                    if ($exists = Storage::disk('local_public')->exists($fileName)) {
                        $fileDeleted = Storage::disk('local_public')->delete($fileName);
                    }
                }

                $reportTitle = "supplier_statement_report_pdf_generated";

                $webPushData = [
                    'title' => $reportTitle,
                    'body' => 'Period : ' . $fromDate . ' - ' . $toDate,
                    'url' => "",
                    'path' => $zipPath,
                ];

                WebPushNotificationService::sendNotification($webPushData, 3, $this->userIds);

                Storage::disk('local_public')->deleteDirectory('supplier-payable');


            }

        return true;
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
