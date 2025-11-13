<?php

namespace App\Repositories;

use App\Models\Revision;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class RevisionRepository
 * @package App\Repositories
 * @version January 15, 2025
 */

class RevisionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'revisionId',
        'budgetPlanningId',
        'submittedBy',
        'submittedDate',
        'reviewComments',
        'revisionType',
        'reopenEditableSection',
        'revisionStatus',
        'sentDateTime',
        'completionComments',
        'completedDateTime',
        'created_by',
        'modified_by'
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
        return Revision::class;
    }

    /**
     * Find revisions by budget planning ID
     *
     * @param int $budgetPlanningId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByBudgetPlanningId($budgetPlanningId)
    {
        return $this->model->where('budgetPlanningId', $budgetPlanningId)
            ->with(['budgetPlanning', 'createdBy', 'modifiedBy', 'attachments'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find active revisions by budget planning ID
     *
     * @param int $budgetPlanningId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findActiveByBudgetPlanningId($budgetPlanningId)
    {
        return $this->model->where('budgetPlanningId', $budgetPlanningId)
            ->where('revisionStatus', 1)
            ->with(['budgetPlanning', 'createdBy', 'attachments'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find revision by revision ID
     *
     * @param string $revisionId
     * @return \App\Models\Revision|null
     */
    public function findByRevisionId($revisionId)
    {
        return $this->model->where('revisionId', $revisionId)
            ->with(['budgetPlanning', 'createdBy', 'modifiedBy', 'attachments'])
            ->first();
    }

    /**
     * Get revisions with pagination
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getRevisionsWithPagination($filters = [], $perPage = 15)
    {
        $query = $this->model->with(['budgetPlanning', 'createdBy', 'modifiedBy']);

        // Apply filters
        if (isset($filters['budgetPlanningId'])) {
            $query->where('budgetPlanningId', $filters['budgetPlanningId']);
        }

        if (isset($filters['revisionStatus'])) {
            $query->where('revisionStatus', $filters['revisionStatus']);
        }

        if (isset($filters['revisionType'])) {
            $query->where('revisionType', $filters['revisionType']);
        }

        if (isset($filters['submittedBy'])) {
            $query->where('submittedBy', 'like', '%' . $filters['submittedBy'] . '%');
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('submittedDate', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('submittedDate', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get revision statistics
     *
     * @param array $filters
     * @return array
     */
    public function getRevisionStatistics($filters = [])
    {
        $query = $this->model;

        // Apply filters
        if (isset($filters['budgetPlanningId'])) {
            $query->where('budgetPlanningId', $filters['budgetPlanningId']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('submittedDate', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('submittedDate', '<=', $filters['date_to']);
        }

        return [
            'total' => $query->count(),
            'active' => $query->where('revisionStatus', 1)->count(),
            'completed' => $query->where('revisionStatus', 2)->count(),
            'cancelled' => $query->where('revisionStatus', 3)->count(),
            'by_type' => $query->selectRaw('revisionType, COUNT(*) as count')
                ->groupBy('revisionType')
                ->pluck('count', 'revisionType')
                ->toArray()
        ];
    }

    /**
     * Create revision with validation
     *
     * @param array $attributes
     * @return \App\Models\Revision
     * @throws \Exception
     */
    public function createRevision($attributes)
    {
        // Check if budget planning exists
        $budgetPlanning = \App\Models\DepartmentBudgetPlanning::find($attributes['budgetPlanningId']);
        if (!$budgetPlanning) {
            throw new \Exception('Budget Planning not found');
        }

        // Check if there's already an active revision for this budget planning
        $activeRevision = $this->model->where('budgetPlanningId', $attributes['budgetPlanningId'])
            ->where('revisionStatus', 1)
            ->first();

        if ($activeRevision) {
            throw new \Exception('There is already an active revision for this budget planning');
        }

        return $this->create($attributes);
    }

    /**
     * Complete revision
     *
     * @param int $id
     * @param array $attributes
     * @return \App\Models\Revision
     * @throws \Exception
     */
    public function completeRevision($id, $attributes = [])
    {
        $revision = $this->find($id);
        
        if (!$revision) {
            throw new \Exception('Revision not found');
        }

        if ($revision->revisionStatus != 1) {
            throw new \Exception('Only active revisions can be completed');
        }

        $attributes['revisionStatus'] = 2; // Completed
        $attributes['completedDateTime'] = now();
        $attributes['modified_at'] = now();

        return $this->update($attributes, $id);
    }

    /**
     * Cancel revision
     *
     * @param int $id
     * @param array $attributes
     * @return \App\Models\Revision
     * @throws \Exception
     */
    public function cancelRevision($id, $attributes = [])
    {
        $revision = $this->find($id);
        
        if (!$revision) {
            throw new \Exception('Revision not found');
        }

        if ($revision->revisionStatus != 1) {
            throw new \Exception('Only active revisions can be cancelled');
        }

        $attributes['revisionStatus'] = 3; // Cancelled
        $attributes['modified_at'] = now();

        return $this->update($attributes, $id);
    }
}
