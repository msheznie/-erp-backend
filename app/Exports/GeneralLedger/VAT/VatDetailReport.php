<?php

namespace App\Exports\GeneralLedger\VAT;

use App\helper\Helper;

class VatDetailReport
{
    public $companyCodeInErp;
    public $companyVatRegistrationNumber;
    public $companyName;
    public $taxPeriod;
    public $accountingDocumentNumber;
    public $accountingDocumentDate;
    public $referenceNo;
    public $year;
    public $revenueGlCode;
    public $revenueGlDescription;
    public $documentCurrency;
    public $documentType;
    public $originalDocumentNo;
    public $originalDocumentDate;
    public $dateOfSupply;
    public $referenceInvoiceNo;
    public $referenceInvoiceDate;
    public $billToCustomerName;
    public $customerType;
    public $billToCountry;
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
    public $cloumn_format;

    /**
     * @return mixed
     */
    public function getCloumnFormat()
    {
        return [
            'F' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'N' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AB' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AC' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AE' =>  \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AF' => \ PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function getHeader() {
        return [
            'Company Code in ERP',
            'Company VAT Registration Number',
            'Company Name',
            'Tax Period',
            'Accounting Document Number',
            'Accounting Document Date',
            'Reference No',
            'Year',
            'Revenue GL Code',
            'Revenue GL Code Description',
            'Document Currency',
            'Document Type',
            'Original Document No',
            'Original Document Date',
            'Date of supply',
            'Reference Invoice No',
            'Reference Invoice Date',
            'Bill To Customer Name',
            'Customer Type',
            'Bill To Country',
            'VATIN',
            'Invoice Line Item No',
            'Invoice Line Item Description',
            'Place Of Supply',
            'Tax Code Type',
            'Tax Code Description',
            'VAT Rate',
            'Value Excluding VAT In Document Currency',
            'VAT In Document Currency',
            'Document Currency To Local Currency Rate',
            'Value Excluding VAT In Local Currency',
            'VAT IN Local Currency',
            'VAT GL Code',
            'VAT GL Description'

        ];
    }
    /**
     * @return mixed
     */
    public function getCompanyCodeInErp()
    {
        return $this->companyCodeInErp;
    }

    /**
     * @param mixed $companyCodeInErp
     */
    public function setCompanyCodeInErp($companyCodeInErp): void
    {
        $this->companyCodeInErp = $companyCodeInErp;
    }

    /**
     * @return mixed
     */
    public function getCompanyVatRegistrationNumber()
    {
        return $this->companyVatRegistrationNumber;
    }

    /**
     * @param mixed $companyVatRegistrationNumber
     */
    public function setCompanyVatRegistrationNumber($companyVatRegistrationNumber): void
    {
        $this->companyVatRegistrationNumber = $companyVatRegistrationNumber;
    }

    /**
     * @return mixed
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param mixed $companyName
     */
    public function setCompanyName($companyName): void
    {
        $this->companyName = $companyName;
    }

    /**
     * @return mixed
     */
    public function getTaxPeriod()
    {
        return $this->taxPeriod;
    }

    /**
     * @param mixed $taxPeriod
     */
    public function setTaxPeriod($taxPeriod): void
    {
        $this->taxPeriod = $taxPeriod;
    }

    /**
     * @return mixed
     */
    public function getAccountingDocumentNumber()
    {
        return $this->accountingDocumentNumber;
    }

    /**
     * @param mixed $accountingDocumentNumber
     */
    public function setAccountingDocumentNumber($accountingDocumentNumber): void
    {
        $this->accountingDocumentNumber = $accountingDocumentNumber;
    }

    /**
     * @return mixed
     */
    public function getAccountingDocumentDate()
    {
        return $this->accountingDocumentDate;
    }

    /**
     * @param mixed $accountingDocumentDate
     */
    public function setAccountingDocumentDate($accountingDocumentDate): void
    {
        $this->accountingDocumentDate = ($accountingDocumentDate) ?  \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($accountingDocumentDate)) : null;
    }

    /**
     * @return mixed
     */
    public function getReferenceNo()
    {
        return $this->referenceNo;
    }

    /**
     * @param mixed $referenceNo
     */
    public function setReferenceNo($referenceNo): void
    {
        $this->referenceNo = $referenceNo;
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year): void
    {
        $this->year = $year;
    }

    /**
     * @return mixed
     */
    public function getRevenueGlCode()
    {
        return $this->revenueGlCode;
    }

    /**
     * @param mixed $revenueGlCode
     */
    public function setRevenueGlCode($revenueGlCode): void
    {
        $this->revenueGlCode = $revenueGlCode;
    }

    /**
     * @return mixed
     */
    public function getRevenueGlDescription()
    {
        return $this->revenueGlDescription;
    }

    /**
     * @param mixed $revenueGlDescription
     */
    public function setRevenueGlDescription($revenueGlDescription): void
    {
        $this->revenueGlDescription = $revenueGlDescription;
    }

    /**
     * @return mixed
     */
    public function getDocumentCurrency()
    {
        return $this->documentCurrency;
    }

    /**
     * @param mixed $documentCurrency
     */
    public function setDocumentCurrency($documentCurrency): void
    {
        $this->documentCurrency = $documentCurrency;
    }

    /**
     * @return mixed
     */
    public function getDocumentType()
    {
        return $this->documentType;
    }

    /**
     * @param mixed $documentType
     */
    public function setDocumentType($documentType): void
    {
        $this->documentType = $documentType;
    }

    /**
     * @return mixed
     */
    public function getOriginalDocumentNo()
    {
        return $this->originalDocumentNo;
    }

    /**
     * @param mixed $originalDocumentNo
     */
    public function setOriginalDocumentNo($originalDocumentNo): void
    {
        $this->originalDocumentNo = $originalDocumentNo;
    }

    /**
     * @return mixed
     */
    public function getOriginalDocumentDate()
    {
        return $this->originalDocumentDate;
    }

    /**
     * @param mixed $originalDocumentDate
     */
    public function setOriginalDocumentDate($originalDocumentDate): void
    {
        $this->originalDocumentDate = ($originalDocumentDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($originalDocumentDate)) : null;
    }

    /**
     * @return mixed
     */
    public function getDateOfSupply()
    {
        return $this->dateOfSupply;
    }

    /**
     * @param mixed $dateOfSupply
     */
    public function setDateOfSupply($dateOfSupply): void
    {
        $this->dateOfSupply = $dateOfSupply;
    }

    /**
     * @return mixed
     */
    public function getReferenceInvoiceNo()
    {
        return $this->referenceInvoiceNo;
    }

    /**
     * @param mixed $referenceInvoiceNo
     */
    public function setReferenceInvoiceNo($referenceInvoiceNo): void
    {
        $this->referenceInvoiceNo = $referenceInvoiceNo;
    }

    /**
     * @return mixed
     */
    public function getReferenceInvoiceDate()
    {
        return $this->referenceInvoiceDate;
    }

    /**
     * @param mixed $referenceInvoiceDate
     */
    public function setReferenceInvoiceDate($referenceInvoiceDate): void
    {
        $this->referenceInvoiceDate = $referenceInvoiceDate;
    }

    /**
     * @return mixed
     */
    public function getBillToCustomerName()
    {
        return $this->billToCustomerName;
    }

    /**
     * @param mixed $billToCustomerName
     */
    public function setBillToCustomerName($billToCustomerName): void
    {
        $this->billToCustomerName = $billToCustomerName;
    }

    /**
     * @return mixed
     */
    public function getCustomerType()
    {
        return $this->customerType;
    }

    /**
     * @param mixed $customerType
     */
    public function setCustomerType($customerType): void
    {
        $this->customerType = $customerType;
    }

    /**
     * @return mixed
     */
    public function getBillToCountry()
    {
        return $this->billToCountry;
    }

    /**
     * @param mixed $billToCountry
     */
    public function setBillToCountry($billToCountry): void
    {
        $this->billToCountry = $billToCountry;
    }

    /**
     * @return mixed
     */
    public function getVatIn()
    {
        return $this->vatIn;
    }

    /**
     * @param mixed $vatIn
     */
    public function setVatIn($vatIn): void
    {
        $this->vatIn = $vatIn;
    }

    /**
     * @return mixed
     */
    public function getInvoiceLineItemNo()
    {
        return $this->invoiceLineItemNo;
    }

    /**
     * @param mixed $invoiceLineItemNo
     */
    public function setInvoiceLineItemNo($invoiceLineItemNo): void
    {
        $this->invoiceLineItemNo = $invoiceLineItemNo;
    }

    /**
     * @return mixed
     */
    public function getLineItemDescription()
    {
        return $this->lineItemDescription;
    }

    /**
     * @param mixed $lineItemDescription
     */
    public function setLineItemDescription($lineItemDescription): void
    {
        $this->lineItemDescription = $lineItemDescription;
    }

    /**
     * @return mixed
     */
    public function getPlaceOfSupply()
    {
        return $this->placeOfSupply;
    }

    /**
     * @param mixed $placeOfSupply
     */
    public function setPlaceOfSupply($placeOfSupply): void
    {
        $this->placeOfSupply = $placeOfSupply;
    }

    /**
     * @return mixed
     */
    public function getTaxCodeType()
    {
        return $this->taxCodeType;
    }

    /**
     * @param mixed $taxCodeType
     */
    public function setTaxCodeType($taxCodeType): void
    {
        $this->taxCodeType = $taxCodeType;
    }

    /**
     * @return mixed
     */
    public function getTaxCodeDescription()
    {
        return $this->taxCodeDescription;
    }

    /**
     * @param mixed $taxCodeDescription
     */
    public function setTaxCodeDescription($taxCodeDescription): void
    {
        $this->taxCodeDescription = $taxCodeDescription;
    }

    /**
     * @return mixed
     */
    public function getVatRate()
    {
        return $this->vatRate;
    }

    /**
     * @param mixed $vatRate
     */
    public function setVatRate($vatRate): void
    {
        $this->vatRate = $vatRate;
    }

    /**
     * @return mixed
     */
    public function getValueExculdingInDocumentCurency()
    {
        return $this->valueExculdingInDocumentCurency;
    }

    /**
     * @param mixed $valueExculdingInDocumentCurency
     */
    public function setValueExculdingInDocumentCurency($valueExculdingInDocumentCurency): void
    {
        $this->valueExculdingInDocumentCurency = $valueExculdingInDocumentCurency;
    }

    /**
     * @return mixed
     */
    public function getVatInDocumentCurrency()
    {
        return $this->vatInDocumentCurrency;
    }

    /**
     * @param mixed $vatInDocumentCurrency
     */
    public function setVatInDocumentCurrency($vatInDocumentCurrency): void
    {
        $this->vatInDocumentCurrency = $vatInDocumentCurrency;
    }

    /**
     * @return mixed
     */
    public function getDocumentCurrencyToLocalCurrencyRate()
    {
        return $this->documentCurrencyToLocalCurrencyRate;
    }

    /**
     * @param mixed $documentCurrencyToLocalCurrencyRate
     */
    public function setDocumentCurrencyToLocalCurrencyRate($documentCurrencyToLocalCurrencyRate): void
    {
        $this->documentCurrencyToLocalCurrencyRate = $documentCurrencyToLocalCurrencyRate;
    }

    /**
     * @return mixed
     */
    public function getValueExculdingInLocalCurency()
    {
        return $this->valueExculdingInLocalCurency;
    }

    /**
     * @param mixed $valueExculdingInLocalCurency
     */
    public function setValueExculdingInLocalCurency($valueExculdingInLocalCurency): void
    {
        $this->valueExculdingInLocalCurency = $valueExculdingInLocalCurency;
    }

    /**
     * @return mixed
     */
    public function getVatInLocalCurrency()
    {
        return $this->vatInLocalCurrency;
    }

    /**
     * @param mixed $vatInLocalCurrency
     */
    public function setVatInLocalCurrency($vatInLocalCurrency): void
    {
        $this->vatInLocalCurrency = $vatInLocalCurrency;
    }

    /**
     * @return mixed
     */
    public function getVatGlCode()
    {
        return $this->vatGlCode;
    }

    /**
     * @param mixed $vatGlCode
     */
    public function setVatGlCode($vatGlCode): void
    {
        $this->vatGlCode = $vatGlCode;
    }

    /**
     * @return mixed
     */
    public function getVatGlDescription()
    {
        return $this->vatGlDescription;
    }

    /**
     * @param mixed $vatGlDescription
     */
    public function setVatGlDescription($vatGlDescription): void
    {
        $this->vatGlDescription = $vatGlDescription;
    }



}
