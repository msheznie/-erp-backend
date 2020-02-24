<?php
/**
 * =============================================
 * -- File Name : EmployeesDepartmentAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report
 * -- Author : Mubashir
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Employees department.
 * -- REVISION HISTORY
 * -- Date: 11-May 2018 By: Mubashir Description: Added new function getApprovalAccessRights(),
 * -- Date: 18-May 2018 By: Mubashir Description: Added new function getApprovalAccessRightsFormData() and getDepartmentDocument(),
 * -- Date: 24-Feb 2020 By: Zakeeul Description: Added new function mirrorAccessRights(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEmployeesDepartmentAPIRequest;
use App\Http\Requests\API\UpdateEmployeesDepartmentAPIRequest;
use App\Models\ApprovalGroups;
use App\Models\Company;
use App\Models\DepartmentMaster;
use App\Models\DocumentMaster;
use App\Models\Employee;
use App\Models\EmployeesDepartment;
use App\Models\SegmentMaster;
use App\Repositories\EmployeesDepartmentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EmployeesDepartmentController
 * @package App\Http\Controllers\API
 */
class EmployeesDepartmentAPIController extends AppBaseController
{
    /** @var  EmployeesDepartmentRepository */
    private $employeesDepartmentRepository;

    public function __construct(EmployeesDepartmentRepository $employeesDepartmentRepo)
    {
        $this->employeesDepartmentRepository = $employeesDepartmentRepo;
    }

    /**
     * Display a listing of the EmployeesDepartment.
     * GET|HEAD /employeesDepartments
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->employeesDepartmentRepository->pushCriteria(new RequestCriteria($request));
        $this->employeesDepartmentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $employeesDepartments = $this->employeesDepartmentRepository->all();

        return $this->sendResponse($employeesDepartments->toArray(), 'Employees Departments retrieved successfully');
    }

    /**
     * Store a newly created EmployeesDepartment in storage.
     * POST /employeesDepartments
     *
     * @param CreateEmployeesDepartmentAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateEmployeesDepartmentAPIRequest $request)
    {
        $input = $request->all();
        foreach ($input as $key => $val) {
            $val = $this->convertArrayToValue($val);
            $input[$key]['companySystemID'] = $val['companySystemID'];
            $input[$key]['documentSystemID'] = $val['documentSystemID'];
            $input[$key]['departmentSystemID'] = $val['departmentSystemID'];
            $input[$key]['ServiceLineSystemID'] = $val['ServiceLineSystemID'];
            $input[$key]['employeeSystemID'] = $val['employeeSystemID'];
            if ($val['companySystemID']) {
                $companyID = Company::find($val['companySystemID']);
                $input[$key]['companyId'] = $companyID->CompanyID;
            }
            if ($val['documentSystemID']) {
                $documentID = DocumentMaster::find($val['documentSystemID']);
                $input[$key]['documentID'] = $documentID->documentID;
            }
            if ($val['departmentSystemID']) {
                $departmentID = DepartmentMaster::find($val['departmentSystemID']);
                $input[$key]['departmentID'] = $departmentID->DepartmentID;
            }
            if ($val['ServiceLineSystemID']) {
                $ServiceLineID = SegmentMaster::find($val['ServiceLineSystemID']);
                $input[$key]['ServiceLineID'] = $ServiceLineID->ServiceLineCode;
            }
            if ($val['employeeSystemID']) {
                $employeeID = Employee::find($val['employeeSystemID']);
                $input[$key]['employeeID'] = $employeeID->empID;
            }
            $input[$key]['timeStamp'] = date("Y-m-d H:m:s");
        }

        //$employeesDepartments = $this->employeesDepartmentRepository->create($input);
        $employeesDepartments = EmployeesDepartment::insert($input);

        return $this->sendResponse($employeesDepartments, 'Employees Department saved successfully');
    }

    /**
     * Display the specified EmployeesDepartment.
     * GET|HEAD /employeesDepartments/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var EmployeesDepartment $employeesDepartment */
        $employeesDepartment = $this->employeesDepartmentRepository->findWithoutFail($id);

        if (empty($employeesDepartment)) {
            return $this->sendError('Employees Department not found');
        }

        return $this->sendResponse($employeesDepartment->toArray(), 'Employees Department retrieved successfully');
    }

    /**
     * Update the specified EmployeesDepartment in storage.
     * PUT/PATCH /employeesDepartments/{id}
     *
     * @param  int $id
     * @param UpdateEmployeesDepartmentAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateEmployeesDepartmentAPIRequest $request)
    {
        $input = $request->all();

        /** @var EmployeesDepartment $employeesDepartment */
        $employeesDepartment = $this->employeesDepartmentRepository->findWithoutFail($id);

        if (empty($employeesDepartment)) {
            return $this->sendError('Employees Department not found');
        }

        $employeesDepartment = $this->employeesDepartmentRepository->update($input, $id);

        return $this->sendResponse($employeesDepartment->toArray(), 'EmployeesDepartment updated successfully');
    }

    /**
     * Remove the specified EmployeesDepartment from storage.
     * DELETE /employeesDepartments/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var EmployeesDepartment $employeesDepartment */
        $employeesDepartment = $this->employeesDepartmentRepository->findWithoutFail($id);

        if (empty($employeesDepartment)) {
            return $this->sendError('Employees Department not found');
        }

        $employeesDepartment->delete();

        return $this->sendResponse($id, 'Employees Department deleted successfully');
    }

    public function getApprovalAccessRightsDatatable(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $employeesDepartment = EmployeesDepartment::with(['company', 'department', 'serviceline', 'document', 'approvalgroup'])->where('employeeSystemID', $request->employeeSystemID)->selectRaw('*,false as selected');
        $search = $request->input('search.value');

        if (array_key_exists('companySystemID', $input)) {
            if(is_array($input['companySystemID'])){
                $input['companySystemID'] = $input['companySystemID'][0];
            }
            if ($input['companySystemID'] > 0) {
                $employeesDepartment->whereHas('company', function ($q) use ($input) {
                    $q->where('companySystemID', $input['companySystemID']);
                });
            }else {
                if (!\Helper::checkIsCompanyGroup($input['globalCompanyId'])) {
                    $employeesDepartment->where('companySystemID',$input['globalCompanyId']);
                }else{
                    $companiesByGroup = \Helper::getGroupCompany($input['globalCompanyId']);
                    $employeesDepartment->whereIN('companySystemID',$companiesByGroup);
                }
            }
        }else{
            if (!\Helper::checkIsCompanyGroup($input['globalCompanyId'])) {
                $employeesDepartment->where('companySystemID',$input['globalCompanyId']);
            }else{
                $companiesByGroup = \Helper::getGroupCompany($input['globalCompanyId']);
                $employeesDepartment->whereIN('companySystemID',$companiesByGroup);
            }
        }
        if (array_key_exists('documentSystemID', $input)) {
            if ($input['documentSystemID'] > 0) {
                $employeesDepartment->whereHas('document', function ($q) use ($input) {
                    $q->where('documentSystemID', $input['documentSystemID']);
                });
            }
        }
        if (array_key_exists('departmentSystemID', $input)) {
            if ($input['departmentSystemID'] > 0) {
                $employeesDepartment->whereHas('department', function ($q) use ($input) {
                    $q->where('departmentSystemID', $input['departmentSystemID']);
                });
            }
        }
        if (array_key_exists('servicelineSystemID', $input)) {
            if ($input['servicelineSystemID'] > 0) {
                $employeesDepartment->whereHas('serviceline', function ($q) use ($input) {
                    $q->where('servicelineSystemID', $input['servicelineSystemID']);
                });
            }
        }
        if (array_key_exists('approvalGroupID', $input)) {
            if ($input['approvalGroupID'] > 0) {
                $employeesDepartment->whereHas('approvalgroup', function ($q) use ($input) {
                    $q->where('employeeGroupID', $input['approvalGroupID']);
                });
            }
        }
        if ($search) {
            $employeesDepartment = $employeesDepartment->where(function ($q) use ($search) {
                $q->whereHas('company', function ($query) use ($search) {
                    $query->where('CompanyID', 'LIKE', "%{$search}%");
                })->orWhereHas('department', function ($query) use ($search) {
                    $query->where('DepartmentDescription', 'LIKE', "%{$search}%");
                })->orWhereHas('serviceline', function ($query) use ($search) {
                    $query->where('ServiceLineDes', 'LIKE', "%{$search}%");
                })->orWhereHas('document', function ($query) use ($search) {
                    $query->where('documentDescription', 'LIKE', "%{$search}%");
                })->orWhereHas('approvalgroup', function ($query) use ($search) {
                    $query->where('rightsGroupDes', 'LIKE', "%{$search}%");
                });
            });
        }

        return \DataTables::eloquent($employeesDepartment)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('employeesDepartmentsID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            //->addColumn('Index', 'Index', "Index")
            ->make(true);

    }

    public function getApprovalAccessRightsFormData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $companiesByGroup = "";
        if (\Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }
        $groupCompany = Company::whereIN("companySystemID", $companiesByGroup)->get();
        $department = DepartmentMaster::where('showInCombo', -1)->get();
        $employeesDepartment = array('company' => $groupCompany, 'approvalGroup' => ApprovalGroups::all(), 'department' => $department);
        return $this->sendResponse($employeesDepartment, 'Employees Department retrieved successfully');
    }

    public function getDepartmentDocument(Request $request)
    {
        $document = DocumentMaster::where('departmentSystemID', $request['departmentSystemID'])->get();
        if (empty($document)) {
            return $this->sendError('Document not found');
        }
        return $this->sendResponse($document, 'Document retrieved successfully');
    }

    function deleteAllAccessRights(Request $request)
    {
        $input = $request->all();

        $employeesDepartment = EmployeesDepartment::where('employeeSystemID', $request->employeeSystemID);
        if (array_key_exists('companySystemID', $input)) {
            if ($input['companySystemID'] > 0) {
                $employeesDepartment->where('companySystemID', $request->companySystemID);
            }
        }
        if (array_key_exists('documentSystemID', $input)) {
            if ($input['documentSystemID'] > 0) {
                $employeesDepartment->where('documentSystemID', $request->documentSystemID);
            }
        }
        if (array_key_exists('departmentSystemID', $input)) {
            if ($input['departmentSystemID'] > 0) {
                $employeesDepartment->where('departmentSystemID', $request->departmentSystemID);
            }
        }
        if (array_key_exists('servicelineSystemID', $input)) {
            if ($input['servicelineSystemID'] > 0) {
                $employeesDepartment->where('servicelineSystemID', $request->servicelineSystemID);
            }
        }
        if (array_key_exists('approvalGroupID', $input)) {
            if ($input['approvalGroupID'] > 0) {
                $employeesDepartment->where('approvalGroupID', $request->approvalGroupID);
            }
        }
        $employeesDepartment->delete();
        return $this->sendResponse(array(), 'Employees Department deleted successfully');
    }


    public function mirrorAccessRights(Request $request)
    {
        $input = $request->all();

        $mirrorEmployeeIDs = $input['mirrorEmployeeID']['employeeSystemID'];

        $existingData = $this->getExistingApprovalAccessRights($input);

        $finalData = $this->copyApprovalAccessRights($existingData, $mirrorEmployeeIDs);
        $employeesDepartments = [];
        foreach ($finalData as $key => $value) {
            $checkIsExisits = EmployeesDepartment::where('companySystemID', $value['companySystemID'])
                                                ->where('employeeSystemID', $value['employeeSystemID'])
                                                ->where('documentSystemID', $value['documentSystemID'])
                                                ->where('departmentSystemID', $value['departmentSystemID'])
                                                ->where('ServiceLineSystemID', $value['ServiceLineSystemID'])
                                                ->where('employeeGroupID', $value['employeeGroupID'])->first();
            if (is_null($checkIsExisits)) {
                 $employeesDepartments[] = EmployeesDepartment::insert($value);
            }
        }
        return $this->sendResponse($employeesDepartments, 'Employees Department saved successfully');
    }

    public function getExistingApprovalAccessRights($input)
    {
        $employeesDepartment = EmployeesDepartment::with(['company', 'department', 'serviceline', 'document', 'approvalgroup'])->where('employeeSystemID', $input['existingEmployeeID'])->selectRaw('*,false as selected');

        if (array_key_exists('companySystemID', $input)) {
            if(is_array($input['companySystemID'])){
                $input['companySystemID'] = $input['companySystemID'][0];
            }
            if ($input['companySystemID'] > 0) {
                $employeesDepartment->whereHas('company', function ($q) use ($input) {
                    $q->where('companySystemID', $input['companySystemID']);
                });
            }else {
                if (!\Helper::checkIsCompanyGroup($input['globalCompanyId'])) {
                    $employeesDepartment->where('companySystemID',$input['globalCompanyId']);
                }else{
                    $companiesByGroup = \Helper::getGroupCompany($input['globalCompanyId']);
                    $employeesDepartment->whereIN('companySystemID',$companiesByGroup);
                }
            }
        }else{
            if (!\Helper::checkIsCompanyGroup($input['globalCompanyId'])) {
                $employeesDepartment->where('companySystemID',$input['globalCompanyId']);
            }else{
                $companiesByGroup = \Helper::getGroupCompany($input['globalCompanyId']);
                $employeesDepartment->whereIN('companySystemID',$companiesByGroup);
            }
        }
        if (array_key_exists('documentSystemID', $input)) {
            if ($input['documentSystemID'] > 0) {
                $employeesDepartment->whereHas('document', function ($q) use ($input) {
                    $q->where('documentSystemID', $input['documentSystemID']);
                });
            }
        }
        if (array_key_exists('departmentSystemID', $input)) {
            if ($input['departmentSystemID'] > 0) {
                $employeesDepartment->whereHas('department', function ($q) use ($input) {
                    $q->where('departmentSystemID', $input['departmentSystemID']);
                });
            }
        }
        if (array_key_exists('servicelineSystemID', $input)) {
            if ($input['servicelineSystemID'] > 0) {
                $employeesDepartment->whereHas('serviceline', function ($q) use ($input) {
                    $q->where('servicelineSystemID', $input['servicelineSystemID']);
                });
            }
        }
        if (array_key_exists('approvalGroupID', $input)) {
            if ($input['approvalGroupID'] > 0) {
                $employeesDepartment->whereHas('approvalgroup', function ($q) use ($input) {
                    $q->where('employeeGroupID', $input['approvalGroupID']);
                });
            }
        }

        $existingData = $employeesDepartment->get()->toArray();

        return $existingData;
    }

    public function copyApprovalAccessRights($existingData, $mirrorEmployeeIDs)
    {
        $finalData = [];
        foreach ($existingData as $key => $value) {
            foreach ($mirrorEmployeeIDs as $ke => $val) {
                $temp['employeeSystemID'] = $val['employeeSystemID'];
                $temp['employeeID'] = $val['empID'];
                $temp['employeeGroupID'] = $value['employeeGroupID'];
                $temp['companySystemID'] = $value['companySystemID'];
                $temp['companyId'] = $value['companyId'];
                $temp['documentSystemID'] = $value['documentSystemID'];
                $temp['documentID'] = $value['documentID'];
                $temp['departmentSystemID'] = $value['departmentSystemID'];
                $temp['departmentID'] = $value['departmentID'];
                $temp['ServiceLineSystemID'] = $value['ServiceLineSystemID'];
                $temp['ServiceLineID'] = $value['ServiceLineID'];
                $temp['warehouseSystemCode'] = $value['warehouseSystemCode'];
                $temp['reportingManagerID'] = $value['reportingManagerID'];
                $temp['isDefault'] = $value['isDefault'];
                $temp['dischargedYN'] = $value['dischargedYN'];
                $temp['approvalDeligated'] = $value['approvalDeligated'];
                $temp['approvalDeligatedFromEmpID'] = $value['approvalDeligatedFromEmpID'];
                $temp['approvalDeligatedFrom'] = $value['approvalDeligatedFrom'];
                $temp['approvalDeligatedTo'] = $value['approvalDeligatedTo'];
                $temp['dmsIsUploadEnable'] = $value['dmsIsUploadEnable'];

                $finalData[] = $temp;
            }
        }

        return $finalData;
    }
}
