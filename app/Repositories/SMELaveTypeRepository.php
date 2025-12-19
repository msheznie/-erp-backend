<?php

namespace App\Repositories;

use App\Models\SMELaveType;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SMELaveTypeRepository
 * @package App\Repositories
 * @version March 9, 2021, 8:49 am +04
 *
 * @method SMELaveType findWithoutFail($id, $columns = ['*'])
 * @method SMELaveType find($id, $columns = ['*'])
 * @method SMELaveType first($columns = ['*'])
*/
class SMELaveTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'policyID',
        'isPaidLeave',
        'isPlanApplicable',
        'isAnnualLeave',
        'isEmergencyLeave',
        'isSickLeave',
        'isShortLeave',
        'shortLeaveMaxHours',
        'shortLeaveMaxMins',
        'sortOrder',
        'typeConfirmed',
        'companyID',
        'companyCode',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp',
        'attachmentRequired'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SMELaveType::class;
    }
}
