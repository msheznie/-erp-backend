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
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetails;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderDetail;
use App\Models\TaxLedger;
use App\Models\TaxVatCategories;
use App\helper\TaxService;
use App\Models\Employee;
use App\Models\SalesReturn;
use App\Models\SalesReturnDetail;

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

                        $master = PurchaseReturn::with(['finance_period_by'])->find($masterModel["autoID"]);

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if (isset($master->finance_period_by->isActive) && $master->finance_period_by->isActive == -1) {
                            $masterDocumentDate = $master->purchaseReturnDate;
                        }

                        $ledgerData['documentCode'] = $master->purchaseReturnCode;
                        $ledgerData['documentDate'] = $masterDocumentDate;

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

                        if ($masterData->isPerforma == 2 || $masterData->isPerforma == 4 || $masterData->isPerforma == 5 || $masterData->isPerforma == 3) {
                            $masterDocumentDate = date('Y-m-d H:i:s');
                            if (isset($masterData->finance_period_by->isActive) && $masterData->finance_period_by->isActive == -1) {
                                $masterDocumentDate = $masterData->bookingDate;
                            }

                            $ledgerData['documentCode'] = $masterData->bookingInvCode;
                            $ledgerData['documentDate'] = $masterDocumentDate;

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
                        $masterData = SalesReturn::with(['finance_period_by'])->find($masterModel["autoID"]);

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if (isset($masterData->finance_period_by->isActive) && $masterData->finance_period_by->isActive == -1) {
                            $masterDocumentDate = $masterData->salesReturnDate;
                        }

                        $ledgerData['documentCode'] = $masterData->salesReturnCode;
                        $ledgerData['documentDate'] = $masterDocumentDate;

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
