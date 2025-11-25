<?php

namespace App\Services\API;

use App\helper\Helper;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CreditNote;
use App\Models\CustomerAssigned;
use App\Models\CustomerCurrency;
use App\Models\CustomerMasterCategory;
use App\Models\CustomerInvoice;
use App\Models\CustomerMaster;
use App\Models\CustomerReceivePayment;
use App\Models\DeliveryOrder;
use App\Models\DocumentMaster;
use App\Models\ItemIssueMaster;
use App\Models\MatchDocumentMaster;
use App\Models\QuotationMaster;
use App\Models\SalesReturn;
use App\Services\DocumentAutoApproveService;
use App\Services\UserTypeService;
use App\Traits\AuditLogsTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ChartOfAccountsAssigned;
use App\Models\CountryMaster;
use App\Models\CurrencyMaster;
use App\Models\CustomerContactDetails;
use App\Models\SupplierContactType;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CustomerMasterAPIService
{
    use AuditLogsTrait;

    public static function validateCustomerMasterData($data): array {

        if($data['custGLAccountSystemID'] == $data['custUnbilledAccountSystemID'] ){
            return [
                'status' => false,
                'message' => "Receivable account and unbilled account cannot be same. Please select different chart of accounts."
            ];
        }

        if($data['custUnbilledAccountSystemID'] == 0){
            return [
                'status' => false,
                'message' => "Unbilled Receivable Account field is required."
            ];
        }


        if($data['custAdvanceAccountSystemID'] == 0){
            return [
                'status' => false,
                'message' => "Advance Account field is required."
            ];
        }
        else{

            $companyID = null;
            if(isset($data['primaryCompanySystemID'])) {
                $companyID = $data['primaryCompanySystemID'];
            }
            elseif (isset($data['company_id'])) {
                $companyID = $data['company_id'];
            }
            else {
                return [
                    'status' => false,
                    'message' => "Company ID is required."
                ];
            }

            $chartOfAccount = ChartOfAccount::where('chartOfAccountSystemID', $data['custAdvanceAccountSystemID'])
                ->where('controllAccountYN', '=', 1)
                ->whereHas('chartofaccount_assigned', function($query) use ($companyID) {
                    $query->where('companySystemID', $companyID)
                        ->where('isAssigned', -1)
                        ->where('isActive', 1);
                })
                ->where(function($q){
                    $q->where('controlAccountsSystemID',3)
                        ->orWhere('controlAccountsSystemID',4)
                        ->orWhere('controlAccountsSystemID',5);
                })
                ->where('catogaryBLorPL', '=', 'BS')
                ->where('isApproved',1)
                ->where('isActive',1)
                ->exists();

            if(!$chartOfAccount){
                return [
                    'status' => false,
                    'message' => "Advance Account is not valid."
                ];
            }
        }

        $validatorResult = Helper::checkCompanyForMasters($data['primaryCompanySystemID']);
        if (!$validatorResult['success']) {
            return [
                'status' => false,
                'message' => $validatorResult['message']
            ];
        }

        if($data['customerCountry'] == 0 || $data['customerCountry'] == ''){
            return [
                'status' => false,
                'message' => "Country field is required",
                'code' => 500
            ];
        }

        return ['status' => true];
    }

    public static function setCustomerMasterData($data) {
        $company = Company::where('companySystemID', $data['primaryCompanySystemID'])->first();

        if ($company) {
            $data['primaryCompanyID'] = $company->CompanyID;
        }

        if (array_key_exists('custGLAccountSystemID', $data)) {
            $financePL = ChartOfAccount::where('chartOfAccountSystemID', $data['custGLAccountSystemID'])->first();
            if ($financePL) {
                $data['custGLaccount'] = $financePL->AccountCode;
            }
        }

        if (array_key_exists('custUnbilledAccountSystemID', $data)) {
            $unbilled = ChartOfAccount::where('chartOfAccountSystemID', $data['custUnbilledAccountSystemID'])->first();
            if ($unbilled) {
                $data['custUnbilledAccount'] = $unbilled->AccountCode;
            }
        }

        if (array_key_exists('custAdvanceAccountSystemID', $data)) {
            $unbilled = ChartOfAccount::where('chartOfAccountSystemID', $data['custAdvanceAccountSystemID'])->first();
            if ($unbilled) {
                $data['custAdvanceAccount'] = $unbilled->AccountCode;
            }
        }

        return $data;
    }

    public static function updateCustomerMaster($input): array {
        if(isset($input['isAutoCreateDocument']) && !$input['isAutoCreateDocument']) {
            $db = $input['db'];
            $uuid = $input['tenant_uuid'];

            if(isset($input['tenant_uuid']) ){
                unset($input['tenant_uuid']);
            }

            if(isset($input['db']) ){
                unset($input['db']);
            }

            if($input['isCustomerActive'] == 0) {
                $Quatation = [];
                $Delivery = [];
                $ItemIssue = [];
                $MatDoc = [];
                $Recived = [];
                $Credit = [];
                $CustomerInvoice = [];
                $SalesReturn = [];

                $quatation = QuotationMaster::where('customerSystemCode',$input['customerCodeSystem'])->where('approvedYN',0);

                if($quatation->count() > 0)
                {
                    $Quatation =  $quatation->pluck('quotationCode')->toArray();
                }

                $delivery = DeliveryOrder::where('customerID',$input['customerCodeSystem'])->where('approvedYN',0);

                if($delivery->count() > 0)
                {
                    $Delivery =  $delivery->pluck('deliveryOrderCode')->toArray();
                }

                $salesReturn = SalesReturn::where('customerID',$input['customerCodeSystem'])->where('approvedYN',0);

                if($salesReturn->count() > 0)
                {
                    $SalesReturn =  $salesReturn->pluck('salesReturnCode')->toArray();
                }

                $customerInvoice = CustomerInvoice::where('customerID',$input['customerCodeSystem'])->where('approved',0);

                if($customerInvoice->count() > 0)
                {
                    $CustomerInvoice =  $customerInvoice->pluck('bookingInvCode')->toArray();
                }

                $credit = CreditNote::where('customerID', $input['customerCodeSystem'])->where('approved',0);

                if($credit->count() > 0)
                {
                    $Credit =  $credit->pluck('creditNoteCode')->toArray();
                }

                $recived = CustomerReceivePayment::where('customerID', $input['customerCodeSystem'])->where('approved',0);

                if($recived->count() > 0)
                {
                    $Recived =  $recived->pluck('custPaymentReceiveCode')->toArray();
                }

                $matDoc = MatchDocumentMaster::where('BPVsupplierID',$input['customerCodeSystem'])->where('approved',0);

                if($matDoc->count() > 0)
                {
                    $MatDoc =  $matDoc->pluck('matchingDocCode')->toArray();
                }

                $itemIssue = ItemIssueMaster::where('customerSystemID',$input['customerCodeSystem'])->where('approved',0);

                if($itemIssue->count() > 0)
                {
                    $ItemIssue =  $itemIssue->pluck('itemIssueCode')->toArray();
                }

                $mergedArray = array_merge($Quatation,$Delivery,$ItemIssue, $MatDoc, $Recived,$Credit,$CustomerInvoice,$SalesReturn);
                if(count($mergedArray) > 0) {
                    return [
                        'status' => false,
                        'message' => "The selected customer has already been pulled into the document.",
                        'code' => 500,
                        'type' => ['type' => 'customerBlock','data' =>$mergedArray],
                    ];
                }
            }

            $empId = $input['empID'];
            if(isset($input['empID'])) {
                unset($input['empID']);
            }
        }
        else {
            $systemUser = UserTypeService::getSystemEmployee();
            $empId = $systemUser->empID;
        }

        $customerMaster = CustomerMaster::where('customerCodeSystem', $input['customerCodeSystem'])->first();

        if(empty($customerMaster)) {
            return [
                'status' => false,
                'message' => "customer not found",
            ];
        }

        if(isset($input['isAutoCreateDocument']) && !$input['isAutoCreateDocument']) {
            if($customerMaster->approvedYN) {

                $customerMasterOld = $customerMaster->toArray();

                $employee = Helper::getEmployeeInfo();

                $policy = Helper::checkRestrictionByPolicy($input['primaryCompanySystemID'],5);

                $customerId = $customerMaster->customerCodeSystem;

                if($policy){
                    $validorMessages = [
                        'creditDays.required' => 'Credit Period field is required.',
                        'creditDays.numeric' => 'Credit Period field is required.'
                    ];

                    $validator = \Validator::make($input, [
                        'creditDays' => 'required|numeric',
                    ],$validorMessages);

                    if ($validator->fails()) {
                        return [
                            'status' => false,
                            'message' => $validator->messages(),
                            'code' => 422
                        ];
                    }

                    $previousValue = $customerMaster->toArray();
                    $newValue = $input;

                    $customerMaster = CustomerMaster::where('customerCodeSystem',$customerId)->update(array_only($input,['customer_registration_expiry_date','customer_registration_no','creditLimit','creditDays','consignee_address','consignee_contact_no','consignee_name','payment_terms','vatEligible','vatNumber','vatPercentage', 'customerSecondLanguage', 'reportTitleSecondLanguage', 'addressOneSecondLanguage', 'addressTwoSecondLanguage','customerShortCode','CustomerName','ReportTitle','customerAddress1','customerAddress2','customerCategoryID','interCompanyYN','customerCountry','customerCity','isCustomerActive','custGLAccountSystemID','custUnbilledAccountSystemID', 'companyLinkedToSystemID', 'companyLinkedTo','custAdvanceAccountSystemID','custAdvanceAccount']));
                    CustomerAssigned::where('customerCodeSystem',$customerId)->update(array_only($input,['creditLimit','creditDays','consignee_address','consignee_contact_no','consignee_name','payment_terms','vatEligible','vatNumber','vatPercentage','customerShortCode','CustomerName','ReportTitle','customerAddress1','customerAddress2','customerCategoryID','customerCountry','customerCity','custGLAccountSystemID','custUnbilledAccountSystemID','custAdvanceAccountSystemID','custAdvanceAccount']));

                    if($customerMaster){
                        $old_array = array_only($customerMasterOld,['creditDays','vatEligible','vatNumber','vatPercentage', 'customerSecondLanguage', 'reportTitleSecondLanguage', 'addressOneSecondLanguage', 'addressTwoSecondLanguage']);
                        $modified_array = array_only($input,['creditDays','vatEligible','vatNumber','vatPercentage', 'customerSecondLanguage', 'reportTitleSecondLanguage', 'addressOneSecondLanguage', 'addressTwoSecondLanguage']);

                        foreach ($old_array as $key => $old){
                            if($old != $modified_array[$key]){
                                $description = $employee->empName." Updated customer (".$customerMaster->CutomerCode.") from ".$old." To ".$modified_array[$key]."";
                            }
                        }
                    }

                    $narrationVariables = $input['CutomerCode'];
                    self::auditLog($db, $input['customerCodeSystem'],$uuid, "customermaster", $narrationVariables, "U", $newValue, $previousValue);

                    return [
                        'status' => true,
                        'message' => 'Customer Master updated successfully',
                        'data' => $customerMaster
                    ];
                }

                return [
                    'status' => false,
                    'message' => 'Customer Master is already approved , You cannot update.',
                    'code' => 500
                ];
            }
        }

        if($customerMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {
            $params = array(
                'autoID' => $input['customerCodeSystem'],
                'company' => $input["primaryCompanySystemID"],
                'document' => $input["documentSystemID"],
                'isAutoCreateDocument' => $input['isAutoCreateDocument']
            );

            $confirm = Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return [
                    'status' => false,
                    'message' => $confirm["message"],
                    'code' => 500
                ];
            }
        }

        if(isset($input['isAutoCreateDocument']) ){
            unset($input['isAutoCreateDocument']);
        }

        foreach ($input as $key => $value) {
            $customerMaster->$key = $value;
        }

        $customerMaster->modifiedPc = gethostname();
        $customerMaster->modifiedUser = $empId;
        $customerMaster->save();

        return [
            'status' => true,
            'message' => 'Customer Master saved successfully',
            'data' => $customerMaster
        ];
    }

    public static function storeCustomerMasterFromAPI($data): array
    {
        $db = $data['db'] ?? "";

        DB::beginTransaction();

        try{
            if(isset($data['isAutoCreateDocument']) && $data['isAutoCreateDocument']) {
                $validation = CustomerMasterAPIService::validateCustomerMasterData($data);
                if(!$validation['status']) {
                    return [
                        'status' => false,
                        'message' => $validation['message'],
                        'code' => isset($validation['code']) ? 500 : 404
                    ];
                }

                $data = CustomerMasterAPIService::setCustomerMasterData($data);

                $systemUser = UserTypeService::getSystemEmployee();

                $data['createdUserID'] = $systemUser->empID;
                $data['createdPcID'] = gethostname();
            }

            $document = DocumentMaster::where('documentID', 'CUSTM')->first();
            $data['documentSystemID'] = $document->documentSystemID;
            $data['documentID'] = $document->documentID;

            $lastCustomer = CustomerMaster::orderBy('customerCodeSystem', 'DESC')->first();
            $lastSerialOrder = 1;
            if(!empty($lastCustomer)) {
                $lastSerialOrder = $lastCustomer->lastSerialOrder + 1;
            }

            $customerCode = 'C' . str_pad($lastSerialOrder, 7, '0', STR_PAD_LEFT);

            $data['lastSerialOrder'] = $lastSerialOrder;
            $data['CutomerCode'] = $customerCode;
            $data['isCustomerActive'] = 1;

            $customerMaster = CustomerMaster::create($data);

            if(isset($data['isAutoCreateDocument']) && $data['isAutoCreateDocument']) {

                if (isset($data['currencyDetails']) && is_array($data['currencyDetails']) && !empty($data['currencyDetails'])) {
                    $defaultIndex = null;
                    foreach ($data['currencyDetails'] as $index => $currency) {
                        if (isset($currency['isDefault']) && $currency['isDefault'] == -1) {
                            if ($defaultIndex === null) {
                                $defaultIndex = $index;
                            } else {
                                $data['currencyDetails'][$index]['isDefault'] = 0;
                            }
                        }
                    }
                    
                    if ($defaultIndex !== null) {
                        CustomerCurrency::where('customerCodeSystem', $customerMaster->customerCodeSystem)
                            ->where('isDefault', -1)
                            ->update(['isDefault' => 0]);
                    }
                    
                    foreach ($data['currencyDetails'] as $currency) {
                        if (isset($currency['currencyID'])) {
                            CustomerCurrency::create([
                                'customerCodeSystem' => $customerMaster->customerCodeSystem,
                                'customerCode' => $customerMaster->CutomerCode,
                                'currencyID' => $currency['currencyID'],
                                'isDefault' => $currency['isDefault'] ?? 0,
                                'isAssigned' => -1,
                                'createdBy' => $systemUser->empID
                            ]);
                        }
                    }
                } else {
                    CustomerCurrency::create(['customerCodeSystem' => $customerMaster->customerCodeSystem,
                        'customerCode' => $customerMaster->CutomerCode,
                        'currencyID' => 1,
                        'isDefault' => -1,
                        'isAssigned' => -1,
                        'createdBy' => $systemUser->empID
                    ]);
                }

                $customerMaster['confirmedYN'] = 1;
                $customerMaster['isAutoCreateDocument'] = true;

                $updateCustomerMaster = CustomerMasterAPIService::updateCustomerMaster($customerMaster);

                if(!$updateCustomerMaster['status']) {
                    DB::rollback();
                    return [
                        'status' => false,
                        'message' => $updateCustomerMaster['message'],
                        'code' => $updateCustomerMaster['code'] ?? 404
                    ];
                }

                $autoApproveParams = DocumentAutoApproveService::getAutoApproveParams($updateCustomerMaster['data']->documentSystemID,$updateCustomerMaster['data']->customerCodeSystem);
                $autoApproveParams['db'] = $db;

                $approveDocument = Helper::approveDocument($autoApproveParams);
                if (!$approveDocument["success"]) {
                    DB::rollBack();
                    return [
                        'status' => false,
                        'message' => $approveDocument['message']
                    ];
                }

                DB::commit();
                return [
                    'status' => true,
                    'data' => $customerMaster->refresh(),
                    'message' => 'Customer Master created successfully'
                ];
            }

            DB::commit();
            return [
                'status' => true,
                'data' => $customerMaster->refresh(),
                'message' => 'Customer Master saved successfully'
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::info('Error Line No: ' . $e->getLine());
            Log::info('Error File: ' . $e->getFile());
            Log::info($e->getMessage());
            Log::info('---- GL  End with Error-----' . date('H:i:s'));
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'code' => 500
            ];
        }
    }

    public static function createCustomerMasterFromAPI($data, $manageTransaction = true): array {
        $systemUser = UserTypeService::getSystemEmployee();
        $db = $data['db'] ?? "";

        $document = DocumentMaster::where('documentID', 'CUSTM')->first();
        $data['documentSystemID'] = $document->documentSystemID;
        $data['documentID'] = $document->documentID;
        $data['createdUserID'] = $systemUser->empID;
        $data['createdPcID'] = gethostname();

        $lastCustomer = CustomerMaster::orderBy('customerCodeSystem', 'DESC')->first();
        $lastSerialOrder = 1;
        if(!empty($lastCustomer)) {
            $lastSerialOrder = $lastCustomer->lastSerialOrder + 1;
        }

        $customerCode = 'C' . str_pad($lastSerialOrder, 7, '0', STR_PAD_LEFT);

        $data['lastSerialOrder'] = $lastSerialOrder;
        $data['CutomerCode'] = $customerCode;
        $data['isCustomerActive'] = 1;

        $customerMaster = CustomerMaster::create($data);

        if (isset($data['currencyDetails']) && is_array($data['currencyDetails']) && !empty($data['currencyDetails'])) {
            $defaultIndex = null;
            foreach ($data['currencyDetails'] as $index => $currency) {
                if (isset($currency['isDefault']) && $currency['isDefault'] == -1) {
                    if ($defaultIndex === null) {
                        $defaultIndex = $index;
                    } else {
                        $data['currencyDetails'][$index]['isDefault'] = 0;
                    }
                }
            }
            
            if ($defaultIndex !== null) {
                CustomerCurrency::where('customerCodeSystem', $customerMaster->customerCodeSystem)
                    ->where('isDefault', -1)
                    ->update(['isDefault' => 0]);
            }
            
            foreach ($data['currencyDetails'] as $currency) {
                if (isset($currency['currencyID'])) {
                    CustomerCurrency::create([
                        'customerCodeSystem' => $customerMaster->customerCodeSystem,
                        'customerCode' => $customerMaster->CutomerCode,
                        'currencyID' => $currency['currencyID'],
                        'isDefault' => $currency['isDefault'] ?? 0,
                        'isAssigned' => -1,
                        'createdBy' => $systemUser->empID
                    ]);
                }
            }
        }

        if (isset($data['contactDetails']) && is_array($data['contactDetails']) && !empty($data['contactDetails'])) {
            $defaultIndex = null;
            foreach ($data['contactDetails'] as $index => $contact) {
                if (isset($contact['isDefault']) && $contact['isDefault'] == -1) {
                    if ($defaultIndex === null) {
                        $defaultIndex = $index;
                    } else {
                        $data['contactDetails'][$index]['isDefault'] = 0;
                    }
                }
            }
            
            if ($defaultIndex === null && count($data['contactDetails']) > 0) {
                $defaultIndex = 0;
                $data['contactDetails'][0]['isDefault'] = -1;
            }
            
            if ($defaultIndex !== null) {
                CustomerContactDetails::where('customerID', $customerMaster->customerCodeSystem)
                    ->where('isDefault', -1)
                    ->update(['isDefault' => 0]);
            }
            
            foreach ($data['contactDetails'] as $contact) {
                CustomerContactDetails::create([
                    'customerID' => $customerMaster->customerCodeSystem,
                    'contactTypeID' => $contact['contactTypeID'],
                    'contactPersonName' => $contact['contactPersonName'],
                    'contactPersonTelephone' => $contact['contactPersonTelephone'],
                    'contactPersonFax' => $contact['contactPersonFax'] ?? null,
                    'contactPersonEmail' => $contact['contactPersonEmail'],
                    'isDefault' => $contact['isDefault'] ?? 0
                ]);
            }
        }

        $customerMaster['confirmedYN'] = 1;
        $customerMaster['isAutoCreateDocument'] = true;

        $updateCustomerMaster = CustomerMasterAPIService::updateCustomerMaster($customerMaster);

        if(!$updateCustomerMaster['status']) {
            if ($manageTransaction) {
                DB::rollback();
            }
            return [
                'status' => false,
                'message' => $updateCustomerMaster['message'],
                'code' => $updateCustomerMaster['code'] ?? 404
            ];
        }

        $autoApproveParams = DocumentAutoApproveService::getAutoApproveParams($updateCustomerMaster['data']->documentSystemID,$updateCustomerMaster['data']->customerCodeSystem);
        $autoApproveParams['db'] = $db;

        $approveDocument = Helper::approveDocument($autoApproveParams);
        if (!$approveDocument["success"]) {
            if ($manageTransaction) {
                DB::rollBack();
            }
            return [
                'status' => false,
                'message' => $approveDocument['message']
            ];
        }

        if ($manageTransaction) {
            DB::commit();
        }
        return [
            'status' => true,
            'data' => $customerMaster->refresh(),
            'message' => 'Customer Master created successfully'
        ];

    }

    public static function validateMasterData($request) {

        $errorData = [];
        $systemUser = UserTypeService::getSystemEmployee();
        $companyId = $request['company_id'] ?? null;
        $receivableAccount = isset($request['receivable_account']) ? $request['receivable_account'] : null;
        $advanceAccount = isset($request['advance_account']) ? $request['advance_account'] : null;
        $customerCountry = isset($request['customer_country']) ? $request['customer_country'] : null;
        $creditLimit = isset($request['credit_limit']) ? $request['credit_limit'] : null;
        $creditDays = isset($request['credit_days']) ? $request['credit_days'] : null;
        $customerCategory = isset($request['customer_category']) ? $request['customer_category'] : null;
        $customerRegistrationNo = isset($request['customer_registration_no']) ? $request['customer_registration_no'] : null;
        $customerRegistrationExpiryDate = isset($request['customer_registration_expiry_date']) ? $request['customer_registration_expiry_date'] : null;
        $vatEligible = isset($request['vat_eligible']) ? $request['vat_eligible'] : null;
        $vatNumber = isset($request['vat_number']) ? $request['vat_number'] : null;
        $vatPercentage = isset($request['vat_percentage']) ? $request['vat_percentage'] : null;

        if (!empty($companyId)) {
            $company = Company::where('companySystemID', $companyId)->first();

            if(empty($company)){
                $errorData[] = [
                    'field' => "company_id",
                    'message' => [trans('custom.company_not_found')]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "company_id",
                'message' => [trans('custom.company_not_found')]
            ];
        }

        if (!isset($request['secondary_code']) || $request['secondary_code'] === '' || $request['secondary_code'] === null) {
            $errorData[] = [
                'field' => "secondary_code",
                'message' => [trans('custom.secondary_code') . ' is mandatory']
            ];
        } else {
            $request['customerShortCode'] = $request['secondary_code'];
        }

        if (!isset($request['customer_name']) || $request['customer_name'] === '' || $request['customer_name'] === null) {
            $errorData[] = [
                'field' => "customer_name",
                'message' => [trans('custom.customer_name') . ' is mandatory']
            ];
        } else {
            $request['CustomerName'] = ($request['customer_name']);
        }

        if (isset($request['report_title']) && $request['report_title'] !== '' && $request['report_title'] !== null) {
            $request['ReportTitle'] = $request['report_title'];
        }  else {
            $request['ReportTitle'] = isset($request['customer_name']) ? $request['customer_name'] : (isset($request['customer_name']) ? $request['customer_name'] : null);
        }

        self::validateChartOfAccount(
            $receivableAccount,
            "receivable_account",
            "Receivable Account field is required",
            $companyId,
            $errorData,
            $request,
            function($chartOfAccount, &$request, &$errorData, $fieldName) {
                if ($chartOfAccount->controlAccountsSystemID == 3 && $chartOfAccount->controllAccountYN == 1) {
                    $request['custGLAccountSystemID'] = $chartOfAccount->chartOfAccountSystemID;
                    $request['custGLaccount'] = $chartOfAccount->AccountCode;
                } else {
                    $errorData[] = [
                        'field' => $fieldName,
                        'message' => ["Selected GL code must control account and type should be BSA"]
                    ];
                }
            }
        );

        self::validateChartOfAccount(
            $advanceAccount,
            "advance_account",
            "Advance Account field is required",
            $companyId,
            $errorData,
            $request,
            function($chartOfAccount, &$request, &$errorData, $fieldName) {
                if ($chartOfAccount->catogaryBLorPL == "BS" && $chartOfAccount->controllAccountYN == 1) {
                    $request['custAdvanceAccountSystemID'] = $chartOfAccount->chartOfAccountSystemID ?? null;
                    $request['custUnbilledAccountSystemID'] = $chartOfAccount->chartOfAccountSystemID ?? null;
                } else {
                    $errorData[] = [
                        'field' => $fieldName,
                        'message' => ["Selected GL code must control account and type should be BS"]
                    ];
                }
            }
        );

        if ($customerCountry === null) {
            $errorData[] = [
                'field' => "customer_country",
                'message' => ["Customer Country field is required"]
            ];
        }
        else {
            $country = CountryMaster::where('countryName', $customerCountry)->first();
            if ($country) {
                $request['customerCountry'] = $country->countryID;
            }
            else{
                $errorData[] = [
                    'field' => "customer_country",
                    'message' => ["Selected Country does not match any record in the system."]
                ];

            }
        }

        if ($creditLimit === null) {
            $errorData[] = [
                'field' => "credit_limit",
                'message' => ["Credit Limit field is required"]
            ];
        } else {
            if (!is_numeric($creditLimit) || $creditLimit < 0) {
                $errorData[] = [
                    'field' => "credit_limit",
                    'message' => ["Credit Limit must be a number and cannot be negative"]
                ];
            } 
            else{
                $request['creditLimit'] = $creditLimit;
            }
        }


        if ($creditDays === null) {
            $errorData[] = [
                'field' => "credit_days",
                'message' => ["Credit Period field is required"]
            ];
        }
        else {
            if (!is_numeric($creditDays) || $creditDays < 0) {
                $errorData[] = [
                    'field' => "credit_days",
                    'message' => ["Credit Period must be a number and cannot be negative"]
                ];
            }
            else{
                $request['creditDays'] = $creditDays;
            }
        }

        if ($customerCategory !== null && $customerCategory !== '') {
            $customerCategoryMaster = CustomerMasterCategory::where('categoryDescription', $customerCategory)
                ->where('companySystemID', $companyId)->first();
            if ($customerCategoryMaster) {
                $request['customerCategoryID'] = $customerCategoryMaster->categoryID;
            } else {
            $errorData[] = [
                'field' => "customer_category",
                'message' => ["Selected Customer Category does not match any record in the system."]
            ];
            }
        }

        if ($customerRegistrationNo !== null && $customerRegistrationNo !== '') {
            $request['customer_registration_no'] = $customerRegistrationNo;
            if ($customerRegistrationExpiryDate === null) {
                $errorData[] = [
                    'field' => "customer_registration_expiry_date",
                    'message' => ["Customer Registration Expiry Date field is required when Customer Registration No is provided"]
                ];
            }
            else{
                try {
                    $parsedDate = Carbon::parse($request['customer_registration_expiry_date']);
                    $request['customer_registration_expiry_date'] = $parsedDate->format('Y-m-d') . ' 00:00:00';
                } catch (\Exception $e) {
                    $errorData[] = [
                        'field' => "customer_registration_expiry_date",
                        'message' => ["Customer Registration Expiry Date is not a valid date"]
                    ];
                }
            }
        }

        if ($vatEligible !== null && $vatEligible !== '') {
            if ($vatEligible != 1 && $vatEligible != 2) {
                $errorData[] = [
                    'field' => "vat_eligible",
                    'message' => ["Invalid input, vat eligible must be 1 (Yes) or 2 (No)"]
                ];
            } else {
                $request['vatEligible'] = (int)$vatEligible;
            }
        }

        if ($vatNumber !== null && $vatNumber !== '') {
            if (!is_numeric($vatNumber) || $vatNumber < 0) {
                $errorData[] = [
                    'field' => "vat_number",
                    'message' => ["Value must be a number and cannot be negative."]
                ];
            } else {
                $request['vatNumber'] = (int)$vatNumber;
            }
        }

        if ($vatPercentage !== null && $vatPercentage !== '') {
            if (!is_numeric($vatPercentage) || $vatPercentage < 0) {
                $errorData[] = [
                    'field' => "vat_percentage",
                    'message' => ["Value must be a number and cannot be negative."]
                ];
            } else {
                $request['vatPercentage'] = (int)$vatPercentage;
            }
        }

        $addContactDetails = isset($request['add_contact_details']) ? $request['add_contact_details'] : null;
        
        if ($addContactDetails !== null && $addContactDetails !== '') {
            if ($addContactDetails != 1 && $addContactDetails != 2) {
                $errorData[] = [
                    'field' => "add_contact_details",
                    'message' => ["Invalid input,add_contact_details must be 1 (Yes) or 2 (No)"]
                ];
            }
        }

        $contactDetails = isset($request['contact_details']) ? $request['contact_details'] : null;
        $validatedContactDetails = [];

        if ($contactDetails !== null && is_array($contactDetails) && isset($request['add_contact_details']) && $request['add_contact_details'] == 1) {
            foreach ($contactDetails as $index => $contact) {
                $contactType = isset($contact['contact_type']) ? $contact['contact_type'] : null;
                $personName = isset($contact['person_name']) ? $contact['person_name'] : null;
                $telephone = isset($contact['telephone']) ? $contact['telephone'] : null;
                $fax = isset($contact['fax']) ? $contact['fax'] : null;
                $email = isset($contact['email']) ? $contact['email'] : null;
                $isDefault = isset($contact['is_default']) ? $contact['is_default'] : null;
                
                if ($contactType !== null && $contactType !== '') {
                    $supplierContactType = SupplierContactType::where('supplierContactTypeID', $contactType)->first();
                    if (!$supplierContactType) {
                        $errorData[] = [
                            'field' => "contact_details[$index].contact_type",
                            'message' => ["Contact type '{$contactType}' does not match any record in the system."]
                        ];
                    } else {
                        $validatedContactDetails[] = [
                            'contactTypeID' => $supplierContactType->supplierContactTypeID,
                            'contactPersonName' => $personName,
                            'contactPersonTelephone' => $telephone,
                            'contactPersonFax' => $fax,
                            'contactPersonEmail' => $email,
                            'isDefault' => ($isDefault == 1) ? -1 : 0
                        ];
                    }
                } else {
                    $errorData[] = [
                        'field' => "contact_details[$index].contact_type",
                        'message' => ["Contact type is required"]
                    ];
                }
                
                if ($personName === null || $personName === '') {
                    $errorData[] = [
                        'field' => "contact_details[$index].person_name",
                        'message' => ["Person name is required"]
                    ];
                }
                
                if ($telephone === null || $telephone === '') {
                    $errorData[] = [
                        'field' => "contact_details[$index].telephone",
                        'message' => ["Telephone is required"]
                    ];
                }
                
                if ($email === null || $email === '') {
                    $errorData[] = [
                        'field' => "contact_details[$index].email",
                        'message' => ["Email is required"]
                    ];
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errorData[] = [
                        'field' => "contact_details[$index].email",
                        'message' => ["Email is not valid"]
                    ];
                }
            }
        }
        
        $currencyDetails = isset($request['currency_detials']) ? $request['currency_detials'] : (isset($request['currency_details']) ? $request['currency_details'] : null);
        $validatedCurrencyDetails = [];

        if ($currencyDetails !== null && is_array($currencyDetails)) {
            if (isset($currencyDetails['currency_code']) || isset($currencyDetails['is_default'])) {
                $currencyDetails = [$currencyDetails];
            }

            foreach ($currencyDetails as $index => $currencyDetail) {
                $currencyCode = isset($currencyDetail['currency_code']) ? $currencyDetail['currency_code'] : null;
                $isDefault = isset($currencyDetail['is_default']) ? $currencyDetail['is_default'] : null;

                if ($currencyCode !== null && $currencyCode !== "") {
                    $currency = CurrencyMaster::where('CurrencyCode', $currencyCode)->first();
                    
                    if (!$currency) {
                        $errorData[] = [
                            'field' => "currency_details[$index].currency_code",
                            'message' => ["The currency code not matching with system"]
                        ];
                    } else {
                        if ($isDefault !== null && $isDefault !== "") {
                            if ($isDefault != 1 && $isDefault != 2) {
                                $errorData[] = [
                                    'field' => "currency_details[$index].is_default",
                                    'message' => ["Invalid input, currency_details[$index].is_default must be 1 (Yes) or 2 (No)"]
                                ];
                            } else {
                                $validatedCurrencyDetails[] = [
                                    'currencyID' => $currency->currencyID,
                                    'isDefault' => ($isDefault == 1) ? -1 : 0
                                ];
                            }
                        } else {
                            $validatedCurrencyDetails[] = [
                                'currencyID' => $currency->currencyID,
                                'isDefault' => null
                            ];
                        }
                    }
                } else {
                    if ($isDefault !== null && $isDefault !== "") {
                        if ($isDefault != 1 && $isDefault != 2) {
                            $errorData[] = [
                                'field' => "currency_details[$index].is_default",
                                'message' => ["Invalid input, currency_details[$index].is_default must be 1 (Yes) or 2 (No)"]
                            ];
                        }
                    }
                }
            }
        }

        if (empty($errorData)) {
            $returnDataset = [
                'status' => true,
                'data' => [
                    "primaryCompanySystemID" => $company->companySystemID ?? null,
                    "primaryCompanyID" => $company->CompanyID ?? null,
                    "customerCategoryID" => isset($request['customerCategoryID']) ? $request['customerCategoryID'] : null,
                    "custGLAccountSystemID" => $request['custGLAccountSystemID'],
                    "custUnbilledAccountSystemID" => $request['custUnbilledAccountSystemID'],
                    "custAdvanceAccountSystemID" => $request['custAdvanceAccountSystemID'] ?? null,
                    "companyLinkedToSystemID" => null,
                    "customerShortCode" => $request['customerShortCode'],
                    "CustomerName" => $request['CustomerName'],
                    "ReportTitle" => $request['ReportTitle'],
                    "customerAddress1" => isset($request['address_one']) ? $request['address_one'] : null,
                    "customerAddress2" => isset($request['address_two']) ? $request['address_two'] : null,
                    "customerCountry" => $request['customerCountry'],
                    "customerCity" => isset($request['customer_city']) ? $request['customer_city'] : null,
                    "customerLogo" => isset($request['customerLogo']) ? $request['customerLogo'] : null,
                    "CustWebsite" => isset($request['customer_website']) ? $request['customer_website'] : null,
                    "creditLimit" => $request['credit_limit'],
                    "creditDays" => $request['credit_days'],
                    "customer_registration_no" => isset($request['customer_registration_no']) ? $request['customer_registration_no'] : null,
                    "customer_registration_expiry_date" => isset($request['customer_registration_expiry_date']) ? $request['customer_registration_expiry_date'] : null,
                    "vatEligible" => isset($request['vatEligible']) ? $request['vatEligible'] : null,
                    "vatNumber" => isset($request['vatNumber']) ? $request['vatNumber'] : null,
                    "vatPercentage" => isset($request['vatPercentage']) ? $request['vatPercentage'] : null,
                    "consignee_name" => isset($request['consignee_name']) ? $request['consignee_name'] : null,
                    "consignee_address" => isset($request['consignee_address']) ? $request['consignee_address'] : null,
                    "payment_terms" => isset($request['payment_terms']) ? $request['payment_terms'] : null,
                    "consignee_contact_no" => isset($request['consignee_contact_no']) ? $request['consignee_contact_no'] : null,
                    "isDelegation" => false,
                    "companyLinkedTo" => null,
                    "isAutoCreateDocument" => true,
                    "custGLaccount" => $request['custGLAccountSystemID'],
                    "custUnbilledAccount" => $request['custUnbilledAccountSystemID'],
                    "custAdvanceAccount" => $request['custAdvanceAccountSystemID'] ?? null,
                    "createdPcID" => gethostname(),
                    "createdUserID" => $systemUser->empID,
                    "documentSystemID" => 58,
                    "documentID" => "CUSTM",
                    "isCustomerActive" => 1,
                    "currencyDetails" => $validatedCurrencyDetails,
                    "contactDetails" => $validatedContactDetails
                ]
            ];
        }
        else {
            $returnDataset = [
                'status' => false,
                'data' => $errorData
            ];
        }

        return $returnDataset;
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

    private static function validateChartOfAccount(
        $accountCode,
        $fieldName,
        $requiredMessage,
        $companyId,
        &$errorData,
        &$request,
        $specificValidation
    ) {
        if ($accountCode === null) {
            $errorData[] = [
                'field' => $fieldName,
                'message' => [$requiredMessage]
            ];
            return;
        }

        $chartOfAccount = ChartOfAccount::where('AccountCode', $accountCode)->first();

        if (!$chartOfAccount) {
            $errorData[] = [
                'field' => $fieldName,
                'message' => ["Selected GL code does not match any record in the system."]
            ];
            return;
        }

        if ($chartOfAccount->isApproved != 1) {
            $errorData[] = [
                'field' => $fieldName,
                'message' => ["Selected GL code is not approved."]
            ];
            return;
        }

        if ($chartOfAccount->isActive != 1) {
            $errorData[] = [
                'field' => $fieldName,
                'message' => ["Selected GL code is not active"]
            ];
            return;
        }

        $chartOfAccountAssigned = ChartOfAccountsAssigned::where('companySystemID', $companyId)
            ->where('chartOfAccountSystemID', $chartOfAccount->chartOfAccountSystemID)->first();

        if (!$chartOfAccountAssigned || $chartOfAccountAssigned->isAssigned != -1) {
            $errorData[] = [
                'field' => $fieldName,
                'message' => ["Selected GL code is not assigned to the company."]
            ];
            return;
        }

        if ($chartOfAccountAssigned->isActive != 1) {
            $errorData[] = [
                'field' => $fieldName,
                'message' => ["Selected GL code is not active"]
            ];
            return;
        }

        if ($chartOfAccountAssigned->isBank == 1) {
            $errorData[] = [
                'field' => $fieldName,
                'message' => ["Selected GL code is bank gl code."]
            ];
            return;
        }

        $specificValidation($chartOfAccount, $request, $errorData, $fieldName);
    }
}
