<?php

namespace App\Exports\GeneralLedger\VAT;

use App\helper\Helper;

class DetailsOfInwardSupplyReport
{
    public $companyCodeInErp;
    public $companyVatRegistrationNumber;
    public $companyName;
    public $taxPeriod;
    public $accountingDocumentNumber;
    public $referenceNo;
    public $accountingDocumentDate;
    public $year;
    public $revenueGlCode;
    public $revenueGlDescription;
    public $documentCurrency;
    public $documentType;
    public $originalDocumentNo;
    public $originalDocumentDate;
    public $paymentDueDate;
    public $dateOfSupply;
    public $referenceInvoiceNo;
    public $referenceInvoiceDate;
    public $supplierName;
    public $supplierType;
    public $supplierCountry;
    public $vatIn;
    public $invoiceLineItemNo;
    public $lineItemDescription;
    public $placeOfSupply;
    public $taxCodeType;
    public $taxCodeDescription;
    public $vatRate;
    public $valueExculdingInDocumentCurency;
    public $vatInDocumentCurrency;
    public $documentCurrencyToLocalCurrencyRate;
    public $valueExculdingInLocalCurency;
    public $vatInLocalCurrency;
    public $vatGlCode;
    public $vatGlDescription;
    public $inputTaxRecoverability;
    public $inputTaxRecoverabilityPercentage;
    public $inputTaxRecoverabilityAmount;

    /**
     * @return mixed
     */
    public function getCloumnFormat()
    {
        return [
            'G' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'N' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'O' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AC' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AD' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AF' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AG' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AL' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function getHeader() {
        return [
            trans('custom.company_code_in_erp'),
            trans('custom.company_vat_registration_number'),
            trans('custom.company_name'),
            trans('custom.tax_period'),
            trans('custom.accounting_document_number'),
            trans('custom.reference_no'),
            trans('custom.accounting_document_date'),
            trans('custom.year'),
            trans('custom.revenue_gl_code'),
            trans('custom.revenue_gl_code_description'),
            trans('custom.document_currency'),
            trans('custom.document_type'),
            trans('custom.original_document_no'),
            trans('custom.original_document_date'),
            trans('custom.payment_due_date'),
            trans('custom.date_of_supply'),
            trans('custom.reference_invoice_no'),
            trans('custom.reference_invoice_date'),
            trans('custom.supplier_name'),
            trans('custom.supplier_type'),
            trans('custom.supplier_country'),
            trans('custom.vat_in'),
            trans('custom.invoice_line_item_no'),
            trans('custom.line_item_description'),
            trans('custom.place_of_supply'),
            trans('custom.tax_code_type'),
            trans('custom.tax_code_description'),
            trans('custom.vat_rate'),
            trans('custom.value_excluding_vat_in_document_currency'),
            trans('custom.vat_in_document_currency'),
            trans('custom.document_currency_to_local_currency_rate'),
            trans('custom.value_excluding_vat_in_local_currency'),
            trans('custom.vat_in_local_currency'),
            trans('custom.vat_gl_code'),
            trans('custom.vat_gl_description'),
            trans('custom.input_tax_recoverability'),
            trans('custom.input_tax_recoverability_percentage'),
            trans('custom.input_tax_recoverability_amount')

        ];
    }

    /**
     * @param mixed $companyCodeInErp
     */
    public function setCompanyCodeInErp($companyCodeInErp): void
    {
        $this->companyCodeInErp = $companyCodeInErp;
    }

    /**
     * @param mixed $companyVatRegistrationNumber
     */
    public function setCompanyVatRegistrationNumber($companyVatRegistrationNumber): void
    {
        $this->companyVatRegistrationNumber = $companyVatRegistrationNumber;
    }

    /**
     * @param mixed $companyName
     */
    public function setCompanyName($companyName): void
    {
        $this->companyName = $companyName;
    }

    /**
     * @param mixed $taxPeriod
     */
    public function setTaxPeriod($taxPeriod): void
    {
        $this->taxPeriod = $taxPeriod;
    }

    /**
     * @param mixed $accountingDocumentNumber
     */
    public function setAccountingDocumentNumber($accountingDocumentNumber): void
    {
        $this->accountingDocumentNumber = $accountingDocumentNumber;
    }

    /**
     * @param mixed $referenceNo
     */
    public function setReferenceNo($referenceNo): void
    {
        $this->referenceNo = $referenceNo;
    }

    /**
     * @param mixed $accountingDocumentDate
     */
    public function setAccountingDocumentDate($accountingDocumentDate): void
    {
        $this->accountingDocumentDate = ($accountingDocumentDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($accountingDocumentDate)) : null;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year): void
    {
        $this->year = $year;
    }

    /**
     * @param mixed $revenueGlCode
     */
    public function setRevenueGlCode($revenueGlCode): void
    {
        $this->revenueGlCode = $revenueGlCode;
    }

    /**
     * @param mixed $revenueGlDescription
     */
    public function setRevenueGlDescription($revenueGlDescription): void
    {
        $this->revenueGlDescription = $revenueGlDescription;
    }

    /**
     * @param mixed $documentCurrency
     */
    public function setDocumentCurrency($documentCurrency): void
    {
        $this->documentCurrency = $documentCurrency;
    }

    /**
     * @param mixed $documentType
     */
    public function setDocumentType($documentType): void
    {
        $this->documentType = $documentType;
    }

    /**
     * @param mixed $originalDocumentNo
     */
    public function setOriginalDocumentNo($originalDocumentNo): void
    {
        $this->originalDocumentNo = $originalDocumentNo;
    }

    /**
     * @param mixed $originalDocumentDate
     */
    public function setOriginalDocumentDate($originalDocumentDate): void
    {
        $this->originalDocumentDate = ($originalDocumentDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($originalDocumentDate)) : null;
    }

    /**
     * @param mixed $paymentDueDate
     */
    public function setPaymentDueDate($paymentDueDate): void
    {
        $this->paymentDueDate = ($paymentDueDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($paymentDueDate)) : null;
    }

    /**
     * @param mixed $dateOfSupply
     */
    public function setDateOfSupply($dateOfSupply): void
    {
        $this->dateOfSupply = $dateOfSupply;
    }

    /**
     * @param mixed $referenceInvoiceNo
     */
    public function setReferenceInvoiceNo($referenceInvoiceNo): void
    {
        $this->referenceInvoiceNo = $referenceInvoiceNo;
    }

    /**
     * @param mixed $referenceInvoiceDate
     */
    public function setReferenceInvoiceDate($referenceInvoiceDate): void
    {
        $this->referenceInvoiceDate = ($referenceInvoiceDate)? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($referenceInvoiceDate)): null;
    }

    /**
     * @param mixed $supplierName
     */
    public function setSupplierName($supplierName): void
    {
        $this->supplierName = $supplierName;
    }

    /**
     * @param mixed $supplierType
     */
    public function setSupplierType($supplierType): void
    {
        $this->supplierType = $supplierType;
    }

    /**
     * @param mixed $supplierCountry
     */
    public function setSupplierCountry($supplierCountry): void
    {
        $this->supplierCountry = $supplierCountry;
    }

    /**
     * @param mixed $vatIn
     */
    public function setVatIn($vatIn): void
    {
        $this->vatIn = $vatIn;
    }

    /**
     * @param mixed $invoiceLineItemNo
     */
    public function setInvoiceLineItemNo($invoiceLineItemNo): void
    {
        $this->invoiceLineItemNo = $invoiceLineItemNo;
    }

    /**
     * @param mixed $lineItemDescription
     */
    public function setLineItemDescription($lineItemDescription): void
    {
        $this->lineItemDescription = $lineItemDescription;
    }

    /**
     * @param mixed $placeOfSupply
     */
    public function setPlaceOfSupply($placeOfSupply): void
    {
        $this->placeOfSupply = $placeOfSupply;
    }

    /**
     * @param mixed $taxCodeType
     */
    public function setTaxCodeType($taxCodeType): void
    {
        $this->taxCodeType = $taxCodeType;
    }

    /**
     * @param mixed $taxCodeDescription
     */
    public function setTaxCodeDescription($taxCodeDescription): void
    {
        $this->taxCodeDescription = $taxCodeDescription;
    }

    /**
     * @param mixed $vatRate
     */
    public function setVatRate($vatRate): void
    {
        $this->vatRate = $vatRate;
    }

    /**
     * @param mixed $valueExculdingInDocumentCurency
     */
    public function setValueExculdingInDocumentCurency($valueExculdingInDocumentCurency): void
    {
        $this->valueExculdingInDocumentCurency = $valueExculdingInDocumentCurency;
    }

    /**
     * @param mixed $vatInDocumentCurrency
     */
    public function setVatInDocumentCurrency($vatInDocumentCurrency): void
    {
        $this->vatInDocumentCurrency = $vatInDocumentCurrency;
    }

    /**
     * @param mixed $documentCurrencyToLocalCurrencyRate
     */
    public function setDocumentCurrencyToLocalCurrencyRate($documentCurrencyToLocalCurrencyRate): void
    {
        $this->documentCurrencyToLocalCurrencyRate = $documentCurrencyToLocalCurrencyRate;
    }

    /**
     * @param mixed $valueExculdingInLocalCurency
     */
    public function setValueExculdingInLocalCurency($valueExculdingInLocalCurency): void
    {
        $this->valueExculdingInLocalCurency = $valueExculdingInLocalCurency;
    }

    /**
     * @param mixed $vatInLocalCurrency
     */
    public function setVatInLocalCurrency($vatInLocalCurrency): void
    {
        $this->vatInLocalCurrency = $vatInLocalCurrency;
    }

    /**
     * @param mixed $vatGlCode
     */
    public function setVatGlCode($vatGlCode): void
    {
        $this->vatGlCode = $vatGlCode;
    }

    /**
     * @param mixed $vatGlDescription
     */
    public function setVatGlDescription($vatGlDescription): void
    {
        $this->vatGlDescription = $vatGlDescription;
    }

    /**
     * @param mixed $inputTaxRecoverability
     */
    public function setInputTaxRecoverability($inputTaxRecoverability): void
    {
        $this->inputTaxRecoverability = $inputTaxRecoverability;
    }

    /**
     * @param mixed $inputTaxRecoverabilityPercentage
     */
    public function setInputTaxRecoverabilityPercentage($inputTaxRecoverabilityPercentage): void
    {
        $this->inputTaxRecoverabilityPercentage = $inputTaxRecoverabilityPercentage;
    }

    /**
     * @param mixed $inputTaxRecoverabilityAmount
     */
    public function setInputTaxRecoverabilityAmount($inputTaxRecoverabilityAmount): void
    {
        $this->inputTaxRecoverabilityAmount = $inputTaxRecoverabilityAmount;
    }
}
