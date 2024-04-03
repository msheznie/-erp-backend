<?php

namespace App\Exports\Procument;

class SupplierWisePoAnalysisReport
{

    public $SupplierID;
    public $SupplierName;
    public $SupplierCountry;
    public $POCapexAmount;
    public $POOpexAmount;
    public $TotalPOAmount;
    public $GRVCapexAmount;
    public $GRVOpexAmount;
    public $TotalGRVAmount;
    public $CapexBalance;
    public $OpexBalance;

    public function getColumnFormat() : Array {
        return [
            'D' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'E' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
        ];
    }

    /**
     * @param mixed $SupplierID
     */
    public function setSupplierID($SupplierID): void
    {
        $this->SupplierID = $SupplierID;
    }

    /**
     * @param mixed $SupplierName
     */
    public function setSupplierName($SupplierName): void
    {
        $this->SupplierName = $SupplierName;
    }

    /**
     * @param mixed $SupplierCountry
     */
    public function setSupplierCountry($SupplierCountry): void
    {
        $this->SupplierCountry = $SupplierCountry;
    }

    /**
     * @param mixed $POCapexAmount
     */
    public function setPOCapexAmount($POCapexAmount): void
    {
        $this->POCapexAmount = $POCapexAmount;
    }

    /**
     * @param mixed $POOpexAmount
     */
    public function setPOOpexAmount($POOpexAmount): void
    {
        $this->POOpexAmount = $POOpexAmount;
    }

    /**
     * @param mixed $TotalPOAmount
     */
    public function setTotalPOAmount($TotalPOAmount): void
    {
        $this->TotalPOAmount = $TotalPOAmount;
    }

    /**
     * @param mixed $GRVCapexAmount
     */
    public function setGRVCapexAmount($GRVCapexAmount): void
    {
        $this->GRVCapexAmount = $GRVCapexAmount;
    }

    /**
     * @param mixed $GRVOpexAmount
     */
    public function setGRVOpexAmount($GRVOpexAmount): void
    {
        $this->GRVOpexAmount = $GRVOpexAmount;
    }

    /**
     * @param mixed $TotalGRVAmount
     */
    public function setTotalGRVAmount($TotalGRVAmount): void
    {
        $this->TotalGRVAmount = $TotalGRVAmount;
    }

    /**
     * @param mixed $CapexBalance
     */
    public function setCapexBalance($CapexBalance): void
    {
        $this->CapexBalance = $CapexBalance;
    }

    /**
     * @param mixed $OpexBalance
     */
    public function setOpexBalance($OpexBalance): void
    {
        $this->OpexBalance = $OpexBalance;
    }
    public function getHeaders(){
        return [
            'SupplierID',
            'Supplier Name',
            'Supplier Country',
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
}
