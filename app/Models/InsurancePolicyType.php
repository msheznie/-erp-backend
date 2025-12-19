<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="InsurancePolicyType",
 *      required={""},
 *      @SWG\Property(
 *          property="insurancePolicyTypesID",
 *          description="insurancePolicyTypesID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="policyDescription",
 *          description="policyDescription",
 *          type="string"
 *      )
 * )
 */
class InsurancePolicyType extends Model
{

    public $table = 'erp_fa_insurancepolicytypes';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'insurancePolicyTypesID';
    protected $appends = ['policyDescription'];


    public $fillable = [
        'policyDescription'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'insurancePolicyTypesID' => 'integer',
        'policyDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * Relationship to InsurancePolicyTypeLanguage
     */
    public function translations()
    {
        return $this->hasMany(InsurancePolicyTypeLanguage::class, 'insurancePolicyTypesID', 'insurancePolicyTypesID');
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
     * Get translated policy description
     */
    public function getPolicyDescriptionAttribute()
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        
        $translation = $this->translation($currentLanguage);
        
        if ($translation && $translation->policyDescription) {
            return $translation->policyDescription;
        }
        
        return $this->attributes['policyDescription'] ?? '';
    }
    
}
