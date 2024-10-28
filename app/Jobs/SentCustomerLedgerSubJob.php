<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Http\Controllers\API\AccountsReceivableReportAPIController;
use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use App\Models\CustomerContactDetails;
use App\Models\CustomerMaster;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class SentCustomerLedgerSubJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $input;
    public $db;
    public $receivableController;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($db, $input)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->input = $input;
        $this->db = $db;
        $this->receivableController = app(AccountsReceivableReportAPIController::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', -1);
        $db = $this->db;
        CommonJobService::db_switch($db);
        Log::info('customer ledger sub job started');

        $input = $this->input;
        $customerCodeSystem = $input['customers'][0]['customerCodeSystem'];

        $customerMaster = CustomerMaster::find($customerCodeSystem);
        $fetchCusEmail = CustomerContactDetails::where('customerID', $customerCodeSystem)->get();
        if ($fetchCusEmail) {
            self::generateReportView($input, $customerCodeSystem, $fetchCusEmail, $customerMaster->CustomerName);
        } else {
            Log::error("Customer email is not updated for " . $customerMaster->CustomerName . ". report is not sent");
        }
    }

    private function generateReportView($request, $customerCodeSystem, $fetchCusEmail, $customerName)
    {
        $reportTypeID = $request['reportTypeID'];
        $baseController = app()->make(AppBaseController::class);

        $checkIsGroup = Company::find($request['companySystemID']);
        $databaseName = $this->db ?? 'local';
        $path = public_path() . '/uploads/emailAttachment/customer_ledger_' . $databaseName . '_' . $request['companySystemID'] . '_' . $customerCodeSystem;
        if (!file_exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
        if ($reportTypeID == 'CLT1') { //customer ledger template 1
            $request = (object)$baseController->convertArrayToSelectedValue($request, array('currencyID'));
            $companyLogo = $checkIsGroup->logo_url;

            $output = $this->receivableController->getCustomerLedgerTemplate1QRY($request);
            if($output) {
                $outputChunkData = collect($output)->chunk(300);
                $reportCount = 1;

                foreach ($outputChunkData as $recordsChuncked) {
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
                    $dataArr = array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'balanceAmount' => $balanceAmount, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'paidAmount' => $paidAmount, 'invoiceAmount' => $invoiceAmount, 'fromDate' => \Helper::dateFormat($request->fromDate),'companyLogo' => $companyLogo);

                    /*** make pdf file */
                    $html = view('print.customer_ledger_template_one', $dataArr)->render();
                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($html)->setPaper('a4', 'landscape')->save($path.'/customer_ledger_' . $customerCodeSystem . '_' . $reportCount . '.pdf');

                    $reportCount++;
                }
            } else {
                Log::error('No details found');
            }
        } else {
            $request = (object)$baseController->convertArrayToSelectedValue($request, array('currencyID'));
            $companyLogo = $checkIsGroup->logo_url;

            $output = $this->receivableController->getCustomerLedgerTemplate2QRY($request);
            if($output) {
                $outputChunkData = collect($output)->chunk(300);
                $reportCount = 1;

                foreach ($outputChunkData as $recordsChuncked) {
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
                    $dataArr = array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'invoiceAmount' => $invoiceAmount, 'fromDate' => \Helper::dateFormat($request->fromDate), 'toDate' => \Helper::dateFormat($request->toDate), 'companyLogo' => $companyLogo);

                    $html = view('print.customer_ledger_template_two', $dataArr)->render();
                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($html)->setPaper('a4', 'landscape')->save($path . '/customer_ledger_' . $customerCodeSystem . '_' . $reportCount . '.pdf');

                    $reportCount++;
                }
            } else {
                Log::error('No details found');
            }
        }

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

        $company = Company::where('companySystemID', $request->companySystemID)->first();

        $footer = "<font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!" .
            "<br>This is an auto generated email. Please do not reply to this email because we are not " .
            "monitoring this inbox.</font>";

        $emailSentTo = 0;
        foreach ($fetchCusEmail as $row) {
            if (!empty($row->contactPersonEmail)) {
                $emailSentTo = 1;
                $dataEmail['empEmail'] = $row->contactPersonEmail;
                $dataEmail['companySystemID'] = $request->companySystemID;

                $temp = "Dear " . $customerName . ',<p> Customer ledger report has been sent from ' . $company->CompanyName . $footer;

                $pdfName = public_path('uploads/emailAttachment/customer_ledger_' . $customerCodeSystem . '.zip');

                $dataEmail['isEmailSend'] = 0;
                $dataEmail['attachmentFileName'] = $pdfName;
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
