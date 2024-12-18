<?php

namespace App\Repositories;

use App\Models\HrDeligationDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class HrDeligationDetailsRepository
 * @package App\Repositories
 * @version December 18, 2024, 10:26 am +04
 *
 * @method HrDeligationDetails findWithoutFail($id, $columns = ['*'])
 * @method HrDeligationDetails find($id, $columns = ['*'])
 * @method HrDeligationDetails first($columns = ['*'])
*/
class HrDeligationDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'approval_level',
        'approval_role',
        'approval_user_id',
        'comment',
        'delegatee_id',
        'delegation_id',
        'document_id',
        'enabled',
        'module_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HrDeligationDetails::class;
    }
}
