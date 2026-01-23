<?php

namespace App\Repositories;

use App\Models\BudgetDetailHistory;
use App\Repositories\BaseRepository;

/**
 * Class BudgetDetailHistoryRepository
 * @package App\Repositories
 * @version June 16, 2021, 11:04 am +04
 *
 * @method BudgetDetailHistory findWithoutFail($id, $columns = ['*'])
 * @method BudgetDetailHistory find($id, $columns = ['*'])
 * @method BudgetDetailHistory first($columns = ['*'])
*/
class BudgetDetailHistoryRepository extends BaseRepository
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
        return BudgetDetailHistory::class;
    }
}
