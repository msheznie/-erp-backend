<?php
/**
 * =============================================
 * -- File Name : DepartmentMaster.php
 * -- Project Name : ERP
 * -- Module Name :  General
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
 * Class DepartmentMaster
 * @package App\Models
 * @version March 22, 2018, 2:40 pm UTC
 *
 * @property string DepartmentID
 * @property string DepartmentDescription
 * @property integer isActive
 * @property string depImage
 * @property integer masterLevel
 * @property integer companyLevel
 * @property integer listOrder
 * @property integer isReport
 * @property string ReportMenu
 * @property string menuInitialImage
 * @property string menuInitialSelectedImage
 * @property integer showInCombo
 * @property integer hrLeaveApprovalLevels
 * @property string managerfield
 * @property integer isFunctionalDepartment
 * @property integer isReportGroupYN
 * @property integer hrObjectiveSetting
 * @property string|\Carbon\Carbon timeStamp
 */
class DepartmentMaster extends Model
{
    //use SoftDeletes;

    public $table = 'departmentmaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'DepartmentID',
        'DepartmentDescription',
        'isActive',
        'depImage',
        'masterLevel',
        'companyLevel',
        'listOrder',
        'isReport',
        'ReportMenu',
        'menuInitialImage',
        'menuInitialSelectedImage',
        'showInCombo',
        'hrLeaveApprovalLevels',
        'managerfield',
        'isFunctionalDepartment',
        'isReportGroupYN',
        'hrObjectiveSetting',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'departmentSystemID' => 'integer',
        'DepartmentID' => 'string',
        'DepartmentDescription' => 'string',
        'isActive' => 'integer',
        'depImage' => 'string',
        'masterLevel' => 'integer',
        'companyLevel' => 'integer',
        'listOrder' => 'integer',
        'isReport' => 'integer',
        'ReportMenu' => 'string',
        'menuInitialImage' => 'string',
        'menuInitialSelectedImage' => 'string',
        'showInCombo' => 'integer',
        'hrLeaveApprovalLevels' => 'integer',
        'managerfield' => 'string',
        'isFunctionalDepartment' => 'integer',
        'isReportGroupYN' => 'integer',
        'hrObjectiveSetting' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
