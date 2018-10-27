<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="TemplatesDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="templatesDetailsAutoID",
 *          description="templatesDetailsAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="templatesMasterAutoID",
 *          description="templatesMasterAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="templateDetailDescription",
 *          description="templateDetailDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="controlAccountID",
 *          description="controlAccountID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="controlAccountSubID",
 *          description="controlAccountSubID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="sortOrder",
 *          description="sortOrder",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="cashflowid",
 *          description="cashflowid",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class TemplatesDetails extends Model
{

    public $table = 'erp_templatesdetails';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'templatesDetailsAutoID';


    public $fillable = [
        'templatesMasterAutoID',
        'templateDetailDescription',
        'controlAccountID',
        'controlAccountSubID',
        'sortOrder',
        'cashflowid',
        'controlAccountSystemID',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'templatesDetailsAutoID' => 'integer',
        'templatesMasterAutoID' => 'integer',
        'templateDetailDescription' => 'string',
        'controlAccountID' => 'string',
        'controlAccountSubID' => 'integer',
        'sortOrder' => 'float',
        'cashflowid' => 'integer',
        'controlAccountSystemID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function gl_codes()
    {
        return $this->hasMany('App\Models\TemplatesGLCode', 'templatesDetailsAutoID', 'templatesDetailsAutoID');
    }
    
}
