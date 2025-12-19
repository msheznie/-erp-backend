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
    public $supplierGroupName;
    public $creditPeriod;
    public $currency;
    public $agingDays;

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

    public function getHeader($typeAging, $header) :Array {
        if($typeAging == 1) {
            return array_merge([
                trans('custom.company_id'),
                trans('custom.company_name'),
                trans('custom.account'),
                trans('custom.supplier_code'),
                trans('custom.supplier_name'),
                trans('custom.supplier_group'),
                trans('custom.credit_period'),
                trans('custom.currency'),
                trans('custom.aging_days'),
                ],
                $header,
                [
                trans('custom.total')
            ]);
        } else {
            return array_merge([
                trans('custom.company_id'),
                trans('custom.company_name'),
                trans('custom.account'),
                trans('custom.employee_code'),
                trans('custom.employee_name'),
                trans('custom.credit_period'),
                trans('custom.currency'),
                trans('custom.aging_days'),
                ],
                $header,
                [
                trans('custom.total')
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
