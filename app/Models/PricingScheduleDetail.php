<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Schema(
 *      schema="PricingScheduleDetail",
 *      required={""},
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
 *          property="pricing_schedule_master_id",
 *          description="pricing_schedule_master_id",
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
class PricingScheduleDetail extends Model
{
   
    public $table = 'srm_pricing_schedule_detail';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $appends = ['level','active'];

    public $fillable = [
        'bid_format_id',
        'bid_format_detail_id',
        'boq_applicable',
        'created_by',
        'field_type',
        'formula_string',
        'is_disabled',
        'label',
        'pricing_schedule_master_id',
        'tender_id',
        'updated_by',
        'description',
        'deleted_by',
        'company_id',
        'remarks',
        'tender_ranking_line_item'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'bid_format_id' => 'integer',
        'boq_applicable' => 'integer',
        'created_by' => 'integer',
        'field_type' => 'integer',
        'formula_string' => 'string',
        'id' => 'integer',
        'is_disabled' => 'integer',
        'label' => 'string',
        'pricing_schedule_master_id' => 'integer',
        'tender_id' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function tender_boq_items(){
        return $this->hasMany('App\Models\TenderBoqItems', 'main_work_id', 'id');
    }

    public function bid_main_work()
    {
        return $this->hasOne('App\Models\BidMainWork', 'main_works_id', 'id');
    }

    public function bid_main_works()
    {
        return $this->hasMany('App\Models\BidMainWork', 'main_works_id', 'id');
    }

    public function bid_format_detail()
    {
        return $this->hasOne('App\Models\ScheduleBidFormatDetails', 'bid_format_detail_id', 'id');
    }

    public function tender_feild_type()
    {
        return $this->hasOne('App\Models\TenderFieldType', 'id', 'field_type');
    }

    public function ranking_items()
    {
        return $this->hasOne('App\Models\CommercialBidRankingItems', 'id', 'tender_ranking_line_item');
    }

    public function getLevelAttribute(){
        return 1;
    }

    public function getActiveAttribute(){
        return false;
    }

    public function tender_bid_format_detail()
    {
        return $this->hasOne('App\Models\TenderBidFormatDetail', 'id', 'bid_format_detail_id');
    }
    public static function getPricingScheduleDetailForAmd($tender_id, $pricingScheduleMasterID = 0){
        return self::where('tender_id', $tender_id)
            ->when($pricingScheduleMasterID > 0, function ($q) use ($pricingScheduleMasterID) {
                $q->where('pricing_schedule_master_id', $pricingScheduleMasterID);
            })
            ->get();
    }
    public static function getPricingScheduleDetails($schedule_id, $price_bid_format_id, $editOrAmend = false)
    {
        $tableDetail = $editOrAmend ? 'srm_pricing_schedule_detail_edit_log' : 'srm_pricing_schedule_detail';
        $tableFormat = $editOrAmend ? 'srm_schedule_bid_format_details_edit_log' : 'srm_schedule_bid_format_details';
        $idField     = $editOrAmend ? 'amd_id' : 'id';
        $masterField = $editOrAmend ? 'amd_pricing_schedule_master_id' : 'schedule_id';
        $otherMasterField = $editOrAmend ? 'amd_pricing_schedule_master_id' : 'pricing_schedule_master_id';
        $biFormatDetailID = $editOrAmend ? 'amd_bid_format_detail_id' : 'bid_format_detail_id';

        $isDeletedCondition = $editOrAmend ? "AND $tableDetail.is_deleted = 0" : "";
        $isDeletedConditionForBid = $editOrAmend ? "AND $tableFormat.is_deleted = 0" : "";

        $val1 = DB::select("
        SELECT
            $tableDetail.$idField AS id,
            $tableDetail.tender_id,
            $tableDetail.label,
            $tableDetail.boq_applicable,
            $tableDetail.is_disabled,
            tender_field_type.type,
            tender_field_type.id as typeId,
            $tableFormat.value
        FROM $tableDetail
        INNER JOIN tender_field_type 
            ON tender_field_type.id = $tableDetail.field_type
        LEFT JOIN $tableFormat 
            ON $tableFormat.$biFormatDetailID = $tableDetail.$idField
            AND $tableFormat.$masterField = ? 
            $isDeletedConditionForBid
        WHERE
            $tableDetail.bid_format_id = ?
            AND $tableDetail.$otherMasterField = ?
            AND $tableDetail.deleted_at IS NULL
            $isDeletedCondition
            AND $tableDetail.field_type != 4
        ORDER BY $tableDetail.$idField ASC
    ", [$schedule_id, $price_bid_format_id, $schedule_id]);

        $val2 = DB::select("
        SELECT
            $tableDetail.$idField AS id,
            $tableDetail.tender_id,
            $tableDetail.label,
            $tableDetail.boq_applicable,
            $tableDetail.is_disabled,
            tender_field_type.type,
            tender_field_type.id as typeId
        FROM $tableDetail
        INNER JOIN tender_field_type 
            ON tender_field_type.id = $tableDetail.field_type
        WHERE
            $tableDetail.bid_format_id = ?
            AND $tableDetail.$otherMasterField = ?
            AND $tableDetail.deleted_at IS NULL
            $isDeletedCondition
            AND $tableDetail.field_type = 4
        ORDER BY $tableDetail.$idField ASC
    ", [$price_bid_format_id, $schedule_id]);

        return [$val1, $val2];
    }
    public static function getPricingScheduleDetailList($tender_id, $schedule_id, $companyId){
        return self::with(['tender_boq_items'])
            ->where('tender_id', $tender_id)
            ->where('pricing_schedule_master_id', $schedule_id)
            ->where('company_id', $companyId)
            ->where(function($query){
                $query->where('boq_applicable',true);
                $query->orWhere('is_disabled',false);
            })->where('field_type','!=',4);
    }
    public static function getTenderPricingSchedule($tenderID, $scheduleID){
        return self::where('tender_id', $tenderID)->where('pricing_schedule_master_id', $scheduleID)->where('is_disabled', true);
    }
    public static function getPricingScheduleMainWork($tenderMasterID, $scheduleID){
        return self::with(['tender_boq_items'])
            ->where('tender_id', $tenderMasterID)
            ->where('deleted_at', null)
            ->where('boq_applicable', true)
            ->where('pricing_schedule_master_id', $scheduleID);
    }
    public static function getPricingScheduleByID($id){
        return self::where('id', $id)
            ->select('id','tender_id','pricing_schedule_master_id')
            ->first();
    }
}
