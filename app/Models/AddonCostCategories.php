<?php
/**
 * =============================================
 * -- File Name : AddonCostCategories.php
 * -- Project Name : ERP
 * -- Module Name :  AddonCostCategories
 * -- Author : Mohamed Nazir
 * -- Create date : 20 - July 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AddonCostCategories",
 *      required={""},
 *      @SWG\Property(
 *          property="idaddOnCostCategories",
 *          description="idaddOnCostCategories",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="costCatDes",
 *          description="costCatDes",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="glCode",
 *          description="glCode",
 *          type="string"
 *      )
 * )
 */
class AddonCostCategories extends Model
{

    public $table = 'erp_addoncostcategories';
    
    const CREATED_AT = 'timesStamp';
    const UPDATED_AT = 'timesStamp';

    protected $primaryKey  = 'idaddOnCostCategories';
    protected $appends = ['costCatDes'];

    public $fillable = [
        'costCatDes',
        'glCode',
        'itemSystemCode',
        'timesStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'idaddOnCostCategories' => 'integer',
        'itemSystemCode' => 'integer',
        'costCatDes' => 'string',
        'glCode' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function item_by()
    {
        return $this->belongsTo('App\Models\ItemMaster', 'itemSystemCode', 'itemCodeSystem');
    }

    /**
     * Get the translations for the addon cost category.
     */
    public function translations()
    {
        return $this->hasMany('App\Models\AddonCostCategoriesTranslation', 'idaddOnCostCategories', 'idaddOnCostCategories');
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
     * Get translated cost category description
     */
    public function getCostCatDesAttribute()
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        
        $translation = $this->translation($currentLanguage);
        
        if ($translation && $translation->costCatDes) {
            return $translation->costCatDes;
        }
        
        return $this->attributes['costCatDes'] ?? '';
    }
    
}
