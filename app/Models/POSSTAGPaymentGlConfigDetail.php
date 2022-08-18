<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSSTAGPaymentGlConfigDetail",
 *      required={""},
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
 *          property="GLCode",
 *          description="GLCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ID",
 *          description="ID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isAuthRequired",
 *          description="isAuthRequired",
 *          type="boolean"
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
 *          property="paymentConfigMasterID",
 *          description="FK from srp_erp_pos_paymentglconfigmaster",
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
 *          property="warehouseID",
 *          description="warehouseID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class POSSTAGPaymentGlConfigDetail extends Model
{

    public $table = 'pos_stag_paymentglconfigdetail';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'companyCode',
        'companyID',
        'createdDateTime',
        'createdPCID',
        'createdUserGroup',
        'createdUserID',
        'createdUserName',
        'GLCode',
        'isAuthRequired',
        'isSync',
        'modifiedDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'paymentConfigMasterID',
        'timestamp',
        'transaction_log_id',
        'warehouseID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'companyCode' => 'string',
        'companyID' => 'integer',
        'createdDateTime' => 'datetime',
        'createdPCID' => 'string',
        'createdUserGroup' => 'integer',
        'createdUserID' => 'string',
        'createdUserName' => 'string',
        'GLCode' => 'integer',
        'ID' => 'integer',
        'isAuthRequired' => 'boolean',
        'isSync' => 'integer',
        'modifiedDateTime' => 'datetime',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedUserName' => 'string',
        'paymentConfigMasterID' => 'integer',
        'timestamp' => 'datetime',
        'transaction_log_id' => 'integer',
        'warehouseID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'isSync' => 'required'
    ];

    
}
