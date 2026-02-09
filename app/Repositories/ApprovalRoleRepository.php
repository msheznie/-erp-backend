<?php

namespace App\Repositories;

use App\Models\ApprovalRole;
use App\Repositories\BaseRepository;

/**
 * Class ApprovalRoleRepository
 * @package App\Repositories
 * @version March 22, 2018, 1:41 pm UTC
 *
 * @method ApprovalRole findWithoutFail($id, $columns = ['*'])
 * @method ApprovalRole find($id, $columns = ['*'])
 * @method ApprovalRole first($columns = ['*'])
*/
class ApprovalRoleRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'rollDescription',
        'documentSystemID',
        'documentID',
        'companySystemID',
        'companyID',
        'departmentSystemID',
        'departmentID',
        'serviceLineSystemID',
        'serviceLineID',
        'rollLevel',
        'approvalLevelID',
        'approvalGroupID',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ApprovalRole::class;
    }
}
