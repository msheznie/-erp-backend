<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSStagMenueSalesItemDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="menuSalesItemDetailID",
 *          description="menuSalesItemDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="warehouseAutoID",
 *          description="warehouseAutoID",
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
 *          property="menuSalesID",
 *          description="menuSalesID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemAutoID",
 *          description="itemAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="qty",
 *          description="qty",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="UOM",
 *          description="UOM",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="UOMID",
 *          description="UOMID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="cost",
 *          description="cost",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="actualInventoryCost",
 *          description="load based on the policy, maintain exact cost of the item master",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="menuID",
 *          description="menuID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="menuSalesQty",
 *          description="menuSalesQty",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="costGLAutoID",
 *          description="Raw material cost GL account from item master table",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="assetGLAutoID",
 *          description="Raw material asset GL account from item master table",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isWastage",
 *          description="isWastage",
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
 *          property="createdUserGroup",
 *          description="createdUserGroup",
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
 *          property="timeStamp",
 *          description="timeStamp",
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
class POSStagMenueSalesItemDetail extends Model
{

    public $table = 'pos_stag_menusalesitemdetails';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'warehouseAutoID',
        'menuSalesItemID',
        'menuSalesID',
        'itemAutoID',
        'qty',
        'UOM',
        'UOMID',
        'cost',
        'actualInventoryCost',
        'menuID',
        'menuSalesQty',
        'costGLAutoID',
        'assetGLAutoID',
        'isWastage',
        'companyID',
        'companyCode',
        'segmentID',
        'segmentCode',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'createdUserGroup',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timeStamp',
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
        'menuSalesItemDetailID' => 'integer',
        'warehouseAutoID' => 'integer',
        'menuSalesItemID' => 'integer',
        'menuSalesID' => 'integer',
        'itemAutoID' => 'integer',
        'qty' => 'float',
        'UOM' => 'string',
        'UOMID' => 'integer',
        'cost' => 'float',
        'actualInventoryCost' => 'float',
        'menuID' => 'integer',
        'menuSalesQty' => 'float',
        'costGLAutoID' => 'integer',
        'assetGLAutoID' => 'integer',
        'isWastage' => 'integer',
        'companyID' => 'integer',
        'companyCode' => 'string',
        'segmentID' => 'integer',
        'segmentCode' => 'string',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdDateTime' => 'datetime',
        'createdUserName' => 'string',
        'createdUserGroup' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedDateTime' => 'datetime',
        'modifiedUserName' => 'string',
        'timeStamp' => 'datetime',
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
        'warehouseAutoID' => 'required',
        'menuSalesItemID' => 'required'
    ];

    
}
