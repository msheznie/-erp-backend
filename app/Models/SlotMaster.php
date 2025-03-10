<?php

namespace App\Models;

use Eloquent as Model;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;

/**
 * @SWG\Definition(
 *      definition="SlotMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="company_id",
 *          description="company_id",
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
 *          property="from_date",
 *          description="from_date",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="no_of_deliveries",
 *          description="no_of_deliveries",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="time_from",
 *          description="time_from",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="time_to",
 *          description="time_to",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="to_date",
 *          description="to_date",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="warehouse_id",
 *          description="warehouse_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class SlotMaster extends Model
{

    public $table = 'slot_master';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'company_id',
        'created_by',
        'from_date',
        'no_of_deliveries',
        'to_date',
        'warehouse_id','limit_deliveries'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'company_id' => 'integer',
        'created_by' => 'integer',
        'from_date' => 'datetime',
        'id' => 'integer',
        'no_of_deliveries' => 'integer',
        'to_date' => 'datetime',
        'warehouse_id' => 'integer',
        'limit_deliveries' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    public function slot_details()
    {
        return $this->hasMany('App\Models\SlotDetails', 'slot_master_id', 'id');
    }

    public function getSlotData($tenantID, $formSrm = 0)
    {
        return SlotMaster::with([
            'slot_details' => function ($q) {
                $q->with([
                    'appointment' => function ($q) {
                        $q->select('id', 'supplier_id', 'slot_detail_id', 'confirmed_yn');
                    }
                ])->select('id', 'slot_master_id', 'start_date', 'end_date', 'status', 'company_id');
            },
            'ware_house'
        ])
            ->when($formSrm == 0, function ($q) use ($tenantID) {
                $q->whereIn('company_id', $tenantID);
            })
            /*  ->where('warehouse_id', $wareHouseID) */
            ->get();
    }
    public function ware_house()
    {
        return $this->hasOne(WarehouseMaster::class, 'wareHouseSystemCode', 'warehouse_id');
    }
    public function checkDaySelectedDate($data)
    {
        $begin = new DateTime($data['dateFrom']);
        $end = clone $begin;
        $end->modify($data['dateTo']);
        $end->modify('+1 day');
        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval, $end);
        $weekDaysActive = [];


        $date1_ts = strtotime(new Carbon($data['dateFrom']));
        $date2_ts = strtotime(new Carbon($data['dateTo']));
        $diff = $date2_ts - $date1_ts;
        $days = round($diff / 86400);

        if($days == 0) {
            foreach ($daterange as $date) {
                $weekDay = WeekDays::select('id')
                    ->where('description', $date->format("l"))
                    ->first();
                $weekDay['isActive'] = true;
                $weekDay['id'] = $weekDay['id'];
                array_push($weekDaysActive, $weekDay);
            }
        } 
        if ($days > 0 && isset($data['weekDays']) && $data['weekDays']!='') {
            $weekDaysActive = $data['weekDays'];
        }
        
        return $weekDaysActive;
    }
    public function slot_days()
    {
        return $this->hasMany('App\Models\SlotMasterWeekDays', 'slot_master_id', 'id');
    } 
}
