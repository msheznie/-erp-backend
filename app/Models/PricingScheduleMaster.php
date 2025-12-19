<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PricingScheduleMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tender_id",
 *          description="tender_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="scheduler_name",
 *          description="scheduler_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="price_bid_format_id",
 *          description="price_bid_format_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="schedule_mandatory",
 *          description="schedule_mandatory",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="items_mandatory",
 *          description="items_mandatory",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="if 1 Complete if 0 Pending",
 *          type="integer",
 *          format="int32"
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
class PricingScheduleMaster extends Model
{

    public $table = 'srm_pricing_schedule_master';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'tender_id',
        'scheduler_name',
        'price_bid_format_id',
        'schedule_mandatory',
        'items_mandatory',
        'status',
        'created_by',
        'updated_by',
        'company_id',
        'boq_status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'tender_id' => 'integer',
        'scheduler_name' => 'string',
        'price_bid_format_id' => 'integer',
        'schedule_mandatory' => 'integer',
        'items_mandatory' => 'integer',
        'status' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'company_id' => 'integer',
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

    public function bid_schedule()
    {
        return $this->hasOne('App\Models\BidSchedule', 'schedule_id', 'id');
    }

    public function tender_main_works()
    {
        return $this->hasMany('App\Models\TenderMainWorks', 'schedule_id', 'id');
    }

    public function pricing_shedule_details()
    {
        return $this->hasMany('App\Models\PricingScheduleDetail', 'pricing_schedule_master_id', 'id');
    }

    public function pricing_shedule_details1()
    {
        return $this->hasMany('App\Models\PricingScheduleDetail', 'pricing_schedule_master_id', 'id');
    }

    public function bid_schedules()
    {
        return $this->hasMany('App\Models\BidSchedule', 'schedule_id', 'id');
    }

    public static function checkScheduleNameExists($id, $tenderMasterId, $scheduler_name, $companySystemID){
        return self::when($id > 0, function ($q) use($id) {
            $q->where('id','!=', $id);
        })->where('tender_id', $tenderMasterId)
            ->where('scheduler_name', $scheduler_name)
            ->where('company_id', $companySystemID)
            ->first();
    }

    public static function getPricingScheduleMasterForAmd($tenderMasterID){
        return self::where('tender_id', $tenderMasterID)->get();
    }
    public static function getPricingScheduleMasterListQry($tender_id, $companyId)
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
        ])->where('tender_id', $tender_id)->where('company_id', $companyId);
    }

    public static function getScheduleMasterData($id){
        return self::with(['tender_bid_format_master'])->where('id', $id)->first();
    }
    public static function getTenderScheduleMaster($tenderMasterID, $type = 'get'){
        $query = self::where('tender_id', $tenderMasterID);

        switch ($type) {
            case 'get':
                return $query->get();
            case 'first':
                return $query->first();
            case 'count':
                return $query->count();
            default:
                return null;
        }
    }
}
