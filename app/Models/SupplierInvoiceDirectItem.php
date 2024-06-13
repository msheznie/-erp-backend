<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SupplierInvoiceDirectItem",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bookingSuppMasInvAutoID",
 *          description="bookingSuppMasInvAutoID",
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
 *          property="financeGLcodePLSystemID",
 *          description="financeGLcodePLSystemID",
 *          type="integer",
 *          format="int32"
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
 *          property="trackingType",
 *          description="trackingType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="noQty",
 *          description="noQty",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="unitCost",
 *          description="unitCost",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="netAmount",
 *          description="netAmount",
 *          type="number",
 *          format="number"
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
 *          format="number"
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
 *          format="number"
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
 *          format="number"
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
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="costPerUnitLocalCur",
 *          description="costPerUnitLocalCur",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="costPerUnitSupDefaultCur",
 *          description="costPerUnitSupDefaultCur",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="costPerUnitSupTransCur",
 *          description="costPerUnitSupTransCur",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class SupplierInvoiceDirectItem extends Model
{

    public $table = 'supplier_invoice_items';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'bookingSuppMasInvAutoID',
        'companySystemID',
        'itemCode',
        'itemPrimaryCode',
        'itemDescription',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodePLSystemID',
        'includePLForGRVYN',
        'supplierPartNumber',
        'unitOfMeasure',
        'trackingType',
        'noQty',
        'unitCost',
        'netAmount',
        'comment',
        'supplierDefaultCurrencyID',
        'supplierDefaultER',
        'supplierItemCurrencyID',
        'foreignToLocalER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'costPerUnitLocalCur',
        'costPerUnitSupDefaultCur',
        'costPerUnitSupTransCur',
        'costPerUnitComRptCur',
        'discountPercentage',
        'discountAmount',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'VATApplicableOn',
        'vatMasterCategoryID',
        'vatSubCategoryID',
        'exempt_vat_portion',
        'timesReferred',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'whtApplicable',
        'whtAmount',
        'whtEdited'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'bookingSuppMasInvAutoID' => 'integer',
        'companySystemID' => 'integer',
        'itemCode' => 'integer',
        'itemPrimaryCode' => 'string',
        'itemDescription' => 'string',
        'itemFinanceCategoryID' => 'integer',
        'itemFinanceCategorySubID' => 'integer',
        'financeGLcodebBSSystemID' => 'integer',
        'financeGLcodePLSystemID' => 'integer',
        'includePLForGRVYN' => 'integer',
        'supplierPartNumber' => 'string',
        'unitOfMeasure' => 'integer',
        'trackingType' => 'integer',
        'noQty' => 'float',
        'unitCost' => 'float',
        'netAmount' => 'float',
        'comment' => 'string',
        'supplierDefaultCurrencyID' => 'integer',
        'supplierDefaultER' => 'float',
        'supplierItemCurrencyID' => 'integer',
        'foreignToLocalER' => 'float',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingER' => 'float',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'costPerUnitLocalCur' => 'float',
        'costPerUnitSupDefaultCur' => 'float',
        'costPerUnitSupTransCur' => 'float',
        'timesReferred' => 'integer',
        'createdPcID' => 'string',
        'createdUserID' => 'integer',
        'modifiedPc' => 'string',
        'modifiedUser' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'bookingSuppMasInvAutoID' => 'required',
        'companySystemID' => 'required',
        'itemCode' => 'required'
    ];

    public function unit(){
        return $this->belongsTo('App\Models\Unit','unitOfMeasure','UnitID');
    }
    
    public function vat_sub_category(){
        return $this->belongsTo('App\Models\TaxVatCategories','vatSubCategoryID','taxVatSubCategoriesAutoID');
    }

    public function master(){
        return $this->belongsTo('App\Models\BookInvSuppMaster','bookingSuppMasInvAutoID','bookingSuppMasInvAutoID');
    }

    public function budget_detail_pl()
    {
        return $this->belongsTo('App\Models\Budjetdetails', 'financeGLcodePLSystemID','chartOfAccountID');
    }

    public function budget_detail_bs()
    {
        return $this->belongsTo('App\Models\Budjetdetails', 'financeGLcodebBSSystemID','chartOfAccountID');
    }
}
