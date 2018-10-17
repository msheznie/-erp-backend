<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="TemplatesGLCode",
 *      required={""},
 *      @SWG\Property(
 *          property="templatesGLCodeAutoID",
 *          description="templatesGLCodeAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="templateMasterID",
 *          description="templateMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="templatesDetailsAutoID",
 *          description="templatesDetailsAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chartOfAccountSystemID",
 *          description="chartOfAccountSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="glCode",
 *          description="glCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="glDescription",
 *          description="glDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="erp_templatesglcodecol",
 *          description="erp_templatesglcodecol",
 *          type="string"
 *      )
 * )
 */
class TemplatesGLCode extends Model
{

    public $table = 'erp_templatesglcode';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'templateMasterID',
        'templatesDetailsAutoID',
        'chartOfAccountSystemID',
        'glCode',
        'glDescription',
        'timestamp',
        'erp_templatesglcodecol'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'templatesGLCodeAutoID' => 'integer',
        'templateMasterID' => 'integer',
        'templatesDetailsAutoID' => 'integer',
        'chartOfAccountSystemID' => 'integer',
        'glCode' => 'string',
        'glDescription' => 'string',
        'erp_templatesglcodecol' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
