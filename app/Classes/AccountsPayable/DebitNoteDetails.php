<?php

namespace App\Classes\AccountsPayable;

use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\Tax;
use App\Models\VatReturnFillingMaster;

class DebitNoteDetails extends DetailsMaster
{

    public function __construct(\App\Models\DebitNote $debitNote, $glAccount = null)
    {
        $this->master = $debitNote;
        $this->details = new \App\Models\DebitNoteDetails();
        $this->details->debitNoteAutoID = $this->master->debitNoteAutoID;
    }

    public function setAdditionalDetatils()
    {
        $this->details->detail_project_id = 0;
        $this->details->supplierID = $this->master->supplierID;
        $this->details->debitAmountCurrency = $this->details->localCurrency;
        $this->details->debitAmountCurrencyER = $this->details->localCurrencyER;

    }

    public function setAmount(float $amount)
    {
        $companyCurrencyConversion = \Helper::currencyConversion($this->master->companySystemID, $this->details->localCurrencyER, $this->details->localCurrencyER, $amount);

        if($this->glAccountType == "InputVATGLAccount")
        {
            $this->details->localAmount = ABS($amount) * -1;
            $this->details->netAmountLocal = ABS($amount) * -1;
            $this->details->debitAmount = ABS($amount) * -1;
            $this->details->netAmount = ABS($amount) * -1;
            $this->details->comRptAmount = ABS($companyCurrencyConversion['reportingAmount']) * -1;
            $this->details->netAmountRpt = ABS($companyCurrencyConversion['reportingAmount']) * -1;
        }else {
            $this->details->localAmount = abs($amount);
            $this->details->netAmountLocal = abs($amount);
            $this->details->debitAmount = abs($amount);
            $this->details->netAmount = abs($amount);
            $this->details->comRptAmount = abs($companyCurrencyConversion['reportingAmount']);
            $this->details->netAmountRpt = abs($companyCurrencyConversion['reportingAmount']);
        }

    }





}
