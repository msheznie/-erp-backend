<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDepartmentBudgetPlanningDetailAPIRequest;
use App\Http\Requests\API\UpdateDepartmentBudgetPlanningDetailAPIRequest;
use App\Models\DepartmentBudgetPlanningDetail;
use App\Repositories\DepartmentBudgetPlanningDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class DepartmentBudgetPlanningDetailController
 * @package App\Http\Controllers\API
 */

class DepartmentBudgetPlanningDetailAPIController extends AppBaseController
{
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
        $departmentPlanningId = $request->input('budgetPlanningId');
        
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
            ]);

            return \DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('responsible_person_name', function ($row) {
                    return $row->responsiblePerson ? $row->responsiblePerson->empName : null;
                })
                ->editColumn('time_for_submission', function ($row) {
                    return $row->time_for_submission ? $row->time_for_submission->format('d/m/Y') : '';
                })
                ->editColumn('request_amount', function ($row) {
                    return number_format($row->request_amount, 2);
                })
                ->editColumn('previous_year_budget', function ($row) {
                    return number_format($row->previous_year_budget, 2);
                })
                ->editColumn('current_year_budget', function ($row) {
                    return number_format($row->current_year_budget, 2);
                })
                ->make(true);

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

    public function getBudgetPlanningChartOfAccounts(Request $request)
    {
        $budgetPlanningId = $request->input('budget_planning_id');
        
        if (!$budgetPlanningId) {
            return $this->sendError('Budget Planning ID is required');
        }

        // below should be unique chart of accounts , unique by budget_template_gl_id
        $chartOfAccounts = DepartmentBudgetPlanningDetail::where('department_planning_id', $budgetPlanningId)->with('budgetTemplateGl.chartOfAccount')->groupBy('budget_template_gl_id')->get();
        return $this->sendResponse($chartOfAccounts, 'Chart of Accounts retrieved successfully');
    }

    public function getBudgetPlanningSegments(Request $request)
    {
        $budgetPlanningId = $request->input('budget_planning_id');
        
        if (!$budgetPlanningId) {
            return $this->sendError('Budget Planning ID is required');
        }

        $segments = DepartmentBudgetPlanningDetail::where('department_planning_id', $budgetPlanningId)->with('departmentSegment.segment')->groupBy('department_segment_id')->get();

        return $this->sendResponse($segments, 'Segments retrieved successfully');
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
            $input = $request->validate([
                'budgetDetailId' => 'required|integer|exists:department_budget_planning_details,id',
                'rows' => 'required|array|min:1',
                'rows.*.rowNumber' => 'required|integer|min:1',
                'rows.*.data' => 'required|array|min:1',
                'rows.*.data.*.templateColumnID' => 'required|integer|exists:budget_template_columns,templateColumnID',
                'rows.*.data.*.value' => 'nullable|string'
            ]);

            $budgetDetailId = $input['budgetDetailId'];
            $rows = $input['rows'];

            // Begin transaction
            \DB::beginTransaction();

            try {
                foreach ($rows as $rowData) {
                    // Create entry record
                    $entry = \DB::table('budget_det_template_entries')->insertGetId([
                        'budget_detail_id' => $budgetDetailId,
                        'rowNumber' => $rowData['rowNumber'],
                        'created_by' => auth()->id() ?? 1, // Default to 1 if no auth
                        'timestamp' => now()
                    ]);

                    // Create entry data records
                    foreach ($rowData['data'] as $columnData) {
                        \DB::table('budget_det_template_entry_data')->insert([
                            'entryID' => $entry,
                            'templateColumnID' => $columnData['templateColumnID'],
                            'value' => $columnData['value'] ?? '',
                            'timestamp' => now()
                        ]);
                    }
                }

                \DB::commit();

                return $this->sendResponse([
                    'message' => 'Template entries saved successfully',
                    'rowsSaved' => count($rows)
                ], 'Budget detail template entries saved successfully');

            } catch (\Exception $e) {
                \DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            return $this->sendError('Error saving template entries - ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get budget detail template entries
     *
     * @param int $budgetDetailId
     * @return Response
     */
    public function getBudgetDetailTemplateEntries($budgetDetailId)
    {
        try {
            // Validate budget detail exists
            $budgetDetail = DepartmentBudgetPlanningDetail::find($budgetDetailId);
            if (!$budgetDetail) {
                return $this->sendError('Budget detail not found');
            }

            // Get entries with their data
            $entries = \DB::table('budget_det_template_entries as e')
                ->leftJoin('budget_det_template_entry_data as ed', 'e.entryID', '=', 'ed.entryID')
                ->where('e.budget_detail_id', $budgetDetailId)
                ->orderBy('e.rowNumber', 'asc')
                ->orderBy('ed.templateColumnID', 'asc')
                ->get();

            // Group entries by row
            $groupedEntries = [];
            foreach ($entries as $entry) {
                if (!isset($groupedEntries[$entry->entryID])) {
                    $groupedEntries[$entry->entryID] = [
                        'entryID' => $entry->entryID,
                        'rowNumber' => $entry->rowNumber,
                        'created_by' => $entry->created_by,
                        'timestamp' => $entry->timestamp,
                        'entryData' => []
                    ];
                }

                if ($entry->templateColumnID) {
                    $groupedEntries[$entry->entryID]['entryData'][] = [
                        'templateColumnID' => $entry->templateColumnID,
                        'value' => $entry->value
                    ];
                }
            }

            return $this->sendResponse(array_values($groupedEntries), 'Budget detail template entries retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving template entries - ' . $e->getMessage(), 500);
        }
    }
}
