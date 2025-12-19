<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CalendarDates",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="calendar_date",
 *          description="calendar_date",
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
class CalendarDates extends Model
{

    public $table = 'srm_calendar_dates';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $timestamps = false;

    public $fillable = [
        'calendar_date',
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
        'calendar_date' => 'string',
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

    public function calendar_dates_detail()
    {
        return $this->hasOne('App\Models\CalendarDatesDetail', 'calendar_date_id', 'id');
    }

    public function calendar_dates_detail_log()
    {
        return $this->hasOne('App\Models\CalendarDatesDetailEditLog', 'calendar_date_id', 'id');
    }

    public static function calendarDateMap($calendarDates)
    {
        return CalendarDates::whereIn('id', array_column($calendarDates, 'id'))
            ->get()->keyBy('id');
    }

    public static function getDefaultCalendarDate($defaultType){
        return CalendarDates::select('id')->where('is_default', $defaultType)->first();
    }

    public static function getCalendarDateDatesQry($editOrEnable, $tenderMasterID, $companySystemID, $versionID){
        $calenderDateDetailTable = $editOrEnable ? 'srm_calendar_dates_detail_edit_log' : 'srm_calendar_dates_detail';

        $additionalJoinConditions = '';
        if ($editOrEnable) {
            $additionalJoinConditions = " AND {$calenderDateDetailTable}.version_id = {$versionID} AND {$calenderDateDetailTable}.is_deleted = 0";
        }

        return " SELECT
            srm_calendar_dates.id as id,
            srm_calendar_dates.calendar_date as calendar_date,
            srm_calendar_dates.company_id as company_id,
            {$calenderDateDetailTable}.from_date as from_date,
            {$calenderDateDetailTable}.to_date as to_date
        FROM
            srm_calendar_dates 
        LEFT JOIN {$calenderDateDetailTable}
            ON {$calenderDateDetailTable}.calendar_date_id = srm_calendar_dates.id
            AND {$calenderDateDetailTable}.tender_id = {$tenderMasterID}
            {$additionalJoinConditions}
        WHERE
            (srm_calendar_dates.company_id = {$companySystemID} OR srm_calendar_dates.company_id IS NULL)
            AND ISNULL({$calenderDateDetailTable}.from_date)
            AND ISNULL({$calenderDateDetailTable}.to_date)
        ORDER BY
            CASE WHEN srm_calendar_dates.is_default IN (1, 2) THEN 0 ELSE 1 END,
            srm_calendar_dates.is_default,
            srm_calendar_dates.id
        ";

    }
    
}
