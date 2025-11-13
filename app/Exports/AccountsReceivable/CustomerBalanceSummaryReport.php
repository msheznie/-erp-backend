<?php

namespace App\Exports\AccountsReceivable;

class CustomerBalanceSummaryReport
{

    public $companyId;
    public $companyName;
    public $customerCode;
    public $customerName;
    public $currency;
    public $amount;

    public function getHeader()
    {
        return [
          trans('custom.company_id'),
          trans('custom.company_name'),
          trans('custom.customer_code'),
          trans('custom.customer_name'),
          trans('custom.currency'),
          trans('custom.amount'),
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
     * @param mixed $companyName
     */
    public function setCompanyName($companyName): void
    {
        $this->companyName = $companyName;
    }

    /**
     * @param mixed $customerCode
     */
    public function setCustomerCode($customerCode): void
    {
        $this->customerCode = $customerCode;
    }

    /**
     * @param mixed $customerName
     */
    public function setCustomerName($customerName): void
    {
        $this->customerName = $customerName;
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
