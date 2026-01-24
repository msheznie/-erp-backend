<?php

namespace App\Exports\AccountsPayable\InvoiceToPayment;

use App\helper\Helper;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class InvoiceToPaymentDetails
{
    public $documentCode;
    public $supplierName;
    public $supplierInvoiceNo;
    public $supplierInvoiceDate;
    public $currencyCode;
    public $totalAmount;
    public $confirmedDate;
    public $finalApprovedDate;
    public $postedDate;
    public $paymentVoucherNo;
    public $paidAmount;
    public $chequeNo;
    public $chequeDate;
    public $chequePrintedBy;
    public $chequePrintedDate;
    public $paymentClearedDate;

    public function getCloumnFormat()
    {
        return [
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'G' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'N' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'O' => NumberFormat::FORMAT_DATE_DDMMYYYY
        ];
    }

    public function getHeader(): Array
    {
        return [
            trans('custom.document_no'),
            trans('custom.supplier_name'),
            trans('custom.supplier_invoice_no'),
            trans('custom.supplier_invoice_date'),
            trans('custom.currency'),
            trans('custom.total_amount'),
            trans('custom.confirmed_date'),
            trans('custom.final_approved_date'),
            trans('custom.posted_date'),
            trans('custom.payment_voucher_no'),
            trans('custom.paid_amount'),
            trans('custom.cheque_no'),
            trans('custom.cheque_date'),
            trans('custom.cheque_printed_by'),
            trans('custom.cheque_printed_date'),
            trans('custom.payment_cleared_date')
        ];
    }

    /**
     * @param mixed $documentCode
     */
    public function setDocumentCode($documentCode): void
    {
        $this->documentCode = $documentCode;
    }

    /**
     * @param mixed $supplierName
     */
    public function setSupplierName($supplierName): void
    {
        $this->supplierName = $supplierName;
    }

    /**
     * @param mixed $supplierInvoiceNo
     */
    public function setSupplierInvoiceNo($supplierInvoiceNo): void
    {
        $this->supplierInvoiceNo = $supplierInvoiceNo;
    }

    /**
     * @param mixed $supplierInvoiceDate
     */
    public function setSupplierInvoiceDate($supplierInvoiceDate): void
    {
        $this->supplierInvoiceDate = $supplierInvoiceDate;
    }

    /**
     * @param mixed $currencyCode
     */
    public function setCurrencyCode($currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }

    /**
     * @param mixed $totalAmount
     */
    public function setTotalAmount($totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @param mixed $confirmedDate
     */
    public function setConfirmedDate($confirmedDate): void
    {
        $this->confirmedDate = $confirmedDate;
    }

    /**
     * @param mixed $finalApprovedDate
     */
    public function setFinalApprovedDate($finalApprovedDate): void
    {
        $this->finalApprovedDate = $finalApprovedDate;
    }

    /**
     * @param mixed $postedDate
     */
    public function setPostedDate($postedDate): void
    {
        $this->postedDate = $postedDate;
    }

    /**
     * @param mixed $paymentVoucherNo
     */
    public function setPaymentVoucherNo($paymentVoucherNo): void
    {
        $this->paymentVoucherNo = $paymentVoucherNo;
    }

    /**
     * @param mixed $paidAmount
     */
    public function setPaidAmount($paidAmount): void
    {
        $this->paidAmount = $paidAmount;
    }

    /**
     * @param mixed $chequeNo
     */
    public function setChequeNo($chequeNo): void
    {
        $this->chequeNo = $chequeNo;
    }

    /**
     * @param mixed $chequeDate
     */
    public function setChequeDate($chequeDate): void
    {
        $this->chequeDate = $chequeDate;
    }

    /**
     * @param mixed $chequePrintedBy
     */
    public function setChequePrintedBy($chequePrintedBy): void
    {
        $this->chequePrintedBy = $chequePrintedBy;
    }

    /**
     * @param mixed $chequePrintedDate
     */
    public function setChequePrintedDate($chequePrintedDate): void
    {
        $this->chequePrintedDate = $chequePrintedDate;
    }

    /**
     * @param mixed $paymentClearedDate
     */
    public function setPaymentClearedDate($paymentClearedDate): void
    {
        $this->paymentClearedDate = $paymentClearedDate;
    }
}
