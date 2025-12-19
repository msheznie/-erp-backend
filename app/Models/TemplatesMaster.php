<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="TemplatesMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="templatesMasterAutoID",
 *          description="templatesMasterAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="templateDescription",
 *          description="templateDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="templateType",
 *          description="templateType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="templateReportName",
 *          description="templateReportName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class TemplatesMaster extends Model
{

    public $table = 'erp_templatesmaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $primaryKey = 'templatesMasterAutoID';


    public $fillable = [
        'templateDescription',
        'templateType',
        'templateReportName',
        'isActive',
        'timestamp',
        'isBudgetUpload'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'templatesMasterAutoID' => 'integer',
        'templateDescription' => 'string',
        'templateType' => 'string',
        'templateReportName' => 'string',
        'isActive' => 'integer',
        'isBudgetUpload' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
