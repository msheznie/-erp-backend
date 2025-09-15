<?php

namespace App\Exports\Procument;

use App\helper\Helper;

class PoToPaymentReport
{

    public $companyID;
    public $poNumber;
    public $category;
    public $poApprovedDate;
    public $narration;
    public $supplierCode;
    public $supplierName;
    public $poAmount;
    public $logisticAmount;
    public $grvCode;
    public $grvDate;
    public $grvAmount;
    public $invoiceCode;
    public $invoiceDate;
    public $invoiceAmount;
    public $paymentCode;
    public $paymentDate;
    public $paymentPostedDate;
    public $paidAmount;

    public function getColumnFormat() {
        return [
            'D' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'K' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'N' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'Q' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'R' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'G' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'H' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'S' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function getHeader() {
        return [
            __('custom.company_id'),
            __('custom.po_number'),
            __('custom.category'),
            __('custom.po_approved_date'),
            __('custom.narration'),
            __('custom.supplier_code'),
            __('custom.supplier_name'),
            __('custom.po_amount'),
            __('custom.logistic_amount'),
            __('custom.grv_code'),
            __('custom.grv_date'),
            __('custom.grv_amount'),
            __('custom.invoice_code'),
            __('custom.invoice_date'),
            __('custom.invoice_amount'),
            __('custom.payment_code'),
            __('custom.payment_date'),
            __('custom.payment_posted_date'),
            __('custom.paid_amount')
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
     * @param mixed $poNumber
     */
    public function setPoNumber($poNumber): void
    {
        $this->poNumber = $poNumber;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category): void
    {
        $this->category = $category;
    }

    /**
     * @param mixed $poApprovedDate
     */
    public function setPoApprovedDate($poApprovedDate): void
    {
        $this->poApprovedDate = ($poApprovedDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($poApprovedDate)): null;
    }

    /**
     * @param mixed $narration
     */
    public function setNarration($narration): void
    {
        $this->narration = $narration;
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
     * @param mixed $poAmount
     */
    public function setPoAmount($poAmount): void
    {
        $this->poAmount = $poAmount;
    }

    /**
     * @param mixed $logisticAmount
     */
    public function setLogisticAmount($logisticAmount): void
    {
        $this->logisticAmount = $logisticAmount;
    }

    /**
     * @param mixed $grvCode
     */
    public function setGrvCode($grvCode): void
    {
        $this->grvCode = $grvCode;
    }

    /**
     * @param mixed $grvDate
     */
    public function setGrvDate($grvDate): void
    {
        $this->grvDate = ($grvDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($grvDate)) : null;
    }

    /**
     * @param mixed $grvAmount
     */
    public function setGrvAmount($grvAmount): void
    {
        $this->grvAmount = $grvAmount;
    }

    /**
     * @param mixed $invoiceCode
     */
    public function setInvoiceCode($invoiceCode): void
    {
        $this->invoiceCode = $invoiceCode;
    }

    /**
     * @param mixed $invoiceDate
     */
    public function setInvoiceDate($invoiceDate): void
    {
        $this->invoiceDate = ($invoiceDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($invoiceDate)) : null;
    }

    /**
     * @param mixed $invoiceAmount
     */
    public function setInvoiceAmount($invoiceAmount): void
    {
        $this->invoiceAmount = $invoiceAmount;
    }

    /**
     * @param mixed $paymentCode
     */
    public function setPaymentCode($paymentCode): void
    {
        $this->paymentCode = $paymentCode;
    }

    /**
     * @param mixed $paymentDate
     */
    public function setPaymentDate($paymentDate): void
    {
        $this->paymentDate = ($paymentDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($paymentDate)): null;
    }

    /**
     * @param mixed $paymentPostedDate
     */
    public function setPaymentPostedDate($paymentPostedDate): void
    {
        $this->paymentPostedDate = ($paymentPostedDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($paymentPostedDate)): null;
    }

    /**
     * @param mixed $paidAmount
     */
    public function setPaidAmount($paidAmount): void
    {
        $this->paidAmount = $paidAmount;
    }


}
