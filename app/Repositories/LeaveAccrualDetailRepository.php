<?php

namespace App\Repositories;

use App\Models\LeaveAccrualDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class LeaveAccrualDetailRepository
 * @package App\Repositories
 * @version September 21, 2021, 8:54 pm +0530
 *
 * @method LeaveAccrualDetail findWithoutFail($id, $columns = ['*'])
 * @method LeaveAccrualDetail find($id, $columns = ['*'])
 * @method LeaveAccrualDetail first($columns = ['*'])
*/
class LeaveAccrualDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'leaveaccrualMasterID',
        'empID',
        'leavePeriod',
        'comment',
        'leaveGroupID',
        'leaveType',
        'daysEntitled',
        'hoursEntitled',
        'previous_balance',
        'carryForwardDays',
        'maxCarryForwardDays',
        'description',
        'calendarHolidayID',
        'leaveMasterID',
        'cancelledLeaveMasterID',
        'createDate',
        'createdUserGroup',
        'createdPCid',
        'modifiedUser',
        'modifiedPc',
        'timestamp',
        'manualYN',
        'initalDate',
        'nextDate',
        'policyMasterID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LeaveAccrualDetail::class;
    }
}
