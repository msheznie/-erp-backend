<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="LeaveApplicationType",
 *      required={""},
 *      @SWG\Property(
 *          property="LeaveApplicationTypeID",
 *          description="LeaveApplicationTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="Type",
 *          description="Type",
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
class LeaveApplicationType extends Model
{

    public $table = 'hrms_leaveapplicationtype';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'Type',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'LeaveApplicationTypeID' => 'integer',
        'Type' => 'string',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'LeaveApplicationTypeID' => 'required'
    ];

    
}
