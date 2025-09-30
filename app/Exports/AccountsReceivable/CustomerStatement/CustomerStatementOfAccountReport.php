<?php

namespace App\Exports\AccountsReceivable\CustomerStatement;

class CustomerStatementOfAccountReport
{

    public $companyID;
    public $companyName;
    public $customerName;
    public $documentCode;
    public $postedDate;
    public $contract;
    public $poNumber;
    public $invoiceDate;
    public $narration;
    public $currency;
    public $invoiceAmount;
    public $receiptCNCode;
    public $receiptCNDate;
    public $receiptAmount;
    public $balanceAmount;

    public function getColumnFormat() {
        return [
            'E' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'H' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'M' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'K' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function getHeader() {
        return  [
            trans('custom.company_id'),
            trans('custom.company_name'),
            trans('custom.customer_name'),
            trans('custom.document_code'),
            trans('custom.posted_date'),
            trans('custom.contract'),
            trans('custom.po_number'),
            trans('custom.invoice_date'),
            trans('custom.narration'),
            trans('custom.currency'),
            trans('custom.invoice_amount'),
            trans('custom.receipt_cn_code'),
            trans('custom.receipt_cn_date'),
            trans('custom.receipt_amount'),
            trans('custom.balance_amount')
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
        $this->postedDate = $postedDate;
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
     * @param mixed $invoiceDate
     */
    public function setInvoiceDate($invoiceDate): void
    {
        $this->invoiceDate = $invoiceDate;
    }

    /**
     * @param mixed $narration
     */
    public function setNarration($narration): void
    {
        $this->narration = $narration;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @param mixed $invoiceAmount
     */
    public function setInvoiceAmount($invoiceAmount): void
    {
        $this->invoiceAmount = $invoiceAmount;
    }

    /**
     * @param mixed $receiptCNCode
     */
    public function setReceiptCNCode($receiptCNCode): void
    {
        $this->receiptCNCode = $receiptCNCode;
    }

    /**
     * @param mixed $receiptCNDate
     */
    public function setReceiptCNDate($receiptCNDate): void
    {
        $this->receiptCNDate = $receiptCNDate;
    }

    /**
     * @param mixed $receiptAmount
     */
    public function setReceiptAmount($receiptAmount): void
    {
        $this->receiptAmount = $receiptAmount;
    }

    /**
     * @param mixed $balanceAmount
     */
    public function setBalanceAmount($balanceAmount): void
    {
        $this->balanceAmount = $balanceAmount;
    }
}
