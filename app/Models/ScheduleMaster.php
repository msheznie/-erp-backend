<?php
/**
 * =============================================
 * -- File Name : ScheduleMaster.php
 * -- Project Name : ERP
 * -- Module Name : Leave Application
 * -- Author : Mohamed Rilwan
 * -- Create date : 01 - September 2019
 * -- Description :
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ScheduleMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="schedulemasterID",
 *          description="schedulemasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="scheduleCode",
 *          description="scheduleCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="scheduleDescription",
 *          description="scheduleDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="leavesEntitled",
 *          description="leavesEntitled",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="noofTickets",
 *          description="noofTickets",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="calculateCalendarDays",
 *          description="-1 = calculated the leave in calender days , 0 - working days only",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="is13MonthApplicable",
 *          description="is13MonthApplicable",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="createDate",
 *          description="createDate",
 *          type="string",
 *          format="date"
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
class ScheduleMaster extends Model
{

    public $table = 'hrms_schedulemaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'scheduleCode',
        'scheduleDescription',
        'leavesEntitled',
        'noofTickets',
        'calculateCalendarDays',
        'is13MonthApplicable',
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
        'schedulemasterID' => 'integer',
        'scheduleCode' => 'string',
        'scheduleDescription' => 'string',
        'leavesEntitled' => 'integer',
        'noofTickets' => 'integer',
        'calculateCalendarDays' => 'integer',
        'is13MonthApplicable' => 'boolean',
        'createDate' => 'date',
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
        'schedulemasterID' => 'required'
    ];

    
}
