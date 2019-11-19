<?php
/**
 * =============================================
 * -- File Name : HRMSLeaveAccrualMaster.php
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
 *      definition="HRMSLeaveAccrualMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="leaveaccrualMasterID",
 *          description="leaveaccrualMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="leaveaccrualMasterCode",
 *          description="leaveaccrualMasterCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serialNo",
 *          description="serialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="Description",
 *          description="Description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Year",
 *          description="Year",
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
 *          property="leaveType",
 *          description="leaveType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="salaryProcessMasterID",
 *          description="salaryProcessMasterID",
 *          type="integer",
 *          format="int32"
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
 *          format="date-time"
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
 *          property="manualAccuralYN",
 *          description="manualAccuralYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createDate",
 *          description="createDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createdpc",
 *          description="createdpc",
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
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class HRMSLeaveAccrualMaster extends Model
{

    public $table = 'hrms_leaveaccrualmaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'companyID',
        'companySystemID',
        'leaveaccrualMasterCode',
        'documentID',
        'documentSystemID',
        'serialNo',
        'Description',
        'Year',
        'leavePeriod',
        'leaveType',
        'salaryProcessMasterID',
        'confirmedYN',
        'confirmedby',
        'confirmedDate',
        'approvedYN',
        'approvedby',
        'approvedDate',
        'manualAccuralYN',
        'createdUserGroup',
        'createDate',
        'createdpc',
        'modifieduser',
        'modifiedpc',
        'RollLevForApp_curr',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'leaveaccrualMasterID' => 'integer',
        'companyID' => 'string',
        'companySystemID' => 'integer',
        'leaveaccrualMasterCode' => 'string',
        'documentID' => 'string',
        'documentSystemID' => 'integer',
        'serialNo' => 'integer',
        'Description' => 'string',
        'Year' => 'integer',
        'leavePeriod' => 'integer',
        'leaveType' => 'integer',
        'salaryProcessMasterID' => 'integer',
        'confirmedYN' => 'integer',
        'confirmedby' => 'string',
        'confirmedDate' => 'datetime',
        'approvedYN' => 'integer',
        'approvedby' => 'string',
        'approvedDate' => 'datetime',
        'manualAccuralYN' => 'integer',
        'createdUserGroup' => 'string',
        'createDate' => 'datetime',
        'createdpc' => 'string',
        'modifieduser' => 'string',
        'modifiedpc' => 'string',
        'RollLevForApp_curr' => 'integer',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'leaveaccrualMasterID' => 'required'
    ];

    
}
