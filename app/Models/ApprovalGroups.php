<?php
/**
 * =============================================
 * -- File Name : ApprovalGroup.php
 * -- Project Name : ERP
 * -- Module Name :  Aprroval Setup
 * -- Author : Mubashir
 * -- Create date : 23 - April 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ApprovalGroups
 * @package App\Models
 * @version March 22, 2018, 2:43 pm UTC
 *
 * @property string rightsGroupDes
 * @property integer isFormsAssigned
 * @property integer documentSystemID
 * @property string documentID
 * @property integer departmentSystemID
 * @property string departmentID
 * @property string condition
 * @property integer sortOrder
 * @property string|\Carbon\Carbon timestamp
 */
class ApprovalGroups extends Model
{
    //use SoftDeletes;

    public $table = 'approvalgroups';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';


    protected $dates = ['deleted_at'];

    protected $primaryKey = 'rightsGroupId';

    public $fillable = [
        'rightsGroupDes',
        'isFormsAssigned',
        'documentSystemID',
        'documentID',
        'departmentSystemID',
        'departmentID',
        'condition',
        'sortOrder',
        'timestamp',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'rightsGroupId' => 'integer',
        'rightsGroupDes' => 'string',
        'isFormsAssigned' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'departmentSystemID' => 'integer',
        'departmentID' => 'string',
        'condition' => 'string',
        'sortOrder' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function department(){
        return $this->belongsTo('App\Models\DepartmentMaster','departmentSystemID','departmentSystemID');
    }

    public function document(){
        return $this->belongsTo('App\Models\DocumentMaster','documentSystemID','documentSystemID');
    }

    public function employee_department(){
        return $this->hasMany('App\Models\EmployeesDepartment','employeeGroupID','rightsGroupId');
    }
}
