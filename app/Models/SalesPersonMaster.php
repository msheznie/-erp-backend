<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SalesPersonMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="salesPersonID",
 *          description="salesPersonID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="empSystemID",
 *          description="empSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="SalesPersonCode",
 *          description="SalesPersonCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="SalesPersonName",
 *          description="SalesPersonName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="salesPersonImage",
 *          description="salesPersonImage",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="wareHouseAutoID",
 *          description="wareHouseAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="wareHouseCode",
 *          description="wareHouseCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="wareHouseDescription",
 *          description="wareHouseDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="wareHouseLocation",
 *          description="wareHouseLocation",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="SalesPersonEmail",
 *          description="SalesPersonEmail",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="SecondaryCode",
 *          description="SecondaryCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contactNumber",
 *          description="contactNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="salesPersonTargetType",
 *          description="salesPersonTargetType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="salesPersonTarget",
 *          description="salesPersonTarget",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="SalesPersonAddress",
 *          description="SalesPersonAddress",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="receivableAutoID",
 *          description="receivableAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="receivableSystemGLCode",
 *          description="receivableSystemGLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="receivableGLAccount",
 *          description="receivableGLAccount",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="receivableDescription",
 *          description="receivableDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="receivableType",
 *          description="receivableType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="expenseAutoID",
 *          description="expenseAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="expenseSystemGLCode",
 *          description="expenseSystemGLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="expenseGLAccount",
 *          description="expenseGLAccount",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="expenseDescription",
 *          description="expenseDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="expenseType",
 *          description="expenseType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="salesPersonCurrencyID",
 *          description="salesPersonCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="salesPersonCurrency",
 *          description="salesPersonCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="salesPersonCurrencyDecimalPlaces",
 *          description="salesPersonCurrencyDecimalPlaces",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="segmentID",
 *          description="segmentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="segmentCode",
 *          description="segmentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserName",
 *          description="createdUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserName",
 *          description="modifiedUserName",
 *          type="string"
 *      )
 * )
 */
class SalesPersonMaster extends Model
{

    public $table = 'erp_salespersonmaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'TIMESTAMP';

    protected $primaryKey = 'salesPersonID';

    public $fillable = [
        'empSystemID',
        'serialNumber',
        'SalesPersonCode',
        'SalesPersonName',
        'salesPersonImage',
        'wareHouseAutoID',
        'wareHouseCode',
        'wareHouseDescription',
        'wareHouseLocation',
        'SalesPersonEmail',
        'SecondaryCode',
        'contactNumber',
        'salesPersonTargetType',
        'salesPersonTarget',
        'SalesPersonAddress',
        'receivableAutoID',
        'receivableSystemGLCode',
        'receivableGLAccount',
        'receivableDescription',
        'receivableType',
        'expenseAutoID',
        'expenseSystemGLCode',
        'expenseGLAccount',
        'expenseDescription',
        'expenseType',
        'salesPersonCurrencyID',
        'salesPersonCurrency',
        'salesPersonCurrencyDecimalPlaces',
        'segmentID',
        'segmentCode',
        'isActive',
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
        'TIMESTAMP'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'salesPersonID' => 'integer',
        'empSystemID' => 'integer',
        'serialNumber' => 'integer',
        'SalesPersonCode' => 'string',
        'SalesPersonName' => 'string',
        'salesPersonImage' => 'string',
        'wareHouseAutoID' => 'integer',
        'wareHouseCode' => 'string',
        'wareHouseDescription' => 'string',
        'wareHouseLocation' => 'string',
        'SalesPersonEmail' => 'string',
        'SecondaryCode' => 'string',
        'contactNumber' => 'string',
        'salesPersonTargetType' => 'integer',
        'salesPersonTarget' => 'float',
        'SalesPersonAddress' => 'string',
        'receivableAutoID' => 'integer',
        'receivableSystemGLCode' => 'string',
        'receivableGLAccount' => 'string',
        'receivableDescription' => 'string',
        'receivableType' => 'string',
        'expenseAutoID' => 'integer',
        'expenseSystemGLCode' => 'string',
        'expenseGLAccount' => 'string',
        'expenseDescription' => 'string',
        'expenseType' => 'string',
        'salesPersonCurrencyID' => 'integer',
        'salesPersonCurrency' => 'string',
        'salesPersonCurrencyDecimalPlaces' => 'integer',
        'segmentID' => 'integer',
        'segmentCode' => 'string',
        'isActive' => 'boolean',
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
