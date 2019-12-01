<?php
/**
 * =============================================
 * -- File Name : HRMSDepartmentMaster.php
 * -- Project Name : ERP
 * -- Module Name :  HRMS Department Master
 * -- Author : Mohamed Rilwan
 * -- Create date : 26 - November 2019
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="employeeDepartmentDelegation",
 *      required={""},
 *      @SWG\Property(
 *          property="employeeDepartmentDelegationID",
 *          description="employeeDepartmentDelegationID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="departmentSystemID",
 *          description="departmentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="departmentID",
 *          description="departmentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="empSystemID",
 *          description="empSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="empID",
 *          description="empID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="employeeName",
 *          description="employeeName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="empEmailID",
 *          description="empEmailID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="sendEmailNotificationForPayment",
 *          description="sendEmailNotificationForPayment",
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
class employeeDepartmentDelegation extends Model
{

    public $table = 'employeedepartmentdelegation';
    protected $primaryKey = 'employeeDepartmentDelegationID';
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    public $fillable = [
        'companySystemID',
        'companyID',
        'departmentSystemID',
        'departmentID',
        'documentSystemID',
        'documentID',
        'empSystemID',
        'empID',
        'employeeName',
        'empEmailID',
        'sendEmailNotificationForPayment',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'employeeDepartmentDelegationID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'departmentSystemID' => 'integer',
        'departmentID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'empSystemID' => 'integer',
        'empID' => 'string',
        'employeeName' => 'string',
        'empEmailID' => 'string',
        'sendEmailNotificationForPayment' => 'integer',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
//        'employeeDepartmentDelegationID' => 'required'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company','companySystemID','companySystemID');
    }

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'empSystemID', 'employeeSystemID');
    }
}
