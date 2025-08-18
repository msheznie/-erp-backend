<?php

namespace App\Repositories;

use App\Models\DepartmentBudgetPlanning;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DepartmentBudgetPlanningRepository
 * @package App\Repositories
 * @version July 31, 2025, 10:44 am +04
 *
 * @method DepartmentBudgetPlanning findWithoutFail($id, $columns = ['*'])
 * @method DepartmentBudgetPlanning find($id, $columns = ['*'])
 * @method DepartmentBudgetPlanning first($columns = ['*'])
*/
class DepartmentBudgetPlanningRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyBudgetPlanningID',
        'departmentID',
        'initiatedDate',
        'periodID',
        'yearID',
        'typeID',
        'submissionDate',
        'workflowID',
        'workStatus'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DepartmentBudgetPlanning::class;
    }
}
