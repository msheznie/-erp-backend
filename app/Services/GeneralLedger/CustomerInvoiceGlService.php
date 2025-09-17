<?php

namespace App\Services\GeneralLedger;

use App\helper\TaxService;
use App\Models\AdvancePaymentDetails;
use App\Models\AdvanceReceiptDetails;
use App\Models\AssetCapitalization;
use App\Models\AssetDisposalDetail;
use App\Models\AssetDisposalMaster;
use App\Models\BookInvSuppDet;
use App\Models\BookInvSuppMaster;
use App\Models\CreditNote;
use App\Models\CreditNoteDetails;
use App\Models\CurrencyConversion;
use App\Models\CurrencyMaster;
use App\Models\POSGLEntries;
use App\Models\StockCount;
use App\Models\StockCountDetail;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\CustomerMaster;
use App\Models\CustomerReceivePayment;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\DebitNote;
use App\Models\DebitNoteDetails;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderDetail;
use App\Models\DirectInvoiceDetails;
use App\Models\DirectPaymentDetails;
use App\Models\DirectReceiptDetail;
use App\Models\Employee;
use App\Models\FixedAssetDepreciationMaster;
use App\Models\FixedAssetDepreciationPeriod;
use App\Models\FixedAssetMaster;
use App\Models\PurchaseReturnLogistic;
use App\Models\GeneralLedger;
use App\Models\GRVDetails;
use App\Models\GRVMaster;
use App\Models\InventoryReclassification;
use App\Models\InventoryReclassificationDetail;
use App\Models\ItemIssueDetails;
use App\Models\ItemIssueMaster;
use App\Models\ItemReturnDetails;
use App\Models\ItemReturnMaster;
use App\Models\JvDetail;
use App\Models\JvMaster;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\PoAdvancePayment;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetails;
use App\Models\SegmentMaster;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetails;
use App\Models\StockReceive;
use App\Models\StockReceiveDetails;
use App\Models\StockTransfer;
use App\Models\StockTransferDetails;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\Taxdetail;
use App\Models\SupplierInvoiceDirectItem;
use App\Models\Company;
use App\Models\SupplierAssigned;
use App\Models\ChartOfAccountsAssigned;
use App\Models\ChartOfAccount;
use App\Models\SalesReturn;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\SalesReturnDetail;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\UnbilledGRVInsert;
use App\Jobs\TaxLedgerInsert;
use App\Services\GeneralLedger\GlPostedDateService;

class CustomerInvoiceGlService
{
	public static function processEntry($masterModel)
	{
        $data = [];
        $taxLedgerData = [];
        $finalData = [];
        if($masterModel['employeeSystemID'] == "SYSTEM"){
            $empID = Employee::where('empID',$masterModel['employeeSystemID'])->first();
        }
        else{
            $empID = Employee::find($masterModel['employeeSystemID']);
        }
        $masterData = CustomerInvoiceDirect::with(['finance_period_by'])->find($masterModel["autoID"]);
        if(!empty($masterData)) {
            $company = Company::select('masterComapanyID')->where('companySystemID', $masterData->companySystemID)->first();
        } else {
            $errorMsg['status'] = false;
            $errorMsg['error']['message'] = 'Customer invoice not found, date: ' . date('H:i:s');

            return $errorMsg;
        }
        $validatePostedDate = GlPostedDateService::validatePostedDate($masterModel["autoID"], $masterModel["documentSystemID"]);

        if (!$validatePostedDate['status']) {
            return ['status' => false, 'message' => $validatePostedDate['message']];
        }

        $masterDocumentDate = isset($masterModel['documentDateOveride']) ? $masterModel['documentDateOveride'] : $validatePostedDate['postedDate'];
        
        if ($masterData->isPerforma == 2 || $masterData->isPerforma == 4 || $masterData->isPerforma == 5) {   // item sales invoice || from sales order || from sales quotation
            $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $masterData->customerGLSystemID)->first();
            
            $proccessData = Self::generateCustomerDirectInvoiceDetailsGL($masterData,$finalData,$masterDocumentDate,$empID);

            $time = Carbon::now();

            $data['companySystemID'] = $masterData->companySystemID;
            $data['companyID'] = $masterData->companyID;
            $data['masterCompanyID'] = $company->masterComapanyID;
            $data['documentID'] = "INV";
            $data['documentSystemID'] = $masterData->documentSystemiD;
            $data['documentSystemCode'] = $masterData->custInvoiceDirectAutoID;
            $data['documentCode'] = $masterData->bookingInvCode;

            $data['documentDate'] = $masterDocumentDate;
            $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
            $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
            $data['invoiceNumber'] = $masterData->customerInvoiceNo;
            $data['invoiceDate'] = $masterData->customerInvoiceDate;
            $data['documentConfirmedDate'] = $masterData->confirmedDate;
            $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
            $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;

            $data['serviceLineSystemID'] = $masterData->serviceLineSystemID;
            $data['serviceLineCode'] = $masterData->serviceLineCode;

            // from customer invoice master table
            $data['chartOfAccountSystemID'] = $chartOfAccount->chartOfAccountSystemID;
            $data['glCode'] = $chartOfAccount->AccountCode;
            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

            $data['documentNarration'] = $masterData->comments;
            $data['clientContractID'] = 'X';
            $data['contractUID'] = 159;
            $data['supplierCodeSystem'] = $masterData->customerID;

            if($masterData->isPerforma == 2){
                $cusTotal = CustomerInvoiceItemDetails::selectRaw("SUM(sellingTotal) as total")->WHERE('custInvoiceDirectAutoID', $masterModel["autoID"])->get();
                $cusTotal = isset($cusTotal[0]->total)?$cusTotal[0]->total:0;
            }
            $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
            $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
            $data['documentTransAmount'] = (($masterData->isPerforma == 2) ? $cusTotal + $masterData->VATAmount : $masterData->bookingAmountTrans + $masterData->VATAmount) + ($proccessData['_documentTransAmount']);

            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
            $data['documentLocalAmount'] = (($masterData->isPerforma == 2) ? ($cusTotal / $masterData->localCurrencyER)  + $masterData->VATAmountLocal : $masterData->bookingAmountLocal + $masterData->VATAmountLocal)  + ($proccessData['_documentLocalAmount']);

            $data['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
            $data['documentRptCurrencyER'] = $masterData->companyReportingER;
            $data['documentRptAmount'] = (($masterData->isPerforma == 2) ? ($cusTotal / $masterData->companyReportingER) + $masterData->VATAmountRpt: $masterData->bookingAmountRpt + $masterData->VATAmountRpt)  + ($proccessData['_documentRptAmount']);

            $data['documentType'] = 11;

            $data['createdUserSystemID'] = $empID->empID;
            $data['createdDateTime'] = $time;
            $data['createdUserID'] = $empID->employeeSystemID;
            $data['createdUserPC'] = getenv('COMPUTERNAME');
            $data['timestamp'] = $time;
            array_push($finalData, $data);

            if($masterData->salesType == 3){
                $bs = CustomerInvoiceItemDetails::selectRaw("0 as transAmount, SUM(qtyIssuedDefaultMeasure * issueCostLocal * userQty) as localAmount, SUM(qtyIssuedDefaultMeasure * issueCostRpt * userQty) as rptAmount,financeGLcodebBSSystemID,financeGLcodebBS,localCurrencyID,localCurrencyER,reportingCurrencyER,reportingCurrencyID")->WHERE('custInvoiceDirectAutoID', $masterModel["autoID"])->where('itemFinanceCategoryID', '!=', 2)->whereNotNull('financeGLcodebBSSystemID')->where('financeGLcodebBSSystemID', '>', 0)->groupBy('financeGLcodebBSSystemID')->get();
            }else{
                $bs = CustomerInvoiceItemDetails::selectRaw("0 as transAmount, SUM(qtyIssuedDefaultMeasure * issueCostLocal) as localAmount, SUM(qtyIssuedDefaultMeasure * issueCostRpt) as rptAmount,financeGLcodebBSSystemID,financeGLcodebBS,localCurrencyID,localCurrencyER,reportingCurrencyER,reportingCurrencyID")->WHERE('custInvoiceDirectAutoID', $masterModel["autoID"])->where('itemFinanceCategoryID', '!=', 2)->whereNotNull('financeGLcodebBSSystemID')->where('financeGLcodebBSSystemID', '>', 0)->groupBy('financeGLcodebBSSystemID')->get();
            }
            //get pnl account
            if($masterData->salesType == 3){
                $pl = CustomerInvoiceItemDetails::selectRaw("0 as transAmount,SUM(qtyIssuedDefaultMeasure * issueCostLocal * userQty) as localAmount, SUM(qtyIssuedDefaultMeasure * issueCostRpt * userQty) as rptAmount,financeCogsGLcodePLSystemID,financeCogsGLcodePL,localCurrencyID,localCurrencyER,reportingCurrencyER,reportingCurrencyID")->WHERE('custInvoiceDirectAutoID', $masterModel["autoID"])->where('itemFinanceCategoryID', '!=', 2)->whereNotNull('financeCogsGLcodePLSystemID')->where('financeCogsGLcodePLSystemID', '>', 0)->groupBy('financeCogsGLcodePLSystemID')->get();
            }else{
                $pl = CustomerInvoiceItemDetails::selectRaw("0 as transAmount,SUM(qtyIssuedDefaultMeasure * issueCostLocal) as localAmount, SUM(qtyIssuedDefaultMeasure * issueCostRpt) as rptAmount,financeCogsGLcodePLSystemID,financeCogsGLcodePL,localCurrencyID,localCurrencyER,reportingCurrencyER,reportingCurrencyID")->WHERE('custInvoiceDirectAutoID', $masterModel["autoID"])->where('itemFinanceCategoryID', '!=', 2)->whereNotNull('financeCogsGLcodePLSystemID')->where('financeCogsGLcodePLSystemID', '>', 0)->groupBy('financeCogsGLcodePLSystemID')->get();
            }
            //get revenue account
            if($masterData->salesType == 3){
                $revenue = CustomerInvoiceItemDetails::selectRaw("0 as transAmount,SUM(qtyIssuedDefaultMeasure * sellingCostAfterMarginLocal * userQty) as localAmount, SUM(qtyIssuedDefaultMeasure * sellingCostAfterMarginRpt * userQty) as rptAmount,financeGLcodeRevenueSystemID,financeGLcodeRevenue,localCurrencyID,localCurrencyER,reportingCurrencyER,reportingCurrencyID")->WHERE('custInvoiceDirectAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodeRevenueSystemID')->where('financeGLcodeRevenueSystemID', '>', 0)->groupBy('financeGLcodeRevenueSystemID')->get();
            }else{
                $revenue = CustomerInvoiceItemDetails::selectRaw("0 as transAmount,SUM(qtyIssuedDefaultMeasure * sellingCostAfterMarginLocal) as localAmount, SUM(qtyIssuedDefaultMeasure * sellingCostAfterMarginRpt) as rptAmount,financeGLcodeRevenueSystemID,financeGLcodeRevenue,localCurrencyID,localCurrencyER,reportingCurrencyER,reportingCurrencyID")->WHERE('custInvoiceDirectAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodeRevenueSystemID')->where('financeGLcodeRevenueSystemID', '>', 0)->groupBy('financeGLcodeRevenueSystemID')->get();
            }

            if ($bs) {
                foreach ($bs as $val) {
                    $currencyConversion = \Helper::currencyConversionByER($val->localCurrencyID, $masterData->custTransactionCurrencyID, $val->localAmount, $val->localCurrencyER);

                    $data['chartOfAccountSystemID'] = $val->financeGLcodebBSSystemID;
                    $data['glCode'] = $val->financeGLcodebBS;
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                    $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                    $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                    $data['documentTransAmount'] = ABS((isset($currencyConversion['documentAmount']) ? $currencyConversion['documentAmount'] : 0)) * -1;

                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                    $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                    $data['documentLocalAmount'] = ABS($val->localAmount) * -1;

                    $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                    $data['documentRptCurrencyER'] = $val->reportingCurrencyER;
                    $data['documentRptAmount'] = ABS($val->rptAmount) * -1;

                    array_push($finalData, $data);
                }
            }

            if ($pl) {
                foreach ($pl as $item) {
                    $currencyConversion = \Helper::currencyConversionByER($item->localCurrencyID, $masterData->custTransactionCurrencyID, $item->localAmount, $item->localCurrencyER);
                    $data['chartOfAccountSystemID'] = $item->financeCogsGLcodePLSystemID;
                    $data['glCode'] = $item->financeCogsGLcodePL;
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                    $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                    $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                    $data['documentTransAmount'] = ABS((isset($currencyConversion['documentAmount']) ? $currencyConversion['documentAmount'] : 0));

                    $data['documentLocalCurrencyID'] = $item->localCurrencyID;
                    $data['documentLocalCurrencyER'] = $item->localCurrencyER;
                    $data['documentLocalAmount'] = ABS($item->localAmount);

                    $data['documentRptCurrencyID'] = $item->reportingCurrencyID;
                    $data['documentRptCurrencyER'] = $item->reportingCurrencyER;
                    $data['documentRptAmount'] = ABS($item->rptAmount);

                    array_push($finalData, $data);
                }
            }

            if ($revenue) {

                foreach ($revenue as $item) {
                    $currencyConversion = \Helper::currencyConversionByER($item->localCurrencyID, $masterData->custTransactionCurrencyID, $item->localAmount, $item->localCurrencyER);

                    $data['chartOfAccountSystemID'] = $item->financeGLcodeRevenueSystemID;
                    $data['glCode'] = $item->financeGLcodeRevenue;
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                    $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                    $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                    $data['documentTransAmount'] = ABS((isset($currencyConversion['documentAmount']) ? $currencyConversion['documentAmount'] : 0)) * -1;

                    $data['documentLocalCurrencyID'] = $item->localCurrencyID;
                    $data['documentLocalCurrencyER'] = $item->localCurrencyER;
                    $data['documentLocalAmount'] = ABS($item->localAmount) * -1;

                    $data['documentRptCurrencyID'] = $item->reportingCurrencyID;
                    $data['documentRptCurrencyER'] = $item->reportingCurrencyER;
                    $data['documentRptAmount'] = ABS($item->rptAmount) * -1;

                    array_push($finalData, $data);
                }

            }

            $erp_taxdetail = Taxdetail::where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemCode', $masterData->custInvoiceDirectAutoID)
                ->where('documentSystemID', 20)
                ->get();
        
            

            if (!empty($erp_taxdetail)) {
                $taxConfigData = TaxService::getOutputVATGLAccount($masterModel["companySystemID"]);
                if (!empty($taxConfigData)) {
                    $taxGL = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')
                        ->where('chartOfAccountSystemID', $taxConfigData->outputVatGLAccountAutoID)
                        ->first();
                    if (!empty($taxGL)) {
                        foreach ($erp_taxdetail as $tax) {
                            $data['serviceLineSystemID'] = 24;
                            $data['serviceLineCode'] = 'X';
                            // from customer invoice master table
                            $data['chartOfAccountSystemID'] = $taxGL['chartOfAccountSystemID'];
                            $data['glCode'] = $taxGL->AccountCode;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                            $data['documentNarration'] = $tax->taxDescription;
                            $data['clientContractID'] = 'X';
                            $data['supplierCodeSystem'] = $masterData->customerID;

                            $data['documentTransCurrencyID'] = $tax->currency;
                            $data['documentTransCurrencyER'] = $tax->currencyER;
                            $data['documentTransAmount'] = $tax->payeeDefaultAmount * -1;
                            $data['documentLocalCurrencyID'] = $tax->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $tax->localCurrencyER;
                            $data['documentLocalAmount'] = $tax->localAmount * -1;
                            $data['documentRptCurrencyID'] = $tax->rptCurrencyID;
                            $data['documentRptCurrencyER'] = $tax->rptCurrencyER;
                            $data['documentRptAmount'] = $tax->rptAmount * -1;

                            if ($data['documentTransAmount'] != 0 || $data['documentLocalAmount'] != 0 || $data['documentRptAmount'] != 0) {
                                array_push($finalData, $data);
                            }

                            $taxLedgerData['outputVatGLAccountID'] = $taxGL['chartOfAccountSystemID'];
                        }
                    } else {
                        Log::info('Customer Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                        Log::info('Output Vat GL Account not assigned to company' . date('H:i:s'));
                    }
                } else {
                    Log::info('Customer Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                    Log::info('Output Vat GL Account not configured' . date('H:i:s'));
                }
            }

            if(isset($proccessData['dataArray'])) {
                $finalData = array_merge($finalData,$proccessData['dataArray']);
            }
        }
        elseif ($masterData->isPerforma == 3) { // From Deivery Note
            $customer = CustomerMaster::find($masterData->customerID);
            $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $masterData->customerGLSystemID)->first();
            $unbilledhartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $customer->custUnbilledAccountSystemID)->first();

            $time = Carbon::now();
            if($masterData->salesType == 3){
                $revenue = CustomerInvoiceItemDetails::selectRaw("SUM(qtyIssuedDefaultMeasure * sellingCostAfterMargin * userQty) as transAmount,SUM(qtyIssuedDefaultMeasure * sellingCostAfterMarginLocal * userQty) as localAmount, SUM(qtyIssuedDefaultMeasure * sellingCostAfterMarginRpt * userQty) as rptAmount,financeGLcodeRevenueSystemID,financeGLcodeRevenue,localCurrencyID,localCurrencyER,reportingCurrencyER,reportingCurrencyID")->WHERE('custInvoiceDirectAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodeRevenueSystemID')->where('financeGLcodeRevenueSystemID', '>', 0)->groupBy('financeGLcodeRevenueSystemID')->get();
            }else{
                $revenue = CustomerInvoiceItemDetails::selectRaw("SUM(qtyIssuedDefaultMeasure * sellingCostAfterMargin) as transAmount,SUM(qtyIssuedDefaultMeasure * sellingCostAfterMarginLocal) as localAmount, SUM(qtyIssuedDefaultMeasure * sellingCostAfterMarginRpt) as rptAmount,financeGLcodeRevenueSystemID,financeGLcodeRevenue,localCurrencyID,localCurrencyER,reportingCurrencyER,reportingCurrencyID")->WHERE('custInvoiceDirectAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodeRevenueSystemID')->where('financeGLcodeRevenueSystemID', '>', 0)->groupBy('financeGLcodeRevenueSystemID')->get();
            }

            if ($chartOfAccount) {
                $data['companySystemID'] = $masterData->companySystemID;
                $data['companyID'] = $masterData->companyID;
                $data['masterCompanyID'] = $company->masterComapanyID;
                $data['documentID'] = "INV";
                $data['documentSystemID'] = $masterData->documentSystemiD;
                $data['documentSystemCode'] = $masterData->custInvoiceDirectAutoID;
                $data['documentCode'] = $masterData->bookingInvCode;
                $data['documentDate'] = $masterDocumentDate;
                $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
                $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
                $data['invoiceNumber'] = $masterData->customerInvoiceNo;
                $data['invoiceDate'] = $masterData->customerInvoiceDate;
                $data['documentConfirmedDate'] = $masterData->confirmedDate;
                $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;

                $data['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                $data['serviceLineCode'] = $masterData->serviceLineCode;

                // from customer invoice master table
                $data['chartOfAccountSystemID'] = $chartOfAccount->chartOfAccountSystemID;
                $data['glCode'] = $chartOfAccount->AccountCode;
                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                $data['documentNarration'] = $masterData->comments;
                $data['clientContractID'] = 'X';
                $data['contractUID'] = 159;
                $data['supplierCodeSystem'] = $masterData->customerID;

                $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                $data['documentTransAmount'] = $masterData->bookingAmountTrans + $masterData->VATAmount;

                $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                $data['documentLocalAmount'] = $masterData->bookingAmountLocal + $masterData->VATAmountLocal;

                $data['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                $data['documentRptCurrencyER'] = $masterData->companyReportingER;
                $data['documentRptAmount'] = $masterData->bookingAmountRpt + $masterData->VATAmountRpt;

                $data['documentType'] = 11;

                $data['createdUserSystemID'] = $empID->empID;
                $data['createdDateTime'] = $time;
                $data['createdUserID'] = $empID->employeeSystemID;
                $data['createdUserPC'] = getenv('COMPUTERNAME');
                $data['timestamp'] = $time;
                array_push($finalData, $data);
            }

            if ($unbilledhartOfAccount) {
                $data['companySystemID'] = $masterData->companySystemID;
                $data['companyID'] = $masterData->companyID;
                $data['masterCompanyID'] = $company->masterComapanyID;
                $data['documentID'] = "INV";
                $data['documentSystemID'] = $masterData->documentSystemiD;
                $data['documentSystemCode'] = $masterData->custInvoiceDirectAutoID;
                $data['documentCode'] = $masterData->bookingInvCode;
                //$data['documentDate'] = ($masterData->isPerforma == 1) ? $time : $masterData->bookingDate;
                $data['documentDate'] = $masterDocumentDate;
                $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
                $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
                $data['invoiceNumber'] = $masterData->customerInvoiceNo;
                $data['invoiceDate'] = $masterData->customerInvoiceDate;
                $data['documentConfirmedDate'] = $masterData->confirmedDate;
                $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;

                $data['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                $data['serviceLineCode'] = $masterData->serviceLineCode;

                // from customer invoice master table
                $data['chartOfAccountSystemID'] = $unbilledhartOfAccount->chartOfAccountSystemID;
                $data['glCode'] = $unbilledhartOfAccount->AccountCode;
                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                $data['documentNarration'] = $masterData->comments;
                $data['clientContractID'] = 'X';
                $data['contractUID'] = 159;
                $data['supplierCodeSystem'] = $masterData->customerID;

                $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                $data['documentTransAmount'] = (ABS($masterData->bookingAmountTrans) + ABS($masterData->VATAmount)) * -1;

                $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                $data['documentLocalAmount'] = (ABS($masterData->bookingAmountLocal) + ABS($masterData->VATAmountLocal)) * -1;

                $data['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                $data['documentRptCurrencyER'] = $masterData->companyReportingER;
                $data['documentRptAmount'] = (ABS($masterData->bookingAmountRpt) + ABS($masterData->VATAmountRpt)) * -1;

                $data['documentType'] = 11;

                $data['createdUserSystemID'] = $empID->empID;
                $data['createdDateTime'] = $time;
                $data['createdUserID'] = $empID->employeeSystemID;
                $data['createdUserPC'] = getenv('COMPUTERNAME');
                $data['timestamp'] = $time;
                // array_push($finalData, $data);
            }

            if ($revenue) {

                foreach ($revenue as $item) {

                    $data['chartOfAccountSystemID'] = $item->financeGLcodeRevenueSystemID;
                    $data['glCode'] = $item->financeGLcodeRevenue;
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                    $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                    $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                    $data['documentTransAmount'] = ABS($item->transAmount) * -1;

                    $data['documentLocalCurrencyID'] = $item->localCurrencyID;
                    $data['documentLocalCurrencyER'] = $item->localCurrencyER;
                    $data['documentLocalAmount'] = ABS($item->localAmount) * -1;

                    $data['documentRptCurrencyID'] = $item->reportingCurrencyID;
                    $data['documentRptCurrencyER'] = $item->reportingCurrencyER;
                    $data['documentRptAmount'] = ABS($item->rptAmount) * -1;

                    array_push($finalData, $data);
                }

            }


            $erp_taxdetail = Taxdetail::where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemCode', $masterData->custInvoiceDirectAutoID)
                ->where('documentSystemID', 20)
                ->get();

            if (!empty($erp_taxdetail)) {

                $taxConfigData = TaxService::getOutputVATGLAccount($masterModel["companySystemID"]);
                if (!empty($taxConfigData)) {
                    $taxGL = ChartOfAccountsAssigned::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')
                        ->where('chartOfAccountSystemID', $taxConfigData->outputVatGLAccountAutoID)
                        ->where('companySystemID', $masterData->companySystemID)
                        ->first();
                    if (!empty($taxGL)) {
                        foreach ($erp_taxdetail as $tax) {

                            $data['serviceLineSystemID'] = 24;
                            $data['serviceLineCode'] = 'X';

                            // from customer invoice master table
                            $data['chartOfAccountSystemID'] = $taxGL['chartOfAccountSystemID'];
                            $data['glCode'] = $taxGL->AccountCode;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                            $data['documentNarration'] = $tax->taxDescription;
                            $data['clientContractID'] = 'X';
                            $data['supplierCodeSystem'] = $masterData->customerID;

                            $data['documentTransCurrencyID'] = $tax->currency;
                            $data['documentTransCurrencyER'] = $tax->currencyER;
                            $data['documentTransAmount'] = ABS($tax->amount) * -1;
                            $data['documentLocalCurrencyID'] = $tax->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $tax->localCurrencyER;
                            $data['documentLocalAmount'] = ABS($tax->localAmount) * -1;
                            $data['documentRptCurrencyID'] = $tax->rptCurrencyID;
                            $data['documentRptCurrencyER'] = $tax->rptCurrencyER;
                            $data['documentRptAmount'] = ABS($tax->rptAmount) * -1;

                            if ($data['documentTransAmount'] != 0 || $data['documentLocalAmount'] != 0 || $data['documentRptAmount'] != 0) {
                                array_push($finalData, $data);
                            }

                            $taxLedgerData['outputVatGLAccountID'] = $taxGL['chartOfAccountSystemID'];
                        }
                    } else {
                        Log::info('Customer Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                        Log::info('Output Vat GL Account not assigned to company' . date('H:i:s'));
                    }
                } else {
                    Log::info('Customer Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                    Log::info('Output Vat GL Account not configured' . date('H:i:s'));
                }


                $taxConfigData2 = TaxService::getOutputVATTransferGLAccount($masterModel["companySystemID"]);
                if (!empty($taxConfigData2)) {
                    $taxGL = ChartOfAccountsAssigned::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')
                        ->where('chartOfAccountSystemID', $taxConfigData2->outputVatTransferGLAccountAutoID)
                        ->where('companySystemID', $taxConfigData2->companySystemID)
                        ->first();
                    if (!empty($taxGL)) {
                        foreach ($erp_taxdetail as $tax) {

                            $data['serviceLineSystemID'] = 24;
                            $data['serviceLineCode'] = 'X';

                            // from customer invoice master table
                            $data['chartOfAccountSystemID'] = $taxGL['chartOfAccountSystemID'];
                            $data['glCode'] = $taxGL->AccountCode;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                            $data['documentNarration'] = $tax->taxDescription;
                            $data['clientContractID'] = 'X';
                            $data['supplierCodeSystem'] = $masterData->customerID;

                            $data['documentTransCurrencyID'] = $tax->currency;
                            $data['documentTransCurrencyER'] = $tax->currencyER;
                            $data['documentTransAmount'] = ABS($tax->amount);
                            $data['documentLocalCurrencyID'] = $tax->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $tax->localCurrencyER;
                            $data['documentLocalAmount'] = ABS($tax->localAmount);
                            $data['documentRptCurrencyID'] = $tax->rptCurrencyID;
                            $data['documentRptCurrencyER'] = $tax->rptCurrencyER;
                            $data['documentRptAmount'] = ABS($tax->rptAmount);
                            // array_push($finalData, $data);

                            $taxLedgerData['outputVatTransferGLAccountID'] = $taxGL['chartOfAccountSystemID'];
                        }
                    } else {
                        Log::info('Customer Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                        Log::info('Output Vat GL Account not assigned to company' . date('H:i:s'));
                    }
                } else {
                    Log::info('Customer Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                    Log::info('Output Vat GL Account not configured' . date('H:i:s'));
                }
            }

        }
        else {
            $detOne = CustomerInvoiceDirectDetail::with(['contract'])->where('custInvoiceDirectID', $masterModel["autoID"])->first();
            $detail = CustomerInvoiceDirectDetail::selectRaw("sum(comRptAmount) as comRptAmount, comRptCurrency, sum(localAmount) as localAmount , localCurrencyER, localCurrency, sum(invoiceAmount) as invoiceAmount, invoiceAmountCurrencyER, invoiceAmountCurrency,comRptCurrencyER, customerID, clientContractID, comments, glSystemID,   serviceLineSystemID,serviceLineCode, sum(VATAmount) as VATAmount, sum(VATAmountLocal) as VATAmountLocal, sum(VATAmountRpt) as VATAmountRpt, sum(VATAmount*invoiceQty) as VATAmountTotal, sum(VATAmountLocal*invoiceQty) as VATAmountLocalTotal, sum(VATAmountRpt*invoiceQty) as VATAmountRptTotal")->with(['contract'])->WHERE('custInvoiceDirectID', $masterModel["autoID"])->groupBy('custInvDirDetAutoID')->get();
            $company = Company::select('masterComapanyID')->where('companySystemID', $masterData->companySystemID)->first();
            $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $masterData->customerGLSystemID)->first();

            $date = new Carbon($masterData->bookingDate);
            $time = Carbon::now();

            $segmentWiseDetail = CustomerInvoiceDirectDetail::selectRaw("sum(comRptAmount) as comRptAmount, comRptCurrency, sum(localAmount) as localAmount , localCurrencyER, localCurrency, sum(invoiceAmount) as invoiceAmount, invoiceAmountCurrencyER, invoiceAmountCurrency,comRptCurrencyER, customerID, clientContractID, comments, glSystemID,   serviceLineSystemID,serviceLineCode, sum(VATAmount) as VATAmount, sum(VATAmountLocal) as VATAmountLocal, sum(VATAmountRpt) as VATAmountRpt, sum(VATAmount*invoiceQty) as VATAmountTotal, sum(VATAmountLocal*invoiceQty) as VATAmountLocalTotal, sum(VATAmountRpt*invoiceQty) as VATAmountRptTotal")->with(['contract'])->WHERE('custInvoiceDirectID', $masterModel["autoID"])->groupBy('serviceLineSystemID', 'clientContractID')->get();

            $data['companySystemID'] = $masterData->companySystemID;
            $data['companyID'] = $masterData->companyID;
            $data['masterCompanyID'] = $company->masterComapanyID;
            $data['documentID'] = "INV";
            $data['documentSystemID'] = $masterData->documentSystemiD;
            $data['documentSystemCode'] = $masterData->custInvoiceDirectAutoID;
            $data['documentCode'] = $masterData->bookingInvCode;
            //$data['documentDate'] = ($masterData->isPerforma == 1) ? $time : $masterData->bookingDate;
            $data['documentDate'] = $masterDocumentDate;
            $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
            $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
            $data['invoiceNumber'] = $masterData->customerInvoiceNo;
            $data['invoiceDate'] = $masterData->customerInvoiceDate;
            $data['documentConfirmedDate'] = $masterData->confirmedDate;
            $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
            $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;


            // from customer invoice master table
            $data['chartOfAccountSystemID'] = $chartOfAccount->chartOfAccountSystemID;
            $data['glCode'] = $chartOfAccount->AccountCode;
            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

            $data['documentNarration'] = $masterData->comments;
            $data['supplierCodeSystem'] = $masterData->customerID;
            $data['documentType'] = 11;
            $data['createdUserSystemID'] = $empID->empID;
            $data['createdDateTime'] = $time;
            $data['createdUserID'] = $empID->employeeSystemID;
            $data['createdUserPC'] = getenv('COMPUTERNAME');
            $data['timestamp'] = $time;
            
            if ($masterData->isPerforma == 1) {
                $data['serviceLineSystemID'] = $detOne->serviceLineSystemID;
                $data['serviceLineCode'] = $detOne->serviceLineCode;
                $data['clientContractID'] = $detOne->clientContractID;
                $data['contractUID'] = $detOne->contract ? $detOne->contract->contractUID : 0;

                $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                $data['documentTransAmount'] = $masterData->bookingAmountTrans + $masterData->VATAmount;
                $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                $data['documentLocalAmount'] = $masterData->bookingAmountLocal + $masterData->VATAmountLocal;
                $data['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                $data['documentRptCurrencyER'] = $masterData->companyReportingER;
                $data['documentRptAmount'] = $masterData->bookingAmountRpt + $masterData->VATAmountRpt;
                array_push($finalData, $data);
            } else {
                foreach ($segmentWiseDetail as $item) {
                    $data['serviceLineSystemID'] = $item->serviceLineSystemID;
                    $data['serviceLineCode'] = $item->serviceLineCode;
                    $data['clientContractID'] = $item->clientContractID;
                    $data['contractUID'] = $item->contract ? $item->contract->contractUID : 0;

                    $data['documentTransCurrencyID'] = $item->invoiceAmountCurrency;
                    $data['documentTransCurrencyER'] = $item->invoiceAmountCurrencyER;
                    $data['documentTransAmount'] = $item->invoiceAmount + $item->VATAmountTotal;
                    $data['documentLocalCurrencyID'] = $item->localCurrency;

                    $data['documentLocalCurrencyER'] = $item->localCurrencyER;
                    $data['documentLocalAmount'] = $data['documentTransAmount'] / $data['documentLocalCurrencyER'];
                    $data['documentRptCurrencyID'] = $item->comRptCurrency;
                    $data['documentRptCurrencyER'] = $item->comRptCurrencyER;
                    $data['documentRptAmount'] = $data['documentTransAmount'] / $data['documentRptCurrencyER'];
                    
                    array_push($finalData, $data);
                }
            }


            if (!empty($detail)) {
                foreach ($detail as $item) {
                    $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $item->glSystemID)->first();

                    $data['companySystemID'] = $masterData->companySystemID;
                    $data['companyID'] = $masterData->companyID;
                    $data['documentSystemID'] = $masterData->documentSystemiD;
                    $data['documentSystemCode'] = $masterData->custInvoiceDirectAutoID;
                    $data['documentCode'] = $masterData->bookingInvCode;
                    $data['documentDate'] = $masterDocumentDate;
                    $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
                    $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
                    $data['invoiceNumber'] = $masterData->customerInvoiceNo;
                    $data['invoiceDate'] = $masterData->customerInvoiceDate;
                    $data['documentConfirmedDate'] = $masterData->confirmedDate;
                    $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                    $data['masterCompanyID'] = $company->masterComapanyID;

                    $data['serviceLineSystemID'] = $item->serviceLineSystemID;
                    $data['serviceLineCode'] = $item->serviceLineCode;

                    // from customer invoice master table
                    $data['chartOfAccountSystemID'] = $item->glSystemID;
                    $data['glCode'] = $chartOfAccount->AccountCode;
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                    $data['documentNarration'] = $item->comments;
                    $data['clientContractID'] = $item->clientContractID;
                    $data['supplierCodeSystem'] = $item->customerID;

                    $data['documentTransCurrencyID'] = $item->invoiceAmountCurrency;
                    $data['documentTransCurrencyER'] = $item->invoiceAmountCurrencyER;
                    $data['documentTransAmount'] = (($masterData->isPerforma == 1) ? ($item->invoiceAmount - $item->VATAmount) : $item->invoiceAmount) * -1;
                    $data['documentLocalCurrencyID'] = $item->localCurrency;

                    $data['documentLocalCurrencyER'] = $item->localCurrencyER;
                    $data['documentLocalAmount'] = $data['documentTransAmount'] / $data['documentLocalCurrencyER'];
                    $data['documentRptCurrencyID'] = $item->comRptCurrency;
                    $data['documentRptCurrencyER'] = $item->comRptCurrencyER;
                    $data['documentRptAmount'] = $data['documentTransAmount'] / $data['documentRptCurrencyER'];
                    array_push($finalData, $data);
                }
            }

            $erp_taxdetail = Taxdetail::where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemCode', $masterData->custInvoiceDirectAutoID)
                ->where('documentSystemID', 20)
                ->get();
            if (count($erp_taxdetail) > 0) {
                // Input VAT control
                $taxConfigData = TaxService::getOutputVATGLAccount($masterModel["companySystemID"]);
                if (!empty($taxConfigData)) {
                    $taxGL = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')
                        ->where('chartOfAccountSystemID', $taxConfigData->outputVatGLAccountAutoID)
                        ->first();

                    if (!empty($taxGL)) {
                        // from customer invoice master table
                        $data['chartOfAccountSystemID'] = $taxGL['chartOfAccountSystemID'];
                        $data['glCode'] = $taxGL->AccountCode;
                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                        if (!empty($detOne) && isset($detOne->clientContractID)) {
                            $data['clientContractID'] = $detOne->clientContractID;
                        } else {
                            $data['clientContractID'] = null;
                        }
                        $data['supplierCodeSystem'] = $masterData->customerID;

                        if ($masterData->isPerforma == 1) {
                            foreach ($erp_taxdetail as $tax) {
                                $data['serviceLineSystemID'] = 24;
                                $data['serviceLineCode'] = 'X';
                                $data['documentNarration'] = $tax->taxDescription;
                                $data['documentTransCurrencyID'] = $tax->currency;
                                $data['documentTransCurrencyER'] = $tax->currencyER;
                                $data['documentTransAmount'] = $tax->amount * -1;
                                $data['documentLocalCurrencyID'] = $tax->localCurrencyID;
                                $data['documentLocalCurrencyER'] = $tax->localCurrencyER;
                                $data['documentLocalAmount'] = $tax->localAmount * -1;
                                $data['documentRptCurrencyID'] = $tax->rptCurrencyID;
                                $data['documentRptCurrencyER'] = $tax->rptCurrencyER;
                                $data['documentRptAmount'] = $tax->rptAmount * -1;

                                if ($data['documentTransAmount'] != 0 || $data['documentLocalAmount'] != 0 || $data['documentRptAmount'] != 0) {
                                    array_push($finalData, $data);
                                }

                                $taxLedgerData['outputVatGLAccountID'] = $taxGL['chartOfAccountSystemID'];
                            }
                        } else {
                            foreach ($segmentWiseDetail as $item) {
                                $data['serviceLineSystemID'] = $item->serviceLineSystemID;
                                $data['serviceLineCode'] = $item->serviceLineCode;
                                $data['clientContractID'] = $item->clientContractID;
                                $data['contractUID'] = $item->contract ? $item->contract->contractUID : 0;
                                $data['documentNarration'] = "";
                                $data['documentTransCurrencyID'] = $item->invoiceAmountCurrency;
                                $data['documentTransCurrencyER'] = $item->invoiceAmountCurrencyER;
                                $data['documentTransAmount'] = $item->VATAmountTotal * -1;
                                $data['documentLocalCurrencyID'] = $item->localCurrency;

                                $data['documentLocalCurrencyER'] = $item->localCurrencyER;
                                $data['documentLocalAmount'] = $data['documentTransAmount'] / $data['documentLocalCurrencyER'];
                                $data['documentRptCurrencyID'] = $item->comRptCurrency;
                                $data['documentRptCurrencyER'] = $item->comRptCurrencyER;
                                $data['documentRptAmount'] = $data['documentTransAmount'] / $data['documentRptCurrencyER'];
                                
                                if ($data['documentTransAmount'] != 0 || $data['documentLocalAmount'] != 0 || $data['documentRptAmount'] != 0) {
                                    array_push($finalData, $data);
                                }
                                $taxLedgerData['outputVatGLAccountID'] = $taxGL['chartOfAccountSystemID'];
                            }
                        }
                    } else {
                        Log::info('Customer Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                        Log::info('Output Vat GL Account not assigned to company' . date('H:i:s'));
                    }
                } else {
                    Log::info('Customer Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                    Log::info('Output Vat GL Account not configured' . date('H:i:s'));
                }
            }
        }

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'taxLedgerData' => $taxLedgerData]];

    }


    public static function generateCustomerDirectInvoiceDetailsGL($masterData,$finalData,$masterDocumentDate,$empID) {
        $_customerInvoiceDirectDetails = CustomerInvoiceDirectDetail::with(['chart_Of_account'])->where('custInvoiceDirectID',$masterData->custInvoiceDirectAutoID)->get();
        $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $masterData->customerGLSystemID)->first();
        $_documentTransAmount = 0; 
        $_documentLocalAmount = 0;
        $_documentRptAmount = 0;
        $dataArray = [];
        $time = Carbon::now();
        $company = Company::select('masterComapanyID')->where('companySystemID', $masterData->companySystemID)->first();

        foreach ($_customerInvoiceDirectDetails as $item) {
            if($item->chart_Of_account) {
                $data['companySystemID'] = $masterData->companySystemID;
                $data['companyID'] = $masterData->companyID;
                $data['masterCompanyID'] = $company->masterComapanyID;
                $data['documentID'] = "INV";
                $data['documentSystemID'] = $masterData->documentSystemiD;
                $data['documentSystemCode'] = $masterData->custInvoiceDirectAutoID;
                $data['documentCode'] = $masterData->bookingInvCode;
                $data['serviceLineSystemID'] = $item->serviceLineSystemID;
                $data['serviceLineCode'] = $item->serviceLineCode;
                $data['clientContractID'] = 'X';
                $data['contractUID'] = $item->contract ? $item->contract->contractUID : 0;           
                $data['documentNarration'] = $item->glCodeDes;
                $data['documentTransCurrencyID'] = $item->invoiceAmountCurrency;
                $data['documentTransCurrencyER'] = $item->invoiceAmountCurrencyER;
                $data['documentLocalCurrencyID'] = $item->localCurrency;
                $data['documentLocalCurrencyER'] = $item->localCurrencyER;
                $data['documentRptCurrencyID'] = $item->comRptCurrency;
                $data['documentRptCurrencyER'] = $item->comRptCurrencyER;
                $data['invoiceNumber'] = $masterData->customerInvoiceNo;
                $data['supplierCodeSystem'] = $masterData->customerID;
                $data['chartOfAccountSystemID'] = $item->chart_Of_account->chartOfAccountSystemID;
                $data['documentDate'] = $masterDocumentDate;
                $data['glCode'] = $item->chart_Of_account->AccountCode;
                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                $data['invoiceDate'] = $masterData->customerInvoiceDate;
                $data['documentConfirmedDate'] = $masterData->confirmedDate;
                $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                $data['documentDate'] = $masterDocumentDate;
                $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
                $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
                $data['createdUserSystemID'] = $empID->empID;
                $data['createdDateTime'] = $time;
                $data['createdUserID'] = $empID->employeeSystemID;
                $data['createdUserPC'] = getenv('COMPUTERNAME');
                $data['timestamp'] = $time;

                if($item->chart_Of_account->controlAccountsSystemID == 2 || $item->chart_Of_account->controlAccountsSystemID == 5 || $item->chart_Of_account->controlAccountsSystemID == 3) {
                    $data['documentTransAmount'] = $item->invoiceAmount + $item->VATAmountTotal;
                    $data['documentLocalAmount'] = $item->localAmount + $item->VATAmountLocalTotal;
                    $data['documentRptAmount'] = $item->comRptAmount + $item->VATAmountRptTotal;
                    array_push($dataArray, $data);
                    $_documentTransAmount -= ($item->invoiceAmount + $item->VATAmountTotal);
                    $_documentLocalAmount -= ($item->localAmount + $item->VATAmountLocalTotal);
                    $_documentRptAmount -= ($item->comRptAmount + $item->VATAmountRptTotal);
                    
                }else if($item->chart_Of_account->controlAccountsSystemID == 4) {
                    // liability
                    $data['documentTransAmount'] = -($item->invoiceAmount + $item->VATAmountTotal);
                    $data['documentLocalAmount'] = -($item->localAmount + $item->VATAmountLocalTotal);
                    $data['documentRptAmount'] = -($item->comRptAmount + $item->VATAmountRptTotal);
                    array_push($dataArray, $data);
                    $_documentTransAmount += $item->invoiceAmount + $item->VATAmountTotal;
                    $_documentLocalAmount += $item->localAmount + $item->VATAmountLocalTotal;
                    $_documentRptAmount += $item->comRptAmount + $item->VATAmountRptTotal;  
                }else{
                    $data['documentTransAmount'] = -($item->invoiceAmount + $item->VATAmountTotal);
                    $data['documentLocalAmount'] = -($item->localAmount + $item->VATAmountLocalTotal);
                    $data['documentRptAmount'] = -($item->comRptAmount + $item->VATAmountRptTotal);
                    array_push($dataArray, $data);
                    $_documentTransAmount += $item->invoiceAmount + $item->VATAmountTotal;
                    $_documentLocalAmount += $item->localAmount + $item->VATAmountLocalTotal;
                    $_documentRptAmount += $item->comRptAmount + $item->VATAmountRptTotal;  
                }
            }           

        }

        return ['dataArray' => $dataArray,'_documentTransAmount' => $_documentTransAmount,'_documentLocalAmount' => $_documentLocalAmount,'_documentRptAmount' =>$_documentRptAmount];


    }
}
