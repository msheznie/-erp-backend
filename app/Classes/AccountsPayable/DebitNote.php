<?php

namespace App\Classes\AccountsPayable;

use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\SupplierAssigned;
use App\Models\SupplierMaster;
use App\Services\UserTypeService;
use Carbon\Carbon;

class DebitNote
{

    public $master;
    public $companySystemID;
    public $code;
    public function __construct($companySystemID,$documentDate,$code)
    {
        $this->master = new \App\Models\DebitNote();
        $this->companySystemID = $companySystemID;
        $this->code = $code;
        $this->setCompanyDetails();
        $this->setDocumentDetails();
        $this->setDateDetails($documentDate);
        $this->setFiananicalYearDetails();
        $this->setDebitNoteCode();
        $this->setComment();
    }

    private function setCompanyDetails()
    {
        $company = Company::find($this->companySystemID);
        if(!isset($company))
            throw  new \Exception(trans('custom.company_details_not_found'));

        $this->master->companySystemID = $company->companySystemID;
        $this->master->companyID = $company->CompanyID;
        $this->master->isVATApplicable = false;
        $this->master->localCurrencyID = $company->localCurrencyID;
        $this->master->companyReportingCurrencyID = $company->reportingCurrency;
    }

    public function setDocumentDetails()
    {
        $this->master->documentSystemID = 15;
        $this->master->documentID = "DN";
    }

    public function setDateDetails($date)
    {
        if (!$this->checkValidDate($date))
            throw new \InvalidArgumentException(trans('custom.invalid_date_format_document_date'));

        $this->master->debitNoteDate = $date;
        $this->master->postedDate = $date;
    }

    public function setFiananicalYearDetails()
    {
        $companyFinanceYear = CompanyFinanceYear::where('companySystemID', $this->master->companySystemID)->where('bigginingDate', "<=", $this->master->debitNoteDate)->where('endingDate', ">=", $this->master->debitNoteDate)->first();

        if($this->master->debitNoteDate->year == Carbon::now()->year)
        {

            if ($companyFinanceYear)
            {
                if($companyFinanceYear->isActive != -1)
                {
                    throw new \Exception(trans('custom.finance_year_not_active'));
                }

                if($companyFinanceYear->isCurrent != -1)
                {
                    throw new \Exception(trans('custom.finance_year_not_current'));
                }
            }else {
                throw new \Exception(trans('custom.company_financial_year_not_found'));
            }

        }else {

            if ($companyFinanceYear)
            {
                if($companyFinanceYear->isActive != -1)
                {
                    throw new \Exception(trans('custom.finance_year_not_active'));
                }
            }else {
                throw new \Exception(trans('custom.company_financial_year_not_found_or_not_active'));
            }
        }


        if(!($companyFinanceYear->bigginingDate <= $this->master->debitNoteDate) && ($this->master->debitNoteDate <= $companyFinanceYear->endingDate))
            throw new \Exception(trans('custom.document_date_not_within_financial_year'));

        $companyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $this->companySystemID)->where('departmentSystemID', 1)->where('dateFrom', "<=", $this->master->debitNoteDate)->where('dateTo', ">=", $this->master->debitNoteDate)->first();

        if(!$companyFinancePeriod)
            throw  new \Exception(trans('custom.financial_period_not_found'));

        if($this->master->debitNoteDate->month == Carbon::now()->month)
        {
            if($companyFinancePeriod->isActive != -1)
            {
                throw new \Exception(trans('custom.finance_period_not_active'));
            }

            if($companyFinancePeriod->isCurrent != -1)
            {
                throw new \Exception(trans('custom.finance_period_not_current'));
            }
        }else {
            if($companyFinancePeriod->isActive != -1)
            {
                throw new \Exception(trans('custom.finance_period_not_active'));
            }

        }


        $this->master->companyFinanceYearID = $companyFinanceYear->companyFinanceYearID;
        $this->master->FYBiggin = $companyFinanceYear->bigginingDate;
        $this->master->FYEnd = $companyFinanceYear->endingDate;
        $this->master->companyFinancePeriodID = $companyFinancePeriod->companyFinancePeriodID;
        $this->master->FYPeriodDateFrom = $companyFinancePeriod->startDate;
        $this->master->FYPeriodDateTo = $companyFinancePeriod->endDate;
    }

    public function setSupplierDetails($supplierId)
    {
        $supplierAssignedDetail = SupplierAssigned::select('liabilityAccountSysemID',
            'liabilityAccount', 'UnbilledGRVAccountSystemID', 'UnbilledGRVAccount','VATPercentage','supplierAssignedID')
            ->where('supplierCodeSytem', $supplierId)
            ->where('companySystemID', $this->master->companySystemID)
            ->first();



        if (!isset($supplierAssignedDetail))
            throw new \Exception(trans('custom.supplier_gl_accounts_details_not_found'));
        
        $supplier = SupplierMaster::find($supplierId);
        if(empty($supplier->liabilityAccountSysemID))
            throw new \Exception(trans('custom.liability_account_not_selected_tax_authority'));

        if(empty($supplier->supplierCurrency->first()))
            throw new \Exception(trans('custom.supplier_transaction_currency_details_not_found'));

        $supplierTranscationCurrency = $supplier->supplierCurrency->first()->currencyMaster->currencyID;
        $companyCurrencyConversion = \Helper::currencyConversion($this->master->companySystemID, $supplierTranscationCurrency, $supplierTranscationCurrency, 0);

        $this->master->supplierID = $supplierId;
        $this->master->supplierVATEligible = false;
        $this->master->supplierGLCodeSystemID = $supplierAssignedDetail->liabilityAccountSysemID;
        $this->master->supplierGLCode = $supplierAssignedDetail->liabilityAccount;
        $this->master->liabilityAccountSysemID = $supplierAssignedDetail->liabilityAccountSysemID;
        $this->master->liabilityAccount = $supplierAssignedDetail->liabilityAccount;
        $this->master->UnbilledGRVAccountSystemID = $supplierAssignedDetail->UnbilledGRVAccountSystemID;
        $this->master->UnbilledGRVAccount = $supplierAssignedDetail->UnbilledGRVAccount;
        $this->master->VATPercentage = $supplierAssignedDetail->VATPercentage;
        $this->master->companyReportingER = $companyCurrencyConversion['trasToRptER'];
        $this->master->localCurrencyER = $companyCurrencyConversion['trasToLocER'];
        $this->master->supplierTransactionCurrencyER = 1;
        $this->master->supplierTransactionCurrencyID = $supplierTranscationCurrency;
    }

    public function setSystemCreatedUserDetails()
    {
        $systemUser = UserTypeService::getSystemEmployee();
        $this->master->createdUserSystemID = $systemUser->empID;
        $this->master->modifiedUser = $systemUser->empID;
        $this->master->createdUserSystemID = $systemUser->employeeSystemID;
        $this->master->modifiedUserSystemID = $systemUser->employeeSystemID;
        $this->master->createdPcID = getenv('COMPUTERNAME');
        $this->master->modifiedPc = getenv('COMPUTERNAME');
    }

    public function setCreatedUserDetails()
    {
        $employee = \Helper::getEmployeeInfo();
        $this->master->createdPcID = gethostname();
        $this->master->createdUserID = $employee->empID;
        $this->master->createdUserSystemID = $employee->employeeSystemID;
    }

    public function setDebitNoteCode()
    {
        $lastSerial = \App\Models\DebitNote::where('companySystemID', $this->master->companySystemID)
            ->where('companyFinanceYearID', $this->master->companyFinanceYearID)
            ->orderBy('serialNo', 'desc')
            ->first();
        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        $companyfinanceyear = CompanyFinanceYear::find($this->master->companyFinanceYearID);

        if ($companyfinanceyear) {
            $startYear = $companyfinanceyear['bigginingDate'];
            $finYearExp = explode('-', $startYear);
            $finYear = $finYearExp[0];
        } else {
            $finYear = date("Y");
        }

        $code = ($this->master->companyID . '\\' . $finYear . '\\' . 'DN' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
        $this->master->debitNoteCode = $code;
        $this->master->serialNo = $lastSerialNumber;

    }

    public function setComment($comment = null)
    {
        if(is_null($comment))
        {
            $this->master->comments = trans('custom.dn_created_by_vat_return_filling')." ".$this->code;
        }else {
            $this->master->comments = $comment;
        }
    }

    public function setEmpControlAccount($account)
    {
        $this->master->empControlAccount = $account;
    }


    public function checkValidDate($date)
    {
        if(!Carbon::parse($date))
            return false;

        return true;
    }



    public function getNetAmount($VATReturnFillingMaster)
    {
        $this->netAmount = $VATReturnFillingMaster->filled_master_categories
            ->where('categoryID',24)->first()->filled_details
            ->where('vatReturnFillingSubCatgeoryID',27)
            ->pluck('taxAmount')->first();

        return $this->netAmount;

    }


}
