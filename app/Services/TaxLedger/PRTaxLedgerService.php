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

        $details = PurchaseReturnDetails::selectRaw('exempt_vat_portion,erp_tax_vat_sub_categories.subCatgeoryType,(VATAmount*noQty) as transVATAmount,(VATAmountLocal*noQty) as localVATAmount ,(VATAmountRpt*noQty) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrencyID,companyReportingCurrencyID as reportingCurrencyID,supplierTransactionCurrencyID,supplierTransactionER,companyReportingER,localCurrencyER')
                                ->where('purhaseReturnAutoID', $masterModel["autoID"])
                                ->whereNotNull('vatSubCategoryID')
                                ->join('erp_tax_vat_sub_categories', 'erp_purchasereturndetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                ->get();

        $master = PurchaseReturn::with(['finance_period_by', 'supplier_by', 'details' => function ($query) {
            $query->selectRaw("SUM(noQty * GRVcostPerUnitLocalCur) as localAmount, SUM(noQty * GRVcostPerUnitComRptCur) as rptAmount,SUM(GRVcostPerUnitSupTransCur*noQty) as transAmount,purhaseReturnAutoID, SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount, supplierTransactionCurrencyID, supplierTransactionER, localCurrencyID, localCurrencyER, companyReportingCurrencyID, companyReportingER");
        }])->find($masterModel["autoID"]);

        $masterDocumentDate = date('Y-m-d H:i:s');
        if (isset($master->finance_period_by->isActive) && $master->finance_period_by->isActive == -1) {
            $masterDocumentDate = $master->purchaseReturnDate;
        }

        $exampteVat = TaxVatCategories::where('subCatgeoryType',3)->where('isActive',1)->first();
        $exemptVatSub = $exampteVat?$exampteVat->taxVatSubCategoriesAutoID:NULL;
        $exemptVatMain = $exampteVat?$exampteVat->mainCategory:NULL;

        $standardRatedSupply = TaxVatCategories::where('subCatgeoryType',1)->where('isActive',1)->first();
        $standardRatedSupplyID = $standardRatedSupply?$standardRatedSupply->taxVatSubCategoriesAutoID:null;

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

                $ledgerData['transER'] = $value->supplierTransactionER;
                $ledgerData['localER'] = $value->localCurrencyER;
                $ledgerData['comRptER'] = $value->companyReportingER;
                $ledgerData['localCurrencyID'] = $value->localCurrencyID;
                $ledgerData['rptCurrencyID'] = $value->reportingCurrencyID;
                $ledgerData['transCurrencyID'] = $value->supplierTransactionCurrencyID;

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

        $detailData = PurchaseReturnDetails::where('purhaseReturnAutoID', $masterModel["autoID"])
                                            ->join('erp_tax_vat_sub_categories', 'erp_purchasereturndetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                            ->whereNotNull('vatSubCategoryID')
                                            ->get();

        foreach ($detailData as $key => $value) {
            $ledgerDetailsData['documentDetailID'] = $value->purhasereturnDetailID;
            $ledgerDetailsData['serviceLineSystemID'] = $master->serviceLineSystemID;
            $ledgerDetailsData['documentDate'] = $masterDocumentDate;
            $ledgerDetailsData['postedDate'] = date('Y-m-d H:i:s');
            $ledgerDetailsData['documentNumber'] = $master->purchaseReturnCode;
            $ledgerDetailsData['chartOfAccountSystemID'] = $value->financeGLcodePLSystemID;
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
            $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount * $value->noQty;
            $ledgerDetailsData['localER'] = $value->localCurrencyER;
            $ledgerDetailsData['reportingER'] = $value->companyReportingER;
            $ledgerDetailsData['taxableAmountLocal'] = ($value->GRVcostPerUnitLocalCur * $value->noQty);
            $ledgerDetailsData['taxableAmountReporting'] = ($value->GRVcostPerUnitComRptCur * $value->noQty);
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
                $exemptAmount =   ($vatPortion/100) * $value->VATAmount * $value->noQty ;
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
                    if($value1['amount'] != 0)
                    {   
                        $ledgerDetailsData['vatSubCategoryID'] = $value1['subcat'];
                        $ledgerDetailsData['vatMasterCategoryID'] = $value1['mastercat'];
                        $ledgerDetailsData['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
                        $ledgerDetailsData['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
                        $ledgerDetailsData['VATAmount'] = \Helper::roundValue($value1['amount']);
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
                    $ledgerDetailsData['inputVATGlAccountID'] = $value->subCatgeoryType == 3?null:isset($taxLedgerData['inputVATGlAccountID']) ? $taxLedgerData['inputVATGlAccountID'] : null;
                    $ledgerDetailsData['inputVatTransferAccountID'] = $value->subCatgeoryType == 3?null:isset($taxLedgerData['inputVatTransferAccountID']) ? $taxLedgerData['inputVatTransferAccountID'] : null;
                    $ledgerDetailsData['outputVatTransferGLAccountID'] = $value->subCatgeoryType == 3?null:isset($taxLedgerData['outputVatTransferGLAccountID']) ? $taxLedgerData['outputVatTransferGLAccountID'] : null;
                    $ledgerDetailsData['outputVatGLAccountID'] = $value->subCatgeoryType == 3?null:isset($taxLedgerData['outputVatGLAccountID']) ? $taxLedgerData['outputVatGLAccountID'] : null;
                    array_push($finalDetailData, $ledgerDetailsData); 
            }


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