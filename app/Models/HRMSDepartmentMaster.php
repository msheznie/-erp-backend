<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="HrmsDepartmentMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DepartmentID",
 *          description="DepartmentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DepartmentDescription",
 *          description="DepartmentDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ServiceLineCode",
 *          description="ServiceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="CompanyID",
 *          description="CompanyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="showInCombo",
 *          description="showInCombo",
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
class HrmsDepartmentMaster extends Model
{
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    public $table = 'hrms_departmentmaster';
    protected $primaryKey = 'DepartmentID';


    public $fillable = [
        'serviceLineSystemID',
        'DepartmentDescription',
        'isActive',
        'ServiceLineCode',
        'CompanyID',
        'showInCombo',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'serviceLineSystemID' => 'integer',
        'DepartmentID' => 'integer',
        'DepartmentDescription' => 'string',
        'isActive' => 'integer',
        'ServiceLineCode' => 'string',
        'CompanyID' => 'string',
        'showInCombo' => 'integer',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function serviceline(){
        return $this->belongsTo('App\Models\SegmentMaster','serviceLineSystemID','serviceLineSystemID');
    }

    public function employeeDetail(){
        return $this->hasMany('App\Models\EmployeeDetails','departmentID','DepartmentID');
    }
}
