<?php
/**
 * =============================================
 * -- File Name : EmployeesDepartment.php
 * -- Project Name : ERP
 * -- Module Name :  Approval Setup
 * -- Author : Mubashir
 * -- Create date : 23 - April 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * -- Date: 17-May 2018 By: Mubashir Description: Added relationship to table,
 * --
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Awobaz\Compoships\Compoships;
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
    use Compoships;
    public $table = 'employeesdepartments';
    
    const CREATED_AT = 'createdDate';
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
        'timeStamp',
        'createdDate',
        'createdByEmpSystemID',
        'isActive',
        'activatedDate',
        'activatedByEmpID',
        'activatedByEmpSystemID',
        'removedYN',
        'removedByEmpID',
        'removedByEmpSystemID',
        'removedDate'
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
        'dmsIsUploadEnable' => 'integer',
        'createdByEmpSystemID' => 'integer',
        'isActive' => 'integer',
        'activatedDate' => 'string',
        'activatedByEmpID' => 'string',
        'activatedByEmpSystemID' => 'integer',
        'removedYN' => 'integer',
        'removedByEmpID' => 'string',
        'createdDate' => 'string',
        'removedByEmpSystemID' => 'integer',
        'removedDate' => 'string'
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

    public function department(){
        return $this->belongsTo('App\Models\DepartmentMaster','departmentSystemID','departmentSystemID');
    }

    public function serviceline(){
        return $this->belongsTo('App\Models\SegmentMaster','ServiceLineSystemID','serviceLineSystemID');
    }

    public function document(){
        return $this->belongsTo('App\Models\DocumentMaster','documentSystemID','documentSystemID');
    }

    public function approvalgroup(){
        return $this->belongsTo('App\Models\ApprovalGroups','employeeGroupID','rightsGroupId');
    }

    public function company(){
        return $this->belongsTo('App\Models\Company','companySystemID','companySystemID');
    } 
    public function delegator_employee(){
        return $this->belongsTo('App\Models\Employee','approvalDeligatedFromEmpID','employeeSystemID');
    }

}
