<?php

namespace App\Repositories;

use App\Models\TaxAuthority;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TaxAuthorityRepository
 * @package App\Repositories
 * @version April 19, 2018, 5:02 am UTC
 *
 * @method TaxAuthority findWithoutFail($id, $columns = ['*'])
 * @method TaxAuthority find($id, $columns = ['*'])
 * @method TaxAuthority first($columns = ['*'])
*/
class TaxAuthorityRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'authoritySystemCode',
        'authoritySecondaryCode',
        'serialNo',
        'AuthorityName',
        'currencyID',
        'telephone',
        'email',
        'fax',
        'address',
        'taxPayableGLAutoID',
        'companySystemID',
        'companyID',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdUserName',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'modifiedDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TaxAuthority::class;
    }
}
