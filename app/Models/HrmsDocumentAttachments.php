<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="HrmsDocumentAttachments",
 *      required={""},
 *      @SWG\Property(
 *          property="attachmentID",
 *          description="attachmentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
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
 *          property="attachmentDescription",
 *          description="attachmentDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="myFileName",
 *          description="myFileName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="docExpirtyDate",
 *          description="docExpirtyDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="timeStamp",
 *          description="timeStamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class HrmsDocumentAttachments extends Model
{

    public $table = 'hrms_documentattachments';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'attachmentID';


    public $fillable = [
        'companyID',
        'companySystemID',
        'documentID',
        'documentSystemID',
        'documentSystemCode',
        'attachmentDescription',
        'myFileName',
        'docExpirtyDate',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'attachmentID' => 'integer',
        'companyID' => 'string',
        'companySystemID' => 'integer',
        'documentID' => 'string',
        'documentSystemID' => 'integer',
        'documentSystemCode' => 'integer',
        'attachmentDescription' => 'string',
        'myFileName' => 'string',
        'docExpirtyDate' => 'datetime',
        'timeStamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    
}
