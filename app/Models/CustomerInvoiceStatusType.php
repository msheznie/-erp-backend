<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustomerInvoiceStatusType",
 *      required={""},
 *      @SWG\Property(
 *          property="typeAutoID",
 *          description="typeAutoID",
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
 *          type="string"
 *      )
 * )
 */
class CustomerInvoiceStatusType extends Model
{

    public $table = 'erp_customerinvoicestatustype';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
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
        'typeAutoID' => 'integer',
        'description' => 'string',
        'timestamp' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * Relationship to CustomerInvoiceStatusTypeLanguage
     */
    public function translations()
    {
        return $this->hasMany(CustomerInvoiceStatusTypeLanguage::class, 'typeAutoID', 'typeAutoID');
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
        
        if ($translation && $translation->description) {
            return $translation->description;
        }
        
        
        return $this->attributes['description'] ?? '';
    }

    
}
