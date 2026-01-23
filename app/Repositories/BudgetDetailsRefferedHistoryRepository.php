<?php

namespace App\Repositories;

use App\Models\BudgetDetailsRefferedHistory;
use App\Repositories\BaseRepository;

/**
 * Class BudgetDetailsRefferedHistoryRepository
 * @package App\Repositories
 * @version August 12, 2021, 12:45 pm +04
 *
 * @method BudgetDetailsRefferedHistory findWithoutFail($id, $columns = ['*'])
 * @method BudgetDetailsRefferedHistory find($id, $columns = ['*'])
 * @method BudgetDetailsRefferedHistory first($columns = ['*'])
*/
class BudgetDetailsRefferedHistoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'budjetDetailsID',
        'budgetmasterID',
        'companySystemID',
        'companyId',
        'companyFinanceYearID',
        'serviceLineSystemID',
        'serviceLine',
        'templateDetailID',
        'chartOfAccountID',
        'glCode',
        'glCodeType',
        'Year',
        'month',
        'budjetAmtLocal',
        'budjetAmtRpt',
        'createdByUserSystemID',
        'createdByUserID',
        'modifiedByUserSystemID',
        'modifiedByUserID',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BudgetDetailsRefferedHistory::class;
    }
}
