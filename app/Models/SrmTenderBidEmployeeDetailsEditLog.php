<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="SrmTenderBidEmployeeDetailsEditLog",
 *      required={""},
 *      @OA\Property(
 *          property="commercial_eval_remarks",
 *          description="commercial_eval_remarks",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="commercial_eval_status",
 *          description="commercial_eval_status",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
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
 *          property="emp_id",
 *          description="emp_id",
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
 *          property="modify_type",
 *          description="modify_type",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="remarks",
 *          description="remarks",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="status",
 *          description="status",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="tender_award_commite_mem_comment",
 *          description="tender_award_commite_mem_comment",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="tender_award_commite_mem_status",
 *          description="tender_award_commite_mem_status",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="tender_edit_version_id",
 *          description="tender_edit_version_id",
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
 *      )
 * )
 */
class SrmTenderBidEmployeeDetailsEditLog extends Model
{

    public $table = 'srm_tender_bid_employee_details_edit_log';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected  $primaryKey = 'amd_id';


    public $fillable = [
        'id',
        'commercial_eval_remarks',
        'commercial_eval_status',
        'emp_id',
        'modify_type',
        'remarks',
        'status',
        'tender_award_commite_mem_comment',
        'tender_award_commite_mem_status',
        'tender_edit_version_id',
        'tender_id',
        'updated_by',
        'level_no',
        'is_deleted',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'amd_id' => 'integer',
        'commercial_eval_remarks' => 'string',
        'commercial_eval_status' => 'boolean',
        'emp_id' => 'integer',
        'id' => 'integer',
        'modify_type' => 'integer',
        'remarks' => 'string',
        'status' => 'boolean',
        'tender_award_commite_mem_comment' => 'string',
        'tender_award_commite_mem_status' => 'boolean',
        'tender_edit_version_id' => 'integer',
        'tender_id' => 'integer',
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

    public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'employeeSystemID', 'emp_id');
    }
    public static function getLevelNo($id){
        return max(1, (self::where('id', $id)->max('level_no') ?? 0) + 1);
    }

    public static function getTenderBidEmployeesAmd($tenderID, $versionID){
        return self::where('tender_id', $tenderID)
            ->where('tender_edit_version_id', $versionID)
            ->where('is_deleted', 0)
            ->get();
    }
    public static function getAmendRecords($versionID, $tenderMasterID, $onlyNullRecord){
        return self::where('tender_edit_version_id', $versionID)
            ->where('tender_id', $tenderMasterID)
            ->where('is_deleted', 0)
            ->when($onlyNullRecord, function ($q) {
                $q->whereNull('id');
            })
            ->when(!$onlyNullRecord, function ($q) {
                $q->whereNotNull('id');
            })
            ->get();
    }

    public static function getTenderBidEmployees($tender_id, $versionID){
        return self::where('tender_id', $tender_id)
            ->where('tender_edit_version_id', $versionID)
            ->where('is_deleted', 0)
            ->with('employee')->get();
    }

}
