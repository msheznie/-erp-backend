<?php

namespace App\Repositories;

use App\Models\DocumentRestrictionAssign;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DocumentRestrictionAssignRepository
 * @package App\Repositories
 * @version December 14, 2018, 4:50 am UTC
 *
 * @method DocumentRestrictionAssign findWithoutFail($id, $columns = ['*'])
 * @method DocumentRestrictionAssign find($id, $columns = ['*'])
 * @method DocumentRestrictionAssign first($columns = ['*'])
*/
class DocumentRestrictionAssignRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentRestrictionPolicyID',
        'documentSystemID',
        'documentID',
        'companySystemID',
        'companyID',
        'userGroupID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentRestrictionAssign::class;
    }
}
