<?php

namespace App\Services\API;

use App\helper\TaxService;
use App\Models\AccountsReceivableLedger;
use App\Models\BankAccount;
use App\Models\BankAssign;
use App\Models\BankMaster;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CurrencyMaster;
use App\Models\CustomerAssigned;
use App\Models\CustomerCurrency;
use App\Models\CustomerInvoice;
use App\Models\CustomerMaster;
use App\Models\CustomerReceivePayment;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\DocumentApproved;
use App\Models\Employee;
use App\Models\Taxdetail;
use Carbon\Carbon;

class ReceiptAPIService
{
    public $financeYearError = array();
    public $financePeriodError = array();

    public $validationErrorArray = array();

    public $isError = false;


    public function storeReceiptVoucherData($receipts,$db) {
        $savedReceipts = array();
        $errorDetails = array();

        if($this->isError) {
            return ['status'=>'fail', "code" => 422,'data' => $this->validationErrorArray];
        }

        foreach ($receipts as $receipt) {
            $receipt = self::serialCodeDetails($receipt);
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

                $receipt = self::setTaxDetails($saveReceipt);

                $confirmation = \Helper::confirmDocumentForApi($params);
                if($confirmation['success'])
                {
                    $documentApproveds = DocumentApproved::where('documentSystemCode', $saveReceipt->custReceivePaymentAutoID)->where('documentSystemID', $saveReceipt->documentSystemID)->get();
                    foreach ($documentApproveds as $documentApproved) {
                        $documentApproved["approvedComments"] = "Generated Customer Invoice through API";
                        $documentApproved["db"] = $db;
                        $documentApproved['empID'] = $receipt->approvedByUserSystemID;
                        $documentApproved['documentSystemID'] = $saveReceipt->documentSystemID;
                            $approval = \Helper::approveDocumentForApi($documentApproved);
                    }
                }
            }
        }

        return ['status'=> 'success','message' => 'Receipt voucher createad successfully!','data' => $savedReceipts];

    }

    public function buildDataToStore($input,$db):Array {
        $data = $input['data'];
        $companyID = $input['company_id'];

        $receipts = array();
        $errors = array();
        foreach ($data as $dt) {
            $receipt = new CustomerReceivePayment();
            $receiptValidationService =  array();
            $this->validationErrorArray[$dt['narration']] = [];
            $receipt->details = $dt['details'];
            $receipt->payeeTypeID = $dt['payeeType'];
            $receipt->payment_type_id = $dt['paymentMode'];


            if(isset($dt['vatApplicable'])) {
                $receipt = self::setVatDetails($dt['vatApplicable'],$receipt);
            }


            $receipt = self::setCommonValidation($dt,$receipt);
            $receipt = self::setCompanyDetails($companyID,$receipt); // set company details of the document
            $receipt = self::setDocumentDetails($dt,$receipt); // set document details (narration,custPaymentReceiveDate,documentIds)
            $receipt = self::setFinancialYear($dt['documentDate'],$receipt);
            $receipt = self::setBankDetails($dt['bank'],$receipt,$receiptValidationService);
            $receipt = self::setCustomerDetails($dt['customer'],$receipt);
            $receipt = self::setCurrency($dt['currency'],$receipt);
            $receipt = self::setBankAccount($dt['account'],$receipt,$receiptValidationService);
            $receipt = self::setBankCurrency($dt['bankCurrency'],$receipt);
            $receipt = self::setBankBalance($receipt);
            $receipt = self::setFinanicalPeriod($dt['documentDate'],$receipt);
            $receipt = self::setCurrencyDetails($receipt);
            $receipt = self::setLocalAndReportingAmounts($receipt);
            $receipt = self::setConfirmedDetails($dt,$receipt);
            $receipt = self::setApprovedDetails($dt,$receipt);

            if($receipt->documentType == 13) {
                $receipt = self::multipleInvoiceAtOneReceiptValidation($receipt);
            }


            foreach ($receipt['details'] as $details) {

                self::checkDecimalPlaces($details,$receipt);

                if($receipt->documentType == 13) {
                    self::validateInvoiceDetails($details,$receipt);
                    self::validateTotalAmount($details,$receipt);
                    self::validateDocumentDate($details,$receipt);
                }

                if($receipt->documentType == 14) {
                    self::validateGlCode($details,$receipt);
                }

                if($receipt->documentType == 14 || $receipt->documentType == 15) {
                    self::validateVatAmount($details,$receipt);
                }

            }

            array_push($receipts,$receipt);
        }

        return $receipts;
    }

    private function validateVatAmount($details,$receipt) {
        $customerDetails = CustomerMaster::where('customerCodeSystem',$receipt->customerID)->first();

        if($receipt->isVATApplicable && ($customerDetails && $customerDetails->vatEligible)) {
            if($details['vatAmount'] >= $details['amount']) {
                $this->isError = true;
                $error[$receipt->narration][$details['comments']] = ['VAT amount cannot be greater or equal than the amount'];
                array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
            }


            $countDecimals = strlen(substr(strrchr($details['vatAmount'], "."), 1));

            $currencyDetails = CurrencyMaster::where('currencyID',$receipt->custTransactionCurrencyID)->first();

            if($currencyDetails && ($countDecimals != $currencyDetails->DecimalPlaces)){
                $this->isError = true;
                $error[$receipt->narration][$details['comments']] = [$currencyDetails->CurrencyName. ' need '. $currencyDetails->DecimalPlaces .' decimal places in VAT'];;
                array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
            }
        }
    }


    private function setTaxDetails($receipt) {
        $taxDetails = Taxdetail::where('documentSystemCode', $receipt->documentSystemCode)
            ->where('documentSystemID', $receipt->documentSystemID)
            ->delete();

        if(isset($receipt->VATAmount) && $receipt->VATAmount > 0){

            if(empty(TaxService::getOutputVATGLAccount($receipt->companySystemID))) {
                $this->isError = true;
                $error[$receipt->narration] = ['Cannot confirm. Output VAT GL Account not configured.'];
                array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
            }

            if($receipt->documentType == 15 && empty(TaxService::getOutputVATTransferGLAccount($receipt->companySystemID))){
                $this->isError = true;
                $error[$receipt->narration] = ['Cannot confirm. Output VAT Transfer GL Account not configured.'];
                array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
            }

            $taxDetail['companyID'] = $receipt->companyID;
            $taxDetail['companySystemID'] = $receipt->companySystemID;
            $taxDetail['documentID'] = $receipt->documentID;
            $taxDetail['documentSystemID'] = $receipt->documentSystemID;
            $taxDetail['documentSystemCode'] = $receipt->custReceivePaymentAutoID;
            $taxDetail['documentCode'] = isset($receipt->custPaymentReceiveCode) ? $receipt->custPaymentReceiveCode : null;
            $taxDetail['taxShortCode'] = '';
            $taxDetail['taxDescription'] = '';
            $taxDetail['taxPercent'] = $receipt->VATPercentage;

            if($receipt->documentType == 15){
                $taxDetail['payeeSystemCode'] = $receipt->customerID;
                $customer = CustomerMaster::where('customerCodeSystem', $receipt->customerID)->first();

                if(!empty($customer)) {
                    $taxDetail['payeeCode'] = $customer->CutomerCode;
                    $taxDetail['payeeName'] = $customer->CustomerName;
                }else{
                    return $this->sendError('Customer not found', 500);
                }
            }else {
                $taxDetail['payeeSystemCode'] = 0;
                $taxDetail['payeeCode'] = '';
                $taxDetail['payeeName'] = '';
            }



            $taxDetail['amount'] = $receipt->VATAmount;
            $taxDetail['localCurrencyER']  = $receipt->localCurrencyER;
            $taxDetail['rptCurrencyER'] = $receipt->companyRptCurrencyER;
            $taxDetail['localAmount'] = $receipt->VATAmountLocal;
            $taxDetail['rptAmount'] = $receipt->VATAmountRpt;
            $taxDetail['currency'] =  $receipt->custTransactionCurrencyID;
            $taxDetail['currencyER'] =  1;

            $taxDetail['localCurrencyID'] =  $receipt->localCurrencyID;
            $taxDetail['rptCurrencyID'] =  $receipt->companyRptCurrencyID;
            $taxDetail['payeeDefaultCurrencyID'] =  $receipt->custTransactionCurrencyID;
            $taxDetail['payeeDefaultCurrencyER'] =  1;
            $taxDetail['payeeDefaultAmount'] =  $receipt->VATAmount;

            $tax = Taxdetail::create($taxDetail);

        }


        return $receipt;
    }

    private function checkDecimalPlaces($detail,$receipt) {
        if($receipt->documentType != 13) {
            $countDecimals = strlen(substr(strrchr($detail['amount'], "."), 1));
        }else {
            $countDecimals = strlen(substr(strrchr($detail['receiptAmount'], "."), 1));
        }

        $currencyDetails = CurrencyMaster::where('currencyID',$receipt->custTransactionCurrencyID)->first();

        if($currencyDetails && $countDecimals != $currencyDetails->DecimalPlaces){
            switch ($receipt->documentType) {
                case 13 :
                    $this->isError = true;
                    $error[$receipt->narration][$detail['invoiceCode']] = [$currencyDetails->CurrencyName. ' need '. $currencyDetails->DecimalPlaces .' decimal places'];
                    array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
                    break;
                case 14:
                case 15:
                    $this->isError = true;
                    $error[$receipt->narration][$detail['comments']] = [$currencyDetails->CurrencyName. ' need '. $currencyDetails->DecimalPlaces .' decimal places'];
                    array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
                    break;
            }

        }
    }

    private function validateGlCode($detail,$receipt) {
        $chartOfAccountDetails = ChartOfAccount::where('AccountCode',$detail['glCode'])->where('controllAccountYN', 0)->first();

        if(!$chartOfAccountDetails) {
            $this->isError = true;
            $error[$receipt->narration][$detail['glCode']] = ['GL Account not found'];
            array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
        }else {
            if(!$chartOfAccountDetails->isApproved) {
                $this->isError = true;
                $error[$receipt->narration][$detail['glCode']] = ['GL Account is not approved'];
                array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
            }

            if(!$chartOfAccountDetails->isActive) {
                $this->isError = true;
                $error[$receipt->narration][$detail['glCode']] = ['GL Account is not active'];
                array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
            }


            $chartOfAccountAssigned = ChartOfAccountsAssigned::where('chartOfAccountSystemID',$chartOfAccountDetails->chartOfAccountSystemID)->where('companySystemID',$receipt->companySystemID)->where('isAssigned',-1)->get();
            if(count($chartOfAccountAssigned) == 0) {
                $this->isError = true;
                $error[$receipt->narration][$detail['glCode']] = ['GL Account is not assigned to the company'];
                array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
            }


        }




    }
    private function validateDocumentDate($details,$receipt) {
        if($receipt->documentType == 13) {
            $invCode = $details['invoiceCode'];
            $invoice = CustomerInvoice::where('bookingInvCode',$invCode)->first();
            if($invoice) {
                if($receipt->postedDate < Carbon::parse($invoice->postedDate)) {
                    $this->isError = true;
                    $error[$receipt->narration][$details['invoiceCode']] = ['Document date of a customer invoice receipt voucher should not be lesser than the invoice dates of customer invoices pulled'];
                    array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);

                }
            }
        }
    }

    private function validateTotalAmount($details,$receipt) {
        if($receipt->documentType == 13) {
            $invCode = $details['invoiceCode'];
            $invoice = CustomerInvoice::where('bookingInvCode',$invCode)->first();
            if(!$invoice) {
                $this->isError = true;
                $error[$receipt->narration][$details['invoiceCode']] = ['Invoice data not found'];
                array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
            }else {
                $accountReceivableLedgerDetails = AccountsReceivableLedger::where('documentCodeSystem',$invoice->custInvoiceDirectAutoID)->first();
                if($accountReceivableLedgerDetails) {
                    $totalAmountReceived = CustomerReceivePaymentDetail::where('arAutoID',$accountReceivableLedgerDetails->arAutoID)->sum('receiveAmountTrans');
                    $bookingAmountTrans = $invoice->bookingAmountTrans + $invoice->VATAmount;
                    if(($totalAmountReceived+$details['receiptAmount']) > $bookingAmountTrans) {
                        $this->isError = true;
                        $error[$receipt->narration][$details['invoiceCode']] = ['Total received amount cannot be greater the invoice amount'];
                        array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
                    }
                }
            }

        }
    }

    private function multipleInvoiceAtOneReceiptValidation($receipt) {

        $groupByInvoiceCode = collect($receipt->details)->groupBy('invoiceCode');

        foreach ($groupByInvoiceCode as $gp) {
            if(count($gp) > 1) {
                $this->isError = true;
                $error[$receipt->narration][$gp[0]['invoiceCode']] = ['Receipt voucher cannot have same invoice more than one time'];
                array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
            }
        }
        return $receipt;
    }
    private function setBankBalance($receipt) {
        $cur_det['companySystemID'] = $receipt->companySystemID;
        $cur_det['bankmasterAutoID'] = $receipt->bankID;
        $cur_det['bankAccountAutoID'] = $receipt->bankAccount;
        $cur_det_info =  (object)$cur_det;
        $document_currency = $receipt->custTransactionCurrencyID;

        $bankBalance = app('App\Http\Controllers\API\BankAccountAPIController')->getBankAccountBalanceSummery($cur_det_info);

        $amount = $bankBalance['netBankBalance'];

        $currencies = CurrencyMaster::where('currencyID','=',$document_currency)->select('DecimalPlaces')->first();

        if(isset($currencies)) {
            $rounded_amount =  number_format($amount,$currencies->DecimalPlaces,'.', '');
            $receipt->bankAccountBalance = $rounded_amount;
        }else {
            $receipt->bankAccountBalance = $amount;
        }


        return $receipt;
    }

    private function setCommonValidation($input,$receipt) {
        if($input['receiptType'] <= 0 || $input['receiptType'] > 3) {
            $this->isError = true;
            $error[$input['narration']] = ['Receipt type not found'];
            array_push($this->validationErrorArray[$input['narration']],$error[$input['narration']]);
        }

        if($input['paymentMode'] <= 0 || $input['paymentMode'] > 4) {
            $this->isError = true;
            $error[$input['narration']] = ['Payment mode not found'];
            array_push($this->validationErrorArray[$input['narration']],$error[$input['narration']]);
        }

        if($input['payeeType'] <= 0 || $input['payeeType'] > 3) {
            $this->isError = true;
            $error[$input['narration']] = ['Payee type not found'];
            array_push($this->validationErrorArray[$input['narration']],$error[$input['narration']]);
        }

        return $receipt;
    }

    private function validateInvoiceDetails($details,$receipt) {
        if($receipt->documentType == 13) {
            $invoice = CustomerInvoice::where('bookingInvCode',$details['invoiceCode'])->first();
            if(!$invoice) {
//                $this->isError = true;
//                $error[$receipt->narration][$details['invoiceCode']] = ['Invoice data not found'];
//                array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);

            }else {
                if($invoice->customerID != $receipt->customerID) {
                    $this->isError = true;
                    $error[$receipt->narration][$details['invoiceCode']] = ['Invoice is not related to the customer you provided'];
                    array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
                }
            }
        }


    }
    private function setConfirmedDetails($detail,$receipt):CustomerReceivePayment {
        $userDetails = Employee::where('empID',$detail['confirmedBy'])->first();

        if(Carbon::parse($detail['documentDate']) > Carbon::parse($detail['confirmedDate'])) {
            $this->isError = true;
            $error[$receipt->narration] = ['Confirmed date should greater than document date'];
            array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
        }

        if(!$userDetails) {
            $this->isError = true;
            $error[$receipt->narration] = ['Confirmed By employee data not found'];
            array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);

        }else {
            $receipt->confirmedByEmpSystemID = $userDetails->employeeSystemID;
            $receipt->confirmedByEmpID = $userDetails->empID;
            $receipt->confirmedByName = $userDetails->empFullName;
            $receipt->confirmedDate = Carbon::parse($detail['confirmedDate']);
        }


        return $receipt;
    }
    private function setApprovedDetails($detail,$receipt):CustomerReceivePayment {
        $userDetails = Employee::where('empID',$detail['approvedBy'])->first();

        if(Carbon::parse($detail['confirmedDate']) > Carbon::parse($detail['approvedDate'])) {
            $this->isError = true;
            $error[$receipt->narration] = ['Approved date should greater than confirmed date'];
            array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
        }

        if(!$userDetails) {
            $this->isError = true;
            $error[$receipt->narration] = ['Approved By employee data not found'];
            array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);

        }else {
            $receipt->approvedByUserSystemID = $userDetails->employeeSystemID;
            $receipt->approvedByUserID = $userDetails->empID;
            $receipt->approvedDate = Carbon::parse($detail['approvedDate']);
        }

        return $receipt;
    }
    private static function setLocalAndReportingAmounts($receipt): CustomerReceivePayment {

        $myCurr = $receipt->custTransactionCurrencyID;
        $customerDetails = CustomerMaster::where('customerCodeSystem',$receipt->customerID)->first();
        $currencyDetails = CurrencyMaster::where('currencyID',$myCurr)->first();
        $companyData = Company::with(['localcurrency', 'reportingcurrency'])->where('companySystemID', $receipt->companySystemID)
            ->first();

        switch ($receipt->documentType) {
            case 15:
            case 14 :
                $totalVatAmount = (($customerDetails && $customerDetails->vatEligible) && $receipt->isVATApplicable) ? collect($receipt->details)->sum('vatAmount') : 0;
                $totalAmount = collect($receipt->details)->sum('amount');
                $totalNetAmount = ($totalAmount-$totalVatAmount);
                break;
            case 13 :
                $totalVatAmount = (($customerDetails && $customerDetails->vatEligible) && $receipt->isVATApplicable) ? collect($receipt->details)->sum('vatAmount') : 0;
                $totalAmount = collect($receipt->details)->sum('receiptAmount');
                $totalNetAmount = ($totalAmount-$totalVatAmount);
                break;
            default;
                $totalVatAmount = 0;
                $totalAmount = 0;
                $totalNetAmount = 0;
                break;
        }

        if($currencyDetails) {
            $companyCurrencyConversionTrans = \Helper::currencyConversion($receipt->companySystemID, $myCurr, $myCurr, $totalAmount);
            $companyCurrencyConversionVat = \Helper::currencyConversion($receipt->companySystemID, $myCurr, $myCurr, $totalVatAmount);
            $companyCurrencyConversionNet = \Helper::currencyConversion($receipt->companySystemID, $myCurr, $myCurr, $totalNetAmount);
            $bankCurrencyConversion = \Helper::currencyConversion($receipt->companySystemID, $myCurr, $receipt->bankCurrency, $totalAmount);

            $receipt->localAmount = \Helper::roundValue($companyCurrencyConversionTrans['localAmount'])  * -1;
            $receipt->receivedAmount = $totalAmount  * -1;
            $receipt->VATAmount = $totalVatAmount;
            $receipt->VATPercentage = ($totalVatAmount / 100);
            $receipt->VATAmountLocal =  $companyCurrencyConversionVat['localAmount'];
            $receipt->VATAmountRpt = $companyCurrencyConversionVat['reportingAmount'];
            $receipt->netAmount = $totalNetAmount;
            $receipt->netAmountLocal = $companyCurrencyConversionNet['localAmount'];
            $receipt->netAmountRpt = $companyCurrencyConversionNet['reportingAmount'];
            $receipt->companyRptAmount = \Helper::roundValue($companyCurrencyConversionTrans['reportingAmount'])  * -1;
            $receipt->bankCurrencyER = $bankCurrencyConversion['transToDocER'];

            if ($receipt->custTransactionCurrencyID == $companyData->localCurrencyID) {
                $receipt->bankAmount = \Helper::roundValue($totalAmount) * -1;
            } else {
                $receipt->bankAmount = \Helper::roundValue($bankCurrencyConversion['documentAmount']) * -1;
            }


        }


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

    private function setCustomerDetails($customerCode,$receipt): CustomerReceivePayment
    {

        $customerDetails = CustomerMaster::where('CutomerCode',$customerCode)->orWhere('customer_registration_no',$customerCode)->first();

        if (!$customerDetails) {
            $errorMessage = 'Customer data not found';
        } elseif (!$customerDetails->isCustomerActive) {
            $errorMessage = 'Customer is not active';
        } else {
            $customerAssigned = CustomerAssigned::where('companySystemID', $receipt->companySystemID)
                ->where('customerCodeSystem', $customerDetails->customerCodeSystem)
                ->where('isAssigned', -1)
                ->first();

            if (!$customerAssigned) {
                $errorMessage = 'Customer is not assigned to the company';
            } else {
                $receipt->customerID = $customerDetails->customerCodeSystem;
                $receipt->customerGLCodeSystemID = $customerDetails->custGLAccountSystemID;
                $receipt->customerGLCode = $customerDetails->custGLaccount;
                $receipt->custAdvanceAccountSystemID = $customerDetails->custGLAccountSystemID;
                $receipt->custAdvanceAccount = $customerDetails->custGLaccount;
            }
        }

        if (isset($errorMessage)) {
            $this->isError = true;
            $error[$receipt->narration] = [$errorMessage];
            $this->validationErrorArray[$receipt->narration] = $error[$receipt->narration];
        }

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
                //direct receipt
                $receipt->documentSystemID = 21;
                $receipt->documentID = "BRV";
                $receipt->documentType = 14;
                break;
            case 2 :
                //customer invoice receipt
                $receipt->documentSystemID = 21;
                $receipt->documentID = "BRV";
                $receipt->documentType = 13;
                break;
            case 3 :
                //advance receipt
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

    private function setFinanicalPeriod($documentDate,$receipt) : CustomerReceivePayment
    {
        $financialPeriods = CompanyFinancePeriod::where('departmentSystemID',4)->where('companySystemID',$receipt->companySystemID)->where('companyFinanceYearID',$receipt->companyFinanceYearID)->get();
        if(count($financialPeriods) == 0) {
            $this->isError = true;
            $error[$receipt->narration] = ['Financial Period not found'];
            array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
        }else {
            foreach ($financialPeriods as $financialPeriod) {
                if(Carbon::parse($financialPeriod->dateFrom)->format('d/m/Y') == Carbon::parse($documentDate)->firstOfMonth()->format('d/m/Y')) {
                    if($financialPeriod->isActive == 0) {
                        $this->isError = true;
                        $error[$receipt->narration] = ['Financial Period should be active'];
                        array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
                    }else {
                        $receipt->FYPeriodDateFrom = $financialPeriod->dateFrom;
                        $receipt->FYPeriodDateTo = $financialPeriod->dateTo;
                        $receipt->companyFinancePeriodID = $financialPeriod->companyFinancePeriodID;
                    }

                }
            }
        }

        return $receipt;
    }

    private function setBankAccount($bankAccount,$receipt,$receiptValidationService): CustomerReceivePayment {
        $accountDetails = BankAccount::where('AccountNo',$bankAccount)->where('bankmasterAutoID',$receipt->bankID)->first();
        if(!$accountDetails) {
            $this->isError = true;
            $error[$receipt->narration] = ['Bank Account is not related to the bank you provided'];
            array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
            $receipt->bankAccount = null;
        }else {
            if(!$accountDetails->approvedYN) {
                $this->isError = true;
                $error[$receipt->narration] = ['Bank account is not fully approved'];
                array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
            }

            if(!$accountDetails->isAccountActive) {
                $this->isError = true;
                $error[$receipt->narration] = ['Bank account is not active'];
                array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
            }

            $receipt->bankAccount = $accountDetails->bankAccountAutoID;
        }

        return $receipt;
    }

    private function setCurrency($currencyCode,$receipt): CustomerReceivePayment {
        $currencyDetails = CurrencyMaster::where('CurrencyCode',$currencyCode)->first();

        if(!$currencyDetails) {
            $this->isError = true;
            $error[$receipt->narration] = ['Currency data not found'];
            array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
        }else {
            $receipt->custTransactionCurrencyID = $currencyDetails->currencyID;
            $receipt->DecimalPlaces = $currencyDetails->DecimalPlaces;
            $this->checkCurrencyAssignedToCustomer($receipt);
        }

        return $receipt;
    }

    private function checkCurrencyAssignedToCustomer($receipt) {
        if(isset($receipt->customerID)) {
            $customerCurrencyDetails = CustomerCurrency::where('currencyID',$receipt->custTransactionCurrencyID)->where('customerCodeSystem',$receipt->customerID)->where('isAssigned',-1)->first();
            if(!$customerCurrencyDetails) {
                $this->isError = true;
                $error[$receipt->narration] = ['Currency is not assigned to the customer'];
                array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
            }
        }
    }

    private function setBankCurrency($currencyCode,$receipt): CustomerReceivePayment {
        $currencyDetails = CurrencyMaster::where('CurrencyCode',$currencyCode)->first();


        if(!$currencyDetails) {
            $this->isError = true;
            $error[$receipt->narration] = ['Currency data not found'];
            array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
        }else {
            $receipt->bankCurrency = $currencyDetails->currencyID;
            $this->checkCurrencyAssignedToBankAccount($receipt);
        }
        return $receipt;
    }


    private function checkCurrencyAssignedToBankAccount($receipt) {
        if(isset($receipt->bankAccount)) {
            $bankAccount = BankAccount::where('bankAccountAutoID',$receipt->bankAccount)->first();
            if($bankAccount->accountCurrencyID != $receipt->bankCurrency) {
                $this->isError = true;
                $error[$receipt->narration] = ['Bank Currency is not assigned to the bank account'];
                array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
            }
        }
    }

    private function setBankDetails($bankCode,$receipt,$receiptValidationService) : CustomerReceivePayment
    {
        $bankDetails = BankMaster::where('bankShortCode',$bankCode)->first();

        if(!$bankDetails) {
            $this->isError = true;
            $error[$receipt->narration] = ['Bank data not found'];
            array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);

        }else {
            $bankAssigned = BankAssign::where('bankmasterAutoID',$bankDetails->bankmasterAutoID)->where('companySystemID',$receipt->companySystemID)->where('isAssigned',-1)->where('isActive',1)->first();

            if(!$bankAssigned) {
                $this->isError = true;
                $error[$receipt->narration] = ['Bank is not assigned/active to the company'];
                array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
            }else {
                $receipt->bankID = $bankDetails->bankmasterAutoID;
            }


        }


        return $receipt;
    }


    private function setCompanyDetails($company_id,$receipt):CustomerReceivePayment
    {

        $companyDetails = Company::select(['companySystemID','CompanyID'])->where('companySystemID',$company_id)->first();
        if(!$companyDetails) {
            $this->isError = true;
            $error[$receipt->narration] = ['Company details not found'];
            array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);

        }

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
                $this->isError = true;
                $error[$receipt->narration] = ['Financial Year not found'];
                array_push($this->validationErrorArray[$receipt->narration],$error[$receipt->narration]);
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
            }else {
            }
        }
        return ['success' => false , 'data' => []];
    }


}
