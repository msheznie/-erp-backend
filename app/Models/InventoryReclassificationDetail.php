<?php
/**
 * =============================================
 * -- File Name : InventoryReclassificationDetail.php
 * -- Project Name : ERP
 * -- Module Name :  Inventory Reclassification
 * -- Author : Mubashir
 * -- Create date : 10 - August 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="InventoryReclassificationDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="inventoryReclassificationDetailID",
 *          description="inventoryReclassificationDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="inventoryreclassificationID",
 *          description="inventoryreclassificationID",
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
 *          property="itemFinanceCategoryID",
 *          description="itemFinanceCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemFinanceCategorySubID",
 *          description="itemFinanceCategorySubID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="financeGLcodebBSSystemID",
 *          description="financeGLcodebBSSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="financeGLcodebBS",
 *          description="financeGLcodebBS",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="financeGLcodePLSystemID",
 *          description="financeGLcodePLSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="financeGLcodePL",
 *          description="financeGLcodePL",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="includePLForGRVYN",
 *          description="includePLForGRVYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="currentStockQty",
 *          description="currentStockQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="unitCostLocal",
 *          description="unitCostLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="unitCostRpt",
 *          description="unitCostRpt",
 *          type="number",
 *          format="float"
 *      )
 * )
 */
class InventoryReclassificationDetail extends Model
{

    public $table = 'erp_inventoryreclassificationdetail';

    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'inventoryReclassificationDetailID';

    public $fillable = [
        'inventoryreclassificationID',
        'itemSystemCode',
        'itemPrimaryCode',
        'itemDescription',
        'unitOfMeasure',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'includePLForGRVYN',
        'currentStockQty',
        'currentWareHouseStockQty',
        'localCurrencyID',
        'unitCostLocal',
        'reportingCurrencyID',
        'unitCostRpt',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'inventoryReclassificationDetailID' => 'integer',
        'inventoryreclassificationID' => 'integer',
        'itemSystemCode' => 'integer',
        'itemPrimaryCode' => 'string',
        'itemDescription' => 'string',
        'unitOfMeasure' => 'integer',
        'itemFinanceCategoryID' => 'integer',
        'itemFinanceCategorySubID' => 'integer',
        'financeGLcodebBSSystemID' => 'integer',
        'financeGLcodebBS' => 'string',
        'financeGLcodePLSystemID' => 'integer',
        'financeGLcodePL' => 'string',
        'includePLForGRVYN' => 'integer',
        'currentStockQty' => 'float',
        'currentWareHouseStockQty' => 'float',
        'localCurrencyID' => 'integer',
        'unitCostLocal' => 'float',
        'reportingCurrencyID' => 'integer',
        'unitCostRpt' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function unit(){
        return $this->belongsTo('App\Models\Unit','unitOfMeasure','UnitID');
    }

    public function itemmaster(){
        return $this->belongsTo('App\Models\ItemMaster','itemSystemCode','itemCodeSystem');
    }

    public function localcurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'localCurrencyID','currencyID');
    }

    public function reportingcurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'reportingCurrencyID','currencyID');
    }

    public function master(){
        return $this->belongsTo('App\Models\InventoryReclassification','inventoryreclassificationID','inventoryreclassificationID');
    }

    
}
