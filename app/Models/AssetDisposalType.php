<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AssetDisposalType",
 *      required={""},
 *      @SWG\Property(
 *          property="disposalTypesID",
 *          description="disposalTypesID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="typeDescription",
 *          description="typeDescription",
 *          type="string"
 *      )
 * )
 */
class AssetDisposalType extends Model
{

    public $table = 'erp_fa_asset_disposaltypes';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'disposalTypesID';
    protected $appends = ['typeDescription', 'purpose', 'transaction'];

    public $fillable = [
        'typeDescription',
        'activeYN',
        'chartOfAccountID',
        'glCode',
        'updated_by',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'disposalTypesID' => 'integer',
        'activeYN' => 'integer',
        'chartOfAccountID' => 'integer',
        'glCode' => 'string',
        'typeDescription' => 'string',
        'purpose' => 'string',
        'transaction' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * Relationship to AssetDisposalTypeLanguage
     */
    public function translations()
    {
        return $this->hasMany(AssetDisposalTypeLanguage::class, 'disposalTypesID', 'disposalTypesID');
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
     * Get translated type description
     */
    public function getTypeDescriptionAttribute()
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        
        $translation = $this->translation($currentLanguage);
        
        if ($translation && $translation->typeDescription) {
            return $translation->typeDescription;
        }
        
        return $this->attributes['typeDescription'] ?? '';
    }

    /**
     * Get translated purpose
     */
    public function getPurposeAttribute()
    {
        $currentLanguage = app()->getLocale() ?: 'en';

        $translation = $this->translation($currentLanguage);

        if ($translation && $translation->purpose) {
            return $translation->purpose;
        }

        return $this->attributes['purpose'] ?? '';
    }

    /**
     * Get translated transaction
     */
    public function getTransactionAttribute()
    {
        $currentLanguage = app()->getLocale() ?: 'en';

        $translation = $this->translation($currentLanguage);

        if ($translation && $translation->transaction) {
            return $translation->transaction;
        }

        return $this->attributes['transaction'] ?? '';
    }

    public function chartofaccount()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'chartOfAccountID', 'chartOfAccountSystemID');
    }

    
}
