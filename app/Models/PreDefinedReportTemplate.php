<?php
/**
 * =============================================
 * -- File Name : ReportTemplateLinks.php
 * -- Project Name : ERP
 * -- Module Name : Configuration
 * -- Author : Mohamed Rilwan
 * -- Create date : 31- January 2020
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PreDefinedReportTemplate",
 *      required={""},
 *      @SWG\Property(
 *          property="preDefinedReportTemplateID",
 *          description="preDefinedReportTemplateID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="preDefinedReportTemplateCode",
 *          description="preDefinedReportTemplateCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="templateName",
 *          description="templateName",
 *          type="string"
 *      )
 * )
 */
class PreDefinedReportTemplate extends Model
{

    public $table = 'erp_predefinedreporttemplate';
    
//    const CREATED_AT = 'created_at';
//    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'preDefinedReportTemplateID';

    public $fillable = [
        'preDefinedReportTemplateCode',
        'templateName'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'preDefinedReportTemplateID' => 'integer',
        'preDefinedReportTemplateCode' => 'string',
        'templateName' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
//        'preDefinedReportTemplateID' => 'required'
    ];

    
}
