<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="LeaveAccrualDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="leaveaccrualDetailID",
 *          description="leaveaccrualDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="leaveaccrualMasterID",
 *          description="leaveaccrualMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="empID",
 *          description="empID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="leavePeriod",
 *          description="leavePeriod",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="leaveGroupID",
 *          description="leaveGroupID",
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
 *          property="daysEntitled",
 *          description="daysEntitled",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="hoursEntitled",
 *          description="hoursEntitled",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="previous_balance",
 *          description="previous_balance",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="carryForwardDays",
 *          description="carryForwardDays",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="maxCarryForwardDays",
 *          description="maxCarryForwardDays",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="calendarHolidayID",
 *          description="calendarHolidayID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="leaveMasterID",
 *          description="Accrued from calendar holiday",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="cancelledLeaveMasterID",
 *          description="Accrual from leave cancel",
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
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPCid",
 *          description="createdPCid",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="manualYN",
 *          description="manualYN",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="initalDate",
 *          description="initalDate",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="nextDate",
 *          description="nextDate",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="policyMasterID",
 *          description="policyMasterID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class LeaveAccrualDetail extends Model
{

    public $table = 'srp_erp_leaveaccrualdetail';
    
    const CREATED_AT = 'createDate';
    const UPDATED_AT = 'timestamp';




    public $fillable = [
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
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'leaveaccrualDetailID' => 'integer',
        'leaveaccrualMasterID' => 'integer',
        'empID' => 'integer',
        'leavePeriod' => 'integer',
        'comment' => 'string',
        'leaveGroupID' => 'integer',
        'leaveType' => 'integer',
        'daysEntitled' => 'float',
        'hoursEntitled' => 'float',
        'previous_balance' => 'float',
        'carryForwardDays' => 'float',
        'maxCarryForwardDays' => 'float',
        'description' => 'string',
        'calendarHolidayID' => 'integer',
        'leaveMasterID' => 'integer',
        'cancelledLeaveMasterID' => 'integer',
        'createDate' => 'datetime',
        'createdUserGroup' => 'string',
        'createdPCid' => 'string',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string',
        'timestamp' => 'datetime',
        'manualYN' => 'string',
        'initalDate' => 'integer',
        'nextDate' => 'integer',
        'policyMasterID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
