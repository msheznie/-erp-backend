<?php
/**
 * =============================================
 * -- File Name : StockReceiveDetails.php
 * -- Project Name : ERP
 * -- Module Name : StockReceiveDetails
 * -- Author : Mohamed Fayas
 * -- Create date : 23 - July 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="StockReceiveDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="stockReceiveDetailsID",
 *          description="stockReceiveDetailsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="stockReceiveAutoID",
 *          description="stockReceiveAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="stockReceiveCode",
 *          description="stockReceiveCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="stockTransferAutoID",
 *          description="stockTransferAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="stockTransferCode",
 *          description="stockTransferCode",
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
 *          property="financeGLcodebBS",
 *          description="financeGLcodebBS",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="localCurrencyID",
 *          description="localCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="unitCostLocal",
 *          description="unitCostLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="reportingCurrencyID",
 *          description="reportingCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="unitCostRpt",
 *          description="unitCostRpt",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="qty",
 *          description="qty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class StockReceiveDetails extends Model
{

    public $table = 'erp_stockreceivedetails';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'stockReceiveDetailsID';


    public $fillable = [
        'stockReceiveAutoID',
        'stockReceiveCode',
        'stockTransferAutoID',
        'stockTransferCode',
        'stockTransferDate',
        'itemCodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'unitOfMeasure',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBS',
        'financeGLcodebBSSystemID',
        'localCurrencyID',
        'unitCostLocal',
        'reportingCurrencyID',
        'unitCostRpt',
        'qty',
        'comments',
        'timesReferred',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'stockReceiveDetailsID' => 'integer',
        'stockReceiveAutoID' => 'integer',
        'stockReceiveCode' => 'string',
        'stockTransferAutoID' => 'integer',
        'stockTransferCode' => 'string',
        'itemCodeSystem' => 'integer',
        'itemPrimaryCode' => 'string',
        'itemDescription' => 'string',
        'unitOfMeasure' => 'integer',
        'itemFinanceCategoryID' => 'integer',
        'itemFinanceCategorySubID' => 'integer',
        'financeGLcodebBS' => 'string',
        'financeGLcodebBSSystemID' => 'integer',
        'localCurrencyID' => 'integer',
        'unitCostLocal' => 'float',
        'reportingCurrencyID' => 'integer',
        'unitCostRpt' => 'float',
        'qty' => 'float',
        'comments' => 'string',
        'timesReferred' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function master_by()
    {
        return $this->belongsTo('App\Models\StockTransfer', 'stockReceiveAutoID', 'stockReceiveAutoID');
    }

    public function unit_by()
    {
        return $this->belongsTo('App\Models\Unit', 'unitOfMeasure', 'UnitID');
    }

    public function transfer()
    {
        return $this->belongsTo('App\Models\StockTransfer', 'stockTransferAutoID', 'stockTransferAutoID');
    }

    public function item_by()
    {
        return $this->belongsTo('App\Models\ItemMaster', 'itemCodeSystem', 'itemCodeSystem');
    }

    public function stock_receive()
    {
        return $this->belongsTo('App\Models\StockReceive', 'stockReceiveAutoID', 'stockReceiveAutoID');
    }

    public function master()
    {
        return $this->stock_receive();
    }
}
