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
          'Company ID',
          'Company Name',
          'Customer Code',
          'Customer Name',
          'Currency',
          'Amount'
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
