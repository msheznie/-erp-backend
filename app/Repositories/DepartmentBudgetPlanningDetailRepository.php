<?php

namespace App\Repositories;

use App\Models\DepartmentBudgetPlanningDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DepartmentBudgetPlanningDetailRepository
 * @package App\Repositories
 * @version January 2, 2024, 12:00 am UTC
*/

class DepartmentBudgetPlanningDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'department_planning_id',
        'department_segment_id',
        'budget_template_gl_id',
        'request_amount',
        'responsible_person',
        'responsible_person_type',
        'internal_status'
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
        return DepartmentBudgetPlanningDetail::class;
    }

    /**
     * Get details by department planning ID with relationships
     *
     * @param int $departmentPlanningId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByDepartmentPlanningId($departmentPlanningId)
    {
        return $this->model->newQuery()
            ->with([
                'departmentSegment',
                'budgetTemplateGl.generalLedger',
                'responsiblePerson'
            ])
            ->where('department_planning_id', $departmentPlanningId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get summary statistics for a department planning
     *
     * @param int $departmentPlanningId
     * @return object
     */
    public function getSummaryByDepartmentPlanningId($departmentPlanningId)
    {
        return $this->model->newQuery()
            ->where('department_planning_id', $departmentPlanningId)
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
    }

    /**
     * Update internal status for multiple details
     *
     * @param array $detailIds
     * @param int $status
     * @return bool
     */
    public function updateStatusForMultiple(array $detailIds, $status)
    {
        return $this->model->newQuery()
            ->whereIn('id', $detailIds)
            ->update(['internal_status' => $status]);
    }

    /**
     * Get details by status
     *
     * @param int $departmentPlanningId
     * @param int $status
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByStatus($departmentPlanningId, $status)
    {
        return $this->model->newQuery()
            ->with([
                'departmentSegment',
                'budgetTemplateGl.generalLedger',
                'responsiblePerson'
            ])
            ->where('department_planning_id', $departmentPlanningId)
            ->where('internal_status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get details by responsible person
     *
     * @param int $departmentPlanningId
     * @param int $responsiblePersonId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByResponsiblePerson($departmentPlanningId, $responsiblePersonId)
    {
        return $this->model->newQuery()
            ->with([
                'departmentSegment',
                'budgetTemplateGl.generalLedger',
                'responsiblePerson'
            ])
            ->where('department_planning_id', $departmentPlanningId)
            ->where('responsible_person', $responsiblePersonId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Calculate total request amount for a department planning
     *
     * @param int $departmentPlanningId
     * @return float
     */
    public function getTotalRequestAmount($departmentPlanningId)
    {
        return $this->model->newQuery()
            ->where('department_planning_id', $departmentPlanningId)
            ->sum('request_amount');
    }

    /**
     * Get variance analysis data
     *
     * @param int $departmentPlanningId
     * @return array
     */
    public function getVarianceAnalysis($departmentPlanningId)
    {
        $details = $this->model->newQuery()
            ->where('department_planning_id', $departmentPlanningId)
            ->selectRaw('
                SUM(previous_year_budget) as total_previous_year,
                SUM(current_year_budget) as total_current_year,
                SUM(request_amount) as total_request_amount,
                SUM(difference_last_current_year) as total_year_on_year_diff,
                SUM(difference_current_request) as total_request_variance
            ')
            ->first();

        return [
            'previous_year_total' => $details->total_previous_year ?? 0,
            'current_year_total' => $details->total_current_year ?? 0,
            'request_amount_total' => $details->total_request_amount ?? 0,
            'year_on_year_variance' => $details->total_year_on_year_diff ?? 0,
            'request_variance' => $details->total_request_variance ?? 0,
            'year_on_year_percentage' => $details->total_current_year ? 
                (($details->total_year_on_year_diff / $details->total_current_year) * 100) : 0,
            'request_variance_percentage' => $details->total_current_year ? 
                (($details->total_request_variance / $details->total_current_year) * 100) : 0
        ];
    }
}