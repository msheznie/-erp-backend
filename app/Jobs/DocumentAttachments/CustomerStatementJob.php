<?php

namespace App\Jobs\DocumentAttachments;

use App\helper\Helper;
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
use App\helper\email as Email;

class CustomerStatementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    public $dataArr;
    public $empEmail;
    public $customerCodeSystem;
    public $input;
    public $reportTypeID;
    public $languageCode;
    private $tag = "payment-released-to-supplier";

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $dataArr, $customerCodeSystem, $input, $reportTypeID, $languageCode)
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
        $customerCodeSystem = $this->customerCodeSystem;
        $companySystemID = $this->input;
        $languageCode = $this->languageCode;
        app()->setLocale($languageCode);

        CommonJobService::db_switch($db);

        if ($this->reportTypeID == 'CSA') {
            $html = view('print.customer_statement_of_account_pdf', $htmlData);
            $htmlHeader = view('print.customer_statement_of_account_header', $htmlData);
            $htmlFooter = view('print.customer_statement_of_account_footer', $htmlData);
        } elseif ($this->reportTypeID == 'CBS') {
            $html = view('print.customer_balance_statement', $htmlData);
            $htmlHeader = view('print.customer_balance_statement_header', $htmlData);
            $htmlFooter = view('print.customer_balance_statement_footer', $htmlData);
        }

        $path = public_path().'/uploads/emailAttachment';

        if (!file_exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
        $nowTime = time();

        $isRTL = ($languageCode === 'ar');
        $mpdfConfig = Helper::getMpdfConfig([
            'tempDir' => public_path('tmp'),
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'setAutoTopMargin' => 'stretch',
            'autoMarginPadding' => -10,
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 40,
            'margin_bottom' => 16,
            'margin_header' => 9,
            'margin_footer' => 9
        ], $languageCode);
        if ($isRTL) {
            $mpdfConfig['direction'] = 'rtl';
        }

        $fileName = trans('custom.customer_statement') . $nowTime.$customerCodeSystem . '.pdf';
        $filePath = $path . '/' . $fileName;

        try {
            $mpdf = new \Mpdf\Mpdf($mpdfConfig);
            if (isset($htmlHeader)) {
                $mpdf->SetHTMLHeader($htmlHeader);
            }
            if (isset($htmlFooter)) {
                $mpdf->SetHTMLFooter($htmlFooter);
            }
            $mpdf->AddPage('L');
            $mpdf->setAutoBottomMargin = 'stretch';
            // Clean HTML to prevent mPDF CSS issues
            $html = $this->cleanHtmlForMpdf($html);
            $mpdf->WriteHTML($html);
            $mpdf->Output($filePath, 'F');
        } catch (\Exception $e) {
            // Fallback: simpler config without margins
            $fallbackConfig = ['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-L'];
            if ($isRTL) {
                $fallbackConfig['direction'] = 'rtl';
            }
            try {
                $mpdf = new \Mpdf\Mpdf($fallbackConfig);
                if (isset($htmlHeader)) {
                    $mpdf->SetHTMLHeader($htmlHeader);
                }
                if (isset($htmlFooter)) {
                    $mpdf->SetHTMLFooter($htmlFooter);
                }
                $mpdf->AddPage('L');
                $html = $this->cleanHtmlForMpdf($html);
                $mpdf->WriteHTML($html);
                $mpdf->Output($filePath, 'F');
            } catch (\Exception $e2) {
                Log::channel('payment_released_to_supplier')->error('mPDF Error in CustomerStatementJob: ' . $e2->getMessage());
                return;
            }
        }


        $fetchCusEmail = CustomerContactDetails::where('customerID', $customerCodeSystem)
                                               ->get();

        $customerMaster = CustomerMaster::find($customerCodeSystem);

        $company = Company::where('companySystemID', $companySystemID)->first();
        $emailSentTo = 0;

        $footer = "<font size='1.5'><i><p><br><br><br>" . trans('custom.save_paper_think_before_print') .
            "<br>" . trans('custom.auto_generated_email_footer') . "</font>";
        
        if ($fetchCusEmail) {
            foreach ($fetchCusEmail as $row) {
                if (!empty($row->contactPersonEmail)) {
                    $emailSentTo = 1;
                    $dataEmail['empEmail'] = $row->contactPersonEmail;

                    $dataEmail['companySystemID'] = $companySystemID;

                    $temp = trans('custom.dear_customer_statement_sent', ['customerName' => $customerMaster->CustomerName, 'companyName' => $company->CompanyName]) . $footer;

                    $pdfName = realpath($filePath);

                    $dataEmail['isEmailSend'] = 0;
                    $dataEmail['attachmentFileName'] = $pdfName;
                    $dataEmail['alertMessage'] = trans('custom.customer_statement_report_from', ['companyName' => $company->CompanyName]);
                    $dataEmail['emailAlertMessage'] = $temp;
                    $sendEmail = Email::sendEmailErp($dataEmail);
                    if (!$sendEmail["success"]) {
                         Log::channel('payment_released_to_supplier')->error('Error');
                        Log::channel('payment_released_to_supplier')->error($sendEmail["message"]);
                    }
                }
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
