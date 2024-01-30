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
 * -- Date: 16-Apr 2020 By: Zakeeul Description: Added new function updateEmployeeDepartmentActive(),
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
use App\Models\YesNoSelection;
use App\Models\FinanceItemCategoryMaster;
use App\Models\CompanyDocumentAttachment;
use App\Repositories\EmployeesDepartmentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\CreateExcel;
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
            $companySystemID = (is_array($val['companySystemID'])) ? $val['companySystemID'][0] : $val['companySystemID'];
            $documentSystemID = (is_array($val['documentSystemID'])) ? $val['documentSystemID'][0] : $val['documentSystemID'];
            $departmentSystemID = (is_array($val['departmentSystemID'])) ? $val['departmentSystemID'][0] : $val['departmentSystemID'];
            $ServiceLineSystemID = (is_array($val['ServiceLineSystemID'])) ? $val['ServiceLineSystemID'][0] : $val['ServiceLineSystemID'];
            $employeeSystemID = (is_array($val['employeeSystemID'])) ? $val['employeeSystemID'][0] : $val['employeeSystemID'];
            $employeeGroupID = (is_array($val['employeeGroupID'])) ? $val['employeeGroupID'][0] : $val['employeeGroupID'];
            $input[$key]['companySystemID'] = $companySystemID;
            $input[$key]['documentSystemID'] = $documentSystemID;
            $input[$key]['departmentSystemID'] = $departmentSystemID;
            $input[$key]['ServiceLineSystemID'] = $ServiceLineSystemID;
            $input[$key]['employeeSystemID'] = $employeeSystemID;
            $input[$key]['employeeGroupID'] = $employeeGroupID;
            $input[$key]['isActive'] = 1;
            $input[$key]['createdByEmpSystemID'] = \Helper::getEmployeeSystemID();
            $input[$key]['createdDate'] = date("Y-m-d H:m:s");
            if ($companySystemID) {
                $companyID = Company::find($companySystemID);
                $input[$key]['companyId'] = $companyID->CompanyID;
            }
            if ($documentSystemID) {
                $documentID = DocumentMaster::find($documentSystemID);
                $input[$key]['documentID'] = $documentID->documentID;
            }
            if ($departmentSystemID) {
                $departmentID = DepartmentMaster::find($departmentSystemID);
                $input[$key]['departmentID'] = $departmentID->DepartmentID;
            }
            if ($ServiceLineSystemID) {
                $ServiceLineID = SegmentMaster::find($ServiceLineSystemID);
                $input[$key]['ServiceLineID'] = $ServiceLineID->ServiceLineCode;
            }
            if ($employeeSystemID) {
                $employeeID = Employee::find($employeeSystemID);
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

        $employeeData = \Helper::getEmployeeInfo();

        $employeesDepartment->removedYN = 1;
        $employeesDepartment->removedByEmpID = $employeeData->empID;
        $employeesDepartment->removedByEmpSystemID = $employeeData->employeeSystemID;
        $employeesDepartment->removedDate = date("Y-m-d H:m:s");

        if ($employeesDepartment->isActive == 1) {
            $employeesDepartment->isActive = 0;
            $employeesDepartment->activatedByEmpID = $employeeData->empID;
            $employeesDepartment->activatedByEmpSystemID = $employeeData->employeeSystemID;
            $employeesDepartment->activatedDate = date("Y-m-d H:m:s");
        }

        $employeesDepartment->save();

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
            if (is_array($input['companySystemID'])) {
                $input['companySystemID'] = $input['companySystemID'][0];
            }
            if ($input['companySystemID'] > 0) {
                $employeesDepartment->whereHas('company', function ($q) use ($input) {
                    $q->where('companySystemID', $input['companySystemID']);
                });
            } else {
                if (!\Helper::checkIsCompanyGroup($input['globalCompanyId'])) {
                    $employeesDepartment->where('companySystemID', $input['globalCompanyId']);
                } else {
                    $companiesByGroup = \Helper::getGroupCompany($input['globalCompanyId']);
                    $employeesDepartment->whereIN('companySystemID', $companiesByGroup);
                }
            }
        } else {
            if (!\Helper::checkIsCompanyGroup($input['globalCompanyId'])) {
                $employeesDepartment->where('companySystemID', $input['globalCompanyId']);
            } else {
                $companiesByGroup = \Helper::getGroupCompany($input['globalCompanyId']);
                $employeesDepartment->whereIN('companySystemID', $companiesByGroup);
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

        if (array_key_exists('isActive', $input)) {
            $employeesDepartment->where('isActive', $input['isActive']);
        }

        if (array_key_exists('removedYN', $input)) {
            $employeesDepartment->where('removedYN', $input['removedYN']);
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
            $companiesByGroup = (array) $selectedCompanyId;
        }
        $groupCompany = Company::whereIN("companySystemID", $companiesByGroup)->get();
        $department = DepartmentMaster::where('showInCombo', -1)->get();
        $documents = DocumentMaster::all();
        $segments = SegmentMaster::all();
        $categories = FinanceItemCategoryMaster::all();
        $yesNoSelections = YesNoSelection::all();
        $employeesDepartment = array('company' => $groupCompany, 'approvalGroup' => ApprovalGroups::all(), 'department' => $department, 'documents' => $documents, 'segments' => $segments, 'categories' => $categories, 'yesNoSelections' => $yesNoSelections);
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

        if (array_key_exists('isActive', $input)) {
            $employeesDepartment->where('isActive', $input['isActive']);
        }

        if (array_key_exists('removedYN', $input)) {
            $employeesDepartment->where('removedYN', $input['removedYN']);
        }

        $employeeData = \Helper::getEmployeeInfo();
        $employeesDepartment->update(['removedYN' => 1, 'removedByEmpID' => $employeeData->empID, 'removedByEmpSystemID' => $employeeData->employeeSystemID, 'removedDate' => date("Y-m-d H:m:s")]);


        return $this->sendResponse(array(), 'Employees Department deleted successfully');
    }


    function approvalAccessActiveInactiveAll(Request $request)
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

        if (array_key_exists('isActive', $input)) {
            $employeesDepartment->where('isActive', $input['isActive']);
        }

        if (array_key_exists('removedYN', $input)) {
            $employeesDepartment->where('removedYN', $input['removedYN']);
        }

        $employeeData = \Helper::getEmployeeInfo();
        $employeesDepartment->update(['isActive' => $input['type'], 'activatedByEmpID' => $employeeData->empID, 'activatedByEmpSystemID' => $employeeData->employeeSystemID, 'activatedDate' => date("Y-m-d H:m:s")]);


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
            if (is_array($input['companySystemID'])) {
                $input['companySystemID'] = $input['companySystemID'][0];
            }
            if ($input['companySystemID'] > 0) {
                $employeesDepartment->whereHas('company', function ($q) use ($input) {
                    $q->where('companySystemID', $input['companySystemID']);
                });
            } else {
                if (!\Helper::checkIsCompanyGroup($input['globalCompanyId'])) {
                    $employeesDepartment->where('companySystemID', $input['globalCompanyId']);
                } else {
                    $companiesByGroup = \Helper::getGroupCompany($input['globalCompanyId']);
                    $employeesDepartment->whereIN('companySystemID', $companiesByGroup);
                }
            }
        } else {
            if (!\Helper::checkIsCompanyGroup($input['globalCompanyId'])) {
                $employeesDepartment->where('companySystemID', $input['globalCompanyId']);
            } else {
                $companiesByGroup = \Helper::getGroupCompany($input['globalCompanyId']);
                $employeesDepartment->whereIN('companySystemID', $companiesByGroup);
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

        if (array_key_exists('isActive', $input)) {
            $employeesDepartment->where('isActive', $input['isActive']);
        }

        if (array_key_exists('removedYN', $input)) {
            $employeesDepartment->where('removedYN', $input['removedYN']);
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
                $temp['dischargedYN'] = 0;
                $temp['isActive'] = 1;
                $temp['approvalDeligated'] = $value['approvalDeligated'];
                $temp['approvalDeligatedFromEmpID'] = $value['approvalDeligatedFromEmpID'];
                $temp['approvalDeligatedFrom'] = $value['approvalDeligatedFrom'];
                $temp['approvalDeligatedTo'] = $value['approvalDeligatedTo'];
                $temp['dmsIsUploadEnable'] = $value['dmsIsUploadEnable'];
                $temp['createdByEmpSystemID'] = \Helper::getEmployeeSystemID();
                $temp['createdDate'] = date("Y-m-d H:m:s");
                $finalData[] = $temp;
            }
        }

        return $finalData;
    }

    public function updateEmployeeDepartmentActive(Request $request)
    {
        $input = $request->all();

        $employeesDepartment = $this->employeesDepartmentRepository->findWithoutFail($input['employeesDepartmentsID']);
        if (empty($employeesDepartment)) {
            return $this->sendError('Employees Department not found');
        }

        $employeeData = \Helper::getEmployeeInfo();

        $employeesDepartment->isActive = ($employeesDepartment->isActive == 1) ? 0 : 1;
        $employeesDepartment->activatedByEmpID = $employeeData->empID;
        $employeesDepartment->activatedByEmpSystemID = $employeeData->employeeSystemID;
        $employeesDepartment->activatedDate = date("Y-m-d H:m:s");

        $employeesDepartment->save();

        return $this->sendResponse($input['employeesDepartmentsID'], 'Employees updated successfully');
    }

    public function approvalMatrixReport(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $approvalMatrix = $this->approvalMatrixData($input);

        $search = $request->input('search.value');

        return \DataTables::of($approvalMatrix)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }


    public function approvalMatrixData($input)
    {
        $companySystemID = isset($input['companySystemID']) ? $input['companySystemID'] : 0;
        $selectedDepartments = (isset($input['selectedDepartments']) && !empty($input['selectedDepartments'])) ? collect($input['selectedDepartments'])->pluck('departmentSystemID')->toArray() : [];

        $selectedDocuments = (isset($input['selectedDocuments']) && !empty($input['selectedDocuments'])) ? collect($input['selectedDocuments'])->pluck('documentSystemID')->toArray() : [];

        $selectedEmployees = (isset($input['selectedEmployees']) && !empty($input['selectedEmployees'])) ? collect($input['selectedEmployees'])->pluck('employeeSystemID')->toArray() : [];

        $selectedCategories = (isset($input['selectedCategories']) && !empty($input['selectedCategories'])) ? collect($input['selectedCategories'])->pluck('itemCategoryID')->toArray() : [];

        $selectedSegments = (isset($input['selectedSegments']) && !empty($input['selectedSegments'])) ? collect($input['selectedSegments'])->pluck('serviceLineSystemID')->toArray() : [];

        // $statusID = (isset($input['statusID']) && is_array($input['statusID'])) ? $input['statusID'][0] : $input['statusID'];
        if (isset($input['statusID'])) {
            if (is_array($input['statusID'])) {
                $statusID = isset($input['statusID'][0]) ? $input['statusID'][0] : 1;
            } else {
                $statusID = $input['statusID'];
            }
        } else {
            $statusID = 1;
        }

        $levelStatusID = (isset($input['levelStatusID']) && is_array($input['levelStatusID'])) ? $input['levelStatusID'][0] : isset($input['levelStatusID'])?$input['levelStatusID']:1;

        $approvalMatrixData = DepartmentMaster::select('DepartmentDescription', 'departmentSystemID')
            ->with(['documents' => function ($query1) use ($companySystemID, $selectedDocuments, $selectedEmployees, $selectedSegments, $selectedCategories, $statusID, $levelStatusID) {
                $query1->select('documentDescription', 'documentSystemID', 'departmentSystemID')
                    ->with(['approval_levels' => function ($query3) use ($companySystemID, $selectedEmployees, $selectedSegments, $selectedCategories, $statusID, $levelStatusID) {
                        $query3->select('levelDescription', 'approvalLevelID', 'documentSystemID', 'valueWise', 'valueFrom', 'valueTo', 'categoryID', 'serviceLineSystemID', 'isActive')
                            ->with(['approvalrole' => function ($query5) use ($companySystemID, $selectedEmployees, $statusID) {
                                $query5
                                    ->with(['approval_group' => function ($query6) use ($companySystemID, $selectedEmployees, $statusID) {
                                        $query6->with(['employee_department' => function ($query7) use ($companySystemID, $selectedEmployees, $statusID) {
                                            $query7->select('employeeGroupID', 'employeeSystemID', 'isActive', 'removedYN', 'employeesDepartmentsID', 'ServiceLineSystemID')
                                                ->with(['employee' => function ($query) {
                                                    $query->select('employeeSystemID', 'empName');
                                                }, 'serviceline'])
                                                ->when(!empty($selectedEmployees), function ($query) use ($selectedEmployees) {
                                                    $query->whereIN('employeeSystemID', $selectedEmployees);
                                                })
                                                ->when($statusID == 1, function ($query) {
                                                    $query->where('isActive', 1)
                                                        ->where('removedYN', 0);
                                                })
                                                ->when($statusID == 2, function ($query) {
                                                    $query->where('isActive', 0)
                                                        ->where('removedYN', 0);
                                                })
                                                ->when($statusID == 3, function ($query) {
                                                    $query->where('removedYN', 1);
                                                })
                                                ->where('companySystemID', $companySystemID);
                                        }])
                                            ->when(!empty($selectedEmployees), function ($query) use ($selectedEmployees, $companySystemID, $statusID) {
                                                $query->whereHas('employee_department', function ($query) use ($selectedEmployees, $companySystemID, $statusID) {
                                                    $query->whereIN('employeeSystemID', $selectedEmployees)
                                                        ->where('companySystemID', $companySystemID)
                                                        ->when($statusID == 1, function ($query) {
                                                            $query->where('isActive', 1)
                                                                ->where('removedYN', 0);
                                                        })
                                                        ->when($statusID == 2, function ($query) {
                                                            $query->where('isActive', 0)
                                                                ->where('removedYN', 0);
                                                        })
                                                        ->when($statusID == 3, function ($query) {
                                                            $query->where('removedYN', 1);
                                                        });
                                                });
                                            });
                                    }])
                                    ->where('companySystemID', $companySystemID)
                                    ->when(!empty($selectedEmployees), function ($query) use ($selectedEmployees, $companySystemID, $statusID) {
                                        $query->whereHas('approval_group', function ($query) use ($selectedEmployees, $companySystemID, $statusID) {
                                            $query->whereHas('employee_department', function ($query) use ($selectedEmployees, $companySystemID, $statusID) {
                                                $query->whereIN('employeeSystemID', $selectedEmployees)
                                                    ->where('companySystemID', $companySystemID)
                                                    ->when($statusID == 1, function ($query) {
                                                        $query->where('isActive', 1)
                                                            ->where('removedYN', 0);
                                                    })
                                                    ->when($statusID == 2, function ($query) {
                                                        $query->where('isActive', 0)
                                                            ->where('removedYN', 0);
                                                    })
                                                    ->when($statusID == 3, function ($query) {
                                                        $query->where('removedYN', 1);
                                                    });
                                            });
                                        });
                                    });
                            }, 'serviceline', 'category'])
                            ->where('companySystemID', $companySystemID)
                            ->when(!empty($selectedSegments), function ($query) use ($selectedSegments) {
                                $query->whereIN('serviceLineSystemID', $selectedSegments);
                            })
                            ->when(!empty($selectedCategories), function ($query) use ($selectedCategories) {
                                $query->whereIN('categoryID', $selectedCategories);
                            })
                            ->when($levelStatusID == 1, function ($query) {
                                $query->where('isActive', -1);
                            })
                            ->when($levelStatusID == 2, function ($query) {
                                $query->where('isActive', 0);
                            })
                            ->whereHas('approvalrole', function ($query8) use ($companySystemID, $selectedEmployees, $statusID) {
                                $query8->where('companySystemID', $companySystemID)
                                    ->when(!empty($selectedEmployees), function ($query) use ($selectedEmployees, $companySystemID, $statusID) {
                                        $query->whereHas('approval_group', function ($query) use ($selectedEmployees, $companySystemID, $statusID) {
                                            $query->whereHas('employee_department', function ($query) use ($selectedEmployees, $companySystemID, $statusID) {
                                                $query->whereIN('employeeSystemID', $selectedEmployees)
                                                    ->where('companySystemID', $companySystemID)
                                                    ->when($statusID == 1, function ($query) {
                                                        $query->where('isActive', 1)
                                                            ->where('removedYN', 0);
                                                    })
                                                    ->when($statusID == 2, function ($query) {
                                                        $query->where('isActive', 0)
                                                            ->where('removedYN', 0);
                                                    })
                                                    ->when($statusID == 3, function ($query) {
                                                        $query->where('removedYN', 1);
                                                    });
                                            });
                                        });
                                    });
                            });
                    }])
                    ->whereIN('documentSystemID', $selectedDocuments)
                    ->whereHas('approval_levels', function ($q1) use ($companySystemID, $selectedEmployees, $selectedSegments, $selectedCategories, $statusID, $levelStatusID) {
                        $q1->where('companySystemID', $companySystemID)
                            ->whereHas('approvalrole', function ($query8) use ($companySystemID, $selectedEmployees, $statusID) {
                                $query8->where('companySystemID', $companySystemID)
                                    ->when(!empty($selectedEmployees), function ($query) use ($selectedEmployees, $companySystemID, $statusID) {
                                        $query->whereHas('approval_group', function ($query) use ($selectedEmployees, $companySystemID, $statusID) {
                                            $query->whereHas('employee_department', function ($query) use ($selectedEmployees, $companySystemID, $statusID) {
                                                $query->whereIN('employeeSystemID', $selectedEmployees)
                                                    ->where('companySystemID', $companySystemID)
                                                    ->when($statusID == 1, function ($query) {
                                                        $query->where('isActive', 1)
                                                            ->where('removedYN', 0);
                                                    })
                                                    ->when($statusID == 2, function ($query) {
                                                        $query->where('isActive', 0)
                                                            ->where('removedYN', 0);
                                                    })
                                                    ->when($statusID == 3, function ($query) {
                                                        $query->where('removedYN', 1);
                                                    });
                                            });
                                        });
                                    });
                            })
                            ->when($levelStatusID == 1, function ($query) {
                                $query->where('isActive', -1);
                            })
                            ->when($levelStatusID == 2, function ($query) {
                                $query->where('isActive', 0);
                            })
                            ->when(!empty($selectedSegments), function ($query) use ($selectedSegments) {
                                $query->whereIN('serviceLineSystemID', $selectedSegments);
                            })
                            ->when(!empty($selectedCategories), function ($query) use ($selectedCategories) {
                                $query->whereIN('categoryID', $selectedCategories);
                            });
                    });
            }])
            ->whereIN('departmentSystemID', $selectedDepartments)
            ->whereHas('documents', function ($query2) use ($companySystemID, $selectedDocuments, $selectedEmployees, $selectedSegments, $selectedCategories, $statusID, $levelStatusID) {
                $query2->whereHas('approval_levels', function ($query4) use ($companySystemID, $selectedEmployees, $selectedSegments, $selectedCategories, $statusID, $levelStatusID) {
                    $query4->whereHas('approvalrole', function ($query9) use ($companySystemID, $selectedEmployees, $statusID) {
                        $query9->where('companySystemID', $companySystemID)
                            ->when(!empty($selectedEmployees), function ($query) use ($selectedEmployees, $companySystemID, $statusID) {
                                $query->whereHas('approval_group', function ($query) use ($selectedEmployees, $companySystemID, $statusID) {
                                    $query->whereHas('employee_department', function ($query) use ($selectedEmployees, $companySystemID, $statusID) {
                                        $query->whereIN('employeeSystemID', $selectedEmployees)
                                            ->where('companySystemID', $companySystemID)
                                            ->when($statusID == 1, function ($query) {
                                                $query->where('isActive', 1)
                                                    ->where('removedYN', 0);
                                            })
                                            ->when($statusID == 2, function ($query) {
                                                $query->where('isActive', 0)
                                                    ->where('removedYN', 0);
                                            })
                                            ->when($statusID == 3, function ($query) {
                                                $query->where('removedYN', 1);
                                            });
                                    });
                                });
                            });
                    })
                        ->where('companySystemID', $companySystemID)
                        ->when($levelStatusID == 1, function ($query) {
                            $query->where('isActive', -1);
                        })
                        ->when($levelStatusID == 2, function ($query) {
                            $query->where('isActive', 0);
                        })
                        ->when(!empty($selectedSegments), function ($query) use ($selectedSegments) {
                            $query->whereIN('serviceLineSystemID', $selectedSegments);
                        })
                        ->when(!empty($selectedCategories), function ($query) use ($selectedCategories) {
                            $query->whereIN('categoryID', $selectedCategories);
                        });
                })
                    ->whereIN('documentSystemID', $selectedDocuments);
            })->get();

        if (!empty($approvalMatrixData)) {
            $approvalMatrixData = $approvalMatrixData->toArray();
        }

       
        foreach ($approvalMatrixData as $key => $department) {
            foreach ($department['documents'] as $key1 => $document) {
                foreach ($document['approval_levels'] as $key2 => $approval_level) {
                    foreach ($approval_level['approvalrole'] as $key3 => $approvalrole) {
                        if (!is_null($approvalrole['approval_group'])) {
                            foreach ($approvalrole['approval_group']['employee_department'] as $key4 => $employee_department) {
                                if (!is_null($approval_level['serviceLineSystemID']) && !is_null($employee_department['ServiceLineSystemID'])) {
                                    if ($approval_level['serviceLineSystemID'] != $employee_department['ServiceLineSystemID']) {
                                        unset($approvalMatrixData[$key]['documents'][$key1]['approval_levels'][$key2]['approvalrole'][$key3]['approval_group']['employee_department'][$key4]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($approvalMatrixData as $key => $department) {
            foreach ($department['documents'] as $key1 => $document) {
                foreach ($document['approval_levels'] as $key2 => $approval_level) {
                    foreach ($approval_level['approvalrole'] as $key3 => $approvalrole) {
                        if (!is_null($approvalrole['approval_group'])) {
                            if (!empty($approvalrole['approval_group']['employee_department'])) {
                                foreach ($approvalrole['approval_group']['employee_department'] as $key4 => $value4) {
                                    $approvalMatrixData[$key]['documents'][$key1]['approval_levels'][$key2]['approvalrole'][$key3]['approval_group']['employee_department_data'][] = $value4;
                                }
                            } else {
                                $approvalMatrixData[$key]['documents'][$key1]['approval_levels'][$key2]['approvalrole'][$key3]['approval_group']['employee_department_data'] = [];
                            }
                        } else {
                            $approvalMatrixData[$key]['documents'][$key1]['approval_levels'][$key2]['approvalrole'][$key3]['approval_group']['employee_department_data'] = [];
                        }
                    }
                }
            }
        }

        return $approvalMatrixData;
    }

    public function exportApprovalMatrixReport(Request $request)
    {
        $input = $request->all();

        $output = $this->approvalMatrixData($input);
        $type = $request->type;
        $data = [];
        if (!empty($output)) {
            $x = 0;
            foreach ($output as $key => $value) {
                $data[$x]['Department'] = $value['DepartmentDescription'];

                if (!empty($value['documents'])) {
                    $documentCount = 0;
                    foreach ($value['documents'] as $key1 => $document) {

                        if ($documentCount != 0) {
                            $x++;
                            $data[$x]['Department'] = '';
                        }

                        $data[$x]['Document Type'] = $document['documentDescription'];

                        if (!empty($document['approval_levels'])) {
                            $approvalLevelCount = 0;
                            foreach ($document['approval_levels'] as $key2 => $approval_level) {

                                if ($approvalLevelCount != 0) {
                                    $x++;
                                    $data[$x]['Department'] = '';
                                    $data[$x]['Document Type'] = '';
                                }

                                $data[$x]['Segment'] = (is_null($approval_level['serviceline'])) ? "" : $approval_level['serviceline']['ServiceLineDes'];
                                $data[$x]['Limit'] = ($approval_level['valueWise'] == 1) ? $approval_level['valueFrom'] . '-' . $approval_level['valueTo'] : "";
                                $data[$x]['Category'] = (is_null($approval_level['category'])) ? "" : $approval_level['category']['categoryDescription'];

                                $data[$x]['Level Description'] = $approval_level['levelDescription'];
                                $data[$x]['Level Status'] = ($approval_level['isActive'] == -1) ? 'Active' : 'In Active';

                                if (!empty($approval_level['approvalrole'])) {
                                    $approvalRoleCount = 0;
                                    foreach ($approval_level['approvalrole'] as $key3 => $approvalrole) {

                                        if ($approvalRoleCount != 0) {
                                            $x++;
                                            $data[$x]['Department'] = '';
                                            $data[$x]['Document Type'] = '';
                                            $data[$x]['Segment'] = '';
                                            $data[$x]['Limit'] = '';
                                            $data[$x]['Category'] = '';
                                            $data[$x]['Level Description'] = '';
                                            $data[$x]['Level Status'] = '';
                                        }
                                        $data[$x]['Approval Level'] = $approvalrole['rollLevel'];
                                        $data[$x]['Group Description'] = (!is_null($approvalrole['approval_group']) && isset($approvalrole['approval_group']['rightsGroupDes'])) ? $approvalrole['approval_group']['rightsGroupDes'] : "";

                                        if (!is_null($approvalrole['approval_group']) && isset($approvalrole['approval_group']['employee_department_data'])) {
                                            $employeeDepCount = 0;
                                            foreach ($approvalrole['approval_group']['employee_department_data'] as $key3 => $employee_department) {

                                                if ($employeeDepCount != 0) {
                                                    $x++;
                                                    $data[$x]['Department'] = '';
                                                    $data[$x]['Document Type'] = '';
                                                    $data[$x]['Segment'] = '';
                                                    $data[$x]['Limit'] = '';
                                                    $data[$x]['Category'] = '';
                                                    $data[$x]['Level Description'] = '';
                                                    $data[$x]['Level Status'] = '';
                                                    $data[$x]['Approval Level'] = '';
                                                    $data[$x]['Group Description'] = '';
                                                }

                                                $data[$x]['Approver Name'] = $employee_department['employee']['empName'];

                                                if ($employee_department['removedYN'] == 1) {
                                                    $data[$x]['Approver Status'] = 'Deleted';
                                                } else {
                                                    $data[$x]['Approver Status'] = ($employee_department['isActive'] == 1) ? 'Active' : 'In Active';
                                                }

                                                $employeeDepCount++;
                                                $x++;
                                            }
                                        } else {
                                            $data[$x]['Approver Name'] = '';
                                            $data[$x]['Approver Status'] = '';
                                        }

                                        $approvalRoleCount++;
                                        $x++;
                                    }
                                }
                                $approvalLevelCount++;
                                $x++;
                            }
                        }
                        $documentCount++;
                        $x++;
                    }
                }
                $x++;
            }
        }

        $companyMaster = Company::find(isset($request->companySystemID[0])?$request->companySystemID[0]: null);
        $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
        $detail_array = array(
            'company_code'=>$companyCode,
        );


        $fileName = 'approval_matrix';
        $path = 'approval-setup/approval_matrix/excel/';
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

    public function getApprovalPersonsByRoll(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $employeesDepartment = EmployeesDepartment::with(['company', 'department', 'serviceline', 'document', 'approvalgroup', 'employee'])
                                                  ->selectRaw('*,false as selected');

        $search = $request->input('search.value');

        $rollMasterDetailData = $input['rollMasterDetailData'];


        if (array_key_exists('companySystemID', $rollMasterDetailData)) {
            if (is_array($rollMasterDetailData['companySystemID'])) {
                $rollMasterDetailData['companySystemID'] = $rollMasterDetailData['companySystemID'][0];
            }
            if ($rollMasterDetailData['companySystemID'] > 0) {
                $employeesDepartment->whereHas('company', function ($q) use ($rollMasterDetailData) {
                    $q->where('companySystemID', $rollMasterDetailData['companySystemID']);
                });
            } else {
                if (!\Helper::checkIsCompanyGroup($input['globalCompanyId'])) {
                    $employeesDepartment->where('companySystemID', $input['globalCompanyId']);
                } else {
                    $companiesByGroup = \Helper::getGroupCompany($input['globalCompanyId']);
                    $employeesDepartment->whereIN('companySystemID', $companiesByGroup);
                }
            }
        } else {
            if (!\Helper::checkIsCompanyGroup($input['globalCompanyId'])) {
                $employeesDepartment->where('companySystemID', $input['globalCompanyId']);
            } else {
                $companiesByGroup = \Helper::getGroupCompany($input['globalCompanyId']);
                $employeesDepartment->whereIN('companySystemID', $companiesByGroup);
            }
        }

        if (array_key_exists('approvalGroupID', $input)) {
            if ($input['approvalGroupID'] > 0) {
                $employeesDepartment->whereHas('approvalgroup', function ($q) use ($input) {
                    $q->where('employeeGroupID', $input['approvalGroupID']);
                });
            }
        }

        if (array_key_exists('documentSystemID', $rollMasterDetailData)) {
            if ($rollMasterDetailData['documentSystemID'] > 0) {
                $employeesDepartment->whereHas('document', function ($q) use ($rollMasterDetailData) {
                    $q->where('documentSystemID', $rollMasterDetailData['documentSystemID']);
                });
            }
        }
        if (array_key_exists('departmentSystemID', $rollMasterDetailData)) {
            if ($rollMasterDetailData['departmentSystemID'] > 0) {
                $employeesDepartment->whereHas('department', function ($q) use ($rollMasterDetailData) {
                    $q->where('departmentSystemID', $rollMasterDetailData['departmentSystemID']);
                });
            }
        }

        if (array_key_exists('serviceLineSystemID', $rollMasterDetailData)) {
            if ($rollMasterDetailData['serviceLineSystemID'] > 0) {
                $employeesDepartment->whereHas('serviceline', function ($q) use ($rollMasterDetailData) {
                    $q->where('servicelineSystemID', $rollMasterDetailData['serviceLineSystemID']);
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
                })->orWhereHas('employee', function ($query) use ($search) {
                    $query->where('empName', 'LIKE', "%{$search}%")
                          ->orWhere('empID', 'LIKE', "%{$search}%");
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
            ->make(true);

    }

    public function assignEmployeeToApprovalGroup(Request $request)
    {   
        $input = $request->all();
        
        $saveData = [];
        foreach ($input['selectedEmpIds']['employeeSystemID'] as $key => $val) {
            $employeeGroupID = (is_array($input['rollMasterDetailData']['approvalGroupID'])) ? $input['rollMasterDetailData']['approvalGroupID'][0] : $input['rollMasterDetailData']['approvalGroupID'];

            $checkEmployeeDepartment = EmployeesDepartment::where('employeeSystemID', $val['employeeSystemID'])
                                                          ->where('employeeGroupID', $employeeGroupID)
                                                          ->where('companySystemID', $input['rollMasterDetailData']['companySystemID'])
                                                          ->where('documentSystemID', $input['rollMasterDetailData']['documentSystemID'])
                                                          ->where('removedYN', 0);

            $companyDocument = CompanyDocumentAttachment::where('companySystemID', $input['rollMasterDetailData']['companySystemID'])
                                                        ->where('documentSystemID', $input['rollMasterDetailData']['documentSystemID'])
                                                        ->first();
            if (!empty($companyDocument)) {
                if ($companyDocument['isServiceLineApproval'] == -1) {
                    $checkEmployeeDepartment = $checkEmployeeDepartment->where('ServiceLineSystemID', $input['rollMasterDetailData']['serviceLineSystemID']);
                }
            }
            
            $checkEmployeeDepartment = $checkEmployeeDepartment->first();

            if (!$checkEmployeeDepartment) {
                $saveData[$key]['companySystemID'] = $input['rollMasterDetailData']['companySystemID'];
                $saveData[$key]['documentSystemID'] = $input['rollMasterDetailData']['documentSystemID'];
                $saveData[$key]['departmentSystemID'] = $input['rollMasterDetailData']['departmentSystemID'];
                $saveData[$key]['ServiceLineSystemID'] = $input['rollMasterDetailData']['serviceLineSystemID'];
                $saveData[$key]['employeeSystemID'] = $val['employeeSystemID'];
                $saveData[$key]['employeeGroupID'] = $employeeGroupID;
                $saveData[$key]['createdByEmpSystemID'] = \Helper::getEmployeeSystemID();
                $saveData[$key]['createdDate'] = date("Y-m-d H:m:s");
                if ($input['rollMasterDetailData']['companySystemID']) {
                    $companyID = Company::find($input['rollMasterDetailData']['companySystemID']);
                    $saveData[$key]['companyId'] = $companyID->CompanyID;
                }
                if ($input['rollMasterDetailData']['documentSystemID']) {
                    $documentID = DocumentMaster::find($input['rollMasterDetailData']['documentSystemID']);
                    $saveData[$key]['documentID'] = $documentID->documentID;
                }
                if ($input['rollMasterDetailData']['departmentSystemID']) {
                    $departmentID = DepartmentMaster::find($input['rollMasterDetailData']['departmentSystemID']);
                    $saveData[$key]['departmentID'] = $departmentID->DepartmentID;
                }
                if ($input['rollMasterDetailData']['serviceLineSystemID']) {
                    $ServiceLineID = SegmentMaster::find($input['rollMasterDetailData']['serviceLineSystemID']);
                    $inpsaveDataut[$key]['ServiceLineID'] = $ServiceLineID->ServiceLineCode;
                }
               
                $saveData[$key]['employeeID'] = $val['empID'];
                $saveData[$key]['timeStamp'] = date("Y-m-d H:m:s");

                $employeeData = \Helper::getEmployeeInfo();
                
                $saveData[$key]['isActive'] = 1;
                $saveData[$key]['activatedByEmpID'] = $employeeData->empID;
                $saveData[$key]['activatedByEmpSystemID'] = $employeeData->employeeSystemID;
                $saveData[$key]['activatedDate'] = date("Y-m-d H:m:s");
            }

        }

        $employeesDepartments = EmployeesDepartment::insert($saveData);

        return $this->sendResponse($employeesDepartments, 'Employees Department saved successfully');
    }
}
