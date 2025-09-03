<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDepartmentBudgetPlanningDetailAPIRequest;
use App\Http\Requests\API\UpdateDepartmentBudgetPlanningDetailAPIRequest;
use App\Models\BudgetDetTemplateEntry;
use App\Models\BudgetDetTemplateEntryData;
use App\Models\BudgetPlanningDetailTempAttachment;
use App\Models\BudgetTemplateColumn;
use App\Models\DepartmentBudgetPlanning;
use App\Models\DepartmentBudgetPlanningDetail;
use App\Models\Employee;
use App\Models\FixedAssetMaster;
use App\Models\ItemMaster;
use App\Models\Months;
use App\Models\Unit;
use App\Repositories\DepartmentBudgetPlanningDetailRepository;
use App\Traits\AuditLogsTrait;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class DepartmentBudgetPlanningDetailController
 * @package App\Http\Controllers\API
 */

class DepartmentBudgetPlanningDetailAPIController extends AppBaseController
{
    use AuditLogsTrait;

    /** @var  DepartmentBudgetPlanningDetailRepository */
    private $departmentBudgetPlanningDetailRepository;

    public function __construct(DepartmentBudgetPlanningDetailRepository $departmentBudgetPlanningDetailRepo)
    {
        $this->departmentBudgetPlanningDetailRepository = $departmentBudgetPlanningDetailRepo;
    }

    /**
     * Display a listing of the DepartmentBudgetPlanningDetail.
     * GET|HEAD /departmentBudgetPlanningDetails
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $departmentBudgetPlanningDetails = $this->departmentBudgetPlanningDetailRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($departmentBudgetPlanningDetails->toArray(), 'Department Budget Planning Details retrieved successfully');
    }

    /**
     * Store a newly created DepartmentBudgetPlanningDetail in storage.
     * POST /departmentBudgetPlanningDetails
     *
     * @param CreateDepartmentBudgetPlanningDetailAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateDepartmentBudgetPlanningDetailAPIRequest $request)
    {
        $input = $request->all();

        $departmentBudgetPlanningDetail = $this->departmentBudgetPlanningDetailRepository->create($input);

        return $this->sendResponse($departmentBudgetPlanningDetail->toArray(), 'Department Budget Planning Detail saved successfully');
    }

    /**
     * Display the specified DepartmentBudgetPlanningDetail.
     * GET|HEAD /departmentBudgetPlanningDetails/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var DepartmentBudgetPlanningDetail $departmentBudgetPlanningDetail */
        $departmentBudgetPlanningDetail = $this->departmentBudgetPlanningDetailRepository->find($id);

        if (empty($departmentBudgetPlanningDetail)) {
            return $this->sendError('Department Budget Planning Detail not found');
        }

        return $this->sendResponse($departmentBudgetPlanningDetail->toArray(), 'Department Budget Planning Detail retrieved successfully');
    }

    /**
     * Update the specified DepartmentBudgetPlanningDetail in storage.
     * PUT/PATCH /departmentBudgetPlanningDetails/{id}
     *
     * @param int $id
     * @param UpdateDepartmentBudgetPlanningDetailAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDepartmentBudgetPlanningDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var DepartmentBudgetPlanningDetail $departmentBudgetPlanningDetail */
        $departmentBudgetPlanningDetail = $this->departmentBudgetPlanningDetailRepository->find($id);

        if (empty($departmentBudgetPlanningDetail)) {
            return $this->sendError('Department Budget Planning Detail not found');
        }

        $departmentBudgetPlanningDetail = $this->departmentBudgetPlanningDetailRepository->update($input, $id);

        return $this->sendResponse($departmentBudgetPlanningDetail->toArray(), 'DepartmentBudgetPlanningDetail updated successfully');
    }

    /**
     * Remove the specified DepartmentBudgetPlanningDetail from storage.
     * DELETE /departmentBudgetPlanningDetails/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var DepartmentBudgetPlanningDetail $departmentBudgetPlanningDetail */
        $departmentBudgetPlanningDetail = $this->departmentBudgetPlanningDetailRepository->find($id);

        if (empty($departmentBudgetPlanningDetail)) {
            return $this->sendError('Department Budget Planning Detail not found');
        }

        $departmentBudgetPlanningDetail->delete();

        return $this->sendSuccess('Department Budget Planning Detail deleted successfully');
    }

    /**
     * Get department budget planning details by department planning ID with DataTables support
     *
     * @param Request $request
     * @return Response
     */
    public function getByDepartmentPlanning(Request $request)
    {
        $input = $request->all();

        $departmentPlanningId = $request->input('budgetPlanningId');

        $page     = (int) $request->input('page', 1);   // current page
        $pageSize = (int) $request->input('pageSize', 10); // items per page
        $offset   = ($page - 1) * $pageSize;

        $sort = 'desc';
        if ($request->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        }
        
        if (!$departmentPlanningId) {
            return $this->sendError('Department Planning ID is required');
        }

        try {
            $query = DepartmentBudgetPlanningDetail::with([
                'departmentSegment.segment',
                'budgetTemplateGl.chartOfAccount.templateCategoryDetails',
                'responsiblePerson'
            ])
            ->forDepartmentPlanning($departmentPlanningId)
            ->select([
                'id',
                'department_planning_id',
                'budget_template_id',
                'department_segment_id',
                'budget_template_gl_id',
                'request_amount',
                'responsible_person',
                'responsible_person_type',
                'time_for_submission',
                'previous_year_budget',
                'current_year_budget',
                'difference_last_current_year',
                'amount_given_by_finance',
                'amount_given_by_hod',
                'internal_status',
                'difference_current_request',
                'created_at'
            ])->orderBy('id', $sort);

            $search = $request->input('search');

            // Handle case where search might be an array
            if (is_array($search)) {
                $search = $search['value'] ?? '';
            }

            if ($search && is_string($search)) {
                $search = str_replace("\\", "\\\\", $search);
                $query = $query->where(function ($query) use ($search) {
                    $query->whereHas('budgetTemplateGl', function ($q1) use ($search) {
                        $q1->whereHas('chartOfAccount', function ($q2) use ($search) {
                            $q2->where('controlAccounts', 'LIKE', "%{$search}%")
                                ->orWhere('AccountCode', 'LIKE', "%{$search}%")
                                ->orWhere('AccountDescription', 'LIKE', "%{$search}%");
                        });
                    })
                    ->orWhereHas('departmentSegment', function ($q3) use ($search) {
                        $q3->whereHas('segment', function ($q4) use ($search) {
                            $q4->where('ServiceLineCode', 'LIKE', "%{$search}%")
                                ->orWhere('ServiceLineDes', 'LIKE', "%{$search}%");
                        });
                    });
                });
            }

            $total = $query->count();

            $data = $query->skip($offset)->take($pageSize)->get();

            return response()->json([
                'data' => $data,
                'total' => $total,
                'page' => $page,
                'pageSize' => $pageSize,
                'lastPage' => ceil($total / $pageSize)
            ]);

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving details - ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update internal status of a detail
     *
     * @param Request $request
     * @return Response
     */
    public function updateInternalStatus(Request $request)
    {
        $input = $request->validate([
            'id' => 'required|integer|exists:department_budget_planning_details,id',
            'internal_status' => 'required|integer|in:1,2,3,4'
        ]);

        try {
            $detail = DepartmentBudgetPlanningDetail::find($input['id']);
            $detail->internal_status = $input['internal_status'];
            $detail->save();

            return $this->sendResponse($detail, 'Internal status updated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Error updating status - ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get summary statistics for department planning details
     *
     * @param Request $request
     * @return Response
     */
    public function getSummary(Request $request)
    {
        $departmentPlanningId = $request->input('departmentPlanningId');
        
        if (!$departmentPlanningId) {
            return $this->sendError('Department Planning ID is required');
        }

        try {
            $summary = DepartmentBudgetPlanningDetail::forDepartmentPlanning($departmentPlanningId)
                ->selectRaw('
                    COUNT(*) as total_items,
                    SUM(request_amount) as total_request_amount,
                    SUM(previous_year_budget) as total_previous_year,
                    SUM(current_year_budget) as total_current_year,
                    SUM(amount_given_by_finance) as total_finance_amount,
                    SUM(amount_given_by_hod) as total_hod_amount,
                    SUM(CASE WHEN internal_status = 1 THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN internal_status = 2 THEN 1 ELSE 0 END) as approved_count,
                    SUM(CASE WHEN internal_status = 3 THEN 1 ELSE 0 END) as rejected_count,
                    SUM(CASE WHEN internal_status = 4 THEN 1 ELSE 0 END) as under_review_count
                ')
                ->first();

            return $this->sendResponse($summary, 'Summary retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Error retrieving summary - ' . $e->getMessage(), 500);
        }
    }

    /**
     * Verify budget template configuration before allowing edit
     *
     * @param int $budgetTemplateId
     * @return Response
     */
    public function verifyBudgetTemplateConfiguration($budgetTemplateId)
    {
        try {
            // Get the budget template with its columns and configuration
            $budgetTemplate = \App\Models\BudgetTemplate::with(['columns'])
                ->where('budgetTemplateID', $budgetTemplateId)
                ->first();

            if (!$budgetTemplate) {
                return $this->sendError('Budget template not found or inactive');
            }

            // Check if budget template has columns configured
            $hasColumns = $budgetTemplate->columns && $budgetTemplate->columns->count() > 0;

            // Check if linkRequestAmount is filled
            $hasLinkRequestAmount = !empty($budgetTemplate->linkRequestAmount);

            $verificationData = [
                'hasColumns' => $hasColumns,
                'hasLinkRequestAmount' => $hasLinkRequestAmount
            ];

            return $this->sendResponse($verificationData, 'Budget template configuration verified successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error verifying budget template configuration - ' . $e->getMessage(), 500);
        }
    }

    /**
     * Save budget detail template entries
     *
     * @param Request $request
     * @return Response
     */
    public function saveBudgetDetailTemplateEntries(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'budgetDetailId' => 'required|numeric|exists:department_budget_planning_details,id',
                'data' => 'required|array|min:1',
                'data.*.templateColumnID' => 'required|integer|exists:budget_template_columns,templateColumnID',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $input = $request->all();

            $budgetDetailId = $input['budgetDetailId'];
            $entryID = $input['entryID'] ?? null;
            $data = $input['data'];

            $record = BudgetDetTemplateEntry::where('entryID',$entryID)->first();
            $entryID = null;
            $state = null;

            $newValue = [];
            $oldValue = [];

            if ($record) {
                $state = "update";
                $entryID = $record->entryID;

                $recordData = $record->entryData;
                if (!$recordData->isEmpty()) {
                    $oldValue = BudgetDetTemplateEntryData::with(['templateColumn.preColumn'])->where('entryID',$entryID)->get()->toArray();
                    BudgetDetTemplateEntryData::where('entryID',$entryID)->delete();
                }
            }
            else {
                $state = "insert";
                $entryID = \DB::table('budget_det_template_entries')->insertGetId([
                    'budget_detail_id' => $budgetDetailId,
                    'created_by' => Auth::user()->employee_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            foreach ($data as $columnData) {
                BudgetDetTemplateEntryData::create([
                    'entryID' => $entryID,
                    'templateColumnID' => $columnData['templateColumnID'],
                    'value' => $columnData['value'] ?? '',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            $newValue = BudgetDetTemplateEntryData::with('templateColumn.preColumn')->where('entryID', $entryID)->get();

            // Add audit log
            $uuid = $request->get('tenant_uuid', 'local');
            $db = $request->get('db', '');

            if ($state == "insert") {
                $this->auditLog(
                    $db,
                    $budgetDetailId,
                    $uuid,
                    "department_budget_planning_details_template_data",
                    "Budget planning detail record has been created",
                    "C",
                    $newValue->toArray(),
                    $oldValue
                );
            }
            else {
                $uuid = $request->get('tenant_uuid', 'local');
                $db = $request->get('db', '');
                $this->auditLog(
                    $db,
                    $budgetDetailId,
                    $uuid,
                    "department_budget_planning_details_template_data",
                    "Budget planning detail record has been updated",
                    "U",
                    $newValue->toArray(),
                    $oldValue
                );
            }

            $dataSet = [
                'status' => $state,
                'entryID' => $entryID,
            ];

            return $this->sendResponse($dataSet,'Budget detail template entries saved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error saving template entries - ' . $e->getMessage());
        }
    }

    /**
     * Get budget detail template entries
     *
     * @param int $budgetDetailId
     * @return Response
     */
    public function getBudgetDetailTemplateEntries(Request $request)
    {
        $input = $request->all();

        try {
            // Validate budget detail exists
            $budgetDetail = DepartmentBudgetPlanningDetail::find($input['id']);
            if (!$budgetDetail) {
                return $this->sendError('Budget detail not found');
            }

            // Get entries with their data and template column information using Eloquent
            $entries = BudgetDetTemplateEntry::with([
                'entryData.templateColumn'
            ])
            ->where('budget_detail_id', $input['id'])
            ->orderByEntryID()
            ->get();

            // Group entries by row using Eloquent relationships
            $groupedEntries = [];
            foreach ($entries as $entry) {

                $groupedEntries[$entry->entryID] = [
                    'entryID' => $entry->entryID,
                    'created_by' => $entry->created_by,
                    'created_at' => $entry->created_at,
                    'updated_at' => $entry->updated_at,
                    'entryData' => [],
                    'unitItems' => []
                ];

                $rowData = [];
                $itemData = [];
                $companyId = $input['companyId'];
                foreach ($entry->entryData as $entryData) {
                    $rowData[$entryData->templateColumnID] = $entryData->value;

                    if ($entryData->templateColumn->preColumnID == 5) {


                        switch ($entryData->value)
                        {
                            case 1: // Employee
                                $itemData = Employee::where('empCompanySystemID', $companyId)
                                    ->where('discharegedYN', 0)
                                    ->where('ActivationFlag', -1)
                                    ->where('empLoginActive', 1)
                                    ->where('empActive', 1)
                                    ->select('employeeSystemID as itemCodeSystem', 'empFullName as itemDescription')
                                    ->get();

                                break;

                            case 2: // Fixed Asset
                                $itemData = ItemMaster::where('primaryCompanySystemID',$companyId)->where('isActive', 1)
                                    ->where('financeCategoryMaster',3)
                                    ->select('itemCodeSystem', DB::raw("CONCAT(primaryCode, ' - ', itemDescription) as itemDescription"))
                                    ->get();
                                break;

                            case 3: // Item
                                $itemData = ItemMaster::where('primaryCompanySystemID',$companyId)->where('isActive', 1)
                                    ->where('financeCategoryMaster',1)
                                    ->select('itemCodeSystem', DB::raw("CONCAT(primaryCode, ' - ', itemDescription) as itemDescription"))
                                    ->get();
                                break;

                            case 4: // Service
                                $itemData = ItemMaster::where('primaryCompanySystemID',$companyId)->where('isActive', 1)
                                    ->where('financeCategoryMaster',2)
                                    ->select('itemCodeSystem', DB::raw("CONCAT(primaryCode, ' - ', itemDescription) as itemDescription"))
                                    ->get();
                                break;

                            default:
                                $itemData = [];
                                break;
                        }
                    }
                }
                $groupedEntries[$entry->entryID]['entryData'] = $rowData;
                $groupedEntries[$entry->entryID]['unitItems'] = count($itemData) > 0 ? $itemData->toArray() : [];
            }

            return $this->sendResponse(array_values($groupedEntries), 'Budget detail template entries retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving template entries - ' . $e->getMessage(), 500);
        }
    }

    public function getTemplateDetailFormData(Request $request) {
        $input = $request->all();

        if(!isset($input['budgetPlanID'])) {
            return $this->sendError("Department Budget plan ID is required");
        }

        $budgetPlan = DepartmentBudgetPlanning::find($input['budgetPlanID']);
        if (!$budgetPlan) {
            return $this->sendError("Department Budget plan not found");
        }

        $month = DB::table('erp_months')->get();
        $units = Unit::where('is_active', 1)->get();

        $data = [
            'months' => $month,
            'units' => $units
        ];

        return $this->sendResponse($data, "Template detail form data retrieved successfully");
    }

    public function deleteBudgetPlanningTemplateDetailRow(Request $request) {
        $input = $request->all();

        if(!isset($input['entryID'])) {
            return $this->sendError("Entry id is required");
        }

        $entry = BudgetDetTemplateEntry::where('entryID',$input['entryID'])->first();
        if ($entry) {
            $oldValue = BudgetDetTemplateEntryData::with(['templateColumn.preColumn'])->where('entryID', $entry['entryID'])->get();

            // delete entry data
            BudgetDetTemplateEntryData::where('entryID', $entry['entryID'])->delete();
            // delete entry attachments
            BudgetPlanningDetailTempAttachment::where('entry_id',$entry['entryID'])->delete();

            $budgetDetailId = $entry['budget_detail_id'];

            $entry->delete();

            // Add audit log
            $uuid = $request->get('tenant_uuid', 'local');
            $db = $request->get('db', '');
            $this->auditLog(
                $db,
                $budgetDetailId,
                $uuid,
                "department_budget_planning_details_template_data",
                "Budget planning detail record has been deleted",
                "D",
                [],
                $oldValue->toArray()
            );
        }

        return $this->sendResponse(null,"Template detail row deleted successfully");
    }


    public function getOptionsForSelectedUnit(Request $request)
    {
        $caseID = $request->input('id');
        $companyId = $request->input('companyId');

        $selectedCompanyId = $companyId;
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }


        try {
            switch ($caseID)
            {
                case 1: // Employee
                    $employees = Employee::where('empCompanySystemID', $companyId)
                        ->where('discharegedYN', 0)
                        ->where('ActivationFlag', -1)
                        ->where('empLoginActive', 1)
                        ->where('empActive', 1)
                        ->select('employeeSystemID as itemCodeSystem', 'empFullName as itemDescription')
                        ->get();
                    
                    return $this->sendResponse($employees->toArray(), 'Employees retrieved successfully');
                    break;
                    
                case 2: // Fixed Asset
                    $fixedAssets = ItemMaster::where('primaryCompanySystemID',$subCompanies)->where('isActive', 1)
                        ->where('financeCategoryMaster',3)
                        ->select('itemCodeSystem', DB::raw("CONCAT(primaryCode, ' - ', itemDescription) as itemDescription"))
                        ->get();
                    return $this->sendResponse($fixedAssets->toArray(), 'Fixed Assets retrieved successfully');
                    break;
                    
                case 3: // Item
                    $fixedAssets = ItemMaster::where('primaryCompanySystemID',$subCompanies)->where('isActive', 1)
                        ->where('financeCategoryMaster',1)
                        ->select('itemCodeSystem', DB::raw("CONCAT(primaryCode, ' - ', itemDescription) as itemDescription"))
                        ->get();
                    return $this->sendResponse($fixedAssets->toArray(), 'Items retrieved successfully');
                    break;
                    
                case 4: // Service
                    $fixedAssets = ItemMaster::where('primaryCompanySystemID',$subCompanies)->where('isActive', 1)
                        ->where('financeCategoryMaster',2)
                        ->select('itemCodeSystem', DB::raw("CONCAT(primaryCode, ' - ', itemDescription) as itemDescription"))
                        ->get();
                    return $this->sendResponse($fixedAssets->toArray(), 'Services retrieved successfully');
                    break;

                default:
                    return $this->sendError('Invalid unit type selected');
                    break;
            }
        } catch (\Exception $e) {
            return $this->sendError('Error retrieving options - ' . $e->getMessage(), 500);
        }
    }
}
