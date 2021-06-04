<?php
/**
 * =============================================
 * -- File Name : CustomerMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Customer Master
 * -- Author : Mohamed Fayas
 * -- Create date : 19 - March 2018
 * -- Description : This file contains the all CRUD for Customer Master
 * -- REVISION HISTORY
 * -- Date: 19-March 2018 By: Fayas Description: Added new functions named as getAllCustomers()
 * -- Date: 20-March 2018 By: Fayas Description: Added new functions named as getCustomerFormData(),getAssignedCompaniesByCustomer()
 * -- Date: 21-June 2018 By: Fayas Description: Added new functions named as getSearchCustomerByCompany()
 * -- Date: 13-August 2018 By: Fayas Description: Added new functions named as getContractByCustomer()
 * -- Date: 19-November 2018 By: Fayas Description: Added new functions named as getJobsByContractAndCustomer()
 * -- Date: 18-December 2018 By: Fayas Description: Added new functions named as customerReferBack()
 * -- Date: 20-January 2019 By: Fayas Description: Added new functions named as getPosCustomerSearch()
 */

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\helper\ReopenDocument;
use App\Http\Requests\API\CreateCustomerMasterAPIRequest;
use App\Http\Requests\API\UpdateCustomerMasterAPIRequest;
use App\Models\CompanyPolicyMaster;
use App\Models\Contract;
use App\Models\CustomerMaster;
use App\Models\Company;
use App\Models\CountryMaster;
use App\Models\CustomerMasterCategory;
use App\Models\CustomerMasterRefferedBack;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\CurrencyMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierContactType;
use App\Models\TicketMaster;
use App\Models\YesNoSelection;
use App\Models\CustomerAssigned;
use App\Models\ChartOfAccount;
use App\Repositories\CustomerMasterRepository;
use App\Traits\UserActivityLogger;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserRepository;
use Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class CustomerMasterController
 * @package App\Http\Controllers\API
 */
class CustomerMasterAPIController extends AppBaseController
{
    /** @var  CustomerMasterRepository */
    private $customerMasterRepository;
    private $userRepository;

    public function __construct(CustomerMasterRepository $customerMasterRepo, UserRepository $userRepo)
    {
        $this->customerMasterRepository = $customerMasterRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the CustomerMaster.
     * GET|HEAD /customerMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->customerMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->customerMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerMasters = $this->customerMasterRepository->all();

        return $this->sendResponse($customerMasters->toArray(), 'Customer Masters retrieved successfully');
    }


    /**
     * Display a listing of the CustomerMaster.
     * GET|HEAD /getAllCustomers
     *
     * @param Request $request
     * @return Response
     */
    public function getAllCustomers(Request $request)
    {

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }
        $customerMasters = CustomerMaster::with(['country'])
            //with(['categoryMaster', 'employee', 'supplierCurrency'])
            //->whereIn('primaryCompanySystemID',$childCompanies)
            ->select('customermaster.*');

        if (isset($input['customerCategoryID']) && $input['customerCategoryID'] > 0) {
            $customerMasters = $customerMasters->where('customerCategoryID', $input['customerCategoryID']);
        }

        $search = $request->input('search.value');
        if ($search) {
            $customerMasters = $customerMasters->where(function ($query) use ($search) {
                $query->where('CutomerCode', 'LIKE', "%{$search}%")
                    ->orWhere('customerShortCode', 'LIKE', "%{$search}%")
                    ->orWhere('CustomerName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($customerMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('customerCodeSystem', $input['order'][0]['dir']);
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
     * get supplier master approval by company.
     * GET|HEAD /getAllCustomerMasterApproval
     *
     * @param Request $request
     * @return Response
     */
    public function getAllCustomerMasterApproval(Request $request)
    {

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request->selectedCompanyID;

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $companyID = \Helper::getGroupCompany($companyId);
        } else {
            $companyID = [$companyId];
        }

        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');

        $customerMasters = DB::table('erp_documentapproved')->select('customermaster.*', 'countrymaster.countryName', 'erp_documentapproved.documentApprovedID', 'rollLevelOrder', 'approvalLevelID', 'documentSystemCode')->join('employeesdepartments', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID')
                ->where('employeesdepartments.documentSystemID', 58)
                ->whereIn('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID)
                ->where('employeesdepartments.isActive', 1)
                ->where('employeesdepartments.removedYN', 0);
        })->join('customermaster', function ($query) use ($companyID, $empID, $search) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'customerCodeSystem')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->whereIn('primaryCompanySystemID', $companyID)
                ->where('customermaster.approvedYN', 0)
                ->where('customermaster.confirmedYN', 1)
                ->when($search != "", function ($q) use ($search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('CutomerCode', 'LIKE', "%{$search}%")
                            ->orWhere('customerShortCode', 'LIKE', "%{$search}%")
                            ->orWhere('CustomerName', 'LIKE', "%{$search}%");
                    });
                });
        })->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('countrymaster', 'customerCountry', '=', 'countryID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', 58)
            ->whereIn('erp_documentapproved.companySystemID', $companyID);

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $customerMasters = [];
        }

        return \DataTables::of($customerMasters)
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
     * get form data for Customer Master.
     * GET /getCustomerFormData
     *
     * @param Request $request
     * @return Response
     */

    public function getCustomerFormData(Request $request)
    {

        $selectedCompanyId = $request['selectedCompanyId'];
        $customerID = isset($request['customerID'])?$request['customerID']:0;

        $masterCompany = Company::where("companySystemID", $selectedCompanyId)->first();

        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            //$subCompanies = \Helper::getGroupCompany($selectedCompanyId);
            $subCompanies = \Helper::getSubCompaniesByGroupCompany($selectedCompanyId);
            /**  Companies by group  Drop Down */
            $allCompanies = Company::whereIn("companySystemID", $subCompanies)->where("isGroup",0)->get();
        } else {
            $allCompanies = Company::where("companySystemID", $selectedCompanyId)->get();
        }

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /**Chart of Account Drop Down */
        $chartOfAccounts = ChartOfAccount::where('controllAccountYN', '=', 1)
            ->where('controlAccountsSystemID',3)
            ->where('catogaryBLorPL', '=', 'BS')
            ->orderBy('AccountDescription', 'asc')
            ->get();

        /**Country Drop Down */
        $country = CountryMaster::orderBy('countryName', 'asc')->get();

        $contactTypes = SupplierContactType::all();

        $hasPolicy = false;
        if($customerID !=0){
            $customer = CustomerMaster::find($customerID);
            if(isset($customer->primaryCompanySystemID) && $customer->primaryCompanySystemID){
                $hasPolicy = CompanyPolicyMaster::where('companySystemID', $customer->primaryCompanySystemID)
                    ->where('companyPolicyCategoryID', 39)
                    ->where('isYesNO',1)
                    ->exists();
            }
        }

        $output = array(
            'allCompanies' => $allCompanies,
            'yesNoSelection' => $yesNoSelection,
            'chartOfAccounts' => $chartOfAccounts,
            'country' => $country,
            'contactTypes' => $contactTypes,
            'isCustomerCatalogPolicyOn'=>$hasPolicy
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getChartOfAccountsByCompanyForCustomer(Request $request)
    {
        $input = $request->all();

        $chartOfAccounts = ChartOfAccount::where('controllAccountYN', '=', 1)
                                         ->whereHas('chartofaccount_assigned', function($query) use ($input) {
                                            $query->where('companySystemID', $input['companySystemID'])
                                                  ->where('isAssigned', -1)    
                                                  ->where('isActive', 1);    
                                         })
                                        ->where('controlAccountsSystemID',3)
                                        ->where('catogaryBLorPL', '=', 'BS')
                                        ->orderBy('AccountDescription', 'asc')
                                        ->get();

        return $this->sendResponse($chartOfAccounts, 'Record retrieved successfully');

    }

    public function getCustomerCatgeoryByCompany(Request $request)
    {
        $input = $request->all();

        $customerCategories = CustomerMasterCategory::whereHas('category_assigned', function ($query) use ($input) {
                                                        $query->when(isset($input['companySystemID']), function($query) use ($input){
                                                                $query->where('companySystemID', $input['companySystemID']);
                                                            })
                                                            ->when(isset($input['companySystemIDFilter']), function($query) use ($input){
                                                                $companyId = $input['companySystemIDFilter'];

                                                                $isGroup = \Helper::checkIsCompanyGroup($companyId);

                                                                if ($isGroup) {
                                                                    $childCompanies = \Helper::getGroupCompany($companyId);
                                                                } else {
                                                                    $childCompanies = [$companyId];
                                                                }

                                                                $query->whereIn('companySystemID', $childCompanies);
                                                            });
                                                    })
                                                    ->get();

        return $this->sendResponse($customerCategories, 'Record retrieved successfully');
    }

    /**
     * get all Assigned Companies for Customer
     * GET /getAssignedCompaniesByCustomer
     *
     * @param Request $request
     * @return Response
     */
    public function getAssignedCompaniesByCustomer(Request $request)
    {

        $customerId = $request['customerId'];
        $selectedCompanyId = $request['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }

        $customer = CustomerMaster::where('customerCodeSystem', '=', $customerId)->first();
        if ($customer) {
            $customerCompanies = CustomerAssigned::where('customerCodeSystem', $customerId)
                ->with(['company'])
                ->whereIn("companySystemID",$subCompanies)
                ->orderBy('customerAssignedID', 'DESC')
                ->get();
        } else {
            $customerCompanies = [];
        }

        return $this->sendResponse($customerCompanies, 'customer companies retrieved successfully');
    }

    /**
     * Store a newly created CustomerMaster in storage.
     * POST /customerMasters
     *
     * @param CreateCustomerMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerMasterAPIRequest $request)
    {

        $input = $request->all();

        if (isset($input['gl_account'])) {
            unset($input['gl_account']);
        }

        if (isset($input['unbilled_account'])) {
            unset($input['unbilled_account']);
        }


        foreach ($input as $key => $value) {
            if (is_array($input[$key])) {
                if (count($input[$key]) > 0) {
                    $input[$key] = $input[$key][0];
                } else {
                    $input[$key] = 0;
                }
            }
        }

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $empId = $user->employee['empID'];
        $empName = $user->employee['empName'];

        $validatorResult = \Helper::checkCompanyForMasters($input['primaryCompanySystemID']);
        if (!$validatorResult['success']) {
            return $this->sendError($validatorResult['message']);
        }

        $company = Company::where('companySystemID', $input['primaryCompanySystemID'])->first();

        if ($company) {
            $input['primaryCompanyID'] = $company->CompanyID;
        }


        if (array_key_exists('custGLAccountSystemID', $input)) {
            $financePL = ChartOfAccount::where('chartOfAccountSystemID', $input['custGLAccountSystemID'])->first();
            if ($financePL) {
                $input['custGLaccount'] = $financePL->AccountCode;
            }
        }

        if (array_key_exists('custUnbilledAccountSystemID', $input)) {
            $unbilled = ChartOfAccount::where('chartOfAccountSystemID', $input['custUnbilledAccountSystemID'])->first();
            if ($unbilled) {
                $input['custUnbilledAccount'] = $unbilled->AccountCode;
            }
        }

        $commonValidorMessages = [
            'customerCountry.required' => 'Country field is required.'
        ];

        $commonValidator = \Validator::make($input, [
            'customerCountry' => 'required',
        ], $commonValidorMessages);

        if ($commonValidator->fails()) {
            return $this->sendError($commonValidator->messages(), 422);
        }

        if($input['customerCountry']==0 || $input['customerCountry']==''){
            return $this->sendError('Country field is required',500);
        }


        if (array_key_exists('customerCodeSystem', $input)) {

            $customerMasters = CustomerMaster::where('customerCodeSystem', $input['customerCodeSystem'])->first();

            if (empty($customerMasters)) {
                return $this->sendError('customer not found');
            }
            $customerMasterOld = $customerMasters->toArray();
            if($customerMasters->approvedYN){
                $employee = Helper::getEmployeeInfo();
                //check policy 5
                $policy = Helper::checkRestrictionByPolicy($input['primaryCompanySystemID'],5);
                $customerId = $customerMasters->customerCodeSystem;
                if($policy){
                    $validorMessages = [
                        'creditDays.required' => 'Credit Period field is required.',
                        'creditDays.numeric' => 'Credit Period field is required.'
                    ];
                    $validator = \Validator::make($input, [
                        'creditDays' => 'required|numeric',
                    ],$validorMessages);

                    if ($validator->fails()) {
                        return $this->sendError($validator->messages(), 422);
                    }
                    $customerMasters = $this->customerMasterRepository->update(array_only($input,['creditLimit','creditDays','vatEligible','vatNumber','vatPercentage', 'customerSecondLanguage', 'reportTitleSecondLanguage', 'addressOneSecondLanguage', 'addressTwoSecondLanguage']), $customerId);
                    CustomerAssigned::where('customerCodeSystem',$customerId)->update(array_only($input,['creditLimit','creditDays','vatEligible','vatNumber','vatPercentage']));
                    // user activity log table
                    if($customerMasters){
                        $old_array = array_only($customerMasterOld,['creditDays','vatEligible','vatNumber','vatPercentage', 'customerSecondLanguage', 'reportTitleSecondLanguage', 'addressOneSecondLanguage', 'addressTwoSecondLanguage']);
                        $modified_array = array_only($input,['creditDays','vatEligible','vatNumber','vatPercentage', 'customerSecondLanguage', 'reportTitleSecondLanguage', 'addressOneSecondLanguage', 'addressTwoSecondLanguage']);

                        // update in to user log table
                        foreach ($old_array as $key => $old){
                            if($old != $modified_array[$key]){
                                $description = $employee->empName." Updated customer (".$customerMasters->CutomerCode.") from ".$old." To ".$modified_array[$key]."";
                               // UserActivityLogger::createUserActivityLogArray($employee->employeeSystemID,$customerMasters->documentSystemID,$customerMasters->primaryCompanySystemID,$customerMasters->supplierCodeSystem,$description,$modified_array[$key],$old,$key);
                            }
                        }
                    }

                    return $this->sendResponse($customerMasters, 'Customer Master updated successfully');
                }
                return $this->sendError('Customer Master is already approved , You cannot update.',500);
            }


            if ($customerMasters->confirmedYN == 0 && $input['confirmedYN'] == 1) {
                $params = array('autoID' => $input['customerCodeSystem'], 'company' => $input["primaryCompanySystemID"], 'document' => $input["documentSystemID"]);
                $confirm = \Helper::confirmDocument($params);
                if (!$confirm["success"]) {
                    return $this->sendError($confirm["message"], 500);
                }
            }

            foreach ($input as $key => $value) {
                $customerMasters->$key = $value;
            }

            $customerMasters->modifiedPc = gethostname();
            $customerMasters->modifiedUser = $empId;
            $customerMasters->save();
        } else {

            $document = DocumentMaster::where('documentID', 'CUSTM')->first();
            $input['documentSystemID'] = $document->documentSystemID;
            $input['documentID'] = $document->documentID;

            $lastCustomer = CustomerMaster::orderBy('customerCodeSystem', 'DESC')->first();
            $lastSerialOrder = 1;
            if(!empty($lastCustomer)){
                $lastSerialOrder = $lastCustomer->lastSerialOrder + 1;
            }

            $customerCode = 'C' . str_pad($lastSerialOrder, 7, '0', STR_PAD_LEFT);

            $input['lastSerialOrder'] = $lastSerialOrder;
            $input['CutomerCode'] = $customerCode;
            $input['createdPcID'] = gethostname();
            $input['createdUserID'] = $empId;
            $input['isCustomerActive'] = 1;
            $customerMasters = $this->customerMasterRepository->create($input);
        }

        return $this->sendResponse($customerMasters->toArray(), 'Customer Master saved successfully');
    }


    /**
     * Display the specified CustomerMaster.
     * GET|HEAD /customerMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CustomerMaster $customerMaster */
        $customerMaster = $this->customerMasterRepository->with(['finalApprovedBy', 'gl_account', 'unbilled_account'])->findWithoutFail($id);
        // $customerMasters = CustomerMaster::where('customerCodeSystem', $id)->first();
        if (empty($customerMaster)) {
            return $this->sendError('Customer Master not found');
        }

        return $this->sendResponse($customerMaster->toArray(), 'Customer Master retrieved successfully');
    }

    /**
     * Update the specified CustomerMaster in storage.
     * PUT/PATCH /customerMasters/{id}
     *
     * @param  int $id
     * @param UpdateCustomerMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerMasterAPIRequest $request)
    {
        $input = $request->all();

        $input = array_except($input, ['final_approved_by']);

        /** @var CustomerMaster $customerMaster */
        $customerMaster = $this->customerMasterRepository->findWithoutFail($id);

        if (empty($customerMaster)) {
            return $this->sendError('Customer Master not found');
        }

        if (array_key_exists('custUnbilledAccountSystemID', $input)) {
            $unbilled = ChartOfAccount::where('chartOfAccountSystemID', $input['custUnbilledAccountSystemID'])->first();
            if ($unbilled) {
                $input['custUnbilledAccount'] = $unbilled->AccountCode;
            }
        }

        $customerMaster = $this->customerMasterRepository->update($input, $id);

        return $this->sendResponse($customerMaster->toArray(), 'CustomerMaster updated successfully');
    }

    /**
     * Remove the specified CustomerMaster from storage.
     * DELETE /customerMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CustomerMaster $customerMaster */
        $customerMaster = $this->customerMasterRepository->findWithoutFail($id);

        if (empty($customerMaster)) {
            return $this->sendError('Customer Master not found');
        }

        $customerMaster->delete();

        return $this->sendResponse($id, 'Customer Master deleted successfully');
    }

    public function approveCustomer(Request $request)
    {
        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }

    }

    public function rejectCustomer(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }
    }

    /**
     *  Search Customer By Company
     * GET /getSearchCustomerByCompany
     *
     * @param  int $id
     *
     * @return Response
     */
    public function getSearchCustomerByCompany(Request $request)
    {

        $companyId = $request->companyId;
        $input = $request->all();
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $companies = \Helper::getGroupCompany($companyId);
        } else {
            $companies = [$companyId];
        }

        $customers = CustomerAssigned::whereIn('companySystemID', $companies)
            ->select(['customerCodeSystem', 'CustomerName', 'CutomerCode'])
            ->when(request('search', false), function ($q, $search) {
                return $q->where(function ($query) use ($search) {
                    return $query->where('CutomerCode', 'LIKE', "%{$search}%")
                        ->orWhere('customerShortCode', 'LIKE', "%{$search}%")
                        ->orWhere('CustomerName', 'LIKE', "%{$search}%");
                });
            })
            //->take(20)
            ->get();


        return $this->sendResponse($customers->toArray(), 'Customer Master retrieved successfully');
    }

    public function getJobsByContractAndCustomer(Request $request)
    {
        $companyId = $request->companyId;
        $input = $request->all();
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $companies = \Helper::getGroupCompany($companyId);
        } else {
            $companies = [$companyId];
        }

        $jobs = TicketMaster::whereIn('companySystemID', $companies)
            ->where('clientSystemID', $input['customer_id'])
            ->where('contractUID', $input['contractUIID'])
            //->where('jobStartedYNBM', 1)
            ->where('jobEndYNSup', '!=', 1)
            ->when(request('search', false), function ($q, $search) {
                return $q->where(function ($query) use ($search) {
                    return $query->where('ticketNo', 'LIKE', "%{$search}%");
                });
            })
            ->get(['ticketidAtuto', 'ticketNo']);


        return $this->sendResponse($jobs->toArray(), 'Jobs by Customer retrieved successfully');
    }

    public function getContractByCustomer(Request $request)
    {

        $companyId = $request->companyId;
        $input = $request->all();
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $companies = \Helper::getGroupCompany($companyId);
        } else {
            $companies = [$companyId];
        }

        $contract = Contract::whereIn('companySystemID', $companies)
            ->where('clientID', $input['customer_id'])
            ->when(request('search', false), function ($q, $search) {
                return $q->where(function ($query) use ($search) {
                    return $query->where('ContractNumber', 'LIKE', "%{$search}%");
                });
            })
            ->get(['ContractNumber', 'contractUID']);


        return $this->sendResponse($contract->toArray(), 'Contracts by Customer retrieved successfully');
    }

    public function getCustomerByCompany(Request $request)
    {

        $companySystemID = $request['companySystemID'];

        $customerCompanies = CustomerAssigned::where('companySystemID', $companySystemID)
            ->with(['company', 'customer_master' => function ($query) {
                $query->select('customerCodeSystem', 'companyLinkedToSystemID');
            }])
            ->orderBy('customerAssignedID', 'DESC')
            ->get();


        return $this->sendResponse($customerCompanies->toArray(), 'customer companies retrieved successfully');
    }

    public function customerReferBack(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];

        $customer = $this->customerMasterRepository->find($id);
        if (empty($customer)) {
            return $this->sendError('Customer Master not found');
        }

        if ($customer->refferedBackYN != -1) {
            return $this->sendError('You cannot refer back this customer');
        }

        $customerArray = $customer->toArray();

        $storeHistory = CustomerMasterRefferedBack::insert($customerArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $customer->primaryCompanySystemID)
            ->where('documentSystemID', $customer->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $customer->timesReferred;
            }
        }

        $documentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentRefereedHistory = DocumentReferedHistory::insert($documentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $customer->primaryCompanySystemID)
            ->where('documentSystemID', $customer->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $updateArray = ['refferedBackYN' => 0,
                'confirmedYN' => 0,
                'confirmedEmpSystemID' => null,
                'confirmedEmpID' => null,
                'confirmedEmpName' => null,
                'confirmedDate' => null,
                'RollLevForApp_curr' => 1];

            $this->customerMasterRepository->update($updateArray, $id);
        }

        return $this->sendResponse($customer->toArray(), 'Customer Master Amend successfully');
    }

    public function exportCustomerMaster(Request $request)
    {
        $input = $request->all();
        $type = $input['type'];

        $customerMasters = CustomerMaster::with(['country', 'customer_default_currency' => function($query){
            $query->where('isDefault',-1)
                ->with(['currencyMaster']);
        }, 'customer_default_contacts' => function($query){
            $query->where('isDefault',-1);
        }])->get();

        if ($customerMasters) {
            $x = 0;
            $data = array();
            foreach ($customerMasters as $val) {
                $data[$x]['Primary Code'] = $val->CutomerCode;
                $data[$x]['Secondary Code'] = $val->customerShortCode;
                $data[$x]['Customer Name'] = $val->CustomerName;
                $data[$x]['Report Title'] = $val->ReportTitle;
                $data[$x]['Currency'] = isset($val->customer_default_currency->currencyMaster->CurrencyCode)?$val->customer_default_currency->currencyMaster->CurrencyCode:'-';
                $data[$x]['Credit Period'] = $val->creditDays;
                $data[$x]['Credit Limit'] = $val->creditLimit;
                $data[$x]['Address'] = $val->customerAddress1.", ".$val->customerAddress2;
                $data[$x]['City'] = $val->customerCity;
                $data[$x]['Country'] = isset($val->country->countryName)?$val->country->countryName:"-";
                $data[$x]['Telephone'] = isset($val->customer_default_contacts->contactPersonTelephone)?$val->customer_default_contacts->contactPersonTelephone:0;
                $data[$x]['Email'] = isset($val->customer_default_contacts->contactPersonEmail)?$val->customer_default_contacts->contactPersonEmail:'-';

                $x++;
            }
        } else {
            $data = array();
        }

         \Excel::create('customer_master', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);

        return $this->sendResponse(array(), 'successfully export');
    }

 public function getPosCustomerSearch(Request $request)
{
    $input = $request->all();
    $input['warehouseSystemCode'] = isset($input['warehouseSystemCode']) ? $input['warehouseSystemCode'] : 0;
    $companyId = isset($input['companyId']) ? $input['companyId'] : 0;
    $customers = CustomerAssigned::where('companySystemID', $companyId)
        ->where('financeCategoryMaster', 1)
        ->where('isPOSItem', 1)
        ->with(['unit', 'outlet_items' => function ($q) use($input){
            $q->where('warehouseSystemCode',$input['warehouseSystemCode']);
        },'item_ledger' => function($q) use($input){
            $q->where('warehouseSystemCode',$input['warehouseSystemCode'])
                ->groupBy('itemSystemCode')
                ->selectRaw('sum(inOutQty) AS stock,itemSystemCode');
        }])
        ->whereHas('outlet_items', function ($q) use($input){
            $q->where('warehouseSystemCode',$input['warehouseSystemCode']);
        })
        ->whereHas('item_ledger', function ($q) use($input){
            $q->where('warehouseSystemCode',$input['warehouseSystemCode'])
                ->groupBy('itemSystemCode')
                ->havingRaw('sum(inOutQty) > 0 ');
        })
        ->select(['itemPrimaryCode', 'itemDescription', 'itemCodeSystem', 'idItemAssigned', 'secondaryItemCode', 'itemUnitOfMeasure', 'sellingCost','barcode']);


    if (array_key_exists('search', $input)) {
        $search = $input['search'];
        $customers = $customers->where(function ($query) use ($search) {
            $query->where('CutomerCode', 'LIKE', "%{$search}%")
                ->orWhere('customerShortCode', 'LIKE', "%{$search}%")
                ->orWhere('CustomerName', 'LIKE', "%{$search}%");
        });
    }

    $customers = $customers->take(10)->get();

    return $this->sendResponse($customers->toArray(), 'Data retrieved successfully');
}

    public function getSelectedCompanyReportingCurrencyData(Request $request)
    {
        $reportingCurrency = "USD";
        $reportCurrencyDecimalPlace = 2;
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        $companySystemID = isset($input['companySystemID']) ? $input['companySystemID'] : 0;

        $comanyMasterData = Company::find($companySystemID);
        if ($comanyMasterData) {
            $currencyData = CurrencyMaster::find($comanyMasterData->reportingCurrency);
            if ($currencyData) {
                $reportCurrencyDecimalPlace = $currencyData->DecimalPlaces;
                $reportingCurrency = $currencyData->CurrencyCode;
            }
        }

        $resData = [
            'reportingCurrency' => $reportingCurrency,
            'reportCurrencyDecimalPlace' => $reportCurrencyDecimalPlace
        ];


        return $this->sendResponse($resData, 'Data retrieved successfully');
    }

     public function customerReOpen(Request $request)
    {
        $reopen = ReopenDocument::reopenDocument($request);
        if (!$reopen["success"]) {
            return $this->sendError($reopen["message"]);
        } else {
            return $this->sendResponse(array(), $reopen["message"]);
        }
    }
}
