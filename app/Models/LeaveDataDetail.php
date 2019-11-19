<?php
/**
 * =============================================
 * -- File Name : LeaveDataDetail.php
 * -- Project Name : ERP
 * -- Module Name : Leave Application
 * -- Author : Mohamed Rilwan
 * -- Create date : 01- September 2019
 * -- Description :
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="LeaveDataDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="leavedatadetailID",
 *          description="leavedatadetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="leavedatamasterID",
 *          description="leavedatamasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="leavemasterID",
 *          description="leavemasterID",
 *          type="integer",
 *          format="int32"
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
 *          property="noOfWorkingDays",
 *          description="noOfWorkingDays",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="noOfNonWorkingDays",
 *          description="noOfNonWorkingDays",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="totalDays",
 *          description="totalDays",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="calculatedDays",
 *          description="Store total applied days according to the selected leavetype",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="startLastHitchDate",
 *          description="startLastHitchDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="endLastHitchDate",
 *          description="endLastHitchDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="startFollowingHitchDate",
 *          description="startFollowingHitchDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="endFollowingHitchDate",
 *          description="endFollowingHitchDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="reportingMangerComment",
 *          description="reportingMangerComment",
 *          type="string"
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
 *          property="claimedDays",
 *          description="claimedDays",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="endFinalDate",
 *          description="endFinalDate",
 *          type="string",
 *          format="date"
 *      )
 * )
 */
class LeaveDataDetail extends Model
{

    public $table = 'hrms_leavedatadetail';
    protected $primaryKey = 'leavedatadetailID';
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    public $fillable = [
        'leavedatamasterID',
        'leavemasterID',
        'startDate',
        'endDate',
        'noOfWorkingDays',
        'noOfNonWorkingDays',
        'totalDays',
        'calculatedDays',
        'startLastHitchDate',
        'endLastHitchDate',
        'startFollowingHitchDate',
        'endFollowingHitchDate',
        'comment',
        'reportingMangerComment',
        'modifieduser',
        'modifiedpc',
        'createduserGroup',
        'createdpc',
        'timestamp',
        'claimedDays',
        'endFinalDate'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'leavedatadetailID' => 'integer',
        'leavedatamasterID' => 'integer',
        'leavemasterID' => 'integer',
        'startDate' => 'datetime',
        'endDate' => 'datetime',
        'noOfWorkingDays' => 'integer',
        'noOfNonWorkingDays' => 'integer',
        'totalDays' => 'integer',
        'calculatedDays' => 'integer',
        'startLastHitchDate' => 'datetime',
        'endLastHitchDate' => 'datetime',
        'startFollowingHitchDate' => 'datetime',
        'endFollowingHitchDate' => 'datetime',
        'comment' => 'string',
        'reportingMangerComment' => 'string',
        'modifieduser' => 'string',
        'modifiedpc' => 'string',
        'createduserGroup' => 'string',
        'createdpc' => 'string',
        'timestamp' => 'datetime',
        'claimedDays' => 'integer',
        'endFinalDate' => 'date'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
    ];

    public function master()
    {
        return $this->belongsTo('App\Models\LeaveDataMaster','leavedatamasterID','leavedatamasterID');
    }

    public function leave_master()
    {
        return $this->belongsTo('App\Models\LeaveMaster','leavemasterID','leavemasterID');
    }
}
