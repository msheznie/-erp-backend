<?php
/**
 * =============================================
 * -- File Name : Tax.php
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
    //use SoftDeletes;

    public $table = 'erp_taxmaster_new';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'modifiedDateTime';

    protected $primaryKey = 'taxMasterAutoID';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'companySystemID',
        'companyID',
        'taxDescription',
        'taxShortCode',
        'taxType',
        'isActive',
        'authorityAutoID',
        'inputVatGLAccountAutoID',
        'inputVatGLAccount',
        'outputVatGLAccountAutoID',
        'outputVatGLAccount',
        'inputVatTransferGLAccountAutoID',
        'inputVatTransferGLAccount',
        'outputVatTransferGLAccountAutoID',
        'outputVatTransferGLAccount',
        'currencyID',
        'effectiveFrom',
        'taxReferenceNo',
        'taxCategory',
        'isDefault',
        'registration_no',
        'identification_no',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp',
        'whtPercentage',
        'whtType',
         'isDelegation'
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
        'taxType' => 'integer',
        'isActive' => 'boolean',
        'authorityAutoID' => 'integer',
        'inputVatGLAccountAutoID' => 'integer',
        'inputVatGLAccount' => 'string',
        'outputVatGLAccountAutoID' => 'integer',
        'outputVatGLAccount' => 'string',
        'inputVatTransferGLAccountAutoID' => 'integer',
        'inputVatTransferGLAccount' => 'string',
        'outputVatTransferGLAccountAutoID' => 'integer',
        'outputVatTransferGLAccount' => 'string',
        'currencyID' => 'integer',
        'effectiveFrom' => 'date',
        'taxReferenceNo' => 'string',
        'taxCategory' => 'integer',
        'isDefault' => 'integer',
        'registration_no' => 'string',
        'identification_no' => 'string',
        'createdUserGroup' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdUserName' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedUserName' => 'string',
        'whtPercentage' => 'float',
        'whtType' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function authority(){
        return $this->hasOne('App\Models\SupplierMaster', 'supplierCodeSystem', 'authorityAutoID');
    }

    public function type()
    {
        /** one tax can have only one tax type */
        return $this->hasOne('App\Models\TaxType', 'taxTypeID', 'taxType');
    }

    public function formula_detail()
    {
        return $this->hasMany('App\Models\TaxFormulaDetail', 'taxMasterAutoID', 'taxMasterAutoID');
    }

    public function vat_categories()
    {
        return $this->hasOne('App\Models\TaxVatCategories', 'taxVatSubCategoriesAutoID','taxCategory');
    }

    public function vat_main_categories()
    {
        return $this->hasMany('App\Models\TaxVatMainCategories', 'taxMasterAutoID', 'taxMasterAutoID');
    }

}
