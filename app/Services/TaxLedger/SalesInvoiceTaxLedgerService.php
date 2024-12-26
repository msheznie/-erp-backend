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

class SalesInvoiceTaxLedgerService
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

            $details = CustomerInvoiceItemDetails::selectRaw('erp_tax_vat_sub_categories.subCatgeoryType,SUM(VATAmount*qtyIssuedDefaultMeasure) as transVATAmount,SUM(VATAmountLocal*qtyIssuedDefaultMeasure) as localVATAmount ,SUM(VATAmountRpt*qtyIssuedDefaultMeasure) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrencyID, localCurrencyER, reportingCurrencyID, reportingCurrencyER, sellingCurrencyID, sellingCurrencyER')
                                    ->where('custInvoiceDirectAutoID', $masterModel["autoID"])
                                    ->whereNotNull('vatSubCategoryID')
                                    ->join('erp_tax_vat_sub_categories', 'erp_customerinvoiceitemdetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
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
                $ledgerData['transER'] = $value->sellingCurrencyER;
                $ledgerData['localER'] = $value->localCurrencyER;
                $ledgerData['comRptER'] = $value->reportingCurrencyER;
                $ledgerData['localCurrencyID'] = $value->localCurrencyID;
                $ledgerData['rptCurrencyID'] = $value->reportingCurrencyID;
                $ledgerData['transCurrencyID'] = $value->sellingCurrencyID;

                if ($ledgerData['transAmount'] != 0) {
                    array_push($finalData, $ledgerData);
                }
            }


            $detailData = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $masterModel["autoID"])
                                            ->join('erp_tax_vat_sub_categories', 'erp_customerinvoiceitemdetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
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

                $vatCategories = TaxVatCategories::where('taxVatSubCategoriesAutoID', $ledgerDetailsData["vatSubCategoryID"])
                                                ->where('mainCategory', $ledgerDetailsData["vatMasterCategoryID"])
                                                ->first();



                $MyLocalAmount = 0;
                $MyRptAmount = 0;
                $totalAmount = 0;
                if ($masterData->isPerforma == 0) {
                    $unitCostForCalculation = ($vatCategories && $vatCategories->applicableOn == 1) ? $value->salesPrice : $value->sellingCostAfterMargin;
                    $totalAmount = $unitCostForCalculation * $value->qtyIssuedDefaultMeasure;
                    $convertAmount = self::calculateLocalAndRpt($totalAmount, $masterData);

                    $MyLocalAmount = $convertAmount['localAmount'];
                    $MyRptAmount = $convertAmount['rptAmount'];
                }

                $chartOfAccountData = ChartOfAccount::find($value->financeGLcodeRevenueSystemID);

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
                $ledgerDetailsData['VATAmount'] = $value->VATAmount * $value->qtyIssuedDefaultMeasure;
                $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount * $value->qtyIssuedDefaultMeasure;
                $ledgerDetailsData['localER'] = $value->localCurrencyER;
                $ledgerDetailsData['reportingER'] = $value->reportingCurrencyER;
                $ledgerDetailsData['taxableAmount'] = ($masterData->isPerforma == 2) ? (($vatCategories && $vatCategories->applicableOn == 1) ? $totalAmount :($value->sellingCostAfterMargin * $value->qtyIssuedDefaultMeasure)) : ($value->sellingCostAfterMargin * $value->qtyIssuedDefaultMeasure);
                $ledgerDetailsData['taxableAmountLocal'] = ($masterData->isPerforma == 2) ? (($vatCategories && $vatCategories->applicableOn == 1) ? $MyLocalAmount :($value->sellingCostAfterMarginLocal * $value->qtyIssuedDefaultMeasure)) : ($value->sellingCostAfterMarginLocal * $value->qtyIssuedDefaultMeasure);
                $ledgerDetailsData['taxableAmountReporting'] = ($masterData->isPerforma == 2) ? (($vatCategories && $vatCategories->applicableOn == 1) ? $MyRptAmount :($value->sellingCostAfterMarginRpt * $value->qtyIssuedDefaultMeasure)) : ($value->sellingCostAfterMarginRpt * $value->qtyIssuedDefaultMeasure);
                $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal * $value->qtyIssuedDefaultMeasure;
                $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt * $value->qtyIssuedDefaultMeasure;
                $ledgerDetailsData['localCurrencyID'] = $value->localCurrencyID;
                $ledgerDetailsData['rptCurrencyID'] = $value->reportingCurrencyID;

                if ($ledgerDetailsData['VATAmount'] != 0) {
                    array_push($finalDetailData, $ledgerDetailsData);
                }

            }
        } else if ($masterData->isPerforma == 0 || $masterData->isPerforma == 1) {
            $details = CustomerInvoiceDirectDetail::selectRaw('erp_tax_vat_sub_categories.subCatgeoryType,SUM(VATAmount*invoiceQty) as transVATAmount,SUM(VATAmountLocal*invoiceQty) as localVATAmount ,SUM(VATAmountRpt*invoiceQty) as rptVATAmount, vatMasterCategoryID, vatSubCategoryID, localCurrency, localCurrencyER, comRptCurrency, comRptCurrencyER, invoiceAmountCurrency, invoiceAmountCurrencyER')
                                    ->where('custInvoiceDirectID', $masterModel["autoID"])
                                    ->whereNotNull('vatSubCategoryID')
                                    ->join('erp_tax_vat_sub_categories', 'erp_custinvoicedirectdet.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
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
                $ledgerData['transER'] = $value->invoiceAmountCurrencyER;
                $ledgerData['localER'] = $value->localCurrencyER;
                $ledgerData['comRptER'] = $value->comRptCurrencyER;
                $ledgerData['localCurrencyID'] = $value->localCurrency;
                $ledgerData['rptCurrencyID'] = $value->comRptCurrency;
                $ledgerData['transCurrencyID'] = $value->invoiceAmountCurrency;

                if ($ledgerData['transAmount'] != 0) {
                    array_push($finalData, $ledgerData);
                }
            }


            $detailData = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $masterModel["autoID"])
                                            ->join('erp_tax_vat_sub_categories', 'erp_custinvoicedirectdet.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
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

                $vatCategories = TaxVatCategories::where('taxVatSubCategoriesAutoID', $ledgerDetailsData["vatSubCategoryID"])
                                                ->where('mainCategory', $ledgerDetailsData["vatMasterCategoryID"])
                                                ->first();

                $chartOfAccountData = ChartOfAccount::find($value->glSystemID);

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

                $MyLocalAmount = 0;
                $MyRptAmount = 0;
                $totalAmount = 0;
                if ($masterData->isPerforma == 0) {
                    $unitCostForCalculation = ($vatCategories && $vatCategories->applicableOn == 1) ? $value->salesPrice : $value->unitCost;
                    $totalAmount = $unitCostForCalculation * $value->invoiceQty;
                    $convertAmount = self::calculateLocalAndRpt($totalAmount, $masterData);

                    $MyLocalAmount = $convertAmount['localAmount'];
                    $MyRptAmount = $convertAmount['rptAmount'];
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
                $ledgerDetailsData['VATAmount'] = $value->VATAmount * $value->invoiceQty;
                $ledgerDetailsData['recoverabilityAmount'] = $value->VATAmount * $value->invoiceQty;
                $ledgerDetailsData['localER'] = $value->localCurrencyER;
                $ledgerDetailsData['reportingER'] = $value->comRptCurrencyER;
                $ledgerDetailsData['taxableAmount'] = ($masterData->isPerforma == 0) ? $totalAmount : $value->invoiceAmount;
                $ledgerDetailsData['taxableAmountLocal'] = ($masterData->isPerforma == 0) ? \Helper::roundValue($MyLocalAmount) : $value->localAmount;
                $ledgerDetailsData['taxableAmountReporting'] = ($masterData->isPerforma == 0) ? \Helper::roundValue($MyRptAmount) : $value->comRptAmount;
                $ledgerDetailsData['VATAmountLocal'] = $value->VATAmountLocal * $value->invoiceQty;
                $ledgerDetailsData['VATAmountRpt'] = $value->VATAmountRpt * $value->invoiceQty;
                $ledgerDetailsData['localCurrencyID'] = $value->localCurrency;
                $ledgerDetailsData['rptCurrencyID'] = $value->comRptCurrency;

                if ($ledgerDetailsData['VATAmount'] != 0) {
                    array_push($finalDetailData, $ledgerDetailsData);
                }
            }
        }

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'finalDetailData' => $finalDetailData]];
	}

    public static function calculateLocalAndRpt($totalAmount, $masterData)
    {
        $MyRptAmount = 0;
        $MyLocalAmount = 0;
        if ($masterData->custTransactionCurrencyID == $masterData->companyReportingCurrencyID) {
            $MyRptAmount = $totalAmount;
        } else {
            if ($masterData->companyReportingER > $masterData->custTransactionCurrencyER) {
                if ($masterData->companyReportingER > 1) {
                    $MyRptAmount = ($totalAmount / $masterData->companyReportingER);
                } else {
                    $MyRptAmount = ($totalAmount * $masterData->companyReportingER);
                }
            } else {
                if ($masterData->companyReportingER > 1) {
                    $MyRptAmount = ($totalAmount * $masterData->companyReportingER);
                } else {
                    $MyRptAmount = ($totalAmount / $masterData->companyReportingER);
                }
            }
        }
        if ($masterData->custTransactionCurrencyID == $masterData->localCurrencyID) {
            $MyLocalAmount = $totalAmount;
        } else {
            if ($masterData->localCurrencyER > $masterData->custTransactionCurrencyER) {
                if ($masterData->localCurrencyER > 1) {
                    $MyLocalAmount = ($totalAmount / $masterData->localCurrencyER);
                } else {
                    $MyLocalAmount = ($totalAmount * $masterData->localCurrencyER);
                }
            } else {
                if ($masterData->localCurrencyER > 1) {
                    $MyLocalAmount = ($totalAmount * $masterData->localCurrencyER);
                } else {
                    $MyLocalAmount = ($totalAmount / $masterData->localCurrencyER);
                }
            }
        }

        return ['localAmount' => $MyLocalAmount, 'rptAmount' => $MyRptAmount];
    }
}