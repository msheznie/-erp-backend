<?php
/**
 * =============================================
 * -- File Name : BudgetConsumedData.php
 * -- Project Name : ERP
 * -- Module Name :  Budget Consumed Data
 * -- Author : Nazir
 * -- Create date : 30 - May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */

namespace App\Models;

use Eloquent as Model;
use Awobaz\Compoships\Compoships;
use Carbon\Carbon;

/**
 * @SWG\Definition(
 *      definition="BudgetConsumedData",
 *      required={""},
 *      @SWG\Property(
 *          property="budgetConsumedDataAutoID",
 *          description="budgetConsumedDataAutoID",
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
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemCode",
 *          description="documentSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentCode",
 *          description="documentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="chartOfAccountID",
 *          description="chartOfAccountID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="GLCode",
 *          description="GLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="year",
 *          description="year",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="month",
 *          description="month",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="consumedLocalCurrencyID",
 *          description="consumedLocalCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="consumedLocalAmount",
 *          description="consumedLocalAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="consumedRptCurrencyID",
 *          description="consumedRptCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="consumedRptAmount",
 *          description="consumedRptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="consumeYN",
 *          description="consumeYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string"
 *      )
 * )
 */
class BudgetConsumedData extends Model
{
    use Compoships;
    public $table = 'erp_budgetconsumeddata';
    
    const CREATED_AT = NULL; //'timestamp';
    const UPDATED_AT = NULL; //'timestamp';

    protected $primaryKey = 'budgetConsumedDataAutoID';
    public $fillable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'companyFinanceYearID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'chartOfAccountID',
        'GLCode',
        'year',
        'month',
        'consumedLocalCurrencyID',
        'consumedLocalAmount',
        'consumedRptCurrencyID',
        'consumedRptAmount',
        'consumeYN',
        'projectID',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'budgetConsumedDataAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'companyFinanceYearID' => 'integer',
        'serviceLineCode' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'documentSystemCode' => 'integer',
        'documentCode' => 'string',
        'chartOfAccountID' => 'integer',
        'GLCode' => 'string',
        'year' => 'integer',
        'month' => 'integer',
        'consumedLocalCurrencyID' => 'integer',
        'consumedLocalAmount' => 'float',
        'consumedRptCurrencyID' => 'integer',
        'consumedRptAmount' => 'float',
        'consumeYN' => 'integer',
        'projectID' => 'integer',
        'timestamp' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function document_by()
    {
        return $this->belongsTo('App\Models\DocumentMaster', 'documentSystemID', 'documentSystemID');
    }
 
    public function segment_by()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function month_by()
    {
        return $this->belongsTo('App\Models\Months', 'month', 'monthID');
    }

     public function reporting_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'consumedRptCurrencyID', 'currencyID');
    }

    public function chart_of_account()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'chartOfAccountID', 'chartOfAccountSystemID');
    }

    public function budget_detail()
    {
        return $this->belongsTo('App\Models\Budjetdetails', ['chartOfAccountID', 'year'], ['chartOfAccountID', 'Year']);
    }

    public function purchase_order()
    {
        return $this->belongsTo('App\Models\ProcumentOrder', ['documentSystemCode', 'documentSystemID'], ['purchaseOrderID', 'documentSystemID']);
    }

    public function purchase_order_detail()
    {
        return $this->belongsTo('App\Models\ProcumentOrder', 'documentSystemCode', 'purchaseOrderID');
    }

    public function debit_note()
    {
        return $this->belongsTo('App\Models\DebitNote', ['documentSystemCode', 'documentSystemID'], ['debitNoteAutoID', 'documentSystemID']);
    }

    public function debit_note_detail()
    {
        return $this->belongsTo('App\Models\DebitNote', 'documentSystemCode', 'debitNoteAutoID');
    }

    public function credit_note()
    {
        return $this->belongsTo('App\Models\CreditNote', ['documentSystemCode', 'documentSystemID'], ['creditNoteAutoID', 'documentSystemiD']);
    }

    public function credit_note_detail()
    {
        return $this->belongsTo('App\Models\CreditNote', 'documentSystemCode', 'creditNoteAutoID');
    }

    public function direct_payment_voucher()
    {
        return $this->belongsTo('App\Models\PaySupplierInvoiceMaster', 'documentSystemCode', 'PayMasterAutoId');
    }

    public function direct_payment_voucher_detail()
    {
        return $this->belongsTo('App\Models\PaySupplierInvoiceMaster', ['documentSystemCode', 'documentSystemID'], ['PayMasterAutoId', 'documentSystemID']);
    }

    public function grv_master()
    {
        return $this->belongsTo('App\Models\GRVMaster', ['documentSystemCode', 'documentSystemID'], ['grvAutoID', 'documentSystemID']);
    }

    public function grv_master_detail()
    {
        return $this->belongsTo('App\Models\GRVMaster', 'documentSystemCode', 'grvAutoID');
    }

    public function jv_master()
    {
        return $this->belongsTo('App\Models\JvMaster', ['documentSystemCode', 'documentSystemID'], ['jvMasterAutoId', 'documentSystemID']);
    }

    public function jv_master_detail()
    {
        return $this->belongsTo('App\Models\JvMaster', 'documentSystemCode', 'jvMasterAutoId');
    }

    public function supplier_invoice_master()
    {
        return $this->belongsTo('App\Models\BookInvSuppMaster', 'documentSystemCode', 'bookingSuppMasInvAutoID');
    }


    public function budget_master()
    {
        return $this->belongsTo('App\Models\BudgetMaster', ['companyFinanceYearID', 'serviceLineSystemID', 'companySystemID'], ['companyFinanceYearID', 'serviceLineSystemID', 'companySystemID']);
    }

    public function financeyear_by()
    {
        return $this->belongsTo('App\Models\CompanyFinanceYear', 'companyFinanceYearID', 'companyFinanceYearID');
    }
}
