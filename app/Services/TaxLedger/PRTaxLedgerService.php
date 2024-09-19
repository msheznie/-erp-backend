<?php

namespace App\Services\TaxLedger;

use App\Models\DirectPaymentDetails;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\POSTaxGLEntries;
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

class PRTaxLedgerService
{
	public static function processEntry($taxLedgerData, $masterModel)
	{
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

        $details = PurchaseReturnDetails::selectRaw('erp_tax_vat_sub_categories.subCatgeoryType,SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrencyID,companyReportingCurrencyID as reportingCurrencyID,supplierTransactionCurrencyID,supplierTransactionER,companyReportingER,localCurrencyER')
                                ->where('purhaseReturnAutoID', $masterModel["autoID"])
                                ->whereNotNull('vatSubCategoryID')
                                ->join('erp_tax_vat_sub_categories', 'erp_purchasereturndetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                ->groupBy('erp_tax_vat_sub_categories.subCatgeoryType')
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

            if($value->subCatgeoryType == 3)
            {
                $ledgerData['inputVATGlAccountID'] = null;
                $ledgerData['inputVatTransferAccountID'] = null;
                $ledgerData['outputVatTransferGLAccountID'] = null;
                $ledgerData['outputVatGLAccountID'] = null;
            }
            else
            {
                $ledgerData['inputVATGlAccountID'] = isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null;
                $ledgerData['inputVatTransferAccountID'] = isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null;
                $ledgerData['outputVatTransferGLAccountID'] = isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null;
                $ledgerData['outputVatGLAccountID'] = isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null;
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
                                            ->join('erp_tax_vat_sub_categories', 'erp_purchasereturndetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
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


            if($value->subCatgeoryType == 3)
            {
                $ledgerDetailsData['inputVATGlAccountID'] = null;
                $ledgerDetailsData['inputVatTransferAccountID'] = null;
                $ledgerDetailsData['outputVatTransferGLAccountID'] = null;
                $ledgerDetailsData['outputVatGLAccountID'] = null;
            }
            else
            {
                $ledgerDetailsData['inputVATGlAccountID'] = isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null;
                $ledgerDetailsData['inputVatTransferAccountID'] = isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null;
                $ledgerDetailsData['outputVatTransferGLAccountID'] = isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null;
                $ledgerDetailsData['outputVatGLAccountID'] = isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null;
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

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'finalDetailData' => $finalDetailData]];
	}
}