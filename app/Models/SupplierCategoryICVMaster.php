<?php
/**
 * =============================================
 * -- File Name : SupplierCategoryICVMaster.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Category ICV Master
 * -- Author : Mohamed Fayas
 * -- Create date : 03 - December 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SupplierCategoryICVMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="supCategoryICVMasterID",
 *          description="supCategoryICVMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="categoryCode",
 *          description="categoryCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="categoryDescription",
 *          description="categoryDescription",
 *          type="string"
 *      )
 * )
 */
class SupplierCategoryICVMaster extends Model
{

    public $table = 'suppliercategoryicvmaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'supCategoryICVMasterID';
    protected $appends = ['categoryDescription'];

    public $fillable = [
        'categoryCode',
        'categoryDescription',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'supCategoryICVMasterID' => 'integer',
        'categoryCode' => 'string',
        'categoryDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * Relationship to SupplierCategoryICVMasterLanguage
     */
    public function translations()
    {
        return $this->hasMany(SupplierCategoryICVMasterLanguage::class, 'supCategoryICVMasterID', 'supCategoryICVMasterID');
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
     * Get translated category description
     */
    public function getCategoryDescriptionAttribute()
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        
        $translation = $this->translation($currentLanguage);
        
        if ($translation) {
            return $translation->categoryDescription;
        }
        
        if ($currentLanguage !== 'en') {
            $englishTranslation = $this->translation('en');
            if ($englishTranslation) {
                return $englishTranslation->categoryDescription;
            }
        }
        
        return $this->categoryDescription;
    }
    
}
