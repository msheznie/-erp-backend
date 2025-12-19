<?php
/**
 * =============================================
 * -- File Name : LogisticShippingMode.php
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
 *      definition="LogisticShippingMode",
 *      required={""},
 *      @SWG\Property(
 *          property="logisticShippingModeID",
 *          description="logisticShippingModeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modeShippingDescription",
 *          description="modeShippingDescription",
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
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string"
 *      )
 * )
 */
class LogisticShippingMode extends Model
{

    public $table = 'erp_logisticshippingmode';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'logisticShippingModeID';
    protected $appends = ['modeShippingDescription'];

    public $fillable = [
        'modeShippingDescription',
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
        'logisticShippingModeID' => 'integer',
        'modeShippingDescription' => 'string',
        'createdUserID' => 'string',
        'createdPCID' => 'string',
        'timestamp' => 'string'
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
        return $this->hasMany(LogisticShippingModeTranslations::class, 'logisticShippingModeID', 'logisticShippingModeID');
    }

    public function translation($languageCode = null)
    {
        if (!$languageCode) {
            $languageCode = app()->getLocale() ?: 'en';
        }
        return $this->translations()->where('languageCode', $languageCode)->first();
    }

    public function getModeShippingDescriptionAttribute($value)
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        $translation = $this->translation($currentLanguage);
        if ($translation && $translation->description) {
            return $translation->description;
        }
        return $this->attributes['modeShippingDescription'] ?? '';
    }
}
