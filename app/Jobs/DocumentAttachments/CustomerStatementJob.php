<?php

namespace App\Jobs\DocumentAttachments;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\CustomerContactDetails;
use App\Models\CustomerMaster;
use App\Models\Company;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\JobErrorLogService;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CustomerStatementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    public $dataArr;
    public $empEmail;
    public $customerCodeSystem;
    public $input;
    public $reportTypeID;
    private $tag = "payment-released-to-supplier";

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $dataArr, $customerCodeSystem, $input, $reportTypeID)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        $this->dispatch_db = $dispatch_db;
        $this->dataArr = $dataArr;
        $this->customerCodeSystem = $customerCodeSystem;
        $this->input = $input;
        $this->reportTypeID = $reportTypeID;
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
        $customerCodeSystem = $this->customerCodeSystem;
        $companySystemID = $this->input;

        Log::useFiles(storage_path() . '/logs/payment_released_to_supplier.log');

        CommonJobService::db_switch($db);

        if ($this->reportTypeID == 'CSA') {
            $html = view('print.customer_statement_of_account_pdf', $htmlData);
        } elseif ($this->reportTypeID == 'CBS') {
            $html = view('print.customer_balance_statement', $htmlData);
        }

        $pdf = \App::make('dompdf.wrapper');
        $path = public_path().'/uploads/emailAttachment';

        if (!file_exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
        $nowTime = time();

        $pdf->loadHTML($html)->setPaper('a4', 'landscape')->save($path.'/customer_statement_' . $nowTime.$customerCodeSystem . '.pdf');


        $fetchCusEmail = CustomerContactDetails::where('customerID', $customerCodeSystem)
                                               ->get();

        $customerMaster = CustomerMaster::find($customerCodeSystem);

        $company = Company::where('companySystemID', $companySystemID)->first();
        $emailSentTo = 0;

        $footer = "<font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!" .
            "<br>This is an auto generated email. Please do not reply to this email because we are not " .
            "monitoring this inbox.</font>";
        
        if ($fetchCusEmail) {
            foreach ($fetchCusEmail as $row) {
                if (!empty($row->contactPersonEmail)) {
                    $emailSentTo = 1;
                    $dataEmail['empEmail'] = $row->contactPersonEmail;

                    $dataEmail['companySystemID'] = $companySystemID;

                    $temp = "Dear " . $customerMaster->CustomerName . ',<p> Customer statement report has been sent from ' . $company->CompanyName . $footer;

                    $pdfName = realpath($path."/customer_statement_" . $nowTime.$customerCodeSystem . ".pdf");

                    $dataEmail['isEmailSend'] = 0;
                    $dataEmail['attachmentFileName'] = $pdfName;
                    $dataEmail['alertMessage'] = "Customer statement report from " . $company->CompanyName;
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
}
