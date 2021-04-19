<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Taxdetail;
use App\Models\Company;
use App\Models\GRVMaster;
use App\Models\GRVDetails;
use App\Models\CreditNote;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetails;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\DeliveryOrder;
use App\Models\CreditNoteDetails;
use App\Models\DeliveryOrderDetail;
use App\Models\TaxLedger;
use App\Models\DebitNote;
use App\Models\DebitNoteDetails;
use App\Models\TaxVatCategories;
use App\helper\TaxService;
use App\Models\Employee;
use App\Models\SalesReturn;
use App\Models\SalesReturnDetail;
use App\Models\BookInvSuppMaster;
use App\Models\DirectInvoiceDetails;

class TaxLedgerInsert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $masterModel;
    protected $taxLedgerData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($masterModel, $taxLedgerData)
    {
        $this->masterModel = $masterModel;
        $this->taxLedgerData = $taxLedgerData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path() . '/logs/tax_ledger_jobs.log');
        Log::info('---- Tax Ledger  Start-----' . date('H:i:s'));
        $masterModel = $this->masterModel;
        $taxLedgerData = $this->taxLedgerData;

        Log::info($taxLedgerData);
        Log::info($masterModel);
        
        if (!empty($masterModel)) {
            DB::beginTransaction();
            try {
                $finalData = [];
                $empID = Employee::find($masterModel['employeeSystemID']);
                $ledgerData = [
                    'documentSystemID' => $masterModel["documentSystemID"],
                    'documentMasterAutoID' => $masterModel["autoID"],
                    'inputVATGlAccountID' => isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null,
                    'inputVatTransferAccountID' => isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null,
                    'outputVatTransferGLAccountID' => isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null,
                    'outputVatGLAccountID' => isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null,
                    'companySystemID' => $masterModel['companySystemID'],
                    'createdPCID' =>  gethostname(),
                    'createdUserID' => $empID->employeeSystemID,
                    'createdDateTime' => \Helper::currentDateTime(),
                    'modifiedPCID' => gethostname(),
                    'modifiedUserID' => $empID->employeeSystemID,
                    'modifiedDateTime' => \Helper::currentDateTime()
                ];

                switch ($masterModel["documentSystemID"]) {
                    case 3: //GRV
                        $details = GRVDetails::selectRaw('SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionER,companyReportingCurrencyID,companyReportingER,localCurrencyID,localCurrencyER')
                                                ->where('grvAutoID', $masterModel["autoID"])
                                                ->whereNotNull('vatSubCategoryID')
                                                ->groupBy('vatSubCategoryID')
                                                ->get();

                        $master = GRVMaster::with(['financeperiod_by'])->find($masterModel["autoID"]);

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if (isset($master->financeperiod_by->isActive) && $master->financeperiod_by->isActive == -1) {
                            $masterDocumentDate = $master->grvDate;
                        }

                        $ledgerData['documentCode'] = $master->grvPrimaryCode;
                        $ledgerData['documentDate'] = $masterDocumentDate;
                        $ledgerData['partyID'] = $master->supplierID;
                        $ledgerData['documentFinalApprovedByEmpSystemID'] = $master->approvedByUserSystemID;
                        $ledgerData['documentTransAmount'] = $master->grvTotalSupplierTransactionCurrency;
                        $ledgerData['documentLocalAmount'] = $master->grvTotalLocalCurrency;
                        $ledgerData['documentReportingAmount'] = $master->grvTotalComRptCurrency;
                        
                        foreach ($details as $key => $value) {
                            $subCategoryData = TaxVatCategories::with(['tax'])->find($value->vatSubCategoryID);

                            if ($subCategoryData) {
                                $ledgerData['taxAuthorityAutoID'] = isset($subCategoryData->tax->authorityAutoID) ? $subCategoryData->tax->authorityAutoID : null;
                            }

                            $ledgerData['subCategoryID'] = $value->vatSubCategoryID;
                            $ledgerData['masterCategoryID'] = $value->vatMasterCategoryID;
                            $ledgerData['localAmount'] = $value->localVATAmount;
                            $ledgerData['rptAmount'] = $value->rptVATAmount;
                            $ledgerData['transAmount'] = $value->transVATAmount;
                            $ledgerData['transER'] = $value->supplierTransactionER;
                            $ledgerData['localER'] = $value->localCurrencyER;
                            $ledgerData['comRptER'] = $value->companyReportingER;
                            $ledgerData['localCurrencyID'] = $value->localCurrencyID;
                            $ledgerData['rptCurrencyID'] = $value->companyReportingCurrencyID;
                            $ledgerData['transCurrencyID'] = $value->supplierTransactionCurrencyID;
                            $ledgerData['rcmApplicableYN'] = TaxService::isGRVRCMActivation($masterModel["autoID"]);

                            array_push($finalData, $ledgerData);
                        }

                        break;
                    case 24://Purchase Return
                        $details = PurchaseReturnDetails::selectRaw('SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrencyID,companyReportingCurrencyID as reportingCurrencyID,supplierTransactionCurrencyID,supplierTransactionER,companyReportingER,localCurrencyER')
                                                ->where('purhaseReturnAutoID', $masterModel["autoID"])
                                                ->whereNotNull('vatSubCategoryID')
                                                ->groupBy('vatSubCategoryID')
                                                ->get();

                        $master = PurchaseReturn::with(['finance_period_by', 'details' => function ($query) {
                            $query->selectRaw("SUM(noQty * GRVcostPerUnitLocalCur) as localAmount, SUM(noQty * GRVcostPerUnitComRptCur) as rptAmount,SUM(GRVcostPerUnitSupTransCur*noQty) as transAmount,purhaseReturnAutoID, SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount, supplierTransactionCurrencyID, supplierTransactionER, localCurrencyID, localCurrencyER, companyReportingCurrencyID, companyReportingER");
                        }])->find($masterModel["autoID"]);

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if (isset($master->finance_period_by->isActive) && $master->finance_period_by->isActive == -1) {
                            $masterDocumentDate = $master->purchaseReturnDate;
                        }

                        $valEligible = TaxService::checkGRVVATEligible($master->companySystemID, $master->supplierID);

                        $ledgerData['documentCode'] = $master->purchaseReturnCode;
                        $ledgerData['documentDate'] = $masterDocumentDate;
                        $ledgerData['partyID'] = $master->supplierID;
                        $ledgerData['documentFinalApprovedByEmpSystemID'] = $master->approvedByUserSystemID;

                        $ledgerData['documentTransAmount'] = \Helper::roundValue((($valEligible) ? $master->details[0]->transAmount + $master->details[0]->transVATAmount : $master->details[0]->transAmount));
                        $ledgerData['documentLocalAmount'] = \Helper::roundValue((($valEligible) ? $master->details[0]->localAmount + $master->details[0]->localVATAmount : $master->details[0]->localAmount));
                        $ledgerData['documentReportingAmount'] = \Helper::roundValue((($valEligible) ? $master->details[0]->rptAmount + $master->details[0]->rptVATAmount : $master->details[0]->rptAmount));

                        foreach ($details as $key => $value) {
                            $subCategoryData = TaxVatCategories::with(['tax'])->find($value->vatSubCategoryID);

                            if ($subCategoryData) {
                                $ledgerData['taxAuthorityAutoID'] = isset($subCategoryData->tax->authorityAutoID) ? $subCategoryData->tax->authorityAutoID : null;
                            }

                            $ledgerData['subCategoryID'] = $value->vatSubCategoryID;
                            $ledgerData['masterCategoryID'] = $value->vatMasterCategoryID;
                            $ledgerData['localAmount'] = $value->localVATAmount;
                            $ledgerData['rptAmount'] = $value->rptVATAmount;
                            $ledgerData['transAmount'] = $value->transVATAmount;
                            $ledgerData['transER'] = $value->supplierTransactionER;
                            $ledgerData['localER'] = $value->localCurrencyER;
                            $ledgerData['comRptER'] = $value->companyReportingER;
                            $ledgerData['localCurrencyID'] = $value->localCurrencyID;
                            $ledgerData['rptCurrencyID'] = $value->reportingCurrencyID;
                            $ledgerData['transCurrencyID'] = $value->supplierTransactionCurrencyID;

                            array_push($finalData, $ledgerData);
                        }

                        break;
                    case 20://Sales Invoice
                        $masterData = CustomerInvoiceDirect::with(['finance_period_by'])->find($masterModel["autoID"]);

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if (isset($masterData->finance_period_by->isActive) && $masterData->finance_period_by->isActive == -1) {
                            $masterDocumentDate = $masterData->bookingDate;
                        }

                        $ledgerData['documentCode'] = $masterData->bookingInvCode;
                        $ledgerData['documentDate'] = $masterDocumentDate;
                        $ledgerData['partyID'] = $masterData->customerID;
                        $ledgerData['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;

                        $ledgerData['documentTransAmount'] = floatval($masterData->bookingAmountTrans) + floatval($masterData->VATAmount);
                        $ledgerData['documentLocalAmount'] = floatval($masterData->bookingAmountLocal) + floatval($masterData->VATAmountLocal);
                        $ledgerData['documentReportingAmount'] = floatval($masterData->bookingAmountRpt) + floatval($masterData->VATAmountRpt);
                        if ($masterData->isPerforma == 2 || $masterData->isPerforma == 4 || $masterData->isPerforma == 5 || $masterData->isPerforma == 3) {

                            $details = CustomerInvoiceItemDetails::selectRaw('SUM(VATAmount*qtyIssuedDefaultMeasure) as transVATAmount,SUM(VATAmountLocal*qtyIssuedDefaultMeasure) as localVATAmount ,SUM(VATAmountRpt*qtyIssuedDefaultMeasure) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrencyID, localCurrencyER, reportingCurrencyID, reportingCurrencyER, sellingCurrencyID, sellingCurrencyER')
                                                    ->where('custInvoiceDirectAutoID', $masterModel["autoID"])
                                                    ->whereNotNull('vatSubCategoryID')
                                                    ->groupBy('vatSubCategoryID')
                                                    ->get();

                            foreach ($details as $key => $value) {
                                $subCategoryData = TaxVatCategories::with(['tax'])->find($value->vatSubCategoryID);

                                if ($subCategoryData) {
                                    $ledgerData['taxAuthorityAutoID'] = isset($subCategoryData->tax->authorityAutoID) ? $subCategoryData->tax->authorityAutoID : null;
                                }

                                $ledgerData['subCategoryID'] = $value->vatSubCategoryID;
                                $ledgerData['masterCategoryID'] = $value->vatMasterCategoryID;
                                $ledgerData['localAmount'] = $value->localVATAmount;
                                $ledgerData['rptAmount'] = $value->rptVATAmount;
                                $ledgerData['transAmount'] = $value->transVATAmount;
                                $ledgerData['transER'] = $value->sellingCurrencyER;
                                $ledgerData['localER'] = $value->localCurrencyER;
                                $ledgerData['comRptER'] = $value->reportingCurrencyER;
                                $ledgerData['localCurrencyID'] = $value->localCurrencyID;
                                $ledgerData['rptCurrencyID'] = $value->reportingCurrencyID;
                                $ledgerData['transCurrencyID'] = $value->sellingCurrencyID;

                                array_push($finalData, $ledgerData);
                            }
                        } else if ($masterData->isPerforma == 0) {
                            $details = CustomerInvoiceDirectDetail::selectRaw('SUM(VATAmount*invoiceQty) as transVATAmount,SUM(VATAmountLocal*invoiceQty) as localVATAmount ,SUM(VATAmountRpt*invoiceQty) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrency, localCurrencyER, comRptCurrency, comRptCurrencyER, invoiceAmountCurrency, invoiceAmountCurrencyER')
                                                    ->where('custInvoiceDirectID', $masterModel["autoID"])
                                                    ->whereNotNull('vatSubCategoryID')
                                                    ->groupBy('vatSubCategoryID')
                                                    ->get();

                            foreach ($details as $key => $value) {
                                $subCategoryData = TaxVatCategories::with(['tax'])->find($value->vatSubCategoryID);

                                if ($subCategoryData) {
                                    $ledgerData['taxAuthorityAutoID'] = isset($subCategoryData->tax->authorityAutoID) ? $subCategoryData->tax->authorityAutoID : null;
                                }

                                $ledgerData['subCategoryID'] = $value->vatSubCategoryID;
                                $ledgerData['masterCategoryID'] = $value->vatMasterCategoryID;
                                $ledgerData['localAmount'] = $value->localVATAmount;
                                $ledgerData['rptAmount'] = $value->rptVATAmount;
                                $ledgerData['transAmount'] = $value->transVATAmount;
                                $ledgerData['transER'] = $value->invoiceAmountCurrencyER;
                                $ledgerData['localER'] = $value->localCurrencyER;
                                $ledgerData['comRptER'] = $value->comRptCurrencyER;
                                $ledgerData['localCurrencyID'] = $value->localCurrency;
                                $ledgerData['rptCurrencyID'] = $value->comRptCurrency;
                                $ledgerData['transCurrencyID'] = $value->invoiceAmountCurrency;

                                array_push($finalData, $ledgerData);
                            }
                        }
                        break;
                    case 71://Delivery Order
                        $masterData = DeliveryOrder::with(['finance_period_by'])->find($masterModel["autoID"]);

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if (isset($masterData->finance_period_by->isActive) && $masterData->finance_period_by->isActive == -1) {
                            $masterDocumentDate = $masterData->deliveryOrderDate;
                        }

                        $ledgerData['documentCode'] = $masterData->deliveryOrderCode;
                        $ledgerData['documentDate'] = $masterDocumentDate;
                        $ledgerData['partyID'] = $masterData->customerID;
                        $ledgerData['documentFinalApprovedByEmpSystemID'] = $masterData->approvedEmpSystemID;

                        $ledgerData['documentTransAmount'] = floatval($masterData->transactionAmount) + floatval($masterData->VATAmount);
                        $ledgerData['documentLocalAmount'] = floatval($masterData->companyLocalAmount) + floatval($masterData->VATAmountLocal);
                        $ledgerData['documentReportingAmount'] = floatval($masterData->companyReportingAmount) + floatval($masterData->VATAmountRpt);

                        $details = DeliveryOrderDetail::selectRaw('SUM(VATAmount*qtyIssuedDefaultMeasure) as transVATAmount,SUM(VATAmountLocal*qtyIssuedDefaultMeasure) as localVATAmount ,SUM(VATAmountRpt*qtyIssuedDefaultMeasure) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, companyLocalCurrencyID,companyLocalCurrencyER,companyReportingCurrencyER,companyReportingCurrencyID, transactionCurrencyID, transactionCurrencyER')
                                                ->where('deliveryOrderID', $masterModel["autoID"])
                                                ->whereNotNull('vatSubCategoryID')
                                                ->groupBy('vatSubCategoryID')
                                                ->get();

                        foreach ($details as $key => $value) {
                            $subCategoryData = TaxVatCategories::with(['tax'])->find($value->vatSubCategoryID);

                            if ($subCategoryData) {
                                $ledgerData['taxAuthorityAutoID'] = isset($subCategoryData->tax->authorityAutoID) ? $subCategoryData->tax->authorityAutoID : null;
                            }

                            $ledgerData['subCategoryID'] = $value->vatSubCategoryID;
                            $ledgerData['masterCategoryID'] = $value->vatMasterCategoryID;
                            $ledgerData['localAmount'] = $value->localVATAmount;
                            $ledgerData['rptAmount'] = $value->rptVATAmount;
                            $ledgerData['transAmount'] = $value->transVATAmount;
                            $ledgerData['transER'] = $value->transactionCurrencyER;
                            $ledgerData['localER'] = $value->companyLocalCurrencyER;
                            $ledgerData['comRptER'] = $value->companyReportingCurrencyER;
                            $ledgerData['localCurrencyID'] = $value->companyLocalCurrencyID;
                            $ledgerData['rptCurrencyID'] = $value->companyReportingCurrencyID;
                            $ledgerData['transCurrencyID'] = $value->transactionCurrencyID;

                            array_push($finalData, $ledgerData);
                        }

                        break;
                    case 87://SalesReturn
                        $masterData = SalesReturn::with(['finance_period_by', 'detail' => function ($query) {
                            $query->selectRaw('SUM(companyLocalAmount) as localAmount, SUM(companyReportingAmount) as rptAmount,SUM(transactionAmount) as transAmount,salesReturnID');
                        }])->find($masterModel["autoID"]);

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if (isset($masterData->finance_period_by->isActive) && $masterData->finance_period_by->isActive == -1) {
                            $masterDocumentDate = $masterData->salesReturnDate;
                        }

                        $ledgerData['documentCode'] = $masterData->salesReturnCode;
                        $ledgerData['documentDate'] = $masterDocumentDate;
                        $ledgerData['partyID'] = $masterData->customerID;
                        $ledgerData['documentFinalApprovedByEmpSystemID'] = $masterData->approvedEmpSystemID;

                        $currencyConversionAmount = \Helper::currencyConversion($masterData->companySystemID, $masterData->transactionCurrencyID, $masterData->transactionCurrencyID, $masterData->transactionAmount);

                        $ledgerData['documentTransAmount'] = \Helper::roundValue($masterData->transactionAmount) + ((!is_null($masterData->VATAmount)) ? $masterData->VATAmount : 0);
                        $ledgerData['documentLocalAmount'] = \Helper::roundValue($currencyConversionAmount['localAmount']) + ((!is_null($masterData->VATAmountLocal)) ? $masterData->VATAmountLocal : 0);
                        $ledgerData['documentReportingAmount'] = \Helper::roundValue($currencyConversionAmount['reportingAmount']) + ((!is_null($masterData->VATAmountRpt)) ? $masterData->VATAmountRpt : 0);
                            

                        $details = SalesReturnDetail::selectRaw('SUM(VATAmount*qtyReturned) as transVATAmount,SUM(VATAmountLocal*qtyReturned) as localVATAmount ,SUM(VATAmountRpt*qtyReturned) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, companyLocalCurrencyID as localCurrencyID,companyReportingCurrencyID as reportingCurrencyID,transactionCurrencyID as transCurrencyID,companyReportingCurrencyER as reportingCurrencyER,companyLocalCurrencyER as localCurrencyER,transactionCurrencyER as transCurrencyER')
                                                ->where('salesReturnID', $masterModel["autoID"])
                                                ->whereNotNull('vatSubCategoryID')
                                                ->groupBy('vatSubCategoryID')
                                                ->get();

                        foreach ($details as $key => $value) {
                            $subCategoryData = TaxVatCategories::with(['tax'])->find($value->vatSubCategoryID);

                            if ($subCategoryData) {
                                $ledgerData['taxAuthorityAutoID'] = isset($subCategoryData->tax->authorityAutoID) ? $subCategoryData->tax->authorityAutoID : null;
                            }

                            $ledgerData['subCategoryID'] = $value->vatSubCategoryID;
                            $ledgerData['masterCategoryID'] = $value->vatMasterCategoryID;
                            $ledgerData['localAmount'] = $value->localVATAmount;
                            $ledgerData['rptAmount'] = $value->rptVATAmount;
                            $ledgerData['transAmount'] = $value->transVATAmount;
                            $ledgerData['transER'] = $value->transCurrencyER;
                            $ledgerData['localER'] = $value->localCurrencyER;
                            $ledgerData['comRptER'] = $value->reportingCurrencyER;
                            $ledgerData['localCurrencyID'] = $value->localCurrencyID;
                            $ledgerData['rptCurrencyID'] = $value->reportingCurrencyID;
                            $ledgerData['transCurrencyID'] = $value->transCurrencyID;

                            array_push($finalData, $ledgerData);
                        }

                        break;
                    case 15://Debit Note
                        $masterData = DebitNote::with(['finance_period_by', 'detail' => function ($query) {
                            $query->selectRaw('SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(debitAmount) as transAmount,debitNoteAutoID');
                        }])->find($masterModel["autoID"]);

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if (isset($masterData->finance_period_by->isActive) && $masterData->finance_period_by->isActive == -1) {
                            $masterDocumentDate = $masterData->debitNoteDate;
                        }

                        $ledgerData['documentCode'] = $masterData->debitNoteCode;
                        $ledgerData['documentDate'] = $masterDocumentDate;
                        $ledgerData['partyID'] = $masterData->supplierID;
                        $ledgerData['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;

                        $currencyConversionAmount = \Helper::currencyConversion($masterData->companySystemID, $masterData->supplierTransactionCurrencyID, $masterData->supplierTransactionCurrencyID, $masterData->debitAmountTrans);

                        $ledgerData['documentTransAmount'] = \Helper::roundValue($masterData->debitAmountTrans);
                        $ledgerData['documentLocalAmount'] = \Helper::roundValue($currencyConversionAmount['localAmount']);
                        $ledgerData['documentReportingAmount'] = \Helper::roundValue($currencyConversionAmount['reportingAmount']);
                            

                        $details = DebitNoteDetails::selectRaw('SUM(VATAmount) as transVATAmount,SUM(VATAmountLocal) as localVATAmount ,SUM(VATAmountRpt) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,debitAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER as localCurrencyER,debitAmountCurrencyER as transCurrencyER')
                                                ->where('debitNoteAutoID', $masterModel["autoID"])
                                                ->whereNotNull('vatSubCategoryID')
                                                ->groupBy('vatSubCategoryID')
                                                ->get();

                        foreach ($details as $key => $value) {
                            $subCategoryData = TaxVatCategories::with(['tax'])->find($value->vatSubCategoryID);

                            if ($subCategoryData) {
                                $ledgerData['taxAuthorityAutoID'] = isset($subCategoryData->tax->authorityAutoID) ? $subCategoryData->tax->authorityAutoID : null;
                            }

                            $ledgerData['subCategoryID'] = $value->vatSubCategoryID;
                            $ledgerData['masterCategoryID'] = $value->vatMasterCategoryID;
                            $ledgerData['localAmount'] = $value->localVATAmount;
                            $ledgerData['rptAmount'] = $value->rptVATAmount;
                            $ledgerData['transAmount'] = $value->transVATAmount;
                            $ledgerData['transER'] = $value->transCurrencyER;
                            $ledgerData['localER'] = $value->localCurrencyER;
                            $ledgerData['comRptER'] = $value->reportingCurrencyER;
                            $ledgerData['localCurrencyID'] = $value->localCurrencyID;
                            $ledgerData['rptCurrencyID'] = $value->reportingCurrencyID;
                            $ledgerData['transCurrencyID'] = $value->transCurrencyID;

                            array_push($finalData, $ledgerData);
                        }

                        break;
                    case 19://Credit Note
                        $masterData = CreditNote::with(['finance_period_by', 'details' => function ($query) {
                            $query->selectRaw('SUM(netAmountLocal) as localAmount, SUM(netAmountRpt) as rptAmount,SUM(netAmount) as transAmount,creditNoteAutoID');
                        }])->find($masterModel["autoID"]);

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if (isset($masterData->finance_period_by->isActive) && $masterData->finance_period_by->isActive == -1) {
                            $masterDocumentDate = $masterData->creditNoteDate;
                        }

                        $ledgerData['documentCode'] = $masterData->creditNoteCode;
                        $ledgerData['documentDate'] = $masterDocumentDate;
                        $ledgerData['partyID'] = $masterData->customerID;
                        $ledgerData['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;

                        $currencyConversionAmount = \Helper::currencyConversion($masterData->companySystemID, $masterData->customerCurrencyID, $masterData->customerCurrencyID, $masterData->creditAmountTrans);

                        $ledgerData['documentTransAmount'] = \Helper::roundValue($masterData->creditAmountTrans);
                        $ledgerData['documentLocalAmount'] = \Helper::roundValue($currencyConversionAmount['localAmount']);
                        $ledgerData['documentReportingAmount'] = \Helper::roundValue($currencyConversionAmount['reportingAmount']);
                            

                        $details = CreditNoteDetails::selectRaw('SUM(VATAmount) as transVATAmount,SUM(VATAmountLocal) as localVATAmount ,SUM(VATAmountRpt) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,creditAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER as localCurrencyER,creditAmountCurrencyER as transCurrencyER')
                                                ->where('creditNoteAutoID', $masterModel["autoID"])
                                                ->whereNotNull('vatSubCategoryID')
                                                ->groupBy('vatSubCategoryID')
                                                ->get();

                        foreach ($details as $key => $value) {
                            $subCategoryData = TaxVatCategories::with(['tax'])->find($value->vatSubCategoryID);

                            if ($subCategoryData) {
                                $ledgerData['taxAuthorityAutoID'] = isset($subCategoryData->tax->authorityAutoID) ? $subCategoryData->tax->authorityAutoID : null;
                            }

                            $ledgerData['subCategoryID'] = $value->vatSubCategoryID;
                            $ledgerData['masterCategoryID'] = $value->vatMasterCategoryID;
                            $ledgerData['localAmount'] = $value->localVATAmount;
                            $ledgerData['rptAmount'] = $value->rptVATAmount;
                            $ledgerData['transAmount'] = $value->transVATAmount;
                            $ledgerData['transER'] = $value->transCurrencyER;
                            $ledgerData['localER'] = $value->localCurrencyER;
                            $ledgerData['comRptER'] = $value->reportingCurrencyER;
                            $ledgerData['localCurrencyID'] = $value->localCurrencyID;
                            $ledgerData['rptCurrencyID'] = $value->reportingCurrencyID;
                            $ledgerData['transCurrencyID'] = $value->transCurrencyID;

                            array_push($finalData, $ledgerData);
                        }

                        break;
                    case 11://Supplier Invoice
                        $masterData = BookInvSuppMaster::with(['financeperiod_by', 'directdetail' => function ($query) {
                            $query->selectRaw('SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DIAmount) as transAmount,directInvoiceAutoID');
                        }])->find($masterModel["autoID"]);

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if (isset($masterData->financeperiod_by->isActive) && $masterData->financeperiod_by->isActive == -1) {
                            $masterDocumentDate = $masterData->bookingDate;
                        }

                        $ledgerData['documentCode'] = $masterData->bookingInvCode;
                        $ledgerData['documentDate'] = $masterDocumentDate;
                        $ledgerData['partyID'] = $masterData->supplierID;
                        $ledgerData['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;

                        $currencyConversionAmount = \Helper::currencyConversion($masterData->companySystemID, $masterData->supplierTransactionCurrencyID, $masterData->supplierTransactionCurrencyID, $masterData->directdetail[0]->transAmount);

                        $ledgerData['documentTransAmount'] = \Helper::roundValue($masterData->directdetail[0]->transAmount);
                        $ledgerData['documentLocalAmount'] = \Helper::roundValue($currencyConversionAmount['localAmount']);
                        $ledgerData['documentReportingAmount'] = \Helper::roundValue($currencyConversionAmount['reportingAmount']);
                            

                        $details = DirectInvoiceDetails::selectRaw('SUM(VATAmount) as transVATAmount,SUM(VATAmountLocal) as localVATAmount ,SUM(VATAmountRpt) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,DIAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER as localCurrencyER,DIAmountCurrencyER as transCurrencyER')
                                                ->where('directInvoiceAutoID', $masterModel["autoID"])
                                                ->whereNotNull('vatSubCategoryID')
                                                ->groupBy('vatSubCategoryID')
                                                ->get();

                        if ($masterData->documentType == 1) {
                            foreach ($details as $key => $value) {
                                $subCategoryData = TaxVatCategories::with(['tax'])->find($value->vatSubCategoryID);

                                if ($subCategoryData) {
                                    $ledgerData['taxAuthorityAutoID'] = isset($subCategoryData->tax->authorityAutoID) ? $subCategoryData->tax->authorityAutoID : null;
                                }

                                $ledgerData['subCategoryID'] = $value->vatSubCategoryID;
                                $ledgerData['masterCategoryID'] = $value->vatMasterCategoryID;
                                $ledgerData['localAmount'] = $value->localVATAmount;
                                $ledgerData['rptAmount'] = $value->rptVATAmount;
                                $ledgerData['transAmount'] = $value->transVATAmount;
                                $ledgerData['transER'] = $value->transCurrencyER;
                                $ledgerData['localER'] = $value->localCurrencyER;
                                $ledgerData['comRptER'] = $value->reportingCurrencyER;
                                $ledgerData['localCurrencyID'] = $value->localCurrencyID;
                                $ledgerData['rptCurrencyID'] = $value->reportingCurrencyID;
                                $ledgerData['transCurrencyID'] = $value->transCurrencyID;

                                array_push($finalData, $ledgerData);
                            }
                        }

                        break;
                    default:
                        # code...
                        break;
                }

                if ($finalData) {
                    Log::info($finalData);
                    foreach ($finalData as $data)
                    {
                        TaxLedger::create($data);
                    }
                    Log::info('Successfully inserted to Tax ledger table ' . date('H:i:s'));
                    DB::commit();
                }
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($this->failed($e));
                Log::info('Error Line No: ' . $e->getLine());
                Log::info($e->getMessage());
                Log::info('---- Tax Ledger  End with Error-----' . date('H:i:s'));
            }
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}
