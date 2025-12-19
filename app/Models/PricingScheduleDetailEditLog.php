<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="PricingScheduleDetailEditLog",
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
 *          property="bid_format_id",
 *          description="bid_format_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="boq_applicable",
 *          description="boq_applicable",
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
 *          property="created_by",
 *          description="created_by",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="deleted_at",
 *          description="deleted_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="deleted_by",
 *          description="deleted_by",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="description",
 *          description="description",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="field_type",
 *          description="field_type",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="formula_string",
 *          description="formula_string",
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
 *          property="is_disabled",
 *          description="is_disabled",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="label",
 *          description="label",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
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
 *          property="pricing_schedule_master_id",
 *          description="pricing_schedule_master_id",
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
 *          property="tender_id",
 *          description="tender_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="tender_ranking_line_item",
 *          description="tender_ranking_line_item",
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
 *          property="updated_by",
 *          description="updated_by",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class PricingScheduleDetailEditLog extends Model
{

    public $table = 'srm_pricing_schedule_detail_edit_log';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'amd_id';

    public $fillable = [
        'id',
        'bid_format_detail_id',
        'bid_format_id',
        'boq_applicable',
        'company_id',
        'created_by',
        'deleted_by',
        'description',
        'field_type',
        'formula_string',
        'is_disabled',
        'label',
        'modify_type',
        'pricing_schedule_master_id',
        'tender_edit_version_id',
        'tender_id',
        'tender_ranking_line_item',
        'updated_by',
        'master_id',
        'ref_log_id',
        'level_no',
        'is_deleted',
        'amd_pricing_schedule_master_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'amd_id' => 'integer',
        'bid_format_detail_id' => 'integer',
        'bid_format_id' => 'integer',
        'boq_applicable' => 'integer',
        'company_id' => 'integer',
        'created_by' => 'integer',
        'deleted_by' => 'integer',
        'description' => 'string',
        'field_type' => 'integer',
        'formula_string' => 'string',
        'id' => 'integer',
        'is_disabled' => 'integer',
        'label' => 'string',
        'modify_type' => 'integer',
        'pricing_schedule_master_id' => 'integer',
        'tender_edit_version_id' => 'integer',
        'tender_id' => 'integer',
        'tender_ranking_line_item' => 'integer',
        'updated_by' => 'integer',
        'level_no' => 'integer',
        'is_deleted' => 'integer',
        'amd_pricing_schedule_master_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function tender_boq_items(){
        return $this->hasMany('App\Models\TenderBoqItemsEditLog', 'amd_main_work_id', 'amd_id');
    }

    public static function getLevelNo($id){
        return max(1, (self::where('id', $id)->max('level_no') ?? 0) + 1);
    }

    public static function getPricingScheduleDetails($tender_id, $schedule_id, $companyId, $versionID){
        return self::with(['tender_boq_items' => function ($q) {
            $q->where('is_deleted', 0);
        }])
            ->where('tender_id', $tender_id)
            ->where('tender_edit_version_id', $versionID)
            ->where('amd_pricing_schedule_master_id', $schedule_id)
            ->where('company_id', $companyId)
            ->where(function($query){
                $query->where('boq_applicable',true);
                $query->orWhere('is_disabled',false);
            })->where('field_type','!=',4);
    }
    public static function getTenderPricingSchedule($tenderID, $scheduleID, $versionID){
        return self::where('tender_id', $tenderID)->where('amd_pricing_schedule_master_id', $scheduleID)->where('is_disabled', true)
            ->where('tender_edit_version_id', $versionID)->where('is_deleted', 0);
    }
    public static function getPricingScheduleMainWork($tenderMasterID, $scheduleID, $versionID, $type = ''){
        $rec = self::with(['tender_boq_items' => function ($q) {
            $q->where('is_deleted', 0);
        }])
            ->where('tender_id', $tenderMasterID)
            ->where('deleted_at', null)
            ->where('tender_edit_version_id', $versionID)
            ->where('is_deleted', 0)
            ->where('boq_applicable', true)
            ->where('amd_pricing_schedule_master_id', $scheduleID);
        if($type == 'get'){
            return $rec->get();
        }
        return $rec;
    }

    public static function getPricingScheduleDetailAmdRecords($scheduleID, $versionID, $onlyNullRecords, $tenderID)
    {
        return self::where('tender_id', $tenderID)
            ->where('is_deleted', 0)
            ->where('tender_edit_version_id', $versionID)
            ->where('amd_pricing_schedule_master_id', $scheduleID)
            ->when($onlyNullRecords, function ($q) {
                $q->whereNull('id');
            })->when(!$onlyNullRecords, function ($q) {
                $q->whereNotNull('id');
            })->get();

    }
    public static function getPricingScheduleByID($id){
        return self::where('amd_id', $id)
            ->select('id','tender_id','pricing_schedule_master_id', 'amd_pricing_schedule_master_id')
            ->first();
    }
}
