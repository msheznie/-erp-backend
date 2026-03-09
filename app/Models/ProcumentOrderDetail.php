<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProcumentOrderDetail
 * @package App\Models
 * @version March 30, 2018, 10:52 am UTC
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
class ProcumentOrderDetail extends Model
{
    //use SoftDeletes;

    public $table = 'erp_purchaseorderdetails';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey  = 'purchaseOrderDetailsID';



    public $fillable = [
        'companyID',
        'departmentID',
        'serviceLineCode',
        'purchaseOrderMasterID',
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
        'noQty',
        'noOfDays',
        'unitCost',
        'discountPercentage',
        'discountAmount',
        'netAmount',
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
        'timeStamp',
        'VATApplicableOn',
        'altUnit',
        'altUnitValue'

    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'purchaseOrderDetailsID' => 'integer',
        'companyID' => 'string',
        'departmentID' => 'string',
        'serviceLineCode' => 'string',
        'purchaseOrderMasterID' => 'integer',
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
        'noQty' => 'float',
        'noOfDays' => 'integer',
        'unitCost' => 'float',
        'discountPercentage' => 'float',
        'discountAmount' => 'float',
        'netAmount' => 'float',
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
        'VATApplicableOn' => 'integer',
        'altUnit'  => 'integer',
        'altUnitValue'  => 'float',
        'deleted_at' => 'datetime',
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

    
    public function erpPurchaseordermaster()
    {
        return $this->belongsTo(\App\Models\ErpPurchaseordermaster::class);
    }

    public function productmentOrder() {
        return $this->belongsTo(\App\Models\ProcumentOrder::class,'purchaseOrderMasterID','purchaseOrderID');
    }
}
