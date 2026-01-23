<?php
/**
 * =============================================
 * -- File Name : TaxFormulaMaster.php
 * -- Project Name : ERP
 * -- Module Name :  Tax Setup
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
    //use SoftDeletes;

    public $table = 'erp_taxcalculationformulamaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'modifiedDateTime';

    protected $primaryKey = 'taxCalculationformulaID';




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
        'modifiedUserName' => 'string',
        'deleted_at' => 'datetime',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function type()
    {
        /** one tax can have only one tax type */
        return $this->hasOne('App\Models\TaxType', 'taxTypeID', 'taxType');
    }

    
}
