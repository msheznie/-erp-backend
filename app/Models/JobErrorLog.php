<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="JobErrorLog",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemCode",
 *          description="documentSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tag",
 *          description="tag",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="errorType",
 *          description="1 - Configuration Error, 2 - Code Error",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="errorMessage",
 *          description="errorMessage",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="error",
 *          description="error",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="0 - not resolved, 1 - Resolved",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updatedBy",
 *          description="updatedBy",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class JobErrorLog extends Model
{

    public $table = 'job_error_logs';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'documentSystemID',
        'documentSystemCode',
        'tag',
        'errorType',
        'errorMessage',
        'error',
        'status',
        'updatedBy'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'documentSystemID' => 'integer',
        'documentSystemCode' => 'integer',
        'tag' => 'string',
        'errorType' => 'integer',
        'errorMessage' => 'string',
        'error' => 'string',
        'status' => 'integer',
        'updatedBy' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'tag' => 'required'
    ];

    
}
