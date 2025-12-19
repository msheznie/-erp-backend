<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ScheduleBidFormatDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bid_format_detail_id",
 *          description="bid_format_detail_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="schedule_id",
 *          description="schedule_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="value",
 *          description="value",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="created_by",
 *          description="created_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_by",
 *          description="updated_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="company_id",
 *          description="company_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class ScheduleBidFormatDetails extends Model
{

    public $table = 'srm_schedule_bid_format_details';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'bid_format_detail_id',
        'schedule_id',
        'value',
        'created_by',
        'updated_by',
        'company_id',
        'bid_master_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'bid_format_detail_id' => 'integer',
        'schedule_id' => 'integer',
        'value' => 'string',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'company_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public static function getScheduleBidFormatForAmd($scheduleId, $bid_format_detail_id){
        return self::where('schedule_id', $scheduleId)->where('bid_format_detail_id', $bid_format_detail_id)->get();
    }
    public static function checkScheduleBidFormatExists($scheduleID, $bidFormatDetailId = 0){
        return self::where('schedule_id', $scheduleID)->when($bidFormatDetailId > 0, function ($q) use ($bidFormatDetailId) {
            $q->where('bid_format_detail_id', $bidFormatDetailId);
        })->first();
    }

    public static function getScheduleBidFormat($scheduleID){
        return self::where('schedule_id', $scheduleID)->get();
    }

}
