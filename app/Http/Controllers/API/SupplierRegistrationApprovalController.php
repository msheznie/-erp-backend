<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Services\SRMService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BankMemoSupplier;
use App\Models\BankMemoTypes;
use App\Models\Company;
use App\Models\CountryMaster;
use App\Models\CurrencyMaster;
use App\Models\DocumentMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierCurrency;
use App\Models\SupplierMaster;
use App\Models\SupplierRegistrationLink;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

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
        $approve = Helper::approveDocument($request);

        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            if ($approve['data'] && $approve['data']['numberOfLevels'] == $approve['data']['currentLevel']) {
                $response = $this->srmService->callSRMAPIs([
                    'apiKey' => $request->input('api_key'),
                    'request' => 'UPDATE_KYC_STATUS',
                    'extra' => [
                        'status'    => APPROVED,
                        'auth'      => $request->user(),
                        'uuid'      => $request->input('uuid')
                    ]
                ]);

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
        $input = $request->all();
        $supplierFormValues = $input['data'];
        $supplierMasterData = $input['supplierRegistration'];
        //$countryID =  $input['supplierCountryID'];
        $company = Company::where('companySystemID', $supplierMasterData['company_id'])->first();
        $employee = \Helper::getEmployeeInfo();
        $document = DocumentMaster::where('documentID', 'SUPM')->first();

        $data['primaryCompanySystemID'] = $supplierMasterData['company_id'];
        $data['primaryCompanyID'] = $company->CompanyID;
        $data['documentSystemID'] = $document->documentSystemID;
        $data['documentID'] = $document->documentID;
        $data['supplierName'] = $supplierFormValues['name'];
        $data['address'] = $supplierFormValues['address'];
        $data['registrationNumber'] = $supplierFormValues['registrationNumber'];
        $data['isActive'] = 1;
        if ($supplierFormValues['country_id'] != "0" || $supplierFormValues['country_id'] != 0) {
            $country = CountryMaster::select('countryID')->where('countryName', $supplierFormValues['country_id'])->first();
            $data['countryID'] = $country['countryID'];
            $data['supplierCountryID'] =  $country['countryID'];
        }
        if ($supplierFormValues['currency'] != "0" || $supplierFormValues['currency'] != 0) {
            $currency = CurrencyMaster::select('currencyID')->where('CurrencyCode', $supplierFormValues['currency'])->first();
            $data['currency'] = $currency['currencyID'];
        }

        $data['vatEligible'] =  $supplierFormValues['vatEligible'];
        $data['vatNumber'] =  $supplierFormValues['vatNumber'];
        $data['vatPercentage'] =  $supplierFormValues['vatPercentage'];
        $data['createdPcID'] = gethostname();
        $data['createdUserID'] = $employee->empID;
        $data['createdUserSystemID'] = $employee->employeeSystemID;
        $data['uniqueTextcode'] = 'S';
        $data['telephone'] = $supplierFormValues['phone_number'];
        $data['nameOnPaymentCheque'] = $supplierFormValues['nameOnPaymentCheque'];

        if ($supplierFormValues['fax'] != 0 ||  $supplierFormValues['fax'] != '0') {
            $data['fax'] = $supplierFormValues['fax'];
        }
        if ($supplierFormValues['email'] != 0 || $supplierFormValues['email'] != '0') {
            $data['supEmail'] = $supplierFormValues['email'];
        }

        $data['webAddress'] = $supplierFormValues['webAddress'];
        $supplierMasters = SupplierMaster::create($data); 
        $dataPrimary['primarySupplierCode'] = 'S0' . strval($supplierMasters['supplierCodeSystem']);
        SupplierMaster::where('supplierCodeSystem', $supplierMasters['supplierCodeSystem'])
            ->update($dataPrimary); 

        $supplierAssigned['supplierCodeSytem'] =  $supplierMasters['supplierCodeSystem'];
        $supplierAssigned['companySystemID'] =  $supplierMasters['primaryCompanySystemID'];
        $supplierAssigned['companyID'] =  $supplierMasters['primaryCompanyID']; 
        $supplierAssigned['uniqueTextcode'] =  $supplierMasters['uniqueTextcode'];
        $supplierAssigned['primarySupplierCode'] =  $dataPrimary['primarySupplierCode'];
        $supplierAssigned['secondarySupplierCode'] =  $supplierMasters['secondarySupplierCode'];
        $supplierAssigned['supplierName'] =  $supplierMasters['supplierName'];
        $supplierAssigned['liabilityAccountSysemID'] =  $supplierMasters['liabilityAccountSysemID'];
        $supplierAssigned['liabilityAccount'] =  $supplierMasters['liabilityAccount'];
        $supplierAssigned['UnbilledGRVAccountSystemID'] =  $supplierMasters['UnbilledGRVAccountSystemID'];
        $supplierAssigned['UnbilledGRVAccount'] =  $supplierMasters['UnbilledGRVAccount'];
        $supplierAssigned['address'] =  $supplierMasters['address'];
        $supplierAssigned['countryID'] =  $supplierMasters['countryID'];
        $supplierAssigned['supplierCountryID'] =  $supplierMasters['supplierCountryID'];
        $supplierAssigned['telephone'] =  $supplierMasters['telephone'];
        $supplierAssigned['fax'] =  $supplierMasters['fax'];
        $supplierAssigned['supEmail'] =  $supplierMasters['supEmail'];
        $supplierAssigned['webAddress'] =  $supplierMasters['webAddress'];
        $supplierAssigned['currency'] =  $supplierMasters['currency'];
        $supplierAssigned['nameOnPaymentCheque'] =  $supplierMasters['nameOnPaymentCheque'];
        $supplierAssigned['creditLimit'] =  $supplierMasters['creditLimit'];
        $supplierAssigned['creditPeriod'] =  $supplierMasters['creditPeriod'];
        $supplierAssigned['supCategoryMasterID'] =  $supplierMasters['supCategoryMasterID'];
        $supplierAssigned['supCategorySubID'] =  $supplierMasters['supCategorySubID'];
        $supplierAssigned['registrationNumber'] =  $supplierMasters['registrationNumber'];
        $supplierAssigned['registrationExprity'] =  $supplierMasters['registrationExprity'];
        $supplierAssigned['supplierImportanceID'] =  $supplierMasters['supplierImportanceID'];
        $supplierAssigned['supplierNatureID'] =  $supplierMasters['supplierNatureID'];
        $supplierAssigned['supplierTypeID'] =  $supplierMasters['supplierTypeID'];
        $supplierAssigned['WHTApplicable'] =  $supplierMasters['WHTApplicable'];
        $supplierAssigned['vatEligible'] =  $supplierMasters['vatEligible'];
        $supplierAssigned['vatNumber'] =  $supplierMasters['vatNumber'];
        $supplierAssigned['vatPercentage'] =  $supplierMasters['vatPercentage'];
        $supplierAssigned['supCategoryICVMasterID'] =  $supplierMasters['supCategoryICVMasterID']; 
        $supplierAssigned['supCategorySubICVID'] =  $supplierMasters['supCategorySubICVID']; 
        $supplierAssigned['isLCCYN'] =  $supplierMasters['isLCCYN']; 
        $supplierAssigned['isMarkupPercentage'] =  $supplierMasters['isMarkupPercentage']; 
        $supplierAssigned['isRelatedPartyYN'] =  $supplierMasters['isRelatedPartyYN']; 
        $supplierAssigned['isCriticalYN'] =  $supplierMasters['isCriticalYN']; 
        $supplierAssigned['jsrsNo'] =  $supplierMasters['jsrsNo']; 
        $supplierAssigned['jsrsExpiry'] =  $supplierMasters['jsrsExpiry']; 
        $supplierAssigned['isActive'] =  $supplierMasters['isActive']; 
        $supplierAssigned['isAssigned'] = -1; 
        $supplierAssign = SupplierAssigned::create($supplierAssigned); 


        $supplierCurrency = new SupplierCurrency();
        $supplierCurrency->supplierCodeSystem = $supplierMasters['supplierCodeSystem'];
        $supplierCurrency->currencyID =  $supplierMasters['currency'];
        $supplierCurrency->isAssigned = -1;
        $supplierCurrency->isDefault = -1;
        $supplierCurrency->save();

        $supplier = SupplierMaster::where('supplierCodeSystem', $supplierMasters['supplierCodeSystem'])->first(); 
        $companyDefaultBankMemos = BankMemoTypes::orderBy('sortOrder', 'asc')->get();
        $employee = \Helper::getEmployeeInfo();
        $empId = $employee['empID'];
        $empName = $employee['empName'];


        foreach ($companyDefaultBankMemos as $value) {
            $temBankMemo = new BankMemoSupplier();
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
        return $this->sendResponse($dataPrimary['primarySupplierCode'] ,"Supplier created successfully");
    }
}
