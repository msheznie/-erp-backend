<?php

namespace App\Exports\Procument;

use App\helper\Helper;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PoToPaymentReport
{

    public $companyID;
    public $supplierCode;
    public $supplierName;
    public $poNumber;
    public $category;
    public $poApprovedDate;
    public $narration;
    public $poAmount;
    public $poStatus;
    public $grvCode;
    public $grvDate;
    public $grvAmount;
    public $logisticAmount;
    public $invoiceCode;
    public $invoiceDate;
    public $invoiceAmount;
    public $paymentCode;
    public $paymentDate;
    public $paymentPostedDate;
    public $paidAmount;

    public function getColumnFormat() {
        return [
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'P' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'R' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'S' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'T' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function getGroupedHeader() {
        return [
            '',
            __('custom.supplier_details'),
            '',
            __('custom.po_details'),
            '',
            '',
            '',
            '',
            '',
            __('custom.grv_details'),
            '',
            '',
            '',
            __('custom.invoice_details'),
            '',
            '',
            __('custom.payment_details'),
            '',
            '',
            ''
        ];
    }

    public function getHeader() {
        return [
            __('custom.company_id'),
            __('custom.supplier_code'),
            __('custom.supplier_name'),
            __('custom.po_number'),
            __('custom.category'),
            __('custom.po_approved_date'),
            __('custom.narration'),
            __('custom.po_amount'),
            __('custom.po_status'),
            __('custom.grv_code'),
            __('custom.grv_date'),
            __('custom.grv_amount'),
            __('custom.logistic_amount'),
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
     * @param mixed $poStatus
     */
    public function setPoStatus($poStatus): void
    {
        $this->poStatus = $poStatus;
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
