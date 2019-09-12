<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="EmployeeManagers",
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
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="managerID",
 *          description="managerID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="level",
 *          description="level",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isFunctionalManager",
 *          description="-1 = functional manager, 0= reporting manager",
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
class EmployeeManagers extends Model
{

    public $table = 'hrms_employeemanagers';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'empID',
        'managerID',
        'level',
        'isFunctionalManager',
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
        'empID' => 'string',
        'managerID' => 'string',
        'level' => 'integer',
        'isFunctionalManager' => 'integer',
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
        'employeeManagersID' => 'required'
    ];

    
}
