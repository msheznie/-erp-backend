<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Support\Str;

/**
 * @OA\Schema(
 *      schema="PaymentTermConfig",
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
 *          property="templateId",
 *          description="templateId",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="term",
 *          description="term",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="description",
 *          description="description",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="sortOrder",
 *          description="sortOrder",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="isSelected",
 *          description="isSelected",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
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
class PaymentTermConfig extends Model
{

    public $table = 'payment_term_configurations';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'templateId',
        'term',
        'description',
        'sortOrder',
        'isSelected'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'templateId' => 'integer',
        'term' => 'string',
        'description' => 'string',
        'sortOrder' => 'integer',
        'isSelected' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'templateId' => 'required',
        'term' => 'required',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function template()
    {
        return $this->belongsTo(\App\Models\PaymentTermTemplate::class, 'templateId');
    }

    public function getTermAttribute($value)
    {
         // Return null if value is null
         if ($value === null) {
            return null;
        }

        $languageCode = app()->getLocale() ?: 'en';
        
        // If the value already starts with 'custom.', use it directly
        if (strpos($value, 'custom.') === 0) {
            return trans($value, [], $languageCode);
        }

        // Specific mapping for known terms
        $termMapping = [
            'Payment Terms' => 'payment_terms',
            'Delivery Terms' => 'delivery_terms',
            'Delivery & shipping' => 'delivery_shipping',
            'Penalty Terms' => 'penalty_terms',
            'Product/Service Specifications' => 'product_service_specifications',
            'Price and Currency' => 'price_and_currency',
            'Taxes and Fees' => 'taxes_and_fees',
            'Warranties and Guarantees' => 'warranties_and_guarantees',
            'Cancellation and Returns' => 'cancellation_and_returns',
            'Limitation of Liability' => 'limitation_of_liability',
            'Confidentiality' => 'confidentiality',
            'Governing Law' => 'governing_law',
            'Dispute Resolution' => 'dispute_resolution',
            'Termination' => 'termination',
            'Insurance' => 'insurance',
            'Indemnity' => 'indemnity',
        ];

        // Check if we have a direct mapping
        if (isset($termMapping[$value])) {
            $translationKey = 'custom.' . $termMapping[$value];
            $translationResult = trans($translationKey, [], $languageCode);
            
            if ($translationResult !== $translationKey) {
                return $translationResult;
            }
        }

        // Fallback: Convert the term to snake_case for translation key
        $snakeCase = strtolower(str_replace([' ', '/', '&'], ['_', '_', 'and'], $value));
        $translationKey = 'custom.' . $snakeCase;
        $translationResult = trans($translationKey, [], $languageCode);

        // If translation exists and is different from the key, return translation
        if ($translationResult !== $translationKey) {
            return $translationResult;
        }

        // Try alternative conversion for "Delivery & shipping" -> "delivery_shipping"
        $alternativeSnakeCase = strtolower(str_replace([' ', '/', '&'], ['_', '_', ''], $value));
        $alternativeTranslationKey = 'custom.' . $alternativeSnakeCase;
        $alternativeTranslationResult = trans($alternativeTranslationKey, [], $languageCode);

        if ($alternativeTranslationResult !== $alternativeTranslationKey) {
            return $alternativeTranslationResult;
        }

        // Return original value if no translation found
        return $value;
    }

    /**
     * Get original term value without translation
     */
    public function getOriginalTermAttribute()
    {
        return $this->attributes['term'] ?? '';
    }
}
