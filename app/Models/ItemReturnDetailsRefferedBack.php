<?php
/**
 * =============================================
 * -- File Name : ItemReturnDetailsRefferedBack.php
 * -- Project Name : ERP
 * -- Module Name :  Item Return Details Reffered Back
 * -- Author : Mohamed Fayas
 * -- Create date : 06 - December 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ItemReturnDetailsRefferedBack",
 *      required={""},
 *      @SWG\Property(
 *          property="itemReturnDetailRefferedbackID",
 *          description="itemReturnDetailRefferedbackID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemReturnDetailID",
 *          description="itemReturnDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemReturnAutoID",
 *          description="itemReturnAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemReturnCode",
 *          description="itemReturnCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="issueCodeSystem",
 *          description="issueCodeSystem",
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
 *          property="unitOfMeasureIssued",
 *          description="unitOfMeasureIssued",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="qtyIssued",
 *          description="qtyIssued",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="convertionMeasureVal",
 *          description="convertionMeasureVal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="qtyIssuedDefaultMeasure",
 *          description="qtyIssuedDefaultMeasure",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
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
 *          property="qtyFromIssue",
 *          description="qtyFromIssue",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="selectedForBillingOP",
 *          description="selectedForBillingOP",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="selectedForBillingOPtemp",
 *          description="selectedForBillingOPtemp",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="opTicketNo",
 *          description="opTicketNo",
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
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class ItemReturnDetailsRefferedBack extends Model
{

    public $table = 'erp_itemreturndetails_refferedback';

    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'itemReturnDetailRefferedbackID';



    public $fillable = [
        'itemReturnDetailID',
        'itemReturnAutoID',
        'itemReturnCode',
        'issueCodeSystem',
        'itemCodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'itemUnitOfMeasure',
        'unitOfMeasureIssued',
        'qtyIssued',
        'trackingType',
        'convertionMeasureVal',
        'qtyIssuedDefaultMeasure',
        'comments',
        'localCurrencyID',
        'unitCostLocal',
        'reportingCurrencyID',
        'unitCostRpt',
        'qtyFromIssue',
        'selectedForBillingOP',
        'selectedForBillingOPtemp',
        'opTicketNo',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'includePLForGRVYN',
        'timesReferred',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'itemReturnDetailRefferedbackID' => 'integer',
        'itemReturnDetailID' => 'integer',
        'itemReturnAutoID' => 'integer',
        'itemReturnCode' => 'string',
        'issueCodeSystem' => 'integer',
        'itemCodeSystem' => 'integer',
        'itemPrimaryCode' => 'string',
        'itemDescription' => 'string',
        'itemUnitOfMeasure' => 'integer',
        'unitOfMeasureIssued' => 'integer',
        'qtyIssued' => 'float',
        'trackingType' => 'integer',
        'convertionMeasureVal' => 'float',
        'qtyIssuedDefaultMeasure' => 'float',
        'comments' => 'string',
        'localCurrencyID' => 'integer',
        'unitCostLocal' => 'float',
        'reportingCurrencyID' => 'integer',
        'unitCostRpt' => 'float',
        'qtyFromIssue' => 'float',
        'selectedForBillingOP' => 'integer',
        'selectedForBillingOPtemp' => 'integer',
        'opTicketNo' => 'integer',
        'itemFinanceCategoryID' => 'integer',
        'itemFinanceCategorySubID' => 'integer',
        'financeGLcodebBSSystemID' => 'integer',
        'financeGLcodebBS' => 'string',
        'financeGLcodePLSystemID' => 'integer',
        'financeGLcodePL' => 'string',
        'includePLForGRVYN' => 'integer',
        'timesReferred' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function uom_issued(){
        return $this->belongsTo('App\Models\Unit','itemUnitOfMeasure','UnitID');
    }

    public function uom_receiving(){
        return $this->belongsTo('App\Models\Unit','unitOfMeasureIssued','UnitID');
    }

    public function issue(){
        return $this->belongsTo('App\Models\ItemIssueMaster','issueCodeSystem','itemIssueAutoID');
    }
}
