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

/**
 * Class EmployeeNavigationController
 * @package App\Http\Controllers\API
 */
class EmployeeNavigationAPIController extends AppBaseController
{
    /** @var  EmployeeNavigationRepository */
    private $employeeNavigationRepository;

    public function __construct(EmployeeNavigationRepository $employeeNavigationRepo)
    {
        $this->employeeNavigationRepository = $employeeNavigationRepo;
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

        $validate = EmployeeNavigation::where('companyID',$request->companyID)->where('employeeSystemID',$request->employeeSystemID)->exists();
        if($validate){
            return $this->sendError('Selected employee already exists in the selected user group');
        }else{
            $employeeNavigations = $this->employeeNavigationRepository->create($input);
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
        $userGroup = EmployeeNavigation::with(['company', 'usergroup', 'employee']);
        if (array_key_exists('selectedCompanyID', $input)) {
            if ($input['selectedCompanyID'] > 0) {
                $userGroup->where('companyID', $input['selectedCompanyID']);
            }
        } else {
            $companiesByGroup = "";
            if (!\Helper::checkIsCompanyGroup($input['globalCompanyId'])) {
                $companiesByGroup = $input['globalCompanyId'];
                $userGroup->where('companyID', $companiesByGroup);
            }
        }

        if (array_key_exists('userGroupID', $input)) {
            $userGroup->where('userGroupID', $input['userGroupID']);
        }

        if (array_key_exists('employeeSystemID', $input)) {
            $userGroup->where('employeeSystemID', $input['employeeSystemID']);
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

}
