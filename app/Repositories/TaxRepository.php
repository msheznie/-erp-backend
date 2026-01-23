<?php

namespace App\Repositories;

use App\Models\Tax;
use App\Repositories\BaseRepository;

/**
 * Class TaxRepository
 * @package App\Repositories
 * @version April 19, 2018, 5:03 am UTC
 *
 * @method Tax findWithoutFail($id, $columns = ['*'])
 * @method Tax find($id, $columns = ['*'])
 * @method Tax first($columns = ['*'])
*/
class TaxRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'taxDescription',
        'taxShortCode',
        'taxType',
        'isActive',
        'authorityAutoID',
        'GLAutoID',
        'currencyID',
        'effectiveFrom',
        'taxReferenceNo',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Tax::class;
    }
}
