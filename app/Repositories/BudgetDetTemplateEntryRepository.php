<?php

namespace App\Repositories;

use App\Models\BudgetDetTemplateEntry;

/**
 * Class BudgetDetTemplateEntryRepository
 * @package App\Repositories
 * @version January 15, 2024, 10:00 am UTC
 */

class BudgetDetTemplateEntryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'budget_detail_id',
        'rowNumber',
        'created_by',
        'timestamp'
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
        return BudgetDetTemplateEntry::class;
    }

    /**
     * Get entries by budget detail ID
     *
     * @param int $budgetDetailId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByBudgetDetailId($budgetDetailId)
    {
        return $this->model->forBudgetDetail($budgetDetailId)
            ->with(['entryData.templateColumn.preColumn'])
            ->orderBy('rowNumber', 'asc')
            ->get();
    }

    /**
     * Get entries by budget detail ID with pagination
     *
     * @param int $budgetDetailId
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByBudgetDetailIdPaginated($budgetDetailId, $perPage = 15)
    {
        return $this->model->forBudgetDetail($budgetDetailId)
            ->with(['entryData.templateColumn.preColumn'])
            ->orderBy('rowNumber', 'asc')
            ->paginate($perPage);
    }

    /**
     * Delete entries by budget detail ID
     *
     * @param int $budgetDetailId
     * @return bool
     */
    public function deleteByBudgetDetailId($budgetDetailId)
    {
        return $this->model->forBudgetDetail($budgetDetailId)->delete();
    }

    /**
     * Get entry by budget detail ID and row number
     *
     * @param int $budgetDetailId
     * @param int $rowNumber
     * @return BudgetDetTemplateEntry|null
     */
    public function getByBudgetDetailAndRowNumber($budgetDetailId, $rowNumber)
    {
        return $this->model->forBudgetDetail($budgetDetailId)
            ->byRowNumber($rowNumber)
            ->with(['entryData.templateColumn.preColumn'])
            ->first();
    }
} 