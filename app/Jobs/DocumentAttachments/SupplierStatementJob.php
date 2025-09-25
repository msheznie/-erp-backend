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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $dataArr, $inputData)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        $this->dispatch_db = $dispatch_db;
        $this->dataArr = $dataArr;
        $this->inputData = $inputData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {    app()->setLocale('en');
        $htmlData = $this->dataArr;
        $db = $this->dispatch_db;
        $input = $this->inputData;

        Log::useFiles(storage_path() . '/logs/supplier_statement_sent.log');

        CommonJobService::db_switch($db);

        $html = view('print.supplier_statement', $htmlData);;
        $pdf = \App::make('dompdf.wrapper');
        $path = public_path().'/uploads/emailAttachment';

        if (!file_exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
        $nowTime = time();

        $supplierID = $input['suppliers'][0]['supplierCodeSytem'];
        $pdf->loadHTML($html)->setPaper('a4', 'landscape')->save($path.'/supplier_statement_' . $nowTime.$supplierID. '.pdf');


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

                    $pdfName = realpath($path."/supplier_statement_" . $nowTime.$supplierID. ".pdf");

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
}
