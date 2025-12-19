<?php

namespace App\Exports\AccountsPayable;

use App\helper\Helper;

class SupplierLedgerReport
{
    public $documentCode;
    public $postedDate;
    public $account;
    public $invoiceNumber;
    public $invoiceDate;
    public $documentNarration;
    public $currency;
    public $documentAmount;
    public $supplierCode;
    public $supplierName;
    public $documentSystemCode;
    public $glCode;
    public $AccountDescription;
    public $documentCurrency;
    public $invoiceAmount;
    public $balanceDecimalPlaces;
    public $supplierGroupName;
    public $documentDate;
    public $invoiceAmountOrg;

    /**
     * @param mixed $invoiceAmountOrg
     */
    public function setInvoiceAmountOrg($invoiceAmountOrg): void
    {
        $this->invoiceAmountOrg = $invoiceAmountOrg;
    }

    /**
     * @param mixed $documentData
     */
    public function setDocumentDate($documentDate): void
    {
        $this->documentDate = ($documentDate != "1970-01-01" || $documentDate != null) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($documentDate)) : null;
    }

    /**
     * @param mixed $balanceDecimalPlaces
     */
    public function setBalanceDecimalPlaces($balanceDecimalPlaces): void
    {
        $this->balanceDecimalPlaces = $balanceDecimalPlaces;
    }
    /**
     * @param mixed $supplierGroupName
     */
    public function setsupplierGroupName($supplierGroupName): void
    {
        $this->supplierGroupName = $supplierGroupName;
    }
    /**
     * @param mixed $invoiceAmount
     */
    public function setInvoiceAmount($invoiceAmount): void
    {
        $this->invoiceAmount = $invoiceAmount;
    }


    public function getHeader() : Array
    {
        return [
          "Document Code",
          "Posted Data",
          "Account",
          "Invoice Number",
          "Document Narration",
          "Currency",
          "Document Amount"
        ];
    }

    /**
     * @param mixed $documentCurrency
     */
    public function setDocumentCurrency($documentCurrency): void
    {
        $this->documentCurrency = $documentCurrency;
    }


    /**
     * @param mixed $accountDescription
     */
    public function setAccountDescription($accountDescription): void
    {
        $this->accountDescription = $accountDescription;
    }

    /**
     * @param mixed $glCode
     */
    public function setGlCode($glCode): void
    {
        $this->glCode = $glCode;
    }

    /**
     * @param mixed $documentSystemCode
     */
    public function setDocumentSystemCode($documentSystemCode): void
    {
        $this->documentSystemCode = $documentSystemCode;
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
     * @param mixed $documentCode
     */
    public function setDocumentCode($documentCode): void
    {
        $this->documentCode = $documentCode;
    }

    /**
     * @param mixed $postedDate
     */
    public function setPostedDate($postedDate): void
    {
        $this->postedDate = ($postedDate != "1970-01-01") ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($postedDate)) : null;
    }

    /**
     * @param mixed $account
     */
    public function setAccount($account): void
    {
        $this->account = $account;
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
        $this->invoiceDate = ($invoiceDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($invoiceDate)) : null;
    }

    /**
     * @param mixed $documentNarration
     */
    public function setDocumentNarration($documentNarration): void
    {
        $this->documentNarration = $documentNarration;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @param mixed $documentAmount
     */
    public function setDocumentAmount($documentAmount): void
    {
        $this->documentAmount = $documentAmount;
    }
}
