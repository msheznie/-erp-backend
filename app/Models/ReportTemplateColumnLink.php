<?php
/**
 * =============================================
 * -- File Name : ReportTemplateColumnLink.php
 * -- Project Name : ERP
 * -- Module Name : Configuration
 * -- Author : Mohamed Mubashir
 * -- Create date : 31- December 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ReportTemplateColumnLink",
 *      required={""},
 *      @SWG\Property(
 *          property="columnLinkID",
 *          description="columnLinkID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="columnID",
 *          description="columnID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="templateID",
 *          description="templateID",
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
 *          property="sortOrder",
 *          description="sortOrder",
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
class ReportTemplateColumnLink extends Model
{

    public $table = 'erp_companyreporttemplatecolumnlink';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'columnLinkID';

    public $fillable = [
        'columnID',
        'templateID',
        'description',
        'shortCode',
        'type',
        'sortOrder',
        'bgColor',
        'width',
        'hideColumn',
        'formulaColumnID',
        'formulaRowID',
        'formula',
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
        'columnLinkID' => 'integer',
        'columnID' => 'integer',
        'templateID' => 'integer',
        'description' => 'string',
        'shortCode' => 'string',
        'type' => 'integer',
        'sortOrder' => 'integer',
        'bgColor' => 'string',
        'width' => 'integer',
        'hideColumn' => 'integer',
        'formulaColumnID' => 'string',
        'formulaRowID' => 'string',
        'formula' => 'string',
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
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOfTemplate($query, $templateMasterID)
    {
        return $query->where('templateID',  $templateMasterID);
    }


    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
