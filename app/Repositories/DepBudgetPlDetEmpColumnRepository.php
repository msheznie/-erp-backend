<?php

namespace App\Repositories;

use App\Models\DepBudgetPlDetEmpColumn;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DepBudgetPlDetEmpColumnRepository
 * @package App\Repositories
 * @version August 22, 2025, 4:33 am +04
 *
 * @method DepBudgetPlDetEmpColumn findWithoutFail($id, $columns = ['*'])
 * @method DepBudgetPlDetEmpColumn find($id, $columns = ['*'])
 * @method DepBudgetPlDetEmpColumn first($columns = ['*'])
*/
class DepBudgetPlDetEmpColumnRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'empID',
        'columnID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DepBudgetPlDetEmpColumn::class;
    }
}
