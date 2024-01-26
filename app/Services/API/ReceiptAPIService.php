<?php

namespace App\Services\API;

use App\Models\BankAccount;
use App\Models\BankMaster;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CurrencyMaster;
use App\Models\CustomerMaster;
use App\Models\CustomerReceivePayment;
use App\Models\DocumentApproved;
use App\Models\Employee;
use Carbon\Carbon;
use Cassandra\Custom;
use PhpParser\Node\Expr\Array_;

class ReceiptAPIService
{

    public $financeYearError = array();
    public $financePeriodError = array();

    public function storeReceiptVoucherData($receipts,$db) {
        $savedReceipts = array();
        $isFailed = false;
        $errorReceipts = array();
        $errorDetails = array();
        $errorConfirmation = array();
        $errorApproval = array();
        foreach ($receipts as $receipt) {
            $receipt = self::serialCodeDetails($receipt);

            if(!isset($receipt->companyFinanceYearID)) {
                $isFailed = true;
                array_push($this->financeYearError,$receipt->narration);
            }

            if(!isset($receipt->FYPeriodDateFrom)) {
                $isFailed = true;
                array_push($this->financePeriodError,$receipt->narration);

            }

            $saveReceipt = CustomerReceivePayment::create($receipt->toArray());
            array_push($savedReceipts,$saveReceipt);
            if($saveReceipt) {
                foreach ($receipt->details as $detail) {
                    $result = ReceiptDetailsAPIService::storeReceiptDetails($detail,$saveReceipt);
                    if($result['status'] === 'fail') {
                        $isFailed = true;
                        array_push($errorDetails,$detail['invoiceCode']);
                    }
                }

                $params = array('autoID' => $saveReceipt->custReceivePaymentAutoID,
                    'company' => $saveReceipt->companySystemID,
                    'document' => $saveReceipt->documentSystemID,
                    'segment' => '',
                    'category' => '',
                    'amount' => '',
                    'receipt' => true
                );

                $confirmation = \Helper::confirmDocumentForApi($params);
                if($confirmation['success'])
                {
                    $documentApproveds = DocumentApproved::where('documentSystemCode', $saveReceipt->custReceivePaymentAutoID)->where('documentSystemID', $saveReceipt->documentSystemID)->get();
                    foreach ($documentApproveds as $documentApproved) {
                        $documentApproved["approvedComments"] = "Generated Customer Invoice through API";
                        $documentApproved["db"] = $db;
                        $documentApproved['empID'] = $receipt->approvedByUserSystemID;
                        $approval = \Helper::approveDocumentForApi($documentApproved); // check approval
                        if($approval['success']) {

                        }else {
                            $isFailed = true;
                            array_push($errorApproval,$saveReceipt->narration);
                        }

                    }
                }else {
                    $isFailed = true;
                    array_push($errorConfirmation,$saveReceipt->narration);
                }
            }else {
                $isFailed = true;
                array_push($errorReceipts,$receipt->narration);

            }
        }


        if($isFailed) {


            if(count($errorApproval) > 0) {
                return ['status'=>'fail','message'=>"Following receipt create successfully , but cannot approve - ".implode(',',$errorApproval ),'data' => []];
            }

            if(count($errorConfirmation) > 0) {
                return ['status'=>'fail','message'=>"Following receipt create successfully , but cannot confirm - ".implode(',',$errorConfirmation ),'data' => []];
            }

            if(count($this->financePeriodError) > 0) {
                return ['status'=>'fail','message'=>"Financial periods not found for the following receipt vouchers - ".implode(',',$this->financeYearError ),'data' => []];
            }

            if(count($this->financeYearError) > 0) {
                return ['status'=>'fail','message'=>"Following receipt vouchers not within the financial year - ".implode(',',$this->financeYearError ),'data' => []];
            }
            if(count($errorDetails) > 0) {
                return ['status'=>'fail','message'=>"Following invoice's total receipt amount is greater than invoice amount ".implode(',',$errorDetails),'data' => []];

            }

            if(count($errorReceipts) > 0) {
                return ['status'=>'fail','message'=>'Following receipt voucher/s not uploaded '.implode(',',$errorReceipts),'data' => []];
            }
        }

        return ['status'=> 'success','message' => 'Receipt voucher createad successfully!','data' => $savedReceipts];

    }

    public function buildDataToStore($input,$db):Array {
        $data = $input['data'];
        $companyID = $input['company_id'];

        $receipts = array();
        foreach ($data as $dt) {
            $receipt = new CustomerReceivePayment();
            $receipt->details = $dt['details'];
            $receipt->payeeTypeID = $dt['payeeType'];
            $receipt->payment_type_id = $dt['paymentMode'];

            $receipt = self::setCompanyDetails($companyID,$receipt); // set company details of the document
            $receipt = self::setDocumentDetails($dt,$receipt); // set document details (narration,custPaymentReceiveDate,documentIds)
            $receipt = self::setFinancialYear($dt['documentDate'],$receipt);
            $receipt = self::setBankDetails($dt['bank'],$receipt);
            $receipt = self::setCustomerDetails($dt['customer'],$receipt);
            $receipt = self::setCurrency($dt['currency'],$receipt);
            $receipt = self::setBankCurrency($dt['bankCurrency'],$receipt);
            $receipt = self::setBankAccount($dt['account'],$receipt);
            $receipt = self::setFinanicalPeriod($dt['documentDate'],$receipt);
            $receipt = self::setCurrencyDetails($receipt);
            $receipt = self::setLocalAndReportingAmounts($receipt);
            $receipt = self::setConfirmedDetails($dt,$receipt);
            $receipt = self::setApprovedDetails($dt,$receipt);

            if(isset($dt['vatApplicable'])) {
                $receipt = self::setVatDetails($dt['vatApplicable'],$receipt);
            }
            array_push($receipts,$receipt);
        }

        return $receipts;
    }

    private static function setConfirmedDetails($detail,$receipt):CustomerReceivePayment {
        $userDetails = Employee::where('empID',$detail['confirmedBy'])->first();

//        $receipt->confirmedYN = true;
        $receipt->confirmedByEmpSystemID = $userDetails->employeeSystemID;
        $receipt->confirmedByEmpID = $userDetails->empID;
        $receipt->confirmedByName = $userDetails->empFullName;
        $receipt->confirmedDate = Carbon::parse($detail['confirmedDate']);

        return $receipt;
    }
    private static function setApprovedDetails($detail,$receipt):CustomerReceivePayment {
        $userDetails = Employee::where('empID',$detail['approvedBy'])->first();
//        $receipt->approved = -1;
        $receipt->approvedByUserSystemID = $userDetails->employeeSystemID;
        $receipt->approvedByUserID = $userDetails->empID;
        $receipt->approvedDate = Carbon::parse($detail['approvedDate']);

        return $receipt;
    }
    private static function setLocalAndReportingAmounts($receipt): CustomerReceivePayment {
        switch ($receipt->documentType) {
            case 15:
            case 14 :
                $totalVatAmount = collect($receipt->details)->sum('vatAmount');
                $totalAmount = collect($receipt->details)->sum('amount');
                $totalNetAmount = ($totalAmount-$totalVatAmount);
                break;
            default;
                $totalVatAmount = 0;
                $totalAmount = 0;
                $totalNetAmount = 0;
                break;
        }
        $receipt->netAmount = $totalNetAmount;
        $receipt->VATAmount = $totalVatAmount;
        $receipt->localAmount = $totalAmount;
        $receipt->receivedAmount = $totalAmount;
        $receipt->netAmountLocal = ($receipt->localAmount / $receipt->localCurrencyER);
        $receipt->netAmountRpt = $receipt->localAmount / $receipt->companyRptCurrencyER;
        $receipt->companyRptAmount = $receipt->localAmount / $receipt->companyRptCurrencyER;
        $receipt->bankAmount = $totalAmount;
        return $receipt;
    }
    private static function setVatDetails($vatApplicable,$receipt): CustomerReceivePayment {

        $vatApplicable = strtolower($vatApplicable);
        if($vatApplicable === 'yes') {
            $receipt->isVATApplicable = true;
        }else {
            $receipt->isVATApplicable = false;
        }
        return $receipt;
    }

    private static function setCustomerDetails($customerCode,$receipt): CustomerReceivePayment
    {
        $customerDetails = CustomerMaster::where('CutomerCode',$customerCode)->first();
        $receipt->customerID = $customerDetails->customerCodeSystem;
        $receipt->customerGLCodeSystemID = $customerDetails->custGLAccountSystemID;
        $receipt->customerGLCode = $customerDetails->custGLaccount;

        return $receipt;
    }

    private static function serialCodeDetails($receipt): CustomerReceivePayment {
        $serialNo = CustomerReceivePayment::where('documentSystemID', 21)
            ->where('companySystemID', $receipt->companySystemID)
            ->where('companyFinanceYearID', $receipt->companyFinanceYearID)
            ->orderBy('serialNo', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($serialNo) {
            $lastSerialNumber = intval($serialNo->serialNo) + 1;
        }
        $CompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $receipt->companyFinanceYearID)->first();

        if(isset($CompanyFinanceYear)) {
            $y = date('Y', strtotime($CompanyFinanceYear->bigginingDate));

            $custPaymentReceiveCode = ($receipt->companyID . '\\' . $y . '\\BRV' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));

            $receipt->custPaymentReceiveCode = $custPaymentReceiveCode;
            $receipt->serialNo = $lastSerialNumber;
        }

        return $receipt;
    }

    private static function setDocumentDetails($data,$receipt) : CustomerReceivePayment {

        $receipt->custPaymentReceiveDate = $data['documentDate'];
        $receipt->narration = $data['narration'];

        switch ($data['receiptType']) {
            case 1 :
                $receipt->documentSystemID = 21;
                $receipt->documentID = "BRV";
                $receipt->documentType = 14;
                break;
            case 2 :
                $receipt->documentSystemID = 21;
                $receipt->documentID = "BRV";
                $receipt->documentType = 13;
                break;
            case 3 :
                $receipt->documentSystemID = 21;
                $receipt->documentID = "BRV";
                $receipt->documentType = 15;
                break;
            default :
                break;
        }
        return $receipt;
    }
    private static function setCurrencyDetails($receipt): CustomerReceivePayment {
        $myCurr = $receipt->custTransactionCurrencyID;

        $companyCurrencyConversion = \Helper::currencyConversion($receipt->companySystemID, $myCurr, $myCurr, 0);

        $company = Company::where('companySystemID', $receipt->companySystemID)->first();
        if ($company) {
            $receipt->localCurrencyID = $company->localCurrencyID;
            $receipt->companyRptCurrencyID = $company->reportingCurrency;
            $receipt->companyRptCurrencyER = $companyCurrencyConversion['trasToRptER'];
            $receipt->localCurrencyER = $companyCurrencyConversion['trasToLocER'];

        }
        $receipt->custTransactionCurrencyER = 1;

        return $receipt;
    }

    private static function setFinanicalPeriod($documentDate,$receipt) : CustomerReceivePayment
    {
        $financialPeriods = CompanyFinancePeriod::where('departmentSystemID',4)->where('companySystemID',$receipt->companySystemID)->where('companyFinanceYearID',$receipt->companyFinanceYearID)->get();
        foreach ($financialPeriods as $financialPeriod) {
            if(Carbon::parse($financialPeriod->dateFrom)->format('d/m/Y') == Carbon::parse($documentDate)->firstOfMonth()->format('d/m/Y')) {
                $receipt->FYPeriodDateFrom = $financialPeriod->dateFrom;
                $receipt->FYPeriodDateTo = $financialPeriod->dateTo;
                $receipt->companyFinancePeriodID = $financialPeriod->companyFinancePeriodID;
            }
        }
        return $receipt;
    }

    private static function setBankAccount($bankAccount,$receipt): CustomerReceivePayment {
        $accountDetails = BankAccount::where('AccountNo',$bankAccount)->first();
        $receipt->bankAccount = $accountDetails->bankAccountAutoID;

        return $receipt;
    }

    private static function setCurrency($currencyCode,$receipt): CustomerReceivePayment {
        $currencyDetails = CurrencyMaster::where('CurrencyCode',$currencyCode)->first();
        $receipt->custTransactionCurrencyID = $currencyDetails->currencyID;
        return $receipt;
    }
    private static function setBankCurrency($currencyCode,$receipt): CustomerReceivePayment {
        $currencyDetails = CurrencyMaster::where('CurrencyCode',$currencyCode)->first();
        $receipt->bankCurrency = $currencyDetails->currencyID;
        return $receipt;
    }

    private static function setBankDetails($bankCode,$receipt) : CustomerReceivePayment
    {
        $bankDetails = BankMaster::where('bankShortCode',$bankCode)->first();
        $receipt->bankID = $bankDetails->bankmasterAutoID;

        return $receipt;
    }


    private static function setCompanyDetails($company_id,$receipt):CustomerReceivePayment
    {

        $companyDetails = Company::select(['companySystemID','CompanyID'])->where('companySystemID',$company_id)->first();
        $receipt->companySystemID = $companyDetails->companySystemID;
        $receipt->companyID = $companyDetails->CompanyID;


        return $receipt;

    }

    private function setFinancialYear($documentDate,$receipt):CustomerReceivePayment
    {
        if(isset($documentDate)) {
            $receipt->postedDate = Carbon::parse($documentDate)->format('d-m-Y');
            $data = $this->getFinancialYear($receipt);

            if($data['success'])
            {
                $receipt->companyFinanceYearID = $data['data']['id'];
                $receipt->FYBiggin = $data['data']['from'];
                $receipt->FYEnd = $data['data']['to'];
            }else {
                $receipt->companyFinanceYearID = null;
                $receipt->FYBiggin = null;
                $receipt->FYEnd = null;
            }
        }

        return $receipt;
    }

    private function getFinancialYear($receipt): Array
    {
        $companyFinanicalYears = CompanyFinanceYear::where('companySystemID',$receipt->companySystemID)->where('isActive',-1)->get();
        foreach ($companyFinanicalYears as $companyFinanicalYear) {
            $finanicalYearFromDate = $companyFinanicalYear['bigginingDate'];
            $finanicalYearToDate =$companyFinanicalYear['endingDate'];
            if( ($finanicalYearFromDate <= $receipt->postedDate) &&  ($receipt->postedDate <= $finanicalYearToDate)) {
                return ['success' => true, 'data' => ['id' => $companyFinanicalYear->companyFinanceYearID, 'from' => $finanicalYearFromDate,'to' => $finanicalYearToDate]];
            }
        }
        return ['success' => false , 'data' => []];
    }


}
