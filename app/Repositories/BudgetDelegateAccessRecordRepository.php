<?php

namespace App\Repositories;

use App\Models\BudgetDelegateAccessRecord;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BudgetDelegateAccessRecordRepository
 * @package App\Repositories
 * @version January 3, 2024, 12:00 am UTC
*/

class BudgetDelegateAccessRecordRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'budget_planning_detail_id',
        'delegatee_id',
        'submission_time',
        'access_permissions',
        'status',
        'created_by'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BudgetDelegateAccessRecord::class;
    }

    /**
     * Get active records for a budget planning detail
     *
     * @param int $budgetPlanningDetailId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveRecords($budgetPlanningDetailId)
    {
        $records = $this->model()::where('budget_planning_detail_id', $budgetPlanningDetailId)
                              ->with(['delegatee.employee', 'delegatee.department'])
                              ->get();

        return $records->map(function($record) {
            $record->access_permissions = json_decode($record->access_permissions, true);
            return $record;
        });
    }

    /**
     * Get records by delegatee
     *
     * @param int $delegateeId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByDelegatee($delegateeId)
    {
        return $this->model()::where('delegatee_id', $delegateeId)
                              ->where('status', 'active')
                              ->with(['budgetPlanningDetail'])
                              ->get();
    }

    /**
     * Check if delegatee already has access to budget planning detail
     *
     * @param int $budgetPlanningDetailId
     * @param int $delegateeId
     * @return bool
     */
    public function delegateeHasAccess($budgetPlanningDetailId, $delegateeId)
    {
        return $this->model()::where('budget_planning_detail_id', $budgetPlanningDetailId)
                              ->where('delegatee_id', $delegateeId)
                              ->where('status', 'active')
                              ->exists();
    }

    /**
     * Create or update delegate access record
     *
     * @param array $data
     * @return BudgetDelegateAccessRecord
     */
    public function createOrUpdate($data)
    {
        $existingRecord = $this->model()::where('budget_planning_detail_id', $data['budget_planning_detail_id'])
                                        ->where('delegatee_id', $data['delegatee_id'])
                                        ->first();

        if ($existingRecord) {
            $existingRecord->update($data);
            return $existingRecord;
        }

        return $this->create($data);
    }

    /**
     * Get existing record
     *
     * @param int $budgetPlanningDetailId
     * @param int $delegateeId
     * @return BudgetDelegateAccessRecord
     */
    public function getExistingRecord($budgetPlanningDetailId, $delegateeId)
    {
        return $this->model()::where('budget_planning_detail_id', $budgetPlanningDetailId)
                              ->where('delegatee_id', $delegateeId)
                              ->first();
    }
} 