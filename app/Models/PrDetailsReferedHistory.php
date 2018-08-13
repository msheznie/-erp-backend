<?php
/**
 * =============================================
 * -- File Name : PrDetailsReferedHistory.php
 * -- Project Name : ERP
 * -- Module Name : Pr Details Refered History
 * -- Author : Mohamed Nazir
 * -- Create date : 01- August 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PrDetailsReferedHistory",
 *      required={""},
 *      @SWG\Property(
 *          property="prReferedID",
 *          description="prReferedID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purchaseRequestID",
 *          description="purchaseRequestID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemCode",
 *          description="itemCode",
 *          type="string"
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
 *          property="quantityRequested",
 *          description="quantityRequested",
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
 *          property="quantityOnOrder",
 *          description="quantityOnOrder",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="unitOfMeasure",
 *          description="unitOfMeasure",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="quantityInHand",
 *          description="quantityInHand",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="timesReffered",
 *          description="timesReffered",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="partNumber",
 *          description="partNumber",
 *          type="string"
 *      )
 * )
 */
class PrDetailsReferedHistory extends Model
{

    public $table = 'erp_prdetailsreferedhistory';

    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey  = 'prReferedID';

    public $fillable = [
        'purchaseRequestID',
        'purchaseRequestDetailsID',
        'companySystemID',
        'companyID',
        'itemCategoryID',
        'itemCode',
        'itemPrimaryCode',
        'itemDescription',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'includePLForGRVYN',
        'partNumber',
        'quantityRequested',
        'estimatedCost',
        'totalCost',
        'budgetYear',
        'budjetAmtLocal',
        'budjetAmtRpt',
        'quantityOnOrder',
        'comments',
        'unitOfMeasure',
        'itemClientReferenceNumberMasterID',
        'clientReferenceNumber',
        'quantityInHand',
        'maxQty',
        'minQty',
        'poQuantity',
        'specificationGrade',
        'jobNo',
        'technicalDataSheetAttachment',
        'selectedForPO',
        'prClosedYN',
        'fullyOrdered',
        'poTrackingID',
        'timeStamp',
        'manuallyClosed',
        'manuallyClosedByEmpSystemID',
        'manuallyClosedByEmpID',
        'manuallyClosedByEmpName',
        'manuallyClosedDate',
        'manuallyClosedComment',
        'timesReffered',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'prReferedID' => 'integer',
        'purchaseRequestDetailsID' => 'integer',
        'purchaseRequestID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'itemCategoryID' => 'integer',
        'itemCode' => 'integer',
        'itemPrimaryCode' => 'string',
        'itemDescription' => 'string',
        'itemFinanceCategoryID' => 'integer',
        'itemFinanceCategorySubID' => 'integer',
        'financeGLcodebBSSystemID' => 'integer',
        'financeGLcodebBS' => 'string',
        'financeGLcodePLSystemID' => 'integer',
        'financeGLcodePL' => 'string',
        'includePLForGRVYN' => 'integer',
        'partNumber' => 'string',
        'quantityRequested' => 'float',
        'estimatedCost' => 'float',
        'totalCost' => 'float',
        'budgetYear' => 'integer',
        'budjetAmtLocal' => 'float',
        'budjetAmtRpt' => 'float',
        'quantityOnOrder' => 'float',
        'comments' => 'string',
        'unitOfMeasure' => 'integer',
        'itemClientReferenceNumberMasterID' => 'integer',
        'clientReferenceNumber' => 'string',
        'quantityInHand' => 'float',
        'maxQty' => 'float',
        'minQty' => 'float',
        'poQuantity' => 'float',
        'specificationGrade' => 'string',
        'jobNo' => 'string',
        'technicalDataSheetAttachment' => 'string',
        'selectedForPO' => 'integer',
        'prClosedYN' => 'integer',
        'fullyOrdered' => 'integer',
        'poTrackingID' => 'integer',
        'manuallyClosed' => 'integer',
        'manuallyClosedByEmpSystemID' => 'integer',
        'manuallyClosedByEmpID' => 'string',
        'manuallyClosedByEmpName' => 'string',
        'manuallyClosedDate' => 'string',
        'manuallyClosedComment' => 'string',
        'timesReffered' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function uom(){
        return $this->belongsTo('App\Models\Unit','unitOfMeasure','UnitID');
    }

    public function podetail(){
        return $this->hasMany('App\Models\PurchaseOrderDetails','purchaseRequestDetailsID','purchaseRequestDetailsID');
    }

    public function purchase_request(){
        return $this->belongsTo('App\Models\PurchaseRequest','purchaseRequestID','purchaseRequestID');
    }

    public function purchase_order_process_detail(){
        return $this->belongsTo('App\Models\PurchaseOrderProcessDetails','purchaseRequestID','purchaseRequestID');
    }

    
}
