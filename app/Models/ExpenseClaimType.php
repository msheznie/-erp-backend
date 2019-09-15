<?php
/**
 * =============================================
 * -- File Name : ExpenseClaimType.php
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
 *      definition="ExpenseClaimType",
 *      required={""},
 *      @SWG\Property(
 *          property="expenseClaimTypeID",
 *          description="expenseClaimTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="expenseClaimTypeDescription",
 *          description="expenseClaimTypeDescription",
 *          type="string"
 *      )
 * )
 */
class ExpenseClaimType extends Model
{

    public $table = 'erp_expenseclaimtype';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey = 'expenseClaimTypeID';


    public $fillable = [
        'expenseClaimTypeDescription',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'expenseClaimTypeID' => 'integer',
        'expenseClaimTypeDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
