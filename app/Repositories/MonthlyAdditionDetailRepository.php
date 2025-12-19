<?php

namespace App\Repositories;

use App\Models\MonthlyAdditionDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class MonthlyAdditionDetailRepository
 * @package App\Repositories
 * @version November 7, 2018, 7:36 am UTC
 *
 * @method MonthlyAdditionDetail findWithoutFail($id, $columns = ['*'])
 * @method MonthlyAdditionDetail find($id, $columns = ['*'])
 * @method MonthlyAdditionDetail first($columns = ['*'])
*/
class MonthlyAdditionDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'monthlyAdditionsMasterID',
        'expenseClaimMasterAutoID',
        'empSystemID',
        'empID',
        'empdepartment',
        'description',
        'declareCurrency',
        'declareAmount',
        'amountMA',
        'currencyMAID',
        'approvedYN',
        'glCode',
        'localCurrencyID',
        'localCurrencyER',
        'localAmount',
        'rptCurrencyID',
        'rptCurrencyER',
        'rptAmount',
        'IsSSO',
        'IsTax',
        'createdpc',
        'createdUserGroup',
        'modifiedUserSystemID',
        'modifieduser',
        'modifiedpc',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MonthlyAdditionDetail::class;
    }
}
