<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SMESystemEmployeeType",
 *      required={""},
 *      @SWG\Property(
 *          property="employeeTypeID",
 *          description="employeeTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="employeeType",
 *          description="employeeType",
 *          type="string"
 *      )
 * )
 */
class SMESystemEmployeeType extends Model
{

    public $table = 'srp_erp_systememployeetype';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'employeeType'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'employeeTypeID' => 'integer',
        'employeeType' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'employeeType' => 'required'
    ];

    
}
