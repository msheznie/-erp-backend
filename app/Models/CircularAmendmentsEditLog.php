<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="CircularAmendmentsEditLog",
 *      required={""},
 *      @OA\Property(
 *          property="amendment_id",
 *          description="amendment_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="circular_id",
 *          description="circular_id",
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
 *          property="status",
 *          description="status",
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
 *          property="vesion_id",
 *          description="vesion_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class CircularAmendmentsEditLog extends Model
{

    public $table = 'srm_circular_amendments_edit_log';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'amd_id';

    public $fillable = [
        'id',
        'amendment_id',
        'circular_id',
        'master_id',
        'modify_type',
        'ref_log_id',
        'status',
        'tender_id',
        'vesion_id',
        'updated_by',
        'created_by',
        'level_no',
        'is_deleted'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'amendment_id' => 'integer',
        'circular_id' => 'integer',
        'id' => 'integer',
        'master_id' => 'integer',
        'modify_type' => 'integer',
        'ref_log_id' => 'integer',
        'status' => 'integer',
        'tender_id' => 'integer',
        'vesion_id' => 'integer',
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
    public function document_attachments()
    {
        return $this->hasOne('App\Models\DocumentAttachmentsEditLog', 'amd_id', 'amendment_id');
    }
    public static function getLevelNo($id){
        return max(1, (self::where('id', $id)->max('level_no') ?? 0) + 1);
    }
    public static function getCircularAmendment($tenderMasterId, $versionID){
        return self::where('tender_id', $tenderMasterId)->where('vesion_id', $versionID)->where('is_deleted', 0)->get();
    }
    public static function getCircularAmendmentByID($circularID, $versionID){
        return self::select('amendment_id')->where('circular_id', $circularID)
            ->where('vesion_id', $versionID)->where('is_deleted', 0)->get()->toArray();
    }
    public static function getAmendmentAttachment($amendmentID, $circularID, $tenderMasterId, $versionID)
    {
        return self::where('amendment_id', $amendmentID)
            ->where('tender_id', $tenderMasterId)
            ->where('circular_id', $circularID)
            ->where('vesion_id', $versionID)
            ->where('is_deleted', 0)
            ->first();
    }
    public static function getAllCircularAmendments($circularID, $versionID){
        return self::where('circular_id', $circularID)->where('vesion_id', $versionID)->where('is_deleted', 0)->get();
    }
    public static function getAmendRecords($circularID,  $versionID, $onlyNullRecords)
    {
        return self::where('circular_id', $circularID)->where('vesion_id', $versionID)->when($onlyNullRecords, function ($q) {
            $q->whereNull('id');
        })->when(!$onlyNullRecords, function ($q) {
            $q->whereNotNull('id');
        })->get();
    }
    public static function checkAmendmentIsUsedInCircular($amendmentID, $tenderMasterId){
        return self::where('amendment_id',  $amendmentID)->where('tender_id', $tenderMasterId)->count();
    }
}
