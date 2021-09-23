<?php

namespace App\Repositories;

use App\Models\LeaveGroupDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class LeaveGroupDetailsRepository
 * @package App\Repositories
 * @version September 21, 2021, 10:03 am +0530
 *
 * @method LeaveGroupDetails findWithoutFail($id, $columns = ['*'])
 * @method LeaveGroupDetails find($id, $columns = ['*'])
 * @method LeaveGroupDetails first($columns = ['*'])
*/
class LeaveGroupDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'leaveGroupID',
        'leaveTypeID',
        'policyMasterID',
        'isDailyBasisAccrual',
        'noOfDays',
        'isAllowminus',
        'isAllowminusdays',
        'isCalenderDays',
        'stretchDays',
        'isCarryForward',
        'maxCarryForward',
        'maxOccurrenceYN',
        'noofOccurrence',
        'timestamp',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'noOfHours',
        'noOfHourscompleted'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LeaveGroupDetails::class;
    }
}
