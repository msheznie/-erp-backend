<?php
/**
 * =============================================
 * -- File Name : LogisticModeOfImport.php
 * -- Project Name : ERP
 * -- Module Name :  Logistic
 * -- Author : Mohamed Fayas
 * -- Create date : 20- June 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="LogisticModeOfImport",
 *      required={""},
 *      @SWG\Property(
 *          property="modeOfImportID",
 *          description="modeOfImportID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modeImportDescription",
 *          description="modeImportDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      )
 * )
 */
class LogisticModeOfImport extends Model
{

    public $table = 'erp_logisticmodeofimport';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'modeOfImportID';
    protected $appends = ['modeImportDescription'];

    public $fillable = [
        'modeImportDescription',
        'createdUserID',
        'createdPCID',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'modeOfImportID' => 'integer',
        'modeImportDescription' => 'string',
        'createdUserID' => 'string',
        'createdPCID' => 'string'
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
        return $this->hasMany(LogisticModeOfImportTranslations::class, 'modeOfImportID', 'modeOfImportID');
    }

    public function translation($languageCode = null)
    {
        if (!$languageCode) {
            $languageCode = app()->getLocale() ?: 'en';
        }
        return $this->translations()->where('languageCode', $languageCode)->first();
    }

    public function getModeImportDescriptionAttribute($value)
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        $translation = $this->translation($currentLanguage);
        if ($translation && $translation->description) {
            return $translation->description;
        }
        return $this->attributes['modeImportDescription'] ?? '';
    }
}
