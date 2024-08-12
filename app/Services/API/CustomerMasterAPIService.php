<?php

namespace App\Services\API;

use App\helper\Helper;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CreditNote;
use App\Models\CustomerAssigned;
use App\Models\CustomerCurrency;
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

class CustomerMasterAPIService
{
    use AuditLogsTrait;

    public static function validateCustomerMasterData($data): array {
        // include common validation in UI & API

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
            $chartOfAccount = ChartOfAccount::where('chartOfAccountSystemID', $data['custAdvanceAccountSystemID'])
                ->where('controllAccountYN', '=', 1)
                ->whereHas('chartofaccount_assigned', function($query) use ($data) {
                    $query->where('companySystemID', $data['company_id'])
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

                //check policy 5
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
                    // user activity log table

                    if($customerMaster){
                        $old_array = array_only($customerMasterOld,['creditDays','vatEligible','vatNumber','vatPercentage', 'customerSecondLanguage', 'reportTitleSecondLanguage', 'addressOneSecondLanguage', 'addressTwoSecondLanguage']);
                        $modified_array = array_only($input,['creditDays','vatEligible','vatNumber','vatPercentage', 'customerSecondLanguage', 'reportTitleSecondLanguage', 'addressOneSecondLanguage', 'addressTwoSecondLanguage']);

                        // update in to user log table
                        foreach ($old_array as $key => $old){
                            if($old != $modified_array[$key]){
                                $description = $employee->empName." Updated customer (".$customerMaster->CutomerCode.") from ".$old." To ".$modified_array[$key]."";
                                // UserActivityLogger::createUserActivityLogArray($employee->employeeSystemID,$customerMasters->documentSystemID,$customerMasters->primaryCompanySystemID,$customerMasters->supplierCodeSystem,$description,$modified_array[$key],$old,$key);
                            }
                        }
                    }

                    self::auditLog($db, $input['customerCodeSystem'],$uuid, "customermaster", $input['CutomerCode']." has updated", "U", $newValue, $previousValue);

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

                CustomerCurrency::create(['customerCodeSystem' => $customerMaster->customerCodeSystem,
                    'customerCode' => $customerMaster->CutomerCode,
                    'currencyID' => 1,
                    'isDefault' => -1,
                    'isAssigned' => -1,
                    'createdBy' => $systemUser->empID
                ]);

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
}
