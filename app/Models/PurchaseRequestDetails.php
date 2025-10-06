<?php
/**
 * =============================================
 * -- File Name : Purchase Request Details.php
 * -- Project Name : ERP
 * -- Module Name : Purchase Request Details
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUomQuantityFormatting;

/**
 * Class PurchaseRequestDetails
 * @package App\Models
 * @version March 29, 2018, 11:41 am UTC
 *
 * @property integer purchaseRequestID
 * @property integer companySystemID
 * @property string companyID
 * @property integer itemCategoryID
 * @property integer itemCode
 * @property string itemPrimaryCode
 * @property string itemDescription
 * @property integer itemFinanceCategoryID
 * @property integer itemFinanceCategorySubID
 * @property integer financeGLcodebBSSystemID
 * @property string financeGLcodebBS
 * @property integer financeGLcodePLSystemID
 * @property string financeGLcodePL
 * @property integer includePLForGRVYN
 * @property string partNumber
 * @property float quantityRequested
 * @property float estimatedCost
 * @property float totalCost
 * @property integer budgetYear
 * @property float budjetAmtLocal
 * @property float budjetAmtRpt
 * @property float quantityOnOrder
 * @property string comments
 * @property integer unitOfMeasure
 * @property integer itemClientReferenceNumberMasterID
 * @property string clientReferenceNumber
 * @property float quantityInHand
 * @property float maxQty
 * @property float minQty
 * @property float poQuantity
 * @property string specificationGrade
 * @property string jobNo
 * @property string technicalDataSheetAttachment
 * @property integer selectedForPO
 * @property integer prClosedYN
 * @property integer fullyOrdered
 * @property integer poTrackingID
 * @property string|\Carbon\Carbon timeStamp
 */
class PurchaseRequestDetails extends Model
{
    use HasUomQuantityFormatting;
    //use SoftDeletes;

    public $table = 'erp_purchaserequestdetails';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'purchaseRequestDetailsID';


    protected $dates = ['deleted_at'];

//    protected $appends = ['quantityRequested'];
    public $fillable = [
        'purchaseRequestID',
        'materialReqeuestID',
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
        'isMRPulled',
        'altUnit',
        'altUnitValue',
        'is_eligible_mr'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
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
        'isMRPulled' => 'boolean',
        'altUnit'  => 'integer',
        'altUnitValue'  => 'float',
        'is_eligible_mr' => 'boolean'
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

    /**
     * Mutator for quantityRequested - formats using UOM decimal precision for saving
     */
    public function setQuantityRequestedAttribute($value)
    {
        $this->setQuantityAttribute('quantityRequested', $value);
    }
    /**
     * Mutator for quantityRequested - formats using UOM decimal precision for saving
     */
    public function setAltUnitValueAttribute($value)
    {
        $this->setQuantityAttribute('altUnitValue', $value, $this->altUnit);
    }

    /**
     * Accessor for quantityRequested - formats using UOM display round off for display
     * Commented the function to check the frontend based calculations
     */
//    public function getQuantityRequestedAttribute($value)
//    {
//        return $this->getQuantityAttribute($this->attributes['quantityRequested'] ?? null, $this->UnitID);
//    }

    public function altUom(){
        return $this->belongsTo('App\Models\Unit','altUnit','UnitID');
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

    public function closed_by(){
        return $this->belongsTo('App\Models\Employee','manuallyClosedByEmpSystemID','employeeSystemID');
    }

    public function budget_detail_pl()
    {
        return $this->belongsTo('App\Models\Budjetdetails', 'financeGLcodePLSystemID','chartOfAccountID');
    } 

    public function budget_detail_bs()
    {
        return $this->belongsTo('App\Models\Budjetdetails', 'financeGLcodebBSSystemID','chartOfAccountID');
    }

    public function allocations(){
        return $this->hasMany('App\Models\SegmentAllocatedItem', 'documentDetailAutoID', 'purchaseRequestDetailsID');
    }


}
