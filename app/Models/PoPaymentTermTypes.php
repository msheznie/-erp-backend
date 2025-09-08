<?php
/**
 * =============================================
 * -- File Name : PoPaymentTermTypes.php
 * -- Project Name : ERP
 * -- Module Name :  Po Payment Term Types
 * -- Author : Nazir
 * -- Create date : 18 - April 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PoPaymentTermTypes
 * @package App\Models
 * @version April 10, 2018, 1:07 pm UTC
 *
 * @property string categoryDescription
 */
class PoPaymentTermTypes extends Model
{
    //use SoftDeletes;

    public $table = 'erp_popaymenttermstype';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'paymentTermsCategoryID';

    protected $dates = ['deleted_at'];

    protected $appends = ['translated_category_description'];

    public $fillable = [
        'categoryDescription'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'paymentTermsCategoryID' => 'integer',
        'categoryDescription' => 'string'
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
        return $this->hasMany(PoPaymentTermTypesLanguage::class, 'paymentTermsCategoryID', 'paymentTermsCategoryID');
    }

    public function translation($languageCode = null)
    {
        if (!$languageCode) {
            $languageCode = app()->getLocale() ?: 'en';
        }
        
        return $this->translations()->where('languageCode', $languageCode)->first();
    }

    public function getTranslatedCategoryDescriptionAttribute($value)
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
