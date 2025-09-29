<?php

namespace App\Jobs\Report;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Company;
use App\Models\CurrencyMaster;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Services\WebPushNotificationService;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use File;

class GenerateARCAPdfReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    public $reportCount;
    public $requestData;
    public $userIds;
    public $outputChunkData;
    public $outputData;
    public $rootPath;
    public $aging;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $request, $reportCount, $userId, $outputData, $outputChunkData, $rootPath,$aging)
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
        $this->aging = $aging;
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
        Log::useFiles(storage_path() . '/logs/account_recivable_report.log'); 
        $db = $this->dispatch_db;
        $request = $this->requestData;
        $outputChunkCount = $this->outputChunkData;
        $output = $this->outputData;
        $rootPaths = $this->rootPath;
        $aging = $this->aging;

        $count = $this->reportCount;
        CommonJobService::db_switch($db);
        $checkIsGroup = Company::find($request->companySystemID);
        $companyLogo = $checkIsGroup->logo_url;

        try {
            if ($request->reportTypeID == 'CAS') {
                $name = 'customer_aging_summary';
                $outputArr = array();
                $grandTotalArr = array();
                if ($aging) {
                    foreach ($aging as $val) {
                        $total = collect($output)->pluck($val)->toArray();
                        $grandTotalArr[$val] = array_sum($total);
                    }
                }
        
                if ($output) {
                    foreach ($output as $val) {
                        $outputArr[$val->concatCompanyName][$val->documentCurrency][] = $val;
                    }
                }
        
                $decimalPlaces = 2;
                $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                if ($companyCurrency) {
                    if ($request->currencyID == 2) {
                        $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                    } else if ($request->currencyID == 3) {
                        $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                    }
                }

                $lang = app()->getLocale();
                $isRTL = ($lang === 'ar');

                $dataArr = array('reportData' => (object)$outputArr, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'decimalPlace' => $decimalPlaces, 'grandTotal' => $grandTotalArr, 'agingRange' => $aging, 'fromDate' => \Helper::dateFormat($request->fromDate), 'lang' => $lang);

                $html = view('print.customer_aging_summary', $dataArr);
                $htmlHeader = view('print.customer_aging_summary_header', $dataArr);
                $htmlFooter = view('print.customer_aging_summary_footer', $dataArr);

                $mpdfConfig = [
                    'tempDir' => public_path('tmp'),
                    'mode' => 'utf-8',
                    'format' => 'A4-L',
                    'setAutoTopMargin' => 'stretch',
                    'autoMarginPadding' => -10,
                    'margin_left' => 15,
                    'margin_right' => 15,
                    'margin_top' => 30,
                    'margin_bottom' => 16,
                    'margin_header' => 9,
                    'margin_footer' => 9
                ];

                if ($isRTL) {
                    $mpdfConfig['direction'] = 'rtl'; // Set RTL direction for mPDF
                }

                $pdf = new \Mpdf\Mpdf($mpdfConfig);
                $pdf->SetHTMLHeader($htmlHeader);
                $pdf->SetHTMLFooter($htmlFooter);
                $pdf->AddPage('L');
                $pdf->setAutoBottomMargin = 'stretch';
                $pdf->WriteHTML($html);
            }
            elseif ($request->reportTypeID == 'CAD')
            {
                $name = 'customer_aging_details';
                $outputArr = array();
                $customerCreditDays = array();
                $grandTotalArr = array();
                if ($aging) {
                    foreach ($aging as $val) {
                        $total = collect($output)->pluck($val)->toArray();
                        $grandTotalArr[$val] = array_sum($total);
                    }
                }

                if ($output) {
                    foreach ($output as $val) {
                        $outputArr[$val->customerName][$val->documentCurrency][] = $val;
                        $customerCreditDays[$val->customerName] = $val->creditDays;
                    }
                }

                $decimalPlaces = 2;
                $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                if ($companyCurrency) {
                    if ($request->currencyID == 2) {
                        $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                    } else if ($request->currencyID == 3) {
                        $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                    }
                }

                $invoiceAmountTotal = collect($output)->pluck('invoiceAmount')->toArray();
                $invoiceAmountTotal = array_sum($invoiceAmountTotal);

                $lang = app()->getLocale();
                $isRTL = ($lang === 'ar');

                $dataArr = array('reportData' => (object)$outputArr, 'customerCreditDays' => $customerCreditDays, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'currencyDecimalPlace' => $decimalPlaces, 'grandTotal' => $grandTotalArr, 'agingRange' => $aging, 'fromDate' => \Helper::dateFormat($request->fromDate), 'invoiceAmountTotal' => $invoiceAmountTotal, 'lang' => $lang);

                $html = view('print.customer_aging_detail', $dataArr);
                $htmlHeader = view('print.customer_aging_detail_header', $dataArr);
                $htmlFooter = view('print.customer_aging_detail_footer', $dataArr);

                $mpdfConfig = [
                    'tempDir' => public_path('tmp'),
                    'mode' => 'utf-8',
                    'format' => 'A4-L',
                    'setAutoTopMargin' => 'stretch',
                    'autoMarginPadding' => -10,
                    'margin_left' => 15,
                    'margin_right' => 15,
                    'margin_top' => 30,
                    'margin_bottom' => 16,
                    'margin_header' => 9,
                    'margin_footer' => 9
                ];

                if ($isRTL) {
                    $mpdfConfig['direction'] = 'rtl'; // Set RTL direction for mPDF
                }

                $pdf = new \Mpdf\Mpdf($mpdfConfig);
                $pdf->SetHTMLHeader($htmlHeader);
                $pdf->SetHTMLFooter($htmlFooter);
                $pdf->AddPage('L');
                $pdf->setAutoBottomMargin = 'stretch';
                $pdf->WriteHTML($html);
            }

            $pdf_content = $pdf->Output('', 'S');
            $fileName = $name.strtotime(date("Y-m-d H:i:s")).'_Part_'.$count.'.pdf';
            $path = $rootPaths.'/'.$fileName;

            $result = Storage::disk('local_public')->put($path, $pdf_content);
            $files = File::files(public_path($rootPaths));
            if (count($files) == $outputChunkCount) {
                $fromDate = new Carbon($request->fromDate);
                $fromDate = $fromDate->format('Y-m-d');

                $toDate = new Carbon($request->toDate);
                $toDate = $toDate->format('Y-m-d');

                $companyCode = isset($checkIsGroup->CompanyID)?$checkIsGroup->CompanyID:'common';


                $zip = new ZipArchive;
                $fileName = $companyCode.'_'.'account_recivable_report_('.$fromDate.'_'.$toDate.')_'.strtotime(date("Y-m-d H:i:s")).'.zip';
                if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE)
                {
                    foreach($files as $key => $value) {
                        $relativeNameInZipFile = basename($value);
                        $zip->addFile($value, $relativeNameInZipFile);
                    }
                    $zip->close();
                }

                $contents = Storage::disk('local_public')->get($fileName);
                $zipPath = $companyCode."/account-recivable/repots/".$fileName;
                $fileMoved = Storage::disk('s3')->put($zipPath, $contents);

                if ($fileMoved) {
                    if ($exists = Storage::disk('local_public')->exists($fileName)) {
                        $fileDeleted = Storage::disk('local_public')->delete($fileName);
                    }
                }

                $reportTitle = "account_receivable_report_pdf_generated";

                $webPushData = [
                    'title' => $reportTitle,
                    'body' => 'Period : '.$fromDate.' - '.$toDate,
                    'url' => "",
                    'path' => $zipPath,
                ];
                WebPushNotificationService::sendNotification($webPushData, 3, $this->userIds);

                Storage::disk('local_public')->deleteDirectory('account-recivable-pdf');
            }

            return true;
            
        } catch (\Exception $e) {
            Log::error($e->getMessage()." Line : ".$e->getLine());
        }
    }
}
