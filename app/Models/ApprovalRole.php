<?php
/**
 * =============================================
 * -- File Name : ApprovalRole.php
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
 * Class ApprovalRole
 * @package App\Models
 * @version March 22, 2018, 1:41 pm UTC
 *
 * @property string rollDescription
 * @property integer documentSystemID
 * @property string documentID
 * @property integer companySystemID
 * @property string companyID
 * @property integer departmentSystemID
 * @property string departmentID
 * @property integer serviceLineSystemID
 * @property string serviceLineID
 * @property integer rollLevel
 * @property integer approvalLevelID
 * @property integer approvalGroupID
 * @property string|\Carbon\Carbon timeStamp
 */
class ApprovalRole extends Model
{
    //use SoftDeletes;

    public $table = 'erp_approvalrollmaster';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey = 'rollMasterID';




    public $fillable = [
        'rollDescription',
        'documentSystemID',
        'documentID',
        'companySystemID',
        'companyID',
        'departmentSystemID',
        'departmentID',
        'serviceLineSystemID',
        'serviceLineID',
        'rollLevel',
        'approvalLevelID',
        'approvalGroupID',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'rollMasterID' => 'integer',
        'rollDescription' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'departmentSystemID' => 'integer',
        'departmentID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineID' => 'string',
        'rollLevel' => 'integer',
        'approvalLevelID' => 'integer',
        'approvalGroupID' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function company(){
        return $this->belongsTo('App\Models\Company','companySystemID','companySystemID');
    }

    public function department(){
        return $this->belongsTo('App\Models\DepartmentMaster','departmentSystemID','departmentSystemID');
    }

    public function document(){
        return $this->belongsTo('App\Models\DocumentMaster','documentSystemID','documentSystemID');
    }

    public function serviceline(){
        return $this->belongsTo('App\Models\SegmentMaster','serviceLineSystemID','serviceLineSystemID');
    }

    public function approval_group(){
        return $this->hasOne('App\Models\ApprovalGroups','rightsGroupId','approvalGroupID');
    }
}
