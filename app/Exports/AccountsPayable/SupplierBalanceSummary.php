<?php

namespace App\Exports\AccountsPayable;

class SupplierBalanceSummary
{

    public $companyID;
    public $companyName;
    public $account;
    public $supplierCode;
    public $supplierName;
    public $supplierGroupName;
    public $currency;
    public $amount;

    public function getHeader()
    {
        return [
          trans('custom.company_id'),
          trans('custom.company_name'),
          trans('custom.account'),
          trans('custom.supplier_code'),
          trans('custom.supplier_name'),
          trans('custom.supplier_group'),
          trans('custom.currency'),
          trans('custom.amount')
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
    public function setSupplierGroupName($supplierGroupName): void
    {
        $this->supplierGroupName = $supplierGroupName;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }
}
