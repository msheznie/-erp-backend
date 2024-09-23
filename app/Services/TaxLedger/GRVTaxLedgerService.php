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

class GRVTaxLedgerService
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

        $details = GRVDetails::selectRaw('exempt_vat_portion,erp_tax_vat_sub_categories.subCatgeoryType,SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionER,companyReportingCurrencyID,companyReportingER,localCurrencyID,localCurrencyER')
                                ->where('grvAutoID', $masterModel["autoID"])
                                ->whereNotNull('vatSubCategoryID')
                                ->join('erp_tax_vat_sub_categories', 'erp_grvdetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                ->groupBy('erp_tax_vat_sub_categories.subCatgeoryType')
                                ->get();

        $master = GRVMaster::with(['financeperiod_by', 'supplier_by'])->find($masterModel["autoID"]);

        $exampteVat = TaxVatCategories::where('subCatgeoryType',3)->where('isActive',1)->first();
        $exemptVatSub = $exampteVat?$exampteVat->taxVatSubCategoriesAutoID:NULL;
        $exemptVatMain = $exampteVat?$exampteVat->mainCategory:NULL;

        $standardRatedSupply = TaxVatCategories::where('subCatgeoryType',1)->where('isActive',1)->first();
        $standardRatedSupplyID = $standardRatedSupply?$standardRatedSupply->taxVatSubCategoriesAutoID:null;


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
                $ledgerData['transER'] = $value->supplierTransactionER;
                $ledgerData['localER'] = $value->localCurrencyER;
                $ledgerData['comRptER'] = $value->companyReportingER;
                $ledgerData['localCurrencyID'] = $value->localCurrencyID;
                $ledgerData['rptCurrencyID'] = $value->companyReportingCurrencyID;
                $ledgerData['transCurrencyID'] = $value->supplierTransactionCurrencyID;
                $ledgerData['rcmApplicableYN'] = TaxService::isGRVRCMActivation($masterModel["autoID"]);

            if($value->subCatgeoryType == 1)
            {

                $vatPortion = $value->exempt_vat_portion;
                $exemptAmount =   ($vatPortion/100) * $value->transVATAmount ;
                $standardAmount = $value->transVATAmount - $exemptAmount;


                $info = [
                    ["amount" => $exemptAmount,"subcat" => $exemptVatSub,"mastercat" => $exemptVatMain,"inVat" => null,"inTra" => null,"outVat" => null,"outTra" => null],
                    ["amount" => $standardAmount,"subcat" => $value->vatSubCategoryID,"mastercat" => $value->vatMasterCategoryID,"inVat" => isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null,
                    "inTra" => isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null,
                    "outVat" => isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null,
                     "outTra" => isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null]
                ];

                foreach ($info as $key1 => $value1) {
                    $currencyConversionVAT = \Helper::currencyConversion($masterModel['companySystemID'], $master->supplierTransactionCurrencyID,$master->supplierTransactionCurrencyID, $value1['amount']);

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

        $detailData = GRVDetails::where('grvAutoID', $masterModel["autoID"])
                                ->join('erp_tax_vat_sub_categories', 'erp_grvdetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                ->whereNotNull('vatSubCategoryID')
                                ->get();

        $ledgerDetailsData['rcmApplicableYN'] = TaxService::isGRVRCMActivation($masterModel["autoID"]);

        foreach ($detailData as $key => $value) {
            $ledgerDetailsData['documentDetailID'] = $value->grvDetailsID;
            $ledgerDetailsData['serviceLineSystemID'] = $master->serviceLineSystemID;
            $ledgerDetailsData['documentDate'] = $masterDocumentDate;
            $ledgerDetailsData['postedDate'] = date('Y-m-d H:i:s');
            $ledgerDetailsData['documentNumber'] = $master->grvPrimaryCode;
            $ledgerDetailsData['chartOfAccountSystemID'] = $value->financeGLcodePLSystemID;
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
            $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount * $value->noQty;
            $ledgerDetailsData['localER'] = $value->localCurrencyER;
            $ledgerDetailsData['reportingER'] = $value->companyReportingER;
            $ledgerDetailsData['localCurrencyID'] = $value->localCurrencyID;
            $ledgerDetailsData['rptCurrencyID'] = $value->companyReportingCurrencyID;
            $ledgerDetailsData['exempt_vat_portion'] = $value->exempt_vat_portion;

            $chartOfAccountData = ChartOfAccount::find($value->financeGLcodePLSystemID);

            if ($chartOfAccountData) {
                $ledgerDetailsData['accountCode'] = $chartOfAccountData->AccountCode;
                $ledgerDetailsData['accountDescription'] = $chartOfAccountData->AccountDescription;
            }
            
            if($value->subCatgeoryType == 1)
            {
                
                $vatPortion = $value->exempt_vat_portion;
                $exemptAmount =   ($vatPortion/100) *  $value->VATAmount * $value->noQty;
                $standardAmount = $value->VATAmount * $value->noQty - $exemptAmount;


                $info = [
                    ["amount" => $exemptAmount,"subcat" => $exemptVatSub,"mastercat" => $exemptVatMain,"inVat" => null,"inTra" => null,"outVat" => null,"outTra" => null],
                    ["amount" => $standardAmount,"subcat" => $value->vatSubCategoryID,"mastercat" => $value->vatMasterCategoryID,"inVat" => isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null,
                    "inTra" => isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null,
                    "outVat" => isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null,
                     "outTra" => isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null]
                ];

                foreach ($info as $key1 => $value1) {
                    $currencyConversionVAT = \Helper::currencyConversion($masterModel['companySystemID'], $master->supplierTransactionCurrencyID,$master->supplierTransactionCurrencyID, $value1['amount']);

                    $ledgerDetailsData['vatSubCategoryID'] = $value1['subcat'];
                    $ledgerDetailsData['vatMasterCategoryID'] = $value1['mastercat'];
                    $ledgerDetailsData['VATAmount'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
                    $ledgerDetailsData['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
                    $ledgerDetailsData['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
                    $ledgerDetailsData['inputVATGlAccountID'] = $value1['inVat'];
                    $ledgerDetailsData['inputVatTransferAccountID'] =  $value1['inTra'];
                    $ledgerDetailsData['outputVatTransferGLAccountID'] = $value1['outTra'];
                    $ledgerDetailsData['outputVatGLAccountID'] =  $value1['outVat'];

                    $subCategory = TaxVatCategories::find($value->vatSubCategoryID);
                    if($subCategory->subCatgeoryType != 2) {
                        if($value->exempt_vat_portion != 0) {
                            $taxableAmountLocal = (($value->landingCost_LocalCur * $value->noQty) -  ($ledgerDetailsData['VATAmountLocal'] / 100) * $value->exempt_vat_portion);
                            $taxableAmountReporting = (($value->landingCost_RptCur * $value->noQty) -  ($ledgerDetailsData['VATAmountRpt'] / 100) * $value->exempt_vat_portion);
                            $taxableAmount =  ($value->landingCost_TransCur * $value->noQty) - (($ledgerDetailsData['VATAmount'] / 100) * $value->exempt_vat_portion);
        
                        }else {
                            $taxableAmountLocal =  ($subCategory->subCatgeoryType == 3) ? (($value->landingCost_LocalCur * $value->noQty) -  $ledgerDetailsData['VATAmountLocal']) : $value->landingCost_LocalCur * $value->noQty;
                            $taxableAmountReporting =  ($subCategory->subCatgeoryType == 3) ? (($value->landingCost_RptCur * $value->noQty)  - $ledgerDetailsData['VATAmountRpt']) : $value->landingCost_RptCur * $value->noQty;
                            $taxableAmount =  ($subCategory->subCatgeoryType == 3) ? (($value->landingCost_TransCur * $value->noQty)  - $ledgerDetailsData['VATAmount']) : $value->landingCost_TransCur * $value->noQty;
        
                        }
                    }else {
                        $taxableAmountLocal =  $value->landingCost_LocalCur * $value->noQty;
                        $taxableAmountReporting =  $value->landingCost_RptCur * $value->noQty;
                        $taxableAmount =  $value->landingCost_TransCur * $value->noQty;
        
                    }    
        
                    $ledgerDetailsData['taxableAmount'] = $taxableAmount;
                    $ledgerDetailsData['taxableAmountLocal'] = $taxableAmountLocal;
                    $ledgerDetailsData['taxableAmountReporting'] = $taxableAmountReporting;

                    array_push($finalDetailData, $ledgerDetailsData);
                }



            }
            else
            {
                    $ledgerDetailsData['vatSubCategoryID'] = $value->vatSubCategoryID;
                    $ledgerDetailsData['vatMasterCategoryID'] = $value->vatMasterCategoryID;
                    $ledgerDetailsData['VATAmount'] = $value->VATAmount * $value->noQty;
                    $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt * $value->noQty;
                    $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal * $value->noQty;
                    $ledgerDetailsData['inputVATGlAccountID'] = $value->subCatgeoryType == 3?null:isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null;
                    $ledgerDetailsData['inputVatTransferAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null;
                    $ledgerDetailsData['outputVatTransferGLAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null;
                    $ledgerDetailsData['outputVatGLAccountID'] =  $value->subCatgeoryType == 3?null:isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null;
                   
                    $subCategory = TaxVatCategories::find($value->vatSubCategoryID);
                    if($subCategory->subCatgeoryType != 2) {
                        if($value->exempt_vat_portion != 0) {
                            $taxableAmountLocal = (($value->landingCost_LocalCur * $value->noQty) -  ($ledgerDetailsData['VATAmountLocal'] / 100) * $value->exempt_vat_portion);
                            $taxableAmountReporting = (($value->landingCost_RptCur * $value->noQty) -  ($ledgerDetailsData['VATAmountRpt'] / 100) * $value->exempt_vat_portion);
                            $taxableAmount =  ($value->landingCost_TransCur * $value->noQty) - (($ledgerDetailsData['VATAmount'] / 100) * $value->exempt_vat_portion);
        
                        }else {
                            $taxableAmountLocal =  ($subCategory->subCatgeoryType == 3) ? (($value->landingCost_LocalCur * $value->noQty) -  $ledgerDetailsData['VATAmountLocal']) : $value->landingCost_LocalCur * $value->noQty;
                            $taxableAmountReporting =  ($subCategory->subCatgeoryType == 3) ? (($value->landingCost_RptCur * $value->noQty)  - $ledgerDetailsData['VATAmountRpt']) : $value->landingCost_RptCur * $value->noQty;
                            $taxableAmount =  ($subCategory->subCatgeoryType == 3) ? (($value->landingCost_TransCur * $value->noQty)  - $ledgerDetailsData['VATAmount']) : $value->landingCost_TransCur * $value->noQty;
        
                        }
                    }else {
                        $taxableAmountLocal =  $value->landingCost_LocalCur * $value->noQty;
                        $taxableAmountReporting =  $value->landingCost_RptCur * $value->noQty;
                        $taxableAmount =  $value->landingCost_TransCur * $value->noQty;
        
                    }    
        
                    $ledgerDetailsData['taxableAmount'] = $taxableAmount;
                    $ledgerDetailsData['taxableAmountLocal'] = $taxableAmountLocal;
                    $ledgerDetailsData['taxableAmountReporting'] = $taxableAmountReporting;
                   
                    array_push($finalDetailData, $ledgerDetailsData);
            }

        }

        $logisticData = PoAdvancePayment::with(['category_by' => function($query) {
                                            $query->with(['item_by']);
                                        }, 'supplier_by'])->where('grvAutoID', $masterModel["autoID"])
                                        ->join('erp_tax_vat_sub_categories', 'erp_purchaseorderadvpayment.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
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


        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'finalDetailData' => $finalDetailData]];
	}
}