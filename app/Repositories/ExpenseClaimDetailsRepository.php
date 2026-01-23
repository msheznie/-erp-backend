<?php

namespace App\Repositories;

use App\Models\ExpenseClaimDetails;
use App\Repositories\BaseRepository;

/**
 * Class ExpenseClaimDetailsRepository
 * @package App\Repositories
 * @version September 10, 2018, 6:06 am UTC
 *
 * @method ExpenseClaimDetails findWithoutFail($id, $columns = ['*'])
 * @method ExpenseClaimDetails find($id, $columns = ['*'])
 * @method ExpenseClaimDetails first($columns = ['*'])
*/
class ExpenseClaimDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'expenseClaimMasterAutoID',
        'companyID',
        'serviceLineCode',
        'expenseClaimCategoriesAutoID',
        'description',
        'docRef',
        'amount',
        'comments',
        'glCode',
        'glCodeDescription',
        'currencyID',
        'currencyER',
        'localCurrency',
        'localCurrencyER',
        'localAmount',
        'comRptCurrency',
        'comRptCurrencyER',
        'comRptAmount',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ExpenseClaimDetails::class;
    }
}
