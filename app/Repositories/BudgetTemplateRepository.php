<?php

namespace App\Repositories;

use App\Models\BudgetTemplate;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BudgetTemplateRepository
 * @package App\Repositories
 * @version January 6, 2024, 12:00 am UTC
*/

class BudgetTemplateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'type',
        'isActive',
        'isDefault',
        'companySystemID'
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
        return BudgetTemplate::class;
    }

    /**
     * Get budget templates query for DataTables
     */
    public function budgetTemplateListQuery($request, $input, $search)
    {
        $query = $this->model->newQuery();

        // Apply company filter
        if (isset($input['companyId']) && $input['companyId']) {
            $query->where('companySystemID', $input['companyId']);
        }

        // Apply type filter
        if (isset($input['type']) && $input['type']) {
            $query->where('type', $input['type']);
        }

        // Apply active status filter
        if (isset($input['isActive']) && $input['isActive'] !== null && $input['isActive'] !== '') {
            $query->where('isActive', $input['isActive']);
        }

        // Apply search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                  ->orWhere('type', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }
} 