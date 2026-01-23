<?php
/**
 * =============================================
 * -- File Name : PurchaseOrderProcessDetails.php
 * -- Project Name : ERP
 * -- Module Name : Purchase Order ProcessDetails
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PurchaseOrderProcessDetails
 * @package App\Models
 * @version April 12, 2018, 4:32 am UTC
 *
 * @property integer POProcessMasterID
 * @property integer purchaseRequestID
 * @property integer purchaseRequestDetailsID
 * @property integer poDeliveryLocation
 * @property integer itemCode
 * @property string itemPrimaryCode
 * @property string itemDescription
 * @property integer unitOfMeasure
 * @property string comments
 * @property float quantityRequested
 * @property float orderedQty
 * @property float supplierPOqty
 * @property float supplierCost
 * @property integer selectedSupplier
 * @property integer catalogueMasterID
 * @property integer catalogueDetailID
 * @property string partNumber
 * @property integer itemClientReferenceNumberMasterID
 * @property string clientReferenceNumber
 * @property integer localCurrencyID
 * @property integer companyReportingCurrencyID
 * @property float companyReportingER
 * @property integer selectedForPO
 * @property integer itemFinanceCategoryID
 * @property integer itemFinanceCategorySubID
 * @property integer financeGLcodebBSSystemID
 * @property string financeGLcodebBS
 * @property integer financeGLcodePLSystemID
 * @property string financeGLcodePL
 * @property integer includePLForGRVYN
 * @property integer isAccrued
 * @property integer budgetYear
 * @property integer prBelongsYear
 * @property string|\Carbon\Carbon timeStamp
 */
class PurchaseOrderProcessDetails extends Model
{
    //use SoftDeletes;

    public $table = 'erp_purchaseorderprocessdetails';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'purchaseProcessDetailID';



    public $fillable = [
        'POProcessMasterID',
        'purchaseRequestID',
        'purchaseRequestDetailsID',
        'poDeliveryLocation',
        'itemCode',
        'itemPrimaryCode',
        'itemDescription',
        'unitOfMeasure',
        'comments',
        'quantityRequested',
        'orderedQty',
        'supplierPOqty',
        'supplierCost',
        'selectedSupplier',
        'catalogueMasterID',
        'catalogueDetailID',
        'partNumber',
        'itemClientReferenceNumberMasterID',
        'clientReferenceNumber',
        'localCurrencyID',
        'companyReportingCurrencyID',
        'companyReportingER',
        'selectedForPO',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'includePLForGRVYN',
        'isAccrued',
        'budgetYear',
        'prBelongsYear',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'purchaseProcessDetailID' => 'integer',
        'POProcessMasterID' => 'integer',
        'purchaseRequestID' => 'integer',
        'purchaseRequestDetailsID' => 'integer',
        'poDeliveryLocation' => 'integer',
        'itemCode' => 'integer',
        'itemPrimaryCode' => 'string',
        'itemDescription' => 'string',
        'unitOfMeasure' => 'integer',
        'comments' => 'string',
        'quantityRequested' => 'float',
        'orderedQty' => 'float',
        'supplierPOqty' => 'float',
        'supplierCost' => 'float',
        'selectedSupplier' => 'integer',
        'catalogueMasterID' => 'integer',
        'catalogueDetailID' => 'integer',
        'partNumber' => 'string',
        'itemClientReferenceNumberMasterID' => 'integer',
        'clientReferenceNumber' => 'string',
        'localCurrencyID' => 'integer',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingER' => 'float',
        'selectedForPO' => 'integer',
        'itemFinanceCategoryID' => 'integer',
        'itemFinanceCategorySubID' => 'integer',
        'financeGLcodebBSSystemID' => 'integer',
        'financeGLcodebBS' => 'string',
        'financeGLcodePLSystemID' => 'integer',
        'financeGLcodePL' => 'string',
        'includePLForGRVYN' => 'integer',
        'isAccrued' => 'integer',
        'budgetYear' => 'integer',
        'prBelongsYear' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function po_details(){
        return $this->hasMany('App\Models\PurchaseOrderDetails', 'purchaseProcessDetailID', 'purchaseProcessDetailID');
    }
}
