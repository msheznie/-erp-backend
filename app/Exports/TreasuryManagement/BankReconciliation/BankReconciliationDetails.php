<?php

namespace App\Exports\TreasuryManagement\BankReconciliation;

class BankReconciliationDetails
{
    public $companyID;
    public $documentCode;
    public $documentDate;
    public $narration;
    public $payeeName;
    public $bankCurrency;
    public $bankAmount;
    public $reconciliationDate;
    public $bankClearedBy;
    public $bankClearedDate;

    public function getCloumnFormat()
    {
        return [
            'C' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'G' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'J' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    public function getHeader(): Array
    {
        return [
            trans('custom.company_id'),
            trans('custom.document_code'),
            trans('custom.document_date'),
            trans('custom.narration'),
            trans('custom.payee_name'),
            trans('custom.bank_currency'),
            trans('custom.bank_amount'),
            trans('custom.reconciliation_date'),
            trans('custom.bank_cleared_by'),
            trans('custom.bank_cleared_date')
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
     * @param mixed $documentCode
     */
    public function setDocumentCode($documentCode): void
    {
        $this->documentCode = $documentCode;
    }

    /**
     * @param mixed $documentDate
     */
    public function setDocumentDate($documentDate): void
    {
        $this->documentDate = $documentDate;
    }

    /**
     * @param mixed $narration
     */
    public function setNarration($narration): void
    {
        $this->narration = $narration;
    }

    /**
     * @param mixed $payeeName
     */
    public function setPayeeName($payeeName): void
    {
        $this->payeeName = $payeeName;
    }

    /**
     * @param mixed $bankCurrency
     */
    public function setBankCurrency($bankCurrency): void
    {
        $this->bankCurrency = $bankCurrency;
    }

    /**
     * @param mixed $bankAmount
     */
    public function setBankAmount($bankAmount): void
    {
        $this->bankAmount = $bankAmount;
    }

    /**
     * @param mixed $reconciliationDate
     */
    public function setReconciliationDate($reconciliationDate): void
    {
        $this->reconciliationDate = $reconciliationDate;
    }

    /**
     * @param mixed $bankClearedBy
     */
    public function setBankClearedBy($bankClearedBy): void
    {
        $this->bankClearedBy = $bankClearedBy;
    }

    /**
     * @param mixed $bankClearedDate
     */
    public function setBankClearedDate($bankClearedDate): void
    {
        $this->bankClearedDate = $bankClearedDate;
    }
}
