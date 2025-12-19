<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SMECompanyPolicyMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="companypolicymasterID",
 *          description="companypolicymasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyPolicyDescription",
 *          description="companyPolicyDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="systemValue",
 *          description="systemValue",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isDocumentLevel",
 *          description="isDocumentLevel",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="code",
 *          description="code",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="defaultValue",
 *          description="defaultValue",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="fieldType",
 *          description="fieldType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="is_active",
 *          description="is_active",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isCompanyLevel",
 *          description="isCompanyLevel",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class SMECompanyPolicyMaster extends Model
{

    public $table = 'srp_erp_companypolicymaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $appends = ['companyPolicyDescription'];

    public $fillable = [
        'companyPolicyDescription',
        'systemValue',
        'isDocumentLevel',
        'code',
        'documentID',
        'defaultValue',
        'fieldType',
        'is_active',
        'isCompanyLevel',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'companypolicymasterID' => 'integer',
        'companyPolicyDescription' => 'string',
        'systemValue' => 'string',
        'isDocumentLevel' => 'integer',
        'code' => 'string',
        'documentID' => 'string',
        'defaultValue' => 'string',
        'fieldType' => 'string',
        'is_active' => 'integer',
        'isCompanyLevel' => 'integer',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function translations()
    {
        return $this->hasMany(CompanyPolicyMasterTranslations::class, 'companypolicymasterID', 'companypolicymasterID');
    }

    public function translation($languageCode = null)
    {
        if (!$languageCode) {
            $languageCode = app()->getLocale() ?: 'en';
        }
        return $this->translations()->where('languageCode', $languageCode)->first();
    }

    public function getCompanyPolicyDescriptionAttribute($value)
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        $translation = $this->translation($currentLanguage);
        if ($translation && $translation->description) {
            return $translation->description;
        }
        return $this->attributes['companyPolicyDescription'] ?? '';
    }
}
