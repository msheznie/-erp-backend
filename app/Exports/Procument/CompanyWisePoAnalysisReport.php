<?php

namespace App\Exports\Procument;

class CompanyWisePoAnalysisReport
{
    private $companyID;
    private $companyName;
    private $poCapexAmount;
    private $poOpexAmount;
    private $totalPoAmount;
    private $grvCapexAmount;
    private $grvOpexAmount;
    private $totalGrvAmount;
    private $capexBalance;
    private $opexBalance;

    public function getHeaders()
    {
        return [
            'CompanyID',
            'Company Name',
            'PO Capex Amount',
            'PO Opex Amount',
            'Total PO Amount',
            'GRV Capex Amount',
            'GRV Opex Amount',
            'Total GRV Amount',
            'Capex Balance',
            'Opex Balance',
        ];
    }


    public function getColumnFormat() : Array {
        return [
            'C' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'D' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'E' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
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
     * @param mixed $companyName
     */
    public function setCompanyName($companyName): void
    {
        $this->companyName = $companyName;
    }

    /**
     * @param mixed $poCapexAmount
     */
    public function setPoCapexAmount($poCapexAmount): void
    {
        $this->poCapexAmount = $poCapexAmount;
    }

    /**
     * @param mixed $poOpexAmount
     */
    public function setPoOpexAmount($poOpexAmount): void
    {
        $this->poOpexAmount = $poOpexAmount;
    }

    /**
     * @param mixed $totalPoAmount
     */
    public function setTotalPoAmount($totalPoAmount): void
    {
        $this->totalPoAmount = $totalPoAmount;
    }

    /**
     * @param mixed $grvCapexAmount
     */
    public function setGrvCapexAmount($grvCapexAmount): void
    {
        $this->grvCapexAmount = $grvCapexAmount;
    }

    /**
     * @param mixed $grvOpexAmount
     */
    public function setGrvOpexAmount($grvOpexAmount): void
    {
        $this->grvOpexAmount = $grvOpexAmount;
    }

    /**
     * @param mixed $totalGrvAmount
     */
    public function setTotalGrvAmount($totalGrvAmount): void
    {
        $this->totalGrvAmount = $totalGrvAmount;
    }

    /**
     * @param mixed $capexBalance
     */
    public function setCapexBalance($capexBalance): void
    {
        $this->capexBalance = $capexBalance;
    }

    /**
     * @param mixed $opexBalance
     */
    public function setOpexBalance($opexBalance): void
    {
        $this->opexBalance = $opexBalance;
    }
}
