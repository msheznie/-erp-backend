<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="ScheduleBidFormatDetailsLog",
 *      required={""},
 *      @OA\Property(
 *          property="bid_format_detail_id",
 *          description="bid_format_detail_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="bid_master_id",
 *          description="bid_master_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
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
 *          property="red_log_id",
 *          description="red_log_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="schedule_id",
 *          description="schedule_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
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
 *          property="updated_at",
 *          description="updated_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="value",
 *          description="value",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      )
 * )
 */
class ScheduleBidFormatDetailsLog extends Model
{

    public $table = 'srm_schedule_bid_format_details_edit_log';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $primaryKey = 'amd_id';

    public $fillable = [
        'id',
        'bid_format_detail_id',
        'amd_bid_format_detail_id',
        'bid_master_id',
        'company_id',
        'master_id',
        'modify_type',
        'red_log_id',
        'schedule_id',
        'amd_pricing_schedule_master_id',
        'tender_edit_version_id',
        'value',
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
        'bid_format_detail_id' => 'integer',
        'amd_bid_format_detail_id' => 'integer',
        'bid_master_id' => 'integer',
        'company_id' => 'integer',
        'id' => 'integer',
        'master_id' => 'integer',
        'modify_type' => 'integer',
        'red_log_id' => 'integer',
        'schedule_id' => 'integer',
        'amd_pricing_schedule_master_id' => 'integer',
        'tender_edit_version_id' => 'integer',
        'value' => 'string',
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

    public static function getLevelNo($id){
        return max(1, (self::where('id', $id)->max('level_no') ?? 0) + 1);
    }
    public static function checkScheduleBidFormatExists($scheduleID){
        return self::where('amd_pricing_schedule_master_id', $scheduleID)->select('id')->first();
    }
    public static function checkScheduleBidFormatDetailExists($scheduleID, $bidFormatDetailId, $versionID){
        return self::where('amd_pricing_schedule_master_id', $scheduleID)
            ->where('tender_edit_version_id', $versionID)
            ->where('is_deleted', 0)
            ->where('amd_bid_format_detail_id', $bidFormatDetailId)
            ->first();
    }
    public static function getAmendRecords($versionID, $amdScheduleID, $amdScheduleDetailID, $onlyNullRecords){
        return self::where('tender_edit_version_id', $versionID)
            ->where('amd_pricing_schedule_master_id', $amdScheduleID)
            ->where('amd_bid_format_detail_id', $amdScheduleDetailID)
            ->where('is_deleted', 0)
            ->when($onlyNullRecords, function ($q) {
                $q->whereNull('id');
            })
            ->when(!$onlyNullRecords, function ($q) {
                $q->whereNotNull('id');
            })
            ->get();
    }
    public static function getScheduleBidFormat($scheduleID, $versionID){
        return self::where('amd_pricing_schedule_master_id', $scheduleID)
            ->where('tender_edit_version_id', $versionID)
            ->where('is_deleted', 0)
            ->get();
    }
}
