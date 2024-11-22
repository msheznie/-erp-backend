<?php

namespace App\Services;

use App\helper\Helper;
use App\helper\PaySupplier;
use App\Models\BankAccount;
use App\Models\BankAssign;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CompanyFinanceYear;
use App\Models\DirectPaymentDetails;
use App\Models\DocumentMaster;
use App\Models\Employee;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\SegmentMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierCurrency;
use App\Models\SupplierMaster;
use App\Models\SystemGlCodeScenarioDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentVoucherServices
{
    public static function generatePaymentVoucher($input)
    {
        DB::beginTransaction();
        try {
            $bankMaster = BankAssign::ofCompany($input['companySystemID'])->isActive()->where('bankmasterAutoID', $input['bankMasterID'])->first();
            if (empty($bankMaster)) {
                DB::rollBack();
                return [
                    'status' => false,
                    'message' => 'Selected Bank is not active',
                    'type' => []
                ];
            }

            $bankAccount = BankAccount::isActive()->find($input['bankAccountAutoId']);
            if (empty($bankAccount)) {
                DB::rollBack();
                return [
                    'status' => false,
                    'message' => 'Selected Bank Account is not active',
                    'type' => []
                ];
            }

            $input['BPVNarration'] = $input['narration'] . ' (' . $input['bankRecCode'] . ')';
            $input['BPVdate'] = $input['documentDate'];
            $input['BPVchequeDate'] = $input['documentDate'];
            $input['rcmActivated'] = false;
            $input['paymentMode'] = 1;
            $input['payeeType'] = 3;
            $input['invoiceType'] = 3;
            $input['documentSystemID'] = 4;
            $input['directPaymentPayee'] = $input['Other'];
            $input['BPVbank'] = $input['bankMasterID'];
            $input['BPVAccount'] = $input['bankAccountAutoId'];
            $input['supplierTransCurrencyID'] = $input['currencyId'];

            $voucherMaster = self::createPaymentVoucher($input);
            if ($voucherMaster['status']) {
                $details['directPaymentAutoID'] = $voucherMaster['data']['PayMasterAutoId'];
                $details['values'] = $input['rows'];
                $details['serviceLineSystemID'] = $input['segment'];
                $details['companySystemID'] = $input['companySystemID'];
                $voucherDetails = self::createPaymentVoucherDetails($details, $bankAccount->chartOfAccountSystemID);
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
                'message' => $exception->getMessage(),
                'type' => []
            ];
        }
    }

    public static function createPaymentVoucher($input)
    {
        $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYear["success"]) {
            return [
                'status' => false,
                'message' => $companyFinanceYear["message"],
                'type' => []
            ];
        } else {
            $input['FYBiggin'] = $companyFinanceYear["message"]->bigginingDate;
            $input['FYEnd'] = $companyFinanceYear["message"]->endingDate;
        }

        $inputParam = $input;
        $inputParam["departmentSystemID"] = 1;
        $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
        if (!$companyFinancePeriod["success"]) {
            return [
                'status' => false,
                'message' => $companyFinancePeriod["message"],
                'type' => []
            ];
        } else {
            $input['FYPeriodDateFrom'] = $companyFinancePeriod["message"]->dateFrom;
            $input['FYPeriodDateTo'] = $companyFinancePeriod["message"]->dateTo;
        }

        unset($inputParam);

        $input['BPVdate'] = new Carbon($input['BPVdate']);
        $input['BPVchequeDate'] = new Carbon($input['BPVchequeDate']);

        $monthBegin = $input['FYPeriodDateFrom'];
        $monthEnd = $input['FYPeriodDateTo'];

        if (($input['BPVdate'] >= $monthBegin) && ($input['BPVdate'] <= $monthEnd)) {
        } else {
            return [
                'status' => false,
                'message' => 'Payment voucher date is not within financial period!',
                'type' => ['type' => 'finance_period']
            ];
        }

        if (isset($input['invoiceType']) && $input['invoiceType'] == 3 && isset($input['preCheck']) && $input['preCheck'] &&  !Helper::isLocalSupplier($input['BPVsupplierID'], $input['companySystemID'])) {
            $company = Company::where('companySystemID', $input['companySystemID'])->first();
            if (!empty($company) && $company->vatRegisteredYN == 1) {
                return [
                    'status' => false,
                    'message' => 'Do you want to activate Reverse Charge Mechanism for this Invoice',
                    'type' => ['type' => 'rcm_confirm']
                ];
            }
        }

        $company = Company::find($input['companySystemID']);
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $documentMaster = DocumentMaster::find($input['documentSystemID']);
        if ($documentMaster) {
            $input['documentID'] = $documentMaster->documentID;
        }

        $lastSerial = PaySupplierInvoiceMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('serialNo', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        if ($companyFinanceYear["message"]) {
            $startYear = $companyFinanceYear["message"]['bigginingDate'];
            $finYearExp = explode('-', $startYear);
            $finYear = $finYearExp[0];
        } else {
            $finYear = date("Y");
        }
        if ($documentMaster) {
            $documentCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['BPVcode'] = $documentCode;
        }
        $input['serialNo'] = $lastSerialNumber;

        if (isset($input['BPVsupplierID']) && !empty($input['BPVsupplierID'])) {
            $supDetail = SupplierAssigned::where('supplierCodeSytem', $input['BPVsupplierID'])->where('companySystemID', $input['companySystemID'])->first();

            $supCurrency = SupplierCurrency::where('supplierCodeSystem', $input['BPVsupplierID'])->where('isAssigned', -1)->where('isDefault', -1)->first();

            if ($supDetail) {
                $input['supplierGLCode'] = $supDetail->liabilityAccount;
                $input['supplierGLCodeSystemID'] = $supDetail->liabilityAccountSysemID;
                $input['VATPercentage'] = $supDetail->vatPercentage;
            }
            $input['supplierTransCurrencyER'] = 1;
            if ($supCurrency) {
                $input['supplierDefCurrencyID'] = $supCurrency->currencyID;
                $currencyConversionDefaultMaster = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransCurrencyID'], $supCurrency->currencyID, 0);
                if ($currencyConversionDefaultMaster) {
                    $input['supplierDefCurrencyER'] = $currencyConversionDefaultMaster['transToDocER'];
                }
            }
            $supplier = SupplierMaster::find($input['BPVsupplierID']);
            $input['directPaymentPayee'] = $supplier->supplierName;
        } else {
            $input['supplierTransCurrencyER'] = 1;
            $input['supplierDefCurrencyID'] = $input['supplierTransCurrencyID'];
            $input['supplierDefCurrencyER'] = 1;
        }

        $bankAccount = BankAccount::find($input['BPVAccount']);
        if ($bankAccount) {
            $input['BPVbankCurrency'] = $bankAccount->accountCurrencyID;
            $currencyConversionDefaultMaster = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransCurrencyID'], $bankAccount->accountCurrencyID, 0);
            if ($currencyConversionDefaultMaster) {
                $input['BPVbankCurrencyER'] = $currencyConversionDefaultMaster['transToDocER'];
            }
        }

        $companyCurrency = \Helper::companyCurrency($input['companySystemID']);
        if ($companyCurrency) {
            $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
            $input['companyRptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
            $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransCurrencyID'], $input['supplierTransCurrencyID'], 0);
            if ($companyCurrencyConversion) {
                $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
            }
        }

        if ($input['invoiceType'] == 3) {
            if ($input['payeeType'] == 3) {
                $input['directPaymentpayeeYN'] = -1;
                $input['directPaymentPayeeSelectEmp'] = 0;
                $input['directPaymentPayeeEmpID'] = null;
            }
            if ($input['payeeType'] == 2) {
                $input['directPaymentPayeeSelectEmp'] = -1;
                $emp = Employee::find($input["directPaymentPayeeEmpID"]);
                $input['directPaymentPayee'] = $emp->empFullName;
            }
        }
        if ($input['invoiceType'] == 5) {
            $supDetail = SupplierAssigned::where('supplierCodeSytem', $input['BPVsupplierID'])->where('companySystemID', $input['companySystemID'])->first();
            if($supDetail)
            {
                $input['AdvanceAccount'] = $supDetail->AdvanceAccount;
                $input['advanceAccountSystemID'] = $supDetail->advanceAccountSystemID;
            }
        }

        if ($input['invoiceType'] == 7) {
            $checkEmployeeControlAccount = SystemGlCodeScenarioDetail::getGlByScenario($input['companySystemID'], $input['documentSystemID'], "employee-control-account");
            if (is_null($checkEmployeeControlAccount)) {
                return [
                    'status' => false,
                    'message' => 'Please configure Employee control account for this company',
                    'type' => []
                ];
            }

            $input['AdvanceAccount'] = ChartOfAccount::getAccountCode($checkEmployeeControlAccount);
            $input['advanceAccountSystemID'] = $checkEmployeeControlAccount;

            $isEmpAdvConfigured = SystemGlCodeScenarioDetail::getGlByScenario($input['companySystemID'], $input['documentSystemID'], "employee-advance-account");
            if (is_null($isEmpAdvConfigured)) {
                return [
                    'status' => false,
                    'message' => 'Please configure employee advance account for this company',
                    'type' => ['type' => 'create']
                ];
            }

            $input['employeeAdvanceAccount'] = ChartOfAccount::getAccountCode($isEmpAdvConfigured);
            $input['employeeAdvanceAccountSystemID'] = $isEmpAdvConfigured;

            $emp = Employee::find($input["directPaymentPayeeEmpID"]);
            $input['directPaymentPayee'] = $emp->empFullName;
        }

        if ($input['invoiceType'] == 6) {
            $checkEmployeeControlAccount = SystemGlCodeScenarioDetail::getGlByScenario($input['companySystemID'], $input['documentSystemID'], "employee-control-account");
            if (is_null($checkEmployeeControlAccount)) {
                return [
                    'status' => false,
                    'message' => 'Please configure Employee control account for this company',
                    'type' => []
                ];
            }
            $input['supplierGLCodeSystemID'] = $checkEmployeeControlAccount;
            $input['supplierGLCode'] = ChartOfAccount::getAccountCode($checkEmployeeControlAccount);
            $emp = Employee::find($input["directPaymentPayeeEmpID"]);
            $input['directPaymentPayee'] = $emp->empFullName;
        }

        if (isset($input['paymentMode'])) {
            if ($input['paymentMode'] == 2) {
                $input['chequePaymentYN'] = -1;
            } else {
                $input['chequePaymentYN'] = 0;
            }
        } else {
            $input['chequePaymentYN'] = 0;
        }

        if (isset($input['pdcChequeYN']) && $input['pdcChequeYN']) {
            $input['chequePaymentYN'] = 0;
            $input['BPVchequeDate'] = null;
        } else {
            $input['pdcChequeYN'] = 0;
        }

        $input['directPayeeCurrency'] = $input['supplierTransCurrencyID'];
        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = \Helper::getEmployeeID();
        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();

        $input['payment_mode'] = $input['paymentMode'];
        unset($input['paymentMode']);

        $paySupplierInvoiceMasters = PaySupplierInvoiceMaster::create($input);

        return [
            'status' => true,
            'data' => $paySupplierInvoiceMasters->toArray(),
            'message' => 'Pay Supplier Invoice Master saved successfully'
        ];
    }

    public static function createPaymentVoucherDetails($input, $bankAccountGl)
    {
        $company = Company::find($input['companySystemID']);
        if (empty($company)) {
            return [
                'status' => false,
                'message' => 'Company not found',
                'type' => []
            ];
        }
        if(isset($input['directPaymentAutoID'])){
            $payMaster = PaySupplierInvoiceMaster::find($input['directPaymentAutoID']);
        }

        if (empty($payMaster)) {
            return [
                'status' => false,
                'message' => 'Direct Payment Supp Master not found',
                'type' => []
            ];
        }

        if($payMaster->confirmedYN){
            return [
                'status' => false,
                'message' => 'You cannot update Direct Payment Detail, this document already confirmed',
                'type' => []
            ];
        }

        $checkDepartmentActive = SegmentMaster::find($input['serviceLineSystemID']);
        if (empty($checkDepartmentActive)) {
            return [
                'status' => false,
                'message' => 'Department not found',
                'type' => []
            ];
        }

        if ($checkDepartmentActive->isActive == 0) {
            return [
                'status' => false,
                'message' => 'Please select an active department',
                'type' => ['type' => 'serviceLine']
            ];
        }
        $input['serviceLineCode'] = $checkDepartmentActive->ServiceLineCode;

        $a = 1;
        foreach ($input['values'] as $row) {
            if ($bankAccountGl == $row['glCode']) {
                return [
                    'status' => false,
                    'message' => 'You are trying to select the same bank account for detail row ' . $a,
                    'type' => []
                ];
            }

            $chartOfAccount = ChartOfAccount::find($row['glCode']);
            if (empty($chartOfAccount)) {
                return [
                    'status' => false,
                    'message' => 'Chart of Account not found for detail row ' . $a,
                    'type' => []
                ];
            }

            if ($chartOfAccount->controlAccountsSystemID == 1) {
                return [
                    'status' => false,
                    'message' => 'Cannot add a revenue GL code for detail row ' . $a,
                    'type' => []
                ];
            }

            $Voucherdetail['directPaymentAutoID'] = $input['directPaymentAutoID'];
            $Voucherdetail['companyID'] = $company->CompanyID;
            $Voucherdetail['companySystemID'] = $input['companySystemID'];
            $Voucherdetail['chartOfAccountSystemID'] = $row['glCode'];
            $Voucherdetail['glCode'] = $chartOfAccount->AccountCode;
            $Voucherdetail['glCodeDes'] = $chartOfAccount->AccountDescription;
            $Voucherdetail['glCodeIsBank'] = $chartOfAccount->isBank;
            $Voucherdetail['relatedPartyYN'] = $chartOfAccount->relatedPartyYN;

            $Voucherdetail['supplierTransCurrencyID'] = $payMaster->supplierTransCurrencyID;
            $Voucherdetail['supplierTransER'] = 1;
            $Voucherdetail['DPAmountCurrency'] = $payMaster->supplierTransCurrencyID;
            $Voucherdetail['DPAmountCurrencyER'] = 1;
            $Voucherdetail['localCurrency'] = $payMaster->localCurrencyID;
            $Voucherdetail['localCurrencyER'] = $payMaster->localCurrencyER;
            $Voucherdetail['comRptCurrency'] = $payMaster->companyRptCurrencyID;
            $Voucherdetail['comRptCurrencyER'] = $payMaster->companyRptCurrencyER;
            $Voucherdetail['bankCurrencyID'] = $payMaster->BPVbankCurrency;
            $Voucherdetail['bankCurrencyER'] = $payMaster->BPVbankCurrencyER;

            $Voucherdetail['serviceLineSystemID'] = $input['serviceLineSystemID'];
            $Voucherdetail['serviceLineCode'] = $input['serviceLineCode'];

            if ($payMaster->BPVsupplierID) {
                $Voucherdetail['supplierTransCurrencyID'] = $payMaster->supplierTransCurrencyID;
                $Voucherdetail['supplierTransER'] = $payMaster->supplierTransCurrencyER;
            }

            if ($payMaster->FYBiggin) {
                $finYearExp = explode('-', $payMaster->FYBiggin);
                $Voucherdetail['budgetYear'] = $finYearExp[0];
            } else {
                $Voucherdetail['budgetYear'] = CompanyFinanceYear::budgetYearByDate(now(), $input['companySystemID']);
            }

            $currency = \Helper::currencyConversion($input['companySystemID'], $Voucherdetail['supplierTransCurrencyID'], $Voucherdetail['supplierTransCurrencyID'], $row['amount']);
            $Voucherdetail['DPAmount'] = \Helper::roundValue($row['amount']);
            $Voucherdetail['localAmount'] = \Helper::roundValue($currency['localAmount']);
            $Voucherdetail['comRptAmount'] = \Helper::roundValue($currency['reportingAmount']);
            $Voucherdetail['bankAmount'] = \Helper::roundValue($row['amount']);

            $Voucherdetail['netAmount'] = \Helper::roundValue($row['amount']);
            $Voucherdetail['netAmountLocal'] = \Helper::roundValue($currency['localAmount']);
            $Voucherdetail['netAmountRpt'] = \Helper::roundValue($currency['reportingAmount']);

            DirectPaymentDetails::create($Voucherdetail);
            $a++;
        }
        PaySupplier::updateMaster($input['directPaymentAutoID']);
        return [
            'status' => true,
            'message' => 'Payment Voucher details added successfully',
            'type' => []
        ];
    }
}
