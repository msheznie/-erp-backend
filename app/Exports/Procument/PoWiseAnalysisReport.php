<?php

namespace App\Exports\Procument;

use App\helper\Helper;

class PoWiseAnalysisReport
{

    public $companyId;
    public $poCode;
    public $segment;
    public $narration;
    public $approvedDate;
    public $createdDate;
    public $expectedDeliveryDate;
    public $supplierCode;
    public $supplierName;
    public $supplierCountry;
    public $lcc;
    public $sme;
    public $icvCategory;
    public $icvSubCategory;
    public $budgetYear;
    public $poCapexAmount;
    public $poOpexAmount;
    public $totalPoAmount;
    public $logisticAmount;
    public $grvCapexAmount;
    public $grvOpexAmount;
    public $totalGrvAmount;
    public $capexBalance;
    public $opexBalance;
    public $advanceReleased;
    public $logisticAdvanceReleased;
    public $paymentReleasedFromInvoice;
    public $balanceToBePaid;
    public $isManuallyClosed;

    public function getHeader() {
        return [
            __('custom.company_id'),
            __('custom.po_code'),
            __('custom.segment'),
            __('custom.narration'),
            __('custom.approved_date'),
            __('custom.created_date'),
            __('custom.expected_delivery_date'),
            __('custom.supplier_code'),
            __('custom.supplier_name'),
            __('custom.supplier_country'),
            __('custom.lcc'),
            __('custom.sme'),
            __('custom.icv_category'),
            __('custom.icv_sub_category'),
            __('custom.budget_year'),
            __('custom.po_capex_amount'),
            __('custom.po_opex_amount'),
            __('custom.total_po_amount'),
            __('custom.logistic_amount'),
            __('custom.grv_capex_amount'),
            __('custom.grv_opex_amount'),
            __('custom.total_grv_amount'),
            __('custom.capex_balance'),
            __('custom.opex_balance'),
            __('custom.advance_released'),
            __('custom.logistic_advance_released'),
            __('custom.payment_released_from_invoice'),
            __('custom.balance_to_be_paid'),
            __('custom.is_manually_closed'),
        ];
    }

    public function getColumnFormat() {
        return [
            'E' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'G' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'P' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Q' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'R' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'S' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'T' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'U' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'V' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'W' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'X' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Y' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Z' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AA' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AB' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
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
     * @param mixed $poCode
     */
    public function setPoCode($poCode): void
    {
        $this->poCode = $poCode;
    }

    /**
     * @param mixed $segment
     */
    public function setSegment($segment): void
    {
        $this->segment = $segment;
    }

    /**
     * @param mixed $narration
     */
    public function setNarration($narration): void
    {
        $this->narration = $narration;
    }

    /**
     * @param mixed $approvedDate
     */
    public function setApprovedDate($approvedDate): void
    {
        $this->approvedDate = ($approvedDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($approvedDate)) : null;
    }

    /**
     * @param mixed $createdDate
     */
    public function setCreatedDate($createdDate): void
    {
        $this->createdDate = ($createdDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($createdDate)) : null;
    }

    /**
     * @param mixed $expectedDeliveryDate
     */
    public function setExpectedDeliveryDate($expectedDeliveryDate): void
    {
        $this->expectedDeliveryDate = ($expectedDeliveryDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($expectedDeliveryDate)): null;
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
     * @param mixed $supplierCountry
     */
    public function setSupplierCountry($supplierCountry): void
    {
        $this->supplierCountry = $supplierCountry;
    }

    /**
     * @param mixed $lcc
     */
    public function setLcc($lcc): void
    {
        $this->lcc = $lcc;
    }

    /**
     * @param mixed $sme
     */
    public function setSme($sme): void
    {
        $this->sme = $sme;
    }

    /**
     * @param mixed $icvCategory
     */
    public function setIcvCategory($icvCategory): void
    {
        $this->icvCategory = $icvCategory;
    }

    /**
     * @param mixed $icvSubCategory
     */
    public function setIcvSubCategory($icvSubCategory): void
    {
        $this->icvSubCategory = $icvSubCategory;
    }

    /**
     * @param mixed $budgetYear
     */
    public function setBudgetYear($budgetYear): void
    {
        $this->budgetYear = $budgetYear;
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
     * @param mixed $logisticAmount
     */
    public function setLogisticAmount($logisticAmount): void
    {
        $this->logisticAmount = $logisticAmount;
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

    /**
     * @param mixed $advanceReleased
     */
    public function setAdvanceReleased($advanceReleased): void
    {
        $this->advanceReleased = $advanceReleased;
    }

    /**
     * @param mixed $logisticAdvanceReleased
     */
    public function setLogisticAdvanceReleased($logisticAdvanceReleased): void
    {
        $this->logisticAdvanceReleased = $logisticAdvanceReleased;
    }

    /**
     * @param mixed $paymentReleasedFromInvoice
     */
    public function setPaymentReleasedFromInvoice($paymentReleasedFromInvoice): void
    {
        $this->paymentReleasedFromInvoice = $paymentReleasedFromInvoice;
    }

    /**
     * @param mixed $balanceToBePaid
     */
    public function setBalanceToBePaid($balanceToBePaid): void
    {
        $this->balanceToBePaid = $balanceToBePaid;
    }

    /**
     * @param mixed $isManuallyClosed
     */
    public function setIsManuallyClosed($isManuallyClosed): void
    {
        $this->isManuallyClosed = $isManuallyClosed;
    }


}
