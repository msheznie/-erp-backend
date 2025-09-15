<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Models\DepartmentBudgetPlanningDetail;
use App\Models\DepartmentBudgetPlanning;
use App\Models\BudgetDelegateAccessRecord;
use App\Services\BudgetDelegateService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BudgetDelegateAPIController extends AppBaseController
{
    protected $budgetDelegateService;

    public function __construct(BudgetDelegateService $budgetDelegateService)
    {
        $this->budgetDelegateService = $budgetDelegateService;
    }

    /**
     * Get existing delegate access records
     *
     * @param Request $request
     * @return Response
     */
    public function getDelegateAccessRecords(Request $request)
    {
        try {
            $request->validate([
                'budget_planning_detail_id' => 'required|integer|exists:department_budget_planning_details,id'
            ]);

            $records = $this->budgetDelegateService->getDelegateAccessRecords($request->budget_planning_detail_id);
            
            return $this->sendResponse($records, 'Delegate access records retrieved successfully');
        } catch (Exception $e) {
            return $this->sendError('Error retrieving delegate access records: ' . $e->getMessage());
        }
    }

    /**
     * Create or update delegate access
     *
     * @param Request $request
     * @return Response
     */
    public function createOrUpdateDelegateAccess(Request $request)
    {
        $input = $request->all();

        if(!isset($input['type']) && isset($input['id']))
        {
            $input = $this->convertArrayToValue($input);
            $updateWorkStatus = BudgetDelegateAccessRecord::find($input['id']);
            $updateWorkStatus->work_status = $input['work_status'];
            $updateWorkStatus->save();
            return $this->sendResponse($updateWorkStatus ,"Workstatus update successfully!");
        }
        
        if ($input['type'] == "single") {
            try {
                $request->validate([
                    'budget_planning_detail_id' => 'required|integer|exists:department_budget_planning_details,id',
                    'delegatee_id' => 'required|integer|exists:company_departments_employees,departmentEmployeeSystemID',
                    'submission_time' => 'required|date',
                    'access_permissions' => 'required|array|min:1',
                    'access_permissions.*' => 'string|exists:budget_delegate_access,slug'
                ]);

                $result = $this->budgetDelegateService->createOrUpdateDelegateAccess($request->all());

                if ($result['success']) {
                    return $this->sendResponse($result['data'], $result['message']);
                } else {
                    return $this->sendError($result['message']);
                }
            } catch (Exception $e) {
                return $this->sendError('Error updating delegate access: ' . $e->getMessage());
            }
        }
        else {
            try {
                Validator::validate($input, [
                    'budget_planning_id' => 'required',
                    'delegatee_id' => 'required|array|min:1',
                    'submission_time' => 'required|date',
                    'access_permissions' => 'required|array|min:1',
                    'access_permissions.*' => 'string|exists:budget_delegate_access,slug',
                    'chart_of_accounts' => 'required|array|min:1',
                    'segments' => 'array'
                ]);
                $submissionDate = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d', strtotime($input['submission_time'])));
                // validate submission time is not in the past
                if ($submissionDate->isBefore(today())) {
                    throw new Exception('Submission time cannot be in the past');
                }

                if(Carbon::parse($input['submission_time'])->isSameDay(Carbon::now()))
                {
                    return $this->sendError('Submission date must be less than current submission date and greater than current date');
                }

                $delegateeIds = collect($input['delegatee_id'])->pluck('id')->toArray();
                $chartOfAccounts = collect($input['chart_of_accounts'])->pluck('id')->toArray();
                $segments = collect($input['segments'])->pluck('id')->toArray();

                // Get department budget planning
                $departmentBudgetPlanning = DepartmentBudgetPlanning::with(['workflow', 'masterBudgetPlannings'])
                    ->find($input['budget_planning_id']);

                if (!$departmentBudgetPlanning) {
                    return $this->sendError('Department budget planning not found');
                }

                // validate submission time is not graeter than budget planning detail submission time
                if (\Carbon\Carbon::parse($input['submission_time'])->gt($departmentBudgetPlanning->submissionDate) || Carbon::parse($input['submission_time'])->isSameDay(Carbon::parse($departmentBudgetPlanning->submissionDate))) {
                    return $this->sendError('Submission date must be less than the current submission date and greater than current date');
                }

                // Check if this is segment-based or GL-based
                $isSegmentBased = $departmentBudgetPlanning->workflow->method == 1;

                // Process all delegations in batch to minimize database calls
                $data = $this->processBatchDelegation(
                    $departmentBudgetPlanning,
                    $delegateeIds,
                    $segments,
                    $chartOfAccounts,
                    $input['submission_time'],
                    $input['access_permissions'],
                    $isSegmentBased
                );

                if ($data['success']) {
                    return $this->sendResponse(null, 'All delegations processed successfully.');
                }
                else {
                    throw new Exception($data['message']);
                }
            } catch (Exception $e) {
                return $this->sendError('Error processing multiple delegations: ' . $e->getMessage());
            }
        }
    }

    private function processBatchDelegation($departmentBudgetPlanning, $delegateeIds, $segments, $chartOfAccounts, $submissionTime, $accessPermissions, $isSegmentBased)
    {
        try {
            $budgetDetailsQuery = DepartmentBudgetPlanningDetail::where('department_planning_id', $departmentBudgetPlanning->id);
            
            if ($isSegmentBased) {
                // Segment-based: Filter by both segments and GLs
                $budgetDetailsQuery->whereIn('department_segment_id', $segments)
                                  ->whereIn('budget_template_gl_id', $chartOfAccounts);
            } else {
                // GL-based: Filter only by GLs
                $budgetDetailsQuery->whereIn('budget_template_gl_id', $chartOfAccounts);
            }
            
            $budgetDetails = $budgetDetailsQuery->get();

            if (!$budgetDetails->isEmpty()) {
                $existingRecords = BudgetDelegateAccessRecord::whereIn('budget_planning_detail_id', $budgetDetails->pluck('id'))
                    ->whereIn('delegatee_id', $delegateeIds)
                    ->get()
                    ->groupBy('budget_planning_detail_id')
                    ->map(function ($records) {
                        return $records->pluck('delegatee_id')->toArray();
                    });

                $recordsToCreate = [];

                // Process each budget detail and delegatee combination
                foreach ($budgetDetails as $budgetDetail) {
                    foreach ($delegateeIds as $delegateeId) {
                        // Check if delegatee already exists
                        $delegateeExists = $existingRecords->get($budgetDetail->id, []);

                        if (!in_array($delegateeId, $delegateeExists)) {
                            // Prepare record data for batch creation
                            $recordData = [
                                'budget_planning_detail_id' => $budgetDetail->id,
                                'delegatee_id' => $delegateeId,
                                'submission_time' => \Carbon\Carbon::parse($submissionTime)->format('Y-m-d'),
                                'access_permissions' => json_encode($accessPermissions),
                                'created_by' => Auth::id(),
                                'created_at' => \Carbon\Carbon::now(),
                                'updated_at' => \Carbon\Carbon::now()
                            ];

                            $recordsToCreate[] = $recordData;
                        }
                    }
                }

                foreach ($recordsToCreate as $record) {
                    BudgetDelegateAccessRecord::create($record);
                }

                return [
                    'success' => true,
                    'message' => 'All delegations processed successfully.'
                ];
            }

            return [
                'success' => false,
                'message' => 'Budget details not found.'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Remove delegate access
     *
     * @param Request $request
     * @return Response
     */
    public function removeDelegateAccess(Request $request)
    {
        try {
            $request->validate([
                'record_id' => 'required|integer|exists:dep_budget_pl_delegate_details,id'
            ]);

            $result = $this->budgetDelegateService->removeDelegateAccess($request->record_id);


            if ($result['success']) {
                return $this->sendResponse(null, $result['message']);
            } else {
                return $this->sendError($result['message']);
            }
        } catch (Exception $e) {
            return $this->sendError('Error removing delegate access: ' . $e->getMessage());
        }
    }

    /**
     * Get delegate access summary
     *
     * @param Request $request
     * @return Response
     */
    public function getDelegateAccessSummary(Request $request)
    {
        try {
            $request->validate([
                'budget_planning_detail_id' => 'required|integer|exists:department_budget_planning_details,id'
            ]);

            $summary = $this->budgetDelegateService->getDelegateAccessSummary($request->budget_planning_detail_id);
            
            return $this->sendResponse($summary, 'Delegate access summary retrieved successfully');
        } catch (Exception $e) {
            return $this->sendError('Error retrieving delegate access summary: ' . $e->getMessage());
        }
    }


    public function getBudgetDelegateFormData(Request $request)
    {
        $data = $request->all();
        $departmentID = $data['department_id'];
        $budgetPlanningId = $data['budget_planning_id'];
        $budgetPlanningDetailId = $data['budget_planning_detail_id'] ?? 0;

        if (!isset($data['department_id'])) {
            return $this->sendError('Department ID is required');
        }

        if (!isset($data['budget_planning_id'])) {
            return $this->sendError('Budget Planning ID is required');
        }

        try {

            // Get the specific budget planning detail with assigned employees
            $budgetPlanningDetail = DepartmentBudgetPlanningDetail::with([
                'budgetDelegateAccessDetails.delegatee' => function ($q) {
                    $q->select('departmentEmployeeSystemID', 'employeeSystemID');
                }
            ])->where('id', $budgetPlanningDetailId)->first();

            // Get assigned employees directly from the budget planning detail
            $assignedEmployees = $budgetPlanningDetail;

            

            $accessTypes = $this->budgetDelegateService->getActiveAccessTypes();
            $allEmployees = $this->budgetDelegateService->getDepartmentEmployees($departmentID);

            // Extract already assigned employeeSystemID values from the budget planning detail
            $assignedEmployeeSystemIDs = collect();
            if ($assignedEmployees && $assignedEmployees->budgetDelegateAccessDetails) {
                foreach ($assignedEmployees->budgetDelegateAccessDetails as $delegateAccess) {
                    if ($delegateAccess->delegatee) {
                        $assignedEmployeeSystemIDs->push($delegateAccess->delegatee->employeeSystemID);
                    }
                }
            }
            $assignedEmployeeSystemIDs = $assignedEmployeeSystemIDs->unique()->values()->toArray();

            // Filter employees to exclude already assigned ones
            $employees = $allEmployees->filter(function ($employee) use ($assignedEmployeeSystemIDs) {
                return !in_array($employee->employeeSystemID, $assignedEmployeeSystemIDs) && ($employee->employeeSystemID != Helper::getEmployeeSystemID());
            })->values();

            if ($data['type'] == 0) {
                $data = [
                    'accessTypes' => $accessTypes,
                    'employees' => $employees
                ];
            }
            else {
                $chartOfAccounts = DepartmentBudgetPlanningDetail::where('department_planning_id', $budgetPlanningId)->with('budgetTemplateGl.chartOfAccount')->whereHas('budgetTemplateGl.chartOfAccount')->groupBy('budget_template_gl_id')->get();
                $segments = DepartmentBudgetPlanningDetail::where('department_planning_id', $budgetPlanningId)->with('departmentSegment.segment')->whereHas('departmentSegment.segment')->groupBy('department_segment_id')->get();

                $data = [
                    'accessTypes' => $accessTypes,
                    'employees' => $employees,
                    'chartOfAccounts' => $chartOfAccounts,
                    'segments' => $segments
                ];
            }

            return $this->sendResponse($data, 'Data retrieved successfully');
        } catch (Exception $e) {
            return $this->sendError('Error retrieving data: ' . $e->getMessage());
        }
    }

    public function updateDelegateStatus(Request $request)
    {
        $input = $request->all();

        if (!isset($input['delegate_id'])) {
            return $this->sendError('Delegate ID is required');
        }

        $data = BudgetDelegateAccessRecord::find($input['delegate_id']);
        if (!$data) {
            return $this->sendError('Delegate not found');
        }

        $data->status = $input['new_status'];
        $data->save();

        return $this->sendResponse($data->refresh()->toArray(), 'Delegate status updated successfully');
    }

    public function updateDelegateAcccessStatus(Request $request)
    {
        $input = $request->all();

        if(empty($input['id']))
        {
            return $this->sendError("Data not found!",500);
        }

        $budgetDelegateAccessRecord = BudgetDelegateAccessRecord::find($input['id']);

        $actions = json_decode($budgetDelegateAccessRecord->access_permissions);
        $actions = array_filter($actions, function ($item) use ($input) {
            return $item !== $input['permission'];
        });

        $actions = array_values($actions);

        $budgetDelegateAccessRecord->access_permissions = json_encode($actions);
        $budgetDelegateAccessRecord->save();
        return $this->sendResponse($budgetDelegateAccessRecord->refresh()->toArray(), 'Delegate status updated successfully');
    }

    public function deleteDelegateAccess(Request $request)
    {
        $input = $request->all();
        if(empty($input['id']))
        {
            return $this->sendError("Data not found!",500);
        }

        $budgetDelegateAccessRecord = BudgetDelegateAccessRecord::find($input['id']);

        if($budgetDelegateAccessRecord)
        {
            $budgetDelegateAccessRecord->delete();
            return $this->sendResponse([], 'Delegate deleted successfully');
        }else {
            return $this->sendError("Data not found!",500);

        }

    }
} 
