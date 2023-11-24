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
use App\Models\SupplierCategoryMaster;
use App\Models\SupplierImportance;
use App\Models\SupplierType;
use App\Models\suppliernature;
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
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Repositories\SupplierMasterRepository;
use App\Models\SupplierCurrency;
use App\Models\BankMemoTypes;
use App\Models\BankMemoSupplier;
use App\Models\FinanceItemCategoryMaster;
use App\Models\FinanceItemCategorySub;
use App\Models\Unit;
use App\Repositories\ItemMasterRepository;
use App\Models\SupplierMaster;
use App\helper\CreateExcel;
use App\Models\CustomerInvoice;
use App\Models\DeliveryOrder;
use App\Models\CreditNote;
use App\Models\CustomerReceivePayment;
use App\Models\QuotationMaster;
/**
 * Class CustomerMasterController
 * @package App\Http\Controllers\API
 */
class CustomerMasterAPIController extends AppBaseController
{
    /** @var  CustomerMasterRepository */
    private $customerMasterRepository;
    private $userRepository;
    private $supplierMasterRepository;
    private $itemMasterRepository;
    public function __construct(ItemMasterRepository $itemMasterRepo,SupplierMasterRepository $supplierMasterRepo,CustomerMasterRepository $customerMasterRepo, UserRepository $userRepo)
    {
        $this->customerMasterRepository = $customerMasterRepo;
        $this->userRepository = $userRepo;
        $this->supplierMasterRepository = $supplierMasterRepo;
        $this->itemMasterRepository = $itemMasterRepo;
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
        $values = implode(',', array_map(function($value)
        {
            return trim($value, ',');
        }, $companyID));


        $search = $request->input('search.value');
        $filter='';
        if($search){
            $search = str_replace("\\", "\\\\\\\\", $search);
            $filter = "AND (( CutomerCode LIKE '%{$search}%') OR ( customerShortCode LIKE '%{$search}%') OR ( CustomerName LIKE '%{$search}%'))";
        }

        $sql = "SELECT customermaster.*, countrymaster.countryName, erp_documentapproved.documentApprovedID, rollLevelOrder, approvalLevelID, documentSystemCode FROM erp_documentapproved
        inner join employeesdepartments on erp_documentapproved.approvalGroupID =  employeesdepartments.employeeGroupID 
        and erp_documentapproved.documentSystemID = employeesdepartments.documentSystemID
        and erp_documentapproved.companySystemID = employeesdepartments.companySystemID
        inner join customermaster on customermaster.customerCodeSystem = erp_documentapproved.documentSystemCode AND
        erp_documentapproved.rollLevelOrder = customermaster.RollLevForApp_curr
        left join countrymaster on customermaster.customerCountry = countryID
        where customermaster.approvedYN = 0 
        {$filter}
        AND customermaster.confirmedYN = 1
        AND employeesdepartments.documentSystemID = 58 
        AND erp_documentapproved.approvedYN = 0
        AND erp_documentapproved.rejectedYN = 0
        AND erp_documentapproved.documentSystemID = 58
        AND employeesdepartments.isActive = 1
        AND employeesdepartments.employeeSystemID = $empID
        AND employeesdepartments.removedYN = 0
        AND employeesdepartments.companySystemID IN ($values) AND primaryCompanySystemID IN ($values)
        GROUP BY customerCodeSystem ORDER BY documentApprovedID 
        ";

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        $customerMasters = DB::select($sql);
        if ($isEmployeeDischarched == 'true') {
            $customerMasters = [];
        }

        return \DataTables::of($customerMasters)
            ->addIndexColumn()
            ->with('orderCondition', $sort)
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
            'company' => (isset($customer)) ? $customer->company : "",
            'contactTypes' => $contactTypes,
            'isCustomerCatalogPolicyOn'=>$hasPolicy
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getApprovedCustomers(Request $request){

        $customers = CustomerMaster::where('primaryCompanySystemID', $request['companySystemID'])->where('approvedYN',1)->get();

        return $this->sendResponse($customers, 'Record retrieved successfully');

    }

    public function getLinkedSupplier(Request $request){

        $supplier = SupplierMaster::where('primaryCompanySystemID', $request['selectedCompanyId'])->where('linkCustomerID',$request['customerID'])->where('linkCustomerYN',1)->first();

        return $this->sendResponse($supplier, 'Record retrieved successfully');

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
                                                            })
                                                            ->where('isAssigned', 1);
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
        $input = $this->convertArrayToSelectedValue($input, array('custGLAccountSystemID', 'custUnbilledAccountSystemID'));

        if($input['custGLAccountSystemID'] == $input['custUnbilledAccountSystemID'] ){
            return $this->sendError('Receivable account and unbilled account cannot be same. Please select different chart of accounts.');
        }
       
        if (isset($input['gl_account'])) {
            unset($input['gl_account']);
        }

        if (array_key_exists('unbilled_account', $input)) {
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

        if($input['custUnbilledAccountSystemID'] == 0){
            return $this->sendError('Unbilled Receivable Account field is required.');
        }

        if(isset($input['customer_registration_no']) && $input['customer_registration_no']){
            if(!$input['customer_registration_expiry_date']){
                return $this->sendError('Registration expiry date is required.');
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
            'customerCountry.required' => 'Country field is required.',
            'custUnbilledAccountSystemID.required' => 'Unbilled Receivable Account field is required.'
        ];

        $commonValidator = \Validator::make($input, [
            'customerCountry' => 'required',
            'custUnbilledAccountSystemID' => 'required'

        ], $commonValidorMessages);

        if ($commonValidator->fails()) {
            return $this->sendError($commonValidator->messages(), 422);
        }

        if($input['customerCountry']==0 || $input['customerCountry']==''){
            return $this->sendError('Country field is required',500);
        }

        if (isset($input['interCompanyYN']) && $input['interCompanyYN']) {
            if (!isset($input['companyLinkedToSystemID'])) {
                return $this->sendError('Linked company is required',500);
            }

            $checkCustomerForInterCompany = CustomerMaster::where('companyLinkedToSystemID', $input['companyLinkedToSystemID'])
                                           ->when(array_key_exists('customerCodeSystem', $input), function($query) use ($input) {
                                                $query->where('customerCodeSystem', '!=', $input['customerCodeSystem']);
                                           })
                                           ->first();

            if ($checkCustomerForInterCompany) {
                return $this->sendError('The selected company is already assigned to ' .$checkCustomerForInterCompany->CustomerName,500);
            }


            $linkedCompany = Company::find($input['companyLinkedToSystemID']);

            $input['companyLinkedTo'] = ($linkedCompany) ? $linkedCompany->CompanyID : null; 
        } else {
            $input['companyLinkedTo'] = null;
            $input['companyLinkedToSystemID'] = null;
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
                    $customerMasters = $this->customerMasterRepository->update(array_only($input,['customer_registration_expiry_date','customer_registration_no','creditLimit','creditDays','consignee_address','consignee_contact_no','consignee_name','payment_terms','vatEligible','vatNumber','vatPercentage', 'customerSecondLanguage', 'reportTitleSecondLanguage', 'addressOneSecondLanguage', 'addressTwoSecondLanguage','customerShortCode','CustomerName','ReportTitle','customerAddress1','customerAddress2','customerCategoryID','interCompanyYN','customerCountry','customerCity','isCustomerActive','custGLAccountSystemID','custUnbilledAccountSystemID', 'companyLinkedToSystemID', 'companyLinkedTo']), $customerId);
                    CustomerAssigned::where('customerCodeSystem',$customerId)->update(array_only($input,['creditLimit','creditDays','consignee_address','consignee_contact_no','consignee_name','payment_terms','vatEligible','vatNumber','vatPercentage','customerShortCode','CustomerName','ReportTitle','customerAddress1','customerAddress2','customerCategoryID','customerCountry','customerCity','custGLAccountSystemID','custUnbilledAccountSystemID']));
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
        $selectedCompanySystemID = $request['selectedCompanySystemID'];

        if(isset($request['selectedAssetDisposalType']) && $request['selectedAssetDisposalType'] == 6) {
            $customerCompanies = CustomerAssigned::where('companySystemID', $selectedCompanySystemID)
            ->with(['company', 'customer_master' => function ($query) use ($companySystemID) {
                $query->select('customerCodeSystem');
            }])
            ->whereHas('customer_master', function($query) use ($companySystemID){
                $query->where('isCustomerActive', 1);
            })
            ->where('isAssigned', -1)
            ->orderBy('customerAssignedID', 'DESC')
            ->get();
        }else {
            $customerCompanies = CustomerAssigned::where('companySystemID', $selectedCompanySystemID)
            ->with(['company', 'customer_master' => function ($query) use ($companySystemID) {
                $query->select('customerCodeSystem', 'companyLinkedToSystemID')
                      ->where('companyLinkedToSystemID', $companySystemID);
            }])
            ->whereHas('customer_master', function($query) use ($companySystemID){
                $query->where('companyLinkedToSystemID', $companySystemID)
                      ->where('isCustomerActive', 1);
            })
            ->where('isAssigned', -1)
            ->orderBy('customerAssignedID', 'DESC')
            ->get();
        }



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
        $companyMaster = Company::find(isset($request->companyId)?$request->companyId:null);
        $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
        $detail_array = array(
            'company_code'=>$companyCode,
        );
        $fileName = 'customer_master';
        $path = 'system/customer_master/excel/';
        $type = 'xls';
        $basePath = CreateExcel::process($data,$type,$fileName,$path,$detail_array);

        if($basePath == '')
        {
             return $this->sendError('Unable to export excel');
        }
        else
        {
             return $this->sendResponse($basePath, trans('custom.success_export'));
        }


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

    
    public function downloadTemplate(Request $request)
    {
        $input = $request->all();
        $document_id = $input['document_id'];

        $isPosIntegrated = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyPolicyCategoryID', 69)
            ->where('isYesNO', 1)
            ->exists();

        $disk = Helper::policyWiseDisk($input['companySystemID'], 'public');

        if($input['document_id'] == 57 && $isPosIntegrated){

            if ($exists = Storage::disk($disk)->exists('Master_Template/'.$document_id.'/item_pos_template.xlsx')) {
                return Storage::disk($disk)->download('Master_Template/'.$document_id.'/item_pos_template.xlsx', 'item_pos_template.xlsx');
            } else {
                return $this->sendError('Attachments not found', 500);
            }
        } else {

            if ($exists = Storage::disk($disk)->exists('Master_Template/'.$document_id.'/template.xlsx')) {
                return Storage::disk($disk)->download('Master_Template/'.$document_id.'/template.xlsx', 'template.xlsx');
            } else {
                return $this->sendError('Attachments not found', 500);
            }

        }

    }

    public function masterBulkUpload(request $request)
    {
        try {

            
           
            $input = $request->all();
            $document_id = $input['documnet_id'];
            $companySystemID = $input['companySystemID'];
            $excelUpload = $input['itemExcelUpload'];

            $input['companySystemIDFilter'] = $companySystemID;
            $input = array_except($request->all(), 'itemExcelUpload');
            $input = $this->convertArrayToValue($input);

            $decodeFile = base64_decode($excelUpload[0]['file'],true);
            $originalFileName = $excelUpload[0]['filename'];
            $extension = $excelUpload[0]['filetype'];
            $size = $excelUpload[0]['size'];
      
            $allowedExtensions = ['xlsx','xls'];

       
            $id = Auth::id();
            $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
            $empId = $user->employee['empID'];
            $empName = $user->employee['empName'];

            if (!in_array($extension, $allowedExtensions))
            {
                return $this->sendError('This type of file not allow to upload.you can only upload .xlsx (or) .xls',500);
            }

            if ($size > 20000000) {
                return $this->sendError('The maximum size allow to upload is 20 MB',500);
            }

           
           
            $disk = 'local';
            Storage::disk($disk)->put($originalFileName, $decodeFile);
            //die();
            //$originalFileName = 'item_template.xlsx';
            
         
            $formatChk = \Excel::selectSheetsByIndex(0)->load(Storage::disk($disk)->url('app/' . $originalFileName), function ($reader) {
              
            })->get();
             $uniqueData = array_filter(collect($formatChk)->toArray());


  
           
            //  $record = \Excel::selectSheetsByIndex(0)->load(Storage::disk($disk)->url('app/' . $originalFileName), function ($reader) {
            // })->select(array('company', 'secondary_code', 'customer_name', 'report_title','gl_account','country','credit_limit_usd','credit_period'))->get()->toArray();

         
             $total_count = count($uniqueData);


        

             $supplier_error = array(
              'Primary Company' => array(),
              'Supplier Name' => array(),
              'Check Name' => array(),
              'Address' => array(),
              'Country'=> array(),
              'Category'=>array(),
              'Mobile'=>array(),
              'Email'=>array(),
              'Currency' =>array(),
              'Credit Limit'=>array(),
              'Credit Period'=>array(),
              'Register Number'=>array(),
              'Register Expire'=>array()
           );
            

            $customer_error = array(
                'Primary Company' => array(),
                'Secondary Code' => array(),
                'Customer Name' => array(),
                'Report title' => array(),
                'GL Account'=> array(),
                'Country'=>array(),
                'Credit Limit (USD)'=>array(),
                'Credit Period'=>array()
            );

            $item_error = array(
                'Primary Company' => array(),
                'Finance Category' => array(),
                'Finance Sub Category' => array(),
                'Part No / Ref.Number' => array(),
                'Item Description'=> array(),
                'Unit of Measure'=>array(),
                'POS type'=>array()
            );
    
          
       
             $totalItemCount = 0;
             $valueNotExit = false;
             $groupOfComapnyFalse = false;
             $nullValue = false;
             $succesfully_created = 0;
             $notValid = false;
             $current_date = date('Y-m-d H:i:s');

            switch ($document_id) {
                case '58':
                    $count = 1;

             
                    foreach ($formatChk as $key => $value)
                     {
                        $customer_data = [];
                        $company_group_msg = '';

                        $supplier_data = [];
                        $company_group_msg = '';

                        $employee = \Helper::getEmployeeInfo();
                   
                        $count++;
                       
                        if ( (isset($value['company']) && !is_null($value['company'])) 
                            || (isset($value['secondary_code']) && !is_null($value['secondary_code'])) 
                            || (isset($value['customer_name']) && !is_null($value['customer_name'])) 
                            || (isset($value['report_title']) && !is_null($value['report_title'])) 
                            || (isset($value['gl_account']) && !is_null($value['gl_account'])) 
                            || (isset($value['country']) && !is_null($value['country'])) 
                            || (isset($value['credit_limit_usd']) && !is_null($value['credit_limit_usd'])) 
                            || (isset($value['credit_period']) && !is_null($value['credit_period'])) 
                            )
                            {
                              $totalItemCount++;
                            }
                            //check companu validation
                            if ( (isset($value['company']) && !is_null($value['company'])) )
                            {

                                $company = Company::where('CompanyID', $value['company'])->select('companySystemID','companyID')->first();

                             
                                if(isset($company))
                                {
                                    $validatorResult = \Helper::checkCompanyForMasters($company->companySystemID);

                                    if (!$validatorResult['success']) {
                                            $groupOfComapnyFalse = true;
                                            $company_group_msg = $validatorResult['message'];
                                            $name = 'N/A';
                                            if(isset($value['customer_name']))
                                            {
                                                $name = $value['customer_name'];
                                            }
                                            array_push($customer_error['Primary Company'], $name.',line number '.$count.' company not exist');
                                    }
                                    else
                                    {
                                        $customer_data['primaryCompanySystemID'] = $company->companySystemID;
                                        $customer_data['primaryCompanyID'] = $company->companyID;
                                    }
                            
                                }
                                else
                                {
                                    $valueNotExit = true;
                                    $name = 'N/A';
                                    if(isset($value['customer_name']))
                                    {
                                        $name = $value['customer_name'];
                                    }
                                    array_push($customer_error['Primary Company'], $name.',line number '.$count.' company not exist');

                                    
                                }

                            }
                            else
                            {
                                $nullValue = true;
                                $name = 'N/A';
                                if(isset($value['customer_name']))
                                {
                                    $name = $value['customer_name'];
                                }
                                array_push($customer_error['Primary Company'], $name.',line number '.$count.' null value');
                            }
                         

                            //check gl account validation
                            if ( (isset($value['gl_account']) && !is_null($value['gl_account'])) )
                            {
                         
                                $gl_account = ChartOfAccount::where('AccountCode', $value['gl_account'])
                                ->where('controllAccountYN', '=', 1)
                                         ->whereHas('chartofaccount_assigned', function($query) use ($input) {
                                            $query->where('companySystemID', $input['companySystemID'])
                                                  ->where('isAssigned', -1)    
                                                  ->where('isActive', 1);    
                                         })
                                        ->where('controlAccountsSystemID',3)
                                        ->where('catogaryBLorPL', '=', 'BS')
                                ->select('chartOfAccountSystemID','AccountCode')->first();

                            
                                if(!isset($gl_account))
                                {
                                    $valueNotExit = true;
                                    $name = 'N/A';
                                    if(isset($value['customer_name']))
                                    {
                                        $name = $value['customer_name'];
                                    }
                                    array_push($customer_error['GL Account'], $name.',line number '.$count.' value invalid');
                            
                                }else
                                {
                                    $customer_data['custGLAccountSystemID'] = $gl_account->chartOfAccountSystemID;
                                    $customer_data['custGLaccount'] = $gl_account->AccountCode;
                                }
                               
                            }
                            else
                            {
                                $nullValue = true;

                                $name = 'N/A';
                                if(isset($value['customer_name']))
                                {
                                    $name = $value['customer_name'];
                                }
                                array_push($customer_error['GL Account'], $name.',line number '.$count.' null value');
                            }


                            //check country account validation
                            if ( (isset($value['country']) && !is_null($value['country'])) )
                            {

                                $country = CountryMaster::where('countryName','=', $value['country'])->select('countryID','countryName')->first();
                              
                                if(!isset($country))
                                {
                                    $valueNotExit = true;
                                    $name = 'N/A';
                                    if(isset($value['customer_name']))
                                    {
                                        $name = $value['customer_name'];
                                    }
                                    array_push($customer_error['Country'], $name.',line number '.$count.' invalid country');
                            
                                }
                                else
                                {
                                    $customer_data['customerCountry'] = $country->countryID;
                                }
                                
                            }
                            else
                            {
                                $nullValue = true;
                           
                                $name = 'N/A';
                                if(isset($value['customer_name']))
                                {
                                    $name = $value['customer_name'];
                                }
                         
                                array_push($customer_error['Country'], $name.',line number '.$count.' null value');

                             
                            }
                      
                            //check Unbilled Account validation
                            if ( (isset($value['unbilled_account']) && !is_null($value['unbilled_account'])) )
                            {

                                $unbilled_account = ChartOfAccount::where('AccountCode', $value['unbilled_account'])
                                ->where('controllAccountYN', '=', 1)
                                ->whereHas('chartofaccount_assigned', function($query) use ($input) {
                                   $query->where('companySystemID', $input['companySystemID'])
                                         ->where('isAssigned', -1)    
                                         ->where('isActive', 1);    
                                })
                               ->where('controlAccountsSystemID',3)
                               ->where('catogaryBLorPL', '=', 'BS')
                               ->select('chartOfAccountSystemID','AccountCode')->first();
                           
                                if(isset($unbilled_account))
                                {
                                    
                                    $customer_data['custUnbilledAccountSystemID'] = $unbilled_account->chartOfAccountSystemID;
                                    $customer_data['custUnbilledAccount'] = $unbilled_account->AccountCode;
                                }
                                
                            }

                            //check category Account validation
                            if ( (isset($value['category']) && !is_null($value['category'])) )
                            {

                                $category = CustomerMasterCategory::where('categoryDescription','=',$value['category'])
                                                        ->whereHas('category_assigned', function ($query) use ($input) {
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
                                                    })->select('categoryID')
                                                    ->first();


                                if(isset($category))
                                {
                                    
                                    $customer_data['customerCategoryID'] = $category->categoryID;
                                }
                                
                            }
                            
                            
                            //check Vat Eligible validation
                            if ( (isset($value['vat_eligible']) && !is_null($value['vat_eligible'])) )
                            {

                             
                                if ( $value['vat_eligible'] == 'Yes' || $value['vat_eligible'] == 'No') 
                                {
                                    if($value['vat_eligible'] == 'No')
                                    {
                                        $vat_el = '0';
                                    }
                                    else
                                    {
                                        $vat_el = '1';
                                    }
                                    $customer_data['vatEligible'] = $vat_el;
                                }
                          
                             
                            }
                            
                            //check customer short code
                            if ( (isset($value['secondary_code']) && !is_null($value['secondary_code'])) )
                            {
                                $customer_data['customerShortCode'] = $value['secondary_code'];
                            }
                            else
                            {
                                $nullValue = true;

                                $name = 'N/A';
                                if(isset($value['customer_name']))
                                {
                                    $name = $value['customer_name'];
                                }
                                array_push($customer_error['Secondary Code'], $name.',line number '.$count.' null value');
                            }
                     
                            //check customer name validation
                            if ( (isset($value['customer_name']) && !is_null($value['customer_name'])) )
                            {
                                if (!is_numeric($value['customer_name']))
                                {
                                    $customer_data['CustomerName'] = $value['customer_name'];
                                }
                                else
                                {
                                    $notValid = true;

                                    $name = 'N/A';
                                    if(isset($value['customer_name']))
                                    {
                                        $name = $value['customer_name'];
                                    }
                                    array_push($customer_error['Customer Name'], 'line number '.$count.' not only numeric value');
                                }
                                
                            }
                            else
                            {
                                $nullValue = true;

                                $name = 'N/A';
                                if(isset($value['customer_name']))
                                {
                                    $name = $value['customer_name'];
                                }
                                array_push($customer_error['Customer Name'], 'line number '.$count.' null value');
                            }
                            
                            //check report title
                            if ( (isset($value['report_title']) && !is_null($value['report_title'])) )
                            {
                                $customer_data['ReportTitle'] = $value['report_title'];
                            }
                            else
                            {
                                $nullValue = true;

                                $name = 'N/A';
                                if(isset($value['customer_name']))
                                {
                                    $name = $value['customer_name'];
                                }
                                array_push($customer_error['Report title'], $name.',line number '.$count.' null value');
                            }  
                            
                            //check address 1
                            if ( (isset($value['address_1']) && !is_null($value['address_1'])) )
                            {
                               
                                if (!is_numeric($value['address_1']))
                                {
                                    $customer_data['customerAddress1'] = $value['address_1'];
                                }
                             
                               
                            }
                      

                            //check address 2
                            if ( (isset($value['address_2']) && !is_null($value['address_2'])) )
                            {
                                if (!is_numeric($value['address_2']))
                                {
                                    $customer_data['customerAddress2'] = $value['address_2'];
                                }
                               
                            }

                            //check city
                            if ( (isset($value['city']) && !is_null($value['city'])) )
                            {
                                $customer_data['customerCity'] = $value['city'];
                            }

                            //check logo
                            if ( (isset($value['logo']) && !is_null($value['logo'])) )
                            {
                                $customer_data['customerLogo'] = $value['logo'];
                            }

                           //check webaddress
                            if ( (isset($value['web_address']) && !is_null($value['web_address'])) )
                            {
                                $customer_data['webAddress'] = $value['web_address'];
                            }

                            
                            //check Credit Limit
                            if ( (isset($value['credit_limit_usd']) && !is_null($value['credit_limit_usd'])) )
                            {
                               


                                if (is_numeric($value['credit_limit_usd'])){

                                    if($value['credit_limit_usd'] >= 0)
                                    {
                                        $customer_data['creditLimit'] = $value['credit_limit_usd'];
                                    }
                                    else
                                    {
                                        $notValid = true;

                                        $name = 'N/A';
                                        if(isset($value['customer_name']))
                                        {
                                            $name = $value['customer_name'];
                                        }
                                        array_push($customer_error['Credit Limit (USD)'], $name.',line number '.$count.' not a positive value');
                                    }
                                }
                                else
                                {
                                    $notValid = true;

                                    $name = 'N/A';
                                    if(isset($value['customer_name']))
                                    {
                                        $name = $value['customer_name'];
                                    }
                                    array_push($customer_error['Credit Limit (USD)'], $name.',line number '.$count.' not numeric value');
                                }


                            }
                            else
                            {
                                $nullValue = true;

                                $name = 'N/A';
                                if(isset($value['customer_name']))
                                {
                                    $name = $value['customer_name'];
                                }
                                array_push($customer_error['Credit Limit (USD)'], $name.',line number '.$count.' null value');
                            }  

                            
                            //check Credit Period
                            if ( (isset($value['credit_period']) && !is_null($value['credit_period'])) )
                            {


                                if (is_numeric($value['credit_period'])){

                                    if($value['credit_period'] >= 0)
                                    {
                                        $customer_data['creditDays'] = $value['credit_period'];
                                    }
                                    else
                                    {
                                        $notValid = true;
                                        $name = 'N/A';
                                        if(isset($value['customer_name']))
                                        {
                                            $name = $value['customer_name'];
                                        }
                                        array_push($customer_error['Credit Period'], $name.',line number '.$count.' not positive value');
                                    }
                                }
                                else
                                {
                                    $notValid = true;
                                    $name = 'N/A';
                                    if(isset($value['customer_name']))
                                    {
                                        $name = $value['customer_name'];
                                    }
                                    array_push($customer_error['Credit Period'], $name.',line number '.$count.' not numeric value');
                                }
                            }
                            else
                            {
                                $nullValue = true;
                                $name = 'N/A';
                                if(isset($value['customer_name']))
                                {
                                    $name = $value['customer_name'];
                                }
                                array_push($customer_error['Credit Period'], $name.',line number '.$count.' null value');
                            }  

                            //check vatNumber
                            if ( (isset($value['vat_number']) && !is_null($value['vat_number'])) )
                            {
                               
                                if (is_numeric($value['vat_number'])){

                                    if($value['vat_number'] >= 0)
                                    {
                                        $customer_data['vatNumber'] = $value['vat_number'];
                                    }
                                }
                            }

                                       
                            //check vatPercentage
                            if ( (isset($value['vat_percentage']) && !is_null($value['vat_percentage'])) )
                            {
                               
                                if (is_numeric($value['vat_percentage'])){

                                    if($value['vat_percentage'] >= 0)
                                    {
                                        $customer_data['vatPercentage'] = $value['vat_percentage'];
                                    }
                                }
                                
                            }

                            $document_info = DocumentMaster::where('documentID', 'CUSTM')->first();
                            $customer_data['documentSystemID'] = $document_info->documentSystemID;
                            $customer_data['documentID'] = $document_info->documentID;

                            $lastCustomer = CustomerMaster::orderBy('customerCodeSystem', 'DESC')->first();
                            $lastSerialOrder = 1;
                            if(!empty($lastCustomer)){
                                $lastSerialOrder = $lastCustomer->lastSerialOrder + 1;
                            }
                
                            $customerCode = 'C' . str_pad($lastSerialOrder, 7, '0', STR_PAD_LEFT);
                
                            $customer_data['lastSerialOrder'] = $lastSerialOrder;
                            $customer_data['CutomerCode'] = $customerCode;
                            $customer_data['createdPcID'] = gethostname();
                            $customer_data['createdUserID'] = $empId;
                            $customer_data['isCustomerActive'] = 1;
                            
                         
                            if(!$nullValue && !$valueNotExit && !$groupOfComapnyFalse  && !$notValid)
                            {
                                $customerMasters = $this->customerMasterRepository->create($customer_data);
                                $succesfully_created++;
                            }
                         

           
                            

                    }
               
                            if($total_count == 0 )
                            {
                                Storage::disk($disk)->delete($originalFileName);
                                return $this->sendError('No Records found!', 500);
                            }
                            else
                            {
                                
                                if ($succesfully_created == $totalItemCount) {
                                    $message = "All record Upload successfully";
                                    $msg_detail = [];
                                    $is_success = true;
                                   
                                } 
                                else if($succesfully_created == 0)
                                {
                                    $message = "Nothing uploaded !try agian";
                                    $msg_detail = $customer_error;
                                    $is_success = false;
                                }
                                 else {
                                    $message = "Successfully uploadedf ".$succesfully_created." customers out of ".$totalItemCount.".";
        
                                   
                                    $msg_detail = $customer_error;
                                    $is_success = true;
                               
                              
                                    
                                   
                                    }
                             
                            }
                          ;
                            $details['message'] = $message;
                            $details['msg_detail'] = $msg_detail;
                            $details['success'] = $is_success;
                            //Storage::disk($disk)->delete($originalFileName);
                             return $this->sendResponse($details, 'Added succefully');
                        
                
                    break;
                case '56':
                    $count = 1;
                    foreach ($formatChk as $key => $value)
                    {

                        $supplier_data = [];
                        $company_group_msg = '';
                        $currency_not_valid = false;

                        $employee = \Helper::getEmployeeInfo();
                   
                        $count++;

                   
                       
                        if ( (isset($value['primary_company']) && !is_null($value['primary_company'])) 
                            || (isset($value['supplier_name']) && !is_null($value['supplier_name'])) 
                            || (isset($value['address']) && !is_null($value['address'])) 
                            || (isset($value['supplier_country']) && !is_null($value['supplier_country'])) 
                            || (isset($value['name_on_the_cheque']) && !is_null($value['name_on_the_cheque'])) 
                            || (isset($value['supplier_main_category']) && !is_null($value['supplier_main_category'])) 
                            || (isset($value['telephone']) && !is_null($value['telephone'])) 
                            || (isset($value['email']) && !is_null($value['email'])) 
                            || (isset($value['currency']) && !is_null($value['currency'])) 
                            || (isset($value['credit_limit']) && !is_null($value['credit_limit'])) 
                            || (isset($value['credit_period']) && !is_null($value['credit_period'])) 
                            || (isset($value['registration_number']) && !is_null($value['registration_number'])) 
                            || (isset($value['registration_expiry']) && !is_null($value['registration_expiry'])) 
                            )
                            {
                              $totalItemCount++;
                            }

                      
                            //check companu validation
                            if ( (isset($value['primary_company']) && !is_null($value['primary_company'])) )
                            {

                                $company = Company::where('CompanyID', $value['primary_company'])->select('companySystemID','companyID')->first();

                             
                                if(isset($company))
                                {
                                    $validatorResult = \Helper::checkCompanyForMasters($company->companySystemID);

                                    if (!$validatorResult['success']) {
                                            $groupOfComapnyFalse = true;
                                            $company_group_msg = $validatorResult['message'];
                                            $name = 'N/A';
                                            if(isset($value['supplier_name']))
                                            {
                                                $name = $value['supplier_name'];
                                            }
                                            array_push($supplier_error['Primary Company'], $name.',line number '.$count.' company not valid');
                                    }
                                    else
                                    {
                                        $supplier_data['primaryCompanySystemID'] = $company->companySystemID;
                                        $supplier_data['primaryCompanyID'] = $company->companyID;
                                    }
                            
                                }
                                else
                                {
                                    $valueNotExit = true;
                                    $name = 'N/A';
                                    if(isset($value['supplier_name']))
                                    {
                                        $name = $value['supplier_name'];
                                    }
                                    array_push($supplier_error['Primary Company'], $name.',line number '.$count.' company not exist');

                                }

                            }
                            else
                            {
                                $nullValue = true;
                                $name = 'N/A';
                                if(isset($value['supplier_name']))
                                {
                                    $name = $value['supplier_name'];
                                }
                                array_push($supplier_error['Primary Company'], $name.',line number '.$count.' null value');

                            }
                      

                            //check gl liabilityAccountSysemID validation
                            if ( (isset($value['liability_account']) && !is_null($value['liability_account'])) )
                            {
                        
                                $lib_account = ChartOfAccount::where('AccountCode', $value['liability_account'])
                                ->where('controllAccountYN', '=', 1)
                                ->where('controlAccountsSystemID', 4)
                                ->where('catogaryBLorPL', '=', 'BS')
                                ->select('chartOfAccountSystemID','AccountCode')->first();
                            
                                if(isset($lib_account))
                                {
                                    $supplier_data['liabilityAccountSysemID'] = $lib_account->chartOfAccountSystemID;
                                    $supplier_data['liabilityAccount'] = $lib_account->AccountCode;
                            
                                }
                            }

                            
                            //check gl UnbilledGRVAccountSystemID validation
                            if ( (isset($value['unbilled_account']) && !is_null($value['unbilled_account'])) )
                            {
                        
                                $unbilled_account = ChartOfAccount::where('AccountCode', $value['unbilled_account'])
                                ->where('controllAccountYN', '=', 1)
                                ->where('controlAccountsSystemID', 4)
                                ->where('catogaryBLorPL', '=', 'BS')
                                ->select('chartOfAccountSystemID','AccountCode')->first();
                            
                                if(isset($lib_account))
                                {
                                    $supplier_data['UnbilledGRVAccountSystemID'] = $unbilled_account->chartOfAccountSystemID;
                                    $supplier_data['UnbilledGRVAccount'] = $unbilled_account->AccountCode;
                            
                                }
                            }

                       
                            //check supplier name validation
                            if ( (isset($value['supplier_name']) && !is_null($value['supplier_name'])) )
                            {
                                 

                                 if (!is_numeric($value['supplier_name']))
                                 {
                                    $supplier_data['supplierName'] = $value['supplier_name'];
                                 }
                                 else
                                 {
                                     $notValid = true;
 
                                     $name = 'N/A';
                                     if(isset($value['supplier_name']))
                                     {
                                         $name = $value['supplier_name'];
                                     }
                                     array_push($supplier_error['Supplier Name'],'line number '.$count.' not only numeric value');
                                 }
                                                               
                            }
                            else
                            {
                                $nullValue = true;
                                array_push($supplier_error['Supplier Name'],'line number '.$count.' null value');
                            }

                            //check payment check name validation
                            if ( (isset($value['name_on_the_cheque']) && !is_null($value['name_on_the_cheque'])) )
                            {
                                $supplier_data['nameOnPaymentCheque'] = $value['name_on_the_cheque'];
                                                                
                            }
                            else
                            {
                                $nullValue = true;

                                $name = 'N/A';
                                if(isset($value['supplier_name']))
                                {
                                    $name = $value['supplier_name'];
                                }
                                array_push($supplier_error['Check Name'], $name.',line number '.$count.' null value');
                            }

                      

                            //check address validation
                            if ( (isset($value['address']) && !is_null($value['address'])) )
                            {
                               

                                if (!is_numeric($value['address']))
                                {
                                    $supplier_data['address'] = $value['address'];
                                }
                                else
                                {
                                    $notValid = true;

                                    $name = 'N/A';
                                    if(isset($value['supplier_name']))
                                    {
                                        $name = $value['supplier_name'];
                                    }
                                    array_push($supplier_error['Address'],'line number '.$count.' not only numeric value');
                                }
                                                                
                            }
                            else
                            {
                                $nullValue = true;

                                
                                $name = 'N/A';
                                if(isset($value['supplier_name']))
                                {
                                    $name = $value['supplier_name'];
                                }
                                array_push($supplier_error['Address'], $name.',line number '.$count.' null value');
                            }

                            //check supplier country validation
                            if ( (isset($value['supplier_country']) && !is_null($value['supplier_country'])) )
                            {

                                $country = CountryMaster::where('countryName','=', $value['supplier_country'])->select('countryID','countryName')->first();
                                
                                if(!isset($country))
                                {
                                    $valueNotExit = true;

                                    $name = 'N/A';
                                    if(isset($value['supplier_name']))
                                    {
                                        $name = $value['supplier_name'];
                                    }
                                    array_push($supplier_error['Country'], $name.',line number '.$count.' country not valid');
                            
                                }
                                else
                                {
                                    $supplier_data['supplierCountryID'] = $country->countryID;
                                    $supplier_data['countryID'] = $country->countryID;
                                }
                                
                            }
                            else
                            {
                                $nullValue = true;

                                $name = 'N/A';
                                if(isset($value['supplier_name']))
                                {
                                    $name = $value['supplier_name'];
                                }
                                array_push($supplier_error['Country'], $name.',line number '.$count.' null value');
                                


                            }



                        //check supplier category validation
                        if ( (isset($value['supplier_main_category']) && !is_null($value['supplier_main_category'])) )
                        {

                            $supplier_cat = SupplierCategoryMaster::where('categoryCode','=', $value['supplier_main_category'])->select('supCategoryMasterID','categoryCode')->first();
                            
                            if(!isset($supplier_cat))
                            {
                                $valueNotExit = true;

                                
                                $name = 'N/A';
                                if(isset($value['supplier_name']))
                                {
                                    $name = $value['supplier_name'];
                                }
                                array_push($supplier_error['Category'], $name.',line number '.$count.' category not exist');
                        
                            }
                            else
                            {
                                $supplier_data['supCategoryMasterID'] = $supplier_cat->supCategoryMasterID;
                            }
                            
                        }
                        else
                        {
                            $nullValue = true;

                            $name = 'N/A';
                            if(isset($value['supplier_name']))
                            {
                                $name = $value['supplier_name'];
                            }
                            array_push($supplier_error['Category'], $name.',line number '.$count.' null value');
                        }
                        

                      
                        //check telephone validation
                        if ( (isset($value['telephone']) && !is_null($value['telephone'])) )
                        {
                           
                           
                          
                            if (is_numeric($value['telephone'])){
                                if($value['telephone'] >= 0)
                                {
                                    $supplier_data['telephone'] = $value['telephone'];
                                }
                                else
                                {
                                    $notValid = true;

                            
                                    $name = 'N/A';
                                    if(isset($value['supplier_name']))
                                    {
                                        $name = $value['supplier_name'];
                                    }
                                    array_push($supplier_error['Mobile'], $name.',line number '.$count.' mobile should positive');
                                }
                            }
                            else
                            {
                                $notValid = true;

                            
                                $name = 'N/A';
                                if(isset($value['supplier_name']))
                                {
                                    $name = $value['supplier_name'];
                                }
                                array_push($supplier_error['Mobile'], $name.',line number '.$count.' mobile should be numbers');
                            }
                            //return $this->sendResponse($value['telephone'], 'Added succefully');
                            
                           
                                                            
                        }
                        else
                        {
                            $nullValue = true;

                            
                            $name = 'N/A';
                            if(isset($value['supplier_name']))
                            {
                                $name = $value['supplier_name'];
                            }
                            array_push($supplier_error['Mobile'], $name.',line number '.$count.' null value');
                        }


                        //check fax
                        if ( (isset($value['fax']) && !is_null($value['fax'])) )
                        {
                           

                            $supplier_data['fax'] = $value['fax'];
                                                            
                        }
                        
                   
                  

                        //check email validation
                        if ( (isset($value['email']) && !is_null($value['email'])) )
                        {
                        
                         
                            $email = $value['email'];
                           
                            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                               
                               $notValid = true;
                               $name = 'N/A';
                               if(isset($value['supplier_name']))
                               {
                                   $name = $value['supplier_name'];
                               }
                               array_push($supplier_error['Email'], $name.',line number '.$count.' email not valid');
                            }
                            else
                            {
                                $supplier_data['supEmail'] = $value['email'];
                            }
                                
                        }
                        else
                        {
                            $nullValue = true;
                            $name = 'N/A';
                            if(isset($value['supplier_name']))
                            {
                                $name = $value['supplier_name'];
                            }
                            array_push($supplier_error['Email'], $name.',line number '.$count.' null value');
                        }
                      
                        //check web address validation
                        if ( (isset($value['web_address']) && !is_null($value['web_address'])) )
                        {
                            $supplier_data['webAddress'] = $value['web_address'];
                                                            
                        }
        
     

                        //check currency validation
                        if ( (isset($value['currency']) && !is_null($value['currency'])) )
                        {

                            $currency_check = CurrencyMaster::where('CurrencyCode','=', $value['currency'])->select('currencyID','CurrencyCode')->first();
                            
                            if(!isset($currency_check))
                            {
                                $valueNotExit = true;
                                $currency_not_valid = true;
                                $name = 'N/A';
                                if(isset($value['supplier_name']))
                                {
                                    $name = $value['supplier_name'];
                                }
                                array_push($supplier_error['Currency'], $name.',line number '.$count.' currency not valid');
                                
                        
                            }
                            else
                            {
                                $supplier_data['currency'] = $currency_check->currencyID;
                            }
                            
                        }
                        else
                        {
                            $nullValue = true;

                            $name = 'N/A';
                            if(isset($value['supplier_name']))
                            {
                                $name = $value['supplier_name'];
                            }
                            array_push($supplier_error['Currency'], $name.',line number '.$count.' null value');
                        }

                   
                        //check supplier importance validation
                        if ( (isset($value['importance']) && !is_null($value['importance'])) )
                        {

                            $importance_check = SupplierImportance::where('importanceDescription','=', $value['importance'])->first();
                            
                            if(isset($importance_check))
                            {
                                $supplier_data['supplierImportanceID'] = $importance_check->supplierImportanceID;
                        
                            }
                     
                        }

                        

                        //check supplier nature validation
                        if ( (isset($value['nature']) && !is_null($value['nature'])) )
                        {

                            $nature_check = suppliernature::where('natureDescription','=', $value['nature'])->first();
                            
                            if(isset($nature_check))
                            {
                                $supplier_data['supplierNatureID'] = $nature_check->supplierNatureID;
                        
                            }
                         
                            
                        }


                        //check supplier type validation
                        if ( (isset($value['type']) && !is_null($value['type'])) )
                        {

                            $typecheck = SupplierType::where('typeDescription','=', $value['type'])->first();
                            
                            if(isset($typecheck))
                            {
                                $supplier_data['supplierTypeID'] = $typecheck->supplierTypeID;
                        
                            }
                           
                            
                        }


                        //check Credit Limit validation
                        if ( (isset($value['credit_limit']) && !is_null($value['credit_limit'])) )
                        {


                            if (is_numeric($value['credit_limit'])){

                                if($value['credit_limit'] >= 0)
                                {
                                    $supplier_data['creditLimit'] = $value['credit_limit'];
                                }
                                else
                                {
                                    $notValid = true;
                                    $name = 'N/A';
                                    if(isset($value['supplier_name']))
                                    {
                                        $name = $value['supplier_name'];
                                    }
                                    array_push($supplier_error['Credit Limit'], $name.',line number '.$count.' not valid');
                                }
                            }
                            else
                            {
                                $notValid = true;
                                $name = 'N/A';
                                if(isset($value['supplier_name']))
                                {
                                    $name = $value['supplier_name'];
                                }
                                array_push($supplier_error['credit_limit'], $name.'-line number '.$count.'not valid');
                            }

                        }
                        else
                        {
                            $nullValue = true;
                            $name = 'N/A';
                            if(isset($value['supplier_name']))
                            {
                                $name = $value['supplier_name'];
                            }
                            array_push($supplier_error['Credit Limit'], $name.',line number '.$count.' null value');
                        }

                        


                        //check creditPeriod validation
                        if ( (isset($value['credit_period']) && !is_null($value['credit_period'])) )
                        {


                            if (is_numeric($value['credit_period'])){

                                if($value['credit_period'] >= 0)
                                {
                                    $supplier_data['creditPeriod'] = $value['credit_period'];
                                }
                                else
                                {
                                    $notValid = true;
                                    if(isset($value['supplier_name']))
                                    {
                                        $name = $value['supplier_name'];
                                    }
                                    array_push($supplier_error['Credit Period'], $name.',line number '.$count.' not valid');
                                }
                            }
                            else
                            {
                                $notValid = true;
                                if(isset($value['supplier_name']))
                                {
                                    $name = $value['supplier_name'];
                                }
                                array_push($supplier_error['credit_period'], $name.'-line number '.$count.' not valid');
                            }
                         
                            
                        }
                        else
                        {
                            $nullValue = true;
                            if(isset($value['supplier_name']))
                            {
                                $name = $value['supplier_name'];
                            }
                            array_push($supplier_error['Credit Period'], $name.',line number '.$count.' null value');
                        }




                        //check Registration Number validation
                        if ( (isset($value['registration_number']) && !is_null($value['registration_number'])) )
                        {

                            


                            if (is_numeric($value['registration_number'])){

                                if($value['registration_number'] >= 0)
                                {
                                    $supplier_data['registrationNumber'] = $value['registration_number'];
                                }
                                else
                                {
                                    $notValid = true;
                                    if(isset($value['supplier_name']))
                                    {
                                        $name = $value['supplier_name'];
                                    }
                                    array_push($supplier_error['Register Number'], $name.',line number '.$count.' register number must positive');
                                }
                            }
                            else
                            {
                                $notValid = true;
                                if(isset($value['supplier_name']))
                                {
                                    $name = $value['supplier_name'];
                                }
                                array_push($supplier_error['Register Number'], $name.',line number '.$count.' only numeric value allowed');
                            }

                         
                            
                        }
                        else
                        {
                            $nullValue = true;
                            $name = 'N/A';
                            if(isset($value['supplier_name']))
                            {
                                $name = $value['supplier_name'];
                            }
                            array_push($supplier_error['Register Number'], $name.',line number '.$count.' null value');
                        }

                        

                         //check registration expiry
                        if ( (isset($value['registration_expiry']) && !is_null($value['registration_expiry'])) )
                        {

                            if(!strtotime($value['registration_expiry']))
                            {
                                $notValid = true;
                                $name = 'N/A';
                                if(isset($value['supplier_name']))
                                {
                                    $name = $value['supplier_name'];
                                }
                                array_push($supplier_error['Register Expire'], $name.',line number '.$count.' not valid');
                            }
                            else
                            {
                               
                                $expire_date = date('Y-m-d H:i:s', strtotime($value['registration_expiry']));
                                if($current_date > $expire_date)
                                {
                                    $notValid = true;
                                    $name = 'N/A';
                                    if(isset($value['supplier_name']))
                                    {
                                        $name = $value['supplier_name'];
                                    }
                                    array_push($supplier_error['Register Expire'], $name.',line number '.$count.' not valid');
                                }
                                else
                                {
                                    $supplier_data['registrationExprity'] = $expire_date;
                                }
    
                            }
    
                            
                        }
                        else
                        {     $name = 'N/A';
                            if(isset($value['supplier_name']))
                            {
                                $name = $value['supplier_name'];
                            }
                            array_push($supplier_error['Register Expire'], $name.',line number '.$count.' null value');
                            $nullValue = true;
                        }


                        //check JSRS Number validation
                        if ( (isset($value['jsrs_number']) && !is_null($value['jsrs_number'])) )
                        {

                            $supplier_data['jsrsNo'] = $value['jsrs_number'];
                        
                            
                        }
                 

                    //check JSRS expiry
                    if ( (isset($value['jsrs_expiry']) && !is_null($value['jsrs_expiry'])) )
                    {

                        if(strtotime($value['jsrs_expiry']))
                        {
                                
                            $expire_date_jers = date('Y-m-d H:i:s', strtotime($value['jsrs_expiry']));
                            if($current_date<> $expire_date_jers)
                            {
                                $supplier_data['jsrsExpiry'] = $expire_date_jers;
                            }
                          
                        }
                      

                        
                    }

                
                    $supplier_data['createdPcID'] = gethostname();
                    $supplier_data['createdUserID'] = $employee->empID;
                    $supplier_data['createdUserSystemID'] = $employee->employeeSystemID;
                    $supplier_data['uniqueTextcode'] = "S";

                    
                    $document_info = DocumentMaster::where('documentID', 'SUPM')->first();
                    $supplier_data['documentSystemID'] = $document_info->documentSystemID;
                    $supplier_data['documentID'] = $document_info->documentID;
                    $supplier_data['isActive'] = 1;
                    

            
                    
                    if(!$nullValue && !$valueNotExit && !$groupOfComapnyFalse && !$currency_not_valid && !$notValid)
                    {
                       
               
                        $supplierMasters = $this->supplierMasterRepository->create($supplier_data);

                        $updateSupplierMasters = SupplierMaster::where('supplierCodeSystem', $supplierMasters['supplierCodeSystem'])->first();
                        $updateSupplierMasters->primarySupplierCode = 'S0' . strval($supplierMasters->supplierCodeSystem);
                
                        $updateSupplierMasters->save();
                    
                        if (isset($supplier_data['currency']) && $supplier_data['currency'] > 0) {
                     
                            if(!$currency_not_valid)
                            {
                                $id = Auth::id();
                                $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
                                $empId = $user->employee['empID'];
                                $empName = $user->employee['empName'];
                    
                                $supplierCurrency = new SupplierCurrency();
                                $supplierCurrency->supplierCodeSystem = $supplierMasters->supplierCodeSystem;
                                $supplierCurrency->currencyID = $supplier_data['currency'];
                                $supplierCurrency->isAssigned = -1;
                                $supplierCurrency->isDefault = -1;
                                $supplierCurrency->save();
                                
                         

                                $companyDefaultBankMemos = BankMemoTypes::orderBy('sortOrder', 'asc')->get();
                    
                                foreach ($companyDefaultBankMemos as $value1) {
                                    $temBankMemo = new BankMemoSupplier();
                                    $temBankMemo->memoHeader = $value1['bankMemoHeader'];
                                    $temBankMemo->bankMemoTypeID = $value1['bankMemoTypeID'];
                                    $temBankMemo->memoDetail = '';
                                    $temBankMemo->supplierCodeSystem = $supplierMasters->supplierCodeSystem;
                                    $temBankMemo->supplierCurrencyID = $supplierCurrency->supplierCurrencyID;
                                    $temBankMemo->updatedByUserID = $empId;
                                    $temBankMemo->updatedByUserName = $empName;
                                    $temBankMemo->save();
                                }
                            }
                         
                        }
                        $succesfully_created++;
                    }
                   
                    }

                    
                
                   
                    if($total_count == 0 )
                    {
                        Storage::disk($disk)->delete($originalFileName);
                        return $this->sendError('No Records found!', 500);
                    }
                    else
                    {
                        if ($succesfully_created == $totalItemCount) {
                            $message = "All record Upload successfully";
                            $msg_detail = [];
                            $is_success = true;
                           
                        } 
                        else if($succesfully_created == 0)
                        {
                            $message = "Nothing uploaded !try agian";
                            $msg_detail = $supplier_error;
                            $is_success = false;
                        }
                         else {
                            $message = "Successfully uploadedf ".$succesfully_created." suppliers out of ".$totalItemCount.".";

                           
                            $msg_detail = $supplier_error;
                            $is_success = true;
                       
                      
                            
                           
                            }
                    
                    }
                    $details['message'] = $message;
                    $details['msg_detail'] = $msg_detail;
                    $details['success'] = $is_success;
                    Storage::disk($disk)->delete($originalFileName);
                    return $this->sendResponse($details, 'Added succefully');



                    break;
                case '57':

                 
                
                        $count = 1;
                    
                     
                    foreach($formatChk as $key=>$value)
                    {   
                        $item_data = [];
                        $company_group_msg = '';

                        $employee = \Helper::getEmployeeInfo();
                        $count++;
                           
                                if ( (isset($value['primary_company']) && !is_null($value['primary_company'])) 
                                || (isset($value['finance_category']) && !is_null($value['finance_category'])) 
                                || (isset($value['finance_sub_category']) && !is_null($value['finance_sub_category'])) 
                                || (isset($value['mfg._part_no']) && !is_null($value['mfg._part_no'])) 
                                || (isset($value['item_description']) && !is_null($value['item_description'])) 
                                || (isset($value['unit_of_measure']) && !is_null($value['unit_of_measure'])) 
                                )
                                {
                                  $totalItemCount++;
                                }

                                      //check companu validation
                            if ( (isset($value['primary_company']) && !is_null($value['primary_company'])) )
                            {

                                $company = Company::where('CompanyID', $value['primary_company'])->select('companySystemID','companyID')->first();

                             
                                if(isset($company))
                                {
                                    $validatorResult = \Helper::checkCompanyForMasters($company->companySystemID);

                                    if (!$validatorResult['success']) {
                                            $groupOfComapnyFalse = true;
                                            $company_group_msg = $validatorResult['message'];

                                        
                                            array_push($item_error['Primary Company'], 'line number '.$count.' company not valid');
                                    }
                                    else
                                    {
                                        $item_data['primaryCompanySystemID'] = $company->companySystemID;
                                        $item_data['primaryCompanyID'] = $company->companyID;
                                    }
                            
                                }
                                else
                                {
                                    $valueNotExit = true;
                                    array_push($item_error['Primary Company'], 'line number '.$count.' company not valid');
                                }

                            }
                            else
                            {
                                $nullValue = true;
                                array_push($item_error['Primary Company'], 'line number '.$count.' null value');
                            }

                            //check finance category master
                            if ( (isset($value['finance_category']) && !is_null($value['finance_category'])) )
                            {
                             
                                $financeCategoryMaster = FinanceItemCategoryMaster::where('categoryDescription', $value['finance_category'])->select('itemCategoryID','categoryDescription','lastSerialOrder','itemCodeDef','numberOfDigits')->first();

                             
                                if(isset($financeCategoryMaster))
                                {

                                 
                                    $item_data['faFinanceCatID'] = null;
                                    $runningSerialOrder = $financeCategoryMaster->lastSerialOrder + 1;
                                    $code = $financeCategoryMaster->itemCodeDef;
                                    $count_dig = $financeCategoryMaster->numberOfDigits;
                                    $primaryCode = $code . str_pad($runningSerialOrder, $count_dig, '0', STR_PAD_LEFT);
                                    $item_data['runningSerialOrder'] = $runningSerialOrder;
                                    $item_data['primaryCode'] = $primaryCode;
                                    $item_data['primaryItemCode'] = $code;
                                    if(!(isset($input['barcode']) && $input['barcode'] != null)){
                                        $item_data['barcode'] = $primaryCode;
                                    }
                                    $item_data['financeCategoryMaster'] = $financeCategoryMaster->itemCategoryID;



                                    if ( (isset($value['finance_sub_category']) && !is_null($value['finance_sub_category'])) )
                                    {
                                        $financeCategorySub = FinanceItemCategorySub::where('categoryDescription', $value['finance_sub_category'])->first();

                                        if(isset($financeCategorySub))
                                        {
                                            $fina_cate_sub_id = $financeCategorySub->itemCategorySubID;
                                            $item_categoryID = $financeCategorySub->itemCategoryID;
                                            if($item_categoryID == $financeCategoryMaster->itemCategoryID)
                                            {
                                                $item_data['financeCategorySub'] = $fina_cate_sub_id;
                                                $item_data['trackingType'] = (is_null($financeCategorySub->trackingType)) ? 0 : $financeCategorySub->trackingType;
                                            }
                                            else
                                            {
                                                $valueNotExit = true;
                                                array_push($item_error['Finance Sub Category'], 'line number '.$count.' subgetory not found for this category'); 
                                            }

                                         
                                        }
                                        else
                                        {
                                            $valueNotExit = true;
                                            array_push($item_error['Finance Sub Category'], 'line number '.$count.' value not valid');
                                        }
                                       

                                        

                                    }
                                    else
                                    {
                                   

                                        $nullValue = true;
                                        array_push($item_error['Finance Sub Category'], 'line number '.$count.' null'); 
                                    }
                                 
                            
                                }
                                else
                                {
                                    $valueNotExit = true;
                                    array_push($item_error['Finance Category'], 'line number '.$count.' value not valid');
                                }

                            }
                            else
                            {
                                $nullValue = true;
                                array_push($item_error['Finance Category'], 'line number '.$count.' null value');
                            }


                       
                            //check secondory code validation
   
                            if ( (isset($value['mfg._part_no']) && !is_null($value['mfg._part_no'])) )
                            {
                                
                                $secondary_Exists = $this->itemMasterRepository->where('secondaryItemCode','=',$value['mfg._part_no'])->select('itemCodeSystem','secondaryItemCode')->first();
                                if(isset($secondary_Exists))
                                {
                                    $valueNotExit = true;
                                    array_push($item_error['Part No / Ref.Number'], 'line number '.$count.' Part No / Ref.Number already exists');
                                }
                                else
                                {
                                    $item_data['secondaryItemCode'] = $value['mfg._part_no'];

                                }
                          
                            } 


                               //check item description
                               if ( (isset($value['item_description']) && !is_null($value['item_description'])) )
                               {
                                       $item_data['itemDescription'] = $value['item_description'];
                             
                               } 
                                else
                               {
                                   $nullValue = true;
                                   array_push($item_error['Item Description'], 'line number '.$count.' null value');
                               }


                               //check unit 


                               if ( (isset($value['unit_of_measure']) && !is_null($value['unit_of_measure'])) )
                               {
   
                                   $units_code = Unit::where('UnitShortCode', $value['unit_of_measure'])->first();
                                
                                
                                   if(isset($units_code))
                                   {
                                        $item_data['unit'] = $units_code->UnitID;
                                   }
                                   else
                                   {
                                       $valueNotExit = true;
                                       array_push($item_error['Unit of Measure'], 'line number '.$count.' unit not valid');
                                   }
   
                               }
                               else
                               {
                                   $nullValue = true;
                                   array_push($item_error['Unit of Measure'], 'line number '.$count.' null value');
                               }


                       
                            //check pos type
                            
                            if ( (isset($value['pos_type']) && !is_null($value['pos_type'])) )
                            {
                             
                                if($value['pos_type'] == 'General POS'){
                                    $item_data['pos_type'] = 1;
                                } elseif ($value['pos_type'] == 'Restaurant POS') {
                                    $item_data['pos_type'] = 2;
                                } elseif ($value['pos_type'] == 'Both') {
                                    $item_data['pos_type'] = 3;
                                } else{
                                        $valueNotExit = true;
                                        array_push($item_error['POS type'], 'line number '.$count.' pos value not valid');
                                }
                            }


                         
                            //check short description
                            if ( (isset($value['item_short_description']) && !is_null($value['item_short_description'])) )
                            {
                                    $item_data['itemShortDescription'] = $value['item_short_description'];
                          
                            }
                            //check item url
                            if ( (isset($value['item_url']) && !is_null($value['item_url'])) )
                            {
                                    $item_data['itemUrl'] = $value['item_url'];
                          
                            }

                            //check barcode url
                            if ( (isset($value['barcode']) && !is_null($value['barcode'])) )
                            {
                                    $item_data['barcode'] = $value['barcode'];
                            
                            }
                            


                            if ( (isset($value['is_active']) && !is_null($value['is_active'])) )
                            {

                             
                                if ( $value['is_active'] == 'Yes' || $value['is_active'] == 'No') 
                                {
                                    if($value['is_active'] == 'No')
                                    {
                                        $vat_el = '0';
                                    }
                                    else
                                    {
                                        $vat_el = '1';
                                    }
                                
                                    $item_data['isActive'] = $vat_el;
                                 
                                }
                          
                             
                            }

                            $employee = Helper::getEmployeeInfo();
                            $item_data['createdPcID'] = gethostname();
                            $item_data['createdUserID'] = $employee->empID;
                            $item_data['createdUserSystemID'] = $employee->employeeSystemID;

                            
                            $document = DocumentMaster::where('documentID', 'ITMM')->first();
                            $item_data['documentSystemID'] = $document->documentSystemID;
                            $item_data['documentID'] = $document->documentID;
                            $item_data['isPOSItem'] = 0;


                            if(!$nullValue && !$valueNotExit && !$groupOfComapnyFalse && !$notValid)
                            {
                             
                       
                                $itemMasters = $this->itemMasterRepository->create($item_data);
        
                                $financeCategoryMaster->lastSerialOrder = $runningSerialOrder;
                                $financeCategoryMaster->modifiedPc = gethostname();
                                $financeCategoryMaster->modifiedUser = $employee->empID;
                                $financeCategoryMaster->save();
                                $succesfully_created++;
                            }
                            
                    }


                       
                    if($total_count == 0 )
                    {
                        Storage::disk($disk)->delete($originalFileName);
                        return $this->sendError('No Records found!', 500);
                    }
                    else
                    {
                        if ($succesfully_created == $totalItemCount) {
                            $message = "All record Upload successfully";
                            $msg_detail = [];
                            $is_success = true;
                           
                        } 
                        else if($succesfully_created == 0)
                        {
                            $message = "Nothing uploaded !try agian";
                            $msg_detail = $item_error;
                            $is_success = false;
                        }
                         else {
                            $message = "Successfully uploaded ".$succesfully_created." items out of ".$totalItemCount.".";

                           
                            $msg_detail = $item_error;
                            $is_success = true;
                       
                      
                            
                           
                            }
                    
                    }
                    $details['message'] = $message;
                    $details['msg_detail'] = $msg_detail;
                    $details['success'] = $is_success;
                    Storage::disk($disk)->delete($originalFileName);
                    return $this->sendResponse($details, 'Added succefully');
                     
                    break;
                default:
                    return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.document')]));
            }













        } catch (\Exception $exception) {
           
            return $this->sendError($exception->getMessage());
        }

    }


    public function getInterCompaniesForCustomerSupplier(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        if (isset($input['primaryCompanySystemID'])) {
            $allCompanies = Company::where('isGroup', 0)
                ->where('isActive', 1)
                ->where('companySystemID', '!=', $input['primaryCompanySystemID'])
                ->get();
        } else {
            $allCompanies = [];
        }

        $hasPolicy = CompanyPolicyMaster::where('companySystemID', $input['primaryCompanySystemID'])
                    ->where('companyPolicyCategoryID', 66)
                    ->where('isYesNO',1)
                    ->exists();


        return $this->sendResponse(['allCompanies' => $allCompanies, 'interCompanyPolicy' => $hasPolicy], 'Record retrieved successfully');
    }

    public function validateCustomerAmend(Request $request)
    {
        $input = $request->all();

        $customerMaster = CustomerMaster::find($input['customerID']);

        if (!$customerMaster) {
            return $this->sendError('Customer Data not found');
        }
      

        $errorMessages = [];
        $successMessages = [];
        $amendable = [];

        $cusInvoice = CustomerInvoice::where('customerID', $input['customerID'])->where('customerGLSystemID', $customerMaster->custGLAccountSystemID)->first();//check GL account
    
        if ($cusInvoice) {
            $errorMessages[] = "GL Account cannot be amended. it had used in customer Invoice";
            $amendable['GLAmendable'] = false;
        } else {
            $successMessages[] = "Use of GL Account checking is done in customer Invoice";
            $amendable['GLAmendable'] = true;
        }


       
        $deliveryOrder = DeliveryOrder::where('customerID', $input['customerID'])
                                      ->where('custUnbilledAccountSystemID', $customerMaster->custUnbilledAccountSystemID)
                                      ->first();//check Unbilled Receivable Account

        if ($deliveryOrder) {
            $errorMessages[] = "Unbilled Account cannot be amended. it had used in delivery order";
            $amendable['unbilledAmendable'] = false;
        } else {
            $successMessages[] = "Use of Unbilled Account checking is done in delivery order";
            $amendable['unbilledAmendable'] = true;
        }
        
   

        $cusInvoicee = CustomerInvoice::where('customerID', $input['customerID'])->where('approved','!=',-1)->first();//check unapproved customer invoice which include the current customer
        if($cusInvoicee)
        {
            $errorMessages[] = "Unable to amend isActive, customer used in Unapproved customer invoice";
            $amendable['isActive'] = false;
        }
        else
        {
            $amendable['isActive'] = true;
        }


        
        $credit = CreditNote::where('customerID', $input['customerID'])->where('approved','!=',-1)->first();//check unapproved customer invoice which include the current customer
        if($credit)
        {
            $errorMessages[] = "Unable to amend isActive, customer used in Unapproved credit Note";
            $amendable['isActive'] = false;
        }
        else
        {
            $amendable['isActive'] = (!$amendable['isActive']) ? false : true;
        }


        $recived = CustomerReceivePayment::where('customerID', $input['customerID'])->where('approved','!=',-1)->first();//check unapproved customer invoice which include the current customer
        if($recived)
        {
            $errorMessages[] = "Unable to amend isActive, customer used in Unapproved Customer Receipt Voucher";
            $amendable['isActive'] = false;
        }
        else
        {
            $amendable['isActive'] = (!$amendable['isActive']) ? false : true;
        }

        $QuotationMaster = QuotationMaster::where('customerSystemCode', $input['customerID'])->where('approvedYN','!=',-1)->first();//check unapproved QuotationMaster which include the current customer
        if($QuotationMaster)
        {
            $errorMessages[] = "Unable to amend isActive, customer used in Unapproved Quotation/Sales Order";
            $amendable['isActive'] = false;
        }
        else
        {
            $amendable['isActive'] =(!$amendable['isActive']) ? false : true;
        }


        $deliveryOrderr = DeliveryOrder::where('customerID', $input['customerID'])->where('approvedYN','!=',-1)->first();//check unapproved deliverymaster which include the current customer 
        if($deliveryOrderr)
        {
            $errorMessages[] = "Unable to amend isActive, customer used in Unapproved delivery order Order";
            $amendable['isActive'] = false;
        }
        else
        {
            $amendable['isActive'] =(!$amendable['isActive']) ? false : true;
        }


        return $this->sendResponse(['errorMessages' => $errorMessages, 'successMessages' => $successMessages, 'amendable'=> $amendable], "validated successfully");
    }

}
