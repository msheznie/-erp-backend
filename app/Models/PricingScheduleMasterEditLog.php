<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="PricingScheduleMasterEditLog",
 *      required={""},
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
 *          property="id",
 *          description="id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="items_mandatory",
 *          description="items_mandatory",
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
 *          property="price_bid_format_id",
 *          description="price_bid_format_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="schedule_mandatory",
 *          description="schedule_mandatory",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="scheduler_name",
 *          description="scheduler_name",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
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
class PricingScheduleMasterEditLog extends Model
{

    public $table = 'srm_pricing_schedule_master_edit_log';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $primaryKey = 'amd_id';

    public $fillable = [
        'id',
        'company_id',
        'created_by',
        'items_mandatory',
        'modify_type',
        'price_bid_format_id',
        'schedule_mandatory',
        'scheduler_name',
        'status',
        'tender_edit_version_id',
        'tender_id',
        'master_id',
        'red_log_id',
        'updated_by',
        'level_no',
        'is_deleted',
        'boq_status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'amd_id' => 'integer',
        'company_id' => 'integer',
        'created_by' => 'integer',
        'id' => 'integer',
        'items_mandatory' => 'integer',
        'modify_type' => 'integer',
        'price_bid_format_id' => 'integer',
        'schedule_mandatory' => 'integer',
        'scheduler_name' => 'string',
        'status' => 'integer',
        'tender_edit_version_id' => 'integer',
        'tender_id' => 'integer',
        'updated_by' => 'integer',
        'level_no' => 'integer',
        'is_deleted' => 'integer',
        'boq_status' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];
    public function tender_master()
    {
        return $this->hasOne('App\Models\TenderMaster', 'id', 'tender_id');
    }
    public function tender_bid_format_master()
    {
        return $this->hasOne('App\Models\TenderBidFormatMaster', 'id', 'price_bid_format_id');
    }
    public function pricing_shedule_details()
    {
        return $this->hasMany('App\Models\PricingScheduleDetailEditLog', 'amd_pricing_schedule_master_id', 'amd_id');
    }

    public function pricing_shedule_details1()
    {
        return $this->hasMany('App\Models\PricingScheduleDetailEditLog', 'amd_pricing_schedule_master_id', 'Amd_id');
    }


    public function bid_schedule()
    {
        return $this->hasOne('App\Models\BidSchedule', 'schedule_id', 'id');
    }
    public static function getLevelNo($attachmentID){
        return max(1, (self::where('id', $attachmentID)->max('level_no') ?? 0) + 1);
    }
    public static function checkScheduleNameExists($id, $amd_id, $tenderMasterId, $scheduler_name, $companySystemID, $versionID){
        return self::when($id > 0 && $amd_id > 0, function ($q) use($id) {
            $q->where('id','!=', $id);
        })->when($id == 0 && $amd_id > 0, function ($q) use ($amd_id, $versionID){
            $q->where('amd_id', '!=', $amd_id);
        })->where('tender_id', $tenderMasterId)
            ->where('scheduler_name', $scheduler_name)
            ->where('company_id', $companySystemID)
            ->where('tender_edit_version_id', $versionID)
            ->where('is_deleted', 0)
            ->first();
    }
    public static function getPricingScheduleMasterListQry($tender_id, $companyId, $versionID)
    {
        return self::with([
            'tender_master' => function ($q) {
                $q->with(['envelop_type']);
            }, 'tender_bid_format_master',
            'pricing_shedule_details' => function ($q) {
                $q->where('boq_applicable',true);
            }, 'pricing_shedule_details1' => function ($q) {
                $q->where('is_disabled',true);
            }
        ])->where('tender_id', $tender_id)->where('company_id', $companyId)->where('tender_edit_version_id', $versionID)->where('is_deleted', 0);
    }
    public static function getScheduleMasterData($amd_id){
        return self::with(['tender_bid_format_master'])->where('amd_id', $amd_id)->first();
    }
    public static function getTenderScheduleMaster($tenderMasterID, $versionID, $type = 'get'){
        $data = self::where('tender_id', $tenderMasterID)
            ->where('tender_edit_version_id', $versionID)
            ->where('is_deleted', 0);
        if($type == 'get'){
            return $data->get();
        }
        return $data->first();
    }
    public static function getScheduleMasterAmd($tenderMasterID, $versionID, $onlyNullRecords){
        return self::where('tender_id', $tenderMasterID)
            ->where('tender_edit_version_id', $versionID)
            ->where('is_deleted', 0)
            ->when($onlyNullRecords, function ($q) {
                $q->whereNull('id');
            })->when(!$onlyNullRecords, function ($q) {
                $q->whereNotNull('id');
            })->get();
    }
}
