<?php

namespace App\Repositories;

use App\Models\BudgetAdditionDetailRefferedBack;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BudgetAdditionDetailRefferedBackRepository
 * @package App\Repositories
 * @version August 15, 2021, 10:18 am +04
 *
 * @method BudgetAdditionDetailRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method BudgetAdditionDetailRefferedBack find($id, $columns = ['*'])
 * @method BudgetAdditionDetailRefferedBack first($columns = ['*'])
*/
class BudgetAdditionDetailRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'budgetAdditionFormAutoID',
        'year',
        'templateDetailID',
        'serviceLineSystemID',
        'serviceLineCode',
        'budjetDetailsID',
        'chartOfAccountSystemID',
        'gLCode',
        'gLCodeDescription',
        'adjustmentAmountLocal',
        'adjustmentAmountRpt',
        'timesReferred',
        'remarks',
        'timestamp',
        'createdDateTime'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BudgetAdditionDetailRefferedBack::class;
    }
}
