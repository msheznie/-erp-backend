<?php

namespace App\Exports\Procument;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

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
            __('custom.company_id'),
            __('custom.company_name'),
            __('custom.po_capex_amount'),
            __('custom.po_opex_amount'),
            __('custom.total_po_amount'),
            __('custom.grv_capex_amount'),
            __('custom.grv_opex_amount'),
            __('custom.total_grv_amount'),
            __('custom.capex_balance'),
            __('custom.opex_balance'),
        ];
    }


    public function getColumnFormat() : Array {
        return [
            'C' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
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
