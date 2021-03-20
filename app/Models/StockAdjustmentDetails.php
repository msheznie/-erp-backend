<?php
/**
 * =============================================
 * -- File Name : StockAdjustmentDetails.php
 * -- Project Name : ERP
 * -- Module Name : Stock Adjustment Details
 * -- Author : Mohamed Fayas
 * -- Create date : 20- August 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="StockAdjustmentDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="stockAdjustmentDetailsAutoID",
 *          description="stockAdjustmentDetailsAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="stockAdjustmentAutoID",
 *          description="stockAdjustmentAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="stockAdjustmentAutoIDCode",
 *          description="stockAdjustmentAutoIDCode",
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
 *          property="noQty",
 *          description="noQty",
 *          type="number",
 *          format="float"
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
 *          format="float"
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
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="wacAdjLocal",
 *          description="wacAdjLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="wacAdjRptER",
 *          description="wacAdjRptER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="wacAdjRpt",
 *          description="wacAdjRpt",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="currenctStockQty",
 *          description="currenctStockQty",
 *          type="number",
 *          format="float"
 *      )
 * )
 */
class StockAdjustmentDetails extends Model
{

    public $table = 'erp_stockadjustmentdetails';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'stockAdjustmentDetailsAutoID';




    public $fillable = [
        'stockAdjustmentAutoID',
        'stockAdjustmentAutoIDCode',
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
        'noQty',
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
        'timestamp',
        'timesReferred'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'stockAdjustmentDetailsAutoID' => 'integer',
        'stockAdjustmentAutoID' => 'integer',
        'stockAdjustmentAutoIDCode' => 'string',
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
        'noQty' => 'float',
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
        'timesReferred' => 'integer'
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

    public function master()
    {
        return $this->belongsTo('App\Models\StockAdjustment', 'stockAdjustmentAutoID', 'stockAdjustmentAutoID');
    }

}
