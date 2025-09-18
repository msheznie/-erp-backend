<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AssetType",
 *      required={""},
 *      @SWG\Property(
 *          property="typeID",
 *          description="typeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="typeDes",
 *          description="typeDes",
 *          type="string"
 *      )
 * )
 */
class AssetType extends Model
{

    public $table = 'erp_fa_assettype';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $appends = ['typeDes'];


    public $fillable = [
        'typeDes',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'typeID' => 'integer',
        'typeDes' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * Get the translations for the accounts type.
     */
    public function translations()
    {
        return $this->hasMany(AssetTypeTranslation::class, 'typeID', 'typeID');
    }

    /**
     * Get the translation for a specific language.
     */
    public function translation($languageCode = null)
    {
        if (!$languageCode) {
            $languageCode = app()->getLocale() ?: 'en';
        }

        return $this->translations()->where('languageCode', $languageCode)->first();
    }

    /**
     * Get the translated description attribute.
     */
    public function getTypeDesAttribute()
    {
        $currentLanguage = app()->getLocale() ?: 'en';

        $translation = $this->translation($currentLanguage);

        if ($translation) {
            return $translation->typeDes;
        }

        return $this->attributes['typeDes'] ?? '';
    }

    
}
