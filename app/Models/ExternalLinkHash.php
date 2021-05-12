<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ExternalLinkHash",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="hashKey",
 *          description="hashKey",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="generatedBy",
 *          description="generatedBy",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="genratedDate",
 *          description="genratedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="expiredIn",
 *          description="expiredIn",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="isUsed",
 *          description="isUsed",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class ExternalLinkHash extends Model
{

    public $table = 'externallinkhash';
    
    const CREATED_AT = 'genratedDate';
    const UPDATED_AT = null;




    public $fillable = [
        'hashKey',
        'generatedBy',
        'genratedDate',
        'expiredIn',
        'companySystemID',
        'isUsed'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'hashKey' => 'string',
        'generatedBy' => 'integer',
        'companySystemID' => 'integer',
        'genratedDate' => 'datetime',
        'expiredIn' => 'datetime',
        'isUsed' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
