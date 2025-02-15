<?php

namespace App\Services;

use App\helper\Helper;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\Contract;
use App\Models\DocumentMaster;
use App\Models\JvDetail;
use App\Models\JvMaster;
use App\Models\SegmentMaster;
use App\Models\SystemGlCodeScenario;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class JournalVoucherService
{
    public static function createJournalVoucher($input)
    {
        $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYear["success"]) {
            return [
                "status" => false,
                "message" => $companyFinanceYear["message"],
                "httpCode" => 500
            ];
        }

        $inputParam = $input;
        $inputParam["departmentSystemID"] = 5;
        $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
        if (!$companyFinancePeriod["success"]) {
            return [
                "status" => false,
                "message" => $companyFinancePeriod["message"],
                "httpCode" => 500
            ];
        }
        else {
            $input['FYBiggin'] = $companyFinancePeriod["message"]->dateFrom;
            $input['FYEnd'] = $companyFinancePeriod["message"]->dateTo;
        }
        unset($inputParam);

        if (isset($input['jvType']) && $input['jvType'] == 5)
        {
            $systemGlCodeScenario = SystemGlCodeScenario::where('slug','po-accrual-liability')->first();
            if($systemGlCodeScenario)
            {
                $glCodeScenarioDetails = SystemGlCodeScenarioDetail::where('systemGlScenarioID',$systemGlCodeScenario->id)->where('companySystemID',$input["companySystemID"])->first();
                if(!$glCodeScenarioDetails || ($glCodeScenarioDetails && is_null($glCodeScenarioDetails->chartOfAccountSystemID)) || ($glCodeScenarioDetails && $glCodeScenarioDetails->chartOfAccountSystemID == 0))
                {
                    return [
                        "status" => false,
                        "message" => "Please configure PO accrual account for this company.",
                        "httpCode" => 500
                    ];
                }
            }else {
                return [
                    "status" => false,
                    "message" => "Gl Code scenario not found for PO Accrual.",
                    "httpCode" => 500
                ];
            }

            if(Carbon::parse($input['reversalDate']) <= Carbon::parse($input['JVdate']))
            {
                return [
                    "status" => false,
                    "message" => "Reversal date should greater the JV date.",
                    "httpCode" => 500
                ];
            }else {
                $input['reversalDate'] = Carbon::parse($input['reversalDate']);
            }
        }
        if (isset($input['jvType']) && $input['jvType'] == 4) {
            $checkPendingJv = JvMaster::where('jvType', $input['jvType'])
                ->where('companySystemID', $input['companySystemID'])
                ->where('refferedBackYN', 0)
                ->where('approved', 0)
                ->first();

            if ($checkPendingJv) {
                return [
                    "status" => false,
                    "message" => "There is a pending allocation JV, please approve those allocation JVs.",
                    "httpCode" => 500
                ];
            }
        }

        if (isset($input['JVdate'])) {
            if ($input['JVdate']) {
                $input['JVdate'] = new Carbon($input['JVdate']);
            }
        }

        if (isset($input['reversalDate'])) {
            if ($input['reversalDate']) {
                $input['reversalDate'] = new Carbon($input['reversalDate']);
            }
        }

        $documentDate = $input['JVdate'];
        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];
        if ($documentDate < $monthBegin || $documentDate > $monthEnd) {
            return [
                "status" => false,
                "message" => "JV date is not within the financial period.",
                "httpCode" => 500
            ];
        }

        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        $FYPeriodDateFrom = $companyfinanceperiod->dateFrom;
        $FYPeriodDateTo = $companyfinanceperiod->dateTo;

        $input['FYPeriodDateFrom'] = $FYPeriodDateFrom;
        $input['FYPeriodDateTo'] = $FYPeriodDateTo;
        $input['createdPcID'] = gethostname();

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            $employee = UserTypeService::getSystemEmployee();
            $input['createdUserID'] = $employee->empID;
            $input['createdUserSystemID'] = $employee->employeeSystemID;
        }
        else{
            $id = Auth::id();
            $user = User::with(['employee'])->find($id);

            $input['createdUserID'] = $user->employee['empID'];
            $input['createdUserSystemID'] = $user->employee['employeeSystemID'];
        }
        $input['documentSystemID'] = '17';
        $input['documentID'] = 'JV';

        $lastSerial = JvMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('serialNo', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['currencyID'], $input['currencyID'], 0);
        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
            $input['rptCurrencyID'] = $company->reportingCurrency;
            $input['rptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
        }

        $input['serialNo'] = $lastSerialNumber;
        $input['currencyER'] = 1;

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        $companyfinanceyear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();

        if ($companyfinanceyear) {
            $startYear = $companyfinanceyear['bigginingDate'];
            $finYearExp = explode('-', $startYear);
            $finYear = $finYearExp[0];
        } else {
            $finYear = date("Y");
        }
        if ($documentMaster) {
            $jvCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['JVcode'] = $jvCode;
        }

        if (isset($input['reversalJV'])) {
            if ($input['reversalJV'] == 0 && $input['jvType'] == 0) {
                $input['reversalDate'] = null;
            }
        }

        $jvMaster = JvMaster::create($input);
        return [
            'status' => true,
            'data' => $jvMaster->refresh()->toArray(),
            'message' => 'Pay Supplier Invoice Master saved successfully',
            "httpCode" => 200
        ];
    }

    public static function updateJournalVoucher($id, $input)
    {
        $jvMaster = JvMaster::find($id);

        if (empty($jvMaster)) {
            return [
                "status" => false,
                "message" => "Jv Master not found",
                "httpCode" => 500
            ];
        }

        $jvConfirmedYN = $input['confirmedYN'];
        $prevJvConfirmedYN = $jvMaster->confirmedYN;
        $currencyDecimalPlace = \Helper::getCurrencyDecimalPlace($jvMaster->currencyID);

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
        }
        else{
            if (isset($input['JVdate'])) {
                if ($input['JVdate']) {
                    $input['JVdate'] = Carbon::parse($input['JVdate']);
                }
            }

            if (isset($input['reversalDate'])) {
                if ($input['reversalDate']) {
                    $input['reversalDate'] = new Carbon($input['reversalDate']);
                }
            }


            if (isset($input['jvType']) && $input['jvType'] == 5) {
                $systemGlCodeScenario = SystemGlCodeScenario::where('slug','po-accrual-liability')->first();

                if($systemGlCodeScenario)
                {
                    $glCodeScenarioDetails = SystemGlCodeScenarioDetail::where('systemGlScenarioID',$systemGlCodeScenario->id)->where('companySystemID',$input["companySystemID"])->first();

                    if(!$glCodeScenarioDetails || ($glCodeScenarioDetails && is_null($glCodeScenarioDetails->chartOfAccountSystemID)) || ($glCodeScenarioDetails && $glCodeScenarioDetails->chartOfAccountSystemID == 0))
                    {
                        return [
                            "status" => false,
                            "message" => "Please configure PO accrual account for this company",
                            "httpCode" => 500
                        ];
                    }
                }else {
                    return [
                        "status" => false,
                        "message" => "Gl Code scenario not found for PO Accrual",
                        "httpCode" => 500
                    ];
                }

                if(Carbon::parse($input['reversalDate']) <= Carbon::parse($input['JVdate']))
                {
                    return [
                        "status" => false,
                        "message" => "Reversal date should greater the JV date",
                        "httpCode" => 500
                    ];
                }else {
                    $input['reversalDate'] = Carbon::parse($input['reversalDate']);
                }
            }
            if (isset($input['jvType']) && $input['jvType'] == 4) {
                $checkPendingJv = JvMaster::where('jvType', $input['jvType'])
                    ->where('companySystemID', $input['companySystemID'])
                    ->where('refferedBackYN', 0)
                    ->where('jvMasterAutoId', '!=', $id)
                    ->where('approved', 0)
                    ->first();

                if ($checkPendingJv) {
                    return [
                        "status" => false,
                        "message" => "There is a pending allocation JV, please approve those allocation JVs",
                        "httpCode" => 500
                    ];
                }
            }

            if(isset($input['reversalJV']) && $input['reversalJV'] == 1){
                if($input['reversalDate'] == null) {
                    return [
                        "status" => false,
                        "message" => "Reversal Date is mandatory",
                        "httpCode" => 500
                    ];
                }
            }

            $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
            if (!$companyFinanceYear["success"]) {
                return [
                    "status" => false,
                    "message" => $companyFinanceYear["message"],
                    "httpCode" => 500
                ];
            } else {
                $input['FYBiggin'] = $companyFinanceYear["message"]->bigginingDate;
                $input['FYEnd'] = $companyFinanceYear["message"]->endingDate;
            }

            $inputParam = $input;
            $inputParam["departmentSystemID"] = 5;
            $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
            if (!$companyFinancePeriod["success"]) {
                return [
                    "status" => false,
                    "message" => $companyFinancePeriod["message"],
                    "httpCode" => 500
                ];
            } else {
                $input['FYPeriodDateFrom'] = $companyFinancePeriod["message"]->dateFrom;
                $input['FYPeriodDateTo'] = $companyFinancePeriod["message"]->dateTo;
            }
            unset($inputParam);

            $documentDate = $input['JVdate'];
            $monthBegin = $input['FYPeriodDateFrom'];
            $monthEnd = $input['FYPeriodDateTo'];

            if ($documentDate < $monthBegin || $documentDate > $monthEnd) {
                return [
                    "status" => false,
                    "message" => "Document date is not within the selected financial period !",
                    "httpCode" => 500
                ];
            }
        }

        if ($jvMaster->confirmedYN == 0 && $input['confirmedYN'] == 1)
        {
            $query = JvDetail::selectRaw("chartofaccounts.AccountCode")
                ->join('chartofaccounts', 'chartofaccounts.chartOfAccountSystemID', '=', 'erp_jvdetail.chartOfAccountSystemID')
                ->where('chartofaccounts.isActive',0)
                ->where('erp_jvdetail.jvMasterAutoId', $input['jvMasterAutoId'])
                ->groupBy('chartofaccounts.AccountCode');

            if($query->count() > 0)
            {
                $inActiveAccounts = $query->pluck('AccountCode');
                $lastKey = count($inActiveAccounts) - 1;
                $msg = '';
                foreach($inActiveAccounts as $key => $account)
                {
                    if ($key != $lastKey) {
                        $msg .= ' '.$account.' ,';
                    }
                    else {
                        $msg .= ' '.$account;
                    }
                }

                if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']) {
                    return [
                        "status" => false,
                        "message" => "The Chart of Account/s are Inactive",
                        "httpCode" => 500
                    ];
                }
                else{
                    return [
                        "status" => false,
                        "message" => "The Chart of Account/s $msg are Inactive, update it as active/change the GL code to proceed.",
                        "type" => 'ca_inactive',
                        "httpCode" => 500
                    ];
                }
            }

            $documentDate = $input['JVdate'];
            $monthBegin = $input['FYPeriodDateFrom'];
            $monthEnd = $input['FYPeriodDateTo'];
            if ($documentDate < $monthBegin || $documentDate > $monthEnd) {
                return [
                    "status" => false,
                    "message" => "Document date is not within the selected financial period !",
                    "httpCode" => 500
                ];
            }

            $checkItems = JvDetail::where('jvMasterAutoId', $id)
                ->count();
            if ($checkItems == 0) {
                return [
                    "status" => false,
                    "message" => "Journal Voucher should have at least one item",
                    "httpCode" => 500
                ];
            }

            if ($jvMaster->jvType != 4) {
                $checkQuantity = JvDetail::where('jvMasterAutoId', $id)
                    ->where('debitAmount', '<=', 0)
                    ->where('creditAmount', '<=', 0)
                    ->count();
                if ($checkQuantity > 0) {
                    return [
                        "status" => false,
                        "message" => "Amount should be greater than 0 for debit amount or credit amount",
                        "httpCode" => 500
                    ];
                }
            }

            $finalError = array(
                'required_serviceLine' => array(),
                'active_serviceLine' => array(),
                'contract_check' => array()
            );
            $error_count = 0;

            $jvDetails = JvDetail::where('jvMasterAutoId', $id)->get();
            foreach ($jvDetails as $item) {
                $updateItem = JvDetail::find($item['jvDetailAutoID']);

                if ($updateItem->serviceLineSystemID && !is_null($updateItem->serviceLineSystemID)) {
                    if ($jvMaster->jvType != 5) {
                        $checkDepartmentActive = SegmentMaster::where('serviceLineSystemID', $updateItem->serviceLineSystemID)
                            ->where('isActive', 1)
                            ->first();
                        if (empty($checkDepartmentActive)) {
                            $updateItem->serviceLineSystemID = null;
                            $updateItem->serviceLineCode = null;
                            array_push($finalError['active_serviceLine'], $updateItem->glAccount);
                            $error_count++;
                        }
                    }
                } else {
                    array_push($finalError['required_serviceLine'], $updateItem->glAccount);
                    $error_count++;
                }
            }

            //if standard jv
            if ($input['jvType'] == 0) {
                $policyConfirmedUserToApprove = CompanyPolicyMaster::where('companyPolicyCategoryID', 15)
                    ->where('companySystemID', $input['companySystemID'])
                    ->first();

                if ($policyConfirmedUserToApprove->isYesNO == 0) {
                    foreach ($jvDetails as $item) {
                        $chartOfAccount = ChartOfAccountsAssigned::select('controlAccountsSystemID')->where('chartOfAccountSystemID', $item->chartOfAccountSystemID)->first();
                        if ($chartOfAccount->controlAccountsSystemID == 1) {
                            if ($item['contractUID'] == '' || $item['contractUID'] == 0) {
                                array_push($finalError['contract_check'], $item->glAccount);
                                $error_count++;
                            }
                        }
                    }
                }
            }

            $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
            if ($error_count > 0) {
                if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                    return [
                        "status" => false,
                        "message" => "You cannot confirm this document.",
                        "httpCode" => 500
                    ];
                }
                else{
                    return [
                        "status" => false,
                        "message" => "Amount should be greater than 0 for debit amount or credit amount",
                        "error_details" => $confirm_error,
                        "httpCode" => 500
                    ];
                }
            }

            $JvDetailDebitSum = JvDetail::where('jvMasterAutoId', $id)
                ->sum('debitAmount');

            $JvDetailCreditSum = JvDetail::where('jvMasterAutoId', $id)
                ->sum('creditAmount');

            if (round($JvDetailDebitSum, $currencyDecimalPlace) != round($JvDetailCreditSum, $currencyDecimalPlace)) {
                return [
                    "status" => false,
                    "message" => "Debit amount total and credit amount total is not matching.",
                    "httpCode" => 500
                ];
            }
            $input['RollLevForApp_curr'] = 1;

            unset($input['confirmedYN']);
            unset($input['confirmedByEmpSystemID']);
            unset($input['confirmedByEmpID']);
            unset($input['confirmedByName']);
            unset($input['confirmedDate']);

            $params = array(
                'autoID' => $id,
                'company' => $input["companySystemID"],
                'document' => $input["documentSystemID"],
                'segment' => 0,
                'category' => 0,
                'amount' => $JvDetailDebitSum,
                'isAutoCreateDocument' => isset($input['isAutoCreateDocument'])
            );

            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return [
                    "status" => false,
                    "message" => $confirm["message"],
                    "httpCode" => 500
                ];
            }
        }

        if (isset($input['reversalJV'])) {
            if ($input['reversalJV'] == 0 && $input['jvType'] == 0) {
                $input['reversalDate'] = null;
            }
        }

        $employee = (isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']) ? UserTypeService::getSystemEmployee() : Helper::getEmployeeInfo();
        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;
        if($jvMaster->jvType == 5)
        {
            $input['reversalDate'] = Carbon::parse($input['reversalDate']);
        }

        if($input['jvType'] == 1 || $input['jvType'] == 2 || $input['jvType'] == 3 || $input['jvType'] == 4)
        {
            $input['reversalJV'] = 0;
            $input['reversalDate'] = null;
        }

        unset($input['isAutoCreateDocument']);
        $jvMasterUpdate = JvMaster::where('jvMasterAutoId', $id)->update($input);
        if ($jvConfirmedYN == 1 && $prevJvConfirmedYN == 0) {
            return [
                "status" => true,
                "message" => "Journal Voucher confirmed successfully",
                "data" => $input,
                "confirm_data" => $confirm['data'] ?? null,
                "httpCode" => 200
            ];
        }
        return [
            "status" => true,
            "message" => "Journal Voucher updated successfully",
            "data" => $input,
            "httpCode" => 200
        ];
    }

    public static function createJournalVoucherDetail($input)
    {
        $jvMaster = JvMaster::find($input['jvMasterAutoId']);
        if (empty($jvMaster)) {
            return [
                "status" => false,
                "message" => "Journal Voucher not found",
                "httpCode" => 500
            ];
        }

        $input['documentSystemID'] = $jvMaster->documentSystemID;
        $input['documentID'] = $jvMaster->documentID;
        $input['companySystemID'] = $jvMaster->companySystemID;
        $input['companyID'] = $jvMaster->companyID;

        $chartOfAccount = ChartOfAccount::find($input['chartOfAccountSystemID']);
        if (empty($chartOfAccount)) {
            return [
                "status" => false,
                "message" => "Chart of Account not found",
                "httpCode" => 500
            ];
        }

        $input['glAccount'] = $chartOfAccount->AccountCode;
        $input['glAccountDescription'] = $chartOfAccount->AccountDescription;

        $input['currencyID'] = $jvMaster->currencyID;
        $input['currencyER'] = $jvMaster->currencyER;
        $input['comments'] = $jvMaster->JVNarration;

        $input['createdPcID'] = gethostname();

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            $employee = UserTypeService::getSystemEmployee();
            $input['createdUserID'] = $employee->empID;
            $input['createdUserSystemID'] = $employee->employeeSystemID;
        }
        else{
            $id = Auth::id();
            $user = User::with(['employee'])->find($id);
            $input['createdUserID'] = $user->employee['empID'];
            $input['createdUserSystemID'] = $user->employee['employeeSystemID'];
        }

        $jvDetails = JvDetail::create($input);

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            $inputData = $jvDetails->refresh()->toArray();
            $returnData = self::updateJournalVoucherDetail($inputData['jvDetailAutoID'],$inputData);

            if($returnData['status']){
                return [
                    'status' => true,
                    'data' => $returnData['data'],
                    'message' => $returnData['message'],
                    "httpCode" => 200
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
                'data' => $jvDetails->refresh()->toArray(),
                'message' => 'Jv Detail saved successfully',
                "httpCode" => 200
            ];
        }
    }

    public static function updateJournalVoucherDetail($id, $input)
    {
        $serviceLineError = array('type' => 'serviceLine');
        $jvDetail = JvDetail::find($id);
        if (empty($jvDetail)) {
            return [
                "status" => false,
                "message" => "Jv Detail not found",
                "httpCode" => 500
            ];
        }

        $jvMaster = JvMaster::find($input['jvMasterAutoId']);
        if (empty($jvMaster)) {
            return [
                "status" => false,
                "message" => "Journal Voucher not found",
                "httpCode" => 500
            ];
        }

        if ($input['creditAmount'] == '') {
            $input['creditAmount'] = 0;
        }
        if ($input['debitAmount'] == '') {
            $input['debitAmount'] = 0;
        }

        if (isset($input['serviceLineSystemID'])) {
            if ($input['serviceLineSystemID'] > 0) {
                $checkDepartmentActive = SegmentMaster::find($input['serviceLineSystemID']);
                if (empty($checkDepartmentActive)) {
                    return [
                        "status" => false,
                        "message" => "Department not found",
                        "httpCode" => 500
                    ];
                }
                if ($checkDepartmentActive->isActive == 0) {
                    JvDetail::where('jvDetailAutoID', $id)->update(['serviceLineSystemID' => null, 'serviceLineCode' => null]);
                    return [
                        "status" => false,
                        "message" => "Please select an active department",
                        "error_details" => $serviceLineError,
                        "httpCode" => 500
                    ];
                }
                $input['serviceLineCode'] = $checkDepartmentActive->ServiceLineCode;
            }
        }

        if (isset($input['contractUID'])) {
            $input['clientContractID'] = NULL;
            $contract = Contract::select('ContractNumber', 'isRequiredStamp', 'paymentInDaysForJob')
                ->where('contractUID', $input['contractUID'])
                ->first();

            if(!empty($contract)) {
                $input['clientContractID'] = $contract['ContractNumber'];
            }
        }

        if(isset($input['line_segments']) || (isset($input['line_segments']) && is_null($input['line_segments']))) {
            unset($input['line_segments']);
        }


        JvDetail::where('jvDetailAutoID', $id)->update(array_except($input, ['isAutoCreateDocument']));
        return [
            "status" => true,
            "message" => "JvDetail updated successfully",
            "data" => $input,
            "httpCode" => 200
        ];
    }
}
