<?php

namespace App\Repositories;

use App\Models\BudgetConsumedData;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BudgetConsumedDataRepository
 * @package App\Repositories
 * @version May 30, 2018, 10:06 am UTC
 *
 * @method BudgetConsumedData findWithoutFail($id, $columns = ['*'])
 * @method BudgetConsumedData find($id, $columns = ['*'])
 * @method BudgetConsumedData first($columns = ['*'])
*/
class BudgetConsumedDataRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'chartOfAccountID',
        'GLCode',
        'year',
        'month',
        'consumedLocalCurrencyID',
        'consumedLocalAmount',
        'consumedRptCurrencyID',
        'consumedRptAmount',
        'consumeYN',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BudgetConsumedData::class;
    }
}
