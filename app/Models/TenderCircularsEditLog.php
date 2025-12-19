<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="TenderCircularsEditLog",
 *      required={""},
 *      @OA\Property(
 *          property="attachment_id",
 *          description="attachment_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="circular_name",
 *          description="circular_name",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="company_id",
 *          description="company_id",
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
 *          property="description",
 *          description="description",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
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
class TenderCircularsEditLog extends Model
{

    public $table = 'srm_tender_circulars_edit_log';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'amd_id';


    public $fillable = [
        'id',
        'attachment_id',
        'circular_name',
        'company_id',
        'description',
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
        'amd_id' => 'integer',
        'attachment_id' => 'integer',
        'circular_name' => 'string',
        'company_id' => 'integer',
        'description' => 'string',
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
        return $this->hasOne('App\Models\DocumentAttachmentsEditLog', 'amd_id', 'attachment_id');
    }

    public function document_amendments()
    {
        return $this->hasMany('App\Models\CircularAmendmentsEditLog', 'circular_id', 'id');
    }

    public static function getLevelNo($id){
        $levelNo = self::where('id', $id)
                ->max('level_no') + 1;

        return  max(1, $levelNo);
    }

    public static function getCircularList($tender_id, $companyID, $versionID)
    {
        return self::with(['document_attachments'])->where('tender_id', $tender_id)
            ->where('company_id', $companyID)
            ->where('vesion_id', $versionID)
            ->where('is_deleted', 0);
    }
    public static function checkCircularNameExists($name, $tenderID, $companyID, $versionID, $id = 0, $amd_id = 0){
        return self::where('circular_name', $name)
            ->when($id > 0 && $amd_id > 0, function ($q) use($id) {
                $q->where('id','!=', $id);
            })->when($id == 0 && $amd_id > 0, function ($q) use ($amd_id, $versionID){
                $q->where('amd_id', '!=', $amd_id);
            })
            ->where('tender_id', $tenderID)
            ->where('vesion_id', $versionID)
            ->where('is_deleted', 0)
            ->where('company_id', $companyID)->first();
    }
    public static function getTenderCirculars($tenderMasterID, $versionID){
        return self::where('tender_id', $tenderMasterID)
            ->where('vesion_id', $versionID)
            ->where('is_deleted', 0)
            ->where('status', 0)
            ->get();
    }
    public static function getAmendmentRecords($tenderMasterID, $versionID, $onlyNullRecords){
        return self::where('tender_id', $tenderMasterID)->where('vesion_id', $versionID)
            ->where('is_deleted', 0)
            ->when($onlyNullRecords, function ($q) {
                $q->whereNull('id');
            })
            ->when(!$onlyNullRecords, function ($q) {
                $q->whereNotNull('id');
            })
            ->get();
    }
    public static function versionWiseCirculars($versionID){
        return self::select('amd_id', 'id', 'description','status','circular_name')
            ->where('vesion_id', $versionID)->where('is_deleted', 0)->where('status', 0)->get();
    }
}
