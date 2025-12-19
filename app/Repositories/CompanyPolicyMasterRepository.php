<?php

namespace App\Repositories;

use App\Models\CompanyPolicyMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CompanyPolicyMasterRepository
 * @package App\Repositories
 * @version March 28, 2018, 9:04 am UTC
 *
 * @method CompanyPolicyMaster findWithoutFail($id, $columns = ['*'])
 * @method CompanyPolicyMaster find($id, $columns = ['*'])
 * @method CompanyPolicyMaster first($columns = ['*'])
*/
class CompanyPolicyMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyPolicyCategoryID',
        'companySystemID',
        'companyID',
        'documentID',
        'isYesNO',
        'policyValue',
        'createdByUserID',
        'createdByUserName',
        'createdByPCID',
        'modifiedByUserID',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CompanyPolicyMaster::class;
    }
}
