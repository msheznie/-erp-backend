<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SubModuleMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="subModuleName",
 *          description="subModuleName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="moduleMasterID",
 *          description="moduleMasterID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class SubModuleMaster extends Model
{

    public $table = 'sub_module_master';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'subModuleName',
        'moduleMasterID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'subModuleName' => 'string',
        'moduleMasterID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
