<?php
/**
 * =============================================
 * -- File Name : PurchaseOrderCategory.php
 * -- Project Name : ERP
 * -- Module Name : PurchaseOrderCategory
 * -- Author : Mohamed Fayas
 * -- Create date : 30- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PurchaseOrderCategory",
 *      required={""},
 *      @SWG\Property(
 *          property="POCategoryID",
 *          description="POCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      )
 * )
 */
class PurchaseOrderCategory extends Model
{

    public $table = 'purchaseordercategory';
    
    const CREATED_AT = NULL;
    const UPDATED_AT = NULL;
    protected $primaryKey  = 'POCategoryID';
    protected $appends = ['description'];


    public $fillable = [
        'description'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'POCategoryID' => 'integer',
        'description' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * Relationship to PurchaseOrderCategoryLanguage
     */
    public function translations()
    {
        return $this->hasMany(PurchaseOrderCategoryLanguage::class, 'POCategoryID', 'POCategoryID');
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
     * Get translated description
     */
    public function getDescriptionAttribute()
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        
        $translation = $this->translation($currentLanguage);
        
        if ($translation && $translation->description) {
            return $translation->description;
        }
        
        return $this->attributes['description'] ?? '';
    }
    
}
