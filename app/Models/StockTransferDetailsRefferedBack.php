<?php
/**
 * =============================================
 * -- File Name : StockTransferDetailsRefferedBack.php
 * -- Project Name : ERP
 * -- Module Name :  Stock Transfer Details Referred Back
 * -- Author : Mohamed Fayas
 * -- Create date : 29 - November 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="StockTransferDetailsRefferedBack",
 *      required={""},
 *      @SWG\Property(
 *          property="stockTransferDetailsRefferedID",
 *          description="stockTransferDetailsRefferedID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="stockTransferDetailsID",
 *          description="stockTransferDetailsID",
 *          type="integer",
 *          format="int32"
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
 *          property="financeGLcodebBSSystemID",
 *          description="financeGLcodebBSSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="qty",
 *          description="qty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="currentStockQty",
 *          description="currentStockQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="warehouseStockQty",
 *          description="warehouseStockQty",
 *          type="number",
 *          format="float"
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
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="addedToRecieved",
 *          description="addedToRecieved",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="stockRecieved",
 *          description="stockRecieved",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      )
 * )
 */
class StockTransferDetailsRefferedBack extends Model
{

    public $table = 'erp_stocktransferdetailsrefferedback';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'stockTransferDetailsRefferedID';


    public $fillable = [
        'stockTransferDetailsID',
        'stockTransferAutoID',
        'stockTransferCode',
        'itemCodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'unitOfMeasure',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBS',
        'financeGLcodebBSSystemID',
        'qty',
        'currentStockQty',
        'warehouseStockQty',
        'localCurrencyID',
        'unitCostLocal',
        'reportingCurrencyID',
        'unitCostRpt',
        'comments',
        'addedToRecieved',
        'stockRecieved',
        'timesReferred',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'stockTransferDetailsRefferedID' => 'integer',
        'stockTransferDetailsID' => 'integer',
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
        'qty' => 'float',
        'currentStockQty' => 'float',
        'warehouseStockQty' => 'float',
        'localCurrencyID' => 'integer',
        'unitCostLocal' => 'float',
        'reportingCurrencyID' => 'integer',
        'unitCostRpt' => 'float',
        'comments' => 'string',
        'addedToRecieved' => 'float',
        'stockRecieved' => 'float',
        'timesReferred' => 'integer',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string'
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
        return $this->belongsTo('App\Models\StockTransfer', 'stockTransferAutoID', 'stockTransferAutoID');
    }

    public function unit_by()
    {
        return $this->belongsTo('App\Models\Unit', 'unitOfMeasure', 'UnitID');
    }
}
