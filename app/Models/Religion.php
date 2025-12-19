<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Religion",
 *      required={""},
 *      @SWG\Property(
 *          property="religionID",
 *          description="religionID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="religionName",
 *          description="religionName",
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
class Religion extends Model
{

    public $table = 'hrms_religion';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'religionName',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'religionID' => 'integer',
        'religionName' => 'string',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'religionID' => 'required'
    ];

    
}
