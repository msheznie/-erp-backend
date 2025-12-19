<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ExpenseEmployeeAllocation",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="employeeSystemID",
 *          description="employeeSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentDetailID",
 *          description="documentDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chartOfAccountSystemID",
 *          description="chartOfAccountSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemCode",
 *          description="documentSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="amount",
 *          description="amount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="amountRpt",
 *          description="amountRpt",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="amountLocal",
 *          description="amountLocal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="dateOfDeduction",
 *          description="dateOfDeduction",
 *          type="string",
 *          format="date-time"
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
class ExpenseEmployeeAllocation extends Model
{

    public $table = 'expense_employee_allocation';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'employeeSystemID',
        'documentSystemID',
        'documentDetailID',
        'chartOfAccountSystemID',
        'documentSystemCode',
        'amount',
        'amountRpt',
        'amountLocal',
        'dateOfDeduction',
        'assignedQty'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'employeeSystemID' => 'integer',
        'documentSystemID' => 'integer',
        'documentDetailID' => 'integer',
        'chartOfAccountSystemID' => 'integer',
        'documentSystemCode' => 'integer',
        'assignedQty' => 'float',
        'amount' => 'float',
        'amountRpt' => 'float',
        'amountLocal' => 'float',
        'dateOfDeduction' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employeeSystemID','employeeSystemID');
    } 

    public function chart_of_account()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'chartOfAccountSystemID','chartOfAccountSystemID');
    }

    public function supplier_invoice()
    {
       return $this->belongsTo('App\Models\BookInvSuppMaster', 'documentSystemCode', 'bookingSuppMasInvAutoID');
    }

    public function payment_voucher()
    {
       return $this->belongsTo('App\Models\PaySupplierInvoiceMaster', 'documentSystemCode', 'PayMasterAutoId');
    }

    public function meterial_issue()
    {
       return $this->belongsTo('App\Models\ItemIssueDetails', 'documentSystemCode', 'itemIssueAutoID');
    }

    public function invoice_detail()
    {
       return $this->belongsTo('App\Models\DirectInvoiceDetails', 'documentDetailID', 'directInvoiceDetailsID');
    }
}
