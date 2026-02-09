<?php

namespace App\Repositories;

use App\Models\BudgetTransferFormDetail;
use App\Repositories\BaseRepository;

/**
 * Class BudgetTransferFormDetailRepository
 * @package App\Repositories
 * @version October 17, 2018, 12:26 pm UTC
 *
 * @method BudgetTransferFormDetail findWithoutFail($id, $columns = ['*'])
 * @method BudgetTransferFormDetail find($id, $columns = ['*'])
 * @method BudgetTransferFormDetail first($columns = ['*'])
*/
class BudgetTransferFormDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'budgetTransferFormAutoID',
        'year',
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
        return BudgetTransferFormDetail::class;
    }
}
