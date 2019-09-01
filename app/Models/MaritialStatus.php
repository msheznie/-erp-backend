<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="MaritialStatus",
 *      required={""},
 *      @SWG\Property(
 *          property="maritialstatusID",
 *          description="maritialstatusID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="code",
 *          description="code",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="description_O",
 *          description="other language",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="noOfkids",
 *          description="noOfkids",
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
class MaritialStatus extends Model
{

    public $table = 'hrms_maritialstatus';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'code',
        'description',
        'description_O',
        'noOfkids',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'maritialstatusID' => 'integer',
        'code' => 'string',
        'description' => 'string',
        'description_O' => 'string',
        'noOfkids' => 'string',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'maritialstatusID' => 'required'
    ];

    
}
