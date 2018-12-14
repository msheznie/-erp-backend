<?php

namespace App\Repositories;

use App\Models\DocumentRestrictionPolicy;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DocumentRestrictionPolicyRepository
 * @package App\Repositories
 * @version December 14, 2018, 4:52 am UTC
 *
 * @method DocumentRestrictionPolicy findWithoutFail($id, $columns = ['*'])
 * @method DocumentRestrictionPolicy find($id, $columns = ['*'])
 * @method DocumentRestrictionPolicy first($columns = ['*'])
*/
class DocumentRestrictionPolicyRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'documentID',
        'policyDescription'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentRestrictionPolicy::class;
    }
}
