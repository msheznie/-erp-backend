<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SrpErpPayShiftEmployees",
 *      required={""},
 *      @SWG\Property(
 *          property="autoID",
 *          description="autoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="shiftID",
 *          description="FK of srp_erp_pay_shiftmaster tables -shiftID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="empID",
 *          description="FK srp_employeesdetails tables-EIdNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="startDate",
 *          description="startDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="endDate",
 *          description="endDate",
 *          type="string",
 *          format="date"
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
 *      )
 * )
 */
class SrpErpPayShiftEmployees extends Model
{

    public $table = 'srp_erp_pay_shiftemployees';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'shiftID',
        'empID',
        'startDate',
        'endDate',
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
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'autoID' => 'integer',
        'shiftID' => 'integer',
        'empID' => 'integer',
        'startDate' => 'date',
        'endDate' => 'date',
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
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function shift_master(){
        return $this->belongsTo(SrpErpPayShiftMaster::class,'shiftID','shiftID');
    }

    function employee(){
        return $this->belongsTo(SrpEmployeeDetails::class, 'empID', 'EIdNo');
    }
    
}
