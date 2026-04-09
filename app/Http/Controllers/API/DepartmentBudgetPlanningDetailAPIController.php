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
use App\Models\EmployeesDepartment;
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
use App\Jobs\ExportCompanyBudgetPlanningDetailsJob;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Response;
use App\helper\Helper;
use Illuminate\Support\Facades\Storage;
use App\Exports\CreateExcelExport;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Auth\Access\AuthorizationException;

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

        $employeeID =  Helper::getEmployeeSystemID();

        $newRequest = new Request();
        $newRequest->replace([
            'companyId' => $request->input('companySystemID'),
            'departmentBudgetPlanningDetailID' => $departmentPlanningId,
            'delegateUser' =>  $employeeID
        ]);
        $controller = app(CompanyBudgetPlanningAPIController::class);
        $userPermission = ($controller->getBudgetPlanningUserPermissions($newRequest))->original;

        $allStatusFalse = collect($userPermission['data'])
        ->every(fn($user) => $user['status'] === false);
        
        if($allStatusFalse)
        {
            return response()->json([
                'data' => [],
                'total' => 0,
                'page' => 1,
                'pageSize' => 0,
                'lastPage' => 1
            ]);
        }
        try {
            
            // Check if isCompany parameter is true
            $isCompany = $request->input('isCompany', false);
            $departmentPlanningIds = [$departmentPlanningId]; // Default to single department planning
            
            // If isCompany is true, get all department budget planning IDs for this company budget planning
            if ($isCompany === true || $isCompany === 'true') {
                $companyBudgetPlanning = CompanyBudgetPlanning::with('departmentBudgetPlannings')->find($departmentPlanningId);
                if ($companyBudgetPlanning && $companyBudgetPlanning->departmentBudgetPlannings) {
                    $departmentPlanningIds = $companyBudgetPlanning->departmentBudgetPlannings->pluck('id')->toArray();
                    // If no department budget plannings found, use empty array to return no results
                    if (empty($departmentPlanningIds)) {
                        $departmentPlanningIds = [-1]; // Use -1 to ensure no results
                    }
                }
            }

            // Execute query logic if type is not 'company_budget_planning' OR if isCompany is true
            if($request->input('type') != 'company_budget_planning' || ($isCompany === true || $isCompany === 'true')) {
                $delegateIDs = CompanyDepartmentEmployee::where('employeeSystemID',$employeeID)->pluck('departmentEmployeeSystemID')->toArray();
            $query = DepartmentBudgetPlanningDetail::with([
                'departmentSegment.segment',
                'budgetDelegateAccessDetails',
                'budgetDelegateAccessDetailsUser',
                'budgetTemplateGl.chartOfAccount.templateCategoryDetails',
                'responsiblePerson',
                'departmentBudgetPlanning.department'
            ]);

            // Only apply delegate access filter if type is not 'company_budget_planning'
            if($request->input('type') != 'company_budget_planning' && $userPermission['success'] && $userPermission['data']['delegateUser']['status'])
            {
                $query ->whereHas('budgetDelegateAccessDetails' , function ($q)use ($delegateIDs) {
                    $q->whereIn('delegatee_id',$delegateIDs);
                });


            }

            // Use whereIn for multiple department planning IDs when isCompany is true
            $query->whereIn('department_planning_id', $departmentPlanningIds)
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

            // Get selected status early so we can apply segment/department filters
            // Status 1=Details, 2=Department, 3=Segment, 4=GL Based, 5=Category
            $selectedStatus = (int) $request->input('selectedStatus', 1);

            // Handle segment filtering: apply whenever user selects segments (all report types), so grouped result is filtered by segment
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

            // Handle Department filtering: apply whenever user selects departments in company view (all report types)
            // So e.g. Report Type Segment + Department filter = group by segment, but only include data from selected department(s)
            if ($isCompany === true || $isCompany === 'true') {
                $departments = $request->input('departments');
                if (!empty($departments) && is_array($departments)) {
                    $deptIds = [];
                    foreach ($departments as $d) {
                        if (is_array($d) && isset($d['id'])) {
                            $deptIds[] = $d['id'];
                        } elseif (is_numeric($d)) {
                            $deptIds[] = (int) $d;
                        }
                    }
                    if (!empty($deptIds)) {
                        $query->whereHas('departmentBudgetPlanning', function ($q) use ($deptIds) {
                            $q->whereIn('departmentID', $deptIds);
                        });
                    }
                }
            }

            // Check if GL-based grouping is requested (either from isGLBased or selectedStatus = 4)
            $isGLBased = $request->input('isGLBased', false) || $selectedStatus == 4;
            
            // Get workflow method to check if segment-based grouping is allowed
            $workflowMethod = null;
            if ($isCompany === true || $isCompany === 'true') {
                $companyBudgetPlanning = CompanyBudgetPlanning::with('workflow')->find($departmentPlanningId);
                if ($companyBudgetPlanning && $companyBudgetPlanning->workflow) {
                    $workflowMethod = $companyBudgetPlanning->workflow->method;
                }
            } else {
                $budgetPlanning = DepartmentBudgetPlanning::with('workflow')->find($departmentPlanningId);
                if ($budgetPlanning && $budgetPlanning->workflow) {
                    $workflowMethod = $budgetPlanning->workflow->method;
                }
            }
            if ($selectedStatus == 2) {
                // Department-wise grouping: Group by (Department, GL) and aggregate — one row per department per GL description
                $allData = $query->get();
                $groupedData = [];
                foreach ($allData as $item) {
                    $deptId = $item->departmentBudgetPlanning ? $item->departmentBudgetPlanning->departmentID : null;
                    $key = ($deptId ?? 'unknown') . '_' . ($item->budget_template_gl_id ?? 'unknown');

                    if (!isset($groupedData[$key])) {
                        $groupedItem = $item->replicate();
                        $groupedItem->request_amount = 0;
                        $groupedItem->previous_year_budget = 0;
                        $groupedItem->current_year_budget = 0;
                        $groupedItem->difference_last_current_year = 0;
                        $groupedItem->amount_given_by_finance = 0;
                        $groupedItem->amount_given_by_hod = 0;
                        $groupedItem->difference_current_request = 0;
                        // $groupedItem->setRelation('departmentSegment', null);
                        // $groupedItem->department_segment_id = null;
                        if ($item->departmentBudgetPlanning) {
                            $groupedItem->setRelation('departmentBudgetPlanning', $item->departmentBudgetPlanning);
                            if (!$groupedItem->departmentBudgetPlanning->relationLoaded('department')) {
                                $groupedItem->departmentBudgetPlanning->load('department');
                            }
                        }
                        $groupedData[$key] = $groupedItem;
                    }

                    $groupedData[$key]->request_amount += ($item->request_amount ?? 0);
                    $groupedData[$key]->previous_year_budget += ($item->previous_year_budget ?? 0);
                    $groupedData[$key]->current_year_budget += ($item->current_year_budget ?? 0);
                    $groupedData[$key]->difference_last_current_year += ($item->difference_last_current_year ?? 0);
                    $groupedData[$key]->amount_given_by_finance += ($item->amount_given_by_finance ?? 0);
                    $groupedData[$key]->amount_given_by_hod += ($item->amount_given_by_hod ?? 0);
                    $groupedData[$key]->difference_current_request += ($item->difference_current_request ?? 0);
                }

                $groupedCollection = collect(array_values($groupedData));
                $total = $groupedCollection->count();
                $data = $groupedCollection->slice($offset, $pageSize)->values();
            } elseif ($selectedStatus == 3 && $workflowMethod == 1) {
                // Segment-wise grouping: Group by (Segment, GL) and aggregate — one row per segment per GL description
                $allData = $query->get();
                $groupedData = [];
                foreach ($allData as $item) {
                    $segmentId = null;
                    if ($item->departmentSegment && $item->departmentSegment->segment) {
                        $segmentId = $item->departmentSegment->segment->serviceLineSystemID ?? $item->departmentSegment->segment->id ?? null;
                    }
                    $key = ($segmentId ?? ($item->department_segment_id ?? 'unknown')) . '_' . ($item->budget_template_gl_id ?? 'unknown');

                    if (!isset($groupedData[$key])) {
                        $groupedItem = $item->replicate();
                        $groupedItem->request_amount = 0;
                        $groupedItem->previous_year_budget = 0;
                        $groupedItem->current_year_budget = 0;
                        $groupedItem->difference_last_current_year = 0;
                        $groupedItem->amount_given_by_finance = 0;
                        $groupedItem->amount_given_by_hod = 0;
                        $groupedItem->difference_current_request = 0;
                        // $groupedItem->setRelation('departmentBudgetPlanning', null);
                        if ($item->departmentSegment && $item->departmentSegment->segment) {
                            $groupedItem->setRelation('departmentSegment', $item->departmentSegment);
                            if (!$groupedItem->departmentSegment->relationLoaded('segment')) {
                                $groupedItem->departmentSegment->load('segment');
                            }
                        }
                        $groupedData[$key] = $groupedItem;
                    }

                    $groupedData[$key]->request_amount += ($item->request_amount ?? 0);
                    $groupedData[$key]->previous_year_budget += ($item->previous_year_budget ?? 0);
                    $groupedData[$key]->current_year_budget += ($item->current_year_budget ?? 0);
                    $groupedData[$key]->difference_last_current_year += ($item->difference_last_current_year ?? 0);
                    $groupedData[$key]->amount_given_by_finance += ($item->amount_given_by_finance ?? 0);
                    $groupedData[$key]->amount_given_by_hod += ($item->amount_given_by_hod ?? 0);
                    $groupedData[$key]->difference_current_request += ($item->difference_current_request ?? 0);
                }

                $groupedCollection = collect(array_values($groupedData));
                $total = $groupedCollection->count();
                $data = $groupedCollection->slice($offset, $pageSize)->values();
            } elseif ($isGLBased || $selectedStatus == 4) {
                // GL-based grouping: Group by GL only and aggregate — one row per GL description
                $allData = $query->get();
                $groupedData = [];
                foreach ($allData as $item) {
                    $key = $item->budget_template_gl_id ?? 'unknown';

                    if (!isset($groupedData[$key])) {
                        $groupedItem = $item->replicate();
                        $groupedItem->request_amount = 0;
                        $groupedItem->previous_year_budget = 0;
                        $groupedItem->current_year_budget = 0;
                        $groupedItem->difference_last_current_year = 0;
                        $groupedItem->amount_given_by_finance = 0;
                        $groupedItem->amount_given_by_hod = 0;
                        $groupedItem->difference_current_request = 0;
                        // $groupedItem->setRelation('departmentSegment', null);
                        $groupedItem->department_segment_id = null;
                        // $groupedItem->setRelation('departmentBudgetPlanning', null);
                        if (!$groupedItem->relationLoaded('budgetTemplateGl')) {
                            $groupedItem->load('budgetTemplateGl.chartOfAccount.templateCategoryDetails');
                        }
                        if (!$groupedItem->relationLoaded('responsiblePerson')) {
                            $groupedItem->load('responsiblePerson');
                        }
                        $groupedData[$key] = $groupedItem;
                    }

                    $groupedData[$key]->request_amount += ($item->request_amount ?? 0);
                    $groupedData[$key]->previous_year_budget += ($item->previous_year_budget ?? 0);
                    $groupedData[$key]->current_year_budget += ($item->current_year_budget ?? 0);
                    $groupedData[$key]->difference_last_current_year += ($item->difference_last_current_year ?? 0);
                    $groupedData[$key]->amount_given_by_finance += ($item->amount_given_by_finance ?? 0);
                    $groupedData[$key]->amount_given_by_hod += ($item->amount_given_by_hod ?? 0);
                    $groupedData[$key]->difference_current_request += ($item->difference_current_request ?? 0);
                }

                $groupedCollection = collect(array_values($groupedData));
                $total = $groupedCollection->count();
                $data = $groupedCollection->slice($offset, $pageSize)->values();
            } elseif ($selectedStatus == 5) {
                // Category-wise grouping: Group by (Category, GL) and aggregate — one row per category per GL description
                $allData = $query->get();
                foreach ($allData as $item) {
                    if (!$item->relationLoaded('budgetTemplateGl')) {
                        $item->load('budgetTemplateGl.chartOfAccount.templateCategoryDetails');
                    } elseif ($item->budgetTemplateGl && !$item->budgetTemplateGl->relationLoaded('chartOfAccount')) {
                        $item->budgetTemplateGl->load('chartOfAccount.templateCategoryDetails');
                    } elseif ($item->budgetTemplateGl && $item->budgetTemplateGl->chartOfAccount && !$item->budgetTemplateGl->chartOfAccount->relationLoaded('templateCategoryDetails')) {
                        $item->budgetTemplateGl->chartOfAccount->load('templateCategoryDetails');
                    }
                }

                $groupedData = [];
                foreach ($allData as $item) {
                    $categoryId = null;
                    $categoryDescription = null;
                    if ($item->budgetTemplateGl && $item->budgetTemplateGl->chartOfAccount && $item->budgetTemplateGl->chartOfAccount->templateCategoryDetails) {
                        $categoryId = $item->budgetTemplateGl->chartOfAccount->templateCategoryDetails->id;
                        $categoryDescription = $item->budgetTemplateGl->chartOfAccount->templateCategoryDetails->description ?? null;
                    }
                    $key = ($categoryId ?? ($categoryDescription ?? 'unknown')) . '_' . ($item->budget_template_gl_id ?? 'unknown');

                    if (!isset($groupedData[$key])) {
                        $groupedItem = $item->replicate();
                        $groupedItem->request_amount = 0;
                        $groupedItem->previous_year_budget = 0;
                        $groupedItem->current_year_budget = 0;
                        $groupedItem->difference_last_current_year = 0;
                        $groupedItem->amount_given_by_finance = 0;
                        $groupedItem->amount_given_by_hod = 0;
                        $groupedItem->difference_current_request = 0;
                        // $groupedItem->setRelation('departmentSegment', null);
                        $groupedItem->department_segment_id = null;
                        // $groupedItem->setRelation('departmentBudgetPlanning', null);
                        if ($item->budgetTemplateGl && $item->budgetTemplateGl->chartOfAccount && $item->budgetTemplateGl->chartOfAccount->templateCategoryDetails) {
                            $groupedItem->setRelation('category', $item->budgetTemplateGl->chartOfAccount->templateCategoryDetails);
                        }
                        $groupedData[$key] = $groupedItem;
                    }

                    $groupedData[$key]->request_amount += ($item->request_amount ?? 0);
                    $groupedData[$key]->previous_year_budget += ($item->previous_year_budget ?? 0);
                    $groupedData[$key]->current_year_budget += ($item->current_year_budget ?? 0);
                    $groupedData[$key]->difference_last_current_year += ($item->difference_last_current_year ?? 0);
                    $groupedData[$key]->amount_given_by_finance += ($item->amount_given_by_finance ?? 0);
                    $groupedData[$key]->amount_given_by_hod += ($item->amount_given_by_hod ?? 0);
                    $groupedData[$key]->difference_current_request += ($item->difference_current_request ?? 0);
                }

                $groupedCollection = collect(array_values($groupedData));
                $total = $groupedCollection->count();
                $data = $groupedCollection->slice($offset, $pageSize)->values();
            } else {
                // Status 1: Details (default - show all details, no grouping)
                $total = $query->count();
                $data = $query->skip($offset)->take($pageSize)->get();
            }

            // Check financeTeamStatus and add isEnable field based on selectedGlSections
            $budgetPlanning = null;
            $companyBudgetPlanning = null;
            $selectedGlSections = [];
            $workflowMethod = null;
            $isFinanceApprovalUser = false;
            
            $checkUserHasApprovalAccess = EmployeesDepartment::where('companySystemID', $request->input('companySystemID'))
            ->where('employeeSystemID', $employeeID)
            ->where('documentSystemID', 133)
            ->where('departmentSystemID', 5)
            ->where('isActive', 1)
            ->where('removedYN', 0);

            if($checkUserHasApprovalAccess->exists()) {
                $isFinanceApprovalUser = true;
            }
            
            $checkUserHasApprovalAccess = EmployeesDepartment::where('companySystemID', $request->input('companySystemID'))
            ->where('employeeSystemID', $employeeID)
            ->where('documentSystemID', 133)
            ->where('departmentSystemID', 5)
            ->where('isActive', 1)
            ->where('removedYN', 0);

            if($checkUserHasApprovalAccess->exists()) {
                $isFinanceApprovalUser = true;
            }

            
            // Handle workflow and revision logic based on isCompany
            if ($isCompany === true || $isCompany === 'true') {
                // Get CompanyBudgetPlanning for workflow/revision checks
                $companyBudgetPlanning = CompanyBudgetPlanning::with('workflow','departmentBudgetPlannings')->find($departmentPlanningId);
                if ($companyBudgetPlanning) {
                    if ($companyBudgetPlanning->workflow) {
                        $workflowMethod = $companyBudgetPlanning->workflow->method;
                    }

                    // Collect revisions from all department budget plannings
                    $allSelectedGlSections = [];
                    foreach($companyBudgetPlanning->departmentBudgetPlannings as $departmentBudgetPlanning) {
                        $revision = \App\Models\Revision::where('budgetPlanningId', $departmentBudgetPlanning->id)
                            ->whereIn('revisionStatus', [1, 2])
                            ->orderBy('created_at', 'desc')
                            ->first();
                            
                        if ($revision && $revision->selectedGlSections) {
                            $deptSelectedGlSections = json_decode($revision->selectedGlSections, true);
                            if (is_array($deptSelectedGlSections)) {
                                // Merge selectedGlSections from all department budget plannings
                                $allSelectedGlSections = array_merge($allSelectedGlSections, $deptSelectedGlSections);
                            }
                        }
                    }
                    // Remove duplicates and reindex array
                    $selectedGlSections = array_values(array_unique($allSelectedGlSections));
                }
            } else {
                // Original logic for single department budget planning
                $budgetPlanning = DepartmentBudgetPlanning::with('workflow','masterBudgetPlannings')->find($departmentPlanningId);
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

            }

            
            $data->transform(function ($item) use ($budgetPlanning, $companyBudgetPlanning, $selectedGlSections, $workflowMethod, $isGLBased, $isFinanceApprovalUser, $isCompany) {
                $isEnable = true;

                
                // Get the related department budget planning for this item
                $itemDepartmentBudgetPlanning = $item->departmentBudgetPlanning ?? $item->department_budget_planning ?? null;
                
                if($isFinanceApprovalUser)
                {
                    $isEnable = true;
                }else {
                    // Check selectedGlSections for both company and department budget planning
                    if (!empty($selectedGlSections)) {
                        if ($workflowMethod == 1 && !$isGLBased) {
                            $isEnable = in_array($item->id, $selectedGlSections);
                        } else {
                            $isEnable = in_array($item->budget_template_gl_id, $selectedGlSections);
                        }
                    }
                    
                    // For department budget planning (single), check workStatus
                    if (!$isCompany && $budgetPlanning) {
                        if(is_null($budgetPlanning) || $budgetPlanning->workStatus == 3){
                            $isEnable = false;
                        }
                        
                        // Check confirmed status for department budget planning
                        if($budgetPlanning->masterBudgetPlannings && ($budgetPlanning->masterBudgetPlannings->confirmed_yn == 1 || $budgetPlanning->confirmed_yn == 1)) {
                        $isEnable = true;
                        }
                    }
                    
                    // For company budget planning, check workStatus of the item's related department budget planning
                    if ($isCompany && $itemDepartmentBudgetPlanning) {
                        if($itemDepartmentBudgetPlanning->workStatus == 3){
                            $isEnable = false;
                        }
                        
                        // Check confirmed status for the item's related department budget planning
                        if($itemDepartmentBudgetPlanning->masterBudgetPlannings && ($itemDepartmentBudgetPlanning->masterBudgetPlannings->confirmed_yn == 1 || $itemDepartmentBudgetPlanning->confirmed_yn == 1)) {
                        $isEnable = true;
                        }
                    }
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
     * Get filter dropdown options (departments, parent GLs, GL descriptions, segments) for budget planning details.
     * POST getBudgetPlanningFilterOptions
     *
     * @param Request $request (budgetPlanningId, companySystemID, isCompany)
     * @return Response
     */
    public function getBudgetPlanningFilterOptions(Request $request)
    {
        $departmentPlanningId = $request->input('budgetPlanningId');
        if (!$departmentPlanningId) {
            return $this->sendError(trans('custom.department_planning_id_is_required'));
        }

        $employeeID = Helper::getEmployeeSystemID();
        $newRequest = new Request();
        $newRequest->replace([
            'companyId' => $request->input('companySystemID'),
            'departmentBudgetPlanningDetailID' => $departmentPlanningId,
            'delegateUser' => $employeeID
        ]);
        $controller = app(CompanyBudgetPlanningAPIController::class);
        $userPermission = ($controller->getBudgetPlanningUserPermissions($newRequest))->original;

        $isCompany = $request->input('isCompany', false);
        $departmentPlanningIds = [$departmentPlanningId];

        if ($isCompany === true || $isCompany === 'true') {
            $companyBudgetPlanning = CompanyBudgetPlanning::with('departmentBudgetPlannings')->find($departmentPlanningId);
            if ($companyBudgetPlanning && $companyBudgetPlanning->departmentBudgetPlannings) {
                $departmentPlanningIds = $companyBudgetPlanning->departmentBudgetPlannings->pluck('id')->toArray();
                if (empty($departmentPlanningIds)) {
                    $departmentPlanningIds = [-1];
                }
            }
        }

        $query = DepartmentBudgetPlanningDetail::with([
            'departmentBudgetPlanning.department',
            'budgetTemplateGl.chartOfAccount.templateCategoryDetails',
            'departmentSegment.segment'
        ])->whereIn('department_planning_id', $departmentPlanningIds);

        if ($request->input('type') != 'company_budget_planning' && $userPermission['success'] && isset($userPermission['data']['delegateUser']['status']) && $userPermission['data']['delegateUser']['status']) {
            $delegateIDs = CompanyDepartmentEmployee::where('employeeSystemID', $employeeID)->pluck('departmentEmployeeSystemID')->toArray();
            $query->whereHas('budgetDelegateAccessDetails', function ($q) use ($delegateIDs) {
                $q->whereIn('delegatee_id', $delegateIDs);
            });
        }

        $details = $query->get();

        $departmentsMap = [];
        $parentGLMap = [];
        $glDescriptionMap = [];
        $parentGLToGLDescMap = [];
        $segmentMap = [];
        $departmentSegmentIds = [];

        foreach ($details as $detail) {
            $deptId = null;
            if ($detail->departmentBudgetPlanning && $detail->departmentBudgetPlanning->department) {
                $dept = $detail->departmentBudgetPlanning->department;
                $deptId = $dept->departmentSystemID ?? $dept->id;
                if ($deptId && !isset($departmentsMap[$deptId])) {
                    $deptCode = $dept->departmentCode ?? $dept->department_code ?? '';
                    $deptName = $dept->departmentName ?? $dept->department_name ?? '';
                    $displayName = ($deptCode && $deptName) ? $deptCode . ' - ' . $deptName : ($deptName ?: $deptCode);
                    $departmentsMap[$deptId] = [
                        'id' => $deptId,
                        'departmentSystemID' => $deptId,
                        'itemName' => $displayName
                    ];
                }
            }

            if ($detail->budgetTemplateGl && $detail->budgetTemplateGl->chartOfAccount) {
                $coa = $detail->budgetTemplateGl->chartOfAccount;
                $parentGL = null;
                if ($coa->templateCategoryDetails) {
                    $parentGL = $coa->templateCategoryDetails->description ?? null;
                }
                if ($parentGL && !isset($parentGLMap[$parentGL])) {
                    $parentGLMap[$parentGL] = ['id' => $parentGL, 'itemName' => $parentGL];
                    $parentGLToGLDescMap[$parentGL] = [];
                }
                $accountCode = $coa->AccountCode ?? '';
                $accountDescription = $coa->AccountDescription ?? '';
                if ($accountCode && $accountDescription) {
                    $glDescKey = $accountCode . ' - ' . $accountDescription;
                    if (!isset($glDescriptionMap[$glDescKey])) {
                        $glDescObj = ['id' => $glDescKey, 'itemName' => $glDescKey];
                        $glDescriptionMap[$glDescKey] = $glDescObj;
                        if ($parentGL) {
                            $parentGLToGLDescMap[$parentGL][] = $glDescObj;
                        }
                    }
                }
            }

            if ($detail->departmentSegment && $detail->departmentSegment->segment) {
                $seg = $detail->departmentSegment->segment;
                $segId = $seg->serviceLineSystemID ?? $seg->id;
                if ($segId && !isset($segmentMap[$segId])) {
                    $segCode = $seg->ServiceLineCode ?? $seg->service_line_code ?? '';
                    $segDes = $seg->ServiceLineDes ?? $seg->service_line_des ?? '';
                    $displayName = ($segCode && $segDes) ? $segCode . ' - ' . $segDes : ($segDes ?: $segCode);
                    $segmentMap[$segId] = [
                        'id' => $segId,
                        'serviceLineSystemID' => $segId,
                        'ServiceLineCode' => $segCode,
                        'ServiceLineDes' => $segDes,
                        'itemName' => $displayName
                    ];
                }
                if ($deptId && $segId) {
                    if (!isset($departmentSegmentIds[$deptId])) {
                        $departmentSegmentIds[$deptId] = [];
                    }
                    if (!in_array($segId, $departmentSegmentIds[$deptId])) {
                        $departmentSegmentIds[$deptId][] = $segId;
                    }
                }
            }
        }

        $workflowMethod = null;
        if ($isCompany === true || $isCompany === 'true') {
            $companyBudgetPlanning = CompanyBudgetPlanning::with('workflow')->find($departmentPlanningId);
            if ($companyBudgetPlanning && $companyBudgetPlanning->workflow) {
                $workflowMethod = $companyBudgetPlanning->workflow->method;
            }
        } else {
            $budgetPlanning = DepartmentBudgetPlanning::with('workflow')->find($departmentPlanningId);
            if ($budgetPlanning && $budgetPlanning->workflow) {
                $workflowMethod = $budgetPlanning->workflow->method;
            }
        }

        $data = [
            'departments' => array_values($departmentsMap),
            'parentGLs' => array_values($parentGLMap),
            'glDescriptions' => array_values($glDescriptionMap),
            'parentGLToGLDescMap' => $parentGLToGLDescMap,
            'segments' => array_values($segmentMap),
            'departmentSegmentIds' => $departmentSegmentIds,
            'workflowMethod' => $workflowMethod
        ];

        return $this->sendResponse($data, trans('custom.filter_options_retrieved_successfully'));
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

            try {
                Gate::authorize('BudgetPlanningUserPermissionGate', [
                    $input['companySystemID'],
                    $budgetDetailId,
                    Helper::getEmployeeSystemID(),
                    $entryID,
                    true // check input/edit/save permissions for this endpoint
                ]);
            } catch (AuthorizationException $e) {
                return $this->sendError($e->getMessage());
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
                'delegateUser' =>  Helper::getEmployeeSystemID()
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

                if(!isset($delegateUserAccess['access']) ||  (isset($delegateUserAccess['access']) && !$delegateUserAccess['access']['show_others_input']) || $delegateUserAccess['isActive'] === false)
                {
                    $entries = $entries->where('created_by',Helper::getEmployeeSystemID());
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
                        (($entry->created_by == Helper::getEmployeeSystemID() && $userPermission['data']['delegateUser']['isActive']) ? true : (isset($delegateUserAccess['access']['edit_input']) && $delegateUserAccess['access']['edit_input'] && $userPermission['data']['delegateUser']['isActive'] ? true : false)) :
                        true,
                    'delete' => (isset($userPermission['data']['delegateUser']) && $userPermission['data']['delegateUser']['status']) ?
                        (($entry->created_by == Helper::getEmployeeSystemID() && $userPermission['data']['delegateUser']['isActive']) ? true : (isset($delegateUserAccess['access']['delete_input']) && $delegateUserAccess['access']['delete_input'] && $userPermission['data']['delegateUser']['isActive'] ? true : false)) :
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
        $departmentBudgetPlanningDetail = DepartmentBudgetPlanningDetail::with('budgetTemplate')->find($entry->budget_detail_id);

        if ($entry) {
            $oldValue = BudgetDetTemplateEntryData::with(['templateColumn.preColumn'])->where('entryID', $entry['entryID'])->get();
           
           
            $departmentBudgetPlanningDetail->request_amount -= $oldValue->where('templateColumnID', $departmentBudgetPlanningDetail->budgetTemplate->linkRequestAmount)->first()->value;
            $departmentBudgetPlanningDetail->difference_current_request -= $oldValue->where('templateColumnID', $departmentBudgetPlanningDetail->budgetTemplate->linkRequestAmount)->first()->value;
            $departmentBudgetPlanningDetail->save();
            // delete entry data
            BudgetDetTemplateEntryData::where('entryID', $entry['entryID'])->delete();
            // delete entry attachments
            BudgetPlanningDetailTempAttachment::where('entry_id',$entry['entryID'])->delete();

            $budgetDetailId = $entry['budget_detail_id'];


            $newRequest = new Request();
            $newRequest->replace([
                'companyId' => $input['companySystemID'],
                'departmentBudgetPlanningDetailID' => $budgetDetailId,
                'delegateUser' =>  Helper::getEmployeeSystemID()
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

            if((isset($userPermission['data']['financeApprovalUser']) && $userPermission['data']['financeApprovalUser']['status']) || (isset($userPermission['data']['financeUser']) && $userPermission['data']['financeUser']['status']))
            {
                return  $this->sendError("User doesn't have permission to delete data");
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
        $isGroup = Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = Helper::getGroupCompany($selectedCompanyId);
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
        $employeeID =  Helper::getEmployeeSystemID();

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

            $oldValue = $budgetPlanning->toArray();
            $budgetPlanning->financeTeamStatus = $newStatus;
            $budgetPlanning->save();
            $budgetPlanning->refresh();
            $uuid = $request->get('tenant_uuid', 'local');
            $db = $request->get('db', '');
            $this->auditLog(
                $db,
                $input['budgetPlanningId'],
                $uuid,
                'department_budget_plannings',
                $budgetPlanning->planningCode,
                'U',
                $budgetPlanning->toArray(),
                $oldValue,
                0
            );

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
            $basePath = $this->runExportBudgetPlanningDetails($request);
            return $this->sendResponse($basePath, trans('custom.success_export'));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    /**
     * Run export logic and return the file path (for use by controller response or job).
     * @param Request $request
     * @return string File path on success
     * @throws \Exception
     */
    public function runExportBudgetPlanningDetails(Request $request)
    {
        try {
            $input = $request->all();
            $departmentPlanningId = $request->input('budgetPlanningId');

            if (!$departmentPlanningId) {
                throw new \Exception(trans('custom.department_planning_id_is_required'));
            }

            $employeeID = Helper::getEmployeeSystemID();
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


            // Check if source is from approval - if so, get all department budget planning details for the company budget planning
            $source = $request->input('source', '');
            if ($source === 'approval' || $source === 'from_approval') {
                // When source is approval, budgetPlanningId is actually a CompanyBudgetPlanning ID
                // Get CompanyBudgetPlanning directly and get all DepartmentBudgetPlanning from it
                $companyBudgetPlanning = CompanyBudgetPlanning::with('departmentBudgetPlannings')
                    ->find($departmentPlanningId);
                
                if ($companyBudgetPlanning && $companyBudgetPlanning->departmentBudgetPlannings && $companyBudgetPlanning->departmentBudgetPlannings->count() > 0) {
                    $allDepartmentPlanningIds = $companyBudgetPlanning->departmentBudgetPlannings->pluck('id')->filter()->toArray();
                    
                    // Only query if we have valid department planning IDs
                    if (!empty($allDepartmentPlanningIds) && count($allDepartmentPlanningIds) > 0) {
                        // Query all department budget planning details for all departments in this company budget planning
                        $query->whereHas('departmentBudgetPlanning', function ($q) use ($allDepartmentPlanningIds) {
                            $q->whereIn('id', $allDepartmentPlanningIds);
                        });
                    } else {
                        // If no valid department planning IDs, return empty result
                        $query->whereRaw('1 = 0'); // This will return no results
                    }
                } else {
                    // Fallback to original query if company budget planning not found or has no departments
                    $query->forDepartmentPlanning($departmentPlanningId);
                }
            } else {
                $query->forDepartmentPlanning($departmentPlanningId);
            }

            // Handle department filtering for company-level exports
            $isCompany = $request->input('isCompany', false);
            $departments = $request->input('departments');
            if (!empty($departments) && is_array($departments) && count($departments) > 0 && ($isCompany === true || $isCompany === 'true')) {
                if (isset($departments[0]) && is_array($departments[0]) && isset($departments[0]['id'])) {
                    $departmentIds = array_column($departments, 'id');
                } else {
                    $departmentIds = $departments;
                }
                if (!empty($departmentIds)) {
                    $query->whereHas('departmentBudgetPlanning', function ($q) use ($departmentIds) {
                        $q->whereIn('departmentID', $departmentIds);
                    });
                }
            }

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
            if (!empty($segments) && is_array($segments) && count($segments) > 0) {
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
                                    if (is_string($glDesc) && !empty($glDesc)) {
                                        $parts = explode(' - ', $glDesc, 2);
                                        if (count($parts) == 2 && isset($parts[0]) && isset($parts[1])) {
                                            $q3->orWhere(function ($q4) use ($parts) {
                                                $q4->where('AccountCode', $parts[0])
                                                   ->where('AccountDescription', $parts[1]);
                                            });
                                        }
                                    }
                                }
                            });
                        });
                    });
                }
            }

            // Get selected status for grouping/filtering
            $selectedStatus = (int) $request->input('selectedStatus', 1);
            
            // Check if GL based export is requested (either from isGLBased or selectedStatus = 4)
            $isGLBased = $request->input('isGLBased', false) || $selectedStatus == 4;
            
            // Get workflow method to check if segment-based grouping is allowed
            $workflowMethod = null;
            if ($isCompany === true || $isCompany === 'true') {
                $companyBudgetPlanning = CompanyBudgetPlanning::with('workflow')->find($departmentPlanningId);
                if ($companyBudgetPlanning && $companyBudgetPlanning->workflow) {
                    $workflowMethod = $companyBudgetPlanning->workflow->method;
                }
            } else {
                $budgetPlanning = DepartmentBudgetPlanning::with('workflow')->find($departmentPlanningId);
                if ($budgetPlanning && $budgetPlanning->workflow) {
                    $workflowMethod = $budgetPlanning->workflow->method;
                }
            }
            
            // Apply same grouping as getByDepartmentPlanning for export
            if ($selectedStatus == 2) {
                // Department-wise: Group by (Department, GL)
                $allData = $query->orderBy('id', 'desc')->get();
                $groupedData = [];
                foreach ($allData as $item) {
                    $deptId = $item->departmentBudgetPlanning ? $item->departmentBudgetPlanning->departmentID : null;
                    $key = ($deptId ?? 'unknown') . '_' . ($item->budget_template_gl_id ?? 'unknown');

                    if (!isset($groupedData[$key])) {
                        $groupedItem = $item->replicate();
                        $groupedItem->request_amount = 0;
                        $groupedItem->previous_year_budget = 0;
                        $groupedItem->current_year_budget = 0;
                        $groupedItem->difference_last_current_year = 0;
                        $groupedItem->amount_given_by_finance = 0;
                        $groupedItem->amount_given_by_hod = 0;
                        $groupedItem->difference_current_request = 0;
                        $groupedItem->setRelation('departmentSegment', null);
                        $groupedItem->department_segment_id = null;
                        if ($item->departmentBudgetPlanning) {
                            $groupedItem->setRelation('departmentBudgetPlanning', $item->departmentBudgetPlanning);
                            if (!$groupedItem->departmentBudgetPlanning->relationLoaded('department')) {
                                $groupedItem->departmentBudgetPlanning->load('department');
                            }
                        }
                        $groupedData[$key] = $groupedItem;
                    }

                    $groupedData[$key]->request_amount += ($item->request_amount ?? 0);
                    $groupedData[$key]->previous_year_budget += ($item->previous_year_budget ?? 0);
                    $groupedData[$key]->current_year_budget += ($item->current_year_budget ?? 0);
                    $groupedData[$key]->difference_last_current_year += ($item->difference_last_current_year ?? 0);
                    $groupedData[$key]->amount_given_by_finance += ($item->amount_given_by_finance ?? 0);
                    $groupedData[$key]->amount_given_by_hod += ($item->amount_given_by_hod ?? 0);
                    $groupedData[$key]->difference_current_request += ($item->difference_current_request ?? 0);
                }
                $dataset = collect(array_values($groupedData));
            } elseif ($selectedStatus == 3 && $workflowMethod == 1) {
                // Segment-wise: Group by (Segment, GL)
                $allData = $query->orderBy('id', 'desc')->get();
                $groupedData = [];
                foreach ($allData as $item) {
                    $segmentId = null;
                    if ($item->departmentSegment && $item->departmentSegment->segment) {
                        $segmentId = $item->departmentSegment->segment->serviceLineSystemID ?? $item->departmentSegment->segment->id ?? null;
                    }
                    $key = ($segmentId ?? ($item->department_segment_id ?? 'unknown')) . '_' . ($item->budget_template_gl_id ?? 'unknown');

                    if (!isset($groupedData[$key])) {
                        $groupedItem = $item->replicate();
                        $groupedItem->request_amount = 0;
                        $groupedItem->previous_year_budget = 0;
                        $groupedItem->current_year_budget = 0;
                        $groupedItem->difference_last_current_year = 0;
                        $groupedItem->amount_given_by_finance = 0;
                        $groupedItem->amount_given_by_hod = 0;
                        $groupedItem->difference_current_request = 0;
                        $groupedItem->setRelation('departmentBudgetPlanning', null);
                        if ($item->departmentSegment && $item->departmentSegment->segment) {
                            $groupedItem->setRelation('departmentSegment', $item->departmentSegment);
                            if (!$groupedItem->departmentSegment->relationLoaded('segment')) {
                                $groupedItem->departmentSegment->load('segment');
                            }
                        }
                        $groupedData[$key] = $groupedItem;
                    }

                    $groupedData[$key]->request_amount += ($item->request_amount ?? 0);
                    $groupedData[$key]->previous_year_budget += ($item->previous_year_budget ?? 0);
                    $groupedData[$key]->current_year_budget += ($item->current_year_budget ?? 0);
                    $groupedData[$key]->difference_last_current_year += ($item->difference_last_current_year ?? 0);
                    $groupedData[$key]->amount_given_by_finance += ($item->amount_given_by_finance ?? 0);
                    $groupedData[$key]->amount_given_by_hod += ($item->amount_given_by_hod ?? 0);
                    $groupedData[$key]->difference_current_request += ($item->difference_current_request ?? 0);
                }
                $dataset = collect(array_values($groupedData));
            } elseif ($isGLBased || $selectedStatus == 4) {
                // GL-based: Group by GL only
                $allData = $query->orderBy('id', 'desc')->get();
                $groupedData = [];
                foreach ($allData as $item) {
                    $key = $item->budget_template_gl_id ?? 'unknown';

                    if (!isset($groupedData[$key])) {
                        $groupedItem = $item->replicate();
                        $groupedItem->request_amount = 0;
                        $groupedItem->previous_year_budget = 0;
                        $groupedItem->current_year_budget = 0;
                        $groupedItem->difference_last_current_year = 0;
                        $groupedItem->amount_given_by_finance = 0;
                        $groupedItem->amount_given_by_hod = 0;
                        $groupedItem->difference_current_request = 0;
                        $groupedItem->setRelation('departmentSegment', null);
                        $groupedItem->department_segment_id = null;
                        $groupedItem->setRelation('departmentBudgetPlanning', null);
                        if (!$groupedItem->relationLoaded('budgetTemplateGl')) {
                            $groupedItem->load('budgetTemplateGl.chartOfAccount.templateCategoryDetails');
                        }
                        if (!$groupedItem->relationLoaded('responsiblePerson')) {
                            $groupedItem->load('responsiblePerson');
                        }
                        $groupedData[$key] = $groupedItem;
                    }

                    $groupedData[$key]->request_amount += ($item->request_amount ?? 0);
                    $groupedData[$key]->previous_year_budget += ($item->previous_year_budget ?? 0);
                    $groupedData[$key]->current_year_budget += ($item->current_year_budget ?? 0);
                    $groupedData[$key]->difference_last_current_year += ($item->difference_last_current_year ?? 0);
                    $groupedData[$key]->amount_given_by_finance += ($item->amount_given_by_finance ?? 0);
                    $groupedData[$key]->amount_given_by_hod += ($item->amount_given_by_hod ?? 0);
                    $groupedData[$key]->difference_current_request += ($item->difference_current_request ?? 0);
                }
                $dataset = collect(array_values($groupedData));
            } elseif ($selectedStatus == 5) {
                // Category-wise: Group by (Category, GL)
                $allData = $query->orderBy('id', 'desc')->get();
                foreach ($allData as $item) {
                    if (!$item->relationLoaded('budgetTemplateGl')) {
                        $item->load('budgetTemplateGl.chartOfAccount.templateCategoryDetails');
                    } elseif ($item->budgetTemplateGl && !$item->budgetTemplateGl->relationLoaded('chartOfAccount')) {
                        $item->budgetTemplateGl->load('chartOfAccount.templateCategoryDetails');
                    } elseif ($item->budgetTemplateGl && $item->budgetTemplateGl->chartOfAccount && !$item->budgetTemplateGl->chartOfAccount->relationLoaded('templateCategoryDetails')) {
                        $item->budgetTemplateGl->chartOfAccount->load('templateCategoryDetails');
                    }
                }

                $groupedData = [];
                foreach ($allData as $item) {
                    $categoryId = null;
                    $categoryDescription = null;
                    if ($item->budgetTemplateGl && $item->budgetTemplateGl->chartOfAccount && $item->budgetTemplateGl->chartOfAccount->templateCategoryDetails) {
                        $categoryId = $item->budgetTemplateGl->chartOfAccount->templateCategoryDetails->id;
                        $categoryDescription = $item->budgetTemplateGl->chartOfAccount->templateCategoryDetails->description ?? null;
                    }
                    $key = ($categoryId ?? ($categoryDescription ?? 'unknown')) . '_' . ($item->budget_template_gl_id ?? 'unknown');

                    if (!isset($groupedData[$key])) {
                        $groupedItem = $item->replicate();
                        $groupedItem->request_amount = 0;
                        $groupedItem->previous_year_budget = 0;
                        $groupedItem->current_year_budget = 0;
                        $groupedItem->difference_last_current_year = 0;
                        $groupedItem->amount_given_by_finance = 0;
                        $groupedItem->amount_given_by_hod = 0;
                        $groupedItem->difference_current_request = 0;
                        $groupedItem->setRelation('departmentSegment', null);
                        $groupedItem->department_segment_id = null;
                        $groupedItem->setRelation('departmentBudgetPlanning', null);
                        if ($item->budgetTemplateGl && $item->budgetTemplateGl->chartOfAccount && $item->budgetTemplateGl->chartOfAccount->templateCategoryDetails) {
                            $groupedItem->setRelation('category', $item->budgetTemplateGl->chartOfAccount->templateCategoryDetails);
                        }
                        $groupedData[$key] = $groupedItem;
                    }

                    $groupedData[$key]->request_amount += ($item->request_amount ?? 0);
                    $groupedData[$key]->previous_year_budget += ($item->previous_year_budget ?? 0);
                    $groupedData[$key]->current_year_budget += ($item->current_year_budget ?? 0);
                    $groupedData[$key]->difference_last_current_year += ($item->difference_last_current_year ?? 0);
                    $groupedData[$key]->amount_given_by_finance += ($item->amount_given_by_finance ?? 0);
                    $groupedData[$key]->amount_given_by_hod += ($item->amount_given_by_hod ?? 0);
                    $groupedData[$key]->difference_current_request += ($item->difference_current_request ?? 0);
                }
                $dataset = collect(array_values($groupedData));
            } else {
                // Status 1: Details (default - show all details, no grouping)
                $dataset = $query->orderBy('id', 'desc')->get();
            }

            // Ensure dataset is a collection
            if (!$dataset instanceof \Illuminate\Support\Collection) {
                $dataset = collect($dataset);
            }

            $columnSlugs = $request->input('columnSlugs');
            $isColumnSlugsArray = !empty($columnSlugs) && is_array($columnSlugs);
            $defaultShowSegment = (($selectedStatus == 1 && !$isGLBased) || $selectedStatus == 3);
            $defaultShowDepartment = (($selectedStatus == 1 && !$isGLBased) || $selectedStatus == 2);
            $showSegmentColumn = $isColumnSlugsArray
                ? in_array('segment', $columnSlugs, true)
                : $defaultShowSegment;
            $showDepartmentColumn = $isColumnSlugsArray
                ? in_array('department', $columnSlugs, true)
                : $defaultShowDepartment;

            $data = array();
            $x = 0;
            $dataset->chunk(200)->each(function ($chunk) use (&$data, &$x, $columnSlugs, $isColumnSlugsArray, $showSegmentColumn, $showDepartmentColumn, $selectedStatus) {
                foreach ($chunk as $val) {
                    $x++;
                    $rowBySlug = $this->buildExportRowBySlug($val, $x, $showSegmentColumn, $showDepartmentColumn, $selectedStatus);
                    if ($isColumnSlugsArray) {
                        $data[$x] = $this->filterExportRowByColumnSlugs($rowBySlug, $columnSlugs);
                    } else {
                        $data[$x] = $this->exportRowSlugToHeader($rowBySlug);
                    }
                }
            });

            // Prepend header row (column labels) so Excel has headers in row 1
            if (!empty($data)) {
                $firstRow = reset($data);
                $headerLabels = array_keys(is_array($firstRow) ? $firstRow : (array) $firstRow);
                $headerRow = array_combine($headerLabels, $headerLabels);
                $data = array_merge([0 => $headerRow], $data);
            }

            // Ensure data array is not empty and has valid structure
            if (empty($data) || !is_array($data)) {
                throw new \Exception(trans('custom.no_data_to_export'));
            }

            // Re-index array to start from 0 (CreateExcel expects $data[0] to exist)
            $data = array_values($data);

            $companyMaster = Company::find($request->input('companySystemID'));
            $companyCode = $companyMaster->CompanyID ?? 'common';
            $detail_array = array(
                'company_code' => $companyCode,
            );

            $lang = app()->getLocale();
            $fontFamily = Helper::getExcelFontFamily($lang);
            $disk = 's3';
            $fileName = 'budget_planning_details';
            $path_dir = 'budget_planning/budget_planning_details/excel/';
            $type = 'xlsx';
            $excelExport = new CreateExcelExport(function($excel) use ($data, $fontFamily) {
                $excel->sheet(trans('custom.excel_sheet_name'), function($sheet) use ($data, $fontFamily) {
                    $sheet->setStyle([
                        'font' => [
                            'name' => $fontFamily,
                            'size' => 11,
                        ]
                    ]);
    
                    $rowNum = 1;
                    $knownHeaders = [
                        trans('custom.excel_company_id'),
                        trans('custom.excel_order_details'),
                        trans('custom.excel_item_code'),
                        trans('custom.excel_pr_number'),
                        trans('custom.excel_logistics_details'),
                        trans('custom.excel_category'),
                        trans('custom.excel_addon_details'),
                    ];
    
                    $maxColumns = 0;
                    foreach ($data as $row) {
                        $maxColumns = max($maxColumns, count($row));
                    }
    
                    // Build indexed rows so PhpSpreadsheet writes columns in order (associative keys break export)
                    $indexedData = [];
                    foreach ($data as $row) {
                        $rowValues = array_values(is_array($row) ? $row : (array) $row);
                        $indexedData[] = array_pad($rowValues, $maxColumns, '');
                    }
    
                    if (!empty($indexedData)) {
                        // Write all data starting at A1
                        $sheet->fromArray($indexedData, null, 'A1', true);

                        // Make first row (header row) bold
                        $highestColumn = Coordinate::stringFromColumnIndex($maxColumns);
                        $sheet->cells("A1:{$highestColumn}1", function($cells) use ($fontFamily) {
                            $cells->setFont([
                                'bold' => true,
                                'size' => 12,
                                'name' => $fontFamily
                            ]);
                        });
                    }
    
                    // Auto-size columns to fit content (after data is written)
                    $sheet->setAutoSize(true);
    
                    // Set right-to-left for Arabic locale
                    if (app()->getLocale() == 'ar') {
                        $sheet->getStyle('A1:Z1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                        $sheet->setRightToLeft(true);
                    }
                });
            }, 'xlsx');
            $excel_content = $excelExport->getContent();

            $full_name = $companyCode.'_'.$fileName.'_'.strtotime(date("Y-m-d H:i:s")).'.'.$type;
            $path = $companyCode.'/'.$path_dir.$full_name;
            $result = Storage::disk($disk)->put($path, $excel_content);
            $basePath = '';
            if ($result) {
                if (Storage::disk($disk)->exists($path)) {
                    $basePath = Helper::getFileUrlFromS3($path);
                }
            }

            if(empty($source))
            {
                return $basePath;
            }else {
                return $path;
            }

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Export all department budget planning details of a company budget planning to Excel (no pagination).
     * Dispatches a job so large exports (e.g. 1000+ records) run in background; user is notified when ready.
     *
     * @param Request $request (budgetPlanningId = CompanyBudgetPlanning ID, companySystemID, optional filters)
     * @return Response
     */
    public function exportCompanyBudgetPlanningDetailsAll(Request $request)
    {
        $request->merge([
            'source' => 'from_approval',
            'isCompany' => true,
        ]);
        $userId = Helper::getEmployeeSystemID();
        $db = $request->input('db', '');
        ExportCompanyBudgetPlanningDetailsJob::dispatch($db, $request->all(), $userId);
        return $this->sendResponse('', trans('custom.budget_planning_export_in_progress'));
    }

    /**
     * Slug to Excel header label map for export
     *
     * @return array
     */
    private function getExportSlugToHeaderMap()
    {
        return [
            '#' => '#',
            'segment' => 'Segment',
            'department' => 'Department',
            'gl_type' => 'GL Type',
            'parent_gl' => 'Parent GL',
            'gl_description' => 'GL Description',
            'category' => 'Category',
            'responsible_person' => 'Responsible Person',
            'request_amount' => 'Request Amount',
            'time_for_submission' => 'Time for Submission',
            'previous_year_budget' => 'Previous Year Budget',
            'current_year_budget' => 'Current Year Budget',
            'difference_last_year_and_current_year' => 'Difference from last year & current year',
            'amount_given_by_finance' => 'Amount Given by Finance',
            'amount_given_by_hod' => 'Amount Given by HOD',
            'difference_from_current_year_and_request_amount' => 'Difference from current year and request amount',
        ];
    }

    /**
     * Build one export row keyed by slug (for filtering by selected columns)
     *
     * @param \Illuminate\Database\Eloquent\Model $val
     * @param int $rowIndex
     * @param int $selectedStatus
     * @param bool $isGLBased
     * @return array
     */
    private function buildExportRowBySlug($val, $rowIndex, $showSegmentColumn, $showDepartmentColumn, $selectedStatus)
    {
        $row = [];
        $row['#'] = $rowIndex;

        $row['segment'] = $showSegmentColumn
            ? ($val->departmentSegment && $val->departmentSegment->segment
                ? ($val->departmentSegment->segment->ServiceLineCode . ' - ' . $val->departmentSegment->segment->ServiceLineDes)
                : '')
            : '';
        $row['department'] = $showDepartmentColumn
            ? ($val->departmentBudgetPlanning && $val->departmentBudgetPlanning->department
                ? $val->departmentBudgetPlanning->department->departmentDescription
                : '')
            : '';
        if ($selectedStatus != 5) {
            $row['gl_type'] = $val->budgetTemplateGl && $val->budgetTemplateGl->chartOfAccount
                ? $val->budgetTemplateGl->chartOfAccount->controlAccounts
                : '';
            $row['parent_gl'] = $val->budgetTemplateGl && $val->budgetTemplateGl->chartOfAccount && $val->budgetTemplateGl->chartOfAccount->templateCategoryDetails
                ? $val->budgetTemplateGl->chartOfAccount->templateCategoryDetails->description
                : '';
            $row['gl_description'] = $val->budgetTemplateGl && $val->budgetTemplateGl->chartOfAccount
                ? ($val->budgetTemplateGl->chartOfAccount->AccountCode . ' - ' . $val->budgetTemplateGl->chartOfAccount->AccountDescription)
                : '';
            $row['category'] = '';
        } else {
            // Category report (5) groups by (Category, GL) — show category and GL description when present
            $row['category'] = $val->category
                ? $val->category->description
                : ($val->budgetTemplateGl && $val->budgetTemplateGl->chartOfAccount && $val->budgetTemplateGl->chartOfAccount->templateCategoryDetails
                    ? $val->budgetTemplateGl->chartOfAccount->templateCategoryDetails->description
                    : '');
            $row['gl_type'] = $val->budgetTemplateGl && $val->budgetTemplateGl->chartOfAccount
                ? $val->budgetTemplateGl->chartOfAccount->controlAccounts
                : '';
            $row['parent_gl'] = $val->budgetTemplateGl && $val->budgetTemplateGl->chartOfAccount && $val->budgetTemplateGl->chartOfAccount->templateCategoryDetails
                ? $val->budgetTemplateGl->chartOfAccount->templateCategoryDetails->description
                : '';
            $row['gl_description'] = $val->budgetTemplateGl && $val->budgetTemplateGl->chartOfAccount
                ? ($val->budgetTemplateGl->chartOfAccount->AccountCode . ' - ' . $val->budgetTemplateGl->chartOfAccount->AccountDescription)
                : '';
        }

        $row['responsible_person'] = $val->responsiblePerson ? $val->responsiblePerson->empName : '';
        $row['request_amount'] = number_format($val->request_amount ?? 0, 2);
        $row['time_for_submission'] = $val->time_for_submission ? Carbon::parse($val->time_for_submission)->format('d/m/Y') : '';
        $row['previous_year_budget'] = number_format($val->previous_year_budget ?? 0, 2);
        $row['current_year_budget'] = number_format($val->current_year_budget ?? 0, 2);
        $row['difference_last_year_and_current_year'] = $val->difference_last_current_year ?? '';
        $row['amount_given_by_finance'] = number_format($val->amount_given_by_finance ?? 0, 2);
        $row['amount_given_by_hod'] = number_format($val->amount_given_by_hod ?? 0, 2);
        $row['difference_from_current_year_and_request_amount'] = $val->difference_current_request ?? '';

        return $row;
    }

    /**
     * Filter row by selected column slugs and return with Excel header keys (in order of columnSlugs)
     *
     * @param array $rowBySlug
     * @param array $columnSlugs
     * @return array
     */
    private function filterExportRowByColumnSlugs(array $rowBySlug, array $columnSlugs)
    {
        $slugToHeader = $this->getExportSlugToHeaderMap();
        $result = [];
        foreach ($columnSlugs as $slug) {
            $slug = is_string($slug) ? trim($slug) : $slug;
            if ($slug === '' || $slug === null) {
                continue;
            }
            if (array_key_exists($slug, $rowBySlug)) {
                $header = isset($slugToHeader[$slug]) ? $slugToHeader[$slug] : $slug;
                $result[$header] = $rowBySlug[$slug];
            } elseif ($slug === '#' && array_key_exists('#', $rowBySlug)) {
                $result['#'] = $rowBySlug['#'];
            }
        }
        return $result;
    }

    /**
     * Convert row keyed by slug to row keyed by Excel header (for backward compatibility when no columnSlugs sent)
     *
     * @param array $rowBySlug
     * @return array
     */
    private function exportRowSlugToHeader(array $rowBySlug)
    {
        $slugToHeader = $this->getExportSlugToHeaderMap();
        $result = [];
        foreach ($rowBySlug as $slug => $value) {
            $header = isset($slugToHeader[$slug]) ? $slugToHeader[$slug] : $slug;
            $result[$header] = $value;
        }
        return $result;
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
