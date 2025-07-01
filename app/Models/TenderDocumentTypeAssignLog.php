<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="TenderDocumentTypeAssignLog",
 *      required={""},
 *      @OA\Property(
 *          property="created_at",
 *          description="created_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="document_type_id",
 *          description="document_type_id",
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
 *          property="ref_log_id",
 *          description="ref_log_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="tender_id",
 *          description="tender_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="version_id",
 *          description="version_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class TenderDocumentTypeAssignLog extends Model
{

    public $table = 'srm_tender_document_type_assign_edit_log';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'amd_id';


    public $fillable = [
        'id',
        'document_type_id',
        'master_id',
        'modify_type',
        'ref_log_id',
        'tender_id',
        'version_id',
        'updated_by',
        'level_no',
        'company_id',
        'is_deleted'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'amd_id' => 'integer',
        'document_type_id' => 'integer',
        'id' => 'integer',
        'master_id' => 'integer',
        'modify_type' => 'integer',
        'ref_log_id' => 'integer',
        'tender_id' => 'integer',
        'company_id' => 'integer',
        'version_id' => 'integer',
        'level_no' => 'integer',
        'is_deleted' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];
    public function document_type()
    {
        return $this->hasOne('App\Models\TenderDocumentTypes', 'id', 'document_type_id');
    }
    public static function getLevelNo($id){
        return max(1, (self::where('id', $id)->max('level_no') ?? 0) + 1);
    }
    public static function getAssignedDocs($tenderMasterID, $versionID){
        return self::where('tender_id', $tenderMasterID)
            ->where('version_id', $versionID)
            ->where('is_deleted', 0)
            ->get();
    }
    public static function getTenderDocumentTypeAssign($tenderMasterID, $versionID){
        return self::with(['document_type'])
            ->where('tender_id', $tenderMasterID)
            ->where('version_id', $versionID)
            ->where('is_deleted', 0)
            ->get();
    }
    public static function getTenderDocumentTypeAssigned($tender_id, $doc_type_id, $company_id, $versionID){
        return self::where('tender_id', $tender_id)
            ->where('document_type_id', $doc_type_id)
            ->where('company_id', $company_id)
            ->where('version_id', $versionID)
            ->where('is_deleted', 0)
            ->first();
    }
    public static function getAmendRecords($versionID, $tenderMasterID, $onlyNullRecords){
        return self::where('version_id', $versionID)
            ->where('tender_id', $tenderMasterID)
            ->where('is_deleted', 0)
            ->when($onlyNullRecords, function ($q) {
                $q->whereNull('id');
            })
            ->when(!$onlyNullRecords, function ($q) {
                $q->whereNotNull('id');
            })->get();
    }
}
