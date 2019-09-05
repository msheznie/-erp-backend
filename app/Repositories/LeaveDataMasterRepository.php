<?php

namespace App\Repositories;

use App\Models\LeaveDataDetail;
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

    public function updateLeaveDataDetails($data = null)
    {
        if($data != null){

            $leaveMasterID = isset($data['leavemasterID'])?$data['leavemasterID']:null;

            $calculatedDays = 0;
            if($leaveMasterID == 16 || $leaveMasterID == 13 || $leaveMasterID == 2 || $leaveMasterID == 3 || $leaveMasterID == 4 || $leaveMasterID == 21 || $leaveMasterID == 5){
                $calculatedDays = $data['noOfWorkingDays'] + $data['noOfNonWorkingDays'];
            }
            else{
                $calculatedDays = $data['noOfWorkingDays'];
            }

            $update_array = array(
                'leavedatamasterID' =>isset($data['leavedatamasterID'])?$data['leavedatamasterID']:null,
                'leavemasterID' =>isset($data['leavemasterID'])?$data['leavemasterID']:null,
                'startDate' =>isset($data['startDate'])?$data['startDate']:null,
                'endDate' =>isset($data['endDate'])?$data['endDate']:null,
                'noOfWorkingDays' =>isset($data['noOfWorkingDays'])?$data['noOfWorkingDays']:null,
                'noOfNonWorkingDays' =>isset($data['noOfNonWorkingDays'])?$data['noOfNonWorkingDays']:0,
                'totalDays' =>isset($data['totalDays'])?$data['totalDays']:0,
                'calculatedDays' =>isset($data['calculatedDays'])?$data['calculatedDays']:0,
                'comment' =>isset($data['comment'])?$data['comment']:null,
                'modifieduser' =>isset($data['modifieduser'])?$data['modifieduser']:null,
                'modifiedpc' =>isset($data['modifiedpc'])?$data['modifiedpc']:null,
                'endFinalDate' =>isset($data['endFinalDate'])?$data['endFinalDate']:null,
            );
            return LeaveDataDetail::where('leavedatamasterID',$data['leavedatamasterID'])->update($update_array);
        }
        return false;
    }

    public function updateLeaveDataMaster($data = null,$ishrapproval = 0)
    {
        if($data != null){

            if($ishrapproval) {

                $update_array = array(
                    'leaveType' => isset($data['leaveType']) ? $data['leaveType'] : null,
                    'confirmedYN' => isset($data['confirmedYN']) ? $data['confirmedYN'] : null,
                    'confirmedby' => isset($data['confirmedby']) ? $data['confirmedby'] : null,
                    'confirmedDate' => isset($data['confirmedDate']) ? $data['confirmedDate'] : null,
                    'leaveAvailable' => isset($data['leaveAvailable']) ? $data['leaveAvailable'] : null
                );
                return LeaveDataMaster::where('leavedatamasterID', $data['leavedatamasterID'])->update($update_array);
            }else {

                $update_array = array(
                    'leaveType' => isset($data['leaveType']) ? $data['leaveType'] : null,
                    'confirmedYN' => isset($data['confirmedYN']) ? $data['confirmedYN'] : null,
                    'confirmedby' => isset($data['confirmedby']) ? $data['confirmedby'] : null,
                    'confirmedDate' => isset($data['confirmedDate']) ? $data['confirmedDate'] : null,
                    'hrapprovalYN' => -1,
                    'RollLevForApp_curr' => 2
                );
                return LeaveDataMaster::where('leavedatamasterID', $data['leavedatamasterID'])->update($update_array);
            }
            return false;
        }

    }

    public function insertLeaveDataDetails($data = null)
    {
        if($data != null){
            $insert_array = array(
                'leavedatamasterID' =>isset($data['leavedatamasterID'])?$data['leavedatamasterID']:null,
                'leavemasterID' =>isset($data['leavemasterID'])?$data['leavemasterID']:null,
                'startDate' =>isset($data['startDate'])?$data['startDate']:null,
                'endDate' =>isset($data['endDate'])?$data['endDate']:null,
                'noOfWorkingDays' =>isset($data['noOfWorkingDays'])?$data['noOfWorkingDays']:null,
                'noOfNonWorkingDays' =>isset($data['noOfNonWorkingDays'])?$data['noOfNonWorkingDays']:0,
                'totalDays' =>isset($data['totalDays'])?$data['totalDays']:0,
                'calculatedDays' =>isset($data['calculatedDays'])?$data['calculatedDays']:0,
                'comment' =>isset($data['comment'])?$data['comment']:null,
                'modifieduser' =>isset($data['modifieduser'])?$data['modifieduser']:null,
                'modifiedpc' =>isset($data['modifiedpc'])?$data['modifiedpc']:null,
                'claimedDays'=>isset($data['claimedDays'])?$data['claimedDays']:null,
                'endFinalDate' =>isset($data['endFinalDate'])?$data['endFinalDate']:null,
            );
            return LeaveDataDetail::create($insert_array);
        }
        return false;
    }


}
