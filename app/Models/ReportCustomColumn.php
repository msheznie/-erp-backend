<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="ReportCustomColumn",
 *      required={""},
 *      @OA\Property(
 *          property="column_name",
 *          description="column_name",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="column_reference",
 *          description="column_reference",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="column_slug",
 *          description="column_slug",
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
 *          property="id",
 *          description="id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="isActive",
 *          description="isActive",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="isDefault",
 *          description="isDefault",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="master_column_reference",
 *          description="master_column_reference",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
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
class ReportCustomColumn extends Model
{

    public $table = 'report_custom_columns';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $appends = ['column_name'];


    public $fillable = [
        'column_name',
        'column_reference',
        'column_slug',
        'isActive',
        'isDefault',
        'master_column_reference',
        'isDate',
        'isDetails'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'column_name' => 'string',
        'column_reference' => 'string',
        'column_slug' => 'string',
        'id' => 'integer',
        'isActive' => 'boolean',
        'isDefault' => 'boolean',
        'isDate' => 'boolean',
        'isDetails' => 'boolean',
        'master_column_reference' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'isActive' => 'required',
        'isDefault' => 'required'
    ];

    public function translations()
    {
        return $this->hasMany(ReportCustomColumnTranslations::class, 'documentSystemID', 'id');
    }

    public function translation($languageCode = null)
    {
        if (!$languageCode) {
            $languageCode = app()->getLocale() ?: 'en';
        }
        return $this->translations()->where('languageCode', $languageCode)->first();
    }

    public function getColumnNameAttribute($value)
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        $translation = $this->translation($currentLanguage);
        if ($translation && $translation->description) {
            return $translation->description;
        }
        return $this->attributes['column_name'] ?? '';
    }
}
