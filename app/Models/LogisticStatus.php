<?php
/**
 * =============================================
 * -- File Name : LogisticStatus.php
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
 *      definition="LogisticStatus",
 *      required={""},
 *      @SWG\Property(
 *          property="StatusID",
 *          description="StatusID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="statusDescriptions",
 *          description="statusDescriptions",
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
class LogisticStatus extends Model
{

    public $table = 'erp_logisticstatusmaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'StatusID';
    protected $appends = ['statusDescriptions'];

    public $fillable = [
        'statusDescriptions',
        'createdUserID',
        'createdDateTime',
        'createdPCID',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'StatusID' => 'integer',
        'statusDescriptions' => 'string',
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
        return $this->hasMany(LogisticStatusTranslations::class, 'StatusID', 'StatusID');
    }

    public function translation($languageCode = null)
    {
        if (!$languageCode) {
            $languageCode = app()->getLocale() ?: 'en';
        }
        return $this->translations()->where('languageCode', $languageCode)->first();
    }

    public function getStatusDescriptionsAttribute($value)
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        $translation = $this->translation($currentLanguage);
        if ($translation && $translation->description) {
            return $translation->description;
        }
        return $this->attributes['statusDescriptions'] ?? '';
    }
}
