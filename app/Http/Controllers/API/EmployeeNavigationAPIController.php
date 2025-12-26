<?php
/**
 * =============================================
 * -- File Name : EmployeeNavigationAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Employee Navigation
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Employee Navigation
 * -- REVISION HISTORY
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEmployeeNavigationAPIRequest;
use App\Http\Requests\API\UpdateEmployeeNavigationAPIRequest;
use App\Models\Company;
use App\Models\EmployeeNavigation;
use App\Repositories\EmployeeNavigationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use App\Models\EmployeeNavigationAccess;

/**
 * Class EmployeeNavigationController
 * @package App\Http\Controllers\API
 */
class EmployeeNavigationAPIController extends AppBaseController
{
    /** @var  EmployeeNavigationRepository */
    private $employeeNavigationRepository;

    public function __construct(EmployeeNavigationRepository $employeeNavigationRepo, UserRepository $userRepo)
    {
        $this->employeeNavigationRepository = $employeeNavigationRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the EmployeeNavigation.
     * GET|HEAD /employeeNavigations
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->employeeNavigationRepository->pushCriteria(new RequestCriteria($request));
        $this->employeeNavigationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $employeeNavigations = $this->employeeNavigationRepository->all();

        return $this->sendResponse($employeeNavigations->toArray(), trans('custom.employee_navigations_retrieved_successfully'));
    }

    /**
     * Store a newly created EmployeeNavigation in storage.
     * POST /employeeNavigations
     *
     * @param CreateEmployeeNavigationAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateEmployeeNavigationAPIRequest $request)
    {
        $input = $request->all();
        $employees = collect($input["employeeSystemID"])->pluck("employeeSystemID")->toArray();

        // Validate access type and dates
        $accessType = isset($input["accessType"]) ? $input["accessType"] : 'permanent';

        if ($accessType === 'time_based') {
            // Validate that start date and end date are provided (mandatory for time-based access)
            if (!isset($input["startDate"]) || empty($input["startDate"])) {
                return $this->sendError(trans('custom.start_date_is_required_for_time_based_access'));
            }
            if (!isset($input["endDate"]) || empty($input["endDate"])) {
                return $this->sendError(trans('custom.end_date_is_required_for_time_based_access'));
            }

            try {
                $startDate = \Carbon\Carbon::createFromFormat('Y-m-d', $input["startDate"])->startOfDay();
                $endDate = \Carbon\Carbon::createFromFormat('Y-m-d', $input["endDate"])->startOfDay();
            } catch (\Exception $e) {
                // If format is different, try parse but force date-only
                $startDate = \Carbon\Carbon::parse($input["startDate"])->startOfDay();
                $endDate = \Carbon\Carbon::parse($input["endDate"])->startOfDay();
            }

            $today = \Carbon\Carbon::today()->startOfDay();

            // Validate start date >= current date
            if ($startDate->lt($today)) {
                return $this->sendError(trans('custom.start_date_must_be_greater_than_or_equal_to_current_date'));
            }

            // Validate end date >= current date
            if ($endDate->lt($today)) {
                return $this->sendError(trans('custom.end_date_must_be_greater_than_or_equal_to_current_date'));
            }

            // Validate end date >= start date
            if ($endDate->lt($startDate)) {
                return $this->sendError(trans('custom.end_date_must_be_greater_than_or_equal_to_start_date'));
            }
        }

        $validate = EmployeeNavigation::with(['usergroup'])->where('companyID',$request->companyID)->whereIN('employeeSystemID',$employees)->first();

        if($validate && $validate->usergroup){
            return $this->sendError(trans('custom.selected_employee_already_exists_in_the').$validate->usergroup->description.' user group');
        }else{
            if($employees){
                foreach ($employees as $val){
                    $inputArr = ["companyID" => $input["companyID"],"userGroupID" => $input["userGroupID"], "employeeSystemID" => $val];
                    $employeeNavigations = $this->employeeNavigationRepository->create($inputArr);
                    $accessData = [
                        'employeeNavigationID' => $employeeNavigations->id,
                        'userGroupID' => $input["userGroupID"],
                        'employeeSystemID' => $val,
                        'companyID' => $input["companyID"],
                        'isDelegation' => isset($input["isDelegation"]) ? $input["isDelegation"] : 0,
                        'accessType' => $accessType,
                        'isActive' => 1
                    ];
                    if ($accessType === 'time_based') {
                        // Format as date-only (Y-m-d) to avoid timezone issues
                        $accessData["startDate"] = $startDate->format('Y-m-d');
                        $accessData["endDate"] = $endDate->format('Y-m-d');
                    }
                    EmployeeNavigationAccess::create($accessData);
                }
            }
        }

        return $this->sendResponse($employeeNavigations->toArray(), trans('custom.employee_navigation_saved_successfully'));
    }

    /**
     * Display the specified EmployeeNavigation.
     * GET|HEAD /employeeNavigations/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var EmployeeNavigation $employeeNavigation */
        $employeeNavigation = $this->employeeNavigationRepository->findWithoutFail($id);

        if (empty($employeeNavigation)) {
            return $this->sendError(trans('custom.employee_navigation_not_found'));
        }

        return $this->sendResponse($employeeNavigation->toArray(), trans('custom.employee_navigation_retrieved_successfully'));
    }

    /**
     * Update the specified EmployeeNavigation in storage.
     * PUT/PATCH /employeeNavigations/{id}
     *
     * @param  int $id
     * @param UpdateEmployeeNavigationAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateEmployeeNavigationAPIRequest $request)
    {
        $input = $request->all();

        /** @var EmployeeNavigation $employeeNavigation */
        $employeeNavigation = $this->employeeNavigationRepository->findWithoutFail($id);

        if (empty($employeeNavigation)) {
            return $this->sendError(trans('custom.employee_navigation_not_found'));
        }

        $employeeNavigation = $this->employeeNavigationRepository->update($input, $id);

        return $this->sendResponse($employeeNavigation->toArray(), trans('custom.employeenavigation_updated_successfully'));
    }

    /**
     * Remove the specified EmployeeNavigation from storage.
     * DELETE /employeeNavigations/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var EmployeeNavigation $employeeNavigation */
        $employeeNavigation = $this->employeeNavigationRepository->findWithoutFail($id);

        if (empty($employeeNavigation)) {
            return $this->sendError(trans('custom.employee_navigation_not_found'));
        }
        EmployeeNavigationAccess::markAsInactive($employeeNavigation);
        $employeeNavigation->delete();

        return $this->sendResponse($id, trans('custom.employee_navigation_deleted_successfully'));
    }

    public function getUserGroupEmployeesByCompanyDatatable(Request $request)
    {
        $input = $request->all();
        $userGroup = EmployeeNavigation::with(['company', 'usergroup'=>function($q){
            $q->where('delegation_id',0);
        }, 'employee' => function ($query) use ($input) {
            if (array_key_exists('dischargedYN', $input)) {
                $query->where('discharegedYN', $input['dischargedYN']);
            }
          }])->whereHas('usergroup',function($q){
            $q->where('delegation_id',0);
        });
        if (array_key_exists('selectedCompanyID', $input)) {
            if ($input['selectedCompanyID'] > 0) {
                $userGroup->where('srp_erp_employeenavigation.companyID', $input['selectedCompanyID']);
            }
        } else {
            $companiesByGroup = "";
            if(isset($input['globalCompanyId'])) {
                if (!\Helper::checkIsCompanyGroup($input['globalCompanyId'])) {
                    $companiesByGroup = $input['globalCompanyId'];
                    $userGroup->where('srp_erp_employeenavigation.companyID', $companiesByGroup);
                }
            }
        }

        if (array_key_exists('userGroupID', $input)) {
            $userGroup->where('userGroupID', $input['userGroupID']);
        }

        if (array_key_exists('employeeSystemID', $input)) {
            $userGroup->where('employeeSystemID', $input['employeeSystemID']);
        }

        if (array_key_exists('dischargedYN', $input)) {
            $userGroup->whereHas('employee', function ($query) use ($input) {
                            $query->where('discharegedYN', $input['dischargedYN']);
                        });
        }

        return \DataTables::eloquent($userGroup)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->make(true);

    }

    public function assignEmployeeUsergroup(Request $request)
    {
        $assignEmployee = EmployeeNavigation::create($request);

    }

    public function getuserGroupAssignedCompanies(Request $request){
        $id = Auth::id();

        $user = $this->userRepository->findWithoutFail($id);

        $selectedCompanyId = (isset($request['selectedCompanyId'])) ? $request['selectedCompanyId'] : 0;
        
        $employee= EmployeeNavigation::select('companyID')->where('employeeSystemID',$user->employee_id)->get();
        $companiesByGroup = array_pluck($employee, 'companyID');

        $groupCompany = Company::whereIN('companySystemID',$companiesByGroup)->where('isGroup',0);

        if ($selectedCompanyId > 0) {
           $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

            if($isGroup){
                $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
            }else{
                $subCompanies = [$selectedCompanyId];
            }

            $groupCompany = $groupCompany->whereIn('companySystemID', $subCompanies);
        }

        $groupCompany = $groupCompany->get();
        return $this->sendResponse($groupCompany, trans('custom.employee_navigation_deleted_successfully'));
    }

    /**
     * Get employees by user group ID for datatable
     * POST /getEmployeesByUserGroupDatatable
     *
     * @param Request $request
     * @return Response
     */
    public function getEmployeesByUserGroupDatatable(Request $request)
    {
        $input = $request->all();
        $userGroupID = isset($input['userGroupID']) ? $input['userGroupID'] : null;

        if (!$userGroupID) {
            return \DataTables::of(collect([]))->make(true);
        }

        $accessRecords = EmployeeNavigationAccess::withTrashed()
        ->with(['employeeNavigation', 'company', 'employee', 'userGroup' => function($q){
            $q->where('delegation_id', 0);
        }])
            ->whereHas('userGroup', function($q){
                $q->where('delegation_id', 0);
            })
            ->where('userGroupID', $userGroupID);

        $today = \Carbon\Carbon::today();

        return \DataTables::eloquent($accessRecords)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->addColumn('empName', function($access) {
                return $access->employee ? $access->employee->empName : '-';
            })
            ->addColumn('empID', function($access) {
                return $access->employee ? $access->employee->empID : '-';
            })
            ->addColumn('companyName', function($access) {
                return $access->company ? $access->company->CompanyName : '-';
            })
            ->addColumn('accessType', function($access) {
                return $access->accessType;
            })
            ->addColumn('startDate', function($access) {
                if ($access->accessType === 'time_based' && $access->startDate) {
                    return \Carbon\Carbon::parse($access->startDate)->format('Y-m-d');
                }
                return '-';
            })
            ->addColumn('endDate', function($access) {
                if ($access->accessType === 'time_based' && $access->endDate) {
                    return \Carbon\Carbon::parse($access->endDate)->format('Y-m-d');
                }
                return '-';
            })
            ->addColumn('status', function($access) use ($today) {
                if (!$access->isActive) {
                    return 'inactive';
                }

                if ($access->accessType === 'permanent') {
                    return 'active';
                } else if ($access->accessType === 'time_based' && $access->endDate) {
                    $endDate = \Carbon\Carbon::parse($access->endDate);
                    return $endDate->lt($today) ? 'inactive' : 'active';
                }
                return 'active';
            })
            ->make(true);
    }

}
