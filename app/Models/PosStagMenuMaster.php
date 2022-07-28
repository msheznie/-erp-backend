<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PosStagMenuMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="menuMasterID",
 *          description="menuMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="menuMasterDescription",
 *          description="menuMasterDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="menuImage",
 *          description="menuImage",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="menuCategoryID",
 *          description="menuCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="menuCost",
 *          description="menuCost",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="barcode",
 *          description="barcode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="sellingPrice",
 *          description="sellingPrice",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="pricewithoutTax",
 *          description="pricewithoutTax",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="revenueGLAutoID",
 *          description="revenueGLAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="TAXpercentage",
 *          description="TAXpercentage",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="totalTaxAmount",
 *          description="to matintain total tax amount from srp_erp_pos_menutaxes",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="taxMasterID",
 *          description="taxMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="totalServiceCharge",
 *          description="total service charge srp_erp_pos_menuservicecharge",
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
 *          property="menuStatus",
 *          description="1 Active, 0 inActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="kotID",
 *          description="Kitchen order Ticket ID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="preparationTime",
 *          description="preparationTime",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="isPass",
 *          description="isPass",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isPack",
 *          description="0 - not pax, 1 - pax (bundle) ",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isVeg",
 *          description="0 : Non-Veg,  1 : vegetarian",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isAddOn",
 *          description="isAddOn",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="showImageYN",
 *          description="showImageYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="menuSizeID",
 *          description="menuSizeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="sortOrder",
 *          description="sortOrder",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="sortOder",
 *          description="sortOder",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isDeleted",
 *          description="0 notDeleted, 1 deleted",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="deletedBy",
 *          description="deletedBy",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="deletedDatetime",
 *          description="deletedDatetime",
 *          type="string",
 *          format="date-time"
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
 *          property="transaction_log_id",
 *          description="transaction_log_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class PosStagMenuMaster extends Model
{

    public $table = 'pos_stag_menumaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'menuMasterDescription',
        'menuImage',
        'menuCategoryID',
        'menuCost',
        'barcode',
        'sellingPrice',
        'pricewithoutTax',
        'revenueGLAutoID',
        'TAXpercentage',
        'totalTaxAmount',
        'taxMasterID',
        'totalServiceCharge',
        'companyID',
        'menuStatus',
        'kotID',
        'preparationTime',
        'isPass',
        'isPack',
        'isVeg',
        'isAddOn',
        'showImageYN',
        'menuSizeID',
        'sortOrder',
        'sortOder',
        'isDeleted',
        'deletedBy',
        'deletedDatetime',
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
        'transaction_log_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'menuMasterID' => 'integer',
        'menuMasterDescription' => 'string',
        'menuImage' => 'string',
        'menuCategoryID' => 'integer',
        'menuCost' => 'float',
        'barcode' => 'string',
        'sellingPrice' => 'float',
        'pricewithoutTax' => 'float',
        'revenueGLAutoID' => 'integer',
        'TAXpercentage' => 'float',
        'totalTaxAmount' => 'float',
        'taxMasterID' => 'integer',
        'totalServiceCharge' => 'float',
        'companyID' => 'integer',
        'menuStatus' => 'integer',
        'kotID' => 'integer',
        'preparationTime' => 'float',
        'isPass' => 'integer',
        'isPack' => 'integer',
        'isVeg' => 'integer',
        'isAddOn' => 'integer',
        'showImageYN' => 'integer',
        'menuSizeID' => 'integer',
        'sortOrder' => 'integer',
        'sortOder' => 'integer',
        'isDeleted' => 'integer',
        'deletedBy' => 'integer',
        'deletedDatetime' => 'datetime',
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
        'transaction_log_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'menuMasterDescription' => 'required',
        'menuCategoryID' => 'required'
    ];

    
}
