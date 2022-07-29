<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSSourceMenuSalesPayment",
 *      required={""},
 *      @SWG\Property(
 *          property="menuSalesPaymentID",
 *          description="menuSalesPaymentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="wareHouseAutoID",
 *          description="wareHouseAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="menuSalesID",
 *          description="menuSalesID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paymentConfigMasterID",
 *          description="paymentConfigMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paymentConfigDetailID",
 *          description="paymentConfigDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="glAccountType",
 *          description="pos_paymentconfigmaster.glAccountType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="GLCode",
 *          description="GLCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="amount",
 *          description="amount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="reference",
 *          description="reference",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerAutoID",
 *          description="ERP Customer master ID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isAdvancePayment",
 *          description="1 - advancePayment, 0 not an advance payment",
 *          type="integer",
 *          format="int32"
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
 *          property="is_sync",
 *          description="is_sync",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="id_store",
 *          description="id_store",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isVerifiedByCashier",
 *          description="isVerifiedByCashier",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="transaction_log_id",
 *          description="transaction_log_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class POSSourceMenuSalesPayment extends Model
{

    public $table = 'pos_source_menusalespayments';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'wareHouseAutoID',
        'menuSalesID',
        'paymentConfigMasterID',
        'paymentConfigDetailID',
        'glAccountType',
        'GLCode',
        'amount',
        'reference',
        'customerAutoID',
        'isAdvancePayment',
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
        'is_sync',
        'id_store',
        'isVerifiedByCashier',
        'transaction_log_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'menuSalesPaymentID' => 'integer',
        'wareHouseAutoID' => 'integer',
        'menuSalesID' => 'integer',
        'paymentConfigMasterID' => 'integer',
        'paymentConfigDetailID' => 'integer',
        'glAccountType' => 'integer',
        'GLCode' => 'integer',
        'amount' => 'float',
        'reference' => 'string',
        'customerAutoID' => 'integer',
        'isAdvancePayment' => 'integer',
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
        'is_sync' => 'integer',
        'id_store' => 'integer',
        'isVerifiedByCashier' => 'boolean',
        'transaction_log_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'wareHouseAutoID' => 'required'
    ];  

    public function paymentConfig(){ 
        return $this->hasOne('App\Models\POSSourcePaymentGlConfig', 'autoID', 'paymentConfigMasterID');
    }
}
