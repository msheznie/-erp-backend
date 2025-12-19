<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ModuleAssigned",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
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
 *          property="moduleID",
 *          description="moduleID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="subModuleID",
 *          description="subModuleID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class ModuleAssigned extends Model
{

    public $table = 'module_assigned';
    
    const CREATED_AT = null;
    const UPDATED_AT = null;




    public $fillable = [
        'companySystemID',
        'moduleID',
        'subModuleID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'companySystemID' => 'integer',
        'moduleID' => 'integer',
        'subModuleID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
