<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SystemGlCodeScenario",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
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
class SystemGlCodeScenario extends Model
{

    public $table = 'system_gl_code_scenario';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $appends = ['description', 'purpose', 'transaction'];

    public $fillable = [
        'documentSystemID',
        'isActive',
        'description',
        'department_master_id',
        'slug'

    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'isActive' => 'integer',
        'documentSystemID' => 'integer',
        'description' => 'string',
        'slug' => 'string',
        'department_master_id' => 'integer'

    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function company_scenario(){
        return $this->hasOne(SystemGlCodeScenarioDetail::class, 'systemGlScenarioID', 'id');
    }

    public function detail()
    {
        return $this->belongsTo('App\Models\SystemGlCodeScenarioDetail', 'id', 'systemGlScenarioID');
    }

    public function translations()
    {
        return $this->hasMany(SystemGlCodeScenarioTranslation::class, 'system_gl_code_scenario_id', 'id');
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

    public function getPurposeAttribute($value)
    {
        $currentLanguage = app()->getLocale() ?: 'en';

        $translation = $this->translation($currentLanguage);

        if ($translation && $translation->purpose) {
            return $translation->purpose;
        }

        return $this->attributes['purpose'] ?? '';
    }

    public function getTransactionAttribute($value)
    {
        $currentLanguage = app()->getLocale() ?: 'en';

        $translation = $this->translation($currentLanguage);

        if ($translation && $translation->transaction) {
            return $translation->transaction;
        }

        return $this->attributes['transaction'] ?? '';
    }
}
