<?php

namespace App\Repositories;

use App\Models\BudgetTransferFormDetailRefferedBack;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BudgetTransferFormDetailRefferedBackRepository
 * @package App\Repositories
 * @version August 13, 2021, 2:28 pm +04
 *
 * @method BudgetTransferFormDetailRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method BudgetTransferFormDetailRefferedBack find($id, $columns = ['*'])
 * @method BudgetTransferFormDetailRefferedBack first($columns = ['*'])
*/
class BudgetTransferFormDetailRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'budgetTransferFormDetailAutoID',
        'budgetTransferFormAutoID',
        'year',
        'timesReferred',
        'isFromContingency',
        'contingencyBudgetID',
        'fromTemplateDetailID',
        'fromServiceLineSystemID',
        'fromServiceLineCode',
        'fromChartOfAccountSystemID',
        'FromGLCode',
        'FromGLCodeDescription',
        'toTemplateDetailID',
        'toServiceLineSystemID',
        'toServiceLineCode',
        'toChartOfAccountSystemID',
        'toGLCode',
        'toGLCodeDescription',
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
        return BudgetTransferFormDetailRefferedBack::class;
    }
}
