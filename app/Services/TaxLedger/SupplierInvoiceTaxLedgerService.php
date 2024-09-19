<?php

namespace App\Services\TaxLedger;

use App\Models\DirectPaymentDetails;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\POSTaxGLEntries;
use App\Models\SupplierInvoiceDirectItem;
use App\Services\JobErrorLogService;
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

class SupplierInvoiceTaxLedgerService
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
        
        $masterData = BookInvSuppMaster::with(['financeperiod_by', 'supplier', 'employee', 'directdetail' => function ($query) {
            $query->selectRaw('SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DIAmount) as transAmount,directInvoiceAutoID');
        }])->find($masterModel["autoID"]);

        $masterDocumentDate = date('Y-m-d H:i:s');
        if (isset($masterData->financeperiod_by->isActive) && $masterData->financeperiod_by->isActive == -1) {
            $masterDocumentDate = $masterData->bookingDate;
        }

        $ledgerData['documentCode'] = $masterData->bookingInvCode;
        $ledgerData['documentDate'] = $masterDocumentDate;
        $ledgerData['partyID'] = ($masterData->documentType == 4) ? $masterData->employeeID : $masterData->supplierID;
        $ledgerData['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;

        $netAmount = ($masterData->documentType == 1) ? $masterData->netAmount : $masterData->bookingAmountTrans; 

        $currencyConversionAmount = \Helper::currencyConversion($masterData->companySystemID, $masterData->supplierTransactionCurrencyID, $masterData->supplierTransactionCurrencyID, $netAmount);

        $ledgerData['documentTransAmount'] = \Helper::roundValue($netAmount);
        $ledgerData['documentLocalAmount'] = \Helper::roundValue($currencyConversionAmount['localAmount']);
        $ledgerData['documentReportingAmount'] = \Helper::roundValue($currencyConversionAmount['reportingAmount']);
            

        if ($masterData->documentType == 1 || $masterData->documentType == 4) {
            $details = DirectInvoiceDetails::selectRaw('erp_tax_vat_sub_categories.subCatgeoryType,SUM(VATAmount) as transVATAmount,SUM(VATAmountLocal) as localVATAmount ,SUM(VATAmountRpt) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,DIAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER as localCurrencyER,DIAmountCurrencyER as transCurrencyER')
                                    ->where('directInvoiceAutoID', $masterModel["autoID"])
                                    ->whereNotNull('vatSubCategoryID')
                                    ->join('erp_tax_vat_sub_categories', 'erp_directinvoicedetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                    ->groupBy('erp_tax_vat_sub_categories.subCatgeoryType')
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

            $detailData = DirectInvoiceDetails::where('directInvoiceAutoID', $masterModel["autoID"])
                                            ->whereNotNull('vatSubCategoryID')
                                            ->join('erp_tax_vat_sub_categories', 'erp_directinvoicedetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                            ->get();

            foreach ($detailData as $key => $value) {
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
                if ($masterData->documentType == 1) {
                    $ledgerDetailsData['partyType'] = 1;
                    $ledgerDetailsData['partyAutoID'] = $masterData->supplierID;
                    $ledgerDetailsData['partyVATRegisteredYN'] = isset($masterData->supplier->vatEligible) ? $masterData->supplier->vatEligible : 0;
                    $ledgerDetailsData['partyVATRegNo'] = isset($masterData->supplier->vatNumber) ? $masterData->supplier->vatNumber : "";
                    $ledgerDetailsData['countryID'] = isset($masterData->supplier->supplierCountryID) ? $masterData->supplier->supplierCountryID : "";
                } else if ($masterData->documentType == 4) {
                    $ledgerDetailsData['partyType'] = 3;
                    $ledgerDetailsData['partyAutoID'] = $masterData->employeeID;
                    $ledgerDetailsData['partyVATRegisteredYN'] = 0;
                    $ledgerDetailsData['partyVATRegNo'] = null;
                    $ledgerDetailsData['countryID'] =null;
                }
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
        } else if($masterData->documentType == 3) {
            $details = SupplierInvoiceDirectItem::selectRaw('erp_tax_vat_sub_categories.subCatgeoryType,noQty, SUM(VATAmount * noQty) as transVATAmount, SUM(VATAmountLocal * noQty) as localVATAmount ,SUM(VATAmountRpt * noQty) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrencyID, companyReportingCurrencyID as reportingCurrencyID, supplierDefaultCurrencyID as transCurrencyID, companyReportingER as reportingCurrencyER, localCurrencyER as localCurrencyER, supplierDefaultER as transCurrencyER')
                ->where('bookingSuppMasInvAutoID', $masterModel["autoID"])
                ->whereNotNull('vatSubCategoryID')
                ->join('erp_tax_vat_sub_categories', 'supplier_invoice_items.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
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

            $detailData = SupplierInvoiceDirectItem::where('bookingSuppMasInvAutoID', $masterModel["autoID"])
                ->join('erp_tax_vat_sub_categories', 'supplier_invoice_items.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                ->whereNotNull('vatSubCategoryID')
                ->get();

            foreach ($detailData as $key => $value) {
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
                $ledgerDetailsData['documentDetailID'] = $value->id;
                $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
                $ledgerDetailsData['vatMasterCategoryID'] = $value->vatMasterCategoryID;
                $ledgerDetailsData['serviceLineSystemID'] = null;
                $ledgerDetailsData['documentDate'] = $masterDocumentDate;
                $ledgerDetailsData['postedDate'] = date('Y-m-d H:i:s');
                $ledgerDetailsData['documentNumber'] = $masterData->bookingInvCode;

                $chartOfAccountSystemID = isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null;
                $ledgerDetailsData['chartOfAccountSystemID'] = $chartOfAccountSystemID;

                $chartOfAccountData = ChartOfAccount::find($chartOfAccountSystemID);

                if ($chartOfAccountData) {
                    $ledgerDetailsData['accountCode'] = $chartOfAccountData->AccountCode;
                    $ledgerDetailsData['accountDescription'] = $chartOfAccountData->AccountDescription;
                }

                $ledgerDetailsData['transactionCurrencyID'] = $value->supplierDefaultCurrencyID;
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
                $ledgerDetailsData['VATAmount'] = $value->VATAmount * $value->noQty;
                $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount * $value->noQty;
                $ledgerDetailsData['localER'] = $value->localCurrencyER;
                $ledgerDetailsData['reportingER'] = $value->companyReportingER;
                $ledgerDetailsData['taxableAmountLocal'] = $value->netAmount/$value->localCurrencyER;
                $ledgerDetailsData['taxableAmountReporting'] = $value->netAmount/$value->companyReportingER;
                $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal * $value->noQty;
                $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt * $value->noQty;
                $ledgerDetailsData['localCurrencyID'] = $value->localCurrencyID;
                $ledgerDetailsData['rptCurrencyID'] = $value->companyReportingCurrencyID;

                array_push($finalDetailData, $ledgerDetailsData);
            }
        }
        else {
            $details = SupplierInvoiceItemDetail::selectRaw('erp_tax_vat_sub_categories.subCatgeoryType,SUM(VATAmount) as transVATAmount,SUM(VATAmountLocal) as localVATAmount ,SUM(VATAmountRpt) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrencyID as localCurrencyID,companyReportingCurrencyID as reportingCurrencyID,supplierTransactionCurrencyID as transCurrencyID,companyReportingER as reportingCurrencyER,localCurrencyER as localCurrencyER,supplierTransactionCurrencyER as transCurrencyER')
                                    ->where('bookingSuppMasInvAutoID', $masterModel["autoID"])
                                    ->whereNotNull('vatSubCategoryID')
                                    ->join('erp_tax_vat_sub_categories', 'erp_bookinvsupp_item_det.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
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

            $detailData = SupplierInvoiceItemDetail::with(['grv_detail', 'logistic_detail' => function($query) {
                                                $query->with(['category_by' => function($query) {
                                                    $query->with(['item_by']);
                                                }]);
                                            }])
                                            ->where('bookingSuppMasInvAutoID', $masterModel["autoID"])
                                            ->whereNotNull('vatSubCategoryID')
                                            ->join('erp_tax_vat_sub_categories', 'erp_bookinvsupp_item_det.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                            ->get();

            foreach ($detailData as $key => $value) {

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

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'finalDetailData' => $finalDetailData]];
	}
}
