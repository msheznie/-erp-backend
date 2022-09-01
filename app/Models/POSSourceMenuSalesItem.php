<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSSourceMenuSalesItem",
 *      required={""},
 *      @SWG\Property(
 *          property="menuSalesItemID",
 *          description="menuSalesItemID",
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
 *          property="menuID",
 *          description="menuID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="menuCategoryID",
 *          description="menuCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="warehouseMenuID",
 *          description="warehouseMenuID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="warehouseMenuCategoryID",
 *          description="warehouseMenuCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="defaultUOM",
 *          description="defaultUOM",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="unitOfMeasure",
 *          description="unitOfMeasure",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="conversionRateUOM",
 *          description="conversionRateUOM",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="qty",
 *          description="qty",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="menuSalesPrice",
 *          description="menuSalesPrice",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="salesPriceSubTotal",
 *          description="qty * per item  : total without discount ",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="salesPriceAfterDiscount",
 *          description="salesPriceAfterDiscount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="salesPriceNetTotal",
 *          description="(qty * per item ) - discount ",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="netRevenueTotal",
 *          description="netRevenueTotal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="totalMenuTaxAmount",
 *          description="totalMenuTaxAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="totalMenuTaxAmountAfterDiscount",
 *          description="totalMenuTaxAmountAfterDiscount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="totalMenuServiceCharge",
 *          description="totalMenuServiceCharge",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="totalMenuServiceChargeAfterDiscount",
 *          description="totalMenuServiceChargeAfterDiscount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="discountPer",
 *          description="discountPer",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="discountAmount",
 *          description="discountAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="menuCost",
 *          description="menuCost",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="kotID",
 *          description="kotID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="kitchenNote",
 *          description="kitchenNote",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="KOTAlarm",
 *          description="KOTAlarm",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="KOTFrontPrint",
 *          description="realated to Send KOT button in the front",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="KOTStartDateTime",
 *          description="KOTStartDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="KOTEndDateTime",
 *          description="KOTEndDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="KOTDoneByEmpID",
 *          description="KOTDoneByEmpID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="parentMenuSalesItemID",
 *          description="Add-On ID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isSamplePrinted",
 *          description="0- No 1 - Yes",
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
 *          property="TAXAmount",
 *          description="TAXAmount",
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
 *          property="isTaxEnabled",
 *          description="0 = tax amount will be added to netRevenueTotal & salesPriceNetTotal.  1 = Tax will separate in different table",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrencyID",
 *          description="transactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrency",
 *          description="transactionCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="transactionAmount",
 *          description="transactionAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrencyDecimalPlaces",
 *          description="transactionCurrencyDecimalPlaces",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="transactionExchangeRate",
 *          description="transactionExchangeRate",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalCurrencyID",
 *          description="companyLocalCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalCurrency",
 *          description="companyLocalCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalAmount",
 *          description="companyLocalAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalExchangeRate",
 *          description="companyLocalExchangeRate",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalCurrencyDecimalPlaces",
 *          description="companyLocalCurrencyDecimalPlaces",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyID",
 *          description="companyReportingCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrency",
 *          description="companyReportingCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingAmount",
 *          description="companyReportingAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyDecimalPlaces",
 *          description="companyReportingCurrencyDecimalPlaces",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingExchangeRate",
 *          description="companyReportingExchangeRate",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="isOrderPending",
 *          description="1 - pending , 0 not pending",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isOrderInProgress",
 *          description="1 - on going/cooking , 0 not taken yet",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isOrderCompleted",
 *          description="1 ready to deliver , 0 not ready",
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
 *          property="revenueGLAutoID",
 *          description="revenueGLAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="remarkes",
 *          description="remarkes",
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
 *          property="isUpdated",
 *          description="isUpdated",
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
class POSSourceMenuSalesItem extends Model
{

    public $table = 'pos_source_menusalesitems';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'wareHouseAutoID',
        'menuSalesID',
        'menuID',
        'menuCategoryID',
        'warehouseMenuID',
        'warehouseMenuCategoryID',
        'defaultUOM',
        'unitOfMeasure',
        'conversionRateUOM',
        'qty',
        'menuSalesPrice',
        'salesPriceSubTotal',
        'salesPriceAfterDiscount',
        'salesPriceNetTotal',
        'netRevenueTotal',
        'totalMenuTaxAmount',
        'totalMenuTaxAmountAfterDiscount',
        'totalMenuServiceCharge',
        'totalMenuServiceChargeAfterDiscount',
        'discountPer',
        'discountAmount',
        'menuCost',
        'kotID',
        'kitchenNote',
        'KOTAlarm',
        'KOTFrontPrint',
        'KOTStartDateTime',
        'KOTEndDateTime',
        'KOTDoneByEmpID',
        'parentMenuSalesItemID',
        'isSamplePrinted',
        'TAXpercentage',
        'TAXAmount',
        'taxMasterID',
        'isTaxEnabled',
        'transactionCurrencyID',
        'transactionCurrency',
        'transactionAmount',
        'transactionCurrencyDecimalPlaces',
        'transactionExchangeRate',
        'companyLocalCurrencyID',
        'companyLocalCurrency',
        'companyLocalAmount',
        'companyLocalExchangeRate',
        'companyLocalCurrencyDecimalPlaces',
        'companyReportingCurrencyID',
        'companyReportingCurrency',
        'companyReportingAmount',
        'companyReportingCurrencyDecimalPlaces',
        'companyReportingExchangeRate',
        'isOrderPending',
        'isOrderInProgress',
        'isOrderCompleted',
        'companyID',
        'companyCode',
        'revenueGLAutoID',
        'remarkes',
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
        'isUpdated',
        'transaction_log_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'menuSalesItemID' => 'integer',
        'wareHouseAutoID' => 'integer',
        'menuSalesID' => 'integer',
        'menuID' => 'integer',
        'menuCategoryID' => 'integer',
        'warehouseMenuID' => 'integer',
        'warehouseMenuCategoryID' => 'integer',
        'defaultUOM' => 'string',
        'unitOfMeasure' => 'string',
        'conversionRateUOM' => 'float',
        'qty' => 'float',
        'menuSalesPrice' => 'float',
        'salesPriceSubTotal' => 'float',
        'salesPriceAfterDiscount' => 'float',
        'salesPriceNetTotal' => 'float',
        'netRevenueTotal' => 'float',
        'totalMenuTaxAmount' => 'float',
        'totalMenuTaxAmountAfterDiscount' => 'float',
        'totalMenuServiceCharge' => 'float',
        'totalMenuServiceChargeAfterDiscount' => 'float',
        'discountPer' => 'float',
        'discountAmount' => 'float',
        'menuCost' => 'float',
        'kotID' => 'integer',
        'kitchenNote' => 'string',
        'KOTAlarm' => 'integer',
        'KOTFrontPrint' => 'integer',
        'KOTStartDateTime' => 'datetime',
        'KOTEndDateTime' => 'datetime',
        'KOTDoneByEmpID' => 'integer',
        'parentMenuSalesItemID' => 'integer',
        'isSamplePrinted' => 'integer',
        'TAXpercentage' => 'float',
        'TAXAmount' => 'float',
        'taxMasterID' => 'integer',
        'isTaxEnabled' => 'integer',
        'transactionCurrencyID' => 'integer',
        'transactionCurrency' => 'string',
        'transactionAmount' => 'float',
        'transactionCurrencyDecimalPlaces' => 'boolean',
        'transactionExchangeRate' => 'float',
        'companyLocalCurrencyID' => 'integer',
        'companyLocalCurrency' => 'string',
        'companyLocalAmount' => 'float',
        'companyLocalExchangeRate' => 'float',
        'companyLocalCurrencyDecimalPlaces' => 'boolean',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingCurrency' => 'string',
        'companyReportingAmount' => 'float',
        'companyReportingCurrencyDecimalPlaces' => 'boolean',
        'companyReportingExchangeRate' => 'float',
        'isOrderPending' => 'integer',
        'isOrderInProgress' => 'integer',
        'isOrderCompleted' => 'integer',
        'companyID' => 'integer',
        'companyCode' => 'string',
        'revenueGLAutoID' => 'integer',
        'remarkes' => 'string',
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
        'isUpdated' => 'integer',
        'transaction_log_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'wareHouseAutoID' => 'required',
        'menuSalesID' => 'required',
        'id_store' => 'required'
    ];

    public function menuMaster(){ 
        return $this->hasOne('App\Models\PosSourceMenuMaster', 'menuMasterID', 'menuID');
    }
}
