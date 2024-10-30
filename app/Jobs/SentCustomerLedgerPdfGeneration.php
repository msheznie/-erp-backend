<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class SentCustomerLedgerPdfGeneration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $db;
    public $dataArray;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($db, $dataArray)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->dataArray = $dataArray;
        $this->db = $db;
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

            $dataArr = array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'balanceAmount' => $balanceAmount, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'paidAmount' => $paidAmount, 'invoiceAmount' => $invoiceAmount, 'fromDate' => \Helper::dateFormat($input['fromDate']),'companyLogo' => $checkIsGroup->logo_url);

            /*** make pdf file */
            $html = view('print.customer_ledger_template_one', $dataArr)->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($html)->setPaper('a4', 'landscape')->save($path.'/customer_ledger_' . $customerCodeSystem . '_' . $reportCount . '.pdf');

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

            $dataArr = array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'invoiceAmount' => $invoiceAmount, 'fromDate' => \Helper::dateFormat($input['fromDate']), 'toDate' => \Helper::dateFormat($input['toDate']), 'companyLogo' => $checkIsGroup->logo_url);

            /*** make pdf file */
            $html = view('print.customer_ledger_template_two', $dataArr)->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($html)->setPaper('a4', 'landscape')->save($path . '/customer_ledger_' . $customerCodeSystem . '_' . $reportCount . '.pdf');

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

            $footer = "<font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!" .
                "<br>This is an auto generated email. Please do not reply to this email because we are not " .
                "monitoring this inbox.</font>";

            $emailSentTo = 0;
            foreach ($fetchCusEmail as $row) {
                if (!empty($row['contactPersonEmail'])) {
                    $emailSentTo = 1;
                    $dataEmail['empEmail'] = $row['contactPersonEmail'];
                    $dataEmail['companySystemID'] = $input['companySystemID'];

                    $temp = "Dear " . $customerName . ',<p> Customer ledger report has been sent from ' . $company->CompanyName . $footer;

                    $dataEmail['isEmailSend'] = 0;
                    $dataEmail['attachmentFileName'] = $zipFilePath;
                    $dataEmail['alertMessage'] = "Customer ledger report from " . $company->CompanyName;
                    $dataEmail['emailAlertMessage'] = $temp;
                    $sendEmail = \Email::sendEmailErp($dataEmail);
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
}
