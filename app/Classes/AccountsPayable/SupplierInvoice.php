<?php

namespace App\Classes\AccountsPayable;

use App\enums\accountsPayable\SupplierInvoiceType;
use App\Models\BookInvSuppMaster;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\SupplierAssigned;
use App\Models\SupplierMaster;
use App\Services\UserTypeService;
use Carbon\Carbon;
use http\Exception\InvalidArgumentException;
use Illuminate\Support\Facades\Log;

class SupplierInvoice
{

    public $master;
    public $companySystemID;
    public $documentDate;

    public function __construct($companySystemID,$documentDate)
    {
        $this->master = new BookInvSuppMaster();
        $this->companySystemID = $companySystemID;
        $this->setCompanyDetails();
        $this->setDocumentDetails();
        $this->setDateDetails($documentDate);
        $this->setFiananicalYearDetails();
        $this->setBookingInvCode();

    }

    public function setCompanyDetails()
    {
        $company = Company::find($this->companySystemID);
        if(!isset($company))
            throw  new \Exception("Company details not found");

        $this->master->companySystemID = $company->companySystemID;
        $this->master->companyID = $company->CompanyID;
        $this->master->vatRegisteredYN = $company->vatRegisteredYN;
        $this->master->localCurrencyID = $company->localCurrencyID;
        $this->master->companyReportingCurrencyID = $company->reportingCurrency;
    }

    public function setSupplierInvoiceNo($supplierInvNo)
    {
        $this->master->supplierInvoiceNo = $supplierInvNo;
    }

    public function setDocumentDetails()
    {
        $this->master->documentSystemID = 11;
        $this->master->documentID = "SI";
        $this->master->whtApplicable = false;
    }

    public function setDateDetails($date)
    {
        if (!$this->checkValidDate($date))
            throw new \InvalidArgumentException("Invalid date format in document date");

        $this->master->bookingDate = $date;
        $this->master->supplierInvoiceDate = $date;
        $this->master->postedDate = $date;
        $this->master->retentionDueDate = $date;
    }

    public function setFiananicalYearDetails()
    {
        $companyFinanceYear = CompanyFinanceYear::where('companySystemID', $this->master->companySystemID)->where('bigginingDate', "<=", $this->master->supplierInvoiceDate)->where('endingDate', ">=", $this->master->supplierInvoiceDate)->first();

        if($this->master->supplierInvoiceDate->year == Carbon::now()->year)
        {

            if ($companyFinanceYear)
            {
                if($companyFinanceYear->isActive != -1)
                {
                    throw new \Exception("The finance year is not active");
                }

                if($companyFinanceYear->isCurrent != -1)
                {
                    throw new \Exception("The finance year is not current.");
                }
            }else {
                throw new \Exception("Company Finanical year not found");
            }

        }else {

            if ($companyFinanceYear)
            {
                if($companyFinanceYear->isActive != -1)
                {
                    throw new \Exception("The finance year is not active");
                }
            }else {
                throw new \Exception("Company Finanical year not found or not active");
            }
        }

        if(!($companyFinanceYear->bigginingDate <= $this->master->supplierInvoiceDate) && ($this->master->supplierInvoiceDate <= $companyFinanceYear->endingDate))
            throw new \Exception("Document date not within the financial year");

        $companyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $this->companySystemID)->where('departmentSystemID', 1)->where('dateFrom', "<=", $this->master->supplierInvoiceDate)->where('dateTo', ">=", $this->master->supplierInvoiceDate)->first();

        if(!$companyFinancePeriod)
            throw  new \Exception("Financial period not found");

        if($this->master->supplierInvoiceDate->month == Carbon::now()->month)
        {
            if($companyFinancePeriod->isActive != -1)
            {
                throw new \Exception("The finance period is not active");
            }

            if($companyFinancePeriod->isCurrent != -1)
            {
                throw new \Exception("The finance period is not current.");
            }
        }else {
            if($companyFinancePeriod->isActive != -1)
            {
                throw new \Exception("The finance period is not active");
            }

        }

        $this->master->companyFinanceYearID = $companyFinanceYear->companyFinanceYearID;
        $this->master->FYBiggin = $companyFinanceYear->bigginingDate;
        $this->master->FYEnd = $companyFinanceYear->endingDate;
        $this->master->companyFinancePeriodID = $companyFinancePeriod->companyFinancePeriodID;
        $this->master->FYPeriodDateFrom = $companyFinancePeriod->startDate;
        $this->master->FYPeriodDateTo = $companyFinancePeriod->endDate;
    }

    public function getBookingAmount($VATReturnFillingMaster)
    {
        $this->bookingAmount = $VATReturnFillingMaster->filled_master_categories
            ->where('categoryID',24)->first()->filled_details
            ->where('vatReturnFillingSubCatgeoryID',27)
            ->pluck('taxAmount')->first();

        return $this->bookingAmount;

    }

    public function setBookingInvCode()
    {
        $lastSerial = BookInvSuppMaster::where('companySystemID', $this->master->companySystemID)
            ->where('companyFinanceYearID', $this->master->companyFinanceYearID)
            ->orderBy('serialNo', 'desc')
            ->first();

        $companyfinanceyear = CompanyFinanceYear::find($this->master->companyFinanceYearID);

        if ($companyfinanceyear) {
            $startYear = $companyfinanceyear['bigginingDate'];
            $finYearExp = explode('-', $startYear);
            $finYear = $finYearExp[0];
        } else {
            $finYear = date("Y");
        }

        $company = Company::find( $this->master->companySystemID);
        $lastSerialNumber = $lastSerial ? intval($lastSerial->serialNo) + 1 : 1;

        $companyID = $company->CompanyID;
        $bookingInvCode = $companyID . '\\' . $finYear . '\\' . 'BSI' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT);

        $this->master->bookingInvCode = $bookingInvCode;
        $this->master->serialNo =  $lastSerialNumber;
    }

    public function setReferenceNo($referenceNo)
    {

        $this->master->secondaryRefNo = $referenceNo;
    }

    public function setInvoiceType($type)
    {
        $this->master->documentType = $type;
    }

    public function setSupplier($supplierId)
    {
        $this->master->supplierID = $supplierId;
    }

    public function setNarration($narration)
    {
        $this->master->comments = $narration;
    }

    public function setSupplierDetails($supplierId)
    {
        $supplierAssignedDetail = SupplierAssigned::select('liabilityAccountSysemID',
            'liabilityAccount', 'UnbilledGRVAccountSystemID', 'UnbilledGRVAccount','VATPercentage')
            ->where('supplierCodeSytem', $supplierId)
            ->where('companySystemID', $this->master->companySystemID)
            ->first();


        if (!isset($supplierAssignedDetail))
            throw new \Exception("Supplier assigned details not found");


        $supplier = SupplierMaster::find($supplierId);
        $supplierTranscationCurrency = $supplier->supplierCurrency->first()->supplierCurrencyID;
        $companyCurrencyConversion = \Helper::currencyConversion($this->master->companySystemID, $supplierTranscationCurrency, $supplierTranscationCurrency, 0);


        $this->master->supplierVATEligible = $supplierAssignedDetail->vatEligible;
        $this->master->supplierGLCodeSystemID = $supplierAssignedDetail->liabilityAccountSysemID;
        $this->master->supplierGLCode = $supplierAssignedDetail->liabilityAccount;
        $this->master->UnbilledGRVAccountSystemID = $supplierAssignedDetail->UnbilledGRVAccountSystemID;
        $this->master->UnbilledGRVAccount = $supplierAssignedDetail->UnbilledGRVAccount;
        $this->master->VATPercentage = $supplierAssignedDetail->VATPercentage;
        $this->master->companyReportingER = $companyCurrencyConversion['trasToRptER'];
        $this->master->localCurrencyER = $companyCurrencyConversion['trasToLocER'];
        $this->master->supplierTransactionCurrencyER = 1;
        $this->master->supplierTransactionCurrencyID = $supplierTranscationCurrency;
    }
    public function checkValidDate($date)
    {
        if(!Carbon::parse($date))
            return false;

        return true;
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


    public function getSupplierInvoice(): BookInvSuppMaster {
        return $this->master;
    }

    public function store()
    {
        try {
            $supplierInvoice = BookInvSuppMaster::create($this->master->toArray());
            return ['success' => true, 'message' => "Supplier invoice created successfully!", 'data' => $supplierInvoice];
        }catch (\Exception $exception)
        {
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }

}
