<?php
/**
 * =============================================
 * -- File Name : GRVDetails.php
 * -- Project Name : ERP
 * -- Module Name :  GRV Details
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class GRVDetails
 * @package App\Models
 * @version April 11, 2018, 12:13 pm UTC
 *
 * @property integer grvAutoID
 * @property integer companySystemID
 * @property string companyID
 * @property string serviceLineCode
 * @property integer purchaseOrderMastertID
 * @property integer purchaseOrderDetailsID
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
 * @property float noQty
 * @property float prvRecievedQty
 * @property float poQty
 * @property float unitCost
 * @property float discountPercentage
 * @property float discountAmount
 * @property float netAmount
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
 * @property float landingCost_TransCur
 * @property float landingCost_LocalCur
 * @property float landingCost_RptCur
 * @property float logisticsCharges_TransCur
 * @property float logisticsCharges_LocalCur
 * @property float logisticsChargest_RptCur
 * @property integer assetAllocationDoneYN
 * @property integer isContract
 * @property integer timesReferred
 * @property float totalWHTAmount
 * @property float WHTBearedBySupplier
 * @property float WHTBearedByCompany
 * @property string extraComment
 * @property integer vatRegisteredYN
 * @property integer supplierVATEligible
 * @property float VATPercentage
 * @property float VATAmount
 * @property float VATAmountLocal
 * @property float VATAmountRpt
 * @property integer logisticsAvailable
 * @property string createdUserGroup
 * @property string createdPcID
 * @property string createdUserID
 * @property string modifiedPc
 * @property string modifiedUser
 * @property string|\Carbon\Carbon createdDateTime
 * @property string|\Carbon\Carbon timeStamp
 */
class GRVDetails extends Model
{
    //use SoftDeletes;

    public $table = 'erp_grvdetails';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey = 'grvDetailsID';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'grvAutoID',
        'companySystemID',
        'companyID',
        'serviceLineCode',
        'purchaseOrderMastertID',
        'purchaseOrderDetailsID',
        'itemCode',
        'itemPrimaryCode',
        'itemDescription',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'exempt_vat_portion',
        'financeGLcodePL',
        'includePLForGRVYN',
        'supplierPartNumber',
        'unitOfMeasure',
        'wasteQty',
        'noQty',
        'trackingType',
        'prvRecievedQty',
        'returnQty',
        'poQty',
        'unitCost',
        'discountPercentage',
        'discountAmount',
        'netAmount',
        'markupPercentage',
        'markupTransactionAmount',
        'markupLocalAmount',
        'markupReportingAmount',
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
        'landingCost_TransCur',
        'landingCost_LocalCur',
        'landingCost_RptCur',
        'logisticsCharges_TransCur',
        'logisticsCharges_LocalCur',
        'logisticsChargest_RptCur',
        'assetAllocationDoneYN',
        'assetAllocatedQty',
        'isContract',
        'timesReferred',
        'totalWHTAmount',
        'WHTBearedBySupplier',
        'WHTBearedByCompany',
        'extraComment',
        'vatRegisteredYN',
        'supplierVATEligible',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'logisticsAvailable',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'purhasereturnDetailID',
        'purhaseReturnAutoID',
        'timeStamp',
        'vatMasterCategoryID',
        'vatSubCategoryID',
        'binNumber',
        'detail_project_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'grvDetailsID' => 'integer',
        'grvAutoID' => 'integer',
        'purhasereturnDetailID' => 'integer',
        'vatMasterCategoryID' => 'integer',
        'vatSubCategoryID' => 'integer',
        'purhaseReturnAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineCode' => 'string',
        'purchaseOrderMastertID' => 'integer',
        'purchaseOrderDetailsID' => 'integer',
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
        'trackingType' => 'integer',
        'wasteQty' => 'float',
        'noQty' => 'float',
        'prvRecievedQty' => 'float',
        'exempt_vat_portion' => 'float',
        'returnQty' => 'float',
        'poQty' => 'float',
        'unitCost' => 'float',
        'discountPercentage' => 'float',
        'discountAmount' => 'float',
        'netAmount' => 'float',
        'markupPercentage' => 'float',
        'markupTransactionAmount' => 'float',
        'markupLocalAmount' => 'float',
        'markupReportingAmount'=> 'float',
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
        'landingCost_TransCur' => 'float',
        'landingCost_LocalCur' => 'float',
        'landingCost_RptCur' => 'float',
        'logisticsCharges_TransCur' => 'float',
        'logisticsCharges_LocalCur' => 'float',
        'logisticsChargest_RptCur' => 'float',
        'assetAllocationDoneYN' => 'integer',
        'assetAllocatedQty' => 'float',
        'isContract' => 'integer',
        'timesReferred' => 'integer',
        'totalWHTAmount' => 'float',
        'WHTBearedBySupplier' => 'float',
        'WHTBearedByCompany' => 'float',
        'extraComment' => 'string',
        'vatRegisteredYN' => 'integer',
        'supplierVATEligible' => 'integer',
        'VATPercentage' => 'float',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'logisticsAvailable' => 'integer',
        'binNumber' => 'integer',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'detail_project_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function grv_master()
    {
        return $this->belongsTo('App\Models\GRVMaster', 'grvAutoID', 'grvAutoID');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'unitOfMeasure', 'UnitID');
    }

    public function po_master()
    {
        return $this->belongsTo('App\Models\ProcumentOrder', 'purchaseOrderMastertID', 'purchaseOrderID');
    }

    public function prn_master()
    {
        return $this->belongsTo('App\Models\PurchaseReturn', 'purhaseReturnAutoID', 'purhaseReturnAutoID');
    }

    public function item_by()
    {
        return $this->belongsTo('App\Models\ItemMaster', 'itemCode', 'itemCodeSystem');
    }

    public function po_detail()
    {
        return $this->belongsTo('App\Models\PurchaseOrderDetails', 'purchaseOrderDetailsID', 'purchaseOrderDetailsID');
    }

    public function localcurrency()
    {
        return $this->hasOne('App\Models\CurrencyMaster', 'currencyID', 'localCurrencyID');
    }

    public function rptcurrency()
    {
        return $this->hasOne('App\Models\CurrencyMaster',  'currencyID', 'companyReportingCurrencyID');
    }

     public function prn_details()
    {
        return $this->hasMany('App\Models\PurchaseReturnDetails',  'grvDetailsID', 'grvDetailsID');
    }

    public function master(){
        return $this->grv_master();
    }
    public function assetMaster(){ 
        return $this->hasMany('App\Models\FixedAssetMaster',  'docOriginDetailID', 'grvDetailsID');
    }

    public function vat_sub_category(){
        return $this->belongsTo('App\Models\TaxVatCategories','vatSubCategoryID','taxVatSubCategoriesAutoID');
    }


     public function supplier_invoice_item_detail()
    {
        return $this->belongsTo('App\Models\SupplierInvoiceItemDetail', 'grvDetailsID', 'grvDetailsID');
    }

    public function budget_detail_pl()
    {
        return $this->belongsTo('App\Models\Budjetdetails', 'financeGLcodePLSystemID','chartOfAccountID');
    }

    public function budget_detail_bs()
    {
        return $this->belongsTo('App\Models\Budjetdetails', 'financeGLcodebBSSystemID','chartOfAccountID');
    }
    public static function getDirectPOGrv($po_detail_id){
        return self::selectRaw('SUM(noQty - COALESCE(returnQty, 0)) as totalReceivedQty')
            ->where('purchaseOrderDetailsID', $po_detail_id)
            ->whereHas('grv_master', function($query) {
                $query->where('grvCancelledYN', '!=', -1);
            })
            ->first();
    }
}
