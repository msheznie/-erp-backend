<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSFinanceLog",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="startTime",
 *          description="startTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="endTime",
 *          description="endTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="status",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="postGroupByYN",
 *          description="postGroupByYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="shiftId",
 *          description="shiftId",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class POSFinanceLog extends Model
{

    public $table = 'pos_finance_log';


    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'startTime',
        'endTime',
        'status',
        'postGroupByYN',
        'shiftId'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'startTime' => 'datetime',
        'endTime' => 'datetime',
        'status' => 'integer',
        'postGroupByYN' => 'integer',
        'shiftId' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
