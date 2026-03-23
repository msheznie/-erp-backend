<?php
/**
 * =============================================
 * -- File Name : CompanyDocumentAttachmentAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Company Document Attachment
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file contains the all CRUD for  Company Document Attachment
 * -- REVISION HISTORY
 * -- functions : getAllCompanyDocumentAttachment() - created by Rilwan 2019-09-17
 * -- functions : getCompanyPolicyFilterOptions() - created by Rilwan 2019-09-18
 * -- functions : checkDocumentAttachmentPolicy() - created by Rilwan 2020-06-30
 */
namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateCompanyDocumentAttachmentAPIRequest;
use App\Http\Requests\API\UpdateCompanyDocumentAttachmentAPIRequest;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\DocumentMaster;
use App\Models\DocumentAccessRole;
use App\Models\DocumentAccessEmployee;
use App\Models\Employee;
use App\Repositories\CompanyDocumentAttachmentRepository;
use App\Services\CompanyDocumentAttachmentService;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Arr;

/**
 * Class CompanyDocumentAttachmentController
 * @package App\Http\Controllers\API
 */

class CompanyDocumentAttachmentAPIController extends AppBaseController
{
    /** @var  CompanyDocumentAttachmentRepository */
    private $companyDocumentAttachmentRepository;

    /** @var  CompanyDocumentAttachmentService */
    private $companyDocumentAttachmentService;

    public function __construct(
        CompanyDocumentAttachmentRepository $companyDocumentAttachmentRepo,
        CompanyDocumentAttachmentService $companyDocumentAttachmentService
    ) {
        $this->companyDocumentAttachmentRepository = $companyDocumentAttachmentRepo;
        $this->companyDocumentAttachmentService = $companyDocumentAttachmentService;
    }

    /**
     * Display a listing of the CompanyDocumentAttachment.
     * GET|HEAD /companyDocumentAttachments
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->companyDocumentAttachmentRepository->pushCriteria(new RequestCriteria($request));
        $this->companyDocumentAttachmentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $companyDocumentAttachments = $this->companyDocumentAttachmentRepository->all();

        return $this->sendResponse($companyDocumentAttachments->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.company_document_attachments')]));
    }

    /**
     * Store a newly created CompanyDocumentAttachment in storage.
     * POST /companyDocumentAttachments
     *
     * @param CreateCompanyDocumentAttachmentAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCompanyDocumentAttachmentAPIRequest $request)
    {
        $input = $request->all();

        $companyDocumentAttachments = $this->companyDocumentAttachmentRepository->create($input);

        return $this->sendResponse($companyDocumentAttachments->toArray(), trans('custom.save', ['attribute' => trans('custom.company_document_attachments')]));
    }

    /**
     * Display the specified CompanyDocumentAttachment.
     * GET|HEAD /companyDocumentAttachments/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CompanyDocumentAttachment $companyDocumentAttachment */
        $companyDocumentAttachment = $this->companyDocumentAttachmentRepository->findWithoutFail($id);

        if (empty($companyDocumentAttachment)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company_document_attachments')]));
        }

        return $this->sendResponse($companyDocumentAttachment->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.company_document_attachments')]));
    }

    /**
     * Update the specified CompanyDocumentAttachment in storage.
     * PUT/PATCH /companyDocumentAttachments/{id}
     *
     * @param  int $id
     * @param UpdateCompanyDocumentAttachmentAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCompanyDocumentAttachmentAPIRequest $request)
    {
        $input = $request->all();

        $input = Arr::except($input, ['companySystemID','companyID','documentSystemID','documentID','timeStamp','company','document','access']);

        $input = $this->convertArrayToValue($input);

        $companyDocumentAttachment = $this->companyDocumentAttachmentRepository->findWithoutFail($id);

        if (empty($companyDocumentAttachment)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company_document_attachments')]));
        }

        $result = $this->companyDocumentAttachmentService->validateAndNormalizeGrvApprovalUpdate($companyDocumentAttachment, $input);
        if (!$result['valid']) {
            return $this->sendAPIError($result['message'], $result['status'], $result['errors']);
        }
        $input = $result['input'];

        $approvalResult = $this->companyDocumentAttachmentService->validateApprovalConfigChange($companyDocumentAttachment, $input);
        if (!$approvalResult['allowed']) {
            return $this->sendError($approvalResult['message'], $approvalResult['status']);
        }

        $companyDocumentAttachment = $this->companyDocumentAttachmentRepository->update($input, $id);

        return $this->sendResponse($companyDocumentAttachment->toArray(), trans('custom.update', ['attribute' => trans('custom.company_document_attachments')]));
    }

    /**
     * Remove the specified CompanyDocumentAttachment from storage.
     * DELETE /companyDocumentAttachments/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CompanyDocumentAttachment $companyDocumentAttachment */
        $companyDocumentAttachment = $this->companyDocumentAttachmentRepository->findWithoutFail($id);

        if (empty($companyDocumentAttachment)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company_document_attachments')]));
        }

        $companyDocumentAttachment->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.company_document_attachments')]));
    }


    /**
     * Get company document attachment data for list
     * @param Request $request
     * @return mixed
     */
    public function getAllCompanyDocumentAttachment(Request $request){

        $input = $request->all();
        $search = $request->input('search.value');

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companySystemID'];

        $isGroup = Helper::checkIsCompanyGroup($companyId);

        if($isGroup){
            $childCompanies = Helper::getGroupCompany($companyId);
        }else{
            $childCompanies = [$companyId];
        }

        $companyDocumentAttachment = CompanyDocumentAttachment::whereIn('companySystemID',$childCompanies)->with(['company','document','access']);

        if (array_key_exists('documentSystemID', $input)) {
            $companyDocumentAttachment = $companyDocumentAttachment->where('documentSystemID', $input['documentSystemID']);
        }

        if($search){
            $companyDocumentAttachment = $companyDocumentAttachment
                ->where(function ($query) use($search){
                    $query->whereHas('company', function ($q) use ($search) {
                        $q->where('CompanyName','LIKE',"%{$search}%");
                    })->orWhereHas('document', function ($q) use ($search) {
                        $q->where('documentDescription','LIKE',"%{$search}%");
                    })->orWhere('docRefNumber','LIKE',"%{$search}%");
                });
        }
        return \DataTables::eloquent($companyDocumentAttachment)
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('companyDocumentAttachmentID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /*
     * get company or subcompanies
     * */
    public function getCompanyDocumentFilterOptions(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $isGroup = Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = Helper::getGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }
        /**  Companies by group  Drop Down */
        $output['companies'] = Company::whereIn("companySystemID",$subCompanies)->get();
        $output['documents'] = DocumentMaster::select('documentSystemID','documentID','documentDescription')->get();
        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    public function checkDocumentAttachmentPolicy(Request $request){

        $input = $request->all();
        $companySystemID = isset($input['companySystemID'])?$input['companySystemID']:0;
        $documentSystemID = isset($input['documentSystemID'])?$input['documentSystemID']:0;

        $result = CompanyDocumentAttachment::where('companySystemID',$companySystemID)
            ->where('documentSystemID',$documentSystemID)
            ->first();

        if(empty($result)){
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.policy')]));
        }

        return $this->sendResponse($result, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }


    private function buildOwnersArray($documentAccessRole = null)
    {
        return [
            'reporting_manager' => [
                'document_view_access' => $documentAccessRole ? ($documentAccessRole->reportingManager_view != 0) : false,
                'create_access_on_behalf' => $documentAccessRole ? ($documentAccessRole->reportingManager_create != 0) : false
            ],
            'hod' => [
                'document_view_access' => $documentAccessRole ? ($documentAccessRole->hod_view != 0) : false,
                'create_access_on_behalf' => $documentAccessRole ? ($documentAccessRole->hod_create != 0) : false
            ],
            'admin' => [
                'document_view_access' => $documentAccessRole ? ($documentAccessRole->admin_view != 0) : false,
                'create_access_on_behalf' => $documentAccessRole ? ($documentAccessRole->admin_create != 0) : false
            ]
        ];
    }

    public function getDocumentAccessRole(Request $request)
    {
        $input = $request->all();
        $documentAttachmentId = $input['document_attachment_id'];
        
        if (empty($documentAttachmentId)) {
            return $this->sendError('Document attachment ID is required');
        }

        $documentAccessRole = DocumentAccessRole::where('document_attachment_id', $documentAttachmentId)
            ->with(['employees.employee'])
            ->first();

        if (empty($documentAccessRole)) {
            $defaultData = [
                'id' => null,
                'document_attachment_id' => $documentAttachmentId,
                'owners' => $this->buildOwnersArray(null),
                'document_view_employees' => [],
                'create_access_employees' => []
            ];
            return $this->sendResponse($defaultData, 'Document access role retrieved successfully');
        }

        $documentViewEmployees = [];
        $createAccessEmployees = [];

        foreach ($documentAccessRole->employees as $emp) {
            if ($emp->employee) {
                $employeeData = [
                    'id' => $emp->id,
                    'employee_id' => $emp->employee_id,
                    'employeeSystemID' => $emp->employee->employeeSystemID,
                    'empID' => $emp->employee->empID,
                    'empFullName' => $emp->employee->empFullName,
                    'empName' => $emp->employee->empName
                ];
                
                $documentAccessType = $emp->document_access_type ?? 0;
                if ($documentAccessType == 1) {
                    $documentViewEmployees[] = $employeeData;
                } elseif ($documentAccessType == 2) {
                    $createAccessEmployees[] = $employeeData;
                }
            }
        }

        $result = [
            'id' => $documentAccessRole->id,
            'document_attachment_id' => $documentAccessRole->document_attachment_id,
            'owners' => $this->buildOwnersArray($documentAccessRole),
            'document_view_employees' => $documentViewEmployees,
            'create_access_employees' => $createAccessEmployees
        ];

        return $this->sendResponse($result, 'Document access role retrieved successfully');
    }

    public function saveDocumentAccessRole(Request $request)
    {
        $input = $request->all();
        $documentAccessRoleId = $input['document_access_role_id'];

        try {
            if (isset($input['employees']) && is_array($input['employees']) && isset($input['document_access_type'])) {
                $documentAccessType = (int)$input['document_access_type'];
                
                $existingEmployeeIds = DocumentAccessEmployee::where('document_access_role_id', $documentAccessRoleId)
                    ->where('document_access_type', $documentAccessType)
                    ->pluck('employee_id')
                    ->toArray();

                foreach ($input['employees'] as $employee) {
                    $employeeId = is_array($employee) ? ($employee['id'] ?? $employee) : ($employee->id ?? $employee);
                    
                    if (!in_array($employeeId, $existingEmployeeIds)) {
                        DocumentAccessEmployee::create([
                            'document_access_role_id' => $documentAccessRoleId,
                            'employee_id' => $employeeId,
                            'document_access_type' => $documentAccessType
                        ]);
                    }
                }
            }
            return $this->sendResponse(true, trans('custom.document_access_role_saved_successfully'));
        } catch (\Exception $e) {
            return $this->sendError(trans('custom.error_saving_document_access_role') . ': ' . $e->getMessage());
        }
    }


    public function updateDocumentAccessRoleToggle(Request $request)
    {
        $input = $request->all();
        $documentAttachmentId =  $input['companyDocumentAttachmentID'];
        $ownerKey = $input['owner_key'];
        $documentViewAccess = filter_var($input['document_view_access'], FILTER_VALIDATE_BOOLEAN);
        $createAccessOnBehalf = filter_var($input['create_access_on_behalf'], FILTER_VALIDATE_BOOLEAN);

        if (empty($documentAttachmentId)) {
            return $this->sendError(trans('custom.document_attachment_id_required'));
        }
        if (empty($ownerKey)) {
            return $this->sendError(trans('custom.owner_key_required'));
        }

        $columnMap = [
            'reporting_manager' => ['view' => 'reportingManager_view', 'create' => 'reportingManager_create'],
            'hod'              => ['view' => 'hod_view', 'create' => 'hod_create'],
            'admin'            => ['view' => 'admin_view', 'create' => 'admin_create'],
        ];
        if (!isset($columnMap[$ownerKey])) {
            return $this->sendError(trans('custom.invalid_owner_key'));
        }

        try {
            $documentAccessRole = DocumentAccessRole::firstOrNew(['document_attachment_id' => $documentAttachmentId]);
            if (!$documentAccessRole->exists) {
                $documentAccessRole->reportingManager_view = 0;
                $documentAccessRole->reportingManager_create = 0;
                $documentAccessRole->hod_view = 0;
                $documentAccessRole->hod_create = 0;
                $documentAccessRole->admin_view = 0;
                $documentAccessRole->admin_create = 0;
            }
            $documentAccessRole->{$columnMap[$ownerKey]['view']} = $documentViewAccess ? 1 : 0;
            $documentAccessRole->{$columnMap[$ownerKey]['create']} = $createAccessOnBehalf ? 1 : 0;
            $documentAccessRole->save();

            return $this->sendResponse($documentAccessRole->toArray(), trans('custom.document_access_role_updated_successfully'));
        } catch (\Exception $e) {
            return $this->sendError(trans('custom.error_updating_document_access_role') . ': ' . $e->getMessage());
        }
    }


    public function deleteDocumentAccessEmployee(Request $request)
    {
        $employeeId = $request->input('id');
        
        if (empty($employeeId)) {
            return $this->sendError('Employee ID is required');
        }

        try {
            $documentAccessEmployee = DocumentAccessEmployee::find($employeeId);
            
            if (empty($documentAccessEmployee)) {
                return $this->sendError('Document access employee not found');
            }

            $documentAccessEmployee->delete();

            return $this->sendResponse([], trans('custom.document_access_employee_deleted_successfully'));
        } catch (\Exception $e) {
            return $this->sendError(trans('custom.error_deleting_document_access_employee') . ': ' . $e->getMessage());
        }
    }

}
