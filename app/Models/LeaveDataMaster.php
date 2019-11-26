<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="LeaveDataMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="leavedatamasterID",
 *          description="leavedatamasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="empID",
 *          description="empID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EntryType",
 *          description="EntryType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="managerAttached",
 *          description="managerAttached",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="SeniorManager",
 *          description="SeniorManager",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="designatiomID",
 *          description="designatiomID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="location",
 *          description="location",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="leaveType",
 *          description="leaveType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="scheduleMasterID",
 *          description="scheduleMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="leaveDataMasterCode",
 *          description="leaveDataMasterCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serialNo",
 *          description="serialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createDate",
 *          description="createDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="CompanyID",
 *          description="CompanyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedYN",
 *          description="confirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedby",
 *          description="confirmedby",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedDate",
 *          description="confirmedDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="approvedYN",
 *          description="approvedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedby",
 *          description="approvedby",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedDate",
 *          description="approvedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="leaveAvailable",
 *          description="leaveAvailable",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="policytype",
 *          description="policytype",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isPicked",
 *          description="isPicked",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifieduser",
 *          description="modifieduser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedpc",
 *          description="modifiedpc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createduserGroup",
 *          description="createduserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdpc",
 *          description="createdpc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="hrapprovalYN",
 *          description="hrapprovalYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="hrapprovedby",
 *          description="hrapprovedby",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="hrapprovedDate",
 *          description="hrapprovedDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="claimedYN",
 *          description="claimedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="claimedLeavedatamasterID",
 *          description="claimedLeavedatamasterID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class LeaveDataMaster extends Model
{

    public $table = 'hrms_leavedatamaster';
    protected $primaryKey = 'leavedatamasterID';
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';



    public $fillable = [
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
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'leavedatamasterID' => 'integer',
        'empID' => 'string',
        'EntryType' => 'integer',
        'managerAttached' => 'string',
        'SeniorManager' => 'string',
        'designatiomID' => 'integer',
        'location' => 'integer',
        'leaveType' => 'integer',
        'scheduleMasterID' => 'integer',
        'leaveDataMasterCode' => 'string',
        'documentID' => 'string',
        'serialNo' => 'integer',
        'createDate' => 'datetime',
        'CompanyID' => 'string',
        'confirmedYN' => 'integer',
        'confirmedby' => 'string',
        'confirmedDate' => 'date',
        'approvedYN' => 'integer',
        'approvedby' => 'string',
        'approvedDate' => 'datetime',
        'leaveAvailable' => 'float',
        'policytype' => 'integer',
        'isPicked' => 'integer',
        'modifieduser' => 'string',
        'modifiedpc' => 'string',
        'createduserGroup' => 'string',
        'createdpc' => 'string',
        'timestamp' => 'datetime',
        'RollLevForApp_curr' => 'integer',
        'hrapprovalYN' => 'integer',
        'hrapprovedby' => 'string',
        'hrapprovedDate' => 'date',
        'claimedYN' => 'integer',
        'claimedLeavedatamasterID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
//        'leavedatamasterID' => 'required'
    ];

    public function detail()
    {
        return $this->hasOne('App\Models\LeaveDataDetail','leavedatamasterID','leavedatamasterID');
    }

    public function application_type()
    {
        return $this->belongsTo('App\Models\LeaveApplicationType','EntryType','LeaveApplicationTypeID');
    }

    public function approved()
    {
        return $this->belongsTo('App\Models\Employee','approvedby','empID');
    }

    public function hrapproved()
    {
        return $this->belongsTo('App\Models\Employee','hrapprovedby','empID');
    }

    public function leave_type()
    {
        return $this->belongsTo('App\Models\LeaveMaster','leaveType','leavemasterID');
    }

    public function employee(){
        return $this->belongsTo('App\Models\Employee','empID','empID');
    }

    public function policy(){
        return $this->belongsTo('App\Models\HRMSLeaveAccrualPolicyType','policytype','leaveaccrualpolicyTypeID');
    }


}
