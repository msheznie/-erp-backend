<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Models\AccountsReceivableLedger;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerReceivePayment;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\DirectReceiptDetail;
use App\Models\DocumentApproved;
use App\Models\StageCustomerInvoice;
use App\Models\StageCustomerInvoiceDirectDetail;
use App\Models\StageCustomerInvoiceItemDetails;
use App\Models\StageCustomerReceivePayment;
use App\Models\StageCustomerReceivePaymentDetail;
use App\Models\StageDirectReceiptDetail;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateStageReceiptVoucher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $api_external_key;
    protected $api_external_url;
    protected $dataBase;


    public function __construct($dataBase, $api_external_key, $api_external_url)
    {
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                self::onConnection('database_main');
            }else{
                self::onConnection('database');
            }
        }else{
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }
        $this->dataBase = $dataBase;
        $this->api_external_key = $api_external_key;
        $this->api_external_url = $api_external_url;
    }


    public function handle()
    {
        CommonJobService::db_switch($this->dataBase);
        DB::beginTransaction();

        try {
            Log::useFiles(storage_path().'/logs/stage_create_receipt_voucher.log');
            $api_external_key = $this->api_external_key;
            $api_external_url = $this->api_external_url;
            $stagCustomerUpdateReceipts = StageCustomerReceivePayment::all();
            $i = 1;

            foreach ($stagCustomerUpdateReceipts as $dt){
                $lastSerial = CustomerReceivePayment::where('companySystemID', $dt['companySystemID'])
                    ->where('companyFinanceYearID', $dt['companyFinanceYearID'])
                    ->orderBy('serialNo', 'desc')
                    ->first();

                $lastAutoID = CustomerReceivePayment::orderBy('custReceivePaymentAutoID', 'desc')
                    ->first();


                $lastSerialNumber = 1;
                if ($lastSerial) {
                    $lastSerialNumber = intval($lastSerial->serialNo) + $i;
                }


                $custReceiptVocuherAutoID = 1;
                if ($lastAutoID) {
                    $custReceiptVocuherAutoID = intval($lastAutoID->custReceivePaymentAutoID) +$i;
                }




                $y = date('Y', strtotime($dt->FYBiggin));
                $bookingInvCode = ($dt->companyID . '\\' . $y . '\\BRV' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));


                StageCustomerReceivePayment::where('custReceivePaymentAutoID', $dt->custReceivePaymentAutoID)->update(['custReceivePaymentAutoID' => $custReceiptVocuherAutoID,'serialNo' => $lastSerialNumber, 'custPaymentReceiveCode' => $bookingInvCode]);
                StageCustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $dt->custReceivePaymentAutoID)->update(['custReceivePaymentAutoID' => $custReceiptVocuherAutoID]);
                StageDirectReceiptDetail::where('directReceiptAutoID', $dt->custReceivePaymentAutoID)->update(['directReceiptAutoID' => $custReceiptVocuherAutoID]);
                $i++;
            }

            $custReceiptVoucherArray = array();

            $stageReceiptVouchers = StageCustomerReceivePayment::all();
            foreach ($stageReceiptVouchers as $dt) {
                $custReceiptVoucherArray[] = array(
                    'custReceivePaymentAutoID' => $dt['custReceivePaymentAutoID'],
                    'companySystemID' => $dt['companySystemID'],
                    'companyID' => $dt['companyID'],
                    'documentSystemID' => $dt['documentSystemID'],
                    'documentID' => $dt['documentID'],
                    'companyFinanceYearID' =>  $dt['companyFinanceYearID'],
                    'FYBiggin' => $dt['FYBiggin'],
                    'FYPeriodDateFrom' => $dt['FYPeriodDateFrom'],
                    'companyFinancePeriodID' => $dt['companyFinancePeriodID'],
                    'FYEnd' => $dt['FYEnd'],
                    'FYPeriodDateTo' => $dt['FYPeriodDateTo'],
                    'custPaymentReceiveCode' => $dt['custPaymentReceiveCode'],
                    'serialNo' => $dt['serialNo'],
                    'custPaymentReceiveDate' => $dt['custPaymentReceiveDate'],
                    'narration' => $dt['narration'],
                    'customerID' => $dt['customerID'],
                    'customerGLCodeSystemID' => $dt['customerGLCodeSystemID'],
                    'customerGLCode' => $dt['customerGLCode'],
                    'custTransactionCurrencyID' => $dt['custTransactionCurrencyID'],
                    'custTransactionCurrencyER' => $dt['custTransactionCurrencyER'],
                    'bankID' => $dt['bankID'],
                    'bankAccount' => $dt['bankAccount'],
                    'bankCurrency' => $dt['bankCurrency'],
                    'bankCurrencyER' => $dt['bankCurrencyER'],
                    'custChequeDate' => $dt['custChequeDate'],
                    'receivedAmount' => $dt['receivedAmount'],
                    'localCurrencyID' => $dt['localCurrencyID'],
                    'localCurrencyER' => $dt['localCurrencyER'],
                    'localAmount' => $dt['localAmount'],
                    'companyRptCurrencyID' => $dt['companyRptCurrencyID'],
                    'companyRptCurrencyER' => $dt['companyRptCurrencyER'],
                    'companyRptAmount' => $dt['companyRptAmount'],
                    'bankAmount' => $dt['bankAmount'],
                    'documentType' => $dt['documentType'],
                    'isVATApplicable' => $dt['isVATApplicable'],
                    'VATPercentage' => $dt['VATPercentage'],
                    'VATAmount' => $dt['VATAmount'],
                    'VATAmountLocal' => $dt['VATAmountLocal'],
                    'VATAmountRpt' => $dt['VATAmountRpt'],
                    'netAmount' => $dt['netAmount'],
                    'netAmountLocal' => $dt['netAmountLocal'],
                    'netAmountRpt' => $dt['netAmountRpt']
                );
            }
            CustomerReceivePayment::insert($custReceiptVoucherArray);

            $stageReceiptVouchersDetails = StageCustomerReceivePaymentDetail::all();
            $custReceiptVoucherDetArray = array();
            foreach ($stageReceiptVouchersDetails as $dt){
                $custReceiptVoucherDetArray[] = array(
                    'custReceivePaymentAutoID' => $dt['custReceivePaymentAutoID'],
                    'companySystemID' => $dt['companySystemID'],
                    'companyID' => $dt['companyID'],
                    'arAutoID' => $dt['arAutoID'],
                    'addedDocumentSystemID' => $dt['addedDocumentSystemID'],
                    'addedDocumentID' => $dt['addedDocumentID'],
                    'bookingInvCodeSystem' => $dt['bookingInvCodeSystem'],
                    'bookingInvCode' => $dt['bookingInvCode'],
                    'bookingDate' => $dt['bookingDate'],
                    'comments' => $dt['comments'],
                    'custTransactionCurrencyID' => $dt['custTransactionCurrencyID'],
                    'custTransactionCurrencyER' => $dt['custTransactionCurrencyER'],
                    'companyReportingCurrencyID' =>  $dt['companyReportingCurrencyID'],
                    'companyReportingER' => $dt['companyReportingER'],
                    'localCurrencyID' => $dt['localCurrencyID'],
                    'localCurrencyER' => $dt['localCurrencyER'],
                    'bookingAmountTrans' => $dt['bookingAmountTrans'],
                    'bookingAmountLocal' => $dt['bookingAmountLocal'],
                    'bookingAmountRpt' => $dt['bookingAmountRpt'],
                    'custReceiveCurrencyID' => $dt['custReceiveCurrencyID'],
                    'custReceiveCurrencyER' => $dt['custReceiveCurrencyER'],
                    'custbalanceAmount' => $dt['custbalanceAmount'],
                    'receiveAmountTrans' => $dt['receiveAmountTrans'],
                    'receiveAmountLocal' => $dt['receiveAmountLocal'],
                    'receiveAmountRpt' => $dt['receiveAmountRpt']

                );
            }
            CustomerReceivePaymentDetail::insert($custReceiptVoucherDetArray);

            $stageDirectReceiptDetails = StageDirectReceiptDetail::all();

            $custReceiptDetails = array();
            foreach ($stageDirectReceiptDetails as $dt) {
                $custReceiptDetails[] = array(
                    'directReceiptAutoID' => $dt['directReceiptAutoID'],
                    'companySystemID' => $dt['companySystemID'],
                    'companyID' => $dt['companyID'],
                    'serviceLineSystemID' => $dt['serviceLineSystemID'],
                    'serviceLineCode' => $dt['ServiceLineCode'],
                    'chartOfAccountSystemID' => $dt['chartOfAccountSystemID'],
                    'glCode' => $dt['glCode'],
                    'glCodeDes' => $dt['glCodeDes'],
                    'comments' => $dt['comments'],
                    'DRAmountCurrency' => $dt['DRAmountCurrency'],
                    'DDRAmountCurrencyER' => $dt['DDRAmountCurrencyER'],
                    'DRAmount' => $dt['DRAmount'],
                    'localCurrency' => $dt['localCurrency'],
                    'localCurrencyER' => $dt['localCurrencyER'],
                    'localAmount' => $dt['localAmount'],
                    'comRptCurrency' => $dt['comRptCurrency'],
                    'comRptCurrencyER' => $dt['comRptCurrencyER'],
                    'comRptAmount' => $dt['comRptAmount'],
                    'VATAmount' => $dt['VATAmount'],
                    'VATAmountLocal' => $dt['VATAmountLocal'],
                    'VATAmountRpt' => $dt['VATAmountRpt'],
                    'netAmount' => $dt['netAmount'],
                    'netAmountLocal' => $dt['netAmountLocal'],
                    'netAmountRpt' => $dt['netAmountRpt'],
                );
            }
            DirectReceiptDetail::insert($custReceiptDetails);

            $stagCustomerPayments = StageCustomerReceivePayment::all();
            $custReceiptApiArray = array();

            foreach ($stagCustomerPayments as $dt) {
                $custReceiptApiArray = array('custReceivePaymentAutoID' => $dt['custReceivePaymentAutoID'],
                    'referenceNumber' => $dt['referenceNumber']
                );
            }


            if($api_external_key != null && $api_external_url != null) {

                $client = new Client();
                $headers = [
                    'content-type' => 'application/json',
                    'Authorization' => 'ERP '.$api_external_key
                ];
                $res = $client->request('POST', $api_external_url . '/updated_receipt_voucher', [
                    'headers' => $headers,
                    'json' => [
                        'data' => $custReceiptApiArray
                    ]
                ]);
                $json = $res->getBody();

            }


            foreach ($stagCustomerPayments as $dt) {
               
                $params = array('autoID' => $dt['custReceivePaymentAutoID'],
                    'company' => $dt['companySystemID'],
                    'document' => $dt['documentSystemID'],
                    'segment' => '',
                    'category' => '',
                    'amount' => ''
                );


                $confirm = \Helper::confirmDocumentForApi($params);

                $documentApproveds = DocumentApproved::where('documentSystemCode', $dt['custReceivePaymentAutoID'])->where('documentSystemID', 21)->get();
                foreach ($documentApproveds as $documentApproved) {
                    $documentApproved["approvedComments"] = "Generated Customer Invoice through Club Management System";
                    $documentApproved["db"] = $this->dataBase;
                    \Helper::approveDocumentForApi($documentApproved);
                }
            }

            StageCustomerReceivePayment::truncate();
            StageCustomerReceivePaymentDetail::truncate();
            StageDirectReceiptDetail::truncate();



            DB::commit();

            }
        catch (\Exception $e){
            DB::rollback();
            StageCustomerReceivePayment::truncate();
            StageCustomerReceivePaymentDetail::truncate();
            StageDirectReceiptDetail::truncate();
            Log::error($e->getMessage());
        }
    }
}
