<?php

namespace App\Exports\AccountsPayable\UnbilledGRV;

use App\helper\Helper;

class UnbilledGrvAgingSummaryReport
{
    public $companyId;
    public $supplierCode;
    public $supplierName;
    public $docNumber;
    public $docDate;
    public $docValueCurerncy;
    public $matchedValueCurrency;
    public $balanceCurrency;
    public $lessThan30;
    public $column2;
    public $column3;
    public $column4;
    public $column5;
    public $column6;
    public $column7;
    public $column8;
    public $column9;
    public $column10;

    public function getCloumnFormat()
    {
        return [
            'E' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'M' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'P' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Q' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'R' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
        ];
    }
    public function getHeader($currencyId) :Array {
        if ($currencyId == 2) {
            return [
                trans('custom.company_id'),
                trans('custom.supplier_code'),
                trans('custom.supplier_name'),
                trans('custom.doc_number'),
                trans('custom.doc_date'),
                trans('custom.doc_value_local_currency'),
                trans('custom.matched_value_local_currency'),
                trans('custom.balance_local_currency'),
                trans('custom.aging_0_30'),
                trans('custom.aging_31_60'),
                trans('custom.aging_61_90'),
                trans('custom.aging_91_120'),
                trans('custom.aging_121_150'),
                trans('custom.aging_151_180'),
                trans('custom.aging_181_210'),
                trans('custom.aging_211_240'),
                trans('custom.aging_241_365'),
                trans('custom.over_365')
            ];
        }else {
            return [
                trans('custom.company_id'),
                trans('custom.supplier_code'),
                trans('custom.supplier_name'),
                trans('custom.doc_number'),
                trans('custom.doc_date'),
                trans('custom.doc_value_reporting_currency'),
                trans('custom.matched_value_reporting_currency'),
                trans('custom.balance_reporting_currency'),
                trans('custom.aging_0_30'),
                trans('custom.aging_31_60'),
                trans('custom.aging_61_90'),
                trans('custom.aging_91_120'),
                trans('custom.aging_121_150'),
                trans('custom.aging_151_180'),
                trans('custom.aging_181_210'),
                trans('custom.aging_211_240'),
                trans('custom.aging_241_365'),
                trans('custom.over_365')
            ];
        }

    }

    /**
     * @param mixed $companyId
     */
    public function setCompanyId($companyId): void
    {
        $this->companyId = $companyId;
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
     * @param mixed $docNumber
     */
    public function setDocNumber($docNumber): void
    {
        $this->docNumber = $docNumber;
    }

    /**
     * @param mixed $docDate
     */
    public function setDocDate($docDate): void
    {
        $this->docDate = ($docDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($docDate)) : null;
    }

    /**
     * @param mixed $docValueCurerncy
     */
    public function setDocValueCurerncy($docValueCurerncy): void
    {
        $this->docValueCurerncy = $docValueCurerncy;
    }

    /**
     * @param mixed $matchedValueCurrency
     */
    public function setMatchedValueCurrency($matchedValueCurrency): void
    {
        $this->matchedValueCurrency = $matchedValueCurrency;
    }

    /**
     * @param mixed $balanceCurrency
     */
    public function setBalanceCurrency($balanceCurrency): void
    {
        $this->balanceCurrency = $balanceCurrency;
    }

    /**
     * @param mixed $lessThan30
     */
    public function setLessThan30($lessThan30): void
    {
        $this->lessThan30 = $lessThan30;
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
     * @param mixed $column6
     */
    public function setColumn6($column6): void
    {
        $this->column6 = $column6;
    }

    /**
     * @param mixed $column7
     */
    public function setColumn7($column7): void
    {
        $this->column7 = $column7;
    }

    /**
     * @param mixed $column8
     */
    public function setColumn8($column8): void
    {
        $this->column8 = $column8;
    }

    /**
     * @param mixed $column9
     */
    public function setColumn9($column9): void
    {
        $this->column9 = $column9;
    }

    /**
     * @param mixed $column10
     */
    public function setColumn10($column10): void
    {
        $this->column10 = $column10;
    }


}
