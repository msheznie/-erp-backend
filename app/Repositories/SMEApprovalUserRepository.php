<?php

namespace App\Repositories;

use App\Models\SMEApprovalUser;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SMEApprovalUserRepository
 * @package App\Repositories
 * @version December 18, 2024, 10:19 am +04
 *
 * @method SMEApprovalUser findWithoutFail($id, $columns = ['*'])
 * @method SMEApprovalUser find($id, $columns = ['*'])
 * @method SMEApprovalUser first($columns = ['*'])
*/
class SMEApprovalUserRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyCode',
        'companyID',
        'createdDateTime',
        'createdPCID',
        'createdUserGroup',
        'createdUserID',
        'createdUserName',
        'delegated_from',
        'delegated_to',
        'delegation_master_id',
        'delegator',
        'deligation_detail_id',
        'designation',
        'document',
        'documentID',
        'employeeID',
        'employeeName',
        'fromAmount',
        'groupID',
        'levelNo',
        'modifiedDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'Status',
        'timestamp',
        'toAmount'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SMEApprovalUser::class;
    }
}
