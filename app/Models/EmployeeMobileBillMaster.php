<?php
/**
 * =============================================
 * -- File Name : EmployeeMobileBillMaster.php
 * -- Project Name : ERP
 * -- Module Name : Mobile Bill Management
 * -- Author : Mohamed Rilwan
 * -- Create date : 20- July 2020
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="EmployeeMobileBillMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="EmployeemobilebillmasterID",
 *          description="EmployeemobilebillmasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="mobilebillMasterID",
 *          description="mobilebillMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companySysID",
 *          description="companySysID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="employeeSystemID",
 *          description="employeeSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="empID",
 *          description="empID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="mobileNo",
 *          description="mobileNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isSubmited",
 *          description="isSubmited",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="totalAmount",
 *          description="totalAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="deductionAmount",
 *          description="deductionAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="exceededAmount",
 *          description="exceededAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="officialAmount",
 *          description="officialAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="personalAmount",
 *          description="personalAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="creditLimit",
 *          description="creditLimit",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="submittedBySysID",
 *          description="submittedBySysID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="submittedby",
 *          description="submittedby",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="submittedpc",
 *          description="submittedpc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createDate",
 *          description="createDate",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createUserID",
 *          description="createUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createPCID",
 *          description="createPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedpc",
 *          description="modifiedpc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
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
 *          property="approvedBySysID",
 *          description="approvedBySysID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedBy",
 *          description="approvedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedDate",
 *          description="approvedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="hrApprovedYN",
 *          description="hrApprovedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="hrApprovedBySystemID",
 *          description="hrApprovedBySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="hrApprovedBy",
 *          description="hrApprovedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="hrApprovedDate",
 *          description="hrApprovedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="managerApprovedYN",
 *          description="managerApprovedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="managerApprovedBy",
 *          description="managerApprovedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="managerApprovedDate",
 *          description="managerApprovedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isDeductedYN",
 *          description="isDeductedYN",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class EmployeeMobileBillMaster extends Model
{

    public $table = 'hrms_employeemobilebillmaster';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey  = 'EmployeemobilebillmasterID';


    public $fillable = [
        'mobilebillMasterID',
        'companySysID',
        'companyID',
        'employeeSystemID',
        'empID',
        'mobileNo',
        'isSubmited',
        'totalAmount',
        'deductionAmount',
        'exceededAmount',
        'officialAmount',
        'personalAmount',
        'creditLimit',
        'submittedBySysID',
        'submittedby',
        'submittedpc',
        'createDate',
        'createUserID',
        'createPCID',
        'modifiedpc',
        'modifiedUser',
        'timestamp',
        'approvedYN',
        'approvedBySysID',
        'approvedBy',
        'approvedDate',
        'hrApprovedYN',
        'hrApprovedBySystemID',
        'hrApprovedBy',
        'hrApprovedDate',
        'managerApprovedYN',
        'managerApprovedBy',
        'managerApprovedDate',
        'RollLevForApp_curr',
        'isDeductedYN'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'EmployeemobilebillmasterID' => 'integer',
        'mobilebillMasterID' => 'integer',
        'companySysID' => 'integer',
        'companyID' => 'string',
        'employeeSystemID' => 'integer',
        'empID' => 'string',
        'mobileNo' => 'integer',
        'isSubmited' => 'integer',
        'totalAmount' => 'float',
        'deductionAmount' => 'float',
        'exceededAmount' => 'float',
        'officialAmount' => 'float',
        'personalAmount' => 'float',
        'creditLimit' => 'float',
        'submittedBySysID' => 'integer',
        'submittedby' => 'string',
        'submittedpc' => 'string',
        'createDate' => 'string',
        'createUserID' => 'string',
        'createPCID' => 'string',
        'modifiedpc' => 'string',
        'modifiedUser' => 'string',
        'timestamp' => 'datetime',
        'approvedYN' => 'integer',
        'approvedBySysID' => 'integer',
        'approvedBy' => 'string',
        'approvedDate' => 'datetime',
        'hrApprovedYN' => 'integer',
        'hrApprovedBySystemID' => 'integer',
        'hrApprovedBy' => 'string',
        'hrApprovedDate' => 'datetime',
        'managerApprovedYN' => 'integer',
        'managerApprovedBy' => 'string',
        'managerApprovedDate' => 'datetime',
        'RollLevForApp_curr' => 'integer',
        'isDeductedYN' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function mobile_pool(){
        return $this->belongsTo('App\Models\MobileNoPool', 'mobileNo','mobileNo');
    }


    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employeeSystemID', 'employeeSystemID');
    }

    
}
