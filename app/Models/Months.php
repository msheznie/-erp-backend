<?php
/**
 * =============================================
 * -- File Name : Months.php
 * -- Project Name : ERP
 * -- Module Name : Months
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Months
 * @package App\Models
 * @version March 27, 2018, 7:40 am UTC
 *
 * @property string monthDes
 */
class Months extends Model
{
    //use SoftDeletes;

    public $table = 'months';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];

    protected $primaryKey = 'monthID';


    public $fillable = [
        'monthDes'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'monthID' => 'integer',
        'monthDes' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * Relationship to MonthsLanguage
     */
    public function translations()
    {
        return $this->hasMany(MonthsLanguage::class, 'monthID', 'monthID');
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
    public function getTranslatedMonthDesAttribute()
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        
        $translation = $this->translation($currentLanguage);
        
        if ($translation) {
            return $translation->monthDes;
        }
        
        if ($currentLanguage !== 'en') {
            $englishTranslation = $this->translation('en');
            if ($englishTranslation) {
                return $englishTranslation->monthDes;
            }
        }
        
        return $this->monthDes;
    }
    
}
