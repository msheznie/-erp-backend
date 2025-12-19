<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="GrvDetailsRefferedback",
 *      required={""},
 *      @SWG\Property(
 *          property="grvDetailRefferedBackID",
 *          description="grvDetailRefferedBackID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvDetailsID",
 *          description="grvDetailsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvAutoID",
 *          description="grvAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="purchaseOrderMastertID",
 *          description="purchaseOrderMastertID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purchaseOrderDetailsID",
 *          description="purchaseOrderDetailsID",
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
 *          property="supplierPartNumber",
 *          description="supplierPartNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="unitOfMeasure",
 *          description="unitOfMeasure",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="noQty",
 *          description="noQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="prvRecievedQty",
 *          description="prvRecievedQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="poQty",
 *          description="poQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="unitCost",
 *          description="unitCost",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="discountPercentage",
 *          description="discountPercentage",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="discountAmount",
 *          description="discountAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="netAmount",
 *          description="netAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierDefaultCurrencyID",
 *          description="supplierDefaultCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierDefaultER",
 *          description="supplierDefaultER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="supplierItemCurrencyID",
 *          description="supplierItemCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="foreignToLocalER",
 *          description="foreignToLocalER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyID",
 *          description="companyReportingCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingER",
 *          description="companyReportingER",
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
 *          property="localCurrencyER",
 *          description="localCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="addonDistCost",
 *          description="addonDistCost",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="GRVcostPerUnitLocalCur",
 *          description="GRVcostPerUnitLocalCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="GRVcostPerUnitSupDefaultCur",
 *          description="GRVcostPerUnitSupDefaultCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="GRVcostPerUnitSupTransCur",
 *          description="GRVcostPerUnitSupTransCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="GRVcostPerUnitComRptCur",
 *          description="GRVcostPerUnitComRptCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="landingCost_TransCur",
 *          description="landingCost_TransCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="landingCost_LocalCur",
 *          description="landingCost_LocalCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="landingCost_RptCur",
 *          description="landingCost_RptCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="logisticsCharges_TransCur",
 *          description="logisticsCharges_TransCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="logisticsCharges_LocalCur",
 *          description="logisticsCharges_LocalCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="logisticsChargest_RptCur",
 *          description="logisticsChargest_RptCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="assetAllocationDoneYN",
 *          description="assetAllocationDoneYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isContract",
 *          description="isContract",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="totalWHTAmount",
 *          description="totalWHTAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="WHTBearedBySupplier",
 *          description="WHTBearedBySupplier",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="WHTBearedByCompany",
 *          description="WHTBearedByCompany",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="extraComment",
 *          description="extraComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="vatRegisteredYN",
 *          description="vatRegisteredYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierVATEligible",
 *          description="supplierVATEligible",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="VATPercentage",
 *          description="VATPercentage",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="VATAmount",
 *          description="VATAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="VATAmountLocal",
 *          description="VATAmountLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="VATAmountRpt",
 *          description="VATAmountRpt",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="logisticsAvailable",
 *          description="logisticsAvailable",
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
class GrvDetailsRefferedback extends Model
{

    public $table = 'erp_grvdetailsrefferedback';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey  = 'grvDetailRefferedBackID';

    public $fillable = [
        'grvDetailsID',
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
        'financeGLcodePL',
        'includePLForGRVYN',
        'supplierPartNumber',
        'unitOfMeasure',
        'noQty',
        'prvRecievedQty',
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
        'timeStamp',
        'binNumber',
        'detail_project_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'grvDetailRefferedBackID' => 'integer',
        'grvDetailsID' => 'integer',
        'grvAutoID' => 'integer',
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
        'noQty' => 'float',
        'prvRecievedQty' => 'float',
        'poQty' => 'float',
        'unitCost' => 'float',
        'discountPercentage' => 'float',
        'discountAmount' => 'float',
        'netAmount' => 'float',
        'markupPercentage' => 'float',
        'markupTransactionAmount' => 'float',
        'markupLocalAmount' => 'float',
        'markupReportingAmount',
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



}
