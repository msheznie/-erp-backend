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
        'dateOfDeduction'
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

    
}
