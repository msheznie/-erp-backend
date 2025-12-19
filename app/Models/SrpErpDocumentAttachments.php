<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SrpErpDocumentAttachments",
 *      required={""},
 *      @SWG\Property(
 *          property="attachmentID",
 *          description="attachmentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentSubID",
 *          description="documentSubID",
 *          type="string"
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
 *          property="docExpiryDate",
 *          description="docExpiryDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="dateofIssued",
 *          description="dateofIssued",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="fileType",
 *          description="fileType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="fileSize",
 *          description="fileSize",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="segmentID",
 *          description="segmentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="segmentCode",
 *          description="segmentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyCode",
 *          description="companyCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="integer",
 *          format="int32"
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
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createdUserName",
 *          description="createdUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
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
class SrpErpDocumentAttachments extends Model
{

    public $table = 'srp_erp_documentattachments';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'documentID',
        'documentSubID',
        'documentSystemCode',
        'attachmentDescription',
        'myFileName',
        'docExpiryDate',
        'dateofIssued',
        'fileType',
        'fileSize',
        'segmentID',
        'segmentCode',
        'companyID',
        'companyCode',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
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
        'attachmentID' => 'integer',
        'documentID' => 'string',
        'documentSubID' => 'string',
        'documentSystemCode' => 'integer',
        'attachmentDescription' => 'string',
        'myFileName' => 'string',
        'docExpiryDate' => 'date',
        'dateofIssued' => 'date',
        'fileType' => 'string',
        'fileSize' => 'float',
        'segmentID' => 'integer',
        'segmentCode' => 'string',
        'companyID' => 'integer',
        'companyCode' => 'string',
        'createdUserGroup' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdDateTime' => 'datetime',
        'createdUserName' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
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
