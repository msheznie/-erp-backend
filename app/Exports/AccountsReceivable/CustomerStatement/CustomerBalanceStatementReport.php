<?php

namespace App\Exports\AccountsReceivable\CustomerStatement;

use App\helper\Helper;

class CustomerBalanceStatementReport
{

    public $companyID;
    public $companyName;
    public $customerName;
    public $documentCode;
    public $postedDate;
    public $narration;
    public $contract;
    public $poNumber;
    public $invoiceNumber;
    public $invoiceDate;
    public $currency;
    public $balanceAmount;


    public function getColumnFormat() {
        return [
            'E' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'J' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'L' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
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
     * @param mixed $customerName
     */
    public function setCustomerName($customerName): void
    {
        $this->customerName = $customerName;
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
        $this->postedDate = ($postedDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($postedDate)) : null;
    }

    /**
     * @param mixed $narration
     */
    public function setNarration($narration): void
    {
        $this->narration = $narration;
    }

    /**
     * @param mixed $contract
     */
    public function setContract($contract): void
    {
        $this->contract = $contract;
    }

    /**
     * @param mixed $poNumber
     */
    public function setPoNumber($poNumber): void
    {
        $this->poNumber = $poNumber;
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
     * @param mixed $balanceAmount
     */
    public function setBalanceAmount($balanceAmount): void
    {
        $this->balanceAmount = $balanceAmount;
    }

    public function getHeader() {
        return  [
            trans('custom.company_id'),
            trans('custom.company_name'),
            trans('custom.customer_name'),
            trans('custom.document_code'),
            trans('custom.posted_date'),
            trans('custom.narration'),
            trans('custom.contract'),
            trans('custom.po_number'),
            trans('custom.invoice_number'),
            trans('custom.invoice_date'),
            trans('custom.currency'),
            trans('custom.balance_amount')
        ];
    }
}
