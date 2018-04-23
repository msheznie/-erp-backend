<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class EmployeesDepartment
 * @package App\Models
 * @version April 2, 2018, 11:51 am UTC
 *
 * @property integer employeeSystemID
 * @property string employeeID
 * @property integer employeeGroupID
 * @property integer companySystemID
 * @property string companyId
 * @property integer documentSystemID
 * @property string documentID
 * @property string departmentID
 * @property integer ServiceLineSystemID
 * @property string ServiceLineID
 * @property integer warehouseSystemCode
 * @property string reportingManagerID
 * @property integer isDefault
 * @property integer dischargedYN
 * @property integer approvalDeligated
 * @property string approvalDeligatedFromEmpID
 * @property string approvalDeligatedFrom
 * @property string approvalDeligatedTo
 * @property integer dmsIsUploadEnable
 * @property string|\Carbon\Carbon timeStamp
 */
class EmployeesDepartment extends Model
{
    //use SoftDeletes;

    public $table = 'employeesdepartments';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey = 'employeesDepartmentsID';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'employeeSystemID',
        'employeeID',
        'employeeGroupID',
        'companySystemID',
        'companyId',
        'documentSystemID',
        'documentID',
        'departmentID',
        'ServiceLineSystemID',
        'ServiceLineID',
        'warehouseSystemCode',
        'reportingManagerID',
        'isDefault',
        'dischargedYN',
        'approvalDeligated',
        'approvalDeligatedFromEmpID',
        'approvalDeligatedFrom',
        'approvalDeligatedTo',
        'dmsIsUploadEnable',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'employeesDepartmentsID' => 'integer',
        'employeeSystemID' => 'integer',
        'employeeID' => 'string',
        'employeeGroupID' => 'integer',
        'companySystemID' => 'integer',
        'companyId' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'departmentID' => 'string',
        'ServiceLineSystemID' => 'integer',
        'ServiceLineID' => 'string',
        'warehouseSystemCode' => 'integer',
        'reportingManagerID' => 'string',
        'isDefault' => 'integer',
        'dischargedYN' => 'integer',
        'approvalDeligated' => 'integer',
        'approvalDeligatedFromEmpID' => 'string',
        'approvalDeligatedFrom' => 'string',
        'approvalDeligatedTo' => 'string',
        'dmsIsUploadEnable' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function employee(){
        return $this->belongsTo('App\Models\Employee','employeeSystemID','employeeSystemID');
    }
    
}
