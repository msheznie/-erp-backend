<?php
/**
 * =============================================
 * -- File Name : PurchaseOrderDetails.php
 * -- Project Name : ERP
 * -- Module Name :  Purchase Order Details
 * -- Author : Nazir
 * -- Create date : 18 - April 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Awobaz\Compoships\Compoships;

/**
 * Class PurchaseOrderDetails
 * @package App\Models
 * @version March 12, 2018, 5:27 am UTC
 *
 * @property \App\Models\ErpPurchaseordermaster erpPurchaseordermaster
 * @property string companyID
 * @property string departmentID
 * @property string serviceLineCode
 * @property integer purchaseOrderMasterID
 * @property integer POProcessMasterID
 * @property integer WO_purchaseOrderMasterID
 * @property integer WP_purchaseOrderDetailsID
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
 * @property string supplierPartNumber
 * @property integer unitOfMeasure
 * @property integer itemClientReferenceNumberMasterID
 * @property string clientReferenceNumber
 * @property float noQty
 * @property integer noOfDays
 * @property float unitCost
 * @property float discountPercentage
 * @property float discountAmount
 * @property float netAmount
 * @property integer budgetYear
 * @property integer prBelongsYear
 * @property integer isAccrued
 * @property float budjetAmtLocal
 * @property float budjetAmtRpt
 * @property string comment
 * @property integer supplierDefaultCurrencyID
 * @property float supplierDefaultER
 * @property integer supplierItemCurrencyID
 * @property float foreignToLocalER
 * @property integer companyReportingCurrencyID
 * @property float companyReportingER
 * @property integer localCurrencyID
 * @property float localCurrencyER
 * @property float addonDistCost
 * @property float GRVcostPerUnitLocalCur
 * @property float GRVcostPerUnitSupDefaultCur
 * @property float GRVcostPerUnitSupTransCur
 * @property float GRVcostPerUnitComRptCur
 * @property float addonPurchaseReturnCost
 * @property float purchaseRetcostPerUnitLocalCur
 * @property float purchaseRetcostPerUniSupDefaultCur
 * @property float purchaseRetcostPerUnitTranCur
 * @property float purchaseRetcostPerUnitRptCur
 * @property integer GRVSelectedYN
 * @property integer goodsRecievedYN
 * @property integer logisticSelectedYN
 * @property integer logisticRecievedYN
 * @property integer isAccruedYN
 * @property integer accrualJVID
 * @property integer timesReferred
 * @property float totalWHTAmount
 * @property float WHTBearedBySupplier
 * @property float WHTBearedByCompany
 * @property float VATPercentage
 * @property float VATAmount
 * @property float VATAmountLocal
 * @property float VATAmountRpt
 * @property string createdUserGroup
 * @property string createdPcID
 * @property string createdUserID
 * @property string modifiedPc
 * @property string modifiedUser
 * @property string|\Carbon\Carbon createdDateTime
 * @property string|\Carbon\Carbon timeStamp
 */
class PurchaseOrderDetails extends Model
{
    //use SoftDeletes;

    public $table = 'erp_purchaseorderdetails';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey  = 'purchaseOrderDetailsID';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'companyID',
        'companySystemID',
        'exempt_vat_portion',
        'departmentID',
        'serviceLineCode',
        'serviceLineSystemID',
        'purchaseOrderMasterID',
        'purchaseRequestDetailsID',
        'purchaseRequestID',
        'POProcessMasterID',
        'WO_purchaseOrderMasterID',
        'WP_purchaseOrderDetailsID',
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
        'supplierPartNumber',
        'unitOfMeasure',
        'itemClientReferenceNumberMasterID',
        'clientReferenceNumber',
        'requestedQty',
        'noQty',
        'balanceQty',
        'noOfDays',
        'unitCost',
        'discountPercentage',
        'discountAmount',
        'netAmount',
        'markupPercentage',
        'markupTransactionAmount',
        'markupLocalAmount',
        'markupReportingAmount',
        'budgetYear',
        'prBelongsYear',
        'isAccrued',
        'budjetAmtLocal',
        'budjetAmtRpt',
        'comment',
        'supplierDefaultCurrencyID',
        'supplierDefaultER',
        'supplierItemCurrencyID',
        'foreignToLocalER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'addonDistCost',
        'GRVcostPerUnitLocalCur',
        'GRVcostPerUnitSupDefaultCur',
        'GRVcostPerUnitSupTransCur',
        'GRVcostPerUnitComRptCur',
        'addonPurchaseReturnCost',
        'purchaseRetcostPerUnitLocalCur',
        'purchaseRetcostPerUniSupDefaultCur',
        'purchaseRetcostPerUnitTranCur',
        'purchaseRetcostPerUnitRptCur',
        'GRVSelectedYN',
        'goodsRecievedYN',
        'logisticSelectedYN',
        'logisticRecievedYN',
        'isAccruedYN',
        'accrualJVID',
        'timesReferred',
        'totalWHTAmount',
        'WHTBearedBySupplier',
        'WHTBearedByCompany',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'supplierCatalogMasterID',
        'supplierCatalogDetailID',
        'timeStamp',
        'madeLocallyYN',
        'vatMasterCategoryID',
        'vatSubCategoryID',
        'VATApplicableOn',
        'altUnit',
        'altUnitValue',
        'detail_project_id',
        'contractID',
        'contractDescription'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'purchaseOrderDetailsID' => 'integer',
        'companyID' => 'string',
        'companySystemID' => 'integer',
        'vatMasterCategoryID' => 'integer',
        'vatSubCategoryID' => 'integer',
        'departmentID' => 'string',
        'serviceLineCode' => 'string',
        'serviceLineSystemID' => 'integer',
        'purchaseOrderMasterID' => 'integer',
        'purchaseRequestDetailsID' => 'integer',
        'purchaseRequestID' => 'integer',
        'POProcessMasterID' => 'integer',
        'WO_purchaseOrderMasterID' => 'integer',
        'WP_purchaseOrderDetailsID' => 'integer',
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
        'supplierPartNumber' => 'string',
        'unitOfMeasure' => 'integer',
        'itemClientReferenceNumberMasterID' => 'integer',
        'clientReferenceNumber' => 'string',
        'requestedQty' => 'float',
        'exempt_vat_portion' => 'float',
        'noQty' => 'float',
        'balanceQty' => 'float',
        'noOfDays' => 'integer',
        'unitCost' => 'float',
        'discountPercentage' => 'float',
        'discountAmount' => 'float',
        'netAmount' => 'float',
        'markupPercentage' => 'float',
        'markupTransactionAmount' => 'float',
        'markupLocalAmount' => 'float',
        'markupReportingAmount',
        'budgetYear' => 'integer',
        'prBelongsYear' => 'integer',
        'isAccrued' => 'integer',
        'budjetAmtLocal' => 'float',
        'budjetAmtRpt' => 'float',
        'comment' => 'string',
        'supplierDefaultCurrencyID' => 'integer',
        'supplierDefaultER' => 'float',
        'supplierItemCurrencyID' => 'integer',
        'foreignToLocalER' => 'float',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingER' => 'float',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'addonDistCost' => 'float',
        'GRVcostPerUnitLocalCur' => 'float',
        'GRVcostPerUnitSupDefaultCur' => 'float',
        'GRVcostPerUnitSupTransCur' => 'float',
        'GRVcostPerUnitComRptCur' => 'float',
        'addonPurchaseReturnCost' => 'float',
        'purchaseRetcostPerUnitLocalCur' => 'float',
        'purchaseRetcostPerUniSupDefaultCur' => 'float',
        'purchaseRetcostPerUnitTranCur' => 'float',
        'purchaseRetcostPerUnitRptCur' => 'float',
        'GRVSelectedYN' => 'integer',
        'goodsRecievedYN' => 'integer',
        'logisticSelectedYN' => 'integer',
        'logisticRecievedYN' => 'integer',
        'isAccruedYN' => 'integer',
        'accrualJVID' => 'integer',
        'timesReferred' => 'integer',
        'totalWHTAmount' => 'float',
        'WHTBearedBySupplier' => 'float',
        'WHTBearedByCompany' => 'float',
        'VATPercentage' => 'float',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'madeLocallyYN' => 'integer',
        'supplierCatalogMasterID' => 'integer',
        'supplierCatalogDetailID' => 'integer',
        'altUnit'  => 'integer',
        'altUnitValue'  => 'float',
        'detail_project_id' => 'integer',
        'contractID' => 'string',
        'contractDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function order()
    {
        return $this->belongsTo(\App\Models\ProcumentOrder::class,'purchaseOrderMasterID','purchaseOrderID');
    }

    public function unit(){
        return $this->belongsTo('App\Models\Unit','unitOfMeasure','UnitID');
    }

    public function altUom(){
        return $this->belongsTo('App\Models\Unit','altUnit','UnitID');
    }


    public function reporting_currency(){
        return $this->belongsTo('App\Models\CurrencyMaster','companyReportingCurrencyID','currencyID');
    }

    public function grv_details(){
        return $this->hasMany('App\Models\GRVDetails', 'purchaseOrderDetailsID', 'purchaseOrderDetailsID');
    }

    public function financecategory(){
        return $this->belongsTo('App\Models\FinanceItemCategoryMaster','itemFinanceCategoryID','itemCategoryID');
    }

    public function financecategorysub(){
        return $this->belongsTo('App\Models\FinanceItemCategorySub','itemFinanceCategorySubID','itemCategorySubID');
    }

    public function requestDetail(){
        return $this->belongsTo('App\Models\PurchaseRequestDetails','purchaseRequestDetailsID','purchaseRequestDetailsID');
    }

    public function setRptTotalAttribute()
    {
        $this->attributes['rpt_total'] = $this->GRVcostPerUnitLocalCur * $this->noQty;
    }

    public function item_ledger(){
        return $this->hasMany('App\Models\ErpItemLedger', 'itemSystemCode', 'itemCode');
    }

    public function closed_by(){
        return $this->belongsTo('App\Models\Employee','manuallyClosedByEmpSystemID','employeeSystemID');
    }

    public function scopeRequestDetailSum($q,$purchaseRequestDetailsID = 0){
        return $q->where('purchaseRequestDetailsID', $purchaseRequestDetailsID)->sum('noQty');
    }

    public function grvDetails(){ 
        return $this->hasMany('App\Models\GRVDetails', 'purchaseOrderDetailsID', 'purchaseOrderDetailsID');  
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
        return $this->hasMany('App\Models\SegmentAllocatedItem', 'documentDetailAutoID', 'purchaseOrderDetailsID');

    }

    public function vat_sub_category(){
        return $this->belongsTo('App\Models\TaxVatCategories','vatSubCategoryID','taxVatSubCategoriesAutoID');
    }

    public function item(){
        return $this->hasOne('App\Models\ItemMaster', 'itemCodeSystem', 'itemCode');

    }

    public function appointmentDetails()
    {
        return $this->hasMany('App\Models\AppointmentDetails',  'po_detail_id', 'purchaseOrderDetailsID');
    }

    public function productmentOrder() {
        return $this->belongsTo(\App\Models\ProcumentOrder::class,'purchaseOrderMasterID','purchaseOrderID');
    }
    
    public function project()
    {
        return $this->belongsTo('App\Models\ErpProjectMaster', 'detail_project_id', 'id');
    }
}
