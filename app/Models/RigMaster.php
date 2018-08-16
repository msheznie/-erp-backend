<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="RigMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="idrigmaster",
 *          description="idrigmaster",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="RigDescription",
 *          description="RigDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="oldID",
 *          description="oldID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isRig",
 *          description="isRig",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class RigMaster extends Model
{

    public $table = 'rigmaster';
    
  /*  const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';*/



    public $fillable = [
        'RigDescription',
        'companyID',
        'oldID',
        'isRig'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'idrigmaster' => 'integer',
        'RigDescription' => 'string',
        'companyID' => 'string',
        'oldID' => 'integer',
        'isRig' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
