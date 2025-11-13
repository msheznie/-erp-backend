<?php

namespace App\Exports\AccountsReceivable;

use App\helper\Helper;

class CustomerAgingDetailReport
{
    public $companyID;
    public $companyName;
    public $documentCode;
    public $documentDate;
    public $glCode;
    public $customerCode;
    public $customerName;
    public $creditDays;
    public $department;
    public $contractID;
    public $invoiceNumber;
    public $poNumber;

    public $invoiceDate;
    public $ageDays;

    public $invoiceDueDate;
    public $documentNarration;
    public $currency;
    public $invoiceAmount;
    public $outStanding;
   

    public function getCloumnFormat():Array {
        return [
            'D' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'M' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'O' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'R' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'S' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'T' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'U' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'V' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'W' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'X' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Y' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Z' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
        ];
    }

    public function getHeader($header) :Array {
        return array_merge([
            trans('custom.company_id'),
            trans('custom.company_name'),
            trans('custom.document_code'),
            trans('custom.document_date'),
            trans('custom.gl_code'),
            trans('custom.customer_code'),
            trans('custom.customer_name'),
            trans('custom.credit_days'),
            trans('custom.department'),
            trans('custom.contract_id'),
            trans('custom.invoice_number'),
            trans('custom.po_number'),
            trans('custom.invoice_date'),
            trans('custom.aged_days'),
            trans('custom.invoice_due_date'),
            trans('custom.document_narration'),
            trans('custom.currency'),
            trans('custom.invoice_amount'),
            trans('custom.outstanding'),
            ],
            $header,
            [
            trans('custom.current_outstanding'),
            trans('custom.subsequent_collection_amount'),
            trans('custom.receipt_matching_brvno'),
            trans('custom.collection_tracker_status')

        ]);
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
     * @param mixed $glCode
     */
    public function setGlCode($glCode): void
    {
        $this->glCode = $glCode;
    }

    /**
     * @param mixed $customerCode
     */
    public function setCustomerCode($customerCode): void
    {
        $this->customerCode = $customerCode;
    }

    /**
     * @param mixed $customerName
     */
    public function setCustomerName($customerName): void
    {
        $this->customerName = $customerName;
    }

    /**
     * @param mixed $creditDays
     */
    public function setCreditDays($creditDays): void
    {
        $this->creditDays = $creditDays;
    }

    /**
     * @param mixed $department
     */
    public function setDepartment($department): void
    {
        $this->department = $department;
    }

    /**
     * @param mixed $contractID
     */
    public function setContractID($contractID): void
    {
        $this->contractID = $contractID;
    }

    /**
     * @param mixed $invoiceNumber
     */
    public function setInvoiceNumber($invoiceNumber): void
    {
        $this->invoiceNumber = $invoiceNumber;
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
        $this->invoiceDate = ($invoiceDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($invoiceDate)) : null;
    }

    /**
     * @param mixed $ageDays
     */
    public function setAgeDays($ageDays): void
    {
        $this->ageDays = $ageDays;
    }

    /**
     * @param mixed $invoiceDueDate
     */
    public function setInvoiceDueDate($invoiceDueDate): void
    {
        $this->invoiceDueDate = ($invoiceDueDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($invoiceDueDate)) : null;
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
     * @param mixed $invoiceAmount
     */
    public function setInvoiceAmount($invoiceAmount): void
    {
        $this->invoiceAmount = $invoiceAmount;
    }

    /**
     * @param mixed $outStanding
     */
    public function setOutStanding($outStanding): void
    {
        $this->outStanding = $outStanding;
    }

    /**
     * @param mixed $column1
     */
    public function setColumn1($column1): void
    {
        $this->column1 = $column1;
    }

    /**
     * @param mixed $column2
     */
    public function setColumn2($column2): void
    {
        $this->column2 = $column2;
    }

    /**
     * @param mixed $column3
     */
    public function setColumn3($column3): void
    {
        $this->column3 = $column3;
    }

    /**
     * @param mixed $column4
     */
    public function setColumn4($column4): void
    {
        $this->column4 = $column4;
    }

    /**
     * @param mixed $column5
     */
    public function setColumn5($column5): void
    {
        $this->column5 = $column5;
    }

    /**
     * @param mixed $currentOutstanding
     */
    public function setCurrentOutstanding($currentOutstanding): void
    {
        $this->currentOutstanding = $currentOutstanding;
    }

    /**
     * @param mixed $collectionAmount
     */
    public function setCollectionAmount($collectionAmount): void
    {
        $this->collectionAmount = $collectionAmount;
    }

    /**
     * @param mixed $receiptMatchingNo
     */
    public function setReceiptMatchingNo($receiptMatchingNo): void
    {
        $this->receiptMatchingNo = $receiptMatchingNo;
    }

    /**
     * @param mixed $collectionTrackerStatus
     */
    public function setCollectionTrackerStatus($collectionTrackerStatus): void
    {
        $this->collectionTrackerStatus = $collectionTrackerStatus;
    }

}
