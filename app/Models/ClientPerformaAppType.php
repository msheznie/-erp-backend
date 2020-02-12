<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ClientPerformaAppType",
 *      required={""},
 *      @SWG\Property(
 *          property="performaAppTypeID",
 *          description="performaAppTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class ClientPerformaAppType extends Model
{

    public $table = 'clientperformaapptype';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';



    public $fillable = [
        'description',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'performaAppTypeID' => 'integer',
        'description' => 'string',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
//        'performaAppTypeID' => 'required'
    ];

    
}
