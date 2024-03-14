<?php

namespace App\Exports\AccountsPayable\SupplierStatement;

use App\helper\Helper;

class SupplierStatementReport
{

    public $companyId;
    public $companyName;
    public $supplierCode;
    public $supplierName;
    public $documentId;
    public $documentCode;
    public $documentDate;
    public $account;
    public $narration;
    public $invoiceNumber;
    public $invoiceDate;
    public $currency;
    public $ageDays;
    public $docAmount;
    public $balanceAmount;




    public function getCloumnFormat():Array {
        return [
            'G' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'K' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'N' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
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
     * @param mixed $documentId
     */
    public function setDocumentId($documentId): void
    {
        $this->documentId = $documentId;
    }

    /**
     * @param mixed $documentCode
     */
    public function setDocumentCode($documentCode): void
    {
        $this->documentCode = $documentCode;
    }

    /**
     * @param mixed $documentDate
     */
    public function setDocumentDate($documentDate): void
    {
        $this->documentDate = ($documentDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($documentDate)) : null;
    }

    /**
     * @param mixed $account
     */
    public function setAccount($account): void
    {
        $this->account = $account;
    }

    /**
     * @param mixed $narration
     */
    public function setNarration($narration): void
    {
        $this->narration = $narration;
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
     * @param mixed $currency
     */
    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @param mixed $ageDays
     */
    public function setAgeDays($ageDays): void
    {
        $this->ageDays = $ageDays;
    }

    /**
     * @param mixed $docAmount
     */
    public function setDocAmount($docAmount): void
    {
        $this->docAmount = $docAmount;
    }

    /**
     * @param mixed $balanceAmount
     */
    public function setBalanceAmount($balanceAmount): void
    {
        $this->balanceAmount = $balanceAmount;
    }


    public function getHeader()
    {
        return  [
            'Company ID',
            'Company Name',
            'Supplier Code',
            'Supplier Name',
            'Document ID',
            'Document Code',
            'Document Date',
            'Account',
            'Narration',
            'Invoice Number',
            'Invoice Date',
            'Currency',
            'Age Days',
            'Doc Amount',
            'Balance Amount'
        ];
    }
}
