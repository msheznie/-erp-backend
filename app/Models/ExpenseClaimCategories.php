<?php
/**
 * =============================================
 * -- File Name : ExpenseClaimCategories.php
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
 *      definition="ExpenseClaimCategories",
 *      required={""},
 *      @SWG\Property(
 *          property="expenseClaimCategoriesAutoID",
 *          description="expenseClaimCategoriesAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="claimcategoriesDescription",
 *          description="claimcategoriesDescription",
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
 *      )
 * )
 */
class ExpenseClaimCategories extends Model
{

    public $table = 'erp_expenseclaimcategories';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey = 'expenseClaimCategoriesAutoID';


    public $fillable = [
        'claimcategoriesDescription',
        'glCode',
        'glCodeDescription',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'expenseClaimCategoriesAutoID' => 'integer',
        'claimcategoriesDescription' => 'string',
        'glCode' => 'string',
        'glCodeDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
