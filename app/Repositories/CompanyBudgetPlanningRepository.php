<?php

namespace App\Repositories;

use App\Models\CompanyBudgetPlanning;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CompanyBudgetPlanningRepository
 * @package App\Repositories
 * @version July 31, 2025, 10:43 am +04
 *
 * @method CompanyBudgetPlanning findWithoutFail($id, $columns = ['*'])
 * @method CompanyBudgetPlanning find($id, $columns = ['*'])
 * @method CompanyBudgetPlanning first($columns = ['*'])
*/
class CompanyBudgetPlanningRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'initiatedDate',
        'periodID',
        'yearID',
        'typeID',
        'submissionDate',
        'workflowID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CompanyBudgetPlanning::class;
    }
}
