<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustomerCatalogDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="customerCatalogDetailID",
 *          description="customerCatalogDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerCatalogMasterID",
 *          description="customerCatalogMasterID",
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
 *          property="isDelete",
 *          description="isDelete",
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
class CustomerCatalogDetail extends Model
{

    public $table = 'erp_customer_catalog_detail';
    
    const CREATED_AT = 'timstamp';
    const UPDATED_AT = 'timstamp';

    protected $primaryKey  = 'customerCatalogDetailID';

    public $fillable = [
        'customerCatalogMasterID',
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
        'customerCatalogDetailID' => 'integer',
        'customerCatalogMasterID' => 'integer',
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
//        'customerCatalogDetailID' => 'required',
//        'isDelete' => 'required'
    ];


    public function master()
    {
        return $this->belongsTo('App\Models\CustomerCatalogMaster', 'customerCatalogMasterID', 'customerCatalogMasterID');
    }

    public function uom_default()
    {
        return $this->belongsTo('App\Models\Unit','itemUnitOfMeasure','UnitID');
    }

    public function reporting_currency()
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
