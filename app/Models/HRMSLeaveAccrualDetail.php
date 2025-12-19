<?php
/**
 * =============================================
 * -- File Name : HRMSLeaveAccrualDetail.php
 * -- Project Name : ERP
 * -- Module Name : Leave Application
 * -- Author : Mohamed Rilwan
 * -- Create date : 19- November 2019
 * -- Description :
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="HRMSLeaveAccrualDetail",
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
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="empSystemID",
 *          description="empSystemID",
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
 *          property="schedulemasterID",
 *          description="schedulemasterID",
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
 *          property="dateAssumed",
 *          description="dateAssumed",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="daysEntitled",
 *          description="daysEntitled",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="startDate",
 *          description="startDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="endDate",
 *          description="endDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="manualAccuralYN",
 *          description="manualAccuralYN",
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
 *      )
 * )
 */
class HRMSLeaveAccrualDetail extends Model
{

    public $table = 'hrms_leaveaccrualdetail';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
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
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'leaveaccrualDetailID' => 'integer',
        'leaveaccrualMasterID' => 'integer',
        'empID' => 'string',
        'empSystemID' => 'integer',
        'leavePeriod' => 'integer',
        'schedulemasterID' => 'integer',
        'leaveType' => 'integer',
        'dateAssumed' => 'datetime',
        'daysEntitled' => 'float',
        'description' => 'string',
        'startDate' => 'datetime',
        'endDate' => 'datetime',
        'manualAccuralYN' => 'integer',
        'createDate' => 'datetime',
        'createdUserGroup' => 'string',
        'createdPCid' => 'string',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
      //  'leaveaccrualDetailID' => 'required'
    ];

    public function master(){
        return $this->belongsTo('App\Models\HRMSLeaveAccrualMaster', 'leaveaccrualMasterID', 'leaveaccrualMasterID');
    }

    public function leave_master()
    {
        return $this->belongsTo('App\Models\LeaveMaster','leaveType','leavemasterID');
    }

    public function period()
    {
        return $this->belongsTo('App\Models\HRMSPeriodMaster','leavePeriod','periodMasterID');
    }
}
