<?php

namespace App\Exports\AccountsPayable\UnbilledGRV;

class UnbilledGrvDetailsSummaryReport
{
    public $companyId;
    public $supplierCode;
    public $supplierName;
    public $docValueLocalCurrency;
    public $matachedValueLocalCurrency;
    public $balanceLocalCurrency;
    public $docValueReportingCurrency;
    public $matchedValueReportingCurrency;
    public $balanceReportingCurrency;
    public $cloumn_format;

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
     * @param mixed $docValueLocalCurrency
     */
    public function setDocValueLocalCurrency($docValueLocalCurrency): void
    {
        $this->docValueLocalCurrency = $docValueLocalCurrency;
    }

    /**
     * @param mixed $matachedValueLocalCurrency
     */
    public function setMatachedValueLocalCurrency($matachedValueLocalCurrency): void
    {
        $this->matachedValueLocalCurrency = $matachedValueLocalCurrency;
    }

    /**
     * @param mixed $balanceLocalCurrency
     */
    public function setBalanceLocalCurrency($balanceLocalCurrency): void
    {
        $this->balanceLocalCurrency = $balanceLocalCurrency;
    }

    /**
     * @param mixed $docValueReportingCurrency
     */
    public function setDocValueReportingCurrency($docValueReportingCurrency): void
    {
        $this->docValueReportingCurrency = $docValueReportingCurrency;
    }

    /**
     * @param mixed $matchedValueReportingCurrency
     */
    public function setMatchedValueReportingCurrency($matchedValueReportingCurrency): void
    {
        $this->matchedValueReportingCurrency = $matchedValueReportingCurrency;
    }

    /**
     * @param mixed $balanceReportingCurrency
     */
    public function setBalanceReportingCurrency($balanceReportingCurrency): void
    {
        $this->balanceReportingCurrency = $balanceReportingCurrency;
    }

    /**
     * @return mixed
     */
    public function getCloumnFormat()
    {
        return [
            'D' => \PHPExcel_Style_NumberFormat::FORMAT_GENERAL
        ];
    }
    public function getHeader() :Array {
        return [
            'Company ID',
            'Supplier Code',
            'Supplier Name',
            'Doc Value (Local Currency)',
            'Matched Value (Local Currency)',
            'Balance (Local Currency)',
            'Doc Value (Reporting Currency)',
            'Matched Value (Reporting Currency)',
            'Balance (Reporting Currency)'
        ];
    }
}
