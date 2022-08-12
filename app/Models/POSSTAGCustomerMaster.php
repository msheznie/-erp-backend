<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSSTAGCustomerMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="capAmount",
 *          description="capAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyCode",
 *          description="companyCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="integer",
 *          format="int32"
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
 *          property="customerAutoID",
 *          description="customerAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerCountry",
 *          description="customerCountry",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerCountryID",
 *          description="customerCountryID",
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
 *          property="customerCreditPeriod",
 *          description="customerCreditPeriod",
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
 *          property="customerCurrencyID",
 *          description="customerCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerEmail",
 *          description="customerEmail",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerFax",
 *          description="customerFax",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerName",
 *          description="customerName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerSystemCode",
 *          description="customerSystemCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerTelephone",
 *          description="customerTelephone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerUrl",
 *          description="customerUrl",
 *          type="string"
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
 *          property="deletedYN",
 *          description="deletedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="erp_customer_master_id",
 *          description="erp_customer_master_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="IdCardNumber",
 *          description="IdCardNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
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
 *          property="masterID",
 *          description="masterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
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
 *          property="partyCategoryID",
 *          description="to maintain party Category",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="secondaryCode",
 *          description="VAT Identification No",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="taxGroupID",
 *          description="taxGroupID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="transaction_log_id",
 *          description="transaction_log_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="vatEligible",
 *          description="1=NO, 2=YES",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="vatIdNo",
 *          description="vatIdNo",
 *          type="string"
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
 *      )
 * )
 */
class POSSTAGCustomerMaster extends Model
{

    public $table = 'pos_stag_customermaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'capAmount',
        'companyCode',
        'companyID',
        'createdDateTime',
        'createdPCID',
        'createdUserGroup',
        'createdUserID',
        'createdUserName',
        'customerAddress1',
        'customerAddress2',
        'customerCountry',
        'customerCountryID',
        'customerCreditLimit',
        'customerCreditPeriod',
        'customerCurrency',
        'customerCurrencyDecimalPlaces',
        'customerCurrencyID',
        'customerEmail',
        'customerFax',
        'customerName',
        'customerSystemCode',
        'customerTelephone',
        'customerUrl',
        'deleteByEmpID',
        'deletedDatetime',
        'deletedYN',
        'erp_customer_master_id',
        'IdCardNumber',
        'isActive',
        'isSync',
        'levelNo',
        'locationID',
        'masterID',
        'modifiedDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'partyCategoryID',
        'secondaryCode',
        'taxGroupID',
        'timestamp',
        'transaction_log_id',
        'vatEligible',
        'vatIdNo',
        'vatNumber',
        'vatPercentage'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'capAmount' => 'float',
        'companyCode' => 'string',
        'companyID' => 'integer',
        'createdDateTime' => 'datetime',
        'createdPCID' => 'string',
        'createdUserGroup' => 'integer',
        'createdUserID' => 'string',
        'createdUserName' => 'string',
        'customerAddress1' => 'string',
        'customerAddress2' => 'string',
        'customerAutoID' => 'integer',
        'customerCountry' => 'string',
        'customerCountryID' => 'integer',
        'customerCreditLimit' => 'float',
        'customerCreditPeriod' => 'integer',
        'customerCurrency' => 'string',
        'customerCurrencyDecimalPlaces' => 'integer',
        'customerCurrencyID' => 'integer',
        'customerEmail' => 'string',
        'customerFax' => 'string',
        'customerName' => 'string',
        'customerSystemCode' => 'string',
        'customerTelephone' => 'string',
        'customerUrl' => 'string',
        'deleteByEmpID' => 'string',
        'deletedDatetime' => 'datetime',
        'deletedYN' => 'integer',
        'erp_customer_master_id' => 'integer',
        'IdCardNumber' => 'string',
        'isActive' => 'integer',
        'isSync' => 'integer',
        'levelNo' => 'boolean',
        'locationID' => 'integer',
        'masterID' => 'integer',
        'modifiedDateTime' => 'datetime',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedUserName' => 'string',
        'partyCategoryID' => 'integer',
        'secondaryCode' => 'string',
        'taxGroupID' => 'integer',
        'timestamp' => 'datetime',
        'transaction_log_id' => 'integer',
        'vatEligible' => 'integer',
        'vatIdNo' => 'string',
        'vatNumber' => 'integer',
        'vatPercentage' => 'float'
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
