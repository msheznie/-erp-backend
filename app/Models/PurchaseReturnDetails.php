<?php
/**
 * =============================================
 * -- File Name : PurchaseReturnDetails.php
 * -- Project Name : ERP
 * -- Module Name : Purchase Return Details
 * -- Author : Mohamed Fayas
 * -- Create date : 31- July 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PurchaseReturnDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="purhasereturnDetailID",
 *          description="purhasereturnDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purhaseReturnAutoID",
 *          description="purhaseReturnAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="grvAutoID",
 *          description="grvAutoID",
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
 *          property="GRVQty",
 *          description="GRVQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="noQty",
 *          description="noQty",
 *          type="number",
 *          format="float"
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
 *          property="supplierTransactionCurrencyID",
 *          description="supplierTransactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransactionER",
 *          description="supplierTransactionER",
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
 *          property="netAmount",
 *          description="netAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="netAmountLocal",
 *          description="netAmountLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="netAmountRpt",
 *          description="netAmountRpt",
 *          type="number",
 *          format="float"
 *      )
 * )
 */
class PurchaseReturnDetails extends Model
{

    public $table = 'erp_purchasereturndetails';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'purhasereturnDetailID';


    public $fillable = [
        'purhaseReturnAutoID',
        'companyID',
        'grvAutoID',
        'grvDetailsID',
        'itemCode',
        'exempt_vat_portion',
        'itemPrimaryCode',
        'itemDescription',
        'supplierPartNumber',
        'unitOfMeasure',
        'GRVQty',
        'comment',
        'noQty',
        'trackingType',
        'receivedQty',
        'supplierDefaultCurrencyID',
        'supplierDefaultER',
        'supplierTransactionCurrencyID',
        'supplierTransactionER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'GRVcostPerUnitLocalCur',
        'GRVcostPerUnitSupDefaultCur',
        'GRVcostPerUnitSupTransCur',
        'GRVcostPerUnitComRptCur',
        'netAmount',
        'netAmountLocal',
        'netAmountRpt',
        'timeStamp',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'GRVSelectedYN',
        'goodsRecievedYN',
        'vatRegisteredYN',
        'supplierVATEligible',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'vatMasterCategoryID',
        'vatSubCategoryID',
        'includePLForGRVYN'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'purhasereturnDetailID' => 'integer',
        'purhaseReturnAutoID' => 'integer',
        'goodsRecievedYN' => 'integer',
        'vatMasterCategoryID' => 'integer',
        'vatSubCategoryID' => 'integer',
        'GRVSelectedYN' => 'integer',
        'vatRegisteredYN' => 'integer',
        'supplierVATEligible' => 'integer',
        'VATPercentage' => 'float',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'companyID' => 'string',
        'grvAutoID' => 'integer',
        'grvDetailsID' => 'integer',
        'itemCode' => 'integer',
        'itemPrimaryCode' => 'string',
        'itemDescription' => 'string',
        'supplierPartNumber' => 'string',
        'unitOfMeasure' => 'integer',
        'trackingType' => 'integer',
        'GRVQty' => 'float',
        'exempt_vat_portion' => 'float',
        'comment' => 'string',
        'noQty' => 'float',
        'receivedQty' => 'float',
        'supplierDefaultCurrencyID' => 'integer',
        'supplierDefaultER' => 'float',
        'supplierTransactionCurrencyID' => 'integer',
        'supplierTransactionER' => 'float',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingER' => 'float',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'GRVcostPerUnitLocalCur' => 'float',
        'GRVcostPerUnitSupDefaultCur' => 'float',
        'GRVcostPerUnitSupTransCur' => 'float',
        'GRVcostPerUnitComRptCur' => 'float',
        'netAmount' => 'float',
        'netAmountLocal' => 'float',
        'netAmountRpt' => 'float',
        'itemFinanceCategoryID'  => 'integer',
        'itemFinanceCategorySubID' => 'integer',
        'financeGLcodebBSSystemID' => 'integer',
        'financeGLcodebBS' => 'string',
        'financeGLcodePLSystemID' => 'integer',
        'financeGLcodePL' => 'string',
        'includePLForGRVYN' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function master()
    {
        return $this->belongsTo('App\Models\PurchaseReturn', 'purhaseReturnAutoID', 'purhaseReturnAutoID');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'unitOfMeasure', 'UnitID');
    }

    public function grv_master()
    {
        return $this->belongsTo('App\Models\GRVMaster', 'grvAutoID', 'grvAutoID');
    }

    public function grv_detail_master()
    {
        return $this->belongsTo('App\Models\GRVDetails', 'grvDetailsID', 'grvDetailsID');
    }

     public function item_by()
    {
        return $this->belongsTo('App\Models\ItemMaster', 'itemCode', 'itemCodeSystem');
    }

    public function vat_sub_category(){
        return $this->belongsTo('App\Models\TaxVatCategories','vatSubCategoryID','taxVatSubCategoriesAutoID');
    }

    public function budget_detail_pl()
    {
        return $this->belongsTo('App\Models\Budjetdetails', 'financeGLcodePLSystemID','chartOfAccountID');
    }

    public function budget_detail_bs()
    {
        return $this->belongsTo('App\Models\Budjetdetails', 'financeGLcodebBSSystemID','chartOfAccountID');
    }

    public function vatSubCategories()
    {
        return $this->belongsTo('App\Models\TaxVatCategories','vatSubCategoryID', 'taxVatSubCategoriesAutoID');
    }
}
