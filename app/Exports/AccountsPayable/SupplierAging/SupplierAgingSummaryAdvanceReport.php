<?php

namespace App\Exports\AccountsPayable\SupplierAging;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SupplierAgingSummaryAdvanceReport
{
    public $companyID;
    public $companyName;
    public $account;
    public $supplierCode;
    public $supplierName;
    public $creditPeriod;
    public $currency;
    public $agingDays;
    public $column1;
    public $column2;
    public $column3;
    public $column4;
    public $column5;
    public $total;

    /**
     * @return mixed
     */
    public function getCloumnFormat()
    {
        return [
            'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'M' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,

        ];
    }

    public function getHeader($typeAging) :Array {
        if($typeAging == 1) {
            return [
                'Company ID',
                'Company Name',
                'Account',
                'Supplier Code',
                'Supplier Name',
                'Credit Period',
                'Currency',
                'Aging Days',
                '0-30',
                '31-60',
                '61-90',
                '91-100',
                '> 100',
                'Total'
            ];
        } else {
            return [
                'Company ID',
                'Company Name',
                'Account',
                'Employee Code',
                'Employee Name',
                'Credit Period',
                'Currency',
                'Aging Days',
                '0-30',
                '31-60',
                '61-90',
                '91-100',
                '> 100',
                'Total'
            ];
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
     * @param mixed $account
     */
    public function setAccount($account): void
    {
        $this->account = $account;
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
     * @param mixed $creditPeriod
     */
    public function setCreditPeriod($creditPeriod): void
    {
        $this->creditPeriod = $creditPeriod;
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
     * @param mixed $total
     */
    public function setTotal($total): void
    {
        $this->total = $total;
    }
}
