<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Models\CustomerInvoice;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\DocumentApproved;
use App\Models\StageCustomerInvoice;
use App\Models\StageCustomerInvoiceDirectDetail;
use App\Models\StageCustomerInvoiceItemDetails;
use App\Services\JobErrorLogService;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateStageCustomerInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $api_external_key;
    protected $api_external_url;
    protected $dataBase;

    public function __construct($dataBase,$api_external_key, $api_external_url)
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
            Log::useFiles(storage_path().'/logs/stage_create_customer_invoice.log');
            $api_external_key = $this->api_external_key;
            $api_external_url = $this->api_external_url;

            $stagCustomerUpdateInvoices = StageCustomerInvoice::all();
            $i = 1;

            foreach ($stagCustomerUpdateInvoices as $dt){
                $lastSerial = CustomerInvoiceDirect::where('companySystemID', $dt['companySystemID'])
                    ->where('companyFinanceYearID', $dt['companyFinanceYearID'])
                    ->orderBy('serialNo', 'desc')
                    ->first();

                $lastAutoID = CustomerInvoiceDirect::orderBy('custInvoiceDirectAutoID', 'desc')
                    ->first();


                $lastSerialNumber = 1;
                if ($lastSerial) {
                    $lastSerialNumber = intval($lastSerial->serialNo) + $i;
                }

                $custInvoiceDirectAutoID = 1;
                if ($lastAutoID) {
                    $custInvoiceDirectAutoID = intval($lastAutoID->custInvoiceDirectAutoID) +$i;
                }

                $y = date('Y', strtotime($dt->FYBiggin));
                $bookingInvCode = ($dt->companyID . '\\' . $y . '\\INV' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                StageCustomerInvoice::where('custInvoiceDirectAutoID', $dt->custInvoiceDirectAutoID)->update(['custInvoiceDirectAutoID' => $custInvoiceDirectAutoID,'serialNo' => $lastSerialNumber, 'bookingInvCode' => $bookingInvCode]);
                StageCustomerInvoiceDirectDetail::where('custInvoiceDirectID', $dt->custInvoiceDirectAutoID)->update(['custInvoiceDirectID' => $custInvoiceDirectAutoID]);
                StageCustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $dt->custInvoiceDirectAutoID)->update(['custInvoiceDirectAutoID' => $custInvoiceDirectAutoID]);
                $i++;
            }

            $custInvoiceArray = array();

            $stagCustomerInvoices = StageCustomerInvoice::all();
            foreach ($stagCustomerInvoices as $dt) {
                $custInvoiceArray[] = array(
                    'custInvoiceDirectAutoID' => $dt['custInvoiceDirectAutoID'],
                    'companySystemID' => $dt['companySystemID'],
                    'companyID' => $dt['companyID'],
                    'documentSystemiD' => $dt['documentSystemiD'],
                    'documentID' =>  $dt['documentID'],
                    'isPerforma' => $dt['isPerforma'],
                    'customerID' => $dt['customerID'],
                    'customerGLCode' => $dt['customerGLCode'],
                    'customerGLSystemID' => $dt['customerGLSystemID'],
                    'customerInvoiceNo' => $dt['customerInvoiceNo'],
                    'bookingInvCode' => $dt['bookingInvCode'],
                    'customerInvoiceDate' => $dt['customerInvoiceDate'],
                    'serialNo' => $dt['serialNo'],
                    'custTransactionCurrencyID' => $dt['custTransactionCurrencyID'],
                    'custTransactionCurrencyER' => $dt['custTransactionCurrencyER'],
                    'companyReportingCurrencyID' => $dt['companyReportingCurrencyID'],
                    'companyReportingER' => $dt['companyReportingER'],
                    'localCurrencyID' => $dt['localCurrencyID'],
                    'localCurrencyER' => $dt['localCurrencyER'],
                    'comments' => $dt['comments'],
                    'bookingDate' => $dt['bookingDate'],
                    'invoiceDueDate' => $dt['invoiceDueDate'],
                    'date_of_supply' => $dt['date_of_supply'],
                    'bookingAmountTrans' => $dt['bookingAmountTrans'],
                    'bookingAmountLocal' => $dt['bookingAmountLocal'],
                    'bookingAmountRpt' => $dt['bookingAmountRpt'],
                    'VATPercentage' => $dt['VATPercentage'],
                    'VATAmount' => $dt['VATAmount'],
                    'VATAmountLocal' => $dt['VATAmountLocal'],
                    'VATAmountRpt' => $dt['VATAmountRpt'],
                    'companyFinanceYearID' => $dt['companyFinanceYearID'],
                    'FYBiggin' => $dt['FYBiggin'],
                    'FYEnd' => $dt['FYEnd'],
                    'companyFinancePeriodID' => $dt['companyFinancePeriodID'],
                    'FYPeriodDateFrom' => $dt['FYPeriodDateFrom'],
                    'FYPeriodDateTo' => $dt['FYPeriodDateTo'],
                    'serviceLineSystemID' => $dt['serviceLineSystemID'],
                    'serviceLineCode' => $dt['serviceLineCode'],
                    'bankID' => $dt['bankID'],
                    'bankAccountID' => $dt['bankAccountID'],
                    'documentType' => 11,

                );

            }
            CustomerInvoice::insert($custInvoiceArray);

            $custInvoiceDetArray = array();
            $stagCustomerInvoicesDirDetail = StageCustomerInvoiceDirectDetail::all();
            foreach ($stagCustomerInvoicesDirDetail as $dt) {
                $custInvoiceDetArray[] = array(
                    'custInvoiceDirectID' => $dt['custInvoiceDirectID'],
                    'companyID' => $dt['companyID'],
                    'companySystemID' => $dt['companySystemID'],
                    'serviceLineSystemID' => $dt['serviceLineSystemID'],
                    'serviceLineCode' => $dt['serviceLineCode'],
                    'customerID' => $dt['customerID'],
                    'glSystemID' => $dt['glSystemID'],
                    'glCode' => $dt['glCode'],
                    'glCodeDes' => $dt['glCodeDes'],
                    'accountType' => $dt['accountType'],
                    'comments' => $dt['comments'],
                    'invoiceAmountCurrency' => $dt['invoiceAmountCurrency'],
                    'invoiceAmountCurrencyER' => $dt['invoiceAmountCurrencyER'],
                    'unitOfMeasure' => $dt['unitOfMeasure'],
                    'invoiceQty' => $dt['invoiceQty'],
                    'unitCost' => $dt['unitCost'],
                    'invoiceAmount' => $dt['invoiceAmount'],
                    'localAmount' => $dt['localAmount'],
                    'comRptAmount' => $dt['comRptAmount'],
                    'comRptCurrency' => $dt['comRptCurrency'],
                    'comRptCurrencyER' => $dt['comRptCurrencyER'],
                    'localCurrency' => $dt['localCurrency'],
                    'localCurrencyER' => $dt['localCurrencyER'],
                    'vatMasterCategoryID' => $dt['vatMasterCategoryID'],
                    'vatSubCategoryID' => $dt['vatSubCategoryID'],
                    'VATPercentage' => $dt['VATPercentage'],
                    'VATAmount' => $dt['VATAmount'],
                    'VATAmountLocal' => $dt['VATAmountLocal'],
                    'VATAmountRpt' => $dt['VATAmountRpt'],
                    'salesPrice' => $dt['salesPrice']
                );
            }
            CustomerInvoiceDirectDetail::insert($custInvoiceDetArray);

            $custInvoiceItemDetArray = array();
            $stagCustomerInvoicesDirDetail = StageCustomerInvoiceItemDetails::all();
            foreach ($stagCustomerInvoicesDirDetail as $dt) {
                $custInvoiceItemDetArray[] = array(
                    'custInvoiceDirectAutoID' => $dt['custInvoiceDirectAutoID'],
                    'itemCodeSystem' => $dt['itemCodeSystem'],
                    'itemPrimaryCode' => $dt['itemPrimaryCode'],
                    'itemDescription' => $dt['itemDescription'],
                    'itemUnitOfMeasure' => $dt['itemUnitOfMeasure'],
                    'unitOfMeasureIssued' => $dt['unitOfMeasureIssued'],
                    'convertionMeasureVal' => $dt['convertionMeasureVal'],
                    'qtyIssued' => $dt['qtyIssued'],
                    'qtyIssuedDefaultMeasure' => $dt['qtyIssuedDefaultMeasure'],
                    'currentStockQty' => $dt['currentStockQty'],
                    'currentWareHouseStockQty' => $dt['currentWareHouseStockQty'],
                    'currentStockQtyInDamageReturn' => $dt['currentStockQtyInDamageReturn'],
                    'comments' => $dt['comments'],
                    'itemFinanceCategoryID' => $dt['itemFinanceCategoryID'],
                    'itemFinanceCategorySubID' => $dt['itemFinanceCategorySubID'],
                    'financeGLcodebBS' => $dt['financeGLcodebBS'],
                    'financeGLcodebBSSystemID' => $dt['financeGLcodebBSSystemID'],
                    'financeGLcodePLSystemID' => $dt['financeGLcodePLSystemID'],
                    'financeGLcodePL' => $dt['financeGLcodePL'],
                    'financeGLcodeRevenueSystemID' => $dt['financeGLcodeRevenueSystemID'],
                    'financeGLcodeRevenue' => $dt['financeGLcodeRevenue'],
                    'localCurrencyID' => $dt['localCurrencyID'],
                    'localCurrencyER' => $dt['localCurrencyER'],
                    'issueCostLocal' => $dt['issueCostLocal'],
                    'issueCostLocalTotal' => $dt['issueCostLocalTotal'],
                    'reportingCurrencyID' => $dt['reportingCurrencyID'],
                    'reportingCurrencyER' => $dt['reportingCurrencyER'],
                    'issueCostRpt' => $dt['issueCostRpt'],
                    'issueCostRptTotal' => $dt['issueCostRptTotal'],
                    'marginPercentage' => $dt['marginPercentage'],
                    'sellingCurrencyID' => $dt['sellingCurrencyID'],
                    'sellingCurrencyER' => $dt['sellingCurrencyER'],
                    'sellingCost' => $dt['sellingCost'],
                    'sellingCostAfterMargin' => $dt['sellingCostAfterMargin'],
                    'sellingTotal' => $dt['sellingTotal'],
                    'sellingCostAfterMarginLocal' => $dt['sellingCostAfterMarginLocal'],
                    'sellingCostAfterMarginRpt' => $dt['sellingCostAfterMarginRpt'],
                    'deliveryOrderDetailID' => $dt['deliveryOrderDetailID'],
                    'deliveryOrderID' => $dt['deliveryOrderID'],
                    'quotationMasterID' => $dt['quotationMasterID'],
                    'quotationDetailsID' => $dt['quotationDetailsID'],
                    'VATPercentage' => $dt['VATPercentage'],
                    'vatMasterCategoryID' => $dt['vatMasterCategoryID'],
                    'vatSubCategoryID' => $dt['vatSubCategoryID'],
                    'VATAmount' => $dt['VATAmount'],
                    'VATAmountLocal' => $dt['VATAmountLocal'],
                    'VATAmountRpt' => $dt['VATAmountRpt'],
                    'salesPrice' => $dt['salesPrice']
                );
            }
            CustomerInvoiceItemDetails::insert($custInvoiceItemDetArray);


            $stagCustomerInvoices = StageCustomerInvoice::all();
            $custInvoiceApiArray = array();

            foreach ($stagCustomerInvoices as $dt) {
                $custInvoiceApiArray = array('custInvoiceAutoID' => $dt['custInvoiceDirectAutoID'],
                    'referenceNumber' => $dt['referenceNumber']
                );
            }


            if($api_external_key != null && $api_external_url != null) {

                $client = new Client();
                $headers = [
                    'content-type' => 'application/json',
                    'Authorization' => 'ERP '.$api_external_key
                ];
                $res = $client->request('POST', $api_external_url . '/updated_customer_invoice', [
                    'headers' => $headers,
                    'json' => [
                        'data' => $custInvoiceApiArray
                    ]
                ]);
                $json = $res->getBody();

            }


            foreach ($stagCustomerInvoices as $dt) {

                $params = array('autoID' => $dt['custInvoiceDirectAutoID'],
                    'company' => $dt['companySystemID'],
                    'document' => $dt['documentSystemiD'],
                    'segment' => '',
                    'category' => '',
                    'amount' => ''
                );


                $confirm = \Helper::confirmDocumentForApi($params);

                $documentApproveds = DocumentApproved::where('documentSystemCode', $dt['custInvoiceDirectAutoID'])->where('documentSystemID', 20)->get();
                foreach ($documentApproveds as $documentApproved) {
                    $documentApproved["approvedComments"] = "Generated Customer Invoice through Club Management System";
                    $documentApproved["db"] = $this->dataBase;
                    \Helper::approveDocumentForApi($documentApproved);
                }



            }
            StageCustomerInvoice::truncate();
            StageCustomerInvoiceItemDetails::truncate();
            StageCustomerInvoiceDirectDetail::truncate();

                DB::commit();
        }

        catch (\Exception $e) {
                DB::rollback();
                StageCustomerInvoice::truncate();
                StageCustomerInvoiceItemDetails::truncate();
                StageCustomerInvoiceDirectDetail::truncate();
                Log::error($e->getMessage());
            }


    }

}
