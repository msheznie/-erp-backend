<?php

namespace App\Models;
use Carbon\Carbon;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CalendarDatesDetail",
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
 *          property="calendar_date_id",
 *          description="calendar_date_id",
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
 *          property="to_date",
 *          description="to_date",
 *          type="string",
 *          format="date-time"
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
class CalendarDatesDetail extends Model
{

    public $table = 'srm_calendar_dates_detail';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $appends = array(
        'from_time',
        'to_time'
    );
    public $fillable = [
        'tender_id',
        'calendar_date_id',
        'from_date',
        'to_date',
        'created_by',
        'updated_by',
        'company_id',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'tender_id' => 'integer',
        'calendar_date_id' => 'integer',
        'from_date' => 'datetime',
        'to_date' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'company_id' => 'integer',

    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function calendarDates(){
        return $this->hasOne('App\Models\CalendarDates', 'id', 'calendar_date_id');
    }


    public function getFromTimeAttribute() {
        if($this->from_date) {
            $time = new Carbon($this->from_date);
            return $time->format('Y-m-d H:i:s');
        }else {
            return null;
        }

    }

    public function getToTimeAttribute() {
        if($this->to_date) {
            $time = new Carbon($this->to_date);
            return $time->format('Y-m-d H:i:s');
        }else {
            return null;
        }

    }

    public static function updateCalendarDates($tenderId, $companyId, $calendarDateId, $dates)
    {
        CalendarDatesDetail::where('tender_id', $tenderId)
            ->where('company_id', $companyId)
            ->where('calendar_date_id', $calendarDateId)
            ->update($dates);
    }

    public static function getCalendarDateDetail($tenderId, $companyId, $calendarDateId)
    {
        return CalendarDatesDetail::where('tender_id', $tenderId)
            ->where('company_id', $companyId)
            ->where('calendar_date_id', $calendarDateId)
            ->first();
    }

    public static function getTenderCalendarDateDetailsAmd($tender_id){
        return self::where('tender_id', $tender_id)->get();
    }
    public static function getCalenderDateDetailEdit($tenderMasterId, $companySystemID = 0, $calendarDateID = 0)
    {
        $record = self::select('id', 'tender_id', 'calendar_date_id', 'from_date', 'to_date')
            ->when($companySystemID > 0, function ($q) use ($companySystemID) {
                $q->where('company_id', $companySystemID);
            })
            ->where('tender_id', $tenderMasterId)
            ->when($calendarDateID > 0, function ($q) use ($calendarDateID) {
                $q->where('calendar_date_id', $calendarDateID);
            });

        if($calendarDateID > 0) {
            return $record->count();
        }
        return $record->get();
    }
    public static function getCalendarDateDetailsRecord($id){
        return self::where('tender_id', $id)
            ->whereHas('calendarDates', function ($query) {
                $query->where('is_default', 0);
            })
            ->get()
            ->toArray();
    }
}
