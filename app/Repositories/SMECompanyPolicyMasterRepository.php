<?php

namespace App\Repositories;

use App\Models\SMECompanyPolicyMaster;
use App\Repositories\BaseRepository;

/**
 * Class SMECompanyPolicyMasterRepository
 * @package App\Repositories
 * @version March 9, 2021, 10:32 am +04
 *
 * @method SMECompanyPolicyMaster findWithoutFail($id, $columns = ['*'])
 * @method SMECompanyPolicyMaster find($id, $columns = ['*'])
 * @method SMECompanyPolicyMaster first($columns = ['*'])
*/
class SMECompanyPolicyMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyPolicyDescription',
        'systemValue',
        'isDocumentLevel',
        'code',
        'documentID',
        'defaultValue',
        'fieldType',
        'is_active',
        'isCompanyLevel',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SMECompanyPolicyMaster::class;
    }
}
