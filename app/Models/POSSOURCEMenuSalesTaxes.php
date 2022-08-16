<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSSOURCEMenuSalesTaxes",
 *      required={""},
 *      @SWG\Property(
 *          property="menuSalesTaxID",
 *          description="menuSalesTaxID",
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
 *          property="menuID",
 *          description="menuID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="menutaxID",
 *          description="menutaxID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="taxmasterID",
 *          description="taxmasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="vatType",
 *          description="vatType",
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
 *          property="taxPercentage",
 *          description="taxPercentage",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="taxAmount",
 *          description="taxAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="beforeDiscountTotalTaxAmount",
 *          description="beforeDiscountTotalTaxAmount",
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
 *          property="unitMenuTaxAmount",
 *          description="unitMenuTaxAmount",
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
 *      ),
 *      @SWG\Property(
 *          property="isSync",
 *          description="0 => Not Synced 
1 => Send to ERP 
2 => Fully Synced",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class POSSOURCEMenuSalesTaxes extends Model
{

    public $table = 'pos_source_menusalestaxes';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'wareHouseAutoID',
        'menuSalesID',
        'menuSalesItemID',
        'menuID',
        'menutaxID',
        'taxmasterID',
        'vatType',
        'GLCode',
        'taxPercentage',
        'taxAmount',
        'beforeDiscountTotalTaxAmount',
        'menusalesDiscount',
        'menusalesPromotionalDiscount',
        'unitMenuTaxAmount',
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
        'transaction_log_id',
        'isSync'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'menuSalesTaxID' => 'integer',
        'wareHouseAutoID' => 'integer',
        'menuSalesID' => 'integer',
        'menuSalesItemID' => 'integer',
        'menuID' => 'integer',
        'menutaxID' => 'integer',
        'taxmasterID' => 'integer',
        'vatType' => 'integer',
        'GLCode' => 'integer',
        'taxPercentage' => 'float',
        'taxAmount' => 'float',
        'beforeDiscountTotalTaxAmount' => 'float',
        'menusalesDiscount' => 'float',
        'menusalesPromotionalDiscount' => 'float',
        'unitMenuTaxAmount' => 'float',
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
        'transaction_log_id' => 'integer',
        'isSync' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'wareHouseAutoID' => 'required',
        'isSync' => 'required'
    ];

    
}
