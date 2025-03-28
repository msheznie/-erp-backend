<?php
/**
 * =============================================
 * -- File Name : EmployeeAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Employee
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Employee
 * -- REVISION HISTORY
 * -- Date: 14-March 2018 By: Fayas Description: Added new functions named as getItemMasterPurchaseHistory(),exportPurchaseHistory(),
 * -- Date: 18-December 2018 By: Nazir Description: Added new functions named as getEmployeeMasterView(),
 * -- Date: 18-December 2018 By: Nazir Description: Added new functions named as confirmEmployeePasswordReset(),
 * -- Date: 19-December 2018 By: Nazir Description: Added new functions named as getEmployeeMasterData(),
 * -- Date: 27-August 2019 By: Rilwan Description: Added new functions amed as getProfileDetails()
 * -- Date: 03-February 2020 By: Zakeeul Description: Added new functions named as getUserCountData()
 */

namespace App\Http\Controllers\API;

use App\helper\CompanyService;
use App\helper\Helper;
use App\Http\Requests\API\CreateEmployeeAPIRequest;
use App\Http\Requests\API\UpdateEmployeeAPIRequest;
use App\Models\Alert;
use App\Models\Company;
use App\Models\Employee;
use App\Models\SrmEmployees;
use App\Models\EmployeeNavigation;
use App\Models\EmployeesDepartment;
use App\Models\LptPermission;
use App\Models\UserRights;
use App\Models\HRMSPersonalDocuments;
use App\Models\User;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\EmployeeRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;
use App\Models\BookInvSuppMaster;
use App\Models\CustomerReceivePayment;
use App\Models\DebitNote;
use App\Models\DirectPaymentDetails;
use App\Models\ExpenseClaimMaster;
use App\Models\MatchDocumentMaster;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\PaySupplierInvoiceMaster;

/**
 * Class EmployeeController
 * @package App\Http\Controllers\API
 */
class EmployeeAPIController extends AppBaseController
{
    /** @var  EmployeeRepository */
    private $employeeRepository;


    public function __construct(EmployeeRepository $employeeRepo)
    {
        $this->employeeRepository = $employeeRepo;
    }

    /**
     * Display a listing of the Employee.
     * GET|HEAD /employees
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->employeeRepository->pushCriteria(new RequestCriteria($request));
        $this->employeeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $employees = $this->employeeRepository->all();

        return $this->sendResponse($employees->toArray(), 'Employees retrieved successfully');
    }

    /**
     * Store a newly created Employee in storage.
     * POST /employees
     *
     * @param CreateEmployeeAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateEmployeeAPIRequest $request)
    {
        $input = $request->all();

        $employees = $this->employeeRepository->create($input);

        return $this->sendResponse($employees->toArray(), 'Employee saved successfully');
    }

    /**
     * Display the specified Employee.
     * GET|HEAD /employees/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Employee $employee */
        $employee = $this->employeeRepository->findWithoutFail($id);

        if (empty($employee)) {
            return $this->sendError('Employee not found');
        }

        return $this->sendResponse($employee->toArray(), 'Employee retrieved successfully');
    }

    /**
     * Update the specified Employee in storage.
     * PUT/PATCH /employees/{id}
     *
     * @param  int $id
     * @param UpdateEmployeeAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateEmployeeAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['desi_master', 'manager', 'emp_company', 'hr_emp', 'manager_hrms']);
        $input = $this->convertArrayToValue($input);
        /** @var Employee $employee */
        $employee = $this->employeeRepository->findWithoutFail($id);

        if (empty($employee)) {
            return $this->sendError('Employee not found');
        }
        if (isset($input['isBasicUser']) && $input['isBasicUser']) {
            $input['isBasicUser'] = -1;
        }
        if (isset($input['isManager']) && $input['isManager']) {
            $input['isManager'] = -1;
        }
        if (isset($input['isApproval']) && $input['isApproval']) {
            $input['isApproval'] = -1;
        }
        if (isset($input['isAdmin']) && $input['isAdmin']) {
            $input['isAdmin'] = -1;
        }
        if (isset($input['isSuperAdmin']) && $input['isSuperAdmin']) {
            $input['isSuperAdmin'] = -1;
        }
        if (isset($input['discharegedYN']) && $input['discharegedYN']) {
            $input['discharegedYN'] = -1;
        }
        if (isset($input['ActivationFlag']) && $input['ActivationFlag']) {
            $input['ActivationFlag'] = -1;
        }
        if (isset($input['isSupportAdmin']) && $input['isSupportAdmin']) {
            $input['isSupportAdmin'] = -1;
        }
        if (isset($input['empUserName']) && $input['empUserName']) {
            $employeeCheck = Employee::where('empUserName',$input['empUserName'])->where('employeeSystemID','!=',$id)->first();
            if (!is_null($employeeCheck)) {
                return $this->sendError('Employee user name already exists.');
            } else {
                $validator = \Validator::make($input, [
                                    'empUserName' => 'required|email|max:255',
                                ]);
                if ($validator->fails()) {
                    return $this->sendError('User name is not valid.');
                }

                $input['empEmail'] = $input['empUserName'];
                //updating users table
                $usersMasterData = User::where('employee_id', $id)->first();
                if (!empty($usersMasterData)) {
                    $usersMasterUpdate = User::where('employee_id', $id)
                        ->update([
                            'email' => $input['empUserName'],
                            'username' => $input['empUserName']
                        ]);
                };
            }
        }

        $employee = $this->employeeRepository->update($input, $id);

        return $this->sendResponse($employee->toArray(), 'Employee updated successfully');
    }

    /**
     * Remove the specified Employee from storage.
     * DELETE /employees/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Employee $employee */
        $employee = $this->employeeRepository->findWithoutFail($id);

        if (empty($employee)) {
            return $this->sendError('Employee not found');
        }

        $employee->delete();

        return $this->sendResponse($id, 'Employee deleted successfully');
    }

    public function getAllEmployees(Request $request){
        $input = $request->all();

        $companyId = $request['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($companyId);
        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $output = Employee::leftJoin('erp_bookinvsuppmaster', function ($join) use ($childCompanies){
                $join->on('employees.employeeSystemID', '=', 'erp_bookinvsuppmaster.employeeID')
                     ->where('erp_bookinvsuppmaster.documentType', 4)
                     ->where('erp_bookinvsuppmaster.approved', -1)
                     ->whereIn('erp_bookinvsuppmaster.companySystemID', $childCompanies);
            })
            ->leftJoin('erp_paysupplierinvoicemaster', function ($join) use ($childCompanies){
                $join->on('employees.employeeSystemID', '=', 'erp_paysupplierinvoicemaster.directPaymentPayeeEmpID')
                     ->where('erp_paysupplierinvoicemaster.invoiceType', 7)
                     ->where('erp_paysupplierinvoicemaster.approved', -1)
                     ->whereIn('erp_paysupplierinvoicemaster.companySystemID', $childCompanies);
            })
            ->where(function ($query) {
                $query->whereNotNull('erp_bookinvsuppmaster.employeeID')
                      ->orWhereNotNull('erp_paysupplierinvoicemaster.directPaymentPayeeEmpID');
            })
            ->groupBy('employees.employeeSystemID');

        if(isset($input['isFromEmployeeLedger']) && $input['isFromEmployeeLedger'] == 1){
            if (array_key_exists('search', $input)) {
                $search = $input['search'];
                $output = $output->where(function ($query) use ($search) {
                    $query->where('employees.empName', 'LIKE', "%{$search}%");
                });
            }

            $output = $output->select(['employees.employeeSystemID','employees.empName'])->take(20)->get();
        } else {
            $output = $output->where('employees.discharegedYN', 0)->get();
        }
        

        return $this->sendResponse($output->toArray(), 'Data retrieved successfully');
    }


    public function getTypeheadEmployees(Request $request)
    {
        $input = $request->all();
        $employees = "";
        $discharged = isset($input['discharged']) ? $input['discharged'] : 0;
        $companySystemID = isset($input['companySystemID']) ? $input['companySystemID'] : 0;
        $checkDischarged = isset($input['checkDischarged']) ? $input['checkDischarged'] : 1;
        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $employees = Employee::where(function ($query) use ($search) {
                $query->where('empID', 'LIKE', "%{$search}%")
                    ->orWhere('empName', 'LIKE', "%{$search}%");
            });

            if ($companySystemID > 0) {
                $employees = $employees->where('empCompanySystemID', $companySystemID);
            }

            if(!$discharged && $checkDischarged == 1){
                $employees = $employees->where('discharegedYN', 0);
            }
        }

        $employees = $employees
            ->take(20)
            ->get();

        return $this->sendResponse($employees->toArray(), 'Data retrieved successfully');
    }

    public function getAllNotDishachargeEmployeesDropdown(Request $request) {
        $companyId = $request['empCompanySystemID'];
        $isGroup = \Helper::checkIsCompanyGroup($companyId);
        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $srm_employees = SrmEmployees::where('company_id',$companyId)->pluck('emp_id')->toArray();

        $employeeData = Employee::whereNotIn('employeeSystemID',$srm_employees)->whereIn('empCompanySystemID',$childCompanies)->where('discharegedYN','!=',-1);
        
        $employees = $employeeData->get();

        $data = [];

        foreach($employees as $emp) {
            array_push($data,["employeeSystemID"  => $emp->employeeSystemID, "empName" => $emp->empID." | ".$emp->empFullName]);
        }

        return $this->sendResponse($data, 'Data retrieved successfully');
    }

    public function getEmployeeMasterView(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }


        $companyId = $request['empCompanySystemID'];
        $isGroup = \Helper::checkIsCompanyGroup($companyId);
        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $empMaster = Employee::whereIn('empCompanySystemID', $childCompanies);

        if (isset($request['discharegedYN'])) {
            $empMaster = $empMaster->where('discharegedYN', $request['discharegedYN']);
        }

        if (isset($request['ActivationFlag'])) {
            $empMaster = $empMaster->where('ActivationFlag', $request['ActivationFlag']);
        }

        if (isset($request['isLock'])) {
            if($request['isLock'] === 0){
                $empMaster = $empMaster->where('isLock', $request['isLock']);
            }else{
                $empMaster = $empMaster->where('isLock', '!=', 0);
            }

        }

        if (isset($request['empActive'])) {
            $empMaster = $empMaster->where('empActive', $request['empActive']);
        }

        if (isset($request['empLoginActive'])) {
            $empMaster = $empMaster->where('empLoginActive', $request['empLoginActive']);
        }

        $empMaster->with(['emp_company', 'manager', 'desi_master' => function ($query) {
            $query->with('designation');
        }]);

        $hrIntegrated_count = CompanyService::hrIntegrated_company_count($childCompanies);

        if($hrIntegrated_count > 0){
            $empMaster->with(['manager_hrms'=> function($q){
                $q->selectRaw('empID,managerID')
                    ->where('active', 1)
                    ->with('info:EIdNo,ECode,Ename2');
            }]);

            $empMaster->with(['hr_emp'=> function($q) use ($childCompanies){
                $q->selectRaw('EIdNo, ECode, Ename2, EmpDesignationId')
                    ->whereIn('Erp_companyID', $childCompanies)
                    ->with('designation:DesignationID,DesDescription');
            }]);
        }


        $empMaster = $empMaster->select(
            ['employees.employeeSystemID',
                'employees.empID',
                'employees.empName',
                'employees.empUserName',
                'employees.designation',
                'employees.empCompanyID',
                'employees.empCompanySystemID',
                'employees.empEmail',
                'employees.empManagerAttached',
                'employees.isBasicUser',
                'employees.isManager',
                'employees.isApproval',
                'employees.isAdmin',
                'employees.isSuperAdmin',
                'employees.discharegedYN',
                'employees.empLoginActive',
                'employees.empActive',
                'employees.ActivationFlag',
                'employees.isSupportAdmin',
                'employees.isLock',
            ]);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $empMaster = $empMaster->where(function ($query) use ($search) {
                $query->where('empID', 'LIKE', "%{$search}%")
                    ->orWhere('empName', 'LIKE', "%{$search}%")
                    ->orWhere('empUserName', 'LIKE', "%{$search}%")
                    ->orWhere('empEmail', 'LIKE', "%{$search}%");
            });
        }

        $request->request->remove('search.value');
        return \DataTables::of($empMaster)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('employeeSystemID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->addColumn('manager_name', function ($row){
                return $this->getManagerName($row);
            })
            ->addColumn('designation_name', function ($row){
                return $this->getDesignationName($row);
            })
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getDesignationName($row)
    {
        if ( $row->emp_company->isHrmsIntergrated ) {
            return $row->hr_emp->designation->designation ?? '';
        } else {
            return $row->desi_master->designation->designation ?? '';
        }
    }

     public function getManagerName($row)
    {
        if ( $row->emp_company->isHrmsIntergrated ) {
            return $row->manager_hrms->info->Ename2 ?? '';
        } else {
            return $row->manager->empName ?? '';
        }
    }

    public function confirmEmployeePasswordReset(Request $request)
    {
        $input = $request->all();

        $employeeSystemID = $input['employeeSystemID'];
        $password = $this->quickRandom();

        $employeeMasterData = Employee::find($employeeSystemID);
        if (empty($employeeMasterData)) {
            return $this->sendError('Employee not found');
        }

        // updating fields
        $employeeMasterData->isLock = 0;
        $employeeMasterData->save();

        //updating users table
        $usersMasterData = User::where('employee_id', $employeeSystemID)->first();
        if (!empty($usersMasterData)) {
            $usersMasterUpdate = User::where('employee_id', $employeeSystemID)
                ->update([
                    'password' => bcrypt($password)
                ]);
        }

        //sending emails
        if(($employeeMasterData->discharegedYN == 0) && ($employeeMasterData->ActivationFlag == -1) && ($employeeMasterData->empLoginActive == 1) && ($employeeMasterData->empActive == 1)){
            $subject = 'GEARS Password Reset';

            $footer = "<font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!" .
                "<br>This is an auto generated email. Please do not reply to this email because we are not " .
                "monitoring this inbox.</font>";

            $body = "Dear " . $employeeMasterData->empName . ',<p> Your GEARS password has been reset to '.$password . $footer;

            $dataEmail['empSystemID'] = $employeeMasterData->employeeSystemID;
            $dataEmail['empID'] = $employeeMasterData->empID;
            $dataEmail['empName'] = $employeeMasterData->empName;
            $dataEmail['empEmail'] = $employeeMasterData->empEmail;
            $dataEmail['companySystemID'] = $employeeMasterData->empCompanySystemID;
            $dataEmail['companyID'] = $employeeMasterData->empCompanyID;
            $dataEmail['ccEmailID'] = $employeeMasterData->empEmail;
            $dataEmail['isEmailSend'] = 0;
            $dataEmail['alertMessage'] = $subject;
            $dataEmail['emailAlertMessage'] = $body;

            $sendEmail = \Email::sendEmailErp($dataEmail);
            if (!$sendEmail["success"]) {
                return $this->sendError($sendEmail["message"], 500);
            }
        }

        return $this->sendResponse($employeeMasterData->toArray(), 'Employee password reset successfully');
    }

    public static function quickRandom($length = 9)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, 2)), 0, $length);
    }

    public function getEmployeeMasterData(Request $request)
    {
        $selectedCompanySystemID = $request['selectedCompanySystemID'];

        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanySystemID);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanySystemID);
        } else {
            $subCompanies = [$selectedCompanySystemID];
        }

        /**  Companies by group  Drop Down */
        $companies = Company::whereIn("companySystemID", $subCompanies)->get();

         /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $output = array('companies' => $companies, 'yesNoSelection' => $yesNoSelection, 'yesNoSelectionForMinus' => $yesNoSelectionForMinus);

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getProfileDetails()
    {
        $employee = Helper::getEmployeeInfo();

        if(!empty($employee)){
            $personal_details = [
                'empID' => $employee->empID,
                'CompanyName' => isset($employee->company->CompanyName)?$employee->company->CompanyName:null,
                'empCompanySystemID' => $employee->empCompanySystemID,
                'empFullName' => $employee->empFullName,
                'empEmail' => $employee->empEmail,
                'empTelOffice' => $employee->empTelOffice,
                'empTelMobile' => $employee->empTelMobile,
                'extNo' => $employee->extNo,
                'DOB' => isset($employee->details->DOB)?Carbon::parse($employee->details->DOB)->format('Y-m-d'):null,
                'dateAssumed' => isset($employee->details->dateAssumed)?Carbon::parse($employee->details->dateAssumed)->format('Y-m-d'):null,
                'designation' => isset($employee->details->designation->designation)?$employee->details->designation->designation:null,
                'description' => isset($employee->details->maritial_status->description)?$employee->details->maritial_status->description:null,
                'religionName' => isset($employee->religions->religionName)?$employee->religions->religionName:null,
                'name' => isset($employee->genders->name)?$employee->genders->name:null,
                'countryName' => isset($employee->details->country->countryName)?$employee->details->country->countryName:null,
                'profileImage'=> isset($employee->profilepic->profileImage)?$employee->profilepic->profileImage:null,
                'department'=> isset($employee->details->departmentMaster->DepartmentDescription)?$employee->details->departmentMaster->DepartmentDescription:null,
            ];

            $reporting_manager =[
                'profileImage' => isset($employee->manager->profilepic->profileImage)?$employee->manager->profilepic->profileImage:null,
                'designation' => isset($employee->manager->details->designation->designation)?$employee->manager->details->designation->designation:null,
                'empFullName' => isset($employee->manager->empFullName)?$employee->manager->empFullName:null,
            ];

            $department = isset($employee->details->departmentMaster)?$employee->details->departmentMaster:null;

            $passport = $this->getEmployeePersonalDetailsByType($employee->empID,1); // type = 1 =>passport

            $resident_card = $this->getEmployeePersonalDetailsByType($employee->empID,4); // type = 4 =>Resident Card

            $output = [
                'personal_details'=>$personal_details,
                'reporting_manager'=>$reporting_manager,
                'passport'=>$passport,
                'resident_card'=>$resident_card,
                'department'=>$department,
            ];
            return $this->sendResponse($output, 'Employee profile details retrieved successfully');
        }else{
            return $this->sendError('Employee profile details not found');
        }
    }

    public function getEmployeePersonalDetailsByType($empID,$type) {
        // after changing personaldocuments crud using employeesystemid, use employeeSystemID, until use empID
        return HRMSPersonalDocuments::where('empID',$empID)->where('documentType',$type)->first();
    }

    public function getUserCountData() {

        $empCompanySystemID = [3,7 ,11,15,16,17,18,19,20,21,22,23,24,26,29,30,31,34,42,43,52,53,58,60,63];

        $noOfTaskUsers = User::whereHas('employee' , function($q) use ($empCompanySystemID){
                                    $q->where('discharegedYN','=',0)
                                      ->where('isSuperAdmin','=',0)
                                      ->whereIn('empCompanySystemID', $empCompanySystemID)->groupBy('employeeSystemID');
                                    })
                                ->count();

        $noOfErpUsers = EmployeeNavigation::whereHas('employee', function ($query) use ($empCompanySystemID) {
                            $query->where('discharegedYN','=',0)
                                ->where('isSuperAdmin','=',0)
                                ->whereIn('empCompanySystemID', $empCompanySystemID);
                        })->groupBy('employeeSystemID')->get();

        $noOfHrmsUsers = UserRights::whereHas('employee', function ($query) use ($empCompanySystemID) {
                            $query->where('discharegedYN','=',0)
                                ->where('isSuperAdmin','=',0)
                                ->whereIn('empCompanySystemID', $empCompanySystemID);
                        })->where('moduleMasterID','=',3)->groupBy('employeeSystemID')->get();

        $noOfQhseTaskUsers = EmployeesDepartment::whereHas('employee', function ($query) use ($empCompanySystemID) {
                                $query->where('discharegedYN','=',0)
                                    ->where('isSuperAdmin','=',0)
                                    ->whereIn('empCompanySystemID', $empCompanySystemID);
                            })->where('departmentSystemID','=',39)->groupBy('employeeSystemID')->get();

        $noOfQhseFuncUsers = LptPermission::whereHas('employee', function ($query) use ($empCompanySystemID) {
                                $query->where('discharegedYN','=',0)
                                    ->where('isSuperAdmin','=',0)
                                    ->whereIn('empCompanySystemID', $empCompanySystemID);
                            })->groupBy('employeeSystemID')->get();

        $noOfCementingUsers = \DB::select("select count(linkedID) as CementingUserCount
from (
select gears_cement.usermastertbl.linkedID
from gears_cement.usermastertbl
inner join employees ON gears_cement.usermastertbl.linkedID=employees.empID AND employees.discharegedYN=0 AND isSuperAdmin=0
WHERE employees.empCompanySystemID IN (3,7 ,11,15,16,17,18,19,20,21,22,23,24,26,29,30,31,34,42,43,52,53,58,60,63) ) CementingUserCount");

        $noOfOpUsers = EmployeesDepartment::whereHas('employee', function ($query) use ($empCompanySystemID) {
                                $query->where('discharegedYN','=',0)
                                    ->where('isSuperAdmin','=',0)
                                    ->whereIn('empCompanySystemID', $empCompanySystemID);
                            })->where('departmentSystemID','=',8)->groupBy('employeeSystemID')->get();

        $output = array(
            'noOfTaskUsers'=> $noOfTaskUsers,
            'noOfErpUsers'=> count($noOfErpUsers),
            'noOfHrmsUsers'=> count($noOfHrmsUsers),
            'noOfQhseTaskUsers'=> count($noOfQhseTaskUsers),
            'noOfQhseFuncUsers'=> count($noOfQhseFuncUsers),
            'noOfOpUsers'=> count($noOfOpUsers),
            'noOfCementingUsers'=> $noOfCementingUsers[0]->CementingUserCount,
        );
        $finalArray = array();

        array_push($finalArray,$output);
        return $this->sendResponse($finalArray, 'User Count details retrieved successfully');
    }

    public function employeeDocumentStatus(Request $request)
    {
        $input = $request->all();

        if(!isset($input['empID'])){
            return $this->sendError('Employee ID is required');
        }
        
        $empID = $input['empID'];

        $employee = Employee::where('empID',$empID)
                            ->where('discharegedYN',0)
                            ->first();

        if(!$employee){
            return $this->sendError('Employee not found');
        }
        $documentDetails = [];

        $supplierInvoice = BookInvSuppMaster::where('employeeID', $employee->employeeSystemID)
                                                ->where('documentType', 4)
                                                ->where('cancelYN', 0)
                                                ->where(function ($query) {
                                                    $query->where('confirmedYN', 0)
                                                        ->orWhere('approved', 0);
                                                })
                                                ->get();

        if ($supplierInvoice) {
            foreach ($supplierInvoice as $invoice) {
                $isSupConfirmed = $invoice->confirmedYN == 1 ? 1 : 0;
                $isSupApproved = $invoice->approved == -1 ? 1 : 0;
                $isSupRefferedBack= $invoice->refferedBackYN == -1 ? 1 : 0;

                $documentDetails[] = [
                    'documentCode' => $invoice->bookingInvCode,
                    'documentType' => 'Employee Direct Invoice',
                ];
                
                if (!$isSupConfirmed && !$isSupApproved && !$isSupRefferedBack) {
                    $documentDetails[count($documentDetails) - 1]['isDraft'] = 1;
                }

                if($isSupConfirmed || $isSupApproved){
                    $documentDetails[count($documentDetails) - 1]['isConfirmed'] = $isSupConfirmed;
                    $documentDetails[count($documentDetails) - 1]['isApproved'] = $isSupApproved;
                }

                if ($isSupRefferedBack && !$isSupApproved && $isSupConfirmed) {
                    $documentDetails[count($documentDetails) - 1]['isRefferedBack'] = $isSupRefferedBack;
                }

            }
        }

        $supplierInvoiceApproved = BookInvSuppMaster::where('employeeID', $employee->employeeSystemID)
                        ->where('documentType', 4)
                        ->where('approved', -1)
                        ->get(); 

        if ($supplierInvoiceApproved) {
            foreach ($supplierInvoiceApproved as $invoiceApproved) {
                $pvSettled = PaySupplierInvoiceDetail::with(['payment_master','matching_master'])
                    ->where('bookingInvSystemCode', $invoiceApproved->bookingSuppMasInvAutoID)
                    ->get();

                $pvSettledCount = PaySupplierInvoiceDetail::where('bookingInvSystemCode', $invoiceApproved->bookingSuppMasInvAutoID)
                    ->count();

                if ($pvSettled && $pvSettledCount > 0) {
                    $partiallySettled = 0;
                    $supplierPaymentAmount = 0;
                    foreach ($pvSettled as $pvSettle) {
                        if($pvSettle->matching_master && $pvSettle->matching_master->matchingConfirmedYN == 1){
                            $supplierPaymentAmount += $pvSettle->paymentLocalAmount;
                        }

                        if(!$pvSettle->matching_master && $pvSettle->payment_master && $pvSettle->payment_master->approved == -1){
                            $supplierPaymentAmount += $pvSettle->paymentLocalAmount;
                        }
                    }

                    $balanceAmount = $invoiceApproved->bookingAmountLocal - $supplierPaymentAmount;
                    


                    if($balanceAmount > 0){
                        $documentDetails[] = [
                            'documentCode' => $invoiceApproved->bookingInvCode,
                            'documentType' => 'Employee Direct Invoice',
                            'isConfirmed' => 1,
                            'isApproved' => 1,
                            'isPartiallySettled' => 1,
                        ];
                    }
                } else if($pvSettledCount == 0){
                    $documentDetails[] = [
                        'documentCode' => $invoiceApproved->bookingInvCode,
                        'documentType' => 'Employee Direct Invoice',
                        'isConfirmed' => 1,
                        'isApproved' => 1,
                        'isNotSettled' => 1,
                    ];
                }
            }

        }

        $expenseClaim = ExpenseClaimMaster::where('claimedByEmpID', $employee->employeeSystemID)
                        ->get();
        if ($expenseClaim) {
            foreach ($expenseClaim as $claim) {
                if($claim->addedForPayment == 0){

                    $documentDetails[] = [
                        'documentCode' => $claim->expenseClaimCode,
                        'documentType' => 'Expense Claim',
                    ];
                    
                    if ($claim->confirmedYN == 0 && $claim->approvedYN == 0) {
                        $documentDetails[count($documentDetails) - 1]['isDraft'] = 1;
                    }
    
                    if($claim->confirmedYN == 1 && $claim->approvedYN == 0){
                        $documentDetails[count($documentDetails) - 1]['isConfirmed'] = 1;
                        $documentDetails[count($documentDetails) - 1]['isApproved'] = 0;
                    }

                    if($claim->confirmedYN == 1 && $claim->approvedYN == 1){
                        $documentDetails[count($documentDetails) - 1]['isConfirmed'] = 1;
                        $documentDetails[count($documentDetails) - 1]['isApproved'] = 1;
                        $documentDetails[count($documentDetails) - 1]['isNotPaid'] = 1;

                    }
    
                    if ($claim->confirmedYN == 2) {
                        $documentDetails[count($documentDetails) - 1]['isRefferedBack'] = 1;
                    }
                }

                if($claim->addedForPayment == -1){
                    $directPaymentDetails = DirectPaymentDetails::where('expenseClaimMasterAutoID', $claim->expenseClaimMasterAutoID)
                        ->latest('directPaymentDetailsID')
                        ->first();
                    if($directPaymentDetails){
                        $directPaymentVoucher = PaySupplierInvoiceMaster::where('PayMasterAutoId', $directPaymentDetails->directPaymentAutoID)
                                                ->first();
                        if($directPaymentVoucher && $directPaymentVoucher->approved == 0){
                            $documentDetails[] = [
                                'documentCode' => $claim->expenseClaimCode,
                                'documentType' => 'Expense Claim',
                                'isConfirmed' => 1,
                                'isApproved' => 1,
                                'isPartiallyPaid' => 1,
                            ];
                        }
                    }
                }
            }
        }

        $paymentVoucher = PaySupplierInvoiceMaster::where('directPaymentPayeeEmpID', $employee->employeeSystemID)
                                                    ->whereIn('invoiceType', [6, 7, 3])
                                                    ->where('cancelYN', 0)
                                                    ->where(function ($query) {
                                                        $query->where('confirmedYN', 0)
                                                            ->orWhere('approved', 0);
                                                    })
                                                    ->get();

        if ($paymentVoucher) {
            foreach ($paymentVoucher as $payVoucher) {
                if ($payVoucher->invoiceType == 6) {
                    $documentType = 'Employee Payment';
                } else if ($payVoucher->invoiceType == 7) {
                    $documentType = 'Employee Advance Payment';
                } else {
                    $documentType = 'Direct Payment';
                }

                $isPayConfirmed = $payVoucher->confirmedYN == 1 ? 1 : 0;
                $isPayApproved = $payVoucher->approved == -1 ? 1 : 0;
                $isPayRefferedBack= $payVoucher->refferedBackYN == -1 ? 1 : 0;

                $documentDetails[] = [
                    'documentCode' => $payVoucher->BPVcode,
                    'documentType' => $documentType
                ];
                
                if (!$isPayConfirmed && !$isPayApproved && !$isPayRefferedBack) {
                    $documentDetails[count($documentDetails) - 1]['isDraft'] = 1;
                }

                if($isPayConfirmed || $isPayApproved){
                    $documentDetails[count($documentDetails) - 1]['isConfirmed'] = $isPayConfirmed;
                    $documentDetails[count($documentDetails) - 1]['isApproved'] = $isPayApproved;
                }

                if ($isPayRefferedBack && !$isPayApproved && $isPayConfirmed) {
                    $documentDetails[count($documentDetails) - 1]['isRefferedBack'] = $isPayRefferedBack;
                }
            }
        }

        $paymentVoucherApproved = PaySupplierInvoiceMaster::where('directPaymentPayeeEmpID', $employee->employeeSystemID)
                        ->whereIn('invoiceType', [6, 7, 3])
                        ->where('approved', -1)
                        ->get();

        if ($paymentVoucherApproved) {
            foreach ($paymentVoucherApproved as $payVoucherApproved) {
                $voucherMatching = MatchDocumentMaster::where('employee_id', $employee->employeeSystemID)
                    ->where('PayMasterAutoId', $payVoucherApproved->PayMasterAutoId)
                    ->where('documentSystemID', 4)
                    ->where('BPVcode', $payVoucherApproved->BPVcode)
                    ->latest('matchDocumentMasterAutoID')
                    ->first();

                $voucherMatchingCount = MatchDocumentMaster::where('employee_id', $employee->employeeSystemID)
                    ->where('PayMasterAutoId', $payVoucherApproved->PayMasterAutoId)
                    ->where('documentSystemID', 4)
                    ->where('BPVcode', $payVoucherApproved->BPVcode)
                    ->count();
                    
                if ($voucherMatching) {
                    $matchingBalance = $voucherMatching->matchBalanceAmount - $voucherMatching->matchingAmount;
                    if ($voucherMatching->matchingConfirmedYN == 0 || ($voucherMatching->matchingConfirmedYN == 1 && $matchingBalance > 0)) {
                        if ($payVoucherApproved->invoiceType == 6) {
                            $documentType = 'Employee Payment';
                        } else if ($payVoucherApproved->invoiceType == 7) {
                            $documentType = 'Employee Advance Payment';
                        } else {
                            $documentType = 'Direct Payment';
                        }
        
                        $documentDetails[] = [
                            'documentCode' => $payVoucherApproved->BPVcode,
                            'documentType' => $documentType,
                            'isConfirmed' => 1,
                            'isApproved' => 1,
                            'isPartiallyMatched' => 1,
                        ];
                    }
                }

                if($voucherMatchingCount == 0 && $payVoucherApproved->invoiceType == 7){
                    $documentDetails[] = [
                        'documentCode' => $payVoucherApproved->BPVcode,
                        'documentType' => 'Employee Advance Payment',
                        'isConfirmed' => 1,
                        'isApproved' => 1,
                        'isNotMatched' => 1,
                    ];
                }
            }
        }
        
        $debitNote = DebitNote::where('empID', $employee->employeeSystemID)
                    ->where('type', 2)
                    ->where(function ($query) {
                        $query->where('confirmedYN', 0)
                            ->orWhere('approved', 0);
                    })
                    ->get();

        if ($debitNote) {
            foreach ($debitNote as $note) {
                $isNoteConfirmed = $note->confirmedYN == 1 ? 1 : 0;
                $isNoteApproved = $note->approved == -1 ? 1 : 0;
                $isNoteRefferedBack= $note->refferedBackYN == -1 ? 1 : 0;

                $documentDetails[] = [
                    'documentCode' => $note->debitNoteCode,
                    'documentType' => 'Debit Note',
                ];
                
                if (!$isNoteConfirmed && !$isNoteApproved && !$isNoteRefferedBack) {
                    $documentDetails[count($documentDetails) - 1]['isDraft'] = 1;
                }

                if($isNoteConfirmed || $isNoteApproved){
                    $documentDetails[count($documentDetails) - 1]['isConfirmed'] = $isNoteConfirmed;
                    $documentDetails[count($documentDetails) - 1]['isApproved'] = $isNoteApproved;
                }

                if ($isNoteRefferedBack && !$isNoteApproved && $isNoteConfirmed) {
                    $documentDetails[count($documentDetails) - 1]['isRefferedBack'] = $isNoteRefferedBack;
                }
            }
        }

        $debitNoteApproved = DebitNote::where('empID', $employee->employeeSystemID)
            ->where('type', 2)
            ->where('approved', -1)
            ->get();

        if ($debitNoteApproved) {
            foreach ($debitNoteApproved as $noteApproved) {
                $debitVoucherMatching = MatchDocumentMaster::where('employee_id', $employee->employeeSystemID)
                    ->where('PayMasterAutoId', $noteApproved->debitNoteAutoID)
                    ->where('documentSystemID', 15)
                    ->where('BPVcode', $noteApproved->debitNoteCode)
                    ->latest('matchDocumentMasterAutoID')
                    ->first();

                $debitVoucherMatchingCount = MatchDocumentMaster::where('employee_id', $employee->employeeSystemID)
                    ->where('PayMasterAutoId', $noteApproved->debitNoteAutoID)
                    ->where('documentSystemID', 15)
                    ->where('BPVcode', $noteApproved->debitNoteCode)
                    ->count();

                if ($debitVoucherMatching) {
                    $debitMatchingBalance = $debitVoucherMatching->matchBalanceAmount - $debitVoucherMatching->matchingAmount;
                    if ($debitVoucherMatching->matchingConfirmedYN == 0 ||  ($debitVoucherMatching->matchingConfirmedYN == 1 && $debitMatchingBalance > 0)) {
                        $documentDetails[] = [
                            'documentCode' => $noteApproved->debitNoteCode,
                            'documentType' => 'Debit Note',
                            'isConfirmed' => 1,
                            'isApproved' => 1,
                            'isPartiallyMatched' => 1,
                        ];
                    }
                }

                if($debitVoucherMatchingCount == 0){
                    $documentDetails[] = [
                        'documentCode' => $noteApproved->debitNoteCode,
                        'documentType' => 'Debit Note',
                        'isConfirmed' => 1,
                        'isApproved' => 1,
                        'isNotMatched' => 1,
                    ];
                }
            }
        }

        $receipetVoucher = CustomerReceivePayment::where('PayeeEmpID', $employee->employeeSystemID)
        ->where('documentType', 14)
        ->where('payeeTypeID', 2)
        ->where('cancelYN', 0)
        ->where(function ($query) {
            $query->where('confirmedYN', 0)
                ->orWhere('approved', 0);
        })
        ->get();

        if ($receipetVoucher) {
            foreach ($receipetVoucher as $recVoucher) {

                $isRecConfirmed = $recVoucher->confirmedYN == 1 ? 1 : 0;
                $isRecApproved = $recVoucher->approved == -1 ? 1 : 0;
                $isRecRefferedBack= $recVoucher->refferedBackYN == -1 ? 1 : 0;

                $documentDetails[] = [
                    'documentCode' => $recVoucher->custPaymentReceiveCode,
                    'documentType' => 'Direct Receipet Voucher ',
                ];
                
                if (!$isRecConfirmed && !$isRecApproved && !$isRecRefferedBack) {
                    $documentDetails[count($documentDetails) - 1]['isDraft'] = 1;
                }

                if($isRecConfirmed || $isRecApproved){
                    $documentDetails[count($documentDetails) - 1]['isConfirmed'] = $isRecConfirmed;
                    $documentDetails[count($documentDetails) - 1]['isApproved'] = $isRecApproved;
                }

                if ($isRecRefferedBack && !$isRecApproved && $isRecConfirmed) {
                    $documentDetails[count($documentDetails) - 1]['isRefferedBack'] = $isRecRefferedBack;
                }
            }
        }


        $result = $this->convertDocumentDetails($documentDetails);
        
        $output = [
            'documentDetails' => $result,
            'employeeDocumentStatus' => (count($documentDetails) > 0) ? true : false 
        ];



        return $this->sendResponse($output, 'Employee document status retrieved successfully');

    }

    public function convertDocumentDetails(array $documents): array
    {
        $statusKeys = [
            'isDraft' => ['true' => 'Draft', 'false' => 'Not Draft'],
            'isApproved' => ['true' => 'Approved', 'false' => 'Not Approved'],
            'isConfirmed' => ['true' => 'Confirmed', 'false' => 'Not Confirmed'],
            'isPartiallyMatched' => ['true' => 'Partially Matched', 'false' => 'Not Partially Matched'],
            'isPartiallySettled' => ['true' => 'Partially Settled', 'false' => 'Not Partially Settled'],
            'isNotSettled' => ['true' => 'Not Settled', 'false' => 'Settled'],
            'isAddedForPayment' => ['true' => 'Added for Payment', 'false' => 'Not Added for Payment'],
            'isNotMatched' => ['true' => 'Not Matched', 'false' => 'Matched'],
            'isRefferedBack' => ['true' => 'Referred Back', 'false' => 'Not Referred Back'],
            'isNotPaid' => ['true' => 'Not Paid', 'false' => 'Paid'],
            'isPartiallyPaid' => ['true' => 'Partially Paid', 'false' => 'Not Partially Paid']
        ];

        $converted = [];

        foreach ($documents as $document) {
            $docStatus = [];

            foreach ($statusKeys as $key => $labels) {
                if (isset($document[$key])) {
                    $docStatus[] = ($document[$key] === 1 || $document[$key] === -1) ? $labels['true'] : $labels['false'];
                } 
            }

            $converted[] = [
                'documentCode' => $document['documentCode'] ?? null,
                'documentType' => $document['documentType'] ?? null,
                'documentStatus' => $docStatus
            ];
        }

        return $converted;
    }
    
}
