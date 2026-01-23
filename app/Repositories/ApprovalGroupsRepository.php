<?php

namespace App\Repositories;

use App\Models\ApprovalGroups;
use App\Repositories\BaseRepository;

/**
 * Class ApprovalGroupsRepository
 * @package App\Repositories
 * @version March 22, 2018, 2:43 pm UTC
 *
 * @method ApprovalGroups findWithoutFail($id, $columns = ['*'])
 * @method ApprovalGroups find($id, $columns = ['*'])
 * @method ApprovalGroups first($columns = ['*'])
*/
class ApprovalGroupsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'rightsGroupDes',
        'isFormsAssigned',
        'documentID',
        'departmentID',
        'condition',
        'sortOrder',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ApprovalGroups::class;
    }
}
