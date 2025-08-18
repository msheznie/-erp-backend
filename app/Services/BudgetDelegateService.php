<?php

namespace App\Services;

use App\Repositories\BudgetDelegateAccessRepository;
use App\Repositories\BudgetDelegateAccessRecordRepository;
use App\Repositories\CompanyDepartmentEmployeeRepository;
use App\Repositories\DepartmentBudgetPlanningDetailRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class BudgetDelegateService
{
    protected $budgetDelegateAccessRepository;
    protected $budgetDelegateAccessRecordRepository;
    protected $companyDepartmentEmployeeRepository;
    protected $departmentBudgetPlanningDetailRepository;

    public function __construct(
        BudgetDelegateAccessRepository $budgetDelegateAccessRepository,
        BudgetDelegateAccessRecordRepository $budgetDelegateAccessRecordRepository,
        CompanyDepartmentEmployeeRepository $companyDepartmentEmployeeRepository,
        DepartmentBudgetPlanningDetailRepository $departmentBudgetPlanningDetailRepository
    ) {
        $this->budgetDelegateAccessRepository = $budgetDelegateAccessRepository;
        $this->budgetDelegateAccessRecordRepository = $budgetDelegateAccessRecordRepository;
        $this->companyDepartmentEmployeeRepository = $companyDepartmentEmployeeRepository;
        $this->departmentBudgetPlanningDetailRepository = $departmentBudgetPlanningDetailRepository;
    }

    /**
     * Get all active access types
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveAccessTypes()
    {
        return $this->budgetDelegateAccessRepository->getActiveAccessTypes();
    }

    /**
     * Get department employees for delegation
     *
     * @param int $departmentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDepartmentEmployees($departmentId)
    {
        return $this->companyDepartmentEmployeeRepository->getDepartmentEmployees($departmentId);
    }

    /**
     * Get existing delegate access records for a budget planning detail
     *
     * @param int $budgetPlanningDetailId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDelegateAccessRecords($budgetPlanningDetailId)
    {
        return $this->budgetDelegateAccessRecordRepository->getActiveRecords($budgetPlanningDetailId);
    }

    /**
     * Create or update delegate access record
     *
     * @param array $data
     * @return array
     */
    public function createOrUpdateDelegateAccess($data)
    {
        try {
            DB::beginTransaction();

            // Validate budget planning detail exists
            $budgetPlanningDetail = $this->departmentBudgetPlanningDetailRepository->find($data['budget_planning_detail_id']);
            if (!$budgetPlanningDetail) {
                throw new Exception('Budget planning detail not found');
            }

            // Validate delegatee exists
            $delegatee = $this->companyDepartmentEmployeeRepository->find($data['delegatee_id']);
            if (!$delegatee) {
                throw new Exception('Delegatee not found');
            }

            // validate delegatee is already assigned to the budget planning detail
            $existingRecord = $this->budgetDelegateAccessRecordRepository->getExistingRecord($data['budget_planning_detail_id'], $data['delegatee_id']);
            if ($existingRecord) {
                throw new Exception('Delegatee already assigned to the budget planning detail');
            }

            // validate submission time is not in the past
            if (\Carbon\Carbon::parse($data['submission_time'])->isPast()) {
                throw new Exception('Submission time cannot be in the past');
            }
            
            // validate submission time is not graeter than budget planning detail submission time
            if (\Carbon\Carbon::parse($data['submission_time'])->gt($budgetPlanningDetail->time_for_submission)) {
                throw new Exception('Submission time cannot be greater than budget planning submission time');
            }

            // Prepare data for creation/update
            $recordData = [
                'budget_planning_detail_id' => $data['budget_planning_detail_id'],
                'delegatee_id' => $data['delegatee_id'],
                'submission_time' => \Carbon\Carbon::parse($data['submission_time'])->format('Y-m-d H:i:s'),
                'access_permissions' => json_encode($data['access_permissions']),
                'created_by' => Auth::id()
            ];

            // Create or update the record
            $record = $this->budgetDelegateAccessRecordRepository->createOrUpdate($recordData);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Delegate access updated successfully',
                'data' => $record
            ];

        } catch (Exception $e) {
            DB::rollback();
            return [
                'success' => false,
                'message' => 'Error updating delegate access: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Remove delegate access
     *
     * @param int $recordId
     * @return array
     */
    public function removeDelegateAccess($recordId)
    {
        try {
            $record = $this->budgetDelegateAccessRecordRepository->find($recordId);
            if (!$record) {
                throw new Exception('Delegate access record not found');
            }

            $record->update(['status' => 'inactive']);

            return [
                'success' => true,
                'message' => 'Delegate access removed successfully'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error removing delegate access: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get delegate access summary for a budget planning detail
     *
     * @param int $budgetPlanningDetailId
     * @return array
     */
    public function getDelegateAccessSummary($budgetPlanningDetailId)
    {
        $records = $this->budgetDelegateAccessRecordRepository->getActiveRecords($budgetPlanningDetailId);
        
        $summary = [
            'total_delegates' => $records->count(),
            'delegates' => $records->map(function ($record) {
                return [
                    'id' => $record->id,
                    'delegatee_name' => $record->delegatee->employee->name ?? 'Unknown',
                    'department' => $record->delegatee->department->departmentName ?? 'Unknown',
                    'submission_time' => $record->submission_time,
                    'access_permissions' => $record->access_permissions,
                    'created_at' => $record->created_at
                ];
            })
        ];

        return $summary;
    }
} 