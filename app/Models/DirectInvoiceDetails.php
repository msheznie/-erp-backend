<?php
/**
 * =============================================
 * -- File Name : DirectInvoiceDetails.php
 * -- Project Name : ERP
 * -- Module Name :  DirectInvoiceDetails
 * -- Author : Nazir
 * -- Create date : 09 - August 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="DirectInvoiceDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="directInvoiceDetailsID",
 *          description="directInvoiceDetailsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="directInvoiceAutoID",
 *          description="directInvoiceAutoID",
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
 *          property="glCode",
 *          description="glCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="glCodeDes",
 *          description="glCodeDes",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="percentage",
 *          description="percentage",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="DIAmountCurrency",
 *          description="DIAmountCurrency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DIAmountCurrencyER",
 *          description="DIAmountCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="DIAmount",
 *          description="DIAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="localCurrency",
 *          description="localCurrency",
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
 *          property="localAmount",
 *          description="localAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="comRptCurrency",
 *          description="comRptCurrency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="comRptCurrencyER",
 *          description="comRptCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="comRptAmount",
 *          description="comRptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="budgetYear",
 *          description="budgetYear",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isExtraAddon",
 *          description="isExtraAddon",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class DirectInvoiceDetails extends Model
{

    public $table = 'erp_directinvoicedetails';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'directInvoiceDetailsID';

    public $fillable = [
        'directInvoiceAutoID',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'chartOfAccountSystemID',
        'glCode',
        'glCodeDes',
        'comments',
        'percentage',
        'DIAmountCurrency',
        'DIAmountCurrencyER',
        'DIAmount',
        'localCurrency',
        'localCurrencyER',
        'localAmount',
        'purchaseOrderID',
        'comRptCurrency',
        'comRptCurrencyER',
        'comRptAmount',
        'budgetYear',
        'isExtraAddon',
        'vatMasterCategoryID',
        'vatSubCategoryID',
        'timesReferred',
        'timeStamp',
        'VATAmount',
        'VATPercentage',
        'VATAmountLocal',
        'VATAmountRpt',
        'exempt_vat_portion',
        'netAmount',
        'deductionType',
        'netAmountLocal',
        'netAmountRpt',
        'detail_project_id',
        'whtApplicable',
        'whtAmount',
        'whtEdited',
        'contractID',
        'contractDescription'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'directInvoiceDetailsID' => 'integer',
        'directInvoiceAutoID' => 'integer',
        'companySystemID' => 'integer',
        'vatMasterCategoryID' => 'integer',
        'vatSubCategoryID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'chartOfAccountSystemID' => 'integer',
        'deductionType' => 'integer',
        'glCode' => 'string',
        'glCodeDes' => 'string',
        'comments' => 'string',
        'percentage' => 'float',
        'DIAmountCurrency' => 'integer',
        'DIAmountCurrencyER' => 'float',
        'DIAmount' => 'float',
        'localCurrency' => 'integer',
        'localCurrencyER' => 'float',
        'localAmount' => 'float',
        'purchaseOrderID' => 'integer',
        'comRptCurrency' => 'integer',
        'comRptCurrencyER' => 'float',
        'exempt_vat_portion' => 'float',
        'comRptAmount' => 'float',
        'budgetYear' => 'integer',
        'isExtraAddon' => 'integer',
        'timesReferred' => 'integer',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATPercentage' => 'float',
        'VATAmountRpt' => 'float',
        'netAmount' => 'float',
        'netAmountLocal' => 'float',
        'netAmountRpt' => 'float',
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

    public function segment()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function purchase_order()
    {
        return $this->belongsTo('App\Models\ProcumentOrder', 'purchaseOrderID', 'purchaseOrderID');
    }

    public function chartofaccount()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'chartOfAccountSystemID','chartOfAccountSystemID');
    }

    public function supplier_invoice_master()
    {
        return $this->belongsTo('App\Models\BookInvSuppMaster', 'directInvoiceAutoID', 'bookingSuppMasInvAutoID');
    }

    public function budget_detail()
    {
        return $this->belongsTo('App\Models\Budjetdetails', 'chartOfAccountSystemID','chartOfAccountID');
    }

    public function vat_sub_category(){
        return $this->belongsTo('App\Models\TaxVatCategories','vatSubCategoryID','taxVatSubCategoriesAutoID');
    }
    
    public function monthly_deduction_det()
    {
        return $this->belongsTo(MonthlyDeclarationsTypes::class, 'deductionType', 'monthlyDeclarationID');
    }

    public function project()
    {
        return $this->belongsTo('App\Models\ErpProjectMaster', 'detail_project_id', 'id');
    }

    public function transactioncurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'DIAmountCurrency', 'currencyID');
    }
}
