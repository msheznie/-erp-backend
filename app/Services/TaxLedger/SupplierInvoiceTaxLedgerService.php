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
use App\helper\CurrencyConversionService;
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
        

        $exampteVat = TaxVatCategories::with(['tax'])->where('subCatgeoryType', 3)->whereHas('tax', function ($query) use ($masterData) {
            $query->where('companySystemID', $masterData->companySystemID)->where('taxCategory', 2);
        })->where('isActive', 1)->first();

        $exemptVatSub = $exampteVat?$exampteVat->taxVatSubCategoriesAutoID:NULL;
        $exemptVatMain = $exampteVat?$exampteVat->mainCategory:NULL;

        $standardRatedSupply = TaxVatCategories::with(['tax'])->where('subCatgeoryType', 1)->whereHas('tax', function ($query) use ($masterData) {
            $query->where('companySystemID', $masterData->companySystemID)->where('taxCategory', 2);
        })->where('isActive', 1)->first();
        $standardRatedSupplyID = $standardRatedSupply?$standardRatedSupply->taxVatSubCategoriesAutoID:null;

        if ($masterData->documentType == 1 || $masterData->documentType == 4) {
            $details = DirectInvoiceDetails::selectRaw('exempt_vat_portion,erp_tax_vat_sub_categories.subCatgeoryType,(VATAmount) as transVATAmount,(VATAmountLocal) as localVATAmount ,(VATAmountRpt) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,DIAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER as localCurrencyER,DIAmountCurrencyER as transCurrencyER')
                                    ->where('directInvoiceAutoID', $masterModel["autoID"])
                                    ->whereNotNull('vatSubCategoryID')
                                    ->join('erp_tax_vat_sub_categories', 'erp_directinvoicedetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                    ->get();

            foreach ($details as $key => $value) {
                $subCategoryData = TaxVatCategories::with(['tax'])->find($value->vatSubCategoryID);

                if ($subCategoryData) {
                    $ledgerData['taxAuthorityAutoID'] = isset($subCategoryData->tax->authorityAutoID) ? $subCategoryData->tax->authorityAutoID : null;
                }

                $ledgerData['transER'] = $value->transCurrencyER;
                $ledgerData['localER'] = $value->localCurrencyER;
                $ledgerData['comRptER'] = $value->reportingCurrencyER;
                $ledgerData['localCurrencyID'] = $value->localCurrencyID;
                $ledgerData['rptCurrencyID'] = $value->reportingCurrencyID;
                $ledgerData['transCurrencyID'] = $value->transCurrencyID;
                
                if($value->subCatgeoryType == 1)
                {

                    $vatPortion = $value->exempt_vat_portion;
                    $exemptAmount =   ($vatPortion/100) * $value->transVATAmount ;
                    $standardAmount = $value->transVATAmount - $exemptAmount;

                    if (($masterData->documentType == 1) && ($masterData['retentionPercentage'] > 0)) {
                        $standardAmount = $standardAmount - $masterData['retentionVatAmount'];
                    }

                    $info = [
                        ["amount" => $exemptAmount,"subcat" => $exemptVatSub,"mastercat" => $exemptVatMain,"inVat" => null,"inTra" => null,"outVat" => null,"outTra" => null],
                        ["amount" => $standardAmount,"subcat" => $value->vatSubCategoryID,"mastercat" => $value->vatMasterCategoryID,"inVat" => isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null,
                        "inTra" => isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null,
                        "outVat" => isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null,
                         "outTra" => isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null]
                    ];

                    foreach ($info as $key1 => $value1) {
                        $currencyConversionVAT = \Helper::currencyConversion($masterModel['companySystemID'], $masterData->supplierTransactionCurrencyID,$masterData->supplierTransactionCurrencyID, $value1['amount']);
                        if($value1['amount'] != 0)
                        {
                        $ledgerData['subCategoryID'] = $value1['subcat'];
                        $ledgerData['masterCategoryID'] = $value1['mastercat'];
                        $ledgerData['localAmount'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
                        $ledgerData['rptAmount'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
                        $ledgerData['transAmount'] = \Helper::roundValue($value1['amount']);
                        $ledgerData['inputVATGlAccountID'] = $value1['inVat'];
                        $ledgerData['inputVatTransferAccountID'] =  $value1['inTra'];
                        $ledgerData['outputVatTransferGLAccountID'] = $value1['outTra'];
                        $ledgerData['outputVatGLAccountID'] =  $value1['outVat'];
                        array_push($finalData, $ledgerData);
                        }
                    }

                }
                else
                {
                    $ledgerData['subCategoryID'] = $value->vatSubCategoryID;
                    $ledgerData['masterCategoryID'] = $value->vatMasterCategoryID;
                    $ledgerData['localAmount'] = $value->localVATAmount;
                    $ledgerData['rptAmount'] = $value->rptVATAmount;
                    $ledgerData['transAmount'] = $value->transVATAmount;
                    $ledgerData['inputVATGlAccountID'] = $value->subCatgeoryType == 3?null:isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null;
                    $ledgerData['inputVatTransferAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null;
                    $ledgerData['outputVatTransferGLAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null;
                    $ledgerData['outputVatGLAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null;
                    array_push($finalData, $ledgerData);

                }
      
            }

            $groupedData = collect($finalData)
                        ->groupBy('subCategoryID')
                        ->map(function ($group) {
                            $sumLocalAmount = $group->sum('localAmount');
                            $sumRptAmount = $group->sum('rptAmount');
                            $sumTransAmount = $group->sum('transAmount');
                            
                            $firstItem = $group->first();
                            $firstItem['localAmount'] = $sumLocalAmount;
                            $firstItem['rptAmount'] = $sumRptAmount;
                            $firstItem['transAmount'] = $sumTransAmount;
                            
                            return $firstItem;
                        })
                        ->values() 
                        ->toArray();

                        $finalData = $groupedData;

            $detailData = DirectInvoiceDetails::where('directInvoiceAutoID', $masterModel["autoID"])
                                            ->whereNotNull('vatSubCategoryID')
                                            ->join('erp_tax_vat_sub_categories', 'erp_directinvoicedetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                            ->get();

            foreach ($detailData as $key => $value) {

                $ledgerDetailsData['documentDetailID'] = $value->directInvoiceDetailsID;
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
                $ledgerDetailsData['localER'] = $value->localCurrencyER;
                $ledgerDetailsData['reportingER'] = $value->comRptCurrencyER;
                $ledgerDetailsData['taxableAmountLocal'] = $value->netAmountLocal;
                $ledgerDetailsData['taxableAmountReporting'] = $value->netAmountRpt;
                $ledgerDetailsData['localCurrencyID'] = $value->localCurrency;
                $ledgerDetailsData['rptCurrencyID'] = $value->comRptCurrency;


                if($value->subCatgeoryType == 1)
                {

                    $vatPortion = $value->exempt_vat_portion;
                    $exemptAmount =   ($vatPortion/100) * $value->VATAmount ;
                    $standardAmount = $value->VATAmount - $exemptAmount;

                    if (($masterData->documentType == 1) && ($masterData['retentionPercentage'] > 0)) {
                        $standardAmount = $standardAmount - $masterData['retentionVatAmount'];
                    }

                    $info = [
                        ["amount" => $exemptAmount,"subcat" => $exemptVatSub,"mastercat" => $exemptVatMain,"inVat" => null,"inTra" => null,"outVat" => null,"outTra" => null],
                        ["amount" => $standardAmount,"subcat" => $value->vatSubCategoryID,"mastercat" => $value->vatMasterCategoryID,"inVat" => isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null,
                        "inTra" => isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null,
                        "outVat" => isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null,
                         "outTra" => isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null]
                    ];


                    foreach ($info as $key1 => $value1) {
                        $currencyConversionVAT = \Helper::currencyConversion($masterModel['companySystemID'], $masterData->supplierTransactionCurrencyID,$masterData->supplierTransactionCurrencyID, $value1['amount']);
                        if($value1['amount'] != 0)
                        {
                            $ledgerDetailsData['vatSubCategoryID'] = $value1['subcat'];
                            $ledgerDetailsData['vatMasterCategoryID'] = $value1['mastercat'];
                            $ledgerDetailsData['VATAmount'] =  \Helper::roundValue($value1['amount']);
                            $ledgerDetailsData['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
                            $ledgerDetailsData['transAmount'] = \Helper::roundValue($value1['amount']);
                            $ledgerDetailsData['recoverabilityAmount'] = \Helper::roundValue($value1['amount']);
                            $ledgerDetailsData['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
                            $ledgerDetailsData['inputVATGlAccountID'] = $value1['inVat'];
                            $ledgerDetailsData['inputVatTransferAccountID'] =  $value1['inTra'];
                            $ledgerDetailsData['outputVatTransferGLAccountID'] = $value1['outTra'];
                            $ledgerDetailsData['outputVatGLAccountID'] =  $value1['outVat'];
                            array_push($finalDetailData, $ledgerDetailsData);
                        }

                    }
                }
                else
                {
                   
                    $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
                    $ledgerDetailsData['vatMasterCategoryID'] = $value->vatMasterCategoryID;
                    $ledgerDetailsData['VATAmount'] = $value->VATAmount;
                    $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal;
                    $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt;
                    $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount;
                    $ledgerDetailsData['transAmount'] = $value->VATAmount;
                    $ledgerDetailsData['inputVATGlAccountID'] = $value->subCatgeoryType == 3?null:isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null;
                    $ledgerDetailsData['inputVatTransferAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null;
                    $ledgerDetailsData['outputVatTransferGLAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null;
                    $ledgerDetailsData['outputVatGLAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null;
                    array_push($finalDetailData, $ledgerDetailsData);
                }
              

             
            }
        }
        else if($masterData->documentType == 3) {
            $details = SupplierInvoiceDirectItem::selectRaw('exempt_vat_portion,erp_tax_vat_sub_categories.subCatgeoryType,noQty, (VATAmount * noQty) as transVATAmount, (VATAmountLocal * noQty) as localVATAmount ,(VATAmountRpt * noQty) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrencyID, companyReportingCurrencyID as reportingCurrencyID, supplierDefaultCurrencyID as transCurrencyID, companyReportingER as reportingCurrencyER, localCurrencyER as localCurrencyER, supplierDefaultER as transCurrencyER')
                ->where('bookingSuppMasInvAutoID', $masterModel["autoID"])
                ->whereNotNull('vatSubCategoryID')
                ->join('erp_tax_vat_sub_categories', 'supplier_invoice_items.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                ->get();


            foreach ($details as $key => $value) {
                $subCategoryData = TaxVatCategories::with(['tax'])->find($value->vatSubCategoryID);

                if ($subCategoryData) {
                    $ledgerData['taxAuthorityAutoID'] = isset($subCategoryData->tax->authorityAutoID) ? $subCategoryData->tax->authorityAutoID : null;
                }

                $ledgerData['transER'] = $value->transCurrencyER;
                $ledgerData['localER'] = $value->localCurrencyER;
                $ledgerData['comRptER'] = $value->reportingCurrencyER;
                $ledgerData['localCurrencyID'] = $value->localCurrencyID;
                $ledgerData['rptCurrencyID'] = $value->reportingCurrencyID;
                $ledgerData['transCurrencyID'] = $value->transCurrencyID;

                if($value->subCatgeoryType == 1)
                {
                 
                    $vatPortion = $value->exempt_vat_portion;
                    $exemptAmount =   ($vatPortion/100) * $value->transVATAmount ;
                    $standardAmount = $value->transVATAmount - $exemptAmount;

                    if (($masterData['retentionPercentage'] > 0)) {
                        $standardAmount = $standardAmount - $masterData['retentionVatAmount'];
                    }

                    $info = [
                        ["amount" => $exemptAmount,"subcat" => $exemptVatSub,"mastercat" => $exemptVatMain,"inVat" => null,"inTra" => null,"outVat" => null,"outTra" => null],
                        ["amount" => $standardAmount,"subcat" => $value->vatSubCategoryID,"mastercat" => $value->vatMasterCategoryID,"inVat" => isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null,
                        "inTra" => isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null,
                        "outVat" => isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null,
                         "outTra" => isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null]
                    ];

                    foreach ($info as $key1 => $value1) {
                        $currencyConversionVAT = \Helper::currencyConversion($masterModel['companySystemID'], $masterData->supplierTransactionCurrencyID,$masterData->supplierTransactionCurrencyID, $value1['amount']);
                        if($value1['amount'] != 0)
                        {
                        $ledgerData['subCategoryID'] = $value1['subcat'];
                        $ledgerData['masterCategoryID'] = $value1['mastercat'];
                        $ledgerData['localAmount'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
                        $ledgerData['rptAmount'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
                        $ledgerData['transAmount'] = \Helper::roundValue($value1['amount']);
                        $ledgerData['inputVATGlAccountID'] = $value1['inVat'];
                        $ledgerData['inputVatTransferAccountID'] =  $value1['inTra'];
                        $ledgerData['outputVatTransferGLAccountID'] = $value1['outTra'];
                        $ledgerData['outputVatGLAccountID'] =  $value1['outVat'];
                        array_push($finalData, $ledgerData);
                        }
                    }
                }
                else
                {
               
                    $ledgerData['subCategoryID'] = $value->vatSubCategoryID;
                    $ledgerData['masterCategoryID'] = $value->vatMasterCategoryID;
                    $ledgerData['localAmount'] = $value->localVATAmount;
                    $ledgerData['rptAmount'] = $value->rptVATAmount;
                    $ledgerData['transAmount'] = $value->transVATAmount;
                    $ledgerData['inputVATGlAccountID'] = $value->subCatgeoryType == 3?null:isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null;
                    $ledgerData['inputVatTransferAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null;
                    $ledgerData['outputVatTransferGLAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null;
                    $ledgerData['outputVatGLAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null;

                    array_push($finalData, $ledgerData);
                    
                }


            }
            $groupedData = collect($finalData)
                        ->groupBy('subCategoryID')
                        ->map(function ($group) {
                            $sumLocalAmount = $group->sum('localAmount');
                            $sumRptAmount = $group->sum('rptAmount');
                            $sumTransAmount = $group->sum('transAmount');
                            
                            $firstItem = $group->first();
                            $firstItem['localAmount'] = $sumLocalAmount;
                            $firstItem['rptAmount'] = $sumRptAmount;
                            $firstItem['transAmount'] = $sumTransAmount;
                            
                            return $firstItem;
                        })
                        ->values() 
                        ->toArray();

                        $finalData = $groupedData;
            $detailData = SupplierInvoiceDirectItem::where('bookingSuppMasInvAutoID', $masterModel["autoID"])
                ->join('erp_tax_vat_sub_categories', 'supplier_invoice_items.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                ->whereNotNull('vatSubCategoryID')
                ->get();
            
            foreach ($detailData as $key => $value) {

                
                $ledgerDetailsData['documentDetailID'] = $value->id;
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
                $ledgerDetailsData['localER'] = $value->localCurrencyER;
                $ledgerDetailsData['reportingER'] = $value->companyReportingER;
                $ledgerDetailsData['taxableAmountLocal'] = $value->netAmount/$value->localCurrencyER;
                $ledgerDetailsData['taxableAmountReporting'] = $value->netAmount/$value->companyReportingER;
                $ledgerDetailsData['localCurrencyID'] = $value->localCurrencyID;
                $ledgerDetailsData['rptCurrencyID'] = $value->companyReportingCurrencyID;


                if($value->subCatgeoryType == 1)
                {
                    $vatPortion = $value->exempt_vat_portion;
                    $exemptAmount =   ($vatPortion/100) * $value->VATAmount * $value->noQty;
                    $standardAmount = ($value->VATAmount* $value->noQty) - $exemptAmount;

                    if (($masterData['retentionPercentage'] > 0)) {
                        $standardAmount = $standardAmount - $masterData['retentionVatAmount'];
                    }

                    $info = [
                        ["amount" => $exemptAmount,"subcat" => $exemptVatSub,"mastercat" => $exemptVatMain,"inVat" => null,"inTra" => null,"outVat" => null,"outTra" => null],
                        ["amount" => $standardAmount,"subcat" => $value->vatSubCategoryID,"mastercat" => $value->vatMasterCategoryID,"inVat" => isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null,
                        "inTra" => isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null,
                        "outVat" => isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null,
                         "outTra" => isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null]
                    ];

                    foreach ($info as $key1 => $value1) {
                        $currencyConversionVAT = \Helper::currencyConversion($masterModel['companySystemID'], $masterData->supplierTransactionCurrencyID,$masterData->supplierTransactionCurrencyID, $value1['amount']);
                        if($value1['amount'] != 0)
                        {
                            $ledgerDetailsData['vatSubCategoryID'] = $value1['subcat'];
                            $ledgerDetailsData['vatMasterCategoryID'] = $value1['mastercat'];
                            $ledgerDetailsData['VATAmount'] = \Helper::roundValue($value1['amount']);
                            $ledgerDetailsData['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
                            $ledgerDetailsData['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
                            $ledgerDetailsData['recoverabilityAmount'] =\Helper::roundValue($value1['amount']);
                            $ledgerDetailsData['inputVATGlAccountID'] = $value1['inVat'];
                            $ledgerDetailsData['inputVatTransferAccountID'] =  $value1['inTra'];
                            $ledgerDetailsData['outputVatTransferGLAccountID'] = $value1['outTra'];
                            $ledgerDetailsData['outputVatGLAccountID'] =  $value1['outVat'];
                            array_push($finalDetailData, $ledgerDetailsData);
                        }
                  
                    }
                }
                else
                {
                   

                    $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
                    $ledgerDetailsData['vatMasterCategoryID'] = $value->vatMasterCategoryID;
                    $ledgerDetailsData['VATAmount'] = $value->VATAmount * $value->noQty;
                    $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal * $value->noQty;
                    $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt * $value->noQty;
                    $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount * $value->noQty;
                    $ledgerDetailsData['inputVATGlAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null;
                    $ledgerDetailsData['inputVatTransferAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null;
                    $ledgerDetailsData['outputVatTransferGLAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null;
                    $ledgerDetailsData['outputVatGLAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null;
                    array_push($finalDetailData, $ledgerDetailsData); 
                }

            }
        }
        else {
            $details = SupplierInvoiceItemDetail::selectRaw('erp_grvdetails.noQty,erp_grvdetails.VATAmount as grvVATAmount,erp_bookinvsupp_item_det.exempt_vat_portion,erp_tax_vat_sub_categories.subCatgeoryType,(erp_bookinvsupp_item_det.VATAmount) as transVATAmount,(erp_bookinvsupp_item_det.VATAmountLocal) as localVATAmount ,(erp_bookinvsupp_item_det.VATAmountRpt) as rptVATAmount, erp_bookinvsupp_item_det.vatMasterCategoryID, erp_bookinvsupp_item_det.vatSubCategoryID, erp_bookinvsupp_item_det.localCurrencyID as localCurrencyID,erp_bookinvsupp_item_det.companyReportingCurrencyID as reportingCurrencyID,supplierTransactionCurrencyID as transCurrencyID,erp_bookinvsupp_item_det.companyReportingER as reportingCurrencyER,erp_bookinvsupp_item_det.localCurrencyER as localCurrencyER,supplierTransactionCurrencyER as transCurrencyER, erp_bookinvsupp_item_det.totTransactionAmount, erp_bookinvsupp_item_det.totLocalAmount, erp_bookinvsupp_item_det.totRptAmount')
                                    ->where('bookingSuppMasInvAutoID', $masterModel["autoID"])
                                    ->whereNotNull('erp_bookinvsupp_item_det.vatSubCategoryID')
                                    ->join('erp_tax_vat_sub_categories', 'erp_bookinvsupp_item_det.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                    ->join('erp_grvdetails', 'erp_bookinvsupp_item_det.grvDetailsID', '=', 'erp_grvdetails.grvDetailsID')
                                    ->get();
            

            foreach ($details as $key => $value) {
                $ledgerData['documentTransAmount'] = $value->totTransactionAmount;
                $ledgerData['documentLocalAmount'] = $value->totLocalAmount;
                $ledgerData['documentReportingAmount'] = $value->totRptAmount;

                $subCategoryData = TaxVatCategories::with(['tax'])->find($value->vatSubCategoryID);

                $ledgerData['transER'] = $value->transCurrencyER;
                $ledgerData['localER'] = $value->localCurrencyER;
                $ledgerData['comRptER'] = $value->reportingCurrencyER;
                $ledgerData['localCurrencyID'] = $value->localCurrencyID;
                $ledgerData['rptCurrencyID'] = $value->reportingCurrencyID;
                $ledgerData['transCurrencyID'] = $value->transCurrencyID;


                if ($subCategoryData) {
                    $ledgerData['taxAuthorityAutoID'] = isset($subCategoryData->tax->authorityAutoID) ? $subCategoryData->tax->authorityAutoID : null;
                }

                if($value->subCatgeoryType == 1)
                {
                    
                    $normalVAT = $value->grvVATAmount - ($value->grvVATAmount * ($value->exempt_vat_portion /100));
                    $exemptAmount = (($value->grvVATAmount - $normalVAT) * $value->noQty);
                    
                    $standardAmount =  ($normalVAT * $value->noQty);
    
    
                    $info = [
                        ["amount" => $exemptAmount,"subcat" => $exemptVatSub,"mastercat" => $exemptVatMain,"inVat" => null,"inTra" => null,"outVat" => null,"outTra" => null],
                        ["amount" => $standardAmount,"subcat" => $value->vatSubCategoryID,"mastercat" => $value->vatMasterCategoryID,"inVat" => isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null,
                        "inTra" => isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null,
                        "outVat" => isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null,
                         "outTra" => isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null]
                    ];
    
                    foreach ($info as $key1 => $value1) {
                        $localVATAmount = CurrencyConversionService::localAndReportingConversionByER($masterData->supplierTransactionCurrencyID, $value->localCurrencyID, $value1['amount'], $value->localCurrencyER);
                    
                        $reportingVATAmount = CurrencyConversionService::localAndReportingConversionByER($masterData->supplierTransactionCurrencyID, $value->reportingCurrencyID, $value1['amount'], $value->reportingCurrencyER);

                        if($value1['amount'] != 0)
                        {
                            $ledgerData['subCategoryID'] = $value1['subcat'];
                            $ledgerData['masterCategoryID'] = $value1['mastercat'];
                            $ledgerData['localAmount'] = \Helper::roundValue($localVATAmount);
                            $ledgerData['rptAmount'] = \Helper::roundValue($reportingVATAmount);
                            $ledgerData['transAmount'] = \Helper::roundValue($value1['amount']);
                            $ledgerData['inputVATGlAccountID'] = $value1['inVat'];
                            $ledgerData['inputVatTransferAccountID'] =  $value1['inTra'];
                            $ledgerData['outputVatTransferGLAccountID'] = $value1['outTra'];
                            $ledgerData['outputVatGLAccountID'] =  $value1['outVat'];
                            array_push($finalData, $ledgerData);
                        }
                    }
    

                }
                else
                {

                    $ledgerData['subCategoryID'] = $value->vatSubCategoryID;
                    $ledgerData['masterCategoryID'] = $value->vatMasterCategoryID;
                    $ledgerData['localAmount'] = $value->localVATAmount;
                    $ledgerData['rptAmount'] = $value->rptVATAmount;
                    $ledgerData['transAmount'] = $value->transVATAmount;
                    $ledgerData['inputVATGlAccountID'] = $value->subCatgeoryType == 3?null:isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null;
                    $ledgerData['inputVatTransferAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null;
                    $ledgerData['outputVatTransferGLAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null;
                    $ledgerData['outputVatGLAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null;
                    array_push($finalData, $ledgerData);
          
                }
            }
            $groupedData = collect($finalData)
                        ->groupBy('subCategoryID')
                        ->map(function ($group) {
                            $sumLocalAmount = $group->sum('localAmount');
                            $sumRptAmount = $group->sum('rptAmount');
                            $sumTransAmount = $group->sum('transAmount');

                            $sumLocalAmountTotal = $group->sum('documentLocalAmount');
                            $sumRptAmountTotal = $group->sum('documentReportingAmount');
                            $sumTransAmountTotal = $group->sum('documentTransAmount');
                            
                            $firstItem = $group->first();
                            $firstItem['localAmount'] = $sumLocalAmount;
                            $firstItem['rptAmount'] = $sumRptAmount;
                            $firstItem['transAmount'] = $sumTransAmount;

                            $firstItem['documentLocalAmount'] = $sumLocalAmountTotal;
                            $firstItem['documentReportingAmount'] = $sumRptAmountTotal;
                            $firstItem['documentTransAmount'] = $sumTransAmountTotal;

                            $firstItem['localER'] = \Helper::roundValue($sumTransAmountTotal / $sumLocalAmountTotal);
                            $firstItem['comRptER'] = \Helper::roundValue($sumTransAmountTotal / $sumRptAmountTotal);
                            
                            return $firstItem;
                        })
                        ->values() 
                        ->toArray();
            $finalData = $groupedData;
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


                $ledgerDetailsData['rcmApplicableYN'] = TaxService::isGRVRCMActivation($value->grvAutoID);
                $isRCMApplicable =  (boolean) $ledgerDetailsData['rcmApplicableYN'];
                $ledgerDetailsData['documentDetailID'] = $value->id; 
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
           
             
                $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount;
                $ledgerDetailsData['localER'] = $value->localCurrencyER;
                $ledgerDetailsData['reportingER'] = $value->companyReportingER;
                $ledgerDetailsData['localCurrencyID'] = $value->localCurrencyID;
                $ledgerDetailsData['rptCurrencyID'] = $value->companyReportingCurrencyID;
                $ledgerDetailsData['exempt_vat_portion'] = $value->exempt_vat_portion;
                $ledgerDetailsData['logisticYN'] = ($value->logisticID > 0) ? 1 : 0;
                $ledgerDetailsData['addVATonPO'] = (isset($value->logistic_detail->addVatOnPO) ? $value->logistic_detail->addVatOnPO : 0) ? 1 : 0;

                if($value->subCatgeoryType == 1)
                {
          
                    $normalVAT = $value->grv_detail->VATAmount - ($value->grv_detail->VATAmount * ($value->exempt_vat_portion /100));
                    $exemptAmount = (($value->grv_detail->VATAmount - $normalVAT) * $value->grv_detail->noQty);
                    
                    $standardAmount =  ($normalVAT * $value->grv_detail->noQty);
                    $totalAmount = $standardAmount + $exemptAmount;
                    
                    $expenseCOA = TaxVatCategories::with(['tax'])->where('subCatgeoryType', 3)->whereHas('tax', function ($query) use ($masterData) {
                        $query->where('companySystemID', $masterData->companySystemID);
                    })->where('isActive', 1)->first();
                    
                    $isCost = $expenseCOA && $expenseCOA->expenseGL === null;
                    $texableAmount = 0;
                    $texableAmountLocal = 0;
                    $texableAmountRpt = 0;
                    $info = [
                        ["type" => 1 ,"amount" => $exemptAmount,"subcat" => $exemptVatSub,"mastercat" => $exemptVatMain,"inVat" => null,"inTra" => null,"outVat" => null,"outTra" => null],
                        ["type" => 2 ,"amount" => $standardAmount,"subcat" => $value->vatSubCategoryID,"mastercat" => $value->vatMasterCategoryID,"inVat" => isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null,
                        "inTra" => isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null,
                        "outVat" => isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null,
                         "outTra" => isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null]
                    ];
    
                    foreach ($info as $key1 => $value1) {
                        $localVATAmountDetail = CurrencyConversionService::localAndReportingConversionByER($masterData->supplierTransactionCurrencyID, $value->localCurrencyID, $value1['amount'], $value->localCurrencyER);
                    
                        $reportingVATAmountDetail = CurrencyConversionService::localAndReportingConversionByER($masterData->supplierTransactionCurrencyID, $value->companyReportingCurrencyID, $value1['amount'], $value->companyReportingER);

                        if($value1['amount'] != 0)
                        {

                            $amountToDeduct = $isCost ? (($value1['type'] == 1) ? $standardAmount : $totalAmount) : $totalAmount;
                            $texableAmount = $value->totTransactionAmount - \Helper::roundValue($amountToDeduct);


                            $standardAmountLocal = CurrencyConversionService::localAndReportingConversionByER($masterData->supplierTransactionCurrencyID, $value->localCurrencyID, $standardAmount, $value->localCurrencyER);
                            $totalAmountLocal = CurrencyConversionService::localAndReportingConversionByER($masterData->supplierTransactionCurrencyID, $value->localCurrencyID, $totalAmount, $value->localCurrencyER);

                            $amountToDeductLocal = $isCost ? (($value1['type'] == 1) ? $standardAmountLocal : $totalAmountLocal) : $totalAmountLocal;
                            $texableAmountLocal = $value->totTransactionAmount - \Helper::roundValue($amountToDeductLocal);


                            $standardAmountRpt = CurrencyConversionService::localAndReportingConversionByER($masterData->supplierTransactionCurrencyID, $value->localCurrencyID, $standardAmount, $value->companyReportingER);
                            $totalAmountRpt = CurrencyConversionService::localAndReportingConversionByER($masterData->supplierTransactionCurrencyID, $value->localCurrencyID, $totalAmount, $value->companyReportingER);

                            $amountToDeductRpt = $isCost ? (($value1['type'] == 1) ? $standardAmountRpt : $standardAmountRpt) : $totalAmountRpt;
                            $texableAmountRpt = $value->totTransactionAmount - \Helper::roundValue($amountToDeductRpt);

                            
                            $ledgerDetailsData['vatSubCategoryID'] = $value1['subcat'];
                            $ledgerDetailsData['vatMasterCategoryID'] = $value1['mastercat'];
                            $ledgerDetailsData['VATAmountLocal'] = \Helper::roundValue($localVATAmountDetail);
                            $ledgerDetailsData['VATAmountRpt'] = \Helper::roundValue($reportingVATAmountDetail);
                            $ledgerDetailsData['VATAmount'] = \Helper::roundValue($value1['amount']);
                            $ledgerDetailsData['taxableAmount'] = $texableAmount;
                            $ledgerDetailsData['taxableAmountLocal'] = ($isRCMApplicable) ? $value->totLocalAmount  : $texableAmountLocal;
                            $ledgerDetailsData['taxableAmountReporting'] = ($isRCMApplicable) ? $value->totRptAmount : $texableAmountRpt;
    
                            $ledgerDetailsData['inputVATGlAccountID'] = $value1['inVat'];
                            $ledgerDetailsData['inputVatTransferAccountID'] =  $value1['inTra'];
                            $ledgerDetailsData['outputVatTransferGLAccountID'] = $value1['outTra'];
                            $ledgerDetailsData['outputVatGLAccountID'] =  $value1['outVat'];
                            array_push($finalDetailData, $ledgerDetailsData); 
                        }
  
                    }
                }
                else
                {
                        $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
                        $ledgerDetailsData['vatMasterCategoryID'] = $value->vatMasterCategoryID;
                        $ledgerDetailsData['VATAmount'] = $value->VATAmount;
                        $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal;
                        $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt;
                        $ledgerDetailsData['taxableAmount'] = ($value->totTransactionAmount - $value->VATAmount);
                        $ledgerDetailsData['taxableAmountLocal'] = ($isRCMApplicable) ? $value->totLocalAmount  : ($value->totLocalAmount - $value->VATAmountLocal);
                        $ledgerDetailsData['taxableAmountReporting'] = ($isRCMApplicable) ? $value->totRptAmount : ($value->totRptAmount - $value->VATAmountRpt);
                        $ledgerDetailsData['inputVATGlAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null;
                        $ledgerDetailsData['inputVatTransferAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null;
                        $ledgerDetailsData['outputVatTransferGLAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null;
                        $ledgerDetailsData['outputVatGLAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null;
                        array_push($finalDetailData, $ledgerDetailsData); 
                }


            }
        }

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'finalDetailData' => $finalDetailData]];
	}
}
