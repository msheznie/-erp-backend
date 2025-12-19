<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SMEDocumentCodeMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="codeID",
 *          description="codeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="document",
 *          description="document",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="prefix",
 *          description="prefix",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="startSerialNo",
 *          description="startSerialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serialNo",
 *          description="serialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="formatLength",
 *          description="formatLength",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvalLevel",
 *          description="approvalLevel",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvalSignatureLevel",
 *          description="approvalSignatureLevel",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="format_1",
 *          description="format_1",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="format_2",
 *          description="format_2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="format_3",
 *          description="format_3",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="format_4",
 *          description="format_4",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="format_5",
 *          description="format_5",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="format_6",
 *          description="format_6",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isPushNotifyEnabled",
 *          description="isPushNotifyEnabled",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isFYBasedSerialNo",
 *          description="0- No 1 - YES",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="postDate",
 *          description="postDate",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="printHeaderFooterYN",
 *          description="printHeaderFooterYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="printFooterYN",
 *          description="printFooterYN",
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
class SMEDocumentCodeMaster extends Model
{

    public $table = 'srp_erp_documentcodemaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'modifiedDateTime';




    public $fillable = [
        'documentID',
        'document',
        'prefix',
        'startSerialNo',
        'serialNo',
        'formatLength',
        'approvalLevel',
        'approvalSignatureLevel',
        'format_1',
        'format_2',
        'format_3',
        'format_4',
        'format_5',
        'format_6',
        'isPushNotifyEnabled',
        'isFYBasedSerialNo',
        'postDate',
        'printHeaderFooterYN',
        'printFooterYN',
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
        'codeID' => 'integer',
        'documentID' => 'string',
        'document' => 'string',
        'prefix' => 'string',
        'startSerialNo' => 'integer',
        'serialNo' => 'integer',
        'formatLength' => 'integer',
        'approvalLevel' => 'integer',
        'approvalSignatureLevel' => 'integer',
        'format_1' => 'string',
        'format_2' => 'string',
        'format_3' => 'string',
        'format_4' => 'string',
        'format_5' => 'string',
        'format_6' => 'string',
        'isPushNotifyEnabled' => 'integer',
        'isFYBasedSerialNo' => 'integer',
        'postDate' => 'integer',
        'printHeaderFooterYN' => 'integer',
        'printFooterYN' => 'integer',
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
        'documentID' => 'required',
        'prefix' => 'required',
        'serialNo' => 'required',
        'formatLength' => 'required'
    ];

    
}
