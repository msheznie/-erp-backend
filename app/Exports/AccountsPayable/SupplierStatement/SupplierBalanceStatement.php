<?php

namespace App\Exports\AccountsPayable\SupplierStatement;

class SupplierBalanceStatement
{
    public $companyId;
    public $companyName;
    public $documentDate;
    public $documentCode;
    public $supplierCode;
    public $supplierName;
    public $invoiceNumber;
    public $invoiceDate;
    public $currency;
    public $amount;
    public $balanceAmount;
    public $localCurrency;
    public $localAmount;
    public $localBalanceAmount;
    public $reportingCurrency;
    public $reportingAmount;
    public $reportingBalanceAmount;


    public function getHeader() {
        return array(
            'Company ID',
            'Company Name',
            'Document Date',
            'Document Code',
            'Supplier Code',
            'Supplier Name',
            'Invoice Number',
            'Invoice Date',
            'Currency',
            'Amount',
            'Balance Amount',
            'Local Currency',
            'Local Amount',
            'Local Balance Amount',
            'Reporting Currency',
            'Reporting Amount',
            'Reporting Balance Amount'
        );
    }
    /**
     * @param mixed $companyId
     */
    public function setCompanyId($companyId): void
    {
        $this->companyId = $companyId;
    }

    /**
     * @param mixed $companyName
     */
    public function setCompanyName($companyName): void
    {
        $this->companyName = $companyName;
    }

    /**
     * @param mixed $documentDate
     */
    public function setDocumentDate($documentDate): void
    {
        $this->documentDate = $documentDate;
    }

    /**
     * @param mixed $documentCode
     */
    public function setDocumentCode($documentCode): void
    {
        $this->documentCode = $documentCode;
    }

    /**
     * @param mixed $supplierCode
     */
    public function setSupplierCode($supplierCode): void
    {
        $this->supplierCode = $supplierCode;
    }

    /**
     * @param mixed $supplierName
     */
    public function setSupplierName($supplierName): void
    {
        $this->supplierName = $supplierName;
    }

    /**
     * @param mixed $invoiceNumber
     */
    public function setInvoiceNumber($invoiceNumber): void
    {
        $this->invoiceNumber = $invoiceNumber;
    }

    /**
     * @param mixed $invoiceDate
     */
    public function setInvoiceDate($invoiceDate): void
    {
        $this->invoiceDate = $invoiceDate;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @param mixed $balanceAmount
     */
    public function setBalanceAmount($balanceAmount): void
    {
        $this->balanceAmount = $balanceAmount;
    }

    /**
     * @param mixed $localCurrency
     */
    public function setLocalCurrency($localCurrency): void
    {
        $this->localCurrency = $localCurrency;
    }

    /**
     * @param mixed $localAmount
     */
    public function setLocalAmount($localAmount): void
    {
        $this->localAmount = $localAmount;
    }

    /**
     * @param mixed $localBalanceAmount
     */
    public function setLocalBalanceAmount($localBalanceAmount): void
    {
        $this->localBalanceAmount = $localBalanceAmount;
    }

    /**
     * @param mixed $reportingCurrency
     */
    public function setReportingCurrency($reportingCurrency): void
    {
        $this->reportingCurrency = $reportingCurrency;
    }

    /**
     * @param mixed $reportingAmount
     */
    public function setReportingAmount($reportingAmount): void
    {
        $this->reportingAmount = $reportingAmount;
    }

    /**
     * @param mixed $reportingBalanceAmount
     */
    public function setReportingBalanceAmount($reportingBalanceAmount): void
    {
        $this->reportingBalanceAmount = $reportingBalanceAmount;
    }
}
