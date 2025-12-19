<?php
/**
 * =============================================
 * -- File Name : ReportTemplateColumns.php
 * -- Project Name : ERP
 * -- Module Name : Configuration
 * -- Author : Mohamed Mubashir
 * -- Create date : 16- January 2019
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ReportTemplateColumns",
 *      required={""},
 *      @SWG\Property(
 *          property="columnID",
 *          description="columnID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="shortCode",
 *          description="shortCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          description="type",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      )
 * )
 */
class ReportTemplateColumns extends Model
{

    public $table = 'erp_companyreporttemplatecolumns';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'columnID';

    protected $appends = ['description'];

    public $fillable = [
        'description',
        'shortCode',
        'type',
        'companySystemID',
        'companyID',
        'createdPCID',
        'createdUserSystemID',
        'createdUserID',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserSystemID',
        'modifiedUserID',
        'modifiedDateTime',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'columnID' => 'integer',
        'description' => 'string',
        'shortCode' => 'string',
        'type' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'createdPCID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUserID' => 'string'
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
        return $this->hasMany(ReportTemplateColumnsTranslations::class,'columnID','id');
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
