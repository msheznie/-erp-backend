<?php

namespace App\Exports\AccountsPayable\SupplierAging;

class SupplierAgingSummaryReport
{
    public $companyID;
    public $companyName;
    public $account;
    public $supplierCode;
    public $supplierName;
    public $supplierGroupName;
    public $creditPeriod;
    public $currency;
    public $agingDays;

    public function getCloumnFormat()
    {
        return [
            'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'M' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
        ];
    }
    public function getHeader($typeAging, $header) :Array {
        if($typeAging == 1) {
            return array_merge([
                'Company ID',
                'Company Name',
                'Account',
                'Supplier Code',
                'Supplier Name',
                'Supplier Group',
                'Credit Period',
                'Currency',
                'Aging Days',
                ],
                $header,
                [
                'Advance/UnAllocated Amount',
                'Total'
            ]);
        }  else {
            return array_merge([
                'Company ID',
                'Company Name',
                'Account',
                'Employee Code',
                'Employee Name',
                'Credit Period',
                'Currency',
                'Aging Days',
                ],
                $header,
                [
                'Advance/UnAllocated Amount',
                'Total'
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
     * @param mixed $supplierGroupName
     */
    public function setsupplierGroupName($supplierGroupName): void
    {
        $this->supplierGroupName = $supplierGroupName;
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
     * @param mixed $advanceAmount
     */
    public function setAdvanceAmount($advanceAmount): void
    {
        $this->advanceAmount = $advanceAmount;
    }

    /**
     * @param mixed $total
     */
    public function setTotal($total): void
    {
        $this->total = $total;
    }
}
