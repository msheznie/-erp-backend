<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="ItemCategoryTypeMaster",
 *      required={""},
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="name",
 *          description="name",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          description="created_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class ItemCategoryTypeMaster extends Model
{

    public $table = 'item_category_type_master';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $appends = ['name'];


    public $fillable = [
        'name'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required'
    ];

    public static function purchaseItems()
    {
        return [1];
    }

    public static function salesItems()
    {
        return [2];
    }

    public static function allItemTypes()
    {
        return [1,2];
    }

    public function translations()
    {
        return $this->hasMany(ItemCategoryTypeMasterTranslation::class, 'typeId', 'id');
    }

    public function translation($languageCode = null)
    {
        if (!$languageCode) {
            $languageCode = app()->getLocale() ?: 'en';
        }

        return $this->translations()->where('languageCode', $languageCode)->first();
    }

    public function getNameAttribute($value)
    {
        $currentLanguage = app()->getLocale() ?: 'en';

        $translation = $this->translation($currentLanguage);

        if ($translation) {
            return $translation->name;
        }

        if ($currentLanguage !== 'en') {
            $englishTranslation = $this->translation('en');
            if ($englishTranslation) {
                return $englishTranslation->name;
            }
        }

        return $value;
    }
}
