<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="HrmsEmployeeManager",
 *      required={""},
 *      @SWG\Property(
 *          property="employeeManagersID",
 *          description="employeeManagersID",
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
 *          property="managerID",
 *          description="managerID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="level",
 *          description="level",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="active",
 *          description="-1 = functional manager, 0= reporting manager",
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
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdDate",
 *          description="createdDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedDate",
 *          description="modifiedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class HrmsEmployeeManager extends Model
{

    public $table = 'srp_erp_employeemanagers';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $appends = ['empName'];


    public $fillable = [
        'empID',
        'managerID',
        'level',
        'active',
        'companyID',
        'createdUserID',
        'createdDate',
        'modifiedUserID',
        'modifiedDate',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'employeeManagersID' => 'integer',
        'empID' => 'integer',
        'managerID' => 'integer',
        'level' => 'integer',
        'active' => 'integer',
        'companyID' => 'integer',
        'createdUserID' => 'string',
        'createdDate' => 'datetime',
        'modifiedUserID' => 'string',
        'modifiedDate' => 'datetime',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function info(){
        return $this->belongsTo(SrpEmployeeDetails::class, 'managerID', 'EIdNo');
    }

    public function getEmpNameAttribute()
    {
        return '';

        /* By : Nasik
         * On : 2021-09-03
         * Do not use the below query it is causing N+1 query issue */
        $employee = Employee::find($this->managerID);

        return (isset($employee)) ? $employee->empName : '';
    }
}
