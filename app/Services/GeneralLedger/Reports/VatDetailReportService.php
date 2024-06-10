<?php

namespace App\Services\GeneralLedger\Reports;

use App\Exports\GeneralLedger\VAT\DetailsOfInwardSupplyReport;
use App\Exports\GeneralLedger\VAT\VatDetailReport;
use App\helper\Helper;
use App\Services\Currency\CurrencyService;
use Carbon\Carbon;

class VatDetailReportService
{

    public function generateDataForVatDetailReport($output,$input) : Array {
        $x = 0;
        $data = [];
        $dataArray = [];
        $taxableAmountTotal = 0;
        $VATAmountTotal = 0;
        $taxableAmountLocalTotal = 0;
        $VATAmountLocalTotal = 0;
        $recoverabilityAmountTotal = 0;
        $transdecimalPlace = 2;

        if(empty($dataArray) && $input['reportTypeID'] == 3) {
            $objVatDetailReportHeader = new VatDetailReport();
            $objHeader = collect($objVatDetailReportHeader->getHeader());
            array_push($dataArray,$objHeader->toArray());
        }

        if(empty($dataArray) && ($input['reportTypeID'] == 4 || $input['reportTypeID'] == 5)) {
            $objVatDetailReportHeader = new DetailsOfInwardSupplyReport();
            $objHeader = collect($objVatDetailReportHeader->getHeader());
            array_push($dataArray,$objHeader->toArray());
        }


        foreach ($output as $val) {
            if($input['reportTypeID'] == 3) {
                $objVatDetailReport = new VatDetailReport();
                isset($val->company->CompanyID) ? $objVatDetailReport->setCompanyCodeInErp($val->company->CompanyID) :  $objVatDetailReport->setCompanyCodeInErp("-");
                isset($val->company->CompanyID) ?  $objVatDetailReport->setCompanyVatRegistrationNumber($val->company->CompanyID) : "";
                isset($val->company->CompanyID) ?  $objVatDetailReport->setCompanyName($val->company->CompanyID) : "";
                $objVatDetailReport->setTaxPeriod($input['fromDate']." - ". $input['toDate']);
                $objVatDetailReport->setAccountingDocumentNumber($val->documentNumber);
                if($val->documentSystemID == 11){
                    if($val->supplier_invoice)
                        $objVatDetailReport->setReferenceNo($val->supplier_invoice->supplierInvoiceNo);

                }
                else if($val->documentSystemID == 3){
                    $objVatDetailReport->setReferenceNo($val->grv->grvDoRefNo);
                }
                else if($val->documentSystemID == 24){
                    if($val->purchase_return)
                        $objVatDetailReport->setReferenceNo($val->purchase_return->purchaseReturnRefNo);

                }
                else{
                    $objVatDetailReport->setReferenceNo('');
                }
                $objVatDetailReport->setAccountingDocumentDate($val->documentDate);
                $objVatDetailReport->setYear( Carbon::parse($val->documentDate)->format('Y'));
                $objVatDetailReport->setVatGlCode($val->accountCode);
                $objVatDetailReport->setVatGlDescription($val->accountDescription);
                isset($val->transcurrency->CurrencyCode) ? $objVatDetailReport->setDocumentCurrency($val->transcurrency->CurrencyCode) : "";
                isset($val->document_master->documentDescription) ? $objVatDetailReport->setDocumentType($val->document_master->documentDescription) : "";
                $objVatDetailReport->setOriginalDocumentNo($val->originalInvoice);
                $objVatDetailReport->setOriginalDocumentDate($val->originalInvoiceDate);
                $objVatDetailReport->setDateOfSupply($val->dateOfSupply);
                if ($val->documentSystemID == 3 || $val->documentSystemID == 24 || $val->documentSystemID == 11 || $val->documentSystemID == 15 || $val->documentSystemID == 4) {
                    isset($val->supplier->supplierName) ? $objVatDetailReport->setBillToCustomerName($val->supplier->supplierName) : "";
                } else if ($val->documentSystemID == 20 || $val->documentSystemID == 19 || $val->documentSystemID == 21 || $val->documentSystemID == 71 || $val->documentSystemID == 87) {
                    isset($val->customer->CustomerName) ?  $objVatDetailReport->setBillToCustomerName($val->customer->CustomerName) : "";
                }
                ($val->partyVATRegisteredYN) ?  $objVatDetailReport->setCustomerType("Registered") : $objVatDetailReport->setCustomerType("Unregistered");
                isset($val->country->countryName) ? $objVatDetailReport->setBillToCountry($val->country->countryName) : "";
                $objVatDetailReport->setVatIn($val->partyVATRegNo);
                $objVatDetailReport->setInvoiceLineItemNo($val->itemCode);
                $objVatDetailReport->setLineItemDescription($val->itemDescription);
                if (isset($val->company->companyCountry) && ($val->company->companyCountry == $val->countryID)) {
                    isset($val->company->country->countryName) ? $objVatDetailReport->setPlaceOfSupply($val->company->country->countryName) : "";
                } else {
                    isset($val->company->country->countryName) ? $objVatDetailReport->setPlaceOfSupply("Outside ".$val->company->country->countryName) : "";
                }
                $objVatDetailReport->setTaxCodeType('');
                isset($val->sub_category->subCategoryDescription) ? $objVatDetailReport->setTaxCodeDescription($val->sub_category->subCategoryDescription) : "";
                $objVatDetailReport->setVatRate($val->VATPercentage);
                $transdecimalPlace = isset($val->transcurrency->DecimalPlaces)? $val->transcurrency->DecimalPlaces : 3;

                $objVatDetailReport->setValueExculdingInDocumentCurency(CurrencyService::convertNumberFormatToNumber(number_format($val->taxableAmount, $transdecimalPlace)));
                $objVatDetailReport->setVatInDocumentCurrency(CurrencyService::convertNumberFormatToNumber(number_format($val->VATAmount, $transdecimalPlace)));
                $objVatDetailReport->setDocumentCurrencyToLocalCurrencyRate($val->localER);
                $objVatDetailReport->setValueExculdingInLocalCurency( CurrencyService::convertNumberFormatToNumber(number_format($val->taxableAmountLocal, $transdecimalPlace)));
                $objVatDetailReport->setVatInLocalCurrency(CurrencyService::convertNumberFormatToNumber(number_format($val->VATAmountLocal, $transdecimalPlace)));
                isset($val->output_vat->AccountCode) ? $objVatDetailReport->setVatGlCode($val->output_vat->AccountCode) : "";
                isset($val->output_vat->AccountDescription) ? $objVatDetailReport->setVatGlDescription($val->output_vat->AccountDescription) : "";
            }

            if ($input['reportTypeID'] == 4 || $input['reportTypeID'] == 5) {
                $objVatDetailReport = new DetailsOfInwardSupplyReport();
                isset($val->company->CompanyID) ? $objVatDetailReport->setCompanyCodeInErp($val->company->CompanyID) :  $objVatDetailReport->setCompanyCodeInErp("-");
                isset($val->company->CompanyID) ?  $objVatDetailReport->setCompanyVatRegistrationNumber($val->company->CompanyID) : "";
                isset($val->company->CompanyID) ?  $objVatDetailReport->setCompanyName($val->company->CompanyID) : "";
                $objVatDetailReport->setTaxPeriod($input['fromDate']." - ". $input['toDate']);
                $objVatDetailReport->setAccountingDocumentNumber($val->documentNumber);
                if($val->documentSystemID == 11){
                    if($val->supplier_invoice)
                        $objVatDetailReport->setReferenceNo($val->supplier_invoice->supplierInvoiceNo);

                }
                else if($val->documentSystemID == 3){
                    $objVatDetailReport->setReferenceNo($val->grv->grvDoRefNo);
                }
                else if($val->documentSystemID == 24){
                    if($val->purchase_return)
                        $objVatDetailReport->setReferenceNo($val->purchase_return->purchaseReturnRefNo);

                }
                else{
                    $objVatDetailReport->setReferenceNo('');
                }
                $objVatDetailReport->setAccountingDocumentDate($val->documentDate);
                $objVatDetailReport->setYear( Carbon::parse($val->documentDate)->format('Y'));
                $objVatDetailReport->setVatGlCode($val->accountCode);
                $objVatDetailReport->setVatGlDescription($val->accountDescription);
                isset($val->transcurrency->CurrencyCode) ? $objVatDetailReport->setDocumentCurrency($val->transcurrency->CurrencyCode) : "";
                isset($val->document_master->documentDescription) ? $objVatDetailReport->setDocumentType($val->document_master->documentDescription) : "";
                $objVatDetailReport->setOriginalDocumentNo($val->originalInvoice);
                $objVatDetailReport->setOriginalDocumentDate($val->originalInvoiceDate);
                $objVatDetailReport->setPaymentDueDate("");
                $objVatDetailReport->setDateOfSupply($val->dateOfSupply);
                if ($val->documentSystemID == 3 || $val->documentSystemID == 24 || $val->documentSystemID == 11 || $val->documentSystemID == 15 || $val->documentSystemID == 4) {
                    isset($val->supplier->supplierName) ? $objVatDetailReport->setSupplierName($val->supplier->supplierName) : "";
                } else if ($val->documentSystemID == 20 || $val->documentSystemID == 19 || $val->documentSystemID == 21 || $val->documentSystemID == 71 || $val->documentSystemID == 87) {
                    isset($val->customer->CustomerName) ?  $objVatDetailReport->setSupplierName($val->customer->CustomerName) : "";
                }
                ($val->partyVATRegisteredYN) ?  $objVatDetailReport->setSupplierType("Registered") : $objVatDetailReport->setSupplierType("Unregistered");
                isset($val->country->countryName) ? $objVatDetailReport->setSupplierCountry($val->country->countryName) : "";
                $objVatDetailReport->setVatIn($val->partyVATRegNo);
                $objVatDetailReport->setInvoiceLineItemNo($val->itemCode);
                $objVatDetailReport->setLineItemDescription($val->itemDescription);
                if (isset($val->company->companyCountry) && ($val->company->companyCountry == $val->countryID)) {
                    isset($val->company->country->countryName) ? $objVatDetailReport->setPlaceOfSupply($val->company->country->countryName) : "";
                } else {
                    isset($val->company->country->countryName) ? $objVatDetailReport->setPlaceOfSupply("Outside ".$val->company->country->countryName) : "";
                }
                $objVatDetailReport->setTaxCodeType('');
                isset($val->sub_category->subCategoryDescription) ? $objVatDetailReport->setTaxCodeDescription($val->sub_category->subCategoryDescription) : "";
                $objVatDetailReport->setVatRate($val->VATPercentage);
                $objVatDetailReport->setValueExculdingInDocumentCurency( CurrencyService::convertNumberFormatToNumber(number_format($val->taxableAmount, $val->transcurrency->DecimalPlaces)));
                $objVatDetailReport->setVatInDocumentCurrency(CurrencyService::convertNumberFormatToNumber(number_format($val->VATAmount, $val->transcurrency->DecimalPlaces)));
                $objVatDetailReport->setDocumentCurrencyToLocalCurrencyRate($val->localER);
                $objVatDetailReport->setValueExculdingInLocalCurency( CurrencyService::convertNumberFormatToNumber(number_format($val->taxableAmountLocal, $val->transcurrency->DecimalPlaces)));
                $objVatDetailReport->setVatInLocalCurrency(CurrencyService::convertNumberFormatToNumber(number_format($val->VATAmountLocal, $val->transcurrency->DecimalPlaces)));
                isset($val->output_vat->AccountCode) ? $objVatDetailReport->setVatGlCode($val->output_vat->AccountCode) : "";
                isset($val->output_vat->AccountDescription) ? $objVatDetailReport->setVatGlDescription($val->output_vat->AccountDescription) : "";
                (isset($val->company->vatRegisteredYN) && $val->company->vatRegisteredYN) ? $objVatDetailReport->setInputTaxRecoverability("Yes") : $objVatDetailReport->setInputTaxRecoverability("No");
                $objVatDetailReport->setInputTaxRecoverabilityPercentage($val->recovertabilityPercentage);
                $objVatDetailReport->setInputTaxRecoverabilityAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->recoverabilityAmount, $val->transcurrency->DecimalPlaces)));
            }

            array_push($dataArray,collect($objVatDetailReport)->toArray());
            $taxableAmountTotal += $val->taxableAmount;
            $VATAmountTotal += $val->VATAmount;
            $taxableAmountLocalTotal += $val->taxableAmountLocal;
            $VATAmountLocalTotal += $val->VATAmountLocal;
            $recoverabilityAmountTotal += $val->recoverabilityAmount;
            $transdecimalPlace = isset($val->transcurrency->DecimalPlaces)? $val->transcurrency->DecimalPlaces : 3;
        }

           if($input['reportTypeID'] == 3) {
            $objLastVatDetailReport = new VatDetailReport();
            $objLastVatDetailReport->setVatRate("Total");
            $objLastVatDetailReport->setValueExculdingInDocumentCurency(CurrencyService::convertNumberFormatToNumber(number_format($taxableAmountTotal, $transdecimalPlace)));
            $objLastVatDetailReport->setVatInDocumentCurrency(CurrencyService::convertNumberFormatToNumber(number_format($VATAmountTotal, $transdecimalPlace)));
            $objLastVatDetailReport->setValueExculdingInLocalCurency(CurrencyService::convertNumberFormatToNumber(number_format($taxableAmountLocalTotal, $transdecimalPlace)));
            $objLastVatDetailReport->setVatInLocalCurrency(CurrencyService::convertNumberFormatToNumber(number_format($VATAmountLocalTotal, $transdecimalPlace)));
            array_push($dataArray,collect($objLastVatDetailReport)->toArray());
        }
        if ($input['reportTypeID'] == 4 || $input['reportTypeID'] == 5) {
            $objLastVatDetailReport = new DetailsOfInwardSupplyReport();
            $objLastVatDetailReport->setVatRate("Total");
            $objLastVatDetailReport->setValueExculdingInDocumentCurency(CurrencyService::convertNumberFormatToNumber(number_format($taxableAmountTotal, $transdecimalPlace)));
            $objLastVatDetailReport->setVatInDocumentCurrency(CurrencyService::convertNumberFormatToNumber(number_format($VATAmountTotal, $transdecimalPlace)));
            $objLastVatDetailReport->setValueExculdingInLocalCurency(CurrencyService::convertNumberFormatToNumber(number_format($taxableAmountLocalTotal, $transdecimalPlace)));
            $objLastVatDetailReport->setVatInLocalCurrency(CurrencyService::convertNumberFormatToNumber(number_format($VATAmountLocalTotal, $transdecimalPlace)));
            $objLastVatDetailReport->setInputTaxRecoverability("");
            $objLastVatDetailReport->setInputTaxRecoverabilityPercentage("");
            $objLastVatDetailReport->setInputTaxRecoverabilityAmount(CurrencyService::convertNumberFormatToNumber(number_format($recoverabilityAmountTotal, $transdecimalPlace)));
            array_push($dataArray,collect($objLastVatDetailReport)->toArray());
        }


        return $dataArray;
    }
}
