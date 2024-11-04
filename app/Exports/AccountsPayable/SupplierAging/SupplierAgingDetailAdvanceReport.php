<?php

namespace App\Exports\AccountsPayable\SupplierAging;

use App\helper\Helper;

class SupplierAgingDetailAdvanceReport
{
    public $companyID;
    public $companyName;
    public $documentDate;
    public $documentCode;
    public $account;
    public $narrtion;
    public $supplierCode;
    public $supplierName;
    public $invoiceNumber;
    public $invoiceDate;
    public $currency;
    public $agingDays;

    /**
     * @return mixed
     */
    public function getCloumnFormat()
    {
        return [
            'C' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'J' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'M' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'P' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Q' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'R' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
        ];
    }
    public function getHeader($typeAging, $header) :Array {
        if($typeAging == 1) {
            return array_merge([
                'Company ID',
                'Company Name',
                'Doc Date',
                'Doc Number',
                'Account',
                'Narration',
                'Supplier Code',
                'Supplier Name',
                'Invoice Number',
                'Invoice Date',
                'Currency',
                'Aging Days',
                ],
                $header,
                [
                'Advance/UnAllocated Amount'
                ]);
        } else {
            return array_merge([
                'Company ID',
                'Company Name',
                'Doc Date',
                'Doc Number',
                'Account',
                'Narration',
                'Employee Code',
                'Employee Name',
                'Invoice Number',
                'Invoice Date',
                'Currency',
                'Aging Days',
                ],
                $header,
                [
                'Advance/UnAllocated Amount'
            ]);
        }
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
     * @param mixed $documentDate
     */
    public function setDocumentDate($documentDate): void
    {
        $this->documentDate = ($documentDate)  ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($documentDate)) : null;
    }

    /**
     * @param mixed $documentCode
     */
    public function setDocumentCode($documentCode): void
    {
        $this->documentCode = $documentCode;
    }

    /**
     * @param mixed $account
     */
    public function setAccount($account): void
    {
        $this->account = $account;
    }

    /**
     * @param mixed $narrtion
     */
    public function setNarrtion($narrtion): void
    {
        $this->narrtion = $narrtion;
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
     * @param mixed $agingDays
     */
    public function setAgingDays($agingDays): void
    {
        $this->agingDays = $agingDays;
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
     * @param mixed $advanceAmount
     */
    public function setAdvanceAmount($advanceAmount): void
    {
        $this->advanceAmount = $advanceAmount;
    }
}
