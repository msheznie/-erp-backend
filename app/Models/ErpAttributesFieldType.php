<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ErpAttributesFieldType",
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
 *          property="created_by",
 *          description="created_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updated_by",
 *          description="updated_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class ErpAttributesFieldType extends Model
{

    public $table = 'erp_attributes_field_type';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $appends = ['description'];


    public $fillable = [
        'description',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'description' => 'string',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public static function fieldName($id)
    {
        $data = ErpAttributesFieldType::find($id);

        return $data ? $data->description : "";
    }

    /**
     * Get the translations for the accounts type.
     */
    public function translations()
    {
        return $this->hasMany(ErpAttributesFieldTypeTranslation::class, 'fieldTypeId', 'id');
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
    public function getDescriptionAttribute()
    {
        $currentLanguage = app()->getLocale() ?: 'en';

        $translation = $this->translation($currentLanguage);

        if ($translation) {
            return $translation->description;
        }

        return $this->attributes['description'] ?? '';
    }
}
