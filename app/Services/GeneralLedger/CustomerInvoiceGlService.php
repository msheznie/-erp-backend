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

class CustomerInvoiceGlService
{
	public static function processEntry($masterModel)
	{
        $data = [];
        $taxLedgerData = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);
        $masterData = CustomerInvoiceDirect::with(['finance_period_by'])->find($masterModel["autoID"]);
        $company = Company::select('masterComapanyID')->where('companySystemID', $masterData->companySystemID)->first();
        if ($masterData->isPerforma == 2 || $masterData->isPerforma == 4 || $masterData->isPerforma == 5) {   // item sales invoice || from sales order || from sales quotation
            $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $masterData->customerGLSystemID)->first();
            $masterDocumentDate = Carbon::now();

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

            $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
            $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
            $data['documentTransAmount'] = 0;

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

            $bs = CustomerInvoiceItemDetails::selectRaw("0 as transAmount, SUM(qtyIssuedDefaultMeasure * issueCostLocal) as localAmount, SUM(qtyIssuedDefaultMeasure * issueCostRpt) as rptAmount,financeGLcodebBSSystemID,financeGLcodebBS,localCurrencyID,localCurrencyER,reportingCurrencyER,reportingCurrencyID")->WHERE('custInvoiceDirectAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->where('financeGLcodebBSSystemID', '>', 0)->groupBy('financeGLcodebBSSystemID')->first();
            //get pnl account
            $pl = CustomerInvoiceItemDetails::selectRaw("0 as transAmount,SUM(qtyIssuedDefaultMeasure * issueCostLocal) as localAmount, SUM(qtyIssuedDefaultMeasure * issueCostRpt) as rptAmount,financeGLcodePLSystemID,financeGLcodePL,localCurrencyID,localCurrencyER,reportingCurrencyER,reportingCurrencyID")->WHERE('custInvoiceDirectAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodePLSystemID')->where('financeGLcodePLSystemID', '>', 0)->groupBy('financeGLcodePLSystemID')->get();

            $revenue = CustomerInvoiceItemDetails::selectRaw("0 as transAmount,SUM(qtyIssuedDefaultMeasure * sellingCostAfterMarginLocal) as localAmount, SUM(qtyIssuedDefaultMeasure * sellingCostAfterMarginRpt) as rptAmount,financeGLcodeRevenueSystemID,financeGLcodeRevenue,localCurrencyID,localCurrencyER,reportingCurrencyER,reportingCurrencyID")->WHERE('custInvoiceDirectAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodeRevenueSystemID')->where('financeGLcodeRevenueSystemID', '>', 0)->groupBy('financeGLcodeRevenueSystemID')->get();

            if ($bs) {

                $data['chartOfAccountSystemID'] = $bs->financeGLcodebBSSystemID;
                $data['glCode'] = $bs->financeGLcodebBS;
                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                $data['documentTransAmount'] = ABS($bs->transAmount) * -1;

                $data['documentLocalCurrencyID'] = $bs->localCurrencyID;
                $data['documentLocalCurrencyER'] = $bs->localCurrencyER;
                $data['documentLocalAmount'] = ABS($bs->localAmount) * -1;

                $data['documentRptCurrencyID'] = $bs->reportingCurrencyID;
                $data['documentRptCurrencyER'] = $bs->reportingCurrencyER;
                $data['documentRptAmount'] = ABS($bs->rptAmount) * -1;

                array_push($finalData, $data);
            }

            if ($pl) {
                foreach ($pl as $item) {
                    $data['chartOfAccountSystemID'] = $item->financeGLcodePLSystemID;
                    $data['glCode'] = $item->financeGLcodePL;
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                    $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                    $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                    $data['documentTransAmount'] = ABS($item->transAmount);

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
                            $data['documentTransAmount'] = 0;
                            $data['documentLocalCurrencyID'] = $tax->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $tax->localCurrencyER;
                            $data['documentLocalAmount'] = $tax->localAmount * -1;
                            $data['documentRptCurrencyID'] = $tax->rptCurrencyID;
                            $data['documentRptCurrencyER'] = $tax->rptCurrencyER;
                            $data['documentRptAmount'] = $tax->rptAmount * -1;
                            array_push($finalData, $data);

                            $taxLedgerData['outputVatGLAccountID'] = $taxGL['chartOfAccountSystemID'];
                        }
                    } else {
                        return ['status' => false, 'error' => ['message' => "Output Vat GL Account not assigned to company"]];
                    }
                } else {
                    return ['status' => false, 'error' => ['message' => "Output Vat GL Account not configured"]];
                }
            }

        }
        elseif ($masterData->isPerforma == 3) { // From Deivery Note
            $customer = CustomerMaster::find($masterData->customerID);
            $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $masterData->customerGLSystemID)->first();
            $unbilledhartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $customer->custUnbilledAccountSystemID)->first();

            $masterDocumentDate = Carbon::now();
            $time = Carbon::now();
            if ($masterData->finance_period_by->isActive == -1) {
                $masterDocumentDate = $masterData->bookingDate;
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
                array_push($finalData, $data);
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
                            array_push($finalData, $data);

                            $taxLedgerData['outputVatGLAccountID'] = $taxGL['chartOfAccountSystemID'];
                        }
                    } else {
                        return ['status' => false, 'error' => ['message' => "Output Vat GL Account not assigned to company"]];
                    }
                } else {
                    return ['status' => false, 'error' => ['message' => "Output Vat GL Account not configured"]];
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
                            array_push($finalData, $data);

                            $taxLedgerData['outputVatTransferGLAccountID'] = $taxGL['chartOfAccountSystemID'];
                        }
                    } else {
                        return ['status' => false, 'error' => ['message' => "Output Vat Transfer GL Account not assigned to company"]];
                    }
                } else {
                    return ['status' => false, 'error' => ['message' => "Output Vat Transfer GL Account not configured"]];
                }
            }

        }
        else {
            $detOne = CustomerInvoiceDirectDetail::with(['contract'])->where('custInvoiceDirectID', $masterModel["autoID"])->first();
            $detail = CustomerInvoiceDirectDetail::selectRaw("sum(comRptAmount) as comRptAmount, comRptCurrency, sum(localAmount) as localAmount , localCurrencyER, localCurrency, sum(invoiceAmount) as invoiceAmount, invoiceAmountCurrencyER, invoiceAmountCurrency,comRptCurrencyER, customerID, clientContractID, comments, glSystemID,   serviceLineSystemID,serviceLineCode, sum(VATAmount) as VATAmount, sum(VATAmountLocal) as VATAmountLocal, sum(VATAmountRpt) as VATAmountRpt")->WHERE('custInvoiceDirectID', $masterModel["autoID"])->groupBy('glCode', 'serviceLineCode', 'comments')->get();
            $company = Company::select('masterComapanyID')->where('companySystemID', $masterData->companySystemID)->first();
            $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $masterData->customerGLSystemID)->first();

            $date = new Carbon($masterData->bookingDate);
            $time = Carbon::now();

            $masterDocumentDate = $time;
            if ($masterData->isPerforma == 1) {
                $masterDocumentDate = $time;
            } else {
                if ($masterData->finance_period_by->isActive == -1) {
                    $masterDocumentDate = $masterData->bookingDate;
                }
            }
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

            $data['serviceLineSystemID'] = $detOne->serviceLineSystemID;
            $data['serviceLineCode'] = $detOne->serviceLineCode;

            // from customer invoice master table
            $data['chartOfAccountSystemID'] = $chartOfAccount->chartOfAccountSystemID;
            $data['glCode'] = $chartOfAccount->AccountCode;
            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

            $data['documentNarration'] = $masterData->comments;
            $data['clientContractID'] = $detOne->clientContractID;
            $data['contractUID'] = $detOne->contract ? $detOne->contract->contractUID : 0;
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
                    $data['documentLocalAmount'] = (($masterData->isPerforma == 1) ? ($item->localAmount - $item->VATAmountLocal) : $item->localAmount) * -1;
                    $data['documentRptCurrencyID'] = $item->comRptCurrency;
                    $data['documentRptCurrencyER'] = $item->comRptCurrencyER;
                    $data['documentRptAmount'] = (($masterData->isPerforma == 1) ? ($item->comRptAmount - $item->VATAmountRpt) : $item->comRptAmount) * -1;
                    array_push($finalData, $data);
                }
            }

            $erp_taxdetail = Taxdetail::where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemCode', $masterData->custInvoiceDirectAutoID)
                ->where('documentSystemID', 20)
                ->get();
            if (!empty($erp_taxdetail)) {

                // Input VAT control
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
                            $data['clientContractID'] = $detOne->clientContractID;
                            $data['supplierCodeSystem'] = $masterData->customerID;

                            $data['documentTransCurrencyID'] = $tax->currency;
                            $data['documentTransCurrencyER'] = $tax->currencyER;
                            $data['documentTransAmount'] = $tax->amount * -1;
                            $data['documentLocalCurrencyID'] = $tax->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $tax->localCurrencyER;
                            $data['documentLocalAmount'] = $tax->localAmount * -1;
                            $data['documentRptCurrencyID'] = $tax->rptCurrencyID;
                            $data['documentRptCurrencyER'] = $tax->rptCurrencyER;
                            $data['documentRptAmount'] = $tax->rptAmount * -1;
                            array_push($finalData, $data);

                            $taxLedgerData['outputVatGLAccountID'] = $taxGL['chartOfAccountSystemID'];
                        }
                    } else {
                        return ['status' => false, 'error' => ['message' => "Output Vat GL Account not assigned to company"]];
                    }
                } else {
                    return ['status' => false, 'error' => ['message' => "Output Vat GL Account not configured"]];
                }
            }
        }

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'taxLedgerData' => $taxLedgerData]];

    }
}