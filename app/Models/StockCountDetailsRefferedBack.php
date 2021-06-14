<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="StockCountDetailsRefferedBack",
 *      required={""},
 *      @SWG\Property(
 *          property="stockCountDetailsAutoRefferedbackID",
 *          description="stockCountDetailsAutoRefferedbackID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="stockCountDetailsAutoID",
 *          description="stockCountDetailsAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="stockCountAutoID",
 *          description="stockCountAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="stockCountAutoIDCode",
 *          description="stockCountAutoIDCode",
 *          type="string"
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
 *          property="partNumber",
 *          description="partNumber",
 *          type="string"
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
 *          property="systemQty",
 *          description="systemQty",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="noQty",
 *          description="noQty",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="adjustedQty",
 *          description="adjustedQty",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="currentWacLocalCurrencyID",
 *          description="currentWacLocalCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="currentWaclocal",
 *          description="currentWaclocal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="currentWacRptCurrencyID",
 *          description="currentWacRptCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="currentWacRpt",
 *          description="currentWacRpt",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="wacAdjLocal",
 *          description="wacAdjLocal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="wacAdjRptER",
 *          description="wacAdjRptER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="wacAdjRpt",
 *          description="wacAdjRpt",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="wacAdjLocalER",
 *          description="wacAdjLocalER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="currenctStockQty",
 *          description="currenctStockQty",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
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
 *          property="updatedFlag",
 *          description="updatedFlag",
 *          type="boolean"
 *      )
 * )
 */
class StockCountDetailsRefferedBack extends Model
{

    public $table = 'erp_stock_count_details_refferedback';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';


    protected $primaryKey = "stockCountDetailsAutoRefferedbackID";

    public $fillable = [
        'stockCountDetailsAutoID',
        'stockCountAutoID',
        'stockCountAutoIDCode',
        'itemCodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'itemUnitOfMeasure',
        'partNumber',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'includePLForGRVYN',
        'systemQty',
        'noQty',
        'adjustedQty',
        'comments',
        'currentWacLocalCurrencyID',
        'currentWaclocal',
        'currentWacRptCurrencyID',
        'currentWacRpt',
        'wacAdjLocal',
        'wacAdjRptER',
        'wacAdjRpt',
        'wacAdjLocalER',
        'currenctStockQty',
        'timesReferred',
        'timestamp',
        'updatedFlag'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'stockCountDetailsAutoRefferedbackID' => 'integer',
        'stockCountDetailsAutoID' => 'integer',
        'stockCountAutoID' => 'integer',
        'stockCountAutoIDCode' => 'string',
        'itemCodeSystem' => 'integer',
        'itemPrimaryCode' => 'string',
        'itemDescription' => 'string',
        'itemUnitOfMeasure' => 'integer',
        'partNumber' => 'string',
        'itemFinanceCategoryID' => 'integer',
        'itemFinanceCategorySubID' => 'integer',
        'financeGLcodebBSSystemID' => 'integer',
        'financeGLcodebBS' => 'string',
        'financeGLcodePLSystemID' => 'integer',
        'financeGLcodePL' => 'string',
        'includePLForGRVYN' => 'integer',
        'systemQty' => 'float',
        'noQty' => 'float',
        'adjustedQty' => 'float',
        'comments' => 'string',
        'currentWacLocalCurrencyID' => 'integer',
        'currentWaclocal' => 'float',
        'currentWacRptCurrencyID' => 'integer',
        'currentWacRpt' => 'float',
        'wacAdjLocal' => 'float',
        'wacAdjRptER' => 'float',
        'wacAdjRpt' => 'float',
        'wacAdjLocalER' => 'float',
        'currenctStockQty' => 'float',
        'timesReferred' => 'integer',
        'timestamp' => 'datetime',
        'updatedFlag' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function uom(){
        return $this->belongsTo('App\Models\Unit','itemUnitOfMeasure','UnitID');
    }

    public function local_currency(){
        return $this->belongsTo('App\Models\CurrencyMaster','currentWacLocalCurrencyID','currencyID');
    }

    public function rpt_currency(){
        return $this->belongsTo('App\Models\CurrencyMaster','currentWacRptCurrencyID','currencyID');
    }
}
