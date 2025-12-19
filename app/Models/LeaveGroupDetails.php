<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="LeaveGroupDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="leaveGroupDetailID",
 *          description="leaveGroupDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="leaveGroupID",
 *          description="leaveGroupID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="leaveTypeID",
 *          description="leaveTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="policyMasterID",
 *          description="policyMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isDailyBasisAccrual",
 *          description="isDailyBasisAccrual",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="noOfDays",
 *          description="noOfDays",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="isAllowminus",
 *          description="isAllowminus",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isAllowminusdays",
 *          description="isAllowminusdays",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isCalenderDays",
 *          description="isCalenderDays",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="stretchDays",
 *          description="stretchDays",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isCarryForward",
 *          description="0- No  1- Yes ",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="maxCarryForward",
 *          description="maxCarryForward",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="maxOccurrenceYN",
 *          description="maxOccurrenceYN",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="noofOccurrence",
 *          description="noofOccurrence",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createdUserName",
 *          description="createdUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="noOfHours",
 *          description="no of hours in minutes",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="noOfHourscompleted",
 *          description="noOfHourscompleted",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class LeaveGroupDetails extends Model
{

    public $table = 'srp_erp_leavegroupdetails';

    protected $primaryKey = 'leaveGroupDetailID';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'modifiedDateTime';




    public $fillable = [
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
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'leaveGroupDetailID' => 'integer',
        'leaveGroupID' => 'integer',
        'leaveTypeID' => 'integer',
        'policyMasterID' => 'integer',
        'isDailyBasisAccrual' => 'boolean',
        'noOfDays' => 'float',
        'isAllowminus' => 'integer',
        'isAllowminusdays' => 'integer',
        'isCalenderDays' => 'integer',
        'stretchDays' => 'integer',
        'isCarryForward' => 'integer',
        'maxCarryForward' => 'float',
        'maxOccurrenceYN' => 'boolean',
        'noofOccurrence' => 'integer',
        'timestamp' => 'datetime',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdDateTime' => 'datetime',
        'createdUserName' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedDateTime' => 'datetime',
        'noOfHours' => 'integer',
        'noOfHourscompleted' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function master(){
        return $this->belongsTo(SMELaveGroup::class, 'leaveGroupID', 'leaveGroupID');
    }
}
