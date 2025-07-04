<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Jobs\ThirdPartySystemNotifications\ThirdPartySystemNotificationJob;
use App\Models\DocumentAttachments;
use App\Models\SRMSupplierValues;
use App\Models\SupplierBusinessCategoryAssign;
use App\Models\SupplierCategory;
use App\Models\SupplierContactDetails;
use App\Models\SupplierGroup;
use App\Models\SupplierSubCategoryAssign;
use App\Services\SRMService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BankMemoSupplier;
use App\Models\BankMemoTypes;
use App\Models\Company;
use App\Models\CountryMaster;
use App\Models\CurrencyMaster;
use App\Models\DocumentMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierCategoryMaster;
use App\Models\SupplierCurrency;
use App\Models\SupplierMaster;
use App\Models\SupplierRegistrationLink;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Models\SystemGlCodeScenario;
use App\Models\ChartOfAccount;


// supplier KYC status
define('PENDING', 0);
define('SUBMITTED', 1);
define('PENDING_FOR_APPROVAL', 2);
define('APPROVED', 3);
define('REJECT', 4);

class SupplierRegistrationApprovalController extends AppBaseController
{
    private $srmService = null;

    public function __construct(SRMService $srmService)
    {
        $this->srmService = $srmService;
    }

    /**
     * get KYC list
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $empID = \Helper::getEmployeeSystemID();

        $suppliersDetail = DB::table('erp_documentapproved')
            ->select(
                'srm_supplier_registration_link.id',
                'erp_documentapproved.documentSystemID',
                'srm_supplier_registration_link.name',
                'srm_supplier_registration_link.approved_yn',
                'srm_supplier_registration_link.email',
                'srm_supplier_registration_link.registration_number',
                'srm_supplier_registration_link.company_id',
                'srm_supplier_registration_link.token',
                'srm_supplier_registration_link.token_expiry_date_time',
                'srm_supplier_registration_link.created_by',
                'srm_supplier_registration_link.updated_by',
                'srm_supplier_registration_link.created_at',
                'srm_supplier_registration_link.updated_at',
                'srm_supplier_registration_link.uuid',
                'srm_supplier_registration_link.supplier_master_id',
                'srm_supplier_registration_link.confirmed_by_emp_id',
                'srm_supplier_registration_link.confirmed_by_name',
                'srm_supplier_registration_link.confirmed_date',
                'erp_documentapproved.documentApprovedID',
                'erp_documentapproved.rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode'
                // 'employees.empName As created_user'
            )->join('employeesdepartments', function ($query) use ($companyID, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                // 107 mean "Supplier registration" id of document master table id
                $query->whereIn('employeesdepartments.documentSystemID', [107])
                    ->where('employeesdepartments.companySystemID', $companyID)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })->join('srm_supplier_registration_link', function ($query) use ($companyID, $empID, $input) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'id');
                //                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')

                if (array_key_exists('approved', $input)) {
                    if ($input['approved'] == 0 && !is_null($input['approved'])) {
                        $query->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr');
                    }
                }

                $query->where('srm_supplier_registration_link.company_id', $companyID)
                    ->where('srm_supplier_registration_link.confirmed_yn', 1);
            })
            // ->join('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [107])
            ->where('erp_documentapproved.companySystemID', $companyID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $suppliersDetail = $suppliersDetail->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('registration_number', 'LIKE', "%{$search}%");
            });
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $suppliersDetail->where('erp_documentapproved.approvedYN', $input['approved']);
            }
        }

        return \DataTables::of($suppliersDetail)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('documentApprovedID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }

    /**
     * handle KYC Status
     * @param Request $request
     * @throws Throwable
     */
    public function update(Request $request)
    {
        switch ($request->input('mode')) {
            case 'approve': {
                    $this->approveSupplierKYC($request);
                    break;
                }
            case 'reject': {
                    $this->rejectSupplierKYC($request);
                    break;
                }
            default: {
                }
        }
    }

    /**
     * approve KYC
     * @param $request
     * @return mixed
     * @throws Throwable
     */
    public function approveSupplierKYC($request)
    {
        $db = isset($request->db) ? $request->db : "";

       $supplierMasterId = $this->isSupplierMasterCreated($request['id']);  
       
       $approve = Helper::approveDocument($request);

        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            if ($approve['data'] && $approve['data']['numberOfLevels'] == $approve['data']['currentLevel']) {
                 ThirdPartySystemNotificationJob::dispatch($db,107,$request['id']);

                $getUpdatedValues = SRMSupplierValues::select('user_name','name')
                    ->where('company_id',$request['company_id'])
                    ->where('supplier_id',$request['id'])
                    ->first();

                $userName = $getUpdatedValues['user_name'];
                $name = $getUpdatedValues['name'];

                SupplierRegistrationLink::where('id', $request['id'])
                    ->update([
                        'email' => $userName,
                        'name' => $name
                ]);

                $response = $this->srmService->callSRMAPIs([
                    'apiKey' => $request->input('api_key'),
                    'request' => 'UPDATE_KYC_STATUS',
                    'extra' => [
                        'status'    => APPROVED,
                        'auth'      => $request->user(),
                        'uuid'      => $request->input('uuid'),
                        'email' => $userName,
                        'name' => $name
                    ]
                ]);

                if($supplierMasterId['supplier_master_id'] > 0){ 
                    $getSupplierData = $this->getKYCData($request);
                    $supData = $getSupplierData->data;
                    $supDataArray = json_decode(json_encode($supData), true);
                    $supplierReg = [
                        'registrationNumber' => $supData->registrationNumber,
                        'company_id'=> $supplierMasterId['company_id'],
                        'supplierMasterId'=> $supplierMasterId['supplier_master_id'],
                        'isApprovalAmmend'=> 1,
                        'primarySupplierCode'=> $supplierMasterId['supplier']['primarySupplierCode']
                    ];
        
                    $request->merge([
                        'data' => $supDataArray,
                        'supplierRegistration' => $supplierReg,
                    ]);
        
                    $this->supplierCreation($request); 
               }
        

                if ($response && $response->success === false) return $this->sendError("Something went wrong!, Supplier status couldn't be updated");
            }

            return $this->sendResponse(array(), $approve["message"]);
        }
    }

    /**
     * reject KYC
     * @param $request
     * @return mixed
     * @throws Throwable
     */
    public function rejectSupplierKYC($request)
    {
        $reject = Helper::rejectDocument($request);

        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            $response = $this->srmService->callSRMAPIs([
                'apiKey' => $request->input('api_key'),
                'request' => 'UPDATE_KYC_STATUS',
                'extra' => [
                    'status'    => REJECT,
                    'auth'      => $request->user(),
                    'uuid'      => $request->input('uuid')
                ]
            ]);

            if ($response && $response->success === false) return $this->sendError("Something went wrong!, Supplier status couldn't be updated");

            return $this->sendResponse(array(), $reject["message"]);
        }
    }
    public function supplierCreation(Request $request)
    {
        $this->sendError('asd');
        $input = $request->all();
        $supplierFormValues = $input['data'];
        $supplierMasterData = $input['supplierRegistration'];
        $isApprovalAmmend = isset($supplierMasterData['isApprovalAmmend']) ? $supplierMasterData['isApprovalAmmend'] : 0;
        //$countryID =  $input['supplierCountryID'];
        $company = Company::where('companySystemID', $supplierMasterData['company_id'])->first();
        $employee = \Helper::getEmployeeInfo();
        $document = DocumentMaster::where('documentID', 'SUPM')->first();
        $selectedCompanyId = $supplierMasterData['company_id'];


        $liabilityAccountConfigs = SystemGlCodeScenario::where('slug','account-payable-liability-account')
                        ->with(['detail'=>function($query) use($selectedCompanyId){
                            $query->where('companySystemID',$selectedCompanyId);
                        }])
                        ->whereHas('detail',function($query) use($selectedCompanyId){
                            $query->where('companySystemID',$selectedCompanyId);
                        })
                        ->first();

        $unbilledAccountConfigs = SystemGlCodeScenario::where('slug','account-payable-unbilled-account')
                        ->with(['detail'=>function($query) use($selectedCompanyId){
                            $query->where('companySystemID',$selectedCompanyId);
                        }])                        
                        ->whereHas('detail',function($query) use($selectedCompanyId){
                            $query->where('companySystemID',$selectedCompanyId);
                        })
                        ->first();

        $advanceAccountConfigs = SystemGlCodeScenario::where('slug','account-payable-advance-account')
                        ->with(['detail'=>function($query) use($selectedCompanyId){
                            $query->where('companySystemID',$selectedCompanyId);
                        }])                        
                        ->whereHas('detail',function($query) use($selectedCompanyId){
                            $query->where('companySystemID',$selectedCompanyId);
                        })
                        ->first();


        if($liabilityAccountConfigs->detail !== null && $liabilityAccountConfigs->detail->chartOfAccountSystemID !== null){
            $data['liabilityAccountSysemID'] = $liabilityAccountConfigs->detail->chartOfAccountSystemID;
            $liabilityAccountSysemID =  ChartOfAccount::where('chartOfAccountSystemID', $data['liabilityAccountSysemID'])->first();
            $data['liabilityAccount'] = $liabilityAccountSysemID['AccountCode'];
        }
        if($unbilledAccountConfigs->detail !== null && $unbilledAccountConfigs->detail->chartOfAccountSystemID !== null){
            $data['UnbilledGRVAccountSystemID'] = $unbilledAccountConfigs->detail->chartOfAccountSystemID;
            $unbilledGRVAccountSystemID = ChartOfAccount::where('chartOfAccountSystemID', $data['UnbilledGRVAccountSystemID'])->first();
            $data['UnbilledGRVAccount'] = $unbilledGRVAccountSystemID['AccountCode'];
        }
        if($advanceAccountConfigs->detail !== null && $advanceAccountConfigs->detail->chartOfAccountSystemID !== null){
            $data['advanceAccountSystemID'] = $advanceAccountConfigs->detail->chartOfAccountSystemID;
            $advanceAccountSystemID = ChartOfAccount::where('chartOfAccountSystemID', $data['advanceAccountSystemID'])->first();
            $data['AdvanceAccount'] = $advanceAccountSystemID['AccountCode'];
        }


        $data['primaryCompanySystemID'] = $supplierMasterData['company_id'];
        $data['primaryCompanyID'] = $company->CompanyID;
        $data['documentSystemID'] = $document->documentSystemID;
        $data['documentID'] = $document->documentID;
        $data['supplierName'] = $supplierFormValues['name'];
        $data['address'] = $supplierFormValues['address'];
        $data['registrationNumber'] = $supplierFormValues['registrationNumber'];
        $data['isActive'] = 1;
        if ($supplierFormValues['country_id'] != "0") {
            $country = CountryMaster::select('countryID')->where('countryID', $supplierFormValues['country_id'])->first();
            $data['countryID'] = $country['countryID'];
            $data['supplierCountryID'] =  $country['countryID'];
        }
        if ($supplierFormValues['currency'] != "0") {
            $currency = CurrencyMaster::select('currencyID')->where('currencyID', $supplierFormValues['currency'])->first();
            $data['currency'] = $currency['currencyID'];
        } else {
            $data['currency'] = 0;
        }
        if ($supplierFormValues['supCategoryMasterID'] != "0") {
            $supplierCat = SupplierCategoryMaster::select('supCategoryMasterID')->where('supCategoryMasterID', $supplierFormValues['supCategoryMasterID'])->first();
            $data['supCategoryMasterID'] = $supplierCat['supCategoryMasterID'];
        }
        
        if (isset($supplierFormValues['supCategory']) && !empty($supplierFormValues['supCategory']) && $supplierFormValues['supCategory'] != "0") {
            $supplierCat = SupplierCategory::select('id')->where('id', $supplierFormValues['supCategory'])->first();
            $data['supplier_category_id'] = $supplierCat['id'];
        }

        if (isset($supplierFormValues['supGroup']) && !empty($supplierFormValues['supGroup']) && $supplierFormValues['supGroup'] != "0") {
            $supplierGrp = SupplierGroup::select('id')->where('id', $supplierFormValues['supGroup'])->first();
            $data['supplier_group_id'] = $supplierGrp['id'];
        }

        $data['vatEligible'] =  $supplierFormValues['vatEligible'];
        $data['createdFrom'] =  6;
        $data['vatNumber'] =  $supplierFormValues['vatNumber'];
        $data['vatPercentage'] =  $supplierFormValues['vatPercentage'];
        $data['createdPcID'] = gethostname();
        $data['createdUserID'] = $employee->empID;
        $data['createdUserSystemID'] = $employee->employeeSystemID;
        $data['uniqueTextcode'] = 'S';
        $data['telephone'] = $supplierFormValues['phone_number'];
        $data['nameOnPaymentCheque'] = $supplierFormValues['nameOnPaymentCheque'];
        $data['isSMEYN'] = $supplierFormValues['smeSupplier'] ?? 0;

        if ($supplierFormValues['fax'] != 0 ||  $supplierFormValues['fax'] != '0') {
            $data['fax'] = $supplierFormValues['fax'];
        }
        if ($supplierFormValues['email'] != 0 || $supplierFormValues['email'] != '0') {
            $data['supEmail'] = $supplierFormValues['email'];
        }

        $data['webAddress'] = $supplierFormValues['webAddress'];
        $data['registrationExprity'] = $supplierFormValues['expireDate'];

        if($isApprovalAmmend!=1){
            $supplierMasters = SupplierMaster::create($data); 
            $dataPrimary['primarySupplierCode'] = 'S0' . strval($supplierMasters['supplierCodeSystem']);
            SupplierMaster::where('supplierCodeSystem', $supplierMasters['supplierCodeSystem'])
                ->update($dataPrimary);
            $supplierID = $supplierMasters['supplierCodeSystem'];
        }else {
            
            SupplierMaster::where('supplierCodeSystem',$supplierMasterData['supplierMasterId'])
            ->update($data);

            $supplierMasters['supplierCodeSystem'] =$supplierMasterData['supplierMasterId'];
            $supplierMasters['currency'] = ($supplierFormValues['currency'] != "0") ? $supplierFormValues['currency']  : null;
            $supplierID = $supplierMasterData['supplierMasterId'];

            SupplierBusinessCategoryAssign::where('supplierID', $supplierID)->delete();
            SupplierSubCategoryAssign::where('supplierID', $supplierID)->delete();
            SupplierContactDetails::where('supplierID', $supplierID)->delete();
            DocumentAttachments::where('documentSystemCode', $supplierID)->where('documentSystemID', 56)->whereIn('attachmentType',[0, 11])->delete();

        }

        foreach ($supplierFormValues['category'] as $value) {
            SupplierBusinessCategoryAssign::insert([
                'supplierID' => $supplierID,
                'supCategoryMasterID' => $value,
                'timestamp' => now(),
            ]);
        }

        foreach ($supplierFormValues['subCategory'] as $value) {
            SupplierSubCategoryAssign::insert([
                'supplierID' => $supplierID,
                'supSubCategoryID' => $value,
                'timestamp' => now(),
            ]);
        }

        // Update Contact Details
        $fieldMappings = [
            70 => 'contactTypeID',
            56 => 'contactPersonName',
            59 => 'contactPersonTelephone',
            61 => 'contactPersonEmail',
            62 => 'contactPersonFax',
            63 => 'isDefault',
        ];
        $supplierContactDetails = new SupplierContactDetails();
        $groupedContacts = [];
        foreach ($supplierFormValues['supplierContacts'] as $item) {
            $sort = $item['sort'];
            $groupedContacts[$sort][] = $item;
        }

        $supplierContactDetails
            ->where('supplierID', $supplierID)
            ->delete();

        foreach ($groupedContacts as $sort => $contacts) {
            $recordValues = [
                'supplierID' => $supplierID,
                'sort' => $sort,
            ];
            foreach ($contacts as $contact) {
                $formFieldId = $contact['form_field_id'];
                $value = $contact['value'];
                if (isset($fieldMappings[$formFieldId])) {
                    $fieldName = $fieldMappings[$formFieldId];
                    if ($formFieldId == 63 && $value == 1) {
                        $value = -1;
                    }
                    $recordValues[$fieldName] = $value;
                }
            }
            $supplierContactDetails->create($recordValues);
        }

        // upload attachments
        foreach ($supplierFormValues['getAttachments'] as $item) {
            // Skip if no attachment path provided
            if ($item['form_field_id_12'] == '-') {
                continue;
            }

            $attachmentDescription = $item['form_field_id_11'];
            $path = $item['form_field_id_12'];
            $docExpiryDate = $item['form_field_id_14'];

            // Handle special characters in the description
            $attachmentDescription = str_replace(['&quot;', '&#039;'], ['"', "'"], $attachmentDescription);

            $attachmentData = [
                'attachmentDescription' => $attachmentDescription,
                'path' => $path,
                'myFileName' => $attachmentDescription,
                'docExpirtyDate' => $docExpiryDate != '-' ? $docExpiryDate : null,
                'companySystemID' => $supplierMasterData['company_id'],
                'companyID' => $company->CompanyID,
                'documentSystemID' => 56,
                'documentID' => 'SUPM',
                'documentSystemCode' => $supplierID,
                'originalFileName' => $attachmentDescription,
                'attachmentType' => 11,
                'sizeInKbs' => 0,
                'isUploaded' => 1,
            ];
            DocumentAttachments::create($attachmentData);
        }



        if (isset($supplierFormValues['supplierCertification']) && sizeof($supplierFormValues['supplierCertification']) > 0) {
            $supplierCertificationData = [];
            foreach ($supplierFormValues['supplierCertification'] as $index => $item) {
                $formFieldId = $item['form_field_id'];
                $value = $item['value'];
                $sort = $item['sort'];
                if ($formFieldId === 17) {
                    $supplierCertificationData[$sort] = [
                        'attachmentDescription' => $value,
                        'myFileName' => $value
                    ];
                } elseif ($formFieldId === 18 || $formFieldId === 19) {
                    if (isset($supplierCertificationData[$sort])) {
                        $supplierCertificationData[$sort][$formFieldId === 18 ? 'docExpirtyDate' : 'path'] = $value;
                    }
                }
            }

            foreach ($supplierCertificationData as $sort => $data) {
                if (isset($data['attachmentDescription'], $data['docExpirtyDate'], $data['path'])) {
                    $data['companySystemID'] = $supplierMasterData['company_id'];
                    $data['companyID'] = $company->CompanyID;
                    $data['documentSystemID'] = 56;
                    $data['documentID'] = 'SUPM';
                    $data['documentSystemCode'] = $supplierID;
                    $data['originalFileName'] = $data['attachmentDescription'];
                    $data['attachmentType'] = 11;
                    $data['sizeInKbs'] = 0;
                    $data['isUploaded'] = 1;
                    DocumentAttachments::create($data);
                }
            }
        }

        if (isset($supplierFormValues['vatCertification']) && sizeof($supplierFormValues['vatCertification']) > 0) {
            $vatCertificationData = [];
            foreach ($supplierFormValues['vatCertification'] as $index => $item) {
                $formFieldId = $item['form_field_id'];
                $value = $item['value'];
                
                switch ($formFieldId) {
                    case 67:
                        $vatCertificationData['path'] = $value;
                        break;
                    case 68:

                        $vatCertificationData['docExpirtyDate'] = $value;
                        break;
                }
            }

            $vatCertificationData['attachmentDescription'] = 'VAT Certificate Document';
            $vatCertificationData['myFileName'] = 'VAT Certificate Document';
            $vatCertificationData['companySystemID'] = $supplierMasterData['company_id'];
            $vatCertificationData['companyID'] = $company->CompanyID;
            $vatCertificationData['documentSystemID'] = 56;
            $vatCertificationData['documentID'] = 'SUPM';
            $vatCertificationData['documentSystemCode'] = $supplierID;
            $vatCertificationData['originalFileName'] = 'VAT Certificate Document';
            $vatCertificationData['attachmentType'] = 11;
            $vatCertificationData['sizeInKbs'] = 0;
            $vatCertificationData['isUploaded'] = 1;
            DocumentAttachments::create($vatCertificationData);
        }

        $supplierCurrency = new SupplierCurrency();
        $supplierCurrency->supplierCodeSystem = $supplierMasters['supplierCodeSystem'];
        $supplierCurrency->currencyID =  $supplierMasters['currency'];
        $supplierCurrency->isAssigned = -1;
        $supplierCurrency->isDefault = -1;

        if($isApprovalAmmend!=1){
            $supplierCurrency->save();
        }else {
            SupplierCurrency::where('supplierCodeSystem', $supplierMasterData['supplierMasterId'])
            ->update([
                'supplierCodeSystem' => $supplierMasters['supplierCodeSystem'],
                'currencyID' => $supplierMasters['currency'],
                'isAssigned' => -1,
                'isDefault' => -1
            ]);
        }

        $supplier = SupplierMaster::where('supplierCodeSystem', $supplierMasters['supplierCodeSystem'])->first(); 
        $companyDefaultBankMemos = BankMemoTypes::orderBy('sortOrder', 'asc')->get();
        $employee = \Helper::getEmployeeInfo();
        $empId = $employee['empID'];
        $empName = $employee['empName'];
        $temBankMemo = new BankMemoSupplier();

        if($isApprovalAmmend!=1){ 
            foreach ($companyDefaultBankMemos as $value) {
                $temBankMemo->memoHeader = $value['bankMemoHeader'];
                $temBankMemo->bankMemoTypeID = $value['bankMemoTypeID'];
                $temBankMemo->memoDetail = '';
                $temBankMemo->supplierCodeSystem = $supplier->supplierCodeSystem;
                $temBankMemo->supplierCurrencyID = $supplierCurrency->supplierCurrencyID;
                $temBankMemo->updatedByUserID = $empId;
                $temBankMemo->updatedByUserName = $empName;
                $temBankMemo->save();
            }

            $isUpdated = SupplierRegistrationLink::where('id', $supplierMasterData['id'])
            ->update([
                'supplier_master_id' => $supplier->supplierCodeSystem
            ]);
        }else { 
            BankMemoSupplier::where('supplierCodeSystem', $supplierMasterData['supplierMasterId'])
            ->update([
                'supplierCurrencyID' => $supplierCurrency->supplierCurrencyID
            ]);

            $dataPrimary['primarySupplierCode'] = $supplierMasterData['primarySupplierCode'];
        }

      
        return $this->sendResponse($dataPrimary['primarySupplierCode'] ,"Supplier created successfully");
    }

    public function isSupplierMasterCreated($supplierId){ 
        $isMasterCreate = SupplierRegistrationLink::select('supplier_master_id','company_id')
        ->with(['supplier'=>function ($q){ 
            $q->select('supplierCodeSystem','supplierName','primarySupplierCode');
        }])
        ->where('id',$supplierId)
        ->first();

        return $isMasterCreate;
    }

    public function getKYCData($request){
        
        $response = $this->srmService->callSRMAPIs([
            'apiKey' => $request->input('api_key'),
            'request' => 'GET_SUPPLIER_DETAIL_CREATIONS',
            'extra' => [ 
                'auth'      => $request->user(),
                'uuid'      => $request->input('uuid')
            ]
        ]);

        if ($response && $response->success === true){ 
            return $response;
        }

        return $this->sendError("Something went wrong!, Supplier data couldn't be fetched");

    }
}
