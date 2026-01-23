<?php

namespace App\Repositories;

use App\Models\SalesPersonTarget;
use App\Repositories\BaseRepository;

/**
 * Class SalesPersonTargetRepository
 * @package App\Repositories
 * @version January 20, 2019, 4:03 pm +04
 *
 * @method SalesPersonTarget findWithoutFail($id, $columns = ['*'])
 * @method SalesPersonTarget find($id, $columns = ['*'])
 * @method SalesPersonTarget first($columns = ['*'])
*/
class SalesPersonTargetRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'salesPersonID',
        'datefrom',
        'dateTo',
        'currencyID',
        'percentage',
        'fromTargetAmount',
        'toTargetAmount',
        'companySystemID',
        'companyID',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'TIMESTAMP'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SalesPersonTarget::class;
    }
}
