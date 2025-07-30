<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\BankAssign;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CurrencyMaster;
use App\Models\CustomerMaster;
use App\Models\CustomerReceivePayment;
use App\Models\DirectReceiptDetail;
use App\Models\SegmentMaster;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerReceivePaymentService
{
    public static function generateCustomerReceivePayment($input)
    {
        DB::beginTransaction();
        try {
            $bankMaster = BankAssign::ofCompany($input['companySystemID'])->isActive()->where('bankmasterAutoID', $input['bankMasterID'])->first();
            if (empty($bankMaster)) {
                DB::rollBack();
                return [
                    'status' => false,
                    'message' => 'Selected Bank is not active'
                ];
            }

            $bankAccount = BankAccount::isActive()->find($input['bankAccountAutoId']);
            if (empty($bankAccount)) {
                DB::rollBack();
                return [
                    'status' => false,
                    'message' => 'Selected Bank Account is not active'
                ];
            }

            $narration = isset($input['bankRecCode']) ? $input['bankRecCode'] : 'Generated from Auto Bank Reconciliation';
            $input['narration'] = $input['narration'] . '(' . $narration . ')';
            $input['custPaymentReceiveDate'] = $input['documentDate'];
            $input['documentType'] = 14;
            $input['payeeTypeID'] = 3;
            $input['paymentType'] = 1;
            $input['custTransactionCurrencyID'] = $input['currencyId'];
            $input['other'] = $input['Other'];
            $voucherMaster = self::createCustomerReceivePayment($input);
            if ($voucherMaster['status']) {
                $details['directReceiptAutoID'] = $voucherMaster['data']['custReceivePaymentAutoID'];
                $details['rows'] = $input['rows'];
                $details['serviceLineSystemID'] = $input['segment'];
                $details['companySystemID'] = $input['companySystemID'];
                $voucherDetails = self::createCustomerReceivePaymentDetails($details);
                if ($voucherDetails['status']) {
                    DB::commit();
                    return $voucherMaster;
                } else {
                    DB::rollBack();
                    return $voucherDetails;
                }
            }
            else{
                DB::rollBack();
                return $voucherMaster;
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => $exception->getMessage()
            ];
        }
    }

    public static function createCustomerReceivePayment($input)
    {
        $input['documentType'] = isset($input['documentType']) ? $input['documentType'] : 0;
        $input['companySystemID'] = isset($input['companySystemID']) ? $input['companySystemID'] : 0;
        if(!isset($input['paymentType'])){
            return [
                'status' => false,
                'message' => 'Payment Mode is required'
            ];
        }
        $input['payment_type_id'] = $input['paymentType'];
        unset($input['paymentType']);

        if (($input['documentType'] == 13 || $input['documentType'] == 15 ) && $input['customerID'] == '') {
            return [
                'status' => false,
                'message' => 'Customer is required'
            ];
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if (empty($company)) {
            return [
                'status' => false,
                'message' => 'Company not found'
            ];
        }

        $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYear["success"]) {
            return [
                'status' => false,
                'message' => $companyFinanceYear["message"]
            ];
        }

        $inputParam = $input;
        $inputParam["departmentSystemID"] = 4;
        $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
        if (!$companyFinancePeriod["success"]) {
            return [
                'status' => false,
                'message' => $companyFinancePeriod["message"]
            ];
        } else {
            $input['FYPeriodDateFrom'] = $companyFinancePeriod["message"]->dateFrom;
            $input['FYPeriodDateTo'] = $companyFinancePeriod["message"]->dateTo;
        }
        unset($inputParam);

        if (isset($input['custPaymentReceiveDate'])) {
            if ($input['custPaymentReceiveDate']) {
                $input['custPaymentReceiveDate'] = new Carbon($input['custPaymentReceiveDate']);
            }
        }

        $documentDate = $input['custPaymentReceiveDate'];
        $monthBegin = $input['FYPeriodDateFrom'];
        $monthEnd = $input['FYPeriodDateTo'];
        if (($documentDate < $monthBegin) || ($documentDate > $monthEnd)) {
            return [
                'status' => false,
                'message' => 'Document date is not within the financial period!'
            ];
        }

        $CompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])->first();
        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        $serialNo = CustomerReceivePayment::where('documentSystemID', 21)
            ->where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('serialNo', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($serialNo) {
            $lastSerialNumber = intval($serialNo->serialNo) + 1;
        }
        $y = date('Y', strtotime($CompanyFinanceYear->bigginingDate));
        $custPaymentReceiveCode = ($company->CompanyID . '\\' . $y . '\\BRV' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));

        $input['documentSystemID'] = 21;
        $input['documentID'] = 'BRV';
        $input['serialNo'] = $lastSerialNumber;
        $input['FYBiggin'] = $CompanyFinanceYear->bigginingDate;
        $input['FYEnd'] = $CompanyFinanceYear->endingDate;
        $input['custPaymentReceiveCode'] = $custPaymentReceiveCode;
        $input['custChequeDate'] = Carbon::now();

        /*currency*/
        $myCurr = $input['custTransactionCurrencyID'];
        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $myCurr, $myCurr, 0);
        if ($company) {
            $input['companyID'] = $company->CompanyID;
            $input['localCurrencyID'] = $company->localCurrencyID;
            $input['companyRptCurrencyID'] = $company->reportingCurrency;
            $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
            $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
        }
        $input['custTransactionCurrencyER'] = 1;

        if(isset($input['bankMasterID'])) {
            $input['bankID'] = $input['bankMasterID'];
            $input['bankAccount'] = $input['bankAccountAutoId'];
            $input['bankCurrency'] = $myCurr;
            $input['bankCurrencyER'] = 1;

            /** Bank Balance update */
            $cur_det['companySystemID'] = $input['companySystemID'];
            $cur_det['bankmasterAutoID'] = $input['bankID'];
            $cur_det['bankAccountAutoID'] = $input['bankAccount'];
            $cur_det_info =  (object)$cur_det;
            $bankBalance = app('App\Http\Controllers\API\BankAccountAPIController')->getBankAccountBalanceSummery($cur_det_info);
            $amount = $bankBalance['netBankBalance'];
            $currencies = CurrencyMaster::where('currencyID','=',$myCurr)->select('DecimalPlaces')->first();
            $rounded_amount =  number_format($amount,$currencies->DecimalPlaces,'.', '');
            $input['bankAccountBalance'] = $rounded_amount;

        } else {
            $bank = BankAssign::select('bankmasterAutoID')
                ->where('companySystemID', $company['companySystemID'])
                ->where('isDefault', -1)
                ->first();

            if ($bank) {
                $input['bankID'] = $bank->bankmasterAutoID;

                $bankAccount = BankAccount::where('companySystemID', $company['companySystemID'])
                    ->where('bankmasterAutoID', $bank->bankmasterAutoID)
                    ->where('isDefault', 1)
                    ->where('accountCurrencyID', $myCurr)
                    ->first();

                if ($bankAccount) {
                    $input['bankAccount'] = $bankAccount->bankAccountAutoID;
                    $input['bankCurrency'] = $myCurr;
                    $input['bankCurrencyER'] = 1;
                }
            }
        }

        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['createdUserID'] = \Helper::getEmployeeID();
        $input['createdPcID'] = getenv('COMPUTERNAME');
        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['modifiedUser'] = \Helper::getEmployeeID();
        $input['modifiedPc'] = getenv('COMPUTERNAME');

        if ($input['documentType'] == 13 || $input['documentType'] == 15) {
            /* Customer Invoice Receipt*/
            $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
            $input['customerGLCodeSystemID'] = $customer->custGLAccountSystemID;
            $input['customerGLCode'] = $customer->custGLaccount;
            $input['custAdvanceAccountSystemID'] = $customer->custAdvanceAccountSystemID;
            $input['custAdvanceAccount'] = $customer->custAdvanceAccount;
        }

        if ($input['documentType'] == 14) {
            /* Direct Invoice*/
            if($input['payeeTypeID'] != 1){
                $input = array_except($input, 'customerID');
            }
        }
        if(isset($input['employeeID'])){
            $input['PayeeEmpID'] = $input['employeeID'];
        }
        if(isset($input['other'])){
            $input['PayeeName'] = $input['other'];
        }

        if (($input['custPaymentReceiveDate'] >= $companyfinanceperiod->dateFrom) && ($input['custPaymentReceiveDate'] <= $companyfinanceperiod->dateTo)) {
            $customerReceivePayments = CustomerReceivePayment::create($input);
            return [
                'status' => true,
                'message' => 'Receipt voucher created successfully',
                'data' => $customerReceivePayments->toArray()
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Receipt voucher document date should be between financial period start and end date'
            ];
        }
    }

    public static function createCustomerReceivePaymentDetails($input)
    {
        $directReceiptAutoID = $input['directReceiptAutoID'];
        $companySystemID = $input['companySystemID'];

        $master = CustomerReceivePayment::where('custReceivePaymentAutoID', $directReceiptAutoID)->first();
        $company = Company::where('companySystemID', $companySystemID)->first();

        $serviceLine = SegmentMaster::select('serviceLineSystemID', 'ServiceLineCode')
            ->where('serviceLineSystemID', $input['serviceLineSystemID'])
            ->first();
        if(empty($serviceLine)){
            return [
                'status' => false,
                'message' => 'Department not found.'
            ];
        }
        $inputData['serviceLineSystemID'] = $serviceLine->serviceLineSystemID;
        $inputData['serviceLineCode'] = $serviceLine->ServiceLineCode;

        $bankGL = BankAccount::select('chartOfAccountSystemID')
            ->where('bankAccountAutoID', $master->bankAccount)
            ->first();

        foreach ($input['rows'] as $row) {
            $glCode = isset($row['glCode']) ? $row['glCode'] : 0;
            if($glCode){
                $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'chartOfAccountSystemID', 'controlAccounts')
                    ->where('chartOfAccountSystemID', $glCode)
                    ->first();
                if (empty($bankGL)){
                    return [
                        'status' => false,
                        'message' => 'Bank details not found.'
                    ];
                }

                if ($bankGL->chartOfAccountSystemID == $chartOfAccount->chartOfAccountSystemID) {
                    return [
                        'status' => false,
                        'message' => 'Cannot add. You are trying to select the same account.'
                    ];
                }
                $inputData['chartOfAccountSystemID'] = $chartOfAccount->chartOfAccountSystemID;
                $inputData['glCode'] = $chartOfAccount->AccountCode;
                $inputData['glCodeDes'] = $chartOfAccount->AccountDescription;
            }

            $inputData['directReceiptAutoID'] = $directReceiptAutoID;
            $inputData['companyID'] = $company->CompanyID;
            $inputData['companySystemID'] = $companySystemID;

            /*** Currency */
            $myCurr = $master->custTransactionCurrencyID;
            $decimal = \Helper::getCurrencyDecimalPlace($myCurr);

            $inputData['DRAmountCurrency'] = $master->custTransactionCurrencyID;
            $inputData['DDRAmountCurrencyER'] = $master->custTransactionCurrencyER;
            $inputData['DRAmount'] = round($row['amount'], $decimal);
            $inputData['netAmount'] = $inputData['DRAmount'];

            $currency = \Helper::currencyConversion($companySystemID, $master->custTransactionCurrencyID, $master->custTransactionCurrencyID, $row['amount']);
            $inputData['comRptCurrency'] = $master->companyRptCurrencyID;
            $inputData['comRptCurrencyER'] = $master->companyRptCurrencyER;
            $inputData["comRptAmount"] = \Helper::roundValue($currency['reportingAmount']);
            $inputData["netAmountRpt"] = $inputData["comRptAmount"];

            $inputData['localCurrency'] = $master->localCurrencyID;
            $inputData['localCurrencyER'] = $master->localCurrencyER;
            $inputData["localAmount"] = \Helper::roundValue($currency['localAmount']);
            $inputData["netAmountLocal"] = $inputData["localAmount"];

            $inputData['VATAmount'] = 0;
            $inputData['VATAmountRpt'] = 0;
            $inputData['VATAmountLocal'] = 0;

            DirectReceiptDetail::create($inputData);
        }

        $details = DirectReceiptDetail::select(
                DB::raw("IFNULL(SUM(DRAmount),0) as receivedAmount"),
                DB::raw("IFNULL(SUM(localAmount),0) as localAmount"),
                DB::raw("IFNULL(SUM(DRAmount),0) as bankAmount"),
                DB::raw("IFNULL(SUM(comRptAmount),0) as companyRptAmount"),
                DB::raw("IFNULL(SUM(VATAmount),0) as VATAmount"),
                DB::raw("IFNULL(SUM(VATAmountLocal),0) as VATAmountLocal"),
                DB::raw("IFNULL(SUM(VATAmountRpt),0) as VATAmountRpt"),
                DB::raw("IFNULL(SUM(netAmount),0) as netAmount"),
                DB::raw("IFNULL(SUM(netAmountLocal),0) as netAmountLocal"),
                DB::raw("IFNULL(SUM(netAmountRpt),0) as netAmountRpt"))
            ->where('directReceiptAutoID', $directReceiptAutoID)
            ->first()
            ->toArray();

        CustomerReceivePayment::where('custReceivePaymentAutoID', $directReceiptAutoID)->update($details);

        return [
            'status' => true,
            'message' => 'Voucher details added successfully.'
        ];
    }
}
