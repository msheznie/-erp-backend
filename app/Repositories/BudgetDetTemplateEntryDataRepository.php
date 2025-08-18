<?php

namespace App\Repositories;

use App\Models\BudgetDetTemplateEntryData;

/**
 * Class BudgetDetTemplateEntryDataRepository
 * @package App\Repositories
 * @version January 15, 2024, 10:00 am UTC
 */

class BudgetDetTemplateEntryDataRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'entryID',
        'templateColumnID',
        'value',
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
        return BudgetDetTemplateEntryData::class;
    }

    /**
     * Get data by entry ID
     *
     * @param int $entryID
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByEntryId($entryID)
    {
        return $this->model->forEntry($entryID)
            ->with(['templateColumn.preColumn'])
            ->orderBy('templateColumnID', 'asc')
            ->get();
    }

    /**
     * Get data by template column ID
     *
     * @param int $templateColumnID
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByTemplateColumnId($templateColumnID)
    {
        return $this->model->forTemplateColumn($templateColumnID)
            ->with(['entry.budgetDetail'])
            ->get();
    }

    /**
     * Delete data by entry ID
     *
     * @param int $entryID
     * @return bool
     */
    public function deleteByEntryId($entryID)
    {
        return $this->model->forEntry($entryID)->delete();
    }

    /**
     * Update or create data for a specific entry and template column
     *
     * @param int $entryID
     * @param int $templateColumnID
     * @param string $value
     * @return BudgetDetTemplateEntryData
     */
    public function updateOrCreate($entryID, $templateColumnID, $value)
    {
        return $this->model->updateOrCreate(
            [
                'entryID' => $entryID,
                'templateColumnID' => $templateColumnID
            ],
            [
                'value' => $value,
                'timestamp' => now()
            ]
        );
    }

    /**
     * Get data by multiple entry IDs
     *
     * @param array $entryIDs
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByEntryIds($entryIDs)
    {
        return $this->model->whereIn('entryID', $entryIDs)
            ->with(['templateColumn.preColumn'])
            ->orderBy('entryID', 'asc')
            ->orderBy('templateColumnID', 'asc')
            ->get();
    }
} 