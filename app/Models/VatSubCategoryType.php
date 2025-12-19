<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="VatSubCategoryType",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          description="type",
 *          type="string"
 *      )
 * )
 */
class VatSubCategoryType extends Model
{

    public $table = 'vat_sub_category_type';
    
    const CREATED_AT = null;
    const UPDATED_AT = null;




    public $fillable = [
        'type'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'type' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['type'];

    /**
     * Get the translations for the vat sub category type.
     */
    public function translations()
    {
        return $this->hasMany(VatSubCategoryTypeTranslation::class, 'vat_sub_category_type_id');
    }

    /**
     * Get the translated type attribute.
     *
     * @return string
     */
    public function getTypeAttribute()
    {
        $languageCode = app()->getLocale();

        $translation = $this->translations()
            ->where('languageCode', $languageCode)
            ->first();

        return $translation ? $translation->type : $this->attributes['type'];
    }

    
}
