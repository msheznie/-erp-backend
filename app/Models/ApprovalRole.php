<?php

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
    use SoftDeletes;

    public $table = 'erp_approvalrollmaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


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
        'approvalGroupID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
