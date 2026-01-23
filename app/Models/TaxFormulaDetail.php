<?php
/**
 * =============================================
 * -- File Name : TaxFormulaDetail.php
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
 * Class TaxFormulaDetail
 * @package App\Models
 * @version April 24, 2018, 6:14 am UTC
 *
 * @property integer taxCalculationformulaID
 * @property integer taxMasterAutoID
 * @property string description
 * @property string taxMasters
 * @property integer sortOrder
 * @property string formula
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
class TaxFormulaDetail extends Model
{
    //use SoftDeletes;

    public $table = 'erp_taxcalculationformuladetails';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'modifiedDateTime';

    protected $primaryKey = 'formulaDetailID';


    public $fillable = [
        'taxCalculationformulaID',
        'taxMasterAutoID',
        'description',
        'taxMasters',
        'sortOrder',
        'formula',
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
        'formulaDetailID' => 'integer',
        'taxCalculationformulaID' => 'integer',
        'taxMasterAutoID' => 'integer',
        'description' => 'string',
        'taxMasters' => 'string',
        'sortOrder' => 'integer',
        'formula' => 'string',
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


    public function taxmaster()
    {
        return $this->hasOne('App\Models\Tax', 'taxMasterAutoID', 'taxMasterAutoID');
    }

    public function master()
    {
        return $this->hasOne('App\Models\TaxFormulaMaster', 'taxCalculationformulaID', 'taxCalculationformulaID');
    }

    
}
