<?php

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
 * @property string documentID
 * @property string departmentID
 * @property string condition
 * @property integer sortOrder
 * @property string|\Carbon\Carbon timestamp
 */
class ApprovalGroups extends Model
{
    use SoftDeletes;

    public $table = 'approvalgroups';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'rightsGroupDes',
        'isFormsAssigned',
        'documentID',
        'departmentID',
        'condition',
        'sortOrder',
        'timestamp'
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
        'documentID' => 'string',
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

    
}
