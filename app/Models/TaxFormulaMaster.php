<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TaxFormulaMaster
 * @package App\Models
 * @version April 24, 2018, 6:13 am UTC
 *
 * @property string Description
 * @property integer taxType
 * @property integer companySystemID
 * @property string companyID
 * @property integer createdUserGroup
 * @property string createdPCID
 * @property string createdUserID
 * @property string|\Carbon\Carbon createdDateTime
 * @property string createdUserName
 * @property string modifiedPCID
 * @property string modifiedUserID
 * @property string|\Carbon\Carbon modifiedDateTime
 * @property string modifiedUserName
 * @property string|\Carbon\Carbon timestamp
 */
class TaxFormulaMaster extends Model
{
    use SoftDeletes;

    public $table = 'erp_taxcalculationformulamaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'Description',
        'taxType',
        'companySystemID',
        'companyID',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'taxCalculationformulaID' => 'integer',
        'Description' => 'string',
        'taxType' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'createdUserGroup' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdUserName' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedUserName' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
