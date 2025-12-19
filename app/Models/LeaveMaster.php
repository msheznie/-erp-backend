<?php
/**
 * =============================================
 * -- File Name : LeaveMaster.php
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
 *      definition="LeaveMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="leavemasterID",
 *          description="leavemasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="leaveCode",
 *          description="leaveCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="leavetype",
 *          description="leavetype",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="deductSalary",
 *          description="deductSalary",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="restrictDays",
 *          description="restrictDays",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isAttachmentMandatory",
 *          description="isAttachmentMandatory",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="managerDeadline",
 *          description="managerDeadline",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="maxDays",
 *          description="maxDays",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="allowMultipleLeave",
 *          description="if -1 allow multiple leave Applications",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isProbation",
 *          description="if -1 then check for probation else dont check.",
 *          type="integer",
 *          format="int32"
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
class LeaveMaster extends Model
{

    public $table = 'hrms_leavemaster';
    protected $primaryKey = 'leavemasterID';
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';



    public $fillable = [
        'leaveCode',
        'leavetype',
        'deductSalary',
        'restrictDays',
        'isAttachmentMandatory',
        'managerDeadline',
        'maxDays',
        'allowMultipleLeave',
        'isProbation',
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
        'leavemasterID' => 'integer',
        'leaveCode' => 'string',
        'leavetype' => 'string',
        'deductSalary' => 'integer',
        'restrictDays' => 'integer',
        'isAttachmentMandatory' => 'integer',
        'managerDeadline' => 'integer',
        'maxDays' => 'integer',
        'allowMultipleLeave' => 'integer',
        'isProbation' => 'integer',
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
    ];

    
}
