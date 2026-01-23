<?php

namespace App\Repositories;

use App\Models\Budjetdetails;
use App\Repositories\BaseRepository;

/**
 * Class BudjetdetailsRepository
 * @package App\Repositories
 * @version October 16, 2018, 7:11 am UTC
 *
 * @method Budjetdetails findWithoutFail($id, $columns = ['*'])
 * @method Budjetdetails find($id, $columns = ['*'])
 * @method Budjetdetails first($columns = ['*'])
*/
class BudjetdetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        return Budjetdetails::class;
    }
}
