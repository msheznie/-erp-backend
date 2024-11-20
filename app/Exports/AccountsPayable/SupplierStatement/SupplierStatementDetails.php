<?php

namespace App\Exports\AccountsPayable\SupplierStatement;

class SupplierStatementDetails
{
    public $payableAccount;
    public $prepaymentAccount;
    public $currency;
    public $supplierName;
    public $supplierGroupName;
    public $openSupplierInvoices;
    public $openAdvanceToSuppliers;
    public $openDebitNotes;
    public $totalPayable;
    public $totalPrepayment;
    public $netOutstanding;


    public function getCloumnFormat():Array {
        return [
            'E' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
        ];
    }
    /**
     * @param mixed $payableAccount
     */
    public function setPayableAccount($payableAccount): void
    {
        $this->payableAccount = $payableAccount;
    }

    /**
     * @param mixed $prepaymentAccount
     */
    public function setPrepaymentAccount($prepaymentAccount): void
    {
        $this->prepaymentAccount = $prepaymentAccount;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @param mixed $supplierName
     */
    public function setSupplierName($supplierName): void
    {
        $this->supplierName = $supplierName;
    }
    /**
     * @param mixed $supplierGroupName
     */
    public function setsupplierGroupName($supplierGroupName): void
    {
        $this->supplierGroupName = $supplierGroupName;
    }

    /**
     * @param mixed $openSupplierInvoices
     */
    public function setOpenSupplierInvoices($openSupplierInvoices): void
    {
        $this->openSupplierInvoices = $openSupplierInvoices;
    }

    /**
     * @param mixed $openAdvanceToSuppliers
     */
    public function setOpenAdvanceToSuppliers($openAdvanceToSuppliers): void
    {
        $this->openAdvanceToSuppliers = $openAdvanceToSuppliers;
    }

    /**
     * @param mixed $openDebitNotes
     */
    public function setOpenDebitNotes($openDebitNotes): void
    {
        $this->openDebitNotes = $openDebitNotes;
    }

    /**
     * @param mixed $totalPayable
     */
    public function setTotalPayable($totalPayable): void
    {
        $this->totalPayable = $totalPayable;
    }

    /**
     * @param mixed $totalPrepayment
     */
    public function setTotalPrepayment($totalPrepayment): void
    {
        $this->totalPrepayment = $totalPrepayment;
    }

    /**
     * @param mixed $netOutstanding
     */
    public function setNetOutstanding($netOutstanding): void
    {
        $this->netOutstanding = $netOutstanding;
    }

    public function getHeader():Array
    {
        return  [
            'Payable Account',
            'Prepayment Account',
            'Currency',
            'Supplier Name',
            'Supplier Group',
            'Open Supplier Invoices',
            'Open Advance to Suppliers',
            'Open Debit Notes',
            'Total Payable',
            'Total Prepayment',
            'Net Outstanding'
        ];
    }
}
