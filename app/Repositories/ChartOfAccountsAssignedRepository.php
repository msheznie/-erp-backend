<?php

namespace App\Repositories;

use App\Models\ChartOfAccountsAssigned;
use App\Repositories\BaseRepository;

/**
 * Class ChartOfAccountsAssignedRepository
 * @package App\Repositories
 * @version March 27, 2018, 8:53 am UTC
 *
 * @method ChartOfAccountsAssigned findWithoutFail($id, $columns = ['*'])
 * @method ChartOfAccountsAssigned find($id, $columns = ['*'])
 * @method ChartOfAccountsAssigned first($columns = ['*'])
*/
class ChartOfAccountsAssignedRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'chartOfAccountSystemID',
        'AccountCode',
        'AccountDescription',
        'masterAccount',
        'catogaryBLorPLID',
        'catogaryBLorPL',
        'controllAccountYN',
        'controlAccountsSystemID',
        'controlAccounts',
        'companySystemID',
        'companyID',
        'isActive',
        'isAssigned',
        'isBank',
        'AllocationID',
        'relatedPartyYN',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ChartOfAccountsAssigned::class;
    }
}
