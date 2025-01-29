<?php
/**
 * =============================================
 * -- File Name : DirectPaymentDetails.php
 * -- Project Name : ERP
 * -- Module Name :  DirectPaymentDetails
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
 *      definition="DirectPaymentDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="directPaymentDetailsID",
 *          description="directPaymentDetailsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="directPaymentAutoID",
 *          description="directPaymentAutoID",
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
 *          property="supplierID",
 *          description="supplierID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="expenseClaimMasterAutoID",
 *          description="expenseClaimMasterAutoID",
 *          type="integer",
 *          format="int32"
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
 *          property="glCodeIsBank",
 *          description="glCodeIsBank",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransCurrencyID",
 *          description="supplierTransCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransER",
 *          description="supplierTransER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="DPAmountCurrency",
 *          description="DPAmountCurrency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DPAmountCurrencyER",
 *          description="DPAmountCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="DPAmount",
 *          description="DPAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="bankAmount",
 *          description="bankAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="bankCurrencyID",
 *          description="bankCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankCurrencyER",
 *          description="bankCurrencyER",
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
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="relatedPartyYN",
 *          description="relatedPartyYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="pettyCashYN",
 *          description="pettyCashYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="glCompanySystemID",
 *          description="glCompanySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="glCompanyID",
 *          description="glCompanyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="toBankID",
 *          description="toBankID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="toBankAccountID",
 *          description="toBankAccountID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="toBankCurrencyID",
 *          description="toBankCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="toBankCurrencyER",
 *          description="toBankCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="toBankAmount",
 *          description="toBankAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="toBankGlCode",
 *          description="toBankGlCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="toBankGLDescription",
 *          description="toBankGLDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="toCompanyLocalCurrencyID",
 *          description="toCompanyLocalCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="toCompanyLocalCurrencyER",
 *          description="toCompanyLocalCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="toCompanyLocalCurrencyAmount",
 *          description="toCompanyLocalCurrencyAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="toCompanyRptCurrencyID",
 *          description="toCompanyRptCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="toCompanyRptCurrencyER",
 *          description="toCompanyRptCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="toCompanyRptCurrencyAmount",
 *          description="toCompanyRptCurrencyAmount",
 *          type="number",
 *          format="float"
 *      )
 * )
 */
class DirectPaymentDetails extends Model
{

    public $table = 'erp_directpaymentdetails';

    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey  = 'directPaymentDetailsID';

    public $fillable = [
        'directPaymentAutoID',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'supplierID',
        'expenseClaimMasterAutoID',
        'chartOfAccountSystemID',
        'glCode',
        'glCodeDes',
        'glCodeIsBank',
        'comments',
        'deductionType',
        'supplierTransCurrencyID',
        'supplierTransER',
        'DPAmountCurrency',
        'DPAmountCurrencyER',
        'DPAmount',
        'bankAmount',
        'bankCurrencyID',
        'bankCurrencyER',
        'localCurrency',
        'localCurrencyER',
        'localAmount',
        'comRptCurrency',
        'comRptCurrencyER',
        'comRptAmount',
        'budgetYear',
        'timesReferred',
        'relatedPartyYN',
        'pettyCashYN',
        'glCompanySystemID',
        'glCompanyID',
        'vatMasterCategoryID',
        'vatSubCategoryID',
        'vatAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'VATPercentage',
        'netAmount',
        'netAmountLocal',
        'netAmountRpt',
        'toBankID',
        'toBankAccountID',
        'toBankCurrencyID',
        'toBankCurrencyER',
        'toBankAmount',
        'toBankGlCodeSystemID',
        'toBankGlCode',
        'toBankGLDescription',
        'toCompanyLocalCurrencyID',
        'toCompanyLocalCurrencyER',
        'toCompanyLocalCurrencyAmount',
        'toCompanyRptCurrencyID',
        'toCompanyRptCurrencyER',
        'toCompanyRptCurrencyAmount',
        'timeStamp',
        'detail_project_id',
        'contractID',
        'contractDescription',
        'expense_claim_er',
        'interBankID',
        'interBankAmount',
        'interBankCurrency'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'directPaymentDetailsID' => 'integer',
        'directPaymentAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'string',
        'serviceLineCode' => 'string',
        'supplierID' => 'integer',
        'expenseClaimMasterAutoID' => 'integer',
        'chartOfAccountSystemID' => 'integer',
        'glCode' => 'string',
        'glCodeDes' => 'string',
        'glCodeIsBank' => 'integer',
        'comments' => 'string',
        'deductionType' => 'integer',
        'supplierTransCurrencyID' => 'integer',
        'supplierTransER' => 'float',
        'DPAmountCurrency' => 'integer',
        'DPAmountCurrencyER' => 'float',
        'DPAmount' => 'float',
        'bankAmount' => 'float',
        'bankCurrencyID' => 'integer',
        'bankCurrencyER' => 'float',
        'localCurrency' => 'integer',
        'localCurrencyER' => 'float',
        'localAmount' => 'float',
        'comRptCurrency' => 'integer',
        'comRptCurrencyER' => 'float',
        'comRptAmount' => 'float',
        'budgetYear' => 'integer',
        'timesReferred' => 'integer',
        'relatedPartyYN' => 'integer',
        'pettyCashYN' => 'integer',
        'glCompanySystemID' => 'integer',
        'glCompanyID' => 'string',
        'toBankID' => 'integer',
        'toBankAccountID' => 'integer',
        'toBankCurrencyID' => 'integer',
        'toBankCurrencyER' => 'float',
        'toBankAmount' => 'float',
        'toBankGlCodeSystemID' => 'integer',
        'toBankGlCode' => 'string',
        'toBankGLDescription' => 'string',
        'toCompanyLocalCurrencyID' => 'integer',
        'toCompanyLocalCurrencyER' => 'float',
        'toCompanyLocalCurrencyAmount' => 'float',
        'toCompanyRptCurrencyID' => 'integer',
        'toCompanyRptCurrencyER' => 'float',
        'toCompanyRptCurrencyAmount' => 'float',
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

    public function chartofaccount()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'chartOfAccountSystemID','chartOfAccountSystemID');
    }

    public function master()
    {
        return $this->belongsTo('App\Models\PaySupplierInvoiceMaster', 'directPaymentAutoID', 'PayMasterAutoId');
    }

    public function budget_detail()
    {
        return $this->belongsTo('App\Models\Budjetdetails', 'chartOfAccountSystemID','chartOfAccountID');
    }

    public function monthly_deduction_det()
    {
        return $this->belongsTo(MonthlyDeclarationsTypes::class, 'deductionType', 'monthlyDeclarationID');
    }

    public function project()
    {
        return $this->belongsTo('App\Models\ErpProjectMaster', 'detail_project_id', 'id');
    }

    public function vatSubCategories()
    {
        return $this->belongsTo('App\Models\TaxVatCategories','vatSubCategoryID', 'taxVatSubCategoriesAutoID');
    }

    public function to_bank()
    {
        return $this->belongsTo('App\Models\BankAccount','interBankID', 'bankAccountAutoID');
    }
}
