<?php

namespace App\Services\TaxLedger;


use App\Models\ChartOfAccount;
use App\Models\DirectPaymentDetails;
use App\Models\Employee;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\TaxVatCategories;
use App\helper\TaxService;

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
            $taxConfigData = TaxService::getInputVATGLAccount($masterData->companySystemID);
            if (!empty($taxConfigData)) {
                $ledgerDetailsData['inputVATGlAccountID'] = $taxConfigData->inputVatGLAccountAutoID;
            }
            $ledgerDetailsData['localCurrencyID'] = $value->localCurrency;
            $ledgerDetailsData['rptCurrencyID'] = $value->comRptCurrency;

            array_push($finalDetailData, $ledgerDetailsData);
        }

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'finalDetailData' => $finalDetailData]];
    }
}
