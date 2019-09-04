<?php

namespace App\Repositories;

use App\Models\LeaveDataMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class LeaveDataMasterRepository
 * @package App\Repositories
 * @version August 29, 2019, 12:56 pm +04
 *
 * @method LeaveDataMaster findWithoutFail($id, $columns = ['*'])
 * @method LeaveDataMaster find($id, $columns = ['*'])
 * @method LeaveDataMaster first($columns = ['*'])
*/
class LeaveDataMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'empID',
        'EntryType',
        'managerAttached',
        'SeniorManager',
        'designatiomID',
        'location',
        'leaveType',
        'scheduleMasterID',
        'leaveDataMasterCode',
        'documentID',
        'serialNo',
        'createDate',
        'CompanyID',
        'confirmedYN',
        'confirmedby',
        'confirmedDate',
        'approvedYN',
        'approvedby',
        'approvedDate',
        'leaveAvailable',
        'policytype',
        'isPicked',
        'modifieduser',
        'modifiedpc',
        'createduserGroup',
        'createdpc',
        'timestamp',
        'RollLevForApp_curr',
        'hrapprovalYN',
        'hrapprovedby',
        'hrapprovedDate',
        'claimedYN',
        'claimedLeavedatamasterID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LeaveDataMaster::class;
    }
}
