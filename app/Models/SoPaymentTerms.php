<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SoPaymentTerms",
 *      required={""},
 *      @SWG\Property(
 *          property="paymentTermID",
 *          description="paymentTermID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paymentTermsCategory",
 *          description="paymentTermsCategory",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="soID",
 *          description="soID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paymentTemDes",
 *          description="paymentTemDes",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comAmount",
 *          description="comAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="comPercentage",
 *          description="comPercentage",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="inDays",
 *          description="inDays",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="comDate",
 *          description="comDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="LCPaymentYN",
 *          description="0 is not an LC payment. 1 is an LC payment",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isRequested",
 *          description="isRequested",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class SoPaymentTerms extends Model
{

    public $table = 'erp_sopaymentterms';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'paymentTermID';

    protected $appends = ['paymentTemDesArabic'];

    public $fillable = [
        'paymentTermsCategory',
        'soID',
        'paymentTemDes',
        'comAmount',
        'comPercentage',
        'inDays',
        'comDate',
        'LCPaymentYN',
        'isRequested',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'paymentTermID' => 'integer',
        'paymentTermsCategory' => 'integer',
        'soID' => 'integer',
        'paymentTemDes' => 'string',
        'comAmount' => 'float',
        'comPercentage' => 'float',
        'inDays' => 'integer',
        'comDate' => 'datetime',
        'LCPaymentYN' => 'integer',
        'isRequested' => 'integer',
        'createdDateTime' => 'datetime',
        'timestamp' => 'datetime'
    ];



    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    /**
     * Accessor to get the translated paymentTemDes based on current locale
     */
    public function getPaymentTemDesAttribute($value)
    {
        // If value is null or empty, return as is
        if (empty($value)) {
            return $value;
        }

        $languageCode = app()->getLocale();
        
        if ($languageCode != 'en') {
            // Create multiple possible translation keys for better matching
            $possibleKeys = [
                strtolower(str_replace(' ', '_', $value)),
                strtolower(str_replace([' ', '-', '/'], '_', $value)),
                strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $value)),
                'payment_term_' . strtolower(str_replace(' ', '_', $value))
            ];
            
            // Try each possible key
            foreach ($possibleKeys as $key) {
                $translated = trans('custom.' . $key);
                
                // If translation exists and is different from the key, return it
                if ($translated !== 'custom.' . $key) {
                    return $translated;
                }
            }
            
            // Special handling for common payment terms
            $commonTerms = [
                'advance payment' => 'advance_payment',
                'net payment' => 'net_payment',
                'cash on delivery' => 'cash_on_delivery',
                'letter of credit' => 'letter_of_credit',
                'bank guarantee' => 'bank_guarantee'
            ];
            
            $lowerValue = strtolower(trim($value));
            if (isset($commonTerms[$lowerValue])) {
                $translated = trans('custom.' . $commonTerms[$lowerValue]);
                if ($translated !== 'custom.' . $commonTerms[$lowerValue]) {
                    return $translated;
                }
            }
            
            // Fallback to original value if no translation found
            return $value;
        } else {
            // If locale is English, return the original value
            return $value;
        }
    }

    /**
     * Mutator to set paymentTemDesArabic based on paymentTemDes
     */
    public function getPaymentTemDesArabicAttribute($value)
    {
        // If value is provided directly, use it
        if (!empty($value)) {
            $this->attributes['paymentTemDesArabic'] = $value;
            return;
        }

        // If no value provided, convert paymentTemDes to Arabic
        $paymentTemDes = $this->attributes['paymentTemDes'] ?? '';
        
        if (empty($paymentTemDes)) {
            $this->attributes['paymentTemDesArabic'] = '';
            return;
        }

        // Create multiple possible translation keys for better matching
        $possibleKeys = [
            strtolower(str_replace(' ', '_', $paymentTemDes)),
            strtolower(str_replace([' ', '-', '/'], '_', $paymentTemDes)),
            strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $paymentTemDes)),
            'payment_term_' . strtolower(str_replace(' ', '_', $paymentTemDes))
        ];
        
        // Try each possible key
        foreach ($possibleKeys as $key) {
            $translated = trans('custom.' . $key);
            
            // If translation exists and is different from the key, use it
            if ($translated !== 'custom.' . $key) {
                $this->attributes['paymentTemDesArabic'] = $translated;
                return;
            }
        }
        
        // Special handling for common payment terms
        $commonTerms = [
            'advance payment' => 'advance_payment',
            'net payment' => 'net_payment',
            'cash on delivery' => 'cash_on_delivery',
            'letter of credit' => 'letter_of_credit',
            'bank guarantee' => 'bank_guarantee'
        ];
        
        $lowerValue = strtolower(trim($paymentTemDes));
        if (isset($commonTerms[$lowerValue])) {
            $translated = trans('custom.' . $commonTerms[$lowerValue]);
            if ($translated !== 'custom.' . $commonTerms[$lowerValue]) {
                $this->attributes['paymentTemDesArabic'] = $translated;
                return;
            }
        }
        
        // Fallback to original value if no translation found
        $this->attributes['paymentTemDesArabic'] = $paymentTemDes;
    }

    public function term_description()
    {
        return $this->belongsTo('App\Models\PoPaymentTermTypes', 'LCPaymentYN', 'paymentTermsCategoryID');
    }
    
}
