<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="VatReturnFillingCategory",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="category",
 *          description="category",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="masterID",
 *          description="masterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class VatReturnFillingCategory extends Model
{

    public $table = 'vat_return_filling_category';
    
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $appends = ['category'];


    public $fillable = [
        'category',
        'masterID',
        'isFormula',
        'isActive'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'category' => 'string',
        'masterID' => 'integer',
        'isFormula' => 'integer',
        'isActive' => 'integer'
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
        return $this->hasMany(VatReturnFillingCategoryLanguage::class, 'returnFillingCategoryID', 'id');
    }

    public function translation($languageCode = null)
    {
        if (!$languageCode) {
            $languageCode = app()->getLocale() ?: 'en';
        }
        return $this->translations()->where('languageCode', $languageCode)->first();
    }

    public function getCategoryAttribute($value)
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        $translation = $this->translation($currentLanguage);
        if ($translation && $translation->description) {
            return $translation->description;
        }
        return $this->attributes['category'] ?? '';
    }
}
