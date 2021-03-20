<?php
/**
 * =============================================
 * -- File Name : MaterielRequestDetails.php
 * -- Project Name : ERP
 * -- Module Name : Materiel Request Details
 * -- Author : Mohamed Fayas
 * -- Create date : 12- June 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="MaterielRequestDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="RequestDetailsID",
 *          description="RequestDetailsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="RequestID",
 *          description="RequestID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemCode",
 *          description="itemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemDescription",
 *          description="itemDescription",
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
 *          property="financeGLcodebBS",
 *          description="financeGLcodebBS",
 *          type="string"
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
 *          property="partNumber",
 *          description="partNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="unitOfMeasure",
 *          description="unitOfMeasure",
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
 *          property="quantityRequested",
 *          description="quantityRequested",
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
 *          property="convertionMeasureVal",
 *          description="convertionMeasureVal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="quantityOnOrder",
 *          description="quantityOnOrder",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="quantityInHand",
 *          description="quantityInHand",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="estimatedCost",
 *          description="estimatedCost",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="minQty",
 *          description="minQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="maxQty",
 *          description="maxQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="selectedForIssue",
 *          description="selectedForIssue",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ClosedYN",
 *          description="ClosedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="allowCreatePR",
 *          description="allowCreatePR",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="selectedToCreatePR",
 *          description="selectedToCreatePR",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class MaterielRequestDetails extends Model
{

    public $table = 'erp_requestdetails';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'RequestDetailsID';


    public $fillable = [
        'RequestID',
        'itemCode',
        'itemDescription',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBS',
        'financeGLcodePL',
        'includePLForGRVYN',
        'partNumber',
        'unitOfMeasure',
        'unitOfMeasureIssued',
        'quantityRequested',
        'qtyIssuedDefaultMeasure',
        'convertionMeasureVal',
        'comments',
        'quantityOnOrder',
        'quantityInHand',
        'estimatedCost',
        'minQty',
        'maxQty',
        'selectedForIssue',
        'ClosedYN',
        'allowCreatePR',
        'selectedToCreatePR',
        'timesReferred',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'RequestDetailsID' => 'integer',
        'RequestID' => 'integer',
        'itemCode' => 'integer',
        'itemDescription' => 'string',
        'itemFinanceCategoryID' => 'integer',
        'itemFinanceCategorySubID' => 'integer',
        'financeGLcodebBS' => 'string',
        'financeGLcodePL' => 'string',
        'includePLForGRVYN' => 'integer',
        'partNumber' => 'string',
        'unitOfMeasure' => 'integer',
        'unitOfMeasureIssued' => 'integer',
        'quantityRequested' => 'float',
        'qtyIssuedDefaultMeasure' => 'float',
        'convertionMeasureVal' => 'float',
        'comments' => 'string',
        'quantityOnOrder' => 'float',
        'quantityInHand' => 'float',
        'estimatedCost' => 'float',
        'minQty' => 'float',
        'maxQty' => 'float',
        'selectedForIssue' => 'integer',
        'ClosedYN' => 'integer',
        'allowCreatePR' => 'integer',
        'selectedToCreatePR' => 'integer',
        'timesReferred' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function uom_default(){
        return $this->belongsTo('App\Models\Unit','unitOfMeasure','UnitID');
    }

    public function uom_issuing(){
        return $this->belongsTo('App\Models\Unit','unitOfMeasureIssued','UnitID');
    }

    public function item_by(){
        return $this->belongsTo('App\Models\ItemMaster','itemCode','itemCodeSystem');
    }

    public function master(){
        return $this->belongsTo('App\Models\MaterielRequest','RequestID','RequestID');
    }
}
