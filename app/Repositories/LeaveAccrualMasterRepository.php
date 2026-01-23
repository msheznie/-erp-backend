<?php

namespace App\Repositories;

use App\Models\LeaveAccrualMaster;
use App\Repositories\BaseRepository;

/**
 * Class LeaveAccrualMasterRepository
 * @package App\Repositories
 * @version September 21, 2021, 8:53 pm +0530
 *
 * @method LeaveAccrualMaster findWithoutFail($id, $columns = ['*'])
 * @method LeaveAccrualMaster find($id, $columns = ['*'])
 * @method LeaveAccrualMaster first($columns = ['*'])
*/
class LeaveAccrualMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentID',
        'leaveGroupID',
        'leaveaccrualMasterCode',
        'description',
        'policyMasterID',
        'company_finance_year_id',
        'dailyAccrualYN',
        'dailyAccrualDate',
        'year',
        'manualYN',
        'month',
        'calendarHolidayID',
        'cancelledLeaveMasterID',
        'leaveMasterID',
        'adjustmentType',
        'isHourly',
        'companyID',
        'serialNo',
        'confirmedYN',
        'confirmedby',
        'confirmedDate',
        'approvedYN',
        'approvedby',
        'approvedDate',
        'createdUserID',
        'createdUserGroup',
        'createDate',
        'createdpc',
        'modifieduser',
        'modifiedpc',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LeaveAccrualMaster::class;
    }
}
