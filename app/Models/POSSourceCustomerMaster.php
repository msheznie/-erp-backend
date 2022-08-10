<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSSourceCustomerMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="customerAutoID",
 *          description="customerAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerSystemCode",
 *          description="customerSystemCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerName",
 *          description="customerName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="partyCategoryID",
 *          description="to maintain party Category",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="masterID",
 *          description="masterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="levelNo",
 *          description="levelNo",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="locationID",
 *          description="locationID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerAddress1",
 *          description="customerAddress1",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerAddress2",
 *          description="customerAddress2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerCountryID",
 *          description="customerCountryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerCountry",
 *          description="customerCountry",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="IdCardNumber",
 *          description="IdCardNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerTelephone",
 *          description="customerTelephone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerEmail",
 *          description="customerEmail",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerUrl",
 *          description="customerUrl",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerFax",
 *          description="customerFax",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="secondaryCode",
 *          description="VAT Identification No",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerCurrencyID",
 *          description="customerCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerCurrency",
 *          description="customerCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerCurrencyDecimalPlaces",
 *          description="customerCurrencyDecimalPlaces",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerCreditPeriod",
 *          description="customerCreditPeriod",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerCreditLimit",
 *          description="customerCreditLimit",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="taxGroupID",
 *          description="taxGroupID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="vatIdNo",
 *          description="vatIdNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="vatEligible",
 *          description="1=NO, 2=YES",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="vatNumber",
 *          description="vatNumber",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="vatPercentage",
 *          description="vatPercentage",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="deletedYN",
 *          description="deletedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="deleteByEmpID",
 *          description="deleteByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="deletedDatetime",
 *          description="deletedDatetime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="capAmount",
 *          description="capAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyCode",
 *          description="companyCode",
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
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
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
 *      ),
 *      @SWG\Property(
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="isSync",
 *          description="0 => Not Synced 
1 => Send to ERP 
2 => Fully Synced",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="transaction_log_id",
 *          description="transaction_log_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="erp_customer_master_id",
 *          description="erp_customer_master_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class POSSourceCustomerMaster extends Model
{

    public $table = 'pos_source_customermaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modifiedDateTime';




    public $fillable = [
        'customerSystemCode',
        'customerName',
        'partyCategoryID',
        'masterID',
        'levelNo',
        'locationID',
        'customerAddress1',
        'customerAddress2',
        'customerCountryID',
        'customerCountry',
        'IdCardNumber',
        'customerTelephone',
        'customerEmail',
        'customerUrl',
        'customerFax',
        'secondaryCode',
        'customerCurrencyID',
        'customerCurrency',
        'customerCurrencyDecimalPlaces',
        'customerCreditPeriod',
        'customerCreditLimit',
        'taxGroupID',
        'vatIdNo',
        'vatEligible',
        'vatNumber',
        'vatPercentage',
        'isActive',
        'deletedYN',
        'deleteByEmpID',
        'deletedDatetime',
        'capAmount',
        'companyID',
        'companyCode',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdUserName',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'modifiedDateTime',
        'timestamp',
        'isSync',
        'transaction_log_id',
        'erp_customer_master_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'customerAutoID' => 'integer',
        'customerSystemCode' => 'string',
        'customerName' => 'string',
        'partyCategoryID' => 'integer',
        'masterID' => 'integer',
        'levelNo' => 'boolean',
        'locationID' => 'integer',
        'customerAddress1' => 'string',
        'customerAddress2' => 'string',
        'customerCountryID' => 'integer',
        'customerCountry' => 'string',
        'IdCardNumber' => 'string',
        'customerTelephone' => 'string',
        'customerEmail' => 'string',
        'customerUrl' => 'string',
        'customerFax' => 'string',
        'secondaryCode' => 'string',
        'customerCurrencyID' => 'integer',
        'customerCurrency' => 'string',
        'customerCurrencyDecimalPlaces' => 'integer',
        'customerCreditPeriod' => 'integer',
        'customerCreditLimit' => 'float',
        'taxGroupID' => 'integer',
        'vatIdNo' => 'string',
        'vatEligible' => 'integer',
        'vatNumber' => 'integer',
        'vatPercentage' => 'float',
        'isActive' => 'integer',
        'deletedYN' => 'integer',
        'deleteByEmpID' => 'string',
        'deletedDatetime' => 'datetime',
        'capAmount' => 'float',
        'companyID' => 'integer',
        'companyCode' => 'string',
        'createdUserGroup' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdUserName' => 'string',
        'createdDateTime' => 'datetime',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedUserName' => 'string',
        'modifiedDateTime' => 'datetime',
        'timestamp' => 'datetime',
        'isSync' => 'integer',
        'transaction_log_id' => 'integer',
        'erp_customer_master_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'customerCurrencyID' => 'required',
        'isSync' => 'required'
    ];

    
}
