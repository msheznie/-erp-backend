<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SMECompanyPolicy",
 *      required={""},
 *      @SWG\Property(
 *          property="companyPolicyAutoID",
 *          description="companyPolicyAutoID",
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
 *          property="companyID",
 *          description="companyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="code",
 *          description="code",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isYN",
 *          description="isYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="value",
 *          description="value",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserName",
 *          description="modifiedUserName",
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
class SMECompanyPolicy extends Model
{

    public $table = 'srp_erp_companypolicy';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'modifiedDateTime';




    public $fillable = [
        'companypolicymasterID',
        'companyID',
        'code',
        'documentID',
        'isYN',
        'value',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'companyPolicyAutoID' => 'integer',
        'companypolicymasterID' => 'integer',
        'companyID' => 'integer',
        'code' => 'string',
        'documentID' => 'string',
        'isYN' => 'integer',
        'value' => 'string',
        'createdUserGroup' => 'string',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdDateTime' => 'string',
        'modifiedPCID' => 'datetime',
        'modifiedUserID' => 'datetime',
        'modifiedDateTime' => 'datetime',
        'modifiedUserName' => 'string',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
