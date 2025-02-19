<?php
/**
 * =============================================
 * -- File Name : WarehouseItems.php
 * -- Project Name : ERP
 * -- Module Name :  Warehouse Items
 * -- Author : Mohamed Fayas
 * -- Create date : 07- September 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="WarehouseItems",
 *      required={""},
 *      @SWG\Property(
 *          property="warehouseItemsID",
 *          description="warehouseItemsID",
 *          type="integer",
 *          format="int32"
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
 *          property="warehouseSystemCode",
 *          description="warehouseSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemSystemCode",
 *          description="itemSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemPrimaryCode",
 *          description="itemPrimaryCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemDescription",
 *          description="itemDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="unitOfMeasure",
 *          description="unitOfMeasure",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="stockQty",
 *          description="stockQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="maximunQty",
 *          description="maximunQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="minimumQty",
 *          description="minimumQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="rolQuantity",
 *          description="rolQuantity",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="wacValueLocalCurrencyID",
 *          description="wacValueLocalCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="wacValueLocal",
 *          description="wacValueLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="wacValueReportingCurrencyID",
 *          description="wacValueReportingCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="wacValueReporting",
 *          description="wacValueReporting",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="totalQty",
 *          description="totalQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="totalValueLocal",
 *          description="totalValueLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="totalValueRpt",
 *          description="totalValueRpt",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="financeCategoryMaster",
 *          description="financeCategoryMaster",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="financeCategorySub",
 *          description="financeCategorySub",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="binNumber",
 *          description="binNumber",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="toDelete",
 *          description="toDelete",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class WarehouseItems extends Model
{

    public $table = 'warehouseitems';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey  = 'warehouseItemsID';

    public $fillable = [
        'companySystemID',
        'companyID',
        'warehouseSystemCode',
        'itemSystemCode',
        'itemPrimaryCode',
        'itemDescription',
        'unitOfMeasure',
        'stockQty',
        'maximunQty',
        'minimumQty',
        'rolQuantity',
        'wacValueLocalCurrencyID',
        'wacValueLocal',
        'wacValueReportingCurrencyID',
        'wacValueReporting',
        'totalQty',
        'totalValueLocal',
        'totalValueRpt',
        'financeCategoryMaster',
        'financeCategorySub',
        'binNumber',
        'toDelete',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'warehouseItemsID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'warehouseSystemCode' => 'integer',
        'itemSystemCode' => 'integer',
        'itemPrimaryCode' => 'string',
        'itemDescription' => 'string',
        'unitOfMeasure' => 'integer',
        'stockQty' => 'float',
        'maximunQty' => 'float',
        'minimumQty' => 'float',
        'rolQuantity' => 'float',
        'wacValueLocalCurrencyID' => 'integer',
        'wacValueLocal' => 'float',
        'wacValueReportingCurrencyID' => 'integer',
        'wacValueReporting' => 'float',
        'totalQty' => 'float',
        'totalValueLocal' => 'float',
        'totalValueRpt' => 'float',
        'financeCategoryMaster' => 'integer',
        'financeCategorySub' => 'integer',
        'binNumber' => 'integer',
        'toDelete' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function unit(){
        return $this->hasOne('App\Models\Unit','UnitID','unitOfMeasure');
    }
    public function financeMainCategory(){
        return $this->hasOne('App\Models\FinanceItemCategoryMaster','itemCategoryID','financeCategoryMaster');
    }

    public function financeSubCategory(){
        return $this->hasOne('App\Models\FinanceItemCategorySub','itemCategorySubID','financeCategorySub');
    }
    public function local_currency(){
        return $this->belongsTo('App\Models\CurrencyMaster', 'wacValueLocalCurrencyID', 'currencyID');
    }

    public function rpt_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'wacValueReportingCurrencyID', 'currencyID');
    }
    public function warehouse_by()
    {
        return $this->belongsTo('App\Models\WarehouseMaster','warehouseSystemCode','wareHouseSystemCode');
    }
    public function binLocation()
    {
        return $this->belongsTo('App\Models\WarehouseBinLocation','binNumber','binLocationID');
    }
    public function item_by()
    {
        return $this->belongsTo('App\Models\ItemMaster', 'itemSystemCode', 'itemCodeSystem');
    }
}
