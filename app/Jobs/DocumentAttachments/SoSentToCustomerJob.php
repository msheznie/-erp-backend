<?php

namespace App\Jobs\DocumentAttachments;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\QuotationMaster;
use App\Models\QuotationDetails;
use App\Models\SoPaymentTerms;
use App\Models\CustomerContactDetails;
use App\Models\CustomerMaster;
use App\Models\Company;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\JobErrorLogService;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class SoSentToCustomerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    public $soData;
    public $empEmail;
    private $tag = "so-sent-to-customer";

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $soData)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        $this->dispatch_db = $dispatch_db;
        $this->soData = $soData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $input = $this->soData;
        $id = $input['quotationMasterID'];
        $documentTypeTitle = $input['documentTypeTitle'];
        $db = $this->dispatch_db;

        Log::useFiles(storage_path() . '/logs/so_sent_to_customer.log');

        CommonJobService::db_switch($db);

        if($documentTypeTitle == 'sales_order'){
            $documentTypeTitle = 'Sales Order';
        }

        if($documentTypeTitle == 'quotation'){
            $documentTypeTitle = 'Quotation';
        }

        $quotationMasterData = QuotationMaster::find($id);

        DB::beginTransaction();
        try {
            $customerCodeSystem = $input['customer']['customerCodeSystem'];

            $path = public_path().'/uploads/emailAttachment';

            if (!file_exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }


            $output = QuotationMaster::where('quotationMasterID', $id)->with(['approved_by' => function ($query) {
                $query->with('employee');
                $query->whereIn('documentSystemID', [67,68]);
            }, 'company', 'detail', 'confirmed_by', 'created_by', 'modified_by', 'sales_person'])->first();

            $quotationCode = $output->quotationCode;

            $netTotal = QuotationDetails::where('quotationMasterID', $id)
                ->sum('transactionAmount');

            $soPaymentTerms = SoPaymentTerms::where('soID', $id)
                                            ->with(['term_description'])
                                            ->get();

            $paymentTermsView = '';

            if ($soPaymentTerms) {
                foreach ($soPaymentTerms as $val) {
                    $paymentTermsView .= $val['term_description']['categoryDescription'] .' '.$val['comAmount'].' '.$output['transactionCurrency'].' '.$val['paymentTemDes'].' '.$val['inDays'] . ' in days, ';
                }
            }

            $order = array(
                'masterdata' => $output,
                'paymentTermsView' => $paymentTermsView,
                'netTotal' => $netTotal
            );

            $html = view('print.sales_quotation', $order);

            $pdf = \App::make('dompdf.wrapper');
            $nowTime = time();
            $pdf->loadHTML($html)->setPaper('a4', 'landscape')->save($path.'/customer_' .$documentTypeTitle . $nowTime.$customerCodeSystem . '.pdf');


            $fetchCusEmail = CustomerContactDetails::where('customerID', $customerCodeSystem)
                                                    ->where('isDefault' , -1)
                                                    ->get();

            $customerMaster = CustomerMaster::find($customerCodeSystem);

            $company = Company::where('companySystemID', $input['companySystemID'])->first();
            $emailSentTo = 0;

            $footer = "<font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!" .
                "<br>This is an auto generated email. Please do not reply to this email because we are not" .
                "monitoring this inbox. To get in touch with us, email us to systems@gulfenergy-int.com.</font>";

            if (count($fetchCusEmail) > 0) {
                foreach ($fetchCusEmail as $row) {
                    if ($row->contactPersonEmail) {
                        $emailSentTo = 1;
                        $dataEmail['empEmail'] = $row->contactPersonEmail;

                        $dataEmail['companySystemID'] = $input['companySystemID'];

                        $temp = "Dear " . $customerMaster->CustomerName .',<p> ' .$documentTypeTitle. ' '  .$quotationCode. ' is attached from ' . $company->CompanyName. '. Please view attachment for further details. ' . $footer;

                        $pdfName = realpath($path."/customer_" .$documentTypeTitle . $nowTime.$customerCodeSystem . ".pdf");

                        $dataEmail['isEmailSend'] = 0;
                        $dataEmail['attachmentFileName'] = $pdfName;
                        $dataEmail['alertMessage'] = trans('email.document_from_company', [
                            'documentType' => $documentTypeTitle,
                            'documentCode' => $quotationCode,
                            'companyName' => $company->CompanyName
                        ]);
                        $dataEmail['emailAlertMessage'] = $temp;
                        $sendEmail = \Email::sendEmailErp($dataEmail);
                        if (!$sendEmail["success"]) {
                            DB::rollback();
                            Log::error('Error');
                            Log::error($sendEmail["message"]);

                            JobErrorLogService::storeError($this->dispatch_db, $quotationMasterData->documentSystemID, $id, $this->tag, 1, $sendEmail["message"]);
                        }
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            JobErrorLogService::storeError($this->dispatch_db, $quotationMasterData->documentSystemID, $id, $this->tag, 2, $this->failed($e), "-****----Line No----:".$e->getLine()."-****----File Name----:".$e->getFile());
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}
