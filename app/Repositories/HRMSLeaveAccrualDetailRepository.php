<?php

namespace App\Repositories;

use App\Models\HRMSLeaveAccrualDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class HRMSLeaveAccrualDetailRepository
 * @package App\Repositories
 * @version November 18, 2019, 3:53 pm +04
 *
 * @method HRMSLeaveAccrualDetail findWithoutFail($id, $columns = ['*'])
 * @method HRMSLeaveAccrualDetail find($id, $columns = ['*'])
 * @method HRMSLeaveAccrualDetail first($columns = ['*'])
*/
class HRMSLeaveAccrualDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'leaveaccrualMasterID',
        'empID',
        'empSystemID',
        'leavePeriod',
        'schedulemasterID',
        'leaveType',
        'dateAssumed',
        'daysEntitled',
        'description',
        'startDate',
        'endDate',
        'manualAccuralYN',
        'createDate',
        'createdUserGroup',
        'createdPCid',
        'modifiedUser',
        'modifiedPc',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HRMSLeaveAccrualDetail::class;
    }
}
