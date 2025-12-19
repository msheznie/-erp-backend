<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SMELaveType",
 *      required={""},
 *      @SWG\Property(
 *          property="leaveTypeID",
 *          description="leaveTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="policyID",
 *          description="policyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isPaidLeave",
 *          description="isPaidLeave",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isPlanApplicable",
 *          description="Is leave plan applicable => 1 , not applicable =>0",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isAnnualLeave",
 *          description="isAnnualLeave",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isEmergencyLeave",
 *          description="isEmergencyLeave",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isSickLeave",
 *          description="IF sick leave => 1 else =>0",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isShortLeave",
 *          description="0 - No 1 - Yes ",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="shortLeaveMaxHours",
 *          description="shortLeaveMaxHours",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="shortLeaveMaxMins",
 *          description="shortLeaveMaxMins",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="sortOrder",
 *          description="sortOrder",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="typeConfirmed",
 *          description="typeConfirmed",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyCode",
 *          description="companyCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="integer",
 *          format="int32"
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
 *          property="modifiedUserName",
 *          description="modifiedUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="attachmentRequired",
 *          description="attachmentRequired",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class SMELaveType extends Model
{

    public $table = 'srp_erp_leavetype';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
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
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'leaveTypeID' => 'integer',
        'description' => 'string',
        'policyID' => 'integer',
        'isPaidLeave' => 'integer',
        'isPlanApplicable' => 'integer',
        'isAnnualLeave' => 'integer',
        'isEmergencyLeave' => 'integer',
        'isSickLeave' => 'integer',
        'isShortLeave' => 'integer',
        'shortLeaveMaxHours' => 'float',
        'shortLeaveMaxMins' => 'float',
        'sortOrder' => 'integer',
        'typeConfirmed' => 'integer',
        'companyID' => 'integer',
        'companyCode' => 'string',
        'createdUserGroup' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdDateTime' => 'datetime',
        'createdUserName' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedDateTime' => 'datetime',
        'modifiedUserName' => 'string',
        'timestamp' => 'datetime',
        'attachmentRequired' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
