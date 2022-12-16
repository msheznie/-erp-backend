<?php

namespace App\Jobs;

use App\Models\CustomerInvoice;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\DocumentApproved;
use App\Models\StageCustomerInvoice;
use App\Models\StageCustomerInvoiceDirectDetail;
use App\Models\StageCustomerInvoiceItemDetails;
use App\Services\JobErrorLogService;
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
    protected $disposalMaster;
    protected $dataBase;

    public function __construct()
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
    }

    public function handle()
    {
        Log::useFiles(storage_path().'/logs/laravel.log');
        Log::warning('Test - Clubmanagment' . date('H:i:s'));

        DB::beginTransaction();
        try {
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

            foreach ($stagCustomerInvoices as $dt) {
                $params = array('autoID' => $dt['custInvoiceDirectAutoID'],
                    'company' => $dt['companySystemID'],
                    'document' => $dt['documentSystemiD'],
                    'segment' => '',
                    'category' => '',
                    'amount' => ''
                );


                $confirm = \Helper::confirmDocument($params);
                Log::info($confirm);

                $documentApproved = DocumentApproved::where('documentSystemCode', $dt['custInvoiceDirectAutoID'])->where('documentSystemID', 20)->first();
                $customerInvoiceDirects = array();
                $customerInvoiceDirects["approvalLevelID"] = 14;
                $customerInvoiceDirects["documentApprovedID"] = $documentApproved->documentApprovedID;
                $customerInvoiceDirects["documentSystemCode"] = $dt['custInvoiceDirectAutoID'];
                $customerInvoiceDirects["documentSystemID"] = $dt['documentSystemiD'];
                $customerInvoiceDirects["approvedComments"] = "Generated Customer Invoice through Club Management System";
                $customerInvoiceDirects["rollLevelOrder"] = 1;
                $approve = \Helper::approveDocument($customerInvoiceDirects);
                Log::info($approve);

//                if (!$approve["success"]) {
//                    return $this->sendError($approve["message"]);
//                }
                StageCustomerInvoice::truncate();
                StageCustomerInvoiceItemDetails::truncate();
                StageCustomerInvoiceDirectDetail::truncate();
                DB::commit();

            }
                DB::commit();
        }

        catch (\Exception $e) {
                DB::rollback();
                Log::info('Error Line No: ' . $e->getLine());
                Log::info('Error Line No: ' . $e->getFile());
                Log::info($e->getMessage());
                Log::info('---- GL  End with Error-----' . date('H:i:s'));
            }


    }

}
