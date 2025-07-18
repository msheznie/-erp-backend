<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="DocumentAttachmentsEditLog",
 *      required={""},
 *      @OA\Property(
 *          property="approvalLevelOrder",
 *          description="approvalLevelOrder",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="attachmentDescription",
 *          description="attachmentDescription",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="attachmentType",
 *          description="attachmentType",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          description="created_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="docExpirtyDate",
 *          description="docExpirtyDate",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date"
 *      ),
 *      @OA\Property(
 *          property="documentID",
 *          description="documentID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="documentSystemCode",
 *          description="documentSystemCode",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="envelopType",
 *          description="envelopType",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="isUploaded",
 *          description="isUploaded",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="master_id",
 *          description="master_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="modify_type",
 *          description="modify_type",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="myFileName",
 *          description="myFileName",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="originalFileName",
 *          description="originalFileName",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="parent_id",
 *          description="parent_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="path",
 *          description="path",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="pullFromAnotherDocument",
 *          description="pullFromAnotherDocument",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="ref_log_id",
 *          description="ref_log_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="sizeInKbs",
 *          description="sizeInKbs",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class DocumentAttachmentsEditLog extends Model
{

    public $table = 'erp_documentattachments_edit_log';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'amd_id';

    public $fillable = [
        'id',
        'approvalLevelOrder',
        'attachmentDescription',
        'attachmentType',
        'companySystemID',
        'companyID',
        'docExpirtyDate',
        'documentID',
        'documentSystemCode',
        'documentSystemID',
        'envelopType',
        'order_number',
        'isAutoCreateDocument',
        'isUploaded',
        'master_id',
        'modify_type',
        'myFileName',
        'originalFileName',
        'parent_id',
        'path',
        'pullFromAnotherDocument',
        'ref_log_id',
        'sizeInKbs',
        'version_id',
        'updated_by',
        'level_no',
        'is_deleted'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'amd_id' => 'integer',
        'approvalLevelOrder' => 'integer',
        'attachmentDescription' => 'string',
        'attachmentType' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'docExpirtyDate' => 'date',
        'documentID' => 'string',
        'documentSystemCode' => 'integer',
        'documentSystemID' => 'integer',
        'envelopType' => 'string',
        'id' => 'integer',
        'isUploaded' => 'integer',
        'master_id' => 'integer',
        'modify_type' => 'integer',
        'myFileName' => 'string',
        'originalFileName' => 'string',
        'parent_id' => 'integer',
        'path' => 'string',
        'pullFromAnotherDocument' => 'integer',
        'ref_log_id' => 'integer',
        'sizeInKbs' => 'float',
        'level_no' => 'integer',
        'is_deleted' => 'integer',
        'version_id' => 'integer',
        'isAutoCreateDocument' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public static function getLevelNo($attachmentID){
        return max(1, (self::where('id', $attachmentID)->max('level_no') ?? 0) + 1);
    }

    public static function getDocumentAttachmentEditLog($documentSystemID, $documentSystemCode, $versionID){
        return self::where('documentSystemID', $documentSystemID)
            ->where('documentSystemCode', $documentSystemCode)
            ->where('version_id', $versionID)
            ->where('is_deleted', 0)->get();
    }

    public static function checkDocumentExists($companySystemID, $documentSystemID, $attachmentType, $documentSystemCode, $attachmentDescription, $versionID, $id, $masterID = 0){
        return self::where('companySystemID',$companySystemID)
            ->when($id > 0, function ($q) use ($id) {
                $q->where('amd_id', '!=', $id);
            })
            ->when($masterID > 0, function ($q) use ($masterID) {
                $q->where('id', '!=', $masterID);
            })
            ->where('documentSystemID',$documentSystemID)
            ->where('attachmentType',$attachmentType)
            ->where('documentSystemCode',$documentSystemCode)
            ->where('attachmentDescription',$attachmentDescription)
            ->where('version_id', $versionID)
            ->where('is_deleted', 0)
            ->where('attachmentDescription',$attachmentDescription)
            ->exists();
    }

    public static function getAttachmentDocumentTypeBase($companySystemID, $documentSystemID,$attachmentType, $documentSystemCode, $versionID){
        return self::where('companySystemID',$companySystemID)
            ->where('documentSystemID',$documentSystemID)
            ->where('attachmentType',$attachmentType)
            ->where('documentSystemCode',$documentSystemCode)
            ->where('version_id', $versionID)
            ->where('is_deleted', 0)
            ->orderBy('amd_id', 'asc')
            ->get();
    }

    public static function getAmendRecords($versionID, $tenderMasterID, $documentSystemID, $onlyNullRecords){
        return self::where('version_id', $versionID)
            ->where('documentSystemID', $documentSystemID)
            ->where('documentSystemCode', $tenderMasterID)
            ->where('is_deleted', 0)
            ->when($onlyNullRecords, function ($q) {
                $q->whereNull('id');
            })
            ->when(!$onlyNullRecords, function ($q) {
                $q->whereNotNull('id');
            })->get();
    }
    public static function getAttachmentForCirculars($attachmentArray, $documentSystemID, $tenderMasterId, $versionID)
    {
        return self::whereNotIn('amd_id', $attachmentArray)
            ->where('documentSystemID', $documentSystemID)
            ->where('version_id', $versionID)
            ->where('is_deleted', 0)
            ->where('attachmentType',3)
            ->where('parent_id', null)
            ->where('documentSystemCode', $tenderMasterId)->orderBy('amd_id', 'asc')->get()->toArray();
    }
    public static function getNotUsedAttachmentForCirculars($circularAttachmentIDs, $versionID){
        return self::whereIn('amd_id', $circularAttachmentIDs)->where('version_id', $versionID)
            ->where('is_deleted', 0)->get();
    }
    public static function getLatestAttachmentAmdID($attachmentID){
        return self::where('id', $attachmentID)->orderBy('amd_id', 'desc')->first();
    }

}
