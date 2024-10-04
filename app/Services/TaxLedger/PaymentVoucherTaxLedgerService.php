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
use App\Models\PaySupplierInvoiceDetail;
use App\Models\TaxLedgerDetail;
use App\Models\AdvancePaymentDetails;
use App\Models\DebitNoteDetails;
use App\Models\TaxVatCategories;
use App\helper\TaxService;
use App\Models\Employee;
use App\Models\SalesReturn;
use App\Models\ChartOfAccount;
use App\Models\SalesReturnDetail;
use App\Models\BookInvSuppMaster;
use App\Models\DirectInvoiceDetails;

class PaymentVoucherTaxLedgerService
{
    public static function processEntry($taxLedgerData, $masterModel)
    {
        $finalData = [];
        $finalDetailData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);
        $ledgerData = [
            'documentSystemID' => $masterModel["documentSystemID"],
            'documentMasterAutoID' => $masterModel["autoID"],
            'inputVATGlAccountID' => isset($taxLedgerData['inputVatGLAccountID']) ? $taxLedgerData['inputVatGLAccountID'] : null,
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


        if ($masterData->invoiceType == 5) {

            if (isset($masterModel["matching"]) && $masterModel["matching"]) {
                $netAdv = PaySupplierInvoiceDetail::where('matchingDocID', $masterModel['matchDocumentMasterAutoID'])
                    ->selectRaw('(SUM(supplierPaymentAmount) - SUM(VATAmount)) as netAmount')
                    ->first();

                $netAmount = $netAdv->netAmount;

                $currencyConversionAmount = \Helper::currencyConversion($masterData->companySystemID, $masterData->supplierTransCurrencyID, $masterData->supplierTransCurrencyID, $netAmount);

                $ledgerData['documentTransAmount'] = \Helper::roundValue($netAmount);
                $ledgerData['documentLocalAmount'] = \Helper::roundValue($currencyConversionAmount['localAmount']);
                $ledgerData['documentReportingAmount'] = \Helper::roundValue($currencyConversionAmount['reportingAmount']);

                $details = PaySupplierInvoiceDetail::selectRaw('erp_tax_vat_sub_categories.subCatgeoryType,SUM(VATAmount) as transVATAmount,SUM(VATAmountLocal) as localVATAmount ,SUM(VATAmountRpt) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrencyID as localCurrencyID,comRptCurrencyID as reportingCurrencyID,supplierTransCurrencyID as transCurrencyID,comRptER as reportingCurrencyER,localER as localCurrencyER,supplierTransER as transCurrencyER')
                        ->where('matchingDocID', $masterModel["matchDocumentMasterAutoID"])
                        ->whereNotNull('vatSubCategoryID')
                        ->join('erp_tax_vat_sub_categories', 'erp_paysupplierinvoicedetail.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                        ->groupBy('vatSubCategoryID')
                        ->get();

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
                    $ledgerData['transER'] = $value->transCurrencyER;
                    $ledgerData['localER'] = $value->localCurrencyER;
                    $ledgerData['comRptER'] = $value->reportingCurrencyER;
                    $ledgerData['localCurrencyID'] = $value->localCurrencyID;
                    $ledgerData['rptCurrencyID'] = $value->reportingCurrencyID;
                    $ledgerData['transCurrencyID'] = $value->transCurrencyID;
                    $ledgerData['matchDocumentMasterAutoID'] = $masterModel['matchDocumentMasterAutoID'];

                    array_push($finalData, $ledgerData);
                }

                $detailData = PaySupplierInvoiceDetail::where('matchingDocID', $masterModel["matchDocumentMasterAutoID"])
                    ->join('erp_tax_vat_sub_categories', 'erp_paysupplierinvoicedetail.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                    ->whereNotNull('vatSubCategoryID')
                    ->get();

                foreach ($detailData as $key => $value) {
                    $ledgerDetailsData['documentDetailID'] = $value->payDetailAutoID;
                    $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
                    $ledgerDetailsData['vatMasterCategoryID'] = $value->vatMasterCategoryID;
                    $ledgerDetailsData['serviceLineSystemID'] = null;
                    $ledgerDetailsData['documentDate'] = $masterDocumentDate;
                    $ledgerDetailsData['postedDate'] = date('Y-m-d H:i:s');
                    $ledgerDetailsData['documentNumber'] = $masterData->BPVcode;
                    // $ledgerDetailsData['chartOfAccountSystemID'] = $value->chartOfAccountSystemID;

                    // $chartOfAccountData = ChartOfAccount::find($value->chartOfAccountSystemID);

                    // if ($chartOfAccountData) {
                    //     $ledgerDetailsData['accountCode'] = $chartOfAccountData->AccountCode;
                    //     $ledgerDetailsData['accountDescription'] = $chartOfAccountData->AccountDescription;
                    // }

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


                    $ledgerDetailsData['transactionCurrencyID'] = $value->supplierTransCurrencyID;
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
                    $ledgerDetailsData['taxableAmount'] = ($value->supplierPaymentAmount);
                    $ledgerDetailsData['VATAmount'] = $value->VATAmount;
                    $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount;
                    $ledgerDetailsData['localER'] = $value->localER;
                    $ledgerDetailsData['reportingER'] = $value->comRptER;
                    $ledgerDetailsData['taxableAmountLocal'] = $value->paymentLocalAmount;
                    $ledgerDetailsData['taxableAmountReporting'] = $value->paymentComRptAmount;
                    $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal;
                    $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt;
                    $taxConfigData = TaxService::getInputVATGLAccount($masterData->companySystemID);
                    if (!empty($taxConfigData)) {
                        $ledgerDetailsData['inputVATGlAccountID'] = $taxConfigData->inputVatGLAccountAutoID;
                    }

                    $taxConfigDataTrans = TaxService::getInputVATTransferGLAccount($masterData->companySystemID);
                    if (!empty($taxConfigDataTrans)) {
                        $ledgerDetailsData['inputVatTransferAccountID'] = $taxConfigDataTrans->inputVatTransferGLAccountAutoID;
                    }
                    $ledgerDetailsData['localCurrencyID'] = $value->localCurrencyID;
                    $ledgerDetailsData['rptCurrencyID'] = $value->comRptCurrencyID;
                    $ledgerDetailsData['matchDocumentMasterAutoID'] = $masterModel['matchDocumentMasterAutoID'];

                    array_push($finalDetailData, $ledgerDetailsData);
                }
            } else {

                $netAdv = AdvancePaymentDetails::where('PayMasterAutoId', $masterModel["autoID"])
                    ->selectRaw('(SUM(paymentAmount) - SUM(VATAmount)) as netAmount')
                    ->first();

                $netAmount = $netAdv->netAmount;

                $currencyConversionAmount = \Helper::currencyConversion($masterData->companySystemID, $masterData->supplierTransCurrencyID, $masterData->supplierTransCurrencyID, $netAmount);

                $ledgerData['documentTransAmount'] = \Helper::roundValue($netAmount);
                $ledgerData['documentLocalAmount'] = \Helper::roundValue($currencyConversionAmount['localAmount']);
                $ledgerData['documentReportingAmount'] = \Helper::roundValue($currencyConversionAmount['reportingAmount']);

                $details = AdvancePaymentDetails::selectRaw('erp_tax_vat_sub_categories.subCatgeoryType,SUM(VATAmount) as transVATAmount,SUM(VATAmountLocal) as localVATAmount ,SUM(VATAmountRpt) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrencyID as localCurrencyID,comRptCurrencyID as reportingCurrencyID,supplierTransCurrencyID as transCurrencyID,comRptER as reportingCurrencyER,localER as localCurrencyER,supplierTransER as transCurrencyER')
                        ->where('PayMasterAutoId', $masterModel["autoID"])
                        ->whereNotNull('vatSubCategoryID')
                        ->join('erp_tax_vat_sub_categories', 'erp_advancepaymentdetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                        ->groupBy('vatSubCategoryID')
                        ->get();

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
                    $ledgerData['transER'] = $value->transCurrencyER;
                    $ledgerData['localER'] = $value->localCurrencyER;
                    $ledgerData['comRptER'] = $value->reportingCurrencyER;
                    $ledgerData['localCurrencyID'] = $value->localCurrencyID;
                    $ledgerData['rptCurrencyID'] = $value->reportingCurrencyID;
                    $ledgerData['transCurrencyID'] = $value->transCurrencyID;

                    array_push($finalData, $ledgerData);
                }

                $detailData = AdvancePaymentDetails::where('PayMasterAutoId', $masterModel["autoID"])
                    ->join('erp_tax_vat_sub_categories', 'erp_advancepaymentdetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                    ->whereNotNull('vatSubCategoryID')
                    ->get();

                foreach ($detailData as $key => $value) {
                    $ledgerDetailsData['documentDetailID'] = $value->advancePaymentDetailAutoID;
                    $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
                    $ledgerDetailsData['vatMasterCategoryID'] = $value->vatMasterCategoryID;
                    $ledgerDetailsData['serviceLineSystemID'] = null;
                    $ledgerDetailsData['documentDate'] = $masterDocumentDate;
                    $ledgerDetailsData['postedDate'] = date('Y-m-d H:i:s');
                    $ledgerDetailsData['documentNumber'] = $masterData->BPVcode;
                    // $ledgerDetailsData['chartOfAccountSystemID'] = $value->chartOfAccountSystemID;

                    // $chartOfAccountData = ChartOfAccount::find($value->chartOfAccountSystemID);

                    // if ($chartOfAccountData) {
                    //     $ledgerDetailsData['accountCode'] = $chartOfAccountData->AccountCode;
                    //     $ledgerDetailsData['accountDescription'] = $chartOfAccountData->AccountDescription;
                    // }

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

                    $ledgerDetailsData['transactionCurrencyID'] = $value->supplierTransCurrencyID;
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
                    $ledgerDetailsData['taxableAmount'] = ($value->paymentAmount);
                    $ledgerDetailsData['VATAmount'] = $value->VATAmount;
                    $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount;
                    $ledgerDetailsData['localER'] = $value->localER;
                    $ledgerDetailsData['reportingER'] = $value->comRptER;
                    $ledgerDetailsData['taxableAmountLocal'] = $value->localAmount;
                    $ledgerDetailsData['taxableAmountReporting'] = $value->comRptAmount;
                    $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal;
                    $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt;
                    $taxConfigData = TaxService::getInputVATGLAccount($masterData->companySystemID);
                    if (!empty($taxConfigData)) {
                        $ledgerDetailsData['inputVATGlAccountID'] = $taxConfigData->inputVatGLAccountAutoID;
                    }

                    $taxConfigDataTrans = TaxService::getInputVATTransferGLAccount($masterData->companySystemID);
                    if (!empty($taxConfigDataTrans)) {
                        $ledgerDetailsData['inputVatTransferAccountID'] = $taxConfigDataTrans->inputVatTransferGLAccountAutoID;
                    }
                    $ledgerDetailsData['localCurrencyID'] = $value->localCurrencyID;
                    $ledgerDetailsData['rptCurrencyID'] = $value->comRptCurrencyID;

                    array_push($finalDetailData, $ledgerDetailsData);
                }
            }
        } else {
            $details = DirectPaymentDetails::selectRaw('erp_tax_vat_sub_categories.subCatgeoryType,SUM(VATAmount) as transVATAmount,SUM(VATAmountLocal) as localVATAmount ,SUM(VATAmountRpt) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,DPAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER as localCurrencyER,DPAmountCurrencyER as transCurrencyER')
                    ->where('directPaymentAutoID', $masterModel["autoID"])
                    ->whereNotNull('vatSubCategoryID')
                    ->join('erp_tax_vat_sub_categories', 'erp_directpaymentdetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                    ->groupBy('vatSubCategoryID')
                    ->get();

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
                $ledgerData['transER'] = $value->transCurrencyER;
                $ledgerData['localER'] = $value->localCurrencyER;
                $ledgerData['comRptER'] = $value->reportingCurrencyER;
                $ledgerData['localCurrencyID'] = $value->localCurrencyID;
                $ledgerData['rptCurrencyID'] = $value->reportingCurrencyID;
                $ledgerData['transCurrencyID'] = $value->transCurrencyID;

                array_push($finalData, $ledgerData);
            }

            $detailData = DirectPaymentDetails::where('directPaymentAutoID', $masterModel["autoID"])
                ->join('erp_tax_vat_sub_categories', 'erp_directpaymentdetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
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
                $ledgerDetailsData['taxableAmount'] = ($value->netAmount);
                $ledgerDetailsData['VATAmount'] = $value->vatAmount;
                $ledgerDetailsData['recoverabilityAmount'] = $value->vatAmount;
                $ledgerDetailsData['localER'] = $value->localCurrencyER;
                $ledgerDetailsData['reportingER'] = $value->comRptCurrencyER;
                $ledgerDetailsData['taxableAmountLocal'] = $value->netAmountLocal;
                $ledgerDetailsData['taxableAmountReporting'] = $value->netAmountRpt;
                $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal;
                $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt;
                $taxConfigData = TaxService::getInputVATGLAccount($masterData->companySystemID);
                if (!empty($taxConfigData)) {
                    $ledgerDetailsData['inputVATGlAccountID'] = $taxConfigData->inputVatGLAccountAutoID;
                }
                $ledgerDetailsData['localCurrencyID'] = $value->localCurrency;
                $ledgerDetailsData['rptCurrencyID'] = $value->comRptCurrency;

                array_push($finalDetailData, $ledgerDetailsData);
            }
        }


        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'finalDetailData' => $finalDetailData]];
    }
}
