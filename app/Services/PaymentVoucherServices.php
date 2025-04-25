<?php

namespace App\Services;

use App\helper\CustomValidation;
use App\helper\Helper;
use App\helper\PaySupplier;
use App\helper\TaxService;
use App\Models\AccountsPayableLedger;
use App\Models\AdvancePaymentDetails;
use App\Models\BankAccount;
use App\Models\BankAssign;
use App\Models\BankMemoPayee;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\ChequeRegister;
use App\Models\ChequeRegisterDetail;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\CurrencyConversion;
use App\Models\CurrencyMaster;
use App\Models\DirectPaymentDetails;
use App\Models\DocumentMaster;
use App\Models\Employee;
use App\Models\EmployeeLedger;
use App\Models\ErpProjectMaster;
use App\Models\ExpenseAssetAllocation;
use App\Models\ExpenseEmployeeAllocation;
use App\Models\MatchDocumentMaster;
use App\Models\PaymentVoucherBankChargeDetails;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\PdcLog;
use App\Models\PoAdvancePayment;
use App\Models\SegmentMaster;
use App\Models\SrpEmployeeDetails;
use App\Models\SupplierAssigned;
use App\Models\SupplierCurrency;
use App\Models\SupplierMaster;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\Tax;
use App\Models\Taxdetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            $employee = UserTypeService::getSystemEmployee();
            $input['createdUserID'] = $employee->empID;
            $input['createdUserSystemID'] = $employee->employeeSystemID;
        }
        else{
            $input['createdUserID'] = \Helper::getEmployeeID();
            $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
        }

        $input['payment_mode'] = $input['paymentMode'];
        unset($input['paymentMode']);

        $paySupplierInvoiceMasters = PaySupplierInvoiceMaster::create($input);

        $is_exist_policy_GCNFCR = Helper::checkPolicy($input['companySystemID'], 35);

        if($input['payment_mode'] == 2 && !$input['pdcChequeYN'] && $is_exist_policy_GCNFCR) {
            $checkRegisterDetails = ChequeRegisterDetail::where('id',$input['BPVchequeNoDropdown'])
                ->where('company_id',$input['companySystemID'])
                ->first();
            Log::info($checkRegisterDetails);
            if($checkRegisterDetails) {

                $cheque_no = $checkRegisterDetails->cheque_no;

                Log::info('$cheque_no');
                Log::info($cheque_no);

                /*update cheque detail table */
                $checkRegisterDetails->document_id = $paySupplierInvoiceMasters->PayMasterAutoId;
                $checkRegisterDetails->document_master_id = $paySupplierInvoiceMasters->documentSystemID;
                $checkRegisterDetails->status = 1;
                $checkRegisterDetails->save();


                Log::info($checkRegisterDetails->cheque_no);

                PaySupplierInvoiceMaster::find($paySupplierInvoiceMasters->PayMasterAutoId)->update([
                    'BPVchequeNo' => $checkRegisterDetails->cheque_no
                ]);
            }
        }

        return [
            'status' => true,
            'data' => $paySupplierInvoiceMasters->refresh()->toArray(),
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

    public static function updatePaymentVoucher($id, $input): array {

        $paySupplierInvoiceMaster = PaySupplierInvoiceMaster::find($id);

        if (empty($paySupplierInvoiceMaster)) {
            return [
                'status' => false,
                'message' => 'Pay Supplier Invoice Master not found'
            ];
        }

        $customValidation = CustomValidation::validation(4, $paySupplierInvoiceMaster, 2, $input);
        if (!$customValidation["success"]) {
            return [
                'status' => false,
                'message' => $customValidation["message"],
                'code' => 500,
                'type' => array('type' => 'already_confirmed')
            ];
        }

        $supplier_id = $input['BPVsupplierID'];
        $supplierMaster = SupplierMaster::where('supplierCodeSystem',$supplier_id)->first();

        $companySystemID = $paySupplierInvoiceMaster->companySystemID;
        $documentSystemID = $paySupplierInvoiceMaster->documentSystemID;
        $input['companySystemID'] = $companySystemID;


        if ($input['payeeType'] == 1) {
            if (isset($input['BPVsupplierID']) && !empty($input['BPVsupplierID'])) {
                $supDetail = SupplierAssigned::where('supplierCodeSytem', $input['BPVsupplierID'])->where('companySystemID', $companySystemID)->first();

                $supCurrency = SupplierCurrency::where('supplierCodeSystem', $input['BPVsupplierID'])->where('isAssigned', -1)->where('isDefault', -1)->first();
                $input['directPaymentPayeeEmpID'] = 0;
                if ($supDetail) {
                    $input['supplierGLCode'] = $supDetail->liabilityAccount;
                    $input['supplierGLCodeSystemID'] = $supDetail->liabilityAccountSysemID;

                }
                $input['supplierTransCurrencyER'] = 1;
                if ($supCurrency) {
                    $input['supplierDefCurrencyID'] = $supCurrency->currencyID;
                    $currencyConversionDefaultMaster = \Helper::currencyConversion($companySystemID, $input['supplierTransCurrencyID'], $supCurrency->currencyID, 0);
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
        } else {
            $input['supplierTransCurrencyER'] = 1;
            $input['supplierDefCurrencyID'] = $input['supplierTransCurrencyID'];
            $input['supplierDefCurrencyER'] = 1;
        }

        if ($input['invoiceType'] == 6 || $input['invoiceType'] == 7) {
            $checkEmployeeControlAccount = SystemGlCodeScenarioDetail::getGlByScenario($input['companySystemID'], $input['documentSystemID'], "employee-control-account");

            if (is_null($checkEmployeeControlAccount)) {
                return [
                    'status' => false,
                    'message' => 'Please configure Employee control account for this company',
                    'code' => 500
                ];
            }


            $input['BPVsupplierID'] = 0;
            $input['supplierGLCodeSystemID'] = $checkEmployeeControlAccount;
            $input['supplierGLCode'] = ChartOfAccount::getAccountCode($checkEmployeeControlAccount);
            $emp = Employee::find($input["directPaymentPayeeEmpID"]);
            if(isset($emp) && $emp != null)
            {
                $input['directPaymentPayee'] = $emp->empFullName;
            }

        }

        if ($input['invoiceType'] == 7) {
            $isEmpAdvConfigured = SystemGlCodeScenarioDetail::getGlByScenario($input['companySystemID'], $input['documentSystemID'], "employee-advance-account");

            if (is_null($isEmpAdvConfigured)) {
                return [
                    'status' => false,
                    'message' => 'Please configure employee advance account for this company',
                    'code' => 500,
                    'type' => array('type' => 'create')
                ];
            }

            $input['employeeAdvanceAccount'] = ChartOfAccount::getAccountCode($isEmpAdvConfigured);
            $input['employeeAdvanceAccountSystemID'] = $isEmpAdvConfigured;
        } else {
            $input['employeeAdvanceAccount'] = null;
            $input['employeeAdvanceAccountSystemID'] = null;
        }

        if ($paySupplierInvoiceMaster->expenseClaimOrPettyCash == 6 || $paySupplierInvoiceMaster->expenseClaimOrPettyCash == 7) {
            if (isset($input['interCompanyToSystemID'])) {
                if ($input['interCompanyToSystemID']) {
                    $interCompany = Company::find($input['interCompanyToSystemID']);
                    if ($interCompany) {
                        $input['interCompanyToID'] = $interCompany->CompanyID;
                    }
                } else {
                    $input['interCompanyToSystemID'] = null;
                    $input['interCompanyToID'] = null;
                }
            } else {
                $input['interCompanyToSystemID'] = null;
                $input['interCompanyToID'] = null;
            }
        }
        else {
            $input['interCompanyToSystemID'] = null;
            $input['interCompanyToID'] = null;
        }

        if (!isset($input['expenseClaimOrPettyCash'])) {
            $input['expenseClaimOrPettyCash'] = null;
        }

        $bankAccount = BankAccount::find($input['BPVAccount']);
        if ($bankAccount) {
            $input['BPVbankCurrency'] = $bankAccount->accountCurrencyID;
            $currencyConversionDefaultMaster = \Helper::currencyConversion($companySystemID, $input['supplierTransCurrencyID'], $bankAccount->accountCurrencyID, 0);
            if (!isset($paySupplierInvoiceMaster->BPVbankCurrencyER)) {
                if($currencyConversionDefaultMaster){
                    $input['BPVbankCurrencyER'] = $currencyConversionDefaultMaster['transToDocER'];
                } else {
                    $input['BPVbankCurrencyER'] = 0;
                }
            }else {
                $input['BPVbankCurrencyER'] = $paySupplierInvoiceMaster->BPVbankCurrencyER;
            }

        }else{
            $input['BPVbankCurrency'] = 0;
            $input['BPVbankCurrencyER'] = 0;
        }

        $companyCurrency = \Helper::companyCurrency($companySystemID);
        if ($companyCurrency) {
            $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
            $input['companyRptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
            $companyCurrencyConversion = \Helper::currencyConversion($companySystemID, $input['supplierTransCurrencyID'], $input['supplierTransCurrencyID'], 0);
            if ($companyCurrencyConversion) {
                $policy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
                    ->where('companyPolicyCategoryID', 67)
                    ->where('isYesNO', 1)
                    ->first();
                $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;

                // if($policy == false || $paySupplierInvoiceMaster->invoiceType != 3) {
                $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                // }
            }
        }


        $checkErChange = isset($input['checkErChange']) ? $input['checkErChange'] : true;

        if ((($paySupplierInvoiceMaster->BPVbankCurrencyER != $input['BPVbankCurrencyER'] && $input['BPVbankCurrency'] == $paySupplierInvoiceMaster->BPVbankCurrency) || $paySupplierInvoiceMaster->localCurrencyER != $input['localCurrencyER'] && $input['localCurrencyID'] == $paySupplierInvoiceMaster->localCurrencyID || $paySupplierInvoiceMaster->companyRptCurrencyER != $input['companyRptCurrencyER'] && $input['companyRptCurrencyID'] == $paySupplierInvoiceMaster->companyRptCurrencyID)) {

            if ($checkErChange && $input['confirmedYN'] == 1) {
                if(($input['BPVbankCurrencyEROld'] != $paySupplierInvoiceMaster->BPVbankCurrencyER) || ($input['localCurrencyEROld'] != $paySupplierInvoiceMaster->localCurrencyER) || ($input['companyRptCurrencyEROld'] != $paySupplierInvoiceMaster->companyRptCurrencyER))
                {
                    $erMessage = "<p>The exchange rates are updated as follows,</p><p style='font-size: medium;'>Previous rates Bank ER ".$input['BPVbankCurrencyEROld']." | Local ER ".$input['localCurrencyEROld']." | Reporting ER ".$input['companyRptCurrencyEROld']."</p><p style='font-size: medium;'>Current rates Bank ER ".$paySupplierInvoiceMaster->BPVbankCurrencyER." | Local ER ".$paySupplierInvoiceMaster->localCurrencyER." | Reporting ER ".$paySupplierInvoiceMaster->companyRptCurrencyER."</p><p>Are you sure you want to proceed ?</p>";
                }else {
                    $erMessage = "<p>The exchange rates are updated as follows,</p><p style='font-size: medium;'>Previous rates Bank ER ".$paySupplierInvoiceMaster->BPVbankCurrencyER." | Local ER ".$paySupplierInvoiceMaster->localCurrencyER." | Reporting ER ".$paySupplierInvoiceMaster->companyRptCurrencyER."</p><p style='font-size: medium;'>Current rates Bank ER ".$input['BPVbankCurrencyER']." | Local ER ".$input['localCurrencyER']." | Reporting ER ".$input['companyRptCurrencyER']."</p><p>Are you sure you want to proceed ?</p>";
                }

                return [
                    'status' => false,
                    'message' => $erMessage,
                    'code' => 500,
                    'type' => ['type' => 'erChange']
                ];
            } else {
                unset($input['localCurrencyER']);
                unset($input['companyRptCurrencyER']);
                //PaySupplierInvoiceMaster::where('PayMasterAutoId', $paySupplierInvoiceMaster->PayMasterAutoId)->update(['BPVbankCurrencyER' => $input['BPVbankCurrencyER'], 'localCurrencyER' => $input['localCurrencyER'], 'companyRptCurrencyER' => $input['companyRptCurrencyER']]);
            }
        }

        if ($paySupplierInvoiceMaster->invoiceType == 3) {
            if ($input['payeeType'] == 3) {
                $input['directPaymentpayeeYN'] = -1;
                $input['directPaymentPayeeSelectEmp'] = 0;
                $input['directPaymentPayeeEmpID'] = null;
                $input['supplierGLCode'] = null;
                $input['supplierGLCodeSystemID'] = null;
                $input['supplierDefCurrencyID'] = null;
                $input['supplierDefCurrencyER'] = null;
                $input['BPVsupplierID'] = null;
            }
            if ($input['payeeType'] == 2) {
                $input['directPaymentPayeeSelectEmp'] = -1;
                $emp = Employee::find($input["directPaymentPayeeEmpID"]);
                if (!empty($emp)) {
                    $input['directPaymentPayee'] = $emp->empFullName;
                } else {
                    $input['directPaymentPayee'] = null;
                }
                $input['directPaymentpayeeYN'] = 0;
                $input['supplierGLCode'] = null;
                $input['supplierGLCodeSystemID'] = null;
                $input['supplierDefCurrencyID'] = null;
                $input['supplierDefCurrencyER'] = null;
                $input['BPVsupplierID'] = null;
            }
            if ($input['payeeType'] == 1) {
                $input['directPaymentpayeeYN'] = 0;
                $input['directPaymentPayeeSelectEmp'] = 0;
                $input['directPaymentPayeeEmpID'] = null;
            }
        }

        $input['directPayeeCurrency'] = $input['supplierTransCurrencyID'];

        if (isset($input['chequePaymentYN'])) {
            if ($input['chequePaymentYN'] && $input['paymentMode'] == 2) {
                $input['chequePaymentYN'] = -1;
            } else {
                $input['chequePaymentYN'] = 0;
            }
        } else {
            $input['chequePaymentYN'] = 0;
        }

        if (isset($input['pdcChequeYN']) && $input['pdcChequeYN']) {
            $input['BPVchequeDate'] = null;
            $input['BPVchequeNo'] = null;
            $input['expenseClaimOrPettyCash'] = null;

            if(!is_null($paySupplierInvoiceMaster->BPVchequeNo) && ($paySupplierInvoiceMaster->BPVchequeNo != 0)) {
                ChequeRegisterDetail::where('document_id', $input['PayMasterAutoId'])
                    ->where('document_master_id', $input['documentSystemID'])
                    ->where('company_id', $companySystemID)
                    ->where('cheque_no', $paySupplierInvoiceMaster->BPVchequeNo)
                    ->update(['status' => 0, 'document_master_id' => null, 'document_id' => null]);
            }

        } else {
            $input['pdcChequeYN'] = 0;
        }

        if (isset($input['pdcChequeYN']) && $input['pdcChequeYN'] == false) {

            $isPdcLog = PdcLog::where('documentSystemID', $input['documentSystemID'])
                ->where('documentmasterAutoID', $input['PayMasterAutoId'])
                ->first();

            if(!empty($isPdcLog)) {
                ChequeRegisterDetail::where('document_id', $input['PayMasterAutoId'])->where('document_master_id', $input['documentSystemID'])->update(['status' => 0, 'document_master_id' => null, 'document_id' => null]);

                PdcLog::where('documentSystemID', $input['documentSystemID'])
                    ->where('documentmasterAutoID', $input['PayMasterAutoId'])
                    ->delete();
            }

        }


        $warningMessage = '';

        if ($input['BPVbankCurrency'] == $input['localCurrencyID'] && $input['supplierTransCurrencyID'] == $input['localCurrencyID']) {

        } else {
            if (isset($input['pdcChequeYN']) && $input['pdcChequeYN'] == 0 && $input['paymentMode'] == 2) {
                $warningMessage = "Cheque number won't be generated. The bank currency and the local currency is not equal.";
            }
        }

        $input['BPVdate'] = new Carbon($input['BPVdate']);
        $input['BPVchequeDate'] = new Carbon($input['BPVchequeDate']);
        Log::useFiles(storage_path() . '/logs/pv_cheque_no_jobs.log');

        $changeChequeNoBaseOnPolicy = false;
        $is_exist_policy_GCNFCR = Helper::checkPolicy($companySystemID, 35);

        if($input['paymentMode'] == 2 && !$input['pdcChequeYN'] && $is_exist_policy_GCNFCR) {
            $checkRegisterDetails = ChequeRegisterDetail::where('id',$input['BPVchequeNoDropdown'])
                ->where('company_id',$companySystemID)
                ->first();

            if($checkRegisterDetails) {
                $input['BPVchequeNo'] = $checkRegisterDetails->cheque_no;
                $changeChequeNoBaseOnPolicy = true;

                /*update cheque detail table */
                $checkRegisterDetails->document_id = $id;
                $checkRegisterDetails->document_master_id = $documentSystemID;
                $checkRegisterDetails->status = 1;
                $checkRegisterDetails->save();

                if((!is_null($paySupplierInvoiceMaster->BPVchequeNo) && $paySupplierInvoiceMaster->BPVchequeNo != 0) && ($paySupplierInvoiceMaster->BPVchequeNo != $checkRegisterDetails->cheque_no)) {
                    $chequeRegisterData = ChequeRegister::where('bank_id',$paySupplierInvoiceMaster['BPVbank'])
                        ->where('bank_account_id',$paySupplierInvoiceMaster['BPVAccount'])
                        ->where('company_id',$paySupplierInvoiceMaster['companySystemID'])
                        ->where('started_cheque_no', '<=' ,$paySupplierInvoiceMaster['BPVchequeNo'])
                        ->where('ended_cheque_no', '>=' ,$paySupplierInvoiceMaster['BPVchequeNo'])
                        ->first();

                    $checkRegisterDetails = ChequeRegisterDetail::where('cheque_register_master_id',$chequeRegisterData->id)
                        ->where('company_id',$paySupplierInvoiceMaster['companySystemID'])
                        ->where('cheque_no',$paySupplierInvoiceMaster['BPVchequeNo'])
                        ->first();

                    $checkRegisterDetails->document_id = null;
                    $checkRegisterDetails->document_master_id = null;
                    $checkRegisterDetails->status = 0;
                    $checkRegisterDetails->save();
                }
            }
            unset($checkRegisterDetails);
        }

        if ($paySupplierInvoiceMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {

            if ($input['invoiceType'] == 3) {
                $taxes = Tax::with(['vat_categories'=> function($query) {
                    $query->where('isActive', true);
                }])->where('companySystemID',$input['companySystemID'])->get();
                $vatCategoreis = array();
                foreach ($taxes as $tax)
                {
                    $vatCategoreis[] = $tax->vat_categories;
                }

                if(count($vatCategoreis) > 0 && count(collect(array_flatten($vatCategoreis))->where('subCatgeoryType',3)) == 0 && $paySupplierInvoiceMaster->directdetail->where('vatSubCategoryID',3)->count() > 0)
                {
                    return [
                        'status' => false,
                        'message' => 'The exempt VAT category has not been created. Please set up the required category before proceeding',
                        'code' => 500
                    ];
                }
            }

            // checking minus value
            if ($input['invoiceType'] == 2) {

                $checkBankChargeTotal = PaymentVoucherBankChargeDetails::where('payMasterAutoID', $input['PayMasterAutoId'])->sum('dpAmount');

                $checkInvoiceDetailTotal = PaySupplierInvoiceDetail::where('PayMasterAutoId', $input['PayMasterAutoId'])->sum('supplierPaymentAmount');

                $netMinustot = $checkBankChargeTotal + $checkInvoiceDetailTotal;

                if ($netMinustot < 0) {
                    return [
                        'status' => false,
                        'message' => 'Net amount cannot be negative value',
                        'code' => 500
                    ];
                }

                $checkQuantity = PaymentVoucherBankChargeDetails::where('payMasterAutoID', $input['PayMasterAutoId'])
                    ->where(function ($q) {
                        $q->where('dpAmount', '=', 0)
                            ->orWhere('localAmount', '=', 0)
                            ->orWhere('comRptAmount', '=', 0)
                            ->orWhereNull('dpAmount')
                            ->orWhereNull('localAmount')
                            ->orWhereNull('comRptAmount');
                    })->count();

                if ($checkQuantity > 0) {
                    return [
                        'status' => false,
                        'message' => 'Amount should be have value',
                        'code' => 500
                    ];
                }

                $pvBankChargeDetail = PaymentVoucherBankChargeDetails::where('payMasterAutoID', $input['PayMasterAutoId'])->get();

                $finalError = array(
                    'amount_zero' => array(),
                    'amount_neg' => array(),
                    'required_serviceLine' => array(),
                    'active_serviceLine' => array()
                );

                $error_count = 0;

                foreach ($pvBankChargeDetail as $item) {

                    $updateItem = PaymentVoucherBankChargeDetails::find($item['id']);

                    if ($updateItem->serviceLineSystemID && !is_null($updateItem->serviceLineSystemID)) {

                        $checkDepartmentActive = SegmentMaster::where('serviceLineSystemID', $updateItem->serviceLineSystemID)
                            ->where('isActive', 1)
                            ->first();
                        if (empty($checkDepartmentActive)) {
                            $updateItem->serviceLineSystemID = null;
                            $updateItem->serviceLineCode = null;
                            array_push($finalError['active_serviceLine'], $updateItem->glCode);
                            $error_count++;
                        }
                    } else {
                        array_push($finalError['required_serviceLine'], $updateItem->glCode);
                        $error_count++;
                    }

                    $updateItem->save();
                }

                $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
                if ($error_count > 0) {
                    return [
                        'status' => false,
                        'message' => 'You cannot confirm this document.',
                        'code' => 500,
                        'type' => $confirm_error
                    ];
                }
            }

            if(($input['isSupplierBlocked']) && ($paySupplierInvoiceMaster->invoiceType == 2))
            {

                $validatorResult = \Helper::checkBlockSuppliers($input['BPVdate'],$supplier_id);
                if (!$validatorResult['success']) {
                    return [
                        'status' => false,
                        'message' => 'The selected supplier has been blocked. Are you sure you want to proceed ?',
                        'code' => 500,
                        'type' => ['type' => 'blockSupplier']
                    ];
                }
            }

            if ($input['pdcChequeYN']) {


                $pdcLogValidation = PdcLog::where('documentSystemID', $paySupplierInvoiceMaster->documentSystemID)
                    ->where('documentmasterAutoID', $id)
                    ->whereNull('chequeDate')
                    ->first();

                if ($pdcLogValidation) {
                    return [
                        'status' => false,
                        'message' => 'PDC Cheque date cannot be empty',
                        'code' => 500,
                    ];
                }


                $totalAmountForPDC = 0;
                if ($paySupplierInvoiceMaster->invoiceType == 2 || $paySupplierInvoiceMaster->invoiceType == 6) {
                    $totalAmountForPDCData = PaySupplierInvoiceDetail::where('PayMasterAutoId', $id)
                        ->selectRaw('SUM(supplierPaymentAmount + retentionVatAmount) as total')
                        ->first();

                    $totalAmountForPDC = $totalAmountForPDCData ? $totalAmountForPDCData->total : 0;

                } else if ($paySupplierInvoiceMaster->invoiceType == 5 || $paySupplierInvoiceMaster->invoiceType == 7) {
                    $totalAmountForPDC = AdvancePaymentDetails::where('PayMasterAutoId', $id)
                        ->sum('paymentAmount');

                } else if ($paySupplierInvoiceMaster->invoiceType == 3) {
                    $totalAmountForPDCData = DirectPaymentDetails::where('directPaymentAutoID', $id)
                        ->selectRaw('SUM(DPAmount + vatAmount) as total')
                        ->first();

                    $totalAmountForPDC = $totalAmountForPDCData ? $totalAmountForPDCData->total : 0;
                }

                $pdcLog = PdcLog::where('documentSystemID', $paySupplierInvoiceMaster->documentSystemID)
                    ->where('documentmasterAutoID', $id)
                    ->get();

                if (count($pdcLog) == 0) {
                    return [
                        'status' => false,
                        'message' => 'PDC Cheques not created, Please create atleast one cheque',
                        'code' => 500,
                    ];
                }

                $pdcLogAmount = PdcLog::where('documentSystemID', $paySupplierInvoiceMaster->documentSystemID)
                    ->where('documentmasterAutoID', $id)
                    ->sum('amount');

                $checkingAmount = round($totalAmountForPDC, 3) - round($pdcLogAmount, 3);

                if ($checkingAmount > 0.001 || $checkingAmount < 0) {
                    return [
                        'status' => false,
                        'message' => 'PDC Cheque amount should equal to PV total amount',
                        'code' => 500,
                    ];
                }

                $checkPlAccount = SystemGlCodeScenarioDetail::getGlByScenario($companySystemID, $paySupplierInvoiceMaster->documentSystemID, "pdc-payable-account");

                if (is_null($checkPlAccount)) {
                    return [
                        'status' => false,
                        'message' => 'Please configure PDC Payable account for payment voucher',
                        'code' => 500,
                    ];
                }
            }

            if ($input['invoiceType'] == 2 || $input['invoiceType'] == 6) {
                $bankCharge = PaymentVoucherBankChargeDetails::selectRaw("SUM(dpAmount) as dpAmount, SUM(localAmount) as localAmount,SUM(comRptAmount) as comRptAmount")->WHERE('payMasterAutoID', $paySupplierInvoiceMaster->PayMasterAutoId)->first();
                $si = PaySupplierInvoiceDetail::selectRaw("SUM(paymentLocalAmount) as localAmount, SUM(paymentComRptAmount) as rptAmount,SUM(supplierPaymentAmount) as transAmount,localCurrencyID,comRptCurrencyID as reportingCurrencyID,supplierPaymentCurrencyID as transCurrencyID,comRptER as reportingCurrencyER,localER as localCurrencyER,supplierPaymentER as transCurrencyER")->WHERE('PayMasterAutoId', $paySupplierInvoiceMaster->PayMasterAutoId)->WHERE('matchingDocID', 0)->first();

                $masterTransAmountTotal = $si->transAmount + $bankCharge->dpAmount;
                $masterLocalAmountTotal = $si->localAmount + $bankCharge->localAmount;
                $masterRptAmountTotal = $si->rptAmount + $bankCharge->comRptAmount;

                $convertAmount = \Helper::convertAmountToLocalRpt(203, $paySupplierInvoiceMaster->PayMasterAutoId, $masterTransAmountTotal);

                $transAmountTotal = $masterTransAmountTotal;
                $localAmountTotal = $convertAmount["localAmount"];
                $rptAmountTotal = $convertAmount["reportingAmount"];

                $diffTrans = $transAmountTotal - $masterTransAmountTotal;
                $diffLocal = $localAmountTotal - $masterLocalAmountTotal;
                $diffRpt = $rptAmountTotal - $masterRptAmountTotal;


                $masterData = PaySupplierInvoiceMaster::with(['localcurrency', 'rptcurrency'])->find($paySupplierInvoiceMaster->PayMasterAutoId);

                if (ABS(round($diffTrans)) != 0 || ABS(round($diffLocal, $masterData->localcurrency->DecimalPlaces)) != 0 || ABS(round($diffRpt, $masterData->rptcurrency->DecimalPlaces)) != 0) {

                    $checkExchangeGainLossAccount = SystemGlCodeScenarioDetail::getGlByScenario($companySystemID, $documentSystemID, "exchange-gainloss-gl");
                    if (is_null($checkExchangeGainLossAccount)) {
                        $checkExchangeGainLossAccountCode = SystemGlCodeScenarioDetail::getGlCodeByScenario($companySystemID, $documentSystemID, "exchange-gainloss-gl");

                        if ($checkExchangeGainLossAccountCode) {
                            return [
                                'status' => false,
                                'message' => 'Please assign Exchange Gain/Loss account for this company',
                                'code' => 500,
                            ];
                        }
                        return [
                            'status' => false,
                            'message' => 'Please configure Exchange Gain/Loss account for this company',
                            'code' => 500,
                        ];
                    }
                }
            }



            $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
            if (!$companyFinanceYear["success"]) {
                return [
                    'status' => false,
                    'message' => $companyFinanceYear["message"],
                    'code' => 500,
                    'type' => ['type' => 'confirm']
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
                    'code' => 500,
                    'type' => ['type' => 'confirm']
                ];
            } else {
                $input['FYPeriodDateFrom'] = $companyFinancePeriod["message"]->dateFrom;
                $input['FYPeriodDateTo'] = $companyFinancePeriod["message"]->dateTo;
            }

            unset($inputParam);
            $validator = \Validator::make($input, [
                'companyFinancePeriodID' => 'required|numeric|min:1',
                'companyFinanceYearID' => 'required|numeric|min:1',
                'BPVdate' => 'required|date',
                'BPVchequeDate' => 'required|date',
                'invoiceType' => 'required|numeric|min:1',
                'paymentMode' => 'required',
                'BPVbank' => 'required|numeric|min:1',
                'BPVAccount' => 'required|numeric|min:1',
                'supplierTransCurrencyID' => 'required|numeric|min:1',
                'BPVNarration' => 'required'
            ]);
            if ($validator->fails()) {
                return [
                    'status' => false,
                    'message' => $validator->messages(),
                    'code' => 422,
                    'type' => ['type' => 'confirm']
                ];
            }

            if(isset($input['payeeType'])){
                if($input['payeeType'] == 1 && $input['invoiceType'] != 6 && $input['invoiceType'] != 7){
                    $validator = \Validator::make($input, [
                        'BPVsupplierID' => 'required|numeric|min:1'
                    ]);
                }else if($input['payeeType'] == 2){
                    $validator = \Validator::make($input, [
                        'directPaymentPayeeEmpID' => 'required|numeric|min:1'
                    ]);
                }else if($input['payeeType'] == 3){
                    $validator = \Validator::make($input, [
                        'directPaymentPayee' => 'required'
                    ]);
                }
            }

            if ($input['invoiceType'] == 6 || $input['invoiceType'] == 7) {
                $validator = \Validator::make($input, [
                    'directPaymentPayeeEmpID' => 'required|numeric|min:1'
                ]);
            }

            if ($validator->fails()) {
                return [
                    'status' => false,
                    'message' => $validator->messages(),
                    'code' => 422,
                    'type' => ['type' => 'confirm']
                ];
            }

            $monthBegin = $input['FYPeriodDateFrom'];
            $monthEnd = $input['FYPeriodDateTo'];

            if (($input['BPVdate'] >= $monthBegin) && ($input['BPVdate'] <= $monthEnd)) {
            } else {
                return [
                    'status' => false,
                    'message' => 'Payment voucher date is not within financial period!',
                    'code' => 500,
                    'type' => ['type' => 'confirm']
                ];
            }

            $bank = BankAccount::find($input['BPVAccount']);
            if (empty($bank)) {
                return [
                    'status' => false,
                    'message' => 'Bank account not found',
                    'code' => 500,
                    'type' => ['type' => 'confirm']
                ];
            }

            if (!$bank->chartOfAccountSystemID) {
                return [
                    'status' => false,
                    'message' => 'Bank account is not linked to gl account',
                    'code' => 500,
                    'type' => ['type' => 'confirm']
                ];
            }


            $overPaymentErrorMessage = [];
            // po payment
            if ($paySupplierInvoiceMaster->invoiceType == 2 || $paySupplierInvoiceMaster->invoiceType == 6) {
                $pvDetailExist = PaySupplierInvoiceDetail::select(DB::raw('PayMasterAutoId'))
                    ->where('PayMasterAutoId', $id)
                    ->first();

                if (empty($pvDetailExist)) {
                    return [
                        'status' => false,
                        'message' => 'PV document cannot confirm without details',
                        'code' => 500,
                        'type' => ['type' => 'confirm']
                    ];
                }

                $checkAmountGreater = PaySupplierInvoiceDetail::selectRaw('SUM(supplierPaymentAmount) as supplierPaymentAmount')
                    ->where('PayMasterAutoId', $id)
                    ->first();

                if (round($checkAmountGreater['supplierPaymentAmount'], 3) < 0) {
                    return [
                        'status' => false,
                        'message' => 'Total Amount should be equal or greater than zero',
                        'code' => 500,
                        'type' => ['type' => 'confirm']
                    ];
                }

                $checkAmount = PaySupplierInvoiceDetail::where('PayMasterAutoId', $id)
                    ->where('supplierPaymentAmount', 0)
                    ->count();

                if ($checkAmount > 0) {
                    return [
                        'status' => false,
                        'message' => 'Every item should have a payment amount',
                        'code' => 500,
                        'type' => ['type' => 'confirm']
                    ];
                }


                $finalError = array(
                    'more_booked' => array(),
                );

                $error_count = 0;

                $pvDetailExist = PaySupplierInvoiceDetail::where('PayMasterAutoId', $id)
                    ->get();

                foreach ($pvDetailExist as $val) {
                    $payDetailMoreBooked = PaySupplierInvoiceDetail::selectRaw('IFNULL(SUM(IFNULL(supplierPaymentAmount,0)),0) as supplierPaymentAmount')
                        ->when(($paySupplierInvoiceMaster->invoiceType == 6 || $paySupplierInvoiceMaster->invoiceType == 7), function($query) {
                            $query->whereHas('payment_master', function($query) {
                                $query->whereIn('invoiceType',[6,7]);
                            });
                        })
                        ->when(($paySupplierInvoiceMaster->invoiceType != 6 && $paySupplierInvoiceMaster->invoiceType != 7), function($query) {
                            $query->whereHas('payment_master', function($query) {
                                $query->where(function($query) {
                                    $query->where('invoiceType', '!=', 6)
                                        ->where('invoiceType', '!=', 7);
                                });
                            });
                        })
                        ->where('apAutoID', $val->apAutoID)
                        ->where('matchingDocID', 0)
                        ->first();

                    $a = ($val->addedDocumentSystemID == 11) ? $payDetailMoreBooked->supplierPaymentAmount : abs($payDetailMoreBooked->supplierPaymentAmount);
                    $b = ($val->addedDocumentSystemID == 11) ? $val->supplierInvoiceAmount : abs($val->supplierInvoiceAmount);
                    $epsilon = 0.0001;
                    //supplier invoice
                    if (($a-$b) > $epsilon) {
                        array_push($finalError['more_booked'], $val->addedDocumentID . ' | ' . $val->bookingInvDocCode);
                        $error_count++;
                    }


                }


                $poIds = array_unique(collect($pvDetailExist)->pluck('purchaseOrderID')->toArray());

                $repository = app(\App\Repositories\PaySupplierInvoiceMasterRepository::class);

                foreach ($poIds as $keyPO => $valuePO) {
                    if (!is_null($valuePO)) {
                        $resValidate = $repository->validatePoPayment($valuePO, $id);

                        if (!$resValidate['status']) {
                            $overPaymentErrorMessage[] = $resValidate['message'];
                        }
                    }
                }


                $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
                if ($error_count > 0) {
                    return [
                        'status' => false,
                        'message' => 'You cannot confirm this document.',
                        'code' => 500,
                        'type' => $confirm_error
                    ];
                }

                foreach ($pvDetailExist as $val) {
                    if ($paySupplierInvoiceMaster->invoiceType == 6) {
                        $updatePayment = EmployeeLedger::find($val->apAutoID);
                    } else {
                        $updatePayment = AccountsPayableLedger::find($val->apAutoID);
                    }
                    if ($updatePayment) {

                        $supplierPaidAmountSum = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
                            ->when(($paySupplierInvoiceMaster->invoiceType == 6 || $paySupplierInvoiceMaster->invoiceType == 7), function($query) {
                                $query->whereHas('payment_master', function($query) {
                                    $query->whereIn('invoiceType',[6,7]);
                                });
                            })
                            ->when(($paySupplierInvoiceMaster->invoiceType != 6 && $paySupplierInvoiceMaster->invoiceType != 7), function($query) {
                                $query->whereHas('payment_master', function($query) {
                                    $query->where(function($query) {
                                        $query->where('invoiceType', '!=', 6)
                                            ->where('invoiceType', '!=', 7);
                                    });
                                });
                            })
                            ->where('apAutoID', $val->apAutoID)
                            ->groupBy('erp_paysupplierinvoicedetail.apAutoID')->first();

                        $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')->where('PayMasterAutoId', $val->bookingInvSystemCode)->where('documentSystemID', $val->addedDocumentSystemID)->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')->first();

                        $machAmount = 0;
                        if ($matchedAmount) {
                            $machAmount = $matchedAmount["SumOfmatchedAmount"];
                        }

                        $totalPaidAmount = ($supplierPaidAmountSum["SumOfsupplierPaymentAmount"] + ($machAmount * -1));

                        if ($val->addedDocumentSystemID == 11) {
                            if ($totalPaidAmount == 0) {
                                $updatePayment->selectedToPaymentInv = 0;
                                $updatePayment->fullyInvoice = 0;
                                $updatePayment->save();
                            } else if ($val->supplierInvoiceAmount == $totalPaidAmount || $totalPaidAmount > $val->supplierInvoiceAmount) {
                                $updatePayment->selectedToPaymentInv = -1;
                                $updatePayment->fullyInvoice = 2;
                                $updatePayment->save();
                            } else if (($val->supplierInvoiceAmount > $totalPaidAmount) && ($totalPaidAmount > 0)) {
                                $updatePayment->selectedToPaymentInv = 0;
                                $updatePayment->fullyInvoice = 1;
                                $updatePayment->save();
                            }
                        } else if ($val->addedDocumentSystemID == 15 || $val->addedDocumentSystemID == 24) {

                            if ($totalPaidAmount == 0) {
                                $updatePayment->selectedToPaymentInv = 0;
                                $updatePayment->fullyInvoice = 0;
                                $updatePayment->save();
                            } else if ($val->supplierInvoiceAmount == $totalPaidAmount) {
                                $updatePayment->selectedToPaymentInv = -1;
                                $updatePayment->fullyInvoice = 2;
                                $updatePayment->save();
                            } else if ($val->supplierInvoiceAmount < $totalPaidAmount) {
                                $updatePayment->selectedToPaymentInv = 0;
                                $updatePayment->fullyInvoice = 1;
                                $updatePayment->save();
                            } else if ($val->supplierInvoiceAmount > $totalPaidAmount) {
                                $updatePayment->selectedToPaymentInv = -1;
                                $updatePayment->fullyInvoice = 2;
                                $updatePayment->save();
                            }
                        }
                    }
                }
            }

            // Advance payment
            if ($paySupplierInvoiceMaster->invoiceType == 5 || $paySupplierInvoiceMaster->invoiceType == 7) {
                $pvDetailExist = AdvancePaymentDetails::select(DB::raw('PayMasterAutoId'))
                    ->where('PayMasterAutoId', $id)
                    ->first();

                if (empty($pvDetailExist)) {
                    return [
                        'status' => false,
                        'message' => 'PV document cannot confirm without details',
                        'code' => 500,
                        'type' => ['type' => 'confirm']
                    ];
                }

                $checkAmountGreater = AdvancePaymentDetails::selectRaw('PayMasterAutoId,SUM(paymentAmount) as supplierPaymentAmount')
                    ->where('PayMasterAutoId', $id)
                    ->first();

                if (round($checkAmountGreater['paymentAmount'], 3) < 0) {
                    return [
                        'status' => false,
                        'message' => 'Total Amount should be equal or greater than zero',
                        'code' => 500,
                        'type' => ['type' => 'confirm']
                    ];
                }

                $checkAmount = AdvancePaymentDetails::where('PayMasterAutoId', $id)
                    ->where('paymentAmount', '<=', 0)
                    ->count();

                if ($checkAmount > 0) {
                    return [
                        'status' => false,
                        'message' => 'Every item should have a payment amount',
                        'code' => 500,
                        'type' => ['type' => 'confirm']
                    ];
                }


                $checkAdvVATAmount = AdvancePaymentDetails::where('PayMasterAutoId', $id)
                    ->sum('VATAmount');

                if ($paySupplierInvoiceMaster->invoiceType == 5 && $paySupplierInvoiceMaster->applyVAT == 1 && $checkAdvVATAmount > 0) {
                    if(empty(TaxService::getInputVATTransferGLAccount($paySupplierInvoiceMaster->companySystemID))){
                        return [
                            'status' => false,
                            'message' => 'Cannot confirm. Input VAT Transfer GL Account not configured.',
                            'code' => 500,
                        ];
                    }

                    $inputVATTransferGL = TaxService::getInputVATTransferGLAccount($paySupplierInvoiceMaster->companySystemID);

                    $checkAssignedStatusInputTrans = ChartOfAccountsAssigned::checkCOAAssignedStatus($inputVATTransferGL->inputVatTransferGLAccountAutoID, $paySupplierInvoiceMaster->companySystemID);

                    if (!$checkAssignedStatusInputTrans) {
                        return [
                            'status' => false,
                            'message' => 'Cannot confirm. Input VAT Transfer GL Account not assigned to company.',
                            'code' => 500,
                        ];
                    }

                    if(empty(TaxService::getInputVATGLAccount($paySupplierInvoiceMaster->companySystemID))){
                        return [
                            'status' => false,
                            'message' => 'Cannot confirm. Input VAT GL Account not configured.',
                            'code' => 500,
                        ];
                    }

                    $inputVATGL = TaxService::getInputVATGLAccount($paySupplierInvoiceMaster->companySystemID);

                    $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($inputVATGL->inputVatTransferGLAccountAutoID, $paySupplierInvoiceMaster->companySystemID);

                    if (!$checkAssignedStatus) {
                        return [
                            'status' => false,
                            'message' => 'Cannot confirm. Input VAT GL Account not assigned to company.',
                            'code' => 500,
                        ];
                    }
                }

                $advancePaymentDetails = AdvancePaymentDetails::where('PayMasterAutoId', $id)->get();

                foreach ($advancePaymentDetails as $val) {
                    $advancePayment = PoAdvancePayment::find($val->poAdvPaymentID);

                    if(isset($advancePayment))
                    {
                        $advancePaymentDetailsSum = AdvancePaymentDetails::selectRaw('IFNULL( Sum( erp_advancepaymentdetails.paymentAmount ), 0 ) AS SumOfpaymentAmount ')
                            ->where('companySystemID', $advancePayment->companySystemID)
                            ->where('poAdvPaymentID', $advancePayment->poAdvPaymentID)
                            ->where('purchaseOrderID', $advancePayment->poID)
                            ->first();

                        if (($advancePayment->reqAmount == $advancePaymentDetailsSum->SumOfpaymentAmount) || $advancePayment->reqAmount < $advancePaymentDetailsSum->SumOfpaymentAmount) {
                            $advancePayment->selectedToPayment = -1;
                            $advancePayment->fullyPaid = 2;
                            $advancePayment->save();
                        } else {
                            $advancePayment->selectedToPayment = 0;
                            $advancePayment->fullyPaid = 1;
                            $advancePayment->save();
                        }

                    }

                    $repository = app(\App\Repositories\PaySupplierInvoiceMasterRepository::class);
                    $resValidate = $repository->validatePoPayment($val->purchaseOrderID, $id);

                    if (!$resValidate['status']) {
                        $overPaymentErrorMessage[] = $resValidate['message'];
                    }
                }

            }

            if (count($overPaymentErrorMessage) > 0) {
                $confirmErrorOverPay = array('type' => 'confirm_error_over_payment', 'data' => $overPaymentErrorMessage);
                return [
                    'status' => false,
                    'message' => 'You cannot confirm this document.',
                    'code' => 500,
                    'type' => $confirmErrorOverPay
                ];
            }

            // Direct payment
            if ($paySupplierInvoiceMaster->invoiceType == 3) {
                $pvDetailExist = DirectPaymentDetails::where('directPaymentAutoID', $id)->get();

                if (count($pvDetailExist) == 0) {
                    return [
                        'status' => false,
                        'message' => 'PV document cannot confirm without details',
                        'code' => 500,
                        'type' => ['type' => 'confirm']
                    ];
                }

                $finalError = array(
                    'required_serviceLine' => array(),
                    'active_serviceLine' => array(),
                    'bank_not_updated' => array(),
                    'bank_account_not_updated' => array(),
                    'bank_account_currency_not_updated' => array(),
                    'bank_account_currency_er_not_updated' => array(),
                    'bank_amount_not_updated' => array(),
                    'bank_account_gl__account_not_updated' => array(),
                    'bank_account_local_currency_not_updated' => array(),
                    'bank_account_local_currency_er_not_updated' => array(),
                    'bank_account_local_currency_amount_not_updated' => array(),
                    'bank_account_reporting_currency_not_updated' => array(),
                    'bank_account_reporting_currency_er_not_updated' => array(),
                    'bank_account_reporting_currency_amount_not_updated' => array(),
                    'inter_company_gl_code_not_created' => array(),
                    'from_comany_not_configured_in_to_company' => array(),
                    'monthly_deduction_not_updated' => [],
                );

                $error_count = 0;
                if($paySupplierInvoiceMaster->expenseClaimOrPettyCash != 15)
                {
                    DirectPaymentDetails::where('directPaymentAutoID', $id)->update(['bankCurrencyER' => $input['BPVbankCurrencyER']]);
                }
                $employeeInvoice = CompanyPolicyMaster::where('companyPolicyCategoryID', 68)
                    ->where('companySystemID', $paySupplierInvoiceMaster->companySystemID)
                    ->first();

                $employeeControlAccount = SystemGlCodeScenarioDetail::getGlByScenario($paySupplierInvoiceMaster->companySystemID, null, "employee-control-account");

                $companyData = Company::find($paySupplierInvoiceMaster->companySystemID);

                if ($employeeInvoice && $employeeInvoice->isYesNO == 1 && $companyData && $companyData->isHrmsIntergrated && ($employeeControlAccount > 0)) {
                    $employeeControlRelatedAc = DirectPaymentDetails::where('directPaymentAutoID', $id)
                        ->where('chartOfAccountSystemID', $employeeControlAccount)
                        ->get();


                    foreach ($employeeControlRelatedAc as $key => $value) {
                        $detailTotalOfLine = $value->DPAmount;

                        $allocatedSum = ExpenseEmployeeAllocation::where('documentDetailID', $value->directPaymentDetailsID)
                            ->where('documentSystemID', $paySupplierInvoiceMaster->documentSystemID)
                            ->sum('amount');

                        if ($allocatedSum != $detailTotalOfLine) {
                            return [
                                'status' => false,
                                'message' => "Please allocate the full amount of ".$value->glCode." - ".$value->glCodeDes,
                                'code' => 500,
                            ];
                        }
                    }
                }

                foreach ($pvDetailExist as $item) {
                    if ($item->serviceLineSystemID && !is_null($item->serviceLineSystemID)) {
                        $checkDepartmentActive = SegmentMaster::where('serviceLineSystemID', $item->serviceLineSystemID)
                            ->where('isActive', 1)
                            ->first();
                        if (empty($checkDepartmentActive)) {
                            $item->serviceLineSystemID = null;
                            $item->serviceLineCode = null;
                            array_push($finalError['active_serviceLine'], $item->glCode . ' | ' . $item->glCodeDes);
                            $error_count++;
                        }
                    } else {
                        array_push($finalError['required_serviceLine'], $item->glCode . ' | ' . $item->glCodeDes);
                        $error_count++;
                    }

                    if ($paySupplierInvoiceMaster->expenseClaimOrPettyCash == 6 || $paySupplierInvoiceMaster->expenseClaimOrPettyCash == 7) {

                        $toRelatedAccounts = ChartOfAccountsAssigned::whereHas('chartofaccount', function ($q) use ($paySupplierInvoiceMaster){
                            $q->where('isApproved', 1)
                                ->where('interCompanySystemID', $paySupplierInvoiceMaster->companySystemID);
                        })
                            ->where('isAssigned', -1)
                            ->where('companySystemID', $paySupplierInvoiceMaster->interCompanyToSystemID)
                            ->where('controllAccountYN', 0)
                            ->where('controlAccountsSystemID', '<>', 1)
                            ->where('isActive', 1)
                            ->first();

                        $fromCompanyData = Company::find($paySupplierInvoiceMaster->companySystemID);
                        $toCompanyData = Company::find($paySupplierInvoiceMaster->interCompanyToSystemID);

                        $fromCompanyName = isset($fromCompanyData->CompanyName) ? $fromCompanyData->CompanyName : "";
                        $toCompanyName = isset($toCompanyData->CompanyName) ? $toCompanyData->CompanyName : "";

                        if (!$toRelatedAccounts) {
                            array_push($finalError['from_comany_not_configured_in_to_company'], $fromCompanyName . ' to ' . $toCompanyName);
                            $error_count++;
                        }

                        if (!$item->toBankID) {
                            array_push($finalError['bank_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                            $error_count++;
                        }
                        if (!$item->toBankAccountID) {
                            array_push($finalError['bank_account_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                            $error_count++;
                        }
                        if (!$item->toBankCurrencyID) {
                            array_push($finalError['bank_account_currency_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                            $error_count++;
                        }
                        if (!$item->toBankCurrencyER) {
                            array_push($finalError['bank_account_currency_er_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                            $error_count++;
                        }
                        if (!$item->toBankAmount) {
                            array_push($finalError['bank_amount_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                            $error_count++;
                        }
                        if (!$item->toBankGlCodeSystemID) {
                            array_push($finalError['bank_account_gl__account_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                            $error_count++;
                        }
                        if (!$item->toCompanyLocalCurrencyID) {
                            array_push($finalError['bank_account_local_currency_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                            $error_count++;
                        }
                        if (!$item->toCompanyLocalCurrencyER) {
                            array_push($finalError['bank_account_local_currency_er_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                            $error_count++;
                        }
                        if (!$item->toCompanyLocalCurrencyAmount) {
                            array_push($finalError['bank_account_local_currency_amount_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                            $error_count++;
                        }
                        if (!$item->toCompanyRptCurrencyID) {
                            array_push($finalError['bank_account_reporting_currency_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                            $error_count++;
                        }
                        if (!$item->toCompanyRptCurrencyER) {
                            array_push($finalError['bank_account_reporting_currency_er_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                            $error_count++;
                        }
                        if (!$item->toCompanyRptCurrencyAmount) {
                            array_push($finalError['bank_account_reporting_currency_amount_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                            $error_count++;
                        }

                        $chartofAccount = ChartOfAccount::where('interCompanySystemID', $paySupplierInvoiceMaster->companySystemID)->get();
                        if (count($chartofAccount) == 0) {
                            array_push($finalError['inter_company_gl_code_not_created'], $item->glCode . ' | ' . $item->glCodeDes);
                        }

                    }

                    if($paySupplierInvoiceMaster->createMonthlyDeduction){
                        if (empty($item->deductionType)) {
                            $finalError['monthly_deduction_not_updated'][] = $item->glCode . ' | ' . $item->glCodeDes;
                            $error_count++;
                        }
                    }
                }
                $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
                if ($error_count > 0) {
                    return [
                        'status' => false,
                        'message' => 'You cannot confirm this document.',
                        'code' => 500,
                        'type' => $confirm_error
                    ];
                }


                $checkAmount = DirectPaymentDetails::where('directPaymentAutoID', $id)
                    ->where('DPAmount', '<=', 0)
                    ->count();

                if ($checkAmount > 0) {
                    return [
                        'status' => false,
                        'message' => 'Every item should have a payment amount',
                        'code' => 500,
                        'type' => ['type' => 'confirm']
                    ];
                }

                $tax = Taxdetail::selectRaw("SUM(localAmount) as localAmount, SUM(rptAmount) as rptAmount,SUM(amount) as transAmount,localCurrencyID,rptCurrencyID as reportingCurrencyID,currency as supplierTransactionCurrencyID,currencyER as supplierTransactionER,rptCurrencyER as companyReportingER,localCurrencyER,payeeSystemCode")
                    ->WHERE('documentSystemCode', $id)
                    ->WHERE('documentSystemID', $paySupplierInvoiceMaster->documentSystemID)
                    ->groupBy('documentSystemCode')
                    ->first();

                $isVATEligible = TaxService::checkCompanyVATEligible($paySupplierInvoiceMaster->companySystemID);

                if ($isVATEligible == 1) {
                    if($tax){
                        $taxInputVATControl = TaxService::getInputVATGLAccount($paySupplierInvoiceMaster->companySystemID);

                        if (!$taxInputVATControl) {
                            return [
                                'status' => false,
                                'message' => 'Input VAT GL Account is not configured for this company',
                                'code' => 500,
                                'type' => ['type' => 'confirm']
                            ];
                        }

                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxInputVATControl->inputVatGLAccountAutoID)
                            ->where('companySystemID', $paySupplierInvoiceMaster->companySystemID)
                            ->where('isAssigned', -1)
                            ->first();

                        if (!$chartOfAccountData) {
                            return [
                                'status' => false,
                                'message' => 'Input VAT GL Account is not assigned to this company',
                                'code' => 500,
                                'type' => ['type' => 'confirm']
                            ];
                        }

                        if($paySupplierInvoiceMaster->rcmActivated == 1) {
                            $taxOutputVATControl = TaxService::getOutputVATGLAccount($paySupplierInvoiceMaster->companySystemID);

                            if (!$taxOutputVATControl) {
                                return [
                                    'status' => false,
                                    'message' => 'Output VAT GL Account is not configured for this company',
                                    'code' => 500,
                                    'type' => ['type' => 'confirm']
                                ];
                            }

                            $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxOutputVATControl->outputVatGLAccountAutoID)
                                ->where('companySystemID', $paySupplierInvoiceMaster->companySystemID)
                                ->where('isAssigned', -1)
                                ->first();

                            if (!$chartOfAccountData) {
                                return [
                                    'status' => false,
                                    'message' => 'Output VAT GL Account is not assigned to this company',
                                    'code' => 500,
                                    'type' => ['type' => 'confirm']
                                ];
                            }
                        }
                    }
                }

            }

            $amountForApproval = 0;
            if ($paySupplierInvoiceMaster->invoiceType == 2 || $paySupplierInvoiceMaster->invoiceType == 6) {
                $bankCharge = PaymentVoucherBankChargeDetails::where('payMasterAutoID',$id)->selectRaw('SUM(localAmount) as total')->first();
                $totalAmountForApprovalData = PaySupplierInvoiceDetail::where('PayMasterAutoId', $id)
                    ->selectRaw('SUM(paymentLocalAmount) as total, SUM(retentionVatAmount) as retentionVatAmount, supplierTransCurrencyID, localCurrencyID')
                    ->first();

                if ($totalAmountForApprovalData) {
                    $currencyConversionRetAmount = \Helper::currencyConversion($paySupplierInvoiceMaster->companySystemID, $totalAmountForApprovalData->supplierTransCurrencyID, $totalAmountForApprovalData->supplierTransCurrencyID, $totalAmountForApprovalData->retentionVatAmount);

                    $retLocal = $currencyConversionRetAmount['localAmount'];


                    $amountForApproval = $totalAmountForApprovalData->total + $bankCharge->total + $retLocal;
                }


            } else if ($paySupplierInvoiceMaster->invoiceType == 5 || $paySupplierInvoiceMaster->invoiceType == 7) {

                $amountForApproval = AdvancePaymentDetails::where('PayMasterAutoId', $id)
                    ->sum('localAmount');

            } else if ($paySupplierInvoiceMaster->invoiceType == 3) {

                $totalAmountForApprovalData = DirectPaymentDetails::where('directPaymentAutoID', $id)
                    ->selectRaw('SUM(localAmount + VATAmountLocal) as total')
                    ->first();

                $amountForApproval = $totalAmountForApprovalData ? $totalAmountForApprovalData->total : 0;
            }
            if ($paySupplierInvoiceMaster->invoiceType == 3) {

                $object = new ChartOfAccountValidationService();
                $result = $object->checkChartOfAccountStatus($input["documentSystemID"], $id, $input["companySystemID"]);

                if (isset($result) && !empty($result["accountCodes"])) {
                    return [
                        'status' => false,
                        'message' => $result["errorMsg"],
                        'code' => 500,
                        'type' => ['type' => 'confirm']
                    ];
                }
            }

            $params = array(
                'autoID' => $id,
                'company' => $companySystemID,
                'document' => $documentSystemID,
                'segment' => '',
                'category' => '',
                'amount' => $amountForApproval,
                'isAutoCreateDocument' => isset($input['isAutoCreateDocument'])
            );
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return [
                    'status' => false,
                    'message' => $confirm["message"],
                    'code' => 500,
                    'type' => ['type' => 'confirm']
                ];
            }

            $paySupplierInvoice = PaySupplierInvoiceMaster::find($id);

            if(!$changeChequeNoBaseOnPolicy) {
                if ($input['BPVbankCurrency'] == $input['localCurrencyID'] && $input['supplierTransCurrencyID'] == $input['localCurrencyID']) {
                    if ($input['chequePaymentYN'] == -1 &&  $input['pdcChequeYN'] == 0) {
                        $bankAccount = BankAccount::find($input['BPVAccount']);
                        /*
                         * check 'Get cheque number from cheque register' policy exist
                         * if policy exist - cheque no should get from erp_cheque register details - Get cheque number from cheque register
                         * else - usual method
                         *
                         * */
                        $is_exist_policy_GCNFCR = CompanyPolicyMaster::where('companySystemID', $companySystemID)
                            ->where('companyPolicyCategoryID', 35)
                            ->where('isYesNO', 1)
                            ->first();
                        if (!empty($is_exist_policy_GCNFCR)) {

                            $repository = app(\App\Repositories\PaySupplierInvoiceMasterRepository::class);
                            $usedCheckID = $repository->getLastUsedChequeID($companySystemID, $bankAccount->bankAccountAutoID);

                            $unUsedCheque = ChequeRegisterDetail::whereHas('master', function ($q) use ($companySystemID, $bankAccount) {
                                $q->where('bank_account_id', $bankAccount->bankAccountAutoID)
                                    ->where('company_id', $companySystemID)
                                    ->where('isActive', 1);
                            })
                                ->where('status', 0)
                                ->where(function ($q) use ($usedCheckID) {
                                    if ($usedCheckID) {
                                        $q->where('id', '>', $usedCheckID);
                                    }
                                })
                                ->orderBy('id', 'ASC')
                                ->first();

                            if (!empty($unUsedCheque)) {
                                $nextChequeNo = $unUsedCheque->cheque_no;
                                $input['BPVchequeNo'] = $nextChequeNo;
                                /*update cheque detail table */
                                $update_array = [
                                    'document_id' => $id,
                                    'document_master_id' => $documentSystemID,
                                    'status' => 1,
                                ];
                                ChequeRegisterDetail::where('id', $unUsedCheque->id)->update($update_array);

                            } else {
                                return [
                                    'status' => false,
                                    'message' => 'Could not found any unassigned cheques. Please add cheques to cheque registryy',
                                    'code' => 500,
                                    'type' => ['type' => 'confirm']
                                ];
                            }

                        } else {
                            $nextChequeNo = $bankAccount->chquePrintedStartingNo + 1;
                        }
                        /*code ended here*/

                        $checkChequeNoDuplicate = PaySupplierInvoiceMaster::where('companySystemID', $paySupplierInvoice->companySystemID)->where('BPVchequeNo', '>', 0)->where('BPVbank', $input['BPVbank'])->where('BPVAccount', $input['BPVAccount'])->where('BPVchequeNo', $nextChequeNo)->first();

                        if ($checkChequeNoDuplicate) {
                            //return $this->sendError('The cheque no ' . $nextChequeNo . ' is already taken in ' . $checkChequeNoDuplicate['BPVcode'] . ' Please check again.', 500, ['type' => 'confirm']);
                        }

                        if ($bankAccount->isPrintedActive == 1 && empty($is_exist_policy_GCNFCR)) {
                            $input['BPVchequeNo'] = $nextChequeNo;
                            $bankAccount->chquePrintedStartingNo = $nextChequeNo;
                            $bankAccount->save();

                            Log::info('Cheque No:' . $input['BPVchequeNo']);
                            Log::info('PV Code:' . $paySupplierInvoiceMaster->BPVcode);
                            Log::info('-------------------------------------------------------');
                        }
                    } else {
                        $chkCheque = PaySupplierInvoiceMaster::where('companySystemID', $paySupplierInvoice->companySystemID)->where('BPVchequeNo', '>', 0)->where('chequePaymentYN', 0)->where('confirmedYN', 1)->where('PayMasterAutoId', '<>', $paySupplierInvoice->PayMasterAutoId)->orderBY('BPVchequeNo', 'DESC')->first();
                        if ($chkCheque) {
                            $input['BPVchequeNo'] = $chkCheque->BPVchequeNo + 1;
                        } else {
                            $input['BPVchequeNo'] = 1;
                        }
                    }
                } else {
                    $chkCheque = PaySupplierInvoiceMaster::where('companySystemID', $paySupplierInvoice->companySystemID)->where('BPVchequeNo', '>', 0)->where('chequePaymentYN', 0)->where('confirmedYN', 1)->where('PayMasterAutoId', '<>', $paySupplierInvoice->PayMasterAutoId)->orderBY('BPVchequeNo', 'DESC')->first();
                    if ($chkCheque) {
                        $input['BPVchequeNo'] = $chkCheque->BPVchequeNo + 1;
                    } else {
                        $input['BPVchequeNo'] = 1;
                    }
                }
            }

            if (isset($input['pdcChequeYN']) && $input['pdcChequeYN']) {
                $input['chequePaymentYN'] = 0;
                $input['BPVchequeDate'] = null;
                $input['BPVchequeNo'] = null;
                $input['expenseClaimOrPettyCash'] = null;
            }
        }

        if ($paySupplierInvoiceMaster->invoiceType == 2 || $paySupplierInvoiceMaster->invoiceType == 6) {
            $bankChargeTotal = PaymentVoucherBankChargeDetails::selectRaw("SUM(dpAmount) as dpAmount, SUM(localAmount) as localAmount,SUM(comRptAmount) as comRptAmount")->WHERE('payMasterAutoID', $id)->first();

            $totalAmount = PaySupplierInvoiceDetail::selectRaw("SUM(supplierInvoiceAmount) as supplierInvoiceAmount,SUM(supplierDefaultAmount) as supplierDefaultAmount, SUM(retentionVatAmount) as retentionVatAmount, SUM(localAmount) as localAmount, SUM(comRptAmount) as comRptAmount, SUM(supplierPaymentAmount) as supplierPaymentAmount, SUM(paymentBalancedAmount) as paymentBalancedAmount, SUM(paymentSupplierDefaultAmount) as paymentSupplierDefaultAmount, SUM(paymentLocalAmount) as paymentLocalAmount, SUM(paymentComRptAmount) as paymentComRptAmount")
                ->where('PayMasterAutoId', $id)
                ->where('matchingDocID', 0)
                ->first();
            $supplierPaymentAmount = $totalAmount->supplierPaymentAmount + $bankChargeTotal->dpAmount;
            if (!empty($supplierPaymentAmount)) {
                if ($paySupplierInvoiceMaster->BPVbankCurrency == $paySupplierInvoiceMaster->supplierTransCurrencyID) {
                    $input['payAmountBank'] = \Helper::roundValue($supplierPaymentAmount);
                    $input['payAmountSuppTrans'] = \Helper::roundValue($supplierPaymentAmount);
                    $input['payAmountSuppDef'] = \Helper::roundValue($supplierPaymentAmount);
                    $input['payAmountCompLocal'] = \Helper::roundValue($totalAmount->paymentLocalAmount + $bankChargeTotal->localAmount);
                    $input['payAmountCompRpt'] = \Helper::roundValue($totalAmount->paymentComRptAmount + $bankChargeTotal->comRptAmount);
                    $input['suppAmountDocTotal'] = \Helper::roundValue($supplierPaymentAmount);
                    $input['retentionVatAmount'] = \Helper::roundValue($totalAmount->retentionVatAmount);
                } else {
                    $bankAmount = \Helper::convertAmountToLocalRpt(203, $id, $supplierPaymentAmount);
                    $input['payAmountBank'] = \Helper::roundValue($bankAmount["defaultAmount"]);
                    $input['payAmountSuppTrans'] = \Helper::roundValue($supplierPaymentAmount);
                    $input['payAmountSuppDef'] = \Helper::roundValue($supplierPaymentAmount);
                    $input['payAmountCompLocal'] = \Helper::roundValue($bankAmount["localAmount"]);
                    $input['payAmountCompRpt'] = \Helper::roundValue($bankAmount["reportingAmount"]);
                    $input['suppAmountDocTotal'] = \Helper::roundValue($supplierPaymentAmount);
                    $input['retentionVatAmount'] = \Helper::roundValue($totalAmount->retentionVatAmount);

                }
                $exchangeAmount =\Helper::convertAmountToLocalRpt(203, $id, $supplierPaymentAmount);
                $input['payAmountBank'] = $exchangeAmount["defaultAmount"];
                $input['payAmountCompLocal'] = \Helper::roundValue($exchangeAmount["localAmount"]);
                $input['payAmountCompRpt'] = \Helper::roundValue($exchangeAmount["reportingAmount"]);
            } else {
                $input['payAmountBank'] = 0;
                $input['payAmountSuppTrans'] = 0;
                $input['payAmountSuppDef'] = 0;
                $input['payAmountCompLocal'] = 0;
                $input['payAmountCompRpt'] = 0;
                $input['suppAmountDocTotal'] = 0;
                $input['retentionVatAmount'] = 0;
            }
        }

        if ($paySupplierInvoiceMaster->invoiceType == 5 || $paySupplierInvoiceMaster->invoiceType == 7) {


            if ($paySupplierInvoiceMaster->invoiceType == 5) {
                $supDetail = SupplierAssigned::where('supplierCodeSytem', $input['BPVsupplierID'])->where('companySystemID', $companySystemID)->first();

                if($supDetail)
                {
                    $input['AdvanceAccount'] = $supDetail->AdvanceAccount;
                    $input['advanceAccountSystemID'] = $supDetail->advanceAccountSystemID;
                }
            } else {
                $checkEmployeeControlAccount = SystemGlCodeScenarioDetail::getGlByScenario($input['companySystemID'], $input['documentSystemID'], "employee-control-account");

                if (is_null($checkEmployeeControlAccount)) {
                    return [
                        'status' => false,
                        'message' => 'Please configure Employee control account for this company',
                        'code' => 500,
                    ];
                }

                $input['AdvanceAccount'] = ChartOfAccount::getAccountCode($checkEmployeeControlAccount);
                $input['advanceAccountSystemID'] = $checkEmployeeControlAccount;
            }

            $totalAmount = AdvancePaymentDetails::selectRaw("SUM(paymentAmount) as paymentAmount,SUM(localAmount) as localAmount, SUM(comRptAmount) as comRptAmount, SUM(supplierDefaultAmount) as supplierDefaultAmount, SUM(supplierTransAmount) as supplierTransAmount")->where('PayMasterAutoId', $id)->first();

            if (!empty($totalAmount->supplierTransAmount)) {
                $bankAmount = \Helper::convertAmountToLocalRpt(203, $id, $totalAmount->supplierTransAmount);
                $input['payAmountBank'] = $bankAmount["defaultAmount"];
                $input['payAmountSuppTrans'] = \Helper::roundValue($totalAmount->supplierTransAmount);
                $input['payAmountSuppDef'] = \Helper::roundValue($totalAmount->supplierDefaultAmount);
                $input['payAmountCompLocal'] = \Helper::roundValue($bankAmount["localAmount"]);
                $input['payAmountCompRpt'] = \Helper::roundValue($bankAmount["reportingAmount"]);
                $input['suppAmountDocTotal'] = \Helper::roundValue($totalAmount->supplierTransAmount);
            } else {
                $input['payAmountBank'] = 0;
                $input['payAmountSuppTrans'] = 0;
                $input['payAmountSuppDef'] = 0;
                $input['payAmountCompLocal'] = 0;
                $input['payAmountCompRpt'] = 0;
                $input['suppAmountDocTotal'] = 0;
            }
        }

        if ($paySupplierInvoiceMaster->invoiceType == 3) {
            $totalAmount = DirectPaymentDetails::selectRaw("SUM(DPAmount) as paymentAmount,SUM(localAmount) as localAmount, SUM(comRptAmount) as comRptAmount")->where('directPaymentAutoID', $id)->first();

            if (!empty($totalAmount->paymentAmount)) {
                $bankAmount = \Helper::convertAmountToLocalRpt(203, $id, $totalAmount->paymentAmount);
                $input['payAmountBank'] = $bankAmount["defaultAmount"];
                $input['payAmountSuppTrans'] = \Helper::roundValue($totalAmount->paymentAmount);
                $input['payAmountSuppDef'] = \Helper::roundValue($totalAmount->paymentAmount);
                $input['payAmountCompLocal'] = \Helper::roundValue($bankAmount["localAmount"]);
                $input['payAmountCompRpt'] = \Helper::roundValue($bankAmount["reportingAmount"]);
                $input['suppAmountDocTotal'] = \Helper::roundValue($totalAmount->paymentAmount);
            } else {
                $input['payAmountBank'] = 0;
                $input['payAmountSuppTrans'] = 0;
                $input['payAmountSuppDef'] = 0;
                $input['payAmountCompLocal'] = 0;
                $input['payAmountCompRpt'] = 0;
                $input['suppAmountDocTotal'] = 0;
            }
        }

        $input['createMonthlyDeduction'] = ($input['createMonthlyDeduction'] == 1)? 1: 0;
        $input['modifiedPc'] = gethostname();

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            $employee = UserTypeService::getSystemEmployee();
            $input['modifiedUser'] = $employee->empID;
            $input['modifiedUserSystemID'] = $employee->employeeSystemID;
        }
        else{
            $input['modifiedUser'] = \Helper::getEmployeeID();
            $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
        }

        Log::info('Cheque No:' . $input['BPVchequeNo']);
        Log::info('PV Code:' . $paySupplierInvoiceMaster->BPVcode);
        Log::info('beforeUpdate______________________________________________________');


        if(isset($input['BPVAccount']))
        {
            if(!empty($input['BPVAccount']) )
            {
                $bank_currency = $input['BPVbankCurrency'];
                $document_currency = $input['supplierTransCurrencyID'];

                $cur_det['companySystemID'] = $input['companySystemID'];
                $cur_det['bankmasterAutoID'] = $input['BPVbank'];
                $cur_det['bankAccountAutoID'] = $input['BPVAccount'];
                $cur_det_info =  (object)$cur_det;

                $bankBalance = app('App\Http\Controllers\API\BankAccountAPIController')->getBankAccountBalanceSummery($cur_det_info);

                $amount = $bankBalance['netBankBalance'];
                $currencies = CurrencyMaster::where('currencyID','=',$document_currency)->select('DecimalPlaces')->first();

                $rounded_amount =  number_format($amount,$currencies->DecimalPlaces,'.', '');


                $input['bankAccountBalance'] = $rounded_amount;

            }
        }

        $input['payment_mode'] = $input['paymentMode'];
        unset(
            $input['paymentMode'],
            $input["confirmedYN"],
            $input["confirmedByName"],
            $input["confirmedByEmpID"],
            $input["confirmedByEmpSystemID"],
            $input["confirmedDate"]
        );

        $paySupplierInvoiceMaster = PaySupplierInvoiceMaster::find($id);
        $paySupplierInvoiceMaster->update($input);

        Log::info('Cheque No:' . $input['BPVchequeNo']);
        Log::info('PV Code:' . $paySupplierInvoiceMaster->BPVcode);
        Log::info($paySupplierInvoiceMaster);
        Log::info('afterUpdate______________________________________________________');

        if ($input['payeeType'] == 1) {
            $bankMemoSupplier = BankMemoPayee::where('documentSystemCode', $id)->delete();
        }

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            return [
                'status' => true,
                'data' => $paySupplierInvoiceMaster->refresh()->toArray(),
                'message' => 'PaySupplierInvoiceMaster updated successfully'
            ];
        }
        else{
            $message = [
                ['status' => 'success', 'message' => 'PaySupplierInvoiceMaster updated successfully'],
                ['status' => 'warning', 'message' => $warningMessage]
            ];

            return [
                'status' => true,
                'data' => $paySupplierInvoiceMaster->refresh()->toArray(),
                'message' => $message,
                'additionalData' => $confirm['data'] ?? null
            ];
        }
    }

    public static function storeDirectPaymentDetails($input): array {
        $payMaster = PaySupplierInvoiceMaster::find($input['directPaymentAutoID']);

        if (empty($payMaster)) {
            return [
                'status' => false,
                'message' => 'Payment voucher not found'
            ];
        }

        if($payMaster->confirmedYN){
            return [
                'status' => false,
                'message' => 'You cannot add Direct Payment Detail, this document already confirmed',
                'code' => 500
            ];
        }


        $bankMaster = BankAssign::ofCompany($payMaster->companySystemID)->isActive()->where('bankmasterAutoID', $payMaster->BPVbank)->first();

        if (empty($bankMaster)) {
            return [
                'status' => false,
                'message' => 'Selected Bank is not active'
            ];
        }

        $bankAccount = BankAccount::isActive()->find($payMaster->BPVAccount);

        if (empty($bankAccount)) {
            return [
                'status' => false,
                'message' => 'Selected Bank Account is not active'
            ];
        }

        $chartOfAccount = ChartOfAccount::find($input['chartOfAccountSystemID']);
        if (empty($chartOfAccount)) {
            return [
                'status' => false,
                'message' => 'Chart of Account not found'
            ];
        }

        if ($chartOfAccount->controlAccountsSystemID == 1) {
            return [
                'status' => false,
                'message' => 'Cannot add a revenue GL code'
            ];
        }

        $company = Company::find($input['companySystemID']);
        if (empty($company)) {
            return [
                'status' => false,
                'message' => 'Company not found'
            ];
        }

        if ($bankAccount->chartOfAccountSystemID == $input['chartOfAccountSystemID']) {
            return [
                'status' => false,
                'message' => 'You are trying to select the same bank account'
            ];
        }

        if ($payMaster->expenseClaimOrPettyCash == 6 || $payMaster->expenseClaimOrPettyCash == 7) {

            if(empty($payMaster->interCompanyToSystemID)){
                return [
                    'status' => false,
                    'message' => 'Please select a company to'
                ];
            }

            $directPaymentDetails = DirectPaymentDetails::where(['directPaymentAutoID' => $input['directPaymentAutoID'], 'relatedPartyYN' => 1])->get();
            if (count($directPaymentDetails) > 0) {
                return [
                    'status' => false,
                    'message' => 'Cannot add GL code as there is a related party GL code added.'
                ];
            }

            $directPaymentDetails = DirectPaymentDetails::where(['directPaymentAutoID' => $input['directPaymentAutoID'], 'relatedPartyYN' => 0])->get();
            if (count($directPaymentDetails) > 0) {
                if ($chartOfAccount->relatedPartyYN) {
                    return [
                        'status' => false,
                        'message' => 'Cannot add related party GL code as there is a GL code added.'
                    ];
                }
            }

        }

        $directPaymentDetails = DirectPaymentDetails::where(['directPaymentAutoID' => $input['directPaymentAutoID'], 'glCodeIsBank' => 1])->get();
        if (count($directPaymentDetails) > 0) {
            return [
                'status' => false,
                'message' => 'Cannot add GL code as there is a bank GL code added.'
            ];
        }

        $directPaymentDetails = DirectPaymentDetails::where(['directPaymentAutoID' => $input['directPaymentAutoID'], 'glCodeIsBank' => 0])->get();

        if (count($directPaymentDetails) > 0) {
            if ($chartOfAccount->isBank) {
                return [
                    'status' => false,
                    'message' => 'Cannot add bank account GL code as there is a GL code added.'
                ];
            }
        }

        $input['companyID'] = $company->CompanyID;

        $input['glCode'] = $chartOfAccount->AccountCode;
        $input['glCodeDes'] = $chartOfAccount->AccountDescription;
        $input['glCodeIsBank'] = $chartOfAccount->isBank;
        $input['relatedPartyYN'] = $chartOfAccount->relatedPartyYN;

        $input['supplierTransCurrencyID'] = $payMaster->supplierTransCurrencyID;
        $input['supplierTransER'] = 1;
        $input['DPAmountCurrency'] = $payMaster->supplierTransCurrencyID;
        $input['DPAmountCurrencyER'] = 1;
        $input['localCurrency'] = $payMaster->localCurrencyID;
        $input['localCurrencyER'] = $payMaster->localCurrencyER;
        $input['comRptCurrency'] = $payMaster->companyRptCurrencyID;
        $input['comRptCurrencyER'] = $payMaster->companyRptCurrencyER;

        if ($chartOfAccount->isBank) {
            $account = BankAccount::where('chartOfAccountSystemID', $input['chartOfAccountSystemID'])->where('companySystemID', $input['companySystemID'])->first();
            if($account) {
                $input['bankCurrencyID'] = $account->accountCurrencyID;
                $conversionAmount = \Helper::currencyConversion($input['companySystemID'], $bankAccount->accountCurrencyID, $account->accountCurrencyID, 0);
                $input['bankCurrencyER'] = $conversionAmount["transToDocER"];
            }else{
                return [
                    'status' => false,
                    'message' => 'No bank account found for the selected GL code.'
                ];
            }
        } else {
            $input['bankCurrencyID'] = $payMaster->BPVbankCurrency;
            $input['bankCurrencyER'] = $payMaster->BPVbankCurrencyER;
        }

        if ($payMaster->projectID) {
            $input['detail_project_id'] = $payMaster->projectID;
        }

        if($payMaster->directPaymentPayeeEmpID > 0 && $payMaster->directPaymentPayeeSelectEmp == -1){
            $employeeSegment = SrpEmployeeDetails::where('EIdNo',$payMaster->directPaymentPayeeEmpID)->first();
            if($employeeSegment && $employeeSegment->segmentID > 0){
                $segment = SegmentMaster::where('serviceLineSystemID',$employeeSegment->segmentID)->where('isActive',1)->first();
                if($segment){
                    $input['serviceLineSystemID'] = $segment->serviceLineSystemID;
                    $input['serviceLineCode'] = $segment->ServiceLineCode;
                }
            }
        }

        if ($payMaster->BPVsupplierID) {
            $input['supplierTransCurrencyID'] = $payMaster->supplierTransCurrencyID;
            $input['supplierTransER'] = $payMaster->supplierTransCurrencyER;
        }

        if ($payMaster->FYBiggin) {
            $finYearExp = explode('-', $payMaster->FYBiggin);
            $input['budgetYear'] = $finYearExp[0];
        } else {
            $input['budgetYear'] = CompanyFinanceYear::budgetYearByDate(now(), $input['companySystemID']);
        }

        $isVATEligible = TaxService::checkCompanyVATEligible($payMaster->companySystemID);

        if ($isVATEligible) {
            $defaultVAT = TaxService::getDefaultVAT($payMaster->companySystemID, $payMaster->BPVsupplierID);
            $input['vatSubCategoryID'] = $defaultVAT['vatSubCategoryID'];
            if (isset($input['VATPercentage']) && $input['VATPercentage'] == 0) {
                $input['VATPercentage'] = $defaultVAT['percentage'];
            }
            $input['vatMasterCategoryID'] = $defaultVAT['vatMasterCategoryID'];
        }

        $directPaymentDetails = DirectPaymentDetails::create($input);

        if (isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']) {
            $inputData = $directPaymentDetails->refresh()->toArray();
            $returnData = self::updateDirectPaymentDetails($inputData['directPaymentDetailsID'],$inputData);

            if($returnData['status']){
                return [
                    'status' => true,
                    'data' => $returnData['data'],
                    'message' => $returnData['message']
                ];
            }
            else{
                return [
                    'status' => false,
                    'message' => $returnData['message']
                ];
            }
        }
        else{
            return [
                'status' => true,
                'data' => $directPaymentDetails->refresh()->toArray(),
                'message' => 'Direct Payment Details saved successfully'
            ];
        }
    }

    public static function updateDirectPaymentDetails($id, $input): array {
        $input = array_except($input, ['segment', 'chartofaccount','to_bank']);
        $serviceLineError = array('type' => 'serviceLine');

        $directPaymentDetails = DirectPaymentDetails::find($id);

        if (empty($directPaymentDetails)) {
            return [
                'status' => false,
                'message' => 'Direct Payment Details not found'
            ];
        }

        if(isset($input['detail_project_id'])){
            $input['detail_project_id'] = $input['detail_project_id'];
        } else {
            $input['detail_project_id'] = null;
        }

        $payMaster = null;

        if(isset($input['directPaymentAutoID'])){
            $payMaster = PaySupplierInvoiceMaster::find($input['directPaymentAutoID']);
        }

        if (empty($payMaster)) {
            return [
                'status' => false,
                'message' => 'Direct Payment Supp Master not found'
            ];
        }

        if($payMaster->confirmedYN){
            return [
                'status' => false,
                'message' => 'You cannot update Direct Payment Detail, this document already confirmed',
                'code' => 500
            ];
        }

        $bankMaster = BankAssign::ofCompany($payMaster->companySystemID)->isActive()->where('bankmasterAutoID', $payMaster->BPVbank)->first();

        if (empty($bankMaster)) {
            return [
                'status' => false,
                'message' => 'Selected Bank is not active'
            ];
        }

        $bankAccount = BankAccount::isActive()->find($payMaster->BPVAccount);

        if (empty($bankAccount)) {
            return [
                'status' => false,
                'message' => 'Selected Bank Account is not active'
            ];
        }

        if (isset($input['serviceLineSystemID'])) {

            if ($input['serviceLineSystemID'] > 0) {
                $checkDepartmentActive = SegmentMaster::find($input['serviceLineSystemID']);
                if (empty($checkDepartmentActive)) {
                    return [
                        'status' => false,
                        'message' => 'Department not found'
                    ];
                }

                if ($checkDepartmentActive->isActive == 0) {
                    DirectPaymentDetails::where('directPaymentDetailsID',$id)->update(['serviceLineSystemID' => null, 'serviceLineCode' => null]);
                    return [
                        'status' => false,
                        'message' => 'Please select an active department',
                        'code' => 500,
                        'type' => $serviceLineError
                    ];
                }

                $input['serviceLineCode'] = $checkDepartmentActive->ServiceLineCode;
            }
        }

        if($input['serviceLineSystemID'] == 0){
            $input['serviceLineSystemID'] = null;
            $input['serviceLineCode'] = null;
        }

        $conversionAmount = \Helper::convertAmountToLocalRpt(202, $input["directPaymentDetailsID"], ABS($input['DPAmount']));

        $input['localAmount'] = \Helper::roundValue($conversionAmount['localAmount']);
        $input['comRptAmount'] = \Helper::roundValue($conversionAmount['reportingAmount']);
        $input['bankAmount'] = \Helper::roundValue($conversionAmount['defaultAmount']);


        $isVATEligible = TaxService::checkCompanyVATEligible($payMaster->companySystemID);
        if($payMaster->invoiceType == 3) {


            $allocatedSum = ExpenseAssetAllocation::where('documentDetailID', $input['directPaymentDetailsID'])
                ->where('documentSystemID', $payMaster->documentSystemID)
                ->where('documentSystemCode', $input['directPaymentAutoID'])
                ->sum('amount');


            if ($allocatedSum > $input['DPAmount']) {
                return [
                    'status' => false,
                    'message' => 'Allocated amount cannot be greater than the detail amount.',
                ];
            }

            $allocatedQtySum = ExpenseEmployeeAllocation::where('documentDetailID', $input['directPaymentDetailsID'])
                ->where('documentSystemID', $payMaster->documentSystemID)
                ->where('documentSystemCode', $input['directPaymentAutoID'])
                ->sum('amount');

            if ($allocatedQtySum > $input['DPAmount']) {
                return [
                    'status' => false,
                    'message' => 'Allocated amount cannot be greater than the detail amount.',
                ];
            }


            if ($isVATEligible && $payMaster->expenseClaimOrPettyCash != 15) {
                $policy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
                    ->where('companyPolicyCategoryID', 67)
                    ->where('isYesNO', 1)
                    ->first();
                $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;

                $currencyConversionVAT = \Helper::currencyConversion($input['companySystemID'], $payMaster->supplierTransCurrencyID, $payMaster->supplierTransCurrencyID, $input['vatAmount']);
                if ($policy == true) {
                    $input['VATAmountLocal'] = \Helper::roundValue($input['vatAmount'] / $payMaster->localCurrencyER);
                    $input['VATAmountRpt'] = \Helper::roundValue($input['vatAmount'] / $payMaster->companyRptCurrencyER);
                }
                if ($policy == false) {
                    $input['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
                    $input['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
                }
                $input['vatAmount'] = \Helper::roundValue($input['vatAmount']);

                $input['netAmount'] = isset($input['netAmount']) ? \Helper::stringToFloat($input['netAmount']) : 0;
                $totalCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $payMaster->supplierTransCurrencyID, $payMaster->supplierTransCurrencyID, $input['netAmount']);

                if ($policy == true) {
                    $input['netAmountLocal'] = \Helper::roundValue($input['netAmount'] / $payMaster->localCurrencyER);
                    $input['netAmountRpt'] = \Helper::roundValue($input['netAmount'] / $payMaster->companyRptCurrencyER);
                }
                if ($policy == false) {
                    $input['netAmountLocal'] = \Helper::roundValue($totalCurrencyConversion['localAmount']);
                    $input['netAmountRpt'] = \Helper::roundValue($totalCurrencyConversion['reportingAmount']);
                }
            }
        }
        $epsilon = 0.000001;
        $isBankChanges = false;

        if ($directPaymentDetails->glCodeIsBank) {
            if($payMaster->expenseClaimOrPettyCash == 15 && $payMaster->invoiceType == 3 && abs($directPaymentDetails->interBankAmount - $input['interBankAmount']) > $epsilon &&  abs($directPaymentDetails->bankCurrencyER - $input['bankCurrencyER']) < 0.000001)
            {
                if(\Helper::roundValue(floatval($input['interBankAmount'])) == 0)
                {
                    return [
                        'status' => false,
                        'message' => 'Inter Bank Amount cannot be zero',
                    ];
                }

                $input["bankCurrencyER"] = \Helper::roundValue($input['DPAmount'] / \Helper::roundValue(floatval($input['interBankAmount'])));
                $isBankChanges = true;
            }
        }
        if(\Helper::roundValue(floatval($input['bankCurrencyER'])) == 0)
        {
            return [
                'status' => false,
                'message' => 'Bank exchange cannot be zero',
            ];
        }

        if ($directPaymentDetails->glCodeIsBank) {
            $trasToDefaultER = $input["bankCurrencyER"];
            $bankAmount = 0;
            if ($bankAccount->accountCurrencyID == $directPaymentDetails->bankCurrencyID) {
                $bankAmount = $input['DPAmount'];
            } else {
                if ($trasToDefaultER > $directPaymentDetails->DPAmountCurrencyER) {
                    if ($trasToDefaultER > 1) {
                        $bankAmount = $input['DPAmount'] / $trasToDefaultER;
                    } else {
                        $bankAmount = $input['DPAmount'] * $trasToDefaultER;
                    }
                } else {
                    If ($trasToDefaultER > 1) {
                        $bankAmount = $input['DPAmount'] * $trasToDefaultER;
                    } else {
                        $bankAmount = $input['DPAmount'] / $trasToDefaultER;
                    }
                }
            }

            if ($directPaymentDetails->bankCurrencyID == $directPaymentDetails->localCurrency) {
                $input['localAmount'] = \Helper::roundValue($bankAmount);
                $input['localCurrencyER'] = $input["bankCurrencyER"];
            }else{
                $conversion = CurrencyConversion::where('masterCurrencyID', $directPaymentDetails->bankCurrencyID)->where('subCurrencyID', $directPaymentDetails->localCurrency)->first();
                if ($conversion->conversion > 1) {
                    if ($conversion->conversion > 1) {
                        $input['localAmount'] = \Helper::roundValue($bankAmount / $conversion->conversion);
                    } else {
                        $input['localAmount'] = \Helper::roundValue($bankAmount * $conversion->conversion);
                    }
                } else {
                    if ($conversion->conversion > 1) {
                        $input['localAmount'] = \Helper::roundValue($bankAmount * $conversion->conversion);
                    } else {
                        $input['localAmount'] = \Helper::roundValue($bankAmount / $conversion->conversion);
                    }
                }
            }

            if ($directPaymentDetails->bankCurrencyID == $directPaymentDetails->comRptCurrency) {
                $input['comRptAmount'] = \Helper::roundValue($bankAmount);
                $input['comRptCurrencyER'] = $input["bankCurrencyER"];
            }else{
                $conversion = CurrencyConversion::where('masterCurrencyID', $directPaymentDetails->bankCurrencyID)->where('subCurrencyID', $directPaymentDetails->comRptCurrency)->first();
                if ($conversion->conversion > 1) {
                    if ($conversion->conversion > 1) {
                        $input['comRptAmount'] = \Helper::roundValue($bankAmount / $conversion->conversion);
                    } else {
                        $input['comRptAmount'] = \Helper::roundValue($bankAmount * $conversion->conversion);
                    }
                } else {
                    if ($conversion->conversion > 1) {
                        $input['comRptAmount'] = \Helper::roundValue($bankAmount * $conversion->conversion);
                    } else {
                        $input['comRptAmount'] = \Helper::roundValue($bankAmount / $conversion->conversion);
                    }
                }
            }

            $input['bankAmount'] = \Helper::roundValue($bankAmount);
            $input['interBankAmount'] = \Helper::roundValue($bankAmount);
        }

        if ($directPaymentDetails->toBankCurrencyID) {
            $conversion = CurrencyConversion::where('masterCurrencyID', $directPaymentDetails->supplierTransCurrencyID)->where('subCurrencyID', $directPaymentDetails->toBankCurrencyID)->first();
            $conversion = $conversion->conversion;
            $bankAmount2 = 0;

            /*if ($directPaymentDetails->toBankCurrencyID == $directPaymentDetails->bankCurrencyID) {
                $bankAmount2 = $input['DPAmount'];*/
            if ($directPaymentDetails->toBankCurrencyID == $directPaymentDetails->localCurrency) {
                $bankAmount2 = $input['localAmount'];
            } else if($directPaymentDetails->toBankCurrencyID == $directPaymentDetails->comRptCurrency){
                $bankAmount2 = $input['comRptAmount'];
            }else
            {
                if ($conversion > $directPaymentDetails->DPAmountCurrencyER) {
                    if ($conversion > 1) {
                        $bankAmount2 = $input['DPAmount'] / $conversion;
                    } else {
                        $bankAmount2 = $input['DPAmount'] * $conversion;
                    }
                } else {
                    If ($conversion > 1) {
                        $bankAmount2 = $input['DPAmount'] * $conversion;
                    } else {
                        $bankAmount2 = $input['DPAmount'] / $conversion;
                    }
                }
            }

            if ($payMaster->interCompanyToSystemID) {
                $companyCurrencyConversion = \Helper::currencyConversion($payMaster->interCompanyToSystemID, $directPaymentDetails->toBankCurrencyID, $directPaymentDetails->toBankCurrencyID, $bankAmount2);

                $input['toCompanyLocalCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                $input['toCompanyLocalCurrencyAmount'] = \Helper::roundValue($companyCurrencyConversion['localAmount']);
                $input['toCompanyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                $input['toCompanyRptCurrencyAmount'] = \Helper::roundValue($companyCurrencyConversion['reportingAmount']);
                $input['toBankCurrencyER'] = $conversion;
                $input['toBankAmount'] = \Helper::roundValue($bankAmount2);
            }
        }

        DirectPaymentDetails::where('directPaymentDetailsID',$id)->update(array_except($input, ['isAutoCreateDocument']));

        // update master table
        PaySupplier::updateMaster($input['directPaymentAutoID']);

        $directPaymentDetails = DirectPaymentDetails::find($id);

        return [
            'status' => true,
            'data' => $directPaymentDetails->refresh()->toArray(),
            'message' => 'DirectPaymentDetails updated successfully'
        ];
    }

    public static function validateAPIDate($date): bool {
        $data = ['date' => $date];

        $rules = [
            'date' => [
                'required',
                'regex:/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                function ($attribute, $value, $fail) {
                    $parts = explode('-', $value);
                    if (!checkdate((int)$parts[1], (int)$parts[2], (int)$parts[0])) {
                        $fail("The $attribute is not a valid date.");
                    }
                }
            ],
        ];

        $validator = Validator::make($data, $rules);

        if (!$validator->fails()) {
            return true;
        }
        else {
            return false;
        }
    }

    private static function validatePVMasterData($request, $index): array {
        $errorData = $fieldErrors = [];

        $companyId = $request['company_id'] ?? null;

        if (isset($request['payment_type'])) {
            if (is_int($request['payment_type'])) {
                if ($request['payment_type'] == 1) {
                    $paymentType = 3;

                    if (isset($request['payee_type'])) {
                        if (is_int($request['payee_type'])) {
                            if (in_array($request['payee_type'],[1,2,3])) {

                                switch ($request['payee_type']) {
                                    // Validate Supplier
                                    case 1:
                                        if (isset($request['supplier'])) {
                                            $supplier = SupplierMaster::where('primarySupplierCode', $request['supplier'])
                                                ->orWhere('registrationNumber',$request['supplier'])
                                                ->where('primaryCompanySystemID',$companyId)
                                                ->first();

                                            if ($supplier) {
                                                if ($supplier->approvedYN == 1) {
                                                    $supplierAssign = SupplierAssigned::where('supplierCodeSytem', $supplier->supplierCodeSystem)
                                                        ->where('companySystemID', $companyId)
                                                        ->first();

                                                    if ($supplierAssign) {
                                                        if ($supplierAssign->isActive == 1) {
                                                            if($supplierAssign->isBlocked != 0){
                                                                $errorData[] = [
                                                                    'field' => "supplier",
                                                                    'message' => ["Selected supplier is blocked."]
                                                                ];
                                                            }
                                                        }
                                                        else {
                                                            $errorData[] = [
                                                                'field' => "supplier",
                                                                'message' => ["Selected supplier is not active."]
                                                            ];
                                                        }
                                                    }
                                                    else {
                                                        $errorData[] = [
                                                            'field' => "supplier",
                                                            'message' => ["Selected supplier is not assigned to the company."]
                                                        ];
                                                    }
                                                }
                                                else {
                                                    $errorData[] = [
                                                        'field' => "supplier",
                                                        'message' => ["Selected supplier is not approved."]
                                                    ];
                                                }
                                            }
                                            else {
                                                $errorData[] = [
                                                    'field' => "supplier",
                                                    'message' => ["Selected Payee type (supplier) is not available in the system."]
                                                ];
                                            }
                                        }
                                        else {
                                            $errorData[] = [
                                                'field' => "supplier",
                                                'message' => ["supplier field is required."]
                                            ];
                                        }

                                        break;
                                    // Validate Employee
                                    case 2:
                                        if (isset($request['employee'])) {
                                            $employee = Employee::where('empID', $request['employee']);
                                            if(Helper::checkHrmsIntergrated($companyId)){
                                                $employee = $employee->whereHas('hr_emp', function($q) use ($request) {
                                                    $q->orWhere('EmpSecondaryCode', $request['employee']);
                                                });
                                            }
                                            $employee = $employee->first();

                                            if ($employee) {
                                                if ($employee->empActive == 1) {
                                                    if($employee->discharegedYN != 0){
                                                        $errorData[] = [
                                                            'field' => "employee",
                                                            'message' => ["Selected employee has already been discharged."]
                                                        ];
                                                    }
                                                }
                                                else {
                                                    $errorData[] = [
                                                        'field' => "employee",
                                                        'message' => ["Selected employee is not active."]
                                                    ];
                                                }
                                            }
                                            else {
                                                $errorData[] = [
                                                    'field' => "employee",
                                                    'message' => ["Selected Payee Type (employee) is not available in the system."]
                                                ];
                                            }
                                        }
                                        else {
                                            $errorData[] = [
                                                'field' => "employee",
                                                'message' => ["employee field is required."]
                                            ];
                                        }

                                        break;
                                    // Validate Other
                                    case 3:
                                        if (!isset($request['other'])) {
                                            $errorData[] = [
                                                'field' => "other",
                                                'message' => ["other field is required."]
                                            ];
                                        }

                                        break;
                                }
                            }
                            else {
                                $errorData[] = [
                                    'field' => "payee_type",
                                    'message' => ["Selected payee type not match with system"]
                                ];
                            }
                        }
                        else {
                            $errorData[] = [
                                'field' => "payee_type",
                                'message' => ["Payee Type must be an integer"]
                            ];
                        }
                    }
                    else {
                        $errorData[] = [
                            'field' => "payee_type",
                            'message' => ["Payee Type field is required"]
                        ];
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "payment_type",
                        'message' => ["payment_type is invalid."]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "payment_type",
                    'message' => ["payment_type must be an integer."]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "payment_type",
                'message' => ["payment_type field is required."]
            ];
        }

        if (isset($request['payment_mode'])) {
            if (is_int($request['payment_mode'])) {
                if (in_array($request['payment_mode'],[1,2,3])) {
                    switch ($request['payment_mode']) {
                        case 1:
                            $paymentMode = 1;
                            break;
                        case 2:
                            $paymentMode = 3;
                            break;
                        case 3:
                            $paymentMode = 4;
                            break;
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "payment_mode",
                        'message' => ["Payment Mode type is invalid"]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "payment_mode",
                    'message' => ["Payment Mode must be an integer"]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "payment_mode",
                'message' => ["Payment Mode field is required"]
            ];
        }

        if (isset($request['currency'])) {
            $request['currency'] = strtoupper($request['currency']);
            $currency = CurrencyMaster::where('CurrencyCode', $request['currency'])->first();
            if (!$currency) {
                $errorData[] = [
                    'field' => "currency",
                    'message' => ["Selected currency is not available in the system."]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "currency",
                'message' => ["currency field is required"]
            ];
        }

        if (isset($request['bank'])) {
            $bank = BankAssign::where('companySystemID', $request['company_id'])
                ->where('bankShortCode',$request['bank'])
                ->first();

            if ($bank) {
                if ($bank->isActive == 1) {
                    if ($bank->isAssigned == -1) {

                        if (isset($request['account'])) {
                            $bankAccount = BankAccount::where('companySystemID', $companyId)
                                ->where('bankmasterAutoID', $bank->bankmasterAutoID)
                                ->where('AccountNo', $request['account'])
                                ->first();

                            if ($bankAccount) {
                                if ($bankAccount->isAccountActive == 1) {
                                    if ($bankAccount->approvedYN != 1) {
                                        $errorData[] = [
                                            'field' => "account",
                                            'message' => ["Selected bank account is not approved in the system."]
                                        ];
                                    }
                                }
                                else {
                                    $errorData[] = [
                                        'field' => "account",
                                        'message' => ["Selected bank account is not active in the system."]
                                    ];
                                }
                            }
                            else {
                                $errorData[] = [
                                    'field' => "account",
                                    'message' => ["Selected bank account is not available in the system."]
                                ];
                            }
                        }
                        else {
                            $errorData[] = [
                                'field' => "account",
                                'message' => ["account field is required"]
                            ];
                        }
                    }
                    else {
                        $errorData[] = [
                            'field' => "bank",
                            'message' => ["Selected bank is not assigned/active to the company"]
                        ];
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "bank",
                        'message' => ["Selected bank is not active."]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "bank",
                    'message' => ["Selected bank is not available in the system."]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "bank",
                'message' => ["bank field is required"]
            ];
        }

        if (isset($request['narration'])) {
            $paymentVoucher = PaySupplierInvoiceMaster::where('BPVNarration', $request['narration'])->where('companySystemID', $companyId)->exists();
            if ($paymentVoucher) {
                $fieldErrors = [
                    'field' => "narration",
                    'message' => ["narration already exists in the system"]
                ];
                $errorData[] = $fieldErrors;
            }
        }
        else {
            $fieldErrors = [
                'field' => "narration",
                'message' => ["narration field is required"]
            ];
            $errorData[] = $fieldErrors;
        }

        if (isset($request['pay_invoice_date'])) {
            $data = self::validateAPIDate($request['pay_invoice_date']);
            if ($data) {
                $payInvoiceDate = Carbon::parse($request['pay_invoice_date']);

                if ($payInvoiceDate->lessThanOrEqualTo(Carbon::today())) {
                    $financeYear = CompanyFinanceYear::where('companySystemID',$companyId)
                        ->where('isDeleted',0)
                        ->where('bigginingDate','<=',$payInvoiceDate)
                        ->where('endingDate','>=',$payInvoiceDate)
                        ->where('isActive', -1)
                        ->first();

                    if ($financeYear) {
                        $financePeriod = CompanyFinancePeriod::where('companySystemID',$companyId)
                            ->where('companyFinanceYearID',$financeYear->companyFinanceYearID)
                            ->where('isActive', -1)
                            ->whereMonth('dateFrom',$payInvoiceDate->month)
                            ->whereMonth('dateTo',$payInvoiceDate->month)
                            ->where(function ($query) {
                                $query->where('departmentSystemID',1)
                                    ->orWhere('departmentSystemID',5);
                            })
                            ->first();
                        if (!$financePeriod) {
                            $errorData[] = [
                                'field' => "pay_invoice_date",
                                'message' => ["Financial period related to the selected pay invoice date is not active for the specified department."]
                            ];
                        }
                    }
                    else{
                        $errorData[] = [
                            'field' => "pay_invoice_date",
                            'message' => ["Financial year related to the selected pay invoice date is either not active or not created."]
                        ];
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "pay_invoice_date",
                        'message' => ["Payment voucher date must be today or before"]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "pay_invoice_date",
                    'message' => ["pay_invoice_date format is invalid"]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "pay_invoice_date",
                'message' => ["pay_invoice_date field is required"]
            ];
        }

        if (isset($request['reverse_charge_mechanism'])) {
            if (is_int($request['reverse_charge_mechanism'])) {
                if (!in_array($request['reverse_charge_mechanism'], [1,2])) {
                    $errorData[] = [
                        'field' => "reverse_charge_mechanism",
                        'message' => ["Invalid RCM Type selected. Please choose the correct type."]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "reverse_charge_mechanism",
                    'message' => ["reverse_charge_mechanism must be an integer."]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "reverse_charge_mechanism",
                'message' => ["reverse_charge_mechanism field is required"]
            ];
        }

        $details = $request['details'] ?? null;

        if (isset($details)) {
            if (is_array($details)) {
                $detailsCollection = collect($details);

                if($detailsCollection->count() < 1) {
                    $errorData[] = [
                        'field' => "details",
                        'message' => ["details cannot be less than one"]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "details",
                    'message' => ["details format invalid"]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "details",
                'message' => ["details field is required"]
            ];
        }

        if (empty($errorData) && empty($fieldErrors)) {
            $returnDataset = [
                'status' => true,
                'data' => [
                    'invoiceType' => $paymentType,
                    'paymentMode' => $paymentMode,
                    'payeeType' => $request['payee_type'],
                    'supplierTransCurrencyID' => $currency->currencyID,
                    'BPVbank' => $bank->bankmasterAutoID,
                    'BPVAccount' => $bankAccount->bankAccountAutoID,
                    'BPVNarration' => $request['narration'],
                    'BPVdate' => $payInvoiceDate->toDateString(),
                    'companyFinanceYearID' => $financeYear->companyFinanceYearID,
                    'companyFinancePeriodID' => $financePeriod->companyFinancePeriodID,
                    'rcmActivated' => $request['reverse_charge_mechanism'],
                    'BPVchequeDate' => Carbon::today()->startOfDay()->format('Y-m-d'),
                    'companySystemID' => $companyId,
                    'documentSystemID' => 4,
                    'isAutoCreateDocument' => true,
                    'initialIndex' => $index
                ]
            ];

            switch ($request['payee_type']) {
                case 1:
                    $returnDataset['data']['BPVsupplierID'] = $supplier->supplierCodeSystem;
                    break;
                case 2:
                    $returnDataset['data']['directPaymentPayeeEmpID'] = $employee->employeeSystemID;
                    break;
                case 3:
                    $returnDataset['data']['directPaymentPayee'] = $request['other'];
                    break;
            }
        }
        else {
            $returnDataset = [
                'status' => false,
                'data' => $errorData,
                'fieldErrors' => $fieldErrors
            ];
        }

        return $returnDataset;
    }

    public static function validatePVDetailsData($masterData, $request): array {
        $errorData = [];

        $companyId = $masterData['company_id'] ?? null;

        // Validate GL Code
        if (isset($request['gl_account'])) {
            $chartOfAccount = ChartOfAccountsAssigned::with('chartofaccount')
                ->where('companySystemID', $companyId)
                ->where('AccountCode',$request['gl_account'])
                ->first();

            if ($chartOfAccount){
                if (($chartOfAccount->isActive == 1) && ($chartOfAccount->isAssigned == -1)) {
                    if ($chartOfAccount->isBank == 0) {
                        $chartOfAccountMaster = $chartOfAccount->chartofaccount;
                        if ($chartOfAccountMaster->isApproved == 1) {
                            if ($chartOfAccount->controllAccountYN == 0) {
                                if ($chartOfAccount->controlAccountsSystemID == 1) {
                                    $errorData[] = [
                                        'field' => "gl_account",
                                        'message' => ["Selected GL code is of type 'Income' and is not allowed for this transaction."]
                                    ];
                                }
                            }
                            else {
                                $errorData[] = [
                                    'field' => "gl_account",
                                    'message' => ["Selected GL code is a control account and cannot be used."]
                                ];
                            }
                        }
                        else {
                            $errorData[] = [
                                'field' => "gl_account",
                                'message' => ["Selected GL code is not approved."]
                            ];
                        }
                    }
                    else {
                        $errorData[] = [
                            'field' => "gl_account",
                            'message' => ["Selected GL code is bank gl code."]
                        ];
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "gl_account",
                        'message' => ["Selected GL code is either not active or not assigned to the company."]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "gl_account",
                    'message' => ["Selected GL code does not match any record in the system."]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "gl_account",
                'message' => ["gl_account field is required"]
            ];
        }

        // Validate Project
        if (isset($request['project'])) {
            $project = ErpProjectMaster::where('projectCode', $request['project'])->first();

            if (!$project) {
                $errorData[] = [
                    'field' => "project",
                    'message' => ["The selected project code does not match with the system."]
                ];
            }
        }
        else {
            $project = null;
        }

        // Validate Segment
        if (isset($request['segment'])) {
            $segment = SegmentMaster::where('ServiceLineCode',$request['segment'])
                ->where('companySystemID', $companyId)
                ->first();

            if ($segment) {
                if ($segment->isActive == 1) {
                    if ($segment->isDeleted != 0) {
                        $errorData[] = [
                            'field' => "segment",
                            'message' => ["Selected segment is deleted"]
                        ];
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "segment",
                        'message' => ["Selected segment not active"]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "segment",
                    'message' => ["Selected segment code does not match with system"]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "segment",
                'message' => ["segment field is required"]
            ];
        }

        // Validate Amount
        $amountValidation = false;
        if (isset($request['amount'])) {
            if (gettype($request['amount']) != 'string') {
                if ($request['amount'] > 0) {
                    $amountValidation = true;
                }
                else {
                    $errorData[] = [
                        'field' => "amount",
                        'message' => ["The amount should be a positive value."]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "amount",
                    'message' => ["amount must be a numeric"]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "amount",
                'message' => ["amount field is required"]
            ];
        }

        // Validate VAT Percentage
        $vatPercentageValidation = false;
        if (isset($request['vat_percentage'])) {
            if (gettype($request['vat_percentage']) != 'string') {
                if ($request['vat_percentage'] >= 0) {
                    $vatPercentageValidation = true;
                }
                else {
                    $errorData[] = [
                        'field' => "vat_percentage",
                        'message' => ["vat_percentage must be at least 0"]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "vat_percentage",
                    'message' => ["vat_percentage must be a numeric"]
                ];
            }
        }

        // Validate VAT Amount
        $vatAmountValidation = false;
        if (isset($request['vat_amount'])) {
            if (gettype($request['vat_amount']) != 'string') {
                if ($request['vat_amount'] >= 0) {
                    $vatAmountValidation = true;
                }
                else {
                    $errorData[] = [
                        'field' => "vat_amount",
                        'message' => ["vat_amount must be at least 0"]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "vat_amount",
                    'message' => ["vat_amount must be a numeric"]
                ];
            }
        }

        if ($amountValidation && ($vatPercentageValidation && $vatAmountValidation)) {
            $vatAmount = ($request['amount'] * $request['vat_percentage']) / 100;
            if ($vatAmount != $request['vat_amount']) {
                $errorData[] = [
                    'field' => "vat_amount",
                    'message' => ["VAT% and VAT Amount is not matching"]
                ];
            }
        }

        if ($amountValidation && (!$vatPercentageValidation && $vatAmountValidation)) {
            $request['vat_percentage'] = ($request['vat_amount'] / $request['amount']) * 100;
        }

        if ($amountValidation && ($vatPercentageValidation && !$vatAmountValidation)) {
            $request['vat_amount'] = ($request['amount'] * $request['vat_percentage']) / 100;
        }

        if (empty($errorData)) {
            $returnData = [
                "status" => true,
                "data" => [
                    'chartOfAccountSystemID' => $chartOfAccount->chartOfAccountSystemID,
                    'serviceLineSystemID' => $segment->serviceLineSystemID,
                    'comments' => $request['comments'] ?? null,
                    'DPAmount' => $request['amount'],
                    'VATPercentage' => $request['vat_percentage'] ?? 0,
                    'vatAmount' => $request['vat_amount'] ?? 0,
                    'netAmount' => $request['amount'],
                    'detail_project_id' => $project != null ? $project->id : null,
                    'companySystemID' => $companyId,
                    'isAutoCreateDocument' => true
                ]
            ];
        }
        else{
            $returnData = [
                "status" => false,
                "data" => $errorData
            ];
        }

        return $returnData;
    }

    public static function createErrorResponseDataArray($narration,$masterIndex,$fieldErrors, $headerData, $detailData): array {
        return [
            'identifier' => [
                'unique-key' => $narration,
                'index' => $masterIndex + 1
            ],
            'fieldErrors' => $fieldErrors,
            'headerData' => [$headerData],
            'detailData' => [$detailData]
        ];
    }

    public static function createSuccessResponseDataArray($narration,$masterIndex,$code): array {
        return [
            'uniqueKey' => $narration,
            'index' => $masterIndex + 1,
            'paymentVoucherCode' => $code,
        ];
    }

    public static function storePaymentVouchersFromAPI($db, $data) {

        $fieldErrors = $masterDatasets = $detailsDataSets = $errorDocuments = $successDocuments = [];
        $headerData = $detailData = ['status' => false , 'errors' => []];
        $pvID = [];

        $masterIndex = 0;
        $paymentVouchers = $data['payment_vouchers'] ?? null;

        foreach ($paymentVouchers as $paymentVoucher) {

            $paymentVoucher['company_id'] = $data['company_id'];

            $datasetMaster = self::validatePVMasterData($paymentVoucher, $masterIndex);

            if (!$datasetMaster['status']) {
                $fieldErrors = $datasetMaster['fieldErrors'];
                $headerData['errors'] = $datasetMaster['data'];
            }

            $detailIndex = 0;
            $details = $paymentVoucher['details'] ?? null;

            foreach ($details as $detail) {

                $datasetDetails = self::validatePVDetailsData($paymentVoucher,$detail);

                if ($datasetDetails['status']) {
                    $detailsDataSets[$masterIndex][] = $datasetDetails['data'];
                }
                else {
                    $detailData['errors'][] = [
                        'index' => $detailIndex + 1,
                        'error' => $datasetDetails['data']
                    ];
                    unset($detailsDataSets[$masterIndex]);
                }

                $detailIndex++;
            }

            if (empty($headerData['errors']) && empty($detailData['errors']) && empty($fieldErrors)) {
                $masterDatasets[] = array_add($datasetMaster['data'],'details',$detailsDataSets[$masterIndex]);
            }
            else {
                if (empty($headerData['errors'])) {
                    $headerData['status'] = true;
                }

                if (empty($detailData['errors'])) {
                    $detailData['status'] = true;
                }

                $errorDocuments[] = self::createErrorResponseDataArray($paymentVoucher['narration'], $masterIndex, $fieldErrors, $headerData, $detailData);

                $fieldErrors = [];
                $headerData = $detailData = ['status' => false , 'errors' => []];
            }

            $masterIndex++;
        }

        if(!empty($masterDatasets)) {
            DB::beginTransaction();

            $headerData = $detailData = ['status' => true , 'errors' => []];

            foreach ($masterDatasets as $masterDataset) {
                $documentStatus = true;
                try {
                    $detailsData = $masterDataset['details'];
                    unset($masterDataset['details']);

                    $masterInsert = self::createPaymentVoucher($masterDataset);

                    if($masterInsert['status']) {
                        $pvMasterAutoId = $masterInsert['data']['PayMasterAutoId'];

                        foreach ($detailsData as $pvDetail) {
                            $pvDetail['directPaymentAutoID'] = $pvMasterAutoId;

                            $detailInsert = self::storeDirectPaymentDetails($pvDetail);

                            if (!$detailInsert['status']) {
                                $documentStatus = false;
                                DB::rollBack();
                                $error = self::createErrorResponseDataArray($masterDataset['BPVNarration'], $masterDataset['initialIndex'], [], $headerData, $detailData);
                                $error['headerData'] = $detailInsert['message'];
                                $errorDocuments[] = $error;
                                break 2;
                            }
                        }

                        if($documentStatus) {
                            $confirmDataSet = $masterInsert['data'];
                            $confirmDataSet['confirmedYN'] = 1;
                            $confirmDataSet['payeeType'] = $masterDataset['payeeType'];
                            $confirmDataSet['paymentMode'] = $masterDataset['paymentMode'];
                            $confirmDataSet['isSupplierBlocked'] = true;
                            $confirmDataSet['isAutoCreateDocument'] = true;

                            $pvUpdateData = self::updatePaymentVoucher($confirmDataSet['PayMasterAutoId'],$confirmDataSet);

                            if($pvUpdateData['status']){

                                $autoApproveParams = DocumentAutoApproveService::getAutoApproveParams($confirmDataSet['documentSystemID'],$confirmDataSet['PayMasterAutoId']);
                                $autoApproveParams['supplierPrimaryCode'] = $confirmDataSet['BPVcode'];
                                $autoApproveParams['createMonthlyDeduction'] = $confirmDataSet['createMonthlyDeduction'];
                                $autoApproveParams['db'] = $db;

                                $approveDocument = Helper::approveDocument($autoApproveParams);

                                if ($approveDocument["success"]) {
                                    DB::commit();
                                    $pvID[] = $confirmDataSet['PayMasterAutoId'];
                                    $success = self::createSuccessResponseDataArray($masterDataset['BPVNarration'], $masterDataset['initialIndex'], $confirmDataSet['BPVcode']);
                                    $successDocuments[] = $success;
                                }
                                else {
                                    DB::rollBack();
                                    $error = self::createErrorResponseDataArray($masterDataset['BPVNarration'], $masterDataset['initialIndex'], [], $headerData, $detailData);
                                    $error['headerData'] = $approveDocument['message'];
                                    $errorDocuments[] = $error;
                                }
                            }
                            else {
                                DB::rollBack();
                                $error = self::createErrorResponseDataArray($masterDataset['BPVNarration'], $masterDataset['initialIndex'], [], $headerData, $detailData);
                                $error['headerData'] = $pvUpdateData['message'];
                                $errorDocuments[] = $error;
                            }
                        }
                    }
                    else {
                        DB::rollBack();
                        $error = self::createErrorResponseDataArray($masterDataset['BPVNarration'], $masterDataset['initialIndex'], [], $headerData, $detailData);
                        $error['headerData'][] = [
                            'field' => "",
                            'message' => [$masterInsert['message']]
                        ];
                        $errorDocuments[] = $error;
                    }
                }
                catch (\Exception $e) {
                    DB::rollBack();
                    $error = self::createErrorResponseDataArray($masterDataset['BPVNarration'], $masterDataset['initialIndex'], [], $headerData, $detailData);
                    $error['headerData'][] = [
                        'field' => "",
                        'message' => [$e->getMessage()]
                    ];
                    $errorDocuments[] = $error;
                }
            }
        }

        $returnData = [];

        if(!empty($errorDocuments)) {
            $returnData[] = [
                'success' => false,
                'message' => "Validation Failed",
                'code' => 422,
                'errors' => $errorDocuments,
            ];
        }

        if(!empty($successDocuments)) {
            $returnData[] = [
                'success' => true,
                'message' => "Payment voucher created Successfully!",
                'code' => 200,
                'data' => $successDocuments,
            ];
        }

        return [
            "finalDataset" => $returnData,
            "successDocumentList" => $pvID
        ];
    }

}
