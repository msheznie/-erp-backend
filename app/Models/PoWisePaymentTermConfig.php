<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Support\Str;

/**
 * @OA\Schema(
 *      schema="PoWisePaymentTermConfig",
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
 *          property="templateID",
 *          description="templateID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="purchaseOrderID",
 *          description="purchaseOrderID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="supplierID",
 *          description="supplierID",
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
 *          property="isDefaultAssign",
 *          description="isDefaultAssign",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="isApproved",
 *          description="isApproved",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="isRejected",
 *          description="isRejected",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="isConfigUpdate",
 *          description="isConfigUpdate",
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
class PoWisePaymentTermConfig extends Model
{

    public $table = 'po_wise_payment_term_config';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'id';

    public $fillable = [
        'templateID',
        'purchaseOrderID',
        'supplierID',
        'term',
        'description',
        'sortOrder',
        'isSelected',
        'isDefaultAssign',
        'isApproved',
        'isRejected',
        'isConfigUpdate'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'templateID' => 'integer',
        'purchaseOrderID' => 'integer',
        'supplierID' => 'integer',
        'term' => 'string',
        'description' => 'string',
        'sortOrder' => 'integer',
        'isSelected' => 'boolean',
        'isDefaultAssign' => 'boolean',
        'isApproved' => 'boolean',
        'isRejected' => 'boolean',
        'isConfigUpdate' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'templateID' => 'required',
        'purchaseOrderID' => 'required',
        'supplierID' => 'required',
        'term' => 'required',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function template()
    {
        return $this->belongsTo(\App\Models\PaymentTermTemplate::class, 'templateID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function purchaseOrder()
    {
        return $this->belongsTo(\App\Models\ProcumentOrder::class, 'purchaseOrderID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function supplier()
    {
        return $this->belongsTo(\App\Models\SupplierMaster::class, 'supplierID');
    }

    /**
     * Get translated term attribute
     */
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
