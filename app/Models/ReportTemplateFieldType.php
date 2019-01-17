<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ReportTemplateFieldType",
 *      required={""},
 *      @SWG\Property(
 *          property="fieldTypeID",
 *          description="fieldTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="fieldType",
 *          description="fieldType",
 *          type="string"
 *      )
 * )
 */
class ReportTemplateFieldType extends Model
{

    public $table = 'erp_companyreporttemplatefieldtypes';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'fieldType'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'fieldTypeID' => 'integer',
        'fieldType' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
