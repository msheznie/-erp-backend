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
    protected $appends = ['expenseClaimTypeDescription'];


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

    public function translations()
    {
        return $this->hasMany(ExpensesClaimTypeLanguage::class, 'typeId', 'expenseClaimTypeID');
    }

    /**
     * Get translation for specific language
     */
    public function translation($languageCode = null)
    {
        if (!$languageCode) {
            $languageCode = app()->getLocale() ?: 'en';
        }

        return $this->translations()->where('languageCode', $languageCode)->first();
    }

    /**
     * Get translated month description
     */
    public function getExpenseClaimTypeDescriptionAttribute()
    {
        $currentLanguage = app()->getLocale() ?: 'en';

        $translation = $this->translation($currentLanguage);

        if ($translation && $translation->description) {
            return $translation->description;
        }

        return $this->attributes['expenseClaimTypeDescription'] ?? '';
    }

    
}
