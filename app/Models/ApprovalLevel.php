<?php
/**
 * =============================================
 * -- File Name : ApprovalLevel.php
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
 * Class ApprovalLevel
 * @package App\Models
 * @version March 22, 2018, 1:35 pm UTC
 *
 * @property integer companySystemID
 * @property string companyID
 * @property integer departmentSystemID
 * @property string departmentID
 * @property integer serviceLineWise
 * @property integer serviceLineSystemID
 * @property string serviceLineCode
 * @property integer documentSystemID
 * @property string documentID
 * @property string levelDescription
 * @property integer noOfLevels
 * @property integer valueWise
 * @property float valueFrom
 * @property float valueTo
 * @property integer isCategoryWiseApproval
 * @property integer categoryID
 * @property integer isActive
 * @property string|\Carbon\Carbon timeStamp
 */
class ApprovalLevel extends Model
{
    //use SoftDeletes;

    public $table = 'erp_approvallevel';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey = 'approvalLevelID';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'companySystemID',
        'companyID',
        'departmentSystemID',
        'departmentID',
        'serviceLineWise',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'levelDescription',
        'noOfLevels',
        'valueWise',
        'valueFrom',
        'valueTo',
        'isCategoryWiseApproval',
        'categoryID',
        'isActive',
        'is_deleted',
        'timeStamp',
        'isDelegation',
        'tenderTypeId',
        'tenderTypeCode'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'approvalLevelID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'departmentSystemID' => 'integer',
        'departmentID' => 'string',
        'serviceLineWise' => 'integer',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'levelDescription' => 'string',
        'noOfLevels' => 'integer',
        'valueWise' => 'integer',
        'valueFrom' => 'float',
        'valueTo' => 'float',
        'isCategoryWiseApproval' => 'integer',
        'categoryID' => 'integer',
        'isActive' => 'integer',
        'is_deleted' => 'integer',
        'tenderTypeId'=> 'integer',
        'tenderTypeCode' => 'string',
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

    public function approvalrole(){
        return $this->hasMany('App\Models\ApprovalRole','approvalLevelID','approvalLevelID');
    }

    public function category(){
        return $this->belongsTo('App\Models\FinanceItemCategoryMaster','categoryID','itemCategoryID');
    }
    public static function isExistsTenderType($tenderTypeId)
    {
        return ApprovalLevel::where('isActive', -1)
            ->where('tenderTypeId', $tenderTypeId)
            ->exists();
    }
}
