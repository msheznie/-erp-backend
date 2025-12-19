<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ErpPrintTemplateMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="printTemplateID",
 *          description="printTemplateID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="printTemplateName",
 *          description="printTemplateName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="printTemplateBlade",
 *          description="printTemplateBlade",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class ErpPrintTemplateMaster extends Model
{

    public $table = 'erp_print_template_master';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'printTemplateName',
        'printTemplateBlade'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'printTemplateID' => 'integer',
        'printTemplateName' => 'string',
        'printTemplateBlade' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'printTemplateID' => 'required'
    ];

    
}
