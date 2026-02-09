<?php

namespace App\Repositories;

use App\Models\LeaveGroup;
use App\Repositories\BaseRepository;

/**
 * Class LeaveGroupRepository
 * @package App\Repositories
 * @version September 21, 2021, 10:07 am +0530
 *
 * @method LeaveGroup findWithoutFail($id, $columns = ['*'])
 * @method LeaveGroup find($id, $columns = ['*'])
 * @method LeaveGroup first($columns = ['*'])
*/
class LeaveGroupRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'companyID',
        'isMonthly',
        'isDefault',
        'approvalLevels',
        'timestamp',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LeaveGroup::class;
    }
}
