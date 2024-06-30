<?php

namespace App\Jobs\DocumentAttachments;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\ProcumentOrder;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CurrencyMaster;
use App\Models\PoPaymentTerms;
use App\Models\SecondaryCompany;
use App\Models\PoAddons;
use App\Models\CompanyPolicyMaster;
use App\Models\SupplierContactDetails;
use App\Models\SupplierMaster;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\JobErrorLogService;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class PoSentToSupplierJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    public $poID;
    public $empEmail;
    private $tag = "po-sent-to-supplier";
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $poID, $empEmail)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        $this->dispatch_db = $dispatch_db;
        $this->poID = $poID;
        $this->empEmail = $empEmail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $purchaseOrderID = $this->poID;
        $db = $this->dispatch_db;

        Log::useFiles(storage_path() . '/logs/po_sent_to_supplier.log');

        CommonJobService::db_switch($db);

        DB::beginTransaction();
        try {

            $typeID = 1; //$request->get('typeID');

            $emailSentTo = 0;

            $procumentOrderUpdate = ProcumentOrder::where('purchaseOrderID', '=', $purchaseOrderID)->first();

            $company = Company::where('companySystemID', $procumentOrderUpdate->companySystemID)->first();

            $outputRecord = ProcumentOrder::where('purchaseOrderID', $procumentOrderUpdate->purchaseOrderID)->with(['segment','created_by','detail' => function ($query) {
                $query->with('unit');
            }, 'approved_by' => function ($query) {
                $query->with(['employee'=>function($query2){
                    $query2->with(['hr_emp'=>function($query3){
                        $query3->with(['designation']);
                    }]);
                }]);
                $query->whereIN('documentSystemID', [2, 5, 52]);
            }, 'suppliercontact' => function ($query) {
                $query->where('isDefault', -1);
            }, 'company', 'transactioncurrency', 'companydocumentattachment', 'paymentTerms_by'])->get();

            $refernaceDoc = CompanyDocumentAttachment::where('companySystemID', $procumentOrderUpdate->companySystemID)
                ->where('documentSystemID', $procumentOrderUpdate->documentSystemID)
                ->first();

            $currencyDecimal = CurrencyMaster::select('DecimalPlaces')->where('currencyID', $procumentOrderUpdate->supplierTransactionCurrencyID)
                ->first();

            $decimal = 2;
            if (!empty($currencyDecimal)) {
                $decimal = $currencyDecimal['DecimalPlaces'];
            }

            $documentTitle = 'Purchase Order';
            if ($procumentOrderUpdate->documentSystemID == 2) {
                $documentTitle = 'Purchase Order';
            } else if ($procumentOrderUpdate->documentSystemID == 5 && $procumentOrderUpdate->poType_N == 5) {
                $documentTitle = 'Work Order';
            } else if ($procumentOrderUpdate->documentSystemID == 5 && $procumentOrderUpdate->poType_N == 6) {
                $documentTitle = 'Sub Work Order';
            } else if ($procumentOrderUpdate->documentSystemID == 52) {
                $documentTitle = 'Direct Order';
            }

            $poPaymentTerms = PoPaymentTerms::where('poID', $procumentOrderUpdate->purchaseOrderID)
                ->get();

            $paymentTermsView = '';

            if ($poPaymentTerms) {
                foreach ($poPaymentTerms as $val) {
                    $paymentTermsView .= $val['paymentTemDes'] . ', ';
                }
            }

            $nowTime = time();

            $orderAddons = PoAddons::where('poId', $procumentOrderUpdate->purchaseOrderID)
                ->with(['category'])
                ->orderBy('idpoAddons', 'DESC')
                ->get();

            $checkCompanyIsMerged = SecondaryCompany::where('companySystemID', $procumentOrderUpdate->companySystemID)
                ->whereDate('cutOffDate', '<=', Carbon::parse($procumentOrderUpdate->createdDateTime))
                ->first();

            $isMergedCompany = false;
            if ($checkCompanyIsMerged) {
                $isMergedCompany = true;
            }

            $isProjectBase = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
            ->where('companySystemID', $procumentOrderUpdate->companySystemID)
            ->where('isYesNO', 1)
            ->exists();

            $order = array(
                'podata' => $outputRecord[0],
                'docRef' => $refernaceDoc,
                'isMergedCompany' => $isMergedCompany,
                'secondaryCompany' => $checkCompanyIsMerged,
                'termsCond' => $typeID,
                'numberFormatting' => $decimal,
                'isProjectBase' => $isProjectBase,
                'title' => $documentTitle,
                'paymentTermsView' => $paymentTermsView,
                'addons' => $orderAddons

            );
            $html = view('print.purchase_order_print_pdf', $order);
            $pdf = \App::make('dompdf.wrapper');

            $path = public_path() . '/uploads/emailAttachment';
            if (!file_exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            $pdf->loadHTML($html)->save($path.'/po_print_' . $nowTime . '.pdf');

            $fetchSupEmail = SupplierContactDetails::where('supplierID', $procumentOrderUpdate->supplierID)
                ->get();

            $supplierMaster = SupplierMaster::find($procumentOrderUpdate->supplierID);

            $footer = "<font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!" .
                "<br>This is an auto generated email. Please do not reply to this email because we are not " .
                "monitoring this inbox.</font>";
            if ($fetchSupEmail) {
                foreach ($fetchSupEmail as $row) {
                    if (!empty($row->contactPersonEmail)) {
                        $emailSentTo = 1;
                        $dataEmail['empName'] = $procumentOrderUpdate->supplierName;
                        $dataEmail['empEmail'] = $row->contactPersonEmail;

                        $dataEmail['companySystemID'] = $procumentOrderUpdate->companySystemID;
                        $dataEmail['companyID'] = $procumentOrderUpdate->companyID;

                        $dataEmail['docID'] = $procumentOrderUpdate->documentID;
                        $dataEmail['docSystemID'] = $procumentOrderUpdate->documentSystemID;
                        $dataEmail['docSystemCode'] = $procumentOrderUpdate->purchaseOrderID;

                        $dataEmail['docApprovedYN'] = $procumentOrderUpdate->approved;
                        $dataEmail['docCode'] = $procumentOrderUpdate->purchaseOrderCode;
                        $dataEmail['ccEmailID'] = $this->empEmail;

                        $temp = "Dear " . $procumentOrderUpdate->supplierName . ',<p> New Order has been released from ' . $company->CompanyName . $footer;

                        //$location = \DB::table('systemmanualfolder')->first();
                        $pdfName = realpath($path."/po_print_" . $nowTime . ".pdf");

                        $dataEmail['isEmailSend'] = 0;
                        $dataEmail['attachmentFileName'] = $pdfName;
                        $dataEmail['alertMessage'] = "New order from " . $company->CompanyName . " " . $procumentOrderUpdate->purchaseOrderCode;
                        $dataEmail['emailAlertMessage'] = $temp;
                        $sendEmail = \Email::sendEmailErp($dataEmail);
                        if (!$sendEmail["success"]) {
                            DB::rollback();
                            Log::error('Error');
                            Log::error($sendEmail["message"]);

                            JobErrorLogService::storeError($this->dispatch_db, 2, $purchaseOrderID, $this->tag, 1, $sendEmail["message"]);
                        }
                    }
                }
            }

            if ($emailSentTo == 0) {
                if ($supplierMaster) {
                    if (!empty($supplierMaster->supEmail)) {
                        $emailSentTo = 1;
                        $dataEmail['empName'] = $procumentOrderUpdate->supplierName;
                        $dataEmail['empEmail'] = $supplierMaster->supEmail;

                        $dataEmail['companySystemID'] = $procumentOrderUpdate->companySystemID;
                        $dataEmail['companyID'] = $procumentOrderUpdate->companyID;

                        $dataEmail['docID'] = $procumentOrderUpdate->documentID;
                        $dataEmail['docSystemID'] = $procumentOrderUpdate->documentSystemID;
                        $dataEmail['docSystemCode'] = $procumentOrderUpdate->purchaseOrderID;

                        $dataEmail['docApprovedYN'] = $procumentOrderUpdate->approved;
                        $dataEmail['docCode'] = $procumentOrderUpdate->purchaseOrderCode;
                        $dataEmail['ccEmailID'] = $this->empEmail;

                        $temp = "Dear " . $procumentOrderUpdate->supplierName . ',<p> New Order has been released from ' . $company->CompanyName . $footer;

                        //$location = \DB::table('systemmanualfolder')->first();
                        $pdfName = realpath($path."/po_print_" . $nowTime . ".pdf");

                        $dataEmail['isEmailSend'] = 0;
                        $dataEmail['attachmentFileName'] = $pdfName;
                        $dataEmail['alertMessage'] = "New order from " . $company->CompanyName . " " . $procumentOrderUpdate->purchaseOrderCode;
                        $dataEmail['emailAlertMessage'] = $temp;
                        $sendEmail = \Email::sendEmailErp($dataEmail);
                        if (!$sendEmail["success"]) {
                            DB::rollback();
                            Log::error('Error');
                            Log::error($sendEmail["message"]);

                            JobErrorLogService::storeError($this->dispatch_db, 2, $purchaseOrderID, $this->tag, 1, $sendEmail["message"]);
                        }
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            JobErrorLogService::storeError($this->dispatch_db, 2, $purchaseOrderID, $this->tag, 2, $this->failed($e), "-****----Line No----:".$e->getLine()."-****----File Name----:".$e->getFile());
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}
