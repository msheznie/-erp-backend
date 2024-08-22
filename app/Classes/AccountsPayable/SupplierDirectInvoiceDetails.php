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

    public function setDefaultValues()
    {

        $segments = Company::find($this->master->companySystemID)
            ->segments()
            ->where('isPublic',true)
            ->select('serviceLineMasterCode', 'serviceLineSystemID')
            ->first();

        $this->details->serviceLineSystemID = ($segments) ? $segments->serviceLineSystemID : null;
        $this->details->serviceLineCode = ($segments) ? $segments->serviceLineMasterCode : null;
        $this->details->detail_project_id = null;
        $this->details->comments = $this->master->comments;
        $this->details->VATPercentage = 0;
        $this->details->VATAmount =0;
        $this->details->VATAmountLocal =0;
        $this->details->VATAmountRpt =0;
    }




    public function setGlAccountDetails($glAccountType)
    {
        $this->glAccountType = $glAccountType;
        $taxMaster = Tax::where('companySystemID',$this->master->companySystemID)
            ->where('taxCategory',2)
            ->where('isDefault',true)
            ->where('isActive',true)
            ->first();

        if(!isset($taxMaster))
            throw new \Exception("Tax Master details not found");

        $vatReturnFillingCategoryDetails = $this->vatReturnFillingMaster->filled_master_categories->where('categoryID',24)->first()->filled_details;
        if(!isset($vatReturnFillingCategoryDetails))
            throw new \Exception("VAT return filling details amount not found!");

        if($glAccountType != "InputVATGLAccount")
        {
            $chartOfAccountID = $taxMaster->outputVatGLAccountAutoID;
            $detailsVAT = $vatReturnFillingCategoryDetails
                ->where('vatReturnFillingSubCatgeoryID',25);

        }else {
            $chartOfAccountID = $taxMaster->inputVatGLAccountAutoID;

            $detailsVAT = $vatReturnFillingCategoryDetails
                ->where('vatReturnFillingSubCatgeoryID',26);
        }

        $chartOfAccount = ChartOfAccount::find($chartOfAccountID);
        if(!isset($chartOfAccount))
            throw new \Exception("Chart of account configuration not found");

        $this->amount =$detailsVAT->pluck('taxAmount')->first();
        $this->details->chartOfAccountSystemID = $chartOfAccount->chartOfAccountSystemID;
        $this->details->glCode = $chartOfAccount->AccountCode;
        $this->details->glCodeDes = $chartOfAccount->AccountDescription;
    }
    public function setAmount(float $amount)
    {
        $companyCurrencyConversion = \Helper::currencyConversion($this->master->companySystemID, $this->details->localCurrencyER, $this->details->localCurrencyER, $amount);

        if($this->glAccountType == "OutputVATGLAccount")
        {
            $this->details->localAmount = ABS($amount);
            $this->details->DIAmount = ABS($amount);
            $this->details->comRptAmount = ABS($companyCurrencyConversion['reportingAmount']);
            $this->details->VATPercentage = 0;
            $this->details->netAmount = ABS($amount);
            $this->details->netAmountLocal = ABS($amount);
            $this->details->netAmountRpt = ABS($companyCurrencyConversion['reportingAmount']);
        }else {
            $this->details->localAmount = abs($amount)  * -1;
            $this->details->DIAmount = abs($amount) * -1;
            $this->details->comRptAmount = abs($companyCurrencyConversion['reportingAmount']) * -1;
            $this->details->VATPercentage = 0;
            $this->details->netAmount = ABS($amount) * -1;
            $this->details->netAmountLocal = ABS($amount) * -1;
            $this->details->netAmountRpt = ABS($companyCurrencyConversion['reportingAmount']) * -1;

        }


    }

    public function setAdditionalDetatils()
    {
        $this->details->DIAmount = $this->details->localAmount;
        $this->details->DIAmountCurrencyER = 1;
        $this->details->DIAmountCurrency = $this->details->localCurrency;

    }

}
