<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PoCategory",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="isDefault",
 *          description="isDefault",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class PoCategory extends Model
{

    public $table = 'erp_pocategory';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $appends = ['description'];

    public $fillable = [
        'description',
        'isActive',
        'isDefault',
        'createdDateTime'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'description' => 'string',
        'isActive' => 'boolean',
        'isDefault' => 'boolean',
        'createdDateTime' => 'datetime'
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
        return $this->hasMany(PoCategoryLanguage::class, 'poCategoryID', 'id');
    }

    public function translation($languageCode = null)
    {
        if (!$languageCode) {
            $languageCode = app()->getLocale() ?: 'en';
        }
        
        return $this->translations()->where('languageCode', $languageCode)->first();
    }

    public function getDescriptionAttribute($value)
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        
        $translation = $this->translation($currentLanguage);
        
        if ($translation && $translation->description) {
            return $translation->description;
        }
        
        
        return $this->attributes['description'] ?? '';
    }

    
}
