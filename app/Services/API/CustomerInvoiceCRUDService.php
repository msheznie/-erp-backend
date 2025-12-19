<?php

namespace App\Services\API;

use App\Models\BankAccount;
use App\Models\BankAssign;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CustomerAssigned;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerMaster;
use App\Models\SegmentMaster;
use App\Services\UserTypeService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerInvoiceCRUDService
{
    public $headerArray = Array();

    public function store(Request $request)
    {
        $input = $request->all();

        if (isset($input['isPerforma']) && $input['isPerforma'] == 2) {
            $wareHouse = isset($input['wareHouseSystemCode']) ? $input['wareHouseSystemCode'] : 0;
            if (!$wareHouse) {
                return $this->sendError('Please select a warehouse', 500);
            }
        }

        $input = $this->convertArrayToSelectedValue($input, array('companyFinancePeriodID', 'companyFinanceYearID', 'custTransactionCurrencyID'));
        if (!isset($input['custTransactionCurrencyID']) || (isset($input['custTransactionCurrencyID']) && ($input['custTransactionCurrencyID'] == 0 || $input['custTransactionCurrencyID'] == null))) {
            return $this->sendError('Please select a currency', 500);
        }
        $companyFinanceYearID = $input['companyFinanceYearID'];

        if (!isset($input['companyFinanceYearID']) || is_null($input['companyFinanceYearID'])) {
            return $this->sendError('Financial year is not selected', 500);
        }

        if (!isset($input['companyFinancePeriodID']) || is_null($input['companyFinancePeriodID'])) {
            return $this->sendError('Financial period is not selected', 500);
        }

        $company = Company::where('companySystemID', $input['companyID'])->first()->toArray();

        $CompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $companyFinanceYearID)->first();
        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        $FYPeriodDateFrom = $companyfinanceperiod->dateFrom;
        $FYPeriodDateTo = $companyfinanceperiod->dateTo;
        $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
        $myCurr = $input['custTransactionCurrencyID'];

        $companyCurrency = \Helper::companyCurrency($company['companySystemID']);
        $companyCurrencyConversion = \Helper::currencyConversion($company['companySystemID'], $myCurr, $myCurr, 0);
        /*exchange added*/
        $input['custTransactionCurrencyER'] = 1;
        $input['companyReportingCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
        $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
        $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
        $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];

        if(!isset($input['isAutoCreateDocument'])){
            $bank = BankAssign::select('bankmasterAutoID')
                ->where('companySystemID', $input['companyID'])
                ->where('isDefault', -1)
                ->first();
            if ($bank) {
                $input['bankID'] = $bank->bankmasterAutoID;
                $bankAccount = BankAccount::where('companySystemID', $input['companyID'])
                    ->where('bankmasterAutoID', $bank->bankmasterAutoID)
                    ->where('isDefault', 1)
                    ->where('accountCurrencyID', $myCurr)
                    ->first();
                if ($bankAccount) {
                    $input['bankAccountID'] = $bankAccount->bankAccountAutoID;
                }

            }
        }

        if (isset($input['isPerforma']) && ($input['isPerforma'] == 2 || $input['isPerforma'] == 3 || $input['isPerforma'] == 4 || $input['isPerforma'] == 5)) {
            $serviceLine = isset($input['serviceLineSystemID']) ? $input['serviceLineSystemID'] : 0;
            if (!$serviceLine) {
                return $this->sendError('Please select a Segment', 500);
            }
            $segment = SegmentMaster::find($input['serviceLineSystemID']);
            $input['serviceLineCode'] = isset($segment->ServiceLineCode) ? $segment->ServiceLineCode : null;
        }

        $lastSerial = CustomerInvoiceDirect::where('companySystemID', $input['companyID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('serialNo', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        $y = date('Y', strtotime($CompanyFinanceYear->bigginingDate));
        $bookingInvCode = ($company['CompanyID'] . '\\' . $y . '\\INV' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));

        $customerGLCodeUpdate = CustomerAssigned::where('customerCodeSystem', $input['customerID'])
            ->where('companySystemID', $input['companyID'])
            ->first();
        if ($customerGLCodeUpdate) {
            $input['customerVATEligible'] = $customerGLCodeUpdate->vatEligible;
        }

        $company = Company::where('companySystemID', $input['companyID'])->first();

        if ($company) {
            $input['vatRegisteredYN'] = $company->vatRegisteredYN;
        }

        $input['documentID'] = "INV";
        $input['documentSystemiD'] = 20;
        $input['bookingInvCode'] = $bookingInvCode;
        $input['serialNo'] = $lastSerialNumber;
        $input['FYBiggin'] = $CompanyFinanceYear->bigginingDate;
        $input['FYEnd'] = $CompanyFinanceYear->endingDate;
        $input['FYPeriodDateFrom'] = $FYPeriodDateFrom;
        $input['FYPeriodDateTo'] = $FYPeriodDateTo;
        try{
            $input['invoiceDueDate'] = Carbon::parse($input['invoiceDueDate'])->format('Y-m-d') . ' 00:00:00';
        }
        catch (\Exception $e){
            return $this->sendError('Invalid Due Date format');
        }
        $input['bookingDate'] = Carbon::parse($input['bookingDate'])->format('Y-m-d') . ' 00:00:00';
        $input['date_of_supply'] = Carbon::parse($input['date_of_supply'])->format('Y-m-d') . ' 00:00:00';
        $input['customerInvoiceDate'] = $input['bookingDate'];
        $input['companySystemID'] = $input['companyID'];
        $input['companyID'] = $company['CompanyID'];
        $input['customerGLCode'] = $customer->custGLaccount;
        $input['customerGLSystemID'] = $customer->custGLAccountSystemID;
        $input['documentType'] = 11;

        if(!isset($input['isAutoCreateDocument'])){
            $input['createdUserID'] = \Helper::getEmployeeID();
            $input['modifiedUser'] = \Helper::getEmployeeID();
            $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
        }
        else{
            $systemUser = UserTypeService::getSystemEmployee();
            $input['createdUserID'] = $systemUser->empID;
            $input['modifiedUser'] = $systemUser->empID;
            $input['createdUserSystemID'] = $systemUser->employeeSystemID;
            $input['modifiedUserSystemID'] = $systemUser->employeeSystemID;
        }

        $input['createdPcID'] = getenv('COMPUTERNAME');
        $input['modifiedPc'] = getenv('COMPUTERNAME');


        $curentDate = Carbon::parse(now())->format('Y-m-d') . ' 00:00:00';
        if ($input['bookingDate'] > $curentDate) {
            return $this->sendResponse('e', 'Document date cannot be greater than current date');
        }
        if (($input['bookingDate'] >= $FYPeriodDateFrom) && ($input['bookingDate'] <= $FYPeriodDateTo)) {
            $customerInvoiceDirects = $this->customerInvoiceDirectRepository->create($input);
            return $this->sendResponse($customerInvoiceDirects->toArray(), 'Customer Invoice  saved successfully');
        } else {
            return $this->sendResponse('e', 'Document date should be between financial period start date and end date');
        }
    }
}
