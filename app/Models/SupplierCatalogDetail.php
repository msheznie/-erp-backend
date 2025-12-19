<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SupplierCatalogDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="supplierCatalogDetailID",
 *          description="supplierCatalogDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierCatalogMasterID",
 *          description="supplierCatalogMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemCodeSystem",
 *          description="itemCodeSystem",
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
 *          property="itemUnitOfMeasure",
 *          description="itemUnitOfMeasure",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="partNo",
 *          description="partNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="localCurrencyID",
 *          description="localCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="localPrice",
 *          description="localPrice",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="reportingCurrencyID",
 *          description="reportingCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="reportingPrice",
 *          description="reportingPrice",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="leadTime",
 *          description="leadTime",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timstamp",
 *          description="timstamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class SupplierCatalogDetail extends Model
{

    public $table = 'erp_supplier_catalog_detail';
    
    const CREATED_AT = 'timstamp';
    const UPDATED_AT = 'timstamp';

    protected $primaryKey  = 'supplierCatalogDetailID';

    public $fillable = [
        'supplierCatalogMasterID',
        'itemCodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'itemUnitOfMeasure',
        'partNo',
        'localCurrencyID',
        'localPrice',
        'reportingCurrencyID',
        'reportingPrice',
        'leadTime',
        'isDeleted',
        'timstamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'supplierCatalogDetailID' => 'integer',
        'supplierCatalogMasterID' => 'integer',
        'itemCodeSystem' => 'integer',
        'itemPrimaryCode' => 'string',
        'itemDescription' => 'string',
        'itemUnitOfMeasure' => 'integer',
        'partNo' => 'string',
        'localCurrencyID' => 'integer',
        'localPrice' => 'float',
        'reportingCurrencyID' => 'integer',
        'reportingPrice' => 'float',
        'leadTime' => 'integer',
        'isDeleted' => 'integer',
        'timstamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
//        'supplierCatalogDetailID' => 'required'
    ];

    public function master()
    {
        return $this->belongsTo('App\Models\SupplierCatalogMaster', 'supplierCatalogMasterID', 'supplierCatalogMasterID');
    }

    public function uom_default()
    {
        return $this->belongsTo('App\Models\Unit','itemUnitOfMeasure','UnitID');
    }

    public function reportingCurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'reportingCurrencyID', 'currencyID');
    }

    public function local_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'localCurrencyID', 'currencyID');
    }

    public function item_by(){
        return $this->belongsTo('App\Models\ItemMaster','itemCodeSystem','itemCodeSystem');
    }
}
