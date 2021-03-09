<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SMECompanyPolicyValue",
 *      required={""},
 *      @SWG\Property(
 *          property="policyValueID",
 *          description="policyValueID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companypolicymasterID",
 *          description="companypolicymasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="value",
 *          description="value",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="systemValue",
 *          description="systemValue",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class SMECompanyPolicyValue extends Model
{

    public $table = 'srp_erp_companypolicymaster_value';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';




    public $fillable = [
        'companypolicymasterID',
        'value',
        'systemValue',
        'companyID',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'policyValueID' => 'integer',
        'companypolicymasterID' => 'integer',
        'value' => 'string',
        'systemValue' => 'string',
        'companyID' => 'integer',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'companypolicymasterID' => 'required'
    ];

    
}
