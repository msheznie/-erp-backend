<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSStagMenuSalesServiceCharge",
 *      required={""},
 *      @SWG\Property(
 *          property="menusalesServiceChargeID",
 *          description="menusalesServiceChargeID",
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
 *          property="menuSalesItemID",
 *          description="menuSalesItemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="menuServiceChargeID",
 *          description="menuServiceChargeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="menuMasterID",
 *          description="menuMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceChargePercentage",
 *          description="serviceChargePercentage",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="serviceChargeAmount",
 *          description="serviceChargeAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="GLAutoID",
 *          description="GLAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="beforeDiscountTotalServiceCharge",
 *          description="beforeDiscountTotalServiceCharge",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="menusalesDiscount",
 *          description="menusalesDiscount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="menusalesPromotionalDiscount",
 *          description="menusalesPromotionalDiscount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="unitMenuServiceCharge",
 *          description="unitMenuServiceCharge",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="menusalesItemQty",
 *          description="menusalesItemQty",
 *          type="integer",
 *          format="int32"
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
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
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
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserName",
 *          description="modifiedUserName",
 *          type="string"
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
 *          property="transaction_log_id",
 *          description="transaction_log_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class POSStagMenuSalesServiceCharge extends Model
{

    public $table = 'pos_stag_menusalesservicecharge';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'wareHouseAutoID',
        'menuSalesID',
        'menuSalesItemID',
        'menuServiceChargeID',
        'menuMasterID',
        'serviceChargePercentage',
        'serviceChargeAmount',
        'GLAutoID',
        'beforeDiscountTotalServiceCharge',
        'menusalesDiscount',
        'menusalesPromotionalDiscount',
        'unitMenuServiceCharge',
        'menusalesItemQty',
        'companyID',
        'companyCode',
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
        'is_sync',
        'id_store',
        'transaction_log_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'menusalesServiceChargeID' => 'integer',
        'wareHouseAutoID' => 'integer',
        'menuSalesID' => 'integer',
        'menuSalesItemID' => 'integer',
        'menuServiceChargeID' => 'integer',
        'menuMasterID' => 'integer',
        'serviceChargePercentage' => 'float',
        'serviceChargeAmount' => 'float',
        'GLAutoID' => 'integer',
        'beforeDiscountTotalServiceCharge' => 'float',
        'menusalesDiscount' => 'float',
        'menusalesPromotionalDiscount' => 'float',
        'unitMenuServiceCharge' => 'float',
        'menusalesItemQty' => 'integer',
        'companyID' => 'integer',
        'companyCode' => 'string',
        'createdUserGroup' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdDateTime' => 'datetime',
        'createdUserName' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedDateTime' => 'datetime',
        'modifiedUserName' => 'string',
        'timestamp' => 'datetime',
        'is_sync' => 'integer',
        'id_store' => 'integer',
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

    
}
