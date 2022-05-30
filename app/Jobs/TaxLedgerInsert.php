<?php

namespace App\Jobs;

use App\Models\DirectPaymentDetails;
use App\Models\PaySupplierInvoiceMaster;
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
use App\Models\PoAdvancePayment;
use App\Models\GRVMaster;
use App\Models\GRVDetails;
use App\Models\CreditNote;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnLogistic;
use App\Models\PurchaseReturnDetails;
use App\Models\SupplierInvoiceItemDetail;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\DeliveryOrder;
use App\Models\CreditNoteDetails;
use App\Models\DeliveryOrderDetail;
use App\Models\TaxLedger;
use App\Models\DebitNote;
use App\Models\TaxLedgerDetail;
use App\Models\DebitNoteDetails;
use App\Models\TaxVatCategories;
use App\helper\TaxService;
use App\Models\Employee;
use App\Models\SalesReturn;
use App\Models\ChartOfAccount;
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
                $finalDetailData = [];
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

                $ledgerDetailsData = $ledgerData;
                $ledgerDetailsData['createdUserSystemID'] = $empID->employeeSystemID;

                switch ($masterModel["documentSystemID"]) {
                    case 3: //GRV
                        $details = GRVDetails::selectRaw('SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionER,companyReportingCurrencyID,companyReportingER,localCurrencyID,localCurrencyER')
                                                ->where('grvAutoID', $masterModel["autoID"])
                                                ->whereNotNull('vatSubCategoryID')
                                                ->groupBy('vatSubCategoryID')
                                                ->get();

                        $master = GRVMaster::with(['financeperiod_by', 'supplier_by'])->find($masterModel["autoID"]);

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

                        $detailData = GRVDetails::where('grvAutoID', $masterModel["autoID"])
                                                ->whereNotNull('vatSubCategoryID')
                                                ->get();

                        $ledgerDetailsData['rcmApplicableYN'] = TaxService::isGRVRCMActivation($masterModel["autoID"]);

                        foreach ($detailData as $key => $value) {
                            $ledgerDetailsData['documentDetailID'] = $value->grvDetailsID;
                            $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
                            $ledgerDetailsData['vatMasterCategoryID'] = $value->vatMasterCategoryID;
                            $ledgerDetailsData['serviceLineSystemID'] = $master->serviceLineSystemID;
                            $ledgerDetailsData['documentDate'] = $masterDocumentDate;
                            $ledgerDetailsData['postedDate'] = date('Y-m-d H:i:s');
                            $ledgerDetailsData['documentNumber'] = $master->grvPrimaryCode;
                            $ledgerDetailsData['chartOfAccountSystemID'] = $value->financeGLcodePLSystemID;

                            $chartOfAccountData = ChartOfAccount::find($value->financeGLcodePLSystemID);

                            if ($chartOfAccountData) {
                                $ledgerDetailsData['accountCode'] = $chartOfAccountData->AccountCode;
                                $ledgerDetailsData['accountDescription'] = $chartOfAccountData->AccountDescription;
                            }

                            $ledgerDetailsData['transactionCurrencyID'] = $value->supplierItemCurrencyID;
                            $ledgerDetailsData['originalInvoice'] = NULL;
                            $ledgerDetailsData['originalInvoiceDate'] = NULL;
                            $ledgerDetailsData['dateOfSupply'] = NULL;
                            $ledgerDetailsData['partyType'] = 1;
                            $ledgerDetailsData['partyAutoID'] = $master->supplierID;
                            $ledgerDetailsData['partyVATRegisteredYN'] = $value->supplierVATEligible;
                            $ledgerDetailsData['partyVATRegNo'] = isset($master->supplier_by->vatNumber) ? $master->supplier_by->vatNumber : "";
                            $ledgerDetailsData['countryID'] = isset($master->supplier_by->supplierCountryID) ? $master->supplier_by->supplierCountryID : "";
                            $ledgerDetailsData['itemSystemCode'] = $value->itemCode;
                            $ledgerDetailsData['itemCode'] = $value->itemPrimaryCode;
                            $ledgerDetailsData['itemDescription'] = $value->itemDescription;
                            $ledgerDetailsData['VATPercentage'] = $value->VATPercentage;
                            $ledgerDetailsData['VATAmount'] = $value->VATAmount * $value->noQty;
                            $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount * $value->noQty;
                            $ledgerDetailsData['localER'] = $value->localCurrencyER;
                            $ledgerDetailsData['reportingER'] = $value->companyReportingER;
                            $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal * $value->noQty;
                            $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt * $value->noQty;
                            
                            $subCategory = TaxVatCategories::find($value->vatSubCategoryID);
                            if($subCategory->subCatgeoryType != 2) {
                                if($value->exempt_vat_portion != 0) {
                                    $taxableAmountLocal = (($value->landingCost_LocalCur * $value->noQty) -  ($ledgerDetailsData['VATAmountLocal'] / 100) * $value->exempt_vat_portion);
                                    $taxableAmountReporting = (($value->landingCost_RptCur * $value->noQty) -  ($ledgerDetailsData['VATAmountRpt'] / 100) * $value->exempt_vat_portion);
                                    $taxableAmount =  ($value->landingCost_TransCur * $value->noQty) - (($ledgerDetailsData['VATAmountRpt'] / 100) * $value->exempt_vat_portion);

                                }else {
                                    $taxableAmountLocal =  ($value->landingCost_LocalCur * $value->noQty) -  $ledgerDetailsData['VATAmountLocal'];
                                    $taxableAmountReporting =  ($value->landingCost_RptCur * $value->noQty)  - $ledgerDetailsData['VATAmountRpt'];
                                    $taxableAmount =  ($value->landingCost_TransCur * $value->noQty)  - $ledgerDetailsData['VATAmount'];

                                }
                            }else {
                                $taxableAmountLocal =  $value->landingCost_LocalCur * $value->noQty;
                                $taxableAmountReporting =  $value->landingCost_RptCur * $value->noQty;
                                $taxableAmount =  $value->landingCost_TransCur * $value->noQty;

                            }    

                            $ledgerDetailsData['taxableAmount'] = $taxableAmount;
                            $ledgerDetailsData['taxableAmountLocal'] = $taxableAmountLocal;
                            $ledgerDetailsData['taxableAmountReporting'] = $taxableAmountReporting;
                            $ledgerDetailsData['localCurrencyID'] = $value->localCurrencyID;
                            $ledgerDetailsData['rptCurrencyID'] = $value->companyReportingCurrencyID;
                            $ledgerDetailsData['exempt_vat_portion'] = $value->exempt_vat_portion;

                            

                            array_push($finalDetailData, $ledgerDetailsData);
                        }

                        $logisticData = PoAdvancePayment::with(['category_by' => function($query) {
                                                            $query->with(['item_by']);
                                                        }, 'supplier_by'])->where('grvAutoID', $masterModel["autoID"])
                                                        ->whereNotNull('vatSubCategoryID')
                                                        ->get();

                        foreach ($logisticData as $key => $value) {
                            $ledgerDetailsData['documentDetailID'] = $value->poAdvPaymentID;
                            $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
                            $ledgerDetailsData['vatMasterCategoryID'] = TaxVatCategories::getMainCategory($value->vatSubCategoryID);
                            $ledgerDetailsData['serviceLineSystemID'] = $value->serviceLineSystemID;
                            $ledgerDetailsData['documentDate'] = $masterDocumentDate;
                            $ledgerDetailsData['postedDate'] = date('Y-m-d H:i:s');
                            $ledgerDetailsData['documentNumber'] = $master->grvPrimaryCode;
                            $ledgerDetailsData['chartOfAccountSystemID'] = $value->UnbilledGRVAccountSystemID;

                            $chartOfAccountData = ChartOfAccount::find($value->UnbilledGRVAccountSystemID);

                            if ($chartOfAccountData) {
                                $ledgerDetailsData['accountCode'] = $chartOfAccountData->AccountCode;
                                $ledgerDetailsData['accountDescription'] = $chartOfAccountData->AccountDescription;
                            }

                            $ledgerDetailsData['transactionCurrencyID'] = $value->currencyID;
                            $ledgerDetailsData['originalInvoice'] = NULL;
                            $ledgerDetailsData['originalInvoiceDate'] = NULL;
                            $ledgerDetailsData['dateOfSupply'] = NULL;
                            $ledgerDetailsData['partyType'] = 1;
                            $ledgerDetailsData['partyAutoID'] = $value->supplierID;
                            $ledgerDetailsData['partyVATRegisteredYN'] = isset($value->supplier_by->vatEligible) ? $value->supplier_by->vatEligible : 0;
                            $ledgerDetailsData['partyVATRegNo'] = isset($value->supplier_by->vatNumber) ? $value->supplier_by->vatNumber : "";
                            $ledgerDetailsData['countryID'] = isset($value->supplier_by->supplierCountryID) ? $value->supplier_by->supplierCountryID : "";
                            $ledgerDetailsData['itemCode'] = isset($value->category_by->item_by->primaryCode) ? $value->category_by->item_by->primaryCode : "";
                            $ledgerDetailsData['itemDescription'] = isset($value->category_by->item_by->itemDescription) ? $value->category_by->item_by->itemDescription : "";
                            $ledgerDetailsData['itemSystemCode'] = isset($value->category_by->itemSystemCode) ? $value->category_by->itemSystemCode : null;
                            $ledgerDetailsData['VATPercentage'] = $value->VATPercentage;
                            $ledgerDetailsData['taxableAmount'] = $value->reqAmountInPOTransCur;
                            $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount;
                            $ledgerDetailsData['VATAmount'] = $value->VATAmount;
                            $ledgerDetailsData['localER'] = $value->reqAmountInPOTransCur / $value->reqAmountInPOLocalCur;
                            $ledgerDetailsData['reportingER'] = $value->reqAmountInPOTransCur / $value->reqAmountInPORptCur;
                            $ledgerDetailsData['taxableAmountLocal'] = ($value->reqAmountInPOLocalCur);
                            $ledgerDetailsData['taxableAmountReporting'] = ($value->reqAmountInPORptCur);
                            $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal;
                            $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt;
                            $ledgerDetailsData['localCurrencyID'] = $master->localCurrencyID;
                            $ledgerDetailsData['rptCurrencyID'] = $master->companyReportingCurrencyID;
                            $ledgerDetailsData['logisticYN'] = 1;
                            $ledgerDetailsData['addVATonPO'] = ($value->addVatOnPO) ? 1 : 0;

                            array_push($finalDetailData, $ledgerDetailsData);
                        }

                        break;
                    case 24://Purchase Return
                        $details = PurchaseReturnDetails::selectRaw('SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrencyID,companyReportingCurrencyID as reportingCurrencyID,supplierTransactionCurrencyID,supplierTransactionER,companyReportingER,localCurrencyER')
                                                ->where('purhaseReturnAutoID', $masterModel["autoID"])
                                                ->whereNotNull('vatSubCategoryID')
                                                ->groupBy('vatSubCategoryID')
                                                ->get();

                        $master = PurchaseReturn::with(['finance_period_by', 'supplier_by', 'details' => function ($query) {
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


                        $detailData = PurchaseReturnDetails::where('purhaseReturnAutoID', $masterModel["autoID"])
                                                            ->whereNotNull('vatSubCategoryID')
                                                            ->get();

                        foreach ($detailData as $key => $value) {
                            $ledgerDetailsData['documentDetailID'] = $value->purhasereturnDetailID;
                            $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
                            $ledgerDetailsData['vatMasterCategoryID'] = $value->vatMasterCategoryID;
                            $ledgerDetailsData['serviceLineSystemID'] = $master->serviceLineSystemID;
                            $ledgerDetailsData['documentDate'] = $masterDocumentDate;
                            $ledgerDetailsData['postedDate'] = date('Y-m-d H:i:s');
                            $ledgerDetailsData['documentNumber'] = $master->purchaseReturnCode;
                            $ledgerDetailsData['chartOfAccountSystemID'] = $value->financeGLcodePLSystemID;

                            $chartOfAccountData = ChartOfAccount::find($value->financeGLcodePLSystemID);

                            if ($chartOfAccountData) {
                                $ledgerDetailsData['accountCode'] = $chartOfAccountData->AccountCode;
                                $ledgerDetailsData['accountDescription'] = $chartOfAccountData->AccountDescription;
                            }

                            $ledgerDetailsData['transactionCurrencyID'] = $value->supplierTransactionCurrencyID;
                            $ledgerDetailsData['originalInvoice'] = NULL;
                            $ledgerDetailsData['originalInvoiceDate'] = NULL;
                            $ledgerDetailsData['dateOfSupply'] = NULL;
                            $ledgerDetailsData['partyType'] = 1;
                            $ledgerDetailsData['partyAutoID'] = $master->supplierID;
                            $ledgerDetailsData['partyVATRegisteredYN'] = $value->supplierVATEligible;
                            $ledgerDetailsData['partyVATRegNo'] = isset($master->supplier_by->vatNumber) ? $master->supplier_by->vatNumber : "";
                            $ledgerDetailsData['countryID'] = isset($master->supplier_by->supplierCountryID) ? $master->supplier_by->supplierCountryID : "";
                            $ledgerDetailsData['itemSystemCode'] = $value->itemCode;
                            $ledgerDetailsData['itemCode'] = $value->itemPrimaryCode;
                            $ledgerDetailsData['itemDescription'] = $value->itemDescription;
                            $ledgerDetailsData['VATPercentage'] = $value->VATPercentage;
                            $ledgerDetailsData['taxableAmount'] = ($value->GRVcostPerUnitSupTransCur * $value->noQty);
                            $ledgerDetailsData['VATAmount'] = $value->VATAmount * $value->noQty;
                            $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount * $value->noQty;
                            $ledgerDetailsData['localER'] = $value->localCurrencyER;
                            $ledgerDetailsData['reportingER'] = $value->companyReportingER;
                            $ledgerDetailsData['taxableAmountLocal'] = ($value->GRVcostPerUnitLocalCur * $value->noQty);
                            $ledgerDetailsData['taxableAmountReporting'] = ($value->GRVcostPerUnitComRptCur * $value->noQty);
                            $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal * $value->noQty;
                            $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt * $value->noQty;
                            $ledgerDetailsData['localCurrencyID'] = $value->localCurrencyID;
                            $ledgerDetailsData['rptCurrencyID'] = $value->companyReportingCurrencyID;
                            $ledgerDetailsData['exempt_vat_portion'] = $value->exempt_vat_portion;
                            array_push($finalDetailData, $ledgerDetailsData);
                        }


                        $logisticData = PurchaseReturnLogistic::with(['logistic_data' => function($query) {
                                                                    $query->with(['category_by' => function($query) {
                                                                                $query->with(['item_by']);
                                                                            }, 'supplier_by']);
                                                            }])
                                                            ->where('purchaseReturnID', $masterModel["autoID"])
                                                            ->whereNotNull('vatSubCategoryID')
                                                            ->get();

                        foreach ($logisticData as $key => $value) {
                            $ledgerDetailsData['documentDetailID'] = $value->id;
                            $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
                            $ledgerDetailsData['vatMasterCategoryID'] = TaxVatCategories::getMainCategory($value->vatSubCategoryID);
                            $ledgerDetailsData['serviceLineSystemID'] = $value->serviceLineSystemID;
                            $ledgerDetailsData['documentDate'] = $masterDocumentDate;
                            $ledgerDetailsData['postedDate'] = date('Y-m-d H:i:s');
                            $ledgerDetailsData['documentNumber'] = $master->purchaseReturnCode;
                            $ledgerDetailsData['chartOfAccountSystemID'] = $value->UnbilledGRVAccountSystemID;

                            $chartOfAccountData = ChartOfAccount::find($value->UnbilledGRVAccountSystemID);

                            if ($chartOfAccountData) {
                                $ledgerDetailsData['accountCode'] = $chartOfAccountData->AccountCode;
                                $ledgerDetailsData['accountDescription'] = $chartOfAccountData->AccountDescription;
                            }

                            $ledgerDetailsData['transactionCurrencyID'] = $value->supplierTransactionCurrencyID;
                            $ledgerDetailsData['originalInvoice'] = NULL;
                            $ledgerDetailsData['originalInvoiceDate'] = NULL;
                            $ledgerDetailsData['dateOfSupply'] = NULL;
                            $ledgerDetailsData['partyType'] = 1;
                            $ledgerDetailsData['partyAutoID'] = $value->supplierID;
                            $ledgerDetailsData['partyVATRegisteredYN'] = isset($value->logistic_data->supplier_by->vatEligible) ? $value->logistic_data->supplier_by->vatEligible : 0;
                            $ledgerDetailsData['partyVATRegNo'] = isset($value->logistic_data->supplier_by->vatNumber) ? $value->logistic_data->supplier_by->vatNumber : "";
                            $ledgerDetailsData['countryID'] = isset($value->logistic_data->supplier_by->supplierCountryID) ? $value->logistic_data->supplier_by->supplierCountryID : "";
                            $ledgerDetailsData['itemCode'] = isset($value->logistic_data->category_by->item_by->primaryCode) ? $value->logistic_data->category_by->item_by->primaryCode : "";
                            $ledgerDetailsData['itemDescription'] = isset($value->logistic_data->category_by->item_by->itemDescription) ? $value->logistic_data->category_by->item_by->itemDescription : "";
                            $ledgerDetailsData['itemSystemCode'] = isset($value->logistic_data->category_by->itemSystemCode) ? $value->logistic_data->category_by->itemSystemCode : null;
                            $ledgerDetailsData['VATPercentage'] = isset($value->logistic_data->VATPercentage) ? $value->logistic_data->VATPercentage : 0;
                            $ledgerDetailsData['taxableAmount'] = $value->logisticAmountTrans;
                            $ledgerDetailsData['recoverabilityAmount'] = $value->logisticVATAmount;
                            $ledgerDetailsData['VATAmount'] = $value->logisticVATAmount;
                            $ledgerDetailsData['localER'] = $value->logisticAmountTrans / $value->logisticAmountLocal;
                            $ledgerDetailsData['reportingER'] = $value->logisticAmountTrans / $value->logisticAmountRpt;
                            $ledgerDetailsData['taxableAmountLocal'] = ($value->logisticAmountLocal);
                            $ledgerDetailsData['taxableAmountReporting'] = ($value->logisticAmountRpt);
                            $ledgerDetailsData['VATAmountLocal'] = $value->logisticVATAmountLocal;
                            $ledgerDetailsData['VATAmountRpt'] = $value->logisticVATAmountRpt;
                            $ledgerDetailsData['localCurrencyID'] = $master->localCurrencyID;
                            $ledgerDetailsData['rptCurrencyID'] = $master->companyReportingCurrencyID;
                            $ledgerDetailsData['logisticYN'] = 1;
                            $ledgerDetailsData['addVATonPO'] = (isset($value->logistic_data->addVatOnPO) ? $value->logistic_data->addVatOnPO : 0) ? 1 : 0;

                            array_push($finalDetailData, $ledgerDetailsData);
                        }

                        break;
                    case 20://Sales Invoice
                        $masterData = CustomerInvoiceDirect::with(['finance_period_by', 'customer'])->find($masterModel["autoID"]);

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


                            $detailData = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $masterModel["autoID"])
                                                            ->whereNotNull('vatSubCategoryID')
                                                            ->get();

                            foreach ($detailData as $key => $value) {
                                $ledgerDetailsData['documentDetailID'] = $value->customerItemDetailID;
                                $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
                                $ledgerDetailsData['vatMasterCategoryID'] = $value->vatMasterCategoryID;
                                $ledgerDetailsData['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                                $ledgerDetailsData['documentDate'] = $masterDocumentDate;
                                $ledgerDetailsData['postedDate'] = date('Y-m-d H:i:s');
                                $ledgerDetailsData['documentNumber'] = $masterData->bookingInvCode;
                                $ledgerDetailsData['chartOfAccountSystemID'] = $value->financeGLcodeRevenueSystemID;

                                $chartOfAccountData = ChartOfAccount::find($value->financeGLcodeRevenueSystemID);

                                if ($chartOfAccountData) {
                                    $ledgerDetailsData['accountCode'] = $chartOfAccountData->AccountCode;
                                    $ledgerDetailsData['accountDescription'] = $chartOfAccountData->AccountDescription;
                                }

                                $ledgerDetailsData['transactionCurrencyID'] = $value->sellingCurrencyID;
                                $ledgerDetailsData['originalInvoice'] = $masterData->customerInvoiceNo;
                                $ledgerDetailsData['originalInvoiceDate'] = $masterData->customerInvoiceDate;
                                $ledgerDetailsData['dateOfSupply'] = $masterData->serviceStartDate;
                                $ledgerDetailsData['partyType'] = 2;
                                $ledgerDetailsData['partyAutoID'] = $masterData->customerID;
                                $ledgerDetailsData['partyVATRegisteredYN'] = $masterData->customerVATEligible;
                                $ledgerDetailsData['partyVATRegNo'] = isset($masterData->customer->vatNumber) ? $masterData->customer->vatNumber : "";
                                $ledgerDetailsData['countryID'] = isset($masterData->customer->customerCountry) ? $masterData->customer->customerCountry : "";
                                $ledgerDetailsData['itemSystemCode'] = $value->itemCodeSystem;
                                $ledgerDetailsData['itemCode'] = $value->itemPrimaryCode;
                                $ledgerDetailsData['itemDescription'] = $value->itemDescription;
                                $ledgerDetailsData['VATPercentage'] = $value->VATPercentage;
                                $ledgerDetailsData['taxableAmount'] = ($value->sellingCostAfterMargin * $value->qtyIssuedDefaultMeasure);
                                $ledgerDetailsData['VATAmount'] = $value->VATAmount * $value->qtyIssuedDefaultMeasure;
                                $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount * $value->qtyIssuedDefaultMeasure;
                                $ledgerDetailsData['localER'] = $value->localCurrencyER;
                                $ledgerDetailsData['reportingER'] = $value->reportingCurrencyER;
                                $ledgerDetailsData['taxableAmountLocal'] = ($value->sellingCostAfterMarginLocal * $value->qtyIssuedDefaultMeasure);
                                $ledgerDetailsData['taxableAmountReporting'] = ($value->sellingCostAfterMarginRpt * $value->qtyIssuedDefaultMeasure);
                                $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal * $value->qtyIssuedDefaultMeasure;
                                $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt * $value->qtyIssuedDefaultMeasure;
                                $ledgerDetailsData['localCurrencyID'] = $value->localCurrencyID;
                                $ledgerDetailsData['rptCurrencyID'] = $value->reportingCurrencyID;

                                array_push($finalDetailData, $ledgerDetailsData);
                            }
                        } else if ($masterData->isPerforma == 0 || $masterData->isPerforma == 1) {
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


                            $detailData = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $masterModel["autoID"])
                                                            ->whereNotNull('vatSubCategoryID')
                                                            ->get();

                            foreach ($detailData as $key => $value) {
                                $ledgerDetailsData['documentDetailID'] = $value->custInvDirDetAutoID;
                                $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
                                $ledgerDetailsData['vatMasterCategoryID'] = $value->vatMasterCategoryID;
                                $ledgerDetailsData['serviceLineSystemID'] = $value->serviceLineSystemID;
                                $ledgerDetailsData['documentDate'] = $masterDocumentDate;
                                $ledgerDetailsData['postedDate'] = date('Y-m-d H:i:s');
                                $ledgerDetailsData['documentNumber'] = $masterData->bookingInvCode;
                                $ledgerDetailsData['chartOfAccountSystemID'] = $value->glSystemID;

                                $chartOfAccountData = ChartOfAccount::find($value->glSystemID);

                                if ($chartOfAccountData) {
                                    $ledgerDetailsData['accountCode'] = $chartOfAccountData->AccountCode;
                                    $ledgerDetailsData['accountDescription'] = $chartOfAccountData->AccountDescription;
                                }

                                $ledgerDetailsData['transactionCurrencyID'] = $value->invoiceAmountCurrency;
                                $ledgerDetailsData['originalInvoice'] = $masterData->customerInvoiceNo;
                                $ledgerDetailsData['originalInvoiceDate'] = $masterData->customerInvoiceDate;
                                $ledgerDetailsData['dateOfSupply'] = $masterData->serviceStartDate;
                                $ledgerDetailsData['partyType'] = 2;
                                $ledgerDetailsData['partyAutoID'] = $masterData->customerID;
                                $ledgerDetailsData['partyVATRegisteredYN'] = $masterData->customerVATEligible;
                                $ledgerDetailsData['partyVATRegNo'] = isset($masterData->customer->vatNumber) ? $masterData->customer->vatNumber : "";
                                $ledgerDetailsData['countryID'] = isset($masterData->customer->customerCountry) ? $masterData->customer->customerCountry : "";
                                $ledgerDetailsData['itemSystemCode'] = null;
                                $ledgerDetailsData['itemCode'] = null;
                                $ledgerDetailsData['itemDescription'] = null;
                                $ledgerDetailsData['VATPercentage'] = $value->VATPercentage;
                                $ledgerDetailsData['taxableAmount'] = $value->invoiceAmount;
                                $ledgerDetailsData['VATAmount'] = $value->VATAmount * $value->invoiceQty;
                                $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount * $value->invoiceQty;
                                $ledgerDetailsData['localER'] = $value->localCurrencyER;
                                $ledgerDetailsData['reportingER'] = $value->comRptCurrencyER;
                                $ledgerDetailsData['taxableAmountLocal'] = $value->localAmount;
                                $ledgerDetailsData['taxableAmountReporting'] = $value->comRptAmount;
                                $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal * $value->invoiceQty;
                                $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt * $value->invoiceQty;
                                $ledgerDetailsData['localCurrencyID'] = $value->localCurrency;
                                $ledgerDetailsData['rptCurrencyID'] = $value->comRptCurrency;

                                array_push($finalDetailData, $ledgerDetailsData);
                            }
                        }
                        break;
                    case 71://Delivery Order
                        $masterData = DeliveryOrder::with(['finance_period_by', 'customer'])->find($masterModel["autoID"]);

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

                        $detailData = DeliveryOrderDetail::where('deliveryOrderID', $masterModel["autoID"])
                                                            ->whereNotNull('vatSubCategoryID')
                                                            ->get();

                        foreach ($detailData as $key => $value) {
                            $ledgerDetailsData['documentDetailID'] = $value->deliveryOrderDetailID;
                            $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
                            $ledgerDetailsData['vatMasterCategoryID'] = $value->vatMasterCategoryID;
                            $ledgerDetailsData['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                            $ledgerDetailsData['documentDate'] = $masterDocumentDate;
                            $ledgerDetailsData['postedDate'] = date('Y-m-d H:i:s');
                            $ledgerDetailsData['documentNumber'] = $masterData->deliveryOrderCode;
                            $ledgerDetailsData['chartOfAccountSystemID'] = $value->financeGLcodeRevenueSystemID;

                            $chartOfAccountData = ChartOfAccount::find($value->financeGLcodeRevenueSystemID);

                            if ($chartOfAccountData) {
                                $ledgerDetailsData['accountCode'] = $chartOfAccountData->AccountCode;
                                $ledgerDetailsData['accountDescription'] = $chartOfAccountData->AccountDescription;
                            }

                            $ledgerDetailsData['transactionCurrencyID'] = $value->transactionCurrencyID;
                            $ledgerDetailsData['originalInvoice'] = null;
                            $ledgerDetailsData['originalInvoiceDate'] = null;
                            $ledgerDetailsData['dateOfSupply'] = null;
                            $ledgerDetailsData['partyType'] = 2;
                            $ledgerDetailsData['partyAutoID'] = $masterData->customerID;
                            $ledgerDetailsData['partyVATRegisteredYN'] = $masterData->customerVATEligible;
                            $ledgerDetailsData['partyVATRegNo'] = isset($masterData->customer->vatNumber) ? $masterData->customer->vatNumber : "";
                            $ledgerDetailsData['countryID'] = isset($masterData->customer->customerCountry) ? $masterData->customer->customerCountry : "";
                            $ledgerDetailsData['itemSystemCode'] = $value->itemCodeSystem;
                            $ledgerDetailsData['itemCode'] = $value->itemPrimaryCode;
                            $ledgerDetailsData['itemDescription'] = $value->itemDescription;
                            $ledgerDetailsData['VATPercentage'] = $value->VATPercentage;
                            $ledgerDetailsData['taxableAmount'] = $value->transactionAmount;
                            $ledgerDetailsData['VATAmount'] = $value->VATAmount * $value->qtyIssued;
                            $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount * $value->qtyIssued;
                            $ledgerDetailsData['localER'] = $value->companyLocalCurrencyER;
                            $ledgerDetailsData['reportingER'] = $value->companyReportingCurrencyER;
                            $ledgerDetailsData['taxableAmountLocal'] = $value->companyLocalAmount;
                            $ledgerDetailsData['taxableAmountReporting'] = $value->companyReportingAmount;
                            $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal * $value->qtyIssued;
                            $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt * $value->qtyIssued;
                            $ledgerDetailsData['localCurrencyID'] = $value->companyLocalCurrencyID;
                            $ledgerDetailsData['rptCurrencyID'] = $value->companyReportingCurrencyID;

                            array_push($finalDetailData, $ledgerDetailsData);
                        }

                        break;
                    case 87://SalesReturn
                        $masterData = SalesReturn::with(['finance_period_by', 'customer','detail' => function ($query) {
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

                        $detailData = SalesReturnDetail::where('salesReturnID', $masterModel["autoID"])
                                                            ->whereNotNull('vatSubCategoryID')
                                                            ->get();

                        foreach ($detailData as $key => $value) {
                            $ledgerDetailsData['documentDetailID'] = $value->salesReturnDetailID;
                            $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
                            $ledgerDetailsData['vatMasterCategoryID'] = $value->vatMasterCategoryID;
                            $ledgerDetailsData['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                            $ledgerDetailsData['documentDate'] = $masterDocumentDate;
                            $ledgerDetailsData['postedDate'] = date('Y-m-d H:i:s');
                            $ledgerDetailsData['documentNumber'] = $masterData->salesReturnCode;
                            $ledgerDetailsData['chartOfAccountSystemID'] = $value->financeGLcodeRevenueSystemID;

                            $chartOfAccountData = ChartOfAccount::find($value->financeGLcodeRevenueSystemID);

                            if ($chartOfAccountData) {
                                $ledgerDetailsData['accountCode'] = $chartOfAccountData->AccountCode;
                                $ledgerDetailsData['accountDescription'] = $chartOfAccountData->AccountDescription;
                            }

                            $ledgerDetailsData['transactionCurrencyID'] = $value->transactionCurrencyID;
                            $ledgerDetailsData['originalInvoice'] = null;
                            $ledgerDetailsData['originalInvoiceDate'] = null;
                            $ledgerDetailsData['dateOfSupply'] = null;
                            $ledgerDetailsData['partyType'] = 2;
                            $ledgerDetailsData['partyAutoID'] = $masterData->customerID;
                            $ledgerDetailsData['partyVATRegisteredYN'] = isset($masterData->customer->vatEligible) ? $masterData->customer->vatEligible : 0;
                            $ledgerDetailsData['partyVATRegNo'] = isset($masterData->customer->vatNumber) ? $masterData->customer->vatNumber : "";
                            $ledgerDetailsData['countryID'] = isset($masterData->customer->customerCountry) ? $masterData->customer->customerCountry : "";
                            $ledgerDetailsData['itemSystemCode'] = $value->itemCodeSystem;
                            $ledgerDetailsData['itemCode'] = $value->itemPrimaryCode;
                            $ledgerDetailsData['itemDescription'] = $value->itemDescription;
                            $ledgerDetailsData['VATPercentage'] = $value->VATPercentage;
                            $ledgerDetailsData['taxableAmount'] = $value->transactionAmount;
                            $ledgerDetailsData['VATAmount'] = $value->VATAmount * $value->qtyReturned;
                            $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount * $value->qtyReturned;
                            $ledgerDetailsData['localER'] = $value->companyLocalCurrencyER;
                            $ledgerDetailsData['reportingER'] = $value->companyReportingCurrencyER;
                            $ledgerDetailsData['taxableAmountLocal'] = $value->companyLocalAmount;
                            $ledgerDetailsData['taxableAmountReporting'] = $value->companyReportingAmount;
                            $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal * $value->qtyReturned;
                            $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt * $value->qtyReturned;
                            $ledgerDetailsData['localCurrencyID'] = $value->companyLocalCurrencyID;
                            $ledgerDetailsData['rptCurrencyID'] = $value->companyReportingCurrencyID;

                            array_push($finalDetailData, $ledgerDetailsData);
                        }

                        break;
                    case 15://Debit Note
                        $masterData = DebitNote::with(['finance_period_by', 'supplier','detail' => function ($query) {
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

                        $detailData = DebitNoteDetails::where('debitNoteAutoID', $masterModel["autoID"])
                                                            ->whereNotNull('vatSubCategoryID')
                                                            ->get();

                        foreach ($detailData as $key => $value) {
                            $ledgerDetailsData['documentDetailID'] = $value->debitNoteDetailsID;
                            $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
                            $ledgerDetailsData['vatMasterCategoryID'] = $value->vatMasterCategoryID;
                            $ledgerDetailsData['serviceLineSystemID'] = $value->serviceLineSystemID;
                            $ledgerDetailsData['documentDate'] = $masterDocumentDate;
                            $ledgerDetailsData['postedDate'] = date('Y-m-d H:i:s');
                            $ledgerDetailsData['documentNumber'] = $masterData->debitNoteCode;
                            $ledgerDetailsData['chartOfAccountSystemID'] = $value->chartOfAccountSystemID;

                            $chartOfAccountData = ChartOfAccount::find($value->chartOfAccountSystemID);

                            if ($chartOfAccountData) {
                                $ledgerDetailsData['accountCode'] = $chartOfAccountData->AccountCode;
                                $ledgerDetailsData['accountDescription'] = $chartOfAccountData->AccountDescription;
                            }

                            $ledgerDetailsData['transactionCurrencyID'] = $value->debitAmountCurrency;
                            $ledgerDetailsData['originalInvoice'] = null;
                            $ledgerDetailsData['originalInvoiceDate'] = null;
                            $ledgerDetailsData['dateOfSupply'] = null;
                            $ledgerDetailsData['partyType'] = 1;
                            $ledgerDetailsData['partyAutoID'] = $masterData->supplierID;
                            $ledgerDetailsData['partyVATRegisteredYN'] = isset($masterData->supplier->vatEligible) ? $masterData->supplier->vatEligible : 0;
                            $ledgerDetailsData['partyVATRegNo'] = isset($masterData->supplier->vatNumber) ? $masterData->supplier->vatNumber : "";
                            $ledgerDetailsData['countryID'] = isset($masterData->supplier->supplierCountryID) ? $masterData->supplier->supplierCountryID : "";
                            $ledgerDetailsData['itemSystemCode'] = null;
                            $ledgerDetailsData['itemCode'] = null;
                            $ledgerDetailsData['itemDescription'] = null;
                            $ledgerDetailsData['VATPercentage'] = $value->VATPercentage;
                            $ledgerDetailsData['taxableAmount'] = $value->netAmount;
                            $ledgerDetailsData['VATAmount'] = $value->VATAmount;
                            $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount;
                            $ledgerDetailsData['localER'] = $value->localCurrencyER;
                            $ledgerDetailsData['reportingER'] = $value->comRptCurrencyER;
                            $ledgerDetailsData['taxableAmountLocal'] = $value->netAmountLocal;
                            $ledgerDetailsData['taxableAmountReporting'] = $value->netAmountRpt;
                            $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal;
                            $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt;
                            $ledgerDetailsData['localCurrencyID'] = $value->localCurrency;
                            $ledgerDetailsData['rptCurrencyID'] = $value->comRptCurrency;

                            array_push($finalDetailData, $ledgerDetailsData);
                        }

                        break;
                    case 19://Credit Note
                        $masterData = CreditNote::with(['finance_period_by', 'customer','details' => function ($query) {
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

                        $detailData = CreditNoteDetails::where('creditNoteAutoID', $masterModel["autoID"])
                                                            ->whereNotNull('vatSubCategoryID')
                                                            ->get();

                        foreach ($detailData as $key => $value) {
                            $ledgerDetailsData['documentDetailID'] = $value->creditNoteDetailsID;
                            $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
                            $ledgerDetailsData['vatMasterCategoryID'] = $value->vatMasterCategoryID;
                            $ledgerDetailsData['serviceLineSystemID'] = $value->serviceLineSystemID;
                            $ledgerDetailsData['documentDate'] = $masterDocumentDate;
                            $ledgerDetailsData['postedDate'] = date('Y-m-d H:i:s');
                            $ledgerDetailsData['documentNumber'] = $masterData->creditNoteCode;
                            $ledgerDetailsData['chartOfAccountSystemID'] = $value->chartOfAccountSystemID;

                            $chartOfAccountData = ChartOfAccount::find($value->chartOfAccountSystemID);

                            if ($chartOfAccountData) {
                                $ledgerDetailsData['accountCode'] = $chartOfAccountData->AccountCode;
                                $ledgerDetailsData['accountDescription'] = $chartOfAccountData->AccountDescription;
                            }

                            $ledgerDetailsData['transactionCurrencyID'] = $value->creditAmountCurrency;
                            $ledgerDetailsData['originalInvoice'] = null;
                            $ledgerDetailsData['originalInvoiceDate'] = null;
                            $ledgerDetailsData['dateOfSupply'] = null;
                            $ledgerDetailsData['partyType'] = 2;
                            $ledgerDetailsData['partyAutoID'] = $masterData->customerID;
                            $ledgerDetailsData['partyVATRegisteredYN'] = isset($masterData->customer->vatEligible) ? $masterData->customer->vatEligible : 0;
                            $ledgerDetailsData['partyVATRegNo'] = isset($masterData->customer->vatNumber) ? $masterData->customer->vatNumber : "";
                            $ledgerDetailsData['countryID'] = isset($masterData->customer->customerCountry) ? $masterData->customer->customerCountry : "";
                            $ledgerDetailsData['itemSystemCode'] = null;
                            $ledgerDetailsData['itemCode'] = null;
                            $ledgerDetailsData['itemDescription'] = null;
                            $ledgerDetailsData['VATPercentage'] = $value->VATPercentage;
                            $ledgerDetailsData['taxableAmount'] = $value->netAmount;
                            $ledgerDetailsData['VATAmount'] = $value->VATAmount;
                            $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount;
                            $ledgerDetailsData['localER'] = $value->localCurrencyER;
                            $ledgerDetailsData['reportingER'] = $value->comRptCurrencyER;
                            $ledgerDetailsData['taxableAmountLocal'] = $value->netAmountLocal;
                            $ledgerDetailsData['taxableAmountReporting'] = $value->netAmountRpt;
                            $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal;
                            $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt;
                            $ledgerDetailsData['localCurrencyID'] = $value->localCurrency;
                            $ledgerDetailsData['rptCurrencyID'] = $value->comRptCurrency;

                            array_push($finalDetailData, $ledgerDetailsData);
                        }

                        break;
                    case 4://payment voucher
                        $masterData = PaySupplierInvoiceMaster::with(['financeperiod_by', 'supplier','directdetail' => function ($query) {
                            $query->selectRaw('SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DPAmount) as transAmount,directPaymentAutoID');
                        }])->find($masterModel["autoID"]);

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if (isset($masterData->financeperiod_by->isActive) && $masterData->financeperiod_by->isActive == -1) {
                            $masterDocumentDate = $masterData->BPVdate;
                        }

                        $ledgerData['documentCode'] = $masterData->BPVcode;
                        $ledgerData['documentDate'] = $masterDocumentDate;
                        $ledgerData['partyID'] = $masterData->BPVsupplierID;
                        $ledgerData['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;

                        $netAmount = $masterData->netAmount;

                        $currencyConversionAmount = \Helper::currencyConversion($masterData->companySystemID, $masterData->supplierTransCurrencyID, $masterData->supplierTransCurrencyID, $netAmount);

                        $ledgerData['documentTransAmount'] = \Helper::roundValue($netAmount);
                        $ledgerData['documentLocalAmount'] = \Helper::roundValue($currencyConversionAmount['localAmount']);
                        $ledgerData['documentReportingAmount'] = \Helper::roundValue($currencyConversionAmount['reportingAmount']);

                         $details = DirectPaymentDetails::selectRaw('SUM(VATAmount) as transVATAmount,SUM(VATAmountLocal) as localVATAmount ,SUM(VATAmountRpt) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,DPAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER as localCurrencyER,DPAmountCurrencyER as transCurrencyER')
                                ->where('directPaymentAutoID', $masterModel["autoID"])
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

                            $detailData = DirectPaymentDetails::where('directPaymentAutoID', $masterModel["autoID"])
                                ->whereNotNull('vatSubCategoryID')
                                ->get();

                            foreach ($detailData as $key => $value) {
                                $ledgerDetailsData['documentDetailID'] = $value->directPaymentAutoID;
                                $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
                                $ledgerDetailsData['vatMasterCategoryID'] = $value->vatMasterCategoryID;
                                $ledgerDetailsData['serviceLineSystemID'] = $value->serviceLineSystemID;
                                $ledgerDetailsData['documentDate'] = $masterDocumentDate;
                                $ledgerDetailsData['postedDate'] = date('Y-m-d H:i:s');
                                $ledgerDetailsData['documentNumber'] = $masterData->BPVcode;
                                $ledgerDetailsData['chartOfAccountSystemID'] = $value->chartOfAccountSystemID;

                                $chartOfAccountData = ChartOfAccount::find($value->chartOfAccountSystemID);

                                if ($chartOfAccountData) {
                                    $ledgerDetailsData['accountCode'] = $chartOfAccountData->AccountCode;
                                    $ledgerDetailsData['accountDescription'] = $chartOfAccountData->AccountDescription;
                                }

                                $ledgerDetailsData['transactionCurrencyID'] = $value->DPAmountCurrency;
                                $ledgerDetailsData['originalInvoice'] = null;
                                $ledgerDetailsData['originalInvoiceDate'] = null;
                                $ledgerDetailsData['dateOfSupply'] = null;
                                $ledgerDetailsData['partyType'] = 1;
                                $ledgerDetailsData['partyAutoID'] = $masterData->BPVsupplierID;
                                $ledgerDetailsData['partyVATRegisteredYN'] = isset($masterData->supplier->vatEligible) ? $masterData->supplier->vatEligible : 0;
                                $ledgerDetailsData['partyVATRegNo'] = isset($masterData->supplier->vatNumber) ? $masterData->supplier->vatNumber : "";
                                $ledgerDetailsData['countryID'] = isset($masterData->supplier->supplierCountryID) ? $masterData->supplier->supplierCountryID : "";
                                $ledgerDetailsData['itemSystemCode'] = null;
                                $ledgerDetailsData['itemCode'] = null;
                                $ledgerDetailsData['itemDescription'] = null;
                                $ledgerDetailsData['VATPercentage'] = $value->VATPercentage;
                                $ledgerDetailsData['taxableAmount'] = ($value->netAmount - $value->vatAmount);
                                $ledgerDetailsData['VATAmount'] = $value->vatAmount;
                                $ledgerDetailsData['recoverabilityAmount'] = $value->vatAmount;
                                $ledgerDetailsData['localER'] = $value->localCurrencyER;
                                $ledgerDetailsData['reportingER'] = $value->comRptCurrencyER;
                                $ledgerDetailsData['taxableAmountLocal'] = $value->netAmountLocal - $value->VATAmountLocal;
                                $ledgerDetailsData['taxableAmountReporting'] = $value->netAmountRpt - $value->VATAmountRpt;
                                $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal;
                                $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt;
                                $ledgerDetailsData['localCurrencyID'] = $value->localCurrency;
                                $ledgerDetailsData['rptCurrencyID'] = $value->comRptCurrency;

                                array_push($finalDetailData, $ledgerDetailsData);
                            }
                        break;
                    case 11://Supplier Invoice
                        $masterData = BookInvSuppMaster::with(['financeperiod_by', 'supplier','directdetail' => function ($query) {
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

                        $netAmount = ($masterData->documentType == 1) ? $masterData->netAmount : $masterData->bookingAmountTrans; 

                        $currencyConversionAmount = \Helper::currencyConversion($masterData->companySystemID, $masterData->supplierTransactionCurrencyID, $masterData->supplierTransactionCurrencyID, $netAmount);

                        $ledgerData['documentTransAmount'] = \Helper::roundValue($netAmount);
                        $ledgerData['documentLocalAmount'] = \Helper::roundValue($currencyConversionAmount['localAmount']);
                        $ledgerData['documentReportingAmount'] = \Helper::roundValue($currencyConversionAmount['reportingAmount']);
                            

                        if ($masterData->documentType == 1) {
                            $details = DirectInvoiceDetails::selectRaw('SUM(VATAmount) as transVATAmount,SUM(VATAmountLocal) as localVATAmount ,SUM(VATAmountRpt) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,DIAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER as localCurrencyER,DIAmountCurrencyER as transCurrencyER')
                                                    ->where('directInvoiceAutoID', $masterModel["autoID"])
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

                            $detailData = DirectInvoiceDetails::where('directInvoiceAutoID', $masterModel["autoID"])
                                                            ->whereNotNull('vatSubCategoryID')
                                                            ->get();

                            foreach ($detailData as $key => $value) {
                                $ledgerDetailsData['documentDetailID'] = $value->directInvoiceDetailsID;
                                $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
                                $ledgerDetailsData['vatMasterCategoryID'] = $value->vatMasterCategoryID;
                                $ledgerDetailsData['serviceLineSystemID'] = $value->serviceLineSystemID;
                                $ledgerDetailsData['documentDate'] = $masterDocumentDate;
                                $ledgerDetailsData['postedDate'] = date('Y-m-d H:i:s');
                                $ledgerDetailsData['documentNumber'] = $masterData->bookingInvCode;
                                $ledgerDetailsData['chartOfAccountSystemID'] = $value->chartOfAccountSystemID;

                                $chartOfAccountData = ChartOfAccount::find($value->chartOfAccountSystemID);

                                if ($chartOfAccountData) {
                                    $ledgerDetailsData['accountCode'] = $chartOfAccountData->AccountCode;
                                    $ledgerDetailsData['accountDescription'] = $chartOfAccountData->AccountDescription;
                                }

                                $ledgerDetailsData['transactionCurrencyID'] = $value->DIAmountCurrency;
                                $ledgerDetailsData['originalInvoice'] = null;
                                $ledgerDetailsData['originalInvoiceDate'] = null;
                                $ledgerDetailsData['dateOfSupply'] = null;
                                $ledgerDetailsData['partyType'] = 1;
                                $ledgerDetailsData['partyAutoID'] = $masterData->supplierID;
                                $ledgerDetailsData['partyVATRegisteredYN'] = isset($masterData->supplier->vatEligible) ? $masterData->supplier->vatEligible : 0;
                                $ledgerDetailsData['partyVATRegNo'] = isset($masterData->supplier->vatNumber) ? $masterData->supplier->vatNumber : "";
                                $ledgerDetailsData['countryID'] = isset($masterData->supplier->supplierCountryID) ? $masterData->supplier->supplierCountryID : "";
                                $ledgerDetailsData['itemSystemCode'] = null;
                                $ledgerDetailsData['itemCode'] = null;
                                $ledgerDetailsData['itemDescription'] = null;
                                $ledgerDetailsData['VATPercentage'] = $value->VATPercentage;
                                $ledgerDetailsData['taxableAmount'] = ($value->netAmount - $value->VATAmount);
                                $ledgerDetailsData['VATAmount'] = $value->VATAmount;
                                $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount;
                                $ledgerDetailsData['localER'] = $value->localCurrencyER;
                                $ledgerDetailsData['reportingER'] = $value->comRptCurrencyER;
                                $ledgerDetailsData['taxableAmountLocal'] = $value->netAmountLocal - $value->VATAmountLocal;
                                $ledgerDetailsData['taxableAmountReporting'] = $value->netAmountRpt - $value->VATAmountRpt;
                                $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal;
                                $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt;
                                $ledgerDetailsData['localCurrencyID'] = $value->localCurrency;
                                $ledgerDetailsData['rptCurrencyID'] = $value->comRptCurrency;

                                array_push($finalDetailData, $ledgerDetailsData);
                            }
                        } else {
                            $details = SupplierInvoiceItemDetail::selectRaw('SUM(VATAmount) as transVATAmount,SUM(VATAmountLocal) as localVATAmount ,SUM(VATAmountRpt) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrencyID as localCurrencyID,companyReportingCurrencyID as reportingCurrencyID,supplierTransactionCurrencyID as transCurrencyID,companyReportingER as reportingCurrencyER,localCurrencyER as localCurrencyER,supplierTransactionCurrencyER as transCurrencyER')
                                                    ->where('bookingSuppMasInvAutoID', $masterModel["autoID"])
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

                            $detailData = SupplierInvoiceItemDetail::with(['grv_detail', 'logistic_detail' => function($query) {
                                                                $query->with(['category_by' => function($query) {
                                                                    $query->with(['item_by']);
                                                                }]);
                                                            }])
                                                            ->where('bookingSuppMasInvAutoID', $masterModel["autoID"])
                                                            ->whereNotNull('vatSubCategoryID')
                                                            ->get();

                            foreach ($detailData as $key => $value) {
                                $ledgerDetailsData['rcmApplicableYN'] = TaxService::isGRVRCMActivation($value->grvAutoID);
                                $isRCMApplicable =  (boolean) $ledgerDetailsData['rcmApplicableYN'];
                                $ledgerDetailsData['documentDetailID'] = $value->id;                               
                                $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
                                $ledgerDetailsData['vatMasterCategoryID'] = $value->vatMasterCategoryID;
                                $ledgerDetailsData['serviceLineSystemID'] = null;
                                $ledgerDetailsData['documentDate'] = $masterDocumentDate;
                                $ledgerDetailsData['postedDate'] = date('Y-m-d H:i:s');
                                $ledgerDetailsData['documentNumber'] = $masterData->bookingInvCode;
                                $ledgerDetailsData['chartOfAccountSystemID'] = ($value->grvDetailsID > 0) ? $value->grv_detail->financeGLcodePLSystemID : (isset($value->logistic_detail->UnbilledGRVAccountSystemID) ? $value->logistic_detail->UnbilledGRVAccountSystemID : 0);

                                $chartOfAccountData = ChartOfAccount::find($ledgerDetailsData['chartOfAccountSystemID']);

                                if ($chartOfAccountData) {
                                    $ledgerDetailsData['accountCode'] = $chartOfAccountData->AccountCode;
                                    $ledgerDetailsData['accountDescription'] = $chartOfAccountData->AccountDescription;
                                }




                                if(!$isRCMApplicable) {
                                    $subCategory = TaxVatCategories::find($value->vatSubCategoryID);
                                    if($subCategory->subCatgeoryType == 2) {
                                        $taxableAmountReporting = ( $value->totRptAmount -  (($value->totRptAmount / 100) * $subCategory->percentage));
                                    }else {
                                        $taxableAmountReporting =  $value->totRptAmount - $value->VATAmountRpt;
                                    }
                                }else {
                                   $taxableAmountReporting =  $value->totRptAmount;
                                }


                                $ledgerDetailsData['transactionCurrencyID'] = $value->supplierTransactionCurrencyID;
                                $ledgerDetailsData['originalInvoice'] = NULL;
                                $ledgerDetailsData['originalInvoiceDate'] = NULL;
                                $ledgerDetailsData['dateOfSupply'] = NULL;
                                $ledgerDetailsData['partyType'] = 1;
                                $ledgerDetailsData['partyAutoID'] = $masterData->supplierID;
                                $ledgerDetailsData['partyVATRegisteredYN'] = $masterData->vatRegisteredYN;
                                $ledgerDetailsData['partyVATRegNo'] = isset($masterData->supplier->vatNumber) ? $masterData->supplier->vatNumber : "";
                                $ledgerDetailsData['countryID'] = isset($masterData->supplier->supplierCountryID) ? $masterData->supplier->supplierCountryID : "";
                                $ledgerDetailsData['itemSystemCode'] = ($value->grvDetailsID > 0) ? $value->grv_detail->itemCode : (isset($value->logistic_detail->category_by->itemSystemCode) ? $value->logistic_detail->category_by->itemSystemCode : null);
                                $ledgerDetailsData['itemCode'] = ($value->grvDetailsID > 0) ? $value->grv_detail->itemPrimaryCode :  (isset($value->logistic_detail->category_by->item_by->primaryCode) ? $value->logistic_detail->category_by->item_by->primaryCode : null);
                                $ledgerDetailsData['itemDescription'] = ($value->grvDetailsID > 0) ? $value->grv_detail->itemDescription :  (isset($value->logistic_detail->category_by->item_by->itemDescription) ? $value->logistic_detail->category_by->item_by->itemDescription : null);
                                $ledgerDetailsData['VATPercentage'] = ($value->grvDetailsID > 0) ? $value->grv_detail->VATPercentage : (isset($value->logistic_detail->VATPercentage) ? $value->logistic_detail->VATPercentage : null);
                                $ledgerDetailsData['taxableAmount'] = ($value->totTransactionAmount - $value->VATAmount);
                                $ledgerDetailsData['VATAmount'] = $value->VATAmount;
                                $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount;
                                $ledgerDetailsData['localER'] = $value->localCurrencyER;
                                $ledgerDetailsData['reportingER'] = $value->companyReportingER;
                                $ledgerDetailsData['taxableAmountLocal'] = ($isRCMApplicable) ? $value->totLocalAmount  : ($value->totLocalAmount - $value->VATAmountLocal);
                                $ledgerDetailsData['taxableAmountReporting'] = ($isRCMApplicable) ? $value->totRptAmount : ($value->totRptAmount - $value->VATAmountRpt);
                                $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal;
                                $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt;
                                $ledgerDetailsData['localCurrencyID'] = $value->localCurrencyID;
                                $ledgerDetailsData['rptCurrencyID'] = $value->companyReportingCurrencyID;
                                $ledgerDetailsData['exempt_vat_portion'] = $value->exempt_vat_portion;
                                $ledgerDetailsData['logisticYN'] = ($value->logisticID > 0) ? 1 : 0;
                                $ledgerDetailsData['addVATonPO'] = (isset($value->logistic_detail->addVatOnPO) ? $value->logistic_detail->addVatOnPO : 0) ? 1 : 0;
                                array_push($finalDetailData, $ledgerDetailsData);
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

                    Log::info("Detail Log");
                    Log::info($finalDetailData);
                    foreach ($finalDetailData as $data)
                    {
                      TaxLedgerDetail::create($data);
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
