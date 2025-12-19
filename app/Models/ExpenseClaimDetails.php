<?php
/**
 * =============================================
 * -- File Name : ExpenseClaimDetails.php
 * -- Project Name : ERP
 * -- Module Name :  Expense Claim
 * -- Author : Fayas
 * -- Create date : 10 - September 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ExpenseClaimDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="expenseClaimDetailsID",
 *          description="expenseClaimDetailsID",
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
 *          property="expenseClaimCategoriesAutoID",
 *          description="expenseClaimCategoriesAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="docRef",
 *          description="docRef",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="amount",
 *          description="amount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="glCode",
 *          description="glCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="glCodeDescription",
 *          description="glCodeDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="currencyID",
 *          description="currencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="currencyER",
 *          description="currencyER",
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
 *      )
 * )
 */
class ExpenseClaimDetails extends Model
{

    public $table = 'erp_expenseclaimdetails';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey = 'expenseClaimDetailsID';


    public $fillable = [
        'expenseClaimMasterAutoID',
        'companyID',
        'serviceLineCode',
        'expenseClaimCategoriesAutoID',
        'description',
        'docRef',
        'amount',
        'comments',
        'glCode',
        'glCodeDescription',
        'currencyID',
        'currencyER',
        'localCurrency',
        'localCurrencyER',
        'localAmount',
        'comRptCurrency',
        'comRptCurrencyER',
        'comRptAmount',
        'companySystemID',
        'serviceLineSystemID',
        'chartOfAccountSystemID',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'expenseClaimDetailsID' => 'integer',
        'expenseClaimMasterAutoID' => 'integer',
        'companyID' => 'string',
        'serviceLineCode' => 'string',
        'expenseClaimCategoriesAutoID' => 'integer',
        'description' => 'string',
        'docRef' => 'string',
        'amount' => 'float',
        'comments' => 'string',
        'glCode' => 'string',
        'glCodeDescription' => 'string',
        'currencyID' => 'integer',
        'currencyER' => 'float',
        'localCurrency' => 'integer',
        'localCurrencyER' => 'float',
        'localAmount' => 'float',
        'comRptCurrency' => 'integer',
        'comRptCurrencyER' => 'float',
        'comRptAmount' => 'float',
        'companySystemID' => 'integer',
        'serviceLineSystemID' => 'integer',
        'chartOfAccountSystemID' => 'integer'
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

    public function chart_of_account()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'chartOfAccountSystemID','chartOfAccountSystemID');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'currencyID', 'currencyID');
    }
    public function local_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'localCurrency', 'currencyID');
    }
    public function category()
    {
        return $this->belongsTo('App\Models\ExpenseClaimCategories', 'expenseClaimCategoriesAutoID', 'expenseClaimCategoriesAutoID');
    }

    public function master()
    {
        return $this->belongsTo('App\Models\ExpenseClaimMaster', 'expenseClaimMasterAutoID', 'expenseClaimMasterAutoID');
    }
}
