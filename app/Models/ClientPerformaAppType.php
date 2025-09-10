<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ClientPerformaAppType",
 *      required={""},
 *      @SWG\Property(
 *          property="performaAppTypeID",
 *          description="performaAppTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class ClientPerformaAppType extends Model
{

    public $table = 'clientperformaapptype';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey = 'performaAppTypeID';
    protected $appends = ['description'];



    public $fillable = [
        'description',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'performaAppTypeID' => 'integer',
        'description' => 'string',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
//        'performaAppTypeID' => 'required'
    ];

    /**
     * Relationship to ClientPerformaAppTypeLanguage
     */
    public function translations()
    {
        return $this->hasMany(ClientPerformaAppTypeLanguage::class, 'performaAppTypeID', 'performaAppTypeID');
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
        
        if ($translation) {
            return $translation->description;
        }
        
        if ($currentLanguage !== 'en') {
            $englishTranslation = $this->translation('en');
            if ($englishTranslation) {
                return $englishTranslation->description;
            }
        }
        
        return $this->attributes['description'] ?? '';
    }
    
}
