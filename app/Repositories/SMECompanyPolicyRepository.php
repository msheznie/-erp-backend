<?php

namespace App\Repositories;

use App\Models\SMECompanyPolicy;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SMECompanyPolicyRepository
 * @package App\Repositories
 * @version March 9, 2021, 8:44 am +04
 *
 * @method SMECompanyPolicy findWithoutFail($id, $columns = ['*'])
 * @method SMECompanyPolicy find($id, $columns = ['*'])
 * @method SMECompanyPolicy first($columns = ['*'])
*/
class SMECompanyPolicyRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companypolicymasterID',
        'companyID',
        'code',
        'documentID',
        'isYN',
        'value',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
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
        return SMECompanyPolicy::class;
    }
}
