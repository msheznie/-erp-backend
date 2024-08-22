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

    public function setAdditionalDetatils()
    {
        $this->details->detail_project_id = 0;
        $this->details->supplierID = $this->master->supplierID;
        $this->details->debitAmountCurrency = $this->details->localCurrency;
        $this->details->debitAmountCurrencyER = $this->details->localCurrencyER;

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

        if($this->glAccountType == "InputVATGLAccount")
        {
            $this->details->localAmount = ABS($amount);
            $this->details->netAmountLocal = ABS($amount);
            $this->details->debitAmount = ABS($amount);
            $this->details->netAmount = ABS($amount);
            $this->details->comRptAmount = ABS($companyCurrencyConversion['reportingAmount']);
            $this->details->netAmountRpt = ABS($companyCurrencyConversion['reportingAmount']);
        }else {
            $this->details->localAmount = abs($amount) * -1;
            $this->details->netAmountLocal = abs($amount) * -1;
            $this->details->debitAmount = abs($amount) * -1;
            $this->details->netAmount = abs($amount) * -1;
            $this->details->comRptAmount = abs($companyCurrencyConversion['reportingAmount']) * -1;
            $this->details->netAmountRpt = abs($companyCurrencyConversion['reportingAmount']) * -1;
        }

    }





}
