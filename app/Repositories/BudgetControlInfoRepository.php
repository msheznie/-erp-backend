<?php

namespace App\Repositories;

use App\Models\BudgetControlInfo;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BudgetControlInfoRepository
 * @package App\Repositories
 * @version August 19, 2025, 3:55 pm +04
 *
 * @method BudgetControlInfo findWithoutFail($id, $columns = ['*'])
 * @method BudgetControlInfo find($id, $columns = ['*'])
 * @method BudgetControlInfo first($columns = ['*'])
*/
class BudgetControlInfoRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'controlName',
        'controlType',
        'createdPCID',
        'createdUserSystemID',
        'definedBehavior',
        'ignoreBudget',
        'ignoreGl',
        'isChecked',
        'modifiedPCID',
        'modifiedUserSystemID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BudgetControlInfo::class;
    }
}
