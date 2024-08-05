<?php

namespace App\Classes\ExchangeSetup\AccountsPayable;

class PaymentVoucherExchangeSetupConfig
{
    public $scenario;
    public $message;
    public $enableRptCurrency = false;
    public $enableLocalCurrency = false;
    public $enableBankCurrency = false;

    /**
     * @param mixed $scenario
     */
    public function setScenario($scenario = null): void
    {
        $this->scenario = $scenario;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message = null): void
    {
        $this->message = $message;
    }

    /**
     * @param mixed $enableRptCurrency
     */
    public function setEnableRptCurrency($enableRptCurrency = false): void
    {
        $this->enableRptCurrency = $enableRptCurrency;
    }

    /**
     * @param mixed $enableLocalCurrency
     */
    public function setEnableLocalCurrency($enableLocalCurrency = false): void
    {
        $this->enableLocalCurrency = $enableLocalCurrency;
    }

    /**
     * @param mixed $enableBankCurrency
     */
    public function setEnableBankCurrency($enableBankCurrency = false): void
    {
        $this->enableBankCurrency = $enableBankCurrency;
    }

    public function getDataArray() {
        return [
            "scenario" => $this->scenario,
            "message" => $this->message,
            "enableRptCurrency" => $this->enableRptCurrency,
            "enableLocalCurrency" => $this->enableLocalCurrency,
            "enableBankCurrency" => $this->enableBankCurrency
        ];
    }
}
