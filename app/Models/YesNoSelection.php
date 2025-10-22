<?php
/**
 * =============================================
 * -- File Name : YesNoSelection.php
 * -- Project Name : ERP
 * -- Module Name : Yes No Selection
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class YesNoSelection
 * @package App\Models
 * @version March 5, 2018, 12:29 pm UTC
 *
 * @property string YesNo
 */
class YesNoSelection extends Model
{
    //use SoftDeletes;

    public $table = 'yesnoselection';

    protected $appends = ['yes_no_label'];
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'YesNo'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'idyesNoselection' => 'integer',
        'YesNo' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * Relationship to YesNoSelectionLanguage
     */
    public function translations()
    {
        return $this->hasMany(YesNoSelectionLanguage::class, 'yesNoSelectionID', 'idyesNoselection');
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
     * Get YesNo value in current language
     */
    public function getYesNoLabelAttribute($value)
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        
        // Try to get translation for current language
        $translation = $this->translation($currentLanguage);
        
        if ($translation) {
            return $translation->YesNo;
        }
        
        // Fallback to English if current language translation not found
        if ($currentLanguage !== 'en') {
            $englishTranslation = $this->translation('en');
            if ($englishTranslation) {
                return $englishTranslation->YesNo;
            }
        }
        
        // Final fallback to original value
        return $value;
    }
}
