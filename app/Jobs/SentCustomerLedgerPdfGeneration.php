<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\helper\Helper;
use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use App\helper\email as Email;

class SentCustomerLedgerPdfGeneration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $db;
    public $dataArray;
    public $languageCode;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($db, $dataArray, $languageCode)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->dataArray = $dataArray;
        $this->db = $db;
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
        $db = $this->db;
        CommonJobService::db_switch($db);
        Log::info('Customer ledger PDF generation started');
        $dataArray = $this->dataArray;
        $input = $dataArray['input'];
        $reportCount = $dataArray['reportCount'];
        $recordsChuncked = $dataArray['recordsChuncked'];
        $path = $dataArray['path'];
        $customerCodeSystem = $dataArray['customerCodeSystem'];
        $fileCount = $dataArray['fileCount'];
        $fetchCusEmail = $dataArray['fetchCusEmail'];
        $customerName = $dataArray['customerName'];

        $checkIsGroup = Company::find($input['companySystemID']);
        $languageCode = $this->languageCode;
        app()->setLocale($languageCode);

        if ($input['reportTypeID'] == 'CLT1') {
            $outputArr = array();
            $invoiceAmount = collect($recordsChuncked)->pluck('invoiceAmount')->toArray();
            $invoiceAmount = array_sum($invoiceAmount);

            $paidAmount = collect($recordsChuncked)->pluck('paidAmount')->toArray();
            $paidAmount = array_sum($paidAmount);

            $balanceAmount = collect($recordsChuncked)->pluck('balanceAmount')->toArray();
            $balanceAmount = array_sum($balanceAmount);

            $decimalPlace = collect($recordsChuncked)->pluck('balanceDecimalPlaces')->toArray();
            $decimalPlace = array_unique($decimalPlace);

            if ($recordsChuncked) {
                foreach ($recordsChuncked as $val) {
                    $outputArr[$val->concatCustomerName][$val->documentCurrency][] = $val;
                }
            }

            $dataArr = array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'balanceAmount' => $balanceAmount, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'paidAmount' => $paidAmount, 'invoiceAmount' => $invoiceAmount, 'fromDate' => Helper::dateFormat($input['fromDate']),'companyLogo' => $checkIsGroup->logo_url,'lang' => $languageCode);

            /*** make pdf file */
            $html = view('print.customer_ledger_template_one', $dataArr)->render();

            if (!file_exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            $fileName = trans('custom.customer_ledger') . $customerCodeSystem . '_' . $reportCount . '.pdf';
            $filePath = $path . '/' . $fileName;

            $lang = app()->getLocale();
            $isRTL = ($lang === 'ar');
            $mpdfConfig = Helper::getMpdfConfig([
                'tempDir' => public_path('tmp'),
                'mode' => 'utf-8',
                'format' => 'A4-L',
                'setAutoTopMargin' => 'stretch',
                'autoMarginPadding' => -10,
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 16,
                'margin_bottom' => 16,
                'margin_header' => 9,
                'margin_footer' => 9
            ], $lang);
            if ($isRTL) {
                $mpdfConfig['direction'] = 'rtl';
            }

            try {
                $mpdf = new \Mpdf\Mpdf($mpdfConfig);
                $mpdf->AddPage('L');
                $mpdf->setAutoBottomMargin = 'stretch';
                $mpdf->WriteHTML($this->cleanHtmlForMpdf($html));
                $mpdf->Output($filePath, 'F');
            } catch (\Exception $e) {
                $fallbackConfig = ['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-L'];
                if ($isRTL) {
                    $fallbackConfig['direction'] = 'rtl';
                }
                try {
                    $mpdf = new \Mpdf\Mpdf($fallbackConfig);
                    $mpdf->AddPage('L');
                    $mpdf->WriteHTML($this->cleanHtmlForMpdf($html));
                    $mpdf->Output($filePath, 'F');
                } catch (\Exception $e2) {
                    Log::error('mPDF Error in SentCustomerLedgerPdfGeneration (CLT1): ' . $e2->getMessage());
                }
            }

        } else {

            $outputArr = array();
            $invoiceAmount = collect($recordsChuncked)->pluck('invoiceAmount')->toArray();
            $invoiceAmount = array_sum($invoiceAmount);

            $decimalPlace = collect($recordsChuncked)->pluck('balanceDecimalPlaces')->toArray();
            $decimalPlace = array_unique($decimalPlace);

            if ($recordsChuncked) {
                foreach ($recordsChuncked as $val) {
                    $outputArr[$val->concatCustomerName][$val->documentCurrency][] = $val;
                }
            }
            $lang = app()->getLocale();
            $dataArr = array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'invoiceAmount' => $invoiceAmount, 'fromDate' => Helper::dateFormat($input['fromDate']), 'toDate' => Helper::dateFormat($input['toDate']), 'companyLogo' => $checkIsGroup->logo_url, 'lang' => $lang);

            /*** make pdf file */
            $html = view('print.customer_ledger_template_two', $dataArr)->render();

            if (!file_exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            $fileName = trans('custom.customer_ledger') . $customerCodeSystem . '_' . $reportCount . '.pdf';
            $filePath = $path . '/' . $fileName;

            $isRTL = ($lang === 'ar');
            $mpdfConfig = Helper::getMpdfConfig([
                'tempDir' => public_path('tmp'),
                'mode' => 'utf-8',
                'format' => 'A4-L',
                'setAutoTopMargin' => 'stretch',
                'autoMarginPadding' => -10,
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 16,
                'margin_bottom' => 16,
                'margin_header' => 9,
                'margin_footer' => 9
            ], $lang);
            if ($isRTL) {
                $mpdfConfig['direction'] = 'rtl';
            }

            try {
                $mpdf = new \Mpdf\Mpdf($mpdfConfig);
                $mpdf->AddPage('L');
                $mpdf->setAutoBottomMargin = 'stretch';
                $mpdf->WriteHTML($this->cleanHtmlForMpdf($html));
                $mpdf->Output($filePath, 'F');
            } catch (\Exception $e) {
                $fallbackConfig = ['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-L'];
                if ($isRTL) {
                    $fallbackConfig['direction'] = 'rtl';
                }
                try {
                    $mpdf = new \Mpdf\Mpdf($fallbackConfig);
                    $mpdf->AddPage('L');
                    $mpdf->WriteHTML($this->cleanHtmlForMpdf($html));
                    $mpdf->Output($filePath, 'F');
                } catch (\Exception $e2) {
                    Log::error('mPDF Error in SentCustomerLedgerPdfGeneration (CLT2): ' . $e2->getMessage());
                }
            }

        }

        if($reportCount == $fileCount) {
            $zipFilePath = public_path('uploads/emailAttachment/customer_ledger_' . $customerCodeSystem . '.zip');
            $zip = new ZipArchive;

            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                foreach (glob($path . '/*.pdf') as $file) {
                    $zip->addFile($file, basename($file));
                }
                $zip->close();
                File::deleteDirectory($path);
            } else {
                Log::error('Failed to create zip file: ' . $zipFilePath);
            }

            $company = Company::where('companySystemID', $input['companySystemID'])->first();

            $footer = "<font size='1.5'><i><p><br><br><br>" . trans('custom.save_paper_think_before_print') .
                "<br>" . trans('custom.auto_generated_email_footer') . "</font>";

            $emailSentTo = 0;
            foreach ($fetchCusEmail as $row) {
                if (!empty($row['contactPersonEmail'])) {
                    $emailSentTo = 1;
                    $dataEmail['empEmail'] = $row['contactPersonEmail'];
                    $dataEmail['companySystemID'] = $input['companySystemID'];

                    $temp = trans('custom.dear_customer_ledger_sent', ['customerName' => $customerName, 'companyName' => $company->CompanyName]) . $footer;

                    $dataEmail['isEmailSend'] = 0;
                    $dataEmail['attachmentFileName'] = $zipFilePath;
                    $dataEmail['alertMessage'] = trans('custom.customer_ledger_report_from', ['companyName' => $company->CompanyName]);
                    $dataEmail['emailAlertMessage'] = $temp;
                    $sendEmail = Email::sendEmailErp($dataEmail);
                    if (!$sendEmail["success"]) {
                        Log::error($sendEmail["message"]);
                    } else {
                        Log::error('Customer ledger email sent successfully');
                    }
                }
            }
            if($emailSentTo == 0) {
                Log::error('Email not sent.');
            }
        }
    }

    private function cleanHtmlForMpdf($html)
    {
        $html = preg_replace('/rgba\((\d+),\s*(\d+),\s*(\d+),\s*0\.1\)/', '#000000', $html);
        $html = preg_replace('/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d.]+)\)/', '#$1$2$3', $html);
        $html = preg_replace('/rgb\((\d+),\s*(\d+),\s*(\d+)\)/', '#$1$2$3', $html);
        $html = preg_replace('/\s*!important\s*/', '', $html);
        $html = str_replace('border-top: 1px solid #0000001', 'border-top: 1px solid #000000', $html);
        $html = preg_replace('/opacity\s*:\s*[\d.]+\s*;?/', '', $html);
        $html = preg_replace('/transform[^;]*;?/', '', $html);
        $html = preg_replace('/transform-origin[^;]*;?/', '', $html);
        $html = preg_replace('/font-family:\s*[^;]*apple-system[^;]*;?/', 'font-family: Arial, sans-serif;', $html);
        return $html;
    }
}
