<?php
/**
 * =============================================
 * -- File Name : ReportTemplate.php
 * -- Project Name : ERP
 * -- Module Name : Configuration
 * -- Author : Mohamed Mubashir
 * -- Create date : 20- December 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ReportTemplate",
 *      required={""},
 *      @SWG\Property(
 *          property="companyReportTemplateID",
 *          description="companyReportTemplateID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="reportID",
 *          description="reportID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isMPREnabled",
 *          description="isMPREnabled",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isAssignToGroup",
 *          description="isAssignToGroup",
 *          type="integer",
 *          format="int32"
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
class ReportTemplate extends Model
{
    public $table = 'erp_companyreporttemplate';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'companyReportTemplateID';

    public $fillable = [
        'description',
        'reportName',
        'reportID',
        'categoryBLorPL',
        'dateType',
        'companySystemID',
        'companyID',
        'chartOfAccountSerialLength',
        'isActive',
        'isMPREnabled',
        'isAssignToGroup',
        'presentationType',
        'drillDownType',
        'preDefinedTemplateYN',
        'preDefinedReportTemplateID',
        'isDefault',
        'showNumbersIn',
        'showDecimalPlaceYN',
        'historicalYN',
        'createdPCID',
        'createdUserSystemID',
        'createdUserID',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserSystemID',
        'modifiedUserID',
        'modifiedDateTime',
        'columnTemplateID',
        'isConsolidation',
        'showZeroGlYN',
        'timestamp',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'companyReportTemplateID' => 'integer',
        'description' => 'string',
        'reportName' => 'string',
        'chartOfAccountSerialLength' => 'integer',
        'reportID' => 'integer',
        'categoryBLorPL' => 'string',
        'dateType' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'isActive' => 'integer',
        'isMPREnabled' => 'integer',
        'isAssignToGroup' => 'integer',
        'presentationType' => 'integer',
        'isDefault' => 'integer',
        'isConsolidation' => 'integer',
        'drillDownType' => 'integer',
        'preDefinedTemplateYN' => 'integer',
        'preDefinedReportTemplateID' => 'integer',
        'showNumbersIn' => 'integer',
        'showDecimalPlaceYN' => 'integer',
        'historicalYN' => 'integer',
        'createdPCID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'columnTemplateID' => 'integer',
        'showZeroGlYN' => 'integer',
        'modifiedUserID' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOfCompany($query, $type)
    {
        return $query->where('companySystemID',  $type);
    }

    public function template_type()
    {
        return $this->belongsTo('App\Models\AccountsType', 'reportID', 'accountsType');
    }

    public function details()
    {
        return $this->hasMany('App\Models\ReportTemplateDetails', 'companyReportTemplateID', 'companyReportTemplateID');
    }

    public function getDescriptionAttribute($value)
    {
        return is_string($value) ? htmlspecialchars_decode($value) : $value;
    }
}
