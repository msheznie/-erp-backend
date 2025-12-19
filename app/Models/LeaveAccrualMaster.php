<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="LeaveAccrualMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="leaveaccrualMasterID",
 *          description="leaveaccrualMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="leaveGroupID",
 *          description="leaveGroupID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="leaveaccrualMasterCode",
 *          description="leaveaccrualMasterCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="policyMasterID",
 *          description="policyMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="company_finance_year_id",
 *          description="company_finance_year_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="dailyAccrualYN",
 *          description="dailyAccrualYN",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="dailyAccrualDate",
 *          description="dailyAccrualDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="year",
 *          description="year",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="manualYN",
 *          description="is leave Adjustment",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="month",
 *          description="month",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="calendarHolidayID",
 *          description="calendarHolidayID",
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
 *          property="leaveMasterID",
 *          description="Accrued from calendar holiday",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="adjustmentType",
 *          description="1=> Standard ,2=> Lieu Leave",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isHourly",
 *          description="isHourly",
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
 *          property="serialNo",
 *          description="serialNo",
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
 *          property="createdUserID",
 *          description="createdUserID",
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
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class LeaveAccrualMaster extends Model
{

    public $table = 'srp_erp_leaveaccrualmaster';

    protected $primaryKey = 'leaveaccrualMasterID';
    
    const CREATED_AT = 'createDate';
    const UPDATED_AT = 'timestamp';




    public $fillable = [
        'documentID',
        'leaveGroupID',
        'leaveaccrualMasterCode',
        'description',
        'policyMasterID',
        'company_finance_year_id',
        'dailyAccrualYN',
        'dailyAccrualDate',
        'year',
        'manualYN',
        'month',
        'calendarHolidayID',
        'cancelledLeaveMasterID',
        'leaveMasterID',
        'adjustmentType',
        'isHourly',
        'companyID',
        'serialNo',
        'accrualPolicyValue',
        'effective_date',
        'confirmedYN',
        'confirmedby',
        'confirmedDate',
        'approvedYN',
        'approvedby',
        'approvedDate',
        'submitYN',
        'submittedBy',
        'submittedDate', 
        'createdUserID',
        'createdUserGroup',
        'createDate',
        'createdpc',
        'modifieduser',
        'modifiedpc',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'leaveaccrualMasterID' => 'integer',
        'documentID' => 'string',
        'leaveGroupID' => 'integer',
        'leaveaccrualMasterCode' => 'string',
        'description' => 'string',
        'policyMasterID' => 'integer',
        'company_finance_year_id' => 'integer',
        'dailyAccrualYN' => 'boolean',
        'dailyAccrualDate' => 'date',
        'year' => 'integer',
        'manualYN' => 'integer',
        'month' => 'integer',
        'calendarHolidayID' => 'integer',
        'cancelledLeaveMasterID' => 'integer',
        'leaveMasterID' => 'integer',
        'adjustmentType' => 'integer',
        'isHourly' => 'integer',
        'companyID' => 'integer',
        'serialNo' => 'integer',
        'accrualPolicyValue' => 'integer',
        'effective_date' => 'date',
        'confirmedYN' => 'integer',
        'confirmedby' => 'string',
        'confirmedDate' => 'datetime',
        'approvedYN' => 'integer',
        'approvedby' => 'string',
        'approvedDate' => 'datetime',
        'submitYN'=> 'integer',
        'submittedBy'=> 'integer',
        'submittedDate'=> 'datetime',
        'createdUserID' => 'integer',
        'createdUserGroup' => 'string',
        'createDate' => 'datetime',
        'createdpc' => 'string',
        'modifieduser' => 'string',
        'modifiedpc' => 'string',
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
