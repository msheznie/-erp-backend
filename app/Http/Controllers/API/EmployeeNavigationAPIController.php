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

        return $this->sendResponse($employeeNavigations->toArray(), 'Employee Navigations retrieved successfully');
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

        $validate = EmployeeNavigation::with(['usergroup'])->where('companyID',$request->companyID)->whereIN('employeeSystemID',$employees)->first();

        if($validate && $validate->usergroup){
            return $this->sendError('Selected employee already exists in the '.$validate->usergroup->description.' user group');
        }else{
            if($employees){
                foreach ($employees as $val){
                    $inputArr = ["companyID" => $input["companyID"],"userGroupID" => $input["userGroupID"], "employeeSystemID" => $val];
                    $employeeNavigations = $this->employeeNavigationRepository->create($inputArr);
                }
            }
        }

        return $this->sendResponse($employeeNavigations->toArray(), 'Employee Navigation saved successfully');
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
            return $this->sendError('Employee Navigation not found');
        }

        return $this->sendResponse($employeeNavigation->toArray(), 'Employee Navigation retrieved successfully');
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
            return $this->sendError('Employee Navigation not found');
        }

        $employeeNavigation = $this->employeeNavigationRepository->update($input, $id);

        return $this->sendResponse($employeeNavigation->toArray(), 'EmployeeNavigation updated successfully');
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
            return $this->sendError('Employee Navigation not found');
        }

        $employeeNavigation->delete();

        return $this->sendResponse($id, 'Employee Navigation deleted successfully');
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
        return $this->sendResponse($groupCompany, 'Employee Navigation deleted successfully');
    }

}
