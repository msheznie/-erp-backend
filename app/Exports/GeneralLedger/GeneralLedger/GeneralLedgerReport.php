<?php

namespace App\Exports\GeneralLedger\GeneralLedger;

class GeneralLedgerReport
{

    public $companyID;
    public $companyName;
    public $glCode;
    public $accountDescription;
    public $glType;
    public $templateDescription;
    public $documentType;
    public $documentNumber;
    public $documentDate;
    public $documentNarration;
    public $serviceLine;
    public $contractID;
    public $supplierOrCustomer;
    public $debitLocalCurrency;
    public $creditLocalCurrency;
    public $debitReportingCurrency;
    public $creditReportingCurrency;

    public function getHeader($currencyLocal,$currencyRpt):array {
        return [
            'Company ID',
            'Company Name',
            'GL Code',
            'Account Description',
            'GL Type',
            'Template Description',
            'Document Type',
            'Document Number',
            'Date',
            'Document Narration',
            'Service Line',
            'Contract',
            'Supplier/Customer',
            'Debit (Local Currency - ' . $currencyLocal . ')',
            'Credit (Local Currency - ' . $currencyLocal . ')',
            'Debit (Reporting Currency - ' . $currencyRpt . ')',
            'Credit (Reporting Currency - ' . $currencyRpt . ')'
        ];
    }


    /**
     * @param mixed $companyID
     */
    public function setCompanyID($companyID): void
    {
        $this->companyID = $companyID;
    }

    /**
     * @param mixed $companyName
     */
    public function setCompanyName($companyName): void
    {
        $this->companyName = $companyName;
    }

    /**
     * @param mixed $glCode
     */
    public function setGlCode($glCode): void
    {
        $this->glCode = $glCode;
    }

    /**
     * @param mixed $accountDescription
     */
    public function setAccountDescription($accountDescription): void
    {
        $this->accountDescription = $accountDescription;
    }

    /**
     * @param mixed $glType
     */
    public function setGlType($glType): void
    {
        $this->glType = $glType;
    }

    /**
     * @param mixed $templateDescription
     */
    public function setTemplateDescription($templateDescription): void
    {
        $this->templateDescription = $templateDescription;
    }

    /**
     * @param mixed $documentType
     */
    public function setDocumentType($documentType): void
    {
        $this->documentType = $documentType;
    }

    /**
     * @param mixed $documentNumber
     */
    public function setDocumentNumber($documentNumber): void
    {
        $this->documentNumber = $documentNumber;
    }

    /**
     * @param mixed $documentDate
     */
    public function setDocumentDate($documentDate): void
    {
        $this->documentDate = $documentDate;
    }

    /**
     * @param mixed $documentNarration
     */
    public function setDocumentNarration($documentNarration): void
    {
        $this->documentNarration = $documentNarration;
    }

    /**
     * @param mixed $serviceLine
     */
    public function setServiceLine($serviceLine): void
    {
        $this->serviceLine = $serviceLine;
    }

    /**
     * @param mixed $contractID
     */
    public function setContractID($contractID): void
    {
        $this->contractID = $contractID;
    }

    /**
     * @param mixed $supplierOrCustomer
     */
    public function setSupplierOrCustomer($supplierOrCustomer): void
    {
        $this->supplierOrCustomer = $supplierOrCustomer;
    }

    /**
     * @param mixed $debitLocalCurrency
     */
    public function setDebitLocalCurrency($debitLocalCurrency): void
    {
        $this->debitLocalCurrency = $debitLocalCurrency;
    }

    /**
     * @param mixed $creditLocalCurrency
     */
    public function setCreditLocalCurrency($creditLocalCurrency): void
    {
        $this->creditLocalCurrency = $creditLocalCurrency;
    }

    /**
     * @param mixed $debitReportingCurrency
     */
    public function setDebitReportingCurrency($debitReportingCurrency): void
    {
        $this->debitReportingCurrency = $debitReportingCurrency;
    }

    /**
     * @param mixed $creditReportingCurrency
     */
    public function setCreditReportingCurrency($creditReportingCurrency): void
    {
        $this->creditReportingCurrency = $creditReportingCurrency;
    }


}
