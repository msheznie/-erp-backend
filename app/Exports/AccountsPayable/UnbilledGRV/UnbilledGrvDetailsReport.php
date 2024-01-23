<?php

namespace App\Exports\AccountsPayable\UnbilledGRV;

use App\helper\Helper;

class UnbilledGrvDetailsReport
{
    public $companyId;
    public $supplierCode;
    public $supplierName;
    public $docNumber;
    public $docDate;
    public $docValueLocalCurrency;
    public $matachedValueLocalCurrency;
    public $balanceLocalCurrency;
    public $docValueReportingCurrency;
    public $matchedValueReportingCurrency;
    public $balanceReportingCurrency;
    public $cloumn_format;

    /**
     * @return mixed
     */
    public function getCloumnFormat()
    {
        return [
            'E' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1



        ];
    }
    public function getHeader() :Array {
        return [
            'Company ID',
            'Supplier Code',
            'Supplier Name',
            'Doc Number',
            'Doc Date',
            'Doc Value (Local Currency)',
            'Matched Value (Local Currency)',
            'Balance (Local Currency)',
            'Doc Value (Reporting Currency)',
            'Matched Value (Reporting Currency)',
            'Balance (Reporting Currency)'
        ];
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
}
