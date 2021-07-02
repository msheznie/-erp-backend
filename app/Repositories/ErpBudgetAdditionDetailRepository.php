<?php

namespace App\Repositories;

use App\Models\ErpBudgetAdditionDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ErpBudgetAdditionDetailRepository
 * @package App\Repositories
 * @version June 30, 2021, 10:38 am +04
 *
 * @method ErpBudgetAdditionDetail findWithoutFail($id, $columns = ['*'])
 * @method ErpBudgetAdditionDetail find($id, $columns = ['*'])
 * @method ErpBudgetAdditionDetail first($columns = ['*'])
*/
class ErpBudgetAdditionDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'budgetTransferFormAutoID',
        'year',
        'fromTemplateDetailID',
        'serviceLineSystemID',
        'serviceLineCode',
        'chartOfAccountSystemID',
        'gLCode',
        'gLCodeDescription',
        'adjustmentAmountLocal',
        'adjustmentAmountRpt',
        'remarks',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ErpBudgetAdditionDetail::class;
    }
}
