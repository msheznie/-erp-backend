<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="DocumentEmailNotificationMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="emailNotificationID",
 *          description="emailNotificationID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      )
 * )
 */
class DocumentEmailNotificationMaster extends Model
{

    public $table = 'erp_documentemailnotificationmaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'emailNotificationID';

    protected $appends = ['description'];
    public $fillable = [
        'description'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'emailNotificationID' => 'integer',
        'description' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function erpDocumentemailnotificationdetails()
    {
        return $this->hasMany(\App\Models\ErpDocumentemailnotificationdetail::class);
    }

    public function translations()
    {
        return $this->hasMany(DocumentEmailNotificationMasterTranslations::class, 'emailNotificationID', 'emailNotificationID');
    }

    public function translation($languageCode = null)
    {
        if (!$languageCode) {
            $languageCode = app()->getLocale() ?: 'en';
        }
        return $this->translations()->where('languageCode', $languageCode)->first();
    }

    public function getDescriptionAttribute($value)
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        $translation = $this->translation($currentLanguage);
        if ($translation && $translation->description) {
            return $translation->description;
        }
        return $this->attributes['description'] ?? '';
    }
}
