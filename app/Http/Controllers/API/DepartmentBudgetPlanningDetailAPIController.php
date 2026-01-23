<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDepartmentBudgetPlanningDetailAPIRequest;
use App\Http\Requests\API\UpdateDepartmentBudgetPlanningDetailAPIRequest;
use App\Models\BudgetDetTemplateEntry;
use App\Models\BudgetDetTemplateEntryData;
use App\Models\BudgetPlanningDetailTempAttachment;
use App\Models\BudgetTemplate;
use App\Models\BudgetTemplateColumn;
use App\Models\BudgetTemplatePreColumn;
use App\Models\CompanyDepartmentEmployee;
use App\Models\CompanyDepartmentSegment;
use App\Models\DepartmentBudgetPlanning;
use App\Models\DepartmentBudgetPlanningDetail;
use App\Models\ChartOfAccount;
use App\Models\Employee;
use App\Models\FixedAssetMaster;
use App\Models\ItemMaster;
use App\Models\Months;
use App\Models\Revision;
use App\Models\SegmentMaster;
use App\Models\CompanyBudgetPlanning;
use App\Models\Unit;
use App\Repositories\DepartmentBudgetPlanningDetailRepository;
use App\Services\ChartOfAccountService;
use App\Traits\AuditLogsTrait;
use App\Models\User;
use App\helper\CreateExcel;
use App\Models\Company;
use Carbon\Carbon;
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
    
    /** @var  ChartOfAccountService */
    private $chartOfAccountService;

    public function __construct(DepartmentBudgetPlanningDetailRepository $departmentBudgetPlanningDetailRepo, ChartOfAccountService $chartOfAccountService)
    {
        $this->departmentBudgetPlanningDetailRepository = $departmentBudgetPlanningDetailRepo;
        $this->chartOfAccountService = $chartOfAccountService;
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

        return $this->sendResponse($departmentBudgetPlanningDetails->toArray(), trans('custom.department_budget_planning_details_retrieved_succe'));
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

        return $this->sendResponse($departmentBudgetPlanningDetail->toArray(), trans('custom.department_budget_planning_detail_saved_successful'));
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
            return $this->sendError(trans('custom.department_budget_planning_detail_not_found'));
        }

        return $this->sendResponse($departmentBudgetPlanningDetail->toArray(), trans('custom.department_budget_planning_detail_retrieved_succes'));
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
            return $this->sendError(trans('custom.department_budget_planning_detail_not_found'));
        }

        $departmentBudgetPlanningDetail = $this->departmentBudgetPlanningDetailRepository->update($input, $id);

        return $this->sendResponse($departmentBudgetPlanningDetail->toArray(), trans('custom.departmentbudgetplanningdetail_updated_successfull'));
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
            return $this->sendError(trans('custom.department_budget_planning_detail_not_found'));
        }

        $departmentBudgetPlanningDetail->delete();

        return $this->sendSuccess(trans('custom.department_budget_planning_detail_deleted_successfully'));
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
            return $this->sendError(trans('custom.department_planning_id_is_required'));
        }

        $employeeID =  \Helper::getEmployeeSystemID();

//        $employeeID = 110;
        $newRequest = new Request();
        $newRequest->replace([
            'companyId' => $request->input('companySystemID'),
            'departmentBudgetPlanningDetailID' => $departmentPlanningId,
            'delegateUser' =>  $employeeID
        ]);
        $controller = app(CompanyBudgetPlanningAPIController::class);
        $userPermission = ($controller->getBudgetPlanningUserPermissions($newRequest))->original;

        try {

            if($request->input('type') != 'company_budget_planning') {
                $delegateIDs = CompanyDepartmentEmployee::where('employeeSystemID',$employeeID)->pluck('departmentEmployeeSystemID')->toArray();
            $query = DepartmentBudgetPlanningDetail::with([
                'departmentSegment.segment',
                'budgetDelegateAccessDetails',
                'budgetDelegateAccessDetailsUser',
                'budgetTemplateGl.chartOfAccount.templateCategoryDetails',
                'responsiblePerson'
            ]);

            if($userPermission['success'] && $userPermission['data']['delegateUser']['status'])
            {
                $query ->whereHas('budgetDelegateAccessDetails' , function ($q)use ($delegateIDs) {
                    $q->whereIn('delegatee_id',$delegateIDs);
                });

            }

            $query->forDepartmentPlanning($departmentPlanningId)
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
                'created_at',
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

            // Handle segment filtering
            $segments = $request->input('segments');
            if (!empty($segments) && is_array($segments)) {
                // Handle both array of objects and array of IDs
                if (isset($segments[0]) && is_array($segments[0]) && isset($segments[0]['id'])) {
                    $segmentIds = array_column($segments, 'id');
                } else {
                    $segmentIds = $segments; // Already an array of IDs
                }
                if (!empty($segmentIds)) {
                    $query->whereHas('departmentSegment', function ($q) use ($segmentIds) {
                        $q->whereHas('segment', function ($q2) use ($segmentIds) {
                            $q2->whereIn('serviceLineSystemID', $segmentIds);
                        });
                    });
                }
            }

            // Handle Parent GL filtering
            $parentGLs = $request->input('parentGLs');
            if (!empty($parentGLs) && is_array($parentGLs)) {
                // Handle both array of strings and array of objects
                $parentGLValues = [];
                foreach ($parentGLs as $gl) {
                    if (is_array($gl) && isset($gl['id'])) {
                        $parentGLValues[] = $gl['id'];
                    } elseif (is_string($gl)) {
                        $parentGLValues[] = $gl;
                    }
                }
                if (!empty($parentGLValues)) {
                    $query->whereHas('budgetTemplateGl', function ($q) use ($parentGLValues) {
                        $q->whereHas('chartOfAccount', function ($q2) use ($parentGLValues) {
                            $q2->whereHas('templateCategoryDetails', function ($q3) use ($parentGLValues) {
                                $q3->whereIn('description', $parentGLValues);
                            });
                        });
                    });
                }
            }

            // Handle GL Description filtering
            $glDescriptions = $request->input('glDescriptions');
            if (!empty($glDescriptions) && is_array($glDescriptions)) {
                // Handle both array of strings and array of objects
                $glDescValues = [];
                foreach ($glDescriptions as $desc) {
                    if (is_array($desc) && isset($desc['id'])) {
                        $glDescValues[] = $desc['id'];
                    } elseif (is_string($desc)) {
                        $glDescValues[] = $desc;
                    }
                }
                if (!empty($glDescValues)) {
                    $query->whereHas('budgetTemplateGl', function ($q) use ($glDescValues) {
                        $q->whereHas('chartOfAccount', function ($q2) use ($glDescValues) {
                            $q2->where(function ($q3) use ($glDescValues) {
                                foreach ($glDescValues as $glDesc) {
                                    // Format: "AccountCode - AccountDescription"
                                    $parts = explode(' - ', $glDesc, 2);
                                    if (count($parts) == 2) {
                                        $q3->orWhere(function ($q4) use ($parts) {
                                            $q4->where('AccountCode', $parts[0])
                                               ->where('AccountDescription', $parts[1]);
                                        });
                                    }
                                }
                            });
                        });
                    });
                }
            }

            // Check if GL-based grouping is requested
            $isGLBased = $request->input('isGLBased', false);
            
            if ($isGLBased) {
                // Group by GL and aggregate amounts
                // Get all data for grouping (without pagination for grouping)
                $allData = $query->get();
                
                // Group by budget_template_gl_id and aggregate
                $groupedData = [];
                foreach ($allData as $item) {
                    $glId = $item->budget_template_gl_id;
                    
                    if (!isset($groupedData[$glId])) {
                        // Use the first item as base and modify it
                        // Create a new model instance to preserve relationships and accessors
                        $groupedItem = $item->replicate();
                        
                        // Reset amount fields to 0 for aggregation
                        $groupedItem->request_amount = 0;
                        $groupedItem->previous_year_budget = 0;
                        $groupedItem->current_year_budget = 0;
                        $groupedItem->difference_last_current_year = 0;
                        $groupedItem->amount_given_by_finance = 0;
                        $groupedItem->amount_given_by_hod = 0;
                        $groupedItem->difference_current_request = 0;
                        
                        // Remove segment for GL-based view
                        $groupedItem->setRelation('departmentSegment', null);
                        $groupedItem->department_segment_id = null;
                        
                        // Ensure relationships are loaded
                        if (!$groupedItem->relationLoaded('budgetTemplateGl')) {
                            $groupedItem->load('budgetTemplateGl.chartOfAccount.templateCategoryDetails');
                        }
                        if (!$groupedItem->relationLoaded('responsiblePerson')) {
                            $groupedItem->load('responsiblePerson');
                        }
                        
                        $groupedData[$glId] = $groupedItem;
                    }
                    
                    // Sum up all amount fields
                    $groupedData[$glId]->request_amount += ($item->request_amount ?? 0);
                    $groupedData[$glId]->previous_year_budget += ($item->previous_year_budget ?? 0);
                    $groupedData[$glId]->current_year_budget += ($item->current_year_budget ?? 0);
                    $groupedData[$glId]->difference_last_current_year += ($item->difference_last_current_year ?? 0);
                    $groupedData[$glId]->amount_given_by_finance += ($item->amount_given_by_finance ?? 0);
                    $groupedData[$glId]->amount_given_by_hod += ($item->amount_given_by_hod ?? 0);
                    $groupedData[$glId]->difference_current_request += ($item->difference_current_request ?? 0);
                }
                
                // Convert to collection and apply pagination
                $groupedCollection = collect(array_values($groupedData));
                $total = $groupedCollection->count();
                $data = $groupedCollection->slice($offset, $pageSize)->values();
            } else {
                $total = $query->count();
                $data = $query->skip($offset)->take($pageSize)->get();
            }

            // Check financeTeamStatus and add isEnable field based on selectedGlSections
            $budgetPlanning = DepartmentBudgetPlanning::with('workflow')->find($departmentPlanningId);
            $selectedGlSections = [];
            $workflowMethod = null;
            
            if ($budgetPlanning) {
                if ($budgetPlanning->workflow) {
                    $workflowMethod = $budgetPlanning->workflow->method;
                }
                $revision = \App\Models\Revision::where('budgetPlanningId', $budgetPlanning->id)
                    ->whereIn('revisionStatus', [1, 2])
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if ($revision && $revision->selectedGlSections) {
                    $selectedGlSections = json_decode($revision->selectedGlSections, true);
                }
            }
            
            $data->transform(function ($item) use ($budgetPlanning, $selectedGlSections, $workflowMethod, $isGLBased) {
                $isEnable = true;
                
                if ($budgetPlanning  && !empty($selectedGlSections)) {
                    if ($workflowMethod == 1 && !$isGLBased) {
                        $isEnable = in_array($item->id, $selectedGlSections);
                    } else {
                        $isEnable = in_array($item->budget_template_gl_id, $selectedGlSections);
                    }
                }
                
                if($budgetPlanning->workStatus == 3 ){
                    $isEnable = false;
                }
                $item->isEnable = $isEnable;
                return $item;
            });
            }
            else {
                $data = [];
                $total = 0;
            }

            

            return response()->json([
                'data' => $data,
                'total' => $total,
                'page' => $page,
                'pageSize' => $pageSize,
                'lastPage' => ceil($total / $pageSize)
            ]);

        } catch (\Exception $e) {
            return $this->sendError(trans('custom.error_retrieving_details') . $e->getMessage(), 500);
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

            return $this->sendResponse($detail, trans('custom.internal_status_updated_successfully'));
        } catch (\Exception $e) {
            return $this->sendError(trans('custom.error_updating_status') . $e->getMessage(), 500);
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
            return $this->sendError(trans('custom.department_planning_id_is_required'));
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

            return $this->sendResponse($summary, trans('custom.summary_retrieved_successfully'));
        } catch (\Exception $e) {
            return $this->sendError(trans('custom.error_retrieving_summary') . $e->getMessage(), 500);
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
                return $this->sendError(trans('custom.budget_template_not_found_or_inactive'));
            }

            // Check if budget template has columns configured
            $hasColumns = $budgetTemplate->columns && $budgetTemplate->columns->count() > 0;

            // Check if linkRequestAmount is filled
            $hasLinkRequestAmount = !empty($budgetTemplate->linkRequestAmount);

            $verificationData = [
                'hasColumns' => $hasColumns,
                'hasLinkRequestAmount' => $hasLinkRequestAmount
            ];

            return $this->sendResponse($verificationData, trans('custom.budget_template_configuration_verified_successfull'));

        } catch (\Exception $e) {
            return $this->sendError(trans('custom.error_verifying_budget_template_configuration') . $e->getMessage(), 500);
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

            $values = collect($data)->pluck('value');

            if ($values->filter()->isEmpty()) {
                return $this->sendError('At least one item must have a non-empty value',500);
            }


            $newRequest = new Request();
            $newRequest->replace([
                'companyId' => $input['companySystemID'],
                'departmentBudgetPlanningDetailID' => $budgetDetailId,
                'delegateUser' =>  \Helper::getEmployeeSystemID()
            ]);
            $controller = app(CompanyBudgetPlanningAPIController::class);
            $userPermission = ($controller->getBudgetPlanningUserPermissions($newRequest))->original;

            if(empty($userPermission) || !$userPermission['success'])
            {
                return $this->sendError('User permissison not exists');
            }


            if(isset($userPermission['data']['delegateUser']) && $userPermission['data']['delegateUser']['status'])
            {
                $delegateUserAccess = $userPermission['data']['delegateUser'];

                if(!empty($delegateUserAccess['access']) && $delegateUserAccess['access']['input'] === false)
                {
                   return  $this->sendError("User doesn't have permission to input data");
                }


                if((!empty($delegateUserAccess['access']) && !$delegateUserAccess['access']['edit_input']) && !empty($entryID))
                {
                    return  $this->sendError("User doesn't have permission to edit data");
                }

            }

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

            $this->updateLinkAmount($budgetDetailId);
            
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
                    "",
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
                    "",
                    "U",
                    $newValue->toArray(),
                    $oldValue
                );
            }

            $dataSet = [
                'status' => $state,
                'entryID' => $entryID,
            ];

            return $this->sendResponse($dataSet,trans('custom.budget_detail_template_entries_saved_successfully'));

        } catch (\Exception $e) {
            return $this->sendError(trans('custom.error_saving_template_entries') . $e->getMessage());
        }
    }

    public function updateLinkAmount($budgetDetailId)
    {

        $budgetDetailData = DepartmentBudgetPlanningDetail::with(['budgetTemplate'])->find($budgetDetailId);

        $budgetTemplateColumn = BudgetTemplateColumn::where('templateColumnID',$budgetDetailData->budgetTemplate->linkRequestAmount)->first();

        $preColumn = BudgetTemplatePreColumn::where('preColumnID',$budgetTemplateColumn->preColumnID)->first();

        switch ($preColumn->columnType)
        {
            case 2: //number;
                $entries = BudgetDetTemplateEntry::where('budget_detail_id',$budgetDetailId)->pluck('entryID')->toArray();

                $total = BudgetDetTemplateEntryData::whereIn('entryID',$entries)->where('templateColumnID',$budgetTemplateColumn->templateColumnID)->sum('value');
                $budgetDetailData->request_amount = $total;
                $budgetDetailData->save();
                break;

            case 4 : // formula;
                $formula = $budgetTemplateColumn->formulaExpression;
                $clean = str_replace(['#', '|'], '', $formula);
                $parts = explode('~', $clean);

                $total = 0;
                $operator = null;
                $values = [];

                $entries = BudgetDetTemplateEntry::where('budget_detail_id',$budgetDetailId)->get();
                foreach ($entries as $entry)
                {
                    $entryTotal = 0;
                    $currentValue = 0;
                    
                    foreach ($parts as $part)
                    {
                        if (in_array($part, ['+', '-', '*', '/'])) {
                            $operator = $part;
                        } else {
                            $row = BudgetDetTemplateEntryData::where('entryID', $entry->entryID)->where('templateColumnID', $part)->first();
                            if ($row) {
                                
                                if(!empty($row->value)) {
                                    $currentValue = $row->value;
                                    if ($entryTotal == 0) {
                                        $entryTotal = $currentValue;
                                    } else {
                                        switch ($operator) {
                                            case '+':
                                                $entryTotal += $currentValue;
                                                break;
                                            case '-':
                                                $entryTotal -= $currentValue;
                                                break;
                                            case '*':
                                                $entryTotal *= $currentValue;
                                                break;
                                            case '/':
                                                if ($currentValue != 0) {
                                                    $entryTotal /= $currentValue;
                                                }
                                                break;
                                        }
                                    }
                                }
                               
                            }
                        }
                    }
                    
                    $total += $entryTotal;
                }


                $budgetDetailData->request_amount = $total;
                $budgetDetailData->save();
                break;
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
                return $this->sendError(trans('custom.budget_detail_not_found'));
            }

            $controller = app(CompanyBudgetPlanningAPIController::class);

            $newRequest = new Request();
            $newRequest->replace([
                'companyId' => $input['companyId'],
                'departmentBudgetPlanningDetailID' => $input['id'],
                'delegateUser' =>  \Helper::getEmployeeSystemID()
            ]);

            $userPermission = ($controller->getBudgetPlanningUserPermissions($newRequest))->original;

            if(empty($userPermission) || !$userPermission['success'])
            {
                return $this->sendError('User permissison not exists');
            }


            if(isset($userPermission['data']['delegateUser']) && $userPermission['data']['delegateUser']['status'])
            {
                $delegateUserAccess = $userPermission['data']['delegateUser'];

                // Get entries with their data and template column information using Eloquent
                $entries = BudgetDetTemplateEntry::with([
                    'entryData.templateColumn'
                ]);

                if(!isset($delegateUserAccess['access']) ||  (isset($delegateUserAccess['access']) && !$delegateUserAccess['access']['show_others_input']))
                {
                    $entries = $entries->where('created_by',\Helper::getEmployeeSystemID());
                }

                $entries = $entries->where('budget_detail_id', $input['id'])
                    ->orderByEntryID()
                    ->get();


            }else {
                // Get entries with their data and template column information using Eloquent
                $entries = BudgetDetTemplateEntry::with([
                    'entryData.templateColumn'
                ])
                    ->where('budget_detail_id', $input['id'])
                    ->orderByEntryID()
                    ->get();
            }


            // Group entries by row using Eloquent relationships
            $groupedEntries = [];
            foreach ($entries as $entry) {

                $groupedEntries[$entry->entryID] = [
                    'entryID' => $entry->entryID,
                    'created_by' => $entry->created_by,
                    'created_at' => $entry->created_at,
                    'updated_at' => $entry->updated_at,
                    'entryData' => [],
                    'unitItems' => [],
                    'edit' => (isset($userPermission['data']['delegateUser']) && $userPermission['data']['delegateUser']['status']) ?
                        (($entry->created_by == \Helper::getEmployeeSystemID() && $userPermission['data']['delegateUser']['isActive']) ? true : (isset($delegateUserAccess['access']['edit_input']) && $delegateUserAccess['access']['edit_input'] && $userPermission['data']['delegateUser']['isActive'] ? true : false)) :
                        true,
                    'delete' => (isset($userPermission['data']['delegateUser']) && $userPermission['data']['delegateUser']['status']) ?
                        (($entry->created_by == \Helper::getEmployeeSystemID() && $userPermission['data']['delegateUser']['isActive']) ? true : (isset($delegateUserAccess['access']['delete_input']) && $delegateUserAccess['access']['delete_input'] && $userPermission['data']['delegateUser']['isActive'] ? true : false)) :
                        true
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

            return $this->sendResponse(array_values($groupedEntries), trans('custom.budget_detail_template_entries_retrieved_successfu'));

        } catch (\Exception $e) {
            return $this->sendError(trans('custom.error_retrieving_template_entries') . $e->getMessage(), 500);
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


            $newRequest = new Request();
            $newRequest->replace([
                'companyId' => $input['companySystemID'],
                'departmentBudgetPlanningDetailID' => $budgetDetailId,
                'delegateUser' =>  \Helper::getEmployeeSystemID()
            ]);
            $controller = app(CompanyBudgetPlanningAPIController::class);
            $userPermission = ($controller->getBudgetPlanningUserPermissions($newRequest))->original;

            if(empty($userPermission) || !$userPermission['success'])
            {
                return $this->sendError('User permissison not exists');
            }


            if(isset($userPermission['data']['delegateUser']) && $userPermission['data']['delegateUser']['status'])
            {
                $delegateUserAccess = $userPermission['data']['delegateUser'];

                if(!empty($delegateUserAccess['access']) && $delegateUserAccess['access']['delete_input'] === false)
                {
                    return  $this->sendError("User doesn't have permission to input data");
                }

            }



            $entry->delete();

            // Add audit log
            $uuid = $request->get('tenant_uuid', 'local');
            $db = $request->get('db', '');
            $this->auditLog(
                $db,
                $budgetDetailId,
                $uuid,
                "department_budget_planning_details_template_data",
                "",
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

    public function updateDepartmentBudgetPlanningDetailAmount(Request $request)
    {
        $employeeID =  \Helper::getEmployeeSystemID();

//        $employeeID = 110;
        $newRequest = new Request();
        $newRequest->replace([
            'companyId' => $request->input('companySystemID'),
            'departmentBudgetPlanningDetailID' => $request->input('departmentSystemID'),
            'delegateUser' =>  $employeeID
        ]);
        $controller = app(CompanyBudgetPlanningAPIController::class);
        $userPermission = ($controller->getBudgetPlanningUserPermissions($newRequest))->original;

        if(!empty($userPermission) && $userPermission['success'])
        {
            if(isset($userPermission['data']['delegateUser']))
            {
                if($userPermission['data']['delegateUser']['status'])
                    return $this->sendError("Delegate User cannot update!",500);
            }

            if(isset($userPermission['data']['financeUser']))
            {
                if($userPermission['data']['financeUser']['status'])
                    return $this->sendError("Finance User cannot update!",500);
            }
            $budgetPlanningDetailId = $request->input('budgetPlanningDetailId');

            $budgetPlanningDetail = DepartmentBudgetPlanningDetail::find($budgetPlanningDetailId);

            $budgetPlanningDetail[$request->input('field')] = (double) $request->input('value');
            $budgetPlanningDetail->save();

            return $this->sendResponse("Amount updated successfully",200);
        }else {
            return $this->sendError("Unable to update amount details",500);
        }
    }

    public function updateFinanceTeamStatus(Request $request)
    {
        $input = $request->validate([
            'budgetPlanningId' => 'required|integer|exists:department_budget_plannings,id',
            'financeTeamStatus' => 'required|integer|in:1,2,3,4'
        ]);
        $input = $request->input();
        try {
            $budgetPlanning = DepartmentBudgetPlanning::with('timeExtensionRequests','revisions')->find($input['budgetPlanningId']);
            if(!isset($budgetPlanning))
                return $this->sendError("Department Budget planning not found!",404);
            
            $currentStatus = $budgetPlanning->financeTeamStatus;
            $newStatus = $input['financeTeamStatus'];

            if (!$this->isValidStatusProgression($currentStatus, $newStatus)) {
                return $this->sendError("Status can only be changed forward.", 422);
            }

            $budgetPlanning->financeTeamStatus = $newStatus;


            $budgetPlanning->save();
            return $this->sendResponse("Finance team status updated",200);

        }catch (\Exception $exception)
        {
            return $this->sendError($exception->getMessage(),500);
        }
    }

    /**
     * Validate if status progression is allowed (forward only)
     * 
     * @param int $currentStatus
     * @param int $newStatus
     * @return bool
     */
    private function isValidStatusProgression($currentStatus, $newStatus)
    {
        // Status flow: 1 (Open) -> 2 (Under Review) -> 3 (Sent Back for Revision) -> 4 (Completed)
        // Special case: From status 3 (Sent Back for Revision), can go back to 2 (Under Review)
        
        // Same status is always valid (no change)
        if ($currentStatus == $newStatus) {
            return true;
        }
        
        // Define valid progressions
        $validProgressions = [
            1 => [2], // From Open: can go to Under Review, Sent Back for Revision, or Completed
            2 => [3,4],    // From Under Review: can go to Sent Back for Revision or Completed
            3 => [4],       // From Sent Back for Revision: can go back to Completed
            4 => []         // From Completed: no further changes allowed
        ];
        
        return in_array($newStatus, $validProgressions[$currentStatus] ?? []);
    }

    public function getChartofAccountsByBudget(Request $request)
    {
        $input = $request->all();
        
        if (!isset($input['budgetPlanningId'])) {
            return $this->sendError('Budget Planning ID is required');
        }
        
        $chartOfAccountSystemIDs = $this->chartOfAccountService->getChartOfAccountsByBudgetPlanning($input['budgetPlanningId']);
        
        return $this->sendResponse($chartOfAccountSystemIDs, 'Chart of accounts retrieved successfully');
    }

    /**
     * Get chart of accounts by revision GL sections
     *
     * @param Request $request
     * @return Response
     */
    public function getChartOfAccountsByRevisionGlSections(Request $request)
    {
        $input = $request->all();
        
        if (!isset($input['selectedGlSections']) || !isset($input['budgetPlanningId'])) {
            return $this->sendError('Selected GL Sections and Budget Planning ID are required');
        }
        
        $selectedGlSections = $input['selectedGlSections'];
        $budgetPlanningId = $input['budgetPlanningId'];
        
        $chartOfAccountSystemIDs = $this->chartOfAccountService->getChartOfAccountsByRevisionGlSections($selectedGlSections, $budgetPlanningId);
        
        return $this->sendResponse($chartOfAccountSystemIDs, 'Chart of accounts retrieved successfully');
    }

    public function getDepartmentBudgetPlanningStatusesByCompany(Request $request)
    {
        $input = $request->all();

        
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }


        $query = DepartmentBudgetPlanning::with('department.hod.employee','revisions','timeExtensionRequests')->where('companyBudgetPlanningID',$input['companyBudgetPlanningId'])->orderBy('id', $sort);;
        return \DataTables::of($query)
            ->addColumn('newDate', function ($row) {
                if ($row->timeExtensionRequests->count() > 0) {
                    $lastTimeExtension = $row->timeExtensionRequests->where('status', 2)->last();
                    return $lastTimeExtension ? $lastTimeExtension->new_time : null;
                } else {
                    return null;
                }
            })
            ->addColumn('submissionDate', function ($row) {
                if ($row->timeExtensionRequests->count() > 0) {
                    $firstTimeExtension = $row->timeExtensionRequests->first();
                    return $firstTimeExtension ? $firstTimeExtension->current_submission_date : $row->submissionDate;
                } else {
                    return $row->submissionDate;
                }
            })
            ->addIndexColumn()
            ->make(true);

    }

    /**
     * Export budget planning details to Excel
     *
     * @param Request $request
     * @return Response
     */
    public function exportBudgetPlanningDetails(Request $request)
    {
        try {
            $input = $request->all();
            $departmentPlanningId = $request->input('budgetPlanningId');

            if (!$departmentPlanningId) {
                return $this->sendError(trans('custom.department_planning_id_is_required'));
            }

            $employeeID = \Helper::getEmployeeSystemID();
            $newRequest = new Request();
            $newRequest->replace([
                'companyId' => $request->input('companySystemID'),
                'departmentBudgetPlanningDetailID' => $departmentPlanningId,
                'delegateUser' => $employeeID
            ]);
            $controller = app(CompanyBudgetPlanningAPIController::class);
            $userPermission = ($controller->getBudgetPlanningUserPermissions($newRequest))->original;

            $query = DepartmentBudgetPlanningDetail::with([
                'departmentSegment.segment',
                'budgetTemplateGl.chartOfAccount.templateCategoryDetails',
                'responsiblePerson'
            ]);

            if ($userPermission['success'] && $userPermission['data']['delegateUser']['status']) {
                $delegateIDs = CompanyDepartmentEmployee::where('employeeSystemID', $employeeID)->pluck('departmentEmployeeSystemID')->toArray();
                $query->whereHas('budgetDelegateAccessDetails', function ($q) use ($delegateIDs) {
                    $q->whereIn('delegatee_id', $delegateIDs);
                });
            }

            $query->forDepartmentPlanning($departmentPlanningId);

            // Apply search filter
            $search = $request->input('search');
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

            // Handle segment filtering
            $segments = $request->input('segments');
            if (!empty($segments) && is_array($segments)) {
                if (isset($segments[0]) && is_array($segments[0]) && isset($segments[0]['id'])) {
                    $segmentIds = array_column($segments, 'id');
                } else {
                    $segmentIds = $segments;
                }
                if (!empty($segmentIds)) {
                    $query->whereHas('departmentSegment', function ($q) use ($segmentIds) {
                        $q->whereHas('segment', function ($q2) use ($segmentIds) {
                            $q2->whereIn('serviceLineSystemID', $segmentIds);
                        });
                    });
                }
            }

            // Handle Parent GL filtering
            $parentGLs = $request->input('parentGLs');
            if (!empty($parentGLs) && is_array($parentGLs)) {
                $parentGLValues = [];
                foreach ($parentGLs as $gl) {
                    if (is_array($gl) && isset($gl['id'])) {
                        $parentGLValues[] = $gl['id'];
                    } elseif (is_string($gl)) {
                        $parentGLValues[] = $gl;
                    }
                }
                if (!empty($parentGLValues)) {
                    $query->whereHas('budgetTemplateGl', function ($q) use ($parentGLValues) {
                        $q->whereHas('chartOfAccount', function ($q2) use ($parentGLValues) {
                            $q2->whereHas('templateCategoryDetails', function ($q3) use ($parentGLValues) {
                                $q3->whereIn('description', $parentGLValues);
                            });
                        });
                    });
                }
            }

            // Handle GL Description filtering
            $glDescriptions = $request->input('glDescriptions');
            if (!empty($glDescriptions) && is_array($glDescriptions)) {
                $glDescValues = [];
                foreach ($glDescriptions as $desc) {
                    if (is_array($desc) && isset($desc['id'])) {
                        $glDescValues[] = $desc['id'];
                    } elseif (is_string($desc)) {
                        $glDescValues[] = $desc;
                    }
                }
                if (!empty($glDescValues)) {
                    $query->whereHas('budgetTemplateGl', function ($q) use ($glDescValues) {
                        $q->whereHas('chartOfAccount', function ($q2) use ($glDescValues) {
                            $q2->where(function ($q3) use ($glDescValues) {
                                foreach ($glDescValues as $glDesc) {
                                    $parts = explode(' - ', $glDesc, 2);
                                    if (count($parts) == 2) {
                                        $q3->orWhere(function ($q4) use ($parts) {
                                            $q4->where('AccountCode', $parts[0])
                                               ->where('AccountDescription', $parts[1]);
                                        });
                                    }
                                }
                            });
                        });
                    });
                }
            }

            $dataset = $query->orderBy('id', 'desc')->get();

            $data = array();
            $x = 0;
            foreach ($dataset as $val) {
                $x++;
                $data[$x]['#'] = $x;
                $data[$x]['Segment'] = $val->departmentSegment && $val->departmentSegment->segment 
                    ? ($val->departmentSegment->segment->ServiceLineCode . ' - ' . $val->departmentSegment->segment->ServiceLineDes) 
                    : '';
                $data[$x]['GL Type'] = $val->budgetTemplateGl && $val->budgetTemplateGl->chartOfAccount 
                    ? $val->budgetTemplateGl->chartOfAccount->controlAccounts 
                    : '';
                $data[$x]['Parent GL'] = $val->budgetTemplateGl && $val->budgetTemplateGl->chartOfAccount && $val->budgetTemplateGl->chartOfAccount->templateCategoryDetails 
                    ? $val->budgetTemplateGl->chartOfAccount->templateCategoryDetails->description 
                    : '';
                $data[$x]['GL Description'] = $val->budgetTemplateGl && $val->budgetTemplateGl->chartOfAccount 
                    ? ($val->budgetTemplateGl->chartOfAccount->AccountCode . ' - ' . $val->budgetTemplateGl->chartOfAccount->AccountDescription) 
                    : '';
                $data[$x]['Responsible Person'] = $val->responsiblePerson 
                    ? $val->responsiblePerson->empName 
                    : '';
                $data[$x]['Request Amount'] = number_format($val->request_amount ?? 0, 2);
                $data[$x]['Time for Submission'] = $val->time_for_submission ? Carbon::parse($val->time_for_submission)->format('d/m/Y') : '';
                $data[$x]['Previous Year Budget'] = number_format($val->previous_year_budget ?? 0, 2);
                $data[$x]['Current Year Budget'] = number_format($val->current_year_budget ?? 0, 2);
                $data[$x]['Amount Given by Finance'] = number_format($val->amount_given_by_finance ?? 0, 2);
                $data[$x]['Amount Given by HOD'] = number_format($val->amount_given_by_hod ?? 0, 2);
                $data[$x]['Internal Status'] = $this->getInternalStatusLabel($val->internal_status ?? 0);
            }

            $companyMaster = Company::find($request->input('companySystemID'));
            $companyCode = $companyMaster->CompanyID ?? 'common';
            $detail_array = array(
                'company_code' => $companyCode,
            );

            $fileName = 'budget_planning_details';
            $path = 'system/budget_planning_details/excel/';
            $type = 'xls';
            $basePath = CreateExcel::process($data, $type, $fileName, $path, $detail_array);

            if ($basePath == '') {
                return $this->sendError('Unable to export excel');
            } else {
                return $this->sendResponse($basePath, trans('custom.success_export'));
            }

        } catch (\Exception $e) {
            return $this->sendError(trans('custom.error_exporting_excel') . ': ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get internal status label
     *
     * @param int $status
     * @return string
     */
    private function getInternalStatusLabel($status)
    {
        switch ($status) {
            case 1: return 'Pending';
            case 2: return 'Approved';
            case 3: return 'Rejected';
            case 4: return 'Under Review';
            default: return 'N/A';
        }
    }
}
