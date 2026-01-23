<?php

namespace App\Repositories;

use App\Models\HRMSLeaveAccrualMaster;
use App\Repositories\BaseRepository;

/**
 * Class HRMSLeaveAccrualMasterRepository
 * @package App\Repositories
 * @version November 18, 2019, 3:50 pm +04
 *
 * @method HRMSLeaveAccrualMaster findWithoutFail($id, $columns = ['*'])
 * @method HRMSLeaveAccrualMaster find($id, $columns = ['*'])
 * @method HRMSLeaveAccrualMaster first($columns = ['*'])
*/
class HRMSLeaveAccrualMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyID',
        'companySystemID',
        'leaveaccrualMasterCode',
        'documentID',
        'documentSystemID',
        'serialNo',
        'Description',
        'Year',
        'leavePeriod',
        'leaveType',
        'salaryProcessMasterID',
        'confirmedYN',
        'confirmedby',
        'confirmedDate',
        'approvedYN',
        'approvedby',
        'approvedDate',
        'manualAccuralYN',
        'createdUserGroup',
        'createDate',
        'createdpc',
        'modifieduser',
        'modifiedpc',
        'RollLevForApp_curr',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HRMSLeaveAccrualMaster::class;
    }
}
