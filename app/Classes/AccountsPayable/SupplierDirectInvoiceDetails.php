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


class SupplierDirectInvoiceDetails
{
    public $master;
    public $details;
    public $vatReturnFillingMaster;
    public $glAccount;
    public $amount;

    public function __construct(BookInvSuppMaster $supplierInvoice, $glAccount = null)
    {
        $this->master = $supplierInvoice;
        $this->details = new DirectInvoiceDetails();
        $this->glAccount = $glAccount;

        $this->setMasterValuesToDetails();
        $this->setDefaultValues();

    }

    private function setMasterValuesToDetails()
    {
        $this->details->directInvoiceAutoID = $this->master->bookingSuppMasInvAutoID;
        $this->details->companySystemID = $this->master->companySystemID;
        $this->details->companyID = $this->master->companyID;
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
        $this->details->comments = "Narration";
        $this->details->VATPercentage = 0;
        $this->details->VATAmount =0;
        $this->details->VATAmountLocal =0;
        $this->details->VATAmountRpt =0;
    }

    public function setCurrenciesAndExchagneRate()
    {
        $this->details->localCurrency = $this->master->localCurrencyID;
        $this->details->comRptCurrency = $this->master->companyReportingCurrencyID;
        $this->details->localCurrencyER = $this->master->localCurrencyER;
        $this->details->comRptCurrencyER = $this->master->companyReportingER;
        $this->details->DIAmountCurrency = $this->master->supplierTransactionCurrencyID;
        $this->details->DIAmountCurrencyER = 1;
    }

    public function setVATReturnFillingMaster(VatReturnFillingMaster $vatReturnFillingMaster)
    {
        $this->vatReturnFillingMaster = $vatReturnFillingMaster;
    }

    public function getAmount()
    {
        return $this->amount;
    }


    public function setAmount(float $amount)
    {
        $this->details->localAmount = $amount;
        $this->details->netAmount = $amount;
        $this->details->DIAmount = $amount;
    }

    public function setGlAccountDetails($glAccountType)
    {
        $taxMaster = Tax::where('companySystemID',$this->master->companySystemID)
                    ->where('taxCategory',2)
                    ->where('isDefault',true)
                    ->where('isActive',true)
                    ->first();
        $vatReturnFillingCategoryDetails = $this->vatReturnFillingMaster->filled_master_categories->where('categoryID',24)->first()->filled_details;
        if(!isset($vatReturnFillingCategoryDetails))
            throw new \Exception("VAT return filling details amount not found!");

        if($glAccountType == "InputVATGLAccount")
        {
            $chartOfAccountID = $taxMaster->inputVatGLAccountAutoID;
            $detailsVAT = $vatReturnFillingCategoryDetails
                            ->where('vatReturnFillingSubCatgeoryID',25);

        }else {
            $chartOfAccountID = $taxMaster->outputVatGLAccountAutoID;
            $detailsVAT = $vatReturnFillingCategoryDetails
                            ->where('vatReturnFillingSubCatgeoryID',26);
        }

        $chartOfAccount = ChartOfAccount::find($chartOfAccountID);
        if(!isset($chartOfAccount))
           throw new \Exception("Chart of account configuration not found");

        $this->amount = ($glAccountType == "InputVATGLAccount") ? -($detailsVAT->pluck('taxAmount')->first()) : $detailsVAT->pluck('taxAmount')->first();
        $this->details->chartOfAccountSystemID = $chartOfAccount->chartOfAccountSystemID;
        $this->details->glCode = $chartOfAccount->AccountCode;
        $this->details->glCodeDes = $chartOfAccount->AccountDescription;
    }

}
