<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="VatSubCategoryTypeTranslation",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="vat_sub_category_type_id",
 *          description="vat_sub_category_type_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="languageCode",
 *          description="languageCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          description="type",
 *          type="string"
 *      )
 * )
 */
class VatSubCategoryTypeTranslation extends Model
{

    public $table = 'vat_sub_category_type_translation';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'vat_sub_category_type_id',
        'languageCode',
        'type'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'vat_sub_category_type_id' => 'integer',
        'languageCode' => 'string',
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
     * Get the vat sub category type that owns the translation.
     */
    public function vatSubCategoryType()
    {
        return $this->belongsTo(VatSubCategoryType::class, 'vat_sub_category_type_id');
    }
}
