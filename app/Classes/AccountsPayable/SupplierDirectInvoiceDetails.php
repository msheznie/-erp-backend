<?php

namespace App\Classes\AccountsPayable;

use App\Models\BookInvSuppMaster;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\DirectInvoiceDetails;
use App\Models\SegmentMaster;
use App\Models\Tax;
use App\Models\VatReturnFillingCategory;
use App\Models\VatReturnFillingMaster;


class SupplierDirectInvoiceDetails extends DetailsMaster
{
    public $master;
    public $details;
    public $glAccount;
    public $amount;

    public function __construct(BookInvSuppMaster $supplierInvoice, $glAccount = null)
    {
        $this->master = $supplierInvoice;
        $this->details = new DirectInvoiceDetails();
        $this->glAccount = $glAccount;
        $this->details->directInvoiceAutoID = $this->master->bookingSuppMasInvAutoID;

    }


    public function setAmount(float $amount)
    {
        $companyCurrencyConversion = \Helper::currencyConversion($this->master->companySystemID, $this->details->localCurrencyER, $this->details->localCurrencyER, $amount);

        if($this->glAccountType == "InputVATGLAccount")
        {
            $this->details->localAmount = ABS($amount) * -1;
            $this->details->DIAmount = ABS($amount) * -1;
            $this->details->comRptAmount = ABS($companyCurrencyConversion['reportingAmount']) * -1;
        }else {
            $this->details->localAmount = abs($amount);
            $this->details->DIAmount = abs($amount);
            $this->details->comRptAmount = abs($companyCurrencyConversion['reportingAmount']);
        }


    }

    public function setAdditionalDetatils()
    {
        $this->details->DIAmount = $this->details->localAmount;
        $this->details->DIAmountCurrencyER = 1;
        $this->details->DIAmountCurrency = $this->details->localCurrency;

    }

}
