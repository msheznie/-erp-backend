<?php

namespace App\Exports\AccountsPayable\UnbilledGRV;

use App\helper\Helper;

class UnbilledGrvLogisticDetails
{
    public $companyId;
    public $poNumber;
    public $grv;
    public $grvDate;
    public $supplierCode;
    public $supplierName;
    public $transcationCurrency;
    public $logisticAmountTrans;
    public $rptCurrency;
    public $logisticAmountRpt;
    public $paidAmountTrans;
    public $paidAmountRpt;
    public $balanceTrans;
    public $balanceRpt;


    public function getCloumnFormat()
    {
        return [
            'D' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'H' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'M' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1

        ];
    }

    public function getHeader() :Array {
            return [
                trans('custom.company_id'),
                trans('custom.po_number'),
                trans('custom.grv'),
                trans('custom.grv_date'),
                trans('custom.supplier_code'),
                trans('custom.supplier_name'),
                trans('custom.trans_cur'),
                trans('custom.logistic_amount_transaction'),
                trans('custom.rpt_cur'),
                trans('custom.logistic_amount_rpt'),
                trans('custom.paid_amount_trans'),
                trans('custom.paid_amount_rpt'),
                trans('custom.balance_trans'),
                trans('custom.balance_rpt')

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
     * @param mixed $poNumber
     */
    public function setPoNumber($poNumber): void
    {
        $this->poNumber = $poNumber;
    }

    /**
     * @param mixed $grv
     */
    public function setGrv($grv): void
    {
        $this->grv = $grv;
    }

    /**
     * @param mixed $grvDate
     */
    public function setGrvDate($grvDate): void
    {
        $this->grvDate = ($grvDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($grvDate)) : null;
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
     * @param mixed $transcationCurrency
     */
    public function setTranscationCurrency($transcationCurrency): void
    {
        $this->transcationCurrency = $transcationCurrency;
    }

    /**
     * @param mixed $logisticAmountTrans
     */
    public function setLogisticAmountTrans($logisticAmountTrans): void
    {
        $this->logisticAmountTrans = $logisticAmountTrans;
    }

    /**
     * @param mixed $rptCurrency
     */
    public function setRptCurrency($rptCurrency): void
    {
        $this->rptCurrency = $rptCurrency;
    }

    /**
     * @param mixed $logisticAmountRpt
     */
    public function setLogisticAmountRpt($logisticAmountRpt): void
    {
        $this->logisticAmountRpt = $logisticAmountRpt;
    }

    /**
     * @param mixed $paidAmountTrans
     */
    public function setPaidAmountTrans($paidAmountTrans): void
    {
        $this->paidAmountTrans = $paidAmountTrans;
    }

    /**
     * @param mixed $paidAmountRpt
     */
    public function setPaidAmountRpt($paidAmountRpt): void
    {
        $this->paidAmountRpt = $paidAmountRpt;
    }

    /**
     * @param mixed $balanceTrans
     */
    public function setBalanceTrans($balanceTrans): void
    {
        $this->balanceTrans = $balanceTrans;
    }

    /**
     * @param mixed $balanceRpt
     */
    public function setBalanceRpt($balanceRpt): void
    {
        $this->balanceRpt = $balanceRpt;
    }


}
