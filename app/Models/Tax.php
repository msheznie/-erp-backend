<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Tax
 * @package App\Models
 * @version April 19, 2018, 5:03 am UTC
 *
 * @property integer companySystemID
 * @property string companyID
 * @property string taxDescription
 * @property string taxShortCode
 * @property boolean taxType
 * @property boolean isActive
 * @property integer authorityAutoID
 * @property integer GLAutoID
 * @property integer currencyID
 * @property date effectiveFrom
 * @property string taxReferenceNo
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
class Tax extends Model
{
    use SoftDeletes;

    public $table = 'erp_taxmaster_new';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'companySystemID',
        'companyID',
        'taxDescription',
        'taxShortCode',
        'taxType',
        'isActive',
        'authorityAutoID',
        'GLAutoID',
        'currencyID',
        'effectiveFrom',
        'taxReferenceNo',
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
        'taxMasterAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'taxDescription' => 'string',
        'taxShortCode' => 'string',
        'taxType' => 'boolean',
        'isActive' => 'boolean',
        'authorityAutoID' => 'integer',
        'GLAutoID' => 'integer',
        'currencyID' => 'integer',
        'effectiveFrom' => 'date',
        'taxReferenceNo' => 'string',
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
